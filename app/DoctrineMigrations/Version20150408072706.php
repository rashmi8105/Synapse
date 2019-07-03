<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150408072706 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE academic_update_request ADD select_course ENUM(\'all\',\'individual\',\'none\') DEFAULT NULL,ADD select_student ENUM(\'all\',\'individual\',\'none\') DEFAULT NULL, ADD select_faculty ENUM(\'all\',\'individual\',\'none\') DEFAULT NULL, ADD select_group ENUM(\'all\',\'individual\',\'none\') DEFAULT NULL, ADD select_metadata ENUM(\'all\',\'individual\',\'none\') DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE academic_update_request DROP select_course, DROP select_faculty, DROP select_student, DROP select_group, DROP select_metadata');
    }
}
