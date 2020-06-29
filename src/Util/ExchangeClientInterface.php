<?php

namespace App\Util;

use App\Model\ExchangeInterface;
use App\Model\Orden;
use App\Model\Libro;

interface ExchangeClientInterface
{
    public function connect();

    public function getExchange(): ExchangeInterface;
    public function getLibro(string $pair): ?Libro;
    public function getSupportedPairs(): array;
}
