<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150818034549 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
 public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("SET @emtid := (SELECT c.value FROM synapse.ebi_config c where c.key ='System_URL');
            UPDATE `ebi_config` SET `value`=CONCAT(@emtid,'#/student-agenda') WHERE `key`='StudentDashboard_AppointmentPage';");
    }
    
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    
    }
}
