<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160506134748 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        /**
         *  Added Descending ordering with respect to contact date.
         *  ORDER BY C.contact_date DESC
         */
        $studentId = '$$studentId$$';
        $facultyId = '$$facultyId$$';
        $teamAccess = '$$teamAccess$$';
        $publicAccess = '$$publicAccess$$';

        $sql = <<<CDATA
UPDATE `ebi_search`
SET `query` = "
SELECT 
    C.id AS activity_id,
    AL.id AS activity_log_id,
    C.created_at AS activity_date,
    C.person_id_faculty AS activity_created_by_id,
    P.firstname AS activity_created_by_first_name,
    P.lastname AS activity_created_by_last_name,
    AC.id AS activity_reason_id,
    AC.short_name AS activity_reason_text,
    C.note AS activity_description,
    C.contact_types_id AS activity_contact_type_id,
    CTL.description AS activity_contact_type_text,
    C.contact_date AS contact_created_date
FROM
    activity_log AS AL
        LEFT JOIN
    contacts AS C ON AL.contacts_id = C.id
        LEFT JOIN
    person AS P ON C.person_id_faculty = P.id
        LEFT JOIN
    contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id
        LEFT JOIN
    activity_category AS AC ON C.activity_category_id = AC.id
        LEFT JOIN
    contacts_teams AS CT ON C.id = CT.contacts_id
        LEFT JOIN
    related_activities AS RA ON C.id = RA.contacts_id
        LEFT JOIN
    activity_log AL1 ON RA.activity_log_id = AL1.id
        LEFT JOIN
    referrals AS R1 ON AL1.referrals_id = R1.id
        LEFT JOIN
    note AS N1 ON AL1.note_id = N1.id
        LEFT JOIN
    contacts AS C1 ON AL1.contacts_id = C1.id
        LEFT JOIN
    Appointments AS A1 ON AL1.appointments_id = A1.id
WHERE
    C.person_id_student = $studentId
        AND C.deleted_at IS NULL
        AND (CASE
        WHEN
            AL1.activity_type IS NOT NULL
                AND ((AL1.activity_type = 'R'
                AND R1.access_private = 1)
                OR (AL1.activity_type = 'C'
                AND C1.access_private = 1)
                OR (AL1.activity_type = 'N'
                AND N1.access_private = 1))
        THEN
            C.person_id_faculty = $facultyId
        ELSE CASE
            WHEN
                C.access_team = 1
            THEN
                CT.teams_id IN (SELECT 
                        teams_id
                    FROM
                        team_members
                    WHERE
                        person_id = $facultyId
                            AND teams_id IN (SELECT 
                                teams_id
                            FROM
                                contacts_teams
                            WHERE
                                contacts_id = C.id
                                    AND deleted_at IS NULL)
                            AND deleted_at IS NULL)
                    AND $teamAccess = 1
            ELSE CASE
                WHEN C.access_private = 1 THEN C.person_id_faculty = $facultyId
                ELSE C.access_public = 1
                    AND $publicAccess = 1
            END
        END
    END
        OR C.person_id_faculty = $facultyId)
GROUP BY C.id
ORDER BY C.contact_date DESC
"
WHERE `query_key` = "Activity_Contact";
CDATA;
        $this->addSql($sql);

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
