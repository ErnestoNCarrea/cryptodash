<?php

namespace App\Util;

use App\Entity\Orden;
use App\Model\Libro;
use App\Entity\Cotizacion;
use App\Model\RipioExchange;
use App\Util\AbstractClient;
use GuzzleHttp\Client;

class RipioClient extends AbstractClient
{
    /** @var array */
    private $simbolosAdmitidos = ['ARS', 'BTC', 'ETH', 'USDC'];

    /** @var array */
    private $paresAdmitidos = ['BTC/ARS', 'ETH/ARS', 'USDC/ARS', 'BTC/USDC', 'ETH/USDC', 'ETH/BTC'];

    /** @var Client */
    private $client;

    /** @var string */
    private $authToken;

    public function __construct(?string $authToken = null)
    {
        $this->exchange = new RipioExchange();

        $this->authToken = $authToken;

        $this->client = new Client([
            'base_uri' => 'https://api.exchange.ripio.com/api/v1/',
            'timeout' => 10,
        ]);
    }

    public function connect()
    {}

    public function getPrecioActual(string $par): Cotizacion
    {
        [$parBase, $parQuote] = explode('/', $par);

        $res = $this->client->request('GET', 'https://ripio.com/api/v1/rates/', [
            'query' => [
                'base' => $parBase,
            ]]);

        $res = json_decode((string) $res->getBody());

        if($parQuote == 'USDC') {
            // A Ripio le preguntás por USDC y en algunos lugares responde USD
            $parQuote = 'USD';
        }
        $parQuote_BUY = $parQuote . '_BUY';
        $parQuote_SELL = $parQuote . '_SELL';

        //echo "$par \n\n";
        //if($par == 'BTC/USDC')
        //print_r($res);
        return new Cotizacion((float) $res->rates->$parQuote_BUY, (float) $res->rates->$parQuote_SELL);
    }

    public function getLibro(string $par): ?Libro
    {
        $res = $this->client->request('GET', 'orderbook/' . urlencode($this->formatearPar($par)), [
            'headers' => [
                'Accept' => '*/*',
                'Content-type' => 'application/json',
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
        $ordenesCompra = $this->deserializarOrdenCollection($json->buy, $par, Orden::LADO_COMPRA);
        $ordenesVenta = $this->deserializarOrdenCollection($json->sell, $par, Orden::LADO_VENTA);

        return new Libro(array_merge($ordenesCompra, $ordenesVenta), $par);
    }

    private function deserializarOrdenCollection(array $json_orders, string $par, int $lado): array
    {
        $res = [];

        foreach ($json_orders as $json_order) {
            $order = new Orden((float) $json_order->amount, (float) $json_order->price, $par);
            $order->setLado($lado);
            $res[] = $order;
        }

        return $res;
    }

    public function getParesAdmitidos(): array
    {
        return $this->paresAdmitidos;
    }

    public function api_getPares(): array
    {
        $res = $this->client->request('GET', 'par/', [
            'query' => [
                'country' => 'AR',
            ],
            'headers' => [
                'Accept' => '*/*',
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->authToken,
            ],
        ]);

        return json_decode((string) $res->getBody());
    }

    /**
     * Convert SYM/SYM to the format used by the exchange (SYM_SYM).
     */
    private function formatearPar(string $par): string
    {
        return str_replace('/', '_', $par);
    }
}
