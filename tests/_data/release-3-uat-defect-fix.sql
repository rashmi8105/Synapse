/*
-- Release-3 UAT defect fix queries
*/

/*
-- ESPRJ-1204 issue fix
*/

UPDATE `synapse`.`ebi_search` SET `query`='select r.id as \'referral_id\',r.person_id_student, p.firstname, p.lastname, p.risk_level, p.risk_model_id, p.intent_to_leave,rml.image_name, rml.risk_text,lc.cnt as login_cnt, p.cohert, p.last_activity  FROM referrals r  join person p on (r.person_id_student = p.id ) LEFT join risk_model_levels rml on (p.risk_model_id = rml.risk_model_id and p.risk_level = rml.risk_level) left outer join Logins_count lc on (lc.person_id = r.person_id_student)   where  r.deleted_at IS NULL AND (r.status = \'O\' or r.status=\'R\') and r.person_id_faculty = $$personid$$' WHERE `query_key`='My_Open_Referrals_Sent_List';

UPDATE `synapse`.`ebi_search` SET `query`='select r.id as \'referral_id\',r.person_id_student, p.firstname,p.lastname, p.risk_level,p.risk_model_id,p.intent_to_leave,rml.image_name,rml.risk_text,lc.cnt as login_cnt,p.cohert,p.last_activity FROM referrals r join person p on (r.person_id_student = p.id ) left join risk_model_levels rml on (p.risk_model_id = rml.risk_model_id and p.risk_level = rml.risk_level) left outer join Logins_count lc on (lc.person_id = r.person_id_student) where  r.deleted_at IS NULL AND r.status = \'O\' and r.person_id_assigned_to = $$personid$$' WHERE `query_key`='My_Open_Referrals_Received_List';

/*
* -- ESPRJ-600 defect fix
*/

UPDATE `synapse`.`email_template_lang` SET `body`='<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\n<head>\n    <title>Email</title>\n</head>\n<body>\n<center>\n    <table align=\"center\">\n        <tr>\n            <th style=\"padding:0px;margin:0px;\">\n               \n               <table  style=\"font-family:helvetica,arial,verdana,san-serif;font-weight:normal;width:800px; height=337px;text-align:center;padding:0px;\">\n               <tr bgcolor=\"#eeeeee\" style=\"width:800px;padding:0px;height:337px;\">\n               <td style=\"width:800px;padding:0px;height:337px;\">\n               <table style=\"text-align:center;width:100%\">\n               <tr>\n                    <td style=\"padding:0px;\">\n                    <table style=\"margin-top:56px;width:100%\">\n		<tr>\n		<td style=\"text-align:center;padding:0px;font-size:33px;height:80px;width:800px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;color:#000000\">\n					<br>Welcome to MAP-Works.		\n		</td>\n		</tr>\n		</table>\n                    </td>\n               </tr>\n               <tr style=\"margin:0px;padding:0px;\">\n		<td style=\"text-align:center;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;color:#333333;font-size: 16px;height:16px;padding-top:8px;\">\n        			Use the link below to create your password and start using MAP-Works.\n		\n		</td></tr>\n           <tr style=\"margin:0px;padding:0px;\"><td style=\"margin:0px;padding:0px;\">\n        \n<table align=\"center\"> \n  <tr style=\"margin:0px;padding:0px;\">\n    <th style=\"margin:0px;padding:0px;\">\n           <table cellpadding=\"36\" style=\"width:100%\">\n        <tr>\n		<td align=\"center\" style=\"text-align:center;color:#000000;font-weight:normal;font-size: 20px;\">		\n		          <table style=\"border-radius:2px;width:175px;font-size:20px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;text-align:center;display: block;margin: 0 auto;padding:0px 0px\">\n		<tr>\n        <td style=\"background-color:#4673a7; height:58px;border-radius:2px;line-height:21px;text-decoration:none ;vertical-align:middle;\">        \n        <a href=\"$$activation_token$$\" style=\"outline-offset:19px !important;background-color: #4673a7; color: #ffffff;display: block;text-decoration: none;width:175px \"target=\"_blank\"><span style=\"text-decoration: none !important;\">Sign In Now</span></a>\n        </td></tr>\n        <tr valign=\"top\" style=\"height:33px;\">\n        <td style=\"margin-left:auto; margin-right:auto;width:100%;font-size: 14px;height:14px;padding-bottom:7px;font-family:helvetica,arial,verdana,san-serif;font-weight:medium;color:#333333;link:#1e73d5;padding-top:8px;\">       \n				<span>Use this link to <a target=\"_blank\" style=\"link:#1e73d5;\" href=\"$$activation_token$$\">sign in.</a></span>  \n			\n        </td></tr>\n        \n        </table>\n		</td></tr>\n        \n        </table>\n        </th>\n    \n  </tr>\n \n</table>\n       </td></tr>\n</table>\n               </td>\n               </tr>\n               <tr valign=\"top\">\n<td >\n<table>\n<tr>\n<td valign=\"top\" align=\"center\">\n<div style=\"text-align:left;margin-left:30px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;\n			margin-right:18px;font-size: 13px;color: #333333;margin-top:30px;link:#1e73d5;font-weight:normal;\" >\n				Thank you for participating in the spring 2015 pilot. We look forward to hearing your feedback as\n				it will inform future releases of our new student retention and success solution.\n				\n				<br><br>\n				If you have any questions, please contact us here.<br>\n				<a href=\"mailto:$$Support_Helpdesk_Email_Address$$\" style=\"link:#1e73d5;\">$$Support_Helpdesk_Email_Address$$</a> \n				<br><br>\n				Sincerely,\n				<div style=\"text-align:left;font-weight:bold;font-size: 14px;color:#333333\" >\n					<b>The EBI MAP-Works Client Services Team</b> \n					\n				</div>\n                </div>\n</td>\n</tr>\n</table>\n</td>\n</tr>\n               </table>\n               \n            </th>\n            \n        </tr>\n        \n    </table>\n    </center>\n</body>\n</html>\n' WHERE `id`='4';

