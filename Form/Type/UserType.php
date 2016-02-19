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
            ->add('email', $this->isLegacy() ? 'email' : 'Symfony\Component\Form\Extension\Core\Type\EmailType')
            ->add('plainPassword', $this->isLegacy() ? 'repeated' : 'Symfony\Component\Form\Extension\Core\Type\RepeatedType', array(
                'first_name' => 'password',
                'second_name' => 'confirm',
                'type' => $this->isLegacy() ? 'password' : 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
                'required' => $isNew,
            ))
            ->add('roles', $this->isLegacy() ? 'choice' : 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array('choices' => $roles, 'multiple' => true))
            ->add('active', $this->isLegacy() ? 'checkbox' : 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array('required' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Beelab\UserBundle\Entity\User',
            'translation_domain' => 'admin',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return $this->getName();
    }

    /**
     * BC for Symfony < 3.0.
     */
    public function getName()
    {
        return 'beelab_user';
    }

    /**
     * @return bool
     */
    private function isLegacy()
    {
        return !method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
    }
}
