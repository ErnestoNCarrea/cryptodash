<?php

namespace App\Model;

use App\Model\AbstractExchange;
use App\Model\HasLibroInterface;

class CryptoMktExchange extends AbstractExchange
{
    public function __construct()
    {
        $this->nombre = 'CryptoMkt';
        $this->makerFee = 0.0048;        // 0.48%
        $this->takerFee = 0.0068;        // 0.68%

        $this->depositFees = null;
        $this->withdrawalFees = null;

        $this->infiniteSupply = false;
    }
}
