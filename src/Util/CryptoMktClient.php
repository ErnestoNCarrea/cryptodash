<?php

namespace App\Util;

use App\Model\BinanceExchange;
use App\Entity\Orden;
use App\Model\Libro;
use App\Model\Rate;
use App\Util\AbstractClient;
use GuzzleHttp\Client;

class CryptoMktClient extends AbstractClient
{
    /** @var array */
    private $supportedSymbols = ['ARS', 'BTC', 'ETH', 'EOS', 'XLM'];

    /** @var array */
    private $supportedPairs = ['BTC/ARS', 'ETH/ARS', 'XML/ARS', 'EOS/ARS'];

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

    public function getLibro(string $pair): ?Libro
    {
        $res = $this->client->request('GET', 'book', [
            'query' => [
                'market' => $this->formatPair($pair),
                'type' => 'buy'
            ],
        ]);
        $ordenesCompra = $this->decodeOrdenCollection(json_decode((string) $res->getBody()));

        $res = $this->client->request('GET', 'book', [
            'query' => [
                'market' => $this->formatPair($pair),
                'type' => 'sell'
            ],
        ]);
        $ordenesVenta = $this->decodeOrdenCollection(json_decode((string) $res->getBody()));
        return new Libro($pair, $ordenesCompra, $ordenesVenta);
    }

    private function decodeOrdenCollection($json_orders): array
    {
        $res = [];

        foreach ($json_orders->data as $json_order) {
            $order = new Orden((float) $json_order->amount, (float) $json_order->price);
            $res[] = $order;
        }

        return $res;
    }

    public function getCurrentPrice(string $pair): Rate
    {
        $res = $this->client->request('GET', 'ticker', [
            'query' => [
                'market' => $this->formatPair($pair),
                'timeframe' => 5        // 5 minutos
            ],
        ]);

        $res = json_decode((string) $res->getBody());

        return new Rate((float) $res->data[0]->bid, (float) $res->data[0]->ask);
    }

    public function getSupportedPairs(): array
    {
        return $this->supportedPairs;
    }

    /**
     * Format SYM/SYM pair to SYMSYM.
     */
    private function formatPair(string $pair): string
    {
        return str_replace('/', '', $pair);
    }
}
