<?php

namespace App\Util;

use App\Model\BinanceExchange;
use App\Entity\Orden;
use App\Model\Libro;
use App\Entity\Cotizacion;
use App\Util\AbstractClient;
use GuzzleHttp\Client;

class BinanceClient extends AbstractClient
{
    /** @var array */
    private $simbolosAdmitidos = ['USD', 'BTC', 'ETH', 'XRP', 'XLM', 'EOS'];

    /** @var array */
    private $paresAdmitidos = ['BTC/USD', 'ETH/USD', 'BTC/ETH', 'XRP/USD', 'XLM/USD', 'EOS/USD'];

    /** @var Client */
    private $client;

    /** @var string */
    private $authToken;

    /** @var bool */
    private $assumeUsdtIsUsd = true;

    public function __construct(?string $authToken = null)
    {
        $this->exchange = new BinanceExchange();

        $this->authToken = $authToken;

        $this->client = new Client([
            'base_uri' => 'https://api.binance.com/',
            'timeout' => 10,
        ]);
    }

    public function connect()
    {}

    public function getLibro(string $par): ?Libro
    {
        $res = $this->client->request('GET', 'api/v3/avgPrice', [
            'query' => [
                'symbol' => $this->formatearPar($this->convertirUsdAUsdt($par)),
            ],
        ]);

        $ordenesCompra = [new Orden(0, $precio, 0)];
        $ordenesVenta = [new Orden(0, $precio, 0)];

        return new Libro($par, $ordenesCompra, $ordenesVenta);
    }

    public function getPrecioActual(string $par): Cotizacion
    {
        $res = $this->client->request('GET', 'api/v3/avgPrice', [
            'query' => [
                'symbol' => $this->formatearPar($this->convertirUsdAUsdt($par)),
            ],
        ]);

        $res = json_decode((string) $res->getBody());

        return new Cotizacion((float) $res->price, (float) $res->price);
    }

    public function getParesAdmitidos(): array
    {
        return $this->paresAdmitidos;
    }

    /**
     * Format SYM/SYM par to SYMSYM.
     */
    private function formatearPar(string $par): string
    {
        return str_replace('/', '', $par);
    }

    /**
     * Convert USD to USDT if assumeUsdtIsUsd.
     */
    private function convertirUsdAUsdt(string $par): string
    {
        if ($this->assumeUsdtIsUsd) {
            return str_replace('USD', 'USDT', $par);
        } else {
            return $par;
        }
    }

    /**
     * Convert USDT to USD if assumeUsdtIsUsd.
     */
    private function convertirUsdtAUsd(string $par): string
    {
        if ($this->assumeUsdtIsUsd) {
            return str_replace('USDT', 'USD', $par);
        } else {
            return $par;
        }
    }
}
