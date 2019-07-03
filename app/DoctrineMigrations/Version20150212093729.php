<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150212093729 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE ebi_metadata_lang (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, lang_id INT DEFAULT NULL, ebi_metadata_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, meta_name VARCHAR(255) DEFAULT NULL, meta_description LONGTEXT DEFAULT NULL, INDEX IDX_50297B15DE12AB56 (created_by), INDEX IDX_50297B1525F94802 (modified_by), INDEX IDX_50297B151F6FA0AF (deleted_by), INDEX IDX_50297B15B213FA4 (lang_id), INDEX IDX_50297B15BB49FE75 (ebi_metadata_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ebi_metadata_list_values (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, lang_id INT DEFAULT NULL, ebi_metadata_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, list_name VARCHAR(1000) DEFAULT NULL, list_value VARCHAR(255) DEFAULT NULL, sequence INT DEFAULT NULL, INDEX IDX_2C2774C3DE12AB56 (created_by), INDEX IDX_2C2774C325F94802 (modified_by), INDEX IDX_2C2774C31F6FA0AF (deleted_by), INDEX IDX_2C2774C3B213FA4 (lang_id), INDEX IDX_2C2774C3BB49FE75 (ebi_metadata_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ebi_metadata_lang ADD CONSTRAINT FK_50297B15DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_metadata_lang ADD CONSTRAINT FK_50297B1525F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_metadata_lang ADD CONSTRAINT FK_50297B151F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_metadata_lang ADD CONSTRAINT FK_50297B15B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE ebi_metadata_lang ADD CONSTRAINT FK_50297B15BB49FE75 FOREIGN KEY (ebi_metadata_id) REFERENCES ebi_metadata (id)');
        $this->addSql('ALTER TABLE ebi_metadata_list_values ADD CONSTRAINT FK_2C2774C3DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_metadata_list_values ADD CONSTRAINT FK_2C2774C325F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_metadata_list_values ADD CONSTRAINT FK_2C2774C31F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_metadata_list_values ADD CONSTRAINT FK_2C2774C3B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE ebi_metadata_list_values ADD CONSTRAINT FK_2C2774C3BB49FE75 FOREIGN KEY (ebi_metadata_id) REFERENCES ebi_metadata (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE ebi_metadata_lang');
        $this->addSql('DROP TABLE ebi_metadata_list_values');
    }
}
