<?php

namespace Beelab\UserBundle\Tests\Listner;

use Beelab\UserBundle\Listener\LastLoginListener;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * @group unit
 */
class LastLoginListenerTestTest extends \PHPUnit_Framework_TestCase
{
    protected
        $listener,
        $em;

    public function setUp()
    {
        $this->em = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->listener = new LastLoginListener($this->em);
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
        $this->em->expects($this->once())->method('flush');

        $this->listener->onSecurityInteractiveLogin($event);
    }
}