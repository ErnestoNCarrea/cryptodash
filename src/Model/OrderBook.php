<?php

namespace App\Model;

use App\Model\Order;

class OrderBook
{
    /** @var string */
    private $pair;

    /** @var Order[]|null */
    private $buyOrders = null;

    /** @var Order[]|null */
    private $sellOrders = null;

    public function __construct(string $pair, ?array $buyOrders = null, ?array $sellOrders = null)
    {
        $this->pair = $pair;
        $this->buyOrders = $buyOrders;
        $this->sellOrders = $sellOrders;
    }

    public function getBestBuyOrder(?float $fillAmount = null): Order
    {
        // FIXME: fillAmount
        return $this->buyOrders[0];
    }

    public function getBestSellOrder(?float $fillAmount = null): Order
    {
        // FIXME: fillAmount
        return $this->sellOrders[0];
    }

    public function getBestBuyPrice(?float $fillAmount = null): float
    {
        return $this->getBestBuyOrder($fillAmount)->getPrice();
    }

    public function getBestSellPrice(?float $fillAmount = null): float
    {
        return $this->getBestSellOrder($fillAmount)->getPrice();
    }

    /**
     * Get the value of pair
     */
    public function getPair()
    {
        return $this->pair;
    }

    /**
     * Set the value of pair
     *
     * @return  self
     */
    public function setPair($pair)
    {
        $this->pair = $pair;

        return $this;
    }

    /**
     * Get the value of buyOrders
     */
    public function getBuyOrders()
    {
        return $this->buyOrders;
    }

    /**
     * Set the value of buyOrders
     *
     * @return  self
     */
    public function setBuyOrders($buyOrders)
    {
        $this->buyOrders = $buyOrders;

        return $this;
    }

    /**
     * Get the value of sellOrders
     */
    public function getSellOrders()
    {
        return $this->sellOrders;
    }

    /**
     * Set the value of sellOrders
     *
     * @return  self
     */
    public function setSellOrders($sellOrders)
    {
        $this->sellOrders = $sellOrders;

        return $this;
    }
}
