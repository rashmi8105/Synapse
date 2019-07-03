<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * AcademicRecord Migration script ESPRJ-16052
 */
class Version20170922063559 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $this->addSql('
                      CREATE TABLE academic_record (
                      id INT AUTO_INCREMENT NOT NULL, 
                      organization_id INT NOT NULL, 
                      org_courses_id INT NOT NULL, 
                      person_id_student INT NOT NULL, 
                      failure_risk_level VARCHAR(10) DEFAULT NULL, 
                      in_progress_grade VARCHAR(20) DEFAULT NULL, 
                      absence INT DEFAULT NULL, 
                      comment VARCHAR(300) DEFAULT NULL, 
                      final_grade VARCHAR(20) DEFAULT NULL,
                      update_date DATETIME DEFAULT NULL, 
                      failure_risk_level_update_date DATETIME DEFAULT NULL, 
                      in_progress_grade_update_date DATETIME DEFAULT NULL, 
                      absence_update_date DATETIME DEFAULT NULL, 
                      comment_update_date DATETIME DEFAULT NULL, 
                      final_grade_update_date DATETIME DEFAULT NULL,
                      created_by INT DEFAULT NULL,
                      created_at DATETIME DEFAULT NULL,
                      modified_at DATETIME DEFAULT NULL,
                      modified_by INT DEFAULT NULL,
                      deleted_by INT DEFAULT NULL,
                      deleted_at DATETIME DEFAULT NULL,
                      INDEX IDX_70CEE1C3DE12AB56 (created_by), 
                      INDEX IDX_70CEE1C325F94802 (modified_by), 
                      INDEX IDX_70CEE1C31F6FA0AF (deleted_by), 
                      INDEX IDX_70CEE1C332C8A3DE (organization_id), 
                      INDEX IDX_70CEE1C37C751C40 (org_courses_id), 
                      INDEX IDX_70CEE1C35F056556 (person_id_student), 
                      INDEX risk_index (organization_id, person_id_student, failure_risk_level, in_progress_grade), 
                      INDEX org_course_student_update_index (organization_id, org_courses_id, person_id_student, update_date), 
                      UNIQUE INDEX org_course_student_unique (organization_id, org_courses_id, person_id_student), 
                      PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('ALTER TABLE academic_record ADD CONSTRAINT FK_70CEE1C3DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_record ADD CONSTRAINT FK_70CEE1C325F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_record ADD CONSTRAINT FK_70CEE1C31F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_record ADD CONSTRAINT FK_70CEE1C332C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE academic_record ADD CONSTRAINT FK_70CEE1C37C751C40 FOREIGN KEY (org_courses_id) REFERENCES org_courses (id)');
        $this->addSql('ALTER TABLE academic_record ADD CONSTRAINT FK_70CEE1C35F056556 FOREIGN KEY (person_id_student) REFERENCES person (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE academic_record');
    }
}
