<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-11204 and ESPRJ-10671
 * Recreating the Audit Dashboard for Student Calculations
 * 1. Removed Success Marker Calculation as it is obsolete (ESPRJ-11204)
 * 2. Added 'Students With No Data' section to Report PDF generation as it was missing
 * 3. Removed most recent date check for Report Calculation and PDF Generation as multiple
 * flags have been replaced by one per person per survey
 * 4. Added checks for '1900-01-01 00:00:00' per ESPRJ-10671
 *
 */
class Version20160713152911 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("CREATE OR REPLACE
                            ALGORITHM = UNDEFINED
                            DEFINER = `synapsemaster`@`%`
                            SQL SECURITY DEFINER
                        VIEW DASHBOARD_Student_Calculations AS
                            SELECT
                                'Factor' AS `Calculation Type`,
                                SUM((CASE
                                    WHEN (org_calc_flags_factor.calculated_at > '1910-10-10 10:10:10') THEN 1
                                    ELSE 0
                                END)) AS `Calculated Students`,
                                SUM((CASE
                                    WHEN (org_calc_flags_factor.calculated_at = '1900-01-01 00:00:00') THEN 1
                                    ELSE 0
                                END)) AS `Students With No Data`,
                                SUM((CASE
                                    WHEN ISNULL(org_calc_flags_factor.calculated_at) THEN 1
                                    ELSE 0
                                END)) AS `Flagged For Calculation`,
                                SUM((CASE
                                    WHEN (org_calc_flags_factor.calculated_at = '1910-10-10 10:10:10') THEN 1
                                    ELSE 0
                                END)) AS `Never Calculated`,
                                COUNT(*) AS `Total Students`,
                                CONCAT(((SUM((CASE
                                            WHEN (org_calc_flags_factor.calculated_at > '1910-10-10 10:10:10') THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `Calculated Percentage`,
                                CONCAT(((SUM((CASE
                                            WHEN (org_calc_flags_factor.calculated_at = '1900-01-01 00:00:00') THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `No Data Percentage`,
                                CONCAT(((SUM((CASE
                                            WHEN ISNULL(org_calc_flags_factor.calculated_at) THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `Calculating Percentage`,
                                CONCAT(((SUM((CASE
                                            WHEN (org_calc_flags_factor.calculated_at = '1910-10-10 10:10:10') THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `Never Calculated Percentage`
                            FROM
                                org_calc_flags_factor
                            UNION SELECT
                                'Risk' AS `Calculation Type`,
                                SUM((CASE
                                    WHEN (org_calc_flags_risk.calculated_at > '1910-10-10 10:10:10') THEN 1
                                    ELSE 0
                                END)) AS `Calculated Students`,
                                SUM((CASE
                                    WHEN (org_calc_flags_risk.calculated_at = '1900-01-01 00:00:00') THEN 1
                                    ELSE 0
                                END)) AS `Students With No Data`,
                                SUM((CASE
                                    WHEN ISNULL(org_calc_flags_risk.calculated_at) THEN 1
                                    ELSE 0
                                END)) AS `Flagged For Calculation`,
                                SUM((CASE
                                    WHEN (org_calc_flags_risk.calculated_at = '1910-10-10 10:10:10') THEN 1
                                    ELSE 0
                                END)) AS `Never Calculated`,
                                COUNT(*) AS `Total Students`,
                                CONCAT(((SUM((CASE
                                            WHEN (org_calc_flags_risk.calculated_at > '1910-10-10 10:10:10') THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `Calculated Percentage`,
                                CONCAT(((SUM((CASE
                                            WHEN (org_calc_flags_risk.calculated_at = '1900-01-01 00:00:00') THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `No Data Percentage`,
                                CONCAT(((SUM((CASE
                                            WHEN ISNULL(org_calc_flags_risk.calculated_at) THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `Calculating Percentage`,
                                CONCAT(((SUM((CASE
                                            WHEN (org_calc_flags_risk.calculated_at = '1910-10-10 10:10:10') THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `Never Calculated Percentage`
                            FROM
                                org_calc_flags_risk
                            UNION SELECT
                                'Talking Points' AS `Calculation Type`,
                                SUM((CASE
                                    WHEN (org_calc_flags_talking_point.calculated_at > '1910-10-10 10:10:10') THEN 1
                                    ELSE 0
                                END)) AS `Calculated Students`,
                                SUM((CASE
                                    WHEN (org_calc_flags_talking_point.calculated_at = '1900-01-01 00:00:00') THEN 1
                                    ELSE 0
                                END)) AS `Students With No Data`,
                                SUM((CASE
                                    WHEN ISNULL(org_calc_flags_talking_point.calculated_at) THEN 1
                                    ELSE 0
                                END)) AS `Flagged For Calculation`,
                                SUM((CASE
                                    WHEN (org_calc_flags_talking_point.calculated_at = '1910-10-10 10:10:10') THEN 1
                                    ELSE 0
                                END)) AS `Never Calculated`,
                                COUNT(*) AS `Total Students`,
                                CONCAT(((SUM((CASE
                                            WHEN (org_calc_flags_talking_point.calculated_at > '1910-10-10 10:10:10') THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `Calculated Percentage`,
                                CONCAT(((SUM((CASE
                                            WHEN (org_calc_flags_talking_point.calculated_at = '1900-01-01 00:00:00') THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `No Data Percentage`,
                                CONCAT(((SUM((CASE
                                            WHEN ISNULL(org_calc_flags_talking_point.calculated_at) THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `Calculating Percentage`,
                                CONCAT(((SUM((CASE
                                            WHEN (org_calc_flags_talking_point.calculated_at = '1910-10-10 10:10:10') THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `Never Calculated Percentage`
                            FROM
                                org_calc_flags_talking_point
                            UNION SELECT
                                'Student Reports' AS `Calculation Type`,
                                SUM((CASE
                                    WHEN (sr.calculated_at > '1910-10-10 10:10:10') THEN 1
                                    ELSE 0
                                END)) AS `Calculated Students`,
                                SUM((CASE
                                    WHEN (sr.calculated_at = '1900-01-01 00:00:00') THEN 1
                                    ELSE 0
                                END)) AS `Students With No Data`,
                                SUM((CASE
                                    WHEN ISNULL(sr.calculated_at) THEN 1
                                    ELSE 0
                                END)) AS `Flagged For Calculation`,
                                SUM((CASE
                                    WHEN (sr.calculated_at = '1910-10-10 10:10:10') THEN 1
                                    ELSE 0
                                END)) AS `Never Calculated`,
                                COUNT(*) AS `Total Students`,
                                CONCAT(((SUM((CASE
                                            WHEN (sr.calculated_at > '1910-10-10 10:10:10') THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `Calculated Percentage`,
                                CONCAT(((SUM((CASE
                                            WHEN (sr.calculated_at = '1900-01-01 00:00:00') THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `No Data Percentage`,
                                CONCAT(((SUM((CASE
                                            WHEN ISNULL(sr.calculated_at) THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `Calculating Percentage`,
                                CONCAT(((SUM((CASE
                                            WHEN (sr.calculated_at = '1910-10-10 10:10:10') THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `Never Calculated Percentage`
                            FROM
                                org_calc_flags_student_reports sr
                            UNION SELECT
                                'Report PDF Generation' AS `Calculation Type`,
                                SUM((CASE
                                    WHEN (sr.file_name IS NOT NULL) THEN 1
                                    ELSE 0
                                END)) AS `Calculated Students`,
                                SUM((CASE
                                    WHEN (sr.calculated_at = '1900-01-01 00:00:00'
                                            AND sr.file_name IS NULL) THEN 1
                                    ELSE 0
                                END)) AS `Students With No Data`,
                                SUM((CASE
                                    WHEN ISNULL(sr.file_name) THEN 1
                                    ELSE 0
                                END)) AS `Flagged For Calculation`,
                                NULL AS `Never Calculated`,
                                COUNT(*) AS `Total Students`,
                                CONCAT(((SUM((CASE
                                            WHEN (sr.file_name IS NOT NULL) THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `Calculated Percentage`,
                                 CONCAT(((SUM((CASE
                                            WHEN (sr.calculated_at = '1900-01-01 00:00:00'
                                            AND sr.file_name IS NULL) THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `No Data Percentage`,
                                CONCAT(((SUM((CASE
                                            WHEN ISNULL(sr.file_name) THEN 1
                                            ELSE 0
                                        END)) / COUNT(*)) * 100),
                                        '%') AS `Calculating Percentage`,
                                NULL AS `Never Calculated Percentage`
                            FROM
                                org_calc_flags_student_reports sr;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
