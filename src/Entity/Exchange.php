<?php

namespace App\Entity;

use App\Entity\Orden;
use App\Entity\Cotizacion;
use App\Model\Libro;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExchangeRepository")
 */
class Exchange
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="boolean")
     */
    private $suministroInfinito;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clase;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Orden", mappedBy="exchange", cascade={"persist", "remove"})
     */
    private $ordenes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Cotizacion", mappedBy="exchange", cascade={"persist", "remove"})
     */
    private $cotizaciones;

    public function __toString(): string
    {
        return $this->nombre;
    }

    /**
     * Devuelve un libro para todas las órdenes de este exchange para un par determinado.
     */
    public function obtenerOrdenesPorPar(string $par) : array
    {
        $res = [];

        foreach($this->ordenes as $orden) {
            if($orden->getPar() == $par) {
                $res[] = $orden;
            }
        }

        return $res;
    }

    /**
     * Devuelve un libro para todas las órdenes de este exchange para un par determinado.
     */
    public function obtenerLibroPorPar(string $par) : Libro
    {
        return new Libro($this->obtenerOrdenesPorPar($par), $par);
    }

    /**
     * Obtener cotizaciones de un símbolo contra el resto de los símbolos.
     */
    public function obtenerCotizacionesParaSimbolo(string $simbolo): array
    {
        $res = [];

        foreach ($this->cotizaciones as $cotizacion) {
            $par = $cotizacion->getPar();
            if ($simbolo === '*' || strpos($par, $simbolo . '/') !== false || strpos($par, '/' . $simbolo)) {
                $res[] = $cotizacion;
            }
        }

        return $res;
    }

    /**
     * Devuelve todos los pares que existen en el libro.
     */
    public function obtenerParesEnLibro() : array
    {
        $pares = [];

        // Get all paris
        foreach ($this->getOrdenes() as $ordenLibro) {
            if (in_array($ordenLibro->getPar(), $pares) == false) {
                $pares[] = $ordenLibro->getPar();
            }
        }

        return $pares;
    }

    /**
     * Obtener cotizaciones de un símbolo contra el resto de los símbolos.
     */
    public function obtenerMejorPrecioParaTodasDivisas(): array
    {
        $pares = $this->obtenerParesEnLibro();

        $res = [];

        foreach ($pares as $par) {
            $ob = $this->obtenerLibroPorPar($par);
            $cotizacion = new Cotizacion();
            $cotizacion->setExchange($this);
            $cotizacion->setPar($par);
            $cotizacion->setPrecioVenta($ob->getMejorPrecioVenta() ?: 0);
            $cotizacion->setPrecioCompra($ob->getMejorPrecioCompra() ?: 0);

            $res[$par] = $cotizacion;
        }

        return $res;
    }

    public function getCotizacionPar(string $par): ?Cotizacion
    {
        foreach ($this->cotizaciones as $cotizacion) {
            if ($cotizacion->getPar() == $par) {
                return $cotizacion;
            }
        }

        return null;
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
     */
    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    /**
     * @ignore
     */
    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @ignore
     */
    public function getSuministroInfinito(): ?bool
    {
        return $this->suministroInfinito;
    }

    /**
     * @ignore
     */
    public function setSuministroInfinito(bool $suministroInfinito): self
    {
        $this->suministroInfinito = $suministroInfinito;

        return $this;
    }

    /**
     * @ignore
     */
    public function getClase(): ?string
    {
        return $this->clase;
    }

    public function setClase(?string $clase): self
    {
        $this->clase = $clase;

        return $this;
    }

    /**
     * @ignore
     * @return Collection|PersistentCollection|Orden[]
     */
    public function getOrdenes(): PersistentCollection
    {
        return $this->ordenes;
    }

    /**
     * @ignore
     */
    public function addOrden(Orden $ordenLibro): self
    {
        if (!$this->ordenes->contains($ordenLibro)) {
            $this->ordenes[] = $ordenLibro;
            $ordenLibro->setExchange($this);
        }

        return $this;
    }


    /**
     * @ignore
     */
    public function removeOrden(Orden $ordenLibro): self
    {
        if ($this->ordenes->contains($ordenLibro)) {
            $this->ordenes->removeElement($ordenLibro);
            // set the owning lado to null (unless already changed)
            if ($ordenLibro->getExchange() === $this) {
                //$ordenLibro->setExchange(null);
            }
        }

        return $this;
    }

    /**
     * @ignore
     * @return Collection|PersistentCollection|Cotizacion[]
     */
    public function getCotizaciones(): PersistentCollection
    {
        return $this->cotizaciones;
    }

    public function addCotizacion(Cotizacion $cotizacion): self
    {
        if (!$this->cotizaciones->contains($cotizacion)) {
            $this->cotizaciones[] = $cotizacion;
            $cotizacion->setExchange($this);
        }

        return $this;
    }

    /**
     * @ignore
     */
    public function removeCotizacion(Cotizacion $cotizacion): self
    {
        if ($this->cotizaciones->contains($cotizacion)) {
            $this->cotizaciones->removeElement($cotizacion);
            // set the owning lado to null (unless already changed)
            if ($cotizacion->getExchange() === $this) {
                $cotizacion->setExchange(null);
            }
        }

        return $this;
    }
}
