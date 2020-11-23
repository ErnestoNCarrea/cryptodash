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
    private $simbolosAdmitidos = ['ARS', 'BTC', 'ETH', 'XRP', 'USD', 'DAI'];

    /** @var array */
    private $paresAdmitidos = ['BTC/ARS', 'ETH/BTC', 'XRP/BTC', 'BTC/USD', 'ETH/USD', 'XRP/USD', 'BTC/DAI', 'DAI/ARS'];

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

    public function getPrecioActual(string $par): Cotizacion
    {
        $res = $this->client->request('GET', 'ticker/', [
            'query' => [
                'book' => $this->formatearPar($par),
            ]]);

        $res = json_decode((string) $res->getBody());

        return new Cotizacion((float) $res->payload->bid, (float) $res->payload->ask);
    }

    public function getLibro(string $par): ?Libro
    {
        $res = $this->client->request('GET', 'order_book/', [
            'query' => [
                'book' => $this->formatearPar($par)
            ],
        ]);

        if ($res->getStatusCode() === 200) {
            return $this->deserializarLibro($par, json_decode((string) $res->getBody()));
        } else {
            return null;
        }
    }

    private function deserializarLibro(string $par, object $json): Libro
    {
        $ordenesCompra = $this->deserializarOrdenCollection($json->payload->bids, $par, Orden::LADO_COMPRA);
        $ordenesVenta = $this->deserializarOrdenCollection($json->payload->asks, $par, Orden::LADO_VENTA);

        return new Libro(array_merge($ordenesCompra, $ordenesVenta), $par);
    }

    private function deserializarOrdenCollection(array $json_orders, string $par, int $lado): array
    {
        $res = [];

        foreach ($json_orders as $json_order) {
            $order = new Orden((float) $json_order->amount, (float) $json_order->price, $par);
            $order->setLado($lado);
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
    private function formatearPar(string $par): string
    {
        return strtolower(str_replace('/', '_', $par));
    }
}
