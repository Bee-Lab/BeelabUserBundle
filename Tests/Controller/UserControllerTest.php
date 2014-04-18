<?php

namespace Beelab\UserBundle\Tests\Controller;

use Beelab\UserBundle\Controller\UserController;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

class UserControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller, $userManager;

    public function setUp()
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')->disableOriginalConstructor()->getMock();
        $this->userManager = $this->getMockBuilder('Beelab\UserBundle\Manager\UserManager')->disableOriginalConstructor()->getMock();

        $container->expects($this->any())->method('get')->with('beelab_user.manager')->will($this->returnValue($this->userManager));
        #$formFactory = $this->getMockBuilder('Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();
        #$formFactoryr->expects($this->any())->method('createBuilder');
        #$container->expects($this->any())->method('get')->with('form.factory')->will($this->returnValue($formFactory));

        $this->controller = new UserController();
        $this->controller->setContainer($container);
    }

    public function testIndex()
    {
        $this->userManager->expects($this->once())->method('getList')->with(1, 20)->will($this->returnValue(array('foo', 'bar')));
        $this->assertEquals(array('paginator' => array('foo', 'bar')), $this->controller->indexAction(new Request()));
    }

    public function testShow()
    {
        $this->markTestIncomplete();
    }

    public function testNew()
    {
        $this->markTestIncomplete();
    }

    public function testEdit()
    {
        $this->markTestIncomplete();
    }

    public function testDelete()
    {
        $this->markTestIncomplete();
    }

    public function testPassword()
    {
        $this->markTestIncomplete();
    }
}
