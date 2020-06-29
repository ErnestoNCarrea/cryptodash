<?php

namespace App\Model;

use App\Model\ExchangeInterface;

class RipioExchange extends AbstractExchange
{
    public function __construct()
    {
        $this->nombre = 'Ripio';
        $this->makerFee = 0;
        $this->takerFee = 0;

        $this->depositFees = null;
        $this->withdrawalFees = 0.005;      // 0.5%

        $this->infiniteSupply = false;
    }
}
