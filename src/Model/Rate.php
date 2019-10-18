<?php

namespace App\Model;

class Rate
{
    /** @var float */
    private $buyPrice = 0;

    /** @var float */
    private $sellPrice = 0;

    public function __construct(float $buyPrice, float $sellPrice)
    {
        $this->buyPrice = $buyPrice;
        $this->sellPrice = $sellPrice;
    }

    public function spread(): float
    {
        return $this->buyPrice - $this->sellPrice;
    }

    /**
     * @ignore
     */
    public function getBuyPrice(): float
    {
        return $this->buyPrice;
    }

    /**
     * @ignore
     */
    public function setBuyPrice(float $buyPrice)
    {
        $this->buyPrice = $buyPrice;
        return $this;
    }

    /**
     * @ignore
     */
    public function getSellPrice(): float
    {
        return $this->sellPrice;
    }

    /**
     * @ignore
     */
    public function setSellPrice(float $sellPrice)
    {
        $this->sellPrice = $sellPrice;
        return $this;
    }
}
