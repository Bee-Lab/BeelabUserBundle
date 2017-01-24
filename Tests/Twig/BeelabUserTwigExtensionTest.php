<?php

namespace Beelab\UserBundle\Tests\Twig;

use Beelab\UserBundle\Twig\BeelabUserTwigExtension;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class BeelabUserTwigExtensionTest extends TestCase
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

    public function testHasPaginatorTrue()
    {
        $paginator = $this->createMock('Knp\Component\Pager\PaginatorInterface');
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
