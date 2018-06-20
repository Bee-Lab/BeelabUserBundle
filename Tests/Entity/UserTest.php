<?php

namespace Beelab\UserBundle\Tests\Entity;

use Beelab\UserBundle\Test\UserStub as User;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class UserTest extends TestCase
{
    public function testGetRoleLabel(): void
    {
        $user = new User();

        $this->assertEquals('admin', $user->getRoleLabel('ROLE_ADMIN'));
        $this->assertEquals('user', $user->getRoleLabel('ROLE_USER'));
    }

    public function testGetRolesWithLabel(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        $this->assertEquals('admin, user', $user->getRolesWithLabel());
        $this->assertEquals('admin | user', $user->getRolesWithLabel(' | '));
    }

    public function testGetRolesWithLabelForUser(): void
    {
        $user = new User();

        $this->assertEquals('user', $user->getRolesWithLabel());
    }

    public function testHasRole(): void
    {
        $user = new User();
        $user->addRole('ROLE_PIPPO');

        $this->assertTrue($user->hasRole('ROLE_PIPPO'));
        $this->assertFalse($user->hasRole('ROLE_ADMIN'));
    }

    public function testIsEqualTo(): void
    {
        $user1 = new User();
        $user1->setEmail('user1@example.org');
        $user2 = new User();
        $user2->setEmail('user1@example.org');
        $user3 = new User();
        $user3->setEmail('user3@example.org');

        $this->assertTrue($user1->isEqualTo($user2));
        $this->assertFalse($user1->isEqualTo($user3));
    }

    public function testSerialize(): void
    {
        $user = new User();
        $user->setEmail('user@example.org');

        $this->assertEquals('a:2:{i:0;i:42;i:1;s:16:"user@example.org";}', $user->serialize());
    }

    public function testUnserialize(): void
    {
        $user = new User();
        $user->unserialize('a:2:{i:0;i:42;i:1;s:16:"user@example.org";}');

        $this->assertEquals(42, $user->getId());
        $this->assertEquals('user@example.org', $user->getEmail());
    }

    public function testRemoveRole(): void
    {
        $user = new User();
        $user->addRole('ROLE_PIPPO')->addRole('ROLE_PLUTO')->removeRole('ROLE_PIPPO');

        $this->assertTrue($user->hasRole('ROLE_PLUTO'));
        $this->assertFalse($user->hasRole('ROLE_PIPPO'));
    }

    public function testToString(): void
    {
        $user = new User();
        $user->setEmail('user@example.org');

        $this->assertEquals('user@example.org', $user->__toString());
    }

    public function testPassword(): void
    {
        $user = new User();
        $user->setPassword('astring');

        $this->assertEquals('astring', $user->getPassword());
    }

    public function testLastLogin(): void
    {
        $datetime = new \DateTime();
        $user = new User();
        $user->setLastLogin($datetime);

        $this->assertEquals($datetime, $user->getLastLogin());
    }
}
