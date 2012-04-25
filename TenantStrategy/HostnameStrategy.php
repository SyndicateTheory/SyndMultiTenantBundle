<?php

namespace Synd\MultiTenantBundle\TenantStrategy;

use Synd\MultiTenantBundle\TenantStrategy\TenantStrategyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

class HostnameStrategy implements TenantStrategyInterface
{
    /**
     * @var    EntityManager
     */
    protected $em;
    
    /**
     * @var    string        Name of Entity to use
     */
    protected $entityName;
    
    /**
     * @var    string        Hostname from $_SERVER
     */
    protected $hostName;
    
    /**
     * Brings hostname into scope from Request
     * 
     * @param    EntityManager
     * @param    string        Entity name to use
     * @param    Request
     */
    public function __construct(EntityManager $em, $entityName, ContainerInterface $container)
    {
        $this->em = $em;
        $this->entityName = $entityName;
        $this->hostName = $container->get('request')->server->get('SERVER_NAME');
    }
    
    /**
     * Fetches the tenant from the database based on the Host header
     * @return    TenantInterface|null    on failure
     */
    public function getTenant()
    {
        return $this->em
            ->getRepository($this->entityName)
            ->findOneByHostname($this->hostName);
    }
}