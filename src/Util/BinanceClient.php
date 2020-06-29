<?php

namespace App\Util;

use App\Model\BinanceExchange;
use App\Model\Orden;
use App\Model\Libro;
use App\Model\Rate;
use App\Util\AbstractClient;
use GuzzleHttp\Client;

class BinanceClient extends AbstractClient
{
    /** @var array */
    private $supportedSymbols = ['USD', 'BTC', 'ETH', 'XRP', 'XLM', 'EOS'];

    /** @var array */
    private $supportedPairs = ['BTC/USD', 'ETH/USD', 'BTC/ETH', 'XRP/USD', 'XLM/USD', 'EOS/USD'];

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

    public function getLibro(string $pair): ?Libro
    {
        $res = $this->client->request('GET', 'api/v3/avgPrice', [
            'query' => [
                'symbol' => $this->formatPair($this->convertUsdToUsdt($pair)),
            ],
        ]);

        $ordenesCompra = [new Orden(0, $price, 0)];
        $ordenesVenta = [new Orden(0, $price, 0)];

        return new Libro($pair, $ordenesCompra, $ordenesVenta);
    }

    public function getCurrentPrice(string $pair): Rate
    {
        $res = $this->client->request('GET', 'api/v3/avgPrice', [
            'query' => [
                'symbol' => $this->formatPair($this->convertUsdToUsdt($pair)),
            ],
        ]);

        $res = json_decode((string) $res->getBody());

        return new Rate((float) $res->price, (float) $res->price);
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

    /**
     * Convert USD to USDT if assumeUsdtIsUsd.
     */
    private function convertUsdToUsdt(string $pair): string
    {
        if ($this->assumeUsdtIsUsd) {
            return str_replace('USD', 'USDT', $pair);
        } else {
            return $pair;
        }
    }

    /**
     * Convert USDT to USD if assumeUsdtIsUsd.
     */
    private function convertUsdtToUsd(string $pair): string
    {
        if ($this->assumeUsdtIsUsd) {
            return str_replace('USDT', 'USD', $pair);
        } else {
            return $pair;
        }
    }
}
