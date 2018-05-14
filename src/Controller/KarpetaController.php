<?php

namespace App\Controller;

use App\Entity\Karpeta;
use App\Form\KarpetaEditType;
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
     * @param KarpetaRepository $karpetaRepository
     *
     * @return Response
     */
    public function index(KarpetaRepository $karpetaRepository): Response
    {
        return $this->render('karpeta/index.html.twig', ['karpetas' => $karpetaRepository->findAll()]);
    }

    /**
     * @Route("/new", name="karpeta_new", methods="GET|POST")
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        $karpetum = new Karpeta();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(KarpetaType::class, $karpetum, array(
            'entity_manager' => $em,
            'action'    => $this->generateUrl('karpeta_new'),
            'method'    => 'POST'
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $arr = explode( '/', $karpetum->getPath() );
            $lastFolder = $result = end($arr);
            $karpetum->setFoldername( $lastFolder );
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
     * @Route("/{id}/edit", name="karpeta_edit", methods="GET|POST")
     * @param Request $request
     * @param Karpeta $karpetum
     *
     * @return Response
     */
    public function edit(Request $request, Karpeta $karpetum): Response
    {
        $form = $this->createForm(KarpetaEditType::class, $karpetum, array(
            'action'    => $this->generateUrl('karpeta_edit', array('id' => $karpetum->getId())),
            'method'    => 'POST'
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $arr = explode( '/', $karpetum->getPath() );
            $lastFolder = $result = end($arr);
            $karpetum->setFoldername( $lastFolder );
            $em = $this->getDoctrine()->getManager();
            $em->persist( $karpetum );
            $em->flush();
//            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('karpeta_index');
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
