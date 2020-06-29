<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Orden;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsuarioRepository")
 */
class Usuario implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $apellido;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @var string|null
     */
    protected $plainPassword;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $passwordRequestToken;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Orden", mappedBy="usuario")
     */
    private $ordenLibros;

    public function __construct()
    {
        $this->ordenLibros = new ArrayCollection();
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRolesString(): ?string
    {
        return json_encode($this->roles);
    }

    public function setRolesString(string $roles): self
    {
        $this->roles = json_decode($roles);

        return $this;
    }

    /**
     * A visual identifier that represents this usuario.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every usuario at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the usuario, clear it here
        $this->plainPassword = null;
    }

    /**
     * @ignore
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @ignore
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @ignore
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @ignore
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @ignore
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * @ignore
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get the value of passwordRequestToken
     *
     * @return  string|null
     */
    public function getPasswordRequestToken()
    {
        return $this->passwordRequestToken;
    }

    /**
     * Set the value of passwordRequestToken
     *
     * @param  string|null  $passwordRequestToken
     *
     * @return  self
     */
    public function setPasswordRequestToken($passwordRequestToken)
    {
        $this->passwordRequestToken = $passwordRequestToken;

        return $this;
    }

    /**
     * @return Collection|Orden[]
     */
    public function getOrdens(): Collection
    {
        return $this->ordenLibros;
    }

    public function addOrden(Orden $ordenLibro): self
    {
        if (!$this->ordenLibros->contains($ordenLibro)) {
            $this->ordenLibros[] = $ordenLibro;
            $ordenLibro->setUsuario($this);
        }

        return $this;
    }

    public function removeOrden(Orden $ordenLibro): self
    {
        if ($this->ordenLibros->contains($ordenLibro)) {
            $this->ordenLibros->removeElement($ordenLibro);
            // set the owning side to null (unless already changed)
            if ($ordenLibro->getUsuario() === $this) {
                $ordenLibro->setUsuario(null);
            }
        }

        return $this;
    }
}
