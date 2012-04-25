<?php

namespace Synd\MultiTenantBundle\Entity;

interface MultiTenantInterface
{
    public function getTenant();
    public function setTenant(TenantInterface $tenant);
}