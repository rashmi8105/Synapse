<?php

namespace Synapse\MapworksToolBundle\Repository;

use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\MapworksToolBundle\Entity\MapworksTool;

class MapworksToolRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseMapworksToolBundle:MapworksTool';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param SynapseException $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants or NULL if no specific lock mode should be used during the search.
     * @param int|null $lockVersion The lock version.
     * @return MapworksTool|null
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $object = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($object, $exception);
    }


    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param SynapseException $exception
     * @return MapworksTool[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $objects = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($objects, $exception);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param SynapseException $exception
     * @param array|null $orderBy
     *
     * @return MapworksTool|null
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null)
    {
        $object = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($object, $exception);
    }

    /**
     * Override for PHPTyping
     *
     * @param SynapseException $exception
     *
     * @return MapworksTool[]
     */
    public function findAll($exception = null)
    {
        $objects =  parent::findAll();
        return $this->doObjectsExist($objects, $exception);
    }

}