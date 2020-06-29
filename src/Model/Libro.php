<?php

namespace App\Model;

use App\Entity\Orden;

class Libro
{
    /** @var string */
    private $par;

    /** @var Orden[]|null */
    private $ordenesCompra = null;

    /** @var Orden[]|null */
    private $ordenesVenta = null;

    public function __construct(string $par, ?array $ordenesCompra = null, ?array $ordenesVenta = null)
    {
        $this->par = $par;
        $this->ordenesCompra = $ordenesCompra ? $ordenesCompra : [];
        $this->ordenesVenta = $ordenesVenta ? $ordenesVenta : [];
    }

    public function addOrdenCompra(Orden $order): self
    {
        $this->ordenesCompra[] = $order;
        return $this;
    }

    public function addOrdenVenta(Orden $order): self
    {
        $this->ordenesVenta[] = $order;
        return $this;
    }

    public function getBestOrdenCompra(?float $fillAmount = null): ?Orden
    {
        if ($this->ordenesCompra && count($this->ordenesCompra) > 0) {
            // FIXME: fillAmount
            return $this->ordenesCompra[0];
        } else {
            return null;
        }
    }

    public function getBestOrdenVenta(?float $fillAmount = null): ?Orden
    {
        if ($this->ordenesCompra && count($this->ordenesCompra) > 0) {
            // FIXME: fillAmount
            return $this->ordenesVenta[0];
        } else {
            return null;
        }
    }

    public function getBestPrecioCompra(?float $fillAmount = null): ?float
    {
        $order = $this->getBestOrdenCompra($fillAmount);
        if ($order) {
            return $order->getPrecio();
        } else {
            return null;
        }
    }

    public function getBestPrecioVenta(?float $fillAmount = null): ?float
    {
        $order = $this->getBestOrdenVenta($fillAmount);
        if ($order) {
            return $order->getPrecio();
        } else {
            return null;
        }
    }

    /**
     * Get the value of par
     */
    public function getPar()
    {
        return $this->par;
    }

    /**
     * Set the value of par
     *
     * @return  self
     */
    public function setPar($par)
    {
        $this->par = $par;

        return $this;
    }

    /**
     * Get the value of ordenesCompra
     */
    public function getOrdenesCompra()
    {
        return $this->ordenesCompra;
    }

    /**
     * Set the value of ordenesCompra
     *
     * @return  self
     */
    public function setOrdenesCompra($ordenesCompra)
    {
        $this->ordenesCompra = $ordenesCompra;

        return $this;
    }

    /**
     * Get the value of ordenesVenta
     */
    public function getOrdenesVenta()
    {
        return $this->ordenesVenta;
    }

    /**
     * Set the value of ordenesVenta
     *
     * @return  self
     */
    public function setOrdenesVenta($ordenesVenta)
    {
        $this->ordenesVenta = $ordenesVenta;

        return $this;
    }
}
