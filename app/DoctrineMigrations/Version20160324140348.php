<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160324140348 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        // This migration script is moving all of the students currently in a cohort in synapse.org_person_student into the new cohort tracking table with an academic year ID.

        $this->addSql("
              INSERT IGNORE INTO synapse.org_person_student_cohort(organization_id, person_id, org_academic_year_id, cohort, created_by, created_at, modified_by, modified_at)
                SELECT
                  ops.organization_id,
                  ops.person_id,
                  oay.id,
                  ops.surveycohort,
                  -6,
                  NOW(),
                  -6,
                  NOW()
                FROM
                  synapse.org_person_student ops
                    JOIN
                  synapse.org_academic_year oay ON ops.organization_id = oay.organization_id AND oay.deleted_at IS NULL
                WHERE
                  ops.deleted_at IS NULL
                  AND ops.surveycohort IS NOT NULL
                  AND ops.surveycohort <>  ''
                  AND oay.year_id = '201516';
");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
