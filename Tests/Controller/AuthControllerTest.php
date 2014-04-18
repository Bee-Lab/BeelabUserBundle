<?php

namespace Beelab\UserBundle\Tests\Controller;

use Beelab\UserBundle\Controller\AuthController;
use PHPUnit_Framework_TestCase;

class AuthControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller, $container;

    public function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')->disableOriginalConstructor()->getMock();

        $this->controller = new AuthController();
        $this->controller->setContainer($this->container);
    }

    public function testLogin()
    {
        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $request->attributes = new \Symfony\Component\HttpFoundation\ParameterBag(array('_security.last_username' => 'user'));
        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');

        $this->container->expects($this->any())->method('get')->with('security.context')->will($this->returnValue($securityContext));
        $securityContext->expects($this->any())->method('isGranted')->with('IS_AUTHENTICATED_FULLY')->will($this->returnValue(false));
        $request->expects($this->any())->method('getSession')->will($this->returnValue($session));
        $session->expects($this->at(1))->method('get')->with('_security.last_error')->will($this->returnValue('user'));
        $session->expects($this->at(0))->method('get')->with('_security.last_username')->will($this->returnValue('user'));

        $this->assertEquals(array('last_username' => 'user', 'error' => null), $this->controller->loginAction($request));
        // TODO ...
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
