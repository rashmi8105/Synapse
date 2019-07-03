<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160726091915 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE org_person_faculty ADD COLUMN google_sync_status ENUM('0','1') NULL  AFTER msexchange_sync_state , ADD COLUMN google_sync_disabled_time DATETIME NULL  AFTER google_sync_status;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("alter table org_person_student drop column `google_sync_status`");
        $this->addSql("alter table org_person_faculty drop column `google_sync_disabled_time`");
    }
}
