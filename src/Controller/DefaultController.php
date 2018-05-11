<?php

namespace App\Controller;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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
            'breadcrumbs' => [],
            'currentDir'  => "/",
            'folders'     => $folders,
            'homeFolders' => $folders,
            'files'       => [],
            'dirs'        => null,
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
    public function dirpath( Request $request )
    {
        $dirpath = $request->get( 'dirpath' );
        $orden   = $request->get( 'orden' );
        $folders = $this->get( 'App\Controller\DefaultController' )->getSidebarFolders();

        $basedir = rtrim( getenv( 'APP_FOLDER_PATH' ), '/' );
        $myPath  = rtrim( $basedir . $dirpath, '/' ) . '/';


        $folderFinder = new Finder();
        if ( isset( $orden ) ) {
            $dirs = $folderFinder->directories()->in( $myPath )->depth( '<1' )->sortByName();
        } else {
            if ( $orden == "name" ) {
                $dirs = $folderFinder->directories()->in( $myPath )->depth( '<1' )->sortByName();
            } elseif ( $orden == "created" ) {
                $dirs = $folderFinder->directories()->in( $myPath )->depth( '<1' )->sortByChangedTime();
            } elseif ( $orden == "updated" ) {
                $dirs = $folderFinder->directories()->in( $myPath )->depth( '<1' )->sortByModifiedTime();
            }
        }

        $filesFinder = new Finder();
        $files       = $filesFinder->files()->in( $myPath );

        $breadcrumbs = explode( '/', ltrim( $dirpath, '/' ) );
        $ogiazalak   = [];

        foreach ( $breadcrumbs as $key => $value ) {
            if ( $key == 0 ) {
                $ogiazalak[ $value ] = $value;
            } else {
                $ogiazalak[ $value ] = $breadcrumbs[ $key - 1 ] . "/" . $value;
            }

        }

        return $this->render( 'default/index.html.twig', [
            'currentDir'  => $dirpath,
            'breadcrumbs' => $ogiazalak,
            'folders'     => $folders,
            'dirs'        => $dirs,
            'files'       => $files,
        ] );

    }

    /**
     * @Route("/finder/file/download", name="file_download")
     *
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fileDownload( Request $request )
    {
        $filePath = $request->get( 'filePath' );

        /* Check if file exists */
        /** @var Filesystem $fs */
        $fs = new Filesystem();
        if ( $fs->exists( $filePath ) ) {
            $response = new BinaryFileResponse( $filePath );
            $response->setContentDisposition( ResponseHeaderBag::DISPOSITION_ATTACHMENT );

            return $response;
        } else {
            throw new FileNotFoundException();
        }


    }
}
