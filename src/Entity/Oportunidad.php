<?php

namespace App\Entity;

use App\Entity\Orden;
use App\Entity\Pierna;
use App\Repository\OportunidadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
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
     * La cantidad o volumen disponible para arbitrar, expresado en DivisaBase.
     * 
     * @ORM\Column(type="decimal", precision=16, scale=8)
     */
    private float $cantidad = 0;

    /**
     * La lista de órdenes que componen esta oportunidad.
     * 
     * @ORM\ManyToMany(targetEntity=Pierna::class, cascade={"persist", "remove"})
     * @ORM\OrderBy({"posicion" = "ASC"})
     */
    private $piernas;

    /**
     * La fecha en la cual se registró la oportunidad.
     * 
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $fecha;

    /**
     * Indica si esta oportunidad todavía está vigente.
     * 
     * @ORM\Column(type="boolean")
     */
    private bool $activa = true;

    /**
     * Indica la cantidad de veces que se vio esta oportunidad
     * 
     * @ORM\Column(type="integer")
     */
    private int $contador = 0;

    public function __construct()
    {
        $this->piernas = new ArrayCollection();
        $this->fecha = new \DateTime();
    }

    /**
     * Compara dos oportunidades para saber si son iguales.
     */
    public static function areEqual(Oportunidad $op1, Oportunidad $op2) : bool
    {
        if ($op1->piernas === null || $op2->piernas === null) {
            if ($op1->piernas === null && $op2->piernas === null) {
                // Ambas están vacías. Son iguales.
                return true;
            } else {
                // Una está vacía y la otra no. No son iguales.
                return false;
            }
        }

        if (count($op1->getPiernas()) === 0 && count($op1->getPiernas()) === count($op2->getPiernas())) {
            return true;
        }

        if(count($op1->getPiernas()) == count($op2->getPiernas())) {
            // Tienen igual cantidad de piernas. Buscar diferencias.
            foreach($op1->getPiernas() as $pi1) {
                $encontre = false;
                foreach($op2->getPiernas() as $pi2) {
                    if (Pierna::areEqual($pi1, $pi2)) {
                        $encontre = true;
                        break;
                    }
                }

                if ($encontre === false) {
                    return false;
                }
            }

            // Si no se encuentran diferencias
            return true;
        } else {
            // Tienen diferente cantidad de piernas. No son iuales.
            return false;
        }

        // Si se comparó todo y no se encontró diferencias, son iguales.
        return true;
    }

    public function __toString() : string
    {
        $res = "Oportunidad: {\n";
        $res .= "  Volumen inical       : " . $this->getCantidadInicial() . ' ' . $this->getDivisaBase() . ",\n";
        $res .= "  Volumen arbitrable   : " . $this->getCantidadArbitrable() . ' ' . $this->getDivisaBase() . ",\n";
        $res .= "  Volumen máximo       : " . $this->cantidad . ' ' . $this->getDivisaBase() . ",\n";
        $res .= "  Piernas              : " . count($this->piernas) . ",\n";
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
        return $this->getPiernaInicial()->getDivisaBase();
    }

    public function getDivisaPrecio() : string
    {
        return $this->getPiernaInicial()->getDivisaPrecio();
    }

    /**
     * Obtiene la ganancia bruta de la oportunidad, expresada en DivisaPrecio.
     */
    public function getGananciaBruta() : float
    {
        $dinero = 0;
        foreach($this->piernas as $pierna) {
            $cant = $pierna->getCantidad();
            if ($cant > $this->getCantidadArbitrable()) {
                $cant = $this->getCantidadArbitrable();
            }
            if($pierna->getLado() == Orden::LADO_COMPRA) {
                $dinero -= $pierna->getPrecio() * $cant;
            } elseif($pierna->getLado() == Orden::LADO_VENTA) {
                $dinero += $pierna->getPrecio() * $cant;
            }
        }

        return abs($dinero);
    }

    /**
     * Obtiene la ganancia bruta de la oportunidad, expresada en porcentaje de la inversión inicial.
     */
    public function getGananciaBrutaPct() : float
    {
        if ($this->getPrecioInicial() > 0) {
            // Evitar división por cero
            return $this->getGananciaBruta() / $this->getCantidadArbitrableEnDivisaPrecio() * 100;
        } else {
            return 0;
        }
    }

    /**
     * Obtiene el volumen operable expresado en divisa precio
     * (normalmente está expresado en divisa base).
     * Usa precio inicial como base del cálculo.
     */
    public function getCantidadArbitrableEnDivisaPrecio() : float
    {
        return $this->getCantidadArbitrable() * $this->getPrecioInicial();
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
     * Devuelve la primera pierna de la oportunidad.
     */
    public function getPiernaInicial() : Pierna
    {
        $arr = $this->getPiernasArray();
        return $arr[0];
    }

    /**
     * Devuelve un array con las piernas.
     */
    public function getPiernasArray() : ?array
    {
        if (is_array($this->piernas)) {
            return $this->piernas;
        } else {
            return $this->piernas->toArray();
        }
        
    }

    /**
     * Devuelve la cantidad de la órden que inicia el arbitraje.
     */
    public function getCantidadInicial() : float
    {
        if ($this->piernas && count($this->piernas) > 0) {
            return $this->getPiernaInicial()->getCantidad();
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
            return $this->getPiernaInicial()->getPrecio();
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
        if (count($this->piernas) > 1) {
            $i = 0;
            $res = 0;
            foreach($this->piernas as $pierna) {
                if ($i > 0) {
                    $res += $pierna->getPrecio();
                }
                $i++;
            }
            return $res / (count($this->piernas) - 1);
        } else {
            return 0;
        }
    }

    /**
     * Devuelve la cantidad remanente.
     */
    public function getCantidadRemanente() : float
    {
        /** @var float */
        $res = 0;

        foreach($this->piernas as $pierna) {
            $res += $pierna->getOrden()->getCantidadRemanente();
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
     * @return Collection|Pierna[]
     */
    public function getPiernas(): Collection
    {
        return $this->piernas;
    }

    /**
     * @ignore
     */
    public function addPierna(Pierna $pierna): self
    {
        if (!$this->piernas->contains($pierna)) {
            $pierna->setPosicion(count($this->piernas) + 1);
            $this->piernas[] = $pierna;
        }

        $this->cantidad = $this->calcularCantidadMaxima();
        return $this;
    }

    /**
     * @ignore
     */
    public function removePierna(Pierna $pierna): self
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
    public function getContador() : int
    {
        return $this->contador;
    }

    /**
     * @ignore
     */
    public function setContador(int $contador) : self
    {
        $this->contador = $contador;

        return $this;
    }
}
