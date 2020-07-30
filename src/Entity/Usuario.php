<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Orden;

/**
 * @ORM\Entity()
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

    /**
     * @ORM\OneToMany(targetEntity=UsuarioExchange::class, mappedBy="usuario", orphanRemoval=true)
     */
    private $usuarioExchanges;

    /**
     * @ORM\OneToMany(targetEntity=UsuarioPar::class, mappedBy="usuario", orphanRemoval=true)
     */
    private $usuarioPares;

    public function __construct()
    {
        $this->ordenLibros = new ArrayCollection();
        $this->usuarioExchanges = new ArrayCollection();
        $this->usuarioPares = new ArrayCollection();
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
    public function getNombre() : ?string
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

    /**
     * @return Collection|UsuarioExchange[]
     */
    public function getUsuarioExchanges(): Collection
    {
        return $this->usuarioExchanges;
    }

    public function addUsuarioExchange(UsuarioExchange $usuarioExchange): self
    {
        if (!$this->usuarioExchanges->contains($usuarioExchange)) {
            $this->usuarioExchanges[] = $usuarioExchange;
            $usuarioExchange->setUsuario($this);
        }

        return $this;
    }

    public function removeUsuarioExchange(UsuarioExchange $usuarioExchange): self
    {
        if ($this->usuarioExchanges->contains($usuarioExchange)) {
            $this->usuarioExchanges->removeElement($usuarioExchange);
            // set the owning side to null (unless already changed)
            if ($usuarioExchange->getUsuario() === $this) {
                $usuarioExchange->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UsuarioPar[]
     */
    public function getUsuarioPares(): Collection
    {
        return $this->usuarioPares;
    }

    public function addUsuarioPare(UsuarioPar $usuarioPare): self
    {
        if (!$this->usuarioPares->contains($usuarioPare)) {
            $this->usuarioPares[] = $usuarioPare;
            $usuarioPare->setUsuario($this);
        }

        return $this;
    }

    public function removeUsuarioPare(UsuarioPar $usuarioPare): self
    {
        if ($this->usuarioPares->contains($usuarioPare)) {
            $this->usuarioPares->removeElement($usuarioPare);
            // set the owning side to null (unless already changed)
            if ($usuarioPare->getUsuario() === $this) {
                $usuarioPare->setUsuario(null);
            }
        }

        return $this;
    }
}
