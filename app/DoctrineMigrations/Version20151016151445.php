<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151016151445 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $yearText = '$$yearText$$';
        $personIds = '$$personIds$$';
        $orgId = '$$orgId$$';

        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id, P.id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, ( SELECT RML.risk_model_id FROM risk_model_levels AS RML WHERE RML.risk_level = RL.id LIMIT 1) AS risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (COUNT(DISTINCT (acl.id))) AS cnt, OPS.status, OPS.photo_url, pem.metadata_value AS class_level FROM person P LEFT JOIN intent_to_leave AS IL FORCE INDEX (PRIMARY) ON P.intent_to_leave = IL.id LEFT JOIN risk_level AS RL FORCE INDEX (PRIMARY) ON P.risk_level = RL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl ON (acl.person_id_student = P.id) INNER JOIN survey_response AS SR ON SR.person_id = P.id INNER JOIN wess_link AS WL ON WL.survey_id = SR.survey_id LEFT JOIN person_ebi_metadata AS pem ON (pem.person_id = P.id AND pem.ebi_metadata_id IN (SELECT id FROM ebi_metadata WHERE meta_key = "ClassLevel")) WHERE P.id IN ($personIds ) AND WL.id = (SELECT id from wess_link where year_id = $yearText AND org_id = $orgId AND status in ("launched", "closed") ORDER BY close_date DESC LIMIT 1) GROUP BY P.id ORDER BY P.risk_level , P.lastname , P.firstname' WHERE `query_key`='Respondents_To_Current_Survey';
CDATA;
        
        $this->addSql($query);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
