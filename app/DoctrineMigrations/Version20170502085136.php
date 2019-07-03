<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration Script to populate mapworks_action_variable_description
 * ESPRJ-14132
 */
class Version20170502085136 extends AbstractMigration
{
    /**
     * Migration Script that populates mapworks_action_variable_description
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO mapworks_action_variable_description(variable,description)VALUES

                          ("$$creator_first_name$$","First name of the original creator of a mapworks activity."),
                          ("$$creator_last_name$$","Last name of the original creator of a mapworks activity."),
                          ("$$creator_email_address$$","Email Address of the original creator of a mapworks activity."),
                          ("$$updater_first_name$$","First name of the updater of a mapworks activity."),
                          ("$$updater_last_name$$","Last name of the updater of a mapworks activity."),
                          ("$$updater_email_address$$","Email Address of the updater of a mapworks activity."),

                          ("$$interested_party_first_name$$","First name of an interested party of a referral."),
                          ("$$interested_party_last_name$$","Last name of an interested party of a referral."),
                          ("$$interested_party_email_address$$","Email Address of an interested party of a referral."),
                          
                          ("$$previous_assignee_first_name$$","First name of the previous assignee of a referral."),
                          ("$$previous_assignee_last_name$$","Last name of the previous assignee of a referral."),
                          ("$$previous_assignee_email_address$$","Email Address of the previous assignee of a referral."),
                          ("$$current_assignee_first_name$$","First name of the current assignee of a referral."),
                          ("$$current_assignee_last_name$$","Last name of the current assignee of a referral."),
                          ("$$current_assignee_email_address$$","Email Address of the current assignee of a referral."),
                          ("$$current_assignee_title$$","Title of the current assignee on a referral."),
                          ("$$previous_assignee_title$$","Title of the previous assignee of a referral."),
                          ("$$creator_title$$","Title of the creator of a mapworks activity."),
                          ("$$updater_title$$","Title of the updater of a mapworks activity."),

                          ("$$interested_party_title$$","Title of an interested party of a mapworks activity."),
                          ("$$date_of_creation$$","The date a mapworks activity was created."),
                          ("$$referral_student_count$$","Count of the total referrals created in a bulk action."),
                          ("$$Skyfactor_Mapworks_logo$$","Skyfactor Logo File Path."),

                          ("$$staff_referral_page$$","Link to the Faculty\'s dashboard, requires user to login."),
                          ("$$coordinator_first_name$$","First name of Primary Coordinator."),
                          ("$$coordinator_last_name$$","Last name of Primary Coordinator."),
                          ("$$coordinator_title$$","Title of Primary Coordinator."),
                          ("$$coordinator_email_address$$","Email Address of Primary Coordinator."),

                          ("$$student_dashboard$$","Link to student Dashboard, requires user to login."),
                          ("$$student_first_name$$","First name of student related to the mapworks activity."),
                          ("$$student_last_name$$","Last name of student related to the mapworks activity."),
                          ("$$student_email_address$$","Email Address of student related to the mapworks activity.")
                  ;');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
