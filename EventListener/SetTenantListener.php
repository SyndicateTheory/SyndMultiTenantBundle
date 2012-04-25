<?php

namespace Synd\MultiTenantBundle\EventListener;

use Synd\MultiTenantBundle\Tenant\TenantInterface;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;

class SetTenantListener implements EventSubscriber
{
    /**
     * @var    AnnotationDrive
     */
    protected $driver;
    
    /**
     * @var    AdapterInterface
     */
    protected $adpater;
    
    /**
     * Creates a new instance
     * @param    AnnotationDriver
     * @param    AdapterInterface
     */
    public function __construct(AnnotationDriver $driver, AdapterInterface $adapter)
    {
        $this->driver = $driver;
        $this->adapter = $adapter;
    }
    
    public function getSubscribedEvents()
    {
        return array(
            'prePersist'
        );
    }
    
    /**
     * Check for @MultiTenant object being saved, and set the Tenant if necessary
     * @param    EventArgs
     */
    public function prePersist(EventArgs $args)
    {
        $entity = $this->adapter->getObjectFromArs($args);
        $config = $this->driver->readAnnotation($obj);
        $tenant = $this->driver->readTenantAnnotation($obj);
        
        if ($tenant instanceof TenantInterface) {
            $entity->setTenant($tenant);
        }
    }
    
    protected function updateObject($obj, TenantInterface $tenant = null, EventArgs $eventArgs = null)
    {
        $obj->setTenant($tenant);
    }
}