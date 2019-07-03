<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Longitudinal Management Toggle was never fully completed.  But it still controls whether the active/inactive
 * value comes from org_person_student or org_person_student_year.  Multi-Year User Management requires it come
 * from org_person_student_year.  Hence this configuration value must be set to true(1).
 *
 * Updating 'Longitudinal_Student_Management' to true in ebi_config.  ESPRJ-12953
 */
class Version20161222191613 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql("UPDATE ebi_config SET value = 1 WHERE `key` = 'Longitudinal_Student_Management'");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
