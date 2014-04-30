<?php

namespace Beelab\UserBundle\Tests\Entity;

use Beelab\UserBundle\Manager\UserManager;
use PHPUnit_Framework_TestCase;

/**
 * @group unit
 */
class UserManagerTest extends PHPUnit_Framework_TestCase
{
    protected
        $manager,
        $em,
        $repository,
        $encoder,
        $security,
        $paginator,
        $dispatcher;

    public function setUp()
    {
        $class = 'Beelab\UserBundle\Entity\User';
        $this->em = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->encoder = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $this->security = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->paginator = $this->getMock('Knp\Component\Pager\PaginatorInterface');
        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->em->expects($this->once())->method('getRepository')->with($class)->will($this->returnValue($this->repository));

        $this->manager = new UserManager($class, $this->em, $this->encoder, $this->security, $this->paginator, $this->dispatcher);
    }

    public function testList()
    {
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $this->repository->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($qb));
        $this->paginator->expects($this->once())->method('paginate')->with($qb, 1, 20)->will($this->returnValue(array()));

        $this->assertEquals(array(), $this->manager->getList());
    }

    public function testFind()
    {
        $user = $this->getMock('Beelab\UserBundle\User\UserInterface');
        $this->repository->expects($this->any())->method('__call')->with('findOneByEmail', array('pippo@example.org'))->will($this->returnValue($user));

        $this->assertEquals($user, $this->manager->find('pippo@example.org'));
    }

    public function testGet()
    {
        $user = $this->getMock('Beelab\UserBundle\User\UserInterface');
        $this->repository->expects($this->any())->method('find')->will($this->returnValue($user));

        $this->assertEquals($user, $this->manager->get(123));
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetUserNotFound()
    {
        $this->repository->expects($this->any())->method('find')->will($this->returnValue(null));

        $this->manager->get(123);
    }

    public function testGetInstance()
    {
        $this->assertInstanceOf('Beelab\UserBundle\User\UserInterface', $this->manager->getInstance());
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testDeleteAdminUserByNonSuperAdminUser()
    {
        $this->security->expects($this->once())->method('isGranted')->with('ROLE_SUPER_ADMIN')->will($this->returnValue(false));
        $user = $this->getMock('Beelab\UserBundle\User\UserInterface');
        $user->expects($this->once())->method('hasRole')->with('ROLE_SUPER_ADMIN')->will($this->returnValue(true));

        $this->manager->delete($user);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testDeleteOwnUser()
    {
        $user = $this->getMock('Beelab\UserBundle\User\UserInterface');
        $user->expects($this->once())->method('hasRole')->with('ROLE_SUPER_ADMIN')->will($this->returnValue(false));
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $this->manager->delete($user);
    }

    public function testDelete()
    {
        $user = $this->getMock('Beelab\UserBundle\User\UserInterface');
        $currentUser = $this->getMock('Beelab\UserBundle\User\UserInterface');
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($currentUser));

        $this->em->expects($this->once())->method('remove');

        $this->manager->delete($user);
    }

    public function testAuthenticate()
    {
        $user = $this->getMock('Beelab\UserBundle\User\UserInterface');
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $user->expects($this->once())->method('getPassword')->will($this->returnValue('foo'));
        $user->expects($this->once())->method('getRoles')->will($this->returnValue(array()));
        $this->security->expects($this->once())->method('setToken');
        $this->dispatcher->expects($this->once())->method('dispatch');

        $this->manager->authenticate($user, $request);
    }

    public function testAuthenticateLogout()
    {
        $user = $this->getMock('Beelab\UserBundle\User\UserInterface');
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $user->expects($this->once())->method('getPassword')->will($this->returnValue('foo'));
        $user->expects($this->once())->method('getRoles')->will($this->returnValue(array()));
        $request->expects($this->once())->method('getSession')->will($this->returnValue($session));
        $session->expects($this->once())->method('invalidate');
        $this->security->expects($this->once())->method('setToken');
        $this->dispatcher->expects($this->once())->method('dispatch');

        $this->manager->authenticate($user, $request, 'main', true);
    }
}
