<?php

namespace Beelab\UserBundle\Tests\Manager;

use Beelab\UserBundle\Manager\LightUserManager;
use Beelab\UserBundle\Test\UserStub;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class LightUserManagerTest extends TestCase
{
    protected $manager;

    protected $em;

    protected $repository;

    protected $encoder;

    protected function setUp(): void
    {
        $this->em = $this->createMock(ObjectManager::class);
        $this->encoder = $this->createMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $this->repository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $this->em->expects($this->once())->method('getRepository')->with(UserStub::class)->willReturn($this->repository);

        $this->manager = new LightUserManager(UserStub::class, $this->em, $this->encoder);
    }

    public function testGetInstance(): void
    {
        $this->assertInstanceOf('Beelab\UserBundle\User\UserInterface', $this->manager->getInstance());
    }

    public function testCreate(): void
    {
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $user->expects($this->once())->method('getPlainPassword')->willReturn('pippo');
        $user->expects($this->once())->method('getSalt')->willReturn('pluto');
        $encoder = $this->createMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        $this->encoder->expects($this->once())->method('getEncoder')->with($user)->willReturn($encoder);
        $encoder->expects($this->once())->method('encodePassword');
        $user->expects($this->once())->method('setPassword');
        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $this->manager->create($user);
    }

    public function testUpdateNoPasswordChangeAndNoFlush(): void
    {
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $user->expects($this->once())->method('getPlainPassword')->willReturn(null);

        $this->manager->update($user, false);
    }

    public function testUpdateWithPasswordChangeAndFlush(): void
    {
        $user = $this->createMock('Beelab\UserBundle\User\UserInterface');
        $user->expects($this->exactly(2))->method('getPlainPassword')->willReturn('pippo');
        $user->expects($this->once())->method('getSalt')->willReturn('pluto');
        $encoder = $this->createMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        $this->encoder->expects($this->once())->method('getEncoder')->with($user)->willReturn($encoder);
        $encoder->expects($this->once())->method('encodePassword');
        $user->expects($this->once())->method('setPassword');
        $this->em->expects($this->once())->method('flush');

        $this->manager->update($user);
    }
}
