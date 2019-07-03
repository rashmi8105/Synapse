<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-13114 Creating the necessary Views for Retention Completion
 *
 * Assumes Migration of ESPRJ-13113 and its tables
 *
 * List of Views and dependency order to be created:
 * 1. potentional_retention_tracking_group_students_view
 * 2. org_person_student_retention_view
 * 3. org_person_student_retention_tracking_group_view
 * 4. org_person_student_retention_by_tracking_group_view
 * 5. org_person_student_retention_with_degree_completion_view
 * 6. org_person_student_completion_names_view
 * 7. org_person_student_retention_completion_pivot_view
 * 8. org_person-student_retention_completion_variables_view
 */
class Version20170123171417 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // First view: linked to 1-potential_retention_tracking_group_students_view.sql
        // Gets all potential retention tracking group students combined with all possible non-future years
        $this->addSql("CREATE OR REPLACE
                            ALGORITHM = MERGE
                            DEFINER = `synapsemaster`@`%`
                            SQL SECURITY INVOKER
                        VIEW `potential_retention_tracking_group_students_view` AS
                            SELECT
                                opsrtg.organization_id,
                                opsrtg.person_id,
                                oay.year_id,
                                oay.name as year_name
                            FROM
                                org_person_student_retention_tracking_group opsrtg
                                    INNER JOIN
                                org_academic_year oay ON opsrtg.organization_id = oay.organization_id
                            WHERE
                                oay.start_date <= DATE(NOW())
                                AND opsrtg.deleted_at IS NULL
                                AND oay.deleted_at IS NULL;");

        //Second View: linked to 2-org_person_student_retention_view.sql
        // copy of org_person_student_retention with year_id and year_name added
        $this->addSql("CREATE OR REPLACE
                            ALGORITHM = MERGE
                            DEFINER = `synapsemaster`@`%`
                            SQL SECURITY INVOKER
                        VIEW `org_person_student_retention_view` AS
                        SELECT
                            opsr.organization_id,
                            opsr.person_id,
                            oay.year_id as year_id,
                            oay.name as year_name,
                            opsr.is_enrolled_beginning_year,
                            opsr.is_enrolled_midyear,
                            opsr.is_degree_completed
                        FROM
                            org_person_student_retention opsr
                            INNER JOIN org_academic_year oay
                                ON opsr.org_academic_year_id = oay.id
                            WHERE
                                opsr.deleted_at IS NULL
                                AND oay.deleted_at IS NULL;");

        //Third View: linked to 3-org_person_student_retention_tracking_group_view.sql
        // copy of org_person_student_retention_tracking_group with year_id and year_name added
        $this->addSql("CREATE OR REPLACE
                            ALGORITHM = MERGE
                            DEFINER = `synapsemaster`@`%`
                            SQL SECURITY INVOKER
                        VIEW `org_person_student_retention_tracking_group_view` AS
                            SELECT
                                opsrtg.organization_id,
                                opsrtg.person_id,
                                oay.year_id AS retention_tracking_year,
                                oay.name as year_name
                            FROM
                                org_person_student_retention_tracking_group opsrtg
                                    INNER JOIN
                                org_academic_year oay ON opsrtg.org_academic_year_id = oay.id
                            WHERE
                                opsrtg.deleted_at IS NULL
                                AND oay.deleted_at IS NULL;");

        //Fourth View: linked to 4-org_person_student_retention_by_tracking_group_view.sql
        // Creates rows for years with no retention data, replaces nulls with zeros, and calculates years from retention track
        $this->addSql("CREATE OR REPLACE
                            DEFINER = `synapsemaster`@`%`
                            SQL SECURITY INVOKER
                        VIEW `org_person_student_retention_by_tracking_group_view` AS
                        SELECT
                            opsrtgv.organization_id,
                            opsrtgv.person_id,
                            opsrtgv.retention_tracking_year,
                            ortgsv.year_id,
                            ortgsv.year_name,
                            IFNULL(opsrv.is_enrolled_beginning_year, 0) as is_enrolled_beginning_year,
                            IFNULL(opsrv.is_enrolled_midyear, 0) as is_enrolled_midyear,
                            opsrv.is_degree_completed as is_degree_completed,
                            (RIGHT(ortgsv.year_id, 2) - RIGHT(opsrtgv.retention_tracking_year, 2)) as years_from_retention_track
                        FROM
                            potential_retention_tracking_group_students_view ortgsv
                            INNER JOIN org_person_student_retention_tracking_group_view opsrtgv
                                ON ortgsv.organization_id = opsrtgv.organization_id
                                AND ortgsv.person_id = opsrtgv.person_id
                            LEFT JOIN org_person_student_retention_view opsrv
                                ON opsrv.person_id = opsrtgv.person_id
                                AND opsrv.organization_id = opsrtgv.organization_id
                                AND opsrv.year_id = ortgsv.year_id
                                AND opsrtgv.retention_tracking_year <= opsrv.year_id;");

        //Fifth View: linked to 4-org_person_student_retention_with_degree_completion_view.sql
        // Replaces nulls with zeros before degree completed year, and replaces nulls/0s after degree completed year with 1
        $this->addSql("CREATE OR REPLACE
                            DEFINER = `synapsemaster`@`%`
                            SQL SECURITY INVOKER
                        VIEW `org_person_student_retention_with_degree_completion_view` AS
                        SELECT
                            opsrbtgv.organization_id,
                            opsrbtgv.person_id,
                            opsrbtgv.retention_tracking_year,
                            opsrbtgv.year_id,
                            opsrbtgv.year_name,
                            opsrbtgv.is_enrolled_beginning_year,
                            opsrbtgv.is_enrolled_midyear,
                            CASE
                                WHEN is_degree_completed = 1 THEN 1
                                WHEN (
                                    SELECT
                                        opsr1.year_id
                                    FROM
                                        org_person_student_retention_view opsr1
                                    WHERE
                                        opsr1.person_id = opsrbtgv.person_id
                                        AND opsr1.organization_id = opsrbtgv.organization_id
                                        AND opsr1.is_degree_completed = 1
                                        AND opsr1.year_id >= opsrbtgv.retention_tracking_year
                                    ORDER BY year_id ASC
                                    LIMIT 1
                                    ) <= opsrbtgv.year_id THEN 1
                                ELSE 0 END AS is_degree_completed,
                            opsrbtgv.years_from_retention_track
                        FROM
                            org_person_student_retention_by_tracking_group_view opsrbtgv;");

        //Sixth View: linked to 6-org_person_student_retention_completion_names_view.sql
        // Links rows with retention completion variable names by years from retention track
        $this->addSql("CREATE OR REPLACE
                            DEFINER = `synapsemaster`@`%`
                            SQL SECURITY INVOKER
                        VIEW `org_person_student_retention_completion_names_view` AS
                        SELECT
                            opsrwdcv.organization_id,
                            opsrwdcv.person_id,
                            opsrwdcv.retention_tracking_year,
                            opsrwdcv.year_id,
                            opsrwdcv.year_name,
                            opsrwdcv.is_enrolled_beginning_year,
                            opsrwdcv.is_enrolled_midyear,
                            opsrwdcv.is_degree_completed,
                            opsrwdcv.years_from_retention_track,
                            rcvn.name_text
                        FROM
                            org_person_student_retention_with_degree_completion_view opsrwdcv
                            INNER JOIN retention_completion_variable_name rcvn
                                ON opsrwdcv.years_from_retention_track = rcvn.years_from_retention_track;");

        //Seventh View: linked to 7-org_person_student_retention_completion_pivot_view.sql
        // Pivots the data so retention completion variables are column names
        $this->addSql("CREATE OR REPLACE
                            DEFINER = `synapsemaster`@`%`
                            SQL SECURITY INVOKER
                        VIEW `org_person_student_retention_completion_pivot_view` AS
                        SELECT
                            opsrcnv.organization_id,
                            opsrcnv.person_id,
                            opsrcnv.retention_tracking_year,
                                CASE
                                    WHEN opsrcnv.name_text ='Retained to Midyear Year 1'
                                    THEN opsrcnv.is_enrolled_midyear
                                    ELSE NULL
                                END
                             AS 'Retained to Midyear Year 1',
                                CASE
                                    WHEN opsrcnv.name_text ='Retained to Start of Year 2'
                                    THEN opsrcnv.is_enrolled_beginning_year
                                    ELSE NULL
                                END
                             AS 'Retained to Start of Year 2',
                                CASE
                                    WHEN opsrcnv.name_text ='Retained to Midyear Year 2'
                                    THEN opsrcnv.is_enrolled_midyear
                                    ELSE NULL
                                END
                             AS 'Retained to Midyear Year 2',
                                CASE
                                    WHEN opsrcnv.name_text ='Retained to Start of Year 3'
                                    THEN opsrcnv.is_enrolled_beginning_year
                                    ELSE NULL
                                END
                             AS 'Retained to Start of Year 3',
                                CASE
                                    WHEN opsrcnv.name_text ='Retained to Midyear Year 3'
                                    THEN opsrcnv.is_enrolled_midyear
                                    ELSE NULL
                                END
                             AS 'Retained to Midyear Year 3',

                                CASE
                                    WHEN opsrcnv.name_text ='Retained to Start of Year 4'
                                    THEN opsrcnv.is_enrolled_beginning_year
                                    ELSE NULL
                                END
                             AS 'Retained to Start of Year 4',
                                CASE
                                    WHEN opsrcnv.name_text ='Retained to Midyear Year 4'
                                    THEN opsrcnv.is_enrolled_midyear
                                    ELSE NULL
                                END
                             AS 'Retained to Midyear Year 4',
                                CASE
                                    WHEN opsrcnv.name_text ='Completed Degree in 1 Year'
                                    THEN opsrcnv.is_degree_completed
                                    ELSE NULL
                                END
                             AS 'Completed Degree in 1 Year',
                                CASE
                                    WHEN opsrcnv.name_text ='Completed Degree in 2 Years'
                                    THEN opsrcnv.is_degree_completed
                                    ELSE NULL
                                END
                             AS 'Completed Degree in 2 Years',
                                CASE
                                    WHEN opsrcnv.name_text ='Completed Degree in 3 Years'
                                    THEN opsrcnv.is_degree_completed
                                    ELSE NULL
                                END
                             AS 'Completed Degree in 3 Years',
                                CASE
                                    WHEN opsrcnv.name_text ='Completed Degree in 4 Years'
                                    THEN opsrcnv.is_degree_completed
                                    ELSE NULL
                                END
                             AS 'Completed Degree in 4 Years',
                                CASE
                                    WHEN opsrcnv.name_text ='Completed Degree in 5 Years'
                                    THEN opsrcnv.is_degree_completed
                                    ELSE NULL
                                END
                             AS 'Completed Degree in 5 Years',

                                CASE
                                    WHEN opsrcnv.name_text ='Completed Degree in 6 Years'
                                    THEN opsrcnv.is_degree_completed
                                    ELSE NULL
                                END
                             AS 'Completed Degree in 6 Years'
                        FROM
                            org_person_student_retention_completion_names_view opsrcnv;");

        //Eighth View: linked to 8-org_person_student_retention_completion_variables_view.sql
        // Aggregates the information so we have one row per student per retention tracking group (year)
        $this->addSql("CREATE OR REPLACE
                            DEFINER = `synapsemaster`@`%`
                            SQL SECURITY INVOKER
                        VIEW `org_person_student_retention_completion_variables_view` AS
                        SELECT
                            opsrcpv.organization_id,
                            opsrcpv.person_id,
                            opsrcpv.retention_tracking_year as 'Retention Tracking Year',
                            MAX(opsrcpv.`Retained to Midyear Year 1`) as 'Retained to Midyear Year 1',
                            MAX(opsrcpv.`Retained to Start of Year 2`) as 'Retained to Start of Year 2',
                            MAX(opsrcpv.`Retained to Midyear Year 2`) as 'Retained to Midyear Year 2',
                            MAX(opsrcpv.`Retained to Start of Year 3`) as 'Retained to Start of Year 3',
                            MAX(opsrcpv.`Retained to Midyear Year 3`) as 'Retained to Midyear Year 3',
                            MAX(opsrcpv.`Retained to Start of Year 4`) as 'Retained to Start of Year 4',
                            MAX(opsrcpv.`Retained to Midyear Year 4`) as 'Retained to Midyear Year 4',
                            MAX(opsrcpv.`Completed Degree in 1 Year`) as 'Completed Degree in 1 Year',
                            MAX(opsrcpv.`Completed Degree in 2 Years`) as 'Completed Degree in 2 Years',
                            MAX(opsrcpv.`Completed Degree in 3 Years`) as 'Completed Degree in 3 Years',
                            MAX(opsrcpv.`Completed Degree in 4 Years`) as 'Completed Degree in 4 Years',
                            MAX(opsrcpv.`Completed Degree in 5 Years`) as 'Completed Degree in 5 Years',
                            MAX(opsrcpv.`Completed Degree in 6 Years`) as 'Completed Degree in 6 Years'
                        FROM
                            org_person_student_retention_completion_pivot_view opsrcpv
                        GROUP BY
                            opsrcpv.organization_id,
                            opsrcpv.person_id,
                            opsrcpv.retention_tracking_year;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
