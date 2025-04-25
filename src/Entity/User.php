<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 25, unique: true)]
    #[Assert\NotBlank(message: "Vous devez saisir un nom d'utilisateur.")]
    private string $username;

    #[ORM\Column(type: 'string', length: 64)]
    private string $password;

    #[ORM\Column(type: 'string', length: 60, unique: true)]
    #[Assert\NotBlank(message: 'Vous devez saisir une adresse email.')]
    #[Assert\Email(message: "Le format de l'adresse n'est pas correcte.")]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /** @var Collection<int,Task> */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Task::class, orphanRemoval: true)]
    private Collection $tasks;


    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->roles = ['ROLE_USER'];
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getUserIdentifier(): string
    {
        return $this->username;
    }


    public function getUsername(): string
    {
        return $this->username;
    }


    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }


    public function getSalt(): ?string
    {
        return null;
    }


    public function getPassword(): string
    {
        return $this->password;
    }


    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    public function getEmail(): string
    {
        return $this->email;
    }


    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }


    public function getRoles(): array
    {
        return $this->roles;
    }


    public function setRoles(array $roles): self
    {
        if (in_array('ROLE_ADMIN', $roles, true)) {
            $this->roles = ['ROLE_ADMIN'];
        } else {
            $this->roles = ['ROLE_USER'];
        }

        return $this;
    }


    public function eraseCredentials(): void
    {
        // Nettoyage des données sensibles si nécessaire
    }


    public function getTasks(): Collection
    {
        return $this->tasks;
    }


}
