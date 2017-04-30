<?php

namespace Beelab\UserBundle\Tests\DependencyInjection;

use Beelab\UserBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigurationTest extends TestCase
{
    public function testThatCanGetConfigTreeBuilder()
    {
        $configuration = new Configuration();
        $this->assertInstanceOf(TreeBuilder::class, $configuration->getConfigTreeBuilder());
    }
}
