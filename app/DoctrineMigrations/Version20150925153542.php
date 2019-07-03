<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150925153542 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

	$this->addSql('DROP VIEW IF EXISTS `synapse`.`AUDIT_DASHBOARD_Organization_Survey_Cohort_Groupings`');
	$this->addSql('CREATE VIEW `synapse`.`AUDIT_DASHBOARD_Organization_Survey_Cohort_Groupings` AS
			SELECT ol.organization_id, o.campus_id,wl.status, wl.cohort_code, wl.survey_id, wl.wess_order_id , ol.organization_name, wl.open_date, wl.close_date, COUNT(DISTINCT(ops.person_id)) AS People_in_Cohort
			FROM synapse.organization_lang ol
			JOIN
			synapse.wess_link wl ON wl.org_id = ol.organization_id
			JOIN
			synapse.organization o ON o.id = ol.organization_id
			JOIN
			synapse.org_person_student ops ON wl.cohort_code = ops.surveycohort AND o.id = ops.organization_id
			WHERE ops.receivesurvey <> 0
			GROUP BY wl.wess_order_id
			ORDER BY ol.organization_id; ');

	$this->addSql('DROP VIEW IF EXISTS `synapse`.`AUDIT_DASHBOARD_Student_With_Survey_Response_And_Null_Cohort`;');
	$this->addSql('CREATE VIEW `synapse`.`AUDIT_DASHBOARD_Student_With_Survey_Response_And_Null_Cohort` AS
			SELECT ops.organization_id, o.campus_id AS Wess_InsID, ol.organization_name,  COUNT(DISTINCT(sr.person_id)) AS students_with_responses_and_null_cohort
			FROM synapse.org_person_student ops 
			JOIN 
			synapse.survey_response sr ON ops.person_id = sr.person_id AND ops.organization_id = sr.org_id
			JOIN 
			synapse.organization_lang ol ON ops.organization_id = ol.organization_id
			JOIN 
			synapse.organization o ON o.id = ol.organization_id
			WHERE ops.surveycohort IS NULL
			GROUP BY o.id;');

	$this->addSql('DROP VIEW IF EXISTS `synapse`.`AUDIT_DASHBOARD_Failed_Uploads_By_Type_Past_24_Hours`;');
	$this->addSql("CREATE VIEW `synapse`.`AUDIT_DASHBOARD_Failed_Uploads_By_Type_Past_24_Hours` AS
			SELECT 
    			upload_type, COUNT(*) AS Failed_Uploads
			FROM
    			synapse.upload_file_log
			WHERE
    			status = 'F'
        		AND created_at > DATE_SUB(now(), INTERVAL 1 DAY) 
			GROUP BY upload_type;");

	$this->addSql('DROP VIEW IF EXISTS `synapse`.`AUDIT_DASHBOARD_Failed_Uploads_By_Organization_Past_24_Hours`;');
	$this->addSql("CREATE VIEW `synapse`.`AUDIT_DASHBOARD_Failed_Uploads_By_Organization_Past_24_Hours` AS
			SELECT 
    			ol.organization_id, ol.organization_name, COUNT(*) AS Failed_Uploads
			FROM
    			synapse.upload_file_log ufl
    			JOIN 
    			synapse.organization_lang ol ON ol.organization_id = ufl.organization_id
			WHERE
    			ufl.status = 'F'
        		AND ufl.created_at > DATE_SUB(now(), INTERVAL 1 DAY)
			GROUP BY ufl.organization_id;");


        $this->addSql('DROP VIEW IF EXISTS `synapse`.`AUDIT_DASHBOARD_0_ReceiveSurvey_Students_With_Survey_Responses`;');
	$this->addSql('CREATE VIEW `synapse`.`AUDIT_DASHBOARD_0_ReceiveSurvey_Students_With_Survey_Responses` AS
			SELECT org_id, COUNT(DISTINCT(sr.person_id)) AS Number_of_Students
			FROM synapse.survey_response sr 
			JOIN 
			synapse.org_person_student ops ON sr.person_id = ops.person_id AND sr.org_id = ops.organization_id
			WHERE ops.receivesurvey = 0
			GROUP BY ops.organization_id
			ORDER BY ops.organization_id;');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
