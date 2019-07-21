<?php

namespace Beelab\UserBundle\Tests\Controller;

use Beelab\UserBundle\Controller\UserController;
use Beelab\UserBundle\Entity\User;
use Beelab\UserBundle\Manager\UserManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

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

    protected function setUp(): void
    {
        $this->container = $this->getMockBuilder(Container::class)->disableOriginalConstructor()->getMock();
        $this->userManager = $this->getMockBuilder(UserManager::class)->disableOriginalConstructor()->getMock();
        $this->formBuilder = $this->getMockBuilder(FormBuilder::class)->disableOriginalConstructor()->getMock();
        $this->router = $this->createMock(RouterInterface::class);
        $this->controller = new UserController();
        $this->controller->setContainer($this->container);
    }

    public function testIndex(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $form = $this->createMock(FormInterface::class);
        $formFactory = $this->getMockBuilder(FormFactory::class)->disableOriginalConstructor()->getMock();
        $twig = $this->createMock(Environment::class);

        $formFactory->expects($this->once())->method('create')->willReturn($form);
        $this->container->expects($this->once())->method('getParameter')->with('beelab_user.filter_form_type')
            ->willReturn('Beelab\UserBundle\Test\FilterFormStub');
        $this->container->expects($this->at(1))->method('get')->with('form.factory')
            ->willReturn($formFactory);
        $this->userManager->expects($this->once())->method('getList')->with(1, 20)
            ->willReturn(['foo', 'bar']);
        $this->container->expects($this->at(2))->method('has')->with('templating')->willReturn(false);
        $this->container->expects($this->at(3))->method('has')->with('twig')->willReturn(true);
        $this->container->expects($this->at(4))->method('get')->with('twig')->willReturn($twig);

        $response = $this->controller->indexAction($eventDispatcher, $this->userManager, new Request());

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testShow(): void
    {
        $formFactory = $this->getMockBuilder(FormFactory::class)->disableOriginalConstructor()->getMock();
        $user = $this->createMock(User::class);
        $form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $twig = $this->createMock(Environment::class);

        $formFactory->expects($this->any())->method('createBuilder')->willReturn($this->formBuilder);
        $this->container->expects($this->at(0))->method('get')->with('form.factory')
            ->willReturn($formFactory);
        $this->container->expects($this->at(1))->method('get')->with('router')
            ->willReturn($this->router);
        $this->userManager->expects($this->once())->method('get')->with(42)->willReturn($user);
        $user->expects($this->once())->method('getId')->willReturn(42);
        $this->formBuilder->expects($this->once())->method('setAction')->willReturnSelf();
        $this->formBuilder->expects($this->once())->method('setMethod')->willReturnSelf();
        $this->formBuilder->expects($this->once())->method('getForm')->willReturn($form);
        $this->router->expects($this->once())->method('generate')->willReturn('foourl');
        $form->expects($this->once())->method('createView');
        $this->container->expects($this->at(2))->method('has')->with('templating')->willReturn(false);
        $this->container->expects($this->at(3))->method('has')->with('twig')->willReturn(true);
        $this->container->expects($this->at(4))->method('get')->with('twig')->willReturn($twig);

        $this->controller->showAction(42, $this->userManager);
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
    }

    public function testDelete(): void
    {
        $this->markTestIncomplete();
    }

    public function testPassword(): void
    {
        $this->markTestIncomplete();
    }
}
