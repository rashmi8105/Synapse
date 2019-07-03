<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150929125820 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE org_survey_report_access_history DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE org_survey_report_access_history ADD student_id INT NOT NULL');
        $this->addSql('ALTER TABLE org_survey_report_access_history ADD CONSTRAINT FK_3B610FEDCB944F1A FOREIGN KEY (student_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_3B610FEDCB944F1A ON org_survey_report_access_history (student_id)');
        $this->addSql('ALTER TABLE org_survey_report_access_history ADD PRIMARY KEY (org_id, person_id, student_id, survey_id, year_id, cohort_code)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE org_survey_report_access_history DROP FOREIGN KEY FK_3B610FEDCB944F1A');
        $this->addSql('DROP INDEX IDX_3B610FEDCB944F1A ON org_survey_report_access_history');
        $this->addSql('ALTER TABLE org_survey_report_access_history DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE org_survey_report_access_history DROP student_id');
        $this->addSql('ALTER TABLE org_survey_report_access_history ADD PRIMARY KEY (org_id, person_id, survey_id, year_id, cohort_code)');
    }
}
