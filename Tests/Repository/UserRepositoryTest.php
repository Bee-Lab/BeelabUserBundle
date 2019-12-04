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

        $this->em->expects($this->any())->method('createQueryBuilder')->willReturn($queryBuilder);
        $queryBuilder->expects($this->any())->method('select')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('from')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('where')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('setParameter')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('getQuery')->willReturn($query);
        $query->expects($this->any())->method('getOneOrNullResult')->willReturn(null);

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

        $this->em->expects($this->any())->method('createQueryBuilder')->willReturn($queryBuilder);
        $queryBuilder->expects($this->any())->method('select')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('from')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('where')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('setParameter')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('getQuery')->willReturn($query);
        $query->expects($this->any())->method('getOneOrNullResult')->willReturn($user);
        $user->expects($this->once())->method('isActive')->willReturn(false);

        $this->repository->loadUserByUsername('foo');
    }

    public function testLoadUserByUsernameFound(): void
    {
        $user = $this->createMock('Beelab\UserBundle\Entity\User');
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')->setMethods(['getOneOrNullResult'])
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $this->em->expects($this->any())->method('createQueryBuilder')->willReturn($queryBuilder);
        $queryBuilder->expects($this->any())->method('select')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('from')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('where')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('setParameter')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('getQuery')->willReturn($query);
        $query->expects($this->any())->method('getOneOrNullResult')->willReturn($user);
        $user->expects($this->once())->method('isActive')->willReturn(true);

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
        $queryBuilder->expects($this->any())->method('select')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('from')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('where')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('setParameter')->willReturnSelf();
        $this->em->expects($this->any())->method('createQueryBuilder')->willReturn($queryBuilder);

        $this->assertEquals($queryBuilder, $this->repository->filterByRole('ROLE'));
    }

    public function testFilterByRoles(): void
    {
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $queryBuilder->expects($this->any())->method('select')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('from')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('where')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('orWhere')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('setParameter')->willReturnSelf();
        $this->em->expects($this->any())->method('createQueryBuilder')->willReturn($queryBuilder);

        $this->assertEquals($queryBuilder, $this->repository->filterByRoles(['ROLE_USER']));
    }
}
