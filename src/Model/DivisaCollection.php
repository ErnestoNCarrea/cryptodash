<?php

namespace App\Model;

class DivisaCollection
{
    /** @var array */
    private $divisas;

    public function __construct(array $divisas)
    {
        $this->divisas = $divisas;
    }
}
