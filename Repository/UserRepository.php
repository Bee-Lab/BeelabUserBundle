<?php

namespace Beelab\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * UserRepository.
 */
class UserRepository extends EntityRepository implements UserProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this
            ->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
        if (empty($user)) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found', $username));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Istances of "%s" are not supported.', $class));
        }

        return $this->find($user->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }

    /**
     * See http://stackoverflow.com/a/16692911/369194.
     *
     * @param string $role
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function filterByRole($role)
    {
        $role = $role === 'ROLE_USER' ? '{}' : '"'.$role.'"';

        return $this
            ->createQueryBuilder('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%'.$role.'%');
    }

    /**
     * @param array $roles
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function filterByRoles(array $roles)
    {
        $qb = $this->createQueryBuilder('u');
        foreach ($roles as $key => $role) {
            $role = $role === 'ROLE_USER' ? '{}' : '"'.$role.'"';
            $qb->orWhere('u.roles LIKE :role'.$key)->setParameter('role'.$key, '%'.$role.'%');
        }

        return $qb;
    }
}
