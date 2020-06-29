<?php

namespace App\Model;

class Divisa
{
    /** @var string */
    private $simbolo;

    /** @var string */
    private $nombre;

    public function __construct(string $simbolo, string $nombre)
    {
        $this->simbolo = $simbolo;
        $this->nombre = $nombre;
    }
}
