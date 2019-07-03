<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Changing failure_risk_level in academic_update table to be the proper case ("High" instead of "high", "Low" instead of "low")
 */
class Version20160830173328 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // change rows with 'high' to 'High'
        $this->addSql(
            " UPDATE academic_update
          SET failure_risk_level = 'High'
          WHERE failure_risk_level = BINARY 'high';"
        );

        // change rows with 'low' to 'Low'
        $this->addSql(
            " UPDATE academic_update
          SET failure_risk_level = 'Low'
          WHERE failure_risk_level = BINARY 'low';"
        );
    }


    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}