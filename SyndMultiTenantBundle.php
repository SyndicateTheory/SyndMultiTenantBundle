<?php

namespace Synd\MultiTenantBundle;

use Synd\MultiTenantBundle\DependencyInjection\Compiler\MultiTenantCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SyndMultiTenantBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MultiTenantCompilerPass());
    }
}