<?php

namespace App\Controller;

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
        return $this->render('default/index.html.twig', [
            'controller_name' => 'frogaaaaaaaaaaaaaaaaaaa',
        ]);
    }
}
