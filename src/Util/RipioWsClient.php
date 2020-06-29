<?php

namespace App\Util;

use Ratchet\Client;
use App\Entity\Orden;
use App\Model\Libro;
use App\Model\RipioExchange;
use App\Util\AbstractWsClient;

class RipioWsClient extends AbstractWsClient
{
    /** @var array */
    private $simbolosAdmitidos = ['ARS', 'BTC', 'ETH '];

    /** @var array */
    private $paresAdmitidos = ['BTC/ARS', 'ETH/ARS'];

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

    public function getLibro(string $par): ?Libro
    {
        $res = $this->client->request('GET', 'orderlibro/' . urlencode($this->formatPar($par)), [
            'headers' => [
                'Accept' => '*/*',
                'Content-type' => 'application/json',
            ]
        ]);

        if ($res->getStatusCode() === 200) {
            return $this->decodeLibro($par, json_decode((string) $res->getBody()));
        } else {
            return null;
        }
    }

    private function decodeLibro(string $par, object $json): Libro
    {
        $ordenesCompra = $this->decodeOrdenCollection($json->buy);
        $ordenesVenta = $this->decodeOrdenCollection($json->sell);

        return new Libro($par, $ordenesCompra, $ordenesVenta);
    }

    private function decodeOrdenCollection(array $json_orders): array
    {
        $res = [];

        foreach ($json_orders as $json_order) {
            $order = new Orden((float) $json_order->amount, (float) $json_order->precio, (float) $json_order->total);
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
     * Format SYM/SYM par to SYM_SYM.
     */
    private function formatPar(string $par): string
    {
        return str_replace('/', '_', $par);
    }
}
