<?php

namespace Synd\MultiTenantBundle\Entity;

use Synd\MultiTenantBundle\Entity\TenantInterface;

interface MultiTenantInterface
{
    public function getTenant();
    public function setTenant(TenantInterface $tenant);
}