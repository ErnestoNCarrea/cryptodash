<?php

namespace App\Util;

use GuzzleHttp\Client;
use App\Model\Order;
use App\Model\OrderBook;

class BinanceClient
{
    /** @var array */
    private $suppoertedSymbols = ['USD', 'BTC', 'ETH '];

    /** @var array */
    private $suppoertedPairs = ['BTC/USD', 'ETH/USD', 'BTC/ETH'];

    /** @var Client */
    private $client;

    /** @var string */
    private $authToken;

    public function __construct(?string $authToken = null)
    {
        $this->authToken = $authToken;

        $this->client = new Client([
            'base_uri' => 'https://...',
            'timeout'  => 10,
        ]);
    }

    public function getOrderBook(string $pair): ?OrderBook
    {
        switch ($pair) {
            case 'BTC/USD':
                $price = 8550;
                break;
            case 'ETH/USD':
                $price = 191;
                break;
            case 'BTC/USD':
                $price = 191 / 8550;
                break;
        }
        $dolar = 58.4;

        $buyOrders =  [new Order(999, $price * .99, 0)];
        $sellOrders = [new Order(999, $price * 1.01, 0)];

        return new OrderBook($pair, $buyOrders, $sellOrders);
    }

    public function getSupportedPairs(): array
    {
        return $this->suppoertedPairs;
    }
}
