<?php

namespace App\Model;

class Exchange
{
    /** @var string */
    private $name;

    /** @var float */
    private $takerFee = 0;

    /** @var float */
    private $makerFee = 0;

    /** @var array */
    private $depositFees;

    /** @var array */
    private $withdrawFees;

    public function __construct(string $name)
    {
    }
}
