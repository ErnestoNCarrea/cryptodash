<?php

namespace App\Util;

use App\Model\ExchangeInterface;
use App\Model\Order;
use App\Model\OrderBook;

interface ExchangeClientInterface
{
    public function connect();

    public function getExchange(): ExchangeInterface;
    public function getOrderBook(string $pair): ?OrderBook;
    public function getSupportedPairs(): array;
}
