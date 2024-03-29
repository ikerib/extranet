<?php

namespace App\Controller;

use App\Entity\Taldea;
use App\Form\TaldeaEditType;
use App\Form\TaldeaType;
use App\Repository\TaldeaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/taldea")
 */
class TaldeaController extends AbstractController
{
    /**
     * @Route("/", name="taldea_index", methods="GET")
     * @param TaldeaRepository $taldeaRepository
     *
     * @return Response
     */
    public function index(TaldeaRepository $taldeaRepository): Response
    {
        return $this->render('taldea/index.html.twig', ['taldeas' => $taldeaRepository->findAll()]);
    }

    /**
     * @Route("/new", name="taldea_new", methods="GET|POST")
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request, SecurityController $ldap): Response
    {
        $taldea = new Taldea();
//        $ldap = $this->get( 'App\Controller\SecurityController' );

        $form = $this->createForm(TaldeaType::class, $taldea, array(
            'ldap'      => $ldap,
            'action'    => $this->generateUrl('taldea_new'),
            'method'    => 'POST'
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($taldea);
            $em->flush();

            return $this->redirectToRoute('taldea_index');
        }

        return $this->render('taldea/new.html.twig', [
            'taldea' => $taldea,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="taldea_show", methods="GET")
     * @param Taldea $taldea
     *
     * @return Response
     */
    public function show(Taldea $taldea): Response
    {
        return $this->render('taldea/show.html.twig', ['taldea' => $taldea]);
    }

    /**
     * @Route("/{id}/edit", name="taldea_edit", methods="GET|POST")
     * @param Request $request
     * @param Taldea  $taldea
     *
     * @return Response
     */
    public function edit(Request $request, Taldea $taldea): Response
    {
        $form = $this->createForm(TaldeaEditType::class, $taldea, array(
            'action'    => $this->generateUrl('taldea_edit', array('id'=>$taldea->getId())),
            'method'    => 'POST'
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute( 'taldea_index' );
        }

        return $this->render('taldea/edit.html.twig', [
            'taldea' => $taldea,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="taldea_delete", methods="DELETE")
     * @param Request $request
     * @param Taldea  $taldea
     *
     * @return Response
     */
    public function delete(Request $request, Taldea $taldea): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taldea->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($taldea);
            $em->flush();
        }

        return $this->redirectToRoute('taldea_index');
    }
}
