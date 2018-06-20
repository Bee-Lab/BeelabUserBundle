<?php

namespace Beelab\UserBundle\Tests\Listener;

use Beelab\UserBundle\Listener\LastLoginListener;
use Beelab\UserBundle\Manager\UserManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * @group unit
 */
class LastLoginListenerTest extends TestCase
{
    protected $listener;
    protected $userManager;

    protected function setUp(): void
    {
        $this->userManager = $this->getMockBuilder(UserManager::class)->disableOriginalConstructor()->getMock();
        $this->listener = new LastLoginListener($this->userManager);
    }

    public function testGetSubscribedEvents(): void
    {
        $this->assertArrayHasKey(SecurityEvents::INTERACTIVE_LOGIN, $this->listener->getSubscribedEvents());
    }

    public function testOnSecurityInteractiveLogin(): void
    {
        $token = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $event = $this->getMockBuilder(InteractiveLoginEvent::class)->disableOriginalConstructor()->getMock();
        $event->expects($this->once())->method('getAuthenticationToken')->will($this->returnValue($token));
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));
        $user->expects($this->once())->method('setLastLogin');
        $this->userManager->expects($this->once())->method('update')->with($user);

        $this->listener->onSecurityInteractiveLogin($event);
    }
}
