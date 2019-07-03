<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150429143237 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE risk_variable_category (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, risk_variable_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, bucket_value INT DEFAULT NULL, option_value VARCHAR(200) DEFAULT NULL, INDEX IDX_1B6EF2A7DE12AB56 (created_by), INDEX IDX_1B6EF2A725F94802 (modified_by), INDEX IDX_1B6EF2A71F6FA0AF (deleted_by), INDEX fk_risk_model_bucket_category_risk_variable1_idx (risk_variable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE risk_variable_category ADD CONSTRAINT FK_1B6EF2A7DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_variable_category ADD CONSTRAINT FK_1B6EF2A725F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_variable_category ADD CONSTRAINT FK_1B6EF2A71F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_variable_category ADD CONSTRAINT FK_1B6EF2A7296E76DF FOREIGN KEY (risk_variable_id) REFERENCES risk_variable (id)');
        
            }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE risk_variable_category');
       
    }
}
