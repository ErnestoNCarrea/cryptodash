<?php

namespace App\Util;

use GuzzleHttp\Client;

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

    public function __construct(string $authToken)
    {
        $this->authToken = $authToken;

        $this->client = new Client([
            'base_uri' => 'https://exchange.ripio.com/api/',
            'timeout'  => 10,
        ]);
    }

    public function getRates()
    {
        $res = $this->client->request('GET', 'https://api.exchange.ripio.com/api/v1/', [
            'headers' => [
                'Content-type' => 'application/json',
            ]
        ]);
    }

    public function getOrders(string $pair)
    {
        $res = $this->client->request('GET', 'orders?state=open&pair=' . urlencode(str_replace('/', '', $pair)), [
            'headers' => [
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->authToken,
            ]
        ]);
    }

    public function getPairs()
    {
        $res = $this->client->request('GET', 'pair?country=AR', [
            'headers' => [
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->authToken,
            ]
        ]);
    }
}
