<?php

namespace L10nBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
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
        $loader->load('twig.xml');

        $container->setParameter('localization_fallback', $config['localization_fallback']);
        $container->setParameter('locale_fallback', $config['locale_fallback']);

        $this->loadDataManager($config, $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function loadDataManager(array $config, ContainerBuilder $container)
    {
        $loadDefinitionMethod = 'load' . ucfirst($config['manager']) . 'Manager';

        if (method_exists($this, $loadDefinitionMethod)) {
            $definition = $this->$loadDefinitionMethod($config[$config['manager']], $container);
            $container->setDefinition('l10n_bundle.l10n_manager', $definition);
        }
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @return Definition
     */
    private function loadYamlManager(array $config, ContainerBuilder $container)
    {
        return new Definition('%l10n_bundle.manager.l10n_yaml.class%', $config);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @return Definition
     */
    private function loadMongodbManager(array $config, ContainerBuilder $container)
    {
        return new Definition('%l10n_bundle.manager.l10n_mongodb.class%', $config);
    }
}
