<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160913103323 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE org_person_faculty ADD COLUMN oauth_one_time_token VARCHAR(100) NULL  AFTER google_sync_disabled_time , ADD COLUMN oauth_cal_access_token VARCHAR(300) NULL  AFTER oauth_one_time_token, ADD COLUMN oauth_cal_refresh_token VARCHAR(100) NULL  AFTER oauth_cal_access_token;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("alter table org_person_faculty drop column `oauth_one_time_token`");
        $this->addSql("alter table org_person_faculty drop column `oauth_cal_access_token`");
        $this->addSql("alter table org_person_faculty drop column `oauth_cal_refresh_token`");
    }
}
