<?php

namespace Beelab\UserBundle\Tests\Command;

use Beelab\UserBundle\Command\PromoteUserCommand;
use Beelab\UserBundle\Entity\User;
use Beelab\UserBundle\Manager\UserManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class PromoteUserCommandTest extends TestCase
{
    private $command;

    private $manager;

    private $user;

    protected function setUp(): void
    {
        $this->manager = $this->createMock(UserManagerInterface::class);
        $this->user = $this->createMock(User::class);

        $this->manager->expects($this->any())->method('getInstance')->will($this->returnValue($this->user));

        $application = new Application();
        $application->add(new PromoteUserCommand($this->manager));

        $this->command = $application->find('beelab:user:promote');
    }

    public function testPromote(): void
    {
        $this->manager->expects($this->once())->method('loadUserByUsername')->will($this->returnValue($this->user));
        $this->user->expects($this->once())->method('hasRole')->will($this->returnValue(false));
        $this->user->expects($this->once())->method('addRole');
        $this->manager->expects($this->once())->method('update')->with($this->user);

        $input = ['email' => 'garak@example.org', 'role' => 'ROLE_ADMIN'];

        $tester = new CommandTester($this->command);
        $tester->execute(\array_merge(['command' => $this->command->getName()], $input));
        $this->assertStringContainsString('Role '.$input['role'].' has been added to user '.$input['email'], $tester->getDisplay());
    }

    public function testUserNotFound(): void
    {
        $this->manager->expects($this->once())->method('loadUserByUsername')->will($this->returnValue(null));

        $input = ['email' => 'garak@example.org', 'role' => 'ROLE_ADMIN'];

        $tester = new CommandTester($this->command);
        $tester->execute(\array_merge(['command' => $this->command->getName()], $input));
        $this->assertStringContainsString('Error: user '.$input['email'].' not found', $tester->getDisplay());
    }

    public function testHasAlreadyRole(): void
    {
        $this->manager->expects($this->once())->method('loadUserByUsername')->will($this->returnValue($this->user));
        $this->user->expects($this->once())->method('hasRole')->will($this->returnValue(true));

        $input = ['email' => 'garak@example.org', 'role' => 'ROLE_USER'];

        $tester = new CommandTester($this->command);
        $tester->execute(\array_merge(['command' => $this->command->getName()], $input));
        $this->assertStringContainsString('User '.$input['email'].' did already have '.$input['role'].' role', $tester->getDisplay());
    }
}
