<?php

namespace App\Util;

use App\Entity\Orden;
use App\Model\Libro;
use App\Model\Rate;
use App\Model\RipioExchange;
use App\Util\AbstractClient;
use GuzzleHttp\Client;

class RipioClient extends AbstractClient
{
    /** @var array */
    private $supportedSymbols = ['ARS', 'BTC', 'ETH', 'USDC'];

    /** @var array */
    private $supportedPairs = ['BTC/ARS', 'ETH/ARS', 'USDC/ARS', 'BTC/USDC', 'ETH/USDC'];

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

    public function getCurrentPrice(string $pair): Rate
    {
        [$pairBase, $pairQuote] = explode('/', $pair);

        $res = $this->client->request('GET', 'https://ripio.com/api/v1/rates/', [
            'query' => [
                'base' => $pairBase,
            ]]);

        $res = json_decode((string) $res->getBody());

        if($pairQuote == 'USDC') {
            // A Ripio le preguntás por USDC y en algunos lugares responde USD
            $pairQuote = 'USD';
        }
        $pairQuote_BUY = $pairQuote . '_BUY';
        $pairQuote_SELL = $pairQuote . '_SELL';

        //echo "$pair \n\n";
        //if($pair == 'BTC/USDC')
        //print_r($res);
        return new Rate((float) $res->rates->$pairQuote_BUY, (float) $res->rates->$pairQuote_SELL);
    }

    public function getLibro(string $pair): ?Libro
    {
        $res = $this->client->request('GET', 'orderbook/' . urlencode($this->formatPair($pair)), [
            'headers' => [
                'Accept' => '*/*',
                'Content-type' => 'application/json',
            ],
        ]);

        if ($res->getStatusCode() === 200) {
            return $this->decodeLibro($pair, json_decode((string) $res->getBody()));
        } else {
            return null;
        }
    }

    private function decodeLibro(string $pair, object $json): Libro
    {
        $ordenesCompra = $this->decodeOrdenCollection($json->buy);
        $ordenesVenta = $this->decodeOrdenCollection($json->sell);

        return new Libro($pair, $ordenesCompra, $ordenesVenta);
    }

    private function decodeOrdenCollection(array $json_orders): array
    {
        $res = [];

        foreach ($json_orders as $json_order) {
            $order = new Orden((float) $json_order->amount, (float) $json_order->price, (float) $json_order->total);
            $res[] = $order;
        }

        return $res;
    }

    public function getSupportedPairs(): array
    {
        return $this->supportedPairs;
    }

    public function api_getPairs(): array
    {
        $res = $this->client->request('GET', 'pair/', [
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
    private function formatPair(string $pair): string
    {
        return str_replace('/', '_', $pair);
    }
}
