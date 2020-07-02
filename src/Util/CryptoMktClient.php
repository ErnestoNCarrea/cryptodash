<?php

namespace App\Util;

use App\Model\BinanceExchange;
use App\Entity\Orden;
use App\Model\Libro;
use App\Entity\Cotizacion;
use App\Util\AbstractClient;
use GuzzleHttp\Client;

class CryptoMktClient extends AbstractClient
{
    /** @var array */
    private $simbolosAdmitidos = ['ARS', 'BTC', 'ETH', 'EOS', 'XLM'];

    /** @var array */
    private $paresAdmitidos = ['BTC/ARS', 'ETH/ARS', 'XML/ARS', 'EOS/ARS'];

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
            'base_uri' => 'https://api.cryptomkt.com/v1/',
            'timeout' => 10,
        ]);
    }

    public function connect()
    {}

    public function getLibro(string $par): ?Libro
    {
        $res = $this->client->request('GET', 'book', [
            'query' => [
                'market' => $this->formatearPar($par),
                'type' => 'buy'
            ],
        ]);
        $ordenesCompra = $this->deserializarOrdenCollection(json_decode((string) $res->getBody()));

        $res = $this->client->request('GET', 'book', [
            'query' => [
                'market' => $this->formatearPar($par),
                'type' => 'sell'
            ],
        ]);
        $ordenesVenta = $this->deserializarOrdenCollection(json_decode((string) $res->getBody()));
        return new Libro($par, $ordenesCompra, $ordenesVenta);
    }

    private function deserializarOrdenCollection($json_orders): array
    {
        $res = [];

        foreach ($json_orders->data as $json_order) {
            $order = new Orden((float) $json_order->amount, (float) $json_order->price);
            $res[] = $order;
        }

        return $res;
    }

    public function getPrecioActual(string $par): Cotizacion
    {
        $res = $this->client->request('GET', 'ticker', [
            'query' => [
                'market' => $this->formatearPar($par),
                'timeframe' => 5        // 5 minutos
            ],
        ]);

        $res = json_decode((string) $res->getBody());

        return new Cotizacion((float) $res->data[0]->bid, (float) $res->data[0]->ask);
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
}
