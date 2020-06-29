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

    public function getAllCotizacionesForSimbolo(string $simbolo) {
        $cotizaciones = [];
        
        foreach($this->exchanges as $exchange) {
            /* @var Libro */
            $cotizaciones[$exchange->getId()] = $exchange->getAllCotizacionesForSimbolo($simbolo);    
        }

        return $cotizaciones;
    }
}