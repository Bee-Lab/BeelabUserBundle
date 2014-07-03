<?php

namespace Beelab\UserBundle\Tests\Manager;

use Beelab\UserBundle\Manager\LightUserManager;
use PHPUnit_Framework_TestCase;

/**
 * @group unit
 */
class LightUserManagerTest extends PHPUnit_Framework_TestCase
{
    protected $manager,
        $em,
        $repository,
        $encoder
    ;

    public function setUp()
    {
        $class = 'Beelab\UserBundle\Entity\User';
        $this->em = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->encoder = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $this->em->expects($this->once())->method('getRepository')->with($class)->will($this->returnValue($this->repository));

        $this->manager = new LightUserManager($class, $this->em, $this->encoder);
    }

    public function testGetInstance()
    {
        $this->assertInstanceOf('Beelab\UserBundle\User\UserInterface', $this->manager->getInstance());
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
}
