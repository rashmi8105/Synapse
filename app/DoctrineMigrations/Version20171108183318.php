<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171108183318 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER EVENT `Survey_Risk_Event` DISABLE;");
        $this->addSql("DO SLEEP(1020);");
        $this->addSql("ALTER TABLE `synapse`.`person_risk_level_history` 
                            ADD COLUMN `created_at` DATETIME NULL DEFAULT NULL AFTER `maximum_weight_value`,
                            ADD COLUMN `queued_at` DATETIME NULL DEFAULT NULL AFTER `created_at`,
                            ADD INDEX `risk_calculation_time` (`created_at` ASC, `queued_at` ASC);");
        $this->addSql("ALTER EVENT `Survey_Risk_Event` ENABLE;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
