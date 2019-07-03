<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150804102341 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //$this->addSql('ALTER TABLE factor_lang ADD id INT AUTO_INCREMENT NOT NULL, CHANGE lang_id lang_id INT DEFAULT NULL, CHANGE factor_id factor_id INT DEFAULT NULL, CHANGE name name VARCHAR(50) DEFAULT NULL, ADD PRIMARY KEY (id)');
      
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    
        //$this->addSql('ALTER TABLE factor_lang DROP PRIMARY KEY');
        //$this->addSql('ALTER TABLE factor_lang DROP id, CHANGE factor_id factor_id INT NOT NULL, CHANGE lang_id lang_id INT NOT NULL, CHANGE name name VARCHAR(200) DEFAULT NULL COLLATE utf8_unicode_ci');
        
    }
}
