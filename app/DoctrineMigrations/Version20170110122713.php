<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script for adding Skyfactor Mapworks Team and adding Skyfactor Mapworks logo in email footer
 */
class Version20170110122713 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $userFirstName = '$$user_first_name$$';
        $downloadFailedLogFile = '$$download_failed_log_file$$';
        $skyfactorMapworksLogo = '$$Skyfactor_Mapworks_logo$$';

        $groupStudentUploadNotificationHTML = <<<CDATA
            <html>
                <head>
                    <style>
                        body {
                            background: none repeat scroll 0 0 #f4f4f4;
                        }
                        table {
                            padding: 21px;
                            width: 799px;
                            font-family: helvetica, arial, verdana, san-serif;
                            font-size: 13px;
                            color: #333;
                        }
                    </style>
                </head>
                <body>
                    <table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
                        <tbody>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>Dear $userFirstName :</td>
                            </tr>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>Your student group upload has finished importing. $downloadFailedLogFile </td>
                            </tr>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>Thank you from the Skyfactor team.</td>
                            </tr>
                            <tr>
                                <td><img width="307" height="89" alt="Skyfactor Mapworks Logo" src="$skyfactorMapworksLogo"/></td>
                            </tr>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td>
                            </tr>
                        </tbody>
                    </table>
                </body>
            </html>
CDATA;

        $groupFacultyUploadNotificationHTML = <<<CDATA
            <html>
                <head>
                    <style>
                        body {
                            background: none repeat scroll 0 0 #f4f4f4;
                        }
                        table {
                            padding: 21px;
                            width: 799px;
                            font-family: helvetica, arial, verdana, san-serif;
                            font-size: 13px;
                            color: #333;
                        }
                    </style>
                </head>
                <body>
                    <table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
                        <tbody>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>Dear $userFirstName:</td>
                            </tr>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>Your faculty group upload has finished importing. $downloadFailedLogFile</td>
                            </tr>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>Thank you from the Skyfactor team.</td>
                            </tr>
                            <tr>
                                <td><img width="307" height="89" alt="Skyfactor Mapworks Logo" src="$skyfactorMapworksLogo"/></td>
                            </tr>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td>
                            </tr>
                        </tbody>
                    </table>
                </body>
            </html>
CDATA;

        $groupUploadNotificationHTML = <<<CDATA
            <html>
                <head>
                    <style>
                        body {
                            background: none repeat scroll 0 0 #f4f4f4;
                        }
                        table {
                            padding: 21px;
                            width: 799px;
                            font-family: helvetica, arial, verdana, san-serif;
                            font-size: 13px;
                            color: #333;
                        }
                    </style>
                </head>
                <body>
                    <table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
                        <tbody>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>Dear $userFirstName:</td>
                            </tr>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>Your group upload has finished importing. $downloadFailedLogFile</td>
                            </tr>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>Thank you from the Skyfactor team.</td>
                            </tr>
                            <tr>
                                <td><img width="307" height="89" alt="Skyfactor Mapworks Logo" src="$skyfactorMapworksLogo"/></td>
                            </tr>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td>
                            </tr>
                        </tbody>
                    </table>
                </body>
            </html>
CDATA;

        $staticListUploadNotificationHTML = <<<CDATA
            <html>
                <head>
                    <style>
                        body {
                            background: none repeat scroll 0 0 #f4f4f4;
                        }
                        table {
                            padding: 21px;
                            width: 799px;
                            font-family: helvetica, arial, verdana, san-serif;
                            font-size: 13px;
                            color: #333;
                        }
                    </style>
                </head>
                <body>
                    <table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
                        <tbody>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>Dear $userFirstName:</td>
                            </tr>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>Your static list upload has finished importing. $downloadFailedLogFile</td>
                            </tr>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>Thank you from the Skyfactor team.</td>
                            </tr>
                            <tr>
                                <td><img width="307" height="89" alt="Skyfactor Mapworks Logo" src="$skyfactorMapworksLogo"/></td>
                            </tr>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td>
                            </tr>
                        </tbody>
                    </table>
                </body>
            </html>
CDATA;

        $this->addSql("UPDATE `email_template_lang` SET `body` = ' $groupStudentUploadNotificationHTML '  WHERE `email_template_id` = (SELECT id FROM email_template where email_key='Group_Student_Upload_Notification');");
        $this->addSql("UPDATE `email_template_lang` SET `body` = ' $groupFacultyUploadNotificationHTML ' WHERE `email_template_id` = (SELECT id FROM email_template where email_key='Group_Faculty_Upload_Notification');");
        $this->addSql("UPDATE `email_template_lang` SET `body` = ' $groupUploadNotificationHTML ' WHERE `email_template_id` = (SELECT id FROM email_template where email_key='Group_Upload_Notification');");
        $this->addSql("UPDATE `email_template_lang` SET `body` = ' $staticListUploadNotificationHTML ' WHERE `email_template_id` = (SELECT id FROM email_template where email_key='StaticList_Upload_Notification');");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
