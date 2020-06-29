<?php

namespace App\Entity;

use App\Entity\Orden;
use App\Entity\Rate;
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
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $infiniteSupply;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $class;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Orden", mappedBy="exchange", cascade={"persist", "remove"})
     */
    private $ordenLibros;

    /**
     * @var Rate
     * @ORM\OneToMany(targetEntity="App\Entity\Rate", mappedBy="exchange", cascade={"persist", "remove"})
     */
    private $currentRates;

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Libro
     */
    public function getLibroForPair(string $pair): Libro
    {
        $res = new Libro($pair);
        foreach ($this->getOrdens() as $ordenLibro) {
            if ($ordenLibro->getPair() == $pair) {
                if ($ordenLibro->getSide() == Orden::SIDE_SELL) {
                    $res->addOrdenVenta(new \App\Model\Orden($ordenLibro->getQuantity(), $ordenLibro->getPrice()));
                } else {
                    $res->addOrdenCompra(new \App\Model\Orden($ordenLibro->getQuantity(), $ordenLibro->getPrice()));
                }
            }
        }

        return $res;
    }

    /**
     * Obtener cotizaciones de un símbolo contra el resto de los símbolos.
     */
    public function getAllRatesForSymbol(string $symbol): array
    {
        $res = [];

        foreach ($this->currentRates as $rate) {
            $pair = $rate->getPair();
            if ($symbol === '*' || strpos($pair, $symbol . '/') !== false || strpos($pair, '/' . $symbol)) {
                $res[] = $rate;
            }
        }

        return $res;
    }

    /**
     * Obtener cotizaciones de un símbolo contra el resto de los símbolos.
     */
    public function getBestOrdensForAllSymbols(): array
    {
        $pairs = [];

        // Get all paris
        foreach ($this->getOrdens() as $ordenLibro) {
            if (in_array($ordenLibro->getPair(), $pairs) == false) {
                $pairs[] = $ordenLibro->getPair();
            }
        }

        $res = [];

        foreach ($pairs as $pair) {
            $ob = $this->getLibroForPair($pair);
            $rate = new Rate();
            $rate->setExchange($this);
            $rate->setPair($pair);
            $rate->setSellPrice($ob->getBestSellPrice() ?: 0);
            $rate->setBuyPrice($ob->getBestBuyPrice() ?: 0);

            $res[] = $rate;
        }

        return $res;
    }

    public function getCurrentRateForPair(string $pair): ?Rate
    {
        foreach ($this->currentRates as $rate) {
            if ($rate->getPair() == $pair) {
                return $rate;
            }
        }

        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(?string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return Collection|PersistentCollection|Orden[]
     */
    public function getOrdens(): PersistentCollection
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
            // set the owning side to null (unless already changed)
            if ($ordenLibro->getExchange() === $this) {
                $ordenLibro->setExchange(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PersistentCollection|Rate[]
     */
    public function getCurrentRates(): PersistentCollection
    {
        return $this->currentRates;
    }

    public function addCurrentRate(Rate $currentRate): self
    {
        if (!$this->currentRates->contains($currentRate)) {
            $this->currentRates[] = $currentRate;
            $currentRate->setExchange($this);
        }

        return $this;
    }

    public function removeCurrentRate(Rate $currentRate): self
    {
        if ($this->currentRates->contains($currentRate)) {
            $this->currentRates->removeElement($currentRate);
            // set the owning side to null (unless already changed)
            if ($currentRate->getExchange() === $this) {
                $currentRate->setExchange(null);
            }
        }

        return $this;
    }
}
