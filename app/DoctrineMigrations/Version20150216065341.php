<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150216065341 extends AbstractMigration
{
    public function up(Schema $schema)  
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        /**
         * For changing the foreign key ,need to drop this table and re creating it
         */
        $this->addSql('DROP TABLE datablock_metadata ');
        $this->addSql('CREATE TABLE datablock_metadata (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, datablock_id INT DEFAULT NULL, ebi_metadata_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_4B799CFFDE12AB56 (created_by), INDEX IDX_4B799CFF25F94802 (modified_by), INDEX IDX_4B799CFF1F6FA0AF (deleted_by), INDEX IDX_4B799CFFF9AE3580 (datablock_id), INDEX IDX_4B799CFFBB49FE75 (ebi_metadata_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE datablock_metadata ADD CONSTRAINT FK_4B799CFFDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE datablock_metadata ADD CONSTRAINT FK_4B799CFF25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE datablock_metadata ADD CONSTRAINT FK_4B799CFF1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE datablock_metadata ADD CONSTRAINT FK_4B799CFFF9AE3580 FOREIGN KEY (datablock_id) REFERENCES datablock_master (id)');
        $this->addSql('ALTER TABLE datablock_metadata ADD CONSTRAINT FK_4B799CFFBB49FE75 FOREIGN KEY (ebi_metadata_id) REFERENCES ebi_metadata (id)');
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP  TABLE datablock_metadata ');
        $this->addSql('CREATE TABLE datablock_metadata (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, datablock_id INT DEFAULT NULL, ebi_metadata_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_4B799CFFDE12AB56 (created_by), INDEX IDX_4B799CFF25F94802 (modified_by), INDEX IDX_4B799CFF1F6FA0AF (deleted_by), INDEX IDX_4B799CFFF9AE3580 (datablock_id), INDEX IDX_4B799CFFBB49FE75 (ebi_metadata_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE datablock_metadata ADD CONSTRAINT FK_4B799CFFDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE datablock_metadata ADD CONSTRAINT FK_4B799CFF25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE datablock_metadata ADD CONSTRAINT FK_4B799CFF1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE datablock_metadata ADD CONSTRAINT FK_4B799CFFF9AE3580 FOREIGN KEY (datablock_id) REFERENCES datablock_master (id)');
        $this->addSql('ALTER TABLE datablock_metadata ADD CONSTRAINT FK_4B799CFFBB49FE75 FOREIGN KEY (ebi_metadata_id) REFERENCES metadata_master (id)');
      
    }
}
