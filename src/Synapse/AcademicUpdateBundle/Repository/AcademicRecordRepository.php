<?php
namespace Synapse\AcademicUpdateBundle\Repository;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\AcademicUpdateBundle\Entity\AcademicRecord;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\SynapseRepository;


class AcademicRecordRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseAcademicUpdateBundle:AcademicRecord';

    /**
     * Finds an entity by its primary key / identifier.
     * Override added to inform PhpStorm about the return type.
     *
     * @param mixed $id The identifier.
     * @param SynapseException| null $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return AcademicRecord
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $academicRecordEntity = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($academicRecordEntity, $exception);
    }


    /**
     * Finds entity by the criteria passed in
     * Override added to inform PhpStorm about the return type.
     *
     * @param array $criteria
     * @param SynapseException| null $exception
     * @param array|null $orderBy
     * @return AcademicRecord|null
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null)
    {
        $academicRecordEntity = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($academicRecordEntity, $exception);
    }

    /**
     * Finds set of academic record entities based on the criteria passed in
     * Override added to inform PhpStorm about the return type.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @param SynapseException $exception
     * @return AcademicRecord[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $academicRecordEntities =  parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($academicRecordEntities, $exception);
    }
}
