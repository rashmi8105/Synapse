<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141110140850 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE feature_master_lang (id INT AUTO_INCREMENT NOT NULL, feature_master_id INT DEFAULT NULL, lang_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, feature_name VARCHAR(100) DEFAULT NULL, INDEX IDX_C2AF353CA5AC1B83 (feature_master_id), INDEX IDX_C2AF353CB213FA4 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE feature_master_lang ADD CONSTRAINT FK_C2AF353CA5AC1B83 FOREIGN KEY (feature_master_id) REFERENCES feature_master (id)');
        $this->addSql('ALTER TABLE feature_master_lang ADD CONSTRAINT FK_C2AF353CB213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE feature_master DROP category, DROP feature_name');      
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE feature_master_lang');
        $this->addSql('ALTER TABLE feature_master ADD category VARCHAR(45) DEFAULT NULL, ADD feature_name VARCHAR(100) DEFAULT NULL');       
    }
}