/*
* - Fix for staff - student permission on details page
*/

UPDATE `synapse`.`ebi_search` SET `query`='select dml.datablock_desc as blockdesc,mml.meta_name,pm.metadata_value as myanswer from datablock_master dm join datablock_master_lang dml ON dm.id = dml.datablock_id JOIN  datablock_metadata dmd ON dmd.datablock_id = dm.id JOIN metadata_master mm ON dmd.ebi_metadata_id = mm.id JOIN metadata_master_lang mml ON mml.metadata_id = mm.id JOIN person_metadata pm ON pm.metadata_id = mm.id  where mml.lang_id=$$lang$$ AND dm.block_type=\"profile\" AND pm.person_id = $$studentid$$ AND dm.id IN($$datablockpermission$$) AND mm.deleted_at IS NULL AND dm.deleted_at IS NULL' WHERE `query_key`='Student_Profile_Datablock_Info';


/*
* Team activities query fix - ESPRJ-1228 & 1229
*/
#My_Team_Interactions_count_Groupby_Teams

UPDATE `synapse`.`ebi_search` SET `query`='select tm.teams_id, t.team_name, count(al.id) as numbers, \'interaction\' as activity from Teams t, team_members tm, activity_log al where tm.teams_id = t.id and tm.organization_id = t.organization_id and al.organization_id = tm.organization_id and al.person_id_faculty = tm.person_id and al.activity_type in (\'R\',\'C\',\'N\',\'A\') and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and tm.teams_id in (SELECT teams_id FROM team_members where is_team_leader = 1 and person_id = \'$$loggedUserId$$\'  and deleted_at IS NULL) and tm.organization_id = \'$$organizationId$$\' and t.deleted_at IS NULL and tm.deleted_at IS NULL and al.deleted_at IS NULL group by t.team_name' WHERE `query_key`='My_Team_Interactions_count_Groupby_Teams';


#My_Team_Open_Referrals_count_Groupby_Teams
UPDATE `synapse`.`ebi_search` SET `query`='select tm.teams_id, t.team_name, count(al.id) as numbers, \'openreferrals\' as activity from Teams t, team_members tm, activity_log al, referrals r where tm.teams_id = t.id and tm.organization_id = t.organization_id and al.organization_id = tm.organization_id and al.person_id_faculty = tm.person_id and al.activity_type = \'R\' and r.id = al.referrals_id and r.status = \'O\' and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and tm.teams_id in (SELECT teams_id FROM team_members where is_team_leader = 1 and person_id = \'$$loggedUserId$$\' and deleted_at IS NULL) and tm.organization_id = \'$$organizationId$$\' and t.deleted_at IS NULL and tm.deleted_at IS NULL and al.deleted_at IS NULL group by t.team_name' WHERE `query_key`='My_Team_Open_Referrals_count_Groupby_Teams';

