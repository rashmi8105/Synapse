<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * New migration script to populate multiyear data into org_person_student 
 */
class Version20161214222529 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("TRUNCATE TABLE synapse.org_person_student_year;");

        $this->addSql("
            INSERT INTO synapse.org_person_student_year(person_id, organization_id, org_academic_year_id, is_active, created_by, created_at, modified_by, modified_at)(
                SELECT
                    DISTINCT ops.person_id,
                    ops.organization_id,
                    oay.id,
                    1,
                    -25,
                    NOW(),
                    -25,
                    NOW()
                FROM
                    synapse.org_person_student ops
                        JOIN
                    synapse.org_academic_year oay ON oay.organization_id = ops.organization_id
                WHERE
                    ops.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                    AND ops.created_at <= oay.end_date
                    AND oay.year_id = '201516'
            );
        ");

        $this->addSql("
            INSERT INTO synapse.org_person_student_year(person_id, organization_id, org_academic_year_id, is_active, created_by, created_at, modified_by, modified_at)(
                SELECT
                    DISTINCT ops.person_id,
                    ops.organization_id,
                    oay.id,
                    ops.status,
                    -25,
                    NOW(),
                    -25,
                    NOW()
                FROM
                    synapse.org_person_student ops
                        JOIN
                    synapse.org_academic_year oay ON oay.organization_id = ops.organization_id
                WHERE
                    ops.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                    AND ops.created_at <= oay.end_date
                    AND oay.year_id = '201617'
            );
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
