<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->render('dash/inicio.html.twig', []);
    }
}
