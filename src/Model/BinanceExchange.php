<?php

namespace App\Model;

use App\Model\AbstractExchange;
use App\Model\HasLibroInterface;

class BinanceExchange extends AbstractExchange
{
    public function __construct()
    {
        $this->name = 'Binance';
        $this->makerFee = 0.001;        // 0.1%
        $this->takerFee = 0.001;        // 0.1%

        $this->depositFees = null;
        $this->withdrawalFees = null;

        $this->infiniteSupply = false;
    }
}
