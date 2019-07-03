<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150508142629 extends AbstractMigration
{
    public function up(Schema $schema)
    {
    	// this up() migration is auto-generated, please modify it to your needs
    	$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    
    	$this->addSql('DROP TABLE risk_model_master_lang');
    	$this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1769F5CF488');
    	$this->addSql('DROP INDEX IDX_34DCD1769F5CF488 ON person');
    	$this->addSql('ALTER TABLE person DROP risk_model_id');
    	$this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176EB88056C FOREIGN KEY (risk_level) REFERENCES risk_levels (id)');
    	$this->addSql('CREATE INDEX fk_person_risk_level1_idx ON person (risk_level)');
    	$this->addSql('ALTER TABLE risk_levels CHANGE color_hex color_hex VARCHAR(10) DEFAULT NULL, CHANGE text risk_text VARCHAR(10) DEFAULT NULL');
    	$this->addSql('ALTER TABLE risk_model_levels MODIFY id INT NOT NULL');
    	$this->addSql('ALTER TABLE risk_model_levels DROP PRIMARY KEY');
    	$this->addSql('ALTER TABLE risk_model_levels DROP FOREIGN KEY FK_A07B4CECEB88056C');
    	$this->addSql('ALTER TABLE risk_model_levels DROP FOREIGN KEY FK_A07B4CEC9F5CF488');
    	$this->addSql('ALTER TABLE risk_model_levels DROP id, DROP risk_text, DROP image_name, CHANGE risk_level risk_level INT NOT NULL, CHANGE risk_model_id risk_model_id INT NOT NULL, CHANGE min min NUMERIC(6, 4) DEFAULT NULL, CHANGE max max NUMERIC(6, 4) DEFAULT NULL');
    	$this->addSql('ALTER TABLE risk_model_levels ADD PRIMARY KEY (risk_model_id, risk_level)');
    	$this->addSql('DROP INDEX idx_a07b4cec9f5cf488 ON risk_model_levels');
    	$this->addSql('CREATE INDEX fk_risk_model_levels_risk_model_master1_idx ON risk_model_levels (risk_model_id)');
    	$this->addSql('DROP INDEX idx_a07b4ceceb88056c ON risk_model_levels');
    	$this->addSql('CREATE INDEX fk_risk_model_levels_risk_level1_idx ON risk_model_levels (risk_level)');
    	$this->addSql('ALTER TABLE risk_model_levels ADD CONSTRAINT FK_A07B4CECEB88056C FOREIGN KEY (risk_level) REFERENCES risk_levels (id)');
    	$this->addSql('ALTER TABLE risk_model_levels ADD CONSTRAINT FK_A07B4CEC9F5CF488 FOREIGN KEY (risk_model_id) REFERENCES risk_model_master (id)');
    }
    
    public function down(Schema $schema)
    {
    	// this down() migration is auto-generated, please modify it to your needs
    	$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    
    	$this->addSql('CREATE TABLE risk_model_master_lang (id INT AUTO_INCREMENT NOT NULL, lang_id INT DEFAULT NULL, deleted_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, risk_model_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, risk_model_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_CA4E9F4CDE12AB56 (created_by), INDEX IDX_CA4E9F4C25F94802 (modified_by), INDEX IDX_CA4E9F4C1F6FA0AF (deleted_by), INDEX IDX_CA4E9F4C9F5CF488 (risk_model_id), INDEX IDX_CA4E9F4CB213FA4 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    	$this->addSql('ALTER TABLE risk_model_master_lang ADD CONSTRAINT FK_CA4E9F4CB213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
    	$this->addSql('ALTER TABLE risk_model_master_lang ADD CONSTRAINT FK_CA4E9F4C1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
    	$this->addSql('ALTER TABLE risk_model_master_lang ADD CONSTRAINT FK_CA4E9F4C25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
    	$this->addSql('ALTER TABLE risk_model_master_lang ADD CONSTRAINT FK_CA4E9F4C9F5CF488 FOREIGN KEY (risk_model_id) REFERENCES risk_model_master (id)');
    	$this->addSql('ALTER TABLE risk_model_master_lang ADD CONSTRAINT FK_CA4E9F4CDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
    
    	$this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176EB88056C');
    	$this->addSql('DROP INDEX fk_person_risk_level1_idx ON person');
    
    
    	$this->addSql('ALTER TABLE person ADD risk_model_id INT DEFAULT NULL');
    	$this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1769F5CF488 FOREIGN KEY (risk_model_id) REFERENCES risk_model_master (id)');
    	$this->addSql('CREATE INDEX IDX_34DCD1769F5CF488 ON person (risk_model_id)');
    
    	$this->addSql('ALTER TABLE risk_levels CHANGE color_hex color_hex VARCHAR(7) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE risk_text text VARCHAR(10) DEFAULT NULL COLLATE utf8_unicode_ci');
    	 
    	$this->addSql('ALTER TABLE risk_model_levels DROP PRIMARY KEY');
    	$this->addSql('ALTER TABLE risk_model_levels DROP FOREIGN KEY FK_A07B4CEC9F5CF488');
    	$this->addSql('ALTER TABLE risk_model_levels DROP FOREIGN KEY FK_A07B4CECEB88056C');
    	$this->addSql('ALTER TABLE risk_model_levels ADD id INT AUTO_INCREMENT NOT NULL, ADD risk_text VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD image_name VARCHAR(200) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE risk_model_id risk_model_id INT DEFAULT NULL, CHANGE risk_level risk_level INT DEFAULT NULL, CHANGE min min NUMERIC(5, 3) DEFAULT NULL, CHANGE max max NUMERIC(5, 3) DEFAULT NULL');
    	$this->addSql('ALTER TABLE risk_model_levels ADD PRIMARY KEY (id)');
    	$this->addSql('DROP INDEX fk_risk_model_levels_risk_model_master1_idx ON risk_model_levels');
    	$this->addSql('CREATE INDEX IDX_A07B4CEC9F5CF488 ON risk_model_levels (risk_model_id)');
    	$this->addSql('DROP INDEX fk_risk_model_levels_risk_level1_idx ON risk_model_levels');
    	$this->addSql('CREATE INDEX IDX_A07B4CECEB88056C ON risk_model_levels (risk_level)');
    	$this->addSql('ALTER TABLE risk_model_levels ADD CONSTRAINT FK_A07B4CEC9F5CF488 FOREIGN KEY (risk_model_id) REFERENCES risk_model_master (id)');
    	$this->addSql('ALTER TABLE risk_model_levels ADD CONSTRAINT FK_A07B4CECEB88056C FOREIGN KEY (risk_level) REFERENCES risk_levels (id)');
    
    }
}
