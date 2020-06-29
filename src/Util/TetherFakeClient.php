<?php

namespace App\Util;

use App\Entity\Orden;
use App\Model\Libro;
use App\Model\TetherFakeExchange;
use App\Util\AbstractClient;

class TetherFakeClient extends AbstractClient
{
    /** @var array */
    private $supportedSimbolos = ['USD', 'USDT'];

    /** @var array */
    private $supportedPairs = ['USD/USDT'];

    public function __construct(?string $authToken = null)
    {
        $this->exchange = new TetherFakeExchange();
    }

    public function connect()
    { }

    public function getLibro(string $pair): ?Libro
    {
        $ordenesCompra =  [new Orden(0, 1, 0)];
        $ordenesVenta = [new Orden(0, 1, 0)];

        return new Libro($pair, $ordenesCompra, $ordenesVenta);
    }

    public function getCurrentPrice(string $pair): float
    {
        return (float) 1;
    }

    public function getSupportedPairs(): array
    {
        return $this->supportedPairs;
    }
}
