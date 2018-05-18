<?php

namespace App\Controller;

use App\Entity\Permission;
use App\Form\PermissionType;
use App\Repository\PermissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/permission")
 */
class PermissionController extends Controller
{
    /**
     * @Route("/", name="permission_index", methods="GET")
     * @param PermissionRepository $permissionRepository
     *
     * @return Response
     */
    public function index( PermissionRepository $permissionRepository ): Response
    {
        return $this->render( 'permission/index.html.twig', [
            'permissions' => $permissionRepository->findAll(),

        ] );
    }

    /**
     * @Route("/new", name="permission_new", methods="GET|POST")
     * @param Request $request
     *
     * @return Response
     */
    public function new( Request $request ): Response
    {
        $permission = new Permission();
        $form       = $this->createForm( PermissionType::class, $permission );
        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist( $permission );
            $em->flush();

            return $this->redirectToRoute( 'permission_index' );
        }

        return $this->render( 'permission/new.html.twig', [
            'permission' => $permission,
            'form'       => $form->createView(),
        ] );
    }

    /**
     * @Route("/{id}/edit", name="permission_edit", methods="GET|POST")
     * @param Request    $request
     * @param Permission $permission
     *
     * @return Response
     */
    public function edit( Request $request, Permission $permission ): Response
    {
        $form = $this->createForm( PermissionType::class, $permission );
        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute( 'permission_edit', [ 'id' => $permission->getId() ] );
        }

        return $this->render( 'permission/edit.html.twig', [
            'permission' => $permission,
            'form'       => $form->createView(),
        ] );
    }

    /**
     * @Route("/{id}", name="permission_delete", methods="DELETE")
     * @param Request    $request
     * @param Permission $permission
     *
     * @return Response
     */
    public function delete( Request $request, Permission $permission ): Response
    {
        if ( $this->isCsrfTokenValid( 'delete' . $permission->getId(), $request->request->get( '_token' ) ) ) {
            $em = $this->getDoctrine()->getManager();
            $em->remove( $permission );
            $em->flush();
        }

        return $this->redirectToRoute( 'permission_index' );
    }
}
