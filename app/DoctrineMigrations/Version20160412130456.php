<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script to add year_id to synapse.survey table, to associate a survey with a year.
 */
class Version20160412130456 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE synapse.survey ADD COLUMN year_id VARCHAR(10);");
        
        $this->addSql("ALTER TABLE `synapse`.`survey` ADD CONSTRAINT `year_id` FOREIGN KEY (`year_id`) REFERENCES `synapse`.`year` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
        
        $this->addSql("ALTER TABLE `synapse`.`survey` CHANGE COLUMN `year_id` `year_id` VARCHAR(10) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL AFTER `external_id`;");

        $this->addSql("UPDATE synapse.survey SET year_id = '201516' WHERE external_id IN ('1647', '1648', '1649', '1650')");

        $this->addSql("UPDATE synapse.survey SET year_id = '201617' WHERE external_id IN ('1783', '1784', '1785', '1786')");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
