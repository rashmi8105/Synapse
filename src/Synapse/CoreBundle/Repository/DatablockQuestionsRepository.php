<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\Util\Constants\SurveyBlockConstant;

class DatablockQuestionsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:DatablockQuestions';

    public function deleteSurveyBlockQuestion($datablockQuestion)
    {
        $em = $this->getEntityManager();
        $em->remove($datablockQuestion);
    }

    public function deleteSurveyBlock($id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update(SurveyBlockConstant::DATA_BLOCK_QUESTIONS_REPO, 'q');
        $qb->set('q.deletedAt', 'CURRENT_TIMESTAMP()');
        $qb->where($qb->expr()
            ->eq('q.datablock', ':id'));
        $qb->setParameters(array(
            'id' => $id
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
    
    
    public function getSurveyBlocks() {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(q.ebiQuestion) as LongitudinalID,IDENTITY(q.survey) as SurvID,IDENTITY(q.factor) as FactorID,IDENTITY(q.datablock) as SurveyBlockID, eql.questionRpt as qNbr, eql.questionText as qtextRpt, fl.name as facdescr');
        $qb->from(SurveyBlockConstant::DATA_BLOCK_QUESTIONS_REPO, 'q');
        $qb->LEFTJoin('SynapseCoreBundle:EbiQuestionsLang', 'eql', \Doctrine\ORM\Query\Expr\Join::WITH, 'q.ebiQuestion = eql.id');
        $qb->LEFTJoin('SynapseSurveyBundle:FactorLang', 'fl', \Doctrine\ORM\Query\Expr\Join::WITH, 'q.factor = fl.factor');
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
    
    public function getFactorForSurvey($surveyId) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(q.survey) as survey_id,IDENTITY(q.factor) as factor_id,fl.name as factor_name');
        $qb->from(SurveyBlockConstant::DATA_BLOCK_QUESTIONS_REPO, 'q');        
        $qb->LEFTJoin('SynapseSurveyBundle:FactorLang', 'fl', \Doctrine\ORM\Query\Expr\Join::WITH, 'q.factor = fl.factor');
        $qb->where('q.type = :type');
		$qb->andWhere('q.survey = :surveyId');
        $qb->setParameters(array(
            'type' => 'factor',
			'surveyId' => $surveyId
        ));
        $qb->orderBy('q.factor', 'asc');
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }


    /**
     * Returns a list of the datablock ids that the user needs to have in his/her permission set(s)
     * in order to view the Student Survey Report (pdf report accessed from the Student Dashboard).
     *
     * @return array
     */
    public function getDatablocksNeededToViewStudentSurveyReport()
    {
        $sql = "SELECT DISTINCT dq.datablock_id
                FROM
                    reports
                        INNER JOIN
                    report_sections rs
                            ON rs.report_id = reports.id
                        INNER JOIN
                    report_section_elements rse
                            ON rse.section_id = rs.id
                        LEFT JOIN
                    survey_questions sq
                            ON sq.id = rse.survey_question_id
                        LEFT JOIN
                    datablock_questions dq
                            ON (dq.factor_id = rse.factor_id OR dq.ebi_question_id = sq.ebi_question_id)
                WHERE
                    reports.short_code = 'SUR-SSR'
                    AND reports.deleted_at IS NULL
                    AND rs.deleted_at IS NULL
                    AND rse.deleted_at IS NULL
                    AND sq.deleted_at IS NULL
                    AND dq.deleted_at IS NULL;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();

        $results = array_map('current', $results);      // un-nest the array

        return $results;
    }


    /**
     * Returns a list of factor_ids (if $type is "factor") or ebi_question_ids (if $type is "question")
     * which are associated with the given datablocks.
     *
     * @param array $datablockIds
     * @param string $type -- "factor" or "question"
     * @return array
     */
    public function getAccessibleFactorsOrQuestions($datablockIds, $type)
    {
        $parameters = ['datablockIds' => $datablockIds];
        $parameterTypes = ['datablockIds' => Connection::PARAM_INT_ARRAY];

        if ($type == 'factor') {
            $column = 'factor_id';
        } elseif ($type == 'question') {
            $column = 'ebi_question_id';
        } else {
            return [];
        }

        $sql = "SELECT DISTINCT $column
                FROM datablock_questions
                WHERE datablock_id IN (:datablockIds)
                    AND $column IS NOT NULL
                    AND deleted_at IS NULL;";

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