<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160309053054 extends AbstractMigration
{
    /**
     * Migration script to add new Email Templates for close referral and Update the interested Parties email template.
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $referralClosedCreatorHTML = '\'<html>
                <head>
                    <style>
                        body {
                            background: none repeat scroll 0 0 #f4f4f4;
                            font-family: helvetica, arial, verdana, sans-serif;
                            font-size: 13px;
                            color: #333;
                        }

                        .outer-div {
                            padding: 21px;
                            width: 799px;
                            background-color: #EEEEEE;
                        }

                        .inner-div{
                            background-color: #FFFFFF;
                            padding: 10px;
                        }
                    </style>
                </head>
                <body>

                <div class="outer-div">
                    <div class="inner-div">
                        <p>$$first_name$$ $$last_name$$,</p>
                        <p>The referral you created for $$student_first_name$$ $$student_last_name$$ on $$date_of_creation$$ has been closed.</p>
                        <p>If you have any questions, please contact $$coordinator_first_name$$ $$coordinator_last_name$$, $$title$$
                            ($$email_address$$).</p>
                        <p>Best regards,</p>
                        <p>Skyfactor Mapworks Team</p>
                        <p><img width="307" height="89" alt="Skyfactor Mapworks Logo"
                                                            src="$$Skyfactor_Mapworks_logo$$"/></p>
                        <p>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.
                        </p>
                    </div>
                </div>
                </body>
                </html>
                \'';


        $this->addSql("INSERT INTO `email_template` (email_key, from_email_address, is_active, bcc_recipient_list) VALUES ('Referral_Closed_Creator','no-reply@mapworks.com',1,null)");
        $this->addSql("INSERT INTO email_template_lang (email_template_id, language_id, body, subject)
                        SELECT id,
                                1,
                                $referralClosedCreatorHTML,
                                'Mapworks Referral Closed'
                        FROM email_template
                        WHERE email_key='Referral_Closed_Creator';");

        $referralClosedAssigneeHTML = '\'<html>
                <head>
                    <style>
                        body {
                            background: none repeat scroll 0 0 #f4f4f4;
                            font-family: helvetica, arial, verdana, sans-serif;
                            font-size: 13px;
                            color: #333;
                        }

                        .outer-div {
                            padding: 21px;
                            width: 799px;
                            background-color: #EEEEEE;
                        }

                        .inner-div{
                            background-color: #FFFFFF;
                            padding: 10px;
                        }
                    </style>
                </head>
                <body>

                <div class="outer-div">
                    <div class="inner-div">
                        <p>$$first_name$$ $$last_name$$,</p>
                        <p>The referral you were assigned for $$student_first_name$$ $$student_last_name$$ on $$date_of_creation$$ has
                            been closed.</p>
                        <p>If you have any questions, please contact $$coordinator_first_name$$ $$coordinator_last_name$$, $$title$$
                            ($$email_address$$).</p>
                        <p>Best regards,</p>
                        <p>Skyfactor Mapworks Team</p>
                        <p><img width="307" height="89" alt="Skyfactor Mapworks Logo"
                                                            src="$$Skyfactor_Mapworks_logo$$"/></p>
                        <p>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.
                        </p>
                    </div>
                </div>
                </body>
                </html>\'';

        $this->addSql("INSERT INTO email_template (email_key, from_email_address, is_active, bcc_recipient_list) VALUES ('Referral_Closed_Assignee','no-reply@mapworks.com',1,null)");
        $this->addSql("INSERT INTO email_template_lang (email_template_id, language_id, body, subject)
                        SELECT id,
                                1,
                                $referralClosedAssigneeHTML,
                                'Mapworks Referral Closed'
                        FROM email_template
                        WHERE email_key='Referral_Closed_Assignee';");

        $referralClosedInterestedPartyHTML = '\'<html>
                <head>
                    <style>
                        body {
                            background: none repeat scroll 0 0 #f4f4f4;
                            font-family: helvetica, arial, verdana, sans-serif;
                            font-size: 13px;
                            color: #333;
                        }

                        .outer-div {
                            padding: 21px;
                            width: 799px;
                            background-color: #EEEEEE;
                        }

                        .inner-div{
                            background-color: #FFFFFF;
                            padding: 10px;
                        }
                    </style>
                </head>
                <body>

                <div class="outer-div">
                    <div class="inner-div">
                        <p>$$first_name$$ $$last_name$$,</p>
                        <p>A referral for $$student_name$$ on $$date_of_creation$$ that you were an interested party for has been closed.</p>
                        <p>If you have any questions, please contact $$coordinator_first_name$$ $$coordinator_last_name$$, $$title$$
                            ($$email_address$$).</p>
                        <p>Best regards,</p>
                        <p>Skyfactor Mapworks Team</p>
                        <p><img width="307" height="89" alt="Skyfactor Mapworks Logo"
                                                            src="$$Skyfactor_Mapworks_logo$$"/></p>
                        <p>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.
                        </p>
                    </div>
                </div>
                </body>
                </html>\'';

        $this->addSql("UPDATE `email_template_lang`
SET `body` = $referralClosedInterestedPartyHTML
WHERE `email_template_id` = (SELECT id FROM email_template where email_key='Referral_InterestedParties_Staff_Closed');");
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
