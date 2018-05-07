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
     * @Route("/froga", name="default")
     */
    public function froga()
    {
        $myPath = "/home/local/PASAIA/iibarguren/dev";
        /** @var Finder $finder */
        $finder = new Finder();

        $dirs = $finder->directories()->in( $myPath );

        dump( $dirs );

        return $this->render('default/index.html.twig', [
            'controller_name' => 'frogaaaaaaaaaaaaaaaaaaa',
        ]);
    }
}
