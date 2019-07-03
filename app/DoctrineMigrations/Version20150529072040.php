<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150529072040 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_riskval_calc_inputs (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT DEFAULT NULL, person_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, is_riskval_calc_required ENUM(\'y\',\'n\') NOT NULL DEFAULT \'n\', INDEX IDX_B95C1D80DE12AB56 (created_by), INDEX IDX_B95C1D8025F94802 (modified_by), INDEX IDX_B95C1D801F6FA0AF (deleted_by), INDEX fk_org_riskval_calc_inputs_person1_idx (person_id), INDEX fk_org_riskval_calc_inputs_organization1_idx (org_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_riskval_calc_inputs ADD CONSTRAINT FK_B95C1D80DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_riskval_calc_inputs ADD CONSTRAINT FK_B95C1D8025F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_riskval_calc_inputs ADD CONSTRAINT FK_B95C1D801F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_riskval_calc_inputs ADD CONSTRAINT FK_B95C1D80F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_riskval_calc_inputs ADD CONSTRAINT FK_B95C1D80217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
   }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_riskval_calc_inputs');
        
    }
}
