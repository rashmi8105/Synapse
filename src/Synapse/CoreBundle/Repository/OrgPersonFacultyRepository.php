<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\OrgPersonFaculty;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Util\Constants\AcademicUpdateConstant;
use Synapse\CoreBundle\Util\Constants\AppointmentsConstant;
use Synapse\CoreBundle\Util\Constants\PersonConstant;

class OrgPersonFacultyRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgPersonFaculty';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     * @param \Exception $exception
     * @return OrgPersonFaculty|null | BaseEntity
     */
    public function find($id, $lockMode = null, $lockVersion = null, $exception = null)
    {
        $orgPersonFaculty = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($orgPersonFaculty, $exception);
    }

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param \Exception $exception
     * @return OrgPersonFaculty[]|null
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $orgPersonFacultyArray = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($orgPersonFacultyArray, $exception);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param SynapseException|null $exception
     * @param array|null $orderBy
     * @param \Exception $exception
     * @return OrgPersonFaculty|null | BaseEntity
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null)
    {
        $orgPersonFacultyEntity = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($orgPersonFacultyEntity, $exception);
    }

    public function remove(OrgPersonFaculty $faculty)
    {
        $em = $this->getEntityManager();
        $em->remove($faculty);
    }

    public function getFacultiesByOrganizationCourse($orgId, $currentDate)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p.id as staff_id, p.firstname, p.lastname, p.username as email, p.externalId');
        $qb->from(PersonConstant::ORG_PERSON_FACULTY, 'opf');
        $qb->Join(AcademicUpdateConstant::PERSON_REPO, 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = opf.person');
        $qb->Join('SynapseAcademicBundle:OrgCourseFaculty', 'ocf', \Doctrine\ORM\Query\Expr\Join::WITH, 'ocf.person = p.id');
        $qb->Join(AcademicUpdateConstant::ORG_COURSES_REPO, 'oc', \Doctrine\ORM\Query\Expr\Join::WITH, 'oc.id = ocf.course');
        $qb->Join('SynapseAcademicBundle:OrgAcademicYear', 'oay', \Doctrine\ORM\Query\Expr\Join::WITH, 'oay.id = oc.orgAcademicYear');
        $qb->Join('SynapseAcademicBundle:OrgAcademicTerms', 'oat', \Doctrine\ORM\Query\Expr\Join::WITH, 'oat.id = oc.orgAcademicTerms');
        $qb->LEFTJoin('SynapseCoreBundle:OrgPermissionset', 'ops', \Doctrine\ORM\Query\Expr\Join::WITH, 'ops.id = ocf.orgPermissionset');
        
        $qb->where(PersonConstant::OPF_ORGANIZATON_EQUAL_ORGID);
        $qb->andWhere('p.organization = :orgId');
        $qb->andWhere('oc.organization = :orgId');
        $qb->andWhere('ocf.organization = :orgId ');
        $qb->andWhere('opf.status = 1 OR opf.status IS NULL');
        $qb->andWhere('oay.organization = :orgId');
        $qb->andWhere('oay.startDate <= :currDate');
        $qb->andWhere('oay.endDate >= :currDate');
        $qb->andWhere('oat.startDate <= :currDate');
        $qb->andWhere('oat.endDate >= :currDate');
        $qb->andWhere('ops.createViewAcademicUpdate = :createViewAU');
        $qb->setParameters(array(
            PersonConstant::ORG_ID => $orgId,
            'currDate' => $currentDate,
            'createViewAU' => 1
        ));
        $qb->orderBy('p.lastname', 'asc');
        $qb->addOrderBy('p.firstname', 'asc');
        $qb->addOrderBy('p.username', 'asc');
        $qb->groupBy('p.id');
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }

    /**
     * Query for getting Student Campus Connections from  - List of Faculty
     * List of student list passed or converted to int array
     * works in both the case of single students or multiple students
     *
     * In the case of creating an appointment, $appointmentCheck will add additional permissions checks to ensure that
     * the campus connection also has the permission to create appointments.
     *
     * @param array|string|null $studentIds
     * @param int $organizationId
     * @param string $currentDate
     * @param bool $appointmentCheck
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getStudentCampusConnection($studentIds, $organizationId, $currentDate, $appointmentCheck = false)
    {
        if (!is_array($studentIds) && !empty($studentIds)) {
            $studentIds = explode(',', $studentIds);
        }

        $parameters = ['studentIds' => $studentIds, 'organizationId' => $organizationId, 'currentDate' => $currentDate];
        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        $appointmentCreatePermissionCheckCourse = '';
        $appointmentCreatePermissionCheckGroup = '';
        $featureMasterLangJoin = '';

        //Below conditions will be added while fetching campus connection for student if faculty has private create permission for appointment
        if ($appointmentCheck) {
            $appointmentCreatePermissionCheckCourse = " INNER JOIN org_permissionset_features opfeature ON opfeature.org_permissionset_id = ocf.org_permissionset_id
                AND opfeature.organization_id = ocf.organization_id
                AND opfeature.feature_id = fml.feature_master_id
                AND opfeature.private_create = 1";

            $appointmentCreatePermissionCheckGroup = " INNER JOIN org_permissionset_features opfeature ON opfeature.org_permissionset_id = ogf.org_permissionset_id
                AND opfeature.organization_id = ogf.organization_id
                AND opfeature.feature_id = fml.feature_master_id
                AND opfeature.private_create = 1";

            $featureMasterLangJoin = " INNER JOIN feature_master_lang fml ON fml.feature_name = :featureName";

            $parameters['featureName'] = AppointmentsConstant::APPOINTMENT_FEATURE_NAME;
        }

        /*
         * The view org_faculty_student_permission_map is not optimized for this situation.
         * TODO: Create a view that optimizes this logic and start using it
         */
        $sql = "SELECT
                    person_id,
                    faculty,
                    fname,
                    lname,
                    title,
                    email,
                    external_id,
                    flag,
                    course_or_group_id,
                    course_or_group_name,
                    is_invisible
                FROM
                    (SELECT
                        opf.person_id,
                        ocf.person_id AS faculty,
                        p.firstname AS fname,
                        p.lastname AS lname,
                        p.title,
                        p.username AS email,
                        p.external_id,
                        'course' AS flag,
                        oc.id AS course_or_group_id,
                        oc.course_name AS course_or_group_name,
                        0 AS is_invisible
                    FROM org_person_faculty opf
                        INNER JOIN org_course_faculty ocf ON ocf.person_id = opf.person_id
                            AND ocf.organization_id = opf.organization_id
                        INNER JOIN org_courses oc ON oc.id = ocf.org_courses_id
                            AND oc.organization_id = ocf.organization_id
                        INNER JOIN org_course_student ocs ON ocs.org_courses_id = oc.id
                            AND ocs.organization_id = ocf.organization_id
                        INNER JOIN org_academic_terms oat ON oat.id = oc.org_academic_terms_id
                            AND oat.organization_id = ocf.organization_id
                        $featureMasterLangJoin
                        INNER JOIN person p ON p.id = ocf.person_id
                            AND p.organization_id = ocf.organization_id
                        $appointmentCreatePermissionCheckCourse
                        WHERE
                            opf.organization_id = :organizationId
                            AND ocs.person_id IN (:studentIds)
                            AND oat.start_date <= :currentDate
                            AND oat.end_date >= :currentDate
                            AND opf.deleted_at IS NULL
                            AND ocf.deleted_at IS NULL
                            AND oc.deleted_at IS NULL
                            AND ocs.deleted_at IS NULL
                            AND oat.deleted_at IS NULL
                            AND p.deleted_at IS NULL
                            AND (opf.status = 1 OR opf.status IS NULL)
                    UNION
                    SELECT
                        opf.person_id,
                        ogf.person_id AS faculty,
                        p.firstname AS fname,
                        p.lastname AS lname,
                        p.title,
                        p.username AS email,
                        p.external_id,
                        'group' AS flag,
                        og.id AS course_or_group_id,
                        og.group_name AS course_or_group_name,
                        ogf.is_invisible AS is_invisible
                    FROM org_person_faculty opf
                        INNER JOIN org_group_faculty ogf ON ogf.person_id  = opf.person_id
                            AND ogf.organization_id = opf.organization_id
                        INNER JOIN org_group og ON og.id = ogf.org_group_id
                            AND og.organization_id = ogf.organization_id
                        INNER JOIN
                               org_group_tree ogt ON og.id = ogt.ancestor_group_id AND ogt.deleted_at IS NULL
                        INNER JOIN
                               org_group_students ogs ON (ogs.org_group_id = ogt.descendant_group_id)
                               AND ogs.organization_id = og.organization_id
                        $featureMasterLangJoin
                        INNER JOIN person p ON p.id = ogf.person_id
                            AND p.organization_id = ogf.organization_id
                        $appointmentCreatePermissionCheckGroup
                        WHERE
                            opf.organization_id = :organizationId
                            AND ogs.person_id IN (:studentIds)
                            AND ogf.is_invisible = 0
                            AND opf.deleted_at IS NULL
                            AND ogf.deleted_at IS NULL
                            AND og.deleted_at IS NULL
                            AND ogs.deleted_at IS NULL
                            AND p.deleted_at IS NULL
                            AND (opf.status = 1 OR opf.status IS NULL)) as merged
                GROUP BY faculty
                ORDER BY lname , fname";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $results = $stmt->fetchAll();
        return $results;
    }

    public function getCourseCampusConnection($studentId, $orgId, $currentDate)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(ocs.person) as student_id, IDENTITY(opf.person) as faculty_id');
        $qb->from(PersonConstant::ORG_PERSON_FACULTY, 'opf');
        $qb->Join('SynapseAcademicBundle:OrgCourseFaculty', 'ocf', \Doctrine\ORM\Query\Expr\Join::WITH, 'ocf.person = opf.person');
        $qb->Join(AcademicUpdateConstant::ORG_COURSES_REPO, 'oc', \Doctrine\ORM\Query\Expr\Join::WITH, 'oc.id = ocf.course');
        $qb->Join(AcademicUpdateConstant::ORG_COURSES_STUDENT_REPO, 'ocs', \Doctrine\ORM\Query\Expr\Join::WITH, 'ocs.course = oc.id');
        $qb->Join('SynapseAcademicBundle:OrgAcademicYear', 'oay', \Doctrine\ORM\Query\Expr\Join::WITH, 'oay.id = oc.orgAcademicYear');
        $qb->where(PersonConstant::OPF_ORGANIZATON_EQUAL_ORGID);
        $qb->where('ocf.organization = :orgId');
        $qb->where('ocs.organization = :orgId');
        $qb->andWhere('oay.startDate <= :currDate');
        $qb->andWhere('oay.endDate >= :currDate');
        $qb->andWhere('(opf.status = 1 OR opf.status IS NULL)');
        $qb->andWhere('ocs.person in (' . $studentId . ')');
        $qb->setParameters(array(
            PersonConstant::ORG_ID => $orgId,
            'currDate' => $currentDate
        ));
        $qb->orderBy('ocs.person', 'asc');
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();

        return $resultSet;
    }

    /**
     * Get group campus connection for student based on studentId, orgId and currentDate
     * @param int $studentId
     * @param int $orgId
     * @param string $currentDate
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getGroupCampusConnection($studentIds, $orgId, $currentDate)
    {
        $parameters = [
            'studentIdsArr' => explode(",",$studentIds),
            'orgId' => $orgId
        ];
        $sql = "SELECT 
            ogs.person_id AS student_id, opf.person_id AS faculty_id
        FROM
            org_person_faculty opf
                INNER JOIN
            org_group_faculty ogf ON (ogf.person_id = opf.person_id )
                AND (ogf.deleted_at IS NULL) AND (ogf.organization_id = opf.organization_id)
                INNER JOIN
            org_group og ON (og.id = ogf.org_group_id)
                AND (og.deleted_at IS NULL) AND (og.organization_id = ogf.organization_id)
                LEFT JOIN
            org_group_tree ogt ON og.id = ogt.ancestor_group_id
                AND ogt.deleted_at IS NULL
                LEFT JOIN
            org_group_students ogs ON (ogs.org_group_id = ogt.descendant_group_id)
                AND (ogs.organization_id = og.organization_id)
        WHERE
                (opf.organization_id = :orgId)
                AND (opf.status = 1 OR opf.status IS NULL)
                AND (ogs.person_id IN (:studentIdsArr))
                AND (opf.deleted_at IS NULL)
        ORDER BY ogs.person_id ASC";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters,['studentIdsArr'=>Connection::PARAM_INT_ARRAY]);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $results = $stmt->fetchAll();
        return $results;
    }

    /**
     * Get all campus connection details for student based on studentId and currentDate
     * @param int $studentId
     * @param string $currentDate
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getAllCampusConnectioDetailsForStudent($studentId, $currentDate)
    {
        $parameters = [
            'studentId' => $studentId,
            'currentDate' => $currentDate
        ];
        $sql = "
            (SELECT
                opf.person_id,
                ocf.person_id AS faculty,
                p.firstname AS fname,
                p.lastname AS lname,
                p.title,
                p.username AS email,
                p.external_id AS externalId,
                ci.home_phone AS phone,
                ci.primary_mobile AS mobile_no,
                'course' AS flag,
                oc.id AS origin_id,
                oc.course_name AS origin_name,
                o.id AS organization_id,
                ol.organization_name,
                ops.person_id_primary_connect AS primary_conn,
                o.campus_id,
                0 AS is_invisible
            FROM
                org_person_faculty opf
                    LEFT JOIN
                org_course_faculty ocf ON ocf.person_id = opf.person_id
                    AND ocf.organization_id = opf.organization_id
                    LEFT JOIN
                org_courses oc ON oc.id = ocf.org_courses_id
                    LEFT JOIN
                org_course_student ocs ON ocs.org_courses_id = oc.id
                    AND ocs.organization_id = oc.organization_id
                    LEFT JOIN
                org_academic_terms oat ON oat.id = oc.org_academic_terms_id
                    LEFT JOIN
                person p ON p.id = ocf.person_id
                    LEFT JOIN
                person_contact_info pci ON pci.person_id = p.id
                    LEFT JOIN
                contact_info ci ON ci.id = pci.contact_id
                    LEFT JOIN
                organization o ON o.id = opf.organization_id
                    LEFT JOIN
                organization_lang ol ON ol.organization_id = o.id
                    LEFT JOIN
                org_person_student ops ON ops.person_id = ocs.person_id
                    AND ops.organization_id = ocs.organization_id
            WHERE
                ocs.person_id = :studentId
                    AND oat.start_date <= :currentDate
                    AND oat.end_date >= :currentDate
                    AND opf.deleted_at IS NULL
                    AND ocf.deleted_at IS NULL
                    AND oc.deleted_at IS NULL
                    AND ocs.deleted_at IS NULL
                    AND oat.deleted_at IS NULL
                    AND p.deleted_at IS NULL
                    AND o.deleted_at IS NULL
                    AND ol.deleted_at IS NULL
                    AND (opf.status = 1 OR opf.status IS NULL))
        UNION
            (SELECT
                opf.person_id,
                ogf.person_id AS faculty,
                p.firstname AS fname,
                p.lastname AS lname,
                p.title,
                p.username AS email,
                p.external_id AS externalId,
                ci.home_phone AS phone,
                ci.primary_mobile AS mobile_no,
                'group' AS flag,
                og.id AS origin_id,
                og.group_name AS origin_name,
                o.id AS organization_id,
                ol.organization_name,
                ops.person_id_primary_connect AS primary_conn,
                o.campus_id,
                ogf.is_invisible AS is_invisible
            FROM
                org_person_faculty opf
                    LEFT JOIN
                org_group_faculty ogf ON ogf.person_id = opf.person_id
                    AND ogf.organization_id = opf.organization_id
                    LEFT JOIN
                org_group og ON og.id = ogf.org_group_id
                    LEFT JOIN
                org_group_tree ogt ON og.id = ogt.ancestor_group_id
                    AND ogt.deleted_at IS NULL
                    LEFT JOIN
                org_group_students ogs ON ogs.org_group_id = ogt.descendant_group_id
                    LEFT JOIN
                person p ON p.id = ogf.person_id
                    LEFT JOIN
                person_contact_info pci ON pci.person_id = p.id
                    LEFT JOIN
                contact_info ci ON ci.id = pci.contact_id
                    LEFT JOIN
                organization o ON o.id = opf.organization_id
                    LEFT JOIN
                organization_lang ol ON ol.organization_id = o.id
                    LEFT JOIN
                org_person_student ops ON ops.person_id = ogs.person_id
                    AND ops.organization_id = ogs.organization_id
            WHERE
                ogs.person_id = :studentId
                    AND ogf.is_invisible = 0
                    AND opf.deleted_at IS NULL
                    AND ogf.deleted_at IS NULL
                    AND og.deleted_at IS NULL
                    AND ogs.deleted_at IS NULL
                    AND p.deleted_at IS NULL
                    AND o.deleted_at IS NULL
                    AND ol.deleted_at IS NULL
                    AND (opf.status = 1 OR opf.status IS NULL))
            ORDER BY lname, fname ASC";
        $result = $this->executeQueryFetchAll($sql, $parameters);
        return $result;
    }

    /**
     * Gets the non-logged in faculty person IDs for the specified organization
     *
     * @param int $organizationId
     * @return array
     */
    public function getNonLoggedInFaculty($organizationId)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];

        $sql = "
                SELECT 	
                    DISTINCT opf.person_id
                FROM 
                    org_person_faculty opf 
                        LEFT JOIN
                    access_log al 
                            ON al.organization_id = :organizationId
                            AND al.person_id = opf.person_id 
                            AND al.event = 'Login'
                            AND al.deleted_at IS NULL 
                WHERE 
                    opf.deleted_at IS NULL 
                    AND al.id IS NULL
                    AND opf.organization_id = :organizationId
                    AND (opf.status = 1 OR opf.status IS NULL);
        ";

        $result = $this->executeQueryFetchAll($sql, $parameters);
        return $result;
    }

    /**
     * Gets the count of the non-logged in faculty for the specified organization
     *
     * @param int $organizationId
     * @return int
     */
    public function getNonLoggedInFacultyCount($organizationId)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];

        $sql = "
                SELECT 	
                    COUNT(DISTINCT opf.person_id) AS count 
                FROM 
                    org_person_faculty opf 
                        LEFT JOIN
                    access_log al 
                            ON al.organization_id = :organizationId
                            AND al.person_id = opf.person_id 
                            AND al.event = 'Login'
                            AND al.deleted_at IS NULL 
                WHERE 
                    opf.deleted_at IS NULL 
                    AND al.id IS NULL
                    AND opf.organization_id = :organizationId
                    AND (opf.status = 1 OR opf.status IS NULL);
        ";

        $result = $this->executeQueryFetchAll($sql, $parameters);
        return $result[0]['count'];
    }

    public function getPersonFacultByExternalId($externalId, $org)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p');
        $qb->from('SynapseCoreBundle:Person', 'p');
        
        $qb->Join(PersonConstant::ORG_PERSON_FACULTY, 'opf', \Doctrine\ORM\Query\Expr\Join::WITH, 'opf.person = p.id');
        $qb->where(PersonConstant::OPF_ORGANIZATON_EQUAL_ORGID);
        $qb->andWhere('p.organization = :orgId');
        $qb->andWhere('p.externalId = :externalId');
        $qb->setParameters(array(
            PersonConstant::ORG_ID => $org,
            PersonConstant::EXT_ID => $externalId
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getOneOrNullResult();
        
        return $resultSet;
    }

    function getAllFacultiesForOrg($orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(opf.person) as faculty_id');
        $qb->from(PersonConstant::ORG_PERSON_FACULTY, 'opf');
        $qb->where(PersonConstant::OPF_ORGANIZATON_EQUAL_ORGID);
        $qb->setParameters(array(
            PersonConstant::ORG_ID => $orgId
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }
    
    public function getFacultyGoogleId($person)
    {
        $em = $this->getEntityManager();
        $results = $em->createQueryBuilder()
            ->select('faculty.googleEmailId, faculty.googleClientId')
            ->from('SynapseCoreBundle:OrgPersonFaculty', 'faculty')
            ->where('faculty.person = :person')
            ->andWhere('faculty.deletedAt IS NULL')
            ->andWhere('faculty.googleEmailId IS NOT NULL')
            ->andWhere('faculty.googleClientId IS NOT NULL')
            ->setParameters(array(
            'person' => $person
            ))
            ->getQuery()
            ->getResult();                
        return $results;
    }
    
    
    function getAllIncactiveFacultiesForOrg($orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(opf.person) as faculty_id');
        $qb->from(PersonConstant::ORG_PERSON_FACULTY, 'opf');
        $qb->where(PersonConstant::OPF_ORGANIZATON_EQUAL_ORGID.' AND opf.status = 0');
        $qb->setParameters(array(
            PersonConstant::ORG_ID => $orgId
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }


    /**
     * Gets the list of users per organization using Google calendar sync
     *
     * @param int $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getListOfGoogleCalendarSyncUsers($organizationId)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];

        $sql = "SELECT 
                    person_id
                FROM
                    org_person_faculty
                WHERE
                    organization_id = :organizationId
                        AND google_sync_status = '1'
                        AND deleted_at IS NULL";

        try {
            $em = $this->getEntityManager();
            $statement = $em->getConnection()->prepare($sql);
            $statement->execute($parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $result = $statement->fetchAll();
        return $result;
    }

}