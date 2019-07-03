<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script for updating the config values
 */
class Version20160922102014 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("UPDATE `synapse`.`ebi_config` SET  `value`='#/student-agenda' WHERE `key`='StudentDashboard_AppointmentPage'");
        $this->addSql("UPDATE `synapse`.`ebi_config` SET  `value`='#/dashboard/' WHERE `key`='Staff_ReferralPage'");
        $this->addSql("UPDATE `synapse`.`ebi_config` SET  `value`='#/dashboard/' WHERE `key`='Gateway_Staff_Landing_page'");
        $this->addSql("UPDATE `synapse`.`ebi_config` SET  `value`='#/student/' WHERE `key`='Gateway_Student_Landing_page'");
        $this->addSql("UPDATE `synapse`.`ebi_config` SET  `value`='#/academic-updates/update/' WHERE `key`='Academic_Update_Reminder_to_Faculty'");

        $this->addSql("UPDATE `synapse`.`ebi_config` SET `deleted_at`= now()  WHERE `key`='StaffDashboard_AppointmentPage'");
        $this->addSql("UPDATE `synapse`.`ebi_config` SET `deleted_at`= now()  WHERE `key`='Academic_Update_View_URL'");
        $this->addSql("UPDATE `synapse`.`ebi_config` SET `deleted_at`= now()  WHERE `key`='MultiCampus_Change_Request'");
        $this->addSql("UPDATE `synapse`.`ebi_config` SET `deleted_at`= now()  WHERE `key`='Student_ResetPwd_URL_Prefix'");


        $this->addSql("UPDATE `synapse`.`ebi_config` SET  `key`='Gateway_Staff_Landing_Page' WHERE `key`='Gateway_Staff_Landing_page'");
        $this->addSql("UPDATE `synapse`.`ebi_config` SET  `key`='Gateway_Student_Landing_Page' WHERE `key`='Gateway_Student_Landing_page'");
    }
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

    }
}
