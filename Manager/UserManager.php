<?php

namespace Beelab\UserBundle\Manager;

use Beelab\UserBundle\User\UserInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * User manager
 */
class UserManager
{
    protected $className, $repository, $em, $encoder, $security, $paginator, $dispatcher;

    /**
     * @param string                   $class
     * @param ObjectManager            $em
     * @param EncoderFactoryInterface  $encoder
     * @param SecurityContextInterface $security
     * @param PaginatorInterface       $paginator
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct($class, ObjectManager $em, EncoderFactoryInterface $encoder, SecurityContextInterface $security, PaginatorInterface $paginator, EventDispatcherInterface $dispatcher)
    {
        $this->className = $class;
        $this->em = $em;
        $this->encoder = $encoder;
        $this->security = $security;
        $this->paginator = $paginator;
        $this->repository = $em->getRepository($class);
        $this->dispatcher = $dispatcher;
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
     * @param  string        $email
     * @return UserInterface
     */
    public function find($email)
    {
        return $this->repository->findOneByEmail($email);
    }

    /**
     * Find user by id
     *
     * @param  integer       $id
     * @return UserInterface
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
     * Get a new instance of user
     *
     * @return UserInterface
     */
    public function getInstance()
    {
        return new $this->className;
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
     * Manual authentication
     *
     * @param UserInterface $user
     * @param Request       $request
     * @param string        $firewall firewall name (see your security.yml config file)
     * @param boolean       $logout   wether to logout before login
     */
    public function authenticate(UserInterface $user, Request $request, $firewall = 'main', $logout = false)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());
        if ($logout) {
            $request->getSession()->invalidate();
        }
        $this->security->setToken($token);
        $event = new InteractiveLoginEvent($request, $token);
        $this->dispatcher->dispatch('security.interactive_login', $event);
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
