<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150921164014 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // this up() migration is auto-generated, please modify it to your needs


        // This creates a view about who has taken what survey for each survey id
        $this->addSql("
    CREATE OR REPLACE
		ALGORITHM = UNDEFINED
		DEFINER = `synapsemaster`@`%`
		SQL SECURITY DEFINER
	VIEW `DASHBOARD_Student_Surveys_By_Org` AS
SELECT
    `organization_lang`.`organization_id` AS `organization_id`,
    `organization`.`campus_id` AS `campus_id`,
    `organization_lang`.`organization_name` AS `organization_name`,
    COUNT(`sr`.`person_id`) AS `Total number of Surveys Taken`,
    SUM(CASE
        WHEN (`sr`.`survey_id` = 11) THEN 1
        ELSE 0
    END) AS 'Students having taken survey_id: 11',
    SUM(CASE
        WHEN (`sr`.`survey_id` = 12) THEN 1
        ELSE 0
    END) AS 'Students having taken survey_id: 12',
    SUM(CASE
        WHEN (`sr`.`survey_id` = 13) THEN 1
        ELSE 0
    END) AS 'Students having taken survey_id: 13',
    SUM(CASE
        WHEN (`sr`.`survey_id` = 14) THEN 1
        ELSE 0
    END) AS 'Students having taken survey_id: 14',
    SUM(CASE
        WHEN
            (org_person_student.receivesurvey = 1
                OR ISNULL(`org_person_student`.`receivesurvey`))
        THEN
            1
        ELSE 0
    END) AS 'Student Survey Eligibility'
FROM
    (((`organization_lang`
    LEFT JOIN `organization` ON ((`organization_lang`.`organization_id` = `organization`.`id`)))
    LEFT JOIN `org_person_student` ON (((`organization`.`id` = `org_person_student`.`organization_id`)
        AND (`organization_lang`.`organization_id` = `org_person_student`.`organization_id`)
        AND (ISNULL(`org_person_student`.`receivesurvey`)
        OR (`org_person_student`.`receivesurvey` = 1)))))
    LEFT JOIN `DASHBOARD_Students_With_Intent_To_Leave` AS `sr` ON (((`sr`.`person_id` = `org_person_student`.`person_id`)
        AND (`organization`.`id` = `sr`.`org_id`))))
WHERE
    `organization`.`campus_id` IS NOT NULL
        AND `organization_lang`.`organization_id` <> 181
        AND `organization_lang`.`organization_id` <> 195
        AND `organization_lang`.`organization_id` <> 196
        AND `organization_lang`.`organization_id` <> 198
        AND `organization_lang`.`organization_id` <> 200
		AND `organization_lang`.`organization_id` <> 201
        AND `organization_lang`.`organization_id` <> 2
		AND `organization_lang`.`organization_id` <> 199
        AND `organization_lang`.`organization_id` <> 3
        AND `organization_lang`.`organization_id` <> 194
        AND `organization_lang`.`organization_id` <> 197
GROUP BY `organization`.`id`
ORDER BY `organization_lang`.`organization_name` , `sr`.`survey_id`;
        ");



    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // this down() migration is auto-generated, please modify it to your needs

        // This drops all views that were created above
        $this->addSql('DROP VIEW DASHBOARD_Student_Surveys_By_Org');

    }
}

