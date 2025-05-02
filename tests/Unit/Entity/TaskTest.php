<?php
namespace Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/** @covers \App\Entity\Task */
class TaskTest extends TestCase
{
    public function test_default_state(): void
    {
        $t = new Task();

        $this->assertFalse($t->isDone());
        $this->assertInstanceOf(\DateTimeInterface::class, $t->getCreatedAt());
    }

    public function test_toggle_changes_state(): void
    {
        $t = new Task();
        $t->toggle(true);

        $this->assertTrue($t->isDone());

        $t->toggle(false);
        $this->assertFalse($t->isDone());
    }

    public function test_setters(): void
    {
        $t = new Task();
        $t->setTitle('Acheter du pain');
        $t->setContent('Baguette tradition');

        $this->assertSame('Acheter du pain', $t->getTitle());
        $this->assertSame('Baguette tradition', $t->getContent());
    }

    public function test_author_relation(): void
    {
        $user = (new User())->setUsername('bob');
        $task = new Task();
        $task->setAuthor($user);

        $this->assertSame($user, $task->getAuthor());
    }

    public function test_set_created_at(): void
    {
        $t   = new Task();
        $now = new \DateTimeImmutable('2025-04-28 10:00:00');
        $t->setCreatedAt($now);

        $this->assertSame($now, $t->getCreatedAt());
    }
}
