<?php


namespace App\EventListener;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $em;
    private $session;
    private $tokenStorage;
    private $authenticationManager;
    /** @var array maps ldap groups to roles */
    private $groupMapping = [   // Definitely requires modification for your setup
        'app-web_egutegia'             => 'ROLE_USER',
        'rol-antolakuntza_informatika' => 'ROLE_ADMIN',
    ];

    private $ldapTaldeak = [];
    private $ldapInfo = [];

    /** @var string extracts group name from dn string */
    private $groupNameRegExp = '/^CN=(?P<group>[^,]+)/i'; // You might want to change it to match your ldap server

    /** @var string extracts group name from dn string */
    private $groupTaldeaRegExp = '(^(ROL|Saila|Taldea))'; // ROL - Taldea - Saila -rekin hasten den begiratzen du

    /** @var string extracts group name from dn string */
    private $groupSarbideExp = '/Sarbide/i'; // ROL - Taldea - Saila -rekin hasten den begiratzen du
//    private $groupSarbideExp = '(^(Sarbide)i)'; // ROL - Taldea - Saila -rekin hasten den begiratzen du

    /** @var string extracts group name from dn string */
    private $TaldeIzenaRegExp = '/^CN=([^,]+)/i'; // ROL - Taldea - Saila -rekin hasten den begiratzen du

    public function __construct(EntityManagerInterface $em, SessionInterface $session, TokenStorageInterface $tokenStoragey, AuthenticationManagerInterface  $authenticationManager)
    {
        $this->em = $em;
        $this->session = $session;
        $this->tokenStorage = $tokenStoragey;
        $this->authenticationManager = $authenticationManager;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        // Get the User entity.
        $user = $event->getAuthenticationToken()->getUser();
        $username = $user->getUsername();
        $result = $this->getLdapInfo( $username );

        $count = count( $result );

        if ( !$count ) {
            throw new UsernameNotFoundException( sprintf( 'User "%s" not found.', $username ) );
        }

        if ( $count > 1 ) {
            throw new UsernameNotFoundException( 'More than one user found' );
        }

        $entry   = $result[ 0 ];
        $roles   = [];
        $taldeak = $entry->getAttribute( 'memberOf' );

        foreach ( $taldeak as $t ) {
            $this->ldapTaldeak[] = $t;
        }

        /**
         * ESKURATU LDAP Taldeak
         */
        foreach ( $entry->getAttribute( 'memberOf' ) as $groupLine ) { // Iterate through each group entry line
            $groupName = strtolower( $this->getGroupName( $groupLine ) ); // Extract and normalize the group name fron the line
            if ( array_key_exists( $groupName, $this->groupMapping ) ) { // Check if the group is in the mapping
                $roles[] = $this->groupMapping[ $groupName ]; // Map the group to the role the user will have
            } else {
                $roles[] = 'ROLE_USER';
            }
            $this->ldap_recursive( $this->getGroupName( $groupLine ) );
        }

        /**
         * Sesio bariable batean gorde agian erabilgarri izan daitekeen informazioa
         */
        foreach ( $this->ldapTaldeak as $talde ) {
            $this->ldapInfo[] = $this->getGroupName($talde);
        }
        sort( $this->ldapInfo );

        $matches  = preg_grep ($this->groupSarbideExp, $this->ldapInfo);
        $this->session->set('ldapInfo', $this->ldapInfo );
        $this->session->set( 'deparment', $entry->getAttribute( 'department' ) );
        $this->session->set( 'sarbideak', $matches );

        $log = new Log( );
        $log->setUser( $user->getUsername() );
        $log->setAction( 'Login' );
        $log->setDescription( 'Saioa hasi du.' );
        $this->em->persist( $log );
        $this->em->flush();
        $token = new UsernamePasswordToken(
            $user,
            null,
            'main',
            $roles
        );
        $this->tokenStorage->setToken($token);

    }

    public function getLdapInfo( $username )
    {
        /** Irakurri .env datuak  **/
        $ip       = $_ENV[ 'LDAP_IP' ];
        $searchdn = $_ENV[ 'LDAP_SEARCH_DN' ];
        $basedn   = $_ENV[ 'LDAP_BASE_DN' ];
        $passwd   = $_ENV[ 'LDAP_PASSWD' ];


        /**
         * LDAP KONTSULTA EGIN erabiltzailearen bila
         */
        $ldap = new Adapter( array( 'host' => $ip ) );
        $ldap->getConnection()->bind( $searchdn, $passwd );
        $query = $ldap->createQuery( $basedn, "(sAMAccountName=$username)", array() );

        return $query->execute();
    }

    public function ldap_recursive( $name )
    {
        if ( preg_match( $this->groupTaldeaRegExp, $name ) ) {
            $tal = $this->getLdapInfo( $name );

            if ( count( $tal ) ) {
                $taldek = $tal[ 0 ]->getAttribute( 'memberOf' );
                if ( !is_null( $taldek ) ) {
                    foreach ( $taldek as $t ) {
                        if ( !in_array( $t, $this->ldapTaldeak ) ) {
                            array_push( $this->ldapTaldeak, $t );
                            $this->ldap_recursive( $this->getGroupName( $t ) );
                        }
                    }
                }
            }
        }
    }

    public function getAllLdapGroups( $filter )
    {
        $resp = $this->getLdapInfo( $filter );

    }

    private function getGroupName( $dn )
    {
        $matches = [];

        return preg_match( $this->groupNameRegExp, $dn, $matches ) ? $matches[ 'group' ] : '';
    }
}
