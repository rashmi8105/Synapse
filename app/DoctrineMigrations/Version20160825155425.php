<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Changing previous Academic Updates to have nulls instead of empty lines
 */
class Version20160825155425 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("UPDATE academic_update SET `grade` = NULL WHERE `grade` = '';");
        $this->addSql("UPDATE academic_update SET `absence` = NULL WHERE `absence` = '';");
        $this->addSql("UPDATE academic_update SET `comment` = NULL WHERE `comment` = '';");
        $this->addSql("UPDATE academic_update SET `final_grade` = NULL WHERE `final_grade` = '';");
        $this->addSql("UPDATE academic_update SET `send_to_student` = NULL WHERE `send_to_student` = '';");
        $this->addSql("UPDATE academic_update SET `failure_risk_level` = NULL WHERE `failure_risk_level` = '';");
        $this->addSql("UPDATE academic_update SET `refer_for_assistance` = NULL WHERE `refer_for_assistance` = '';");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
