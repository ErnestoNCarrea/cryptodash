<?php

namespace App\Util;

use Ratchet\Client;
use App\Model\Order;
use App\Model\OrderBook;
use App\Model\RipioExchange;
use App\Util\AbstractWsClient;

class RipioWsClient extends AbstractWsClient
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
            'base_uri' => 'https://exchange.ripio.com/ws',
            'timeout'  => 10,
        ]);
    }

    public function connect()
    {
        $loop = \React\EventLoop\Factory::create();
        $reactConnector = new \React\Socket\Connector($loop, [
            'timeout' => 10
        ]);
        $connector = new \Ratchet\Client\Connector($loop, $reactConnector);
    }

    public function getOrderBook(string $pair): ?OrderBook
    {
        $res = $this->client->request('GET', 'orderbook/' . urlencode($this->formatPair($pair)), [
            'headers' => [
                'Accept' => '*/*',
                'Content-type' => 'application/json',
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

    /**
     * Format SYM/SYM pair to SYM_SYM.
     */
    private function formatPair(string $pair): string
    {
        return str_replace('/', '_', $pair);
    }
}
