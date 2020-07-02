<?php

namespace App\Util;

use App\Model\DolarIolExchange;
use App\Entity\Orden;
use App\Model\Libro;
use App\Entity\Cotizacion;
use App\Util\AbstractClient;

class DolarIolClient extends AbstractClient
{
    /** @var array */
    private $simbolosAdmitidos = ['ARS', 'USD'];

    /** @var array */
    private $paresAdmitidos = ['USD/ARS'];

    public function __construct(?string $authToken = null)
    {
        $this->exchange = new DolarIolExchange();
    }

    public function connect()
    {}

    public function getPrecioActual(?string $par = null): Cotizacion
    {
        $dolar = (float) $_ENV['DOLAR'];

        return new Cotizacion($dolar, $dolar);
    }

    public function getLibro(?string $par = null): ?Libro
    {
        $dolar = (float) $_ENV['DOLAR'];

        $ordenesCompra = [new Orden(999, $dolar, 0)];
        $ordenesVenta = [new Orden(999, $dolar, 0)];

        return new Libro($par, $ordenesCompra, $ordenesVenta);
    }

    public function getParesAdmitidos(): array
    {
        return $this->paresAdmitidos;
    }
}
