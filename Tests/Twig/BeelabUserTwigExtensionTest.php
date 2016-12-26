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
        $extension = new BeelabUserTwigExtension('fooLayout', 'bazRoute');
        $expected = [
            'beelab_user_layout' => 'fooLayout',
            'beelab_user_route' => 'bazRoute',
        ];
        $this->assertEquals($expected, $extension->getGlobals());
    }

    public function testGetName()
    {
        $extension = new BeelabUserTwigExtension('foo', 'barRoute');
        $this->assertEquals('Beelab\\UserBundle\\Twig\\BeelabUserTwigExtension', $extension->getName());
    }

    public function testHasPaginatorTrue()
    {
        $paginator = $this->getMock('Knp\Component\Pager\PaginatorInterface');
        $extension = new BeelabUserTwigExtension('foo', 'barRoute', $paginator);
        $this->assertTrue($extension->hasPaginator());
    }

    public function testHasPaginatorFalse()
    {
        $extension = new BeelabUserTwigExtension('foo', 'barRoute');
        $this->assertFalse($extension->hasPaginator());
    }

    public function testGetFunctions()
    {
        $extension = new BeelabUserTwigExtension('foo', 'barRoute');
        $this->assertCount(1, $extension->getFunctions());
    }
}