#My_Team_Logins_Count_Groupby_Teams
UPDATE `synapse`.`ebi_search` SET `query`='select tm.teams_id, t.team_name, count(al.id) as numbers, \'logins\' as activity from Teams t, team_members tm, activity_log al where tm.teams_id = t.id and tm.organization_id = t.organization_id and al.organization_id = tm.organization_id and al.person_id_faculty = tm.person_id and al.activity_type = \'L\' and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and tm.teams_id in (SELECT teams_id FROM team_members where is_team_leader = 1 and person_id = \'$$loggedUserId$$\' and deleted_at IS NULL) and tm.organization_id = \'$$organizationId$$\' and t.deleted_at IS NULL and tm.deleted_at IS NULL and al.deleted_at IS NULL group by t.team_name' WHERE `query_key`='My_Team_Logins_Count_Groupby_Teams';


/*
* Fix for ESPRJ-1365
*/
UPDATE `synapse`.`risk_model_levels` SET `risk_text`='No risk' WHERE `id`='5';


/*
* Fix for ESPRJ-1030
*/
UPDATE `synapse`.`email_template_lang` SET `body`='<html>\r\n	<head>\r\n		<style>\r\n		body {\r\n    background: none repeat scroll 0 0 #f4f4f4;\r\n	\r\n}\r\n		table {\r\n    padding: 21px;\r\n    width: 799px;\r\n	font-family: helvetica,arial,verdana,san-serif;\r\n	font-size:13px;\r\n	color:#333;\r\n	}\r\n		</style>\r\n	</head>\r\n	<body>\r\n	\r\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r\n			<tbody>\r\n			\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi $$firstname$$:</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A referral was recently assigned to you in MAP-Works. Please sign in to your account to view and take action on this referral.</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Mapworks team!</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r\n			\r\n			</tbody>\r\n		</table>\r\n	</body>\r\n</html>', `subject`='You have a new Mapworks referral\r\n\r\n' WHERE `id`='14';


/*
* Fix for ESPRJ-228
*/
UPDATE `synapse`.`ebi_search` SET `query`='select p.id,p.firstname, p.lastname, p.risk_level, p.risk_model_id, p.intent_to_leave, rml.image_name, rml.risk_text, lc.cnt as login_cnt, p.cohert, p.last_activity from person p join risk_model_levels rml on (p.risk_model_id = rml.risk_model_id and p.risk_level = rml.risk_level) left outer join Logins_count lc on (lc.person_id = p.id) where p.id in (select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = $$personId$$ and deleted_at is  null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null)) and ogs.deleted_at is null) and p.deleted_at is null and rml.risk_text = \'$$riskLevel$$\' ' WHERE `query_key`='My_Total_students_List_By_RiskLevel';



/*
* Fix for ESPRJ 1389 & ESPRJ-1410
*/
DELETE FROM `synapse`.`risk_model_levels` WHERE `id`='5';

UPDATE `synapse`.`ebi_search` SET `query`='select p.id,p.firstname, p.lastname, p.risk_level, p.risk_model_id,p.intent_to_leave, rml.image_name, rml.risk_text, lc.cnt as login_cnt, p.cohert, p.last_activity from person p join risk_model_levels rml on (p.risk_model_id = rml.risk_model_id and p.risk_level = rml.risk_level) left outer join Logins_count lc on (lc.person_id = p.id) where p.id in (select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = $$personId$$ and deleted_at is null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null)) and ogs.deleted_at is null) and p.risk_level in (1,2,3,4) and p.deleted_at is null' WHERE `query_key`='My_Total_students_List';

/* Priority Students -  removed hard code risk levels and handled in the ebi config */

UPDATE `synapse`.`ebi_search` SET `query`='select count(per.id) as highCount from person per where per.id in (select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = $$personId$$ and deleted_at is  null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null)) and ogs.deleted_at is null) and per.last_contact_date < per.risk_update_date and per.risk_level in ($$risklevel$$) and per.deleted_at is null' WHERE `query_key`='My_High_priority_students_Count';


UPDATE `synapse`.`ebi_search` SET `query`='select p.id,p.firstname, p.lastname, p.risk_level, p.risk_model_id, p.intent_to_leave, rml.image_name, rml.risk_text, lc.cnt as login_cnt, p.cohert, p.last_activity from person p join risk_model_levels rml on (p.risk_model_id = rml.risk_model_id and p.risk_level = rml.risk_level) left outer join Logins_count lc on (lc.person_id = p.id) where p.id in (select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = $$personId$$ and deleted_at is  null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null)) and ogs.deleted_at is null) and p.last_contact_date < p.risk_update_date and p.risk_level in ($$risklevel$$) and p.deleted_at is null' WHERE `query_key`='My_High_priority_students_List';



