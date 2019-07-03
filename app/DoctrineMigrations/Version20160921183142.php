<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adding a feature toggle for Longitudinal Student Management.
 * When it has value 0, students' status (active/inactive) will be obtained from org_person_student.
 * When it has value 1, students' status (active/inactive/archived, per academic year) will be obtained from org_person_student_year.
 */
class Version20160921183142 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $sql = "SELECT 1 FROM ebi_config WHERE `key` = 'Longitudinal_Student_Management';";

        $results = $this->connection->executeQuery($sql)->fetchAll();

        if (empty($results)) {
            $this->addSql("INSERT INTO ebi_config (`key`, value)
                        VALUES ('Longitudinal_Student_Management', 0);");
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');
    }
}
