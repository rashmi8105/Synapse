<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150713050947 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $query = <<<CDATA
SET @ebiMetaId = (SELECT value FROM synapse.ebi_config where `key` = 'cohort_ids');
SET @id = (SELECT id FROM ebi_metadata WHERE id= @ebiMetaId);
SET @langId = (SELECT id FROM language_master WHERE langcode = 'en_US');
INSERT INTO `ebi_metadata_lang`(`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`lang_id`,`ebi_metadata_id`,`meta_name`,`meta_description`)
VALUES (NULL,NULL,NULL,NULL,NULL,NULL,@langId,@id,'Cohort Names',NULL);
CDATA;
        $this->addSql($query);
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
