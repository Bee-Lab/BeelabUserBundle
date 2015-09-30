<?php

namespace Beelab\UserBundle\Tests\DependencyInjection;

use Beelab\UserBundle\DependencyInjection\BeelabUserExtension;
use PHPUnit_Framework_TestCase;

class BeelabUserExtensionTest extends PHPUnit_Framework_TestCase
{
    public function testLoadFailure()
    {
        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')->disableOriginalConstructor()->getMock();
        $extension = $this->getMockBuilder('Beelab\\UserBundle\\DependencyInjection\\BeelabUserExtension')->getMock();

        $extension->load([[]], $container);
    }

    public function testLoadSetParameters()
    {
        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')->disableOriginalConstructor()->getMock();
        $parameterBag = $this->getMockBuilder('Symfony\Component\DependencyInjection\ParameterBag\\ParameterBag')->disableOriginalConstructor()->getMock();

        $parameterBag->expects($this->any())->method('add');

        $container->expects($this->any())->method('getParameterBag')->will($this->returnValue($parameterBag));

        $extension = new BeelabUserExtension();
        $configs = [
            ['user_class'         => 'foo'],
            ['user_manager_class' => 'foo'],
            ['user_form_type'     => 'foo'],
            ['password_form_type' => 'foo'],
            ['layout'             => 'bar'],
        ];
        $extension->load($configs, $container);
    }
}
