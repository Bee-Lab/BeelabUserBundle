<?php

namespace Beelab\UserBundle\Tests\Command;

use Beelab\UserBundle\Command\PromoteUserCommand;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class PromoteUserCommandTest extends PHPUnit_Framework_TestCase
{
    protected $command;

    public function setUp()
    {
        $application = new Application();
        $application->add(new PromoteUserCommand());

        $this->command = $application->find('beelab:user:promote');
    }

    public function testPromote()
    {
        $input = ['email' => 'garak@example.org', 'role' => 'ROLE_ADMIN'];

        $this->command->setContainer($this->getMockContainer());
        $tester = new CommandTester($this->command);
        $tester->execute(array_merge(['command' => $this->command->getName()], $input));
        $this->assertContains('Role '.$input['role'].' has been added to user '.$input['email'], $tester->getDisplay());
    }

    public function testUserNotFound()
    {
        $input = ['email' => 'garak@example.org', 'role' => 'ROLE_ADMIN'];

        $this->command->setContainer($this->getMockContainer(false));
        $tester = new CommandTester($this->command);
        $tester->execute(array_merge(['command' => $this->command->getName()], $input));
        $this->assertContains('Error: user '.$input['email'].' not found', $tester->getDisplay());
    }

    public function testHasAlreadyRole()
    {
        $input = ['email' => 'garak@example.org', 'role' => 'ROLE_USER'];

        $this->command->setContainer($this->getMockContainer(true, $input['role']));
        $tester = new CommandTester($this->command);
        $tester->execute(array_merge(['command' => $this->command->getName()], $input));
        $this->assertContains('User '.$input['email'].' did already have '.$input['role'].' role', $tester->getDisplay());
    }

    private function getMockContainer($found = true, $role = 'ROLE_ADMIN')
    {
        $userManager = $this->getMockBuilder('Beelab\UserBundle\Manager\UserManager')->disableOriginalConstructor()->getMock();
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')->disableOriginalConstructor()->getMock();
        $user = $this->getMock('Beelab\UserBundle\Entity\User');

        $container->expects($this->once())->method('get')->with('beelab_user.manager')->will($this->returnValue($userManager));
        if ($found) {
            $userManager->expects($this->at(0))->method('find')->will($this->returnValue($user));
            if ($role == 'ROLE_ADMIN') {
                $user->expects($this->at(0))->method('hasRole')->with($role)->will($this->returnValue(false));
                $user->expects($this->at(1))->method('addRole')->with($role);
                $userManager->expects($this->at(1))->method('update')->with($user);
            } else {
                $user->expects($this->at(0))->method('hasRole')->with($role)->will($this->returnValue(true));
            }
        } else {
            $userManager->expects($this->once())->method('find')->will($this->returnValue(null));
        }

        return $container;
    }
}
