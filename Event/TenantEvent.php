<?php

namespace Synd\MultiTenantBundle\Event;

use Synd\MultiTenantBundle\Entity\TenantInterface;
use Symfony\Component\EventDispatcher\Event;

class TenantEvent extends Event
{
    protected $tenant;
    
    public function __construct(TenantInterface $tenant = null)
    {
        if ($tenant) {
            $this->setTenant($tenant);
        }
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