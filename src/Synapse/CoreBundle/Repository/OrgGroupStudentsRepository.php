<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\CoreBundle\Entity\OrgGroupStudents;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use Synapse\RestBundle\Exception\ValidationException;

class OrgGroupStudentsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgGroupStudents';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_GROUP_STUDENTS = 'SynapseCoreBundle:OrgGroupStudents';

    const S_ORG_ORGANIZATION = 's.organization = :organization';

    const ORGANIZATION = 'organization';

    const FACULTY = 'faculty';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    private $orgGroupStudentsEntity = 'SynapseCoreBundle:OrgGroupStudents';

    /**
     * @DI\Inject("logger")
     */
    private $logger;

    // const QUERY_CACHE_LIFE = 3600;
    public function remove(OrgGroupStudents $orgGroupStudents)
    {
        $em = $this->getEntityManager();
        $em->remove($orgGroupStudents);
    }

    /**
     * Looks up student IDs for students that belong to one or more of the org_group IDs passed into the method,
     * and are not archived for the passed in org academic year
     *
     * @param array $orgGroupIds
     * @param int $orgAcademicYearId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getNonArchivedStudentsByGroups($orgGroupIds, $orgAcademicYearId)
    {
        $parameters = [
            'orgGroupIds' => $orgGroupIds,
            'orgAcademicYearId' => $orgAcademicYearId
        ];
        $parameterTypes = [
            'orgGroupIds' => Connection::PARAM_INT_ARRAY
        ];

        $sql = "SELECT DISTINCT ogs.person_id
                FROM
                    org_group_students ogs
                        INNER JOIN
                    org_group_tree ogt
                            ON ogt.descendant_group_id = ogs.org_group_id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ogs.person_id
                            AND opsy.organization_id = ogs.organization_id
                WHERE ogt.ancestor_group_id IN (:orgGroupIds)
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND ogs.deleted_at IS NULL
                    AND ogt.deleted_at IS NULL
                    AND opsy.deleted_at IS NULL;";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();
        $results = array_map('current', $results);      // un-nest the array

        return $results;
    }


    /**
     * Returns an array of IDs of all distinct students in the given groups and any subgroups.
     *
     * @param array $orgGroupIds
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStudentsByGroups($orgGroupIds)
    {
        $parameters = ['orgGroupIds' => $orgGroupIds];
        $parameterTypes = ['orgGroupIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT DISTINCT ogs.person_id
                FROM
                    org_group_students ogs
                        INNER JOIN
                    org_group_tree ogt
                            ON ogt.descendant_group_id = ogs.org_group_id
                WHERE ogs.deleted_at IS NULL
                    AND ogt.deleted_at IS NULL
                    AND ogt.ancestor_group_id IN (:orgGroupIds);";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();
        $results = array_map('current', $results);      // un-nest the array
        return $results;
    }


    /**
     * This gets the list of group IDs and group names a given student is a member of for a given organization.
     * @param int $studentId
     * @param int $orgId
     * @return array in the format of ['group_id'=>#, 'group_name'=>string]
     */
    public function getStudentGroupsDetails($studentId, $orgId)
    {

        $sql = "SELECT OGS.org_group_id as group_id, OG.group_name 
                FROM org_group_students as OGS  
                INNER JOIN  org_group as OG ON OG.id  =  OGS.org_group_id
                WHERE OGS.person_id  = :studentId
                AND OGS.organization_id = :orgId
                AND OGS.deleted_at IS NULL 
                AND OG.deleted_at IS NULL
                ORDER BY OG.id DESC";
        try {
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, [
                'studentId' => $studentId,
                'orgId' => $orgId
            ]);
            $result = $stmt->fetchAll();
            return $result;

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }

    public function getGroupStudentsList($orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('g.externalId as Group_Id', 'p.externalId as External_ID');

        $qb->from($this->orgGroupStudentsEntity, 'ogs');

        $qb->join('SynapseCoreBundle:Person', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = ogs.person');
        $qb->join('SynapseCoreBundle:OrgGroup', 'g', \Doctrine\ORM\Query\Expr\Join::WITH, 'g.id = ogs.orgGroup');

        $qb->where('ogs.organization = :orgId');
        $qb->setParameters(array(
            'orgId' => $orgId
        ));
        $qb->orderBy('g.id', 'asc');
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }

    /**
     * Finds out the number of students assigned to different groups other than the ALLSTUDENTS Group for the organization
     * @param $organization
     * @return int
     */
    public function getGroupStudentCountOrg($organization)
    {

        $sql = "SELECT COUNT(DISTINCT(person_id)) AS student_id_count FROM 
                org_group_students AS ogs
                INNER JOIN org_group AS og ON ogs.org_group_id =  og.id
                WHERE ogs.organization_id =  :orgId
                AND og.external_id <> :allStudents
                AND ogs.deleted_at IS NULL
                AND og.deleted_at IS NULL";
        try {
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, [
                'orgId' => $organization,
                'allStudents' => "ALLSTUDENTS"
            ]);
            $result = $stmt->fetchColumn();
            return $result;

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }

    /**
     * Gets external_ids for all students within the passed
     * in group and organization and does not include students of subgroups
     *
     * @param int $organizationId
     * @param int $groupId
     * @param bool $includePersonMetadata
     * @return array
     */
    public function listExternalIdsForStudentsInGroup($organizationId, $groupId, $includePersonMetadata = false)
    {
        $parameters = [
            'groupId' => $groupId,
            'orgId' => $organizationId
        ];

        if ($includePersonMetadata) {
            $selectFields = "
                p.external_id,
                p.firstname AS first_name,
                p.lastname AS last_name,
                p.username AS primary_email ";
        } else {

            $selectFields = "p.external_id ";
        }

        $sql = "SELECT DISTINCT
                    $selectFields
                FROM
                    org_group_students ogs
                        INNER JOIN
                    person p ON ogs.person_id = p.id
                WHERE
                    ogs.organization_id = :orgId
                    AND ogs.org_group_id = :groupId
                    AND ogs.deleted_at IS NULL
                    AND p.deleted_at IS NULL;";

        $result = $this->executeQueryFetchAll($sql, $parameters);
        return $result;
    }

    public function deleteBulkStudentEnrolledGroups($studentId, $orgId)
    {
        $dateTime = new \DateTime('now');
        $em = $this->getEntityManager();

        $query = $em->createQuery('UPDATE  ' . $this->orgGroupStudentsEntity . ' as e SET e.deletedAt = :datetime WHERE e.person = :person AND e.organization = :org');
        $query->setParameters([
            'datetime' => $dateTime,
            'person' => $studentId,
            'org' => $orgId
        ]);
        $query->execute();
    }

    /**
     * It will return the allowed students for feature.
     * @param $orgId
     * @param $facultyId
     * @param $featureId
     * @return array
     */
    public function getAllStudentsForFeature($orgId, $facultyId, $featureId)
    {
        $parameters = [
            'orgId' => $orgId,
            'facultyId' => $facultyId,
            'featureId' => $featureId
        ];

        $accessCheck = 'AND opfeat.public_create = 1';
        if ($featureId == 1) {
            $accessCheck = 'AND (opfeat.public_create = 1 OR opfeat.reason_referral_public_create = 1)';
        }

        $sql = "SELECT DISTINCT
                    merged.student_id
                FROM
                    (SELECT
                        S.person_id AS student_id,
                            F.org_permissionset_id AS permissionset_id,S.org_group_id as groupcourse
                    FROM
                        org_group_students AS S
                    INNER JOIN org_group_tree ogt ON S.org_group_id = ogt.descendant_group_id
                        AND ogt.deleted_at IS NULL
                    INNER JOIN org_group_faculty AS F ON F.org_group_id = ogt.ancestor_group_id
                        and F.deleted_at is null
                    WHERE
                        S.deleted_at is null
                            AND F.person_id = :facultyId
                    UNION ALL
                    SELECT
                        S.person_id AS student_id,
                            F.org_permissionset_id AS permissionset_id,S.org_courses_id as groupcourse
                    FROM
                        org_course_student AS S
                    INNER JOIN org_courses AS C ON C.id = S.org_courses_id
                        AND C.deleted_at is null
                    INNER JOIN org_course_faculty AS F ON F.org_courses_id = S.org_courses_id
                        AND F.deleted_at is null
                    INNER JOIN org_academic_terms AS OAT ON OAT.id = C.org_academic_terms_id
                        AND OAT.end_date >= now() AND OAT.start_date <= now()
                        AND OAT.deleted_at is null
                    WHERE
                        S.deleted_at is null
                            AND F.person_id = :facultyId) AS merged
                        INNER JOIN
                    person AS P ON P.id = merged.student_id
                        AND P.deleted_at IS NULL
                        AND P.organization_id = :orgId
                        INNER JOIN
                    org_permissionset OPS ON merged.permissionset_id = OPS.id
                        JOIN    org_permissionset_features opfeat ON (opfeat.org_permissionset_id = OPS.id
                        $accessCheck
                        AND feature_id = :featureId)
                        AND OPS.accesslevel_ind_agg = 1 group by merged.student_id";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $studentArr = $stmt->fetchAll();
        return $studentArr;
    }

    /**
     * @param int $studentId
     * @param int $groupId
     * @param int $organizationId
     * @throws \Doctrine\ORM\ORMException
     * @throws \RuntimeException
     */
    public function addStudentGroupAssoc($studentId, $groupId)
    {
        $em = $this->getEntityManager();

        /** @var OrgGroupStudent[] $groups */
        $groups = $this->findBy([
            'person' => $studentId,
            'orgGroup' => $groupId,
        ]);

        if (!empty($groups)) {
            throw new \RuntimeException("Record exists: " . json_encode($groups));
        }

        $q = $em->createQuery('select IDENTITY(ops.organization) as organizationId from SynapseCoreBundle:OrgPersonStudent ops WHERE ops.person=' . $studentId);
        $partial = $q->getArrayResult();
        $organizationId = null;
        if (!empty($partial)) {
            $organizationId = $partial[0]['organizationId'];
        }

        $orgGroupStudent = new OrgGroupStudents();
        $orgGroupStudent->setPerson($em->getReference(Person::class, $studentId));
        $orgGroupStudent->setOrgGroup($em->getReference(OrgGroup::class, $groupId));
        $orgGroupStudent->setOrganization($em->getReference(Organization::class, $organizationId));


        $em->persist($orgGroupStudent);
        $em->flush($orgGroupStudent);
    }

    /**
     * @param int $studentId
     * @param int $groupId
     */
    public function removeStudentGroupAssoc($studentId, $groupId)
    {
        /** @var OrgGroupStudent[] $groups */
        $groups = $this->findBy([
            'person' => $studentId,
            'orgGroup' => $groupId,
        ]);

        foreach ($groups as $group) {
            $this->remove($group);
        }

        $this->flush();
    }


    /**
     * Gets the Count of non-archived student count for a group for an academic year
     *
     * @param int $organizationId
     * @param int $groupId
     * @return bool|string
     * @throws SynapseDatabaseException
     */
    public function countStudentsForGroup($organizationId, $groupId)
    {
        $parameters = [
            'organizationId' => (int)$organizationId,
            'groupId' => (int)$groupId
        ];

        $sql = "
            SELECT 
                COUNT(DISTINCT ogs.person_id)
            FROM
                org_group_students ogs
            WHERE
                ogs.organization_id = :organizationId
                AND ogs.org_group_id = :groupId
                AND ogs.deleted_at IS NULL
               ";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $resultSet = $stmt->fetchColumn();

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $resultSet;
    }
}
