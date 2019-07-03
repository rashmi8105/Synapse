<?php

namespace Synapse\SurveyBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;


class SuccessMarkerTopicRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSurveyBundle:SuccessMarkerTopic';

    /**
     * Given the datablocks in the user's permission sets, returns a list of the topic ids for which the user
     * has access to the representative factor or question.  These are the topics which can have a color
     * associated with them on the top-level Student Survey Dashboard.
     *
     * @param array $datablockIds
     * @return array
     */
    public function getTopicsWhichHaveAccessibleRepresentatives($datablockIds)
    {
        $parameters = ['datablockIds' => $datablockIds];
        $parameterTypes = ['datablockIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT DISTINCT
                    smtd.topic_id
                FROM
                    success_marker sm
                        INNER JOIN
                    success_marker_topic smt
                            ON smt.success_marker_id = sm.id
                        INNER JOIN
                    success_marker_topic_representative smtr
                            ON smtr.topic_id = smt.id
                        INNER JOIN
                    success_marker_topic_detail smtd
                            ON smtd.id = smtr.representative_detail_id
                        INNER JOIN
                    datablock_questions dq
                            ON (dq.factor_id = smtd.factor_id OR dq.ebi_question_id = smtd.ebi_question_id)
                WHERE dq.datablock_id IN (:datablockIds)
                    AND sm.deleted_at IS NULL
                    AND smt.deleted_at IS NULL
                    AND smtr.deleted_at IS NULL
                    AND smtd.deleted_at IS NULL
                    AND dq.deleted_at IS NULL;";

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


    /**
     * Given the datablocks in the user's permission sets, returns a list of topic ids in the given success marker
     * for which the user has access to at least one associated factor or question.
     *
     * @param int $successMarkerId
     * @param array $datablockIds
     * @return array
     */
    public function getAccessibleTopicsInSuccessMarker($successMarkerId, $datablockIds)
    {
        $parameters = [
            'successMarkerId' => $successMarkerId,
            'datablockIds' => $datablockIds
        ];

        $parameterTypes = ['datablockIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT DISTINCT
                    smtd.topic_id
                FROM
                    success_marker sm
                        INNER JOIN
                    success_marker_topic smt
                            ON smt.success_marker_id = sm.id
                        INNER JOIN
                    success_marker_topic_detail smtd
                            ON smtd.topic_id = smt.id
                        INNER JOIN
                    datablock_questions dq
                            ON (dq.factor_id = smtd.factor_id OR dq.ebi_question_id = smtd.ebi_question_id)
                WHERE dq.datablock_id IN (:datablockIds)
                    AND sm.id = :successMarkerId
                    AND sm.deleted_at IS NULL
                    AND smt.deleted_at IS NULL
                    AND smtd.deleted_at IS NULL
                    AND dq.deleted_at IS NULL;";

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