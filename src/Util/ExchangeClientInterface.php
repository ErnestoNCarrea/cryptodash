<?php

namespace App\Util;

use App\Model\ExchangeInterface;
use App\Entity\Orden;
use App\Model\Libro;

interface ExchangeClientInterface
{
    public function connect();

    public function getExchange(): ExchangeInterface;
    public function getLibro(string $par): ?Libro;
    public function getParesAdmitidos(): array;
}
