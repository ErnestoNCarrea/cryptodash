<?php

namespace App\Util;

use App\Entity\Exchange;
use App\Entity\Order;
use App\Entity\Rate;
use App\Model\OrderBook;
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
        /* @var OrderBook */
        $ob1 = $exchange1->getOrderBookForPair($pair);

        /* @var OrderBook */
        $ob2 = $exchange2->getOrderBookForPair($pair);


        $order1 = $ob1->getBestBuyOrder();
        $order2 = $ob2->getBestSellOrder();

        $price1 = $order1->getPrice();
        $price2 = $order2->getPrice();

        /*if($price1 > $price2) {
        $opr = new Opportunity($exchange1, $exchange2, $pair, $price1, $price2, min($order1->getAmount(), ));
        } elseif($price2 > $price1) {

        } */
    }
}