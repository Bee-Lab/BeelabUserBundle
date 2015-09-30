<?php

namespace Beelab\UserBundle\Tests\Controller;

use Beelab\UserBundle\Controller\UserController;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group unit
 */
class UserControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller;
    protected $container;
    protected $formBuilder;
    protected $userManager;

    public function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()->getMock();
        $this->userManager = $this->getMockBuilder('Beelab\UserBundle\Manager\UserManager')
            ->disableOriginalConstructor()->getMock();
        $this->formBuilder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()->getMock();
        $this->controller = new UserController();
        $this->controller->setContainer($this->container);
    }

    public function testIndex()
    {
        $this->container->expects($this->once())->method('get')->with('beelab_user.manager')
            ->will($this->returnValue($this->userManager));
        $this->userManager->expects($this->once())->method('getList')->with(1, 20)
            ->will($this->returnValue(['foo', 'bar']));
        $this->assertEquals(['users' => ['foo', 'bar']], $this->controller->indexAction(new Request()));
    }

    public function testShow()
    {
        $formFactory = $this->getMockBuilder('Symfony\Component\Form\FormFactory')->disableOriginalConstructor()
            ->getMock();
        $formFactory->expects($this->any())->method('createBuilder')->will($this->returnValue($this->formBuilder));
        $user = $this->getMock('Beelab\UserBundle\Entity\User');
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();

        $this->container->expects($this->at(0))->method('get')->with('beelab_user.manager')
            ->will($this->returnValue($this->userManager));
        $this->container->expects($this->at(1))->method('get')->with('form.factory')
            ->will($this->returnValue($formFactory));
        $this->userManager->expects($this->once())->method('get')->with(42)->will($this->returnValue($user));
        $user->expects($this->once())->method('getId')->will($this->returnValue(42));
        $this->formBuilder->expects($this->once())->method('add')->will($this->returnSelf());
        $this->formBuilder->expects($this->once())->method('getForm')->will($this->returnValue($form));
        $form->expects($this->once())->method('createView');

        $this->controller->showAction(42);
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
