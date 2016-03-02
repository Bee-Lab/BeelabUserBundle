<?php

namespace Beelab\UserBundle\Tests\Form\Type;

use Beelab\UserBundle\Form\Type\UserType;
use Beelab\UserBundle\Test\UserStub as User;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @group unit
 */
class UserTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'email' => 'test@example.org',
            'roles' => ['ROLE_USER'],
            'active' => true,
        ];

        $type = new UserType();
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $type = get_class($type);
        }
        $form = $this->factory->create($type, null, ['data_class' => 'Beelab\UserBundle\Test\UserStub']);

        $user = new User();
        $user->setEmail($formData['email']);

        // send data directly to form
        $form->submit($formData);
        // workaround: without this, salts would never match!
        $user->setSalt($form->getData()->getSalt());

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    public function testIsOld()
    {
        $formData = [
            'email' => 'test@example.org',
            'roles' => ['ROLE_USER'],
            'active' => true,
        ];

        $type = new UserType();
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $type = get_class($type);
        }

        $user = new User();

        $form = $this->factory->create($type, null, ['data' => $user]);

        // send data directly to form
        $form->submit($formData);
        // workaround: without this, salts would never match!
        $user->setSalt($form->getData()->getSalt());

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
