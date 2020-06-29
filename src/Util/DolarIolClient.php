<?php

namespace App\Util;

use App\Model\DolarIolExchange;
use App\Model\Orden;
use App\Model\Libro;
use App\Model\Rate;
use App\Util\AbstractClient;

class DolarIolClient extends AbstractClient
{
    /** @var array */
    private $supportedSymbols = ['ARS', 'USD'];

    /** @var array */
    private $supportedPairs = ['USD/ARS'];

    public function __construct(?string $authToken = null)
    {
        $this->exchange = new DolarIolExchange();
    }

    public function connect()
    {}

    public function getCurrentPrice(?string $pair = null): Rate
    {
        $dolar = (float) $_ENV['DOLAR'];

        return new Rate($dolar, $dolar);
    }

    public function getLibro(?string $pair = null): ?Libro
    {
        $dolar = (float) $_ENV['DOLAR'];

        $ordenesCompra = [new Orden(999, $dolar, 0)];
        $ordenesVenta = [new Orden(999, $dolar, 0)];

        return new Libro($pair, $ordenesCompra, $ordenesVenta);
    }

    public function getSupportedPairs(): array
    {
        return $this->supportedPairs;
    }
}
