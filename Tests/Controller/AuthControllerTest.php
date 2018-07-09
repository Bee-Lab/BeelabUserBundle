<?php

namespace Beelab\UserBundle\Tests\Controller;

use Beelab\UserBundle\Controller\AuthController;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

/**
 * @group unit
 */
class AuthControllerTest extends TestCase
{
    /**
     * @var AuthController
     */
    protected $controller;

    /**
     * @var Container
     */
    protected $container;

    protected function setUp(): void
    {
        $this->container = $this->getMockBuilder(Container::class)->disableOriginalConstructor()->getMock();

        $this->controller = new AuthController();
        $this->controller->setContainer($this->container);
    }

    public function testLoginAuthenticated(): void
    {
        $authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $request = $this->createMock(Request::class);
        $router = $this->createMock(UrlGeneratorInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $bag = $this->createMock(ParameterBagInterface::class);

        $bag->expects($this->once())->method('get')->will($this->returnValue('an_url'));
        $this->container->expects($this->at(0))->method('get')->with('router')->will($this->returnValue($router));
        $authChecker->expects($this->any())->method('isGranted')->with('IS_AUTHENTICATED_FULLY')
            ->will($this->returnValue(true));
        $router->expects($this->once())->method('generate')->will($this->returnValue('an_url'));

        $response = $this->controller->loginAction($authChecker, $logger, $request, $bag);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testLogin(): void
    {
        $authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['_security.last_username' => 'user']);
        $session = $this->createMock(SessionInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $bag = $this->createMock(ParameterBagInterface::class);
        $twig = $this->createMock(Environment::class);

        $this->container->expects($this->at(0))->method('has')->with('templating')->will($this->returnValue(false));
        $this->container->expects($this->at(1))->method('has')->with('twig')->will($this->returnValue(true));
        $this->container->expects($this->at(2))->method('get')->with('twig')->will($this->returnValue($twig));
        $authChecker->expects($this->any())->method('isGranted')->with('IS_AUTHENTICATED_FULLY')
            ->will($this->returnValue(false));
        $request->expects($this->any())->method('getSession')->will($this->returnValue($session));
        $session->expects($this->at(1))->method('get')->with('_security.last_error')->will($this->returnValue(new \Exception('foo')));
        $session->expects($this->at(0))->method('get')->with('_security.last_username')
            ->will($this->returnValue('user'));

        $response = $this->controller->loginAction($authChecker, $logger, $request, $bag);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testLogout(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->controller->logoutAction();
    }

    public function testLoginCheck(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->controller->loginCheckAction();
    }
}
