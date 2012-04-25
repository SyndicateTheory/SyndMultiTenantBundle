<?php

namespace Synd\MultiTenantBundle\EventListener;

use Synd\MultiTenantBundle\ORM\MultiTenantEntityManager;
use Synd\MultiTenantBundle\TenantStrategy\TenantStrategyInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;


class GetTenantListener 
{
    /**
     * @var    ContainerInterface
     */
    protected $container;
    
    /**
     * @var    MultiTenantEntityManager
     */
    protected $em;
    
    /**
     * @var    TenantStrategyInterface
     */
    protected $strategy;
    
    /**
     * @var    string        Repository Class to use
     */
    protected $repoClass;
    
    /**
     * Brings EM and TenantStrategy into scope
     * @param    MultiTenantEntityManager
     * @param    TenantStrategyInterface
     */
    public function __construct(ContainerInterface $container, MultiTenantEntityManager $em, TenantStrategyInterface $strategy, $repoClass)
    {
        $this->container = $container;
        $this->em = $em;
        $this->tenantStrategy = $strategy;
        $this->repoClass = $repoClass;
    }
    
    /**
     * Set the em.tenant from our tenant strategy
     */
    public function onEarlyKernelRequest(GetResponseEvent $event)
    {
        $tenant = $this->tenantStrategy->getTenant();
        
        $this->em->setMultiTenantRepositoryClass($this->repoClass);
        $this->em->setTenant($tenant);
        
        $this->container->set('synd_multitenant.tenant', $tenant);
    }
}