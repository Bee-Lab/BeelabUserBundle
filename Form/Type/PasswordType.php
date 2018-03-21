<?php

namespace Beelab\UserBundle\Form\Type;

use Beelab\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', Type\RepeatedType::class, [
                'first_name' => 'new_password',
                'second_name' => 'confirm_new_password',
                'type' => Type\PasswordType::class,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['password'],
            'translation_domain' => 'admin',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'beelab_password';
    }
}
