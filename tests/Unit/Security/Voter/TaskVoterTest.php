<?php

namespace Tests\Unit\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use App\Security\Voter\TaskVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/** @covers \App\Security\Voter\TaskVoter */
class TaskVoterTest extends TestCase
{
    private TaskVoter $voter;

    protected function setUp(): void
    {
        $this->voter = new TaskVoter();
    }

    private function token(User $user): UsernamePasswordToken
    {
        // Depuis Symfony 6.4 : __construct(UserInterface $user, string $firewallName, array $roles = [])
        return new UsernamePasswordToken($user, 'memory', $user->getRoles());
    }

    private function isGranted(int $result): bool
    {
        return $result === VoterInterface::ACCESS_GRANTED;
    }

    public function test_create_is_allowed_for_any_authenticated_user(): void
    {
        $user = new User();

        $result = $this->voter->vote($this->token($user), null, [TaskVoter::CREATE]);
        $this->assertTrue($this->isGranted($result));
    }

    public function test_edit_allowed_for_owner(): void
    {
        $owner = new User();
        $task  = (new Task())->setAuthor($owner);

        $result = $this->voter->vote($this->token($owner), $task, [TaskVoter::EDIT]);
        $this->assertTrue($this->isGranted($result));
    }

    public function test_edit_denied_for_other_user(): void
    {
        $owner = new User();
        $task  = (new Task())->setAuthor($owner);
        $other = (new User())->setUsername('charlie');

        $result = $this->voter->vote($this->token($other), $task, [TaskVoter::EDIT]);
        $this->assertFalse($this->isGranted($result));
    }

    public function test_admin_can_edit_and_delete_anonymous_task(): void
    {
        $admin = (new User())->setUsername('root')->setRoles(['ROLE_ADMIN']);
        $anon  = (new User())->setUsername('anonyme');
        $task  = (new Task())->setAuthor($anon);

        $this->assertTrue(
            $this->isGranted(
                $this->voter->vote($this->token($admin), $task, [TaskVoter::EDIT])
            )
        );
        $this->assertTrue(
            $this->isGranted(
                $this->voter->vote($this->token($admin), $task, [TaskVoter::DELETE])
            )
        );
    }

    public function test_delete_by_owner_is_allowed(): void
    {
        $owner = new User();
        $task  = (new Task())->setAuthor($owner);

        $this->assertTrue(
            $this->isGranted(
                $this->voter->vote($this->token($owner), $task, [TaskVoter::DELETE])
            )
        );
    }

    public function test_delete_denied_for_other_user(): void
    {
        $owner = new User();
        $task  = (new Task())->setAuthor($owner);
        $other = (new User())->setUsername('eve');

        $this->assertFalse(
            $this->isGranted(
                $this->voter->vote($this->token($other), $task, [TaskVoter::DELETE])
            )
        );
    }
}
