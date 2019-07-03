<?php
namespace Synapse\StaticListBundle\Repository;

use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\StaticListBundle\Entity\OrgStaticList;

class OrgStaticListRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseStaticListBundle:OrgStaticList';

    public function createStaticList(OrgStaticList $orgStaticList)
    {
        $orgStaticList = $this->persist($orgStaticList);
        return $orgStaticList;
    }

    public function updateStaticList(OrgStaticList $orgStaticList)
    {
        $orgStaticList = $this->update($orgStaticList);
        return $orgStaticList;
    }

    public function remove(OrgStaticList $orgStaticList)
    {
        $orgStaticList = $this->delete($orgStaticList);
        return $orgStaticList;
    }

    /**
     * Gets the list of static list for student associated with
     *
     * @param int $facultyId
     * @param int $studentId
     * @param string|null $sortBy
     * @param int $recordsPerPage
     * @param int|null $offset
     * @param int $currentOrgAcademicYearId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStaticListsWithStudentId($facultyId, $studentId, $sortBy = null, $recordsPerPage, $offset = null, $currentOrgAcademicYearId)
    {

        $deterministicSort = 'osl.name, osl.id DESC';
        switch ($sortBy) {
            case 'staticlist_name':
            case '+staticlist_name':
                $orderByClause = 'ORDER BY ' . $deterministicSort;
                break;
            case '-staticlist_name':
                $orderByClause = 'ORDER BY osl.name DESC, osl.id DESC';
                break;
            case 'created_at':
            case '+created_at':
                $orderByClause = 'ORDER BY osl.created_at, ' . $deterministicSort;
                break;
            case '-created_at':
                $orderByClause = 'ORDER BY osl.created_at DESC, ' . $deterministicSort;
                break;
            case 'modified_at':
            case '+modified_at':
                $orderByClause = 'ORDER BY osl.modified_at, ' . $deterministicSort;
                break;
            case '-modified_at':
                $orderByClause = 'ORDER BY osl.modified_at DESC, ' . $deterministicSort;
                break;
            case 'student_count':
            case '+student_count':
                $orderByClause = 'ORDER BY student_count, ' . $deterministicSort;
                break;
            case '-student_count':
                $orderByClause = 'ORDER BY student_count DESC, ' . $deterministicSort;
                break;
            default:
                $orderByClause = 'ORDER BY osl.modified_at DESC, ' . $deterministicSort;
        }


        $sql = "SELECT
                        osl.id,
                        osl.name,
                        osl.description,
                        osl.created_at,
                        osl.modified_at,
                        created_by.id AS created_by_person_id,
                        created_by.firstname AS created_by_firstname,
                        created_by.lastname AS created_by_lastname,
                        modified_by.id AS modified_by_person_id,
                        modified_by.firstname AS modified_by_firstname,
                        modified_by.lastname AS modified_by_lastname,
                        (SELECT COUNT(person_id) FROM org_static_list_students osls WHERE org_static_list_id = osl.id AND osls.deleted_at IS NULL) AS student_count
                    FROM
                        org_static_list_students osls
                            INNER JOIN
                        org_static_list osl ON osls.org_static_list_id = osl.id
                            LEFT JOIN
                        org_person_student_year opsy ON opsy.person_id = osls.person_id AND opsy.deleted_at IS NULL AND opsy.org_academic_year_id = :orgAcademicYearId
                            LEFT JOIN
                        (SELECT * FROM org_faculty_student_permission_map WHERE faculty_id = :facultyId) ofspm ON opsy.person_id = ofspm.student_id
                            LEFT JOIN
                        person created_by ON osl.created_by = created_by.id
                            AND created_by.deleted_at IS NULL
                            LEFT JOIN
                        person modified_by ON osl.modified_by = modified_by.id
                            AND modified_by.deleted_at IS NULL
                    WHERE
                        osls.person_id = :studentId
                        AND osl.person_id = :facultyId
                        AND osls.deleted_at IS NULL
                        AND osl.deleted_at IS NULL
                        GROUP BY osl.id
                        $orderByClause
                        LIMIT :recordsPerPage OFFSET :offset";

        $parameters = [
            'studentId' => $studentId,
            'facultyId' => $facultyId,
            'offset' => $offset,
            'recordsPerPage' => $recordsPerPage,
            'orgAcademicYearId' => $currentOrgAcademicYearId
        ];

        $parameterTypes = [
            'recordsPerPage' => \PDO::PARAM_INT,
            'offset' => \PDO::PARAM_INT
        ];

        try {
            $staticListArray = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
            return $staticListArray;

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

    }

    /**
     * gets the count of static lists for a faculty associated with a student
     *
     * @param int $facultyId
     * @param int $studentId
     * @param int $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getCountOfStaticListsWithStudentID($facultyId, $studentId, $organizationId)
    {

        $sql = "SELECT
                        COUNT( DISTINCT osl.id) AS static_list_count
                    FROM
                        org_static_list_students osls
                            INNER JOIN
                        org_static_list osl ON osls.org_static_list_id = osl.id
                    WHERE
                        osls.person_id = :studentId
                        AND osl.person_id = :facultyId
                        AND osl.organization_id = :organizationId
                        AND osls.deleted_at IS NULL
                        AND osl.deleted_at IS NULL";

        $parameters = [
            'studentId' => $studentId,
            'facultyId' => $facultyId,
            'organizationId' => $organizationId
        ];

        try {
            $staticListCount = $this->executeQueryFetch($sql, $parameters);
            return $staticListCount['static_list_count'];

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }

    /**
     * Gets the list of static list faculty
     *
     * @param int $facultyId
     * @param string|null $sortBy
     * @param int $recordsPerPage
     * @param int|null $offset
     * @param int $currentOrgAcademicYearId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStaticListsForFaculty($facultyId, $sortBy = null, $recordsPerPage, $offset = null, $currentOrgAcademicYearId)
    {
        $deterministicSort = 'osl.name, osl.id DESC';
        switch ($sortBy) {
            case 'staticlist_name':
            case '+staticlist_name':
                $orderByClause = 'ORDER BY ' . $deterministicSort;
                break;
            case '-staticlist_name':
                $orderByClause = 'ORDER BY osl.name DESC, osl.id DESC';
                break;
            case 'created_at':
            case '+created_at':
                $orderByClause = 'ORDER BY osl.created_at, ' . $deterministicSort;
                break;
            case '-created_at':
                $orderByClause = 'ORDER BY osl.created_at DESC, ' . $deterministicSort;
                break;
            case 'modified_at':
            case '+modified_at':
                $orderByClause = 'ORDER BY osl.modified_at, ' . $deterministicSort;
                break;
            case '-modified_at':
                $orderByClause = 'ORDER BY osl.modified_at DESC, ' . $deterministicSort;
                break;
            case 'student_count':
            case '+student_count':
                $orderByClause = 'ORDER BY student_count, ' . $deterministicSort;
                break;
            case '-student_count':
                $orderByClause = 'ORDER BY student_count DESC, ' . $deterministicSort;
                break;
            default:
                $orderByClause = 'ORDER BY osl.modified_at DESC, ' . $deterministicSort;
        }

        $sql = "SELECT
                    osl.id,
                    osl.name,
                    osl.description,
                    osl.created_at,
                    osl.modified_at,
                    created_by.id AS created_by_person_id,
                    created_by.firstname AS created_by_firstname,
                    created_by.lastname AS created_by_lastname,
                    modified_by.id AS modified_by_person_id,
                    modified_by.firstname AS modified_by_firstname,
                    modified_by.lastname AS modified_by_lastname,
                    COUNT(DISTINCT(ofspm.student_id)) AS student_count
                FROM
                    org_static_list osl
                        LEFT JOIN
                    org_static_list_students osls ON osl.id = osls.org_static_list_id
                                                      AND osls.deleted_at IS NULL
			LEFT JOIN
                    org_person_student_year opsy ON opsy.person_id = osls.person_id AND opsy.deleted_at IS NULL AND opsy.org_academic_year_id = :orgAcademicYearId
                        LEFT JOIN
		    (SELECT student_id FROM org_faculty_student_permission_map WHERE faculty_id = :facultyId) ofspm ON opsy.person_id = ofspm.student_id
			LEFT JOIN
                    person created_by ON osl.created_by = created_by.id
                                          AND created_by.deleted_at IS NULL
                        LEFT JOIN
                    person modified_by ON osl.modified_by = modified_by.id
                                            AND modified_by.deleted_at IS NULL
                WHERE
                    osl.person_id = :facultyId
                        AND osl.deleted_at IS NULL
                GROUP BY osl.id
                $orderByClause
                LIMIT :recordsPerPage OFFSET :offset";

        $parameters = [
            'facultyId' => $facultyId,
            'offset' => $offset,
            'recordsPerPage' => $recordsPerPage,
            'orgAcademicYearId' => $currentOrgAcademicYearId
        ];

        $parameterTypes = [
            'recordsPerPage' => \PDO::PARAM_INT,
            'offset' => \PDO::PARAM_INT
        ];

        try {
            $staticListArray = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
            return $staticListArray;

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

    }

    /**
     * Gets the count of accessible participant students in the static list specified.
     *
     * @param int $orgStaticListId
     * @param int $facultyId
     * @param int $orgAcademicYearId
     * @return int
     */
    public function getStaticListStudentCount($orgStaticListId, $facultyId, $orgAcademicYearId)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'orgAcademicYearId' => $orgAcademicYearId,
            'orgStaticListId' => $orgStaticListId
        ];

        $sql = "
            SELECT
                COUNT(DISTINCT osls.person_id) AS student_count
            FROM
                org_static_list_students osls
                    INNER JOIN
                org_faculty_student_permission_map ofspm ON osls.person_id = ofspm.student_id
                    INNER JOIN
                org_person_student_year opsy ON opsy.person_id = osls.person_id
            WHERE
                ofspm.faculty_id = :facultyId
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND osls.org_static_list_id = :orgStaticListId
                    AND osls.deleted_at IS NULL
                    AND opsy.deleted_at IS NULL;
        ";

        $results = $this->executeQueryFetchAll($sql, $parameters);
        return (int)$results[0]['student_count'];
    }

    /**
     * gets the count of static lists for a faculty
     *
     * @param int $facultyId
     * @param int $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getCountOfStaticListsForFaculty($facultyId, $organizationId)
    {
        $sql = "SELECT
                        COUNT(id) AS static_list_count
                    FROM
                        org_static_list
                    WHERE
                        person_id = :facultyId
                        AND organization_id = :organizationId
                        AND deleted_at IS NULL";

        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId
        ];

        try {
            $staticListCount = $this->executeQueryFetch($sql, $parameters);
            return $staticListCount['static_list_count'];

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
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
            ->from('SynapseStaticListBundle:' . $tname, 't')
            ->where('t.organization = :organization')
            ->andWhere('t.deletedAt IS NULL')
            ->setParameters(array(
            'organization' => $orgId
        ))
            ->getQuery()
            ->getSingleScalarResult();
        
        return $count;
    }
    
    
    public function getStaticListReferance($listId)
    {
        $em = $this->getEntityManager();
        return $em->getReference('SynapseStaticListBundle:OrgStaticList', $listId);
    }
    
    public function getAllStaticLists($organization , $person)
    {
        $filters = array(
            'orgId' => $organization,
            'person' => $person
        );
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
        ->select('sl.id')
        ->from('SynapseStaticListBundle:OrgStaticList', 'sl')
        
        ->where('sl.organization = :orgId AND sl.person = :person ' )
       
        ->setParameters($filters)
       
        ->getQuery();
        $resultSet = $qb->getArrayResult();
        return $resultSet;
    }
}
