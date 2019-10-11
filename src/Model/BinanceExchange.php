<?php

namespace App\Model;

use App\Model\AbstractExchange;
use App\Model\HasOrderBookInterface;

class BinanceExchange extends AbstractExchange
{
    public function __construct()
    {
        $this->name = 'Binance';
        $this->makerFee = 0.00075;
        $this->takerFee = 0.00075;

        $this->depositFees = null;
        $this->withdrawalFees = null;

        $this->infiniteSupply = false;
    }
}
