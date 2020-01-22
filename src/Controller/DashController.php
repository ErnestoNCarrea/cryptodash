<?php

namespace App\Controller;

use App\Entity\Exchange;
use App\Util\AnalizadorRipio;
use App\Util\AnalizadorArbitrajes;
use App\Util\AnalizadorCotizaciones;
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

    /**
     * @Route("/arbi", name="dash_arbi")
     */
    public function arbi()
    {
        /** @var Exchange */
        $exchangeRipio = $this->em->getRepository('App\Entity\Exchange')->find(9000);

        /** @var Exchange */
        $exchangeBuenbit = $this->em->getRepository('App\Entity\Exchange')->find(9002);

        $analizador = new AnalizadorArbitrajes([$exchangeRipio, $exchangeBuenbit]);

        return $this->render('dash/arbi.html.twig', ['analizador' => $analizador]);
    }

    /**
     * @Route("/coti", name="dash_coti")
     */
    public function coti()
    {
        $exchangesIds = [1000, 9000, 9003, 9002];
        $exchanges = [];
        foreach($exchangesIds as $exchangeId) {
            $exchanges[] = $this->em->getRepository('App\Entity\Exchange')->find($exchangeId);
        }

        $clientDolar = new DolarIolClient();

        $analizador = new AnalizadorCotizaciones($exchanges);
        $reference = $exchanges[0];

        return $this->render('dash/coti.html.twig', [
            'analizador' => $analizador,
            'reference' => $reference,
            'dolar' => $clientDolar
            ]);
    }
}
