<?php

namespace Beelab\UserBundle\Form\Type;

use Beelab\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
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
            ->add('email', Type\EmailType::class)
            ->add('plainPassword', Type\RepeatedType::class, [
                'first_name' => 'password',
                'second_name' => 'confirm',
                'type' => Type\PasswordType::class,
                'required' => $isNew,
            ])
            ->add('roles', Type\ChoiceType::class, [
                'choices' => array_flip($roles),
                'choices_as_values' => true,
                'multiple' => true,
            ])
            ->add('active', Type\CheckboxType::class, ['required' => false])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
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
