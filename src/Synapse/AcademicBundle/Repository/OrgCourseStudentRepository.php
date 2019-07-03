<?php
namespace Synapse\AcademicBundle\Repository;

use DateTime;
use Facile\DoctrineMySQLComeBack\Doctrine\DBAL\Connection;
use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\AcademicBundle\Entity\OrgCourseStudent;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\CourseConstant;

class OrgCourseStudentRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseAcademicBundle:OrgCourseStudent';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return OrgCourseStudent|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }


    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return OrgCourseStudent[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }


    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param SynapseException|null $exception
     * @param array|null $orderBy
     *
     * @return OrgCourseStudent|null
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null)
    {
        $orgPersonFacultyEntity = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($orgPersonFacultyEntity, $exception);
    }


    public function remove(OrgCourseStudent $OrgCourseStudent)
    {
        $this->delete($OrgCourseStudent);
    }

    public function getCourseStudentForOrganization($orgId, $unique = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select($unique.' cs, oc.externalId as UniqueCourseSectionID')
            ->from(CourseConstant::ORG_STUDENT_REPO, 'cs')
            ->LEFTJoin('SynapseAcademicBundle:OrgCourses', 'oc', \Doctrine\ORM\Query\Expr\Join::WITH, CourseConstant::CS_COURSE_OCID)
            ->where('cs.organization = :orgId')
            ->setParameters(array(
            'orgId' => $orgId
        ))
            ->orderBy('cs.id')
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    /**
     * Get all course-student combinations for the organization.
     *
     * @param int $organizationId
     * @return array
     */
    public function getCourseStudentDumpDataForOrganization($organizationId)
    {
        $parameters = ['organizationId' => $organizationId];

        $sql = "
        SELECT
            p.external_id AS StudentId,
            course_section_id AS UniqueCourseSectionId
        FROM
            org_course_student ocs
                JOIN
            person p ON p.id = ocs.person_id
                JOIN
            org_person_student ops ON ops.person_id = p.id
                JOIN
            org_courses oc ON oc.id = ocs.org_courses_id
        WHERE
            ocs.organization_id = :organizationId
                AND ISNULL(ocs.deleted_at)
                AND ISNULL(p.deleted_at)
                AND ISNULL(ops.deleted_at)
                AND ISNULL(oc.deleted_at)";

        $records = $this->executeQueryFetchAll($sql, $parameters);

        return $records;
    }

    public function createCourseStudent(OrgCourseStudent $OrgCourseStudent)
    {
        $em = $this->getEntityManager();
        $em->persist($OrgCourseStudent);
        return $OrgCourseStudent;
    }

    /**
     * Gets the unique course-student combinations for the passed in student list,
     * on terms that encompass the passed-in datetime.
     *
     * @param array $studentIds
     * @param DateTime $datetime
     * @return array|null
     */
    public function getCoursesForStudent($studentIds, $datetime)
    {
        $startDate = null;
        $endDate = null;
        if (!empty($datetime) && is_object($datetime)) {
            $startDate = $datetime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
            $endDate = $datetime->format(SynapseConstant::DEFAULT_DATE_FORMAT) . " 23:59:59";
        }

        $parameters = [
            'studentIds' => $studentIds,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $parameterTypes = [
            'studentIds' => Connection::PARAM_INT_ARRAY
        ];

        $sql = "
            SELECT
                ocs.person_id,
                ocs.org_courses_id
            FROM
                org_course_student ocs
                    JOIN
                org_courses oc ON oc.id = ocs.org_courses_id
                    JOIN
                org_academic_terms oat ON oat.id = oc.org_academic_terms_id
            WHERE
                oc.deleted_at IS NULL
                AND ocs.deleted_at IS NULL
                AND oat.deleted_at IS NULL
                AND ocs.person_id IN (:studentIds)
                AND :startDate >= oat.start_date
                AND :endDate <= oat.end_date
            GROUP BY ocs.person_id, ocs.org_courses_id;

        ";

        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $resultSet;
    }

    /**
     * Return all the students associated with a course
     *
     * Used in the academic update process, either when selecting a faculty member, or selecting a course
     * to collect updates from
     *
     * @param int $courseId
     * @return array
     */
    public function getStudentsByCourse($courseId)
    {
        $parameters = [
            'courseId' => $courseId
        ];

        $sql = "SELECT
                    ocs.person_id AS studentId,
                    oc.id AS courseId
                FROM
                    org_course_student ocs
                        INNER JOIN
                    org_courses oc
                            ON ocs.org_courses_id = oc.id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ocs.person_id
                            AND opsy.organization_id = ocs.organization_id
                            AND opsy.org_academic_year_id = oc.org_academic_year_id
                WHERE
                    oc.id = :courseId
                    AND oc.deleted_at IS NULL
                    AND ocs.deleted_at IS NULL
                    AND opsy.deleted_at IS NULL;";

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
     * Returns an array of IDs of all students in the given courses.
     *
     * @param array $orgCoursesIds
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStudentsByCourses($orgCoursesIds)
    {
        $placeholders = implode(',', array_fill(0, count($orgCoursesIds), '?'));

        $sql = "select distinct person_id
                from org_course_student
                where deleted_at is null
                and org_courses_id in ($placeholders);";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute($orgCoursesIds);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();
        $results = array_map('current', $results);      // un-nest the array
        return $results;
    }


    /**
     * Adding student to a course based on $studentId, $organizationId and $courseId
     * 
     * @param int $studentId
     * @param int $organizationId
     * @param int $courseId
     * @throws \Doctrine\ORM\ORMException
     * @throws \RunException
     */
    public function addStudentCourseAssoc($studentId, $organizationId, $courseId)
    {
        $em = $this->getEntityManager();
        $orgCourseStudent = new OrgCourseStudent();
        $orgCourseStudent->setPerson($em->getReference(Person::class, $studentId));
        $orgCourseStudent->setCourse($em->getReference(OrgCourses::class, $courseId));
        $orgCourseStudent->setOrganization($em->getReference(Organization::class, $organizationId));
        $this->persist($orgCourseStudent);
        $this->flush();
    }

    /**
     * @param int $studentId
     * @param int $courseId
     */
    public function removeStudentCourseAssoc($studentId, $courseId)
    {
        /** @var OrgCourseStudent[] $courses */
        $courses = $this->findBy([
            'person' => $studentId,
            'course' => $courseId,
        ]);

        foreach ($courses as $course) {
            $this->remove($course);
        }

        $this->flush();
    }
    

    /**
     * Gets the list of all students in any course for any current academic term.
     * Used when creating an academic update request for "all students" (which really means all students currently enrolled in a course).
     *
     * We can filter the list by passing students ids if required.
     *
     * @param int $organizationId
     * @param array $studentIds
     * @return array
     */
    public function getStudentsInAnyCurrentCourse($organizationId, $studentIds = [])
    {
        $parameters = [
            'organizationId' => $organizationId
        ];
        $parameterTypes = [];
        $studentFilterCondition = '';
        if(!empty($studentIds)){
            $parameters['studentIds'] = $studentIds;
            $studentFilterCondition = ' AND p.id IN (:studentIds)';
            $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];
        }

        try {
            $sql = "SELECT DISTINCT
                        p.id AS student_id,
                        p.firstname,
                        p.lastname,
                        p.username AS email,
                        p.external_id,
                        opsy.is_active AS status
                    FROM
                        person p
                            INNER JOIN
                        org_person_student_year opsy
                                ON opsy.person_id = p.id
                                AND opsy.organization_id = p.organization_id
                            INNER JOIN
                        org_course_student ocs
                                ON ocs.person_id = p.id
                                AND ocs.organization_id = p.organization_id
                            INNER JOIN
                        org_courses oc
                                ON oc.id = ocs.org_courses_id
                                AND oc.organization_id = p.organization_id
                                AND oc.org_academic_year_id = opsy.org_academic_year_id
                            INNER JOIN
                        org_academic_year oay
                                ON oay.id = oc.org_academic_year_id
                                AND oay.organization_id = p.organization_id
                            INNER JOIN
                        org_academic_terms oat
                                ON oat.id = oc.org_academic_terms_id
                                AND oat.org_academic_year_id = oay.id
                                AND oat.organization_id = p.organization_id
                    WHERE
                        p.organization_id = :organizationId
                        $studentFilterCondition
                        AND oat.start_date <= NOW()
                        AND oat.end_date >= NOW()
                        AND p.deleted_at IS NULL
                        AND opsy.deleted_at IS NULL
                        AND ocs.deleted_at IS NULL
                        AND oc.deleted_at IS NULL
                        AND oay.deleted_at IS NULL
                        AND oat.deleted_at IS NULL
                    ORDER BY p.lastname, p.firstname, p.username";

            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $resultSet = $stmt->fetchAll();
        return $resultSet;
    }

    /**
     * Gets the count of students for all courses by organization
     *
     * @param integer $organizationId
     * @return integer
     */
    public function getCourseStudentCountByOrganization($organizationId)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];

        $sql = "SELECT
                COUNT(DISTINCT person_id) AS student_count
            FROM
                org_course_student
            WHERE
                organization_id = :organizationId
                AND deleted_at IS NULL";

        $result = $this->executeQueryFetch($sql, $parameters);
        return (int)$result['student_count'];

    }
}