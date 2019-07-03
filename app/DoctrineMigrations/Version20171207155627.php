<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-12224 Altering risk_group_person_history UNIQUE index to be correct
 */
class Version20171207155627 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE `synapse`.`risk_group_person_history` 
                            DROP INDEX `fk_risk_group_person_history_person1_idx` ,
                            ADD UNIQUE INDEX `fk_risk_group_person_history_person1_idx` (`person_id` ASC);");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
