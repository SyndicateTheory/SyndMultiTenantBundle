<?php

namespace Synd\MultiTenantBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('synd_multi_tenant');

        $rootNode
            ->children()
                ->arrayNode('domainstrategy')
                    ->children()
                        ->scalarNode('entity_class')->end()
                        ->scalarNode('entity_field')->defaultValue('domain')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}