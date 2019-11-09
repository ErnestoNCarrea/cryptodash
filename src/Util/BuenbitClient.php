<?php

namespace App\Util;

use App\Model\Order;
use App\Model\OrderBook;
use App\Model\Rate;
use App\Model\BuenbitExchange;
use App\Util\AbstractClient;
use GuzzleHttp\Client;

class BuenbitClient extends AbstractClient
{
    /** @var array */
    private $supportedSymbols = ['ARS', 'BTC', 'ETH', 'DAI'];

    /** @var array */
    private $suppoertedPairs = ['BTC/ARS', 'ETH/ARS', 'DAI/ARS', 'BTC/DAI', 'DAI/ARS'];

    /** @var Client */
    private $client;

    /** @var string */
    private $authToken;

    public function __construct(?string $authToken = null)
    {
        $this->exchange = new BuenbitExchange();

        $this->authToken = $authToken;

        $this->client = new Client([
            'base_uri' => 'https://exchange.buenbit.com/api/v2/',
            'timeout' => 10,
        ]);
    }

    public function connect()
    {}

    public function getCurrentPrice(string $pair): Rate
    {
        $res = $this->client->request('GET', 'tickers');

        $res = json_decode((string) $res->getBody());

        $pairname = $this->formatPair($pair);

        return new Rate((float) $res->$pairname->ticker->buy, (float) $res->$pairname->ticker->sell);
    }

    public function getOrderBook(string $pair): ?OrderBook
    {
        $res = $this->client->request('GET', 'order_book', [
            'query' => [
                'market' => urlencode($this->formatPair($pair)),
                'asks_limit' => 15,
                'bids_limit' => 15
            ],
            'headers' => [
                'Accept' => 'application/json',
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
        $buyOrders = $this->decodeOrderCollection($json->bids);
        $sellOrders = $this->decodeOrderCollection($json->asks);

        return new OrderBook($pair, $buyOrders, $sellOrders);
    }

    private function decodeOrderCollection(array $json_orders): array
    {
        $res = [];

        foreach ($json_orders as $json_order) {
            $order = new Order((float) $json_order->remaining_volume, (float) $json_order->price);
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
     * Convert SYM/SYM to the format used by the exchange (symsym).
     */
    private function formatPair(string $pair): string
    {
        return strtolower(str_replace('/', '', $pair));
    }
}
