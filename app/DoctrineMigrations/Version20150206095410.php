<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150206095410 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE org_search_shared (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_search_id_source INT DEFAULT NULL, person_id_sharedby INT DEFAULT NULL, person_id_sharedwith INT DEFAULT NULL, org_search_id_dest INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_49F4EBA8DE12AB56 (created_by), INDEX IDX_49F4EBA825F94802 (modified_by), INDEX IDX_49F4EBA81F6FA0AF (deleted_by), INDEX IDX_49F4EBA8F459207D (org_search_id_source), INDEX IDX_49F4EBA8BF1A33A5 (person_id_sharedby), INDEX IDX_49F4EBA85EA2D51A (person_id_sharedwith), INDEX IDX_49F4EBA887A20C50 (org_search_id_dest), UNIQUE INDEX fk_org_search_shared_unique (org_search_id_source, person_id_sharedby, person_id_sharedwith, org_search_id_dest), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_search_shared ADD CONSTRAINT FK_49F4EBA8DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search_shared ADD CONSTRAINT FK_49F4EBA825F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search_shared ADD CONSTRAINT FK_49F4EBA81F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search_shared ADD CONSTRAINT FK_49F4EBA8F459207D FOREIGN KEY (org_search_id_source) REFERENCES org_search (id)');
        $this->addSql('ALTER TABLE org_search_shared ADD CONSTRAINT FK_49F4EBA8BF1A33A5 FOREIGN KEY (person_id_sharedby) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search_shared ADD CONSTRAINT FK_49F4EBA85EA2D51A FOREIGN KEY (person_id_sharedwith) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search_shared ADD CONSTRAINT FK_49F4EBA887A20C50 FOREIGN KEY (org_search_id_dest) REFERENCES org_search (id)');
        $this->addSql('ALTER TABLE org_search DROP FOREIGN KEY FK_17B296205EA2D51A');
        $this->addSql('ALTER TABLE org_search DROP FOREIGN KEY FK_17B29620BF1A33A5');
        $this->addSql('DROP INDEX IDX_17B29620BF1A33A5 ON org_search');
        $this->addSql('DROP INDEX IDX_17B296205EA2D51A ON org_search');
        $this->addSql('ALTER TABLE org_search DROP person_id_sharedwith, DROP person_id_sharedby, CHANGE shared_on shared_on DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE org_search_shared');
        $this->addSql('ALTER TABLE org_search ADD person_id_sharedwith INT DEFAULT NULL, ADD person_id_sharedby INT DEFAULT NULL, CHANGE shared_on shared_on DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE org_search ADD CONSTRAINT FK_17B296205EA2D51A FOREIGN KEY (person_id_sharedwith) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search ADD CONSTRAINT FK_17B29620BF1A33A5 FOREIGN KEY (person_id_sharedby) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_17B29620BF1A33A5 ON org_search (person_id_sharedby)');
        $this->addSql('CREATE INDEX IDX_17B296205EA2D51A ON org_search (person_id_sharedwith)');   
    }
}
