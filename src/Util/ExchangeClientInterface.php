<?php

namespace App\Util;

use App\Model\Order;
use App\Model\OrderBook;

interface ExchangeClientInterface
{
    public function getOrderBook(string $pair): ?OrderBook;
    public function getSupportedPairs(): array;
}
