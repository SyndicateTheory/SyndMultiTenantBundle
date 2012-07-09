<?php

namespace Synd\MultiTenantBundle\Event;

use Synd\MultiTenantBundle\Entity\TenantInterface;
use Symfony\Component\EventDispatcher\Event;

class TenantEvent extends Event
{
    protected $tenant;
    
    public function __construct(TenantInterface $tenant)
    {
        $this->tenant = $tenant;
    }
    
    public function setTenant(TenantInterface $tenant)
    {
        $this->tenant = $tenant;
    }
    
    public function getTenant()
    {
        return $this->tenant;
    }
}