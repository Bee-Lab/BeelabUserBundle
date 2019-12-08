<?php

namespace Beelab\UserBundle\DependencyInjection;

use Beelab\UserBundle\Form\Type\PasswordType;
use Beelab\UserBundle\Form\Type\UserType;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('beelab_user');
        // Keep compatibility with symfony/config < 4.2
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('beelab_user');
        }

        $rootNode
            ->children()
                ->scalarNode('user_class')
                    ->isRequired()
                ->end()
                ->scalarNode('password_form_type')
                    ->cannotBeEmpty()
                    ->defaultValue(PasswordType::class)
                ->end()
                ->scalarNode('user_form_type')
                    ->cannotBeEmpty()
                    ->defaultValue(UserType::class)
                ->end()
                ->scalarNode('filter_form_type')
                    ->defaultNull()
                ->end()
                ->scalarNode('layout')
                    ->cannotBeEmpty()
                    ->defaultValue('BeelabUserBundle::layout.html.twig')
                ->end()
                ->scalarNode('route')
                    ->cannotBeEmpty()
                    ->defaultValue('admin')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
