<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-11548-ESPRJ-13299
 */
class Version20171108210737 extends AbstractMigration
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
                                WHERE
                                    org_calc_flags_factor.deleted_at IS NULL
                                UNION
                                SELECT
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
                                WHERE
                                    org_calc_flags_risk.deleted_at IS NULL
                                UNION
                                SELECT
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
                                WHERE
                                    org_calc_flags_talking_point.deleted_at IS NULL
                                UNION
                                SELECT
                                    'Student Reports' AS `Calculation Type`,
                                    SUM((CASE
                                        WHEN (ocfsr.calculated_at > '1910-10-10 10:10:10') THEN 1
                                        ELSE 0
                                    END)) AS `Calculated Students`,
                                    SUM((CASE
                                        WHEN (ocfsr.calculated_at = '1900-01-01 00:00:00') THEN 1
                                        ELSE 0
                                    END)) AS `Students With No Data`,
                                    SUM((CASE
                                        WHEN ISNULL(ocfsr.calculated_at) THEN 1
                                        ELSE 0
                                    END)) AS `Flagged For Calculation`,
                                    SUM((CASE
                                        WHEN (ocfsr.calculated_at = '1910-10-10 10:10:10') THEN 1
                                        ELSE 0
                                    END)) AS `Never Calculated`,
                                    COUNT(*) AS `Total Students`,
                                    CONCAT(((SUM((CASE
                                                WHEN (ocfsr.calculated_at > '1910-10-10 10:10:10') THEN 1
                                                ELSE 0
                                            END)) / COUNT(*)) * 100),
                                            '%') AS `Calculated Percentage`,
                                    CONCAT(((SUM((CASE
                                                WHEN (ocfsr.calculated_at = '1900-01-01 00:00:00') THEN 1
                                                ELSE 0
                                            END)) / COUNT(*)) * 100),
                                            '%') AS `No Data Percentage`,
                                    CONCAT(((SUM((CASE
                                                WHEN ISNULL(ocfsr.calculated_at) THEN 1
                                                ELSE 0
                                            END)) / COUNT(*)) * 100),
                                            '%') AS `Calculating Percentage`,
                                    CONCAT(((SUM((CASE
                                                WHEN (ocfsr.calculated_at = '1910-10-10 10:10:10') THEN 1
                                                ELSE 0
                                            END)) / COUNT(*)) * 100),
                                            '%') AS `Never Calculated Percentage`
                                FROM
                                    org_calc_flags_student_reports ocfsr
                                WHERE
                                    ocfsr.deleted_at IS NULL
                                UNION SELECT
                                    'Report PDF Generation' AS `Calculation Type`,
                                    SUM((CASE
                                        WHEN (ocfsr.file_name IS NOT NULL) THEN 1
                                        ELSE 0
                                    END)) AS `Calculated Students`,
                                    SUM((CASE
                                        WHEN (ocfsr.calculated_at = '1900-01-01 00:00:00'
                                                AND ocfsr.file_name IS NULL) THEN 1
                                        ELSE 0
                                    END)) AS `Students With No Data`,
                                    SUM((CASE
                                        WHEN ISNULL(ocfsr.file_name) THEN 1
                                        ELSE 0
                                    END)) AS `Flagged For Calculation`,
                                    NULL AS `Never Calculated`,
                                    COUNT(*) AS `Total Students`,
                                    CONCAT(((SUM((CASE
                                                WHEN (ocfsr.file_name IS NOT NULL) THEN 1
                                                ELSE 0
                                            END)) / COUNT(*)) * 100),
                                            '%') AS `Calculated Percentage`,
                                     CONCAT(((SUM((CASE
                                                WHEN (ocfsr.calculated_at = '1900-01-01 00:00:00'
                                                AND ocfsr.file_name IS NULL) THEN 1
                                                ELSE 0
                                            END)) / COUNT(*)) * 100),
                                            '%') AS `No Data Percentage`,
                                    CONCAT(((SUM((CASE
                                                WHEN ISNULL(ocfsr.file_name) THEN 1
                                                ELSE 0
                                            END)) / COUNT(*)) * 100),
                                            '%') AS `Calculating Percentage`,
                                    NULL AS `Never Calculated Percentage`
                                FROM
                                    org_calc_flags_student_reports ocfsr
                                WHERE
                                    ocfsr.deleted_at IS NULL;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
