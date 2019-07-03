<?php
namespace Synapse\ReportsBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\ReportsBundle\Entity\ReportsRunningJson;

class ReportsRunningJsonRepository extends SynapseRepository
{

	const REPOSITORY_KEY = 'SynapseReportsBundle:ReportsRunningJson';

    /**
     * @param mixed $id
     * @param int|null|null $lockMode
     * @param int|null|null $lockVersion
     * @return ReportsRunningJson|null
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return ReportsRunningJson[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Find only one entity based on criteria
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @return null|ReportsRunningJson
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

}