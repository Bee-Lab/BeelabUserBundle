<?php

namespace Beelab\UserBundle\Controller;

use Beelab\UserBundle\Event\FormEvent;
use Beelab\UserBundle\Event\UserEvent;
use Beelab\UserBundle\Manager\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * Lists all User entities (with possible filter).
     *
     * @Route("", name="user")
     * @Method("GET")
     */
    public function indexAction(EventDispatcherInterface $dispatcher, UserManagerInterface $manager, Request $request): Response
    {
        if (null !== $filter = $this->getFilterFormName()) {
            $form = $this->createForm($filter);
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch('beelab_user.filter', $event);
            if (null !== $response = $event->getResponse()) {
                return $response;
            }
            $dispatcher->dispatch('beelab_user.filter_apply', $event);
        }
        $users = $manager->getList($request->query->get('page', 1), 20);

        return $this->render('BeelabUserBundle:User:index.html.twig', [
            'users' => $users,
            'form' => isset($form) ? $form->createView() : null,
        ]);
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}/show", name="user_show")
     * @Method("GET")
     */
    public function showAction($id, UserManagerInterface $manager): Response
    {
        $user = $manager->get($id);
        $deleteForm = $this->createDeleteForm($user->getId());

        return $this->render('BeelabUserBundle:User:show.html.twig', [
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(UserManagerInterface $manager, Request $request): Response
    {
        $user = $manager->getInstance();
        $form = $this->createForm($this->getUserFormName(), $user, ['validation_groups' => ['create']]);
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $manager->create($user);

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render('BeelabUserBundle:User:new.html.twig',[
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "PUT"})
     */
    public function editAction($id, UserManagerInterface $manager, Request $request): Response
    {
        $user = $manager->get($id);
        $editForm = $this->createForm($this->getUserFormName(), $user, ['validation_groups' => ['update'], 'method' => 'PUT']);
        if ($editForm->handleRequest($request)->isSubmitted() && $editForm->isValid()) {
            $manager->update($user);

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }
        $deleteForm = $this->createDeleteForm($user->getId());

        return $this->render('BeelabUserBundle:User:edit.html.twig',[
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}/delete", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id, UserManagerInterface $manager, Request $request): Response
    {
        $user = $manager->get($id);
        $form = $this->createDeleteForm($user->getId());
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $manager->delete($user);
        }

        return $this->redirectToRoute('user');
    }

    /**
     * Change password.
     *
     * @Route("/password", name="user_password")
     * @Method({"GET", "POST"})
     */
    public function passwordAction(EventDispatcherInterface $dispatcher, UserManagerInterface $manager, Request $request, ParameterBagInterface $bag): Response
    {
        $user = $this->getUser();
        $form = $this->createForm($this->getPasswordFormName(), $user);
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $manager->update($user);
            $dispatcher->dispatch('beelab_user.change_password', new UserEvent($user));

            return $this->redirectToRoute($bag->get('beelab_user.route'));
        }

        return $this->render('BeelabUserBundle:User:password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    protected function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder(['id' => $id], ['attr' => ['id' => 'delete']])
            ->setAction($this->generateUrl('user_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    protected function getUserFormName(): string
    {
        return $this->container->getParameter('beelab_user.user_form_type');
    }

    protected function getPasswordFormName(): string
    {
        return $this->container->getParameter('beelab_user.password_form_type');
    }

    protected function getFilterFormName(): string
    {
        return $this->container->getParameter('beelab_user.filter_form_type');
    }
}
