<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Inserts records into the new org_person_student_year table,
 * using the assumption that their status in org_person_student is for 201516.
 */
class Version20160513182436 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT IGNORE INTO org_person_student_year (organization_id, person_id, org_academic_year_id, is_active, created_at, created_by, modified_at, modified_by)
                        SELECT oay.organization_id, ops.person_id, oay.id, ops.status, NOW(), -25, NOW(), -25
                        FROM org_person_student ops
                        INNER JOIN org_academic_year oay
                            ON oay.organization_id = ops.organization_id
                        WHERE ops.deleted_at IS NULL
                            AND oay.deleted_at IS NULL
                            AND oay.year_id = 201516;');
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
