<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script for  updating  the new column retention_completion
 */
class Version20170118100425 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $this->addSql('ALTER TABLE org_permissionset ADD retention_completion  TINYINT(1) DEFAULT NULL');
        $this->addSql('UPDATE org_permissionset SET  retention_completion  = 0');
        $this->addSql("UPDATE org_permissionset SET  retention_completion  = 1 WHERE id IN(

          SELECT id FROM(
            SELECT 
                op.id
            FROM
                synapse.organization o 
                    JOIN
                synapse.org_permissionset op ON  o.id = op.organization_id 
                    JOIN
                synapse.org_permissionset_datablock opd ON op.id = opd.org_permissionset_id 
                        AND opd.datablock_id IN (SELECT datablock_id FROM synapse.datablock_master_lang WHERE datablock_desc IN ('Retention','Completion'))
                    
            WHERE
                opd.deleted_at IS NULL
                AND op.deleted_at IS NULL
                AND o.deleted_at IS NULL
                AND o.status = 'A' 
                AND o.is_mock = 'n' 
            GROUP BY op.id
            HAVING COUNT(opd.datablock_id) = 2 ) AS SUB
        )");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
