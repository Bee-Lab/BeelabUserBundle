<?php

namespace Beelab\UserBundle\Tests\Manager;

use Beelab\UserBundle\Manager\UserManager;
use Beelab\UserBundle\Test\UserStub;
use Knp\Component\Pager\Pagination\PaginationInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @group unit
 */
class UserManagerTest extends TestCase
{
    protected $manager;

    protected $em;

    protected $repository;

    protected $encoder;

    protected $authChecker;

    protected $tokenStorage;

    protected $paginator;

    protected $dispatcher;

    protected function setUp(): void
    {
        $this->em = $this->createMock('Doctrine\Common\Persistence\ObjectManager');
        $this->encoder = $this->createMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $this->authChecker = $this->createMock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->tokenStorage = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        $this->paginator = $this->createMock('Knp\Component\Pager\PaginatorInterface');
        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()
            ->getMock();
        $this->dispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->em->expects($this->any())->method('getRepository')->with(UserStub::class)
            ->willReturn($this->repository);

        $this->manager = new UserManager(UserStub::class, $this->em, $this->encoder, $this->authChecker, $this->tokenStorage, $this->paginator, $this->dispatcher);
    }

    protected function getManager($withPaginator = true): void
    {
    }

    public function testList(): void
    {
        $pagination = $this->createMock(PaginationInterface::class);
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $this->repository->expects($this->once())->method('createQueryBuilder')->willReturn($qb);
        $this->paginator->expects($this->once())->method('paginate')->with($qb, 1, 20)
            ->willReturn($pagination);

        $this->assertInstanceOf(PaginationInterface::class, $this->manager->getList());
    }

    public function testListWithoutPaginator(): void
    {
        $manager = new UserManager(UserStub::class, $this->em, $this->encoder, $this->authChecker, $this->tokenStorage, null, $this->dispatcher);
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')->setMethods(['execute'])
            ->disableOriginalConstructor()->getMockForAbstractClass();
        $this->repository->expects($this->once())->method('createQueryBuilder')->willReturn($qb);
        $qb->expects($this->once())->method('getQuery')->willReturn($query);
        $query->expects($this->once())->method('execute')->willReturn([]);

        $this->assertEquals([], $manager->getList());
    }

    public function testLoad(): void
    {
        $user = $this->createMock('Beelab\UserBundle\Entity\User');
        $this->repository->expects($this->any())->method('__call')->with('loadUserByUsername', ['pippo@example.org'])
            ->willReturn($user);

        $this->assertEquals($user, $this->manager->loadUserByUsername('pippo@example.org'));
    }

    public function testGet(): void
    {
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $this->repository->expects($this->any())->method('find')->willReturn($user);

        $this->assertEquals($user, $this->manager->get(123));
    }

    public function testGetUserNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->repository->expects($this->any())->method('find')->willReturn(null);

        $this->manager->get(123);
    }

    public function testGetInstance(): void
    {
        $this->assertInstanceOf('Beelab\UserBundle\User\UserInterface', $this->manager->getInstance());
    }

    public function testDeleteAdminUserByNonSuperAdminUser(): void
    {
        $this->expectException(AccessDeniedException::class);
        $this->authChecker->expects($this->once())->method('isGranted')->with('ROLE_SUPER_ADMIN')
            ->willReturn(false);
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $user->expects($this->once())->method('hasRole')->with('ROLE_SUPER_ADMIN')->willReturn(true);

        $this->manager->delete($user);
    }

    public function testDeleteOwnUser(): void
    {
        $this->expectException(AccessDeniedException::class);
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $user->expects($this->once())->method('hasRole')->with('ROLE_SUPER_ADMIN')->willReturn(false);
        $token = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($token);
        $token->expects($this->once())->method('getUser')->willReturn($user);

        $this->manager->delete($user);
    }

    public function testDelete(): void
    {
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $currentUser = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $token = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($token);
        $token->expects($this->once())->method('getUser')->willReturn($currentUser);

        $this->em->expects($this->once())->method('remove');

        $this->manager->delete($user);
    }

    public function testAuthenticate(): void
    {
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $user->expects($this->once())->method('getPassword')->willReturn('foo');
        $user->expects($this->once())->method('getRoles')->willReturn([]);
        $this->tokenStorage->expects($this->once())->method('setToken');
        $this->dispatcher->expects($this->once())->method('dispatch');

        $this->manager->authenticate($user, $request);
    }

    public function testAuthenticateLogout(): void
    {
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $session = $this->createMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $user->expects($this->once())->method('getPassword')->willReturn('foo');
        $user->expects($this->once())->method('getRoles')->willReturn([]);
        $request->expects($this->once())->method('getSession')->willReturn($session);
        $session->expects($this->once())->method('invalidate');
        $this->tokenStorage->expects($this->once())->method('setToken');
        $this->dispatcher->expects($this->once())->method('dispatch');

        $this->manager->authenticate($user, $request, 'main', true);
    }
}
