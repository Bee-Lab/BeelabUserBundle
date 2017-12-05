<?php

namespace Beelab\UserBundle\Tests\Manager;

use Beelab\UserBundle\Manager\UserManager;
use Beelab\UserBundle\Test\UserStub;
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

    protected function setUp()
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
            ->will($this->returnValue($this->repository));

        $this->manager = new UserManager(UserStub::class, $this->em, $this->encoder, $this->authChecker, $this->tokenStorage, $this->paginator, $this->dispatcher);
    }

    protected function getManager($withPaginator = true)
    {
    }

    public function testList()
    {
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $this->repository->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($qb));
        $this->paginator->expects($this->once())->method('paginate')->with($qb, 1, 20)
            ->will($this->returnValue([]));

        $this->assertEquals([], $this->manager->getList());
    }

    public function testListWithoutPaginator()
    {
        $manager = new UserManager(UserStub::class, $this->em, $this->encoder, $this->authChecker, $this->tokenStorage, null, $this->dispatcher);
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')->setMethods(['execute'])
            ->disableOriginalConstructor()->getMockForAbstractClass();
        $this->repository->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($qb));
        $qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));
        $query->expects($this->once())->method('execute')->will($this->returnValue([]));

        $this->assertEquals([], $manager->getList());
    }

    public function testLoad()
    {
        $user = $this->createMock('Beelab\UserBundle\Entity\User');
        $this->repository->expects($this->any())->method('__call')->with('loadUserByUsername', ['pippo@example.org'])
            ->will($this->returnValue($user));

        $this->assertEquals($user, $this->manager->loadUserByUsername('pippo@example.org'));
    }

    public function testGet()
    {
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $this->repository->expects($this->any())->method('find')->will($this->returnValue($user));

        $this->assertEquals($user, $this->manager->get(123));
    }

    public function testGetUserNotFound()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->repository->expects($this->any())->method('find')->will($this->returnValue(null));

        $this->manager->get(123);
    }

    public function testGetInstance()
    {
        $this->assertInstanceOf('Beelab\UserBundle\User\UserInterface', $this->manager->getInstance());
    }

    public function testDeleteAdminUserByNonSuperAdminUser()
    {
        $this->expectException(AccessDeniedException::class);
        $this->authChecker->expects($this->once())->method('isGranted')->with('ROLE_SUPER_ADMIN')
            ->will($this->returnValue(false));
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $user->expects($this->once())->method('hasRole')->with('ROLE_SUPER_ADMIN')->will($this->returnValue(true));

        $this->manager->delete($user);
    }

    public function testDeleteOwnUser()
    {
        $this->expectException(AccessDeniedException::class);
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $user->expects($this->once())->method('hasRole')->with('ROLE_SUPER_ADMIN')->will($this->returnValue(false));
        $token = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->tokenStorage->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $this->manager->delete($user);
    }

    public function testDelete()
    {
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $currentUser = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $token = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->tokenStorage->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($currentUser));

        $this->em->expects($this->once())->method('remove');

        $this->manager->delete($user);
    }

    public function testAuthenticate()
    {
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $user->expects($this->once())->method('getPassword')->will($this->returnValue('foo'));
        $user->expects($this->once())->method('getRoles')->will($this->returnValue([]));
        $this->tokenStorage->expects($this->once())->method('setToken');
        $this->dispatcher->expects($this->once())->method('dispatch');

        $this->manager->authenticate($user, $request);
    }

    public function testAuthenticateLogout()
    {
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $session = $this->createMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $user->expects($this->once())->method('getPassword')->will($this->returnValue('foo'));
        $user->expects($this->once())->method('getRoles')->will($this->returnValue([]));
        $request->expects($this->once())->method('getSession')->will($this->returnValue($session));
        $session->expects($this->once())->method('invalidate');
        $this->tokenStorage->expects($this->once())->method('setToken');
        $this->dispatcher->expects($this->once())->method('dispatch');

        $this->manager->authenticate($user, $request, 'main', true);
    }
}
