<?php

namespace Beelab\UserBundle\Listener;

use Beelab\UserBundle\Manager\LightUserManager;
use Beelab\UserBundle\User\UserInterface;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Set "last login" date when user login.
 */
class LastLoginListener implements EventSubscriberInterface
{
    protected $userManager;

    public function __construct(LightUserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        ];
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if ($user instanceof UserInterface) {
            $user->setLastLogin(new DateTime());
            $this->userManager->update($user);
        }
    }
}
