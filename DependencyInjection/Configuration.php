<?php

namespace Beelab\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('beelab_user');

        $rootNode
            ->children()
                ->scalarNode('user_class')
                    ->isRequired()
                ->end()
                ->scalarNode('user_manager_class')
                    ->cannotBeEmpty()
                    ->defaultValue('Beelab\UserBundle\Manager\UserManager')
                ->end()
                ->scalarNode('password_form_type')
                    ->cannotBeEmpty()
                    ->defaultValue('Beelab\UserBundle\Form\Type\PasswordType')
                ->end()
                ->scalarNode('user_form_type')
                    ->cannotBeEmpty()
                    ->defaultValue('Beelab\UserBundle\Form\Type\UserType')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
