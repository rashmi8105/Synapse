<?php

namespace Synapse\SurveyBundle\Repository;


use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;

class SurveyBranchRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSurveyBundle:SurveyBranch';

    /**
     * Returns information about the question and option(s) that branched to the given $surveyQuestionId.
     *
     * @param int $surveyId
     * @param int $surveyQuestionId
     * @return array
     */
    public function getQuestionBranchDetails($surveyId, $surveyQuestionId)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'surveyQuestionId' => $surveyQuestionId
        ];

        $sql = "SELECT
                    sq.ebi_question_id,		-- question we're branching from
                    sq.qnbr,
                    sq.type,
                    IF(eql.question_text > '', CONCAT(eql.question_text, ' ', eql.question_rpt), eql.question_rpt) AS question_text,
                    sb.ebi_question_options_id,
                    eqo.option_text,
                    eqo.sequence,
                    sb.survey_question_id 	-- question we're branching to
                FROM
                    survey_branch sb
                        INNER JOIN
                    ebi_question_options eqo
                            ON eqo.id = sb.ebi_question_options_id
                        INNER JOIN
                    ebi_questions_lang eql
                            ON eql.ebi_question_id = eqo.ebi_question_id
                        INNER JOIN
                    survey_questions sq
                            ON sq.ebi_question_id = eqo.ebi_question_id
                WHERE
                    sb.survey_question_id = :surveyQuestionId
                    AND sb.survey_id = :surveyId
                    AND sb.deleted_at IS NULL
                    AND eqo.deleted_at IS NULL
                    AND eql.deleted_at IS NULL
                    AND sq.deleted_at IS NULL
                ORDER BY sb.survey_question_id, eqo.sequence;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();

        return $results;
    }
}