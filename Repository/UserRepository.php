<?php

namespace Beelab\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserRepository extends EntityRepository implements UserProviderInterface, UserLoaderInterface
{
    public function loadUserByUsername($username): UserInterface
    {
        $user = $this
            ->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        if (empty($user)) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found', $username));
        }
        if (!$user->isActive()) {
            throw new DisabledException('User is not active.');
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): ?UserInterface
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Istances of "%s" are not supported.', $class));
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class): bool
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }

    /**
     * See http://stackoverflow.com/a/16692911/369194.
     *
     * @param string $role
     *
     * @return QueryBuilder
     */
    public function filterByRole(string $role): QueryBuilder
    {
        $role = $role === 'ROLE_USER' ? '{}' : '"'.$role.'"';

        return $this
            ->createQueryBuilder('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%'.$role.'%')
        ;
    }

    public function filterByRoles(array $roles): QueryBuilder
    {
        $qb = $this->createQueryBuilder('u');
        foreach ($roles as $key => $role) {
            $role = $role === 'ROLE_USER' ? '{}' : '"'.$role.'"';
            $qb->orWhere('u.roles LIKE :role'.$key)->setParameter('role'.$key, '%'.$role.'%');
        }

        return $qb;
    }
}
