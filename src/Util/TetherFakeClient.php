<?php

namespace App\Util;

use App\Model\Order;
use App\Model\OrderBook;
use App\Model\TetherFakeExchange;
use App\Util\AbstractClient;

class TetherFakeClient extends AbstractClient
{
    /** @var array */
    private $suppoertedSymbols = ['USD', 'USDT'];

    /** @var array */
    private $suppoertedPairs = ['USD/USDT'];

    public function __construct(?string $authToken = null)
    {
        $this->exchange = new TetherFakeExchange();
    }

    public function getOrderBook(string $pair): ?OrderBook
    {
        $buyOrders =  [new Order(0, 1, 0)];
        $sellOrders = [new Order(0, 1, 0)];

        return new OrderBook($pair, $buyOrders, $sellOrders);
    }

    public function getCurrentPrice(string $pair): float
    {
        return (float) 1;
    }

    public function getSupportedPairs(): array
    {
        return $this->suppoertedPairs;
    }
}
