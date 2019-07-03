<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160406142119 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        /**
         * Updating Issues Calculation Views 
         * Using opssl has some performance advantages and having cohort available
         * provides these views with more flexibility, because of these concerns,
         * we have to deal with the issue of bad cohorts that exist in OPSSL.
         * Opting to keep cohort in these views, we need to use 
         * org_person_student_cohort (opsc) to limit the cohorts to the correct ones 
         * for the designated academic year.  In order to use these views correctly, 
         * there needs to be a way to pass org_academic_year_id into the system.
         * Also pushed up org_academic_year_id to the top view so a new variable
         * org_academic_year_id can be used as criteria.
         */
        

        $this->addSQL("CREATE OR REPLACE
		    ALGORITHM = MERGE
		    DEFINER = `synapsemaster`@`%`
		    SQL SECURITY DEFINER
			VIEW `Issues_Factors` AS
		    SELECT
		        `pfc`.`organization_id` AS `org_id`,
		        `pfc`.`person_id` AS `student_id`,
		        `pfc`.`survey_id` AS `survey_id`,
		        `iss`.`id` AS `issue_id`,
		        `wl`.`cohort_code` AS `cohort`,
		        `pfc`.`factor_id` AS `factor_id`,
		        `ISFS`.`faculty_id` AS `faculty_id`,
		        `pfc`.`mean_value` AS `permitted_value`,
		        `pfc`.`modified_at` AS `modified_at`,
		        `opsc`.`org_academic_year_id` AS `org_academic_year_id`
		    FROM
		        `org_faculty_student_permission_map` `ISFS`
		        INNER JOIN `org_person_student_survey_link` `opssl` ON `opssl`.`org_id` = `ISFS`.`org_id`
		            AND `opssl`.`person_id` = `ISFS`.`student_id`
		            AND `opssl`.`deleted_at` IS NULL
				INNER JOIN `org_person_student_cohort` `opsc` ON `opsc`.`organization_id` = `opssl`.`org_id` 
					AND `opsc`.`person_id` = `opssl`.`person_id`
		            AND `opsc`.`cohort` = `opssl`.`cohort`
		            AND `opsc`.`org_academic_year_id` = `opssl`.`org_academic_year_id`
		            AND `opsc`.`deleted_at` IS NULL
		        INNER JOIN `person_factor_calculated` `pfc` ON `ISFS`.`student_id` = `pfc`.`person_id`
		            AND `ISFS`.`org_id` = `pfc`.`organization_id`
		            AND `opssl`.`survey_id` = `pfc`.`survey_id`  /*This is a key performance line*/
		            AND `pfc`.`deleted_at` IS NULL
		        INNER JOIN `issue` `iss` ON `iss`.`factor_id` = `pfc`.`factor_id`
		            AND `iss`.`survey_id` = `pfc`.`survey_id`
		            AND `iss`.`deleted_at` IS NULL
		        INNER JOIN `wess_link` `wl` ON `wl`.`survey_id` = `pfc`.`survey_id`
		            AND `wl`.`org_id` = `pfc`.`organization_id`
		            AND `wl`.`cohort_code` = `opssl`.`cohort`
		            AND `wl`.`status` = 'closed'
		            AND `wl`.`deleted_at` IS NULL
		        INNER JOIN `datablock_questions` `dq` ON `dq`.`factor_id` = `pfc`.`factor_id`
		            AND `dq`.`deleted_at` IS NULL
		        INNER JOIN `org_permissionset_datablock` `opd` ON `opd`.`organization_id` = `pfc`.`organization_id`
		            AND `opd`.`datablock_id` = `dq`.`datablock_id`
		            AND `opd`.`org_permissionset_id` = `ISFS`.`permissionset_id`
		            AND `opd`.`deleted_at` IS NULL
		    WHERE
		        `pfc`.`id` = (
					SELECT
						`fc`.`id`
					FROM
		                `person_factor_calculated` `fc`
		            WHERE
		                `fc`.`organization_id` = `pfc`.`organization_id`
		                    AND `fc`.`person_id` = `pfc`.`person_id`
		                    AND `fc`.`factor_id` = `pfc`.`factor_id`
		                    AND `fc`.`survey_id` = `pfc`.`survey_id`
		                    AND `fc`.`deleted_at` IS NULL
		            ORDER BY `fc`.`modified_at` DESC
		            LIMIT 1);");

        $this->addSQL("CREATE OR REPLACE
		    ALGORITHM = MERGE
		    DEFINER = `synapsemaster`@`%`
		    SQL SECURITY DEFINER
			VIEW `Issues_Survey_Questions` AS
		    SELECT
		        `sr`.`org_id` AS `org_id`,
		        `sr`.`person_id` AS `student_id`,
		        `sr`.`survey_id` AS `survey_id`,
		        `iss`.`id` AS `issue_id`,
		        `wl`.`cohort_code` AS `cohort`,
		        `sq`.`id` AS `survey_question_id`,
		        `sq`.`ebi_question_id` AS `ebi_question_id`,
		        `ISFS`.`faculty_id` AS `faculty_id`,
		        `sr`.`decimal_value` AS `permitted_value`,
		        `sr`.`modified_at` AS `modified_at`,
		        `sr`.`org_academic_year_id` as `org_academic_year_id`
		    FROM
		        `org_faculty_student_permission_map` `ISFS`
		        INNER JOIN `org_person_student_survey_link` `opssl` ON `ISFS`.`org_id` = `opssl`.`org_id`
		            AND `opssl`.`person_id` = `ISFS`.`student_id`
		            AND `opssl`.`deleted_at` IS NULL
		        INNER JOIN `survey_response` `sr` FORCE INDEX (FK_SURVEY_RESPONSE_ORGANIZATION1) ON `ISFS`.`student_id` = `sr`.`person_id`
		            AND `ISFS`.`org_id` = `sr`.`org_id`
		            AND `opssl`.`org_academic_year_id` = `sr`.`org_academic_year_id`
		            AND `sr`.`deleted_at` IS NULL
				    INNER JOIN `org_person_student_cohort` `opsc` ON `opsc`.`organization_id` = `sr`.`org_id`
				        AND `opsc`.`person_id` = `sr`.`person_id`
					      AND `opsc`.`org_academic_year_id` = `sr`.`org_academic_year_id`
		            AND `opsc`.`cohort` = `opssl`.`cohort`
		            AND `opsc`.`deleted_at` IS NULL
		        INNER JOIN `issue` `iss` ON `iss`.`survey_questions_id` = `sr`.`survey_questions_id`
		            AND `iss`.`survey_id` = `sr`.`survey_id`
		            AND `iss`.`deleted_at` IS NULL
		        INNER JOIN `survey_questions` `sq` ON `sr`.`survey_questions_id` = `sq`.`id`
		            AND `sq`.`survey_id` = `sr`.`survey_id`
		            AND `sq`.`deleted_at` IS NULL
		        INNER JOIN `ebi_question` `eq` ON `sq`.`ebi_question_id` = `eq`.`id`
		            AND `eq`.`deleted_at` IS NULL
		        INNER JOIN `datablock_questions` `dq` USE INDEX (PERMFUNC) ON `dq`.`ebi_question_id` = `eq`.`id`
		            AND `dq`.`deleted_at` IS NULL
		        INNER JOIN `org_permissionset_datablock` `opd` ON `opd`.`organization_id` = `sr`.`org_id`
		            AND `opd`.`datablock_id` = `dq`.`datablock_id`
		            AND `opd`.`org_permissionset_id` = `ISFS`.`permissionset_id`
		            AND `opd`.`deleted_at` IS NULL
		        INNER JOIN `wess_link` `wl` ON `wl`.`survey_id` = `sr`.`survey_id`
		            AND `wl`.`org_id` = `sr`.`org_id`
		            AND `wl`.`cohort_code` = `opsc`.`cohort`
		            AND `wl`.`status` = 'closed'
		            AND `wl`.`deleted_at` IS NULL;");

        $this->addSQL("CREATE OR REPLACE
        ALGORITHM = UNDEFINED 
        DEFINER = `synapsemaster`@`%` 
        SQL SECURITY DEFINER
        VIEW `Issues_Datum` AS
        SELECT 
            `ofs`.`organization_id` AS `org_id`,
            `ofs`.`person_id` AS `faculty_id`,
            COALESCE(`ISQ`.`survey_id`, `ISF`.`survey_id`) AS `survey_id`,
            COALESCE(`ISQ`.`student_id`, `ISF`.`student_id`) AS `student_id`,
            COALESCE(`ISQ`.`issue_id`, `ISF`.`issue_id`) AS `issue_id`,
            COALESCE(`ISQ`.`cohort`, `ISF`.`cohort`) AS `cohort`,
            `CU`.`datum_type` AS `type`,
            COALESCE(`ISQ`.`survey_question_id`,
                    `ISF`.`factor_id`) AS `source_id`,
            COALESCE(`ISQ`.`permitted_value`,
                    `ISF`.`permitted_value`) AS `source_value`,
            COALESCE(`ISF`.`org_academic_year_id`, 
                    `ISQ`.`org_academic_year_id`) AS `org_academic_year_id`,
            COALESCE(`ISQ`.`modified_at`, `ISF`.`modified_at`) AS `modified_at`
        FROM
            `org_person_faculty` `ofs`
            JOIN `Factor_Question_Constants` `CU`
            LEFT JOIN `Issues_Survey_Questions` `ISQ` ON `CU`.`datum_type` = 'Question'
                AND `ofs`.`person_id` = `ISQ`.`faculty_id`
                AND `ofs`.`organization_id` = `ISQ`.`org_id`
            LEFT JOIN `Issues_Factors` `ISF` ON `CU`.`datum_type` = 'Factor'
                AND `ofs`.`person_id` = `ISF`.`faculty_id`
                AND `ofs`.`organization_id` = `ISF`.`org_id`
        WHERE
            `ISQ`.`permitted_value` IS NOT NULL
            OR `ISF`.`permitted_value` IS NOT NULL;");

        $this->addSQL("CREATE OR REPLACE
        ALGORITHM = UNDEFINED 
        DEFINER = `synapsemaster`@`%` 
        SQL SECURITY DEFINER
        VIEW `Issues_Calculation` AS
        SELECT 
            `theID`.`org_id` AS `org_id`,
            `theID`.`faculty_id` AS `faculty_id`,
            `theID`.`survey_id` AS `survey_id`,
            `theID`.`issue_id` AS `issue_id`,
            `theID`.`cohort` AS `cohort`,
            `theID`.`student_id` AS `student_id`,
            IFNULL(((`theID`.`source_value` BETWEEN `iss`.`min` AND `iss`.`max`)
                        OR (CAST(`theID`.`source_value` AS UNSIGNED) = `eqo`.`option_value`)),
                    0) AS `has_issue`,
            `theID`.`org_academic_year_id` as `org_academic_year_id`,
            `issl`.`name` AS `name`,
            `iss`.`icon` AS `icon`
        FROM
            `Issues_Datum` `theID`
            JOIN `issue` `iss` ON `iss`.`id` = `theID`.`issue_id`
            LEFT JOIN `issue_lang` `issl` ON `iss`.`id` = `issl`.`issue_id`
            LEFT JOIN `issue_options` `issO` ON `iss`.`id` = `issO`.`issue_id`
            LEFT JOIN `ebi_question_options` `eqo` ON `eqo`.`id` = `issO`.`ebi_question_options_id`;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
