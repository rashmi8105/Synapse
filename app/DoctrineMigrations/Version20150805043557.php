<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150805043557 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE `synapse`.`upload_file_log` CHANGE COLUMN `upload_type` `upload_type` ENUM('A','C','F','G','S','SB','SM','T','TP','P','H','SL','RV','RM','RMA','FA') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL  ;");
    }
    
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    
        $this->addSql("ALTER TABLE `synapse`.`upload_file_log` CHANGE COLUMN `upload_type` `upload_type` ENUM('A','C','F','G','S','SB','SM','T','TP','P','H','SL','RV','RM','RMA') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL  ;");
    }
}
