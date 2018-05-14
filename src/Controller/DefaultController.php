<?php

namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        /**
         * Security check
         */
//        $em = $this->getDoctrine()->getManager();
//        $securityCheck = $em->getRepository('App:Karpeta')->isThisFolderAllowed()


        $folders = $this->get( 'App\Controller\DefaultController' )->getSidebarFolders();
        $dirs    = null;
        $basedir = rtrim( getenv( 'APP_FOLDER_PATH' ), '/' );
        $myPath  = rtrim( $basedir . $dirpath, '/' ) . '/';


        $folderFinder = new Finder();
        if ( isset( $orden ) || ( is_null( $orden ) ) ) {
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
        $files       = $filesFinder->files()->in( $myPath )->depth( '<1' )->sortByName();

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
     * @Route("/finder/newfolder", name="finder_newfolder")
     * @Method("POST")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newfolder( Request $request )
    {
        $form = $this->createFormBuilder()
                     ->setAction( $this->generateUrl( 'finder_newfolder' ) )
                     ->setMethod( 'POST' )
                     ->add( 'name', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                         'required' => true,
                     ) )
                     ->add( 'curdir', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
                     ->getForm();

        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $data       = $form->getData();
            $base       = rtrim(getenv('APP_FOLDER_PATH'), "/");
            $currentPath= rtrim($data[ 'curdir' ],"/").'/';
            $folderName = rtrim($data[ 'name' ],"/").'/';
            $realNewFolderPath = $base . $currentPath . $folderName;

            $fs         = new Filesystem();
            if ( !$fs->exists( $realNewFolderPath ) ) {
                $fs->mkdir( $realNewFolderPath );

                return $this->redirectToRoute( 'dirpath', array( 'dirpath' => $currentPath,
                ) );
            }
        }

        return $this->render( 'default/newform.html.twig', array(
            'form'   => $form->createView(),
        ) );
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
