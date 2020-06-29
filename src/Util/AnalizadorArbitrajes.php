<?php

namespace App\Util;

use App\Entity\Exchange;
use App\Entity\Orden;
use App\Entity\Cotizacion;
use App\Model\Libro;
use App\Model\Opportunity;

class AnalizadorArbitrajes
{
    /** @var Exchange[] */
    private $exchanges;

    public function __construct(array $exchanges)
    {
        $this->exchanges = $exchanges;
    }

    public function findOpportunities(string $par) {
        foreach($this->exchanges as $exchange1) {
            foreach($this->exchanges as $exchange2) {
                if($exchange1 != $exchange2) {
                    $ops = $this->findOpportunitiesBetweenExchanges($exchange1, $exchange2, $par);
                }
            }
        }
    }

    private function findOpportunitiesBetweenExchanges($exchange1, $exchange2, $par)
    {
        /* @var Libro */
        $ob1 = $exchange1->getLibroForPar($par);

        /* @var Libro */
        $ob2 = $exchange2->getLibroForPar($par);


        $order1 = $ob1->getBestOrdenCompra();
        $order2 = $ob2->getBestOrdenVenta();

        $precio1 = $order1->getPrecio();
        $precio2 = $order2->getPrecio();

        /*if($precio1 > $precio2) {
        $opr = new Opportunity($exchange1, $exchange2, $par, $precio1, $precio2, min($order1->getAmount(), ));
        } elseif($precio2 > $precio1) {

        } */
    }
}