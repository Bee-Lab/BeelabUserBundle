<?php

namespace Beelab\UserBundle\Controller;

use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

class AuthController extends AbstractController
{
    /**
     * Login form.
     *
     * @Route("/login", name="login")
     * @Method("GET")
     */
    public function loginAction(AuthorizationCheckerInterface $checker, LoggerInterface $logger, Request $request): Response
    {
        if ($checker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute($this->getParameter('beelab_user.route'));
        }

        return $this->render('BeelabUserBundle:User:login.html.twig', [
            'last_username' => $request->getSession()->get(Security::LAST_USERNAME),
            'error' => $this->getLoginError($logger, $request),
        ]);
    }

    /**
     * Logout (implemented by Symfony security system).
     *
     * @Route("/logout", name="logout")
     * @Method("GET")
     */
    public function logoutAction(): void
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    /**
     * Login check (implemented by Symfony security system).
     *
     * @Route("/login_check", name="login_check")
     * @Method("POST")
     */
    public function loginCheckAction(): void
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    /**
     * Get possible authentication error.
     *
     * @param LoggerInterface $logger
     * @param Request $request
     *
     * @return \Exception|null
     */
    protected function getLoginError(LoggerInterface $logger, Request $request): ?\Exception
    {
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(Security::AUTHENTICATION_ERROR);
            $request->getSession()->remove(Security::AUTHENTICATION_ERROR);
        }
        // see https://github.com/symfony/symfony/issues/837#issuecomment-3000155
        if ($error instanceof \Exception && !$error instanceof AuthenticationException) {
            $logger->log('error', $error->getMessage());
            $error = new \Exception('Unexpected error.');
        }

        return $error;
    }
}
