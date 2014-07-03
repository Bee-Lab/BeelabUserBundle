<?php

namespace Beelab\UserBundle\Tests\Form\Type;

use Beelab\UserBundle\Entity\User;
use Beelab\UserBundle\Form\Type\PasswordType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @group unit
 */
class PasswordTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'plainPassowrd' => array(
                'new_password'        => 'paperino',
                'repeat_new_password' => 'paperino',
            )
        );

        $type = new PasswordType();
        $form = $this->factory->create($type);

        $user = new User();
        $user->setPlainPassword(null);

        // invia direttamente i dati al form
        $form->submit($formData);
        // workaround: altrimenti i salt non saranno mai uguali!
        $user->setSalt($form->getData()->getSalt());

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user, $form->getData());
    }
}
