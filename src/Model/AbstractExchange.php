<?php

namespace App\Model;

abstract class AbstractExchange implements ExchangeInterface
{
    /** @var string */
    private $nombre;

    /** @var float */
    private $takerFee = 0;

    /** @var float */
    private $makerFee = 0;

    /** @var array */
    private $depositFees;

    /** @var array */
    private $withdrawFees;

    /** @var bool */
    private $infinteSupply = false;

    public function infiniteSupply(): bool
    {
        return $this->infinteSupply;
    }
}
