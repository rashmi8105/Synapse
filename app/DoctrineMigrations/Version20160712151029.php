<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-10990
 * Replacing is_email_sent column with two new columns in_progress_email_sent and completion_email_sent
 * Removal of is_email_sent will come later
 */
class Version20160712151029 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE org_calc_flags_student_reports
                        ADD in_progress_email_sent tinyint(1) NOT NULL DEFAULT 0
                        AFTER is_email_sent,
                        ADD completion_email_sent tinyint(1) NOT NULL DEFAULT 0
                        AFTER in_progress_email_sent;");

        $this->addSql("update org_calc_flags_student_reports SET in_progress_email_sent = 1 WHERE modified_at < '2016-08-01 00:00:00';");


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
