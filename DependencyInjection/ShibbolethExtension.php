<?php

namespace ShibbolethBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;


class ShibbolethExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        /*
        $authenticator = $container->register('shibboleth_authenticator',
            'ShibbolethBundle\Security\ShibbolethGuardAuthenticator');
        $authenticator->setArguments(array($config));

        $container->register('shibboleth_provider',
            'ShibbolethBundle\Security\User\ShibbolethUserProvider');
        */

        $container->setParameter('shibboleth', $config);


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
