/*
*/
 INSERT INTO `intent_to_leave` VALUES 
 	(1,NULL,NULL,NULL,NULL,NULL,NULL,'red','leave-intent-leave-stated.png','#c70009'),
 	(2,NULL,NULL,NULL,NULL,NULL,NULL,'yellow','leave-intent-leave-implied.png','#fec82a'),
 	(3,NULL,NULL,NULL,NULL,NULL,NULL,'green','leave-intent-stay-stated.png','#95cd3c'),
 	(4,NULL,NULL,NULL,NULL,NULL,NULL,'gray','leave-intent-not-stated.png','#cccccc');
	
/*
-- ESPRJ - 1627 - Student Notification of Referrals - As a creator of a referral, I want to choose whether or not the student in the referral is notified of 
the referral being created so that I can signal to the student that I think they need to take action.


-- Date: 2015-02-17 16:39 By Subash
*/
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Referral_Student_Notification',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');


SET @mmid := (SELECT MAX(id) FROM email_template);


INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\r\n	<head>\r\n		<style>\r\n		body {\r\n    background: none repeat scroll 0 0 #f4f4f4;\r\n	\r\n}\r\n		table {\r\n    padding: 21px;\r\n    width: 799px;\r\n	font-family: helvetica,arial,verdana,san-serif;\r\n	font-size:13px;\r\n	color:#333;\r\n	}\r\n		</style>\r\n	</head>\r\n	<body>\r\n	\r\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r\n			<tbody>\r\n			\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$firstname$$:</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A faculty/staff member has referred you to a campus resource through the Mapworks system. To view the referral details, please log in to your Mapworks homepage and visit $$dashboard$$.</td></tr>\r\n			<tr style=\"background:#fff;border-collapse:collapse;\"><td>If you have any questions, please contact $$coordinator_details$$</td></tr>\r\n		<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,</td></tr>\r\n	<tr style=\"background:#fff;border-collapse:collapse;\"><td>Skyfactor Mapworks Team</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r\n			\r\n			</tbody>\r\n		</table>\r\n	</body>\r\n</html>','Referral to a campus resource');

INSERT INTO `synapse`.`ebi_config`
(`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`)
VALUES
(NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Staff_ReferralPage','http://synapse-qa.mnv-tech.com/#/team-interactions');

INSERT INTO `email_template` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `email_key`, `is_active`, `from_email_address`, `bcc_recipient_list`)
VALUES
(NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Referral_InterestedParties_Staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`, `email_template_id`, `language_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `body`, `subject`)
VALUES
(NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\r\n	<head>\r\n		<style>\r\n		body {\r\n    background: none repeat scroll 0 0 #f4f4f4;\r\n	\r\n}\r\n		table {\r\n    padding: 21px;\r\n    width: 799px;\r\n	font-family: helvetica,arial,verdana,san-serif;\r\n	font-size:13px;\r\n	color:#333;\r\n	}\r\n		</style>\r\n	</head>\r\n	<body>\r\n	\r\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r\n			<tbody>\r\n			\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$staff_firstname$$:</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A faculty/staff member has referred $$firstname$$ to a campus resource through the Mapworks system and added you as an interested party. To view the referral details, please log in to Mapworks and visit <a class="external-link" href="$$staff_referralpage$$" target="_blank" style="color: rgb(41, 114, 155); text-decoration: underline;">MAP-Works student dashboard view appointment module</a>. If you have any questions, please contact ($$coordinator_name$$, $$coordinator_email$$, $$coordinator_title$$ ). </td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI MAP-Works</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r\n			\r\n			</tbody>\r\n		</table>\r\n	</body>\r\n</html>','Interested party on a Mapworks referral');

/*
* Include refresh_token as additional grant_type 18th Feb 2015
*/
UPDATE `synapse`.`Client` SET `allowed_grant_types`='a:2:{i:0;s:8:\"password\";i:1;s:13:\"refresh_token\";}' WHERE `id`='1';

/*
-- ESPRJ - 1292 - Institution-Specific Years - As a Campus Coordinator, I want to set up institution-specific academic year 
date ranges so that the MAP-Works implementation reflects our academic calendar.
-- Date: 2015-02-20 16:12 By Mukesh
*/
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('201415',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('201516',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('201617',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('201718',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('201819',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('201920',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('202021',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('202122',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('202223',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('202324',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('202425',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('202526',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('202627',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('202728',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('202829',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `year` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES ('202930',NULL,NULL,NULL,NULL,NULL,NULL);

/*
-- 
Creating Ebi Lang for datablock

-- Date: 2015-02-24 20:12
*/
INSERT INTO `ebi_config` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ebi_Lang','1');

/*
-- 
ESPRJ-1729

-- Date: 2015-03-01 16:00
*/
UPDATE `synapse`.`intent_to_leave` SET `text`='gray' WHERE `id`='4';


--
-- Dumping data for table `risk_levels` ESPRJ 150
-- Date: 2015-02-28 11:00
--
INSERT INTO `risk_levels` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'red2','risk-level-icon-r2.png','#c70009'),(2,NULL,NULL,NULL,NULL,NULL,NULL,'red','risk-level-icon-r1.png','#f72d35'),(3,NULL,NULL,NULL,NULL,NULL,NULL,'yellow','risk-level-icon-y.png','#fec82a'),(4,NULL,NULL,NULL,NULL,NULL,NULL,'green','risk-level-icon-g.png','#95cd3c');
--
-- Updating data for table `intent_to_leave` ESPRJ 150
-- Date: 2015-02-28 11:00
--

UPDATE `synapse`.`intent_to_leave` SET `image_name`='leave-intent-leave-stated.png', `color_hex`='#c70009' WHERE `id`='1';
UPDATE `synapse`.`intent_to_leave` SET `image_name`='leave-intent-leave-implied.png', `color_hex`='#fec82a' WHERE `id`='2';
UPDATE `synapse`.`intent_to_leave` SET `image_name`='leave-intent-stay-stated.png', `color_hex`='#95cd3c' WHERE `id`='3';

UPDATE `synapse`.`intent_to_leave` SET `image_name`='leave-intent-not-stated.png', `color_hex`='#cccccc' WHERE `id`='4';
--
-- Updating data for table `intent_to_leave` ESPRJ-1728
-- Date: 2015-03-04 12:00
--

UPDATE `synapse`.`intent_to_leave` SET `text`='green', `image_name`='leave-intent-stay-stated.png', `color_hex`='#95cd3c' WHERE `id`='2';
UPDATE `synapse`.`intent_to_leave` SET `text`='yellow', `image_name`='leave-intent-leave-implied.png', `color_hex`='#fec82a' WHERE `id`='3';

/*
	Update email_template_lang 
*/

update `email_template_lang` set email_template_id = 1, language_id = 1, body = '<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/><br/>\n\nA Mapworks password was successfully created for your account.If this was not you or you believe this is an error,\nplease contact Mapworks support at &nbsp;<a class=\"external-link\" href=\"mailto:$$Support_Helpdesk_Email_Address$$\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">$$Support_Helpdesk_Email_Address$$</a><br/><br/>\n\nWe\'re very happy to have you on board, and are here to support you!<br/><br/>\nThank you from the Mapworks team!\n\n</div>\n</html>', subject = 'Mapworks password created' where id = 1;

update `email_template_lang` set email_template_id = 2, language_id = 1, body = '<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/></br>\n\nPlease use the link below and follow the displayed instructions to create your new password. This link will expire after $$Reset_Password_Expiry_Hrs$$ hours.<br />\n<br/>\n$$activation_token$$<br/><br/>\n\nIf you believe that you received this email in error or if you have any questions,please contact Mapworks support at <span style=\"color: #99ccff;\">$$Support_Helpdesk_Email_Address$$</span>.<br/><br/>\nThank you from the Mapworks team!\n </div>\n</html>\n\n', subject = 'Mapworks - how to reset your password' where id = 2;

update `email_template_lang` set email_template_id = 3, language_id = 1, body = '<html>\n 	\n 		<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\n 			Hi $$firstname$$,<br/><br/>\n 		\n 			An update to your Mapworks account was successfully made. The following information was updated: <br/><br/> \n 		\n 			$$Updated_MyAccount_fields$$\n 		<br/>\n 		\n 			If this was not you or you believe this is an error, please contact Mapworks support at&nbsp;<a class=\"external-link\" href=\"mailto:support@map-works.com\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">support@map-works.com</a></p>\n 		\n 			<br>Thank you from the Mapworks team!</br>\n 	</div>\n </html>\n ', subject = 'Mapworks profile updated' where id = 3;

update `email_template_lang` set email_template_id = 4, language_id = 1, body = '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\n<head>\n    <title>Email</title>\n</head>\n<body>\n<center>\n    <table align=\"center\">\n        <tr>\n            <th style=\"padding:0px;margin:0px;\">\n               \n               <table  style=\"font-family:helvetica,arial,verdana,san-serif;font-weight:normal;width:800px; height=337px;text-align:center;padding:0px;\">\n               <tr bgcolor=\"#eeeeee\" style=\"width:800px;padding:0px;height:337px;\">\n               <td style=\"width:800px;padding:0px;height:337px;\">\n               <table style=\"text-align:center;width:100%\">\n               <tr>\n                    <td style=\"padding:0px;\">\n                    <table style=\"margin-top:56px;width:100%\">\n		<tr>\n		<td style=\"text-align:center;padding:0px;font-size:33px;height:80px;width:800px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;color:#000000\">\n					<br>Welcome to Mapworks.		\n		</td>\n		</tr>\n		</table>\n                    </td>\n               </tr>\n               <tr style=\"margin:0px;padding:0px;\">\n		<td style=\"text-align:center;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;color:#333333;font-size: 16px;height:16px;padding-top:8px;\">\n        			Use the link below to create your password and start using Mapworks.\n		\n		</td></tr>\n           <tr style=\"margin:0px;padding:0px;\"><td style=\"margin:0px;padding:0px;\">\n        \n<table align=\"center\"> \n  <tr style=\"margin:0px;padding:0px;\">\n    <th style=\"margin:0px;padding:0px;\">\n           <table cellpadding=\"36\" style=\"width:100%\">\n        <tr>\n		<td align=\"center\" style=\"text-align:center;color:#000000;font-weight:normal;font-size: 20px;\">		\n		          <table style=\"border-radius:2px;width:175px;font-size:20px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;text-align:center;display: block;margin: 0 auto;padding:0px 0px\">\n		<tr>\n        <td style=\"background-color:#4673a7; height:58px;border-radius:2px;line-height:21px;text-decoration:none ;vertical-align:middle;\">        \n        <a href=\"$$activation_token$$\" style=\"outline-offset:19px !important;background-color: #4673a7; color: #ffffff;display: block;text-decoration: none;width:175px \"target=\"_blank\"><span style=\"text-decoration: none !important;\">Sign In Now</span></a>\n        </td></tr>\n        <tr valign=\"top\" style=\"height:33px;\">\n        <td style=\"margin-left:auto; margin-right:auto;width:100%;font-size: 14px;height:14px;padding-bottom:7px;font-family:helvetica,arial,verdana,san-serif;font-weight:medium;color:#333333;link:#1e73d5;padding-top:8px;\">       \n				<span>Use this link to <a target=\"_blank\" style=\"link:#1e73d5;\" href=\"$$activation_token$$\">sign in.</a></span>  \n			\n        </td></tr>\n        \n        </table>\n		</td></tr>\n        \n        </table>\n        </th>\n    \n  </tr>\n \n</table>\n       </td></tr>\n</table>\n               </td>\n               </tr>\n               <tr valign=\"top\">\n<td >\n<table>\n<tr>\n<td valign=\"top\" align=\"center\">\n<div style=\"text-align:left;margin-left:30px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;\n			margin-right:18px;font-size: 13px;color: #333333;margin-top:30px;link:#1e73d5;font-weight:normal;\" >\n				Thank you for participating in the spring 2015 pilot. We look forward to hearing your feedback as\n				it will inform future releases of our new student retention and success solution.\n				\n				<br><br>\n				If you have any questions, please contact us here.<br>\n				<a href=\"mailto:$$Support_Helpdesk_Email_Address$$\" style=\"link:#1e73d5;\">$$Support_Helpdesk_Email_Address$$</a> \n				<br><br>\n				Sincerely,\n				<div style=\"text-align:left;font-weight:bold;font-size: 14px;color:#333333\" >\n					<b>The EBI Mapworks Client Services Team</b> \n					\n				</div>\n                </div>\n</td>\n</tr>\n</table>\n</td>\n</tr>\n               </table>\n               \n            </th>\n            \n        </tr>\n        \n    </table>\n    </center>\n</body>\n</html>\n', subject = 'Mapworks - SignIn Instructions' where id = 4;

update `email_template_lang` set email_template_id = 5, language_id = 1, body = '<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/></br>\n\nPlease use the link below and follow the displayed instructions to create your new password. This link will expire after $$Reset_Password_Expiry_Hrs$$ hours.<br />\n<br/>\n$$activation_token$$<br/><br/>\n\nIf you believe that you received this email in error or if you have any questions,please contact Mapworks support at <span style=\"color: #99ccff;\">$$Support_Helpdesk_Email_Address$$</span>.<br/><br/>\nThank you from the Mapworks team!\n </div>\n</html>\n\n', subject = 'Mapworks - how to reset your password' where id = 5;

update `email_template_lang` set email_template_id = 6, language_id = 1, body = '<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/><br/>\n\nA Mapworks password was successfully created for your account. If this was not you or you believe this is an error,\nplease contact Mapworks support at &nbsp;<a class=\"external-link\" href=\"mailto:support@map-works.com\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">support@map-works.com</a><br/><br/>\n\nWe\'re very happy to have you on board, and are here to support you!<br/><br/>\nThank you from the Mapworks team!\n\n</div>\n</html>', subject = 'Mapworks password created' where id = 6;

update `email_template_lang` set email_template_id = 7, language_id = 1, body = '<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/><br/>\n\nYour Mapworks password has been changed. If this was not you or you believe this is an error, please contact Mapworks support at &nbsp;<a class=\"external-link\" href=\"mailto:support@map-works.com\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: none;\">support@map-works.com</a>\n<br/><br/>\nThank you from the Mapworks team!\n\n</div>\n</html>', subject = 'Mapworks password reset' where id = 7;

update `email_template_lang` set email_template_id = 8, language_id = 1, body = '<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/><br/>\n\nYour Mapworks password has been changed. If this was not you or you believe this is an error, please contact Mapworks support at &nbsp;<a class=\"external-link\" href=\"mailto:support@map-works.com\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">support@map-works.com</a>\n<br/><br/>\nThank you from the Mapworks team!\n\n</div>\n</html>', subject = 'Mapworks password reset' where id = 8;

update `email_template_lang` set email_template_id = 9, language_id = 1, body = '<html>\n	<head>\n		<style>\n		body {\n    background: none repeat scroll 0 0 #f4f4f4;\n	\n}\n		table {\n    padding: 21px;\n    width: 799px;\n	font-family: helvetica,arial,verdana,san-serif;\n	font-size:13px;\n	color:#333;\n	}\n		</style>\n	</head>\n	<body>\n	\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\n			<tbody>\n			\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$student_name$$:</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>An appointment has been booked with $$staff_name$$  \n				on $$app_datetime$$. To view the appointment details,\n				please log in to your Mapworks dashboard and visit <a class=\"external-link\" href=\"$$student_dashboard$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155);text-decoration: underline;\">Mapworks student dashboard view appointment module</a>.</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\n			\n			</tbody>\n		</table>\n	</body>\n</html>', subject = 'Mapworks appointment booked' where id = 9;

update `email_template_lang` set email_template_id = 10, language_id = 1, body = '<html>\n	<head>\n		<style>\n		body {\n    background: none repeat scroll 0 0 #f4f4f4;\n	\n}\n		table {\n    padding: 21px;\n    width: 799px;\n	font-family: helvetica,arial,verdana,san-serif;\n	font-size:13px;\n	color:#333;\n	}\n		</style>\n	</head>\n	<body>\n	\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\n			<tbody>\n			\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$student_name$$:</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A booked appointment with $$staff_name$$\n				has been modified. The appointment is now scheduled for $$app_datetime$$. To view the modified appointment details,\n				please log in to your Mapworks dashboard and visit <a class=\"external-link\" href=\"$$student_dashboard$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">Mapworks student dashboard view appointment module</a>.</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\n			\n			</tbody>\n		</table>\n	</body>\n</html>', subject = 'Mapworks appointment modified' where id = 10;

update `email_template_lang` set email_template_id = 11, language_id = 1, body = '<html>\n	<head>\n		<style>\n		body {\n    background: none repeat scroll 0 0 #f4f4f4;\n	\n}\n		table {\n    padding: 21px;\n    width: 799px;\n	font-family: helvetica,arial,verdana,san-serif;\n	font-size:13px;\n	color:#333;\n	}\n		</style>\n	</head>\n	<body>\n	\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\n			<tbody>\n			\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$student_name$$:</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Your booked appointment with \n				$$staff_name$$ on $$app_datetime$$ has been cancelled.\n				To book a new appointment, please log in to your Mapworks dashboard and visit <a class=\"external-link\" href=\"$$student_dashboard$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">Mapworks student dashboard view appointment module</a>.</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\n			\n			</tbody>\n		</table>\n	</body>\n</html>', subject = 'Mapworks appointment cancelled' where id = 11;

update `email_template_lang` set email_template_id = 12, language_id = 1, body = '<html>\n	<head>\n		<style>\n		body {\n    background: none repeat scroll 0 0 #f4f4f4;\n	\n}\n		table {\n    padding: 21px;\n    width: 799px;\n	font-family: helvetica,arial,verdana,san-serif;\n	font-size:13px;\n	color:#333;\n	}\n		</style>\n	</head>\n	<body>\n	\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\n			<tbody>\n			\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$fullname$$:</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>You have been added as a delegate user for $$delegater_name$$\'s calendar.</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\n			\n			</tbody>\n		</table>\n	</body>\n</html>', subject = 'Mapworks Delegate Added' where id = 12;

update `email_template_lang` set email_template_id = 13, language_id = 1, body = '<html>\n	<head>\n		<style>\n		body {\n    background: none repeat scroll 0 0 #f4f4f4;\n	\n}\n		table {\n    padding: 21px;\n    width: 799px;\n	font-family: helvetica,arial,verdana,san-serif;\n	font-size:13px;\n	color:#333;\n	}\n		</style>\n	</head>\n	<body>\n	\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\n			<tbody>\n			\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$fullname$$:</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>You have been removed as a delegate user for $$delegater_name$$\'s calendar.</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\n			\n			</tbody>\n		</table>\n	</body>\n</html>', subject = 'Mapworks Delegate removed' where id = 13;

update `email_template_lang` set email_template_id = 14, language_id = 1, body = '<html>\r\n	<head>\r\n		<style>\r\n		body {\r\n    background: none repeat scroll 0 0 #f4f4f4;\r\n	\r\n}\r\n		table {\r\n    padding: 21px;\r\n    width: 799px;\r\n	font-family: helvetica,arial,verdana,san-serif;\r\n	font-size:13px;\r\n	color:#333;\r\n	}\r\n		</style>\r\n	</head>\r\n	<body>\r\n	\r\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r\n			<tbody>\r\n			\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi $$firstname$$:</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A referral was recently assigned to you in Mapworks. Please sign in to your account to view and take action on this referral.</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Mapworks team!</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r\n			\r\n			</tbody>\r\n		</table>\r\n	</body>\r\n</html>', subject = 'You have a new Mapworks referral\r\n\r\n' where id = 14;

update `email_template_lang` set email_template_id = 15, language_id = 1, body = '<html>\n    <head>\n        <style>\n			 body {\n				background: none repeat scroll 0 0;	\n			\n			}\n			table {\n				padding: 21px;\n				width: 799px;\n				font-family: Helvetica,Arial,Verdana,San-serif;\n				font-size:13px;\n				color:#333;\n			}\n   </style>\n    </head>\n    <body>\n        <table cellpadding=\"10\" cellspacing=\"0\">\n            <tbody>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td>Dear $$student_name$$:</td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td style=\"line-height: 1.6;\">This is a reminder that you have an appointment with $$staff_name$$ on $$app_datetime$$. <br/><br/> To view the appointment details, please log in to your Mapworks dashboard and visit\n					<a style=\"color: #0033CC;\" href=\"$$student_dashboard$$\">Mapworks student dashboard view appointment module</a>.\n					</td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td>Best regards,\n                        <br/>EBI Mapworks\n                    </td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td><span style=\"font-size:11px; color: #575757; line-height: 120%; text-decoration: none;\">This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</span></td>\n                </tr>\n            </tbody>\n        </table>\n    </body>\n</html>\n', subject = 'Mapworks appointment reminder' where id = 15

/*
* ESPRJ-2069 - 
	Date: 2015-03-20 21:00
*/


UPDATE `synapse`.`ebi_config` SET `value`='http://synapse-dev.mnv-tech.com/#/dashboard' WHERE `key`='Staff_ReferralPage';

UPDATE `synapse`.`email_template_lang` SET `body`='<html>\r\n	<head>\r\n		<style>\r\n		body {\r\n    background: none repeat scroll 0 0 #f4f4f4;\r\n	\r\n}\r\n		table {\r\n    padding: 21px;\r\n    width: 799px;\r\n	font-family: helvetica,arial,verdana,san-serif;\r\n	font-size:13px;\r\n	color:#333;\r\n	}\r\n		</style>\r\n	</head>\r\n	<body>\r\n	\r\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r\n			<tbody>\r\n			\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$staff_firstname$$:</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A faculty/staff member has referred $$firstname$$ to a campus resource through the Mapworks system and added you as an interested party. </tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>To view the referral details, please log in to Mapworks and visit <a class=\"external-link\" href=\"$$staff_referralpage$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">MAP-Works student dashboard view referral module</a>. If you have any questions, please contact ($$coordinator_name$$,$$coordinator_title$$,$$coordinator_email$$ ). </td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>Skyfactor Mapworks Team</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r\n			\r\n			</tbody>\r\n		</table>\r\n	</body>\r\n</html>' WHERE `email_template_id`='17';

/*
* ESPRJ-2073 - 
	Date: 2015-03-24
*/
UPDATE `synapse`.`email_template_lang` SET `body`='<html>\r\n	<head>\r\n		<style>\r\n		body {\r\n    background: none repeat scroll 0 0 #f4f4f4;\r\n	\r\n}\r\n		table {\r\n    padding: 21px;\r\n    width: 799px;\r\n	font-family: helvetica,arial,verdana,san-serif;\r\n	font-size:13px;\r\n	color:#333;\r\n	}\r\n		</style>\r\n	</head>\r\n	<body>\r\n	\r\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r\n			<tbody>\r\n			\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$staff_firstname$$:</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A faculty/staff member has referred $$student_name$$ to a campus resource through the Mapworks system and added you as an interested party. </tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>To view the referral details, please log in to Mapworks and visit <a class=\"external-link\" href=\"$$staff_referralpage$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">MAP-Works student dashboard view referral module</a>. If you have any questions, please contact ($$coordinator_name$$,$$coordinator_title$$,$$coordinator_email$$ ). </td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>Skyfactor Mapworks Team</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r\n			\r\n			</tbody>\r\n		</table>\r\n	</body>\r\n</html>' WHERE `email_template_id`='17';


/*
* Student Profile Permissin fix 
	Date: 2015-03-27
*/

UPDATE `synapse`.`ebi_search` SET `query`='select mm.id,mm.metadata_type,mm.meta_name,pm.metadata_value as myanswer from  org_metadata mm JOIN person_org_metadata pm ON pm.org_metadata_id = mm.id  where mm.definition_type="O" AND pm.person_id = $$studentid$$ AND mm.id IN($$isppermission$$) AND mm.deleted_at IS NULL' WHERE `query_key`='Student_Profile_ISP_Info';



--
-- for ESPRJ 2199 - Email notification Dumping data for table `email_template`
--
INSERT INTO `email_template` ( `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `email_key`, `is_active`, `from_email_address`, `bcc_recipient_list`) 
VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Course_Upload_Notification',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,harisudhakar.govindaraju@techmahindra.com'),
(NULL,NULL,NULL,NULL,NULL,NULL,'Course_Faculty_Upload_Notification',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,harisudhakar.govindaraju@techmahindra.com'),
(NULL,NULL,NULL,NULL,NULL,NULL,'Course_Student_Upload_Notification',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,harisudhakar.govindaraju@techmahindra.com');


--
-- for ESPRJ 2199 - Email notification Dumping data for table `email_template_lang`
--
LOCK TABLES `email_template_lang` WRITE;
/*!40000 ALTER TABLE `email_template_lang` DISABLE KEYS */;
INSERT INTO `email_template_lang` (`email_template_id`, `language_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `body`, `subject`) 
VALUES (18,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>
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

Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>','Mapworks course upload has finished'),
(19,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>
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

$user_first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your faculty upload has finished importing. 

Click <a class="external-link" href="$$download_url$$" target="_blank" style="color: rgb(41, 114, 155);text-decoration: 

underline;">here </a>to download error file .</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI 

Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. 

Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>','Mapworks course faculty upload has finished'),
(20,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>
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

$user_first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your student upload has finished importing. 

Click <a class="external-link" href="$$download_url$$" target="_blank" style="color: rgb(41, 114, 155);text-decoration: 

underline;">here </a>to download error file .</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI 

Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. 

Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>','Mapworks course student upload has finished');
/*!40000 ALTER TABLE `email_template_lang` ENABLE KEYS */;
UNLOCK TABLES;


--
-- for ESPRJ 2199 - Email notification Updating the Email Template Lang. Removed the link to download error log and made it as a token
--
UPDATE `synapse`.`email_template_lang` SET `body`='
<html>
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
 </head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$user_first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your course upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>' WHERE `id`='18';

UPDATE `synapse`.`email_template_lang` SET `body`='
<html>
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
 </head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$user_first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your faculty upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>' WHERE `id`='19';

UPDATE `synapse`.`email_template_lang` SET `body`='
<html>
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
 </head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$user_first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your student upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>' WHERE `id`='20';

 /*
* ESPRJ-2211 - 
	Date: 2015-04-13
*/
UPDATE `synapse`.`email_template_lang` SET `body`='<html>\r\n	<head>\r\n		<style>\r\n		body {\r\n    background: none repeat scroll 0 0 #f4f4f4;\r\n	\r\n}\r\n		table {\r\n    padding: 21px;\r\n    width: 799px;\r\n	font-family: helvetica,arial,verdana,san-serif;\r\n	font-size:13px;\r\n	color:#333;\r\n	}\r\n		</style>\r\n	</head>\r\n	<body>\r\n	\r\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r\n			<tbody>\r\n			\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$staff_firstname$$:</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A faculty/staff member has referred $$student_name$$ to a campus resource through the Mapworks system and added you as an interested party. </tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>To view the referral details, please log in to Mapworks and visit <a class=\"external-link\" href=\"$$staff_referralpage$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">Mapworks student dashboard view referral module</a>. If you have any questions, please contact ($$coordinator_name$$,$$coordinator_title$$,$$coordinator_email$$ ). </td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>Skyfactor Mapworks Team</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r\n			\r\n			</tbody>\r\n		</table>\r\n	</body>\r\n</html>' WHERE `email_template_id`='17';
 
/* 
* ESPRJ - 1303
  Date: 2015-04-13
*/
UPDATE `synapse`.`ebi_search` SET `query`='select mm.id,mm.metadata_type,dml.datablock_desc as blockdesc,mml.meta_name,pm.metadata_value as myanswer from datablock_master dm join datablock_master_lang dml ON dm.id = dml.datablock_id JOIN  datablock_metadata dmd ON dmd.datablock_id = dm.id JOIN ebi_metadata mm ON dmd.ebi_metadata_id = mm.id JOIN ebi_metadata_lang mml ON mml.ebi_metadata_id = mm.id JOIN person_ebi_metadata pm ON pm.ebi_metadata_id = mm.id  where mml.lang_id=$$lang$$ AND dm.block_type="profile" AND pm.person_id = $$studentid$$ AND dm.id IN($$datablockpermission$$) AND mm.deleted_at IS NULL AND dm.deleted_at IS NULL' WHERE `query_key` ='Student_Profile_Datablock_Info';

/*
-------------------------------
My_High_priority_students_List
--------------------------------
*/
UPDATE `synapse`.`ebi_search` SET `query`='select p.id,p.firstname, p.lastname,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then p.risk_level else "" end) as risk_level,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then il.image_name else "" end) as intent_imagename,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then il.text else "" end) as intent_text,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then rl.image_name else "" end) as risk_imagename,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then rl.text else "" end) as risk_text, p.risk_model_id, 
(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person pi where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = pi.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave=1 and deleted_at is null) order by pi.risk_level desc ,pi.lastname,pi.firstname) = p.id) then p.intent_to_leave else "" end) as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity from person p join risk_model_levels rml on (p.risk_model_id = rml.risk_model_id and p.risk_level = rml.risk_level)
left join risk_levels rl on (p.risk_level = rl.id)
left join intent_to_leave il on (p.intent_to_leave = il.id) 
left outer join Logins_count lc on (lc.person_id = p.id) 
where p.id in 
(
	select distinct person_id from org_group_students ogs where ogs.org_group_id in 
	(
		select org_group_id from org_group_faculty where person_id = ($$personId$$) and deleted_at is  null 
			and org_permissionset_id in 
			(
				select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null
			)
	) and ogs.deleted_at is null
) and p.last_contact_date < p.risk_update_date and p.risk_level in ($$risklevel$$) and p.deleted_at is null' 
WHERE `query_key`='My_High_priority_students_List';

/*
-----------------------
My_Total_students_List
------------------------
*/
UPDATE `synapse`.`ebi_search` SET `query`='select p.id,p.firstname, p.lastname, (CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then p.risk_level else "" end) as risk_level,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then il.image_name else "" end) as intent_imagename,
(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then il.text else "" end) as intent_text,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then rl.image_name else "" end) as risk_imagename,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then rl.text else "" end) as risk_text, p.risk_model_id,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person pi where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = pi.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave=1 and deleted_at is null) order by pi.risk_level desc ,pi.lastname,pi.firstname) = p.id) then p.intent_to_leave else "" end) as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity 
from person p
join risk_model_levels rml on (p.risk_model_id = rml.risk_model_id and p.risk_level = rml.risk_level)
left join risk_levels rl on (p.risk_level = rl.id)
left join intent_to_leave il on (p.intent_to_leave = il.id) 
left outer join Logins_count lc on (lc.person_id = p.id) 
where p.id in 
(
                select distinct person_id from org_group_students ogs where ogs.org_group_id in 
                (
                select org_group_id from org_group_faculty where person_id = ($$personId$$) and deleted_at is null and 
                                org_permissionset_id in
                                                (select id from org_permissionset op where accesslevel_ind_agg = 1 and deleted_at is null)
                ) 
                and ogs.deleted_at is null
) 
and p.risk_level in ($$risklevel$$) and p.deleted_at is null order by p.risk_level desc ,p.lastname,p.firstname' WHERE `query_key`='My_Total_students_List';

/*
-------------------------------------
My_Total_students_List_By_RiskLevel
------------------------------------
*/

UPDATE `synapse`.`ebi_search` SET `query`='select p.id,p.firstname, p.lastname, 
(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then p.risk_level else "" end) as risk_level,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then il.image_name else "" end) as intent_imagename,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then il.text else "" end) as intent_text,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then rl.image_name else "" end) as risk_imagename,
p.risk_model_id, (CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then rl.text else "" end) as risk_text,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person pi where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = pi.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave=1 and deleted_at is null) order by pi.risk_level desc ,pi.lastname,pi.firstname) = p.id) then p.intent_to_leave else "" end) as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity from person p join risk_model_levels rml on (p.risk_model_id = rml.risk_model_id and p.risk_level = rml.risk_level) 
left join risk_levels rl on (p.risk_level = rl.id)
left join intent_to_leave il on (p.intent_to_leave = il.id) 
left outer join Logins_count lc on (lc.person_id = p.id) 
where p.id in 
(
	select distinct person_id from org_group_students ogs where ogs.org_group_id in 
		(
			select org_group_id from org_group_faculty where person_id = ($$personId$$) and deleted_at is  null 
				and org_permissionset_id in 
					(
						select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null
					)
		) and ogs.deleted_at is null
) and p.deleted_at is null and rml.risk_text = \'$$riskLevel$$\' ' WHERE `query_key`='My_Total_students_List_By_RiskLevel';

/*
* permission fix for activity stream 
*/

/*
-- Query: SELECT * FROM synapse.ebi_search
LIMIT 0, 1000

-- Date: 2015-04-28 09:40
*/

/*
-- Query: SELECT * FROM synapse.ebi_search
LIMIT 0, 1000

-- Date: 2015-04-28 20:34
*/

INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Activity_All','E',1,'SELECT A.id as AppointmentId, N.id as NoteId,R.id as ReferralId,C.id  as ContactId,AL.id as activity_log_id,AL.created_at as activity_date,AL.activity_type  as activity_type,AL.person_id_faculty as activity_created_by_id,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text,C.contact_types_id as activity_contact_type_id,CTL.description  as activity_contact_type_text,R.status as activity_referral_status,C.note as contactDescription,R.note as referralDescription,A.description as appointmentDescription,N.note as noteDescription FROM activity_log as AL LEFT JOIN Appointments as A ON AL.appointments_id = A.id LEFT JOIN note as N ON AL.note_id = N.id LEFT JOIN note_teams  as NT ON N.id = NT.note_id LEFT JOIN contacts as C ON AL.contacts_id = C.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id LEFT JOIN referrals as R ON AL.referrals_id = R.id LEFT JOIN referrals_teams  as RT ON R.id = RT.referrals_id LEFT JOIN activity_category as AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person as P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id WHERE AL.person_id_student = $$studentId$$ AND AL.organization_id = $$orgId$$ AND AL.activity_type IN ($$acivityArr$$) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND  AL.id NOT IN( SELECT ALOG.id  FROM  related_activities as related  LEFT JOIN activity_log as ALOG ON related.note_id = ALOG.note_id where related.note_id IS NOT NULL  AND related.deleted_at IS NULL  AND ALOG.deleted_at IS NULL)  AND  AL.id NOT IN( SELECT ALOG.id  FROM  related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL  AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN AL.activity_type = \"N\" THEN CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$) ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $$faculty$$ ELSE N.access_public = 1 END END ELSE CASE WHEN AL.activity_type = \"C\" THEN CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$)  ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$faculty$$  ELSE C.access_public = 1 END END ELSE CASE WHEN AL.activity_type = \"R\" THEN CASE WHEN R.access_team = 1 THEN RT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$) ELSE CASE WHEN R.access_private = 1 THEN R.person_id_faculty = $$faculty$$ ELSE R.access_public = 1 END END ELSE CASE WHEN AL.activity_type = \"A\" THEN 1 = 1 ELSE 1 =1 END END END END GROUP BY AL.id ORDER BY AL.created_at desc');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Activity_Note','E',1,'SELECT N.id as activity_id,AL.id as activity_log_id,  N.created_at as  activity_date,  N.person_id_faculty as activity_created_by_id , P.firstname as activity_created_by_first_name, P.lastname as activity_created_by_last_name, AC.id as activity_reason_id, AC.short_name as activity_reason_text, N.note as activity_description FROM activity_log as AL  LEFT JOIN note as N ON  AL.note_id = N.id LEFT JOIN person as P ON  N.person_id_faculty = P.id LEFT JOIN activity_category as AC ON N.activity_category_id  = AC.id LEFT JOIN note_teams  as NT ON N.id = NT.note_id WHERE AL.person_id_student = $$studentId$$ /*Student id in request parameter */ AND AL.deleted_at IS NULL AND N.deleted_at IS NULL AND  AL.id NOT IN(SELECT ALOG.id  FROM  related_activities as related LEFT JOIN activity_log as ALOG ON related.note_id = ALOG.note_id	where related.note_id IS NOT NULL 	AND related.deleted_at IS NULL 	AND ALOG.deleted_at IS NULL) AND CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$) /* logged in person id*/ ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $$faculty$$ /* logged in person id*/ ELSE N.access_public = 1 END END GROUP BY N.id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Activity_Contact','E',1,'SELECT  C.id as activity_id, AL.id as activity_log_id, C.created_at as  activity_date, C.person_id_faculty as activity_created_by_id , P.firstname as activity_created_by_first_name, P.lastname as activity_created_by_last_name, AC.id as activity_reason_id, AC.short_name as activity_reason_text, C.note as activity_description, C.contact_types_id  as activity_contact_type_id,CTL.description  as activity_contact_type_text FROM activity_log as AL LEFT JOIN contacts as C ON  AL.contacts_id = C.id LEFT JOIN person as P  ON C.person_id_faculty = P.id  LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category as AC ON C.activity_category_id  =  AC.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id  WHERE C.person_id_student  = $$studentId$$ AND C.deleted_at  IS NULL AND  AL.id NOT IN(SELECT ALOG.id  FROM  related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL)AND CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$facultyId$$) /* logged in person id*/ ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$facultyId$$ /* logged in person id*/ ELSE C.access_public = 1 END END GROUP BY C.id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Activity_Referral','E',1,'SELECT R.id as activity_id,AL.id as activity_log_id,R.created_at as  activity_date,R.person_id_faculty as activity_created_by_id ,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text, R.note as activity_description,R.status as activity_referral_status FROM activity_log as AL LEFT JOIN referrals as R ON  AL.referrals_id = R.id LEFT JOIN person as P ON  R.person_id_faculty = P.id LEFT JOIN activity_category as AC ON R.activity_category_id  =  AC.id LEFT JOIN referrals_teams  as RT ON R.id = RT.referrals_id  WHERE R.person_id_student = $$studentId$$ /* Student id in request parameter */ AND R.deleted_at IS NULL AND CASE WHEN access_team = 1 THEN RT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$) /* logged in person id*/ ELSE CASE WHEN access_private = 1 THEN R.person_id_faculty = $$faculty$$ ELSE R.access_public = 1 END END GROUP BY R.id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Activity_Contact_Interaction','E',1,'SELECT C.id as activity_id, AL.id as activity_log_id,C.created_at as  activity_date,C.person_id_faculty as activity_created_by_id ,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text,C.note as activity_description,C.contact_types_id  as activity_contact_type_id,CTL.description  as activity_contact_type_text FROM activity_log as AL LEFT JOIN contacts as C ON  AL.contacts_id = C.id LEFT JOIN person as P  ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category as AC ON C.activity_category_id  =  AC.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id LEFT JOIN contact_types  as CONT ON C.contact_types_id = CONT.id WHERE C.person_id_student  = $$studentId$$ /* Student id in request parameter */ AND (CONT.parent_contact_types_id = 1 OR CONT.parent_contact_types_id IS NULL) /* is interaction */ AND C.deleted_at  IS NULL AND  AL.id NOT IN( SELECT ALOG.id  FROM  related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$facultyId$$) /* logged in person id*/ ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$facultyId$$ /* logged in person id*/ ELSE C.access_public = 1 END END GROUP BY C.id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Activity_Count','E',1,'SELECT  AL.activity_type as activity_type FROM activity_log as AL LEFT JOIN Appointments as A ON AL.appointments_id = A.id LEFT JOIN note as N ON AL.note_id = N.id LEFT JOIN note_teams  as NT ON N.id = NT.note_id LEFT JOIN contacts as C ON AL.contacts_id = C.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id LEFT JOIN referrals as R ON AL.referrals_id = R.id LEFT JOIN referrals_teams  as RT ON R.id = RT.referrals_id LEFT JOIN activity_category as AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person as P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id WHERE AL.person_id_student = $$studentId$$ AND AL.organization_id = $$orgId$$ AND AL.activity_type IN ($$acivityArr$$) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND CASE WHEN AL.activity_type = \"N\" THEN CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$) ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $$faculty$$ ELSE N.access_public = 1 END END ELSE CASE WHEN AL.activity_type = \"C\" THEN CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$)  ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$faculty$$  ELSE C.access_public = 1 END END ELSE CASE WHEN AL.activity_type = \"R\" THEN CASE WHEN R.access_team = 1 THEN RT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$) ELSE CASE WHEN R.access_private = 1 THEN R.person_id_faculty = $$faculty$$ ELSE R.access_public = 1 END END ELSE CASE WHEN AL.activity_type = \"A\" THEN 1 = 1 ELSE 1 =1 END END END END GROUP BY AL.id ORDER BY AL.created_at desc');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Activity_Contact_Int_Count','E',1,'SELECT COUNT(DISTINCT(C.id)) as cnt FROM activity_log as AL LEFT JOIN contacts as C ON  AL.contacts_id = C.id LEFT JOIN person as P  ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category as AC ON C.activity_category_id  =  AC.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id LEFT JOIN contact_types  as CONT ON C.contact_types_id = CONT.id WHERE C.person_id_student  = $$studentId$$ /* Student id in request parameter */ AND (CONT.parent_contact_types_id = 1 OR CONT.parent_contact_types_id IS NULL) /* is interaction */ AND C.deleted_at  IS NULL AND  AL.id NOT IN( SELECT ALOG.id  FROM  related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$facultyId$$) /* logged in person id*/ ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$facultyId$$ /* logged in person id*/ ELSE C.access_public = 1 END END');

/*
* date: 30/04/2015 
* 2073 - Referral_InterestedParties_Staff_Closed
*/
INSERT INTO `email_template` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `email_key`, `is_active`, `from_email_address`, `bcc_recipient_list`)
VALUES
(NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Referral_InterestedParties_Staff_Closed',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`, `email_template_id`, `language_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `body`, `subject`)
VALUES
(NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\r\n	<head>\r\n		<style>\r\n		body {\r\n    background: none repeat scroll 0 0 #f4f4f4;\r\n	\r\n}\r\n		table {\r\n    padding: 21px;\r\n    width: 799px;\r\n	font-family: helvetica,arial,verdana,san-serif;\r\n	font-size:13px;\r\n	color:#333;\r\n	}\r\n		</style>\r\n	</head>\r\n	<body>\r\n	\r\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r\n			<tbody>\r\n			\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$staff_firstname$$:</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A referral that you were watching in Mapworks has recently been closed. Please sign in to your account to view this referral.</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Mapworks team!</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r\n			\r\n			</tbody>\r\n		</table>\r\n	</body>\r\n</html>','Interested party on a Mapworks referral');

/*
* Queries added for activity stream for coordinators
*/

INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_All','E',1,'SELECT A.id as AppointmentId, N.id as NoteId,R.id as ReferralId,C.id  as ContactId,AL.id as activity_log_id,AL.created_at as activity_date,AL.activity_type  as activity_type,AL.person_id_faculty as activity_created_by_id,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text,C.contact_types_id as activity_contact_type_id,CTL.description  as activity_contact_type_text,R.status as activity_referral_status,C.note as contactDescription,R.note as referralDescription,A.description as appointmentDescription,N.note as noteDescription FROM activity_log as AL LEFT JOIN Appointments as A ON AL.appointments_id = A.id LEFT JOIN note as N ON AL.note_id = N.id LEFT JOIN note_teams  as NT ON N.id = NT.note_id LEFT JOIN contacts as C ON AL.contacts_id = C.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id LEFT JOIN referrals as R ON AL.referrals_id = R.id LEFT JOIN referrals_teams  as RT ON R.id = RT.referrals_id LEFT JOIN activity_category as AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person as P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id WHERE AL.person_id_student = $$studentId$$ AND AL.organization_id = $$orgId$$ AND AL.activity_type IN ($$acivityArr$$) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND  AL.id NOT IN( SELECT ALOG.id  FROM  related_activities as related  LEFT JOIN activity_log as ALOG ON related.note_id = ALOG.note_id where related.note_id IS NOT NULL  AND related.deleted_at IS NULL  AND ALOG.deleted_at IS NULL)  AND  AL.id NOT IN( SELECT ALOG.id  FROM  related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL  AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) GROUP BY AL.id ORDER BY AL.created_at desc');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_Note','E',1,'SELECT N.id as activity_id,AL.id as activity_log_id,  N.created_at as  activity_date,  N.person_id_faculty as activity_created_by_id , P.firstname as activity_created_by_first_name, P.lastname as activity_created_by_last_name, AC.id as activity_reason_id, AC.short_name as activity_reason_text, N.note as activity_description FROM activity_log as AL  LEFT JOIN note as N ON  AL.note_id = N.id LEFT JOIN person as P ON  N.person_id_faculty = P.id LEFT JOIN activity_category as AC ON N.activity_category_id  = AC.id LEFT JOIN note_teams  as NT ON N.id = NT.note_id WHERE AL.person_id_student = $$studentId$$ /*Student id in request parameter */ AND AL.deleted_at IS NULL AND N.deleted_at IS NULL AND AL.activity_type = "N" AND AL.id NOT IN(SELECT ALOG.id  FROM  related_activities as related LEFT JOIN activity_log as ALOG ON related.note_id = ALOG.note_id	where related.note_id IS NOT NULL 	AND related.deleted_at IS NULL 	AND ALOG.deleted_at IS NULL) GROUP BY N.id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_Contact','E',1,'SELECT  C.id as activity_id, AL.id as activity_log_id, C.created_at as  activity_date, C.person_id_faculty as activity_created_by_id , P.firstname as activity_created_by_first_name, P.lastname as activity_created_by_last_name, AC.id as activity_reason_id, AC.short_name as activity_reason_text, C.note as activity_description, C.contact_types_id  as activity_contact_type_id,CTL.description  as activity_contact_type_text FROM activity_log as AL LEFT JOIN contacts as C ON  AL.contacts_id = C.id LEFT JOIN person as P  ON C.person_id_faculty = P.id  LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category as AC ON C.activity_category_id  =  AC.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id  WHERE C.person_id_student  = $$studentId$$ AND C.deleted_at  IS NULL AND  AL.id NOT IN(SELECT ALOG.id  FROM  related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) GROUP BY C.id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_Referral','E',1,'SELECT R.id as activity_id,AL.id as activity_log_id,R.created_at as  activity_date,R.person_id_faculty as activity_created_by_id ,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text, R.note as activity_description,R.status as activity_referral_status FROM activity_log as AL LEFT JOIN referrals as R ON  AL.referrals_id = R.id LEFT JOIN person as P ON  R.person_id_faculty = P.id LEFT JOIN activity_category as AC ON R.activity_category_id  =  AC.id LEFT JOIN referrals_teams  as RT ON R.id = RT.referrals_id  WHERE R.person_id_student = $$studentId$$ /* Student id in request parameter */ AND R.deleted_at IS NULL GROUP BY R.id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_Contact_Interaction','E',1,'SELECT C.id as activity_id, AL.id as activity_log_id,C.created_at as  activity_date,C.person_id_faculty as activity_created_by_id ,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text,C.note as activity_description,C.contact_types_id  as activity_contact_type_id,CTL.description  as activity_contact_type_text FROM activity_log as AL LEFT JOIN contacts as C ON  AL.contacts_id = C.id LEFT JOIN person as P  ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category as AC ON C.activity_category_id  =  AC.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id LEFT JOIN contact_types  as CONT ON C.contact_types_id = CONT.id WHERE C.person_id_student  = $$studentId$$ /* Student id in request parameter */ AND (CONT.parent_contact_types_id = 1 OR CONT.parent_contact_types_id IS NULL) /* is interaction */ AND C.deleted_at  IS NULL AND  AL.id NOT IN( SELECT ALOG.id  FROM  related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) GROUP BY C.id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_Count','E',1,'SELECT  AL.activity_type as activity_type FROM activity_log as AL LEFT JOIN Appointments as A ON AL.appointments_id = A.id LEFT JOIN note as N ON AL.note_id = N.id LEFT JOIN note_teams  as NT ON N.id = NT.note_id LEFT JOIN contacts as C ON AL.contacts_id = C.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id LEFT JOIN referrals as R ON AL.referrals_id = R.id LEFT JOIN referrals_teams  as RT ON R.id = RT.referrals_id LEFT JOIN activity_category as AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person as P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id WHERE AL.person_id_student = $$studentId$$ AND AL.organization_id = $$orgId$$ AND AL.activity_type IN ($$acivityArr$$) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL GROUP BY AL.id ORDER BY AL.created_at desc');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_Contact_Int_Count','E',1,'SELECT COUNT(DISTINCT(C.id)) as cnt FROM activity_log as AL LEFT JOIN contacts as C ON  AL.contacts_id = C.id LEFT JOIN person as P  ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category as AC ON C.activity_category_id  =  AC.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id LEFT JOIN contact_types  as CONT ON C.contact_types_id = CONT.id WHERE C.person_id_student  = $$studentId$$ /* Student id in request parameter */ AND (CONT.parent_contact_types_id = 1 OR CONT.parent_contact_types_id IS NULL) /* is interaction */ AND C.deleted_at  IS NULL AND  AL.id NOT IN( SELECT ALOG.id  FROM  related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL)');

/*
* ESPRJ-2721 - 5/5/2015
*/
UPDATE `email_template` SET `email_key`='Forgot_Password_Staff' WHERE `email_key`='Forgot_Password_Faculty';

/* 
* ESPRJ - 2781
  Date: 2015-05-14
*/
UPDATE `synapse`.`ebi_search` SET `query`='select mm.id,mm.metadata_type,dml.datablock_desc as blockdesc,mml.meta_name,pm.metadata_value as myanswer from datablock_master dm join datablock_master_lang dml ON dm.id = dml.datablock_id JOIN  datablock_metadata dmd ON dmd.datablock_id = dm.id JOIN ebi_metadata mm ON dmd.ebi_metadata_id = mm.id JOIN ebi_metadata_lang mml ON mml.ebi_metadata_id = mm.id JOIN person_ebi_metadata pm ON pm.ebi_metadata_id = mm.id  where mml.lang_id=$$lang$$ AND dm.block_type="profile" AND pm.person_id = $$studentid$$ AND dm.id IN($$datablockpermission$$) AND mm.deleted_at IS NULL AND dm.deleted_at IS NULL AND dml.deleted_at IS NULL AND dmd.deleted_at IS NULL AND pm.deleted_at IS NULL AND mml.deleted_at IS NULL' WHERE `query_key` ='Student_Profile_Datablock_Info';

UPDATE `synapse`.`ebi_search` SET `query`='select mm.id,mm.metadata_type,mm.meta_name,pm.metadata_value as myanswer from  org_metadata mm JOIN person_org_metadata pm ON pm.org_metadata_id = mm.id  where mm.definition_type="O" AND pm.person_id = $$studentid$$ AND mm.id IN($$isppermission$$) AND mm.deleted_at IS NULL AND pm.deleted_at IS NULL' WHERE `query_key`='Student_Profile_ISP_Info';

/*
Added missing AWS related keys in ebi-config
*/
INSERT INTO `ebi_config` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'AWS_Key','AKIAJJWBI4AF5T4VLVSA');
INSERT INTO `ebi_config` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'AWS_Secret','6gHgrpMsa1Ty6ntBFloJ0WKOWY54GmLYGpzVz+zF');
INSERT INTO `ebi_config` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'AWS_Region','us-east-1');
INSERT INTO `ebi_config` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'AWS_Bucket','ebi-synapse-bucket');
INSERT INTO `ebi_config` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'AWS_Get_Expire_Time','600');
INSERT INTO `ebi_config` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'AWS_Put_Expire_Time','1800');