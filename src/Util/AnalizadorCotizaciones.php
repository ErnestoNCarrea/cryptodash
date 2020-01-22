<?php

namespace App\Util;

use App\Entity\Exchange;
use App\Entity\Order;
use App\Entity\Rate;
use App\Model\OrderBook;
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
            /* @var OrderBook */
            $rates[$exchange->getId()] = $exchange->getAllRatesForSymbol($symbol);    
        }

        return $rates;
    }
}