
/*
-- ESPRJ - 1573 - all faculty recipients who still had updates to submit are notified via email that the request has been closed.

-- Date: 2015-04-02 16:05 By Saravanan
*/
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Academic_Update_Cancel_to_Faculty',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,saravanan.rajagopal@techmahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\r <head>\r <style>body{background: none repeat scroll 0 0 #f4f4f4;}\r table{\r padding: 21px; width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;\r color:#333;}</style>\r </head>\r <body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r <tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$faculty_name$$:</td></tr>\r <tr style=\"background:#fff;border-collapse:collapse;\"><td>This academic update request has been cancelled and removed from your queue:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>$$request_name$$ ($$due_date$$)</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>$$request_description$$</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Requestor: $$requestor_name$$ ($$requestor_email$$)</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Student Updates: $$student_update_count$$</td></tr>\r <tr style=\"background:#fff;border-collapse:collapse;\"><td>$$custom_message$$</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table>\r </body>\r</html>','Reminder:');

/*
-- ESPRJ - 1573 - Sends an email reminder to all faculty who still have request responses pending.

-- Date: 2015-04-02 18:30 By Saravanan
*/
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Academic_Update_Reminder_to_Faculty',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,saravanan.rajagopal@techmahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head>\r<style>body{background: none repeat scroll 0 0 #f4f4f4;}\r table{\r \r \r padding: 21px; width: 799px;font-family: helvetica,arial,verdana,san-serif;font-\r \r size:13px;\r color:#333;}</style>\r</head><body><table cellpadding=\"10\" \r \r style=\"background:#eeeeee;\" cellspacing=\"0\">\r <tbody><tr \r \r style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$faculty_name$$:</td></tr>\r <tr style=\"background:#fff;border-collapse:collapse;\"><td>Please submit your academic updates for this request:</td></tr><tr \r \r style=\"background:#fff;border-collapse:collapse;\"><td><a class=\"external-link\" \r \r href=\"$$faculty_au_submission_page$$\" target=\"_blank\" style=\"color: rgb(41, 114, \r \r 155);text-decoration: underline;\">View and complete this academic update request \r \r on Mapworks</a></td></tr><tr style=\"background:#fff;border-\r \r collapse:collapse;\"><td>$$request_name$$ ($$due_date$$)</td></tr><tr \r \r style=\"background:#fff;border-collapse:collapse;\"><td>$$request_description$$</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Requestor: $$requestor_name$$ ($$requestor_email$$)</td></tr><tr \r \r style=\"background:#fff;border-collapse:collapse;\"><td>Student Updates: $$student_update_count$$</td></tr>\r <tr style=\"background:#fff;border-collapse:collapse;\"><td><a class=\"external-\r \r link\" href=\"$$faculty_au_submission_page$$\" target=\"_blank\" style=\"color: rgb(41, \r \r 114, 155);text-decoration: underline;\">Update</a></td></tr><tr \r \r style=\"background:#fff;border-collapse:collapse;\"><td>$$custom_message$$</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Best \r \r regards,<br/>EBI Mapworks</td></tr><tr style=\"background:#fff;border-\r \r collapse:collapse;\"><td>This email confirmation is an auto-generated message. \r \r Replies to automated messages are not monitored.</td></tr></tbody></table>\r</body></html>','Reminder:');

INSERT INTO `ebi_config` (`key`, `value`) VALUES ('Academic_Update_Reminder_to_Faculty', 'http://synapse-dev.mnv-tech.com/#/');


/*
-- Subash

-- Date: 2015-04-07 10:28
*/
INSERT INTO `ebi_config` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Academic_Update_View_URL','http://synapse-dev.mnv-tech.com/');

/*
-- Delli Babu

-- ESPRJ-1827 View Mapworks Contact Information

-- Date: 2015-04-07 10:28
*/
INSERT INTO `ebi_config` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Support_Helpdesk_Phone_Number','(888) MAP-WORKS (888-862-7967)'),(NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Training_Site_URL','http://mapworks-training.skyfactor.com');

/*
-- Query: select * from email_template where id = 18
LIMIT 0, 1000
Subash
-- Date: 2015-04-07 10:32
*/
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Academic_Update_Request_Staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

