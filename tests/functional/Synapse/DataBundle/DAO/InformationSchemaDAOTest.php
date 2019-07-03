<?php

/**
 * Class InformationSchemaDAOTest
 */

use Codeception\TestCase\Test;

class InformationSchemaDAOTest extends Test
{
    use Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\DataBundle\DAO\InformationSchemaDAO
     */
    private $informationSchemaDao;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->informationSchemaDao = $this->container->get('information_schema_dao');
    }

    public function testGetCharacterLengthForColumnsInTable()
    {
        $this->specify("Verify the functionality of getting character field lengths for columns", function ($expectedColumnLength, $tableName, $columns) {
            $results = $this->informationSchemaDao->getCharacterLengthForColumnsInTable($tableName, $columns);
            verify($results[0]['length'])->notEmpty();
            verify($results[0]['length'])->equals($expectedColumnLength);
        }, ["examples" =>
            [
                [100, 'org_group', ['group_name']],
                [255, 'person', ['external_id']]
            ]
        ]);
    }

}