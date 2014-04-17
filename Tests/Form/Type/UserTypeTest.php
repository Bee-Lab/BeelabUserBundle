<?php

namespace Beelab\UserBundle\Tests\Form\Type;

use Beelab\UserBundle\Entity\User;
use Beelab\UserBundle\Form\Type\UserType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @group unit
 */
class UserTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'email'   => 'test@example.org',
            'roles'   => array('ROLE_USER'),
            'active'  => true,
        );

        $type = new UserType();
        $form = $this->factory->create($type);

        $user = new User();
        $user->setEmail($formData['email']);

        // invia direttamente i dati al form
        $form->submit($formData);
        // workaround: altrimenti i salt non saranno mai uguali!
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
        $formData = array(
            'email'   => 'test@example.org',
            'roles'   => array('ROLE_USER'),
            'active'  => true,
        );

        $type = new UserType();

        #$user = new User();
        $user = $this->getMock('Beelab\UserBundle\Entity\User');
        $user->expects($this->once())->method('getId')->will($this->returnValue(123));
        $user->setEmail($formData['email']);

        $form = $this->factory->create($type, null, array('data' => $user));

        // invia direttamente i dati al form
        $form->submit($formData);
        // workaround: altrimenti i salt non saranno mai uguali!
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
