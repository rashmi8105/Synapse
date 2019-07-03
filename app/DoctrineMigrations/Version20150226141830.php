<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150226141830 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE risk_levels (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, text VARCHAR(10) DEFAULT NULL, image_name VARCHAR(200) DEFAULT NULL, color_hex VARCHAR(7) DEFAULT NULL, INDEX IDX_5FE16A7DDE12AB56 (created_by), INDEX IDX_5FE16A7D25F94802 (modified_by), INDEX IDX_5FE16A7D1F6FA0AF (deleted_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE risk_levels ADD CONSTRAINT FK_5FE16A7DDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_levels ADD CONSTRAINT FK_5FE16A7D25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_levels ADD CONSTRAINT FK_5FE16A7D1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE intent_to_leave ADD color_hex VARCHAR(7) DEFAULT NULL');        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');        
        $this->addSql('DROP TABLE risk_levels');        
        $this->addSql('ALTER TABLE intent_to_leave DROP color_hex');
    }
}
