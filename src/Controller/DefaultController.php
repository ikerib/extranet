<?php

namespace App\Controller;


use App\Entity\Karpeta;
use App\Entity\Log;
use App\Entity\Permission;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Comparator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Finder\Finder;
use Ouzo;


class DefaultController extends AbstractController
{

    private function Nirelog( $action, $desc )
    {
        $em  = $this->getDoctrine()->getManager();
        $log = new Log();
        $log->setUser( $this->getUser()->getUsername() );
        $log->setAction( $action );
        $log->setDescription( $desc );
        $em->persist( $log );
        $em->flush();
    }

    private function isArrayInArray( $lookingFor, $mainArray )
    {
        $badago = false;
        foreach ( $mainArray as $m ) {
            if ($m == $lookingFor) {
                $badago = true;
            }
        }

        return $badago;
    }

    /**
     * @Route("/sidebarfolders", name="sidebarfolders")
     * @return array
     */
    public function getSidebarFolders()
    {
        $ldapInfo          = $this->get( 'session' )->get( 'ldapInfo' );
        $groupTaldeaRegExp = '/(Sarbide|Denak)/i';

        $em      = $this->getDoctrine()->getManager();
        $folders = [];

        foreach ( $ldapInfo as $l ) {

            if ( preg_match( $groupTaldeaRegExp, $l ) ) {

                $dirs = $em->getRepository( Karpeta::class )->getSidebarFoldersForSarbide( $l );

                if ( count( $dirs ) > 0 ) {

                    foreach ( $dirs as $dir ) {
                        if ( !$this->isArrayInArray( $dir, $folders ) ) {
                            array_push( $folders, $dir );
                        }
                    }
                }
            }
        }

        // Alfabetikoki ordenatu
        $result = Arrays::sort( $folders, Comparator::compareBy( 'foldername' ) );

        return $result;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        $folders = $this->getSidebarFolders();

        return $this->render( 'default/homepage.html.twig', [
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
     */
    public function dirpath( Request $request ): Response
    {
        $dirpath = $request->get( 'dirpath' );
        $orden   = $request->get( 'orden' );
        $direction   = $request->get( 'direction' );

        str_replace( "//", "/", $dirpath );

        if ( $dirpath !== "/" ) {
            $firstPath = "/" . explode( "/", $dirpath )[ 1 ];
        } else {
            $firstPath = "/";
        }

        $this->Nirelog( 'dir', $dirpath );

        /**
         * Security check
         */
        $em        = $this->getDoctrine()->getManager();
        $sarbideak = $this->get( 'session' )->get( 'sarbideak' );
        $baimendua = false;
        $canupload = false;
        foreach ( $sarbideak as $sarbide ) {
            $securityCheck = $em->getRepository( Karpeta::class )->isThisFolderAllowed( $firstPath, $sarbide );

            if ( count( $securityCheck ) > 0 ) {

                $baimendua   = true;
                $permissions = $em->getRepository( Permission::class )->canUpload( $firstPath, $sarbide );

                /** @var Permission $p */
                foreach ( $permissions as $p ) {
                    if ($p->getCanWrite()) {
                        $canupload = true;
                    }
                }
            }
        }



        if (!$baimendua) {
            $this->Nirelog( 'Baimenik ez', $dirpath );
            $this->addFlash(
                'danger',
                'Ez duzu baimenik karpeta honetara sartzeko'
            );

            if ( is_null( $request->server->get( 'HTTP_REFERER' ) ) ) {
                return $this->redirectToRoute( 'homepage' );
            }

            return $this->redirect( $request->server->get( 'HTTP_REFERER' ) );
        }


        $folders = $this->getSidebarFolders();
        $dirs    = null;
        $basedir = rtrim( $_ENV[ 'APP_FOLDER_PATH'], '/' );
        $myPath  = rtrim( $basedir . $dirpath, '/' ) . '/';

        $_breadcrumbs = explode( '/', ltrim( $dirpath, '/' ) );
        $_ogiazalak   = [];

        foreach ( $_breadcrumbs as $_key => $_value ) {
            if ( $_key === 0 ) {
                $_ogiazalak[ $_value ] = $_value;
            } else {
                $_ogiazalak[ $_value ] = $_ogiazalak[ $_breadcrumbs[ $_key - 1 ] ] . "/" . $_value;
            }

        }

        $folderFinder = new Finder();
        if ( isset( $orden ) || ( is_null( $orden ) ) ) {
            try {
                if ($direction === "ASC") {
                    $dirs = $folderFinder->directories()->in( $myPath )->depth( '<1' )->sortByChangedTime();
                } else {
                    $dirs = $folderFinder->directories()->in( $myPath )->depth( '<1' )->sortByChangedTime()->reverseSorting();
                }
            } catch ( \Exception $e) {

                return $this->render( 'default/error.html.twig', [
                    'currentDir'  => $dirpath,
                    'breadcrumbs' => $_ogiazalak,
                    'folders'     => $folders,
                    'dirs'        => $dirs,
                    'files'       => null,
                    'canupload'   => $canupload,
                    'errors'      => $e
                ] );
            }

        } else if ( $orden === "name" ) {
            if ($direction === "ASC") {
                $dirs = $folderFinder->directories()->in( $myPath )->depth( '<1' )->sortByName();
            } else {
                $dirs = $folderFinder->directories()->in( $myPath )->depth( '<1' )->sortByName()->reverseSorting();
            }
        } elseif ( $orden === "created" ) {
            if ( $direction === "ASC") {
                $dirs = $folderFinder->directories()->in( $myPath )->depth( '<1' )->sortByChangedTime();
            } else {
                $dirs = $folderFinder->directories()->in( $myPath )->depth( '<1' )->sortByChangedTime()->reverseSorting();
            }
        } elseif ( $orden === "updated" ) {
            if ( $direction === "ASC") {
                $dirs = $folderFinder->directories()->in( $myPath )->depth( '<1' )->sortByModifiedTime();
            } else {
                $dirs = $folderFinder->directories()->in( $myPath )->depth( '<1' )->sortByModifiedTime()->reverseSorting();
            }
        }

        $filesFinder = new Finder();

        if ( !isset( $orden ) || ( is_null( $orden ) ) ) {
            try {
                if ($direction === "ASC") {
                    $files       = $filesFinder->files()->in( $myPath )->depth( '<1' )->sortByChangedTime();
                } else {
                    $files       = $filesFinder->files()->in( $myPath )->depth( '<1' )->sortByChangedTime()->reverseSorting();
                }
            } catch ( \Exception $e) {

                return $this->render( 'default/error.html.twig', [
                    'currentDir'  => $dirpath,
                    'breadcrumbs' => $_ogiazalak,
                    'folders'     => $folders,
                    'dirs'        => $dirs,
                    'files'       => null,
                    'canupload'   => $canupload,
                    'errors'      => $e
                ] );
            }

        } else if ( $orden === "izena" ) {
            if ( $direction === "ASC") {
                $files       = $filesFinder->files()->in( $myPath )->depth( '<1' )->sortByName();
            } else {
                $files       = $filesFinder->files()->in( $myPath )->depth( '<1' )->sortByName()->reverseSorting();
            }
        } elseif ( $orden === "created" ) {
            if ( $direction === "ASC") {
                $files       = $filesFinder->files()->in( $myPath )->depth( '<1' )->sortByChangedTime();
            } else {
                $files       = $filesFinder->files()->in( $myPath )->depth( '<1' )->sortByChangedTime()->reverseSorting();
            }
        } elseif ( $orden === "updated" ) {
            if ( $direction === "ASC") {
                $files       = $filesFinder->files()->in( $myPath )->depth( '<1' )->sortByModifiedTime();
            } else {
                $files       = $filesFinder->files()->in( $myPath )->depth( '<1' )->sortByModifiedTime()->reverseSorting();
            }
        }




        $this->get( 'session' )->set( 'curdir', $dirpath );


        return $this->render( 'default/index.html.twig', [
            'currentDir'  => $dirpath,
            'breadcrumbs' => $_ogiazalak,
            'folders'     => $folders,
            'dirs'        => $dirs,
            'files'       => $files,
            'canupload'   => $canupload,
        ] );

    }

    /**
     * @Route("/finder/newfolder", name="finder_newfolder", methods={"POST"})
     * @param Request $request
     *
     * @return Response
     */
    public function newfolder( Request $request )
    {
        $form = $this->createFormBuilder()
                     ->setAction( $this->generateUrl( 'finder_newfolder' ) )
                     ->setMethod( 'POST' )
                     ->add( 'name', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                         'required' => true,
                     ) )
                     ->add( 'curdir', 'Symfony\Component\Form\Extension\Core\Type\HiddenType' )
                     ->getForm();

        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $data              = $form->getData();
            $base              = rtrim( $_ENV[ 'APP_FOLDER_PATH'], "/" );
            $currentPath       = rtrim( $data[ 'curdir' ], "/" ) . '/';
            $folderName        = rtrim( $data[ 'name' ], "/" ) . '/';
            $realNewFolderPath = $base . $currentPath . $folderName;


            $fs = new Filesystem();
            if ( !$fs->exists( $realNewFolderPath ) ) {
                $fs->mkdir( $realNewFolderPath );

                $this->Nirelog( 'Karpeta berria', $realNewFolderPath );

                return $this->redirectToRoute( 'dirpath', array(
                    'dirpath' => $currentPath,
                ) );
            } else {

                $this->Nirelog( 'Error', "Karpeta existitzen da: " . $realNewFolderPath );

                $this->addFlash( 'danger', 'Existe' );

                return $this->redirectToRoute( 'dirpath', array(
                    'dirpath' => $currentPath,
                    'error'   => 'Karpeta existitzen da',
                ) );
            }
        }

        return $this->render( 'default/newform.html.twig', array(
            'form' => $form->createView(),
        ) );
    }

    /**
     * @Route("/finder/rename", name="finder_rename_file_folder", methods={"POST"})
     * @param Request $request
     *
     * @return Response
     */
    public function renameFileFolder( Request $request )
    {
        $form = $this->createFormBuilder()
                     ->setAction( $this->generateUrl( 'finder_rename_file_folder' ) )
                     ->setMethod( 'POST' )
                     ->add( 'newname', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                         'required' => true,
                     ) )
                     ->add( 'oldFilename', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                         'required' => true,
                     ) )
                     ->add( 'currentdir', 'Symfony\Component\Form\Extension\Core\Type\HiddenType' )
                     ->getForm();

        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $data              = $form->getData();
            $base              = rtrim( $_ENV[ 'APP_FOLDER_PATH' ], "/" );
            $currentDir        = rtrim( $data[ 'currentdir' ], "/" ) . '/';
            $folderName        = $data[ 'newname' ];
            $oldFileName       = $data[ 'oldFilename' ];
            $realNewFolderPath = $base . $currentDir . $folderName;

