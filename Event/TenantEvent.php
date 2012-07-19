<?php

namespace Synd\MultiTenantBundle\Event;

use Synd\MultiTenantBundle\Entity\TenantInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class TenantEvent extends Event
{
    protected $tenant;
    protected $getResponseEvent;
    
    public function __construct(TenantInterface $tenant = null, GetResponseEvent $event = null)
    {
        $this->getResponseEvent = $event;
        
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
    
    public function getRequest()
    {
        return $this->getResponseEvent->getRequest();
    }
    
    public function setResponse(Response $response)
    {
        $this->getResponseEvent->setResponse($response);
    }
}