<?php

namespace App\Util;

use App\Entity\Exchange;
use App\Entity\Orden;
use App\Entity\Cotizacion;
use App\Model\Libro;
use App\Model\Opportunity;

class AnalizadorCotizaciones
{
    /** @var Exchange[] */
    private $exchanges;

    private $mainCurrencies = ['ARS', 'USD'];

    public function __construct(array $exchanges)
    {
        $this->exchanges = $exchanges;
    }

    public function getExchanges() : array
    {
        return $this->exchanges;
    }

    public function obtenerCotizacionesParaSimbolo(string $simbolo) {
        $cotizaciones = [];
        
        foreach($this->exchanges as $exchange) {
            /* @var Libro */
            $cotizaciones[$exchange->getId()] = $exchange->obtenerCotizacionesParaSimbolo($simbolo);    
        }

        return $cotizaciones;
    }

    /**
     * Devuelve la diferencia de precio con Binance para un par /USD.
     */
    public function obtenerDiferenciaConReferencia(Cotizacion $coti, int $lado) : float
    {
        $exchRef = $this->obtenerExchangeReferenciaUsd();
        $cotisRef = $exchRef->obtenerCotizacionesParaSimbolo($coti->getDivisaBase());
        foreach($cotisRef as $cotiRef) {
            if ($cotiRef->getDivisaPrecio() == $coti->getDivisaPrecio()) {
                if ($lado == 1) {
                    $di = 1 - $coti->getPrecioCompra() / $cotiRef->getPrecioCompra();
                } else {
                    $di = 1 - $coti->getPrecioVenta() / $cotiRef->getPrecioVenta();
                }
                return $di;
            }
        }

        // Si no se encuentra la misma divisa, buscar USD alternativos
        foreach($cotisRef as $cotiRef) {
            if ($cotiRef->getDivisaPrecio() == 'USD' 
                || $cotiRef->getDivisaPrecio() == 'USDC'
                || $cotiRef->getDivisaPrecio() == 'USDT'
                || $cotiRef->getDivisaPrecio() == 'TUSD')
            {
                if ($lado == 1) {
                    $di = 1 - $coti->getPrecioCompra() / $cotiRef->getPrecioCompra();
                } else {
                    $di = 1 - $coti->getPrecioVenta() / $cotiRef->getPrecioVenta();
                }
                return $di;
            }
        }

        return 0;
    }

    /**
     * Devuelve el dólar implícito para un par /ARS, con base en la cotización de Binance.
     */
    public function obtenerDolarImplicito(Cotizacion $coti, int $lado) : float
    {
        $parInverso = $this->intercambiarArsUsd($coti->getPar());
        $exchRef = $this->obtenerExchangeReferenciaUsd();
        $cotisRef = $exchRef->obtenerCotizacionesParaSimbolo($coti->getDivisaBase());
        foreach($cotisRef as $cotiRef) {
            if ($cotiRef->getDivisaPrecio() == 'USD' 
                || $cotiRef->getDivisaPrecio() == 'USDC'
                || $cotiRef->getDivisaPrecio() == 'USDT'
                || $cotiRef->getDivisaPrecio() == 'TUSD')
            {
                if ($lado == 1) {
                    $di = $coti->getPrecioCompra() / $cotiRef->getPrecioCompra();
                } else {
                    $di = $coti->getPrecioVenta() / $cotiRef->getPrecioVenta();
                }
                return $di;
            }
        }

        return 0;
    }

    private function obtenerExchangeReferenciaUsd() : ?Exchange
    {
        foreach($this->exchanges as $ex) {
            if ($ex->getId() == 1000) {
                return $ex;
            }
        }
    }

    /**
     * Devuelve verdadero si un par tiene su precio en pesos argentinos.
     */
    public function parEsEnArs(string $par) :bool
    {
        return strpos($par, '/ARS') !== false;
    }

    /**
     * Devuelve un par en USD a partir de un par en ARS y viceversa.
     */
    public function intercambiarArsUsd(string $par) : string
    {
        if($this->parEsEnArs($par)) {
            return str_replace('/ARS', '/USD', $par);
        } elseif($this->parEsEnArs($par)) {
            return str_replace('/USDC', '/ARS', 
                str_replace('/USDT', '/ARS', 
                str_replace('/TUSD', '/ARS', 
                str_replace('/USD', '/ARS', $ars
                ))));
        } else {
            return $par;
        }
    }

    /**
     * Devuelve verdadero si un par tiene su precio en dólares (o sus variantes).
     */
    public function parEsEnUsd(string $par) :bool
    {
        return strpos($par, '/USD') !== false;
    }

    public function obtenerPrecioReferenciaEnUsd($par) : float
    {
        return 0;
    }
}