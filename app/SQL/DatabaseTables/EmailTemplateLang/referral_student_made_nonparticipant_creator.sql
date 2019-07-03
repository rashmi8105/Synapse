INSERT INTO `synapse`.`email_template` (`email_key`, `from_email_address`, `is_active`) VALUES ('referral_student_made_nonparticipant_creator', 'no-reply@mapworks.com', '1');
SET @emailtemplateId = (SELECT id from email_template where email_key = 'referral_student_made_nonparticipant_creator');
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
				<tr style="background:#fff;border-collapse:collapse;"><td>The referral you created for $$student_first_name$$ $$student_last_name$$ on $$date_of_creation$$ is</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>no longer visible as they are no longer participating in Mapworks.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>If you have any questions, please contact $coordinator_first_name $$coordinator_last_name$$ at</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>$$coordinator_email_address$$</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Best regards,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Skyfactor Mapworks Team</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
			</tbody>
		</table>
	</body>
</html>' ,
language_id = 1,
email_template_id = @emailtemplateId,
subject = 'New Mapworks Referral';