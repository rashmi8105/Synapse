<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150826063456 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_calc_flags_student_reports ADD report_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE org_calc_flags_student_reports ADD CONSTRAINT FK_F4644A694BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('CREATE INDEX IDX_F4644A694BD2A4C0 ON org_calc_flags_student_reports (report_id)');
        $this->addSql('ALTER TABLE report_calculated_values ADD survey_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE report_calculated_values ADD CONSTRAINT FK_CB8DFCDCB3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('CREATE INDEX IDX_CB8DFCDCB3FE509D ON report_calculated_values (survey_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_calc_flags_student_reports DROP FOREIGN KEY FK_F4644A694BD2A4C0');
        $this->addSql('DROP INDEX IDX_F4644A694BD2A4C0 ON org_calc_flags_student_reports');
        $this->addSql('ALTER TABLE org_calc_flags_student_reports DROP report_id');
        $this->addSql('ALTER TABLE report_calculated_values DROP FOREIGN KEY FK_CB8DFCDCB3FE509D');
        $this->addSql('DROP INDEX IDX_CB8DFCDCB3FE509D ON report_calculated_values');
        $this->addSql('ALTER TABLE report_calculated_values DROP survey_id');
        
    }
}
