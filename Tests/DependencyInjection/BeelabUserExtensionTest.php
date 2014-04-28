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

        $extension->load(array(array()), $container);
    }

    public function testLoadSetParameters()
    {
        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')->disableOriginalConstructor()->getMock();
        $parameterBag = $this->getMockBuilder('Symfony\Component\DependencyInjection\ParameterBag\\ParameterBag')->disableOriginalConstructor()->getMock();

        $parameterBag->expects($this->any())->method('add');

        $container->expects($this->any())->method('getParameterBag')->will($this->returnValue($parameterBag));

        $extension = new BeelabUserExtension();
        $configs = array(
            array('user_class' => 'foo'),
            array('user_manager_class' => 'foo'),
            array('user_form_type' => 'foo'),
            array('password_form_type' => 'foo'),
            array('layout' => 'bar'),
        );
        $extension->load($configs, $container);
    }
}
