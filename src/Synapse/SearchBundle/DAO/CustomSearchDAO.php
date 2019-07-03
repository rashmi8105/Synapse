<?php

namespace Synapse\SearchBundle\DAO;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;


/**
 * @DI\Service("custom_search_dao")
 */
class CustomSearchDAO
{

    const DAO_KEY = 'custom_search_dao';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * CustomSearchDAO constructor
     *
     * @param $connection
     *
     * @DI\InjectParams({
     *     "connection" = @DI\Inject("database_connection")
     * })
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }


    /**
     * Returns an array of ids for the students in the given organization which match the given query.
     *
     * Note: This function does not attempt the monumental task of making custom search follow standards.
     * At this point, I'm only trying to clean it up a bit to make it easier to follow as I'm standardizing sorting.
     *
     * Passing in a query as a string is an anti-pattern that we would not like to repeat.
     * It is necessary here because refactoring Custom Search is out-of-scope for the current set of changes.
     *
     * @param int $organizationId
     * @param string $studentQuery
     * @return array
     */
    public function getStudentsForCustomSearch($organizationId, $studentQuery)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];

        $sql = "SELECT DISTINCT p.id
                FROM
                    person p
                WHERE
                    p.deleted_at IS NULL
                    AND p.organization_id = :organizationId
                    AND $studentQuery;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

}