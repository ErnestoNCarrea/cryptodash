<?php

namespace App\Model;

use App\Model\ExchangeInterface;

class CexExchange extends AbstractExchange implements HasLibroInterface
{
    public function __construct()
    {
        $this->name = 'CEX';
        $this->makerFee = 0.0035;
        $this->takerFee = 0.0016;

        $this->depositFees = [
            'USD' => 0.031
        ];
        $this->withdrawalFees = [
            'BTC' => 0.00005,
            'ETH' => 0.01,
            'XRP' => 0.02,
        ];

        $this->infiniteSupply = false;
    }
}
