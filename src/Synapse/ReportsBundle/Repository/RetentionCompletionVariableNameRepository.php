<?php
namespace Synapse\ReportsBundle\Repository;

use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\ReportsBundle\Entity\RetentionCompletionVariableName;

class RetentionCompletionVariableNameRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseReportsBundle:RetentionCompletionVariableName';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return RetentionCompletionVariableName|null
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
     * @return RetentionCompletionVariableName[]
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
     * @return RetentionCompletionVariableName|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * Gets the retention variables ordered by years from retention track and type.
     * Allows for time boxing (usually between retention tracking year and current year)
     *
     * @param int $retentionTrackingYear
     * @param int $yearLimit
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getRetentionVariablesOrderedByYearType($retentionTrackingYear, $yearLimit) {

        $parameters = [
            'retentionTrackingYear' => $retentionTrackingYear,
            'yearLimit' => $yearLimit
        ];

        $sql = "SELECT
                    years_from_retention_track,
                    CASE WHEN type = 'enrolledMidYear' THEN 1 ELSE 0 END AS 'is_midyear_variable',
                    name_text AS 'retention_variable'
                FROM
                    retention_completion_variable_name rcvn
                    INNER JOIN `year` y ON RIGHT((y.id - :retentionTrackingYear), 2) = rcvn.years_from_retention_track
                WHERE
                    rcvn.`type` <> 'completion'
                    AND y.id BETWEEN :retentionTrackingYear AND :yearLimit
                ORDER BY rcvn.years_from_retention_track ASC, rcvn.`type` ASC";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $result = $stmt->fetchAll();
            return $result;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }


    /**
     * Get all the retention and completion variables
     *
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getAllVariableNames()
    {
        $sql = "SELECT 
                  name_text 
                FROM 
                  retention_completion_variable_name 
                WHERE 
                  deleted_at IS NULL";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql);
            $resultArray = $stmt->fetchAll();
            $result = array_column($resultArray, 'name_text');
            return $result;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }
}
