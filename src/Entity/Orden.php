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
    public const LADO_BUY = 1;
    public const LADO_SELL = 2;

    private bool $activo = false;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Exchange")
     * @ORM\JoinColumn(nullable=false)
     */
    private Exchange $exchange;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $par;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario", inversedBy="orders")
     * @ORM\JoinColumn(nullable=true)
     */
    private Usuario $usuario;

    /**
     * @ORM\Column(type="float")
     */
    private float $precio = 0;

    /**
     * @ORM\Column(type="float")
     */
    private float $cantidad = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $lado = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $dateTime;

    /**
     * Sin mapear.
     */
    private float $total = 0;

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
    public function getUsuario() : ?Usuario
    {
        return $this->usuario;
    }

    /**
     * @ignore
     */
    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

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
    public function getDateTime() : ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    /**
     * @ignore
     */
    public function setDateTime(\DateTimeInterface $dateTime) : self
    {
        $this->dateTime = $dateTime;

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
        return $this->total;
    }

    /**
     * @ignore
     */
    public function setTotal(float $total) : self
    {
        $this->total = $total;

        return $this;
    }
}
