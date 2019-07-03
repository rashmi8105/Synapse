SET @emailtemplateId = (SELECT id FROM email_template WHERE email_key = "Referral_Student_Notification");

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
                <tr style="background:#fff;border-collapse:collapse;"><td>Hi $$student_first_name$$ ,</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>A faculty/staff member has referred you to a campus resource through the Mapworks system. To view the referral details, please log in to your Mapworks homepage and visit $$student_dashboard$$.</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>If you have any questions, please contact $$coordinator_first_name$$ $$coordinator_last_name$$ at $$coordinator_email_address$$.</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>Best Regards,</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>Skyfactor Mapworks Team.</td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/></td></tr>
                <tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
          </tbody>
		  </table>
	</body>
</html>'
WHERE
  `email_template_id` = @emailtemplateId;