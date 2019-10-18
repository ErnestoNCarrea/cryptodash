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
     * @Route("/ripio", name="ripio")
     */
    public function ripio()
    {
        /** @var Exchange */
        $exchangeRipio = $this->em->getRepository('App\Entity\Exchange')->find(9000);
        /** @var Exchange */
        $exchangeBinance = $this->em->getRepository('App\Entity\Exchange')->find(1000);
        $clientDolar = new DolarIolClient();

        $analizador = new AnalizadorRipio($exchangeRipio, $exchangeBinance, $clientDolar->getCurrentPrice('USD/ARS'));

        /* $clientRipio = new RipioClient('8f2104688f50a866fe648be370c9d80ef04d2203c59a1dc5ee8eea7118a94e6f');
        $clientBinance = new BinanceClient();

        $analizador = new AnalizadorRipio(
        $clientRipio->getOrderBook('BTC/ARS'),
        $clientRipio->getOrderBook('ETH/ARS'),
        $clientDolar->getOrderBook('USD/ARS'),
        $clientBinance->getCurrentPrice('BTC/USD'),
        $clientBinance->getCurrentPrice('ETH/USD'),
        $clientBinance->getCurrentPrice('ETH/BTC')
        ); */

        dump($analizador);

        return $this->render('dash/ripio.html.twig', ['analizador' => $analizador]);
    }
}
