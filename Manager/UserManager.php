<?php

namespace Beelab\UserBundle\Manager;

use Beelab\UserBundle\User\UserInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * User manager.
 */
class UserManager extends LightUserManager
{
    protected $authChecker;
    protected $tokenStorage;
    protected $paginator;
    protected $dispatcher;

    /**
     * @param string                        $class
     * @param ObjectManager                 $em
     * @param EncoderFactoryInterface       $encoder
     * @param AuthorizationCheckerInterface $authChecker
     * @param TokenStorageInterface         $tokenStorage
     * @param PaginatorInterface            $paginator
     * @param EventDispatcherInterface      $dispatcher
     */
    public function __construct($class, ObjectManager $em, EncoderFactoryInterface $encoder, AuthorizationCheckerInterface $authChecker, TokenStorageInterface $tokenStorage, PaginatorInterface $paginator = null, EventDispatcherInterface $dispatcher)
    {
        parent::__construct($class, $em, $encoder);
        $this->authChecker = $authChecker;
        $this->tokenStorage = $tokenStorage;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    /**
     * List of users (can be paginated).
     *
     * @param int $page
     * @param int $limit
     *
     * @return mixed \Knp\Component\Pager\Pagination\PaginationInterface or array
     */
    public function getList($page = 1, $limit = 20)
    {
        $qb = $this->repository->createQueryBuilder('u');
        if (!is_null($this->paginator)) {
            return $this->paginator->paginate($qb, $page, $limit);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * Find user by email.
     *
     * @param string $email
     *
     * @return UserInterface
     */
    public function find($email)
    {
        return $this->repository->findOneByEmail($email);
    }

    /**
     * Find user by id.
     *
     * @param int $id
     *
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
     * Delete user.
     *
     * @param UserInterface $user
     * @param bool          $flush
     */
    public function delete(UserInterface $user, $flush = true)
    {
        if ($user->hasRole('ROLE_SUPER_ADMIN') && !$this->authChecker->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('You cannot delete a super admin user.');
        }
        if ($this->tokenStorage->getToken()->getUser() == $user) {
            throw new AccessDeniedException('You cannot delete your user.');
        }
        $this->em->remove($user);
        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * Manual authentication.
     *
     * @param UserInterface $user
     * @param Request       $request
     * @param string        $firewall firewall name (see your security.yml config file)
     * @param bool          $logout   wether to logout before login
     */
    public function authenticate(UserInterface $user, Request $request, $firewall = 'main', $logout = false)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());
        if ($logout) {
            $request->getSession()->invalidate();
        }
        $this->tokenStorage->setToken($token);
        $event = new InteractiveLoginEvent($request, $token);
        $this->dispatcher->dispatch('security.interactive_login', $event);
    }
}
