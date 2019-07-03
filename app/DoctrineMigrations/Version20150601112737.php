<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150601112737 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_calculated_risk_variables (org_id INT NOT NULL, person_id INT NOT NULL, risk_variable_id INT NOT NULL, risk_model_id INT NOT NULL, calc_bucket_value INT DEFAULT NULL, calc_weight NUMERIC(8, 4) DEFAULT NULL, risk_source_value NUMERIC(12, 4) DEFAULT NULL, INDEX IDX_93D7B9DDF4837C1B (org_id), INDEX fk_org_computed_risk_variables_person1_idx (person_id), INDEX fk_org_computed_risk_variables_risk_variable1_idx (risk_variable_id), INDEX fk_org_calculated_risk_variables_risk_model_master1_idx (risk_model_id), PRIMARY KEY(org_id, person_id, risk_variable_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_calculated_risk_variables ADD CONSTRAINT FK_93D7B9DDF4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_calculated_risk_variables ADD CONSTRAINT FK_93D7B9DD217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_calculated_risk_variables ADD CONSTRAINT FK_93D7B9DD296E76DF FOREIGN KEY (risk_variable_id) REFERENCES risk_variable (id)');
        $this->addSql('ALTER TABLE org_calculated_risk_variables ADD CONSTRAINT FK_93D7B9DD9F5CF488 FOREIGN KEY (risk_model_id) REFERENCES risk_model_master (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_calculated_risk_variables');
        
    }
}
