<?php
namespace Synapse\ReportsBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\ReportsBundle\Entity\OrgPersonStudentRetention;

class OrgPersonStudentRetentionRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseReportsBundle:OrgPersonStudentRetention';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return OrgPersonStudentRetention|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }


    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return OrgPersonStudentRetention[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return OrgPersonStudentRetention|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * Get the aggregated count of retention data based on the passed in student ID
     *
     * @param string $retentionTrackingYear
     * @param int $organizationId
     * @param array $studentIds
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getAggregatedCountsOfRetentionDataByStudentList($retentionTrackingYear, $organizationId, $studentIds)
    {
        $parameters = [
            'retentionTrackingYear' => $retentionTrackingYear,
            'organizationId' => $organizationId,
            'studentIds' => $studentIds
        ];

        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    years_from_retention_track,
                    SUM(is_enrolled_beginning_year) AS beginning_year_numerator_count,
                    SUM(is_enrolled_midyear) AS midyear_numerator_count,
                    COUNT(DISTINCT person_id) AS denominator_count
                FROM
                    org_person_student_retention_by_tracking_group_view
                WHERE
                    organization_id = :organizationId
                        AND retention_tracking_year = :retentionTrackingYear
                        AND person_id IN (:studentIds)
                GROUP BY years_from_retention_track";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;
    }
}
