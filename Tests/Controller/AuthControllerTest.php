<?php

namespace Beelab\UserBundle\Tests\Controller;

use Beelab\UserBundle\Controller\AuthController;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @group unit
 */
class AuthControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller;
    protected $container;

    public function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()->getMock();

        $this->controller = new AuthController();
        $this->controller->setContainer($this->container);
    }

    public function testLoginAuthenticated()
    {
        $authChecker = $this->getMock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $router = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');

        $this->container->expects($this->at(0))->method('get')->with('security.authorization_checker')
            ->will($this->returnValue($authChecker));
        $this->container->expects($this->at(1))->method('getParameter')->will($this->returnValue('homepage'));
        $this->container->expects($this->at(2))->method('get')->with('router')->will($this->returnValue($router));
        $authChecker->expects($this->any())->method('isGranted')->with('IS_AUTHENTICATED_FULLY')
            ->will($this->returnValue(true));
        $router->expects($this->once())->method('generate')->will($this->returnValue('url'));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse',
                                $this->controller->loginAction($request));
    }

    public function testLogin()
    {
        $authChecker = $this->getMock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $request->attributes = new ParameterBag(['_security.last_username' => 'user']);
        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');

        $this->container->expects($this->at(0))->method('get')->with('security.authorization_checker')
            ->will($this->returnValue($authChecker));
        $authChecker->expects($this->any())->method('isGranted')->with('IS_AUTHENTICATED_FULLY')
            ->will($this->returnValue(false));
        $request->expects($this->any())->method('getSession')->will($this->returnValue($session));
        $session->expects($this->at(1))->method('get')->with('_security.last_error')->will($this->returnValue('user'));
        $session->expects($this->at(0))->method('get')->with('_security.last_username')
            ->will($this->returnValue('user'));

        $this->assertEquals(['last_username' => 'user', 'error' => 'user'],
                            $this->controller->loginAction($request));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testLogout()
    {
        $this->controller->logoutAction();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testLoginCheck()
    {
        $this->controller->loginCheckAction();
    }
}
