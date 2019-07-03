<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150224113148 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');        
        $this->addSql('ALTER TABLE org_permissionset ADD view_courses TINYINT(1) DEFAULT NULL, ADD create_view_academic_update TINYINT(1) DEFAULT NULL, ADD view_all_academic_update_courses TINYINT(1) DEFAULT NULL, ADD view_all_final_grades TINYINT(1) DEFAULT NULL');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE org_permissionset DROP view_courses, DROP create_view_academic_update, DROP view_all_academic_update_courses, DROP view_all_final_grades');
       
    }
}
