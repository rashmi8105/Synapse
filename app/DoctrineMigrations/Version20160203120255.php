<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds column to report_sections table for use in Executive Summary Report filter.
 * Adds report_sections and report_section_elements records for Executive Summary Report.
 */
class Version20160203120255 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('alter table report_sections add column retention_tracking_type enum("required", "optional", "none");');


        $this->addSql('insert into report_sections (report_id, title, sequence, retention_tracking_type)
                        select id, "What is Mapworks?", 1, "none"
                        from reports where short_code="EXEC";');

        $this->addSql('insert into report_sections (report_id, title, sequence, retention_tracking_type)
                        select id, "Risk Profile", 2, "optional"
                        from reports where short_code="EXEC";');

        $this->addSql('insert into report_sections (report_id, title, sequence, retention_tracking_type)
                        select id, "GPA by Risk", 3, "optional"
                        from reports where short_code="EXEC";');

        $this->addSql('insert into report_sections (report_id, title, sequence, retention_tracking_type)
                        select id, "Intent to Leave and Persistence", 4, "required"
                        from reports where short_code="EXEC";');

        $this->addSql('insert into report_sections (report_id, title, sequence, retention_tracking_type)
                        select id, "Persistence and Retention by Risk", 5, "required"
                        from reports where short_code="EXEC";');

        $this->addSql('insert into report_sections (report_id, title, sequence, retention_tracking_type)
                        select id, "Top Factors with Correlation to Persistence and Retention", 6, "required"
                        from reports where short_code="EXEC";');

        $this->addSql('insert into report_sections (report_id, title, sequence, retention_tracking_type)
                        select id, "Top Factors with Correlation to GPA", 7, "optional"
                        from reports where short_code="EXEC";');

        $this->addSql('insert into report_sections (report_id, title, sequence, retention_tracking_type)
                        select id, "Activity Overview", 8, "optional"
                        from reports where short_code="EXEC";');


        $this->addSql('insert into report_section_elements (section_id, title, description)
                        select id, "Purpose", "Mapworks is a holistic approach to student success and retention, providing a platform of information that faculty and staff use to identify at-risk students early in the term. It also allows faculty and staff the ability to coordinate interventions with at-risk students by providing the power of real-time analytics, strategic communications, and differentiated user interfacing, with integrated statistical testing and outcomes reporting."
                        from report_sections
                        where title = "What is Mapworks?";');

        $this->addSql('insert into report_section_elements (section_id, title)
                        select id, "Rationale"
                        from report_sections
                        where title = "What is Mapworks?";');

        $this->addSql('insert into report_section_elements (section_id, title, description)
                        select id, "Process", "The Mapworks process includes combining data from the institution with information from the students. Using that information, Mapworks uses real-time analytics to provide information directly to the students as well as to the faculty and staff working with the students."
                        from report_sections
                        where title = "What is Mapworks?";');

        $this->addSql('insert into report_section_elements (section_id, title)
                        select id, "Graphic"
                        from report_sections
                        where title = "What is Mapworks?";');


        $this->addSql('insert into report_section_elements (section_id, title)
                        select id, "Referrals"
                        from report_sections
                        where title = "Activity Overview";');

        $this->addSql('insert into report_section_elements (section_id, title)
                        select id, "Appointments"
                        from report_sections
                        where title = "Activity Overview";');

        $this->addSql('insert into report_section_elements (section_id, title)
                        select id, "Contacts"
                        from report_sections
                        where title = "Activity Overview";');

        $this->addSql('insert into report_section_elements (section_id, title)
                        select id, "Interaction Contacts"
                        from report_sections
                        where title = "Activity Overview";');

        $this->addSql('insert into report_section_elements (section_id, title)
                        select id, "Notes"
                        from report_sections
                        where title = "Activity Overview";');

        $this->addSql('insert into report_section_elements (section_id, title)
                        select id, "Academic Updates"
                        from report_sections
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
