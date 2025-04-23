<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité représentant une tâche à réaliser.
 *
 * Fournit les accesseurs/mutateurs ainsi qu'une méthode toggle
 * pour changer l'état d'achèvement. Toutes les propriétés et
 * méthodes sont désormais documentées afin que les analyseurs
 * statiques et les outils de qualité de code (Codacy, phpstan, etc.)
 * ne signalent plus l'absence de documentation.
 */
#[ORM\Entity]
#[ORM\Table]
class Task
{
    /**
     * Identifiant primaire.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    /**
     * Date et heure de création de la tâche.
     *
     * @var \DateTimeInterface
     */
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    /**
     * Titre de la tâche.
     *
     * @var string
     */
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: "Vous devez saisir un titre.")]
    private string $title;

    /**
     * Description détaillée de la tâche.
     *
     * @var string
     */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "Vous devez saisir du contenu.")]
    private string $content;

    /**
     * Indique si la tâche est terminée.
     *
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isDone = false;

    /**
     * Le constructeur initialise la date de création à l'instant présent.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Retourne l'identifiant de la tâche.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Retourne la date de création.
     *
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Définit la date de création.
     *
     * @param \DateTimeInterface $createdAt Date de création
     * @return void
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Retourne le titre de la tâche.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Définit le titre de la tâche.
     *
     * @param string $title Titre de la tâche
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Retourne la description de la tâche.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Définit la description détaillée.
     *
     * @param string $content Contenu détaillé
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Vérifie si la tâche est terminée.
     *
     * @return bool
     */
    public function isDone(): bool
    {
        return $this->isDone;
    }

    /**
     * Modifie l'état d'achèvement.
     *
     * @param bool $flag Nouvel état (true = terminé)
     * @return void
     */
    public function toggle(bool $flag): void
    {
        $this->isDone = $flag;
    }
}
