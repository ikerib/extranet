<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;

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
            'files'   => [],
            'dirs'    => [],
        ] );
    }


    /**
     * @Route("/finder/", name="dirpath")
     *
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dirpath(Request $request)
    {
        $dirpath = $request->get( 'dirpath' );
        $folders = $this->get( 'App\Controller\DefaultController' )->getSidebarFolders();

        $basedir = getenv( 'APP_FOLDER_PATH' );
        $myPath  = rtrim( $basedir . $dirpath, '/' ) . '/';


        $folderFinder = new Finder();
        $dirs         = $folderFinder->directories()->in( $myPath )->depth( '<1' )->sortByName();

        $filesFinder = new Finder();
        $files       = $filesFinder->files()->in( $myPath );


        return $this->render( 'default/index.html.twig', [
            'currentDir' => $dirpath,
            'folders'    => $folders,
            'dirs'       => $dirs,
            'files'      => $files,
        ] );

    }
}
