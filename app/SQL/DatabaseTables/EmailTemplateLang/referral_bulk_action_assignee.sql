INSERT INTO `synapse`.`email_template` (`email_key`, `from_email_address`) VALUES ('referral_bulk_action_assignee', 'no-reply@mapworks.com');
SET @emailtemplateId = (SELECT id from email_template where email_key = 'referral_bulk_action_assignee');

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
                <tr style="background:#fff;border-collapse:collapse;"><td>Hi $$current_assignee_first_name$$,</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>You have been has assigned new referrals for $$referral_student_count$$ students in Mapworks. Please sign in to your account to view and take action on these referrals.</td></tr>
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