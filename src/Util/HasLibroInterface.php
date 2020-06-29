<?php

namespace App\Util;

interface HasLibroInterface
{
    public function getLibro(string $par): ?Libro;
}
