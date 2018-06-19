<?php

namespace Beelab\UserBundle\Tests\Repository;

use Beelab\UserBundle\Repository\UserRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * @group unit
 */
class UserRepositoryTest extends TestCase
{
    protected $repository;
    protected $em;
    protected $class;

    protected function setUp(): void
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $this->class = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')->disableOriginalConstructor()
            ->getMock();
        $this->class->name = 'Beelab\UserBundle\Test\UserStub';

        $this->repository = new UserRepository($this->em, $this->class);
    }

    public function testLoadUserByUsernameNotFound(): void
    {
        $this->expectException(UsernameNotFoundException::class);
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder(AbstractQuery::class)->setMethods(['getOneOrNullResult'])
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

    public function testLoadUserByUsernameDisabled(): void
    {
        $this->expectException(DisabledException::class);
        $user = $this->createMock('Beelab\UserBundle\Entity\User');
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder(AbstractQuery::class)->setMethods(['getOneOrNullResult'])
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $this->em->expects($this->any())->method('createQueryBuilder')->will($this->returnValue($queryBuilder));
        $queryBuilder->expects($this->any())->method('select')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('from')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('where')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('setParameter')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('getQuery')->will($this->returnValue($query));
        $query->expects($this->any())->method('getOneOrNullResult')->will($this->returnValue($user));
        $user->expects($this->once())->method('isActive')->will($this->returnValue(false));

        $this->repository->loadUserByUsername('foo');
    }

    public function testLoadUserByUsernameFound(): void
    {
        $user = $this->createMock('Beelab\UserBundle\Entity\User');
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')->setMethods(['getOneOrNullResult'])
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $this->em->expects($this->any())->method('createQueryBuilder')->will($this->returnValue($queryBuilder));
        $queryBuilder->expects($this->any())->method('select')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('from')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('where')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('setParameter')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('getQuery')->will($this->returnValue($query));
        $query->expects($this->any())->method('getOneOrNullResult')->will($this->returnValue($user));
        $user->expects($this->once())->method('isActive')->will($this->returnValue(true));

        $this->assertInstanceOf('Symfony\Component\Security\Core\User\UserInterface',
                                $this->repository->loadUserByUsername('baz'));
    }

    public function testRefreshUserUnsupported(): void
    {
        $this->expectException(UnsupportedUserException::class);
        $user = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $this->repository->refreshUser($user);
    }

    public function testRefreshUserSupported(): void
    {
        $user = $this->createMock('Beelab\UserBundle\Test\UserStub');
        $user->expects($this->once())->method('getId');
        $this->repository->refreshUser($user);
    }

    public function testSupportsClass(): void
    {
        $this->assertTrue($this->repository->supportsClass('Beelab\UserBundle\Test\UserStub'));
    }

    public function testSupportsClassFalse(): void
    {
        $this->assertFalse($this->repository->supportsClass('Foo'));
    }

    public function testFilterByRole(): void
    {
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $queryBuilder->expects($this->any())->method('select')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('from')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('where')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('setParameter')->will($this->returnSelf());
        $this->em->expects($this->any())->method('createQueryBuilder')->will($this->returnValue($queryBuilder));

        $this->assertEquals($queryBuilder, $this->repository->filterByRole('ROLE'));
    }

    public function testFilterByRoles(): void
    {
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $queryBuilder->expects($this->any())->method('select')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('from')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('where')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('orWhere')->will($this->returnSelf());
        $queryBuilder->expects($this->any())->method('setParameter')->will($this->returnSelf());
        $this->em->expects($this->any())->method('createQueryBuilder')->will($this->returnValue($queryBuilder));

        $this->assertEquals($queryBuilder, $this->repository->filterByRoles(['ROLE_USER']));
    }
}
