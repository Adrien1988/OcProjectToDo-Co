<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    public const EDIT = 'TASK_EDIT';
    public const DELETE = 'TASK_DELETE';
    public const CREATE = 'TASK_CREATE';


    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::EDIT, self::DELETE, self::CREATE], true)
            && ($attribute === self::CREATE || $subject instanceof Task);
    }


    /**
     * @param Task|null $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User|null $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            // anonyme ⇒ toujours refusé
            return false;
        }

        // CREATE : tout utilisateur authentifié peut créer
        if ($attribute === self::CREATE) {
            return true;
        }

        // À ce stade $subject est une Task
        $task = $subject;
        $author = $task->getAuthor();

        $isAdmin = \in_array('ROLE_ADMIN', $user->getRoles(), true);
        $isOwner = $author === $user;

        // ----- Règles -----
        if ($attribute === self::EDIT) {
            // auteur OU (admin ET auteur = anonyme)
            return $isOwner || ($isAdmin && $this->isAnonymous($author));
        }

        if ($attribute === self::DELETE) {
            // auteur
            if ($isOwner) {
                return true;
            }

            // admin peut supprimer si tâche “anonyme”
            if ($isAdmin && $this->isAnonymous($author)) {
                return true;
            }

            return false;
        }

        return false;
    }


    private function isAnonymous(?User $author): bool
    {
        return $author && $author->getUsername() === 'anonyme';
    }


}