            $fs = new Filesystem();
            if ( $fs->exists( $oldFileName ) ) {

                $this->Nirelog( 'Izena aldatu', "Izen zaharra: " . $oldFileName . " // Izen berria: " . $realNewFolderPath );

                $fs->rename( $oldFileName, $realNewFolderPath );

                return $this->redirectToRoute( 'dirpath', array(
                    'dirpath' => $currentDir,
                ) );
            } else {
                $this->addFlash( 'danger', 'Existe' );

                $this->Nirelog( 'Error', "Karpeta ez da existitzen: " . $oldFileName );

                return $this->redirectToRoute( 'dirpath', array(
                    'dirpath' => $currentDir,
                    'error'   => 'Karpeta ez da existitzen.',
                ) );
            }
        }

        return $this->render( 'default/rename.html.twig', array(
            'form' => $form->createView(),
        ) );
    }

    /**
     * @Route("/finder/delete", name="finder_delete", methods={"DELETE"})
     * @param Request $request
     *
     * @return Response
     */
    public function deleteFileFolder( Request $request )
    {

        $form = $this->createFormBuilder()
                     ->setAction( $this->generateUrl( 'finder_delete' ) )
                     ->setMethod( 'DELETE' )
                     ->add( 'filefolders', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array(
                         'required' => true,
                     ) )
                     ->add( 'currentdir2', 'Symfony\Component\Form\Extension\Core\Type\HiddenType' )
                     ->getForm();

        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $data        = $form->getData();
            $base        = rtrim( $_ENV[ 'APP_FOLDER_PATH' ], "/" );
            $currentDir  = rtrim( $data[ 'currentdir2' ], "/" ) . '/';
            $filefolders = json_decode( $data[ 'filefolders' ] );
            $fs          = new Filesystem();

            foreach ( $filefolders as $f ) {
                if ( $fs->exists( $f ) ) {
                    $this->Nirelog( 'Ezabatu', "Ezabatu da: " . $f );
                    $fs->remove( $f );
                }
            }

            return $this->redirectToRoute( 'dirpath', array(
                'dirpath' => $currentDir,
            ) );

        }

        return $this->render( 'default/delete.html.twig', array(
            'form' => $form->createView(),
        ) );
    }

    /**
     * @Route("/finder/file/download", name="file_download")
     *
     *
     * @param Request $request
     *
     * @return Response
     */
    public function fileDownload( Request $request )
    {
        $filePath = $request->get( 'filePath' );

        /* Check if file exists */
        /** @var Filesystem $fs */
        $fs = new Filesystem();
        if ( $fs->exists( $filePath ) ) {

            $this->Nirelog( 'Download', $filePath );

            $response = new BinaryFileResponse( $filePath );
            $response->setContentDisposition( ResponseHeaderBag::DISPOSITION_ATTACHMENT );

            return $response;
        } else {
            throw new FileNotFoundException();
        }


    }

    /**
     * @Route("/finder/export", name="finder_export")
     *
     *
     * @param Request $request
     *
     * @return Response
     */
    public function export( Request $request )
    {
        $form = $this->createFormBuilder()
                     ->setAction( $this->generateUrl( 'finder_export' ) )
                     ->setMethod( 'POST' )
                     ->add( 'files', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                         'required' => true,
                     ) )
                     ->add( 'exportcurrentdir', 'Symfony\Component\Form\Extension\Core\Type\TextType' )
                     ->getForm();

        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $data       = $form->getData();
            $base       = rtrim( $_ENV[ 'APP_FOLDER_PATH' ], "/" );
            $currentDir = rtrim( $data[ 'exportcurrentdir' ], "/" ) . '/';

            $removeString = $base . $currentDir;

            $artxiboak = explode( "||", $data[ 'files' ] );

            $zip     = new \ZipArchive();
            $zipName = 'export/Documents_' . time() . ".zip";
            $zip->open( $zipName, \ZipArchive::CREATE );
            foreach ( $artxiboak as $f ) {
                /** check if is folder */
                if ( is_dir( $f ) ) {
                    $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $f ), RecursiveIteratorIterator::SELF_FIRST );

                    foreach ( $files as $file ) {

                        $file = realpath( $file );

                        if ( is_dir( $file ) === true ) {
                            $tmp   = str_replace( $f . '/', '', $file . '/' );
                            $final = str_replace( $removeString, "", $tmp );

                            $zip->addEmptyDir( $final );
                        } else if ( is_file( $file ) === true ) {
                            $tmp   = str_replace( $f . '/', '', $file );
                            $final = str_replace( $removeString, "", $tmp );
                            $zip->addFromString( $final, file_get_contents( $file ) );
                        }
                    }
                } else {
                    if ( is_file( $f ) ) {
                        $zip->addFromString( basename( $f ), file_get_contents( $f ) );
                    }
                }

                $this->Nirelog( 'Zip', "Konprimitzera gehitu da " . $f );

            }
            $zip->close();

            $response = new Response( file_get_contents( $zipName ) );
            $response->headers->set( 'Content-Type', 'application/zip' );
            $response->headers->set( 'Content-Disposition', 'attachment;filename="' . $zipName . '"' );
            $response->headers->set( 'Content-length', filesize( $zipName ) );

            $this->Nirelog( 'Zip', "Fitxategia sortu eta deskargatu da: " . $zipName );

            return $response;
        }

        return $this->render( 'default/export.html.twig', array(
            'form' => $form->createView(),
        ) );
    }
}
