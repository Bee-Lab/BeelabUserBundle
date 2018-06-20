<?php

namespace Beelab\UserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/bundles/extension.html}.
 */
class BeelabUserExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('beelab_user.user_class', $config['user_class']);
        $container->setParameter('beelab_user.user_form_type', $config['user_form_type']);
        $container->setParameter('beelab_user.password_form_type', $config['password_form_type']);
        $container->setParameter('beelab_user.filter_form_type', $config['filter_form_type']);
        $container->setParameter('beelab_user.layout', $config['layout']);
        $container->setParameter('beelab_user.route', $config['route']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('forms.xml');
        $loader->load('services.xml');
        $loader->load('controllers.xml');
    }
}
