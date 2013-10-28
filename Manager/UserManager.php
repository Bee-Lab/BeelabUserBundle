<?php

namespace Beelab\UserBundle\Manager;

use Beelab\UserBundle\User\UserInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * User manager
 */
class UserManager
{
    protected $repository, $em, $encoder, $security, $paginator;

    /**
     * @param string                   $class
     * @param ObjectManager            $em
     * @param EncoderFactoryInterface  $encoder
     * @param SecurityContextInterface $security
     * @param Paginator                $paginator
     */
    public function __construct($class, ObjectManager $em, EncoderFactoryInterface $encoder, SecurityContextInterface $security, Paginator $paginator)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->security = $security;
        $this->paginator = $paginator;
        $this->repository = $em->getRepository($class);
    }

    /**
     * List of users
     *
     * @param  integer   $page
     * @param  integer   $limit
     * @return Paginator
     */
    public function getList($page = 1, $limit = 20)
    {
        $qb = $this->repository->createQueryBuilder('u');

        return $this->paginator->paginate($qb, $page, $limit);
    }

    /**
     * Find user by email
     *
     * @param  string $email
     * @return User
     */
    public function find($email)
    {
        return $this->repository->findOneByEmail($email);
    }

    /**
     * Find user by id
     *
     * @param  integer $id
     * @return User
     */
    public function get($id)
    {
        $user = $this->repository->find($id);
        if (empty($user)) {
            throw new NotFoundHttpException(sprintf('Cannot find user with id %u', $id));
        }

        return $user;
    }

    /**
     * Create new user
     *
     * @param UserInterface $user
     * @param boolean       $flush
     */
    public function create(UserInterface $user, $flush = true)
    {
        $this->updatePassword($user);
        $this->em->persist($user);
        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * Update user
     *
     * @param UserInterface $user
     * @param boolean       $flush
     */
    public function update(UserInterface $user, $flush = true)
    {
        if (!is_null($user->getPlainPassword())) {
            $this->updatePassword($user);
        }
        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * Delete user
     *
     * @param UserInterface $user
     * @param boolean       $flush
     */
    public function delete(UserInterface $user, $flush = true)
    {
        if ($user->hasRole('ROLE_SUPER_ADMIN') && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('You cannot delete a super admin user.');
        }
        if ($this->security->getToken()->getUser() == $user) {
            throw new AccessDeniedException('You cannot delete your user.');
        }
        $this->em->remove($user);
        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * Password update
     *
     * @param UserInterface $user
     */
    protected function updatePassword(UserInterface $user)
    {
        $passwordEncoder = $this->encoder->getEncoder($user);
        $password = $passwordEncoder->encodePassword($user->getPlainPassword(), $user->getSalt());
        $user->setPassword($password);
    }
}