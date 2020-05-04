<?php

namespace Beelab\UserBundle\Command;

use Beelab\UserBundle\Manager\UserManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Command\Command;

/**
 * Inspired by CreateUserCommand by FOSUserBundle
 * See https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Command/PromoteUserCommand.php.
 */
class PromoteUserCommand extends Command
{
    /**
     * @var UserManagerInterface
     */
    private $manager;

    public function __construct(UserManagerInterface $manager)
    {
        $this->manager = $manager;

        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('beelab:user:promote')
            ->setDescription('Promotes a user by adding a role')
            ->setHelp(
                <<<'EOT'
The <info>%command.name%</info> command promotes a user by adding a role

  <info>%command.full_name% garak@example.com ROLE_CUSTOM</info>
EOT
            )->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('role', InputArgument::REQUIRED, 'The role'),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');

        $user = $this->manager->loadUserByUsername($email);

        if (null === $user) {
            $output->writeln(sprintf('<error>Error</error>: user <comment>%s</comment> not found.', $email));

            return 1;
        }
        if ($user->hasRole($role)) {
            $output->writeln(sprintf('User <comment>%s</comment> did already have <comment>%s</comment> role.', $email, $role));
        } else {
            $user->addRole($role);
            $this->manager->update($user);

            $output->writeln(sprintf('Role <comment>%s</comment> has been added to user <comment>%s</comment>.', $role, $email));
        }

        return 0;
    }

    /**
     * @codeCoverageIgnore
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \InvalidArgumentException('Email can not be empty');
                }

                return $email;
            });
            $email = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('email', $email);
        }
        if (!$input->getArgument('role')) {
            $question = new Question('Please choose a role:');
            $question->setValidator(function ($role) {
                if (empty($role)) {
                    throw new \InvalidArgumentException('Role can not be empty');
                }

                return $role;
            });
            $role = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('role', $role);
        }
    }
}
