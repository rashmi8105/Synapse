<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150916143633 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // This creates the views that tells the user which flag calculations have been ran
        $this->addSql("
            CREATE OR REPLACE
                DEFINER=`synapsemaster`@`%` VIEW `DASHBOARD_Student_Calculations` AS

                SELECT
                   'Factor' as 'Calculation Type',
                        sum(case when calculated_at  > '1910-10-10 10:10:10'then 1 else 0 end) as 'Calculated Students',
                        sum(case when calculated_at  = '1900-01-01 00:00:00'then 1 else 0 end) as 'Students With No Data',
                        sum(case when calculated_at is null then 1 else 0 end) as 'Flagged For Calculation',
                        sum(case when calculated_at  = '1910-10-10 10:10:10'then 1 else 0 end) as 'No Survey Data',
                        COUNT(*) as 'Total Students',
                        concat(((sum(case when calculated_at  > '1910-10-10 10:10:10'then 1 else 0 end)/COUNT(*))*100), '%') As 'Calculated Percentage',
                        concat(((sum(case when calculated_at = '1900-01-01 00:00:00' then 1 else 0 end)/COUNT(*))*100), '%')As 'No Data Percentage',
                        concat(((sum(case when calculated_at is null then 1 else 0 end)/count(*))*100), '%') As 'Calculating Percentage',
                        concat(((sum(case when calculated_at = '1910-10-10 10:10:10' then 1 else 0 end)/COUNT(*))*100), '%')As 'No Survey Data Percentage'
                    FROM
                synapse.org_calc_flags_factor
                    union
                    SELECT
                   'Risk',
                        sum(case when calculated_at  > '1910-10-10 10:10:10'then 1 else 0 end) as 'Calculated Students',
                        sum(case when calculated_at  = '1900-01-01 00:00:00'then 1 else 0 end) as 'Students With No Data',
                        sum(case when calculated_at is null then 1 else 0 end) as 'Flagged For Calculation',
                        sum(case when calculated_at  = '1910-10-10 10:10:10'then 1 else 0 end) as 'No Survey Data',
                        COUNT(*) as 'Total Students',
                        concat(((sum(case when calculated_at  > '1910-10-10 10:10:10'then 1 else 0 end)/COUNT(*))*100), '%') As 'Calculated Percentage',
                        concat(((sum(case when calculated_at = '1900-01-01 00:00:00' then 1 else 0 end)/COUNT(*))*100), '%')As 'No Data Percentage',
                        concat(((sum(case when calculated_at is null then 1 else 0 end)/count(*))*100), '%') As 'Calculating Percentage',
                        concat(((sum(case when calculated_at = '1910-10-10 10:10:10' then 1 else 0 end)/COUNT(*))*100), '%')As 'No Survey Data Percentage'
                    FROM
                    synapse.org_calc_flags_risk union
                    SELECT
                   'Success Marker',
                        sum(case when calculated_at  > '1910-10-10 10:10:10'then 1 else 0 end) as 'Calculated Students',
                        sum(case when calculated_at  = '1900-01-01 00:00:00' then 1 else 0 end) as 'Students With No Data',
                        sum(case when calculated_at is null then 1 else 0 end) as 'Flagged For Calculation',
                        sum(case when calculated_at  = '1910-10-10 10:10:10'then 1 else 0 end) as 'No Survey Data',
                        COUNT(*) as 'Total Students',
                        concat(((sum(case when calculated_at  > '1910-10-10 10:10:10'then 1 else 0 end)/COUNT(*))*100), '%') As 'Calculated Percentage',
                        concat(((sum(case when calculated_at = '1900-01-01 00:00:00' then 1 else 0 end)/COUNT(*))*100), '%')As 'No Data Percentage',
                        concat(((sum(case when calculated_at is null then 1 else 0 end)/count(*))*100), '%') As 'Calculating Percentage',
                        concat(((sum(case when calculated_at = '1910-10-10 10:10:10' then 1 else 0 end)/COUNT(*))*100), '%')As 'No Survey Data Percentage'

                    FROM
                    synapse.org_calc_flags_success_marker union
                    SELECT
                   'Talking Points',
                       sum(case when calculated_at  > '1910-10-10 10:10:10'then 1 else 0 end) as 'Calculated Students',
                        sum(case when calculated_at  = '1900-01-01 00:00:00'then 1 else 0 end) as 'Students With No Data',
                        sum(case when calculated_at is null then 1 else 0 end) as 'Flagged For Calculation',
                        sum(case when calculated_at  = '1910-10-10 10:10:10'then 1 else 0 end) as 'No Survey Data',
                        COUNT(*) as 'Total Students',
                        concat(((sum(case when calculated_at  > '1910-10-10 10:10:10'then 1 else 0 end)/COUNT(*))*100), '%') As 'Calculated Percentage',
                        concat(((sum(case when calculated_at = '1900-01-01 00:00:00' then 1 else 0 end)/COUNT(*))*100), '%')As 'No Data Percentage',
                        concat(((sum(case when calculated_at is null then 1 else 0 end)/count(*))*100), '%') As 'Calculating Percentage',
                        concat(((sum(case when calculated_at = '1910-10-10 10:10:10' then 1 else 0 end)/COUNT(*))*100), '%')As 'No Survey Data Percentage'

                    FROM
                    synapse.org_calc_flags_talking_point union
                    SELECT
                   'Student Reports',
                          sum(case when calculated_at  > '1910-10-10 10:10:10'then 1 else 0 end) as 'Calculated Students',
                        sum(case when calculated_at  = '1900-01-01 00:00:00'then 1 else 0 end) as 'Students With No Data',
                        sum(case when calculated_at is null then 1 else 0 end) as 'Flagged For Calculation',
                        sum(case when calculated_at  = '1910-10-10 10:10:10'then 1 else 0 end) as 'No Survey Data',
                        COUNT(*) as 'Total Students',
                        concat(((sum(case when calculated_at  > '1910-10-10 10:10:10'then 1 else 0 end)/COUNT(*))*100), '%') As 'Calculated Percentage',
                        concat(((sum(case when calculated_at = '1900-01-01 00:00:00' then 1 else 0 end)/COUNT(*))*100), '%')As 'No Data Percentage',
                        concat(((sum(case when calculated_at is null then 1 else 0 end)/count(*))*100), '%') As 'Calculating Percentage',
                        concat(((sum(case when calculated_at = '1910-10-10 10:10:10' then 1 else 0 end)/COUNT(*))*100), '%')As 'No Survey Data Percentage'
                    FROM
                    org_calc_flags_student_reports sr where sr.modified_at = (SELECT
                                  MAX(modified_at)
                                  FROM org_calc_flags_student_reports SRin
                                  WHERE SRin.org_id = sr.org_id
                                  AND SRin.person_id = sr.person_id)
                    union
                        SELECT
                   'Report PDF Generation',
                        sum(case when file_name is not null then 1 else 0 end) as 'Calculated Students',
                        null as 'Students With No Data',
                        sum(case when file_name is null then 1 else 0 end) as 'Flagged For Calculation',
                        null as 'No Survey Data',
                        COUNT(*)  AS 'Total Students',
                        concat((sum(case when file_name is not null then 1 else 0 end)/count(*))*100, '%') As 'Calculated Percentage',
                        null as 'No Data Percentage',
                        concat((sum(case when file_name is null then 1 else 0 end)/count(*))*100, '%')As 'Calculating Percentage',
                        null as 'No Survey Data Percentage'
                    FROM
                    org_calc_flags_student_reports sr where sr.modified_at = (SELECT
                                  MAX(modified_at)
                                  FROM org_calc_flags_student_reports SRin
                                  WHERE SRin.org_id = sr.org_id
                                  AND SRin.person_id = sr.person_id)
        ");

        $this->addSql("
           CREATE OR REPLACE
		ALGORITHM = UNDEFINED
		DEFINER = `synapsemaster`@`%`
		SQL SECURITY DEFINER
	VIEW `DASHBOARD_Students_With_Intent_To_Leave` AS

    select ssr.org_id, ssr.person_id, ssr.survey_id, ssr.decimal_value from survey_response ssr INNER JOIN survey_questions sq on sq.id = ssr.survey_questions_id where sq.qnbr = 4  GROUP BY ssr.org_id, ssr.person_id, ssr.survey_id

");
        // This creates a view about who has taken what survey for each survey id
        $this->addSql("
    CREATE OR REPLACE
		ALGORITHM = UNDEFINED
		DEFINER = `synapsemaster`@`%`
		SQL SECURITY DEFINER
	VIEW `DASHBOARD_Student_Surveys_By_Org` AS

          SELECT
                `organization_lang`.`organization_id` AS `organization_id`,
                `organization_lang`.`organization_name` AS `organization_name`,
                `organization`.`campus_id` AS `campus_id`,
                #`sr`.`survey_id` AS `survey_id`,
                COUNT(`sr`.`person_id`) AS `Total number of Surveys Taken`,
                sum(case when (`sr`.`survey_id` = 11) then 1 else 0 end) as 'Number of 11 Surveys taken',
                sum(case when (`sr`.`survey_id` = 12) then 1 else 0 end) as 'Number of 12 Surveys taken',
                sum(case when (`sr`.`survey_id` = 13) then 1 else 0 end) as 'Number of 13 Surveys taken',
                sum(case when (`sr`.`survey_id` = 14) then 1 else 0 end) as 'Number of 14 Surveys taken',
                sum(case when (org_person_student.receivesurvey=1 or ISNULL(`org_person_student`.`receivesurvey`)) then 1 else 0 end) as 'Number of Possible Students to receive Surveys'
            FROM
                ((`organization_lang`
                LEFT JOIN `organization` ON

                `organization_lang`.`organization_id` = `organization`.`id`
                )
                LEFT JOIN `org_person_student` ON
                (

                `organization`.`id` = `org_person_student`.`organization_id`
                    AND `organization_lang`.`organization_id` = `org_person_student`.`organization_id`
                    AND (ISNULL(`org_person_student`.`receivesurvey`)
                    OR (`org_person_student`.`receivesurvey` = 1))
                    )
                LEFT JOIN `DASHBOARD_Students_With_Intent_To_Leave`
                AS `sr` ON (((`sr`.`person_id` = `org_person_student`.`person_id`)
                    AND (`organization`.`id` = `sr`.`org_id`))))
            GROUP BY `organization`.`id`
            ORDER BY `organization_lang`.`organization_name` , `sr`.`survey_id`;
        ");


        // This creates a section of Hai's Query
        $this->addSql("
             CREATE OR REPLACE
             DEFINER=`synapsemaster`@`%` VIEW `PART_Upload_Status_part_1` AS

            SELECT
                    organization_id,
                        upload_type,
                        status,
                        MAX(upload_date) most_recent_upload_date
                FROM
                    upload_file_log
                WHERE
                    status IN ('F' , 'Q')
                GROUP BY organization_id , upload_type , status

        ");


        // This creates a view that tells people about the uploads
        $this->addSql("
         CREATE OR REPLACE
             DEFINER=`synapsemaster`@`%` VIEW `DASHBOARD_Upload_Status` AS

           SELECT
                ulf.organization_id as 'organization id',
                ol.organization_name as 'organization name',
                ulf.upload_type as 'upload tyoe',
                ulf.status as 'status',
                ulf2.most_recent_upload_date as 'most recent upload date',
                ulf.uploaded_file_path as 'uploaded file path',
                uploaded_row_count as 'uploaded row count'
            FROM
                upload_file_log ulf
                    INNER JOIN
                 PART_Upload_Status_part_1 as ulf2 ON ulf.organization_id = ulf2.organization_id
                    AND ulf.upload_type = ulf2.upload_type
                    AND ulf.upload_date = ulf2.most_recent_upload_date
                    AND ulf.status = ulf2.status
                    INNER JOIN
                organization_lang ol ON ol.organization_id = ulf.organization_id
            ORDER BY ol.organization_name ASC , ulf.upload_type ASC , ulf.status ASC
        ");




    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // This drops all views that were created above
        $this->addSql('DROP VIEW DASHBOARD_Students_With_Intent_To_Leave');
        $this->addSql('DROP VIEW DASHBOARD_Upload_Status');
        $this->addSql('DROP VIEW PART_Upload_Status_part_1');
        $this->addSql('DROP VIEW DASHBOARD_Surveys_Taken_By_University');
        $this->addSql('DROP VIEW DASHBOARD_Student_Calculations');



    }
}
