<?php

namespace App\Util;

interface HasLibroInterface
{
    public function getLibro(string $pair): ?Libro;
}
