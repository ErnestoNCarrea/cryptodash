<?php

namespace App\Model;

use App\Model\ExchangeInterface;

class RipioExchange extends AbstractExchange
{
    public function __construct()
    {
        $this->name = 'Ripio';
        $this->makerFee = 0;
        $this->takerFee = 0.05;

        $this->depositFees = null;
        $this->withdrawalFees = null;

        $this->infiniteSupply = false;
    }
}
