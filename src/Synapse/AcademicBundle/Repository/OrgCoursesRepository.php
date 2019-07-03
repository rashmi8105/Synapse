<?php
namespace Synapse\AcademicBundle\Repository;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use Synapse\RestBundle\Exception\ValidationException;

class OrgCoursesRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseAcademicBundle:OrgCourses';

    /**
     * Override function for PHPTyping
     *
     * @param mixed $id
     * @param SynapseException $exception
     * @param null $lockMode
     * @param null $lockVersion
     * @return null|OrgCourses
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $object = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($object, $exception);
    }

    /**
     * Override function for PHPTyping
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @param SynapseException $exception
     * @return OrgCourses[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $object = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($object, $exception);
    }

    /**
     * Override function for PHPTyping
     *
     * @param array $criteria
     * @param SynapseException $exception
     * @param array|null $orderBy
     * @return null|OrgCourses
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null)
    {
        $orgCourseEntity = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($orgCourseEntity, $exception);
    }


    /**
     * @DI\Inject("logger")
     */
    private $logger;

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
            ->from(CourseConstant::ACADEMICBUNDLE . $tname, 't')
            ->where(CourseConstant::IS_OWN_ORG)
            ->andWhere(CourseConstant::IS_DELETED)
            ->setParameters(array(
            CourseConstant::ORGANIZATION => $orgId
        ))
            ->getQuery()
            ->getSingleScalarResult();
        
        return $count;
    }

    /**
     * Get all faculty details for the specified course
     *
     * @param int $courseId
     * @param int $organizationId
     * @param bool $includeInternalIds
     * @return array|null
     */
    public function getSingleCourseFacultiesDetails($courseId, $organizationId, $includeInternalIds = true)
    {
        $parameters = [
            'courseId' => $courseId,
            'organizationId' => $organizationId
        ];

        $internalIdSql = "";
        if ($includeInternalIds) {
            $internalIdSql = "p.id AS faculty_id, 
            ocf.org_permissionset_id AS org_permission_set, ";
        }

        $sql = "SELECT 
                    $internalIdSql
                    op.permissionset_name AS permissionset,
                    p.firstname,
                    p.lastname,
                    p.username AS primary_email,
                    p.external_id
                FROM
                    org_courses oc
                        INNER JOIN
                    org_course_faculty ocf ON ocf.org_courses_id = oc.id
                        INNER JOIN
                    person p ON p.id = ocf.person_id
                        LEFT JOIN
                    org_permissionset op ON op.id = ocf.org_permissionset_id
                        AND op.deleted_at IS NULL
                WHERE
                    oc.organization_id = :organizationId 
                        AND oc.id = :courseId
                        AND oc.deleted_at IS NULL
                        AND ocf.deleted_at IS NULL
                        AND p.deleted_at IS NULL
                ORDER BY p.lastname ASC, p.id ASC";

        $result = $this->executeQueryFetchAll($sql, $parameters);
        return $result;
    }

    /**
     * Gets the Student details for the given course in an organization
     *
     * @param int $courseId
     * @param int $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getParticipantStudentsInCourse($courseId, $organizationId)
    {
        $parameters = [
            'courseId' => $courseId,
            'orgId' => $organizationId
        ];

        $sql = "SELECT
                    ocs.person_id AS student_id,
                    p.lastname,
                    p.firstname,
                    p.username AS primary_email,
                    p.external_id,
                    null AS permissionset,
                    opsy.is_active AS student_status
                FROM
                    org_courses oc
                        INNER JOIN
                    org_course_student ocs ON ocs.org_courses_id = oc.id
                        INNER JOIN
                    person p ON p.id = ocs.person_id
                        INNER JOIN
                    org_person_student_year opsy ON opsy.person_id = ocs.person_id
                        AND opsy.organization_id = ocs.organization_id
                        AND opsy.org_academic_year_id = oc.org_academic_year_id
                WHERE
                    oc.organization_id = :orgId
                    AND oc.id = :courseId
                    AND oc.deleted_at IS NULL
                    AND ocs.deleted_at IS NULL
                    AND p.deleted_at IS NULL
                    AND opsy.deleted_at IS NULL
                ORDER BY p.lastname ASC, p.firstname ASC";

        $results = $this->executeQueryFetchAll($sql, $parameters);
        return $results;
    }

    /**
     * Get all courses for a student, filtering on criteria presented.
     *
     * @param int $organizationId
     * @param int $studentId
     * @param string $currentDate - date string. 'Y-m-d H:m:s'
     * @param string $year - year_id, 'all'
     * @param string $term - org_academic_terms_id, 'current', 'future', 'all'
     * @param string $collegeCode - org_courses.college_code. Code of the college within the organization in which the course resides.
     * @param string $departmentCode - org_courses.dept_code. Code of department within the college within the organization.
     * @param string $searchText - Compared against org_courses.subject_code, org_courses.course_number, org_courses.course_name, or a concatenation of subject_code and course_number
     * @return array
     */
    public function getCoursesForStudent($organizationId, $studentId, $currentDate = '', $year = 'all', $term = 'all', $collegeCode = 'all', $departmentCode = 'all', $searchText = '')
    {

        $parameters = [
            "organizationId" => $organizationId,
            "studentId" => $studentId
        ];

        if ($year != 'all') {
            $parameters['yearId'] = $year;
            $yearFilterCondition = ' AND oay.year_id = :yearId ';
        } else {
            $yearFilterCondition = '';
        }

        if ($term == 'current') {
            $parameters['currentDate'] = $currentDate;
            $termFilterCondition = ' AND :currentDate BETWEEN oat.start_date AND oat.end_date ';
        } elseif ($term == 'future') {
            $parameters['currentDate'] = $currentDate;
            $termFilterCondition = ' AND oat.start_date > :currentDate ';
        } elseif ($term == 'all') {
            $termFilterCondition = '';
        } else {
            $parameters['term'] = $term;
            $termFilterCondition = ' AND oat.id = :term';
        }

        if ($collegeCode != 'all') {
            $parameters['collegeCode'] = $collegeCode;
            $collegeCodeFilterCondition = ' AND oc.college_code = :collegeCode ';
        } else {
            $collegeCodeFilterCondition = '';
        }

        if ($departmentCode != 'all') {
            $parameters['departmentCode'] = $departmentCode;
            $departmentCodeFilterCondition = ' AND oc.dept_code = :departmentCode ';
        } else {
            $departmentCodeFilterCondition = '';
        }

        if ($searchText != '') {
            $searchToken = "%$searchText%";
            $parameters['searchToken'] = $searchToken;
            $searchTextFilterCondition = " AND (oc.subject_code LIKE :searchToken OR oc.course_number LIKE :searchToken OR oc.course_name LIKE :searchToken OR CONCAT(oc.subject_code, oc.course_number) LIKE :searchToken) ";
        } else {
            $searchTextFilterCondition = '';
        }


        $sql = "
                SELECT
                    oc.college_code,
                    oc.dept_code,
                    oc.org_academic_year_id,
                    oc.org_academic_terms_id,
                    oay.year_id,
                    oay.name AS year_name,
                    oat.name AS term_name,
                    oat.term_code,
                    oc.id AS org_course_id,
                    oc.subject_code,
                    oc.course_number,
                    oc.section_number,
                    oc.course_section_id,
                    oc.course_name,
                    oc.location,
                    oc.days_times,
                    oat.start_date,
                    oat.end_date,
                    IF(NOW() <= oat.end_date, 1, 0) AS current_or_future_term_course
                FROM
                    org_courses oc
                        JOIN
                    org_course_student ocs ON oc.organization_id = ocs.organization_id
                        AND ocs.org_courses_id = oc.id
                        JOIN
                    org_academic_year oay ON oay.organization_id = oc.organization_id
                        AND oay.id = oc.org_academic_year_id
                        JOIN
                    org_academic_terms oat ON oat.organization_id = oc.organization_id
                        AND oay.id = oat.org_academic_year_id
                        AND oc.org_academic_terms_id = oat.id
                WHERE
                    oc.organization_id = :organizationId
                    AND ocs.person_id = :studentId
                    AND oc.deleted_at IS NULL
                    AND ocs.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                    AND oat.deleted_at IS NULL
                    $yearFilterCondition
                    $termFilterCondition
                    $collegeCodeFilterCondition
                    $departmentCodeFilterCondition
                    $searchTextFilterCondition
                ORDER BY oat.end_date DESC, oay.start_date DESC, oc.college_code ASC, oc.dept_code ASC, oc.id DESC;
        ";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();

        return $records;

    }

    /**
     * Get all courses for a faculty, filtering on criteria presented.
     *
     * @param int $organizationId
     * @param int $facultyId
     * @param string $currentDate - date string. 'Y-m-d H:m:s'
     * @param string $year - year_id, 'all'
     * @param string $term - org_academic_terms_id, 'current', 'future', 'all'
     * @param string $collegeCode - org_courses.college_code. Code of the college within the organization in which the course resides.
     * @param string $departmentCode - org_courses.dept_code. Code of department within the college within the organization.
     * @param string $searchText - Compared against org_courses.subject_code, org_courses.course_number, org_courses.course_name, or a concatenation of subject_code and course_number
     * @param bool $dataCount - Counts number of records found
     * @return array
     */
    public function getCoursesForFaculty($organizationId, $facultyId, $currentDate = '', $year = 'all', $term = 'all', $collegeCode = 'all', $departmentCode = 'all', $searchText = '', $dataCount = false)
    {
        $parameters = [
            "organizationId" => $organizationId,
            "facultyId" => $facultyId,
        ];

        if ($year != 'all') {
            $parameters['yearId'] = $year;
            $yearFilterCondition = ' AND oay.year_id = :yearId ';
        } else {
            $yearFilterCondition = '';
        }

        if ($term == 'current') {
            $parameters['currentDate'] = $currentDate;
            $termFilterCondition = ' AND :currentDate BETWEEN oat.start_date AND oat.end_date ';
        } elseif ($term == 'future') {
            $parameters['currentDate'] = $currentDate;
            $termFilterCondition = ' AND oat.start_date > :currentDate ';
        } elseif ($term == 'all') {
            $termFilterCondition = '';
        } else {
            $parameters['term'] = $term;
            $termFilterCondition = ' AND oat.id = :term';
        }

        if ($collegeCode != 'all') {
            $parameters['collegeCode'] = $collegeCode;
            $collegeCodeFilterCondition = ' AND oc.college_code = :collegeCode ';
        } else {
            $collegeCodeFilterCondition = '';
        }

        if ($departmentCode != 'all') {
            $parameters['departmentCode'] = $departmentCode;
            $departmentCodeFilterCondition = ' AND oc.dept_code = :departmentCode ';
        } else {
            $departmentCodeFilterCondition = '';
        }

        if ($searchText != '') {
            $searchToken = "%$searchText%";
            $parameters['searchToken'] = $searchToken;
            $searchTextFilterCondition = " AND (oc.subject_code LIKE :searchToken OR oc.course_number LIKE :searchToken OR oc.course_name LIKE :searchToken OR CONCAT(oc.subject_code, oc.course_number) LIKE :searchToken) ";
        } else {
            $searchTextFilterCondition = '';
        }
        if ($dataCount) {
            $selectStatementColumns = "COUNT(*) AS data_count";
        } else {
            $selectStatementColumns = "oc.college_code,
                    oc.dept_code,
                    oc.org_academic_year_id,
                    oc.org_academic_terms_id,
                    oay.year_id,
                    oay.name AS year_name,
                    oat.name AS term_name,
                    oat.term_code,
                    oc.id AS org_course_id,
                    oc.subject_code,
                    oc.course_number,
                    oc.section_number,
                    oc.course_section_id,
                    oc.course_name,
                    oc.location,
                    oc.days_times,
                    op.create_view_academic_update,
                    op.view_all_academic_update_courses";
        }

        $sql = "SELECT
                    $selectStatementColumns
                FROM
                    org_courses oc
                        JOIN
                    org_course_faculty ocf ON oc.organization_id = ocf.organization_id
                        AND ocf.org_courses_id = oc.id
                        JOIN
                    org_permissionset op ON op.organization_id = oc.organization_id
                        AND op.id = ocf.org_permissionset_id
                        JOIN
                    org_academic_year oay ON oay.organization_id = oc.organization_id
                        AND oay.id = oc.org_academic_year_id
                        JOIN
                    org_academic_terms oat ON oat.organization_id = oc.organization_id
                        AND oay.id = oat.org_academic_year_id
                        AND oc.org_academic_terms_id = oat.id
                WHERE
                    oc.organization_id = :organizationId
                    AND ocf.person_id = :facultyId
                    AND oc.deleted_at IS NULL
                    AND ocf.deleted_at IS NULL
                    AND op.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                    AND oat.deleted_at IS NULL
                    $yearFilterCondition
                    $termFilterCondition
                    $collegeCodeFilterCondition
                    $departmentCodeFilterCondition
                    $searchTextFilterCondition
                ORDER BY oay.start_date DESC, oat.start_date DESC, oc.college_code ASC,
                         oc.dept_code ASC, oc.subject_code ASC, oc.course_number ASC, oc.course_name ASC,
                         oc.section_number ASC, oc.days_times ASC, oc.location ASC, oc.id DESC
                         ";

        $records = $this->executeQueryFetchAll($sql, $parameters);
        if ($dataCount) {
            $records = (int)$records[0]['data_count'];
        }

        return $records;
    }

    /**
     * Gets all courses for the organization, filtered for the criteria present.
     *
     * @param int $organizationId
     * @param string $currentDate - date string. 'Y-m-d H:m:s'
     * @param string $year - year_id or empty string
     * @param string $term - org_academic_terms_id (isInternal = true) or term_code (isInternal = false), 'current', 'future', 'all'
     * @param string $collegeCode - org_courses.college_code. Code of the college within the organization in which the course resides.
     * @param string $departmentCode - org_courses.dept_code. Code of department within the college within the organization.
     * @param string $searchText - Compared against org_courses.subject_code, org_courses.course_number, org_courses.course_name, or a concatenation of subject_code and course_number
     * @param int|null $recordsPerPage - Number of records desired for the result set.
     * @param int|null $offset - offset number of records desired for the result set.
     * @param bool $formatResultSet - Changes the select statement based on the desired output format of the API - layered (false) or flat (true)
     * @param bool $isInternal - Source of call is webapp (true) vs. external source (false). Determines what to compare the term ID against in the query.
     * @param bool $dataCount - Counts number of records found
     * @return array | int
     */
    public function getCoursesForOrganization($organizationId, $currentDate = '', $year = 'all', $term = 'all', $collegeCode = 'all', $departmentCode = 'all', $searchText = '', $recordsPerPage = null, $offset = null, $formatResultSet = true, $isInternal = true, $dataCount = true)
    {
        $parameters = [
            "organizationId" => $organizationId
        ];
        $parameterTypes = [];

        if ($isInternal) {
            $selectStatementAdditionalColumns = 'oc.id AS org_course_id,';
        } else {
            $selectStatementAdditionalColumns = '
                oc.org_academic_year_id,
                oc.org_academic_terms_id,
                oc.id AS org_course_id,';
        }

        if (is_numeric($recordsPerPage)) {
            $parameters['recordsPerPage'] = (int)$recordsPerPage;
            $parameters['offset'] = $offset;
            $parameterTypes['recordsPerPage'] = 'integer';
            $parameterTypes['offset'] = 'integer';
            $limitClause = 'LIMIT :recordsPerPage OFFSET :offset';
        } else {
            $limitClause = '';
        }

        if ($year != 'all') {
            $parameters['yearId'] = $year;
            $yearFilterCondition = ' AND oay.year_id = :yearId ';
        } else {
            $yearFilterCondition = '';
        }

        if ($term == 'current') {
            $parameters['currentDate'] = $currentDate;
            $termFilterCondition = ' AND :currentDate BETWEEN oat.start_date AND oat.end_date ';
        } elseif ($term == 'future') {
            $parameters['currentDate'] = $currentDate;
            $termFilterCondition = ' AND oat.start_date > :currentDate ';
        } elseif ($term == 'all') {
            $termFilterCondition = '';
        } else {
            $parameters['term'] = $term;
            if ($isInternal) {
                $termFilterCondition = ' AND oat.id = :term';
            } else {
                $termFilterCondition = ' AND oat.term_code = :term';
            }
        }

        if ($collegeCode != 'all') {
            $parameters['collegeCode'] = $collegeCode;
            $collegeCodeFilterCondition = ' AND oc.college_code = :collegeCode ';
        } else {
            $collegeCodeFilterCondition = '';
        }

        if ($departmentCode != 'all') {
            $parameters['departmentCode'] = $departmentCode;
            $departmentCodeFilterCondition = ' AND oc.dept_code = :departmentCode ';
        } else {
            $departmentCodeFilterCondition = '';
        }

        if ($searchText != '') {
            $searchToken = "%$searchText%";
            $parameters['searchToken'] = $searchToken;
            $searchTextFilterCondition = " AND (oc.subject_code LIKE :searchToken OR oc.course_number LIKE :searchToken OR oc.course_name LIKE :searchToken OR CONCAT(oc.subject_code, oc.course_number) LIKE :searchToken) ";
        } else {
            $searchTextFilterCondition = '';
        }

        if ($dataCount) {
            $selectStatementColumns = "COUNT(*) AS data_count";
        } else {
            $selectStatementColumns = "$selectStatementAdditionalColumns
                oay.year_id,
                oay.name AS year_name,
                oat.name AS term_name,
                oat.term_code,
                oc.subject_code,
                oc.course_number,
                oc.section_number,
                oc.course_section_id,
                oc.course_name,
                oc.location,
                oc.days_times,
                oc.college_code,
                oc.dept_code";
        }

        $sql = "
            SELECT
                $selectStatementColumns
            FROM
                org_courses oc
                    JOIN
                org_academic_year oay ON oay.organization_id = oc.organization_id
                    AND oay.id = oc.org_academic_year_id
                    JOIN
                org_academic_terms oat ON oat.organization_id = oc.organization_id
                    AND oay.id = oat.org_academic_year_id
                    AND oc.org_academic_terms_id = oat.id
            WHERE
                oc.organization_id = :organizationId
                AND oc.deleted_at IS NULL
                AND oay.deleted_at IS NULL
                AND oat.deleted_at IS NULL
                $yearFilterCondition
                $termFilterCondition
                $collegeCodeFilterCondition
                $departmentCodeFilterCondition
                $searchTextFilterCondition
            ORDER BY oay.start_date DESC, oat.start_date DESC, oc.college_code ASC,
                     oc.dept_code ASC, oc.subject_code ASC, oc.course_number ASC, oc.course_name ASC,
                     oc.section_number ASC, oc.days_times ASC, oc.location ASC, oc.id DESC
            $limitClause
    ";

        $records = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        if ($dataCount) {
            $records = (int)$records[0]['data_count'];
        }

        return $records;
    }

    /**
     * get count of faculty per course by organizationId
     *
     * @param int $organizationId
     * @param array $courseIds
     * @return array
     */
    public function getCountOfFacultyInCourse($organizationId, $courseIds)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'courseIds' => $courseIds
        ];

        $parameterTypes = ['courseIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    oc.id AS course_id,
                    count(ocf.id) AS faculty_count
                FROM
                    org_courses oc
                        INNER JOIN
                    org_course_faculty ocf ON ocf.org_courses_id = oc.id
                WHERE
                    oc.organization_id = :organizationId
                        AND oc.id IN( :courseIds )
                        AND oc.deleted_at IS NULL
                        AND ocf.deleted_at IS NULL
                GROUP BY oc.id";

        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $resultSet;
    }

    /**
     * get count of student per course by organizationId
     *
     * @param int $organizationId
     * @param  array $courseIds
     * @return array
     */
    public function getCountOfStudentInCourse($organizationId, $courseIds)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'courseIds' => $courseIds
        ];

        $parameterTypes = ['courseIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    oc.id AS course_id, count(ocs.id) AS student_count
                FROM
                    org_courses oc
                        INNER JOIN
                    org_course_student ocs ON ocs.org_courses_id = oc.id
                WHERE
                    oc.organization_id = :organizationId
                        AND oc.id IN( :courseIds )
                        AND oc.deleted_at IS NULL
                        AND ocs.deleted_at IS NULL
                GROUP BY oc.id";

        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $resultSet;
    }

    public function remove(OrgCourses $OrgCourses)
    {
        $this->delete($OrgCourses);
    }

    public function getCoursesbyFilter($orgId, $year, $term, $college, $department, $filter, $user = '')
    {
        $em = $this->getEntityManager();
        if ($user) {
            $queryBuilder = $em->createQueryBuilder()
                ->select('c.id, c.subjectCode, c.courseNumber, c.sectionNumber, c.externalId')
                ->from(CourseConstant::ORG_COURSES_REPO, 'c')
                ->join(CourseConstant::ORG_FACULTY_REPO, 'ocf', \Doctrine\ORM\Query\Expr\Join::WITH, 'ocf.course = c.id')
                ->where('c.orgAcademicYear = :year')
                ->andWhere(CourseConstant::IS_ACADEMIC_TERM)
                ->andWhere(CourseConstant::IS_DEPTCODE)
                ->andWhere(CourseConstant::IS_COLLEGECODE)
                ->andWhere(CourseConstant::IS_ORG_ID)
                ->andWhere('ocf.person = :userId')
                ->andWhere('ocf.organization = :facultyOrgId');
            $parameter[] = array(
                CourseConstant::YEAR => $year,
                CourseConstant::TERM => $term,
                CourseConstant::DEPARTMENT => $department,
                CourseConstant::COLLEGE => $college,
                CourseConstant::ORGID => $orgId,
                CourseConstant::USERID => $user,
                'facultyOrgId' => $orgId
            );
        } else {
            $queryBuilder = $em->createQueryBuilder()
                ->select('c.id, c.subjectCode, c.courseNumber, c.sectionNumber, c.externalId, c.location')
                ->from(CourseConstant::ORG_COURSES_REPO, 'c')
                ->where('c.orgAcademicYear = :year')
                ->andWhere(CourseConstant::IS_ACADEMIC_TERM)
                ->andWhere(CourseConstant::IS_DEPTCODE)
                ->andWhere(CourseConstant::IS_COLLEGECODE)
                ->andWhere(CourseConstant::IS_ORG_ID);
            $parameter[] = array(
                CourseConstant::YEAR => $year,
                CourseConstant::TERM => $term,
                CourseConstant::DEPARTMENT => $department,
                CourseConstant::COLLEGE => $college,
                CourseConstant::ORGID => $orgId
            );
        }
        if ($filter) {
            $queryBuilder = $queryBuilder->andWhere('c.subjectCode LIKE :subjectCode OR c.courseNumber LIKE :subjectCode');
            $parameter[] = array(
                'subjectCode' => "%" . $filter . "%"
            );
        }
        $parameter = call_user_func_array(CourseConstant::FUNC_ARRAY_MERGE, $parameter);
        $db = $queryBuilder->setParameters($parameter)->getQuery();
        $resultSet = $db->getArrayResult();
        return $resultSet;
    }

    public function getPersonCount($tname, $orgId)
    {
        $em = $this->getEntityManager();
        $count = $em->createQueryBuilder()
            ->select('DISTINCT(t.person)')
            ->from(CourseConstant::ACADEMICBUNDLE . $tname, 't')
            ->where(CourseConstant::IS_OWN_ORG)
            ->andWhere(CourseConstant::IS_DELETED)
            ->setParameters(array(
            CourseConstant::ORGANIZATION => $orgId
        ))
            ->getQuery()
            ->getArrayResult();
        return count($count);
    }

    public function getFacultyList($orgId)
    {
        $em = $this->getEntityManager();
        $personList = $em->createQueryBuilder()
            ->select('IDENTITY(t.person) as personId, pf.firstname as personFirstName, pf.lastname as personLastName, oc.id')
            ->from(CourseConstant::ORG_FACULTY_REPO, 't')
            ->LEFTJoin(CourseConstant::ORG_COURSES_REPO, 'oc', \Doctrine\ORM\Query\Expr\Join::WITH, 'oc.id = t.course')
            ->LEFTJoin(CourseConstant::PERSON_REPO, 'pf', \Doctrine\ORM\Query\Expr\Join::WITH, 'pf.id = t.person')
            ->where(CourseConstant::IS_OWN_ORG)
            ->andWhere(CourseConstant::IS_DELETED)
            ->setParameters(array(
            CourseConstant::ORGANIZATION => $orgId
        ))
            ->getQuery()
            ->getArrayResult();
        return $personList;
    }

    public function getCourseForOrganization($orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('IDENTITY(a.yearId) as yearId, t.termCode as termId, c.externalId, c.subjectCode, c.courseNumber, c.sectionNumber, 
            c.courseName, c.creditHours, c.collegeCode, c.deptCode, c.daysTimes, c.createdAt, c.location, t.name')
            ->from(CourseConstant::ORG_COURSES_REPO, 'c')
            ->join(CourseConstant::ACADEMIC_TERM_REPO, 't', \Doctrine\ORM\Query\Expr\Join::WITH, 't.id = c.orgAcademicTerms')
            ->join(CourseConstant::ACADEMIC_YEAR_REPO, 'a', \Doctrine\ORM\Query\Expr\Join::WITH, 'a.id = c.orgAcademicYear')
            ->where(CourseConstant::IS_ORG_ID)
            ->setParameters(array(
            CourseConstant::ORGID => $orgId
        ))
            ->orderBy('t.startDate')
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    public function getCourseDetailList($orgId, $type, $queryParam, $currentDate = null, $loggedInUserId = null,$userType=null,$isSearch= false,$isViewAllCourses = false)
    {
        $em = $this->getEntityManager();
        if ($type == CourseConstant::TERM) {
            $resultSet = $this->getTermsforCourse($queryParam, $em, $orgId);
        } else {
            $parameter[] = array(
                CourseConstant::ORGID => $orgId
            );
            $conditions = array(
                CourseConstant::YEAR => 'y.yearId = :year',
                CourseConstant::TERM => CourseConstant::IS_ACADEMIC_TERM,
                CourseConstant::COLLEGE => CourseConstant::IS_COLLEGECODE,
                CourseConstant::DEPARTMENT => CourseConstant::IS_DEPTCODE,
                'subject' => 'c.subjectCode = :subject',
                CourseConstant::COURSE_FIELD => 'c.courseNumber = :course',
                'section' => 'c.sectionNumber = :section'
            );
            $selectFields = array(
                CourseConstant::COLLEGE => 'distinct(c.collegeCode) as key, c.collegeCode as value ',
                CourseConstant::DEPARTMENT => 'distinct(c.deptCode) as key, c.deptCode as value',
                'subject' => 'distinct(c.subjectCode) as key, c.subjectCode as value',
                CourseConstant::COURSE_FIELD => 'distinct(c.courseNumber) as key, c.courseName as value',
                'section' => 'distinct(c.sectionNumber) as key, c.sectionNumber as value'
            );
            $queryBuilder = $em->createQueryBuilder();
                $queryBuilder->select($selectFields[$type]);
                $queryBuilder->from(CourseConstant::ORG_COURSES_REPO, 'c');
                $queryBuilder->join(CourseConstant::ACADEMIC_YEAR_REPO, 'y', \Doctrine\ORM\Query\Expr\Join::WITH, 'c.orgAcademicYear   = y.id');
            $queryBuilder->join(CourseConstant::ACADEMIC_TERM_REPO, 't', \Doctrine\ORM\Query\Expr\Join::WITH, 'c.orgAcademicTerms   = t.id');
            if ($loggedInUserId && ($userType != 'coordinator' || $userType == '')) {
                if ($isSearch && $isViewAllCourses) {
                   
                    $queryBuilder->where(CourseConstant::IS_ORG_ID);
                    $queryBuilder = $queryBuilder->andWhere("t.startDate <= '$currentDate'")->andWhere("t.endDate >= '$currentDate'");
                } else {
                    $queryBuilder->join(CourseConstant::ORG_FACULTY_REPO, 'ocf', \Doctrine\ORM\Query\Expr\Join::WITH, 'c.id   = ocf.course');
                    $queryBuilder->where(CourseConstant::IS_ORG_ID);
                        $queryBuilder->andWhere('ocf.person =:person');
                        $parameter[] = array(
                            'person' => $loggedInUserId
                        );
                    }
                    
                }else{                    
                    $queryBuilder->where(CourseConstant::IS_ORG_ID);
                }           
            if (! empty($queryParam)) {
                foreach ($queryParam as $key => $param) {
                    if (! empty($param) && $param != 'all') {
                        if (($type == 'college' || $type == 'department') && $param == 'current') {
                            $queryBuilder = $queryBuilder->andWhere("t.startDate <= '$currentDate'")->andWhere("t.endDate >= '$currentDate'");
                        } else {
                            $queryBuilder = $queryBuilder->andWhere($conditions[$key]);
                            $parameter[] = array(
                                $key => $param
                            );
                        }
                    }
                }
            }
            $parameter = call_user_func_array(CourseConstant::FUNC_ARRAY_MERGE, $parameter);
            $order = explode(' ', explode(',', $selectFields[$type])[1])[1];
            $queryBuilder = $queryBuilder->orderBy($order);
            $db = $queryBuilder->setParameters($parameter)->getQuery();
            $resultSet = $db->getArrayResult();
        }
       
        return $resultSet;
    }

    private function getTermsforCourse($queryParam, $em, $orgId)
    {
        $parameter[] = array(
            CourseConstant::ORGID => $orgId
        );
        $queryBuilder = $em->createQueryBuilder()
            ->select('IDENTITY(t.orgAcademicYearId) as year_id , t.id, t.name, t.termCode, IDENTITY(y.yearId) as yearId, t.startDate, t.endDate')
            ->from(CourseConstant::ACADEMIC_TERM_REPO, 't')
            ->join(CourseConstant::ACADEMIC_YEAR_REPO, 'y', \Doctrine\ORM\Query\Expr\Join::WITH, 'y.id = t.orgAcademicYearId')
            ->where('t.organization = :orgId');
        if (! empty($queryParam) && $queryParam[CourseConstant::YEAR] != 'all') {
            $queryBuilder = $queryBuilder->andWhere('y.yearId = :id');
            $parameter[] = array(
                'id' => $queryParam[CourseConstant::YEAR]
            );
        }
        $parameter = call_user_func_array(CourseConstant::FUNC_ARRAY_MERGE, $parameter);
        $order = ($queryParam[CourseConstant::YEAR] == 'all') ? 'yearId, t.termCode' : 't.termCode';
        $queryBuilder->add('orderBy', $order);
        $db = $queryBuilder->setParameters($parameter)->getQuery();
        $resultSet = $db->getArrayResult();
        
        return $resultSet;
    }

    public function getPersonCountByCourse($tname, $orgId, $course)
    {
        $em = $this->getEntityManager();
        $count = $em->createQueryBuilder()
            ->select('count(t.id)')
            ->from(CourseConstant::ACADEMICBUNDLE . $tname, 't')
            ->where(CourseConstant::IS_OWN_ORG)
            ->andWhere('t.course = :course')
            ->andWhere(CourseConstant::IS_DELETED)
            ->setParameters(array(
            CourseConstant::ORGANIZATION => $orgId,
            CourseConstant::COURSE_FIELD => $course
        ))
            ->getQuery()
            ->getSingleScalarResult();
        return $count;
    }

    public function getActiveCourseByOrganization($orgId, $currentDate)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('oc.id, oc.courseName, oc.subjectCode, oc.courseNumber, oc.sectionNumber, oat.name');
        $qb->from(CourseConstant::ORG_COURSES_REPO, 'oc');
        $qb->Join('SynapseAcademicBundle:OrgAcademicYear', 'oay', \Doctrine\ORM\Query\Expr\Join::WITH, 'oay.id = oc.orgAcademicYear');
        $qb->Join('SynapseAcademicBundle:OrgAcademicTerms', 'oat', \Doctrine\ORM\Query\Expr\Join::WITH, 'oat.id = oc.orgAcademicTerms');
        $qb->where('oc.organization = :orgId');
        $qb->andWhere('oay.organization = :orgId');
        $qb->andWhere('oat.organization = :orgId');
        $qb->andWhere('oay.startDate <= :currDate');
        $qb->andWhere('oay.endDate >= :currDate');
        $qb->andWhere('oat.startDate <= :currDate');
        $qb->andWhere('oat.endDate >= :currDate');
        
        $qb->setParameters(array(
            'orgId' => $orgId,
            'currDate' => $currentDate
        ));
        $qb->orderBy('oc.courseName', 'asc');
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }


    /**
     * Get all courses overlapping the specified datetime string.
     *
     * @param int $orgId
     * @param string $datetimeString
     * @return mixed
     */
    public function getAllCoursesEncapsulatingDatetime($orgId, $datetimeString)
    {
        $parameters = [
            'organizationId' => $orgId,
            'datetimeString' => $datetimeString
        ];

        $sql = "
            SELECT 
                oc.id AS org_courses_id
            FROM 
                synapse.org_courses oc 
                    JOIN 
                synapse.org_academic_terms oat 
                      ON oat.organization_id = oc.organization_id
                      AND oat.id = oc.org_academic_terms_id
            WHERE 
                oc.organization_id = :organizationId
                AND :datetimeString BETWEEN oat.start_date AND oat.end_date
                AND oc.deleted_at IS NULL 
                AND oat.deleted_at IS NULL 
                
        ";

        $resultSet = $this->executeQueryFetchAll($sql, $parameters);

        return $resultSet;
    }
    
    public function detectDeleted($orgId, $externalId)
    {
        $em = $this->getEntityManager();
        $sql = "SELECT id as CourseId FROM org_courses WHERE organization_id = {$orgId} AND externalID={$externalId} AND deleted_at IS NOT NULL";
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }

    public function activateCourseAgain($orgId, $existingCourseId)
    {
        try {
            $sql = "UPDATE org_courses SET deleted_at = null WHERE organization_id = {$orgId} AND id={$existingCourseId}";
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
        } catch (\Exception $e) {            
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], SearchConstant::QUERY_ERROR, SearchConstant::QUERY_ERROR);
        }
    }

    public function detectCourseEntityDeleted($orgId, $courseId, $personId, $entity)
    {
        $em = $this->getEntityManager();
        $sql = "SELECT id as Course{$entity}Id, deleted_at FROM org_course_" . strtolower($entity) . " WHERE organization_id = {$orgId} AND org_courses_id={$courseId} AND person_id={$personId}";
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }

    public function activateEntityCourse($orgId, $courseId, $personId, $entity)
    {
        try {
            $sql = "UPDATE org_course_{$entity} SET deleted_at = null WHERE organization_id = {$orgId} AND org_courses_id={$courseId} AND person_id={$personId}";
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
        } catch (\Exception $e) {           
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], SearchConstant::QUERY_ERROR, SearchConstant::QUERY_ERROR);
        }
    }

    public function getStudentCourseList($orgId, $personId, $filterParam, $userType)
    {
        $em = $this->getEntityManager();
        $table = 'OrgCourseStudent';
        $queryBuilder = $em->createQueryBuilder()
            ->select('c.collegeCode,c.deptCode,IDENTITY(c.orgAcademicYear) as year,IDENTITY(c.orgAcademicTerms) as term, IDENTITY(y.yearId) as yearId, y.name as yearName, t.name as termName, c.id, c.subjectCode, c.courseNumber, c.sectionNumber, c.externalId, c.courseName, c.location, c.createdAt, c.daysTimes, t.startDate, t.endDate, orgl.organizationName as campus_name')
            ->from(CourseConstant::ORG_COURSES_REPO, 'c')
            ->join(CourseConstant::ACADEMICBUNDLE . $table, 'oc', \Doctrine\ORM\Query\Expr\Join::WITH, CourseConstant::OC_COURSE_CID);
        $queryBuilder->join(CourseConstant::ACADEMIC_YEAR_REPO, 'y', \Doctrine\ORM\Query\Expr\Join::WITH, CourseConstant::IS_ACADEMICYEAR_MATCH);
        $queryBuilder->join(CourseConstant::ACADEMIC_TERM_REPO, 't', \Doctrine\ORM\Query\Expr\Join::WITH, CourseConstant::IS_ACADMICTERM_MATCH);
        $queryBuilder->join("SynapseCoreBundle:Organization", 'org', \Doctrine\ORM\Query\Expr\Join::WITH, "org.id=t.organization");
        $queryBuilder->join("SynapseCoreBundle:OrganizationLang", 'orgl', \Doctrine\ORM\Query\Expr\Join::WITH, "org.id=orgl.organization");
        $queryBuilder->addSelect(' (CASE WHEN(:today BETWEEN t.startDate AND t.endDate) THEN 0 ELSE 1 END) as ORD ');
        $queryBuilder->andWhere(CourseConstant::IS_PERSONID_MATCH);
        $queryBuilder->setParameters(array(
            CourseConstant::USERID => $personId,
            CourseConstant::TODAY => date(CourseConstant::YMD, time())
        ));
        $queryBuilder->orderBy('ORD', 'ASC');
        $db = $queryBuilder->getQuery();
        $resultSet = $db->getArrayResult();
        return $resultSet;
    }

    /**
     * Get the list of student ids specific to a course
     *
     * @param int $courseId - Internal Course Id
     * @param int $organizationId
     * @param bool $returnInternalIDs - To identify whether external/internal student id should be returned
     *                              true - Return student's internal id
     *                              false - Return student's external id
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getAllStudentsInCourse($courseId, $organizationId, $returnInternalIDs = false)
    {
        if ($returnInternalIDs) {
            $studentIdColumn = 'p.id';
        } else {
            $studentIdColumn = 'p.external_id';
        }
        $parameters = [
            'courseId' => $courseId,
            'organizationId' => $organizationId,
        ];
        $sql = "SELECT
                    $studentIdColumn as student_id
                FROM
                    org_course_student ocs
                        JOIN
                    person p ON p.id = ocs.person_id
                WHERE
                    ocs.org_courses_id = :courseId
                    AND ocs.organization_id = :organizationId
                    AND p.deleted_at IS NULL
                    AND ocs.deleted_at IS NULL";
        try {
            $statement = $this->getEntityManager()->getConnection()->executeQuery($sql, $parameters);
            $courseStudentIds = $statement->fetchAll();
            return $courseStudentIds;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }
}