<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150909102216 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE reports CHANGE name name VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE reports ADD short_code VARCHAR(10) NOT NULL');
        
        $this->addSql("UPDATE `reports` SET `short_code` = 'SUR-SSR' WHERE id = 1");
        
        $this->addSql("INSERT INTO `reports` (`short_code`,`name`, `description`, `is_batch_job`, `is_coordinator_report`) VALUES ('SUR-IRR', 'Individual Survey Response Rate Report', 'Survey - Individual Response Rate Report', 'y', 'y');");
        $this->addSql("INSERT INTO `reports` (`short_code`,`name`, `description`, `is_batch_job`, `is_coordinator_report`) VALUES ('AU-R', 'Academic Update Report', 'Academic Update Report', 'y', 'y');");
        $this->addSql("INSERT INTO `reports` (`short_code`,`name`, `description`, `is_batch_job`, `is_coordinator_report`) VALUES ('ACT-R', 'Our Mapworks Activity Report', 'Mapworks activities between faculty/staff and students over a given time period', 'y', 'y');");
        $this->addSql("INSERT INTO `reports` (`short_code`,`name`, `description`, `is_batch_job`, `is_coordinator_report`) VALUES ('SUR-SR', 'Survey Snapshot Report', 'Report to see detailed analysis of survey questions and results for a given survey and cohort', 'y', 'n');");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE reports CHANGE name name VARCHAR(25) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE reports DROP short_code');
        
    }
}
