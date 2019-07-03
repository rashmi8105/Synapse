<?php
namespace Synapse\SearchBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;

class IntentToLeaveRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSearchBundle:IntentToLeave';

    /**
     * Returns an array of student ids for students from the given list who are in the given cohort for the given year
     * and answered that they intend to leave on the given survey.
     *
     * @param int $surveyId
     * @param int $cohort
     * @param int $orgAcademicYearId
     * @param array $studentIds
     * @return array
     */
    public function getStudentsWhoIntendToLeave($surveyId, $cohort, $orgAcademicYearId, $studentIds)
    {
        $parameters = [
            'intentToLeaveQnbr' => 4,
            'surveyId' => $surveyId,
            'cohort' => $cohort,
            'orgAcademicYearId' => $orgAcademicYearId,
            'studentIds' => $studentIds
        ];

        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT sr.person_id
                FROM survey_response sr
                INNER JOIN survey_questions sq
                  ON sq.id = sr.survey_questions_id
                INNER JOIN intent_to_leave itl
                  ON sr.decimal_value BETWEEN itl.min_value AND itl.max_value
                INNER JOIN org_person_student_survey_link opssl
                  ON opssl.org_id = sr.org_id
                  AND opssl.person_id = sr.person_id
                  AND opssl.survey_id = sr.survey_id
                INNER JOIN org_person_student_cohort opsc
                  ON opsc.organization_id = sr.org_id
                  AND opsc.person_id = sr.person_id
                  AND opsc.cohort = opssl.cohort
                  AND opsc.org_academic_year_id = opssl.org_academic_year_id
                WHERE sr.deleted_at IS NULL
                  AND sq.deleted_at IS NULL
                  AND itl.deleted_at IS NULL
                  AND opssl.deleted_at IS NULL
                  AND opsc.deleted_at IS NULL
                  AND sq.qnbr = :intentToLeaveQnbr
                  AND itl.text = 'red'
                  AND sr.survey_id = :surveyId
                  AND opsc.cohort = :cohort
                  AND opsc.org_academic_year_id = :orgAcademicYearId
                  AND sr.person_id IN (:studentIds);";

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
}