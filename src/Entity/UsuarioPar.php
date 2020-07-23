<?php

namespace App\Entity;

use App\Repository\UsuarioParRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UsuarioParRepository::class)
 */
class UsuarioPar
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class, inversedBy="usuarioPares")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity=Divisa::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $divisa1;

    /**
     * @ORM\ManyToOne(targetEntity=Divisa::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $divisa2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $par = '';

    public function armarPar()
    {
        if ($this->getDivisa1() && $this->getDivisa2()) {
            $this->par = $this->getDivisa1()->getSimbolo() . '/' . $this->getDivisa2()->getSimbolo();
        } else {
            $this->par = '';
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getDivisa1(): ?Divisa
    {
        return $this->divisa1;
    }

    public function setDivisa1(?Divisa $divisa1): self
    {
        $this->divisa1 = $divisa1;
        $this->armarPar();
        return $this;
    }

    public function getDivisa2(): ?Divisa
    {
        return $this->divisa2;
    }

    public function setDivisa2(?Divisa $divisa2): self
    {
        $this->divisa2 = $divisa2;
        $this->armarPar();
        return $this;
    }

    public function getPar(): ?string
    {
        return $this->par;
    }

    public function setPar(string $par): self
    {
        $this->par = $par;

        return $this;
    }
}
