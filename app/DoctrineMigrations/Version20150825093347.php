<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150825093347 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
        $this->addSql('ALTER TABLE org_person_student_survey_link ADD survey_assigned_date DATETIME DEFAULT NULL, ADD survey_completion_date DATETIME DEFAULT NULL, ADD survey_completion_status enum(\'Assigned\', \'InProgress\', \'CompletedMandatory\', \'CompletedAll\'), ADD survey_opt_out_status enum(\'Yes\', \'No\')');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
       
        $this->addSql('ALTER TABLE org_person_student_survey_link DROP survey_assigned_date, DROP survey_completion_date, DROP survey_completion_status, DROP survey_opt_out_status');
    }
}
