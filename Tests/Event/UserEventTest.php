<?php

namespace Beelab\UserBundle\Tests\Event;

use Beelab\UserBundle\Event\UserEvent;
use Beelab\UserBundle\Test\UserStub;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class UserEventTest extends TestCase
{
    public function testGetUser(): void
    {
        $user = new UserStub();
        $userEvent = new UserEvent($user);
        $this->assertEquals($user, $userEvent->getUser());
    }
}
