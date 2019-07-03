<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151029072346 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

      $query = <<<CDATA
                    ALTER TABLE person CHANGE firstname firstname VARCHAR(100) DEFAULT NULL, CHANGE lastname lastname VARCHAR(100) DEFAULT NULL;
                    INSERT INTO `role` ( `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`,`deleted_at`,`status`) 
                    VALUES  (NULL, NULL, NULL, NULL, NULL, NULL, "A");

                    SET @roleId := (select max(id) from role);
                    SET @langId := (select id from language_master where langcode = 'en_US');

                    INSERT INTO `role_lang` ( `role_id`, `lang_id`, `role_name`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`,`deleted_at`) 
                                VALUES  (@roleId, @langId, 'Skyfactor Admin',  NULL, NULL, NULL, NULL, NULL, NULL);
CDATA;
        $this->addSql($query);

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

      
    }
}
