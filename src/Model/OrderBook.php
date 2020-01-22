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
        $this->buyOrders = $buyOrders ? $buyOrders : [];
        $this->sellOrders = $sellOrders ? $sellOrders : [];
    }

    public function addBuyOrder(Order $order): self
    {
        $this->buyOrders[] = $order;
        return $this;
    }

    public function addSellOrder(Order $order): self
    {
        $this->sellOrders[] = $order;
        return $this;
    }

    public function getBestBuyOrder(?float $fillAmount = null): ?Order
    {
        if ($this->buyOrders && count($this->buyOrders) > 0) {
            // FIXME: fillAmount
            return $this->buyOrders[0];
        } else {
            return null;
        }
    }

    public function getBestSellOrder(?float $fillAmount = null): ?Order
    {
        if ($this->buyOrders && count($this->buyOrders) > 0) {
            // FIXME: fillAmount
            return $this->sellOrders[0];
        } else {
            return null;
        }
    }

    public function getBestBuyPrice(?float $fillAmount = null): ?float
    {
        $order = $this->getBestBuyOrder($fillAmount);
        if ($order) {
            return $order->getPrice();
        } else {
            return null;
        }
    }

    public function getBestSellPrice(?float $fillAmount = null): ?float
    {
        $order = $this->getBestSellOrder($fillAmount);
        if ($order) {
            return $order->getPrice();
        } else {
            return null;
        }
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
