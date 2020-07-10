<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('divisa_simbolo', [$this, 'divisaSimbolo']),
        ];
    }

    public function divisaSimbolo($divisaNombre)
    {
        switch($divisaNombre) {
            case 'BTC':
                return '₿';
            case 'ARS':
                return '$';
            case 'ETH':
                return 'Ξ';
            default:
                return $divisaNombre;
        }

        return $price;
    }
}