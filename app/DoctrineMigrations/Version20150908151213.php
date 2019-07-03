<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150908151213 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
        $this->addSql('ALTER TABLE reports_running_status ADD response_json  LONGTEXT DEFAULT NULL, CHANGE is_viewed is_viewed enum(\'Y\',\'N\'), CHANGE filtered_student_ids filtered_student_ids LONGTEXT DEFAULT NULL, CHANGE filter_criteria filter_criteria LONGTEXT DEFAULT NULL, CHANGE report_custom_title report_custom_title VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

       
        $this->addSql('ALTER TABLE reports_running_status DROP response_json , CHANGE is_viewed is_viewed VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE filtered_student_ids filtered_student_ids LONGTEXT NOT NULL COLLATE utf8_unicode_ci, CHANGE filter_criteria filter_criteria LONGTEXT NOT NULL COLLATE utf8_unicode_ci, CHANGE report_custom_title report_custom_title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');       
    }
}
