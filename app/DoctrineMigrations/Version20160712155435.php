<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-11207
 * Removing is_email_sent column
 */
class Version20160712155435 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        //Checking to see if the column exists
        $sql = "SELECT 1
                FROM
                    INFORMATION_SCHEMA.COLUMNS
                WHERE
                    TABLE_NAME = 'org_calc_flags_student_reports'
                    AND TABLE_SCHEMA = 'synapse'
                    AND COLUMN_NAME = 'is_email_sent';";

        $results = $this->connection->executeQuery($sql)->fetchAll();


        if (!empty($results)) {
            $this->addSql("ALTER TABLE org_calc_flags_student_reports
                        DROP COLUMN is_email_sent;");
        }

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
