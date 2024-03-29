<?php
namespace Synapse\SurveyBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;

/**
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrgPersonStudentSurveyLinkRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSurveyBundle:OrgPersonStudentSurveyLink';

    // TODO: Remove this function when the /surveys/deprecated API is removed.
    public function getStudentSurveysByOrgId($orgId, $studentId, $status=NULL)
    {
		if($status) {
			$surveyCompletionStatus = $status;			
		} else {
			$surveyCompletionStatus = ReportsConstants::SURVEY_STATUS_COMPLETED;
		}
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s.id, sl.name as survey_name, ops.cohort as cohort, ops.surveyLink as survey_link, wl.openDate as open_date, wl.closeDate as close_date, ops.surveyCompletionStatus as status, ops.surveyOptOutStatus as optout_status, y.id as year')
            ->from(ReportsConstants::ORG_PERSON_STUD_SURVEY_LINK_REPO, 'ops')
			->join(ReportsConstants::SURVEY_REPO, 's', \Doctrine\ORM\Query\Expr\Join::WITH, 'ops.survey = s.id')
            ->join(ReportsConstants::SURVEY_LANG_REPO, 'sl', \Doctrine\ORM\Query\Expr\Join::WITH, 'sl.survey = s.id')
			->LEFTJoin(ReportsConstants::ORG_ACADEMIC_YEAR_REPO, 'ay', \Doctrine\ORM\Query\Expr\Join::WITH, 'ops.orgAcademicYear = ay.id')
			->LEFTJoin(ReportsConstants::YEAR_REPO, 'y', \Doctrine\ORM\Query\Expr\Join::WITH, 'y.id = ay.yearId')
			->join(ReportsConstants::WESS_LINK_REPO, 'wl', \Doctrine\ORM\Query\Expr\Join::WITH, 'wl.survey = s.id')
            ->where('ops.person = :person')
			->andWhere('ops.organization = :organization')
			->andWhere('wl.organization = :organization');
		if($status)
		{			
			$qb = $qb->andWhere('ops.surveyCompletionStatus = :status')			
            ->setParameters(array(
            'organization' => $orgId, 
			'person' => $studentId, 
			'status' => $surveyCompletionStatus
			));
		} else {
			$qb = $qb->setParameters(array(
            'organization' => $orgId, 
			'person' => $studentId			
			));
		}
		
		$qb = $qb->groupBy('sl.name')
			->orderBy('wl.openDate', 'DESC')
            ->getQuery();			
        return $qb->getArrayResult();
    }

    /**
     * Get the person_ids of the students in a survey and cohort
     * TODO: When the refactoring effort around ReportService::getSurveyStatusReportSQL() happens, use this repository method. The base query is structurally similar.
     *
     * @param int $surveyId
     * @param int $cohort
     * @param int $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStudentIdsForSurveyAndCohort($surveyId, $cohort, $organizationId)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'cohort' => $cohort,
            'organizationId' => $organizationId
        ];

        $sql = "
            SELECT
                DISTINCT opssl.person_id
            FROM
                org_person_student_survey_link opssl
                    INNER JOIN
                org_person_student_survey opss ON opssl.org_id = opss.organization_id
                    AND opss.person_id = opssl.person_id
                    AND opssl.survey_id = opss.survey_id
                    INNER JOIN
                wess_link wl ON wl.org_id = opssl.org_id
                    AND wl.survey_id = opssl.survey_id
                    INNER JOIN
                org_academic_year oay ON oay.organization_id = opssl.org_id
                    AND wl.year_id = oay.year_id
                    INNER JOIN
                org_person_student_cohort opsc ON opsc.organization_id = opssl.org_id
                    AND opssl.person_id = opsc.person_id
                    AND oay.id = opsc.org_academic_year_id
                    AND wl.cohort_code = opsc.cohort
            WHERE
                opssl.org_id = :organizationId
                AND opssl.survey_id = :surveyId
                AND opsc.cohort = :cohort
                AND (opss.receive_survey = 1 OR opssl.Has_Responses = 'Yes')
                AND opssl.deleted_at IS NULL
                AND opss.deleted_at IS NULL
                AND wl.deleted_at IS NULL
                AND oay.deleted_at IS NULL
                AND opsc.deleted_at IS NULL;
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
    
    /*
     * It will give the survey response date for the given survey based on survey id
     * @param int $surveyId
     * @param int $personId
     * @param int $orgId
     * return resultset
     */	
	public function getSurveyCompletionDate($surveyId, $personId, $orgId)
    {
		$parameters = [
            'orgId' => $orgId,
            'personId' => $personId,
            'surveyId' => $surveyId            
            ];
        /*
         * Changing this query in order to take the latest responded date
         */        
        $sql = "select max(survey_completion_date) as survey_completion_date
                from org_person_student_survey_link
                where org_id= :orgId
                    and person_id = :personId
                    and survey_id = :surveyId and deleted_at is null";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);            
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }     
        $results = $stmt->fetchAll();
        return $results;
	}

    // TODO: Remove this function when the /surveys/deprecated API is removed.
    public function getStudentCohort($studentId)
	{
	    $em = $this->getEntityManager();
	    $getCohortSql = "SELECT DISTINCT(cohort)  from org_person_student_survey_link where person_id =  :studentId  and deleted_at  IS NULL";
	    $stmt = $this->getEntityManager()
	    ->getConnection()
	    ->executeQuery($getCohortSql, [
	        'studentId' => $studentId
	        ]);
	    $resultSet = $stmt->fetchAll();
	    $resultArr = [];
	    foreach($resultSet as $result){
	        $resultArr[] = $result['cohort'];
	    }
	    return $resultArr;
	}


    /**
     * Returns data, including the number of students who have and have not responded, about each cohort-survey combination
     * for the given organization.  Only includes cohort-survey combinations where students have actually been assigned
     * to take the survey.
     * If $studentIds is set, only includes these students in the counts.
     *
     * @param int $orgId
     * @param int|null $yearId - an identifier with a format like 201516
     * @param array|null $status - an array containing "launched" or "closed" or both
     *          ("open" and "ready" are also valid statuses, but will not give useful data, as records are not put in opssl for surveys in these statuses)
     * @param boolean|null $hasResponses
     * @param array|null $studentIds
     * @return array
     */
    public function getCohortsAndSurveysForOrganizationForReporting($orgId, $yearId = null, $status = null, $hasResponses = null, $studentIds = null)
    {
        $parameters = ['orgId' => $orgId];
        $parameterTypes = [];

        if ($yearId) {
            $yearString = 'AND wl.year_id = :yearId';
            $parameters['yearId'] = $yearId;
        } else {
            $yearString = '';
        }

        if ($status) {
            $statusString = 'AND wl.status IN (:status)';
            $parameters['status'] = $status;
            $parameterTypes['status'] = Connection::PARAM_STR_ARRAY;
        } else {
            $statusString = '';
        }

        if ($hasResponses) {
            $responsesString = "AND opssl.Has_Responses = 'Yes'";
        } else {
            $responsesString = '';
        }

        if ($studentIds) {
            $studentsString = 'AND opssl.person_id IN (:studentIds)';
            $parameters['studentIds'] = $studentIds;
            $parameterTypes['studentIds'] = Connection::PARAM_INT_ARRAY;
        } else {
            $studentsString = '';
        }

        $sql = "SELECT wl.year_id,
                  opsc.cohort,
                  ocn.cohort_name,
                  wl.survey_id,
                  sl.name AS survey_name,
                  opssl.Has_Responses,
                  wl.status,
                  wl.open_date,
                  wl.close_date,
                  COUNT(*) AS student_count
                FROM wess_link wl
                INNER JOIN survey_lang sl
                  ON sl.survey_id = wl.survey_id
                INNER JOIN org_person_student_survey_link opssl
                  ON opssl.org_id = wl.org_id
                  AND opssl.survey_id = wl.survey_id
                  AND opssl.cohort = wl.cohort_code
                INNER JOIN org_person_student_cohort opsc
                  ON opsc.organization_id = wl.org_id
	              AND opsc.person_id = opssl.person_id
	              AND opsc.cohort = wl.cohort_code
	              AND opsc.org_academic_year_id = opssl.org_academic_year_id
	            INNER JOIN org_cohort_name ocn
	              ON ocn.organization_id = wl.org_id
	              AND ocn.cohort = opsc.cohort
                  AND ocn.org_academic_year_id = opsc.org_academic_year_id
                WHERE wl.deleted_at IS NULL
                  AND sl.deleted_at IS NULL
                  AND opssl.deleted_at IS NULL
                  AND opsc.deleted_at IS NULL
                  AND ocn.deleted_at IS NULL
                  AND wl.org_id = :orgId
                  $yearString
                  $statusString
                  $responsesString
                  $studentsString
                GROUP BY wl.year_id, opsc.cohort, wl.survey_id, opssl.Has_Responses
                ORDER BY wl.year_id, opsc.cohort, wl.survey_id, opssl.Has_Responses;";

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
     * Lists all surveys that the student has been assigned, along with lots of metadata,
     * such as survey status and whether the student has responded.
     * If $orgAcademicYearId is set, only includes surveys from that year.
     * If $hasResponses is true, only includes surveys the student has responded to.
     *
     * @param int $studentId
     * @param int $orgId
     * @param int|null $orgAcademicYearId
     * @param bool|null $hasResponses
     * @return array
     */
    public function listSurveysForStudent($studentId, $orgId, $orgAcademicYearId = null, $hasResponses = null)
    {
        $parameters = [
            'studentId' => $studentId,
            'orgId' => $orgId
        ];

        if (isset($orgAcademicYearId)) {
            $yearString = 'AND opssl.org_academic_year_id = :orgAcademicYearId';
            $parameters['orgAcademicYearId'] = $orgAcademicYearId;
        } else {
            $yearString = '';
        }

        if ($hasResponses) {
            $respondedString = 'AND opssl.Has_Responses = "Yes"';
        } else {
            $respondedString = '';
        }

        $sql = "SELECT
                    opssl.survey_id,
                    sl.name AS survey_name,
                    opssl.cohort,
                    ocn.cohort_name,
                    wl.year_id,
                    opssl.org_academic_year_id,
                    oay.name AS year_name,
                    wl.open_date,
                    wl.close_date,
                    wl.status AS survey_status,
                    opssl.survey_completion_status,
                    opssl.Has_Responses AS has_responses,
                    opssl.survey_completion_date,
                    opssl.survey_link
                FROM
                    wess_link wl
                        INNER JOIN
                    survey_lang sl
                            ON sl.survey_id = wl.survey_id
                        INNER JOIN
                    org_person_student_survey_link opssl
                            ON opssl.survey_id = wl.survey_id
                            AND opssl.cohort = wl.cohort_code
                            AND opssl.org_id = wl.org_id
                        INNER JOIN
                    org_cohort_name ocn
                            ON ocn.cohort = opssl.cohort
                            AND ocn.organization_id = opssl.org_id
                            AND ocn.org_academic_year_id = opssl.org_academic_year_id
                        INNER JOIN
                    org_academic_year oay
                            ON oay.id = opssl.org_academic_year_id
                            AND oay.organization_id = opssl.org_id
                WHERE wl.deleted_at IS NULL
                    AND sl.deleted_at IS NULL
                    AND opssl.deleted_at IS NULL
                    AND ocn.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                    AND wl.org_id = :orgId
                    AND opssl.person_id = :studentId
                    $yearString
                    $respondedString
                ORDER BY wl.open_date DESC;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();

        return $records;
    }



}