<?php

namespace Beelab\UserBundle\Tests\Form\Type;

use Beelab\UserBundle\Form\Type\PasswordType;
use Beelab\UserBundle\Test\UserStub as User;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @group unit
 */
class PasswordTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'plainPassowrd' => [
                'new_password'        => 'paperino',
                'repeat_new_password' => 'paperino',
            ],
        ];

        $type = new PasswordType();
        $form = $this->factory->create($type, null, ['data_class' => 'Beelab\UserBundle\Test\UserStub']);

        $user = new User();
        $user->setPlainPassword(null);

        // send directly data to form
        $form->submit($formData);
        // workaround: without this, salts will never match!
        $user->setSalt($form->getData()->getSalt());

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user, $form->getData());
    }
}
