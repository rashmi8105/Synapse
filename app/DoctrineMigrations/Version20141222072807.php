<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141222072807 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE risk_model_levels (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, risk_model_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, risk_level VARCHAR(255) DEFAULT NULL, risk_text VARCHAR(255) DEFAULT NULL, min NUMERIC(5, 3) DEFAULT NULL, max NUMERIC(5, 3) DEFAULT NULL, image_name VARCHAR(200) DEFAULT NULL, INDEX IDX_A07B4CECDE12AB56 (created_by), INDEX IDX_A07B4CEC25F94802 (modified_by), INDEX IDX_A07B4CEC1F6FA0AF (deleted_by), INDEX IDX_A07B4CEC9F5CF488 (risk_model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE risk_model_master (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, risk_key VARCHAR(255) DEFAULT NULL, effective_from DATETIME DEFAULT NULL, effective_to DATETIME DEFAULT NULL, status VARCHAR(1) DEFAULT NULL, INDEX IDX_12588B23DE12AB56 (created_by), INDEX IDX_12588B2325F94802 (modified_by), INDEX IDX_12588B231F6FA0AF (deleted_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE risk_model_master_lang (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, risk_model_id INT DEFAULT NULL, lang_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, risk_model_name VARCHAR(255) DEFAULT NULL, INDEX IDX_CA4E9F4CDE12AB56 (created_by), INDEX IDX_CA4E9F4C25F94802 (modified_by), INDEX IDX_CA4E9F4C1F6FA0AF (deleted_by), INDEX IDX_CA4E9F4C9F5CF488 (risk_model_id), INDEX IDX_CA4E9F4CB213FA4 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE risk_model_levels ADD CONSTRAINT FK_A07B4CECDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_model_levels ADD CONSTRAINT FK_A07B4CEC25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_model_levels ADD CONSTRAINT FK_A07B4CEC1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_model_levels ADD CONSTRAINT FK_A07B4CEC9F5CF488 FOREIGN KEY (risk_model_id) REFERENCES risk_model_master (id)');
        $this->addSql('ALTER TABLE risk_model_master ADD CONSTRAINT FK_12588B23DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_model_master ADD CONSTRAINT FK_12588B2325F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_model_master ADD CONSTRAINT FK_12588B231F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_model_master_lang ADD CONSTRAINT FK_CA4E9F4CDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_model_master_lang ADD CONSTRAINT FK_CA4E9F4C25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_model_master_lang ADD CONSTRAINT FK_CA4E9F4C1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_model_master_lang ADD CONSTRAINT FK_CA4E9F4C9F5CF488 FOREIGN KEY (risk_model_id) REFERENCES risk_model_master (id)');
        $this->addSql('ALTER TABLE risk_model_master_lang ADD CONSTRAINT FK_CA4E9F4CB213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
       
        $this->addSql('ALTER TABLE person ADD risk_model_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD17625F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1761F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1769F5CF488 FOREIGN KEY (risk_model_id) REFERENCES risk_model_master (id)');
        $this->addSql('CREATE INDEX IDX_34DCD176DE12AB56 ON person (created_by)');
        $this->addSql('CREATE INDEX IDX_34DCD17625F94802 ON person (modified_by)');
        $this->addSql('CREATE INDEX IDX_34DCD1761F6FA0AF ON person (deleted_by)');
        $this->addSql('CREATE INDEX IDX_34DCD1769F5CF488 ON person (risk_model_id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1769F5CF488');
        $this->addSql('ALTER TABLE risk_model_levels DROP FOREIGN KEY FK_A07B4CEC9F5CF488');
        $this->addSql('ALTER TABLE risk_model_master_lang DROP FOREIGN KEY FK_CA4E9F4C9F5CF488');
        $this->addSql('DROP TABLE risk_model_levels');
        $this->addSql('DROP TABLE risk_model_master');
        $this->addSql('DROP TABLE risk_model_master_lang');
        $this->addSql('DROP INDEX IDX_34DCD176DE12AB56 ON person');
        $this->addSql('DROP INDEX IDX_34DCD17625F94802 ON person');
        $this->addSql('DROP INDEX IDX_34DCD1761F6FA0AF ON person');
        $this->addSql('DROP INDEX IDX_34DCD1769F5CF488 ON person');
        $this->addSql('ALTER TABLE person DROP risk_model_id');
       
    }
}
