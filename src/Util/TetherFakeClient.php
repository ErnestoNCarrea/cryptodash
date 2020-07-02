<?php

namespace App\Util;

use App\Entity\Orden;
use App\Model\Libro;
use App\Model\TetherFakeExchange;
use App\Util\AbstractClient;

class TetherFakeClient extends AbstractClient
{
    /** @var array */
    private $simbolosAdmitidos = ['USD', 'USDT'];

    /** @var array */
    private $paresAdmitidos = ['USD/USDT'];

    public function __construct(?string $authToken = null)
    {
        $this->exchange = new TetherFakeExchange();
    }

    public function connect()
    { }

    public function getLibro(string $par): ?Libro
    {
        $ordenesCompra =  [new Orden(0, 1, 0)];
        $ordenesVenta = [new Orden(0, 1, 0)];

        return new Libro(array_merge($ordenesCompra, $ordenesVenta), $par);
    }

    public function getPrecioActual(string $par): float
    {
        return (float) 1;
    }

    public function getParesAdmitidos(): array
    {
        return $this->paresAdmitidos;
    }
}
