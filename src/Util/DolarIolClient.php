<?php

namespace App\Util;

use App\Model\DolarIolExchange;
use App\Model\Order;
use App\Model\OrderBook;
use App\Util\AbstractClient;

class DolarIolClient extends AbstractClient
{
    /** @var array */
    private $supportedSymbols = ['ARS', 'USD'];

    /** @var array */
    private $suppoertedPairs = ['USD/ARS'];

    public function __construct(?string $authToken = null)
    {
        $this->exchange = new DolarIolExchange();
    }

    public function connect()
    { }

    public function getOrderBook(string $pair): ?OrderBook
    {
        $dolar = 59 * 1.05;

        $buyOrders = [new Order(999, $dolar, 0)];
        $sellOrders = [new Order(999, $dolar, 0)];

        return new OrderBook($pair, $buyOrders, $sellOrders);
    }

    public function getSupportedPairs(): array
    {
        return $this->suppoertedPairs;
    }
}
