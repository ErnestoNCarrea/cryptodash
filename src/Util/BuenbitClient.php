<?php

namespace App\Util;

use App\Entity\Orden;
use App\Model\Libro;
use App\Entity\Cotizacion;
use App\Model\BuenbitExchange;
use App\Util\AbstractClient;
use GuzzleHttp\Client;

class BuenbitClient extends AbstractClient
{
    /** @var array */
    private $simbolosAdmitidos = ['ARS', 'BTC', 'ETH', 'DAI'];

    /** @var array */
    private $paresAdmitidos = ['BTC/ARS', 'ETH/ARS', 'DAI/ARS', 'BTC/DAI', 'DAI/ARS'];

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

    public function getCurrentPrecio(string $par): Cotizacion
    {
        $res = $this->client->request('GET', 'tickers');

        $res = json_decode((string) $res->getBody());

        $parname = $this->formatPar($par);

        return new Cotizacion((float) $res->$parname->ticker->buy, (float) $res->$parname->ticker->sell);
    }

    public function getLibro(string $par): ?Libro
    {
        $res = $this->client->request('GET', 'order_libro', [
            'query' => [
                'market' => urlencode($this->formatPar($par)),
                'asks_limit' => 15,
                'bids_limit' => 15
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ],
        ]);

        if ($res->getStatusCode() === 200) {
            return $this->decodeLibro($par, json_decode((string) $res->getBody()));
        } else {
            return null;
        }
    }

    private function decodeLibro(string $par, object $json): Libro
    {
        $ordenesCompra = $this->decodeOrdenCollection($json->bids);
        $ordenesVenta = $this->decodeOrdenCollection($json->asks);

        return new Libro($par, $ordenesCompra, $ordenesVenta);
    }

    private function decodeOrdenCollection(array $json_orders): array
    {
        $res = [];

        foreach ($json_orders as $json_order) {
            $order = new Orden((float) $json_order->remaining_volume, (float) $json_order->precio);
            $res[] = $order;
        }

        return $res;
    }

    public function getParesAdmitidos(): array
    {
        return $this->paresAdmitidos;
    }

    public function api_getPares(): array
    {
        $res = $this->client->request('GET', 'par/', [
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
    private function formatPar(string $par): string
    {
        return strtolower(str_replace('/', '', $par));
    }
}
