<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150923193541 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

         $this->addSQL('ALTER TABLE `synapse`.`person_ebi_metadata` 
    ADD INDEX `JC_8ABD58A3217BBB47` (`person_id` ASC, `ebi_metadata_id` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`person_org_metadata` 
    ADD INDEX `JC_D0B544BA217BBB47` (`person_id` ASC, `org_metadata_id` ASC);');

           $this->addSQL('ALTER TABLE `synapse`.`org_calc_flags_risk` 
    DROP INDEX `person_idx` ,
    ADD INDEX `person_idx` (`person_id` ASC, `calculated_at` ASC),
    DROP INDEX `id_idx` ;');

        $this->addSQL('ALTER TABLE `synapse`.`risk_variable` 
    ADD INDEX `RV_agg` (`calc_type` ASC, `calculation_start_date` ASC, `calculation_end_date` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`ebi_metadata` 
    ADD INDEX `modified` (`modified_at` ASC, `created_at` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`org_metadata` 
    ADD INDEX `modified` (`modified_at` ASC, `created_at` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`org_question_response` 
    ADD INDEX `modified` (`modified_at` ASC, `created_at` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`survey_response` 
    ADD INDEX `modified` (`modified_at` ASC, `created_at` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`factor_questions` 
    ADD INDEX `modified` (`modified_at` ASC, `created_at` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`person_factor_calculated` 
    ADD INDEX `modified` (`modified_at` ASC, `created_at` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`org_question_response` 
    ADD INDEX `created` (`created_at` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`survey_response` 
    ADD INDEX `created` (`created_at` ASC);');

       

        $this->addSQL('ALTER TABLE `synapse`.`survey_response` 
    DROP INDEX `fk_survey_response_organization1` ,
    ADD INDEX `fk_survey_response_organization1` (`org_id` ASC, `person_id` ASC, `survey_questions_id` ASC, `created_at` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`org_question_response` 
    DROP INDEX `fk_org_question_response_organization1_idx` ,
    ADD INDEX `fk_org_question_response_organization1_idx` (`org_id` ASC, `person_id` ASC, `org_question_id` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`risk_variable_range` 
    DROP INDEX `fk_risk_model_bucket_range_risk_variable1_idx` ,
    ADD INDEX `fk_risk_model_bucket_range_risk_variable1_idx` (`risk_variable_id` ASC, `min` ASC, `max` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`org_calc_flags_risk` 
    DROP INDEX `calculated_at_idx` ,
    ADD INDEX `calculated_at_idx` (`calculated_at` ASC, `modified_at` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`academic_update` 
    DROP INDEX `fk_academic_update_organization1_idx` ,
    ADD INDEX `fk_academic_update_organization1_idx` (`org_id` ASC, `person_id_student` ASC, `modified_at` ASC, `org_courses_id` ASC);'); 

        $this->addSQL('ALTER TABLE `synapse`.`person_risk_level_history` 
    ADD INDEX `captured` (`date_captured` ASC),
    DROP INDEX `fk_person_risk_level_history_person1_idx` ;');

     




    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


    }
}
