<?php

namespace App\Util;

interface HasOrderBookInterface
{
    public function getOrderBook(string $pair): ?OrderBook;
}
