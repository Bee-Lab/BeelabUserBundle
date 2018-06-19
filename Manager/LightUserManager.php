<?php

namespace Beelab\UserBundle\Manager;

use Beelab\UserBundle\User\UserInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * Light User manager.
 */
class LightUserManager implements LightUserManagerInterface
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoder;

    public function __construct(string $class, ObjectManager $em, EncoderFactoryInterface $encoder)
    {
        $this->className = $class;
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->encoder = $encoder;
    }

    public function getInstance(): UserInterface
    {
        return new $this->className();
    }

    public function create(UserInterface $user, bool $flush = true): void
    {
        $this->updatePassword($user);
        $this->em->persist($user);
        if ($flush) {
            $this->em->flush();
        }
    }

    public function update(UserInterface $user, bool $flush = true): void
    {
        if (null !== $user->getPlainPassword()) {
            $this->updatePassword($user);
        }
        if ($flush) {
            $this->em->flush();
        }
    }

    protected function updatePassword(UserInterface $user): void
    {
        $passwordEncoder = $this->encoder->getEncoder($user);
        $password = $passwordEncoder->encodePassword($user->getPlainPassword(), $user->getSalt());
        $user->setPassword($password);
    }
}
