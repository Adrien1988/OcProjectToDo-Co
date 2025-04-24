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
        return in_array($attribute, [self::EDIT, self::DELETE, self::CREATE])
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

        // les admins peuvent tout faire
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // règle par opération
        return match ($attribute) {
            self::CREATE => true,
            self::EDIT   => $subject?->getAuthor() === $user,
            self::DELETE => $subject?->getAuthor() === $user,
            default      => false,
        };
    }


}
