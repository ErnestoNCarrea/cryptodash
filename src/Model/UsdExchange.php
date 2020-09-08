<?php

namespace App\Model;

use App\Model\ExchangeInterface;

class UsdExchange extends AbstractExchange
{
    public function __construct()
    {
        $this->nombre = 'Exchange virtual USD';
        $this->makerFee = 0.01;
        $this->takerFee = 0.01;

        $this->depositFees = null;
        $this->withdrawalFees = null;

        $this->suministroInfinito = true;
    }
}
