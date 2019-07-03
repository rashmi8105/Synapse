<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\CoreBundle\Entity\OrgGroupFaculty;
use Synapse\CoreBundle\Entity\OrgPermissionset;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Exception\SynapseException;

/**
 * Class OrgGroupFacultyRepository is responsible for user RBAC permissions.
 *
 * @package Synapse\CoreBundle\Repository
 */
class OrgGroupFacultyRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgGroupFaculty';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    private $orgGroupEntity = 'SynapseCoreBundle:OrgGroupFaculty';

    // const QUERY_CACHE_LIFE = 3600;
    const ORGN = 'organization';


    /**
     * Finds an entity by its primary key / identifier.
     * Override added to inform PhpStorm about the return type.
     *
     * @param mixed $id The identifier.
     * @param SynapseException $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return OrgGroupFaculty|null
     */
    public function find($id, $exception =  null , $lockMode = null, $lockVersion = null)
    {
        $orgGroupFacultyEntity =  parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($orgGroupFacultyEntity, $exception);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param SynapseException $exception
     * @param array|null $orderBy
     * @param \Exception $exception
     *
     * @return OrgGroupFaculty|null
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null)
    {
        $orgGroupFacultyEntity =  parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($orgGroupFacultyEntity, $exception);
    }

    public function createGroupFaculty(OrgGroupFaculty $orgGroupFaculty)
    {
        $em = $this->getEntityManager();
        $em->persist($orgGroupFaculty);
        return $orgGroupFaculty;
    }

    public function remove(OrgGroupFaculty $orgGroupFaculty)
    {
        $em = $this->getEntityManager();
        $em->remove($orgGroupFaculty);
    }

     /**
     * Gets Faculty information for a group.
     *
     * @param int $organizationId
     * @param int $groupId
     * @param bool $isInternal
     * @return array
     */
    public function getGroupStaffList($organizationId, $groupId, $isInternal = true)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'groupId' => $groupId
        ];

        if ($isInternal) {
            $selectFields = "ogf.id AS group_staff_id,
                    p.id AS staff_id,
                    p.firstname AS staff_firstname,
                    p.lastname AS staff_lastname,
                    ogf.is_invisible AS staff_is_invisible,
                    op.id AS staff_permissionset_id,
                    op.permissionset_name AS staff_permissionset_name";
        } else {
            $selectFields = "p.id AS mapworks_internal_id,
                    p.external_id AS faculty_external_id,
                    p.firstname,
                    p.lastname,
                    p.username AS primary_email,
                    op.permissionset_name,
                    ogf.is_invisible";
        }

        $sql = "SELECT
                    $selectFields
                FROM
                    org_group_faculty ogf
                        INNER JOIN
                    person p ON ogf.person_id = p.id
                        LEFT JOIN
                    org_permissionset op ON ogf.org_permissionset_id = op.id
                        AND ogf.organization_id = op.organization_id
                        AND op.deleted_at IS NULL
                WHERE
                    ogf.organization_id = :organizationId
                        AND ogf.org_group_id = :groupId
                        AND ogf.deleted_at IS NULL
                        AND p.deleted_at IS NULL";

        $results = $this->executeQueryFetchAll($sql, $parameters);

        $response = [];

        if (count($results) > 0) {
            foreach ($results as $result) {
                if ($isInternal) {
                    if (is_null($result['staff_permissionset_id'])) {
                        $result['staff_permissionset_id'] = 0;
                        $result['staff_permissionset_name'] = "";
                    }
                }
                $response[] = $result;
            }
        }
        return $response;
    }

    public function getOrganizationGroupByUser($organization, $userId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(f.orgGroup)');
        $qb->from($this->orgGroupEntity, 'f');
        $qb->where('f.organization = :organization', 'f.person = :user');
        $qb->setParameters(array(
            self::ORGN => $organization,
            'user' => $userId
        ));
        $query = $qb->getQuery();
        return $query->getArrayResult();
    }

    /**
     * Fetch all of the user's permission sets.
     *
     * This method fetches all of the permission sets assigned to all of the
     * groups to which any individual user belongs. It organizes them into
     * permission sets, so duplicate permissions with potentially conflicting
     * access rights, which usually requires additional processing.
     *
     * @param int $userId
     * @return array
     */
    public function getGroupByUserPermissionSet($userId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(f.orgPermissionset) as permission_set', 'IDENTITY(f.orgGroup) as org_group', 'IDENTITY(f.organization) as organization', 'g.groupName');
        $qb->from($this->orgGroupEntity, 'f');
        $qb->leftJoin('SynapseCoreBundle:OrgGroup', 'g', \Doctrine\ORM\Query\Expr\Join::WITH, 'g.id = f.orgGroup');
        $qb->where('f.person = :user');
        $qb->setParameters(array(
            'user' => $userId
        ));
        $query = $qb->getQuery();
        $data = $query->getArrayResult();

        // Filter out groups without permissions.
        foreach ($data as $key => $value) {
            if (empty($value['permission_set'])) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    /**
     * Fetch permission sets for a faculty and student combination 
     * 
     * @param int $facultyId
     * @param int $studentId
     * @return array
     */
    public function getPermissionsByFacultyStudent($facultyId, $studentId)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'studentId' => $studentId
        ];

        $sql = "SELECT
                    DISTINCT permissionset_id as org_permissionset_id
                FROM
                    org_faculty_student_permission_map ofspm
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.student_id = :studentId";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $resultSet = $stmt->fetchAll();
        return $resultSet;
    }

    /**
     * Fetch all permission sets for a faculty
     *
     * @param int $facultyId
     * @param int $organizationId
     * @return array
     */
    public function getAllPermissionSetsForFaculty($organizationId, $facultyId)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'facultyId' => $facultyId
        ];

        $sql = "SELECT
                    DISTINCT permissionset_id as org_permissionset_id
                FROM
                    org_faculty_student_permission_map ofspm
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $resultSet = $stmt->fetchAll();
        return array_column($resultSet, 'org_permissionset_id');
    }

    /**
     * Return the list of faculty belonging to different groups with the group hierarchy.
     * @param $orgId
     * @return array
     */
    public function getGroupFacultyList($orgId)
    {
        $sql = "SELECT
                p.external_id AS ExternalId,
                p.firstname AS Firstname,
                p.lastname AS Lastname,
                p.username AS PrimaryEmail,
                tree.FullPathNames,
                tree.FullPathGroupIDs,
                og.group_name AS GroupName,
                og.external_id AS GroupId,
                op.permissionset_name AS PermissionSet,
                ogf.is_invisible AS Invisible
            FROM
                org_group_faculty AS ogf
                    INNER JOIN
                person AS p ON ogf.person_id = p.id
                    INNER JOIN
                org_group AS og ON ogf.org_group_id = og.id
                    LEFT JOIN
                org_permissionset AS op ON ogf.org_permissionset_id = op.id AND op.deleted_at IS NULL
                    LEFT JOIN
                (
                    SELECT
                        descendant_group_id,
                            GROUP_CONCAT(og.group_name
                                ORDER BY path_length DESC
                                SEPARATOR ' | ') AS FullPathNames,
                            GROUP_CONCAT(og.external_id
                                ORDER BY path_length DESC
                                SEPARATOR ' | ') AS FullPathGroupIDs
                    FROM
                        org_group_tree AS ogt
                    INNER JOIN org_group AS og ON ancestor_group_id = og.id
                    WHERE
                        descendant_group_id != ancestor_group_id
                        AND og.organization_id = :orgId
                        AND og.deleted_at IS NULL
                        AND ogt.deleted_at IS NULL
                    GROUP BY descendant_group_id
                ) AS tree ON og.id = tree.descendant_group_id
            WHERE
                ogf.organization_id = :orgId
                AND og.deleted_at IS NULL
                AND ogf.deleted_at IS NULL
                AND p.deleted_at IS NULL
            ORDER BY og.external_id ASC";

        try {
            $em = $this->getEntityManager();
            $parameters = [
                'orgId' => $orgId
            ];
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $resultSet = $stmt->fetchAll();
        return $resultSet;
    }

    public function getGroupStaffCountOrg($oragnization)
    {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
            ->select('(s.person)')
            ->from($this->orgGroupEntity, 's')
            ->where('s.organization = :organization')
            ->distinct()
            ->setParameters(array(
            self::ORGN => $oragnization
        ))
            ->getQuery();

        return count($query->getArrayResult());
    }


    public function getGroupsByPerson($facultyId)
    {
        $em = $this->getEntityManager();

        $sql = <<<SQL
SELECT ogf.org_group_id group_id, og.group_name, ogf.org_permissionset_id as staff_permissionset_id, op.permissionset_name,ogf.is_invisible as staff_is_invisible
FROM org_group_faculty ogf
JOIN org_group og ON og.id=ogf.org_group_id
LEFT JOIN org_permissionset op ON op.id=ogf.org_permissionset_id
WHERE ogf.person_id=? AND ogf.deleted_at IS NULL
SQL;

        $db = $em->getConnection();
        $q = $db->prepare($sql);
        $q->execute([$facultyId]);
        $groupsList = $q->fetchAll();

        return $groupsList;
    }

    /**
     * @param $facultyId
     * @param $groupId
     * @param $permissionsetId
     * @param $isInvisible
     * @throws \Doctrine\ORM\ORMException
     */
    public function addFacultyGroupAssoc($facultyId, $groupId, $permissionsetId = false, $isInvisible = false)
    {
        $em = $this->getEntityManager();

        /** @var OrgGroupFaculty[] $groups */
        $groups = $this->findBy([
            'person' => $facultyId,
            'orgGroup' => $groupId,
        ]);

        if (!empty($groups)) {
            throw new \RuntimeException("Record exists: " . json_encode($groups));
        }

        $q = $em->createQuery('select IDENTITY(opf.organization) as organizationId from SynapseCoreBundle:OrgPersonFaculty opf WHERE opf.person=' . $facultyId);
        $partial = $q->getArrayResult();
        $organizationId = null;
        if (!empty($partial)) {
            $organizationId = $partial[0]['organizationId'];
        }

        $orgGroupFaculty = new OrgGroupFaculty();
        $orgGroupFaculty->setPerson($em->getReference(Person::class, $facultyId));
        $orgGroupFaculty->setOrgGroup($em->getReference(OrgGroup::class, $groupId));
        $orgGroupFaculty->setOrganization($em->getReference(Organization::class, $organizationId));
        if ($permissionsetId) {
            $orgGroupFaculty->setOrgPermissionset($em->getReference(OrgPermissionset::class, $permissionsetId));
        }
        $orgGroupFaculty->setIsInvisible($isInvisible);

        $em->persist($orgGroupFaculty);
        $em->flush();
    }

    /**
     * @param int $facultyId
     * @param int $groupId
     */
    public function removeFacultyGroupAssoc($facultyId, $groupId)
    {
        /** @var OrgGroupFaculty[] $groups */
        $groups = $this->findBy([
            'person' => $facultyId,
            'orgGroup' => $groupId,
        ]);

        foreach ($groups as $group) {
            $this->remove($group);
        }

        $this->flush();
    }

    public function deleteBulkFacultyEnrolledGroups($facultyId, $orgId)
    {
        $dateTime = new \DateTime('now');
        $em = $this->getEntityManager();

        $query = $em->createQuery('UPDATE  ' . $this->orgGroupEntity . ' as e SET e.deletedAt = :datetime WHERE e.person = :person AND e.organization = :org AND e.deletedAt IS NULL');
        $query->setParameters([
            'datetime' => $dateTime,
            'person' => $facultyId,
            'org' => $orgId
            ]);
        $query->execute();
    }
    
    
    public function getGroupFaculty($orgId, $groupIds = null) 
    {
        
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(f.person) as faculty_id');
        $qb->from($this->orgGroupEntity, 'f');
        $qb->where('f.organization = :organization');
        if(!is_null($groupIds)){
            
            $qb->andwhere( 'f.orgGroup IN ( :group )');
            $qb->setParameters(array(
                self::ORGN => $orgId,
                'group' => $groupIds
            ));
            
        }else{
            
            $qb->setParameters(array(
                self::ORGN => $orgId,
            ));
        }
        $query = $qb->getQuery();
        $results = $query->getArrayResult();
        
        return $results;
    }
    
}
