<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Util\RipioClient;
use App\Util\DolarIolClient;
use App\Util\BinanceClient;
use App\Util\AnalizadorRipio;

/**
 * @Route("/dash")
 */
class DashController extends AbstractController
{
    /**
     * @Route("/ripio")
     */
    public function ripio()
    {
        $clientRipio = new RipioClient('8f2104688f50a866fe648be370c9d80ef04d2203c59a1dc5ee8eea7118a94e6f');
        $clientDolar = new DolarIolClient();
        $clientBinance = new BinanceClient();

        $analizador = new AnalizadorRipio(
            $clientRipio->getOrderBook('BTC/ARS'),
            $clientRipio->getOrderBook('ETH/ARS'),
            $clientDolar->getOrderBook('USD/ARS'),
            $clientBinance->getCurrentPrice('BTC/USD'),
            $clientBinance->getCurrentPrice('ETH/USD'),
            $clientBinance->getCurrentPrice('ETH/BTC')
        );

        dump($analizador);

        return $this->render('dash/ripio.html.twig', ['analizador' => $analizador]);
    }
}
