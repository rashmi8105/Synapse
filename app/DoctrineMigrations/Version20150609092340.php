<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150609092340 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_person_student_survey_link (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT DEFAULT NULL, person_id INT DEFAULT NULL, org_academic_year_id INT DEFAULT NULL, survey_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, cohort VARCHAR(20) DEFAULT NULL, survey_link VARCHAR(500) DEFAULT NULL, INDEX IDX_E7BDE868DE12AB56 (created_by), INDEX IDX_E7BDE86825F94802 (modified_by), INDEX IDX_E7BDE8681F6FA0AF (deleted_by), INDEX fk_org_person_student_survey_link_survey1_idx (survey_id), INDEX fk_org_person_student_survey_link_org_academic_year1_idx (org_academic_year_id), INDEX fk_org_person_student_survey_link_organization1_idx (org_id), INDEX fk_org_person_student_survey_link_person1_idx (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_person_student_survey_link ADD CONSTRAINT FK_E7BDE868DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student_survey_link ADD CONSTRAINT FK_E7BDE86825F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student_survey_link ADD CONSTRAINT FK_E7BDE8681F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student_survey_link ADD CONSTRAINT FK_E7BDE868F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_person_student_survey_link ADD CONSTRAINT FK_E7BDE868217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_student_survey_link ADD CONSTRAINT FK_E7BDE868F3B0CE4A FOREIGN KEY (org_academic_year_id) REFERENCES org_academic_year (id)');
        $this->addSql('ALTER TABLE org_person_student_survey_link ADD CONSTRAINT FK_E7BDE868B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
            }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_person_student_survey_link');
    }
}
