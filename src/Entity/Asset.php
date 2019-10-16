<?php

namespace App\Entity;

class Asset
{
    /** @var string */
    private $symbol;

    /** @var string */
    private $name;

    public function __construct(string $symbol, string $name)
    {
        $this->symbol = $symbol;
        $this->name = $name;
    }
}
