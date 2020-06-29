<?php

namespace App\Util;

use App\Entity\Orden;
use App\Model\Libro;
use App\Entity\Cotizacion;
use App\Model\BitsoExchange;
use App\Util\AbstractClient;
use GuzzleHttp\Client;

class BitsoClient extends AbstractClient
{
    /** @var array */
    private $simbolosAdmitidos = ['ARS', 'BTC', 'ETH', 'XRP'];

    /** @var array */
    private $paresAdmitidos = ['BTC/ARS', 'ETH/BTC', 'XRP/BTC'];

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

    public function getCurrentPrecio(string $par): Cotizacion
    {
        $res = $this->client->request('GET', 'ticker/', [
            'query' => [
                'libro' => $this->formatPar($par),
            ]]);

        $res = json_decode((string) $res->getBody());

        return new Cotizacion((float) $res->payload->bid, (float) $res->payload->ask);
    }

    public function getLibro(string $par): ?Libro
    {
        $res = $this->client->request('GET', 'order_libro/', [
            'query' => [
                'libro' => $this->formatPar($par)
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
        $ordenesCompra = $this->decodeOrdenCollection($json->payload->bids);
        $ordenesVenta = $this->decodeOrdenCollection($json->payload->asks);

        return new Libro($par, $ordenesCompra, $ordenesVenta);
    }

    private function decodeOrdenCollection(array $json_orders): array
    {
        $res = [];

        foreach ($json_orders as $json_order) {
            $order = new Orden((float) $json_order->amount, (float) $json_order->precio);
            $res[] = $order;
        }

        return $res;
    }

    public function getParesAdmitidos(): array
    {
        return $this->paresAdmitidos;
    }

    /**
     * Convert SYM/SYM to the format used by the exchange (SYM_SYM).
     */
    private function formatPar(string $par): string
    {
        return strtolower(str_replace('/', '_', $par));
    }
}
