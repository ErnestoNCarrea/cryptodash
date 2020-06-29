<?php

namespace App\Util;

use App\Entity\Exchange;
use App\Entity\Orden;
use App\Entity\Rate;
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

    public function findOpportunities(string $pair) {
        foreach($this->exchanges as $exchange1) {
            foreach($this->exchanges as $exchange2) {
                if($exchange1 != $exchange2) {
                    $ops = $this->findOpportunitiesBetweenExchanges($exchange1, $exchange2, $pair);
                }
            }
        }
    }

    private function findOpportunitiesBetweenExchanges($exchange1, $exchange2, $pair)
    {
        /* @var Libro */
        $ob1 = $exchange1->getLibroForPair($pair);

        /* @var Libro */
        $ob2 = $exchange2->getLibroForPair($pair);


        $order1 = $ob1->getBestOrdenCompra();
        $order2 = $ob2->getBestOrdenVenta();

        $price1 = $order1->getPrice();
        $price2 = $order2->getPrice();

        /*if($price1 > $price2) {
        $opr = new Opportunity($exchange1, $exchange2, $pair, $price1, $price2, min($order1->getAmount(), ));
        } elseif($price2 > $price1) {

        } */
    }
}