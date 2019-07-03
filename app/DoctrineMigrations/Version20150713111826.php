<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150713111826 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE academic_update_request_static_list (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_static_list_id INT DEFAULT NULL, academic_update_request_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_D9E4575FDE12AB56 (created_by), INDEX IDX_D9E4575F25F94802 (modified_by), INDEX IDX_D9E4575F1F6FA0AF (deleted_by), INDEX fk_academic_update_request_static_list_organization1_idx (organization_id), INDEX fk_academic_update_request_static_list_academic_update_requ_idx (academic_update_request_id), INDEX fk_academic_update_request_static_list_org_static_list1_idx (org_static_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE academic_update_request_static_list ADD CONSTRAINT FK_D9E4575FDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update_request_static_list ADD CONSTRAINT FK_D9E4575F25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update_request_static_list ADD CONSTRAINT FK_D9E4575F1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE academic_update_request_static_list ADD CONSTRAINT FK_D9E4575FAD199442 FOREIGN KEY (org_static_list_id) REFERENCES org_static_list (id)');
        $this->addSql('ALTER TABLE academic_update_request_static_list ADD CONSTRAINT FK_D9E4575FCA3D7B42 FOREIGN KEY (academic_update_request_id) REFERENCES academic_update_request (id)');
        $this->addSql('ALTER TABLE academic_update_request_static_list ADD CONSTRAINT FK_D9E4575F32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE academic_update_request_static_list');
    }
}
