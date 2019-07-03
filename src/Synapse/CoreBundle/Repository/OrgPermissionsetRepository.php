<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\OrgPermissionset;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\RestBundle\Entity\Error;

class OrgPermissionsetRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgPermissionset';

    const PERM_TEMP_ID = 'permissionTemplateId';

    const ACCESS_LEVEL = 'accessLevel';

    const COURSE_ACCESS = 'coursesAccess';

    const RISK_INDICATOR = 'riskIndicator';

    const INTENT_TO_LEAVE = 'intentToLeave';
    
    const CURRENT_FUTURE_ISQ = 'currentFutureIsq';

    const PROFILE_BLOCK = 'profileBlocks';

    const SURVEY_BLOCK = 'surveyBlocks';

    const FEATURES = 'features';

    const LAST_UPDATED = 'lastUpdated';

    const GROUPS = 'groups';


    /**
     * Finds an OrgPermissionset entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return OrgPermissionset|null The entity instance or NULL if the entity can not be found.
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }


    /**
     * Finds OrgPermissionsets by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return OrgPermissionset[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }


    /**
     * Finds an entity based on the passed in criteria. If not found, throws the passed in exception.
     *
     * @param array $criteria
     * @param SynapseException | null $exception
     * @param array|null $orderBy
     * @return null|OrgPermissionset
     * @throws \Exception
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null)
    {
        $object = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($object, $exception);
    }


    public function getActivePermissionset($orgId)
    {
        $em = $this->getEntityManager();
        $organization = $em->getRepository('SynapseCoreBundle:Organization')->find($orgId);
        if (! isset($organization)) {
            return new Error("validation_error", "organization Not Found");
        }
        return $em->getRepository('SynapseCoreBundle:OrgPermissionset')->findBy(array(
            'organization' => $organization,
            'isArchived' => NULL
        ), array(
            'permissionsetName' => 'ASC'
        ));
    }

    /**
     * Creating the structure of array to be returned from the array retrieved from database
     *
     * @param $permissionDataSet
     * @return array
     */
    protected function buildPermissionsetGraph($permissionDataSet)
    {
        $permissionSets = [];
        $trackBlocks = [];
        $trackGroups = [];
        $permissionSet = [];
        foreach ($permissionDataSet as $permissionData) {
            $permissionTemplateId = $permissionData['permissionTemplateId'];
            
            if (! isset($permissionSets[$permissionTemplateId])) {
                $permissionSet = [
                    'permissionTemplateId' => $permissionData['permissionTemplateId'],
                    'organizationId' => $permissionData['organizationId'],
                    'organizationLangId' => isset($permissionData['orgLangId']) ? $permissionData['orgLangId'] : null,
                    'permissionTemplateName' => $permissionData['permissionTemplateName'],
                    'accessLevel' => isset($permissionData['accessLevel']) ? $permissionData['accessLevel'] : null,
                    'coursesAccess' => isset($permissionData['coursesAccess']) ? $permissionData['coursesAccess'] : null,
                    'riskIndicator' => isset($permissionData['riskIndicator']) ? $permissionData['riskIndicator'] : null,
                    'intentToLeave' => isset($permissionData['intentToLeave']) ? $permissionData['intentToLeave'] : null,
                    'retentionCompletion' => isset($permissionData['retentionCompletion']) ? $permissionData['retentionCompletion'] : null,
                    'currentFutureIsq' => isset($permissionData['currentFutureIsq']) ? $permissionData['currentFutureIsq'] : null,
                    'profileBlocks' => isset($permissionData['profileBlocks']) ? $permissionData['profileBlocks'] : [],
                    'isp' => isset($permissionData['isp']) ? $permissionData['isp'] : null,
                    'surveyBlocks' => isset($permissionData['surveyBlocks']) ? $permissionData['surveyBlocks'] : [],
                    'features' => isset($permissionData['features']) ? $permissionData['features'] : null,
                    'isq' => isset($permissionData['isq']) ? $permissionData['isq'] : null,
                    'lastUpdated' => isset($permissionData['lastUpdated']) ? $permissionData['lastUpdated'] : null,
                    'groups' => isset($permissionData['groups']) ? $permissionData['groups'] : null
                ];
                
                $accessLevel = [
                    'individualAndAggregate' => $permissionData['accesslevel_ind_agg'],
                    'aggregateOnly' => $permissionData['accesslevel_agg']
                ];

                $permissionSet['accessLevel'] = $accessLevel;
                $coursesAccess = [
                    'viewCourses' => $permissionData['view_courses'],
                    'createViewAcademicUpdate' => $permissionData['create_view_academic_update'],
                    'viewAllAcademicUpdateCourses' => $permissionData['view_all_academic_update_courses'],
                    'viewAllFinalGrades' => $permissionData['view_all_final_grades']
                ];
                $permissionSet['coursesAccess'] = $coursesAccess;
            }
            
            $blockId = $permissionData['blockId'];
            if (! isset($trackBlocks[$permissionTemplateId][$blockId])) {
                $block = [
                    'blockId' => $blockId,
                    'blockName' => $permissionData['blockName'],
                    'blockSelection' => $permissionData['blockSelection'],
                    'lastUpdated' => $permissionData['blockLastUpdated']
                ];
                $key = $permissionData['blockType'] . 'Blocks';
                $permissionSet[$key][] = $block;
                $trackBlocks[$permissionTemplateId][$blockId] = true;
            }

            // evaluating groups
            if (! empty($permissionData['flag']) && $permissionData['flag'] == 'group' && ! empty($permissionData['source_id'])) {
                $groupId = $permissionData['source_id'];
                if (! isset($trackGroups[$permissionTemplateId][$groupId])) {
                    $permissionSet['groups'][$groupId] = $permissionData['source_name'];
                    $trackGroups[$permissionTemplateId][$groupId] = true;
                }
            }
            
            $permissionSets[$permissionTemplateId] = $permissionSet;
        }
        
        $resultSet = array_values($permissionSets);
        return $resultSet;
    }

    /*
     * Gets permission set, profile, and survey blocks based on the permission set id.
     *
     * @param integer $permissionSetId
     * @param int $languageMasterId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getDatablockInformationByPermissionsetId($permissionSetId, $languageMasterId = 1)
    {
        $parameters = [
            'languageMasterId' => $languageMasterId,
            'permissionSetId' => $permissionSetId
        ];
        
        $sql = "SELECT 
                    op.id AS permissionTemplateId,
                    op.organization_id AS organizationId,
                    op.permissionset_name AS permissionTemplateName,
                    op.accesslevel_ind_agg,
                    op.accesslevel_agg,
                    op.risk_indicator AS riskIndicator,
                    op.intent_to_leave AS intentToLeave,
                    op.retention_completion AS retentionCompletion,
                    op.view_courses,
                    op.create_view_academic_update,
                    op.view_all_academic_update_courses,
                    op.view_all_final_grades,
                    op.current_future_isq currentFutureIsq,
                    dbm.id AS blockId,
                    dbm.block_type AS blockType,
                    dbml.datablock_desc AS blockName,
                    (opdb.id IS NOT NULL) AS blockSelection,
                    opdb.modified_at AS blockLastUpdated
                FROM
                    org_permissionset op
                        LEFT JOIN
                    org_permissionset_datablock opdb ON op.id = opdb.org_permissionset_id
                        AND opdb.deleted_at IS NULL
                        LEFT JOIN
                    datablock_master dbm ON dbm.id = opdb.datablock_id
                        LEFT JOIN
                    datablock_master_lang dbml ON dbm.id = dbml.datablock_id
                WHERE
                    op.id = :permissionSetId
                        AND (dbml.lang_id = :languageMasterId OR dbml.lang_id IS NULL)
                        AND op.deleted_at IS NULL
                        AND dbm.deleted_at IS NULL
                        AND dbml.deleted_at IS NULL
                ORDER BY op.id , dbm.block_type , dbm.id";

        try {
            $results = $this->executeQueryFetchAll($sql, $parameters);
            $resultSet = $this->buildPermissionsetGraph($results);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $resultSet;
    }

    /**
     *  This will return all permission templates based off on
     *  an user id
     *
     * @param int $userId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getPermissionSetsDataByUser($userId)
    {
        // Get the permissionset data, profiles and survey blocks.
        $sql = "SELECT 
                op.id permissionTemplateId,
                op.organization_id organizationId,
                op.permissionset_name permissionTemplateName,
                op.accesslevel_ind_agg,
                op.accesslevel_agg,
                op.risk_indicator riskIndicator,
                op.intent_to_leave intentToLeave,
                op.retention_completion retentionCompletion,
                op.view_courses,
                op.create_view_academic_update,
                op.view_all_academic_update_courses,
                op.view_all_final_grades,
                dbm.id blockId,
                dbm.block_type blockType,
                dbml.datablock_desc blockName,
                (opdb.id IS NOT NULL) blockSelection,
                opdb.modified_at blockLastUpdated,
                ogf.source_id,
                ogf.source_name,
                ol.lang_id orgLangId,
                ogf.flag
            FROM
                org_permissionset op
                    INNER JOIN
                (SELECT 
                    ogf.org_permissionset_id,
                        ogf.person_id,
                        ogt.descendant_group_id as source_id,
                        og.group_name as source_name,
                        ogf.organization_id,
                        'group' as flag
                FROM
                    org_group_faculty ogf
                INNER JOIN org_group_tree ogt ON ogt.ancestor_group_id = ogf.org_group_id
                INNER JOIN org_group og ON (og.id = ogt.descendant_group_id
                    AND ogf.organization_id = og.organization_id)
                WHERE
                    ogf.deleted_at IS NULL
                        AND og.deleted_at IS NULL
                        AND ogt.deleted_at IS NULL
                        AND ogf.person_id = :userId 
                UNION 
                SELECT 
                    ocf.org_permissionset_id,
                        ocf.person_id,
                        ocf.org_courses_id as source_id,
                        oc.course_name as source_name,
                        ocf.organization_id,
                        'course' as flag
                FROM
                    org_course_faculty ocf
                LEFT JOIN org_courses oc ON (oc.id = ocf.org_courses_id
                    AND ocf.organization_id = oc.organization_id)
                LEFT JOIN org_academic_terms oat ON (oat.id = oc.org_academic_terms_id
                    AND oat.organization_id = oc.organization_id)
                WHERE
                    ocf.deleted_at IS NULL
                        AND oc.deleted_at IS NULL
                        AND oat.deleted_at IS NULL
                        AND ocf.person_id = :userId
                        AND oat.start_date <= now()
                        AND oat.end_date >= now()) ogf ON ogf.org_permissionset_id = op.id
                    RIGHT JOIN
                datablock_master_lang dbml ON (dbml.lang_id = 1 OR dbml.lang_id IS NULL)
                    INNER JOIN
                datablock_master dbm ON dbm.id = dbml.datablock_id
                    LEFT JOIN
                org_permissionset_datablock opdb ON (opdb.org_permissionset_id = op.id
                    AND opdb.datablock_id = dbml.datablock_id
                    AND opdb.deleted_at IS NULL)
                    INNER JOIN
                organization_lang ol ON ol.organization_id = ogf.organization_id
            WHERE
                ogf.person_id = :userId
                    AND op.deleted_at IS NULL
                    AND dbm.deleted_at IS NULL
                    AND dbml.deleted_at IS NULL
            ORDER BY op.id , dbm.block_type , dbm.id";
        try {
            $parameters = ['userId' => $userId];
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
            $resultSet = $this->buildPermissionsetGraph($results);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $resultSet;
    }


    /**
     * Returns a lookup table where the keys are student ids (from the passed-in array) and the values are 0 or 1
     * depending on whether the given faculty member has permission to view the student's intent to leave value.
     * Note that in order to see the student's intent to leave value, the permission set granting that permission must give individual access.
     *
     * @param int $facultyId
     * @param array $studentIds
     * @return array
     */
    public function getIntentToLeavePermissionForFacultyAndStudents($facultyId, $studentIds)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'studentIds' => $studentIds
        ];

        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    pm.student_id,
                    MAX(op.intent_to_leave) AS intent_to_leave_permission
                FROM
                    org_faculty_student_permission_map pm
                        INNER JOIN
                    org_permissionset op
                            ON pm.permissionset_id = op.id
                WHERE op.deleted_at IS NULL
                    AND op.accesslevel_ind_agg = 1
                    AND pm.faculty_id = :facultyId
                    AND pm.student_id IN (:studentIds)
                GROUP BY pm.student_id;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $records = $stmt->fetchAll();

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $lookupTable = [];
        foreach ($records as $record) {
            $lookupTable[$record['student_id']] = $record['intent_to_leave_permission'];
        }

        return $lookupTable;
    }


    /**
     * Returns a lookup table where the keys are student ids (from the passed-in array) and the values are 0 or 1
     * depending on whether the given faculty member has permission to view the student's risk level.
     * Note that in order to see the student's risk level, the permission set granting that permission must give individual access.
     *
     * @param int $facultyId
     * @param array $studentIds
     * @return array
     */
    public function getRiskPermissionForFacultyAndStudents($facultyId, $studentIds)
    {
        $parameters = [
            'studentIds' => $studentIds,
            'facultyId' => $facultyId
        ];

        $parameterTypes = [
            'studentIds' => Connection::PARAM_INT_ARRAY
        ];

        $sql = "
            SELECT
                ofspm.student_id,
                MAX(op.risk_indicator) AS risk_permission
            FROM
                org_faculty_student_permission_map ofspm
                    INNER JOIN
                org_permissionset op ON op.id = ofspm.permissionset_id
            WHERE
                op.deleted_at IS NULL
                AND op.accesslevel_ind_agg = 1
                AND ofspm.student_id IN (:studentIds)
                AND ofspm.faculty_id = :facultyId
            GROUP BY ofspm.student_id;
        ";

        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);

        $studentRiskPermissionMapping = [];
        foreach ($resultSet as $result) {
            $studentRiskPermissionMapping[$result['student_id']] = $result['risk_permission'];
        }

        return $studentRiskPermissionMapping;
    }


    /**
     * Returns a lookup table where the keys are 0 or 1, indicating permission (or not) to view risk levels,
     * and the values are the corresponding counts of students (from the given list).
     *
     * @param int $facultyId
     * @param array $studentIds
     * @return array
     */
    public function getGroupedRiskPermissionsForFacultyAndStudents($facultyId, $studentIds)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'studentIds' => $studentIds
        ];

        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT risk_permission, COUNT(*) AS count
                FROM
                (
                    SELECT pm.student_id, MAX(op.risk_indicator) AS risk_permission
                    FROM org_faculty_student_permission_map pm
                    INNER JOIN org_permissionset op ON pm.permissionset_id = op.id
                    WHERE op.deleted_at IS NULL
                    AND op.accesslevel_ind_agg = 1
                    AND pm.faculty_id = :facultyId
                    AND pm.student_id IN (:studentIds)
                    GROUP BY pm.student_id
                ) AS risk_permissions
                GROUP BY risk_permission;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();
        $lookupTable = [];
        foreach ($records as $record) {
            $lookupTable[$record['risk_permission']] = $record['count'];
        }

        return $lookupTable;
    }


    /**
     * Returns a lookup table where the keys are student ids (from the passed-in array) and the values are 0 or 1
     * depending on whether the given faculty member has individual access to the student.
     *
     * @param int $facultyId
     * @param array $studentIds
     * @return array
     */
    public function getAccessLevelForFacultyAndStudents($facultyId, $studentIds)
    {
        $studentPlaceholders = implode(',', array_fill(0, count($studentIds), '?'));

        $sql = "select pm.student_id, max(op.accesslevel_ind_agg) as accesslevel_ind
                from org_faculty_student_permission_map pm
                inner join org_permissionset op on pm.permissionset_id = op.id
                where op.deleted_at is null
                and pm.faculty_id = ?
                and pm.student_id in ($studentPlaceholders)
                group by pm.student_id;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $parameters = array_merge([$facultyId], $studentIds);
            $stmt->execute($parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();
        $lookupTable = [];
        foreach ($records as $record) {
            $lookupTable[$record['student_id']] = $record['accesslevel_ind'];
        }

        return $lookupTable;
    }


    /**
     * Returns a lookup table where the keys are the access levels (0 for aggregate, 1 for individual)
     * and the values are the counts of students (from the given list) to which the faculty has that access level.
     *
     * @param $facultyId
     * @param $studentIds
     * @return array
     */
    public function getGroupedAccessLevelsForFacultyAndStudents($facultyId, $studentIds)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'studentIds' => $studentIds
        ];

        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT accesslevel_ind, COUNT(*) AS count
                FROM
                (
                    SELECT pm.student_id, MAX(op.accesslevel_ind_agg) AS accesslevel_ind
                    FROM org_faculty_student_permission_map pm
                    INNER JOIN org_permissionset op ON pm.permissionset_id = op.id
                    WHERE op.deleted_at IS NULL
                    AND pm.faculty_id = :facultyId
                    AND pm.student_id IN (:studentIds)
                    GROUP BY pm.student_id
                ) AS access_levels
                GROUP BY accesslevel_ind;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();
        $lookupTable = [];
        foreach ($records as $record) {
            $lookupTable[$record['accesslevel_ind']] = $record['count'];
        }

        return $lookupTable;
    }


    /**
     * This function returns all permission sets a person has or false
     * if the user does not have permission sets within a given org id
     *
     * @param $personId
     * @param $organizationId
     * @param $ISOTimezone
     * @return array|bool
     * @throws SynapseDatabaseException
     */
    public function getAllPermissionsetIdsByPerson($personId, $organizationId, $ISOTimezone = null)
    {

        $parameterTypes = [];
        $parameters['person_id'] = $personId;
        $parameters['organization_id'] = $organizationId;

        // keeping the code clean looking,
        // setting parameters for timezone
        if (!empty($ISOTimezone)) {
            $parameters['ISOTimezone'] = $ISOTimezone;
            $currentDateSQL = " DATE(CONVERT_TZ(NOW(),
                                             'UTC',
                                             :ISOTimezone))  ";
        } else {
            $currentDateSQL = "CURRENT_DATE()";
        }

        $sql = "
                SELECT DISTINCT
                    org_group_faculty.org_permissionset_id
                FROM
                    org_group_faculty
                    INNER JOIN
                    org_group
                    ON org_group.id= org_group_faculty.org_group_id
                WHERE
                    org_group_faculty.person_id = :person_id
                        AND org_group_faculty.organization_id = :organization_id
                        AND org_group_faculty.deleted_at IS NULL
                        AND org_group.deleted_at IS NULL
                UNION
                SELECT DISTINCT
                    org_course_faculty.org_permissionset_id
                FROM
                    org_course_faculty
                    INNER JOIN
                    org_courses
                    ON org_courses.id = org_course_faculty.org_courses_id
                    INNER JOIN
                    org_academic_terms
                    ON org_courses.org_academic_terms_id = org_academic_terms.id
                WHERE
                    org_course_faculty.person_id = :person_id
                        AND org_course_faculty.organization_id = :organization_id
                        AND org_courses.deleted_at IS NULL
                        AND org_course_faculty.deleted_at IS NULL
                        AND org_academic_terms.deleted_at IS NULL
                        AND org_academic_terms.start_date <= $currentDateSQL
                        AND org_academic_terms.end_date   >= $currentDateSQL
        ";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();

        return $records;
    }


    /**
     * Returns true if the faculty member has individual access to the student, and false otherwise.
     *
     * @param int $facultyId
     * @param int $studentId
     * @return bool
     */
    public function checkAccessToStudent($facultyId, $studentId)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'studentId' => $studentId
        ];

        $sql = "SELECT 1
                FROM
                    org_faculty_student_permission_map pm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = pm.permissionset_id
                WHERE op.deleted_at is null
                    AND op.accesslevel_ind_agg = 1
                    AND faculty_id = :facultyId
                    AND student_id = :studentId
                LIMIT 1;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $result = $stmt->fetchAll();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Returns 1 if the user has individual access to any student,
     * 0 if the user only has aggregate access,
     * and null if the user is not connected to any students.
     *
     * @param int $facultyId
     * @return int
     */
    public function determineWhetherUserHasIndividualAccess($facultyId)
    {
        $parameters = [
            'facultyId' => $facultyId
        ];

        $sql = "SELECT
                    MAX(op.accesslevel_ind_agg) AS accesslevel_ind_agg
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON ofspm.permissionset_id = op.id
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND op.deleted_at IS NULL;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $result = $stmt->fetchAll();

        return $result[0]['accesslevel_ind_agg'];
    }


    /**
     * Returns the user's overall permission to see risk and intent to leave.
     * If the user has individual permission to view risk or intent to leave for at least one student,
     * the appropriate attribute ("risk_indicator" or "intent_to_leave") has value 1.
     * Otherwise, it has value 0 or null.
     *
     * @param int $facultyId
     * @return array
     */
    public function getRiskAndIntentToLeavePermissions($facultyId)
    {
        $parameters = [
            'facultyId' => $facultyId
        ];

        $sql = "SELECT
                    MAX(op.risk_indicator) AS risk_indicator,
	                MAX(op.intent_to_leave) AS intent_to_leave
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON ofspm.permissionset_id = op.id
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND op.accesslevel_ind_agg = 1
                    AND op.deleted_at IS NULL;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $result = $stmt->fetchAll();

        return $result[0];
    }


    /**
     * Returns the user's overall course and academic update permissions.
     * For each permission ("view_courses", "create_view_academic_update", "view_all_academic_update_courses", "view_all_final_grades"),
     * if the user has that permission for at least one student (with individual access), the appropriate attribute has value 1.
     * Otherwise, it has value 0 or null.
     *
     * @param int $facultyId
     * @return array
     */
    public function getCourseAndAcademicUpdatePermissions($facultyId)
    {
        $parameters = [
            'facultyId' => $facultyId
        ];

        $sql = "SELECT
                    MAX(op.view_courses) AS view_courses,
                    MAX(op.create_view_academic_update) AS create_view_academic_update,
                    MAX(op.view_all_academic_update_courses) AS view_all_academic_update_courses,
                    MAX(op.view_all_final_grades) AS view_all_final_grades
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON ofspm.permissionset_id = op.id
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND op.accesslevel_ind_agg = 1
                    AND op.deleted_at IS NULL;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $result = $stmt->fetchAll();

        return $result[0];
    }

    /**
     * Gets student ids the faculty has access  for an organization
     *
     * @param int $userId
     * @param int $academicYearId
     * @return array $allStudents
     */
    public function getStudentsForStaff($userId, $academicYearId)
    {
        try {
            $sql = 'SELECT DISTINCT
                        OFSPM.student_id
                    FROM
                        org_faculty_student_permission_map OFSPM
                            INNER JOIN
                        org_person_student_year OPSY ON OPSY.person_id = OFSPM.student_id
                            AND OPSY.organization_id = OFSPM.org_id
                            INNER JOIN
                        org_permissionset OPS ON OFSPM.permissionset_id = OPS.id
                            AND OPS.accesslevel_ind_agg = 1
                            AND OPS.deleted_at IS NULL
                    WHERE
                        OPSY.org_academic_year_id = :orgAcademicYearId
                            AND OFSPM.faculty_id = :userId
                            AND OPSY.deleted_at IS NULL';

            $stmt = $this->getEntityManager()
                ->getConnection()
                ->executeQuery($sql, array(
                    'userId' => $userId,
                    'orgAcademicYearId' => $academicYearId
                ));
            $studentArray = $stmt->fetchAll();
            $allStudents = [];
            if ($studentArray && count($studentArray) > 0) {
                $allStudents = array_column($studentArray, 'student_id');
            }

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $allStudents;
    }

    /**
     * get Student Ids Filtered by Faculty Permission to Student
     * Note: This give back BOTH participating and non-participating students.
     * Note: This returns students for whom the faculty has aggregate only permission
     *
     * @param int $facultyId
     * @param int $orgId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getParticipatingAndNonParticipatingStudentIdsBasedOnFacultyPermission($facultyId, $orgId) {

        $parameters = [
            'orgId' => $orgId,
            'facultyId' => $facultyId
        ];


        try {
            $sql = "SELECT
                        DISTINCT ofspm.student_id
                    FROM
                        org_faculty_student_permission_map ofspm
                    WHERE
                        ofspm.faculty_id = :facultyId
                        AND ofspm.org_id = :orgId";

            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, $parameters);
            $studentArray = $stmt->fetchAll();
            $allStudents = [];
            if ($studentArray && count($studentArray) > 0) {
                $allStudents = array_column($studentArray, 'student_id');
            }

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $allStudents;
    }

    /**
     * Finds out the maximum value for retention completion variable in a set of permissionset ids, the value can be either 1 or 0
     * 1 =  has access to retention and completion
     * 0 =  does not have access to retention  and completion
     *
     * @param  array $orgPermissionSetIds
     * @return bool
     * @throws SynapseDatabaseException
     */
    public function hasRetentionAndCompletionAccess($orgPermissionSetIds)
    {

        $parameters = [
            'orgPermissionSetIds' => $orgPermissionSetIds
        ];

        $parameterTypes = ['orgPermissionSetIds' => Connection::PARAM_INT_ARRAY];

        $sql = "
        SELECT 
            MAX(retention_completion) as retentionCompletion
        FROM
            org_permissionset op
        WHERE
            op.deleted_at IS NULL AND op.id IN (:orgPermissionSetIds)
        ";
        try{
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $resultSet = $stmt->fetchAll();
            if($resultSet[0]['retentionCompletion'] == 1){
                return true;
            }else{
                return false;
            }
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

    }

    /**
     * Finds out what Students in list the Faculty has access to
     *
     * @param int $facultyId
     * @param array $studentIds
     * @return bool
     * @throws SynapseDatabaseException
     */
    public function hasRetentionAccessToStudents($facultyId, $studentIds)
    {

        $parameters = [
            'facultyId' => $facultyId,
            'studentIds' => $studentIds
        ];

        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        $sql = "
            SELECT 
                DISTINCT
                ofspm.student_id
            FROM
                org_faculty_student_permission_map ofspm
                INNER JOIN
                    org_permissionset op ON ofspm.permissionset_id = op.id
            WHERE
                op.deleted_at IS NULL
                AND ofspm.faculty_id = :facultyId
                AND ofspm.student_id IN (:studentIds)
                AND op.retention_completion = 1;";

        $results = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);

        return array_column($results, 'student_id');
    }

}
