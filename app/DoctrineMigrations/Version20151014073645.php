<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151014073645 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $query = <<<'CDATA'
INSERT INTO `feature_master` (`created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES (NULL,NULL,NULL,NULL,NULL,NULL);
select max(id) into @featureId from `feature_master` limit 1;
INSERT INTO `feature_master_lang` (`feature_master_id`, `lang_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`,`feature_name`)
VALUES (@featureId,1,NULL,NULL,NULL,NULL,NULL,NULL,"Primary Campus Connection Referral Routing");
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
