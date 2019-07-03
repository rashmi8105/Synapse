<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160405185303 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        /**
         * Updating Talking Points View 
         * Removing references to OPSSL since it contains bad
         * references to cohort.  It also is not necessary for this calculation
         * to be sure there is a survey link.
         * 
         */
        $this->addSQL("CREATE OR REPLACE ALGORITHM = MERGE 
		DEFINER = `synapsemaster`@`%` 
		SQL SECURITY INVOKER
		VIEW `person_survey_talking_points_calculated` AS
		    SELECT 
		        `ocftp`.`org_id` AS `org_id`,
		        `ocftp`.`person_id` AS `person_id`,
		        `tp`.`id` AS `talking_points_id`,
		        `tp`.`ebi_question_id` AS `ebi_question_id`,
		        `svr`.`survey_id` AS `survey_id`,
		        `tp`.`talking_points_type` AS `response`,
		        `svr`.`modified_at` AS `source_modified_at`
		    FROM 
				`talking_points` `tp`
					INNER JOIN `survey_questions` AS `svq` ON `tp`.`ebi_question_id` = `svq`.`ebi_question_id`
					INNER JOIN `survey_response` AS `svr` ON `svq`.`id` = `svr`.`survey_questions_id`
						AND CASE WHEN `svr`.`response_type` = 'decimal' THEN `svr`.`decimal_value` END 
							BETWEEN `tp`.`min_range` AND `tp`.`max_range`
					INNER JOIN `org_calc_flags_talking_point` AS `ocftp` ON `svr`.`person_id` = `ocftp`.`person_id`
						AND `svr`.`org_id` = `ocftp`.`org_id`
			WHERE 
				`tp`.`deleted_at` IS NULL
				AND `svq`.`deleted_at` IS NULL
				AND `svr`.`deleted_at` IS NULL
				AND `ocftp`.`deleted_at` IS NULL;");
        

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
