SET @emailtemplateId = (SELECT id from email_template where email_key = "referral_create_creator");

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
                <tr style="background:#fff;border-collapse:collapse;"><td>Hi $$creator_first_name$$,</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>Your referral for $$student_first_name$$ $$student_last_name$$ has been sent to $$current_assignee_first_name$$ $$current_assignee_last_name$$ in Mapworks.</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>Best Regards,</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>Skyfactor Mapworks Team.</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
            </tbody>
        </table>
    </body>
</html>' ,
language_id = 1,
email_template_id = @emailtemplateId

