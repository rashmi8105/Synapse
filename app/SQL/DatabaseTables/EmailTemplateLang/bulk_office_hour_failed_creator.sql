INSERT INTO `synapse`.`email_template` (`email_key`, `from_email_address`, `is_active`) VALUES ('bulk_office_hour_failed_creator', 'no-reply@mapworks.com', '1');

SET @emailtemplateId = (SELECT id from email_template where email_key = 'bulk_office_hour_failed_creator');

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
				<tr style="background:#fff;border-collapse:collapse;"><td>An error occurred while Mapworks was syncing changes to your calendar. Please contact Skyfactor client services. Event id $eventId</td></tr>
			</tbody>
		</table>
	</body>
</html>' ,
language_id = 1,
email_template_id = @emailtemplateId,
subject = 'Mapworks calendar sync error'