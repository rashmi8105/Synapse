<?php

namespace Synapse\SurveyBundle\Repository;

use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;


class SuccessMarkerRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSurveyBundle:SuccessMarker';

    /**
     * Returns an array of all the success markers in the system, where the keys are success marker ids
     * and the values are singleton arrays containing associative arrays with keys "success_marker_id" and "success_marker_name".
     * (The formatting is a bit odd so that this "skeleton" array can have more data added to some
     * success markers where more data is available.)
     *
     * @return array
     */
    public function getSuccessMarkers()
    {

        $sql = "SELECT id, name
                FROM success_marker
                ORDER BY sequence;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();
        $lookupTable = [];
        foreach ($records as $record) {
            $lookupTable[$record['id']][] = [
                'success_marker_id' => $record['id'],
                'success_marker_name' => $record['name']
            ];
        }

        return $lookupTable;
    }


    /**
     * Returns success markers, topics, and colors for the topics for the given student's responses on the given survey.
     * This is most of the data needed for the top level of the Student Survey Dashboard.
     * If $successMarkerId is set, only returns data for that single success marker.
     *
     * @param int $studentId
     * @param int $surveyId
     * @param int|null $successMarkerId
     * @return array
     */
    public function getSuccessMarkersAndTopicsForStudent($studentId, $surveyId, $successMarkerId = null)
    {
        $parameters = [
            'studentId' => $studentId,
            'surveyId' => $surveyId
        ];

        if (isset($successMarkerId)) {
            $successMarkerClause = 'AND sm.id = :successMarkerId';
            $parameters['successMarkerId'] = $successMarkerId;
        } else {
            $successMarkerClause = '';
        }

        $sql = "SELECT
                    smt.success_marker_id,
                    sm.name AS success_marker_name,
                    smtr.topic_id,
                    smt.name AS topic_name,
                    calculated.color,
                    smc.base_value
                FROM
                    success_marker sm
                        INNER JOIN
                    success_marker_topic smt
                            ON smt.success_marker_id = sm.id
                        INNER JOIN
                    success_marker_topic_representative smtr
                            ON smtr.topic_id = smt.id
                        INNER JOIN
                    (
                        SELECT
                            smtd.id AS detail_id,
                            smtdc.color
                        FROM
                            success_marker_topic_detail smtd
                                INNER JOIN
                            success_marker_topic_detail_color smtdc
                                    ON smtdc.topic_detail_id = smtd.id
                                INNER JOIN
                            person_factor_calculated pfc
                                    ON pfc.factor_id = smtd.factor_id
                        WHERE pfc.mean_value BETWEEN smtdc.min_value AND smtdc.max_value
                            AND pfc.person_id = :studentId
                            AND pfc.survey_id = :surveyId
                            AND smtd.deleted_at IS NULL
                            AND smtdc.deleted_at IS NULL
                            AND pfc.deleted_at IS NULL
                    UNION
                        SELECT
                            smtd.id AS detail_id,
                            smtdc.color
                        FROM
                            success_marker_topic_detail smtd
                                INNER JOIN
                            success_marker_topic_detail_color smtdc
                                    ON smtdc.topic_detail_id = smtd.id
                                INNER JOIN
                            survey_questions sq1
                                    ON sq1.ebi_question_id = smtd.ebi_question_id
                                INNER JOIN
                            survey_questions sq2
                                    ON sq2.qnbr = sq1.qnbr
                                INNER JOIN
                            survey_response sr
                                    ON sr.survey_questions_id = sq2.id
                        WHERE sr.decimal_value BETWEEN smtdc.min_value AND smtdc.max_value
                            AND sr.person_id = :studentId
                            AND sr.survey_id = :surveyId
                            AND smtd.deleted_at IS NULL
                            AND smtdc.deleted_at IS NULL
                            AND sq1.deleted_at IS NULL
                            AND sq2.deleted_at IS NULL
                            AND sr.deleted_at IS NULL
                    ) AS calculated
                            ON calculated.detail_id = smtr.representative_detail_id
                        INNER JOIN
                    success_marker_color smc
	                        ON smc.color = calculated.color
                WHERE sm.deleted_at IS NULL
                    AND smt.deleted_at IS NULL
                    AND smtr.deleted_at IS NULL
                    AND smc.deleted_at IS NULL
                    $successMarkerClause
                ORDER BY
                    sm.sequence,
                    FIELD(calculated.color, 'green', 'yellow', 'red'),
                    smtr.topic_id;";

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