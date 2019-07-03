<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150504220150 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_static_list (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT NOT NULL, person_id INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(95) NOT NULL, description VARCHAR(300) DEFAULT NULL, personIdSharedBy INT DEFAULT NULL, sharedOn DATE DEFAULT NULL, INDEX IDX_43EAFB4BDE12AB56 (created_by), INDEX IDX_43EAFB4B25F94802 (modified_by), INDEX IDX_43EAFB4B1F6FA0AF (deleted_by), INDEX fk_org_staticlist_organization1_idx (organization_id), INDEX fk_staticlist_person1_idx (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_static_list ADD CONSTRAINT FK_43EAFB4BDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_static_list ADD CONSTRAINT FK_43EAFB4B25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_static_list ADD CONSTRAINT FK_43EAFB4B1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_static_list ADD CONSTRAINT FK_43EAFB4B32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_static_list ADD CONSTRAINT FK_43EAFB4B217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_static_list');
    }
}
