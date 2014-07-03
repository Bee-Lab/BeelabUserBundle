<?php

namespace Beelab\UserBundle\Tests\Twig;

use Beelab\UserBundle\Twig\BeelabUserTwigExtension;
use PHPUnit_Framework_TestCase;

/**
 * @group unit
 */
class BeelabUserTwigExtensionTest extends PHPUnit_Framework_TestCase
{
    public function testGetGlobals()
    {
        $extension = new BeelabUserTwigExtension('fooLayout', 'barTheme', 'bazRoute');
        $expected = array(
            'beelab_user_layout' => 'fooLayout',
            'beelab_user_theme'  => 'barTheme',
            'beelab_user_route'  => 'bazRoute',
        );
        $this->assertEquals($expected, $extension->getGlobals());
    }

    public function testGetName()
    {
        $extension = new BeelabUserTwigExtension('foo', 'bar', 'barRoute');
        $this->assertEquals('beelab_user_twig_extension', $extension->getName());
    }
}
