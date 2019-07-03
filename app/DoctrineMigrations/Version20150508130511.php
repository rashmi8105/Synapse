<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150508130511 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE IF EXISTS org_risk_group_model');
        $this->addSql('CREATE TABLE org_risk_group_model (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT DEFAULT NULL, risk_model_id INT DEFAULT NULL, risk_group_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, assignment_date DATETIME DEFAULT NULL, INDEX IDX_6B2BABA4DE12AB56 (created_by), INDEX IDX_6B2BABA425F94802 (modified_by), INDEX IDX_6B2BABA41F6FA0AF (deleted_by), INDEX fk_orgriskmodel_orgid (org_id), INDEX fk_orgriskmodel_riskmodelid (risk_model_id), INDEX fk_org_risk_group_model_risk_group1_idx (risk_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_risk_group_model ADD CONSTRAINT FK_6B2BABA4DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_risk_group_model ADD CONSTRAINT FK_6B2BABA425F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_risk_group_model ADD CONSTRAINT FK_6B2BABA41F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_risk_group_model ADD CONSTRAINT FK_6B2BABA4F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_risk_group_model ADD CONSTRAINT FK_6B2BABA49F5CF488 FOREIGN KEY (risk_model_id) REFERENCES risk_model_master (id)');
        $this->addSql('ALTER TABLE org_risk_group_model ADD CONSTRAINT FK_6B2BABA4187D9A28 FOREIGN KEY (risk_group_id) REFERENCES risk_group (id)');        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_risk_group_model'); 
        $this->addSql('CREATE TABLE org_risk_group_model (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT DEFAULT NULL, risk_model_id INT DEFAULT NULL, risk_group_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, assignment_date DATETIME DEFAULT NULL, INDEX IDX_6B2BABA4DE12AB56 (created_by), INDEX IDX_6B2BABA425F94802 (modified_by), INDEX IDX_6B2BABA41F6FA0AF (deleted_by), INDEX fk_orgriskmodel_orgid (org_id), INDEX fk_orgriskmodel_riskmodelid (risk_model_id), INDEX fk_org_risk_group_model_risk_group1_idx (risk_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        
    }
}
