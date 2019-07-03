<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150826143454 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE reports CHANGE COLUMN is_batch_job `is_batch_job` VARCHAR(3) NULL DEFAULT NULL') ;
        
        $this->addSql('update reports set is_batch_job = NULL');
        
        $this->addSql('ALTER TABLE reports CHANGE description description VARCHAR(255) NOT NULL, CHANGE is_batch_job is_batch_job enum(\'y\',\'n\'), CHANGE is_coordinator_report is_coordinator_report enum(\'y\',\'n\') DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reports CHANGE description description VARCHAR(256) NOT NULL COLLATE utf8_unicode_ci, CHANGE is_batch_job is_batch_job VARCHAR(3) NOT NULL COLLATE utf8_unicode_ci, CHANGE is_coordinator_report is_coordinator_report TINYINT(1) DEFAULT NULL');
    }
}
