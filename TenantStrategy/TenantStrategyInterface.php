<?php

namespace Synd\MultiTenantBundle\TenantStrategy;

interface TenantStrategyInterface
{
    public function getTenant();
}