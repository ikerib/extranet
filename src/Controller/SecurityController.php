<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
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
     * @param Request             $request
     *
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
//        $query = $ldap->createQuery($basedn, "CN=iibarguren,CN=Users,DC=pasaia,DC=net", array());
//        $query = $ldap->createQuery($basedn, "(&(sAMAccountName={$username})(memberOf=cn=users))", array());
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


        $this->getUser()->setAttribute( 'memberOf', $entry->getAttribute( 'memberOf' ) );
        $this->getUser()->setAttribute( 'deparment', $entry->getAttribute( 'department' ) );

        return $this->redirectToRoute( 'froga' );

    }
}
