<?php

namespace App\Controller;

use App\Entity\Exchange;
use App\Model\Libro;
use App\Service\Detector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/arbi")
 */
class ArbitrajeController extends AbstractController
{
    private EntityManagerInterface $em;

    private Detector $detector;

    public function __construct(EntityManagerInterface $em, Detector $detector)
    {
        $this->em = $em;
        $this->detector = $detector;
    }

    /**
     * @Route("/inicio", name="arbi_inicio")
     */
    public function inicio()
    {
        $ordenes = $this->em->getRepository('App\Entity\Orden')->findAll();

        $libro = new Libro($ordenes);
        $this->detector->setLibro($libro);
        $oportunidades = $this->detector->detectarOportunidades();

        return $this->render('arbi/inicio.html.twig', ['oportunidades' => $oportunidades]);
    }
}
