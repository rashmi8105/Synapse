<?php
namespace Synapse\SurveyBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\Util\Constants\SearchConstant;

class SurveyQuestionsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSurveyBundle:SurveyQuestions';

    /**
     * Returns a list of all survey questions on the given survey, excluding short answer and long answer questions,
     * along with the options available for each question.
     *
     * @param int $surveyId
     * @return array
     */
    public function getSurveyQuestions($surveyId)
    {
        $parameters = [
            'surveyId' => $surveyId
        ];

        $sql = "SELECT DISTINCT
                    sq.id AS survey_questions_id,
                    sq.ebi_question_id,
                    eq.question_type_id,
                    IF(eql.question_text > '', CONCAT(eql.question_text, ' ', eql.question_rpt), eql.question_rpt) AS question_text,
                    eqo.id AS option_id,
                    eqo.option_text,
                    eqo.option_value
                FROM
                    survey_questions sq
                        INNER JOIN
                    ebi_question eq
                            ON eq.id = sq.ebi_question_id
                        INNER JOIN
                    ebi_questions_lang eql
                            ON eql.ebi_question_id = eq.id
                        LEFT JOIN
                    ebi_question_options eqo
                            ON eqo.ebi_question_id = sq.ebi_question_id        
                WHERE
                    sq.survey_id = :surveyId
                    AND eq.question_type_id NOT IN ('LA','SA')
                    AND sq.deleted_at IS NULL
                    AND eq.deleted_at IS NULL
                    AND eql.deleted_at IS NULL
                    AND eqo.deleted_at IS NULL;";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();

        return $results;
    }
    /**
     * Returns a list of all ISQ questions on the given survey along with the options available for each question.
     *
     * @param int $orgId
     * @param string $currentDate
     * @param int $surveyId
     * @param int $langId
     * @param string $cohortId
     * @return array
     */
    public function getIsqWithOptions($orgId, $currentDate, $surveyId, $langId, $cohortId = '')
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('sq.id as survey_ques_id', 'IDENTITY(sq.orgQuestion) as org_ques_id', 'IDENTITY(oq.questionType) as question_type', 'oq.questionText as ques_text', 'oqo.id as option_id', 'oqo.optionName as option_text', 'oqo.optionValue as option_value');
        $qb->from('SynapseSurveyBundle:SurveyQuestions', 'sq');
        $qb->LEFTJoin('SynapseCoreBundle:OrgQuestion', 'oq', \Doctrine\ORM\Query\Expr\Join::WITH, 'oq.id = sq.orgQuestion');
        $qb->LEFTJoin('SynapseCoreBundle:OrgQuestionOptions', 'oqo', \Doctrine\ORM\Query\Expr\Join::WITH, 'oqo.orgQuestion = oq.id');
        $qb->LEFTJoin('SynapseAcademicBundle:OrgAcademicYear', 'oay', \Doctrine\ORM\Query\Expr\Join::WITH, 'oay.organization = oq.organization');
        $qb->where('oq.organization = :orgId');
        $qb->andWhere('oay.startDate <= :currDate');
        $qb->andWhere('oay.endDate >= :currDate');
        $qb->andWhere('sq.survey = :surveyId');
        $qb->andWhere("oq.questionType NOT IN ('LA','SA','CS','PA','PC','RO')");
        if (!empty($cohortId)) {

            $qb->andWhere("sq.cohortCode = :cohortId");
            $qb->setParameters(array(
                'orgId' => $orgId,
                'currDate' => $currentDate,
                'surveyId' => $surveyId,
                'cohortId' => $cohortId
            ));
        } else {

            $qb->setParameters(array(
                'orgId' => $orgId,
                'currDate' => $currentDate,
                'surveyId' => $surveyId,
            ));
        }

        $qb->groupBy('org_ques_id, option_id');
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
    
    public function getQuestionsForSurvey($surveyId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('sq.id as survey_ques_id', 'sq.type as type', 'eql.questionRpt as ebi_ques_text', 'IDENTITY(eq.questionType) as ebi_question_type', 'IDENTITY(eql.ebiQuestion) as ebi_question_id', 'IDENTITY(eq.questionType) as question_type','sq.qnbr');
        $qb->from('SynapseSurveyBundle:SurveyQuestions', 'sq');
        $qb->LEFTJoin('SynapseCoreBundle:EbiQuestion', 'eq', \Doctrine\ORM\Query\Expr\Join::WITH, 'eq.id = sq.ebiQuestion');
        $qb->LEFTJoin('SynapseCoreBundle:EbiQuestionsLang', 'eql', \Doctrine\ORM\Query\Expr\Join::WITH, 'eql.ebiQuestion = eq.id');              
        $qb->andWhere('sq.survey = :surveyId');        
        $qb->andWhere('sq.ebiQuestion IS NOT NULL');   
        $qb->andWhere("eq.questionType IN ('Q','D', 'MR', 'N', 'LA', 'SA')");
        $qb->setParameters(array(            
            'surveyId' => $surveyId           
        ));

        $qb->orderBy('sq.qnbr+0', 'ASC');
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
    
    public function getOptionsForSurveyQuestions($surveyId, $questionId)
    {    
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('eqo.id as ebi_option_id', 'eqo.optionText as ebi_option_text', 'eqo.optionValue as ebi_option_value');
        $qb->from('SynapseSurveyBundle:SurveyQuestions', 'sq');
        $qb->LEFTJoin('SynapseCoreBundle:EbiQuestionOptions', 'eqo', \Doctrine\ORM\Query\Expr\Join::WITH, 'eqo.ebiQuestion = sq.ebiQuestion');        
        $qb->andWhere('sq.survey = :surveyId');        
        $qb->andWhere('sq.ebiQuestion = :questionId');
        $qb->setParameters(array(            
            'surveyId' => $surveyId,
            'questionId' => $questionId
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
    
    public function getDataBlockQuestionsBasedPermission($orgId, $personId, $surveyId, $surveyQuestions, $isAggregate = false)
    {
        if($isAggregate){
            
            $aggregateCond = ' and ( ops.accesslevel_ind_agg = 1 or ops.accesslevel_agg = 1 ) ';
            $datablockField = ' , dbq.datablock_id ';
        }else {
            
            $aggregateCond = ' and ops.accesslevel_ind_agg = 1 ';
            $datablockField = ' ';
        }
        
        if(empty($surveyQuestions)){
            
            $surveyQues = " ";
        }else{
            
            $surveyQues = " and sq.id IN ($surveyQuestions) ";
        }
        try {
            $em = $this->getEntityManager();
            $sql = "
                select
                    (sq.ebi_question_id) $datablockField
                from
                    survey_questions sq
                        inner join
                    datablock_questions dbq on (dbq.ebi_question_id = sq.ebi_question_id
                            and (dbq.survey_id = sq.survey_id or dbq.survey_id is null)
                            and dbq.deleted_at is null
                            and dbq.ebi_question_id is not null)
                        inner join
                    org_permissionset_datablock opd ON (opd.datablock_id = dbq.datablock_id
                        and opd.organization_id = [ORG_ID]
                        and opd.deleted_at is null
                        and opd.block_type = 'survey')
                        inner join

                    (select 
                        ogf.org_permissionset_id as permissionset_id
                    from
                        org_group_faculty ogf
                    where
                        ogf.person_id = [FACULTY_ID]
                            and ogf.organization_id = [ORG_ID]
                            and ogf.deleted_at is null 
                    UNION ALL 
                    select 
                            ocf.org_permissionset_id as permissionset_id
                    from
                        org_course_faculty ocf
                        inner join org_courses oc ON oc.id = ocf.org_courses_id
                        inner join org_academic_terms AS oat ON (oat.id = oc.org_academic_terms_id
                            AND DATE(now()) between oat.start_date and oat.end_date)
                    where
                        ocf.organization_id = [ORG_ID]
                           and oat.organization_id = [ORG_ID]
                           and ocf.person_id = [FACULTY_ID]
                           and ocf.deleted_at is null
                           and oat.deleted_at is null
                    ) as my_permissions ON (my_permissions.permissionset_id = opd.org_permissionset_id)

                        inner join
                    org_permissionset ops ON (ops.id = my_permissions.permissionset_id
                                                and ops.organization_id = [ORG_ID] 
                                                [ACCESS_LEVEL])
                where
                    sq.survey_id = [SURVEY_ID]
                    [SURVEY_QUESTIONS]
            ";
        
            $sql = str_replace( '[ORG_ID]', $orgId, $sql);
            $sql = str_replace( '[FACULTY_ID]', $personId, $sql);
            $sql = str_replace( '[SURVEY_ID]', $surveyId, $sql);
            $sql = str_replace( '[ACCESS_LEVEL]', $aggregateCond, $sql);
            $sql = str_replace( '[SURVEY_QUESTIONS]', $surveyQues, $sql);            
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
        } catch (\Exception $e) {
        throw new ValidationException([
            SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
            return $stmt->fetchAll();
    }


    public function getOrgIsqperm()
    {
        $em = $this->getEntityManager();
        $sql = "SELECT org.id as org_id, ps.id as permissionset_id FROM synapse.organization as org 
                inner join org_permissionset as ps on ps.organization_id = org.id where ps.current_future_isq = 1";
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }
    
    public function getFutureIsqs($org_id, $permissionset_id, $survey, $cohort) {
        try {
            $em = $this->getEntityManager();
            $sql = "SELECT
                    sq.org_question_id as question_id, 
                    sq.survey_id,
                    sq.cohort_code as cohort_id
                FROM
                    synapse.survey_questions as sq
                        left join
                    org_question as oq ON oq.id = sq.org_question_id
                where
                    sq.survey_id = ".$survey." and sq.cohort_code = ".$cohort."
                        and oq.organization_id = ".$org_id."
                        and sq.org_question_id not in (select 
                            org_question_id
                        from
                            org_permissionset_question as opq
                        where
                            opq.survey_id = ".$survey."
                                and opq.cohort_code = ".$cohort."
                                and opq.organization_id = ".$org_id."
                                and opq.org_permissionset_id = ".$permissionset_id." and opq.deleted_at is null)
                        and sq.deleted_at is null
                        and oq.deleted_at is null";
        
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
                ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return $stmt->fetchAll();
    }

    /*
     * Get Survey ISQ with cohort
     * @param int $orgId
     * @param int $surveyId
     * @param int $cohortId
     * @return array
     * @throws ValidationException
     */
    public function getSurveyCohortISQ($orgId, $surveyId, $cohortId)
    {

        $parameters = [
            'orgId' => $orgId,
            'surveyId' => $surveyId,
            'cohortId' => $cohortId
        ];
        $em = $this->getEntityManager();
        $sql = "SELECT 
                    sq.survey_id,
                    sq.cohort_code AS cohort_id,
                    sq.org_question_id AS question_id,
                    oq.question_type_id AS type_id,
                    oq.question_category_id AS category_id,
                    oq.question_key,
                    oq.question_text
                FROM
                    synapse.survey_questions AS sq
                        LEFT JOIN
                    org_question AS oq ON oq.id = sq.org_question_id
                WHERE
                    sq.survey_id = :surveyId AND sq.cohort_code = :cohortId
                        AND oq.organization_id = :orgId AND sq.deleted_at IS NULL AND oq.deleted_at IS NULL";
        try {

            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }

    }

    public function getQuestionsQnbrForSurvey($surveyId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('sq.qnbr as survey_ques_no');
        $qb->from('SynapseSurveyBundle:SurveyQuestions', 'sq');
        $qb->LEFTJoin('SynapseCoreBundle:EbiQuestion', 'eq', \Doctrine\ORM\Query\Expr\Join::WITH, 'eq.id = sq.ebiQuestion');
        $qb->LEFTJoin('SynapseCoreBundle:EbiQuestionsLang', 'eql', \Doctrine\ORM\Query\Expr\Join::WITH, 'eql.ebiQuestion = eq.id');              
        $qb->andWhere('sq.survey = :surveyId');        
        $qb->andWhere('sq.ebiQuestion IS NOT NULL');   
        $qb->andWhere("eq.questionType IN ('Q','D', 'MR', 'NA', 'SA', 'LA','MR')");
        $qb->setParameters(array(            
            'surveyId' => $surveyId           
        ));
        $qb->orderBy('sq.qnbr+0', 'ASC');
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
    
    /**
     * Return students org question response based on surveyId and Organization id.
     *
     * @param int $surveyId
     * @param int $orgId     
     * @return resultset
     */
    public function getOrgQuestionsForSurvey($surveyId, $orgId, $cohortId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('sq.id as survey_ques_id','org.questionText as org_ques_text', 'IDENTITY(org.questionType) as org_question_type', 'org.id as org_question_id');
        $qb->from('SynapseSurveyBundle:SurveyQuestions', 'sq');
        $qb->LEFTJoin('SynapseCoreBundle:OrgQuestion', 'org', \Doctrine\ORM\Query\Expr\Join::WITH, 'org.id = sq.orgQuestion');                      
        $qb->andWhere('sq.survey = :surveyId');        
        $qb->andWhere('sq.orgQuestion IS NOT NULL');   
        $qb->andWhere("org.questionType IN ('Q','D', 'MR', 'NA', 'LA', 'SA')");
        $qb->andWhere("org.organization = :orgId");
        $qb->andWhere("sq.cohortCode = :cohortId");
        $qb->setParameters(array(            
            'surveyId' => $surveyId,
            'orgId' => $orgId,
            'cohortId' => $cohortId
        ));
		$qb->groupBy('org_question_id');
        $qb->orderBy('org_question_id', 'ASC');
        $query = $qb->getQuery();        
        $resultSet = $query->getResult();
        return $resultSet;
    }
    
    public function getOrgQuestionOptions($surveyId, $questionId)
    {    
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('oqo.id as org_option_id', 'oqo.optionName as org_option_text', 'oqo.optionValue as org_option_value', 'oqo.sequence as sequence');
        $qb->from('SynapseSurveyBundle:SurveyQuestions', 'sq');
        $qb->LEFTJoin('SynapseCoreBundle:OrgQuestionOptions', 'oqo', \Doctrine\ORM\Query\Expr\Join::WITH, 'oqo.orgQuestion = sq.orgQuestion');        
        $qb->andWhere('sq.survey = :surveyId');        
        $qb->andWhere('sq.orgQuestion = :questionId');
        $qb->setParameters(array(            
            'surveyId' => $surveyId,
            'questionId' => $questionId
        ));
        $qb->orderBy('oqo.sequence', 'ASC');
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }

    /**
    * Returns the unique list of survey questions for the list of given survey id's
    *     
    * @param array $surveyIds
    * @return array
    */
    public function getUniqueSurveyQuestionsForCohort($surveyIds)
    {        
        $parameters = implode(',',$surveyIds);         
        $sql = "SELECT sq.qnbr as qnbr, eql.question_rpt as ebi_ques_text, sq.id as survey_ques_id, sq.type as type,          
               eq.question_type_id AS ebi_question_type, sq.ebi_question_id AS ebi_question_id, eq.question_type_id AS question_type, sq.survey_id as survey_id
               FROM survey_questions sq 
               JOIN ebi_question eq ON (eq.id = sq.ebi_question_id)
               JOIN ebi_questions_lang eql ON (eql.ebi_question_id = eq.id)
               WHERE sq.survey_id IN (?)
               AND sq.ebi_question_id IS NOT NULL
               AND eq.question_type_id IN ('Q' , 'D', 'MR', 'NA', 'LA', 'SA') group by qnbr
               ORDER BY sq.qnbr + 0 ASC";                       
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);            
            $stmt->execute(array($parameters));     
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }     
        $records = $stmt->fetchAll();                  
        return $records;
    }

}