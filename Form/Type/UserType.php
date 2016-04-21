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
            ->add('email', 'Symfony\Component\Form\Extension\Core\Type\EmailType')
            ->add('plainPassword', 'Symfony\Component\Form\Extension\Core\Type\RepeatedType', [
                'first_name' => 'password',
                'second_name' => 'confirm',
                'type' => 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
                'required' => $isNew,
            ])
            ->add('roles', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'choices' => array_flip($roles),
                'choices_as_values' => true,
                'multiple' => true,
            ])
            ->add('active', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', ['required' => false])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Beelab\UserBundle\Entity\User',
            'translation_domain' => 'admin',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'beelab_user';
    }
}
