<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FormatExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('divisa_simbolo', [$this, 'divisaSimbolo']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('precio', [$this, 'precio']),
        ];
    }

    public function divisaSimbolo(string  $divisaNombre) : string
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
    }

    public function precio(float $precio, string $divisa) : string
    {
        switch($divisa) {
            case 'BTC':
                return '₿ ' . number_format($precio, 8);
            case 'ARS':
                return '$ ' . number_format($precio, 2);
            case 'ETH':
                return 'Ξ ' . number_format($precio, 6);
            default:
                return $divisa . ' ' . number_format($precio, 2);
        }
    }
}