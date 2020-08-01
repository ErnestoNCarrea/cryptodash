<?php

namespace App\Controller;

use App\Entity\UsuarioExchange;
use App\Form\UsuarioExchangeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/usuario/exchange")
 */
class UsuarioExchangeController extends AbstractController
{
    private EntityManagerInterface $em;

    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/", name="usuario_exchange_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('usuario_exchange/index.html.twig', [
            'usuario_exchanges' => $this->em->getRepository(UsuarioExchange::class)->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="usuario_exchange_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $usuarioExchange = new UsuarioExchange();
        $usuarioExchange->setUsuario($this->tokenStorage->getToken()->getUser());

        $form = $this->createForm(UsuarioExchangeType::class, $usuarioExchange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($usuarioExchange);
            $entityManager->flush();

            return $this->redirectToRoute('usuario_exchange_index');
        }

        return $this->render('usuario_exchange/new.html.twig', [
            'usuario_exchange' => $usuarioExchange,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="usuario_exchange_show", methods={"GET"})
     */
    public function show(UsuarioExchange $usuarioExchange): Response
    {
        return $this->render('usuario_exchange/show.html.twig', [
            'usuario_exchange' => $usuarioExchange,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="usuario_exchange_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UsuarioExchange $usuarioExchange): Response
    {
        $form = $this->createForm(UsuarioExchangeType::class, $usuarioExchange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('usuario_exchange_index');
        }

        return $this->render('usuario_exchange/edit.html.twig', [
            'usuario_exchange' => $usuarioExchange,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="usuario_exchange_delete", methods={"DELETE"})
     */
    public function delete(Request $request, UsuarioExchange $usuarioExchange): Response
    {
        if ($this->isCsrfTokenValid('delete'.$usuarioExchange->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($usuarioExchange);
            $entityManager->flush();
        }

        return $this->redirectToRoute('usuario_exchange_index');
    }
}
