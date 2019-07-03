<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161028091715 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE `synapse`.`org_person_faculty` CHANGE COLUMN `oauth_cal_access_token` `oauth_cal_access_token` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL");
        $this->addSql("ALTER TABLE `synapse`.`org_corporate_google_access` CHANGE COLUMN `oauth_cal_access_token` `oauth_cal_access_token` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL");
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        
    }
}
