<?php

namespace Synd\MultiTenantBundle\ORM;

use Synd\MultiTenantBundle\Entity\TenantInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\Common\EventManager;

/**
 * Taken from Doctrine\ORM\EntityManager where needed
 */
class MultiTenantEntityManager extends EntityManager
{
    /**
     * @var    TenantInterface
     */
    protected $tenant;
    
    /**
     * @var    string        Multi tenant repository class
     */
    protected $multiTenantRepositoryClass;
    
    /**
     * Return self instead of hardcoded EntityManager
     * 
     * {@inheritDoc}
     */
    public static function create($conn, Configuration $config, EventManager $eventManager = null)
    {
        if (!$config->getMetadataDriverImpl()) {
            throw ORMException::missingMappingDriverImpl();
        }
    
        if (is_array($conn)) {
            $conn = \Doctrine\DBAL\DriverManager::getConnection($conn, $config, ($eventManager ?: new EventManager()));
        } else if ($conn instanceof Connection) {
            if ($eventManager !== null && $conn->getEventManager() !== $eventManager) {
                throw ORMException::mismatchedEventManager();
            }
        } else {
            throw new \InvalidArgumentException("Invalid argument: " . $conn);
        }
    
        return new self($conn, $config, $conn->getEventManager());
    }
    
    /**
     * Brings the Tenant into scope
     * @param    TenantInterface
     */
    public function setTenant(TenantInterface $tenant)
    {
        $this->tenant = $tenant;
    }
    
    /**
     * Sets the default  multi tenant repo class
     * @param    string        Classname to use
     */
    public function setMultiTenantRepositoryClass($class)
    {
        $this->multiTenantRepositoryClass = $class;
    }
    
    /**
     * Gets the active Tenant
     * @return    TenantInterface
     */
    public function getTenant()
    {
        return $this->tenant;
    }
    
    /**
     * Check if $entity implements MultiTenantInterface
     * If it does, return MultiTenantEntityRepository
     * 
     * {@inheritDoc}
     */
    public function getRepository($entityName)
    {
        $entityName = ltrim($entityName, '\\');
        if (isset($this->repositories[$entityName])) {
            return $this->repositories[$entityName];
        }
    
        $metadata = $this->getClassMetadata($entityName);
        $customRepositoryClassName = $metadata->customRepositoryClassName;
    
        if ($customRepositoryClassName !== null) {
            $repository = new $customRepositoryClassName($this, $metadata);
        } elseif ($this->tenant and $metadata->reflClass->implementsInterface('Synd\\MultiTenantBundle\\Entity\\MultiTenantInterface')) {
            $repository = new $this->multiTenantRepositoryClass($this, $metadata);
        } else {
            $repository = new EntityRepository($this, $metadata);
        }
    
        $this->repositories[$entityName] = $repository;
    
        return $repository;
    }
    
}