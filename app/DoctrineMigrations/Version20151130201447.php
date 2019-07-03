<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151130201447 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $this->addSql(' UPDATE synapse.ebi_search
                    SET query = \'select mm.id,
                                mm.metadata_type,
                                dml.datablock_desc as blockdesc,
                                mml.meta_name,
                                pm.metadata_value as myanswer,
                                oay.name AS meta_year,
                                oat.name AS meta_term,
                                mm.scope
                                from datablock_master dm
                                JOIN datablock_master_lang dml ON dm.id = dml.datablock_id
                                JOIN  datablock_metadata dmd ON dmd.datablock_id = dm.id
                                JOIN ebi_metadata mm ON dmd.ebi_metadata_id = mm.id
                                JOIN ebi_metadata_lang mml ON mml.ebi_metadata_id = mm.id
                                JOIN person_ebi_metadata pm ON pm.ebi_metadata_id = mm.id
                                LEFT JOIN org_academic_year oay ON pm.org_academic_year_id = oay.id
                                LEFT JOIN org_academic_terms oat ON oat.id = pm.org_academic_terms_id
                                where mml.lang_id= $$lang$$
                                AND dm.block_type= \\\'profile\\\'
                                AND pm.person_id = $$studentid$$
                                AND dm.id IN($$datablockpermission$$)
                                AND dm.deleted_at IS NULL
                                AND  dml.deleted_at IS NULL
                                AND dmd.deleted_at IS NULL
                                AND mm.deleted_at IS NULL
                                AND mml.deleted_at IS NULL
                                AND pm.deleted_at IS NULL   -- maxscale route to server slave1
                                \'
                    WHERE query_key = \'Student_Profile_Datablock_Info\';');


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
