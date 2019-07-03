<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150515042040 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_survey_report_access_history (org_id INT NOT NULL, person_id INT NOT NULL, survey_id INT NOT NULL, year_id VARCHAR(10) NOT NULL, cohort_code INT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, last_accessed_on DATETIME DEFAULT NULL, INDEX IDX_3B610FEDDE12AB56 (created_by), INDEX IDX_3B610FED25F94802 (modified_by), INDEX IDX_3B610FED1F6FA0AF (deleted_by), INDEX IDX_3B610FEDF4837C1B (org_id), INDEX IDX_3B610FED217BBB47 (person_id), INDEX IDX_3B610FEDB3FE509D (survey_id), INDEX IDX_3B610FED40C1FEA7 (year_id), PRIMARY KEY(org_id, person_id, survey_id, year_id, cohort_code)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_survey_report_access_history ADD CONSTRAINT FK_3B610FEDDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_survey_report_access_history ADD CONSTRAINT FK_3B610FED25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_survey_report_access_history ADD CONSTRAINT FK_3B610FED1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_survey_report_access_history ADD CONSTRAINT FK_3B610FEDF4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_survey_report_access_history ADD CONSTRAINT FK_3B610FED217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_survey_report_access_history ADD CONSTRAINT FK_3B610FEDB3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE org_survey_report_access_history ADD CONSTRAINT FK_3B610FED40C1FEA7 FOREIGN KEY (year_id) REFERENCES year (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_survey_report_access_history');
    }
}
