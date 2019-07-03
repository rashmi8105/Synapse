<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20152210185911 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("update reports set description = 'Compare survey response rates for different groups.  Export to csv' where short_code = 'SUR-GRR';");
		
        $this->addSql("update reports set description = 'See aggregated values of all survey factors for a given survey and cohort.  Drill down to see individual students' where short_code = 'SUR-FR';");
		
        $this->addSql("update reports set description = 'See aggregated responses to all survey questions for a given survey and cohort.  Drill down to see individual students' where short_code = 'SUR-SR';");
		
        $this->addSql("update reports set description = 'View statistics on faculty and student activity tracked in Mapworks for a given date range.  Export to pdf' where short_code = 'MAR';");        
		
        $this->addSql("update reports set description = 'See Top Five issues, high-level survey data and demographics for a single survey and cohort.. Export to pdf' where short_code = 'OSR';");
		
        $this->addSql("update reports set description = 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions' where short_code = 'SUR-IRR';");
		
		$this->addSql("update reports set description = 'See all academic updates for your students.  Export to csv, perform individual or bulk actions' where short_code = 'AU-R';");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}



