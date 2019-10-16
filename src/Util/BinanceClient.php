<?php

namespace App\Util;

use GuzzleHttp\Client;
use App\Model\BinanceExchange;
use App\Model\Order;
use App\Model\OrderBook;
use App\Util\AbstractClient;

class BinanceClient extends AbstractClient
{
    /** @var array */
    private $supportedSymbols = ['USD', 'BTC', 'ETH '];

    /** @var array */
    private $suppoertedPairs = ['BTC/USD', 'ETH/USD', 'BTC/ETH'];

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
            'timeout'  => 10,
        ]);
    }

    public function connect()
    { }

    public function getOrderBook(string $pair): ?OrderBook
    {
        $res = $this->client->request('GET', 'api/v3/avgPrice', [
            'query' => [
                'symbol' => $this->formatPair($this->convertUsdToUsdt($pair))
            ],
        ]);

        $buyOrders =  [new Order(0, $price, 0)];
        $sellOrders = [new Order(0, $price, 0)];

        return new OrderBook($pair, $buyOrders, $sellOrders);
    }

    public function getCurrentPrice(string $pair): float
    {
        $res = $this->client->request('GET', 'api/v3/avgPrice', [
            'query' => [
                'symbol' => $this->formatPair($this->convertUsdToUsdt($pair))
            ],
        ]);

        $res = json_decode((string) $res->getBody());

        return (float) $res->price;
    }

    public function getSupportedPairs(): array
    {
        return $this->suppoertedPairs;
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
