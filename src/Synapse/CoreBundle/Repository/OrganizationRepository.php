<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Util\Constants\UsersConstant;

class OrganizationRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:Organization';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param \Exception $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                           or NULL if no specific lock mode should be used
     *                           during the search.
     * @param int|null $lockVersion The lock version.
     * @return Organization|null
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
     * @return Organization[]|null
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
     * @return Organization|null
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null){
        $object = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($object, $exception);
    }

    /**
     * @return null|Organization[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    public function createOrganization(Organization $organization)
    {
        $em = $this->getEntityManager();
        $em->persist($organization);
        return $organization;
    }

    public function remove(Organization $organizationInstance)
    {
        $em = $this->getEntityManager();
        $em->remove($organizationInstance);
    }

    public function updateOrganization(Organization $organization)
    {
        $em = $this->getEntityManager();
        $em->merge($organization);
    }

    public function getInstitutionDetailsLang($orgID)
    {
        $em = $this->getEntityManager();
        
        $organization = $em->getRepository("SynapseCoreBundle:OrganizationLang")->find($orgID);
        
        return $organization;
    }

    /**
     * Generic function is used to get count of any table
     *
     * @param tablename $tname            
     * @param Organization_id $orgId            
     * @return \Doctrine\ORM\mixed int
     */
    public function getCount($tname, $orgId)
    {
        $em = $this->getEntityManager();
        $count = $em->createQueryBuilder()
            ->select('count(t.id)')
            ->from('SynapseCoreBundle:' . $tname, 't')
            ->where('t.organization = :organization')
            ->andWhere('t.deletedAt IS NULL')
            ->setParameters(array(
            UsersConstant::ORGANIZATION => $orgId
        ))
            ->getQuery()
            ->getSingleScalarResult();
        
        return $count;
    }

    /**
     * Get count and lastupdate date of faculties based an organization
     *
     * @param int $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getCountAndLastUpdateOfOrgPersonFaculty($organizationId)
    {
        $sql = 'SELECT
                    COUNT(opf.id) AS faculty_count,
                    (SELECT
                            MAX(opf.modified_at)
                        FROM
                            org_person_faculty opf
                        WHERE
                            (opf.organization_id = :organizationId )) AS modifiedAt
                FROM
                    org_person_faculty opf
                WHERE
                    (opf.organization_id = :organizationId
                        AND opf.deleted_at IS NULL)';

        $parameters = ["organizationId" => $organizationId];
        $parameterTypes = [];
        $results = $this->executeQueryFetch($sql, $parameters, $parameterTypes);

        return $results;
    }

    /**
     * Get count and lastupdate date of students based an organization
     *
     * @param int $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getCountAndLastUpdateOfOrgPersonStudent($organizationId)
    {
        $sql = 'SELECT
                    COUNT(ops.id) AS student_count,
                    (SELECT
                            MAX(ops.modified_at)
                        FROM
                            org_person_student ops
                        WHERE
                            (ops.organization_id = :organizationId )) AS modifiedAt
                FROM
                    org_person_student ops
                WHERE
                    (ops.organization_id = :organizationId
                        AND ops.deleted_at IS NULL)';

        $parameters = ["organizationId" => $organizationId];
        $parameterTypes = [];
        $results = $this->executeQueryFetch($sql, $parameters, $parameterTypes);

        return $results;
    }

    public function usersCount($organization)
    {
        $em = $this->getEntityManager();
        $count = $em->createQueryBuilder()
            ->select('count(person.id)')
            ->from(UsersConstant::ORG_REPO, 'org')
            ->LEFTJoin('SynapseCoreBundle:Person', 'person', \Doctrine\ORM\Query\Expr\Join::WITH, 'org.id = person.organization')
            ->where('person.organization = :organization')
            ->setParameters(array(
            UsersConstant::ORGANIZATION => $organization
        ))
            ->getQuery()
            ->getSingleScalarResult();
        return $count;
    }

    public function listCampuses($parentTier, $tierLevel)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('orglan.organizationName as organizationName, org.campusId as campusId, org.id as orgId, org.subdomain as subdomain')
            ->from(UsersConstant::ORG_REPO, 'org')
            ->LEFTJoin(UsersConstant::ORG_LANG, UsersConstant::ORG_LAN, \Doctrine\ORM\Query\Expr\Join::WITH, UsersConstant::ORGID_ORGLAN_ORG)
            ->where('org.tier = :tierLevel');
        $parameter[] = array(
            'tierLevel' => $tierLevel
        );
        if ($tierLevel != 0) {
            $queryBuilder = $queryBuilder->andWhere('org.parentOrganizationId IN (:parentTier)');
            $parameter[] = array(
                'parentTier' => $parentTier
            );
        }
        $queryBuilder->add('orderBy', 'orglan.organizationName');
        $parameterArray = call_user_func_array('array_merge', $parameter);
        $db = $queryBuilder->setParameters($parameterArray)->getQuery();
        $resultSet = $db->getResult();
        
        return $resultSet;
    }

    public function listPrimaryTiers()
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('orglan.organizationName as organizationName, org.campusId as campusId,org.id as orgId')
            ->from(UsersConstant::ORG_REPO, 'org')
            ->LEFTJoin(UsersConstant::ORG_LANG, UsersConstant::ORG_LAN, \Doctrine\ORM\Query\Expr\Join::WITH, UsersConstant::ORGID_ORGLAN_ORG)
            ->where("org.tier = '1'");
        $queryBuilder->add('orderBy', 'orglan.organizationName');
        $resultSet = $queryBuilder->getQuery()->getResult();
        return $resultSet;
    }

    public function searchHierarchyCampuses($campusType, $filter, $secondaryTierIds = '')
    {
        $tierLevel = ($campusType == 'solo') ? '0' : '3';
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('orglan.organizationName as organizationName, org.campusId as campusId, org.id as orgId, org.parentOrganizationId as parentId')
            ->from(UsersConstant::ORG_REPO, 'org')
            ->LEFTJoin(UsersConstant::ORG_LANG, UsersConstant::ORG_LAN, \Doctrine\ORM\Query\Expr\Join::WITH, UsersConstant::ORGID_ORGLAN_ORG)
            ->where('org.tier = :tierLevel');
        $parameter[] = array(
            'tierLevel' => $tierLevel
        );
        if ($campusType == 'hierarchy') {
            $queryBuilder = $queryBuilder->andWhere('org.parentOrganizationId IN (:parentIds)');
            $parameter[] = array(
                'parentIds' => $secondaryTierIds
            );
        }
        if (! empty($filter)) {
            $queryBuilder = $queryBuilder->andWhere('org.campusId LIKE :filter OR orglan.organizationName LIKE :filter');
            $parameter[] = array(
                'filter' => '%' . $filter . '%'
            );
        }
        $parameterArray = call_user_func_array('array_merge', $parameter);
        $db = $queryBuilder->setParameters($parameterArray)->getQuery();
        $resultSet = $db->getResult();
        return $resultSet;
    }
    /*
     * Get all campus only throughout the system 0=>hierarchy campus 3=>Solo campus
     */
    public function getAllCampusIds()
    {
        $em = $this->getEntityManager();
        $sql = "SELECT group_concat(campus_id order by campus_id asc) as campus_ids FROM organization where tier IN ('0','3') and deleted_at is NULL";
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }

    /**
     * Group concat was not supporing so applied flat sql
     *
     * @param unknown $type            
     * @return unknown
     */
    public function getSourceTypeQuestions($type)
    {
        $em = $this->getEntityManager();
        if ($type == 'isp') {
            $sql = "SELECT o.id as org_id,o.campus_id, group_concat(om.meta_name,'(',om.id,')') as ids 
                FROM synapse.organization as o left join org_metadata as om on (om.organization_id = o.id) where o.deleted_at is NULL and om.deleted_at is NULL group by o.id";
        } else {
            $sql = "SELECT o.id as org_id,o.campus_id, group_concat(oq.id) as ids
                FROM synapse.organization as o left join org_question as oq on (oq.organization_id = o.id) where o.deleted_at is NULL and oq.deleted_at is NULL group by o.id";
        }
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }

    public function listCampusUsers($organization)
    {
        $em = $this->getEntityManager();
        $query = "select p.id, p.firstname, p.lastname, p.title, p.external_id, f.organization_id, ci.primary_email, ci.primary_mobile  
		from person p 
		left join person_contact_info pi on pi.person_id=p.id 
		left join contact_info ci on ci.id = pi.contact_id, org_person_faculty f 
		where 
		(p.id = f.person_id AND f.organization_id in (" . $organization . ") AND f.deleted_at IS NULL)";
        $query .= "group by p.id order by firstname, lastname";
        $resultSet = $em->getConnection()->fetchAll($query);
        return $resultSet;
    }

    public function listHierarchyStudents($organization, $filter)
    {
        $em = $this->getEntityManager();
        $query = "select p.id, p.firstname, p.lastname, p.external_id, s.organization_id, ci.primary_email, ci.primary_mobile  from person p left join person_contact_info pi on pi.person_id=p.id left join contact_info ci on ci.id = pi.contact_id, org_person_student s where 
		(p.id = s.person_id AND s.organization_id in (" . $organization . ") AND s.deleted_at IS NULL)";
        if (! empty($filter)) {
            $query .= " AND (p.firstname LIKE '%" . $filter . "%' OR p.lastname LIKE '%" . $filter . "%' OR p.external_id LIKE '%" . $filter . "%' OR ci.primary_email LIKE '%" . $filter . "%') ";
        }
        $query .= "group by p.id order by firstname, lastname";
        $resultSet = $em->getConnection()->fetchAll($query);
        return $resultSet;
    }

    public function getHierarchyOrder($campusId)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('campusorglan.organizationName as campusName, campus.id as campusId, secondaryorglan.organizationName as secondaryName, secondary.id as secondaryId, primaryorglan.organizationName as primaryName, primaryTier.id as primaryId')
            ->from(UsersConstant::ORG_REPO, 'campus')
            ->INNERJoin(UsersConstant::ORG_REPO, 'secondary', \Doctrine\ORM\Query\Expr\Join::WITH, 'secondary.id = campus.parentOrganizationId')
            ->INNERJoin(UsersConstant::ORG_REPO, 'primaryTier', \Doctrine\ORM\Query\Expr\Join::WITH, 'primaryTier.id = secondary.parentOrganizationId')
            ->JOIN(UsersConstant::ORG_LANG, 'campusorglan', \Doctrine\ORM\Query\Expr\Join::WITH, 'campus.id = campusorglan.organization')
            ->JOIN(UsersConstant::ORG_LANG, 'secondaryorglan', \Doctrine\ORM\Query\Expr\Join::WITH, 'secondary.id = secondaryorglan.organization')
            ->JOIN(UsersConstant::ORG_LANG, 'primaryorglan', \Doctrine\ORM\Query\Expr\Join::WITH, 'primaryTier.id = primaryorglan.organization')
            ->where('campus.id = :campusId')
            ->setParameters(array(
            'campusId' => $campusId
        ));
        $resultSet = $queryBuilder->getQuery()->getResult();
        return $resultSet;
    }

    public function getTierLevelOrder($secondaryTierId)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('secondaryorglan.organizationName as secondaryName, secondary.id as secondaryId, primaryorglan.organizationName as primaryName, primaryTier.id as primaryId')
            ->from(UsersConstant::ORG_REPO, 'secondary')
            ->INNERJoin(UsersConstant::ORG_REPO, 'primaryTier', \Doctrine\ORM\Query\Expr\Join::WITH, 'primaryTier.id = secondary.parentOrganizationId')
            ->JOIN(UsersConstant::ORG_LANG, 'secondaryorglan', \Doctrine\ORM\Query\Expr\Join::WITH, 'secondary.id = secondaryorglan.organization')
            ->JOIN(UsersConstant::ORG_LANG, 'primaryorglan', \Doctrine\ORM\Query\Expr\Join::WITH, 'primaryTier.id = primaryorglan.organization')
            ->where('secondary.id = :secondaryTierId')
            ->setParameters(array(
            'secondaryTierId' => $secondaryTierId
        ));
        $resultSet = $queryBuilder->getQuery()->getResult();
        return $resultSet;
    }

    public function getAllCampus()
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('O.campusId')
            ->from('SynapseCoreBundle:Organization', 'O')
            ->where('O.campusId IS NOT NULL');
        $resultSet = $queryBuilder->getQuery()->getResult();
        return $resultSet;
    }

    public function getPrimaryTierId($campusId)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('org_prim.id as primaryId, org_sec.id as secondaryId, org_campus.id as campusId')
            ->from(UsersConstant::ORG_REPO, 'org_prim')
            ->JOIN(UsersConstant::ORG_REPO, 'org_sec', \Doctrine\ORM\Query\Expr\Join::WITH, 'org_sec.parentOrganizationId = org_prim.id')
            ->JOIN(UsersConstant::ORG_REPO, 'org_campus', \Doctrine\ORM\Query\Expr\Join::WITH, 'org_campus.parentOrganizationId = org_sec.id')
            ->where('org_campus.id = :campusId or org_sec.id = :campusId or org_prim.id = :campusId')
            ->setParameters(array(
            'campusId' => $campusId
        ))
            ->groupBy('primaryId');
        $results = $queryBuilder->getQuery()->getResult();
        return $results;
    }
    
    /*
     * Get all Org Id only throughout the system 0=>hierarchy campus 3=>Solo campus
    */
    public function getAllOrganizationIds()
    {
        $em = $this->getEntityManager();
        $sql = "SELECT id as orgid FROM organization where tier IN ('0','3') and deleted_at is NULL";
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }
}
