<?php

namespace UniceSIL\ShibbolethBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        
        $treeBuilder = new TreeBuilder('unice_sil_shibboleth');
        // BC for symfony/config < 4.2
        $rootNode = method_exists($treeBuilder, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('unice_sil_shibboleth');

        $rootNode
            ->children()
            ->scalarNode('login_path')->defaultValue('Shibboleth.sso/Login')->end()
            ->scalarNode('logout_path')->defaultValue('Shibboleth.sso/Logout')->end()
            ->scalarNode('login_target')->defaultValue('')->end()
            ->scalarNode('logout_target')->defaultValue('')->end()
            ->scalarNode('session_id')->defaultValue('Shib-Session-ID')->end()
            ->scalarNode('username')->defaultValue('username')->end()
            ->arrayNode('attributes')
            ->scalarPrototype()->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
