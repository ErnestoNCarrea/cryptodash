<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Util\RipioClient;
use App\Util\DolarIolClient;

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
        $clientRipio = new RipioClient('8f2104688f50a866fe648be370c9d80ef04d2203c59a1dc5ee8eea7118a94e6f');
        $clientDolar = new DolarIolClient();

        //$pairs = $client->getPairs();
        $ordersRipio = $clientRipio->getOrderBook('BTC/ARS');
        $ordersDolar = $clientDolar->getOrderBook('USD/ARS');

        dump($ordersRipio);
        dump($ordersDolar);

        return $this->render('dash/inicio.html.twig', ['orders_ripio' => $ordersRipio, 'orders_dolar' =>  $ordersDolar]);
    }
}
