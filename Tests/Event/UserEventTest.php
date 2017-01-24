<?php

namespace Beelab\UserBundle\Tests\Event;

use Beelab\UserBundle\Event\UserEvent;
use Beelab\UserBundle\Test\UserStub;
use PHPUnit_Framework_TestCase;

/**
 * @group unit
 */
class UserEventTest extends PHPUnit_Framework_TestCase
{
    public function testGetUser()
    {
        $user = new UserStub();
        $userEvent = new UserEvent($user);
        $this->assertEquals($user, $userEvent->getUser());
    }
}
