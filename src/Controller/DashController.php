<?php

namespace App\Controller;

use App\Entity\Exchange;
use App\Util\AnalizadorRipio;
use App\Util\DolarIolClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dash")
 */
class DashController extends AbstractController
{
    //** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/ripio", name="dash_ripio")
     */
    public function ripio()
    {
        /** @var Exchange */
        $exchangeRipio = $this->em->getRepository('App\Entity\Exchange')->find(9000);
        /** @var Exchange */
        $exchangeBinance = $this->em->getRepository('App\Entity\Exchange')->find(1000);
        $clientDolar = new DolarIolClient();

        $analizador = new AnalizadorRipio($exchangeRipio, $exchangeBinance, $clientDolar->getCurrentPrice('USD/ARS'));

        //dump($analizador);

        return $this->render('dash/ripio.html.twig', ['analizador' => $analizador]);
    }
}
