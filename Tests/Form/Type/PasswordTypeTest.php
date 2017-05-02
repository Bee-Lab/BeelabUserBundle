<?php

namespace Beelab\UserBundle\Tests\Form\Type;

use Beelab\UserBundle\Form\Type\PasswordType;
use Beelab\UserBundle\Test\TypeTestCase;
use Beelab\UserBundle\Test\UserStub as User;

/**
 * @group unit
 */
class PasswordTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'plainPassowrd' => [
                'new_password' => 'paperino',
                'repeat_new_password' => 'paperino',
            ],
        ];

        $form = $this->factory->create(PasswordType::class, null, ['data_class' => User::class]);

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
