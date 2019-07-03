<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150813051602 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO `language_master` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`,`deleted_at`, `langcode`, `langdescription`, `issystemdefault`)
VALUES (1, NULL, NULL, NULL, NULL, NULL, NULL, "en_US", "US English", 1);');

        $this->addSql('INSERT INTO `feature_master` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES (7,NULL,NULL,NULL,NULL,NULL,NULL);');

        $this->addSql('INSERT INTO `feature_master_lang` (`id`, `feature_master_id`, `lang_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`,`feature_name`)
VALUES (7,7,1,NULL,NULL,NULL,NULL,NULL,NULL,"Email");');

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
