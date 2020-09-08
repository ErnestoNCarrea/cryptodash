<?php

namespace App\Util;

use App\Entity\Orden;
use App\Model\Libro;
use App\Entity\Cotizacion;
use App\Model\UsdExchange;
use App\Util\AbstractClient;
use GuzzleHttp\Client;

/**
 * Es un exchange falso que permite convertir entre dólares y criptodólares.
 */
class UsdClient extends AbstractClient
{
    /** @var array */
    private $simbolosAdmitidos = ['USD', 'USDT', 'USDC'];

    /** @var array */
    private $paresAdmitidos = ['USDT/USD', 'USDC/USD', 'USDT/USDC'];

    public function __construct(?string $authToken = null)
    {
        $this->exchange = new UsdExchange();
    }

    public function connect()
    {}

    public function getLibro(string $par): ?Libro
    {
        // 100 millones, a 0.5% de diferencia (1% entre puntas)
        $ordenesCompra =  [new Orden(100000000, 0.995, $par, Orden::LADO_COMPRA)];
        $ordenesVenta = [new Orden(100000000, 1.005, $par, Orden::LADO_VENTA)];

        return new Libro(array_merge($ordenesCompra, $ordenesVenta), $par);
    }

    public function getPrecioActual(string $par): Cotizacion
    {
        return new Cotizacion((float) 0.995, (float) 1.005);
    }

    public function getParesAdmitidos(): array
    {
        return $this->paresAdmitidos;
    }
}
