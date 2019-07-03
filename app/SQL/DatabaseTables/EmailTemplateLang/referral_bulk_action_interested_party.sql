INSERT INTO `synapse`.`email_template` (`email_key`, `from_email_address`) VALUES ('referral_bulk_action_interested_party', 'no-reply@mapworks.com');
SET @emailtemplateId = (SELECT id from email_template where email_key = 'referral_bulk_action_interested_party');

INSERT INTO `email_template_lang`
SET
    `body` = '
<html>
    <head>
        <style>
            body {
                background: none repeat scroll 0 0 #f4f4f4;
            }
            table {
                padding: 21px;
                width: 799px;
                font-family: helvetica,arial,verdana,san-serif;
                font-size:13px;
                color:#333;
            }
        </style>
    </head>
    <body>
        <table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
            <tbody>
                <tr style="background:#fff;border-collapse:collapse;"><td>Hi $$interested_party_first_name$$,</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>You have been added as an interested party for $$referral_student_count$$ referrals on $$date_of_creation$$.</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>If you have any questions, please contact $$coordinator_first_name$$ $$coordinator_last_name$$ at $$coordinator_email_address$$.</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>Best Regards,</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>Skyfactor Mapworks Team.</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
            </tbody>
        </table>
    </body>
</html>' ,
language_id = 1,
email_template_id = @emailtemplateId,
subject = 'Mapworks Referral Created'
