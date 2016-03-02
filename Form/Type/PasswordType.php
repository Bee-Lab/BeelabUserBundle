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
            ->add('plainPassword', 'repeated', array(
                'first_name' => 'new_password',
                'second_name' => 'confirm_new_password',
                'type' => 'password',
                'required' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Beelab\UserBundle\Entity\User',
            'validation_groups' => array('password'),
            'translation_domain' => 'admin',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'beelab_password';
    }
}
