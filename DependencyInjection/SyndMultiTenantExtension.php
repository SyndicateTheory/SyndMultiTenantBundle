<?php

namespace Synd\MultiTenantBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

class SyndMultiTenantExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        
        $container->setParameter('synd_multitenant.hostname_entity', $config['domainstrategy']['entity_class']);
        $container->setParameter('synd_multitenant.hostname_field', $config['domainstrategy']['entity_field']);
    }
}