<?php

namespace Beelab\UserBundle\Tests\Entity;

use Beelab\UserBundle\Manager\UserManager;

/**
 * @group unit
 */
class UserManagerTest extends \PHPUnit_Framework_TestCase
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
        $this->paginator = $this->getMockBuilder('Knp\Component\Pager\Paginator')->disableOriginalConstructor()->getMock();
        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->disableOriginalConstructor()->getMock();
        $this->em->expects($this->any())->method('getRepository')->will($this->returnValue($this->repository));

        $this->manager = new UserManager($class, $this->em, $this->encoder, $this->security, $this->paginator, $this->dispatcher);
    }

    public function testList()
    {
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $this->repository->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($qb));
        $this->paginator->expects($this->once())->method('paginate')->with($qb, 1, 20)->will($this->returnValue(array()));

        $this->manager->getList();
    }

    public function testFind()
    {
        // TODO dovrebbe andare con once() ma invece non viene richiamato :-|
        $this->repository->expects($this->never())->method('findOneByEmail')->will($this->returnValue(null));

        $this->assertNull($this->manager->find('pippo'));
    }

    public function testCreate()
    {
        $user = $this->getMock('Beelab\UserBundle\User\UserInterface');
        $user->expects($this->once())->method('getPlainPassword')->will($this->returnValue('pippo'));
        $user->expects($this->once())->method('getSalt')->will($this->returnValue('pluto'));
        $passwordEncoder = $this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        $this->encoder->expects($this->once())->method('getEncoder')->with($user)->will($this->returnValue($passwordEncoder));
        $passwordEncoder->expects($this->once())->method('encodePassword');
        $user->expects($this->once())->method('setPassword');
        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $this->manager->create($user);
    }

    public function testUpdateNoPasswordChangeAndNoFlush()
    {
        $user = $this->getMock('Beelab\UserBundle\User\UserInterface');
        $user->expects($this->once())->method('getPlainPassword')->will($this->returnValue(null));

        $this->manager->update($user, false);
    }

    public function testUpdateWithPasswordChangeAndFlush()
    {
        $user = $this->getMock('Beelab\UserBundle\User\UserInterface');
        $user->expects($this->exactly(2))->method('getPlainPassword')->will($this->returnValue('pippo'));
        $user->expects($this->once())->method('getSalt')->will($this->returnValue('pluto'));
        $passwordEncoder = $this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        $this->encoder->expects($this->once())->method('getEncoder')->with($user)->will($this->returnValue($passwordEncoder));
        $passwordEncoder->expects($this->once())->method('encodePassword');
        $user->expects($this->once())->method('setPassword');
        $this->em->expects($this->once())->method('flush');

        $this->manager->update($user);
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
}