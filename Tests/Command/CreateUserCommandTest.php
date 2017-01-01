<?php

namespace Beelab\UserBundle\Tests\Command;

use Beelab\UserBundle\Command\CreateUserCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends TestCase
{
    protected $command;

    protected function setUp()
    {
        $application = new Application();
        $application->add(new CreateUserCommand());

        $this->command = $application->find('beelab:user:create');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidEmailError()
    {
        $input = ['email' => 'invalid', 'password' => 'fooBarBaz'];

        $tester = new CommandTester($this->command);
        $tester->execute(array_merge(['command' => $this->command->getName()], $input));
    }

    public function testCreate()
    {
        $input = ['email' => 'garak@example.org', 'password' => 'fooBarBaz'];

        $this->command->setContainer($this->getMockContainer());
        $tester = new CommandTester($this->command);
        $tester->execute(array_merge(['command' => $this->command->getName()], $input));
        $this->assertContains('Created user '.$input['email'], $tester->getDisplay());
    }

    public function testCreateError()
    {
        $input = ['email' => 'garak@example.org', 'password' => 'fooBarBaz'];

        $this->command->setContainer($this->getMockContainer(false));
        $tester = new CommandTester($this->command);
        $tester->execute(array_merge(['command' => $this->command->getName()], $input));
        $this->assertContains('Error, user '.$input['email'].' not created. Generic error', $tester->getDisplay());
    }

    private function getMockContainer($success = true)
    {
        $userManager = $this->getMockBuilder('Beelab\UserBundle\Manager\LightUserManager')->disableOriginalConstructor()->getMock();
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')->disableOriginalConstructor()->getMock();
        $user = $this->createMock('Beelab\UserBundle\Entity\User');

        $container->expects($this->any())->method('get')->with('beelab_user.light_manager')->will($this->returnValue($userManager));
        $userManager->expects($this->at(0))->method('getInstance')->will($this->returnValue($user));
        $user->expects($this->at(0))->method('setEmail')->will($this->returnSelf());
        $user->expects($this->at(1))->method('setPlainPassword')->will($this->returnSelf());
        $user->expects($this->at(2))->method('setActive')->will($this->returnSelf());

        if ($success) {
            $userManager->expects($this->at(1))->method('create')->with($user);
        } else {
            $userManager->expects($this->at(1))->method('create')->will($this->throwException(new \Exception('Generic error')));
        }

        return $container;
    }
}
