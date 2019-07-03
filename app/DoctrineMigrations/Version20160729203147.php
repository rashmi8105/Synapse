<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Updating errant OPSSL completion status records.
 */
class Version20160729203147 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        //Org_person_student_survey_link: Students that have responses but are marked something other than a completed status will be marked as CompletedAll
        $this->addSql("UPDATE org_person_student_survey_link SET survey_completion_status = 'CompletedAll', modified_at = NOW(), modified_by = -25 WHERE survey_completion_status NOT IN ( 'CompletedAll', 'CompletedMandatory') AND Has_Responses = 'Yes';");

        //Org_person_student_survey_link: Students that do not have responses but are marked something other than Assigned will be marked as Assigned.
        $this->addSql("UPDATE org_person_student_survey_link SET survey_completion_status = 'Assigned', modified_at = NOW(), modified_by = -25 WHERE survey_completion_status NOT IN ( 'Assigned') AND Has_Responses = 'No';");

        //Org_person_student_survey_link: Students with a null survey_opt_out_status will be set to 'No'. This situation only applies to Kurt's dataset (organization 203) on all environments, but should still be corrected.
        $this->addSql("UPDATE org_person_student_survey_link SET survey_opt_out_status = 'No', modified_at = NOW(), modified_by = -25 WHERE survey_opt_out_status IS NULL;");

        //Add in view to show completion status checks
        $this->addSql("CREATE OR REPLACE
                          ALGORITHM = UNDEFINED
                          DEFINER = `synapsemaster`@`%`
                          SQL SECURITY DEFINER
                        VIEW `AUDIT_DASHBOARD_Survey_Completion_Status` AS
                        SELECT
                          survey_completion_status,
                          survey_opt_out_status,
                          Has_Responses,
                          CASE WHEN (survey_completion_status = 'Assigned' AND survey_opt_out_status = 'Yes' AND Has_Responses = 'No') THEN 'Yes'
                          WHEN (survey_completion_status = 'Assigned' AND survey_opt_out_status = 'No'  AND Has_Responses = 'No') THEN 'Yes'
                          WHEN (survey_completion_status = 'CompletedMandatory' AND survey_opt_out_status = 'No'   AND Has_Responses = 'Yes') THEN 'Yes'
                          WHEN (survey_completion_status = 'CompletedMandatory' AND survey_opt_out_status = 'Yes'  AND Has_Responses = 'Yes') THEN 'Yes'
                          WHEN (survey_completion_status = 'CompletedAll' AND survey_opt_out_status = 'No'   AND Has_Responses = 'Yes') THEN 'Yes'
                          WHEN (survey_completion_status = 'CompletedAll' AND survey_opt_out_status = 'Yes'  AND Has_Responses = 'Yes') THEN 'Yes'
                          ELSE 'No'
                          END as valid_combination,
                          CASE WHEN (survey_completion_status = 'Assigned' AND survey_opt_out_status = 'Yes' AND Has_Responses = 'No') THEN 'No'
                          WHEN (survey_completion_status = 'Assigned' AND survey_opt_out_status = 'No'  AND Has_Responses = 'No') THEN 'No'
                          WHEN (survey_completion_status = 'CompletedMandatory' AND survey_opt_out_status = 'No'   AND Has_Responses = 'Yes') THEN 'No'
                          WHEN (survey_completion_status = 'CompletedMandatory' AND survey_opt_out_status = 'Yes'  AND Has_Responses = 'Yes') THEN 'No'
                          WHEN (survey_completion_status = 'CompletedAll' AND survey_opt_out_status = 'No'   AND Has_Responses = 'Yes') THEN 'No'
                          WHEN (survey_completion_status = 'CompletedAll' AND survey_opt_out_status = 'Yes'  AND Has_Responses = 'Yes') THEN 'No'
                          WHEN modified_at < NOW() - INTERVAL 1 HOUR THEN 'Yes'
                          ELSE 'No'
                          END as needs_manual_intervention,
                          COUNT(*) as student_survey_link_count,
                          GROUP_CONCAT(DISTINCT org_id ORDER BY org_id ASC) as org_id,
                          MAX(modified_at) AS date_last_updated
                        FROM synapse.org_person_student_survey_link
                        GROUP BY survey_completion_status, survey_opt_out_status, Has_Responses;
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
