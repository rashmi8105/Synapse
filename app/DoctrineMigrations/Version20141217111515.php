<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141217111515 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE ebi_search_lang DROP FOREIGN KEY FK_DC48627D5D3A0FB');
        $this->addSql('DROP INDEX IDX_DC48627D5D3A0FB ON ebi_search_lang');
        $this->addSql('ALTER TABLE ebi_search_lang CHANGE language_master_id language_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ebi_search_lang ADD CONSTRAINT FK_DC4862782F1BAF4 FOREIGN KEY (language_id) REFERENCES language_master (id)');
        $this->addSql('CREATE INDEX IDX_DC4862782F1BAF4 ON ebi_search_lang (language_id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE ebi_search_lang DROP FOREIGN KEY FK_DC4862782F1BAF4');
        $this->addSql('DROP INDEX IDX_DC4862782F1BAF4 ON ebi_search_lang');
        $this->addSql('ALTER TABLE ebi_search_lang CHANGE language_id language_master_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ebi_search_lang ADD CONSTRAINT FK_DC48627D5D3A0FB FOREIGN KEY (language_master_id) REFERENCES language_master (id)');
        $this->addSql('CREATE INDEX IDX_DC48627D5D3A0FB ON ebi_search_lang (language_master_id)');
        
    }
}
