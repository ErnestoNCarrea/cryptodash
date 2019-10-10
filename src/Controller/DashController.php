<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Util\RipioClient;

/**
 * @Route("/dash")
 */
class DashController extends AbstractController
{
    /**
     * @Route("/inicio")
     */
    public function inicio()
    {
        $client = new RipioClient('8f2104688f50a866fe648be370c9d80ef04d2203c59a1dc5ee8eea7118a94e6f');

        $pairs = $client->getPairs();

        dump($pairs);

        return $this->render('dash/inicio.html.twig', ['pairs' => $pairs]);
    }
}
