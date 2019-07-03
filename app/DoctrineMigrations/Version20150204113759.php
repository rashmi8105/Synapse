<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150204113759 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE org_search (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT NOT NULL, person_id INT NOT NULL, person_id_sharedby INT NOT NULL, person_id_sharedwith INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(100) DEFAULT NULL, query VARCHAR(5000) DEFAULT NULL, json VARCHAR(3000) DEFAULT NULL, shared_on DATE DEFAULT NULL, INDEX IDX_17B29620DE12AB56 (created_by), INDEX IDX_17B2962025F94802 (modified_by), INDEX IDX_17B296201F6FA0AF (deleted_by), INDEX IDX_17B2962032C8A3DE (organization_id), INDEX IDX_17B29620217BBB47 (person_id), INDEX IDX_17B29620BF1A33A5 (person_id_sharedby), INDEX IDX_17B296205EA2D51A (person_id_sharedwith), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_search ADD CONSTRAINT FK_17B29620DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search ADD CONSTRAINT FK_17B2962025F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search ADD CONSTRAINT FK_17B296201F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search ADD CONSTRAINT FK_17B2962032C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_search ADD CONSTRAINT FK_17B29620217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search ADD CONSTRAINT FK_17B29620BF1A33A5 FOREIGN KEY (person_id_sharedby) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search ADD CONSTRAINT FK_17B296205EA2D51A FOREIGN KEY (person_id_sharedwith) REFERENCES person (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE org_search');
    }
}
