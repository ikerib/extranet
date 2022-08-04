<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="login")
     * @param Request             $request
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login( Request $request, AuthenticationUtils $authenticationUtils )
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render( 'security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ) );
    }


    /**
     * @Route("/logout", name="logout")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logout( Request $request )
    {
        $this->get( 'security.token_storage' )->setToken( null );
        $this->get( 'request_stack' )->getCurrentRequest()->getSession()->invalidate();

        return $this->redirectToRoute( 'login' );
    }


    public function getLdapInfo( $username )
    {
        /** Irakurri .env datuak  **/
        $ip       = $_ENV ['LDAP_IP' ];
        $searchdn = $_ENV ['LDAP_SEARCH_DN' ];
        $basedn   = $_ENV ['LDAP_BASE_DN' ];
        $passwd   = $_ENV ['LDAP_PASSWD' ];


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
}
