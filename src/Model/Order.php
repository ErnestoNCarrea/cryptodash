<?php

namespace App\Model;

class Order
{
    /** @var float */
    private $amount = 0;

    /** @var float */
    private $price = 0;

    /** @var float */
    private $total = 0;

    public function __construct(float $amount, float $price, ?float $total = 0)
    {
        $this->amount = $amount;
        $this->price = $price;
        $this->total = $total;
    }
}
