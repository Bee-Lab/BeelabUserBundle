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
        $extension = new BeelabUserTwigExtension('fooLayout', 'barTheme');
        $this->assertEquals(array('beelab_user_layout' => 'fooLayout', 'beelab_user_theme' => 'barTheme'), $extension->getGlobals());
    }

    public function testGetName()
    {
        $extension = new BeelabUserTwigExtension('foo', 'bar');
        $this->assertEquals('beelab_user_twig_extension', $extension->getName());
    }
}