<?php

namespace App\Util;

use App\Entity\Exchange;
use App\Entity\Orden;
use App\Entity\Rate;
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

    public function getAllRatesForSymbol(string $symbol) {
        $rates = [];
        
        foreach($this->exchanges as $exchange) {
            /* @var Libro */
            $rates[$exchange->getId()] = $exchange->getAllRatesForSymbol($symbol);    
        }

        return $rates;
    }
}