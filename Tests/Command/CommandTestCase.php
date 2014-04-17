<?php

namespace Beelab\UserBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Base class for testing the CLI tools.
 *
 * See http://alexandre-salome.fr/blog/Test-your-commands-in-Symfony2
 */
abstract class CommandTestCase extends WebTestCase
{
    /**
     * Runs a command and returns it output
     *
     * @param  Client $client
     * @param  string $command
     * @return string
     */
    public function runCommand(Client $client, $command)
    {
        $application = new Application($client->getKernel());
        $application->setAutoExit(false);

        $fp = tmpfile();
        $input = new StringInput($command);
        $output = new StreamOutput($fp);

        $application->run($input, $output);

        fseek($fp, 0);
        $output = '';
        while (!feof($fp)) {
            $output = fread($fp, 4096);
        }
        fclose($fp);

        return $output;
    }
}