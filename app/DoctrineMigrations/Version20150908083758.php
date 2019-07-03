<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150908083758 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
        $this->addSql('CREATE TABLE report_run_details (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, report_instance_id INT DEFAULT NULL, section_id INT DEFAULT NULL, question_id INT DEFAULT NULL, person_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, survey_qnbr INT DEFAULT NULL, response_json LONGTEXT NOT NULL, type VARCHAR(100) DEFAULT NULL, INDEX IDX_9B5CD0A2DE12AB56 (created_by), INDEX IDX_9B5CD0A225F94802 (modified_by), INDEX IDX_9B5CD0A21F6FA0AF (deleted_by), INDEX IDX_9B5CD0A28B49D915 (report_instance_id), INDEX IDX_9B5CD0A2D823E37A (section_id), INDEX IDX_9B5CD0A21E27F6BF (question_id), INDEX IDX_9B5CD0A2217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reports_running_status (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, report_id INT DEFAULT NULL, org_id INT DEFAULT NULL, person_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, is_viewed enum(\'Y\',\'N\'), filtered_student_ids LONGTEXT NOT NULL, filter_criteria LONGTEXT NOT NULL, report_custom_title VARCHAR(255) NOT NULL, status enum(\'Q\',\'IP\',\'C\',\'F\'), INDEX IDX_80023648DE12AB56 (created_by), INDEX IDX_8002364825F94802 (modified_by), INDEX IDX_800236481F6FA0AF (deleted_by), INDEX IDX_800236484BD2A4C0 (report_id), INDEX IDX_80023648F4837C1B (org_id), INDEX IDX_80023648217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE report_run_details ADD CONSTRAINT FK_9B5CD0A2DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_run_details ADD CONSTRAINT FK_9B5CD0A225F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_run_details ADD CONSTRAINT FK_9B5CD0A21F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_run_details ADD CONSTRAINT FK_9B5CD0A28B49D915 FOREIGN KEY (report_instance_id) REFERENCES reports_running_status (id)');
        $this->addSql('ALTER TABLE report_run_details ADD CONSTRAINT FK_9B5CD0A2D823E37A FOREIGN KEY (section_id) REFERENCES report_sections (id)');
        $this->addSql('ALTER TABLE report_run_details ADD CONSTRAINT FK_9B5CD0A21E27F6BF FOREIGN KEY (question_id) REFERENCES survey_questions (id)');
        $this->addSql('ALTER TABLE report_run_details ADD CONSTRAINT FK_9B5CD0A2217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
		$this->addSql('ALTER TABLE reports_running_status ADD CONSTRAINT FK_80023648DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE reports_running_status ADD CONSTRAINT FK_8002364825F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE reports_running_status ADD CONSTRAINT FK_800236481F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE reports_running_status ADD CONSTRAINT FK_800236484BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE reports_running_status ADD CONSTRAINT FK_80023648F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE reports_running_status ADD CONSTRAINT FK_80023648217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE report_run_details DROP FOREIGN KEY FK_9B5CD0A28B49D915');
        $this->addSql('DROP TABLE report_run_details');
        $this->addSql('DROP TABLE reports_running_status');       
    }
}
