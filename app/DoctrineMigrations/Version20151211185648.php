<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151211185648 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL("CREATE OR REPLACE
        ALGORITHM=MERGE DEFINER=`synapsemaster`@`%` 
        SQL SECURITY INVOKER 
        VIEW `person_survey_talking_points_calculated` AS 
        #--explain
            select 
                `orc`.`org_id` AS `org_id`,
                `orc`.`person_id` AS `person_id`,
                `tp`.`id` AS `talking_points_id`,
                `tp`.`ebi_question_id` AS `ebi_question_id`,
                `svr`.`survey_id` AS `survey_id`,
                `tp`.`talking_points_type` AS `response`,
                `svr`.`modified_at` AS `source_modified_at`
            from `talking_points` `tp` 
            join `survey_questions` `svq` 
                on`tp`.`ebi_question_id` = `svq`.`ebi_question_id` 
            join `survey_response` `svr` 
                on`svq`.`id` = `svr`.`survey_questions_id` 
                and case when `svr`.`response_type` = 'decimal' then `svr`.`decimal_value` end between `tp`.`min_range` and `tp`.`max_range` 
            join `org_calc_flags_talking_point` `orc` 
                on`svr`.`person_id` = `orc`.`person_id` 
                and `svr`.`org_id` = `orc`.`org_id`
            join `org_person_student` `ops` 
                on`orc`.`person_id` = `ops`.`person_id` 
            join `org_person_student_survey_link` `opssl` 
                on`ops`.`surveycohort` = `opssl`.`cohort` 
                and `opssl`.`survey_id` = `svr`.`survey_id` 
                and `opssl`.`person_id` = `svr`.`person_id`
                and `opssl`.`org_id` = `svr`.`org_id`
            WHERE `tp`.`deleted_at` is null AND `svq`.`deleted_at` is null AND `svr`.`deleted_at` is null AND `orc`.`deleted_at` is null 
            AND `ops`.`deleted_at` is null AND `opssl`.`deleted_at` is null;");


        $this->addSQL("CREATE OR REPLACE
        ALGORITHM=MERGE DEFINER=`synapsemaster`@`%` 
        SQL SECURITY INVOKER 
        VIEW `person_MD_talking_points_calculated` AS
        #--EXPLAIN
            select `orc`.`org_id` AS `org_id`, 
                `orc`.`person_id` AS `person_id`, 
                `tp`.`id` AS `talking_points_id`,
                `pem`.`ebi_metadata_id` AS `ebi_metadata_id`,
                `pem`.`org_academic_year_id` AS `org_academic_year_id`, 
                `pem`.`org_academic_terms_id` AS `org_academic_terms_id`,
                `tp`.`talking_points_type` AS `response`, 
                `pem`.`modified_at` AS `source_modified_at`
            from `talking_points` `tp` 
            join `person_ebi_metadata` `pem` 
                on`tp`.`ebi_metadata_id` = `pem`.`ebi_metadata_id` 
                and `pem`.`metadata_value` between `tp`.`min_range` and `tp`.`max_range` 
            join `org_calc_flags_talking_point` `orc` 
                on`pem`.`person_id` = `orc`.`person_id` 
            WHERE `tp`.`deleted_at` is null AND `pem`.`deleted_at` is null and `orc`.`deleted_at` is null;");

        $this->addSQL('DROP PROCEDURE IF EXISTS `Talking_Point_Calc`;');

        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Talking_Point_Calc`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
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
                AND deleted_at is NULL
                LIMIT chunksize
                ;
                
                
                #--Sourced from surveys
                insert into org_talking_points(organization_id, person_id, talking_points_id, survey_id, response, source_modified_at, created_at, modified_at)
                #--EXPLAIN
                SELECT 
                    pc.org_id,
                    pc.person_id,
                    pc.talking_points_id,
                    pc.survey_id,
                    pc.response,
                    pc.source_modified_at,
                    timeVar,
                    timeVar
                FROM
                    person_survey_talking_points_calculated pc
                INNER JOIN org_calc_flags_talking_point AS O
                    ON (O.org_id,   O.person_id)
                    = (pc.org_id,   pc.person_id)
                LEFT JOIN org_talking_points otp_out
                    ON (otp_out.organization_id, otp_out.person_id, otp_out.talking_points_id, otp_out.survey_id)
                    = (pc.org_id, pc.person_id, pc.talking_points_id, pc.survey_id)
                    AND pc.response <=> otp_out.response
                    AND otp_out.source_modified_at = (
                        SELECT MAX(otp_in.source_modified_at)
                        FROM org_talking_points otp_in
                        INNER JOIN talking_points tp on otp_in.talking_points_id = tp.id
                        WHERE 
                            otp_out.organization_id = otp_in.organization_id 
                            AND otp_out.person_id = otp_in.person_id
                            AND pc.ebi_question_id = tp.ebi_question_id
                            AND otp_out.survey_id = otp_in.survey_id
                            AND tp.deleted_at is null
                            AND otp_in.deleted_at is null
                    )
                WHERE
                    otp_out.organization_id IS NULL #--Get only pc entries with no corresponding otp_out
                    AND pc.response IS NOT NULL
                    AND O.calculated_at=timeVar
                    AND otp_out.deleted_at IS NULL
                ;
                
                
                #--Sourced from metadata
                insert into org_talking_points(organization_id, person_id, talking_points_id, org_academic_year_id, org_academic_terms_id, response, source_modified_at, created_at, modified_at)
                #--EXPLAIN
                SELECT 
                    pc.org_id,
                    pc.person_id,
                    pc.talking_points_id,
                    pc.org_academic_year_id, 
                    pc.org_academic_terms_id,
                    pc.response,
                    pc.source_modified_at,
                    timeVar,
                    timeVar
                FROM
                    person_MD_talking_points_calculated pc
                INNER JOIN org_calc_flags_talking_point AS O
                    ON (O.org_id,   O.person_id)
                    = (pc.org_id,   pc.person_id)
                LEFT JOIN org_talking_points otp_out
                    ON (otp_out.organization_id, otp_out.person_id, otp_out.talking_points_id, otp_out.org_academic_year_id, otp_out.org_academic_terms_id)
                    <=> (pc.org_id, pc.person_id, pc.talking_points_id, pc.org_academic_year_id, pc.org_academic_terms_id)
                    AND pc.response <=> otp_out.response
                    AND otp_out.source_modified_at = (
                        SELECT MAX(otp_in.source_modified_at)
                        FROM org_talking_points otp_in
                        INNER JOIN talking_points tp on otp_in.talking_points_id = tp.id
                        WHERE 
                            otp_out.organization_id = otp_in.organization_id 
                            AND otp_out.person_id = otp_in.person_id
                            AND pc.ebi_metadata_id = tp.ebi_metadata_id
                            AND otp_out.org_academic_year_id <=> otp_in.org_academic_year_id
                            AND otp_out.org_academic_terms_id <=> otp_in.org_academic_terms_id
                            AND tp.deleted_at is null
                            AND otp_in.deleted_at is null
                    )
                WHERE
                    otp_out.organization_id IS NULL #--Get only pc entries with no corresponding otp_out
                    AND pc.response IS NOT NULL
                    AND O.calculated_at=timeVar
                    AND otp_out.deleted_at IS NULL
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
                    AND tp.organization_id IS NULL
                    AND orf.deleted_at is null
                    AND tp.deleted_at is null#--These got no value out of calculation
                ;

            END WHILE;
            
        END");
        

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
