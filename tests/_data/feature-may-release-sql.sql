/*
-- ESPRJ - 2121 - As a faculty/staff user, I get an email notification when a student books.

-- Date:  11-05-2015 4.30pm By Saravanan
*/

INSERT INTO `ebi_config` (`key`, `value`) VALUES ('Staff_Dashboard_Appointment_Module', 'http://synapse-dev.mnv-tech.com/#/schedule');

INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Appointment_Book_Student_to_Staff',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,saravanan.rajagopal@techmahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$staff_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>An appointment has been booked with $$student_name$$ on $$app_datetime$$. To view the appointment details,please log in to your Mapworks dashboard and visit <a class="external-link" href="$$staff_dashboard$$" target="_blank" style="color: rgb(41, 114, 155);text-decoration: underline;">MAP-Works faculty dashboard view appointment module</a>.</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>','Mapworks appointment booked');

/*
-- ESPRJ - 2121 - As a faculty/staff user, I get an email notification when a student cancel an appointment.

-- Date:  11-05-2015 4.30pm By Saravanan
*/

INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Appointment_Cancel_Student_to_Staff',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,saravanan.rajagopal@techmahindra.com');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Appointment_Cancel_Student_to_Staff',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,saravanan.rajagopal@techmahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$staff_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your booked appointment with $$student_name$$ on $$app_datetime$$ has been cancelled. To book a new appointment, please log in to your MAP-Works dashboard and visit <a class="external-link" href="$$staff_dashboard$$" target="_blank" style="color: rgb(41, 114, 155);text-decoration: underline;">MAP-Works faculty dashboard view appointment module</a>.</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>','Mapworks appointment cancelled');


/*
-- ESPRJ-1313 - Risk Variable Creation/Modification

-- Date:  12-may-2015 3.10pm By Preet
*/
INSERT INTO ebi_config(`key`,`value`) values('Risk_Source_Types', 'profile, surveyquestion, surveyfactor, isp, isq, questionbank');

/* 11 MAY 2015 - Risk re-factoring start */

UPDATE `synapse`.`ebi_search` SET `query`='select p.risk_level, count(p.id) as totalStudentsHighPriority, rml.risk_text, rml.image_name from person p, risk_level rml where p.id in (select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = $$personId$$ and deleted_at is  null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null)) and ogs.deleted_at is null) and rml.id = p.risk_level and p.deleted_at is null group by p.risk_level' WHERE `query_key` ='My_Total_Students_Count_Groupby_Risk';


UPDATE `synapse`.`ebi_search` SET `query`='select p.id,p.firstname, p.lastname,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then p.risk_level else "" end) as risk_level,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then il.image_name else "" end) as intent_imagename,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then il.text else "" end) as intent_text,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then rl.image_name else "" end) as risk_imagename,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then rl.risk_text else "" end) as risk_text,
(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person pi where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = pi.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave=1 and deleted_at is null) order by pi.risk_level desc ,pi.lastname,pi.firstname) = p.id) then p.intent_to_leave else "" end) as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity from person p join risk_level rml on (p.risk_level = rml.id)
left join risk_level rl on (p.risk_level = rl.id)
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


UPDATE `synapse`.`ebi_search` SET `query`='select p.id,p.firstname, p.lastname,
(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then p.risk_level else "" end) as risk_level,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then il.image_name else "" end) as intent_imagename,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then il.text else "" end) as intent_text,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then rl.image_name else "" end) as risk_imagename,
(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then rl.risk_text else "" end) as risk_text,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person pi where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = pi.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave=1 and deleted_at is null) order by pi.risk_level desc ,pi.lastname,pi.firstname) = p.id) then p.intent_to_leave else "" end) as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity from person p join risk_level rml on ( p.risk_level = rml.id)
left join risk_level rl on (p.risk_level = rl.id)
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
) and p.deleted_at is null and rml.risk_text = \'$$riskLevel$$\'' WHERE `query_key`='My_Total_students_List_By_RiskLevel';


UPDATE `synapse`.`ebi_search` SET `query`='select p.id,p.firstname, p.lastname, (CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then p.risk_level else "" end) as risk_level,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then il.image_name else "" end) as intent_imagename,
(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then il.text else "" end) as intent_text,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then rl.image_name else "" end) as risk_imagename,(CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) then rl.risk_text else "" end) as risk_text, (CASE when ((select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person pi where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = pi.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave=1 and deleted_at is null) order by pi.risk_level desc ,pi.lastname,pi.firstname) = p.id) then p.intent_to_leave else "" end) as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity
from person p
join  risk_level rl on (p.risk_level = rl.id)
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


UPDATE `synapse`.`ebi_search` SET `query`='select r.id as \'referral_id\',r.person_id_student, p.firstname, p.lastname, p.risk_level, p.intent_to_leave,rml.image_name, rml.risk_text,lc.cnt as login_cnt, p.cohert, p.last_activity  FROM referrals r  join person p on (r.person_id_student = p.id ) LEFT join risk_level rml on (p.risk_level = rml.id) left outer join Logins_count lc on (lc.person_id = r.person_id_student)  where  r.deleted_at IS NULL AND (r.status = \'O\' or r.status=\'R\') and r.person_id_faculty = $$personid$$' WHERE `query_key`='My_Open_Referrals_Sent_List';


UPDATE `synapse`.`ebi_search` SET `query`='select r.id as \'referral_id\',r.person_id_student, p.firstname,p.lastname, p.risk_level,p.intent_to_leave,rml.image_name,rml.risk_text,lc.cnt as login_cnt,p.cohert,p.last_activity FROM referrals r join person p on (r.person_id_student = p.id ) left join risk_level rml on (p.risk_level = rml.id) left outer join Logins_count lc on (lc.person_id = r.person_id_student) where  r.deleted_at IS NULL AND r.status = \'O\' and r.person_id_assigned_to = $$personid$$' WHERE `query_key`='My_Open_Referrals_Received_List';

update synapse.person set risk_level=4 where risk_level>4;

/* end risk re-factoring end */

/*
-- Audit Trail Configuration
 */
