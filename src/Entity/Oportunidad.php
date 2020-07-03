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
        $res .= "  Volumen inical       : " . $this->getCantidadInicial() . ' ' . $this->getDivisaBase() . ",\n";
        $res .= "  Volumen arbitrable   : " . $this->getCantidadArbitrable() . ' ' . $this->getDivisaBase() . ",\n";
        $res .= "  Volumen máximo       : " . $this->cantidad . ' ' . $this->getDivisaBase() . ",\n";
        $res .= "  Precio inical        : " . $this->getPrecioInicial() . ' ' . $this->getDivisaBase() . ",\n";
        $res .= "  Precio arb. promedio : " . $this->getPrecioArbitrablePromedio() . ' ' . $this->getDivisaBase() . ",\n";
        foreach($this->piernas as $pierna) {
            if($pierna->getLado() == Orden::LADO_COMPRA) {
                $res .= "  Vender ";
            } else {
                $res .= "  Comprar ";
            }
            $res .= $pierna->getCantidad(). ' ' . $pierna->getDivisaBase() . ' a ' . $pierna->getPrecio() . ' ' . $pierna->getDivisaPrecio() . ' en ' . $pierna->getExchange();
            $res .= ",\n";
        }
        $res .= "  Ganancia: " . $this->getGananciaBruta() . ' ' . $this->getDivisaPrecio() . ' (' . number_format($this->getGananciaBrutaPct(), 2) . "%),\n";

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
    public function getGananciaBruta() : float
    {
        return abs($this->getPrecioInicial() - $this->getPrecioArbitrablePromedio()) * $this->cantidad;
    }

    /**
     * Obtiene la ganancia bruta de la oportunidad, expresada en porcentaje de la inversión inicial.
     */
    public function getGananciaBrutaPct() : float
    {
        return $this->getGananciaBruta() / $this->getPrecioInicial() * 100;
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
     * Devuelve la cantidad de la órden que inicia el arbitraje.
     */
    public function getCantidadInicial() : float
    {
        if ($this->piernas && count($this->piernas) > 0) {
            return $this->piernas[0]->getCantidad();
        } else {
            return 0;
        }
    }

    /**
     * Devuelve el precio de la órden que inicia el arbitraje.
     */
    public function getPrecioInicial() : float
    {
        if ($this->piernas && count($this->piernas) > 0) {
            return $this->piernas[0]->getPrecio();
        } else {
            return 0;
        }
    }

    /**
     * Devuelve la cantidad de las órdenes de arbitraje (segunda en adelante).
     */
    public function getCantidadArbitrable() : float
    {
        $i = 0;
        $res = 0;
        foreach($this->piernas as $pierna) {
            if ($i > 0) {
                $res += $pierna->getCantidad();
            }
            $i++;
        }
        return $res;
    }

    /**
     * Devuelve el precio promedio de las órdenes de arbitraje (segunda en adelante).
     */
    public function getPrecioArbitrablePromedio() : float
    {
        $i = 0;
        $res = 0;
        foreach($this->piernas as $pierna) {
            if ($i > 0) {
                $res += $pierna->getPrecio();
            }
            $i++;
        }
        return $res / (count($this->piernas) - 1);
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
        return min(
            $this->getCantidadInicial(),
            $this->getCantidadArbitrable()
        );

        /** @var float */
        $res = 0;

        foreach($this->piernas as $pierna) {
            if ($res == 0 || $pierna->getCantidad() < $res) {
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
