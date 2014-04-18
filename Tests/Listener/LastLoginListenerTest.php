<?php

namespace Beelab\UserBundle\Tests\Listner;

use Beelab\UserBundle\Listener\LastLoginListener;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * @group unit
 */
class LastLoginListenerTestTest extends PHPUnit_Framework_TestCase
{
    protected
        $listener,
        $userManager;

    public function setUp()
    {
        $this->userManager = $this->getMockBuilder('Beelab\UserBundle\Manager\UserManager')->disableOriginalConstructor()->getMock();
        $this->listener = new LastLoginListener($this->userManager);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertArrayHasKey(SecurityEvents::INTERACTIVE_LOGIN, $this->listener->getSubscribedEvents());
    }

    public function testOnSecurityInteractiveLogin()
    {
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $event = $this->getMockBuilder('Symfony\Component\Security\Http\Event\InteractiveLoginEvent')->disableOriginalConstructor()->getMock();
        $event->expects($this->once())->method('getAuthenticationToken')->will($this->returnValue($token));
        $user = $this->getMock('Beelab\UserBundle\User\UserInterface');
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));
        $user->expects($this->once())->method('setLastLogin');
        $this->userManager->expects($this->once())->method('update');

        $this->listener->onSecurityInteractiveLogin($event);
    }
}