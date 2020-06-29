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
    private $par;

    /** @var float */
    private $precio1;

    /** @var float */
    private $precio2;

    /** @var float */
    private $amount;

    public function __construct(Exchange $exchange1, Exchange $exchange2, string $par, float $precio1, float $precio2, float $amount)
    {
        $this->exchange1 = $exchange1;
        $this->exchange2 = $exchange2;
        $this->par = $par;
        $this->precio1 = $precio1;
        $this->precio2 = $precio2;
    }
}