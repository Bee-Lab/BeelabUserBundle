<?php

namespace Beelab\UserBundle\Tests\Command;

use Beelab\UserBundle\Command\CreateUserCommand;
use Beelab\UserBundle\Entity\User;
use Beelab\UserBundle\Manager\LightUserManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class CreateUserCommandTest extends TestCase
{
    private $command;

    private $manager;

    private $user;

    protected function setUp(): void
    {
        $this->manager = $this->createMock(LightUserManagerInterface::class);
        $this->user = $this->createMock(User::class);

        $this->manager->expects($this->any())->method('getInstance')->willReturn($this->user);

        $application = new Application();
        $application->add(new CreateUserCommand($this->manager));

        $this->command = $application->find('beelab:user:create');
    }

    public function testInvalidEmailError(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $input = ['email' => 'invalid', 'password' => 'fooBarBaz'];

        $tester = new CommandTester($this->command);
        $tester->execute(\array_merge(['command' => $this->command->getName()], $input));
    }

    public function testCreate(): void
    {
        $this->manager->expects($this->once())->method('create')->with($this->user);

        $input = ['email' => 'garak@example.org', 'password' => 'fooBarBaz'];

        $tester = new CommandTester($this->command);
        $tester->execute(\array_merge(['command' => $this->command->getName()], $input));
        $this->assertStringContainsString('Created user '.$input['email'], $tester->getDisplay());
    }

    public function testCreateError(): void
    {
        $this->manager->expects($this->once())->method('create')->will($this->throwException(new \Exception('Generic error')));

        $input = ['email' => 'garak@example.org', 'password' => 'fooBarBaz'];

        $tester = new CommandTester($this->command);
        $tester->execute(\array_merge(['command' => $this->command->getName()], $input));
        $this->assertStringContainsString('Error, user '.$input['email'].' not created. Generic error', $tester->getDisplay());
    }
}
