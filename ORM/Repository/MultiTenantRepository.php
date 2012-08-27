<?php

namespace Synd\MultiTenantBundle\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;

/**
 * Taken from Doctrine\ORM\EntityRepository where needed
 */
class MultiTenantRepository extends EntityRepository
{
    protected $tenantFiltering = true;
    protected $tenantField;
    
    /**
     * {@inheritDoc}
     */
    public function find($id, $lockMode = LockMode::NONE, $lockVersion = null)
    {
        // Check identity map first
        if ($entity = $this->_em->getUnitOfWork()->tryGetById($id, $this->_class->rootEntityName)) {
            if (!($entity instanceof $this->_class->name)) {
                return null;
            }

            if ($lockMode != LockMode::NONE) {
                $this->_em->lock($entity, $lockMode, $lockVersion);
            }

            return $entity; // Hit!
        }

        if ( ! is_array($id) || count($id) <= 1) {
            // @todo FIXME: Not correct. Relies on specific order.
            $value = is_array($id) ? array_values($id) : array($id);
            $id = array_combine($this->_class->identifier, $value);
        }
        
        $id = $this->addTenantFilter($id);

        if ($lockMode == LockMode::NONE) {
            return $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName)->load($id);
        } else if ($lockMode == LockMode::OPTIMISTIC) {
            if (!$this->_class->isVersioned) {
                throw OptimisticLockException::notVersioned($this->_entityName);
            }
            $entity = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName)->load($id);

            $this->_em->getUnitOfWork()->lock($entity, $lockMode, $lockVersion);

            return $entity;
        } else {
            if (!$this->_em->getConnection()->isTransactionActive()) {
                throw TransactionRequiredException::transactionRequired();
            }

            return $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName)->load($id, null, null, array(), $lockMode);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy(
            $this->addTenantFilter($criteria),
            $orderBy,
            $limit,
            $offset
        );
    }
    
    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria)
    {
        return parent::findOneBy($this->addTenantFilter($criteria));
    }
    
    /**
     * {@inheritDoc}
     */
    public function createQueryBuilder($alias)
    {
        $query = parent::createQueryBuilder($alias);
        if ($this->tenantFiltering) {
            $query->andWhere("$alias.site = ?", $this->_em->getTenant());
        }
        
        return $query;
    }
    
    public function setTenantFiltering($flag = true)
    {
        $this->tenantFiltering = $flag;
    }
    
    public function setTenantField($field)
    {
        $this->tenantField = $field;
    }
    
    /**
     * Adds a Tenant filter to our criteria
     * @param    array        Query criteria
     * @return   array        Query criteria, with Tenant added
     */
    protected function addTenantFilter(array $criteria)
    {
        if ($this->tenantFiltering and $this->_em->getTenant()) {
            $criteria[$this->tenantField] = $this->_em->getTenant()->getId();
        }
        
        return $criteria;
    }
    
    // not supported - named queries?
    // custom code that hits persister->load* directly
}