<?php

namespace Beelab\UserBundle\Form\Type;

use Beelab\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isNew = true;
        if (isset($options['data'])) {
            if (!is_null($options['data']->getId())) {
                $isNew = false;
            }
            $user = $options['data'];
            $roles = $user::getRoleLabels();
        } else {
            $roles = User::getRoleLabels();
        }

        $builder
            ->add('email')
            ->add('plainPassword', 'repeated', array(
                'first_name'  => 'password',
                'second_name' => 'confirm',
                'type'        => 'password',
                'required'    => $isNew,
            ))
            ->add('roles', 'choice', array('choices' => $roles, 'multiple' => true))
            ->add('active', 'checkbox', array('required' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'Beelab\UserBundle\Entity\User',
            'translation_domain' => 'admin',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'beelab_user';
    }
}
