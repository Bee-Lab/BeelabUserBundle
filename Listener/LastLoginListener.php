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

    /**
     * @param LightUserManager $userManager
     */
    public function __construct(LightUserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        ];
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if ($user instanceof UserInterface) {
            $user->setLastLogin(new DateTime());
            $this->userManager->update($user);
        }
    }
}
