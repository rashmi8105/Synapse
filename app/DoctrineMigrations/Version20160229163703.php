<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds survey_contingent column to report_sections table for use in Executive Summary Report filter
 * and populates it for sections in the Executive Summary Report.
 */
class Version20160229163703 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('alter table report_sections add column survey_contingent tinyint(1);');


        $this->addSql('update report_sections
                        set survey_contingent = 0
                        where title = "What is Mapworks?";');

        $this->addSql('update report_sections
                        set survey_contingent = 0
                        where title = "Risk Profile";');

        $this->addSql('update report_sections
                        set survey_contingent = 0
                        where title = "GPA by Risk";');

        $this->addSql('update report_sections
                        set survey_contingent = 1
                        where title = "Intent to Leave and Persistence";');

        $this->addSql('update report_sections
                        set survey_contingent = 0
                        where title = "Persistence and Retention by Risk";');

        $this->addSql('update report_sections
                        set survey_contingent = 1
                        where title = "Top Factors with Correlation to Persistence and Retention";');

        $this->addSql('update report_sections
                        set survey_contingent = 1
                        where title = "Top Factors with Correlation to GPA";');

        $this->addSql('update report_sections
                        set survey_contingent = 0
                        where title = "Activity Overview";');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
