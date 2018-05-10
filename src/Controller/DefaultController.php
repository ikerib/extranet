<?php

namespace App\Controller;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="default")
     */
    public function index()
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }



    /**
     * @Route("/froga", name="froga")
     */
    public function froga()
    {
        $ldapInfo = $this->get( 'session' )->get( 'ldapInfo' );
        $groupTaldeaRegExp = '(^(Sarbide))';

        $em = $this->getDoctrine()->getManager();
        $folders = [];

        foreach ($ldapInfo as $l) {

            if ( preg_match($groupTaldeaRegExp,$l) ) {

                $dirs = $em->getRepository( 'App:Karpeta' )->getSidebarFoldersForSarbide( $l );

                if ( count($dirs) >0 ) {

                    foreach ( $dirs as $dir ) {
                        array_push( $folders, $dir );
                    }

                }

            }

        }

        return $this->render('default/index.html.twig', [
            'controller_name' => 'frogaaaaaaaaaaaaaaaaaaa',
            'folders' => $folders
        ]);
    }
}
