<?php

namespace Beelab\UserBundle\Tests\Repository;

use Beelab\UserBundle\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class UserRepositoryTest extends TestCase
{
    protected $repository;
    protected $em;
    protected $class;

    public function setUp()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $this->class = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')->disableOriginalConstructor()
            ->getMock();
        $this->class->name = 'Beelab\UserBundle\Test\UserStub';

        $this->repository = new UserRepository($this->em, $this->class);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByUsernameNotFound()
    {
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')->setMethods(['getOneOrNullResult'])
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $this->em->expects($this->any())->method('createQueryBuilder')->will($this->returnValue($queryBuilder));
        $queryBuilder->expects($this->any())->method('select')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('from')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('where')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('setParameter')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('getQuery')->will($this->returnValue($query));
        $query->expects($this->any())->method('getOneOrNullResult')->will($this->returnValue(null));

        $this->assertInstanceOf('Symfony\Component\Security\Core\User\UserInterface',
                                $this->repository->loadUserByUsername('foo'));
    }

    public function testLoadUserByUsernameFound()
    {
        $user = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')->setMethods(['getOneOrNullResult'])
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $this->em->expects($this->any())->method('createQueryBuilder')->will($this->returnValue($queryBuilder));
        $queryBuilder->expects($this->any())->method('select')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('from')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('where')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('setParameter')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('getQuery')->will($this->returnValue($query));
        $query->expects($this->any())->method('getOneOrNullResult')->will($this->returnValue($user));

        $this->assertInstanceOf('Symfony\Component\Security\Core\User\UserInterface',
                                $this->repository->loadUserByUsername('baz'));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testRefreshUserUnsupported()
    {
        $user = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $this->repository->refreshUser($user);
    }

    public function testRefreshUserSupported()
    {
        $user = $this->createMock('Beelab\UserBundle\Test\UserStub');
        $this->repository->refreshUser($user);
    }

    public function testSupportsClass()
    {
        $this->assertTrue($this->repository->supportsClass('Beelab\UserBundle\Test\UserStub'));
    }

    public function testSupportsClassFalse()
    {
        $this->assertFalse($this->repository->supportsClass('Foo'));
    }

    public function testFilterByRole()
    {
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $queryBuilder->expects($this->any())->method('select')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('from')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('where')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('setParameter')->will($this->returnSelf());
        $this->em->expects($this->any())->method('createQueryBuilder')->will($this->returnValue($queryBuilder));

        $this->assertEquals($queryBuilder, $this->repository->filterByRole('ROLE'));
    }

    public function testFilterByRoles()
    {
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $queryBuilder->expects($this->any())->method('select')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('from')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('where')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('orWhere')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('setParameter')->will($this->returnSelf());
        $this->em->expects($this->any())->method('createQueryBuilder')->will($this->returnValue($queryBuilder));

        $this->assertEquals($queryBuilder, $this->repository->filterByRoles(['ROLE_USER']));
    }
}
