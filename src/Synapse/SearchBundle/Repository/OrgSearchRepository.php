<?php
namespace Synapse\SearchBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\SearchBundle\Entity\OrgSearch;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

class OrgSearchRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSearchBundle:OrgSearch';

    public function getOrgSearch($orgSearch)
    {
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($orgSearch);
            $stmt->execute();
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return $stmt->fetchAll();
    }

    public function createSharedSearch(OrgSearch $sharedOrgSearch)
    {
        $em = $this->getEntityManager();
        $em->persist($sharedOrgSearch);
        return $sharedOrgSearch;
    }

    public function createOrgSearch($OrgSearch)
    {
        $em = $this->getEntityManager();
        $em->persist($OrgSearch);
        return $OrgSearch;
    }

    public function deleteSaveSearch($OrgSearch)
    {
        $this->getEntityManager()->remove($OrgSearch);
    }

    public function deleteSharedOrgSearch(OrgSearch $orgSearch)
    {
        $this->getEntityManager()->remove($orgSearch);
    }

    public function getUsersSharedList($loggedInUser, $orgId)
    {
        try {
            $em = $this->getEntityManager();
            $sql = "(SELECT w.org_search_id as saved_search_id, w.org_search_id_dest as shared_search_id,
    	w.shared_on,'with' as flag,s.name ,w.person_id_sharedwith as person_id, p.firstname,p.lastname
    	FROM org_search_shared_with w LEFT JOIN org_search s on (w.org_search_id=s.id)
    	LEFT JOIN person p on p.id=w.person_id_sharedwith
    	WHERE w.deleted_at IS NULL and p.deleted_at IS NULL and s.person_id= " . $loggedInUser . " and 
    	s.organization_id = " . $orgId . " group by w.org_search_id)
    	UNION
    	(SELECT b.org_search_id_source as saved_search_id, b.org_search_id as shared_search_id,
    	b.shared_on,'by' as flag,s.name ,b.person_id_shared_by as person_id, p.firstname,p.lastname
    	FROM org_search_shared_by b LEFT JOIN org_search s on (b.org_search_id=s.id)
    	LEFT JOIN person p on p.id=b.person_id_shared_by
    	WHERE b.deleted_at IS NULL and p.deleted_at IS NULL and s.person_id = " . $loggedInUser . " and 
    	s.organization_id = " . $orgId . ") order by shared_on DESC";
            
            $resultSet = $em->getConnection()->fetchAll($sql);
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return $resultSet;
    }

    public function findSearchSharedWith($searchId, $loggedInUser)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('os.id as orgSearch')
            ->from('SynapseSearchBundle:OrgSearch', 'os')
            ->where('os.editedByMe = :edited')
            ->andWhere('os.person = :userId')
            ->andWhere('os.id = :searchId')
            ->setParameters(array(
            'edited' => true,
            'userId' => $loggedInUser,
            'searchId' => $searchId
        ))
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    /**
     * get student ids with risk display flag and intent display flag with respect to faculty id
     *
     * @param int $facultyId
     * @param string $studentIds
     * @return array
     */
    public function getRiskIntentData($facultyId, $studentIds)
    {
        $studentIdsArray = [];
        if($studentIds){
            $studentIdsArray = explode(',',$studentIds);
        }

        $sql = "SELECT 
                    ofspm.student_id,
                    ofspm.permissionset_id,
                    ops.intent_to_leave AS intent_flag,
                    ops.risk_indicator AS risk_flag
                FROM
                    org_faculty_student_permission_map AS ofspm
                        INNER JOIN
                    org_permissionset AS ops ON ops.id = ofspm.permissionset_id
                        AND ops.deleted_at IS NULL
                WHERE
                    faculty_id = :facultyId
                    AND ofspm.student_id IN (:studentIds)";

        try {
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql,[
                'facultyId' => $facultyId,
                'studentIds' => $studentIdsArray
            ], [
                'studentIds' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY
            ]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }
    
}