<?php

namespace Synd\MultiTenantBundle\EventListener;

use Synd\MultiTenantBundle\Event\TenantEvent;
use Synd\MultiTenantBundle\TenantEvents;
use Synd\MultiTenantBundle\TenantStrategy\TenantStrategyInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FindTenantListener 
{
    /**
     * @var    ContainerInterface
     */
    protected $container;
    
    /**
     * @var    EventDispatcherInterface
     */
    protected $dispatcher;
    
    /**
     * @var    TenantStrategyInterface
     */
    protected $strategy;
    
    public function __construct(ContainerInterface $container, EventDispatcherInterface $dispatcher, TenantStrategyInterface $strategy)
    {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->tenantStrategy = $strategy;
    }
    
    /**
     * Set the em.tenant from our tenant strategy
     */
    public function onEarlyKernelRequest(GetResponseEvent $event)
    {
        if (!$tenant = $this->tenantStrategy->getTenant()) {
            $this->dispatcher->dispatch(TenantEvents::TENANT_NOT_FOUND, $event = new TenantEvent());
            if (!$event->getTenant()) {
                return;
            }
        }
        
        $this->dispatcher->dispatch(TenantEvents::TENANT_FOUND, new TenantEvent($tenant));
        $this->container->set('synd_multitenant.tenant', $tenant);
    }
}