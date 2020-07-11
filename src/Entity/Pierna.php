<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Exchange;
use App\Entity\Orden;

/**
 * Representa una pierna en una oportunidad de arbitraje.
 * 
 * @ORM\Entity(repositoryClass="App\Repository\PiernaRepository")
 */
class Pierna
{
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
     * El orden de esta pierna en la oportunidad.
     * 
     * @ORM\Column(type="integer")
     */
    private int $posicion = 1;

    /**
     * La orden a la cual está relacionada esta pierna.
     * 
     * @ORM\ManyToOne(targetEntity=Orden::class)
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $orden;

    /**
     * Crea una pierna a partir de una orden.
     */
    public static function fromOrden(Orden $orden, ?float $cantidadMaxima = 0) : Pierna
    {
        $res = new Pierna();

        $res->setPar($orden->getPar());

        if ($cantidadMaxima > 0 && $orden->getCantidad() > $cantidadMaxima) {
            $res->setCantidad($cantidadMaxima);
        } else {
            $res->setCantidad($orden->getCantidad());
        }
        $res->setPrecio($orden->getPrecio());
        $res->setLado($orden->getLado());
        $res->setExchange($orden->getExchange());
        $res->setOrden($orden);

        return $res;
    }


    /**
     * Compara dos piernas para saber si son iguales.
     */
    public static function areEqual(Pierna $pi1, Pierna $pi2) : bool
    {
        return $pi1->getPar() == $pi2->getPar() &&
            $pi1->getCantidad() == $pi2->getCantidad() &&
            $pi1->getPrecio() == $pi2->getPrecio() &&
            $pi1->getLado() == $pi2->getLado() &&
            $pi1->getPosicion() == $pi2->getPosicion() &&
            (
                ($pi1->getExchange() === null && $pi1->getExchange() === $pi2->getExchange())
                ||
                ($pi2->getExchange() !== null && $pi1->getExchange()->getId() === $pi2->getExchange()->getId())
            ) && 
            (
                ($pi1->getOrden() === null && $pi1->getOrden() === $pi2->getOrden())
                ||
                ($pi2->getOrden() !== null && $pi1->getOrden()->getId() === $pi2->getOrden()->getId())
            );
    }

    public function __toString() : string
    {
        return 'Pierna de ' . $this->getLadoNombre() . ' ' . number_format($this->getCantidad(), 4) . ' ' . $this->getDivisaBase() . ' a ' . $this->getDivisaPrecio() . ' ' . number_format($this->getPrecio(), 4);
    }

    public function getLadoNombre() : string
    {
        return Orden::LADOS_NOMBRES[$this->lado];
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

    /**
     * Devuelve el importe total de la pierna (precio x cantidad).
     */
    public function getTotal() : float
    {
        return $this->cantidad * $this->precio;
    }


    public function __construct(?float $cantidad = 0 , ?float $precio = 0, ?string $par = null)
    {
        $this->cantidad = $cantidad;
        $this->precio = $precio;
        if ($par != null) {
            $this->par = $par;
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
    public function getActiva() : bool
    {
        return $this->activa;
    }

    /**
     * @ignore
     */
    public function setActiva(bool $activa) : self
    {
        $this->activa = $activa;

        return $this;
    }

    /**
     * @ignore
     */
    public function getOrden(): ?Orden
    {
        return $this->orden;
    }

    /**
     * @ignore
     */
    public function setOrden(?Orden $orden): self
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * @ignore
     */
    public function getPosicion() : int
    {
        return $this->posicion;
    }

    /**
     * @ignore
     */
    public function setPosicion(int $posicion) : self
    {
        $this->posicion = $posicion;

        return $this;
    }
}
