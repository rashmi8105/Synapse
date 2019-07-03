<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\ORM\Query\ResultSetMapping;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Util\Constants\GroupConstant;

class OrgGroupRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgGroup';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    private $orgGroupEntity = 'SynapseCoreBundle:OrgGroup';

    const FIELD_ORGN = 'organization';

    const S_ORG_ORGANIZATION = 's.organization = :organization';

    const COUNT_ID = 'count(s.id)';


    /**
     * Finds an entity by its primary key / identifier.
     * Override added to inform PhpStorm about the return type.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return OrgGroup|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param \Exception $exception
     * @return OrgGroup|null
     */
    public function findOneBy(array $criteria, array $orderBy = null , $exception =  null)
    {
        $orgGroupEntity =  parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($orgGroupEntity, $exception);
    }

    /**
     * Create Group
     *
     * @param OrgGroup $orgGroup
     * @return OrgGroup
     */
    public function createGroup(OrgGroup $orgGroup)
    {
        $em = $this->getEntityManager();
        $em->persist($orgGroup);

        return $orgGroup;
    }

    public function remove(OrgGroup $orgGroup)
    {
        $em = $this->getEntityManager();
        $em->remove($orgGroup);
    }


    public function getAllSubgroupPersonCount($ids)
    {
        $em = $this->getEntityManager();
        $rsm = new ResultSetMapping();
        $sql = "select a.*, count(b.person_id) as personcount
                from org_group a left outer join org_group_faculty b
                on (a.id = b.org_group_id)
                where b.deleted_at is null and a.id in (?)
                group by a.id
                order by a.id;";

        $query = $em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $ids);
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult(GroupConstant::GROUP_NAME, GroupConstant::GROUP_NAME);
        $rsm->addScalarResult(GroupConstant::EXTERNAL_ID, GroupConstant::EXTERNAL_ID);
        $rsm->addScalarResult(GroupConstant::ORGANIZATION_ID, GroupConstant::ORGANIZATION_ID);
        $rsm->addScalarResult(GroupConstant::PARENT_GROUP_ID, GroupConstant::PARENT_GROUP_ID);
        $rsm->addScalarResult(GroupConstant::PERSON_COUNT, GroupConstant::PERSON_COUNT);
        return $query->getArrayResult();
    }
    
    public function getGroupByExternalId($organization, $externalId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('g')
            ->from($this->orgGroupEntity, 'g')
            ->where('g.externalId = :externalId AND g.organization = :organization')
            ->setParameters([
                'externalId' => $externalId,
                self::FIELD_ORGN => $organization
            ]);
        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

    public function getGroupDetails($organization, $groupid)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('partial g.{id,groupName}')
            ->from($this->orgGroupEntity, 'g')
            ->where('g.id = :id AND g.organization = :organization')
            ->setParameters([
                'id' => $groupid,
                self::FIELD_ORGN => $organization
            ]);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

    public function getGroupByOrganization($orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('og.id as group_id, og.groupName');
        $qb->from($this->orgGroupEntity, 'og');
        $qb->where('og.organization = :orgId');
        $qb->setParameters(array(
            'orgId' => $orgId
        ));
        $qb->orderBy('og.groupName', 'asc');
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }

    public function getPersonReferance($groupId)
    {
        $em = $this->getEntityManager();
        return $em->getReference($this->orgGroupEntity, $groupId);
    }

    /**
     * Get groups based on organization with the paths to those groups
     *
     * @param int $orgId
     * @return array
     */
    public function getGroupsWithPathsForOrganization($orgId)
    {
        $sql = "SELECT
                    g.id AS gid,
                    g.group_name AS Group_Name,
                    g.external_id AS Group_ID,
                    p.external_id AS Parent_Group_ID,
                    tree.FullPathNames,
                    tree.FullPathGroupIDs
                FROM
                    org_group g
                        LEFT JOIN
                    org_group p
                            ON p.id = g.parent_group_id
                        LEFT JOIN
                    (SELECT
                        descendant_group_id,
                        GROUP_CONCAT(og.group_name
                            ORDER BY path_length DESC
                            SEPARATOR ' | ') AS FullPathNames,
                        GROUP_CONCAT(og.external_id
                            ORDER BY path_length DESC
                            SEPARATOR ' | ') AS FullPathGroupIDs
                    FROM
                        org_group_tree ogt
                            INNER JOIN
                        org_group og
                                ON ogt.ancestor_group_id = og.id
                    WHERE
                        og.organization_id = :orgId
                        AND og.deleted_at IS NULL
                        AND ogt.deleted_at IS NULL
                    GROUP BY descendant_group_id) AS tree
                            ON g.id = tree.descendant_group_id
                WHERE
                    g.organization_id = :orgId
                    AND g.deleted_at IS NULL
                    AND p.deleted_at IS NULL
                ORDER BY tree.FullPathGroupIDs ASC;";
        try {
            $em = $this->getEntityManager();
            $parameters = [
                ':orgId' => (int)$orgId
            ];
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $resultSet = $stmt->fetchAll();
        return $resultSet;
    }

    public function getSubGroupCounts($orgId)
    {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
            ->select(self::COUNT_ID)
            ->from($this->orgGroupEntity, 's')
            ->where('s.parentGroup IS NOT NULL', self::S_ORG_ORGANIZATION)
            ->setParameters(array(
                self::FIELD_ORGN => $orgId
            ))
            ->getQuery();

        return $query->getSingleScalarResult();
    }


    /**
     * Get group Total Count based on organization.
     *
     * @param int $organizationId
     * @param string|null $searchText
     * @return array
     */
    function fetchOrgGroupTotalCount($organizationId, $searchText = null)
    {

        $parameters = [
            'organizationId' => (int)$organizationId
        ];
        $parameterTypes = [];
        if (!is_null($searchText)) {
            $searchText =  $this->escapeMysqlWildCards($searchText);
            $filterSearchText = " 
                        AND (og.group_name like :searchText
                        OR og.external_id like :searchText )";

            $parameters['searchText'] = "%$searchText%";
            $parameterTypes['searchText'] = 'string';
        } else {
            $filterSearchText = '';
        }

        $sql = "
            SELECT
                COUNT(id) AS total_groups
            FROM
                org_group og
            WHERE
                og.organization_id = :organizationId
                AND og.deleted_at IS NULL
                $filterSearchText";

        $records = $this->executeQueryFetch($sql, $parameters, $parameterTypes);
        return $records;
    }


    /**
     * Get group info based on organization id and group id
     *
     * @param int $orgId
     * @param int $groupId
     * @throws SynapseDatabaseException
     * @return array
     */
    public function fetchGroupInfo($orgId, $groupId)
    {
        try {
            $sql = "SELECT 
                        og.id AS group_id,
                        og.external_id AS external_id,
                        og.group_name,
                        og.created_at,
                        og.modified_at,
                        og.parent_group_id AS parent_id,
                        og1.group_name AS parent_name
                    FROM
                        org_group og
                            LEFT JOIN
                        org_group as og1 ON og.parent_group_id = og1.id
                    WHERE
                        og.organization_id = :orgId AND og.id = :groupId
                        AND og1.deleted_at IS NULL
                        AND og.deleted_at IS NULL";

            $em = $this->getEntityManager();
            $parameters = [
                'orgId' => (int)$orgId,
                'groupId' => (int)$groupId
            ];
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $resultSet = $stmt->fetchAll();

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $resultSet[0];
    }


    /**
     * Get groups based on organization with rootOnly optional parameter
     *
     * @param int $organizationId
     * @param boolean $rootOnly
     * @return array
     * @throws SynapseDatabaseException
     */
    public function fetchListOfGroups($organizationId,$rootOnly = false)
    {

        $parameters = [
            'organizationId' => (int)$organizationId,
        ];

        if ($rootOnly) {
            $rootOnlySql = "AND og.parent_group_id IS NULL";
        } else {
            $rootOnlySql = "";
        }

        try {
            $sql = "
                    SELECT
                        og.id AS group_id,
                        og.external_id AS external_id,
                        og.group_name,
                        og.created_at,
                        og.modified_at,
                        og.parent_group_id AS parent_id,
                        (SELECT
                            COUNT(ogs.id)
                        FROM
                            org_group_students ogs
                        WHERE
                            ogs.organization_id = :organizationId
                            AND ogs.deleted_at IS NULL
                            AND ogs.org_group_id = og.id) AS student_count,
                        (SELECT
                            COUNT(id)
                        FROM
                            org_group_faculty ogf
                        WHERE
                            organization_id = :organizationId
                            AND ogf.org_group_id = og.id
                            AND ogf.deleted_at IS NULL) AS staff_count
                    FROM
                        org_group og
                    WHERE
                        og.organization_id = :organizationId
                        AND og.deleted_at IS NULL
                        $rootOnlySql
                    ORDER BY og.id ASC
            ";

            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $resultSet = $stmt->fetchAll();
        return $resultSet;
    }



    /**
     * This will convert External Id or Group Name
     * to the system id. This will default to external id
     * if there is a conflict between the two.
     * Returns an associative array with key 'error' if there's not exactly one group with the given external id / group name.
     *
     * @param string|int $externalIdOrGroupName
     * @param int $orgId
     * @param string $isExternalIdOrGroupName
     * @return int|array
     */
    public function convertExternalIdOrGroupNameToId($externalIdOrGroupName, $orgId, $isExternalIdOrGroupName = 'both'){
        try {
            $em = $this->getEntityManager();

            $externalIdOrGroupNameSQL = '(group_name LIKE :externalId OR external_id LIKE :externalId)';

            if(strtolower($isExternalIdOrGroupName) == 'groupname'){
                $externalIdOrGroupNameSQL = 'group_name LIKE :externalId';
            } elseif (strtolower($isExternalIdOrGroupName) == 'externalid') {
                $externalIdOrGroupNameSQL = 'external_id LIKE :externalId';
            }

            $sql = "
			SELECT
                  IF(external_id LIKE :externalId, 1, 0) AS 'is_external_id',
                  id
              FROM
                  org_group
              WHERE
                  organization_id = :orgId
                      AND deleted_at IS NULL
                      AND $externalIdOrGroupNameSQL
              ORDER BY is_external_id DESC;
            ";
            $stmt = $em->getConnection()->executeQuery($sql, ['orgId'=>$orgId, 'externalId'=>$externalIdOrGroupName],[]);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());

        }
        // need to check to see if there is only one row
        // if there are more than one row, see if there is
        // a row with 1 in it vs 0
        $results = $stmt->fetchAll();

        if(count($results) == 1)   {
            // return the one row that the id/groupname applies to
            return $results[0]['id'];
        } else {
            $returnResultsExternalId = array();
            $returnResultsGroupName = array();

            // divide each return value as the externalId matches
            foreach($results as $resultRow){
                if($resultRow['is_external_id'] == 1){
                    $returnResultsExternalId[] = $resultRow;
                }  else {
                    $returnResultsGroupName[] = $resultRow;
                }
            }
            if(count($returnResultsExternalId) > 1){
                // throw an error here this external id has tracks to multiple groups
                return array('error' => 'There are two external ids with the '.$externalIdOrGroupName . ' external id');
            } elseif(count($returnResultsExternalId) == 1) {
                return $returnResultsExternalId['id'];
            } elseif  (count($returnResultsGroupName) > 1) {
                // throw error here this group name tracks to multiple groups
                return array('error' => 'There are two group names with the '.$externalIdOrGroupName . ' group name');
            } elseif (count($returnResultsGroupName) == 1) {
                return $returnResultsGroupName['id'];
            } else {
                // throw error here No results returned
                if(strtolower($isExternalIdOrGroupName) == 'groupname'){
                    return array('error' => 'There are no groups with the group name ' . $externalIdOrGroupName);
                } elseif (strtolower($isExternalIdOrGroupName) == 'externalid') {
                    return array('error' => 'There are no groups with the external id ' . $externalIdOrGroupName);
                } else {
                    return array('error' => 'There are no groups with the external id or group name ' . $externalIdOrGroupName);
                }
            }
        }
    }

    /**
     * This will convert an id to either external id
     * or group name. It will default to external id
     * and only will show group name when external id
     * is null or an empty string.
     *
     * @param int $orgGroupId
     * @param int $orgId
     * @return Array
     */
    public function convertIdToExternalIdOrGroupName($orgGroupId, $orgId){
        try {
            $em = $this->getEntityManager();
            $sql = "
			SELECT
                  IF(external_id IS NOT NULL AND external_id != '', external_id, group_name) AS 'external_id'
            FROM
                  org_group
            WHERE
                  organization_id = :orgId
                      AND deleted_at IS NULL
                      AND id = :orgGroupId;
            ";
            $stmt = $em->getConnection()->executeQuery($sql, ['orgId'=>$orgId, 'orgGroupId'=>$orgGroupId],[]);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();

        return $results[0]['external_id'];


    }


    /**
     * This will return the external_ids for all
     * top level groups. You can choose whether
     * or not you want to include ALLSTUDENTS
     *
     * @param int $organizationId
     * @param boolean $includeALLSTUDENTS
     * @return array
     */
    public function getTopLevelGroups($organizationId, $includeALLSTUDENTS = true){
        try{
            $em = $this->getEntityManager();

            if($includeALLSTUDENTS){
                $allStudents = "";
            } else {
                $allStudents = "AND external_id != 'ALLSTUDENTS'";
            }

            $sql = "SELECT
                        IF(external_id IS NULL OR external_id = '',
                        group_name,
                        external_id) AS group_name
                FROM
                    org_group
                WHERE
                    organization_id = :organizationId
                        AND deleted_at IS NULL
                        AND parent_group_id IS NULL
                        $allStudents
                  ORDER BY group_name;";
            $stmt = $em->getConnection()->executeQuery($sql, ['organizationId'=>$organizationId],[]);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $results = $stmt->fetchAll();
        return $results;
    }

    /**
     * This will return all immediate external_id/group_name
     * of the ALLSTUDENTS group for a given organization
     *
     * @param $organizationId
     * @return array
     */
    public function getImmediateChildrenOfAllStudentsGroup($organizationId)
    {
        try {
            $em = $this->getEntityManager();

            $sql = "SELECT
                        IF(og.external_id IS NULL OR og.external_id = '',
                        og.group_name,
                        og.external_id) AS group_name
                FROM
                    org_group og
                        INNER JOIN
                    org_group parent_group ON og.parent_group_id = parent_group.id
                                              AND og.organization_id = parent_group.organization_id
                WHERE
                    og.organization_id = :organizationId
                        AND og.deleted_at IS NULL
                        AND parent_group.deleted_at IS NULL
                        AND parent_group.external_id = 'ALLSTUDENTS'
                  ORDER BY group_name;";
            $stmt = $em->getConnection()->executeQuery($sql, ['organizationId' => $organizationId], []);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $results = $stmt->fetchAll();
        return $results;
    }

    /**
     * Gets the last updated date for the org group, org group faculty
     * and org group student tables.
     *
     * @param int|string $organizationId
     * @return array
     */
    function fetchGroupSummaryLastModifiedDate($organizationId)
    {
        $parameters = [
            'organizationId' => (int)$organizationId
        ];

        $sql = "SELECT
                    GREATEST(COALESCE(MAX(deleted_at), 0),
                            COALESCE(MAX(modified_at), 0)) AS last_updated
                FROM
                    (
                    SELECT
                        MAX(deleted_at) deleted_at, MAX(modified_at) modified_at
                    FROM
                        org_group
                    WHERE
                        organization_id = :organizationId
                UNION ALL
                    SELECT
                        MAX(deleted_at) deleted_at, MAX(modified_at) modified_at
                    FROM
                        org_group_students
                    WHERE
                        organization_id = :organizationId
                UNION ALL
                    SELECT
                        MAX(deleted_at) deleted_at, MAX(modified_at) modified_at
                    FROM
                        org_group_faculty
                    WHERE
                        organization_id = :organizationId) as lastUpdatedGroupQuery;
        ";

        try {
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, $parameters);
            $result = $stmt->fetchAll();
            $result = (count($result) > 0 ? $result[0] : null);

        } catch (\Exception $e) {

            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());

        }

        return $result;
    }


    /**
     * Gets list of groups based on search text
     *
     * @param int $organizationId
     * @param string|null $searchText
     * @param int|null $offset
     * @param int|null $recordCount
     * @return array
     */
    public function getGroupsMetaData($organizationId, $searchText = null, $offset = null, $recordCount = null)
    {

        $parameters['organizationId'] = $organizationId;
        $parameterTypes = [];



        if (!is_null($searchText)) {
            $searchText =  $this->escapeMysqlWildCards($searchText);
            $filterSearchText = " 
                        AND (og.group_name like :searchText
                        OR og.external_id like :searchText)";

            $parameters['searchText'] = "%$searchText%";
            $parameterTypes['searchText'] = 'string';
        } else {
            $filterSearchText = '';
        }

        if (!is_null($offset) && !is_null($recordCount)) {

            $paginationText = "LIMIT :limit OFFSET :offset ";
            $parameters['offset'] = $offset;
            $parameters['limit'] = $recordCount;

            $parameterTypes ['offset'] = \PDO::PARAM_INT;
            $parameterTypes ['limit'] = \PDO::PARAM_INT;
        } else {
            $paginationText = "";
        }

        $sql = "SELECT
                    og.id AS mapworks_internal_id,
                    og.group_name,
                    og.external_id
                FROM
                    org_group og
                WHERE
                    og.organization_id = :organizationId
                    AND og.deleted_at IS NULL
                    $filterSearchText
                    $paginationText";

        $records = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $records;
    }

}