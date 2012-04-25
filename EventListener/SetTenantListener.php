<?php

namespace Synd\MultiTenantBundle\EventListener;

use Synd\MultiTenantBundle\Entity\MultiTenantInterface;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;

class SetTenantListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array('prePersist');
    }
    
    /**
     * If entity being persisted is multi-tenant capable, assign the 
     * active tenant to it and recalculate changes.
     * 
     * @param    EventArgs
     */
    public function prePersist(EventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();
        
        if ($entity instanceof MultiTenantInterface and $tenant = $em->getTenant()) {
            $entity->setTenant($tenant);
            
            $metadata = $em->getClassMetadata(get_class($entity));

            $uow = $em->getUnitOfWork();
            $uow->recomputeSingleEntityChangeSet($metadata, $entity);
        }
    }
}