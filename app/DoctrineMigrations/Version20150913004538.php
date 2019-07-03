<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150913004538 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
                ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSQL('update intent_to_leave set text = "darkgray" where min_value = 99;');

        $this->addSQL('ALTER TABLE `synapse`.`wess_link` DROP INDEX `fk_wess_link_organization1` , ADD INDEX `fk_wess_link_organization1` (`org_id` ASC, `year_id` ASC, `status` ASC, `survey_id` ASC);');
        $this->addSQL('ALTER TABLE `synapse`.`ebi_metadata` ADD INDEX `EM_metakey` (`meta_key` ASC);');
        $this->addSQL('ALTER TABLE `synapse`.`person_ebi_metadata` ADD INDEX `PEM_Person_ebimetaperson` (`ebi_metadata_id` ASC, `person_id` ASC);');
        $this->addSQL('ALTER TABLE `synapse`.`risk_model_levels` DROP INDEX `fk_risk_model_levels_risk_level1_idx` , ADD INDEX `fk_risk_model_levels_risk_level1_idx` (`risk_level` ASC, `risk_model_id` ASC);');
        $this->addSQL('ALTER TABLE `synapse`.`survey_response` DROP INDEX `fk_survey_response_person1` , ADD INDEX `fk_survey_response_person1` (`person_id` ASC, `survey_id` ASC);');

        $yearText = '$$yearText$$';
        $personIds = '$$personIds$$';
        $orgId = '$$orgId$$';

        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id, P.id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (COUNT(DISTINCT (acl.id))) AS cnt, OPS.status, OPS.photo_url, pem.metadata_value AS class_level FROM person P LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl ON (acl.person_id_student = P.id) INNER JOIN survey_response AS SR ON SR.person_id = P.id INNER JOIN wess_link AS WL ON WL.survey_id = SR.survey_id LEFT JOIN person_ebi_metadata AS pem ON (pem.person_id = P.id AND pem.ebi_metadata_id IN (SELECT id FROM ebi_metadata WHERE meta_key = "ClassLevel")) WHERE P.id IN ($personIds ) AND WL.year_id = $yearText AND WL.status = "launched" AND WL.org_id = $orgId GROUP BY P.id order by P.risk_level, P.lastname, P.firstname' WHERE `query_key`='Respondents_To_Current_Survey';
CDATA;
        $this->addSql($query);

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
