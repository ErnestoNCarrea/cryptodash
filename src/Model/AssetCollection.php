<?php

namespace App\Model;

class AssetCollection
{
    /** @var array */
    private $assets;

    public function __construct(array $assets)
    {
        $this->assets = $assets;
    }
}
