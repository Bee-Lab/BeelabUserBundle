<?php

namespace Beelab\UserBundle\Controller;

use Beelab\UserBundle\Event\FormEvent;
use Beelab\UserBundle\Event\UserEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities (with possibile filter).
     *
     * @Route("", name="user")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        if (!is_null($filter = $this->getFilterFormName())) {
            $form = $this->createForm($filter);
            $event = new FormEvent($form, $request);
            $this->get('event_dispatcher')->dispatch('beelab_user.filter', $event);
            if (!is_null($response = $event->getResponse())) {
                return $response;
            }
            $this->get('event_dispatcher')->dispatch('beelab_user.filter_apply', $event);
        }
        $users = $this->get('beelab_user.manager')->getList($request->query->get('page', 1), 20);

        return empty($form) ? ['users' => $users] : ['users' => $users, 'form' => $form->createView()];
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}/show", name="user_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $user = $this->get('beelab_user.manager')->get($id);
        $deleteForm = $this->createDeleteForm($user->getId());

        return [
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $user = $this->get('beelab_user.manager')->getInstance();
        $form = $this->createForm($this->getUserFormName(), $user, ['validation_groups' => ['create']]);
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $this->get('beelab_user.manager')->create($user);

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return [
            'user' => $user,
            'form' => $form->createView(),
        ];
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "PUT"})
     * @Template()
     */
    public function editAction($id, Request $request)
    {
        $user = $this->get('beelab_user.manager')->get($id);
        $editForm = $this->createForm($this->getUserFormName(), $user, ['validation_groups' => ['update'], 'method' => 'PUT']);
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $this->get('beelab_user.manager')->update($user);

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }
        $deleteForm = $this->createDeleteForm($user->getId());

        return [
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}/delete", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id, Request $request)
    {
        $user = $this->get('beelab_user.manager')->get($id);
        $form = $this->createDeleteForm($user->getId());
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $this->get('beelab_user.manager')->delete($user);
        }

        return $this->redirectToRoute('user');
    }

    /**
     * Change password.
     *
     * @Route("/password", name="user_password")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function passwordAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm($this->getPasswordFormName(), $user);
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $this->get('beelab_user.manager')->update($user);
            $this->get('event_dispatcher')->dispatch('beelab_user.change_password', new UserEvent($user));

            return $this->redirectToRoute($this->getParameter('beelab_user.route'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Create Delete form.
     *
     * @param int $id
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder(['id' => $id], ['attr' => ['id' => 'delete']])
            ->setAction($this->generateUrl('user_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @return string
     */
    protected function getUserFormName()
    {
        return $this->container->getParameter('beelab_user.user_form_type');
    }

    /**
     * @return string
     */
    protected function getPasswordFormName()
    {
        return $this->container->getParameter('beelab_user.password_form_type');
    }

    /**
     * @return string
     */
    protected function getFilterFormName()
    {
        return $this->container->getParameter('beelab_user.filter_form_type');
    }
}
