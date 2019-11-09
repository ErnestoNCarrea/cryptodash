<?php

namespace App\Model;

class Order
{
    /** @var float */
    private $quantity = 0;

    /** @var float */
    private $price = 0;

    /** @var float */
    private $total = 0;

    public function __construct(float $amount, float $price, ?float $total = 0)
    {
        $this->amount = $amount;
        $this->price = $price;
        if (!$total) {
            $this->total = $amount * $price;
        } else {
            $this->total = $total;
        }
    }

    /**
     * Get the value of quantity
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set the value of quantity
     *
     * @return  self
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get the value of price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @return  self
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get the value of total
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set the value of total
     *
     * @return  self
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }
}
