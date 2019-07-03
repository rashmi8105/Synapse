<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151016102219 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_report_permissions (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, org_permissionset_id INT DEFAULT NULL, report_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, timeframe_all TINYINT(1) DEFAULT NULL, current_calendar TINYINT(1) DEFAULT NULL, previous_calendar TINYINT(1) DEFAULT NULL, next_period TINYINT(1) DEFAULT NULL, INDEX IDX_D210DC54DE12AB56 (created_by), INDEX IDX_D210DC5425F94802 (modified_by), INDEX IDX_D210DC541F6FA0AF (deleted_by), INDEX fk_org_report_permission_organization_id (organization_id), INDEX fk_org_report_permission_report_id (report_id), INDEX fk_org_report_permission_permissionset_id (org_permissionset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');        
        $this->addSql('ALTER TABLE org_report_permissions ADD CONSTRAINT FK_D210DC54DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_report_permissions ADD CONSTRAINT FK_D210DC5425F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_report_permissions ADD CONSTRAINT FK_D210DC541F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_report_permissions ADD CONSTRAINT FK_D210DC5432C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_report_permissions ADD CONSTRAINT FK_D210DC547ABB76BC FOREIGN KEY (org_permissionset_id) REFERENCES org_permissionset (id)');
        $this->addSql('ALTER TABLE org_report_permissions ADD CONSTRAINT FK_D210DC544BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE org_report_permissions');

    }
}
