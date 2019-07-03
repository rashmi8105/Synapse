<?php

namespace Synapse\CoreBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Entity\OrgPersonStudentYear;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

class OrgPersonStudentYearRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgPersonStudentYear';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return OrgPersonStudentYear|null
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
     * @return OrgPersonStudentYear[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return OrgPersonStudentYear|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * Given a list of student ids, returns only the ids of students which are participants in the given year.
     *
     * @param array $studentIds
     * @param int $orgId
     * @param int $orgAcademicYearId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getParticipantStudentsFromStudentList($studentIds, $orgId, $orgAcademicYearId)
    {
        $parameters = [
            'studentIds' => $studentIds,
            'orgId' => $orgId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT person_id
                FROM org_person_student_year
                WHERE organization_id = :orgId
                    AND person_id IN (:studentIds)
                    AND org_academic_year_id = :orgAcademicYearId
                    AND deleted_at IS NULL;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
            if ($results) {
                $results = array_column($results, 'person_id');
            }

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Looks up status of all students passed to the method based on current academic year.
     * Returns true or false for those students for the current academic year
     *
     * @param array $studentIds
     * @param int $organizationAcademicYearId
     * @return boolean
     * @throws SynapseDatabaseException
     */
    public function doesStudentIdListContainNonParticipants($studentIds, $organizationAcademicYearId)
    {
        $parameters = [
            'studentIds' => $studentIds,
            'orgAcademicYearId' => $organizationAcademicYearId
        ];

        $parameterTypes = [
            'studentIds' => Connection::PARAM_INT_ARRAY
        ];

        $sql = "SELECT
                    COUNT(DISTINCT person_id) AS student_count
                FROM
                    org_person_student_year
                WHERE
                    person_id IN (:studentIds)
                    AND org_academic_year_id = :orgAcademicYearId
                    AND deleted_at IS NULL;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();

        $studentCount = count($studentIds);
        $participatingStudentCount = $results[0]['student_count'];

        if ($studentCount == $participatingStudentCount) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Participating students count by active filter
     *
     * @param int $organizationId
     * @param int $organizationAcademicYearId
     * @param bool $activeStudents
     * @return int
     * @throws SynapseDatabaseException
     */
    public function getParticipantAndActiveStudents($organizationId, $organizationAcademicYearId, $activeStudents = false)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $organizationAcademicYearId
        ];
        $filterCondition = '';
        if($activeStudents) {
            $parameters['isActive'] = 1;
            $filterCondition = ' AND is_active = :isActive';
        }

        $sql = "SELECT
                    COUNT(DISTINCT person_id) AS student_count
                FROM
                    org_person_student_year
                WHERE
                    organization_id = :organizationId
                    AND org_academic_year_id = :orgAcademicYearId
                    $filterCondition
                    AND deleted_at IS NULL";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();

        if(!empty($results)) {
            $studentsCount = $results[0]['student_count'];
        }
        else {
            $studentsCount = 0;
        }

        return $studentsCount;
    }


    /**
     * Gets active status of the participant students from the list of students passed in
     *
     * @param integer $organizationId
     * @param integer $orgAcademicYearId
     * @param array $studentIds
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getActiveStatusForStudentList($organizationId, $orgAcademicYearId, $studentIds)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $orgAcademicYearId,
            'studentIds' => $studentIds
        ];

        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                   person_id, is_active
                FROM
                    org_person_student_year
                WHERE
                    organization_id = :organizationId
                    AND org_academic_year_id = :orgAcademicYearId
                    AND deleted_at IS NULL
                    AND person_id IN (:studentIds)";



        $results = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);

        $activeStatusStudentList = [];
        foreach ($results as $student) {
            $activeStatusStudentList[$student['person_id']] = $student['is_active'];
        }

        return $activeStatusStudentList;
    }
}