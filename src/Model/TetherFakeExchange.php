<?php

namespace App\Model;

class TetherFakeExchange extends AbstractExchange
{
    public function __construct()
    {
        $this->nombre = 'TetherFakeExchange';
        $this->makerFee = 0;
        $this->takerFee = 0;

        $this->depositFees = null;
        $this->withdrawalFees = null;

        $this->infiniteSupply = false;
    }
}
