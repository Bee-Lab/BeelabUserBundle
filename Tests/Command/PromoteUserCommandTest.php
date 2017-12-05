<?php

namespace Beelab\UserBundle\Tests\Command;

use Beelab\UserBundle\Command\PromoteUserCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class PromoteUserCommandTest extends TestCase
{
    protected $command;

    protected function setUp()
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
        $user = $this->createMock('Beelab\UserBundle\Entity\User');

        $container->expects($this->once())->method('get')->with('beelab_user.manager')->will($this->returnValue($userManager));
        if ($found) {
            $userManager->expects($this->at(0))->method('loadUserByUsername')->will($this->returnValue($user));
            if ('ROLE_ADMIN' === $role) {
                $user->expects($this->at(0))->method('hasRole')->with($role)->will($this->returnValue(false));
                $user->expects($this->at(1))->method('addRole')->with($role);
                $userManager->expects($this->at(1))->method('update')->with($user);
            } else {
                $user->expects($this->at(0))->method('hasRole')->with($role)->will($this->returnValue(true));
            }
        } else {
            $userManager->expects($this->once())->method('loadUserByUsername')->will($this->returnValue(null));
        }

        return $container;
    }
}
