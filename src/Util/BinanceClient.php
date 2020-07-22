<?php

namespace App\Util;

use App\Model\BinanceExchange;
use App\Entity\Orden;
use App\Model\Libro;
use App\Entity\Cotizacion;
use App\Util\AbstractClient;
use GuzzleHttp\Client;

class BinanceClient extends AbstractClient
{
    /** @var array */
    private $simbolosAdmitidos = ['USDC', 'BTC', 'ETH', 'XRP', 'XLM', 'EOS'];

    /** @var array */
    private $paresAdmitidos = ['BTC/USDC', 'ETH/USDC', 'BTC/ETH', 'XRP/USDC', 'XLM/USDC', 'EOS/USDC'];

    /** @var Client */
    private $client;

    /** @var string */
    private $authToken;

    /** @var bool */
    private $assumeUsdtIsUsd = true;

    public function __construct(?string $authToken = null)
    {
        $this->exchange = new BinanceExchange();

        $this->authToken = $authToken;

        $this->client = new Client([
            'base_uri' => 'https://api.binance.com/',
            'timeout' => 10,
        ]);
    }

    public function connect()
    {}

    public function getLibro(string $par): ?Libro
    {
        $res = $this->client->request('GET', 'api/v3/depth', [
            'query' => [
                'symbol' => $this->formatearPar($par),
                'limit' => 50,
            ],
        ]);

        if ($res->getStatusCode() === 200) {
            return $this->deserializarLibro($par, json_decode((string) $res->getBody()));
        } else {
            return null;
        }
    }

    private function deserializarLibro(string $par, object $json): Libro
    {
        $ordenesCompra = $this->deserializarOrdenCollection($json->bids, $par, Orden::LADO_COMPRA);
        $ordenesVenta = $this->deserializarOrdenCollection($json->asks, $par, Orden::LADO_VENTA);

        return new Libro(array_merge($ordenesCompra, $ordenesVenta), $par);
    }

    private function deserializarOrdenCollection(array $json_orders, string $par, int $lado): array
    {
        $res = [];

        foreach ($json_orders as $json_order) {
            $order = new Orden((float) $json_order[1], (float) $json_order[0], $par);
            $order->setLado($lado);
            $res[] = $order;
        }

        return $res;
    }

    public function getPrecioActual(string $par): Cotizacion
    {
        $res = $this->client->request('GET', 'api/v3/avgPrice', [
            'query' => [
                'symbol' => $this->formatearPar($par),
            ],
        ]);

        $res = json_decode((string) $res->getBody());

        return new Cotizacion((float) $res->price, (float) $res->price);
    }

    public function getParesAdmitidos(): array
    {
        return $this->paresAdmitidos;
    }

    /**
     * Format SYM/SYM par to SYMSYM.
     */
    private function formatearPar(string $par): string
    {
        return str_replace('/', '', $par);
    }

    /**
     * Convert USD to USDT if assumeUsdtIsUsd.
     */
    private function convertirUsdAUsdt(string $par): string
    {
        if ($this->assumeUsdtIsUsd) {
            return str_replace('USD', 'USDT', $par);
        } else {
            return $par;
        }
    }

    /**
     * Convert USDT to USD if assumeUsdtIsUsd.
     */
    private function convertirUsdtAUsd(string $par): string
    {
        if ($this->assumeUsdtIsUsd) {
            return str_replace('USDT', 'USD', $par);
        } else {
            return $par;
        }
    }
}
