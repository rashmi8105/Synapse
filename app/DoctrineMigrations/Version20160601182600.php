<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160601182600 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("alter table org_person_student
                               add column `auth_key` varchar(200) AFTER `surveycohort`");
        $this->addSql("alter table org_person_faculty
                               add column `auth_key` varchar(200) AFTER `status`");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("alter table org_person_student drop column `auth_key`");
        $this->addSql("alter table org_person_faculty drop column `auth_key`");
    }
}
