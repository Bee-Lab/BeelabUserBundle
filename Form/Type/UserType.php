<?php

namespace Beelab\UserBundle\Form\Type;

use Beelab\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = true;
        if (isset($options['data'])) {
            if (null !== $options['data']->getId()) {
                $isNew = false;
            }
            $user = $options['data'];
            $roles = $user::getRoleLabels();
        } else {
            $roles = User::getRoleLabels();
        }

        $builder
            ->add('email', Type\EmailType::class)
            ->add('plainPassword', Type\RepeatedType::class, [
                'first_name' => 'password',
                'second_name' => 'confirm',
                'type' => Type\PasswordType::class,
                'required' => $isNew,
            ])
            ->add('roles', Type\ChoiceType::class, [
                'choices' => array_flip($roles),
                'multiple' => true,
            ])
            ->add('active', Type\CheckboxType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'admin',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'beelab_user';
    }
}
