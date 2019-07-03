<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-13750
 * Migration script for creating mapworks_action,mapworks_action_variable_description,mapworks_action_variable tables and data population for
 * mapworks_action,referral_history,alert_notification_referral and referrals_interested_parties  tables
 */
class Version20170502100615 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //mapworks_action
        $this->addSql('CREATE TABLE mapworks_action (
                                id INT AUTO_INCREMENT NOT NULL,
                                event_key VARCHAR(100) DEFAULT NULL,
                                action VARCHAR(100) DEFAULT NULL,
                                recipient_type VARCHAR(100) DEFAULT NULL,
                                event_type VARCHAR(100) DEFAULT NULL,
                                email_template_id INT DEFAULT NULL,
                                notification_body_text VARCHAR(300) DEFAULT NULL,
                                notification_hover_text VARCHAR(300) DEFAULT NULL,
                                receives_email TINYINT(1) NOT NULL,
                                receives_notification TINYINT(1) NOT NULL,
                                created_by INT DEFAULT NULL,
                                modified_by INT DEFAULT NULL,
                                deleted_by INT DEFAULT NULL,
                                created_at DATETIME DEFAULT NULL,
                                modified_at DATETIME DEFAULT NULL,
                                deleted_at DATETIME DEFAULT NULL,
                                INDEX IDX_7CFD4496DE12AB56 (created_by),
                                INDEX IDX_7CFD449625F94802 (modified_by),
                                INDEX IDX_7CFD44961F6FA0AF (deleted_by),
                                INDEX IDX_7CFD4496131A730F (email_template_id),
                                UNIQUE INDEX Unique_action_recipient_type_event_type (action , recipient_type , event_type),
                                PRIMARY KEY (id)
                              )  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addSql('ALTER TABLE mapworks_action ADD CONSTRAINT FK_7CFD4496DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE mapworks_action ADD CONSTRAINT FK_7CFD449625F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE mapworks_action ADD CONSTRAINT FK_7CFD44961F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE mapworks_action ADD CONSTRAINT FK_7CFD4496131A730F FOREIGN KEY (email_template_id) REFERENCES email_template (id)');


        //mapworks_action_variable_description
        $this->addSql('CREATE TABLE mapworks_action_variable_description (
                                id INT AUTO_INCREMENT NOT NULL,
                                variable VARCHAR(100) DEFAULT NULL,
                                description VARCHAR(300) DEFAULT NULL,
                                created_by INT DEFAULT NULL,
                                modified_by INT DEFAULT NULL,
                                deleted_by INT DEFAULT NULL,
                                created_at DATETIME DEFAULT NULL,
                                modified_at DATETIME DEFAULT NULL,
                                deleted_at DATETIME DEFAULT NULL,
                                INDEX IDX_EF878B6EDE12AB56 (created_by),
                                INDEX IDX_EF878B6E25F94802 (modified_by),
                                INDEX IDX_EF878B6E1F6FA0AF (deleted_by),
                                PRIMARY KEY (id)
                              )  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addSql('ALTER TABLE mapworks_action_variable_description ADD CONSTRAINT FK_EF878B6EDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE mapworks_action_variable_description ADD CONSTRAINT FK_EF878B6E25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE mapworks_action_variable_description ADD CONSTRAINT FK_EF878B6E1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');

        //mapworks_action_variable
        $this->addSql('CREATE TABLE mapworks_action_variable (
                                id INT AUTO_INCREMENT NOT NULL,
                                mapworks_action_id INT DEFAULT NULL,
                                mapworks_action_variable_description_id INT DEFAULT NULL,
                                created_by INT DEFAULT NULL,
                                modified_by INT DEFAULT NULL,
                                deleted_by INT DEFAULT NULL,
                                created_at DATETIME DEFAULT NULL,
                                modified_at DATETIME DEFAULT NULL,
                                deleted_at DATETIME DEFAULT NULL,
                                INDEX IDX_8F9CA3EDDE12AB56 (created_by),
                                INDEX IDX_8F9CA3ED25F94802 (modified_by),
                                INDEX IDX_8F9CA3ED1F6FA0AF (deleted_by),
                                INDEX IDX_8F9CA3EDC8C605AE (mapworks_action_id),
                                INDEX IDX_8F9CA3ED8FA374F6 (mapworks_action_variable_description_id),
                                PRIMARY KEY (id)
                              )  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addSql('ALTER TABLE mapworks_action_variable ADD CONSTRAINT FK_8F9CA3EDDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE mapworks_action_variable ADD CONSTRAINT FK_8F9CA3ED25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE mapworks_action_variable ADD CONSTRAINT FK_8F9CA3ED1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE mapworks_action_variable ADD CONSTRAINT FK_8F9CA3EDC8C605AE FOREIGN KEY (mapworks_action_id) REFERENCES mapworks_action (id)');
        $this->addSql('ALTER TABLE mapworks_action_variable ADD CONSTRAINT FK_8F9CA3ED8FA374F6 FOREIGN KEY (mapworks_action_variable_description_id) REFERENCES mapworks_action_variable_description (id)');

        //data for mapworks action
        $this->addSql("INSERT INTO `mapworks_action` (`event_key`, `action`, `recipient_type`, `event_type`, `receives_email`, `receives_notification`,`created_by`,`created_at`) VALUES
            ('referral_create_current_assignee','create','current_assignee','referral',1,1,-25,NOW()),
            ('referral_create_interested_party','create','interested_party','referral'	,1,	1,-25,NOW()),
            ('referral_create_creator','create','creator','referral',1,1,-25,NOW()),
            ('referral_create_student','create','student','referral',1,0,-25,NOW()),
            ('referral_update_content_current_assignee','update_content','current_assignee','referral',0,1,-25,NOW()),
            ('referral_update_content_creator','update_content','creator','referral',0,	1,-25,NOW()),
            ('referral_update_content_updater','update_content','updater','referral'	,0,	1,-25,NOW()),
            ('referral_update_content_interested_party','update_content','interested_party',	'referral',0,1,-25,NOW()),
            ('referral_reassign_current_assignee','reassign','current_assignee','referral',1,1,-25,NOW()),
            ('referral_reassign_previous_assignee','reassign','previous_assignee','referral'	,0,	1,-25,NOW()),
            ('referral_reassign_creator','reassign',	'creator','referral',0,	1,-25,NOW()),
            ('referral_reassign_updater','reassign','updater','referral'	,0,	1,-25,NOW()),
            ('referral_close_current_assignee','close','current_assignee','referral',1,	1,-25,NOW()),
            ('referral_close_interested_party','close','interested_party','referral',1,	1,-25,NOW()),
            ('referral_close_creator','close','creator','referral',1,1,-25,NOW()),
            ('referral_close_closer','close','closer','referral',0,1,-25,NOW()),
            ('referral_reopen_current_assignee','reopen','current_assignee',	'referral',1,1,-25,NOW()),
            ('referral_reopen_interested_party',	'reopen','interested_party','referral',0,1,-25,NOW()),
            ('referral_reopen_creator','reopen','creator','referral',0,1,-25,NOW()),
            ('referral_reopen_reopener','reopen','reopener','referral',0,1,-25,NOW()),
            ('referral_add_interested_party_interested_party','add_interested_party','interested_party',	'referral',1,0,-25,NOW()),
            ('referral_remove_interested_party_interested_party','remove_interested_party','interested_party','referral'	,0,	1,-25,NOW()),
            ('referral_student_made_nonparticipant_current_assignee','student_made_nonparticipant','current_assignee','referral',1,1,-25,NOW()),
            ('referral_student_made_nonparticipant_creator','student_made_nonparticipant','creator','referral',1,1,-25,NOW()),
            ('referral_student_made_nonparticipant_interested_party','student_made_nonparticipant','interested_party','referral',0,	1,-25,NOW()),
            ('referral_student_made_participant_current_assignee','student_made_participant','current_assignee',	'referral',1,1,-25,NOW()),
            ('referral_student_made_participant_creator','student_made_participant','creator','referral',0,	1,-25,NOW()),
            ('referral_student_made_participant_interested_party','student_made_participant','interested_party','referral',0,1,-25,NOW()),
            ('referral_bulk_action_current_assignee','bulk_action','current_assignee','referral',1,1,-25,NOW()),
            ('referral_bulk_action_interested_party','bulk_action','interested_party','referral',1,1,-25,NOW()),
            ('referral_bulk_action_creator','bulk_action','creator','referral',0,1,-25,NOW());");


        // updating the referral history table
        $this->addSql("INSERT INTO referral_history (
                        referral_id,
                        action,
                        created_by,
                        created_at,
                        person_id_assigned_to,
                        activity_category_id,
                        note,
                        status,
                        is_leaving,
                        is_discussed,
                        referrer_permission,
                        is_high_priority,
                        notify_student,
                        access_private,
                        access_public,
                        access_team,
                        is_reason_routed,
                        user_key)
                        SELECT id,
                           'create',
                           IF(created_by > 0, created_by, person_id_faculty) AS created_by,
                           created_at,
                           person_id_assigned_to,
                           activity_category_id,
                           note,
                           status,
                           is_leaving,
                           is_discussed,
                           referrer_permission,
                           is_high_priority,
                           notify_student,
                           access_private,
                           access_public,
                           access_team,
                           is_reason_routed,
                           user_key
                      FROM referrals;"
        );


        //updating alert notification referral table
        $this->addSql("INSERT INTO alert_notification_referral (
                                    alert_notification_id,
                                    referral_history_id,
                                    created_at,
                                    modified_at,
                                    deleted_at,
                                    created_by,
                                    modified_by,
                                    deleted_by)
                                            SELECT an.id AS alert_notifications_id,
                                                   rh.id AS referral_history_id,
                                                   an.created_at,
                                                   an.modified_at,
                                                   an.deleted_at,
                                                   an.created_by,
                                                   an.modified_by,
                                                   an.deleted_by
                                            FROM alert_notifications an
                                            INNER JOIN referral_history rh ON rh.referral_id = an.referrals_id ;"
        );


        //populating  referral intrested parties referral history table
        $this->addSql("UPDATE 
                            referrals_interested_parties rip
                            INNER JOIN referral_history rh 
                                ON rip.referrals_id = rh.referral_id
                        SET
                            rip.referral_history_id = rh.id;");

    }


    public function down(Schema $schema)
    {

    }
}
