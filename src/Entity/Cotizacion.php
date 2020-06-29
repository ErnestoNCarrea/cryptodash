<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CotizacionRepository")
 */
class Cotizacion
{
    /** @var bool */
    private $activo = false;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Exchange")
     * @ORM\JoinColumn(nullable=false)
     */
    private $exchange;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $par;

    /**
     * @ORM\Column(type="float")
     */
    private float $precioCompra = 0;

    /**
     * @ORM\Column(type="float")
     */
    private float $precioVenta = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTime;

    public function __construct(?float $precioCompra = 0, ?float $precioVenta = 0)
    {
        $this->precioCompra = $precioCompra;
        $this->precioVenta = $precioVenta;
    }

    public function spread() : float
    {
        return $this->precioCompra - $this->precioVenta;
    }

    public function getId() : ?int
    {
        return $this->id;
    }

    public function getExchange() : ?Exchange
    {
        return $this->exchange;
    }

    public function setExchange(?Exchange $exchange) : self
    {
        $this->exchange = $exchange;

        return $this;
    }

    public function getPar() : ?string
    {
        return $this->par;
    }

    public function setPar(string $par) : self
    {
        $this->par = $par;

        return $this;
    }

    public function getPrecioCompra() : float
    {
        return $this->precioCompra;
    }

    public function setPrecioCompra(float $precioCompra) : self
    {
        $this->precioCompra = $precioCompra;

        return $this;
    }

    public function getPrecioVenta() : float
    {
        return $this->precioVenta;
    }

    public function setPrecioVenta(float $precioVenta) : self
    {
        $this->precioVenta = $precioVenta;

        return $this;
    }

    public function getDateTime() : ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime) : self
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get the value of activo
     */
    public function getActivo() : bool
    {
        return $this->activo;
    }

    /**
     * Set the value of activo
     *
     * @return  self
     */
    public function setActivo(bool $activo) : self
    {
        $this->activo = $activo;

        return $this;
    }
}
