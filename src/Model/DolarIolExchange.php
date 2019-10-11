<?php

namespace App\Model;

use App\Model\ExchangeInterface;

class DolarIolExchange extends AbstractExchange
{
    public function __construct()
    {
        $this->name = 'Dólar IOL';
        $this->makerFee = 0;
        $this->takerFee = 0;

        $this->depositFees = null;
        $this->withdrawalFees = null;

        $this->infiniteSupply = true;
    }
}
