<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DivisaRepository")
 */
class Divisa
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
    private $simbolo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    public function __toString() : string
    {
        return $this->nombre;
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
    public function getSimbolo(): ?string
    {
        return $this->simbolo;
    }

    /**
     * @ignore
     */
    public function setSimbolo(string $simbolo): self
    {
        $this->simbolo = $simbolo;

        return $this;
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
}
