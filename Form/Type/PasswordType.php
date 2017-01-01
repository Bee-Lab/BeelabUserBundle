<?php

namespace Beelab\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', 'Symfony\Component\Form\Extension\Core\Type\RepeatedType', [
                'first_name' => 'new_password',
                'second_name' => 'confirm_new_password',
                'type' => 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
                'required' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Beelab\UserBundle\Entity\User',
            'validation_groups' => ['password'],
            'translation_domain' => 'admin',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'beelab_password';
    }
}
