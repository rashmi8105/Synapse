<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141217084859 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE ebi_search (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, query_key VARCHAR(255) DEFAULT NULL, search_type VARCHAR(1) DEFAULT NULL, is_enabled TINYINT(1) DEFAULT NULL, query LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ebi_search_criteria (id INT AUTO_INCREMENT NOT NULL, ebi_search_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, table_name VARCHAR(255) DEFAULT NULL, field_name VARCHAR(255) DEFAULT NULL, operator VARCHAR(255) DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, join_condition VARCHAR(255) DEFAULT NULL, INDEX IDX_486A32643849DC27 (ebi_search_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ebi_search_lang (id INT AUTO_INCREMENT NOT NULL, ebi_search_id INT DEFAULT NULL, language_master_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_DC486273849DC27 (ebi_search_id), INDEX IDX_DC48627D5D3A0FB (language_master_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ebi_search_criteria ADD CONSTRAINT FK_486A32643849DC27 FOREIGN KEY (ebi_search_id) REFERENCES ebi_search (id)');
        $this->addSql('ALTER TABLE ebi_search_lang ADD CONSTRAINT FK_DC486273849DC27 FOREIGN KEY (ebi_search_id) REFERENCES ebi_search (id)');
        $this->addSql('ALTER TABLE ebi_search_lang ADD CONSTRAINT FK_DC48627D5D3A0FB FOREIGN KEY (language_master_id) REFERENCES language_master (id)');       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE ebi_search_criteria DROP FOREIGN KEY FK_486A32643849DC27');
        $this->addSql('ALTER TABLE ebi_search_lang DROP FOREIGN KEY FK_DC486273849DC27');
        $this->addSql('DROP TABLE ebi_search');
        $this->addSql('DROP TABLE ebi_search_criteria');
        $this->addSql('DROP TABLE ebi_search_lang');       
    }
}
