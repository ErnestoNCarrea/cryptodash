<?php

namespace App\Util;

use App\Model\Order;
use App\Model\OrderBook;

class DolarIolClient
{
    /** @var array */
    private $suppoertedSymbols = ['ARS', 'USD'];

    /** @var array */
    private $suppoertedPairs = ['USD/ARS'];

    public function __construct(?string $authToken = null)
    { }

    public function getOrderBook(string $pair): ?OrderBook
    {
        $dolar = 58.4;

        $buyOrders =  [new Order(999999, 1 / $dolar, 0)];
        $sellOrders = [new Order(999999, $dolar, 0)];

        return new OrderBook($pair, $buyOrders, $sellOrders);
    }

    public function getPairs()
    {
        return $this->suppoertedPairs;
    }
}