UPDATE `synapse`.`ebi_search` SET `query`='select p.id,p.firstname, p.lastname, p.risk_level, p.risk_model_id,p.intent_to_leave, rml.image_name, rml.risk_text, lc.cnt as login_cnt, p.cohert, p.last_activity from person p join risk_model_levels rml on (p.risk_model_id = rml.risk_model_id and p.risk_level = rml.risk_level) left outer join Logins_count lc on (lc.person_id = p.id) where p.id in (select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = $$personId$$ and deleted_at is null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null)) and ogs.deleted_at is null) and p.risk_level in ($$risklevel$$) and p.deleted_at is null order by p.risk_level desc ,p.lastname,p.firstname' WHERE `query_key`='My_Total_students_List';

INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('HIGH_RISK_DEFINITION_RISK_LEVEL', '1,2');
INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('TOTALSTUDENTS_RISK_LEVEL', '1,2,3,4');

/* Priority Students -  removed hard code risk levels and handled in the ebi config */


/*
* Fix for ESPRJ 1373 : 4 Feb 2014 by Subash
*/
UPDATE `synapse`.`ebi_search` SET `query`='select p.id,p.firstname, p.lastname, p.risk_level, p.risk_model_id,p.intent_to_leave, rml.image_name, rml.risk_text, lc.cnt as login_cnt, p.cohert, p.last_activity from person p join risk_model_levels rml on (p.risk_model_id = rml.risk_model_id and p.risk_level = rml.risk_level) left outer join Logins_count lc on (lc.person_id = p.id) where p.id in (select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = $$personId$$ and deleted_at is null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null)) and ogs.deleted_at is null) and p.risk_level in ($$risklevel$$) and p.deleted_at is null order by p.risk_level desc ,p.lastname,p.firstname' WHERE `query_key`='My_Total_students_List';



/*
* No Jira tciket - Appointment Reminder email dashboard url change
* 4 Feb 2014 by Subash
*/

UPDATE `synapse`.`email_template_lang` SET `body`='<html>\n    <head>\n        <style>\n			 body {\n				background: none repeat scroll 0 0;	\n			\n			}\n			table {\n				padding: 21px;\n				width: 799px;\n				font-family: Helvetica,Arial,Verdana,San-serif;\n				font-size:13px;\n				color:#333;\n			}\n   </style>\n    </head>\n    <body>\n        <table cellpadding=\"10\" cellspacing=\"0\">\n            <tbody>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td>Dear $$student_name$$:</td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td style=\"line-height: 1.6;\">This is a reminder that you have an appointment with $$staff_name$$ on $$app_datetime$$. <br/><br/> To view the appointment details, please log in to your MAP-Works dashboard and visit\n					<a style=\"color: #0033CC;\" href=\"$$student_dashboard$$\">Mapworks student dashboard view appointment module</a>.\n					</td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td>Best regards,\n                        <br/>EBI MAP-Works\n                    </td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td><span style=\"font-size:11px; color: #575757; line-height: 120%; text-decoration: none;\">This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</span></td>\n                </tr>\n            </tbody>\n        </table>\n    </body>\n</html>\n' WHERE `id`='15';


/*
-- My_Team_List_with_only_OpenReferrals update to fix multiple record issue - same student be part of more than one group
-- 09/02/2015
*/
UPDATE `synapse`.`ebi_search` SET `query`='select \'openreferrals\' as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id,pa.firstname as team_member_firstname,pa.lastname as team_member_lastname, pa.username as primary_email,al.person_id_student as student_id, (CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.firstname ELSE \'\' END) as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE \'\' END) as student_lastname,\'O\' as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, \'\' as reason_id, al.reason as reason_text,r.status,gs.person_id from activity_log al left join referrals r on r.id = al.referrals_id left outer join org_group_students gs on (al.person_id_student = gs.person_id and gs.org_group_id in (select distinct org_group_id from org_group_faculty where person_id in ($$personId$$) and deleted_at IS NULL )) left join person pa on pa.id = al.person_id_faculty left join person p on p.id = al.person_id_student where al.activity_type in (\'R\') and r.status = \'O\' and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and al.person_id_faculty in ($$teamMemberId$$) and al.deleted_at IS NULL and al.organization_id = \'$$organizationId$$\' group by al.id order by activity_date' WHERE `query_key`='My_Team_List_with_only_OpenReferrals';

/*
* -- 09/02/2015
*/

