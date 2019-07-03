<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150924021556 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER EVENT survey_talking_points_calc DISABLE;');
        $this->addSql('DROP EVENT IF EXISTS survey_talking_points_calc;');

        $this->addSql('ALTER TABLE `synapse`.`org_calc_flags_talking_point` DROP INDEX `calculated_at_idx`, ADD INDEX `calculated_at_idx` (`calculated_at` ASC, `org_id` ASC, `person_id` ASC);');
        $this->addSql('ALTER TABLE `synapse`.`org_talking_points` ADD INDEX `OPTM` (`organization_id` ASC, `person_id` ASC, `talking_points_id` ASC, `modified_at` ASC);');

        $this->addSql('DROP VIEW IF EXISTS `person_talking_points_calculated`;');

        $this->addSql("CREATE OR REPLACE ALGORITHM=MERGE DEFINER=`synapsemaster`@`%`
            SQL SECURITY INVOKER
            VIEW `person_survey_talking_points_calculated` AS
	        select
                `orc`.`org_id` AS `org_id`,
                `orc`.`person_id` AS `person_id`,
                `tp`.`id` AS `talking_points_id`,
                `svr`.`survey_id` AS `survey_id`,
                `tp`.`talking_points_type` AS `response`,
                NOW() AS `CURRENT_TIMESTAMP`,
                NOW() AS `My_exp_CURRENT_TIMESTAMP`
            from `talking_points` `tp`
            join `survey_questions` `svq` on`tp`.`ebi_question_id` = `svq`.`ebi_question_id`
	        join `survey_response` `svr` on`svq`.`id` = `svr`.`survey_questions_id`
	            and case when `svr`.`response_type` = 'decimal' then `svr`.`decimal_value` end between `tp`.`min_range` and `tp`.`max_range`
	        join `org_calc_flags_talking_point` `orc` on`svr`.`person_id` = `orc`.`person_id` and `svr`.`org_id` = `orc`.`org_id`
	        join `org_person_student` `ops` on`orc`.`person_id` = `ops`.`person_id`
	        join `org_person_student_survey_link` `opssl` on`ops`.`surveycohort` = `opssl`.`cohort`
	            and `opssl`.`survey_id` = `svr`.`survey_id`
                and `opssl`.`person_id` = `svr`.`person_id`;
        ");

        $this->addSql("CREATE OR REPLACE ALGORITHM=MERGE DEFINER=`synapsemaster`@`%`
            SQL SECURITY INVOKER
            VIEW `person_MD_talking_points_calculated` AS
	        select `orc`.`org_id` AS `org_id`,`orc`.`person_id` AS `person_id`,`tp`.`id` AS `talking_points_id`,NULL AS `survey_id`,`tp`.`talking_points_type` AS `response`,
	            now() AS `CURRENT_TIMESTAMP`,now() AS `My_exp_CURRENT_TIMESTAMP`
            from `talking_points` `tp`
            join `person_ebi_metadata` `pem` on`tp`.`ebi_metadata_id` = `pem`.`ebi_metadata_id`
                and `pem`.`metadata_value` between `tp`.`min_range` and `tp`.`max_range`
	        join `org_calc_flags_talking_point` `orc` on`pem`.`person_id` = `orc`.`person_id`;
	    ");

        $this->addSql("DROP PROCEDURE IF EXISTS `Talking_Point_Calc`;
            CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Talking_Point_Calc`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
            DETERMINISTIC
            SQL SECURITY INVOKER
            BEGIN
	            DECLARE timeVar DATETIME;
	            SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

                WHILE(
                    NOW() < deadline
                    AND (select 1 from org_calc_flags_talking_point where calculated_at IS NULL LIMIT 1) > 0
                ) DO

                    SET timeVar = CURRENT_TIMESTAMP();

                    #--Carve out a chunk of work to do
                    UPDATE org_calc_flags_talking_point
                    SET
                        calculated_at=timeVar,
                        modified_at=timeVar
                    WHERE calculated_at IS NULL
                    LIMIT chunksize
                    ;

                    #--Sourced from surveys
                    insert into org_talking_points(organization_id,person_id,talking_points_id,survey_id,response, created_at, modified_at)
                    SELECT
                        pc.org_id,
                        pc.person_id,
                        pc.talking_points_id,
                        pc.survey_id,
                        pc.response,
                        timeVar,
                        timeVar
                    FROM
                        person_survey_talking_points_calculated pc
                    INNER JOIN org_calc_flags_talking_point AS O
                        ON (O.org_id,	O.person_id)
                            = (pc.org_id,	pc.person_id)
                    LEFT JOIN org_talking_points otp_out
                        ON (otp_out.organization_id, otp_out.person_id, otp_out.talking_points_id)
                            = (pc.org_id, pc.person_id, pc.talking_points_id)
                        AND pc.response <=> otp_out.response
                        AND otp_out.modified_at = (
                            SELECT MAX(otp_in.modified_at)
                                        FROM org_talking_points otp_in
                                        WHERE
                                            otp_out.organization_id=otp_in.organization_id
                                            AND otp_out.person_id=otp_in.person_id
                                            AND otp_out.talking_points_id=otp_in.talking_points_id
                        )
                    WHERE
                        otp_out.organization_id IS NULL #--Get only pc entries with no corresponding otp_out
                            AND pc.response IS NOT NULL
                            AND O.calculated_at=timeVar
                    ;


                    #--Sourced from metadata
                    insert into org_talking_points(organization_id,person_id,talking_points_id,survey_id,response, created_at, modified_at)
                    SELECT
                        pc.org_id,
                        pc.person_id,
                        pc.talking_points_id,
                        pc.survey_id,
                        pc.response,
                        timeVar,
                        timeVar
                    FROM
                        person_MD_talking_points_calculated pc
                    INNER JOIN org_calc_flags_talking_point AS O
                        ON (O.org_id,	O.person_id)
                            = (pc.org_id,	pc.person_id)
                    LEFT JOIN org_talking_points otp_out
                        ON (otp_out.organization_id, otp_out.person_id, otp_out.talking_points_id)
                            = (pc.org_id, pc.person_id, pc.talking_points_id)
                        AND pc.response <=> otp_out.response
                        AND otp_out.modified_at = (
                            SELECT MAX(otp_in.modified_at)
                                    FROM org_talking_points otp_in
                                    WHERE
                                        otp_out.organization_id=otp_in.organization_id
                                        AND otp_out.person_id=otp_in.person_id
                                        AND otp_out.talking_points_id=otp_in.talking_points_id
                        )
                    WHERE
                        otp_out.organization_id IS NULL #--Get only pc entries with no corresponding otp_out
                            AND pc.response IS NOT NULL
                            AND O.calculated_at=timeVar
                    ;


                    UPDATE org_calc_flags_talking_point orf
                    LEFT JOIN org_talking_points AS tp
                        ON tp.organization_id = orf.org_id
                            AND tp.person_id = orf.person_id
                            AND tp.modified_at = timeVar
                    SET
                        orf.calculated_at = '1900-01-01 00:00:00',
                        orf.modified_at = timeVar
                    WHERE
                        orf.calculated_at = timeVar
                            AND tp.organization_id IS NULL #--These got no value out of calculation
                    ;

            	END WHILE;

            END");

        $this->addSql("CREATE EVENT survey_talking_points_calc
            ON SCHEDULE EVERY 15 minute
            STARTS '2015-08-16 03:14:37'
            DISABLE ON SLAVE
            DO BEGIN
                CALL Talking_Point_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 1000);
            END");

        $this->addSql('ALTER EVENT survey_talking_points_calc ENABLE;');

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
