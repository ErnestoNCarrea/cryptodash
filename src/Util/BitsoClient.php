<?php

namespace App\Util;

use App\Model\Order;
use App\Model\OrderBook;
use App\Model\Rate;
use App\Model\BitsoExchange;
use App\Util\AbstractClient;
use GuzzleHttp\Client;

class BitsoClient extends AbstractClient
{
    /** @var array */
    private $supportedSymbols = ['ARS', 'BTC', 'ETH', 'XRP'];

    /** @var array */
    private $supportedPairs = ['BTC/ARS', 'ETH/BTC', 'XRP/BTC'];

    /** @var Client */
    private $client;

    /** @var string */
    private $authToken;

    public function __construct(?string $authToken = null)
    {
        $this->exchange = new BitsoExchange();

        $this->authToken = $authToken;

        $this->client = new Client([
            'base_uri' => 'https://api.bitso.com/v3/',
            'timeout' => 10,
        ]);
    }

    public function connect()
    {}

    public function getCurrentPrice(string $pair): Rate
    {
        $res = $this->client->request('GET', 'ticker/', [
            'query' => [
                'book' => $this->formatPair($pair),
            ]]);

        $res = json_decode((string) $res->getBody());

        return new Rate((float) $res->payload->bid, (float) $res->payload->ask);
    }

    public function getOrderBook(string $pair): ?OrderBook
    {
        $res = $this->client->request('GET', 'order_book/', [
            'query' => [
                'book' => $this->formatPair($pair)
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
        $buyOrders = $this->decodeOrderCollection($json->payload->bids);
        $sellOrders = $this->decodeOrderCollection($json->payload->asks);

        return new OrderBook($pair, $buyOrders, $sellOrders);
    }

    private function decodeOrderCollection(array $json_orders): array
    {
        $res = [];

        foreach ($json_orders as $json_order) {
            $order = new Order((float) $json_order->amount, (float) $json_order->price);
            $res[] = $order;
        }

        return $res;
    }

    public function getSupportedPairs(): array
    {
        return $this->supportedPairs;
    }

    /**
     * Convert SYM/SYM to the format used by the exchange (SYM_SYM).
     */
    private function formatPair(string $pair): string
    {
        return strtolower(str_replace('/', '_', $pair));
    }
}
