<?php

namespace App\Model;

use App\Model\ExchangeInterface;

class BuenbitExchange extends AbstractExchange
{
    public function __construct()
    {
        $this->name = 'Buenbit';
        $this->makerFee = 0.0035;       // 0.35%
        $this->takerFee = 0.0035;       // 0.35%

        $this->depositFees = 0.006;     // 0.6%
        $this->withdrawalFees = 0.006;  // 0.6%

        $this->infiniteSupply = false;
    }
}
