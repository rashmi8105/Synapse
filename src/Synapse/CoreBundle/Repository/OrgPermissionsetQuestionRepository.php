<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use Synapse\RestBundle\Exception\ValidationException;


class OrgPermissionsetQuestionRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgPermissionsetQuestion';

    /**
     * @DI\Inject("logger")
     */
    private $logger;

    public function remove($entity)
    {
        $this->getEntityManager()->remove($entity);
    }

    public function getIsqsByPermissionSet($permissionsetId, $organizationId)
    {
        try {
            $em = $this->getEntityManager();
            
            // Had to use regular SQL because DQL does not support composite keys.
            // http://www.doctrine-project.org/jira/browse/DDC-726
            /*
             * $dql = <<<DQL SELECT omd, partial opmd.{id} FROM SynapseCoreBundle:OrgMetadata omd LEFT JOIN SynapseCoreBundle:OrgPermissionsetMetadata opmd WITH (opmd.orgMetadata=omd.organization) WHERE omd.organization=:organizationId AND (opmd.id IS NULL OR opmd.orgPermissionset=:permissionsetId) DQL;
             */
            
            $sql = <<<SQL
SELECT oq.id isqId, oq.question_key isqLabel, (opq.id IS NOT NULL) isqSelection, opq.survey_id as surveyId, opq.cohort_code as cohortId, question_text as questionText
FROM org_question oq
LEFT JOIN org_permissionset_question opq
  ON opq.org_question_id=oq.id AND opq.organization_id=oq.organization_id
  AND opq.org_permissionset_id=:permissionsetId
WHERE oq.organization_id=:organizationId
AND oq.deleted_at IS NULL AND opq.deleted_at IS NULL
SQL;
            $stmt = $this->getEntityManager()
                ->getConnection()
                ->prepare($sql);
            $stmt->execute([
                'organizationId' => $organizationId,
                'permissionsetId' => $permissionsetId
            ]);
            
            $data = $stmt->fetchAll();
        } catch (\Exception $e) {          
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return $data;
    }


    /**
     * This function will return true if at least one
     * permissionset the user has has access to
     * all current and future isqs. Access to future
     * ISQ is determined by current_future_isq being
     * set to one in the org_permissionset table.
     *
     * @param array $permissionsetId - an array of permissionset Id records from the database
     * @param bool $shouldAllowAggregatePermissionsets
     * @return boolean
     */
    public function permissionsetsHaveAccessToEveryCurrentAndFutureISQ($permissionsetIds, $shouldAllowAggregatePermissionsets){

        if($permissionsetIds){
            $parameters['permissionsetIds'] = $permissionsetIds;
            $parameterTypes = ['permissionsetIds' => Connection::PARAM_INT_ARRAY];

        } else {
            return false;
        }

        if ($shouldAllowAggregatePermissionsets) {
            $aggSQL = "";
        }
        else {
            $aggSQL = " and op.accesslevel_ind_agg = 1 ";
        }

        $sql = "SELECT
                        IF(SUM(op.current_future_isq) >= 1,
                            1,
                            0) AS `has_access`
                    FROM
                        org_permissionset op
                    WHERE
                        id IN (:permissionsetIds)
                           $aggSQL
                          limit 1";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $records = $stmt->fetchAll();

        if($records['0']['has_access'] == '1') {
            return true;
        }else{
            return false;
        }
    }

    /**
     * This gets a filter ISQs based off of a
     * the cohorts and surveys the ISQs are in
     *
     * @param string $cohort
     * @param Array $orgQuestions
     * @param string $survey
     * @return array
     */
    public function filterISQsBasedOffOfCohortSurveyAndOrgQuestions($orgQuestions, $cohort = null, $survey = null)
    {
        if(!empty($cohort)){
            $cohortSQL = " AND survey_questions.cohort_code = :cohortId";
            $parameters['cohortId'] = $cohort;
        } else {
            $cohortSQL = "";
        }

        $orgQuestionsSQL =  " AND org_question.id IN (:orgQuestions)";
        $parameters['orgQuestions'] = $orgQuestions;
        $parameterTypes = ['orgQuestions' => Connection::PARAM_INT_ARRAY];

        if(!empty($survey)){
            $surveySQL =  " AND survey_questions.survey_id = :survey";
            $parameters['survey'] = $survey;
        } else {
            $surveySQL = "";
        }


        $sql = "SELECT
                        org_question.id
                    FROM
                        org_question
                            INNER JOIN
                        survey_questions ON survey_questions.org_question_id = org_question.id
                    WHERE
                      org_question.deleted_at is NULL
                        AND survey_questions.deleted_at is NULL
                        $cohortSQL
                        $orgQuestionsSQL
                        $surveySQL
                        ;";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $records = $stmt->fetchAll();
        return $records;
    }

    public function getOrgQuestionsByPermissionSet($permissionsetId, $orgQuestions = '')
    {
		$em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('DISTINCT(oq.orgQuestion) as org_question');
        $qb->from('SynapseCoreBundle:OrgPermissionsetQuestion', 'oq');
        if(empty($orgQuestions))
		{
			$qb->where('oq.orgPermissionset IN (:permissionset)');        
			$qb->setParameters(array(
				'permissionset' => $permissionsetId,           
			));
		} else {
			$qb->where('oq.orgPermissionset IN (:permissionset)');        
			$qb->andWhere('oq.orgQuestion IN (:orgQuestions)'); 
			$qb->setParameters(array(
				'permissionset' => $permissionsetId,
				'orgQuestions'  => $orgQuestions
			));
		}
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
		return $resultSet;
	}
    
    public function updateIsqSurveyCohart($org_question_id, $survey_id, $cohort_code) {
        try {
            $em = $this->getEntityManager();
            $sql = "Update org_permissionset_question set survey_id = ".$survey_id.", cohort_code = ".$cohort_code." where org_question_id = ".$org_question_id." and survey_id is null and cohort_code is null and deleted_at is null";

            $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare( $sql);

            $stmt->execute();
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
                ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
    }

    /**
     * Gets the org questions based off of an array of permissionsets.
     * Will Also check to see if the permissionsets are aggregate sets
     * and if the function should include any permissionsets that are
     * aggregate only
     *
     * @param Array $permissionsetIDs => array of permissionsets Ids that the person uses
     * @param bool $shouldAllowAggregatePermissionsets => include ind_and_agg or just Agg permissionsets
     * @param null|Array $orgQuestions => ISQs to test against
     * @param null|int $cohortId => filter ISQs by cohort
     * @param null|int $surveyId => filter ISQs by survey
     * @return array
     */
    public function getOrgQuestionsByPermissionsets($permissionsetIDs, $shouldAllowAggregatePermissionsets, $orgQuestions = null, $cohortId = null,  $surveyId = null)
    {

        $parameterTypes = [];
        if(!empty($orgQuestions))
        {
            $orgQuestionsSQL = " and org_question_id IN (:orgQuestions) ";
            $parameters['orgQuestions'] = explode(',',$orgQuestions);
            $parameterTypes['orgQuestions'] = Connection::PARAM_INT_ARRAY;

        }else{
            $orgQuestionsSQL = '';
        }

        if (! empty($permissionsetIDs))
        {
            $permissionsetIDSQL = " and ops.id IN (:permissionsetIds) ";
            $parameters['permissionsetIds'] = array_filter($permissionsetIDs);
            $parameterTypes['permissionsetIds'] = Connection::PARAM_INT_ARRAY;

        }else{
            return array();
        }

        if(!empty($cohortId)){

            $cohortCond = " and cohort_code = :cohortCond ";
            $parameters['cohortCond'] = $cohortId;
        }else{
            $cohortCond = " ";
        }

        if($shouldAllowAggregatePermissionsets){
            $indAndAggregate = "";
        }else{

            $indAndAggregate = " and ops.accesslevel_ind_agg = 1 ";
        }
        if(!empty($surveyId)){
            $surveyIdSQL = " and survey_id = :surveyId ";
            $parameters['surveyId'] = $surveyId;
        }else{
            $surveyIdSQL = " ";

        }


        $sql = "SELECT distinct(org_question_id)
            FROM
                org_permissionset_question opq
                inner join
                org_permissionset ops on ( ops.id = opq.org_permissionset_id )
            where
                opq.deleted_at is null
                $indAndAggregate
                $cohortCond
                $orgQuestionsSQL
                $permissionsetIDSQL
                $surveyIdSQL
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
     * @param int $personId - The person for which we wish to look up ISQ permissions for
     * @param int $orgId - The organization the person belongs to
     * @param array|string $studentIds - The student ids that we wish to look up for the given person ID
     *                                   (This will also accept a string in the format "id, id, id... etc")
     * @return array - ['0'=>['student_id'=>int, 'org_question_id'=>int]]
     */
    public function getStudentsAnsweredOrgQuestionPermission($personId, $orgId, $studentIds = [])
    {
        // the query needs personId and orgId to work
        if(!$personId || !$orgId){
            return array();
        }

        $parameters = ['orgId'=>$orgId, 'personId'=>$personId];

        // does not need studentIds, will just grab all ids
        if(!empty($studentIds)) {

            // will accept strings in the format "id, id, ... "
            if(!is_array($studentIds)){
                $studentIds = explode(",", $studentIds);
            }
            $student_list = "
            AND ofspm.student_id in (:studentIds)
            ";
            $parameterType = ['studentIds'=>Connection::PARAM_INT_ARRAY];
            $parameters['studentIds'] = $studentIds;
        } else {
            $parameterType = [];
        }

        try {
            $em = $this->getEntityManager();
            $sql = "
			SELECT
                student_cfi.student_id,
                oqr.org_question_id
            FROM
                (SELECT
                    ofspm.student_id,
                    MAX(op.current_future_isq) AS cfi,
                    ofspm.org_id as organization_id
                FROM
                    org_faculty_student_permission_map ofspm
                INNER JOIN 
                    org_permissionset op ON op.id = ofspm.permissionset_id
				        AND ofspm.org_id = op.organization_id
                WHERE
                    ofspm.faculty_id = :personId
                    AND ofspm.org_id = :orgId
                    AND op.deleted_at IS NULL
                    $student_list
                GROUP BY student_id) AS student_cfi
            INNER JOIN
                org_question_response oqr ON oqr.org_id = student_cfi.organization_id
                    AND oqr.person_id = student_cfi.student_id
            WHERE
                oqr.deleted_at IS NULL
                    AND student_cfi.cfi = 1
            UNION 
                SELECT
                    student_id, oqr.org_question_id
                FROM
                    (SELECT
                        student_id,
                        permissionset_id,
                        current_future_isq AS cfi,
                        ofspm.org_id as organization_id
                    FROM
                        org_faculty_student_permission_map ofspm
                    INNER JOIN 
                        org_permissionset op ON op.id = ofspm.permissionset_id                         
                            AND ofspm.org_id = op.organization_id
                    WHERE
                        ofspm.faculty_id = :personId
                        AND ofspm.org_id = :orgId
                        AND (op.current_future_isq != 1
                            OR op.current_future_isq IS NULL)
                        AND op.deleted_at IS NULL
                        $student_list
                    GROUP BY student_id, permissionset_id) AS student_cfi
                INNER JOIN
                    org_permissionset_question opq ON opq.organization_id = student_cfi.organization_id
                        AND opq.org_permissionset_id = student_cfi.permissionset_id
                INNER JOIN
                    org_question_response oqr ON oqr.org_id = student_cfi.organization_id
                        AND oqr.person_id = student_cfi.student_id
                        AND oqr.org_question_id = opq.org_question_id
                WHERE
                     oqr.deleted_at IS NULL
                        AND opq.deleted_at IS NULL
			; ";
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterType);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $stmt->fetchAll();
    }


    /**
     * Get Student Id List Based on Connection With Student And Permission to Org Questions
     * Note: Only Brings back students who have responded to the Org Question
     * Note: allows filtering by a set of student ids
     *
     * @param int $personId
     * @param int $orgId
     * @param int $surveyId
     * @param array $studentIds
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStudentIdsBasedOnFacultyOrgQuestionPermission($personId, $orgId, $surveyId, $orgQuestionId, $studentIds = [])
    {
        // the query needs all in order to work, don't bother if a piece is missing
        if(!$personId || !$orgId || !$surveyId || !$orgQuestionId ){
            return array();
        }

        $parameters = [
            'orgId' => $orgId,
            'personId' => $personId,
            'surveyId' => $surveyId,
            'orgQuestionId' => $orgQuestionId];

        $studentCondition = "";
        if(!empty($studentIds)) {
            $studentCondition = "AND ofspm.student_id in (:studentIds)";
            $parameterType = ['studentIds' => Connection::PARAM_INT_ARRAY];
            $parameters['studentIds'] = $studentIds;
        } else {
            $parameterType = [];
        }

        try {
            $sql = "SELECT
                        DISTINCT ofspm.student_id
                    FROM
                        org_faculty_student_permission_map ofspm
                    INNER JOIN
                        org_permissionset_question opq ON opq.organization_id = ofspm.org_id
                        AND opq.org_permissionset_id = ofspm.permissionset_id
                    INNER JOIN
                        org_person_student_cohort opsc ON opsc.person_id = ofspm.student_id
                        AND opsc.organization_id = ofspm.org_id
                    INNER JOIN
                        wess_link wl ON wl.cohort_code = opsc.cohort
                        AND wl.org_id = opsc.organization_id
                    INNER JOIN
                        org_question oq ON wl.survey_id = oq.survey_id
                        AND wl.cohort_code = oq.cohort
                        AND opq.org_question_id = oq.id
                    WHERE
                        ofspm.faculty_id = :personId
                        AND ofspm.org_id = :orgId
                        AND oq.survey_id = :surveyId
                        AND opq.org_question_id = :orgQuestionId
                        AND opq.deleted_at IS NULL
                        AND opsc.deleted_at IS NULL
                        AND wl.deleted_at IS NULL
                        AND oq.deleted_at IS NULL
                        $studentCondition";

            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, $parameters, $parameterType);
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

}