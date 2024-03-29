<?php
namespace Synapse\CoreBundle\Repository;

/**
 * xListValuesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
use Doctrine\ORM\Query\Expr\Expr;
use Doctrine\ORM\Query\Expr\Join;
use Synapse\CoreBundle\Entity\MetadataListValues;


class MetadataListValuesRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:MetadataListValues';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param \Exception $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                           or NULL if no specific lock mode should be used
     *                           during the search.
     * @param int|null $lockVersion The lock version.
     * @return MetadataListValues|null
     * @throws \Exception
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $object = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($object, $exception);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param \Exception $exception
     * @return MetadataListValues[]|null
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $object = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($object, $exception);
    }

    /**
     *  Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param \Exception $exception
     * @param array|null $orderBy
     * @return MetadataListValues|null
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null){
        $object = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($object, $exception);
    }


    public function create(MetadataListValues $metaDataListValues)
    {
        $em = $this->getEntityManager();
        $em->persist($metaDataListValues);
        return $metaDataListValues;
    }

    public function remove(MetadataListValues $metaDataListValues)
    {
        $em = $this->getEntityManager();
        $em->remove($metaDataListValues);

    }


    /**
     * Get All Timezones
     *
     * @return array  List of timezones
     */

    public function getTimezones()
    {
        $entity_manager = $this->getEntityManager();

        $query_bulder = $entity_manager->createQueryBuilder();
        $query_bulder->select('trim(metavals.listName) as timezone_name','trim(metavals.listValue) as timezone')
        ->from('SynapseCoreBundle:MetadataListValues', 'metavals')
        ->join('metavals.metadata', 'master')
        ->where('master.key= :key')
        ->setParameters(array('key'=> 'System_timezones'))
        ->orderBy('timezone', 'ASC');    
        $query = $query_bulder->getQuery();
        $result = $query->getArrayResult();


        if (count($result) <= 0) {
            $result = null;
        }
        return $result;
    }
    
    public function getListValues($metadataid,$listvalue)
    {
        $entity_manager = $this->getEntityManager();
         
        $query_bulder = $entity_manager->createQueryBuilder();
        $query_bulder->select('metavals.listName as listName')
        ->from('SynapseCoreBundle:MetadataListValues', 'metavals')
    
        ->where('metavals.metadata= :key AND metavals.listValue = :listValue')
        ->setParameters(array('key'=> $metadataid,'listValue'=>$listvalue));
        $query = $query_bulder->getQuery();
        $result = $query->getArrayResult();
         
         
        return $result;
    }
    

}