<?php

namespace ShibbolethBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('shibboleth');

        $rootNode
            ->children()
                ->scalarNode('login_route')->defaultValue('login')->end()
                ->scalarNode('target')->defaultValue('')->end()
                ->scalarNode('session_id')->defaultValue('Shib-Session-ID')->end()
                ->scalarNode('username')->defaultValue('username')->end()
                ->arrayNode('attributes')
                    ->scalarPrototype()->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
