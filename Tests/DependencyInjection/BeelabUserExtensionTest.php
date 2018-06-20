<?php

namespace Beelab\UserBundle\Tests\DependencyInjection;

use Beelab\UserBundle\DependencyInjection\BeelabUserExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class BeelabUserExtensionTest extends TestCase
{
    public function testLoadFailure(): void
    {
        $container = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();
        $extension = $this->getMockBuilder(BeelabUserExtension::class)->getMock();

        $extension->load([[]], $container);
    }

    public function testLoadSetParameters(): void
    {
        $container = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();
        $parameterBag = $this->getMockBuilder(ParameterBag::class)->disableOriginalConstructor()->getMock();

        $parameterBag->expects($this->any())->method('add');
        $container->expects($this->any())->method('getParameterBag')->will($this->returnValue($parameterBag));

        $extension = new BeelabUserExtension();
        $configs = [
            ['user_class' => 'foo'],
            ['user_manager_class' => 'foo'],
            ['user_form_type' => 'foo'],
            ['password_form_type' => 'foo'],
            ['layout' => 'bar'],
        ];
        $extension->load($configs, $container);
    }
}
