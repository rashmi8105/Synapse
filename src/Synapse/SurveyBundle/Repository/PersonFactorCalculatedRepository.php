<?php
namespace Synapse\SurveyBundle\Repository;


use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;

class PersonFactorCalculatedRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSurveyBundle:PersonFactorCalculated';

    /**
     * Calculates the correlations between the given profile item (ebi_metadata) and each of the factors on the given survey
     * for the given students who are also in the given cohort.
     * Returns a list of the 5 factors which have the highest correlations (in absolute value).
     * For a year/term-specific profile item, only uses values from the given year/term.
     *
     * @param array $studentIds
     * @param int $surveyId
     * @param int $cohort
     * @param int $orgAcademicYearIdForCohort
     * @param int $ebiMetadataId
     * @param int|null $orgAcademicYearIdForEbiMetadata
     * @param int|null $orgAcademicTermsId
     * @return array
     */
    public function getTopFactorsCorrelatedWithEbiMetadata($studentIds, $surveyId, $cohort, $orgAcademicYearIdForCohort, $ebiMetadataId, $orgAcademicYearIdForEbiMetadata = null, $orgAcademicTermsId = null)
    {
        $parameters = [
            'studentIds' => $studentIds,
            'surveyId' => $surveyId,
            'cohort' => $cohort,
            'orgAcademicYearIdForCohort' => $orgAcademicYearIdForCohort,
            'ebiMetadataId' => $ebiMetadataId
        ];

        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        if (isset($orgAcademicYearIdForEbiMetadata)) {
            $yearSQLsubstring = "AND pem.org_academic_year_id = :orgAcademicYearIdForEbiMetadata";
            $parameters['orgAcademicYearIdForEbiMetadata'] = $orgAcademicYearIdForEbiMetadata;
        } else {
            $yearSQLsubstring = "";
        }

        if (isset($orgAcademicTermsId)) {
            $termSQLsubstring = "AND pem.org_academic_terms_id = :orgAcademicTermsId";
            $parameters['orgAcademicTermsId'] = $orgAcademicTermsId;
        } else {
            $termSQLsubstring = "";
        }

        $sql = "SELECT pfc1.factor_id, fl.name,
                ABS((AVG(pfc1.mean_value * pem.metadata_value) - AVG(pfc1.mean_value) * AVG(pem.metadata_value)) / (STD(pfc1.mean_value) * STD(pem.metadata_value))) AS correlation
                FROM person_ebi_metadata pem
                INNER JOIN person_factor_calculated pfc1
                    ON pfc1.person_id = pem.person_id
                INNER JOIN
                (
                    SELECT person_id, factor_id, survey_id, MAX(modified_at) AS modified_at
                    FROM person_factor_calculated
                    WHERE deleted_at IS NULL
                      AND person_id IN (:studentIds)
                    GROUP BY person_id, factor_id, survey_id
                ) AS pfc2
                    ON pfc1.person_id = pfc2.person_id
                    AND pfc1.factor_id = pfc2.factor_id
                    AND pfc1.survey_id = pfc2.survey_id
                    AND pfc1.modified_at = pfc2.modified_at
                INNER JOIN factor_lang fl
                    ON fl.factor_id = pfc1.factor_id
                INNER JOIN org_person_student_survey_link opssl
                    ON opssl.org_id = pfc1.organization_id
                    AND opssl.person_id = pem.person_id
                    AND opssl.survey_id = pfc1.survey_id
                INNER JOIN org_person_student_cohort opsc
                    ON opsc.organization_id = pfc1.organization_id
                    AND opsc.person_id = pem.person_id
                    AND opsc.cohort = opssl.cohort
                    AND opsc.org_academic_year_id = opssl.org_academic_year_id
                WHERE pem.deleted_at IS NULL
                    AND opssl.deleted_at IS NULL
                    AND opsc.deleted_at IS NULL
                    AND pem.ebi_metadata_id = :ebiMetadataId
                    $yearSQLsubstring
                    $termSQLsubstring
                    AND pfc1.survey_id = :surveyId
                    AND opsc.cohort = :cohort
                    AND opsc.org_academic_year_id = :orgAcademicYearIdForCohort
                GROUP BY pfc1.factor_id
                HAVING correlation IS NOT NULL
                ORDER BY correlation DESC
                LIMIT 5;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();
        return $results;
    }

    /**
     * Get the student IDs with a calculated factor value between the minimum and maximum values specified.
     *
     * @param int $factorId
     * @param int $surveyId
     * @param array $studentIds
     * @param int|null $minimumFactorValue
     * @param int|null $maximumFactorValue
     * @return array
     */
    public function getStudentsWithCalculatedFactorValueWithinRange($factorId, $surveyId, $studentIds, $minimumFactorValue = null, $maximumFactorValue = null)
    {
        $parameters = [
            'factorId' => $factorId,
            'surveyId' => $surveyId,
            'studentIds' => $studentIds
        ];

        if ($minimumFactorValue && $maximumFactorValue) {
            $parameters['minimumFactorValue'] = $minimumFactorValue;
            $parameters['maximumFactorValue'] = $maximumFactorValue;
            $factorCalculatedValueRangeCondition = ' AND mean_value >= :minimumFactorValue AND mean_value < :maximumFactorValue ';
        } else {
            $factorCalculatedValueRangeCondition = '';
        }

        $parameterTypes = [
            'studentIds' => Connection::PARAM_INT_ARRAY
        ];

        $sql = "
            SELECT
                DISTINCT person_id
            FROM
                person_factor_calculated
            WHERE
                factor_id = :factorId
                AND survey_id = :surveyId
                AND person_id IN (:studentIds)
                $factorCalculatedValueRangeCondition
                AND deleted_at IS NULL;
        ";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $records = $stmt->fetchAll();

            if (!empty($records)) {
                $records = array_column($records, 'person_id');
            }

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $records;
    }


    /**
     * Get the Top Factors Correlated to Persistence , defaulting limit to 5
     *
     * @param array $studentIds
     * @param integer $surveyId
     * @param integer $cohort
     * @param integer $orgAcademicYearId
     * @param integer $yearsFromRetentionTrack
     * @param bool $isMidYear
     * @param integer $limit
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getTopFactorsCorrelatedToPersistence($studentIds, $surveyId, $cohort, $orgAcademicYearId, $yearsFromRetentionTrack, $isMidYear , $limit = 5 )
    {

        $parameters = [
            'studentIds' => $studentIds,
            'surveyId' => $surveyId,
            'cohort' => $cohort,
            'orgAcademicYearId' => $orgAcademicYearId,
            'yearsFromRetentionTrack' => $yearsFromRetentionTrack,
            'limit' => $limit
        ];

        $parameterTypes = [
                'studentIds' => Connection::PARAM_INT_ARRAY,
                'limit' => \PDO::PARAM_INT
        ];


        if ($isMidYear) {
            $beginningOrMidYearColumn = " opsrbtgv.is_enrolled_midyear ";
        } else {
            $beginningOrMidYearColumn = " opsrbtgv.is_enrolled_beginning_year ";
        }


        $sql = "SELECT
                    pfc.factor_id,
                    fl.name,
                    ABS((AVG(pfc.mean_value * $beginningOrMidYearColumn) - AVG(pfc.mean_value) * AVG($beginningOrMidYearColumn)) / (STD(pfc.mean_value) * STD($beginningOrMidYearColumn))) AS correlation
                FROM 
                  org_person_student_retention_by_tracking_group_view opsrbtgv
                INNER JOIN 
                  person_factor_calculated pfc ON pfc.person_id = opsrbtgv.person_id 
                    AND pfc.organization_id = opsrbtgv.organization_id
                INNER JOIN 
                  factor_lang fl ON fl.factor_id = pfc.factor_id
                INNER JOIN 
                  org_person_student_survey_link opssl ON opssl.org_id = pfc.organization_id 
                    AND opssl.person_id = opsrbtgv.person_id
                    AND opssl.survey_id = pfc.survey_id
                INNER JOIN 
                  org_person_student_cohort opsc ON opsc.organization_id = pfc.organization_id
                    AND opsc.person_id = opsrbtgv.person_id
                    AND opsc.cohort = opssl.cohort
                    AND opsc.org_academic_year_id = opssl.org_academic_year_id
                WHERE 
                    opsrbtgv.years_from_retention_track = :yearsFromRetentionTrack 
                    AND opsrbtgv.person_id IN (:studentIds) 
                    AND pfc.survey_id = :surveyId 
                    AND opsc.cohort = :cohort
                    AND opsc.org_academic_year_id = :orgAcademicYearId
                    AND opssl.deleted_at IS NULL
                    AND opsc.deleted_at IS NULL
                    AND pfc.deleted_at IS NULL
                    AND fl.deleted_at IS NULL
                GROUP BY pfc.factor_id
                HAVING correlation IS NOT NULL
                ORDER BY correlation DESC
                LIMIT :limit";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $records = $stmt->fetchAll();
            return $records;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }
}