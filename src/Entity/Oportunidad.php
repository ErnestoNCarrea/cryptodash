<?php

namespace App\Entity;

use App\Entity\Orden;
use App\Repository\OportunidadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OportunidadRepository::class)
 */
class Oportunidad
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=8)
     */
    private float $cantidad = 0;

    /**
     * @ORM\ManyToMany(targetEntity=Orden::class)
     */
    private $piernas;

    /**
     * La fecha en la cual se registró la oportunidad.
     * 
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $fecha;

    public function __construct()
    {
        $this->piernas = new ArrayCollection();
        $this->fecha = new \DateTime();
    }

    public function __toString() : string
    {
        $res = "Oportunidad: {\n";
        $res .= "  Volumen: " . number_format($this->cantidad, 8) . ' ' . $this->getDivisaBase() . ",\n";
        foreach($this->piernas as $pierna) {
            if($pierna->getLado() == Orden::LADO_COMPRA) {
                $res .= "  Vender ";
            } else {
                $res .= "  Comprar ";
            }
            $res .= $pierna->getCantidad(). ' ' . $pierna->getDivisaBase() . ' a ' . number_format($pierna->getPrecio()) . ' ' . $pierna->getDivisaPrecio() . ' en ' . $pierna->getExchange();
            $res .= ",\n";
        }

        $res .= "  Diferencia de precio: " . number_format($this->getDiferenciaPrecio(), 8) . ",\n";
        $res .= "  Ganacia: " . number_format($this->getGanaciaBruta(), 8) . ' ' . $this->getDivisaPrecio() . ",\n";

        $res .= "};\n";

        return $res;
    }

    public function getDivisaBase() : string
    {
        return $this->piernas[0]->getDivisaBase();
    }

    public function getDivisaPrecio() : string
    {
        return $this->piernas[0]->getDivisaPrecio();
    }

    /**
     * Obtiene la ganancia bruta de la oportunidad, expresada en DivisaPrecio.
     */
    public function getGanaciaBruta() : float
    {
        return $this->getDiferenciaPrecio() * $this->cantidad;
    }


    /**
     * Obtiene la diferencia de precio entre las piernas, expresada en DivisaPrecio.
     */
    public function getDiferenciaPrecio() : float
    {
        $dinero = 0;
        foreach($this->piernas as $pierna) {
            if($pierna->getLado() == Orden::LADO_COMPRA) {
                $dinero -= $pierna->getPrecio();
            } elseif($pierna->getLado() == Orden::LADO_VENTA) {
                $dinero += $pierna->getPrecio();
            }
        }

        return abs($dinero);
    }

    /**
     * Devuelve la cantidad remanente.
     */
    public function getCantidadRemanente() : float
    {
        /** @var float */
        $res = 0;

        foreach($this->piernas as $pierna) {
            $res += $pierna->getCantidadRemanente();
        }

        return $res;
    }

    /**
     * Devuelve la cantidad máxima arbitrable.
     */
    private function calcularCantidadMaxima() : float
    {
        /** @var float */
        $res = 0;

        foreach($this->piernas as $pierna) {
            if ($pierna->getCantidad() > $res) {
                $res = $pierna->getCantidad();
            }
        }

        return $res;
    }

    /**
     * @ignore
     */
    public function getCantidad(): ?float
    {
        return $this->cantidad;
    }

    /**
     * @ignore
     */
    public function setCantidad(float $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * @ignore
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @ignore
     * @return Collection|Orden[]
     */
    public function getPiernas(): Collection
    {
        return $this->piernas;
    }

    /**
     * @ignore
     */
    public function addPierna(Orden $pierna): self
    {
        if (!$this->piernas->contains($pierna)) {
            $this->piernas[] = $pierna;
        }

        $this->cantidad = $this->calcularCantidadMaxima();
        return $this;
    }

    /**
     * @ignore
     */
    public function removePierna(Orden $pierna): self
    {
        if ($this->piernas->contains($pierna)) {
            $this->piernas->removeElement($pierna);
        }
        
        $this->cantidad = $this->calcularCantidadMaxima();

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
}
