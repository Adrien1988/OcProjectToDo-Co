<?php
namespace Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/** @covers \App\Entity\User */
class UserTest extends TestCase
{
    public function test_default_state(): void
    {
        $u = new User();

        // rôle par défaut
        $this->assertSame(['ROLE_USER'], $u->getRoles());
        // collection de tâches vide
        $this->assertCount(0, $u->getTasks());
    }

    public function test_username_and_identifier(): void
    {
        $u = (new User())->setUsername('alice');

        $this->assertSame('alice', $u->getUsername());
        $this->assertSame('alice', $u->getUserIdentifier());
    }

    public function test_set_roles_promotes_admin_and_fallback_user(): void
    {
        $u = new User();

        // promotion admin
        $u->setRoles(['ROLE_ADMIN']);
        $this->assertSame(['ROLE_ADMIN'], $u->getRoles());

        // toute autre valeur retombe sur ROLE_USER
        $u->setRoles(['FOO']);
        $this->assertSame(['ROLE_USER'], $u->getRoles());
    }

    public function test_password_accessors_and_salt(): void
    {
        $u = (new User())->setPassword('hash');
        $this->assertSame('hash', $u->getPassword());
        $this->assertNull($u->getSalt());
    }

    /** couvre setEmail / getEmail */
    public function test_email_accessors(): void
    {
        $u = (new User())->setEmail('john@example.com');
        $this->assertSame('john@example.com', $u->getEmail());
    }
}
