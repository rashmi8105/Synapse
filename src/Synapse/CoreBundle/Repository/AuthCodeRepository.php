<?php
namespace Synapse\CoreBundle\Repository;



use Synapse\CoreBundle\Entity\AuthCode;

class AuthCodeRepository extends SynapseRepository {

    const REPOSITORY_KEY = 'SynapseCoreBundle:AuthCode';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return AuthCode |null
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
     *
     * @return AuthCode[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return AuthCode|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }
}