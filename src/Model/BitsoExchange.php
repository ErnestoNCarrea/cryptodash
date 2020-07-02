<?php

namespace App\Model;

use App\Model\ExchangeInterface;

class BitsoExchange extends AbstractExchange
{
    public function __construct()
    {
        $this->nombre = 'Bitso';
        $this->makerFee = 0.002; //0.2%
        $this->takerFee = 0.002; //0.2%

        $this->depositFees = null;
        $this->withdrawalFees = null;

        $this->suministroInfinito = false;
    }
}
