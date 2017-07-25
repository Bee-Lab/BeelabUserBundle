<?php

namespace Beelab\UserBundle\Tests\Controller;

use Beelab\UserBundle\Controller\UserController;
use Beelab\UserBundle\Manager\UserManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group unit
 */
class UserControllerTest extends TestCase
{
    protected $controller;
    protected $container;
    protected $formBuilder;
    protected $userManager;
    protected $router;

    protected function setUp()
    {
        $this->container = $this->getMockBuilder(Container::class)->disableOriginalConstructor()->getMock();
        $this->userManager = $this->getMockBuilder(UserManager::class)->disableOriginalConstructor()->getMock();
        $this->formBuilder = $this->getMockBuilder(FormBuilder::class)->disableOriginalConstructor()->getMock();
        $this->router = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $this->controller = new UserController();
        $this->controller->setContainer($this->container);
    }

    public function testIndex()
    {
        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')->getMock();
        $formFactory = $this->getMockBuilder(FormFactory::class)->disableOriginalConstructor()->getMock();
        $formFactory->expects($this->once())->method('create')->will($this->returnValue($form));
        $this->container->expects($this->once())->method('getParameter')->with('beelab_user.filter_form_type')
            ->will($this->returnValue('Beelab\UserBundle\Test\FilterFormStub'));
        $this->container->expects($this->at(1))->method('get')->with('form.factory')
            ->will($this->returnValue($formFactory));
        $this->container->expects($this->at(2))->method('get')->with('event_dispatcher')
            ->will($this->returnValue($eventDispatcher));
        $this->container->expects($this->at(3))->method('get')->with('event_dispatcher')
            ->will($this->returnValue($eventDispatcher));
        $this->container->expects($this->at(4))->method('get')->with('beelab_user.manager')
            ->will($this->returnValue($this->userManager));

        $this->userManager->expects($this->once())->method('getList')->with(1, 20)
            ->will($this->returnValue(['foo', 'bar']));
        $this->assertEquals(['users' => ['foo', 'bar'], 'form' => null], $this->controller->indexAction(new Request()));
    }

    public function testShow()
    {
        $formFactory = $this->getMockBuilder(FormFactory::class)->disableOriginalConstructor()->getMock();
        $formFactory->expects($this->any())->method('createBuilder')->will($this->returnValue($this->formBuilder));
        $user = $this->createMock('Beelab\UserBundle\Entity\User');
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();

        $this->container->expects($this->at(0))->method('get')->with('beelab_user.manager')
            ->will($this->returnValue($this->userManager));
        $this->container->expects($this->at(1))->method('get')->with('form.factory')
            ->will($this->returnValue($formFactory));
        $this->container->expects($this->at(2))->method('get')->with('router')
            ->will($this->returnValue($this->router));
        $this->userManager->expects($this->once())->method('get')->with(42)->will($this->returnValue($user));
        $user->expects($this->once())->method('getId')->will($this->returnValue(42));
        $this->formBuilder->expects($this->once())->method('setAction')->will($this->returnSelf());
        $this->formBuilder->expects($this->once())->method('setMethod')->will($this->returnSelf());
        $this->formBuilder->expects($this->once())->method('getForm')->will($this->returnValue($form));
        $this->router->expects($this->once())->method('generate')->will($this->returnValue('foourl'));
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
