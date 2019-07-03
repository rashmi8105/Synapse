<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Entity\EbiSearch;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Constants\SearchConstant;

class SearchRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:EbiSearch';

    /**
     * Finds a single entity by a set of criteria.
     * Override added to inform PhpStorm about the return type.
     *
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return EbiSearch|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }


    public function getQueryResult($query)
    {
        try {
            $stmt = $this->getEntityManager()
                ->getConnection()
                ->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll();
        } catch (\Exception $e) {          
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return $result;
    }


    /**
     * Returns a list of predefined searches in the given category, along with the last time the given faculty ran them (if ever).
     *
     * @param string $category -- "student_search" or "academic_update_search" or "activity_search"
     * @param int $facultyId
     * @return array
     */
    public function getPredefinedSearchListByCategory($category, $facultyId)
    {
        $parameters = [
            'category' => $category,
            'facultyId' => $facultyId
        ];

        $sql = "SELECT
                    es.query_key AS search_key,
                    es.name,
                    es.description,
                    esh.created_at AS last_run
                FROM
                    ebi_search es
                        LEFT JOIN
                    ebi_search_history esh
                          ON es.id = esh.ebi_search_id
                          AND esh.person_id = :facultyId
                          AND esh.deleted_at IS NULL
                WHERE
                    es.category = :category
                    AND es.deleted_at IS NULL
                    AND es.is_enabled = 1
                ORDER BY es.sequence;";

        $results = $this->executeQueryFetchAll($sql, $parameters);
        return $results;
    }
}