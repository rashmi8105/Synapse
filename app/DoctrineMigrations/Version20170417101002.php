<?php

    namespace Synapse\Migrations;

    use Doctrine\DBAL\Migrations\AbstractMigration;
    use Doctrine\DBAL\Schema\Schema;

    /**
     * Migration script to add Compare report entry in "reports" table. This entry enables listing of report under the Mapworks Report Centre
     *
     * Ticket: https://jira-mnv.atlassian.net/browse/ESPRJ-14324
     */
    class Version20170417101002 extends AbstractMigration
    {
        /**
         * @param Schema $schema
         */
        public function up(Schema $schema)
        {
            $this->addSql("DELETE FROM reports where short_code like 'SUB-COM'");
            $this->addSql("INSERT INTO reports (created_by, modified_by, deleted_by, created_at, modified_at, deleted_at, name, description, is_batch_job, is_coordinator_report, short_code, is_active)
                           VALUES( NULL,
                                   NULL,
                                   NULL,
                                   NULL,
                                   NULL,
                                   NULL,
                                   'Compare',
                                   'Compare outcomes of two student subpopulations. Choose subpopulations by profile item, ISP, survey question or ISQ. Compare survey factors and GPA. Export to csv, print to pdf.',
                                   'n',
                                   'n',
                                   'SUB-COM',
                                   'y')");
        }

        /**
         * @param Schema $schema
         */
        public function down(Schema $schema)
        {
            $this->addSql("DELETE FROM reports WHERE short_code like 'SUB-COM'");

        }
    }
