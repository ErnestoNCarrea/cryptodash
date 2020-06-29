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
    public const SIDE_BUY = 1;
    public const SIDE_SELL = 2;

    private bool $active = false;

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
    private string $pair;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario", inversedBy="orders")
     * @ORM\JoinColumn(nullable=true)
     */
    private Usuario $usuario;

    /**
     * @ORM\Column(type="float")
     */
    private float $price = 0;

    /**
     * @ORM\Column(type="float")
     */
    private float $quantity = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $side = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $dateTime;

    /**
     * Sin mapear.
     */
    private float $total = 0;

    public function __construct(?float $quantity = 0 , ?float $price = 0, ?float $total = 0)
    {
        $this->quantity = $quantity;
        $this->price = $price;
        if (!$total) {
            $this->total = $quantity * $price;
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
    public function getPair() : ?string
    {
        return $this->pair;
    }

    /**
     * @ignore
     */
    public function setPair(string $pair) : self
    {
        $this->pair = $pair;

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
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @ignore
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @ignore
     */
    public function getQuantity() : float
    {
        return $this->quantity;
    }

    /**
     * @ignore
     */
    public function setQuantity(float $quantity) : self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @ignore
     */
    public function getSide() : int
    {
        return $this->side;
    }

    /**
     * @ignore
     */
    public function setSide(int $side) : self
    {
        $this->side = $side;

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
    public function getActive() : bool
    {
        return $this->active;
    }

    /**
     * @ignore
     */
    public function setActive(bool $active) : self
    {
        $this->active = $active;

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
