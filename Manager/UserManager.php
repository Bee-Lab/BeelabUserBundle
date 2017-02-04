<?php

namespace Beelab\UserBundle\Manager;

use Beelab\UserBundle\User\UserInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * User manager.
 */
class UserManager extends LightUserManager
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authChecker;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @param string                        $class
     * @param ObjectManager                 $em
     * @param EncoderFactoryInterface       $encoder
     * @param AuthorizationCheckerInterface $authChecker
     * @param TokenStorageInterface         $tokenStorage
     * @param PaginatorInterface            $paginator
     * @param EventDispatcherInterface      $dispatcher
     */
    public function __construct(
        string $class,
        ObjectManager $em,
        EncoderFactoryInterface $encoder,
        AuthorizationCheckerInterface $authChecker,
        TokenStorageInterface $tokenStorage,
        PaginatorInterface $paginator = null,
        EventDispatcherInterface $dispatcher
    ) {
        parent::__construct($class, $em, $encoder);
        $this->authChecker = $authChecker;
        $this->tokenStorage = $tokenStorage;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    /**
     * List of users (can be paginated).
     *
     * @param int    $page
     * @param int    $limit
     * @param string $sortBy
     *
     * @return mixed \Knp\Component\Pager\Pagination\PaginationInterface or array
     */
    public function getList(int $page = 1, int $limit = 20, string $sortBy = 'email')
    {
        $this->getQueryBuilder();
        $this->queryBuilder->orderBy('u.'.$sortBy);
        if (!is_null($this->paginator)) {
            return $this->paginator->paginate($this->queryBuilder, $page, $limit);
        }

        return $this->queryBuilder->getQuery()->execute();
    }

    /**
     * Find user by email.
     *
     * @deprecated Use loadUserByUsername() instead
     *
     * @param string $email
     *
     * @return UserInterface|null
     */
    public function find(string $email)
    {
        @trigger_error('Retrieving user with find() is deprecated. Use loadUserByUsername() instead.', E_USER_DEPRECATED);

        return $this->loadUserByUsername($email);
    }

    /**
     * Find user.
     *
     * @param string $email
     *
     * @return UserInterface|null
     */
    public function loadUserByUsername(string $email)
    {
        if (is_null($user = $this->repository->findOneByEmail($email))) {
            return;
        }
        if (!$user->isActive()) {
            throw new DisabledException('User is not active.');
        }

        return $user;
    }

    /**
     * Find user by id.
     *
     * @param int $id
     *
     * @return UserInterface
     */
    public function get(int $id): UserInterface
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
    public function delete(UserInterface $user, bool $flush = true)
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
    public function authenticate(UserInterface $user, Request $request, string $firewall = 'main', bool $logout = false)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());
        if ($logout) {
            $request->getSession()->invalidate();
        }
        $this->tokenStorage->setToken($token);
        $event = new InteractiveLoginEvent($request, $token);
        $this->dispatcher->dispatch('security.interactive_login', $event);
    }

    /**
     * Get QueryBuilder.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        if (is_null($this->queryBuilder)) {
            $this->queryBuilder = $this->repository->createQueryBuilder('u');
        }

        return $this->queryBuilder;
    }
}
