<?php

namespace App\Controller;

use App\Entity\Karpeta;
use App\Form\KarpetaType;
use App\Repository\KarpetaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/karpeta")
 */
class KarpetaController extends Controller
{
    /**
     * @Route("/", name="karpeta_index", methods="GET")
     */
    public function index(KarpetaRepository $karpetaRepository): Response
    {
        return $this->render('karpeta/index.html.twig', ['karpetas' => $karpetaRepository->findAll()]);
    }

    /**
     * @Route("/new", name="karpeta_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $karpetum = new Karpeta();
        $form = $this->createForm(KarpetaType::class, $karpetum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($karpetum);
            $em->flush();

            return $this->redirectToRoute('karpeta_index');
        }

        return $this->render('karpeta/new.html.twig', [
            'karpetum' => $karpetum,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="karpeta_show", methods="GET")
     */
    public function show(Karpeta $karpetum): Response
    {
        return $this->render('karpeta/show.html.twig', ['karpetum' => $karpetum]);
    }

    /**
     * @Route("/{id}/edit", name="karpeta_edit", methods="GET|POST")
     */
    public function edit(Request $request, Karpeta $karpetum): Response
    {
        $form = $this->createForm(KarpetaType::class, $karpetum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('karpeta_edit', ['id' => $karpetum->getId()]);
        }

        return $this->render('karpeta/edit.html.twig', [
            'karpetum' => $karpetum,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="karpeta_delete", methods="DELETE")
     */
    public function delete(Request $request, Karpeta $karpetum): Response
    {
        if ($this->isCsrfTokenValid('delete'.$karpetum->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($karpetum);
            $em->flush();
        }

        return $this->redirectToRoute('karpeta_index');
    }
}
