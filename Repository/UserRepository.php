<?php

namespace Beelab\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserRepository extends EntityRepository implements UserProviderInterface
{
    /**
     * @inheritDoc
     */
    public function loadUserByUsername($email)
    {
        try {
            $user = $this
                ->createQueryBuilder('u')
                ->where('u.email = :email')
                ->setParameter('email', $email)
                ->getQuery()
                ->getSingleResult();
            ;
        } catch (NoResultException $e) {
            $message = sprintf('Utente "%s" non trovato', $email);
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf('Istanze di "%s" non supportate.', $class)
            );
        }

        return $this->find($user->getId());
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}
