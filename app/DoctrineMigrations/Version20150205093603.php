<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150205093603 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE org_search CHANGE person_id_sharedwith person_id_sharedwith INT DEFAULT NULL, CHANGE person_id person_id INT DEFAULT NULL, CHANGE person_id_sharedby person_id_sharedby INT DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE org_search CHANGE person_id person_id INT NOT NULL, CHANGE person_id_sharedby person_id_sharedby INT NOT NULL, CHANGE person_id_sharedwith person_id_sharedwith INT NOT NULL');
    }
}
