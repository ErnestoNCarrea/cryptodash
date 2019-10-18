<?php

namespace App\Model;

class Rate
{
    /** @var float */
    private $buy = 0;

    /** @var float */
    private $sell = 0;

    public function __construct(float $buy, float $sell)
    {
        $this->buy = $buy;
        $this->sell = $sell;
    }

    public function spread(): float
    {
        return $this->buy - $this->sell;
    }

    /**
     * @ignore
     */
    public function getBuy(): float
    {
        return $this->buy;
    }

    /**
     * @ignore
     */
    public function setBuy(float $buy)
    {
        $this->buy = $buy;
        return $this;
    }

    /**
     * @ignore
     */
    public function getSell(): float
    {
        return $this->sell;
    }

    /**
     * @ignore
     */
    public function setSell(float $sell)
    {
        $this->sell = $sell;
        return $this;
    }
}
