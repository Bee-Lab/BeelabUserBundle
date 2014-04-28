<?php

namespace Beelab\UserBundle\Tests\Listner;

use Beelab\UserBundle\Twig\BeelabUserTwigExtension;
use PHPUnit_Framework_TestCase;

/**
 * @group unit
 */
class BeelabUserTwigExtensionTest extends PHPUnit_Framework_TestCase
{
    public function testGetGlobals()
    {
        $extension = new BeelabUserTwigExtension('fooLayout');
        $this->assertEquals(array('beelab_user_layout' => 'fooLayout'), $extension->getGlobals());
    }

    public function testGetName()
    {
        $extension = new BeelabUserTwigExtension('foo');
        $this->assertEquals('beelab_user_twig_extension', $extension->getName());
    }
}