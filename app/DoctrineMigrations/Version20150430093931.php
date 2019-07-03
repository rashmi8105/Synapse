<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150430093931 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE risk_model_weights (risk_model_id INT NOT NULL, risk_variable_id INT NOT NULL, weight NUMERIC(8, 4) DEFAULT NULL, INDEX fk_risk_model_bucket_risk_model_master1_idx (risk_model_id), INDEX fk_risk_model_bucket_risk_variable1_idx (risk_variable_id), PRIMARY KEY(risk_model_id, risk_variable_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE risk_model_weights ADD CONSTRAINT FK_D73A24E39F5CF488 FOREIGN KEY (risk_model_id) REFERENCES risk_model_master (id)');
        $this->addSql('ALTER TABLE risk_model_weights ADD CONSTRAINT FK_D73A24E3296E76DF FOREIGN KEY (risk_variable_id) REFERENCES risk_variable (id)');
        
        
        
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE risk_model_weights');       
        
    }
}
