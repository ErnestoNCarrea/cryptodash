<?php

namespace App\Controller;

use App\Entity\Oportunidad;
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
        $oportunidades = $this->em->getRepository(Oportunidad::class)->findBy([], [ 'activa' => 'DESC', 'fecha' => 'DESC'], 100);
        return $this->render('arbi/inicio.html.twig', ['oportunidades' => $oportunidades]);
    }

    /**
     * @Route("/ver/{id}", name="arbi_ver")
     */
    public function ver(Oportunidad $opor)
    {
        return $this->render('arbi/ver.html.twig', ['opor' => $opor]);
    }
}
