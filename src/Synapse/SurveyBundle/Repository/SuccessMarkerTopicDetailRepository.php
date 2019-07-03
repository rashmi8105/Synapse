<?php

namespace Synapse\SurveyBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;


class SuccessMarkerTopicDetailRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSurveyBundle:SuccessMarkerTopicDetail';

    /**
     * Uses response and permission data to determine whether any responses would be available in the drilldown for a topic
     * on the Student Survey Dashboard.
     * This function is used to determine whether to display a topic's icon as gray or not display it at all
     * when permissions or lack of data prevent us from displaying a color for the representative factor or question.
     *
     * @param int $studentId
     * @param int $surveyId
     * @param int $topicId
     * @param array $datablockIds
     * @return bool
     */
    public function responsesAreAvailableInDrilldown($studentId, $surveyId, $topicId, $datablockIds)
    {
        $parameters = [
            'studentId' => $studentId,
            'surveyId' => $surveyId,
            'topicId' => $topicId,
            'datablockIds' => $datablockIds
        ];

        $parameterTypes = ['datablockIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT sr.decimal_value, sr.char_value, sr.charmax_value
                FROM
                    survey_response sr
                        INNER JOIN
                    survey_questions sq
                            ON sq.id = sr.survey_questions_id
                        INNER JOIN
                    success_marker_topic_detail smtd
                            ON smtd.ebi_question_id = sq.ebi_question_id
                        INNER JOIN
                    datablock_questions dq
                            ON dq.ebi_question_id = sq.ebi_question_id
                WHERE sr.person_id = :studentId
                    AND sr.survey_id = :surveyId
                    AND smtd.topic_id = :topicId
                    AND dq.datablock_id IN (:datablockIds)
                    AND sr.deleted_at IS NULL
                    AND sq.deleted_at IS NULL
                    AND smtd.deleted_at IS NULL
                    AND dq.deleted_at IS NULL;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();

        if (!empty($results)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Returns factors and their associated colors for a given topic on the Student Survey Dashboard.
     *
     * @param int $studentId
     * @param int $topicId
     * @param int $surveyId
     * @return array
     */
    public function getSuccessMarkerTopicDetailsFromFactors($studentId, $topicId, $surveyId)
    {
        $parameters = [
            'studentId' => $studentId,
            'topicId' => $topicId,
            'surveyId' => $surveyId
        ];

        $sql = "SELECT
                    smtd.factor_id,
                    fl.name AS factor_name,
                    COALESCE(smtdc.color, 'gray') AS color
                FROM
                    success_marker_topic_detail smtd
                        INNER JOIN
                    success_marker_topic_detail_color smtdc
                            ON smtdc.topic_detail_id = smtd.id
                        INNER JOIN
                    factor_lang fl
                            ON fl.factor_id = smtd.factor_id
                        INNER JOIN
                    person_factor_calculated pfc
                            ON pfc.factor_id = smtd.factor_id
                WHERE smtd.topic_id = :topicId
                    AND pfc.person_id = :studentId
                    AND pfc.survey_id = :surveyId
                    AND pfc.mean_value BETWEEN smtdc.min_value AND smtdc.max_value
                    AND smtd.deleted_at IS NULL
                    AND smtdc.deleted_at IS NULL
                    AND fl.deleted_at IS NULL
                    AND pfc.deleted_at IS NULL
                ORDER BY smtd.factor_id + 0;";      // order them as numbers, not as strings

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
     * Returns questions, their responses, and their associated colors for a given topic on the Student Survey Dashboard.
     *
     * @param int $studentId
     * @param int $topicId
     * @param int $surveyId
     * @return array
     */
    public function getSuccessMarkerTopicDetailsFromQuestions($studentId, $topicId, $surveyId)
    {
        $parameters = [
            'studentId' => $studentId,
            'topicId' => $topicId,
            'surveyId' => $surveyId
        ];

        $sql = "SELECT
                    sq1.qnbr,
                    smtd.ebi_question_id,
                    fq.factor_id AS associated_factor_id,
                    IF(eql.question_text > '', CONCAT(eql.question_text, ' ', eql.question_rpt), eql.question_rpt) AS question_text,
                    COALESCE(eqo.extended_option_text, sr.charmax_value, sr.char_value, sr.decimal_value) AS response,
                    COALESCE(smtdc.color, 'gray') AS color
                FROM
                    success_marker_topic_detail smtd
                        INNER JOIN
                    ebi_questions_lang eql
                            ON eql.ebi_question_id = smtd.ebi_question_id
                        INNER JOIN
                    survey_questions sq1
                            ON sq1.ebi_question_id = smtd.ebi_question_id
                        INNER JOIN
                    survey_questions sq2
                            ON sq2.qnbr = sq1.qnbr
                        INNER JOIN
                    survey_response sr
                            ON sr.survey_questions_id = sq2.id
                        LEFT JOIN
                    success_marker_topic_detail_color smtdc
                            ON smtdc.topic_detail_id = smtd.id
                            AND sr.decimal_value BETWEEN smtdc.min_value AND smtdc.max_value
                        LEFT JOIN
                    ebi_question_options eqo
                            ON eqo.ebi_question_id = smtd.ebi_question_id
                            AND eqo.option_value = sr.decimal_value
                        LEFT JOIN
                    factor_questions fq
                            ON fq.ebi_question_id = smtd.ebi_question_id
                WHERE smtd.topic_id = :topicId
                    AND sr.person_id = :studentId
                    AND sr.survey_id = :surveyId
                    AND smtd.deleted_at IS NULL
                    AND eql.deleted_at IS NULL
                    AND sq1.deleted_at IS NULL
                    AND sq2.deleted_at IS NULL
                    AND sr.deleted_at IS NULL
                    AND smtdc.deleted_at IS NULL
                    AND eqo.deleted_at IS NULL
                    AND fq.deleted_at IS NULL
                ORDER BY sq1.qnbr + 0;";        // order them as numbers, not as strings

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