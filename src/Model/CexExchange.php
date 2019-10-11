<?php

namespace App\Model;

use App\Model\ExchangeInterface;

class CexExchange extends AbstractExchange implements HasOrderBookInterface
{
    public function __construct()
    {
        $this->name = 'CEX';
        $this->makerFee = 0.0035;
        $this->takerFee = 0.0016;

        $this->depositFees = [
            'USD' => 0.03
        ];
        $this->withdrawalFees = null;

        $this->infiniteSupply = false;
    }
}
