<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150814063553 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $this->addSql('UPDATE `ebi_config` SET `value`="support@map-works.com" WHERE `key`= "Coordinator_Support_Helpdesk_Email_Address";');
        $this->addSql('UPDATE `ebi_config` SET `value`="support@map-works.com" WHERE `key`= "Staff_Support_Helpdesk_Email_Address";');
        $this->addSql('UPDATE `ebi_config` SET `value`="support@map-works.com" WHERE `key`= "Student_Support_Helpdesk_Email_Address";');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
