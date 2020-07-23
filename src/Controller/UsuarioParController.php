<?php

namespace App\Controller;

use App\Entity\UsuarioPar;
use App\Form\UsuarioParType;
use App\Repository\UsuarioParRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/usuario/par")
 */
class UsuarioParController extends AbstractController
{
    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/", name="usuario_par_index", methods={"GET"})
     */
    public function index(UsuarioParRepository $usuarioParRepository): Response
    {
        return $this->render('usuario_par/index.html.twig', [
            'usuario_pars' => $usuarioParRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="usuario_par_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $usuarioPar = new UsuarioPar();
        $usuarioPar->setUsuario($this->tokenStorage->getToken()->getUser());

        $form = $this->createForm(UsuarioParType::class, $usuarioPar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($usuarioPar);
            $entityManager->flush();

            return $this->redirectToRoute('usuario_par_index');
        }

        return $this->render('usuario_par/new.html.twig', [
            'usuario_par' => $usuarioPar,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="usuario_par_show", methods={"GET"})
     */
    public function show(UsuarioPar $usuarioPar): Response
    {
        return $this->render('usuario_par/show.html.twig', [
            'usuario_par' => $usuarioPar,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="usuario_par_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UsuarioPar $usuarioPar): Response
    {
        $form = $this->createForm(UsuarioParType::class, $usuarioPar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('usuario_par_index');
        }

        return $this->render('usuario_par/edit.html.twig', [
            'usuario_par' => $usuarioPar,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="usuario_par_delete", methods={"DELETE"})
     */
    public function delete(Request $request, UsuarioPar $usuarioPar): Response
    {
        if ($this->isCsrfTokenValid('delete'.$usuarioPar->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($usuarioPar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('usuario_par_index');
    }
}
