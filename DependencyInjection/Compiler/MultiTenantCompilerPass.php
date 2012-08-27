<?php

namespace Synd\MultiTenantBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Parameter;

class MultiTenantCompilerPass implements CompilerPassInterface
{
    /**
     * Overrides the em service to use our own entity manager class instead
     * Overrides the em service to call bring the active tenant and set the tenant repo class
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->getParameter('doctrine.orm.entity_manager.class') == 'Doctrine\\ORM\\EntityManager') {
            $container->setParameter('doctrine.orm.entity_manager.class', 'Synd\\MultiTenantBundle\\ORM\\MultiTenantEntityManager');#'%synd_multitenant.em_class%');
        }
        
        if ($container->hasDefinition('doctrine.orm.default_entity_manager')) {
            $reference = $container->getDefinition('doctrine.orm.default_entity_manager');
            $reference->addMethodCall('setTenantField', array($container->getParameter('synd_multitenant.tenant_field')));
        }
    }
}