UPDATE `synapse`.`ebi_search` SET `query`='(select \'openreferrals\' as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id,pa.firstname as team_member_firstname,pa.lastname as team_member_lastname, pa.username as primary_email,al.person_id_student as student_id,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.firstname ELSE \'\' END) as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE \'\' END) as student_lastname,\'O\' as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, \'\' as reason_id, al.reason as reason_text,r.status,gs.person_id from activity_log al left join referrals r on r.id = al.referrals_id left outer join org_group_students gs on (al.person_id_student = gs.person_id and gs.org_group_id in (select distinct org_group_id from org_group_faculty where person_id in ($$personId$$) and deleted_at IS NULL )) left join person pa on pa.id = al.person_id_faculty left join person p on p.id = al.person_id_student where al.activity_type in (\'R\') and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and al.person_id_faculty in ($$teamMemberId$$) and al.deleted_at IS NULL and al.organization_id = \'$$organizationId$$\' group by al.id) union (select \'interactions\' as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id, pa.firstname as team_member_firstname,pa.lastname as team_member_lastname, pa.username as primary_email,al.person_id_student as student_id,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.firstname ELSE \'\' END) as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE \'\' END) as student_lastname, al.activity_type as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, \'\' as reason_id, al.reason as reason_text,\'\' as status,gs.person_id from activity_log al left outer join org_group_students gs on (al.person_id_student = gs.person_id and gs.org_group_id in (select distinct org_group_id from org_group_faculty where person_id in ($$personId$$) and deleted_at IS NULL )) left join person pa on pa.id = al.person_id_faculty left join person p on p.id = al.person_id_student where al.activity_type in (\'A\',\'C\',\'N\',\'L\') and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and al.person_id_faculty in ($$teamMemberId$$) and al.deleted_at IS NULL and al.organization_id = \'$$organizationId$$\' group by al.id) order by activity_date' WHERE `query_key`='My_Team_List_with_OpenReferrals_and_otherActivities';


UPDATE `synapse`.`ebi_search` SET `query`='select \'interactions\' as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id,pa.firstname as team_member_firstname,pa.lastname as team_member_lastname, pa.username as primary_email,al.person_id_student as student_id, (CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.firstname ELSE \'\' END) as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE \'\' END) as student_lastname,al.activity_type as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, \'\' as reason_id, al.reason as reason_text,r.status,gs.person_id from activity_log al left join referrals r on r.id = al.referrals_id left outer join org_group_students gs on (al.person_id_student = gs.person_id and gs.org_group_id in (select distinct org_group_id from org_group_faculty where person_id in ($$personId$$) and deleted_at IS NULL )) left join person pa on pa.id = al.person_id_faculty left join person p on p.id = al.person_id_student where al.activity_type in (\'A\',\'C\',\'N\',\'R\') and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and al.person_id_faculty in ($$teamMemberId$$) and al.deleted_at IS NULL and al.organization_id = \'$$organizationId$$\'  group by al.id order by activity_date' WHERE `query_key`='My_Team_List_without_Referrals';


/*
* -- 18/02/2015 - ESPRJ-1588 - brajesh
*/
UPDATE `synapse`.`ebi_search` SET `query`='select mm.id,mm.metadata_type,dml.datablock_desc as blockdesc,mml.meta_name,pm.metadata_value as myanswer from datablock_master dm join datablock_master_lang dml ON dm.id = dml.datablock_id JOIN  datablock_metadata dmd ON dmd.datablock_id = dm.id JOIN metadata_master mm ON dmd.ebi_metadata_id = mm.id JOIN metadata_master_lang mml ON mml.metadata_id = mm.id JOIN person_metadata pm ON pm.metadata_id = mm.id  where mml.lang_id=$$lang$$ AND dm.block_type="profile" AND pm.person_id = $$studentid$$ AND dm.id IN($$datablockpermission$$) AND mm.deleted_at IS NULL AND dm.deleted_at IS NULL' WHERE `query_key`='Student_Profile_Datablock_Info';


UPDATE `synapse`.`ebi_search` SET `query`='select mm.id,mm.metadata_type,mml.meta_name,pm.metadata_value as myanswer from  metadata_master mm JOIN metadata_master_lang mml ON mml.metadata_id = mm.id JOIN person_metadata pm ON pm.metadata_id = mm.id  where mml.lang_id=$$lang$$ AND mm.definition_type="O" AND pm.person_id = $$studentid$$ AND mm.deleted_at IS NULL' WHERE `query_key`='Student_Profile_ISP_Info';