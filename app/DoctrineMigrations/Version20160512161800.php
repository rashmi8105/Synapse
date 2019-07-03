<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script for creating org_person_student_year table and appropriate indexes.
 */
class Version20160512161800 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // drop table if it exists
        $this->addSql('DROP TABLE IF EXISTS synapse.org_person_student_year');

        // create table
        $this->addSql("CREATE TABLE synapse.org_person_student_year (
                          id int(11) NOT NULL AUTO_INCREMENT,
                          organization_id int(11) NOT NULL,
                          person_id int(11) NOT NULL,
                          org_academic_year_id int(11) NOT NULL,
                          is_active tinyint(1) DEFAULT NULL,
                          created_by int(11) DEFAULT NULL,
                          created_at datetime DEFAULT NULL,
                          modified_by int(11) DEFAULT NULL,
                          modified_at datetime DEFAULT NULL,
                          deleted_by int(11) DEFAULT NULL,
                          deleted_at datetime DEFAULT NULL,
                          PRIMARY KEY (id)
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        // add foreign key and index
        $this->addSql('ALTER TABLE synapse.org_person_student_year ADD CONSTRAINT FK_1B384A68DE12AB57 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE synapse.org_person_student_year ADD CONSTRAINT FK_1B384A6825F94803 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE synapse.org_person_student_year ADD CONSTRAINT FK_1B384A681F6FA0B0 FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE synapse.org_person_student_year ADD CONSTRAINT FK_1B384A68582DCD12 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE synapse.org_person_student_year ADD CONSTRAINT FK_1B384A68FCA5FAC0 FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE synapse.org_person_student_year ADD CONSTRAINT FK_1B384A68FCA5FAC1 FOREIGN KEY (org_academic_year_id) REFERENCES org_academic_year (id)');
        $this->addSql('ALTER TABLE synapse.org_person_student_year ADD INDEX IDX_person_org_year_del(person_id, organization_id, org_academic_year_id, deleted_at)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
