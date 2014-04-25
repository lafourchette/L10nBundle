<?php

namespace L10nBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class L10nExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('manager.xml');
        $loader->load('business.xml');

        $container->setParameter('default_localisation', $config['default_localisation']);
        $container->setParameter('default_locale', $config['default_locale']);


        //YAML config
        $container->setParameter('yaml_data_file', count($config['yaml']) ? $config['yaml']['data_file'] : '');

        //MongoDB config
        $container->setParameter('mongodb_host', count($config['mongodb']) ? $config['mongodb']['host'] : '');
        $container->setParameter('mongodb_port', count($config['mongodb']) ? $config['mongodb']['port'] : '');
        $container->setParameter('mongodb_username', count($config['mongodb']) ? $config['mongodb']['username'] : '');
        $container->setParameter('mongodb_password', count($config['mongodb']) ? $config['mongodb']['password'] : '');
        $container->setParameter('mongodb_database', count($config['mongodb']) ? $config['mongodb']['database'] : '');

        $container->setAlias('l10n_bundle.l10n_manager', $config['manager']);
    }
}