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
    private $infiniteSupply;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clase;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Orden", mappedBy="exchange", cascade={"persist", "remove"})
     */
    private $ordenLibros;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Cotizacion", mappedBy="exchange", cascade={"persist", "remove"})
     */
    private $cotizaciones;

    public function __toString(): string
    {
        return $this->nombre;
    }

    /**
     * @return Libro
     */
    public function getLibroForPar(string $par): Libro
    {
        $res = new Libro($par);
        foreach ($this->getOrdenes() as $ordenLibro) {
            if ($ordenLibro->getPar() == $par) {
                if ($ordenLibro->getLado() == Orden::LADO_SELL) {
                    $res->addOrdenVenta(new \App\Entity\Orden($ordenLibro->getCantidad(), $ordenLibro->getPrecio()));
                } else {
                    $res->addOrdenCompra(new \App\Entity\Orden($ordenLibro->getCantidad(), $ordenLibro->getPrecio()));
                }
            }
        }

        return $res;
    }

    /**
     * Obtener cotizaciones de un símbolo contra el resto de los símbolos.
     */
    public function getAllCotizacionesForSimbolo(string $simbolo): array
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
     * Obtener cotizaciones de un símbolo contra el resto de los símbolos.
     */
    public function obtenerMejorPrecioParaTodasDivisas(): array
    {
        $pares = [];

        // Get all paris
        foreach ($this->getOrdenes() as $ordenLibro) {
            if (in_array($ordenLibro->getPar(), $pares) == false) {
                $pares[] = $ordenLibro->getPar();
            }
        }

        $res = [];

        foreach ($pares as $par) {
            $ob = $this->getLibroForPar($par);
            $cotizacion = new Cotizacion();
            $cotizacion->setExchange($this);
            $cotizacion->setPar($par);
            $cotizacion->setPrecioVenta($ob->getBestPrecioVenta() ?: 0);
            $cotizacion->setPrecioCompra($ob->getBestPrecioCompra() ?: 0);

            $res[] = $cotizacion;
        }

        return $res;
    }

    public function getCotizacionForPar(string $par): ?Cotizacion
    {
        foreach ($this->cotizaciones as $cotizacion) {
            if ($cotizacion->getPar() == $par) {
                return $cotizacion;
            }
        }

        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getInfiniteSupply(): ?bool
    {
        return $this->infiniteSupply;
    }

    public function setInfiniteSupply(bool $infiniteSupply): self
    {
        $this->infiniteSupply = $infiniteSupply;

        return $this;
    }

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
     * @return Collection|PersistentCollection|Orden[]
     */
    public function getOrdenes(): PersistentCollection
    {
        return $this->ordenLibros;
    }

    public function addOrden(Orden $ordenLibro): self
    {
        if (!$this->ordenLibros->contains($ordenLibro)) {
            $this->ordenLibros[] = $ordenLibro;
            $ordenLibro->setExchange($this);
        }

        return $this;
    }

    public function removeOrden(Orden $ordenLibro): self
    {
        if ($this->ordenLibros->contains($ordenLibro)) {
            $this->ordenLibros->removeElement($ordenLibro);
            // set the owning lado to null (unless already changed)
            if ($ordenLibro->getExchange() === $this) {
                $ordenLibro->setExchange(null);
            }
        }

        return $this;
    }

    /**
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
