<?php

namespace App\Model;

use App\Entity\Exchange;

class Opportunity
{
    /** @var Exchange */
    private $exchange1;

    /** @var Exchange */
    private $exchange2;

    /** @var string */
    private $pair;

    /** @var float */
    private $price1;

    /** @var float */
    private $price2;

    /** @var float */
    private $amount;

    public function __construct(Exchange $exchange1, Exchange $exchange2, string $pair, float $price1, float $price2, float $amount)
    {
        $this->exchange1 = $exchange1;
        $this->exchange2 = $exchange2;
        $this->pair = $pair;
        $this->price1 = $price1;
        $this->price2 = $price2;
    }
}