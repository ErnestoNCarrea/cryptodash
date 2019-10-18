<?php

namespace App\Util;

use App\Model\Order;
use App\Model\OrderBook;
use App\Model\Rate;
use App\Model\RipioExchange;
use App\Util\AbstractClient;
use GuzzleHttp\Client;

class RipioClient extends AbstractClient
{
    /** @var array */
    private $supportedSymbols = ['ARS', 'BTC', 'ETH '];

    /** @var array */
    private $suppoertedPairs = ['BTC/ARS', 'ETH/ARS'];

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
        $res = $this->client->request('GET', 'https://ripio.com/api/v1/rates/');

        $res = json_decode((string) $res->getBody());

        return new Rate((float) $res->rates->ARS_BUY, (float) $res->rates->ARS_SELL);
    }

    public function getOrderBook(string $pair): ?OrderBook
    {
        $res = $this->client->request('GET', 'orderbook/' . urlencode($this->formatPair($pair)), [
            'headers' => [
                'Accept' => '*/*',
                'Content-type' => 'application/json',
            ],
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
     * Format SYM/SYM pair to SYM_SYM.
     */
    private function formatPair(string $pair): string
    {
        return str_replace('/', '_', $pair);
    }
}
