<?php

namespace App\Util;

use App\Model\ExchangeInterface;
use App\Util\ExchangeClientInterface;

abstract class AbstractClient implements ExchangeClientInterface
{
    /** @var ExchangeInterface */
    private $exchange;

    /**
     * Get the value of exchange
     */
    public function getExchange(): ExchangeInterface
    {
        return $this->exchange;
    }

    /**
     * Set the value of exchange
     *
     * @return  self
     */
    public function setExchange(ExchangeInterface $exchange)
    {
        $this->exchange = $exchange;

        return $this;
    }
}
