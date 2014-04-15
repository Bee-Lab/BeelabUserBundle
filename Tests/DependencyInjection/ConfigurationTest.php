<?php

namespace Beelab\UserBundle\Tests\DependencyInjection;

use Beelab\UserBundle\DependencyInjection\Configuration;
use PHPUnit_Framework_TestCase;

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testThatCanGetConfigTreeBuilder()
    {
        $configuration = new Configuration();
        $this->assertInstanceOf('Symfony\Component\Config\Definition\Builder\TreeBuilder', $configuration->getConfigTreeBuilder());
    }
}
