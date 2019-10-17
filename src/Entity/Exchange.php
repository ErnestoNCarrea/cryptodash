<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use App\Entity\BookOrder;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $class;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BookOrder", mappedBy="exchange", cascade={"persist", "remove"})
     */
    private $bookOrders;

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
}
