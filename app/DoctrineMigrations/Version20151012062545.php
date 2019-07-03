<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151012062545 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE report_bucket_range (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, element_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, value NUMERIC(8, 4) DEFAULT NULL, INDEX IDX_F214B74BDE12AB56 (created_by), INDEX IDX_F214B74B25F94802 (modified_by), INDEX IDX_F214B74B1F6FA0AF (deleted_by), INDEX fk_report_element_buckets_report_elements1_idx (element_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE report_bucket_range ADD CONSTRAINT FK_F214B74BDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_bucket_range ADD CONSTRAINT FK_F214B74B25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_bucket_range ADD CONSTRAINT FK_F214B74B1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_bucket_range ADD CONSTRAINT FK_F214B74B1F1F2A24 FOREIGN KEY (element_id) REFERENCES report_element_buckets (id)');
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE report_bucket_range');
        
    }
}
