<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150901061840 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE report_calc_history (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT DEFAULT NULL, person_id INT DEFAULT NULL, report_id INT DEFAULT NULL, survey_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, INDEX IDX_23CCB636DE12AB56 (created_by), INDEX IDX_23CCB63625F94802 (modified_by), INDEX IDX_23CCB6361F6FA0AF (deleted_by), INDEX fk_report_calc_history_org1_idx (org_id), INDEX fk_report_calc_history_survey1_idx (survey_id), INDEX fk_report_calc_history_reports1_idx (report_id), INDEX fk_report_calc_history_person1_idx (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE report_calc_history ADD CONSTRAINT FK_23CCB636DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_calc_history ADD CONSTRAINT FK_23CCB63625F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_calc_history ADD CONSTRAINT FK_23CCB6361F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_calc_history ADD CONSTRAINT FK_23CCB636F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE report_calc_history ADD CONSTRAINT FK_23CCB636217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_calc_history ADD CONSTRAINT FK_23CCB6364BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE report_calc_history ADD CONSTRAINT FK_23CCB636B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE org_calc_flags_student_reports ADD survey_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE org_calc_flags_student_reports ADD CONSTRAINT FK_F4644A69B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('CREATE INDEX IDX_F4644A69B3FE509D ON org_calc_flags_student_reports (survey_id)');
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE report_calc_history');
        $this->addSql('ALTER TABLE org_calc_flags_student_reports DROP FOREIGN KEY FK_F4644A69B3FE509D');
        $this->addSql('DROP INDEX IDX_F4644A69B3FE509D ON org_calc_flags_student_reports');
        $this->addSql('ALTER TABLE org_calc_flags_student_reports DROP survey_id');
        
    }
}
