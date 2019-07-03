<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This migrations script is a one time run that will move all students into the new
 * org_person_student_year table. This query will add each student in twice: once
 * for 201516 and once for 201617. This query will take the 201516 year and 201617
 * will be what is in the org_person_student.is_active column. This function will
 * first truncate anything in the org_person_student_year. Then it will populate
 * the table with the new information.
 *
 * Note: there is a check in the where clause of the insert statement that checks the
 * years against the first of the second year:
 * AND DATE('2016-01-01') BETWEEN start_date AND end_date) for 201516 and
 * AND DATE('2017-01-01') BETWEEN start_date AND end_date) for 201617
 * this is to try to eliminate faulty years that take place during a incorrect time,
 * for example: in the 2020 - 2021 school year.
 */
class Version20161202170116 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("TRUNCATE org_person_student_year;");
        $this->addSql(
<<<MYSQL
        INSERT INTO org_person_student_year
 (organization_id, person_id, org_academic_year_id, is_active, created_by, created_at, modified_by, modified_at)
SELECT
    org_person_student.organization_id AS 'organization_id',
    org_person_student.person_id AS 'person_id',
    org_academic_year.id AS 'org_academic_year_id',
    IF(org_academic_year.year_id = 201516,
        1,
        org_person_student.status) AS 'is_active',
    '-25' AS 'created_by',
    NOW() AS 'created_at',
	'-25' AS 'modified_by',
    NOW() AS 'modified_at'
FROM
    org_person_student
        INNER JOIN
    org_academic_year ON org_academic_year.organization_id = org_person_student.organization_id
WHERE
    org_academic_year.deleted_at IS NULL
        AND org_person_student.deleted_at IS NULL
        AND ((org_academic_year.year_id = 201516
        AND DATE('2016-01-01') BETWEEN start_date AND end_date)
        OR (org_academic_year.year_id = 201617
        AND DATE('2017-01-01') BETWEEN start_date AND end_date));
MYSQL
);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
