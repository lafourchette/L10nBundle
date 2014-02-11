<?php

namespace L10nBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('l10n_bundle');
        $rootNode
            ->children()
            ->scalarNode('manager')->isRequired()->end()
        ;

        return $treeBuilder;
    }
}