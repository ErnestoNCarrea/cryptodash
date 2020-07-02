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
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @ignore
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @ignore
     */
    public function getRolesString(): ?string
    {
        return json_encode($this->roles);
    }

    /**
     * @ignore
     */
    public function setRolesString(string $roles): self
    {
        $this->roles = json_decode($roles);

        return $this;
    }

    /**
     * @ignore
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @ignore
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every usuario at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @ignore
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @ignore
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
     * @ignore
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @ignore
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
    public function getNombre() : ?nombre
    {
        return $this->nombre;
    }

    /**
     * @ignore
     */
    public function setNombre(string $nombre) : self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @ignore
     */
    public function getApellido() : ?string
    {
        return $this->apellido;
    }

    /**
     * @ignore
     */
    public function setApellido(string $apellido) : self
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * @ignore
     */
    public function getPasswordRequestToken() : ?string
    {
        return $this->passwordRequestToken;
    }

    /**
     * @ignore
     */
    public function setPasswordRequestToken(?string $passwordRequestToken) : self
    {
        $this->passwordRequestToken = $passwordRequestToken;

        return $this;
    }
}
