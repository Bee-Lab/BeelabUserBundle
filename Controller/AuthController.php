<?php

namespace Beelab\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends Controller
{
    /**
     * Login form
     *
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        return array(
            'last_username' => $request->getSession()->get(Security::LAST_USERNAME),
            'error'         => $this->getLoginError($request),
        );
    }

    /**
     * Logout (implemented by Symfony security system)
     *
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    /**
     * Login check (implemented by Symfony security system)
     *
     * @Route("/login_check", name="login_check")
     * @Method("POST")
     */
    public function loginCheckAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    /**
     * Get possible authentication error
     *
     * @param  Request $request
     * @return mixed   Exception or array
     */
    protected function getLoginError(Request $request)
    {
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(Security::AUTHENTICATION_ERROR);
            $request->getSession()->remove(Security::AUTHENTICATION_ERROR);
        }
        // see https://github.com/symfony/symfony/issues/837#issuecomment-3000155
        if ($error instanceof \Exception && !$error instanceof BadCredentialsException) {
            $this->get('logger')->log('error', $error->getMessage());
            $error = array('message' => 'Unexpected error.');
        }

        return $error;
    }
}
