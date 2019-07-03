<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150511232551 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE audit_trail (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, route VARCHAR(255) NOT NULL, class VARCHAR(255) NOT NULL, method VARCHAR(255) NOT NULL, request LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', unit_of_work LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', status enum(\'SUCCESS\', \'FAIL\'), INDEX IDX_B523E178DE12AB56 (created_by), INDEX IDX_B523E17825F94802 (modified_by), INDEX IDX_B523E1781F6FA0AF (deleted_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit_trail ADD CONSTRAINT FK_B523E178DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE audit_trail ADD CONSTRAINT FK_B523E17825F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE audit_trail ADD CONSTRAINT FK_B523E1781F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE audit_trail');
    }
}
