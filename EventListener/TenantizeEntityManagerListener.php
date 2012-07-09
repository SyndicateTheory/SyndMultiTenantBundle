<?php

namespace Synd\MultiTenantBundle\EventListener;

use Synd\MultiTenantBundle\Event\TenantEvent;
use Synd\MultiTenantBundle\MultiTenantEvents;
use Synd\MultiTenantBundle\ORM\MultiTenantEntityManager;

class TenantizeEntityManagerListener 
{
    protected $em;

    protected $repoClass;
    
    public function __construct(MultiTenantEntityManager $em, $repoClass)
    {
        $this->em = $em;
        $this->repoClass = $repoClass;
    }
    
    /**
     * Set the em.tenant from our tenant strategy
     */
    public function onTenantFound(TenantEvent $event)
    {
        $this->em->setMultiTenantRepositoryClass($this->repoClass);
        $this->em->setTenant($event->getTenant());
    }
}