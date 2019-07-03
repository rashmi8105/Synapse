<?php

namespace Synapse\DataBundle\DAO;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

/**
 * @DI\Service("information_schema_dao")
 */
class InformationSchemaDAO
{

    const DAO_KEY = 'information_schema_dao';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * InformationSchemaDAO constructor.
     *
     * @param $connection
     *
     * @DI\InjectParams({
     *          "connection" = @DI\Inject("database_connection")
     *      })
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * It will return the information about a table's columns
     *
     * @param string $tableName
     * @param array $columns
     * @param boolean $orderByFlag
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getCharacterLengthForColumnsInTable($tableName, $columns, $orderByFlag = true)
    {
        $databaseName = $this->connection->getDatabase();

        if ($orderByFlag) {
            $orderByColumn = " ORDER BY COLUMN_NAME";
        } else {
            $orderByColumn = " ";
        }

        try {
            $sql = "SELECT
                        DISTINCT(COLUMN_NAME) AS columnName, CHARACTER_MAXIMUM_LENGTH AS length
                    FROM
                        INFORMATION_SCHEMA.COLUMNS
                    WHERE
                        TABLE_NAME = :tableName
                        AND COLUMN_NAME IN (:columns)
                        AND TABLE_SCHEMA = :databaseName
                    $orderByColumn";

            $parameters = [
                'tableName' => $tableName,
                'columns' => $columns,
                'databaseName' => $databaseName
            ];

            $stmt = $this->connection->executeQuery($sql, $parameters, ['columns' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]);
            $results = $stmt->fetchAll();

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

}
