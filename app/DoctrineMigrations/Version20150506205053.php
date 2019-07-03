<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150506205053 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_static_list_students (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT NOT NULL, person_id INT NOT NULL, org_static_list_id INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_5A73DC8FDE12AB56 (created_by), INDEX IDX_5A73DC8F25F94802 (modified_by), INDEX IDX_5A73DC8F1F6FA0AF (deleted_by), INDEX fk_org_staticlist_organization1_idx (organization_id), INDEX fk_staticlist_person1_idx (person_id), INDEX fk_staticlist_org_static_list_id1_idx (org_static_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_static_list_students ADD CONSTRAINT FK_5A73DC8FDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_static_list_students ADD CONSTRAINT FK_5A73DC8F25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_static_list_students ADD CONSTRAINT FK_5A73DC8F1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_static_list_students ADD CONSTRAINT FK_5A73DC8F32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_static_list_students ADD CONSTRAINT FK_5A73DC8F217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_static_list_students ADD CONSTRAINT FK_5A73DC8FAD199442 FOREIGN KEY (org_static_list_id) REFERENCES org_static_list (id)');       
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_static_list_students');
    }
}
