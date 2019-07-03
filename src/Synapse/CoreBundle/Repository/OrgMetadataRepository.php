<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\OrgMetadata;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\Util\Constants\PersonConstant;

class OrgMetadataRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgMetadata';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param \Exception $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                           or NULL if no specific lock mode should be used
     *                           during the search.
     * @param int|null $lockVersion The lock version.
     * @return OrgMetadata
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
     * @return OrgMetadata[]
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
     * @return OrgMetadata
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null){
        $object = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($object, $exception);
    }

    public function getProfile($organizationid, $exclude = false, $blocks = null, $status = null ,$excludeType = null)
    {
        
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('om.id as org_metadata_id', 'om.modifiedAt as modified_at', 'om.metaKey as item_label','om.metaName as display_name', 'om.metaDescription as item_subtext', 'om.metadataType as item_data_type', 'om.definitionType as definition_type', 'om.sequence as sequence_no', 'om.noOfDecimals as decimal_points', 'om.minRange as min_digits', 'om.maxRange as max_digits', 'om.status', 'pom.id as pom_id', 'au.id as au_id','om.scope as calendar_assignment');
        $qb->from(PersonConstant::SYNAPSE_ORG_META_DATA_ENTITY, 'om');
        $qb->LEFTJoin('SynapseCoreBundle:PersonOrgMetadata', 'pom', \Doctrine\ORM\Query\Expr\Join::WITH, 'pom.orgMetadata = om.id');
        $qb->LEFTJoin('SynapseAcademicUpdateBundle:AcademicUpdateRequestMetadata', 'au', \Doctrine\ORM\Query\Expr\Join::WITH, 'au.orgMetadata = om.id');
        $qb->where('om.organization = :organizationid');
        if ($exclude == "text") {

            
            $qb->andWhere('om.metadataType != :metadataType');
            $qb->andWhere('om.id IN (:id)');
            if($excludeType){
                
                $qb->andWhere('(om.scope != :excludeType) OR om.scope IS NULL');
                $qb->setParameters(array(
                    PersonConstant::ORGANIZATION_ID => $organizationid,
                    'metadataType' => 'T',
                    'id' => $blocks,
                    'excludeType' => $excludeType
                ));
            
            }else{
                $qb->setParameters(array(
                    PersonConstant::ORGANIZATION_ID => $organizationid,
                    'metadataType' => 'T',
                    'id' => $blocks
                ));
            }
        
        
        } else {
            if (strtolower($status) == 'active') {
                $qb->andWhere('om.status = :status OR om.status IS NULL');
                
                $paramArr =  array(
                    PersonConstant::ORGANIZATION_ID => $organizationid,
                    'status' => $status
                );
               /* $qb->setParameters(array(
                    PersonConstant::ORGANIZATION_ID => $organizationid,
                    'status' => $status
                ));*/
            } elseif(strtolower($status) == 'archive') {
                $qb->andWhere('om.status = :status');
                $paramArr =  array(
                    PersonConstant::ORGANIZATION_ID => $organizationid,
                    'status' => "archived"
                );
                
               /* $qb->setParameters(array(
                    PersonConstant::ORGANIZATION_ID => $organizationid,
                    'status' => $status
                ));*/
            }else{
               $paramArr =  array(
                    PersonConstant::ORGANIZATION_ID => $organizationid
                );
            }
            
            if($excludeType){
                $qb->andWhere('(om.scope != :excludeType) OR om.scope IS NULL');
                $paramArr['excludeType'] = $excludeType ;
            }
            
            $qb->setParameters($paramArr);
        }
        $qb->orderBy('om.sequence', 'asc');
        $qb->groupBy(PersonConstant::OM_ID);
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }

    public function getOrgProfileCount($organization)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('max(mdm.sequence) as max_sequence');
        $qb->from(PersonConstant::SYNAPSE_ORG_META_DATA_ENTITY, 'mdm');
        $qb->where('mdm.definitionType = :definitionType AND mdm.organization =:organization');
        $qb->setParameters(array(
            "definitionType" => "O",
            "organization" => $organization
        ));
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        if ($result) {
            $result = $result[0]['max_sequence'];
        }
        return (int) $result;
    }

    public function remove($orgMetadata)
    {
        $em = $this->getEntityManager();
        $em->remove($orgMetadata);
    }

    public function merge($orgMetadata)
    {
        $em = $this->getEntityManager();
        $em->merge($orgMetadata);
    }

    public function getOrgMetadataReferance($id)
    {
        $em = $this->getEntityManager();
        return $em->getReference(PersonConstant::SYNAPSE_ORG_META_DATA_ENTITY, $id);
    }

    public function getProfileByName($profileName, $campus)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(PersonConstant::OM_ID);
        $qb->from(PersonConstant::SYNAPSE_ORG_META_DATA_ENTITY, 'om');
        $qb->Join('SynapseCoreBundle:Organization', 'o', \Doctrine\ORM\Query\Expr\Join::WITH, 'o.id = om.organization');
        
        $qb->where('o.campusId = :campus AND om.metaName = :profileName');
        $qb->setParameters(array(
            'campus' => $campus,
            'profileName' => $profileName
        ));
        
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }
	
    public function IsOrgProfileExists($key)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('mdm.id');
        $qb->from(PersonConstant::SYNAPSE_ORG_META_DATA_ENTITY, 'mdm');
        $qb->where('mdm.metaKey = :metaKey');
        $qb->setParameters(array(
           
            "metaKey" => $key
        ));
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        if(count($result) > 0)
        {
            return false;
        }else{
            return true;
        }        
    }	
}