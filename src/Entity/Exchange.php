<?php

namespace App\Entity;

use App\Entity\BookOrder;
use App\Entity\Rate;
use App\Model\OrderBook;
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
     * @ORM\OneToMany(targetEntity="App\Entity\BookOrder", mappedBy="exchange", cascade={"persist", "remove"})
     */
    private $bookOrders;

    /**
     * @var Rate
     * @ORM\OneToMany(targetEntity="App\Entity\Rate", mappedBy="exchange", cascade={"persist", "remove"})
     */
    private $currentRates;

    public function __toString() : string
    {
        return $this->name;
    }

    /**
     * @return OrderBook
     */
    public function getOrderBookForPair(string $pair): OrderBook
    {
        $res = new OrderBook($pair);
        foreach ($this->getBookOrders() as $bookOrder) {
            if ($bookOrder->getPair() == $pair) {
                if ($bookOrder->getSide() == BookOrder::SIDE_SELL) {
                    $res->addSellOrder(new \App\Model\Order($bookOrder->getQuantity(), $bookOrder->getPrice()));
                } else {
                    $res->addBuyOrder(new \App\Model\Order($bookOrder->getQuantity(), $bookOrder->getPrice()));
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
            if ($symbol === '*' || strpos($pair, $symbol . '/') !== false || strpos($pair,  '/' . $symbol)) {
                $res[] = $rate;
            }
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
     * @return Collection|PersistentCollection|BookOrder[]
     */
    public function getBookOrders(): PersistentCollection
    {
        return $this->bookOrders;
    }

    public function addBookOrder(BookOrder $bookOrder): self
    {
        if (!$this->bookOrders->contains($bookOrder)) {
            $this->bookOrders[] = $bookOrder;
            $bookOrder->setExchange($this);
        }

        return $this;
    }

    public function removeBookOrder(BookOrder $bookOrder): self
    {
        if ($this->bookOrders->contains($bookOrder)) {
            $this->bookOrders->removeElement($bookOrder);
            // set the owning side to null (unless already changed)
            if ($bookOrder->getExchange() === $this) {
                $bookOrder->setExchange(null);
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
