<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150320154639 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_search_shared_by (org_search_id INT NOT NULL, org_search_id_source INT NOT NULL, person_id_shared_by INT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, shared_on DATETIME DEFAULT NULL, INDEX IDX_36826091DE12AB56 (created_by), INDEX IDX_3682609125F94802 (modified_by), INDEX IDX_368260911F6FA0AF (deleted_by), INDEX fk_org_search_shared_by_org_search1_idx (org_search_id), INDEX fk_org_search_shared_by_org_search2_idx (org_search_id_source), INDEX fk_org_search_shared_by_person1_idx (person_id_shared_by), PRIMARY KEY(org_search_id, org_search_id_source, person_id_shared_by)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_search_shared_with (org_search_id INT NOT NULL, org_search_id_dest INT NOT NULL, person_id_sharedwith INT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, shared_on DATETIME DEFAULT NULL, INDEX IDX_17EAAF3BDE12AB56 (created_by), INDEX IDX_17EAAF3B25F94802 (modified_by), INDEX IDX_17EAAF3B1F6FA0AF (deleted_by), INDEX fk_org_search_shared_org_search1_idx (org_search_id), INDEX fk_org_search_shared_org_search2 (org_search_id_dest), INDEX fk_org_search_shared_person2 (person_id_sharedwith), PRIMARY KEY(org_search_id, org_search_id_dest, person_id_sharedwith)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_search_shared_by ADD CONSTRAINT FK_36826091DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search_shared_by ADD CONSTRAINT FK_3682609125F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search_shared_by ADD CONSTRAINT FK_368260911F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search_shared_by ADD CONSTRAINT FK_368260915C787CFB FOREIGN KEY (org_search_id) REFERENCES org_search (id)');
        $this->addSql('ALTER TABLE org_search_shared_by ADD CONSTRAINT FK_36826091F459207D FOREIGN KEY (org_search_id_source) REFERENCES org_search (id)');
        $this->addSql('ALTER TABLE org_search_shared_by ADD CONSTRAINT FK_36826091D85DD618 FOREIGN KEY (person_id_shared_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search_shared_with ADD CONSTRAINT FK_17EAAF3BDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search_shared_with ADD CONSTRAINT FK_17EAAF3B25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search_shared_with ADD CONSTRAINT FK_17EAAF3B1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_search_shared_with ADD CONSTRAINT FK_17EAAF3B5C787CFB FOREIGN KEY (org_search_id) REFERENCES org_search (id)');
        $this->addSql('ALTER TABLE org_search_shared_with ADD CONSTRAINT FK_17EAAF3B87A20C50 FOREIGN KEY (org_search_id_dest) REFERENCES org_search (id)');
        $this->addSql('ALTER TABLE org_search_shared_with ADD CONSTRAINT FK_17EAAF3B5EA2D51A FOREIGN KEY (person_id_sharedwith) REFERENCES person (id)');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_search_shared_by');
        $this->addSql('DROP TABLE org_search_shared_with');
    }
}
