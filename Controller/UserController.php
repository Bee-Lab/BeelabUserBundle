<?php

namespace Beelab\UserBundle\Controller;

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
     * Lists all User entities.
     *
     * @Route("/", name="user")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $users = $this->get('beelab_user.manager')->getList($request->query->get('page', 1), 20);

        return array('users' => $users);
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

        return array(
            'user'        => $user,
            'delete_form' => $deleteForm->createView(),
        );
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
        $form = $this->createForm('beelab_user', $user, array('validation_groups' => array('create')));
        if ($request->isMethod('post') && $form->handleRequest($request)->isValid()) {
            $this->get('beelab_user.manager')->create($user);

            return $this->redirect($this->generateUrl('user_show', array('id' => $user->getId())));
        }

        return array(
            'user' => $user,
            'form' => $form->createView(),
        );
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
        $editForm = $this->createForm('beelab_user', $user, array('validation_groups' => array('update'), 'method' => 'PUT'));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->get('beelab_user.manager')->update($user);

            return $this->redirect($this->generateUrl('user_show', array('id' => $user->getId())));
        }
        $deleteForm = $this->createDeleteForm($user->getId());

        return array(
            'user'        => $user,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
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
        if ($form->handleRequest($request)->isValid()) {
            $this->get('beelab_user.manager')->delete($user);
        }

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * Change password.
     *
     * @Route("/password", name="user_password")
     * @Method({"GET", "PUT"})
     * @Template()
     */
    public function passwordAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm('beelab_password', $user, array('method' => 'PUT'));
        if ($form->handleRequest($request)->isValid()) {
            $this->get('beelab_user.manager')->update($user);

            return $this->redirect($this->generateUrl('user_show', array('id' => $user->getId())));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Create Delete form
     *
     * @param  int $id
     *
     * @return Form
     */
    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id), array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl('user_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
