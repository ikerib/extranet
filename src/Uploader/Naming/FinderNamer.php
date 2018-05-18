<?php
/**
 * Created by PhpStorm.
 * User: iibarguren
 * Date: 16/05/18
 * Time: 13:26
 */

namespace App\Uploader\Naming;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;
use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Session\Session;

class FinderNamer implements NamerInterface
{
    private $session;

    public function __construct( Session $session )
    {
        $this->session = $session;
    }

    /**
     * Name a given file and return the name.
     *
     * @param FileInterface $file
     *
     * @return string
     */
    public function name( FileInterface $file )
    {
        $curdir   = $this->session->get( 'curdir' );
        $izena    = ( rtrim( $curdir, '/' ) . '/' ) . $file->getClientOriginalName();
        $base     = rtrim( getenv( 'APP_FOLDER_PATH' ), '/' ) . '/';
        $realName = $base . $izena;

        $fs = new Filesystem();
        if ( $fs->exists( $realName ) ) {
            $izena = ( rtrim( $curdir, '/' ) . '/' ) . "-KOPIA-" .date("d-m-Y H:i:s") . $file->getClientOriginalName();
        }

        return $izena;
    }
}