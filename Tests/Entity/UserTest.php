<?php

namespace Beelab\UserBundle\Tests\Entity;

use Beelab\UserBundle\Entity\User;

/**
 * @group unit
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRoleLabel()
    {
        $user = new User();

        $this->assertEquals('admin', $user->getRoleLabel('ROLE_ADMIN'));
        $this->assertEquals('user', $user->getRoleLabel('ROLE_USER'));
    }

    public function testHasRole()
    {
        $user = new User();
        $user->addRole('ROLE_PIPPO');

        $this->assertTrue($user->hasRole('ROLE_PIPPO'));
        $this->assertFalse($user->hasRole('ROLE_ADMIN'));
    }
}