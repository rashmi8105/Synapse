<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140805185717 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE upload_file_log (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, person_id INT DEFAULT NULL, upload_type VARCHAR(1) NOT NULL, upload_date DATETIME NOT NULL, uploaded_columns VARCHAR(6000) NOT NULL, uploaded_row_count INT NOT NULL, status VARCHAR(1) NOT NULL, uploaded_file_path VARCHAR(500) NOT NULL, error_file_path VARCHAR(500) NOT NULL, job_number VARCHAR(500) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE upload_file_log");
    }
}
