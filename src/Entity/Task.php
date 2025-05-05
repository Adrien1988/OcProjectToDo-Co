<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table]
class Task
{
    /**
     * Identifiant primaire.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    /**
     * Date et heure de création de la tâche.
     */
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    /**
     * Titre de la tâche.
     */
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Vous devez saisir un titre.')]
    private string $title;

    /**
     * Description détaillée de la tâche.
     */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Vous devez saisir du contenu.')]
    private string $content;

    /**
     * Indique si la tâche est terminée.
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isDone = false;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;


    /**
     * Le constructeur initialise la date de création à l'instant présent.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }


    /**
     * Retourne l'identifiant de la tâche.
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * Retourne la date de création.
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }


    /**
     * Définit la date de création.
     *
     * @param \DateTimeInterface $createdAt Date de création
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }


    /**
     * Retourne le titre de la tâche.
     */
    public function getTitle(): string
    {
        return $this->title;
    }


    /**
     * Définit le titre de la tâche.
     *
     * @param string $title Titre de la tâche
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }


    /**
     * Retourne la description de la tâche.
     */
    public function getContent(): string
    {
        return $this->content;
    }


    /**
     * Définit la description détaillée.
     *
     * @param string $content Contenu détaillé
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }


    /**
     * Vérifie si la tâche est terminée.
     */
    public function isDone(): bool
    {
        return $this->isDone;
    }


    /**
     * Modifie l'état d'achèvement.
     *
     * @param bool $flag Nouvel état (true = terminé)
     */
    public function toggle(bool $flag): void
    {
        $this->isDone = $flag;
    }


    public function getAuthor(): ?User
    {
        return $this->author;
    }


    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }


}
