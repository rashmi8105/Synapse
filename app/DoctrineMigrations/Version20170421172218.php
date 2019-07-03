<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration to delete old data and insert properly named record for Outcomes Comparison (Compare) Report.
 *
 * ESPRJ-14317
 */
class Version20170421172218 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $this->addSql("INSERT INTO synapse.reports(created_by, modified_by, created_at, modified_at, name, description, is_batch_job, is_coordinator_report, short_code, is_active)
                       VALUES (-25, -25, NOW(), NOW(), 'Compare', 'Compare outcomes of two student subpopulations. Choose by profile item or survey question, compare survey factors and GPA.', 'y', 'n', 'SUB-COM', 'y');");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
