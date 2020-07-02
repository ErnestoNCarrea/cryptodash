<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Exchange;
use App\Entity\Usuario;

/**
 * Representa una orden de mercado.
 * 
 * @ORM\Entity(repositoryClass="App\Repository\OrdenRepository")
 */
class Orden
{
    public const LADO_NINGUNO = 0;
    public const LADO_COMPRA = 1;
    public const LADO_VENTA = 2;
    public const LADOS_NOMBRES = [
        self::LADO_NINGUNO => 'Ninguno',
        self::LADO_COMPRA => 'Compra',
        self::LADO_VENTA => 'Venta',
    ];

    private bool $activo = false;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * El exchange al cual pertenece esta orden.
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Exchange")
     * @ORM\JoinColumn(nullable=false)
     */
    private Exchange $exchange;

    /**
     * El par en formato "divisa_base/divisa_precio".
     * 
     * @ORM\Column(type="string", length=255)
     */
    private string $par;

    /**
     * El precio de la orden, expresada en divisa_precio.
     * @ORM\Column(type="decimal", precision=16, scale=8)
     */
    private float $precio = 0;

    /**
     * La cantidad, expresada en divisa_base.
     * 
     * @ORM\Column(type="decimal", precision=16, scale=8)
     */
    private float $cantidad = 0;

    /**
     * El lado (comprador o vendedor).
     * 
     * @ORM\Column(type="smallint")
     */
    private int $lado = 0;

    /**
     * La fecha en la cual se registró la orden.
     * 
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $fecha;

    public function __toString() : string
    {
        return 'Orden de ' . $this->getLadoNombre() . ' ' . number_format($this->getCantidad(), 4) . ' ' . $this->getDivisaBase() . ' a ' . $this->getDivisaPrecio() . ' ' . number_format($this->getPrecio(), 4);
    }

    public function getLadoNombre() : string
    {
        return self::LADOS_NOMBRES[$this->lado];
    }

    public function getDivisaBase() : string
    {
        [ $divisaBase, $divisaPrecio ] = explode('/', $this->getPar());
        return $divisaBase;
    }

    public function getDivisaPrecio() : string
    {
        [ $divisaBase, $divisaPrecio ] = explode('/', $this->getPar());
        return $divisaPrecio;
    }

    public function __construct(?float $cantidad = 0 , ?float $precio = 0, ?float $total = 0)
    {
        $this->cantidad = $cantidad;
        $this->precio = $precio;
        if (!$total) {
            $this->total = $cantidad * $precio;
        } else {
            $this->total = $total;
        }
    }

    /**
     * @ignore
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @ignore
     */
    public function getExchange() : ?Exchange
    {
        return $this->exchange;
    }

    /**
     * @ignore
     */
    public function setExchange(Exchange $exchange) : self
    {
        $this->exchange = $exchange;

        return $this;
    }

    /**
     * @ignore
     */
    public function getPar() : ?string
    {
        return $this->par;
    }

    /**
     * @ignore
     */
    public function setPar(string $par) : self
    {
        $this->par = $par;

        return $this;
    }

    /**
     * @ignore
     */
    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    /**
     * @ignore
     */
    public function setPrecio(float $precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * @ignore
     */
    public function getCantidad() : float
    {
        return $this->cantidad;
    }

    /**
     * @ignore
     */
    public function setCantidad(float $cantidad) : self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * @ignore
     */
    public function getLado() : int
    {
        return $this->lado;
    }

    /**
     * @ignore
     */
    public function setLado(int $lado) : self
    {
        $this->lado = $lado;

        return $this;
    }

    /**
     * @ignore
     */
    public function getFecha() : ?\DateTimeInterface
    {
        return $this->fecha;
    }

    /**
     * @ignore
     */
    public function setFecha(\DateTimeInterface $fecha) : self
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * @ignore
     */
    public function getActivo() : bool
    {
        return $this->activo;
    }

    /**
     * @ignore
     */
    public function setActivo(bool $activo) : self
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * @ignore
     */
    public function getTotal() : float
    {
        return $this->cantidad * $this->precio;
    }
}
