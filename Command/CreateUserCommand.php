<?php

namespace Beelab\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Inspired by CreateUserCommand by FOSUserBundle
 * See https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Command/CreateUserCommand.php.
 */
class CreateUserCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('beelab:user:create')
            ->setDescription('Create a user.')
            ->setDefinition(array(
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
            ))
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates a user:

  <info>%command.full_name%</info>

This interactive shell will ask you for an email and then a password.

You can alternatively specify the email and password as arguments:

  <info>%command.full_name% garak@example.com mypassword</info>

You can create an inactive user (will not be able to log in):

  <info>%command.full_name% inactive@example.com --inactive</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        if (empty($email) || false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }
        $password = $input->getArgument('password');
        $inactive = $input->getOption('inactive');
        $manager = $this->getContainer()->get('beelab_user.light_manager');

        $user = $manager->getInstance();
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setActive(!$inactive);

        try {
            $manager->create($user);
            $output->writeln(sprintf('Created user <comment>%s</comment>', $email));
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error</error>, user <comment>%s</comment> not created. %s', $email, $e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose an email:',
                function ($email) {
                    if (empty($email) || false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        throw new \InvalidArgumentException('Invalid email');
                    }

                    return $email;
                }
            );
            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askHiddenResponseAndValidate(
                $output,
                'Please choose a password:',
                function ($password) {
                    if (empty($password)) {
                        throw new \InvalidArgumentException('Password can not be empty');
                    }

                    return $password;
                }
            );
            $input->setArgument('password', $password);
        }
    }
}
