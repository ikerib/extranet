<?php

namespace App\Controller;

use http\Env\Request;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/sidebarfolders", name="sidebarfolders")
     * @return array
     */
    public function getSidebarFolders()
    {
        $ldapInfo          = $this->get( 'session' )->get( 'ldapInfo' );
        $groupTaldeaRegExp = '(^(Sarbide))';

        $em      = $this->getDoctrine()->getManager();
        $folders = [];

        foreach ( $ldapInfo as $l ) {

            if ( preg_match( $groupTaldeaRegExp, $l ) ) {

                $dirs = $em->getRepository( 'App:Karpeta' )->getSidebarFoldersForSarbide( $l );

                if ( count( $dirs ) > 0 ) {

                    foreach ( $dirs as $dir ) {
                        array_push( $folders, $dir );
                    }

                }

            }

        }

        return $folders;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        $folders = $this->get( 'App\Controller\DefaultController' )->getSidebarFolders();

        return $this->render( 'default/index.html.twig', [
            'folders' => $folders,
            'files' => [],
            'dirs'  => []
        ] );
    }


    /**
     * @Route("/finder/{dirpath}", name="dirpath")
     * @param         $dirpath
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dirpath( $dirpath )
    {
        $finder = new Finder();

        $folders = $this->get( 'App\Controller\DefaultController' )->getSidebarFolders();

        $basedir = getenv( 'APP_FOLDER_PATH' );
        $dirs    = $finder->directories()->in( $basedir . $dirpath );
        $files   = $finder->files()->in( $basedir . $dirpath );

        return $this->render( 'default/index.html.twig', [
            'folders' => $folders,
            'dirs'    => $dirs,
            'files'   => $files,
        ] );
    }
}
