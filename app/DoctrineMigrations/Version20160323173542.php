<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds two more columns to the report_sections table for use in the Executive Summary Report filters.
 */
class Version20160323173542 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('alter table report_sections add column academic_term_contingent tinyint(1);');

        $this->addSql('alter table report_sections add column risk_contingent tinyint(1);');


        $this->addSql('update report_sections
                        set academic_term_contingent = 0, risk_contingent = 0
                        where title = "What is Mapworks?";');

        $this->addSql('update report_sections
                        set academic_term_contingent = 0, risk_contingent = 1
                        where title = "Risk Profile";');

        $this->addSql('update report_sections
                        set academic_term_contingent = 1, risk_contingent = 1
                        where title = "GPA by Risk";');

        $this->addSql('update report_sections
                        set academic_term_contingent = 0, risk_contingent = 0
                        where title = "Intent to Leave and Persistence";');

        $this->addSql('update report_sections
                        set academic_term_contingent = 0, risk_contingent = 1
                        where title = "Persistence and Retention by Risk";');

        $this->addSql('update report_sections
                        set academic_term_contingent = 0, risk_contingent = 0
                        where title = "Top Factors with Correlation to Persistence and Retention";');

        $this->addSql('update report_sections
                        set academic_term_contingent = 1, risk_contingent = 0
                        where title = "Top Factors with Correlation to GPA";');

        $this->addSql('update report_sections
                        set academic_term_contingent = 0, risk_contingent = 0
                        where title = "Activity Overview";');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
