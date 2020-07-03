<?php

namespace App\Controller;

use App\Entity\Exchange;
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
     * @Route("/ver", name="arbi_ver")
     */
    public function ver()
    {
        $ordenes = $this->em->getRepository('App\Entity\Orden')->findBy(
            ['activo' => 1]
        );

        $libro = new OrderCollection($ordenes);
        $this->detector->setLibro($libro);
        
        $oportunidades = $this->detector->detectarOportunidades();

        //return $this->render('arbi/ver.html.twig', ['analizador' => $analizador]);
    }
}
