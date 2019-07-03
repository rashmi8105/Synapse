<?php

namespace Synapse\UploadBundle\Repository;

use Flow\Exception;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\UploadBundle\Entity\EbiDownloadType;

class EbiDownloadTypeRepository extends SynapseRepository
{

const REPOSITORY_KEY = 'SynapseUploadBundle:EbiDownloadType';


    /**
     * Finds an entity by its primary key / identifier.
     * Override added to inform PhpStorm about the return type.
     *
     * @param mixed $id The identifier.
     * @param \Exception $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return null | EbiDownloadType
     * @throws  \Exception
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $object = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($object, $exception);
    }

    /**
     * @param array $criteria
     * @param \Exception $exception
     * @param array|null $orderBy
     * @return null | EbiDownloadType
     * @throws \Exception
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null)
    {
        $object = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($object, $exception);
    }


    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param \Exception|null $exception
     *
     * @return EbiDownloadType[]
     * @throws \Exception
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $objectArray = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($objectArray, $exception);
    }

}