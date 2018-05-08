<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\User\LdapUserProvider as SymfonyLdapUserProvider;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Ldap\Entry;

class SecurityController extends Controller
{

    /**
     * LDAP TALDEEN IZENAK MINUSKULAZ IPINI!!
     */

    /** @var array maps ldap groups to roles */
    private $groupMapping = [   // Definitely requires modification for your setup
        'app-web_egutegia' => 'ROLE_USER',
        'rol-antolakuntza_informatika' => 'ROLE_ADMIN'
    ];


    /** @var string extracts group name from dn string */
    private $groupNameRegExp = '/^CN=(?P<group>[^,]+)/i'; // You might want to change it to match your ldap server
//    private $groupNameRegExp = '/^CN=(?P<group>[^,]+),ou.*$/i'; // You might want to change it to match your ldap server






    /**
     * @Route("/login", name="login")
     * @param Request             $request
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/userdata", name="userdata")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function userdata(Request $request)
    {

        $user = $this->getUser();
        $username = $user->getUsername();

        /** Irakurri .env datuak  **/
        $ip = getenv( 'LDAP_IP' );
        $searchdn = getenv( 'LDAP_SEARCH_DN' );
        $basedn = getenv( 'LDAP_BASE_DN' );
        $passwd = getenv( 'LDAP_PASSWD' );


        $ldap = new Adapter(array('host' => $ip));
        $ldap->getConnection()->bind($searchdn, $passwd);
        $query = $ldap->createQuery($basedn, "(sAMAccountName=iibarguren)", array());
        $result = $query->execute();

        $count = count($result);

        if (!$count) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }

        if ($count > 1) {
            throw new UsernameNotFoundException('More than one user found');
        }

        $entry = $result[0];
        $roles = [];





        foreach ($entry->getAttribute('memberOf') as $groupLine) { // Iterate through each group entry line
            $groupName = strtolower($this->getGroupName($groupLine)); // Extract and normalize the group name fron the line
            if (array_key_exists($groupName, $this->groupMapping)) { // Check if the group is in the mapping
                $roles[] = $this->groupMapping[$groupName]; // Map the group to the role the user will have
            }
        }

        $this->get('session')->set('memberOf', $entry->getAttribute( 'memberOf' ));
        $this->get('session')->set('deparment', $entry->getAttribute( 'department' ) );

        $token = new UsernamePasswordToken(
            $this->getUser(),
            null,
            'main',
            $roles
        );

        $this->get( 'security.token_storage' )->setToken( $token );


        return $this->redirectToRoute( 'froga' );

    }

    /**
     * Get the group name from the DN
     * @param string $dn
     * @return string
     */
    private function getGroupName($dn)
    {
        $matches = [];
        return preg_match($this->groupNameRegExp, $dn, $matches) ? $matches['group'] : '';
    }

    /**
     * @Route("/logout", name="logout")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logout(Request $request) {
        $this->get( 'security.token_storage' )->setToken( null );
        $this->get( 'request_stack' )->getCurrentRequest()->getSession()->invalidate();
        return $this->redirectToRoute( 'login' );
    }

}
