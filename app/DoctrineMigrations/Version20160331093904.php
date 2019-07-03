<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script: migrate data from synapse.org_person_student_survey_link to synapse.org_person_student_survey
 */
class Version20160331093904 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // This migration script is moving data in org_person_student_survey_link into org_person_student_survey.
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql( "
             INSERT IGNORE INTO synapse.org_person_student_survey(organization_id, person_id, survey_id, receive_survey)
               SELECT
                 opssl.org_id,
                 opssl.person_id,
                 opssl.survey_id,
                 opssl.receivesurvey
               FROM
                 synapse.org_person_student_survey_link opssl
               WHERE
                 opssl.receivesurvey IS NOT NULL AND opssl.deleted_at IS NULL;
        ");
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        
    }
}
