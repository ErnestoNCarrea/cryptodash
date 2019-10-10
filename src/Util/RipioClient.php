<?php

namespace App\Util;

use GuzzleHttp\Client;
use App\Model\Order;
use App\Model\OrderBook;

class RipioClient
{
    /** @var array */
    private $suppoertedSymbols = ['ARS', 'BTC', 'ETH '];

    /** @var array */
    private $suppoertedPairs = ['BTC/ARS', 'ETH/ARS'];

    /** @var Client */
    private $client;

    /** @var string */
    private $authToken;

    public function __construct(?string $authToken = null)
    {
        $this->authToken = $authToken;

        $this->client = new Client([
            'base_uri' => 'https://api.exchange.ripio.com/api/v1/',
            'timeout'  => 10,
        ]);
    }

    public function getOrderBook(string $pair): ?OrderBook
    {
        $res = $this->client->request('GET', 'orderbook/' . urlencode(str_replace('/', '_', $pair)), [
            'headers' => [
                'Accept' => '*/*',
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->authToken,
            ]
        ]);

        if ($res->getStatusCode() === 200) {
            return $this->decodeOrderBook($pair, json_decode((string) $res->getBody()));
        } else {
            return null;
        }
    }

    private function decodeOrderBook(string $pair, object $json): OrderBook
    {
        $buyOrders = $this->decodeOrderCollection($json->buy);
        $sellOrders = $this->decodeOrderCollection($json->sell);

        return new OrderBook($pair, $buyOrders, $sellOrders);
    }

    private function decodeOrderCollection(array $json_orders): array
    {
        $res = [];

        foreach ($json_orders as $json_order) {
            $order = new Order((float) $json_order->amount, (float) $json_order->price, (float) $json_order->total);
            $res[] = $order;
        }

        return $res;
    }


    public function getSupportedPairs(): array
    {
        return $this->suppoertedPairs;
    }

    public function api_getPairs(): array
    {
        $res = $this->client->request('GET', 'pair/', [
            'query' => [
                'country' => 'AR'
            ],
            'headers' => [
                'Accept' => '*/*',
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->authToken,
            ]
        ]);

        return json_decode((string) $res->getBody());
    }
}
