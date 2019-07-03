<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150806124822 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("
              SET @emtid := (SELECT c.value FROM synapse.ebi_config c where c.key =\"System_URL\");
        
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/activate/') WHERE `key`='Staff_Activation_URL_Prefix';
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/resetPassword/') WHERE `key`='Coordinator_ResetPwd_URL_Prefix';
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/activatecoordinator/') WHERE `key`='Coordinator_Activation_URL_Prefix';
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/resetPassword/') WHERE `key`='Staff_ResetPwd_URL_Prefix';
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/viewstaffcalendars/') WHERE `key`='StaffDashboard_AppointmentPage';
        
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/viewstudentcalendars/') WHERE `key`='StudentDashboard_AppointmentPage';
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/dashboard/') WHERE `key`='Gateway_Staff_Landing_page';
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/student/') WHERE `key`='Gateway_Student_Landing_page';
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/dashboard/') WHERE `key`='Staff_ReferralPage';
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/academic-updates/update/') WHERE `key`='Academic_Update_Reminder_to_Faculty';
        
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/academic-updates/update/') WHERE `key`='Academic_Update_View_URL';
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/overview/') WHERE `key`='Gateway_Coordinator_Landing_page';
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/login/') WHERE `key`='Email_Login_Landing_Page';
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/resetPassword/') WHERE `key`='Student_ResetPwd_URL_Prefix';
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid) WHERE `key`='MultiCampus_Change_Request';
          ");
        
        $this->addSql("
              SET @emtid := (SELECT id FROM synapse.ebi_config where `key` =\"Student_Support_Helpdesk_Email_Address\");
              UPDATE `synapse`.`ebi_config` SET `value`='support@mapworks.com' WHERE `id`=@emtid;
          ");
        
        $this->addSql("
              SET @emtid := (SELECT id FROM synapse.ebi_config where `key` =\"Staff_Support_Helpdesk_Email_Address\");
              UPDATE `synapse`.`ebi_config` SET `value`='support@mapworks.com' WHERE `id`=@emtid;
          ");
        
        $this->addSql("
              SET @emtid := (SELECT id FROM synapse.ebi_config where `key` =\"Coordinator_Support_Helpdesk_Email_Address\");
              UPDATE `synapse`.`ebi_config` SET `value`='support@mapworks.com' WHERE `id`=@emtid;
          ");
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
