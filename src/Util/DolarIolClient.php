<?php

namespace App\Util;

use App\Model\Order;
use App\Model\OrderBook;
use App\Util\ExchangeClientInterface;

class DolarIolClient implements ExchangeClientInterface
{
    /** @var array */
    private $suppoertedSymbols = ['ARS', 'USD'];

    /** @var array */
    private $suppoertedPairs = ['USD/ARS'];

    public function __construct(?string $authToken = null)
    { }

    public function getOrderBook(string $pair): ?OrderBook
    {
        $dolar = 59;

        $buyOrders =  [new Order(999, $dolar, 0)];
        $sellOrders = [new Order(999, $dolar, 0)];

        return new OrderBook($pair, $buyOrders, $sellOrders);
    }

    public function getSupportedPairs(): array
    {
        return $this->suppoertedPairs;
    }
}
