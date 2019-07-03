SET @emailtemplateId = (SELECT id from email_template where email_key = "Referral_InterestedParties_Staff");

UPDATE `email_template_lang`
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
                <tr style="background:#fff;border-collapse:collapse;"><td>You have been added as an interested party on a referral for $$student_first_name$$ $$student_last_name$$ on $$date_of_creation$$. </td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>If you have any questions, please contact $$coordinator_first_name$$ $$coordinator_last_name$$ at $$coordinator_email_address$$.</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>Best Regards,</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>Skyfactor Mapworks Team.</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td><img src="$$Skyfactor_Mapworks_logo$$" title="Skyfactor Mapworks logo" alt="Skyfactor Mapworks logo"/></td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
            </tbody>
        </table>
    </body>
</html>'
WHERE
    `email_template_id` = @emailtemplateId;