/*
-- Query: select * from email_template_lang where id = 19
LIMIT 0, 1000
Subash

-- Date: 2015-04-07 10:33
*/
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<!DOCTYPE html>\r\n<html>\r\n\r\n<body>\r\n<h4>View and complete this academic update request on Map-Works > </h4>\r\n<b>$$requestname$$ (due $$duedate$$)</b><br/>\r\n<p>$$description$$</p>\r\n<p>Requestor : $$requestor$$</p>\r\n<p>Student Updates : $$studentupdate$$</p>\r\n</body>\r\n\r\n</html>','Custom Subject');


/*-- Query: select * from email_template where id = 18LIMIT 0, 1000Subash-- Date: 2015-04-07 10:32*/

INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Academic_Update_Notification_Student',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

/*-- Query: 
Subash-- Date: 2015-04-07 10:33*/

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<!DOCTYPE html>\r\n<html>\r\n\r\n<body>\r\n<p> Hi $$studentname$$ </p>
<p>An Academic Update was created for you. View it now</body></html>','Academic Update Notification');



/*-- Query: 
Subash-- Date: 2015-04-10 10:33
 Academic Update Upload Notification
*/

INSERT INTO `email_template` ( `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `email_key`, `is_active`, `from_email_address`, `bcc_recipient_list`) 
VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'AcademicUpdate_Upload_Notification',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,harisudhakar.govindaraju@techmahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`email_template_id`, `language_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `body`, `subject`) 
VALUES (@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>
 <head>
 <style>body {
      background: none repeat scroll 0 0 #f4f4f4;
  	
  }table {
      padding: 21px;
      width: 799px;
  	

font-family: helvetica,arial,verdana,san-serif;
  	font-size:13px;
  	color:#333;
 }
 	</style>
 </head><body><table 

cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $

$user_first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your course upload has finished importing. Click 

<a class="external-link" href="$$download_url$$" target="_blank" style="color: rgb(41, 114, 155);text-decoration: underline;">here 

</a>to download error file .</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI 

Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. 

Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>','Mapworks course upload has finished');


/*
-- ESPRJ - 1573 - Update button link as per UI URL. Sends an email reminder to all faculty who still have request responses pending.
-- Date: 2015-04-22 13:40 By Saravanan
*/
UPDATE `ebi_config` SET `value`='http://synapse-dev.mnv-tech.com/#/academic-updates/update/' WHERE `key`='Academic_Update_Reminder_to_Faculty';

/*
-- Data for Survey Cohort Names
*/
SET @meta_sequence := (select MAX(sequence) FROM ebi_metadata);
/*
-- Query: SELECT * FROM synapse.ebi_metadata where meta_key = 'Cohort Names'
-- Date: 2015-04-22 14:58
*/
INSERT INTO `ebi_metadata` (`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`meta_key`,`definition_type`,`metadata_type`,`no_of_decimals`,`is_required`,`min_range`,`max_range`,`entity`,`sequence`,`meta_group`,`scope`) VALUES (NULL,NULL,NULL,'2015-04-22 09:21:49','2015-04-22 09:21:49',NULL,'Cohort Names','E','S',NULL,NULL,NULL,NULL,NULL,(@meta_sequence+1),NULL,NULL);


SET @ebi_metadata_id := (select id FROM ebi_metadata where meta_key = 'Cohort Names' and metadata_type = 'S');


/*
-- Query: SELECT * FROM synapse.ebi_metadata_list_values where ebi_metadata_id = 151
-- Date: 2015-04-22 14:59
*/
INSERT INTO `ebi_metadata_list_values` (`created_by`,`modified_by`,`deleted_by`,`lang_id`,`ebi_metadata_id`,`created_at`,`modified_at`,`deleted_at`,`list_name`,`list_value`,`sequence`) VALUES (NULL,NULL,NULL,1,@ebi_metadata_id,'2015-04-22 09:21:49','2015-04-22 09:21:49',NULL,'1','Survey Cohort 1',0);
INSERT INTO `ebi_metadata_list_values` (`created_by`,`modified_by`,`deleted_by`,`lang_id`,`ebi_metadata_id`,`created_at`,`modified_at`,`deleted_at`,`list_name`,`list_value`,`sequence`) VALUES (NULL,NULL,NULL,1,@ebi_metadata_id,'2015-04-22 09:21:49','2015-04-22 09:21:49',NULL,'2','Survey Cohort 2',0);
INSERT INTO `ebi_metadata_list_values` (`created_by`,`modified_by`,`deleted_by`,`lang_id`,`ebi_metadata_id`,`created_at`,`modified_at`,`deleted_at`,`list_name`,`list_value`,`sequence`) VALUES (NULL,NULL,NULL,1,@ebi_metadata_id,'2015-04-22 09:21:49','2015-04-22 09:21:49',NULL,'3','Survey Cohort 3',0);
INSERT INTO `ebi_metadata_list_values` (`created_by`,`modified_by`,`deleted_by`,`lang_id`,`ebi_metadata_id`,`created_at`,`modified_at`,`deleted_at`,`list_name`,`list_value`,`sequence`) VALUES (NULL,NULL,NULL,1,@ebi_metadata_id,'2015-04-22 09:21:49','2015-04-22 09:21:49',NULL,'4','Survey Cohort 4',0);

/*Data for ebi_config*/

INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'cohort_ids',@ebi_metadata_id);
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'WESS_SERVER_URL ','http://wess-ws-1.internal.mnv-tech.com/');


/* 22 APR 2015 - Academic Update Upload email template - start*/

SET @templateid := (SELECT id FROM email_template where email_key ='AcademicUpdate_Upload_Notification');

UPDATE `synapse`.`email_template_lang` SET `body`='<html>\n <head>\n <style>body {\n      background: none repeat scroll 0 0 #f4f4f4;\n  	\n  }table {\n      padding: 21px;\n      width: 799px;\n  	\n\nfont-family: helvetica,arial,verdana,san-serif;\n  	font-size:13px;\n  	color:#333;\n }\n 	</style>\n </head><body><table \n\ncellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$user_first_name$$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your Academic Update upload has finished importing. Click \n\n<a class=\"external-link\" href=\"$$download_failed_log_file$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155);text-decoration: underline;\">here \n\n</a>to download error file .</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI \n\nMapworks</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. \n\nReplies to automated messages are not monitored.</td></tr></tbody></table></body></html>', `subject`='Mapworks Academic Update upload has finished' WHERE `email_template_id`= @templateid;
/* 22 APR 2015 - Academic Update Upload  email template - END */



/* 24 APR 2015 - Academic Update Upload email template - start*/

SET @templateid := (SELECT id FROM email_template where email_key ='AcademicUpdate_Upload_Notification');
UPDATE `synapse`.`email_template_lang` SET `body`='<html>\n <head>\n <style>body {\n      background: none repeat scroll 0 0 #f4f4f4;\n  	\n  }table {\n      padding: 21px;\n      width: 799px;\n  	\n\nfont-family: helvetica,arial,verdana,san-serif;\n  	font-size:13px;\n  	color:#333;\n }\n 	</style>\n </head><body><table \n\ncellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$user_first_name$$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your Academic Update upload has finished importing. $$download_failed_log_file$$</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI \n\nMapworks</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. \n\nReplies to automated messages are not monitored.</td></tr></tbody></table></body></html>', `subject`='Mapworks Academic Update upload has finished' WHERE `email_template_id`= @templateid;

/* 24 APR 2015 - Academic Update Upload  email template - END */




/* 27 APR 2015 - Academic Update Create email template - start*/

SET @templateid := (SELECT id FROM email_template where email_key ='Academic_Update_Request_Staff');
UPDATE `synapse`.`email_template_lang` SET `body`='<!DOCTYPE html>
<html>
                <head>
                                <title></title>
                </head>
<body>
<table class="table table-bordered" style="border: 1px solid #428BCA !important; border-collapse:collapse; width:40%; margin-top:20px; margin-bottom:20px;">
	<tr>
		<td style=" padding:5px; background-color: #4F9BD9; border: 1px solid #428BCA !important; border-collapse:collapse"><p style="font-weight: bold; font-size: 14px; color:#fff;">View and complete this academic update request on MAP-Works &gt;</p></td>
	</tr>
	<tr>
		<td style="background-color: #D4EEFF; border: 1px solid #4F9BD9 !important; border-collapse:collapse; padding:15px 5px 15px 5px; vertical-align: middle;">
			<p style="font-size:14px; font-weight: bold;   margin: 0px !important;">$$requestname$$ (due <span>$$duedate$$)</span>)</p>
			<p style="font-size:14px;   margin: 0px !important;">$$description$$</p>
			<p style="font-size:14px;   margin: 0px !important;">Requestor: <span>$$requestor$$.</span></p>
			<p style="display:inline-block; float:left; font-size:14px;   margin: 0px !important;">Student Updates: <span>$$studentupdate$$</span></p>
			<input type="button" value="update" class="btn btn-default" style="width: 70px;   padding: 2px; display:inline-block; float: right; border: 1px solid #ccc; margin: 2px;"/>
		</td>	
	</tr>
</table>
<p style="width:40%; margin-bottom:20px;">$$optional_message$$</p>

	
</body>
</html>' WHERE `email_template_id`= @templateid;

/* 27 APR 2015 - Academic Update Create  email template - END */


/* 06 MAY 2015 - Academic Update Create email template - start*/

SET @templateid := (SELECT id FROM email_template where email_key ='Academic_Update_Request_Staff');
UPDATE `synapse`.`email_template_lang` SET `body`='<!DOCTYPE html>
<html>
                <head>
                                <title></title>
                </head>
<body>
<table class="table table-bordered" style="border: 1px solid #428BCA !important; border-collapse:collapse; width:40%; margin-top:20px; margin-bottom:20px;">
	<tr>
		<td style=" padding:5px; background-color: #4F9BD9; border: 1px solid #428BCA !important; border-collapse:collapse"><p style="font-weight: bold; font-size: 14px; color:#fff;">View and complete this academic update request on Mapworks &gt;</p></td>
	</tr>
	<tr>
		<td style="background-color: #D4EEFF; border: 1px solid #4F9BD9 !important; border-collapse:collapse; padding:15px 5px 15px 5px; vertical-align: middle;">
			<p style="font-size:14px; font-weight: bold;   margin: 0px !important;">$$requestname$$ (due <span>$$duedate$$)</span>)</p>
			<p style="font-size:14px;   margin: 0px !important;">$$description$$</p>
			<p style="font-size:14px;   margin: 0px !important;">Requestor: <span>$$requestor$$.</span></p>
			<p style="display:inline-block; float:left; font-size:14px;   margin: 0px !important;">Student Updates: <span>$$studentupdate$$</span></p>
			<a style="width: 65px; height:20px; background-color:#ccc; text-align: center; display:inline-block; float: right; border: 1px solid #ccc; margin: 2px; text-decoration: none; color:#000;" href="$$updateviewurl$$">update</a>
		</td>	
	</tr>
</table>
<p style="width:40%; margin-bottom:20px;">$$optional_message$$</p>

	
</body>
</html>' WHERE `email_template_id`= @templateid;

/* 06 MAY 2015 - Academic Update Create  email template - END */

UPDATE `synapse`.`ebi_config` SET `value`='http://synapse-qa.mnv-tech.com/#/academic-updates/update/' WHERE `key`='Academic_Update_View_URL';


/* 08MAY 2015 - Academic Update Create email template - start*/

SET @templateid := (SELECT id FROM email_template where email_key ='Academic_Update_Request_Staff');
UPDATE `synapse`.`email_template_lang` SET `body`='<!DOCTYPE html>
<html>
                <head>
                                <title></title>
                </head>
<body>
<table class="table table-bordered" style="border: 1px solid #428BCA !important; border-collapse:collapse; width:40%; margin-top:20px; margin-bottom:20px;">
	<tr>
		<td style=" padding:5px; background-color: #4F9BD9; border: 1px solid #428BCA !important; border-collapse:collapse"><p><a href="$$updateviewurl$$" style="font-weight: bold; font-size: 14px; color:#fff; text-decoration: none;">View and complete this academic update request on Mapworks &gt;</a></p></td>

	</tr>
	<tr>
		<td style="background-color: #D4EEFF; border: 1px solid #4F9BD9 !important; border-collapse:collapse; padding:15px 5px 15px 5px; vertical-align: middle;">
			<p style="font-size:14px; font-weight: bold;   margin: 0px !important;">$$requestname$$ (due <span>$$duedate$$)</span>)</p>
			<p style="font-size:14px;   margin: 0px !important;">$$description$$</p>
			<p style="font-size:14px;   margin: 0px !important;">Requestor: <span>$$requestor$$.</span></p>
			<p style="display:inline-block; float:left; font-size:14px;   margin: 0px !important;">Student Updates: <span>$$studentupdate$$</span></p>
			<a style="width: 65px; height:20px; background-color:#ccc; text-align: center; display:inline-block; float: right; border: 1px solid #ccc; margin: 2px; text-decoration: none; color:#000;" href="$$updateviewurl$$">update</a>
		</td>	
	</tr>
</table>
<p style="width:40%; margin-bottom:20px;">$$optional_message$$</p>

	
</body>
</html>' WHERE `email_template_id`= @templateid;

/* 08 MAY 2015 - Academic Update Create  email template - END */