INSERT INTO `ebi_config` (`created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `key`, `value`) VALUES  (NULL, NULL, NULL, NULL, NULL, NULL, 'Audit_Entities', '[\"Synapse\\\\CoreBundle\\\\Entity\\\\OrgFeatures\"]');

/*
-- ESPRJ - 1783 ,1893, 1784, 1785 - Multi Campus Hierarchy 

-- Date:  15/5/2015
*/

INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Send_Invitation_to_User',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,harisudhakar.govindaraju@techmahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>
Welcome to Mapworks. Use the link below to create your password and start using Mapworks. $$activation_token$$
If you believe that you received this email in error or if you have any questions, please contact Mapworks support at support@mapworks.com.
Thank you from the Skyfactor Mapworks team.
[Skyfactor Mapworks logo].
</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>','Welcome to Mapworks');


INSERT INTO `ebi_config` (`key`, `value`) VALUES ('MultiCampus_Change_Request', 'http://synapse-dev.mnv-tech.com/');

INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Accept_Change_Request',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,harisudhakar.govindaraju@techmahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>
Welcome to Mapworks. Use the link below to create your password and start using Mapworks. 
If you believe that you received this email in error or if you have any questions, please contact Mapworks support at support@mapworks.com.
Thank you from the Skyfactor Mapworks team.
[Skyfactor Mapworks logo].
</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>','Change Requested Accepted');

INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Deny_Change_Request',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,harisudhakar.govindaraju@techmahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>
Welcome to Mapworks. Use the link below to create your password and start using Mapworks. 
If you believe that you received this email in error or if you have any questions, please contact Mapworks support at support@mapworks.com.
Thank you from the Skyfactor Mapworks team.
[Skyfactor Mapworks logo].
</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>','Change Request Denied');

INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activate_Email',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,harisudhakar.govindaraju@techmahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>
Welcome to Mapworks. Use the link below to create your password and start using Mapworks. $$activation_token$$
If you believe that you received this email in error or if you have any questions, please contact Mapworks support at support@mapworks.com.
Thank you from the Skyfactor Mapworks team.
[Skyfactor Mapworks logo].
</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>','Activate Email');


INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Deactivate_Email',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,harisudhakar.govindaraju@techmahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>
Welcome to Mapworks. Email Deactivated. 
If you believe that you received this email in error or if you have any questions, please contact Mapworks support at support@mapworks.com.
Thank you from the Skyfactor Mapworks team.
[Skyfactor Mapworks logo].
</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>','Deactivate Email');

/*
updated case of values
-- ESPRJ-1313 - Risk Variable Creation/Modification
-- Date:  18-may-2015 By Preet
*/
update ebi_config set `value` = 'profile, surveyquestion, surveyfactor, ISP, ISQ, questionbank'
where `key`= 'Risk_Source_Types';


/*
* Predefined Search Data
*/
/*
-- Query: SELECT * FROM synapse.ebi_search
LIMIT 0, 1000

-- Date: 2015-05-25 15:21
*/

INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'All_My_Student','P',1,'SELECT OC.org_academic_year_id, OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OC.org_academic_year_id = $$yearId$$ AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id ');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'My_Primary_Campus_Connection','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OC.deleted_at IS NULL AND OPS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND OPS.person_id_primary_connect = $$facultyId$$ AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Class_Level','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN person_ebi_metadata AS PEM ON PEM.person_id = OGS.person_id WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND PEM.deleted_At IS NULL AND PEM.ebi_metadata_id IN (SELECT id FROM ebi_metadata WHERE meta_key="Class Level") GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'At_Risk','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OC.org_academic_year_id = $$yearId$$ AND P.risk_level IN(1,2) AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'High_Priority_Students','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND P.risk_level IN(1,2) AND OPS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND OGS.person_id IN ( $$personIds$$ ) AND (P.risk_update_date IS NOT NULL AND P.risk_update_date > P.last_contact_date)');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Respondents_To_Current_Survey','P',1,'SELECT SR.id AS srid, OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN survey_response AS SR ON SR.person_id = OGS.person_id WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND SR.org_id = $$orgId$$ AND SR.org_academic_year_id = $$yearId$$ AND OC.org_academic_year_id = $$yearId$$ AND OGS.person_id IN ( $$personIds$$ ) AND SR.survey_id IN (SELECT DISTINCT(survey_id) FROM wess_link WHERE year_id = $$yearText$$ AND status= "launched") GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Non_Respondents_To_Current_Survey','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OC.org_academic_year_id = $$yearId$$ AND OGS.person_id IN ( $$personIds$$ ) AND OGS.person_id NOT IN (SELECT DISTINCT(person_id) FROM survey_response AS SR WHERE SR.org_id = $$orgId$$ AND SR.org_academic_year_id = $$yearId$$ AND SR.survey_id IN (SELECT DISTINCT(survey_id) FROM wess_link WHERE year_id = $$yearText$$ AND status= "launched")) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Accessed_Current_Survey_Report','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN org_survey_report_access_history AS SR ON SR.person_id = OGS.person_id WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OC.org_academic_year_id = $$yearId$$ AND SR.org_id = $$orgId$$ AND SR.year_id = $$yearText$$ AND OGS.person_id IN ( $$personIds$$ ) AND SR.survey_id IN (SELECT DISTINCT(survey_id) FROM wess_link WHERE year_id = $$yearText$$ AND status= "launched") AND SR.deleted_at IS NULL GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Not_Accessed_Current_Survey_Report','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OC.org_academic_year_id = $$yearId$$ AND OGS.person_id IN ( $$personIds$$ ) AND OGS.person_id NOT IN (SELECT DISTINCT(person_id) FROM org_survey_report_access_history AS SR WHERE SR.org_id = $$orgId$$ AND SR.year_id = $$yearText$$ AND SR.survey_id IN (SELECT DISTINCT(survey_id) FROM wess_link WHERE year_id = $$yearText$$ AND status= "launched" AND SR.deleted_at IS NULL)) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'High_Intent_To_Leave','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OGS.person_id IN ( $$personIds$$ ) AND P.intent_to_leave = 1 GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'At_Risk_Of_Failure','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND AU.failure_risk_level = "high" AND AU.update_date BETWEEN "$$startDate$$" AND "$$endDate$$" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Missed_3_Classes','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND AU.absence > 2 AND AU.update_date BETWEEN "$$startDate$$" AND "$$endDate$$" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'In-progress_Grade_Of_C_Or_Below','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND ASCII(AU.grade) >= 67 AND AU.update_date BETWEEN "$$startDate$$" AND "$$endDate$$" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Final_Grade_Of_C_Or_Below','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND ASCII(AU.final_grade) >= 67 AND AU.update_date BETWEEN "$$startDate$$" AND "$$endDate$$" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'In-progress_Grade_Of_D_Or_Below','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND ASCII(AU.grade) >= 68 AND AU.update_date BETWEEN "$$startDate$$" AND "$$endDate$$" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Final_Grade_Of_D_Or_Below','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND ASCII(AU.final_grade) >= 68 AND AU.update_date BETWEEN "$$startDate$$" AND "$$endDate$$" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Students_With_More_Than_One_In-progress_Grade_Of_D_Or_Below','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url, AU.grade, count(AU.person_id_student) AS t FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND ASCII(AU.grade) >= 68 AND AU.update_date BETWEEN "$$startDate$$" AND "$$endDate$$" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY AU.person_id_student HAVING t > 1');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Students_With_More_Than_One_Final_Grade_Of_D_Or_Below','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url, AU.final_grade, count(AU.person_id_student) AS t FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND ASCII(AU.final_grade) >= 68 AND AU.update_date BETWEEN "$$startDate$$" AND "$$endDate$$" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY AU.person_id_student HAVING t > 1 ');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Interaction_Activity','P',1,'SELECT C.person_id_faculty AS faculty, C.person_id_student AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM activity_log AS AL LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN person AS P ON C.person_id_student = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category AS AC ON C.activity_category_id = AC.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = C.person_id_student LEFT JOIN org_course_student AS OCS ON OCS.person_id = C.person_id_student LEFT JOIN org_courses AS OC ON (OCS.org_courses_id = OC.id) LEFT JOIN Logins_count LC ON (LC.person_id = C.person_id_student) WHERE C.person_id_faculty = $$facultyId$$ AND (CONT.parent_contact_types_id = 1 OR CONT.id = 1) AND C.deleted_at IS NULL AND OC.org_academic_year_id = $$yearId$$ AND C.person_id_student IN ( $$personIds$$ ) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.contacts_id = ALOG.contacts_id WHERE related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$facultyId$$)/* logged in person id*/ ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$facultyId$$ /* logged in person id*/ ELSE C.access_public = 1 END END GROUP BY C.id');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Non-interaction_Activity','P',1,'SELECT C.person_id_faculty AS faculty, C.person_id_student AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM activity_log AS AL LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN person AS P ON C.person_id_student = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category AS AC ON C.activity_category_id = AC.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = C.person_id_student LEFT JOIN org_course_student AS OCS ON OCS.person_id = C.person_id_student LEFT JOIN org_courses AS OC ON (OCS.org_courses_id = OC.id) LEFT JOIN Logins_count LC ON (LC.person_id = C.person_id_student) WHERE C.person_id_faculty = $$facultyId$$ AND (CONT.parent_contact_types_id = 2 OR CONT.id = 2) AND C.deleted_at IS NULL AND OC.org_academic_year_id = $$yearId$$ AND C.person_id_student IN ( $$personIds$$ ) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.contacts_id = ALOG.contacts_id WHERE related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$facultyId$$)/* logged in person id*/ ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$facultyId$$ /* logged in person id*/ ELSE C.access_public = 1 END END GROUP BY C.id ');
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Have_Not_Been_Reviewed','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN student_db_view_log SDV ON (SDV.person_id_student = OGS.person_id AND SDV.person_id_faculty = OGF.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OGS.person_id IN ( $$personIds$$ ) AND SDV.organization_id = $$orgId$$ AND SDV.last_viewed_on > P.risk_update_date');


SET @All_My_Student := (select id from  ebi_search where query_key ='All_My_Student');
SET @My_Primary_Campus_Connection := (select id from  ebi_search where query_key ='My_Primary_Campus_Connection');
SET @Class_Level := (select id from  ebi_search where query_key ='Class_Level');
SET @At_Risk := (select id from  ebi_search where query_key ='At_Risk');
SET @High_Priority_Students := (select id from  ebi_search where query_key ='High_Priority_Students');
SET @Respondents_To_Current_Survey := (select id from  ebi_search where query_key ='Respondents_To_Current_Survey');
SET @Non_Respondents_To_Current_Survey := (select id from  ebi_search where query_key ='Non_Respondents_To_Current_Survey');
SET @Accessed_Current_Survey_Report := (select id from  ebi_search where query_key ='Accessed_Current_Survey_Report');
SET @Not_Accessed_Current_Survey_Report := (select id from  ebi_search where query_key ='Not_Accessed_Current_Survey_Report');
SET @High_Intent_To_Leave := (select id from  ebi_search where query_key ='High_Intent_To_Leave');
SET @At_Risk_Of_Failure := (select id from  ebi_search where query_key ='At_Risk_Of_Failure');
SET @Missed_3_Classes := (select id from  ebi_search where query_key ='Missed_3_Classes');
SET @In_progress_Grade_Of_C_Or_Below := (select id from  ebi_search where query_key ='In-progress_Grade_Of_C_Or_Below');
SET @Final_Grade_Of_C_Or_Below := (select id from  ebi_search where query_key ='Final_Grade_Of_C_Or_Below');
SET @In_progress_Grade_Of_D_Or_Below := (select id from  ebi_search where query_key ='In-progress_Grade_Of_D_Or_Below');
SET @Final_Grade_Of_D_Or_Below := (select id from  ebi_search where query_key ='Final_Grade_Of_D_Or_Below');
SET @Students_With_More_Than_One_In_progress_Grade_Of_D_Or_Below := (select id from  ebi_search where query_key ='Students_With_More_Than_One_In-progress_Grade_Of_D_Or_Below');
SET @Students_With_More_Than_One_Final_Grade_Of_D_Or_Below := (select id from  ebi_search where query_key ='Students_With_More_Than_One_Final_Grade_Of_D_Or_Below');
SET @Interaction_Activity := (select id from  ebi_search where query_key ='Interaction_Activity');
SET @Non_interaction_Activity := (select id from  ebi_search where query_key ='Non-interaction_Activity');
SET @Have_Not_Been_Reviewed := (select id from  ebi_search where query_key ='Have_Not_Been_Reviewed');



INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@All_My_Student,1,NULL,NULL,NULL,NULL,NULL,NULL,'student_search','All My Students');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@My_Primary_Campus_Connection,1,NULL,NULL,NULL,NULL,NULL,NULL,'student_search','My Primary Campus Connections');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@Class_Level,1,NULL,NULL,NULL,NULL,NULL,NULL,'student_search','Class Level');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@At_Risk,1,NULL,NULL,NULL,NULL,NULL,NULL,'student_search','At-risk');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@High_Priority_Students,1,NULL,NULL,NULL,NULL,NULL,NULL,'student_search','High Priority Students');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@Respondents_To_Current_Survey,1,NULL,NULL,NULL,NULL,NULL,NULL,'survey_search','Respondents to current survey');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@Non_Respondents_To_Current_Survey,1,NULL,NULL,NULL,NULL,NULL,NULL,'survey_search','Non-respondents to current survey');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@Accessed_Current_Survey_Report,1,NULL,NULL,NULL,NULL,NULL,NULL,'survey_search','Accessed current survey report');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@Not_Accessed_Current_Survey_Report,1,NULL,NULL,NULL,NULL,NULL,NULL,'survey_search','Not accessed current survey report');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@High_Intent_To_Leave,1,NULL,NULL,NULL,NULL,NULL,NULL,'survey_search','High Intent to Leave');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@At_Risk_Of_Failure,1,NULL,NULL,NULL,NULL,NULL,NULL,'academic_update_search','At risk of failure');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@Missed_3_Classes,1,NULL,NULL,NULL,NULL,NULL,NULL,'academic_update_search','Missed more than 3 classes in current academic term');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@In_progress_Grade_Of_C_Or_Below,1,NULL,NULL,NULL,NULL,NULL,NULL,'academic_update_search','In-progress Grade of C or below');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@Final_Grade_Of_D_Or_Below,1,NULL,NULL,NULL,NULL,NULL,NULL,'academic_update_search','Final grade of C or below');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@In_progress_Grade_Of_D_Or_Below,1,NULL,NULL,NULL,NULL,NULL,NULL,'academic_update_search','In-progress Grade of D or below');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@Final_Grade_Of_D_Or_Below,1,NULL,NULL,NULL,NULL,NULL,NULL,'academic_update_search','Final grade of D or below');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@Students_With_More_Than_One_In_progress_Grade_Of_D_Or_Below,1,NULL,NULL,NULL,NULL,NULL,NULL,'academic_update_search','Students with more than one in-progress grade of D or below');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@Students_With_More_Than_One_Final_Grade_Of_D_Or_Below,1,NULL,NULL,NULL,NULL,NULL,NULL,'academic_update_search','Students with more than one final grade of D or below');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@Interaction_Activity,1,NULL,NULL,NULL,NULL,NULL,NULL,'activity_search','Interaction Activity');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@Non_interaction_Activity,1,NULL,NULL,NULL,NULL,NULL,NULL,'activity_search','Non-interaction Activity');
INSERT INTO `ebi_search_lang` (`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`,`sub_category_name`) VALUES (@Have_Not_Been_Reviewed,1,NULL,NULL,NULL,NULL,NULL,NULL,'activity_search','Have not been reviewed');

/*
* Predefined Search Data ends here
*/

/*
-- ESPRJ - 2121 - As a faculty/staff user, I get an email notification when a student books. Template update

-- Date:  20-05-2015 1.00pm By Saravanan
*/
UPDATE `email_template_lang` SET `body`='<html>\r 	<head>\r 		<style>\r 		body {\r     background: none repeat scroll 0 0 #f4f4f4;\r 	\r }\r 		table {\r     padding: 21px;\r     width: 799px;\r 	font-family: helvetica,arial,verdana,san-serif;\r 	font-size:13px;\r 	color:#333;\r 	}\r 		</style>\r 	</head>\r 	<body>\r 	\r 		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r 			<tbody>\r 			\r 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$staff_name$$:</td></tr>\r 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>An appointment has been booked with $$student_name$$  \r 				on $$app_datetime$$. To view the appointment details,\r 				please log in to your Mapworks dashboard and visit <a class=\"external-link\" href=\"$$staff_dashboard$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155);text-decoration: underline;\">Mapworks student dashboard view appointment module</a>.</td></tr>\r 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/><img src=\"$$Skyfactor_Mapworks_logo$$\" alt=\"Skyfactor Mapworks logo\" title=\"Skyfactor Mapworks logo\" /></td></tr>\r 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r 			\r 			</tbody>\r 		</table>\r</body>\r</html>' WHERE `id`='27';

/*
-- ESPRJ - 2121 - As a faculty/staff user, I get an email notification when a student cancel an appointment. Template Update

-- Date:  20-05-2015 1.00pm By Saravanan
*/
UPDATE `email_template_lang` SET `body`='<html> <head>\r <style>\r body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}\r </style>\r </head>\r <body>\r <table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r <tbody>\r <tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$staff_name$$:</td></tr>\r <tr style=\"background:#fff;border-collapse:collapse;\"><td>Your booked appointment with $$student_name$$ on $$app_datetime$$ has been cancelled. To book a new appointment, please log in to your Mapworks dashboard and visit \r <a class=\"external-link\" href=\"$$staff_dashboard$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155);text-decoration: underline;\">\r Mapworks faculty dashboard view appointment module</a>.</td></tr>\r <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/><img src=\"$$Skyfactor_Mapworks_logo$$\" alt=\"Skyfactor Mapworks logo\" title=\"Skyfactor Mapworks logo\" /></td></tr>\r <tr style=\"background:#fff;border-collapse:collapse;\"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>' WHERE `id`='28';

/*
-- ESPRJ-1826 ESPRJ-1447 ESPRJ-1448, Added completed URL for Traning Site URL and Sandbox URL
-- Date:  26-05-2015 9.19PM
-- By Delli Babu
*/

UPDATE `ebi_config` SET `value`='https://$$Sub_Domain$$-training.skyfactor.com' WHERE `key`='Training_Site_URL';
INSERT INTO `ebi_config` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Sandbox_Site_URL','https://$$Sub_Domain$$-sandbox.skyfactor.com');

/*
-- ESPRJ - 2069 - Referral - Interested party email content fix
-- Date: 27-05-2015 2.15pm
*/
SET @emtid := (SELECT id FROM email_template where email_key='Referral_InterestedParties_Staff');

UPDATE `synapse`.`email_template_lang` SET `body`='<html>\r 	<head>\r 		<style>\r 		body {\r     background: none repeat scroll 0 0 #f4f4f4;\r 	\r }\r 		table {\r     padding: 21px;\r     width: 799px;\r 	font-family: helvetica,arial,verdana,san-serif;\r 	font-size:13px;\r 	color:#333;\r 	}\r 		</style>\r 	</head>\r 	<body>\r 	\r 		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r 			<tbody>\r 			\r 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$staff_firstname$$,</td></tr>\r 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A faculty/staff member has referred $$student_name$$ to a campus resource through the Mapworks system and added you as an interested party. </tr>\r 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>To view the referral details, please log in to Mapworks and visit <a class=\"external-link\" href=\"$$staff_referralpage$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">Mapworks student dashboard view referral module</a>. If you have any questions, please contact ($$coordinator_name$$,$$coordinator_title$$,$$coordinator_email$$ ). </td></tr>\r 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.</td></tr>\r 				<tr style=\"background:#fff;border-collapse:collapse;\"><td><img src=\"$$Skyfactor_Mapworks_logo$$\" title=\"Skyfactor Mapworks logo\" alt=\"Skyfactor Mapworks logo\"/></td></tr>\r 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r 			\r 			</tbody>\r 		</table>\r 	</body>\r</html>' WHERE `email_template_id`= @emtid;

/* ESPRJ - 2069 -  Referral - Interested party email content fix Ends Here */


/*
-- ESPRJ -2873 - Fix Date - 25/5/2015
-- By Harisudhakar Govindaraju
*/
UPDATE `synapse`.`email_template` SET `email_key`='Forgot_Password_Staff' WHERE `id`='2';

/* MC Stories - Updated Email Template Starts Here */
-- Date:  29-05-2015 By Harisudhakar Govindaraju

/* Send Invitation Email to User */
SET @emtid := (SELECT id FROM email_template where email_key='Send_Invitation_to_User');

UPDATE `synapse`.`email_template_lang` SET `body`='<o:AllowPNG/>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
<![endif]--> 
<title>MAP-Works Faculty Invitation</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<style type="text/css">
/* e-mail bugfixes */
#outlook a {padding: 0 0 0 0;}
.ReadMsgBody {width: 100%;}
.ExternalClass {width: 100%; line-height: 100%;}
.ExternalClass * {line-height: 100%}
sup, sub {vertical-align: baseline; position: relative; top: -0.4em;}
sub {top: 0.4em;}
.applelinks a {color:#262727; text-decoration: none;}


/* General classes */
body {width: 100% !important; margin: 0; padding: 0; -webkit-text-size-adjust:none; -ms-text-size-adjust:100%; font-size:13px; color:#333333; font-family: helvetica neue, helvetica, arial, verdana, san-serif; }
img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic; border: none;}
.bodytemplate, td { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; mso-line-height-rule:exactly }
.bodytemplate { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; }
.bodytemplate a, .bodytemplate a:hover { color: #F48C00; text-decoration: underline; }
.main_table td{font-size:14px; color:#333333; text-align:left; font-family: helvetica neue, helvetica, arial, verdana, san-serif; padding:5px 10px;}

<!-- NOTE: Remove this css-code to make te email scalable instead of responsive -->
/* Smartphones (portrait and landscape) ----------- */
@media only screen and (max-width:800px) {
*[class=mHide] {display: none !important;}
*[class=mWidth100] {width:100% !important; max-width: 100% !important;}
*[class=mPaddingbottom] {padding-bottom:12px !important;}
*[class=mGmail] {width:100% !important; min-width: 100% !important; padding: 0 10px !important;}
*[class=titeloranje] {-webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; background-color:#F48C00;}
*[class=nieuwsbrieftitel] { padding-top: 20px !important;}
*[class=openhtml] { padding: 10px !important; }
}
<!-- NOTE: End CSS-code to remove if scalable email -->
</style>
</head>
<body bgcolor="#ffffff">
<table bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" width="100%" style="table-layout: fixed">
  <tbody>
    <tr>
      <td class="bodytemplate" align="center" valign="top">
	  <table align="center" class="mGmail" style="width:800px; min-width:800px;  " cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
          <tbody>
            <tr>
              <td style="width:800px; max-width:800px; padding-bottom:20px;">
				
			  </td>
            </tr>
			 <tr>
			    <td>
				<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
									Hi $$firstname$$,
								  </td>
								</tr>
							  </tbody>
							</table>

							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
										Welcome to Mapworks. Use the link below to create your password and start using Mapworks.
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
										<a href="$$Coordinator_ResetPwd_URL_Prefix$$">[password reset link here]</a>
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
										If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href="mailto:$$Support_Helpdesk_Email_Address$$" class="external-link" rel="nofollow">$$Support_Helpdesk_Email_Address$$</a>
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
									Thank you from the Skyfactor Mapworks team.</br>
									<img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/ ><br/>
									This email is an auto-generated message. Replies to automated messages are not monitored.
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					
			    </td>
			</tr>
          </tbody>
        </table>
		</td>
    </tr>
    <tr>
      <td>
		
	  </td>
    </tr>
  </tbody>
</table>
</body>
</html>' WHERE `email_template_id`=@emtid;

/* Change Requested Accepted */

SET @emtid := (SELECT id FROM email_template where email_key='Accept_Change_Request');

UPDATE `synapse`.`email_template_lang` SET `body`='<!-- Versie 2.0 !-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<!--[if gte mso 9]>
<xml>
<o:OfficeDocumentSettings>
<o:AllowPNG/>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
<![endif]--> 
<title>MAP-Works Faculty Invitation</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<style type="text/css">
/* e-mail bugfixes */
#outlook a {padding: 0 0 0 0;}
.ReadMsgBody {width: 100%;}
.ExternalClass {width: 100%; line-height: 100%;}
.ExternalClass * {line-height: 100%}
sup, sub {vertical-align: baseline; position: relative; top: -0.4em;}
sub {top: 0.4em;}
.applelinks a {color:#262727; text-decoration: none;}


/* General classes */
body {width: 100% !important; margin: 0; padding: 0; -webkit-text-size-adjust:none; -ms-text-size-adjust:100%; font-size:13px; color:#333333; font-family: helvetica neue, helvetica, arial, verdana, san-serif; }
img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic; border: none;}
.bodytemplate, td { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; mso-line-height-rule:exactly }
.bodytemplate { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; }
.bodytemplate a, .bodytemplate a:hover { color: #F48C00; text-decoration: underline; }
.main_table td{font-size:14px; color:#333333; text-align:left; font-family: helvetica neue, helvetica, arial, verdana, san-serif; padding:5px 10px;}

<!-- NOTE: Remove this css-code to make te email scalable instead of responsive -->
/* Smartphones (portrait and landscape) ----------- */
@media only screen and (max-width:800px) {
*[class=mHide] {display: none !important;}
*[class=mWidth100] {width:100% !important; max-width: 100% !important;}
*[class=mPaddingbottom] {padding-bottom:12px !important;}
*[class=mGmail] {width:100% !important; min-width: 100% !important; padding: 0 10px !important;}
*[class=titeloranje] {-webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; background-color:#F48C00;}
*[class=nieuwsbrieftitel] { padding-top: 20px !important;}
*[class=openhtml] { padding: 10px !important; }
}
<!-- NOTE: End CSS-code to remove if scalable email -->
</style>
</head>
<body bgcolor="#ffffff">
<table bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" width="100%" style="table-layout: fixed">
  <tbody>
    <tr>
      <td class="bodytemplate" align="center" valign="top">
	  <table align="center" class="mGmail" style="width:800px; min-width:800px;  " cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
          <tbody>
            <tr>
              <td style="width:800px; max-width:800px; padding-bottom:20px;">
				
			  </td>
            </tr>
			 <tr>
			    <td>
				<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
									Hi $$firstname$$,
								  </td>
								</tr>
							  </tbody>
							</table>

							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
										Your home campus change request for [$$student_firstname$$ $$student_lastname$$] has been approved.
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
										<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
										If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href="mailto:$$Support_Helpdesk_Email_Address$$" class="external-link" rel="nofollow">$$Support_Helpdesk_Email_Address$$</a>
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
									Thank you from the Skyfactor Mapworks team.</br>
								<img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/ ><br/>
									This email is an auto-generated message. Replies to automated messages are not monitored.
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					
			    </td>
			</tr>
          </tbody>
        </table>
		</td>
    </tr>
    <tr>
      <td>
		
	  </td>
    </tr>
  </tbody>
</table>
</body>
</html>' WHERE `email_template_id`=@emtid;

/* Change Request Denied */

SET @emtid := (SELECT id FROM email_template where email_key='Deny_Change_Request');

UPDATE `synapse`.`email_template_lang` SET `body`='<!-- Versie 2.0 !-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<!--[if gte mso 9]>
<xml>
<o:OfficeDocumentSettings>
<o:AllowPNG/>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
<![endif]--> 
<title>MAP-Works Faculty Invitation</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<style type="text/css">
/* e-mail bugfixes */
#outlook a {padding: 0 0 0 0;}
.ReadMsgBody {width: 100%;}
.ExternalClass {width: 100%; line-height: 100%;}
.ExternalClass * {line-height: 100%}
sup, sub {vertical-align: baseline; position: relative; top: -0.4em;}
sub {top: 0.4em;}
.applelinks a {color:#262727; text-decoration: none;}


/* General classes */
body {width: 100% !important; margin: 0; padding: 0; -webkit-text-size-adjust:none; -ms-text-size-adjust:100%; font-size:13px; color:#333333; font-family: helvetica neue, helvetica, arial, verdana, san-serif; }
img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic; border: none;}
.bodytemplate, td { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; mso-line-height-rule:exactly }
.bodytemplate { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; }
.bodytemplate a, .bodytemplate a:hover { color: #F48C00; text-decoration: underline; }
.main_table td{font-size:14px; color:#333333; text-align:left; font-family: helvetica neue, helvetica, arial, verdana, san-serif; padding:5px 10px;}

<!-- NOTE: Remove this css-code to make te email scalable instead of responsive -->
/* Smartphones (portrait and landscape) ----------- */
@media only screen and (max-width:800px) {
*[class=mHide] {display: none !important;}
*[class=mWidth100] {width:100% !important; max-width: 100% !important;}
*[class=mPaddingbottom] {padding-bottom:12px !important;}
*[class=mGmail] {width:100% !important; min-width: 100% !important; padding: 0 10px !important;}
*[class=titeloranje] {-webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; background-color:#F48C00;}
*[class=nieuwsbrieftitel] { padding-top: 20px !important;}
*[class=openhtml] { padding: 10px !important; }
}
<!-- NOTE: End CSS-code to remove if scalable email -->
</style>
</head>
<body bgcolor="#ffffff">
<table bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" width="100%" style="table-layout: fixed">
  <tbody>
    <tr>
      <td class="bodytemplate" align="center" valign="top">
	  <table align="center" class="mGmail" style="width:800px; min-width:800px;  " cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
          <tbody>
            <tr>
              <td style="width:800px; max-width:800px; padding-bottom:20px;">
				
			  </td>
            </tr>
			 <tr>
			    <td>
				<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
									Hi $$firstname$$,
								  </td>
								</tr>
							  </tbody>
							</table>

							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
										Your home campus change request for [$$student_firstname$$ $$student_lastname$$] has been denied.
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
										<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
										If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href="mailto:$$Support_Helpdesk_Email_Address$$" class="external-link" rel="nofollow">$$Support_Helpdesk_Email_Address$$</a>
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
									Thank you from the Skyfactor Mapworks team.</br>
									<img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/ ><br/>
									This email is an auto-generated message. Replies to automated messages are not monitored.
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					
			    </td>
			</tr>
          </tbody>
        </table>
		</td>
    </tr>
    <tr>
      <td>
		
	  </td>
    </tr>
  </tbody>
</table>
</body>
</html>' WHERE `email_template_id`=@emtid;

/*Activate Email*/
SET @emtid := (SELECT id FROM email_template where email_key='Activate_Email');

UPDATE `synapse`.`email_template_lang` SET `body`='<!-- Versie 2.0 !-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<!--[if gte mso 9]>
<xml>
<o:OfficeDocumentSettings>
<o:AllowPNG/>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
<![endif]--> 
<title>MAP-Works Faculty Invitation</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<style type="text/css">
/* e-mail bugfixes */
#outlook a {padding: 0 0 0 0;}
.ReadMsgBody {width: 100%;}
.ExternalClass {width: 100%; line-height: 100%;}
.ExternalClass * {line-height: 100%}
sup, sub {vertical-align: baseline; position: relative; top: -0.4em;}
sub {top: 0.4em;}
.applelinks a {color:#262727; text-decoration: none;}


/* General classes */
body {width: 100% !important; margin: 0; padding: 0; -webkit-text-size-adjust:none; -ms-text-size-adjust:100%; font-size:13px; color:#333333; font-family: helvetica neue, helvetica, arial, verdana, san-serif; }
img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic; border: none;}
.bodytemplate, td { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; mso-line-height-rule:exactly }
.bodytemplate { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; }
.bodytemplate a, .bodytemplate a:hover { color: #F48C00; text-decoration: underline; }
.main_table td{font-size:14px; color:#333333; text-align:left; font-family: helvetica neue, helvetica, arial, verdana, san-serif; padding:5px 10px;}

<!-- NOTE: Remove this css-code to make te email scalable instead of responsive -->
/* Smartphones (portrait and landscape) ----------- */
@media only screen and (max-width:800px) {
*[class=mHide] {display: none !important;}
*[class=mWidth100] {width:100% !important; max-width: 100% !important;}
*[class=mPaddingbottom] {padding-bottom:12px !important;}
*[class=mGmail] {width:100% !important; min-width: 100% !important; padding: 0 10px !important;}
*[class=titeloranje] {-webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; background-color:#F48C00;}
*[class=nieuwsbrieftitel] { padding-top: 20px !important;}
*[class=openhtml] { padding: 10px !important; }
}
<!-- NOTE: End CSS-code to remove if scalable email -->
</style>
</head>
<body bgcolor="#ffffff">
<table bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" width="100%" style="table-layout: fixed">
  <tbody>
    <tr>
      <td class="bodytemplate" align="center" valign="top">
	  <table align="center" class="mGmail" style="width:800px; min-width:800px;  " cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
          <tbody>
            <tr>
              <td style="width:800px; max-width:800px; padding-bottom:20px;">
				
			  </td>
            </tr>
			 <tr>
			    <td>
				<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
									Hi $$firstname$$ ,
								  </td>
								</tr>
							  </tbody>
							</table>

							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
										Your Mapworks accounts have been merged, and this email has been set as your master account address and log-in. You may use the link below to reset your password and resume using Mapworks.
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
										<a href="$$Coordinator_ResetPwd_URL_Prefix$$">[password reset link here]</a>
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
										If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href="mailto:$$Support_Helpdesk_Email_Address$$" class="external-link" rel="nofollow">$$Support_Helpdesk_Email_Address$$</a>
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
									Thank you from the Skyfactor Mapworks team.</br>
								<img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/ ><br/>
									This email is an auto-generated message. Replies to automated messages are not monitored.
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					
			    </td>
			</tr>
          </tbody>
        </table>
		</td>
    </tr>
    <tr>
      <td>
		
	  </td>
    </tr>
  </tbody>
</table>
</body>
</html>' WHERE `email_template_id`=@emtid;

/* Deactivate Email */
SET @emtid := (SELECT id FROM email_template where email_key='Deactivate_Email');

UPDATE `synapse`.`email_template_lang` SET `body`='<!-- Versie 2.0 !-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<!--[if gte mso 9]>
<xml>
<o:OfficeDocumentSettings>
<o:AllowPNG/>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
<![endif]--> 
<title>MAP-Works Faculty Invitation</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<style type="text/css">
/* e-mail bugfixes */
#outlook a {padding: 0 0 0 0;}
.ReadMsgBody {width: 100%;}
.ExternalClass {width: 100%; line-height: 100%;}
.ExternalClass * {line-height: 100%}
sup, sub {vertical-align: baseline; position: relative; top: -0.4em;}
sub {top: 0.4em;}
.applelinks a {color:#262727; text-decoration: none;}


/* General classes */
body {width: 100% !important; margin: 0; padding: 0; -webkit-text-size-adjust:none; -ms-text-size-adjust:100%; font-size:13px; color:#333333; font-family: helvetica neue, helvetica, arial, verdana, san-serif; }
img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic; border: none;}
.bodytemplate, td { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; mso-line-height-rule:exactly }
.bodytemplate { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; }
.bodytemplate a, .bodytemplate a:hover { color: #F48C00; text-decoration: underline; }
.main_table td{font-size:14px; color:#333333; text-align:left; font-family: helvetica neue, helvetica, arial, verdana, san-serif; padding:5px 10px;}

<!-- NOTE: Remove this css-code to make te email scalable instead of responsive -->
/* Smartphones (portrait and landscape) ----------- */
@media only screen and (max-width:800px) {
*[class=mHide] {display: none !important;}
*[class=mWidth100] {width:100% !important; max-width: 100% !important;}
*[class=mPaddingbottom] {padding-bottom:12px !important;}
*[class=mGmail] {width:100% !important; min-width: 100% !important; padding: 0 10px !important;}
*[class=titeloranje] {-webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; background-color:#F48C00;}
*[class=nieuwsbrieftitel] { padding-top: 20px !important;}
*[class=openhtml] { padding: 10px !important; }
}
<!-- NOTE: End CSS-code to remove if scalable email -->
</style>
</head>
<body bgcolor="#ffffff">
<table bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" width="100%" style="table-layout: fixed">
  <tbody>
    <tr>
      <td class="bodytemplate" align="center" valign="top">
	  <table align="center" class="mGmail" style="width:800px; min-width:800px;  " cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
          <tbody>
            <tr>
              <td style="width:800px; max-width:800px; padding-bottom:20px;">
				
			  </td>
            </tr>
			 <tr>
			    <td>
				<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
									Hi $$firstname$$,
								  </td>
								</tr>
							  </tbody>
							</table>

							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
										Your Mapworks accounts have been merged, and this account address has been deactivated. You will receive an email notification at your main account email address with instructions to reset your password.
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
										If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href="mailto:$$Support_Helpdesk_Email_Address$$" class="external-link" rel="nofollow">$$Support_Helpdesk_Email_Address$$</a>
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					<!--Paragraph content---->
					<table cellpadding="0" cellspacing="0" border="0">
					  <tbody>
						<tr>
						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
							  <tbody>
								<tr>
								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
									Thank you from the Skyfactor Mapworks team.</br>
									<img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/ ><br/>
									This email is an auto-generated message. Replies to automated messages are not monitored.
								  </td>
								</tr>
							  </tbody>
							</table>
							</td>
						</tr>
					  </tbody>
					</table>
					
			    </td>
			</tr>
          </tbody>
        </table>
		</td>
    </tr>
    <tr>
      <td>
		
	  </td>
    </tr>
  </tbody>
</table>
</body>
</html>' WHERE `email_template_id`=@emtid;

/* MC Stories - Updated Email Template Ends Here */

/* 
ESPRJ 3490 
Date : 01/06/2015 
Author : Harisudhakar Govindaraju
*/

SET @emtid := (SELECT id FROM email_template where email_key='Forgot_Password_Staff');
UPDATE `synapse`.`email_template_lang` SET `subject`='How to reset your Mapworks password' WHERE `email_template_id`=@emtid;
UPDATE `synapse`.`email_template_lang` SET `body`='<html>
<div style="margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);">
Hi $$firstname$$,<br/></br>
 
Please use the link below and follow the displayed instructions to create your new password. This link will expire after $$Reset_Password_Expiry_Hrs$$ hours.<br />
<br/>
$$activation_token$$<br/><br/>
 
If you believe that you received this email in error or if you have any questions,please contact Mapworks support at <span style="color: #99ccff;">$$Support_Helpdesk_Email_Address$$</span>.<br/><br/>
<p>Thank you from the Mapworks team.</br></p>
<p><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
</div>
</html>' WHERE `email_template_id`=@emtid;


SET @emtid := (SELECT id FROM email_template where email_key='Forgot_Password_Coordinator');
UPDATE `synapse`.`email_template_lang` SET `subject`='How to reset your Mapworks password' WHERE `email_template_id`=@emtid;
UPDATE `synapse`.`email_template_lang` SET `body`='<html>
<div style="margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);">
Hi $$firstname$$,<br/></br>
 
Please use the link below and follow the displayed instructions to create your new password. This link will expire after $$Reset_Password_Expiry_Hrs$$ hours.<br />
<br/>
$$activation_token$$<br/><br/>
 
If you believe that you received this email in error or if you have any questions,please contact Mapworks support at <span style="color: #99ccff;">$$Support_Helpdesk_Email_Address$$</span>.<br/><br/>
<p>Thank you from the Mapworks team.</br></p>
<p><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
</div>
</html>' WHERE `email_template_id`=@emtid;

/* 
ESPRJ 3491 
Date : 01/06/2015 
Author : Harisudhakar Govindaraju
*/
SET @emtid := (SELECT id FROM email_template where email_key='Sucessful_Password_Reset_Staff');
UPDATE `synapse`.`email_template_lang` SET `body`='<html>
 <div style="margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);">
 Hi $$firstname$$,<br/><br/>
 
 Your Mapworks password has been changed. If this was not you or you believe this is an error, please contact Mapworks support at &nbsp;<a class="external-link" href="mailto:support@mapworks.com" rel="nofollow" style="color: rgb(41, 114, 155); text-decoration: none;">support@mapworks.com</a>
 <br/><br/>
<p>Thank you from the Mapworks team.</br></p>
<p><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
				
 </div>
 </html>' WHERE `email_template_id`=@emtid;


SET @emtid := (SELECT id FROM email_template where email_key='Sucessful_Password_Reset_Coordinator');
UPDATE `synapse`.`email_template_lang` SET `body`='<html>
 <div style="margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);">
 Hi $$firstname$$,<br/><br/>
 
 Your Mapworks password has been changed. If this was not you or you believe this is an error, please contact Mapworks support at &nbsp;<a class="external-link" href="mailto:support@mapworks.com" rel="nofollow" style="color: rgb(41, 114, 155); text-decoration: none;">support@mapworks.com</a>
 <br/><br/>
<p>Thank you from the Mapworks team.</br></p>
<p><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
				
 </div>
 </html>' WHERE `email_template_id`=@emtid;
 
/* 
* Fix For ESPRJ 3440 
*/
SET @Final_Grade_Of_C_Or_Below := (select id from  ebi_search where query_key ='Final_Grade_Of_C_Or_Below');
UPDATE `ebi_search_lang` SET `ebi_search_id` = @Final_Grade_Of_C_Or_Below WHERE `sub_category_name` = 'Final grade of C or below';
