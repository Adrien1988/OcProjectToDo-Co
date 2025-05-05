<?php

/**  tests/Unit/Security/Voter/TaskVoterTest.php
 *   Couverture complète du TaskVoter en Symfony 6.4
 *   ──────────────────────────────────────────────
 *   • CREATE autorisé pour tout utilisateur authentifié
 *   • EDIT / DELETE autorisés ou refusés selon owner / admin / anon
 *   • Branche « utilisateur anonyme » testée avec NullToken
 */

namespace Tests\Unit\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use App\Security\Voter\TaskVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TaskVoterTest extends TestCase
{
    private TaskVoter $voter;

    protected function setUp(): void
    {
        $this->voter = new TaskVoter();
    }

    /* ------------------------------------------------------------
       Helpers
    ------------------------------------------------------------ */
    private function token(User $user): UsernamePasswordToken
    {
        //  __construct(UserInterface $user, string $firewallName, array $roles = [])
        return new UsernamePasswordToken($user, 'memory', $user->getRoles());
    }

    private static function granted(int $decision): bool
    {
        return $decision === VoterInterface::ACCESS_GRANTED;
    }

    /* ------------------------------------------------------------
       CREATE – tout user connecté
    ------------------------------------------------------------ */
    public function test_create_is_allowed_for_any_authenticated_user(): void
    {
        $user = new User();

        $res = $this->voter->vote($this->token($user), null, [TaskVoter::CREATE]);
        $this->assertTrue(self::granted($res));
    }

    /* ------------------------------------------------------------
       EDIT
    ------------------------------------------------------------ */
    public function test_edit_allowed_for_owner(): void
    {
        $owner = new User();
        $task  = (new Task())->setAuthor($owner);

        $res = $this->voter->vote($this->token($owner), $task, [TaskVoter::EDIT]);
        $this->assertTrue(self::granted($res));
    }

    public function test_edit_denied_for_other_user(): void
    {
        $owner = new User();
        $task  = (new Task())->setAuthor($owner);
        $other = (new User())->setUsername('charlie');

        $res = $this->voter->vote($this->token($other), $task, [TaskVoter::EDIT]);
        $this->assertFalse(self::granted($res));
    }

    /* ------------------------------------------------------------
       DELETE
    ------------------------------------------------------------ */
    public function test_delete_allowed_for_owner(): void
    {
        $owner = new User();
        $task  = (new Task())->setAuthor($owner);

        $res = $this->voter->vote($this->token($owner), $task, [TaskVoter::DELETE]);
        $this->assertTrue(self::granted($res));
    }

    public function test_delete_denied_for_other_user(): void
    {
        $owner = new User();
        $task  = (new Task())->setAuthor($owner);
        $other = (new User())->setUsername('eve');

        $res = $this->voter->vote($this->token($other), $task, [TaskVoter::DELETE]);
        $this->assertFalse(self::granted($res));
    }

    /* ------------------------------------------------------------
       ADMIN sur tâche « anonyme »
    ------------------------------------------------------------ */
    public function test_admin_can_edit_and_delete_anonymous_task(): void
    {
        $admin = (new User())->setUsername('root')->setRoles(['ROLE_ADMIN']);
        $anon  = (new User())->setUsername('anonyme');          // auteur « anonyme »
        $task  = (new Task())->setAuthor($anon);

        $this->assertTrue(
            self::granted($this->voter->vote($this->token($admin), $task, [TaskVoter::EDIT]))
        );
        $this->assertTrue(
            self::granted($this->voter->vote($this->token($admin), $task, [TaskVoter::DELETE]))
        );
    }

    /* ------------------------------------------------------------
       Branche « utilisateur ANONYME » – NullToken (SF 6.4)
    ------------------------------------------------------------ */
    public function test_actions_denied_for_anonymous_token(): void
    {
        $task  = new Task();
        $token = new NullToken();

        $this->assertSame(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote($token, $task, [TaskVoter::EDIT])
        );
        $this->assertSame(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote($token, $task, [TaskVoter::DELETE])
        );
    }

    /* ------------------------------------------------------------
   ADMIN ne peut PAS supprimer la tâche d’un autre utilisateur
   (auteur ≠ admin ET auteur n’est pas « anonyme »)
------------------------------------------------------------ */
    public function test_admin_cannot_delete_task_of_other_user(): void
    {
        $owner = (new User())->setUsername('bob');
        $task  = (new Task())->setAuthor($owner);

        $admin = (new User())->setUsername('root')->setRoles(['ROLE_ADMIN']);

        $decision = $this->voter->vote($this->token($admin), $task, [TaskVoter::DELETE]);

        $this->assertFalse(self::granted($decision));
    }
}
