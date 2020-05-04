<?php

namespace Beelab\UserBundle\Tests\Repository;

use Beelab\UserBundle\Entity\User;
use Beelab\UserBundle\Repository\UserRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

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
        $this->em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $this->class = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMock();
        $this->class->name = 'Beelab\UserBundle\Test\UserStub';

        $this->repository = new UserRepository($this->em, $this->class);
    }

    public function testLoadUserByUsernameNotFound(): void
    {
        $this->expectException(UsernameNotFoundException::class);
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder(AbstractQuery::class)->setMethods(['getOneOrNullResult'])
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $this->em->method('createQueryBuilder')->willReturn($queryBuilder);
        $queryBuilder->method('select')->willReturnSelf();
        $queryBuilder->method('from')->willReturnSelf();
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();
        $queryBuilder->method('getQuery')->willReturn($query);
        $query->method('getOneOrNullResult')->willReturn(null);

        $this->assertInstanceOf(UserInterface::class, $this->repository->loadUserByUsername('foo'));
    }

    public function testLoadUserByUsernameDisabled(): void
    {
        $this->expectException(DisabledException::class);
        $user = $this->createMock(User::class);
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder(AbstractQuery::class)->setMethods(['getOneOrNullResult'])
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $this->em->method('createQueryBuilder')->willReturn($queryBuilder);
        $queryBuilder->method('select')->willReturnSelf();
        $queryBuilder->method('from')->willReturnSelf();
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();
        $queryBuilder->method('getQuery')->willReturn($query);
        $query->method('getOneOrNullResult')->willReturn($user);
        $user->expects($this->once())->method('isActive')->willReturn(false);

        $this->repository->loadUserByUsername('foo');
    }

    public function testLoadUserByUsernameFound(): void
    {
        $user = $this->createMock(User::class);
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder(AbstractQuery::class)->setMethods(['getOneOrNullResult'])
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $this->em->method('createQueryBuilder')->willReturn($queryBuilder);
        $queryBuilder->method('select')->willReturnSelf();
        $queryBuilder->method('from')->willReturnSelf();
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();
        $queryBuilder->method('getQuery')->willReturn($query);
        $query->method('getOneOrNullResult')->willReturn($user);
        $user->expects($this->once())->method('isActive')->willReturn(true);

        $this->assertInstanceOf(UserInterface::class, $this->repository->loadUserByUsername('baz'));
    }

    public function testRefreshUserUnsupported(): void
    {
        $this->expectException(UnsupportedUserException::class);
        $user = $this->createMock(UserInterface::class);
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
        $queryBuilder->method('select')->willReturnSelf();
        $queryBuilder->method('from')->willReturnSelf();
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();
        $this->em->method('createQueryBuilder')->willReturn($queryBuilder);

        $this->assertEquals($queryBuilder, $this->repository->filterByRole('ROLE'));
    }

    public function testFilterByRoles(): void
    {
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $queryBuilder->method('select')->willReturnSelf();
        $queryBuilder->method('from')->willReturnSelf();
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('orWhere')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();
        $this->em->method('createQueryBuilder')->willReturn($queryBuilder);

        $this->assertEquals($queryBuilder, $this->repository->filterByRoles(['ROLE_USER']));
    }
}
