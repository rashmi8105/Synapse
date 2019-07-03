<?php

    namespace Synapse\Migrations;

    use Doctrine\DBAL\Migrations\AbstractMigration;
    use Doctrine\DBAL\Schema\Schema;

    /**
     * Migration script to add tables used by compare report to store the JSONs used by R-Script for doing statistical analysis and generating JSON output to be used by the Report. These tables were earlier created in wall schema, but now being moved to synapse schema
     *
     * Ticket: https://jira-mnv.atlassian.net/browse/ESPRJ-14324
     */
    class Version20170417101001 extends AbstractMigration
    {
        /**
         * @param Schema $schema
         */
        public function up(Schema $schema)
        {
            // this up() migration is auto-generated, please modify it to your needs
            $this->addSQL('DROP TABLE IF EXISTS reports_running_json');

            $this->addSQL('CREATE TABLE reports_running_json (
                id int(11) NOT NULL AUTO_INCREMENT,
                request_json longtext COLLATE utf8_unicode_ci,
                report_running_status_json longtext COLLATE utf8_unicode_ci,
                factor_json longtext COLLATE utf8_unicode_ci,
                gpa_json longtext COLLATE utf8_unicode_ci,
                created_by int(11) DEFAULT NULL,
                modified_by int(11) DEFAULT NULL,
                deleted_by int(11) DEFAULT NULL,
                created_at datetime DEFAULT NULL,
                modified_at datetime DEFAULT NULL,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id),
                KEY `IDX_reports_running_json_ibfk_1` (`created_by`),
                KEY `IDX_reports_running_json_ibfk_2` (`modified_by`),
                KEY `IDX_reports_running_json_ibfk_3` (`deleted_by`),
                CONSTRAINT `FK_reports_running_json_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
                CONSTRAINT `FK_reports_running_json_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
                CONSTRAINT `FK_reports_running_json_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`)
            )');

        }

        /**
         * @param Schema $schema
         */
        public function down(Schema $schema)
        {
            $this->addSQL('DROP TABLE IF EXISTS reports_running_json');

        }
    }
