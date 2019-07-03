#Date: 2018-03-28
#Author:Victor Belt
#This is a capture of some ad hoc changes for ENV Refreshes that are not within the main DataDeidentification script.

-- -------------------------------------------------
-- UPLOADS
-- -------------------------------------------------
update synapse.upload_file_log set viewed = 1 where viewed <> 1 or viewed is null;


-- -- -------------------------------------------------
-- -- ORG Campus Resource (REDUNDANT)
-- -- -------------------------------------------------
-- -- 		UPDATE
-- -- 		    synapse.org_campus_resource ocr
-- -- 		        JOIN
-- -- 		    synapse.person p ON p.id = ocr.person_id
-- -- 		SET ocr.email = p.username
-- -- 		WHERE
-- -- 		    p.deleted_at IS NULL;
-- --

-- -------------------------------------------------
-- OPSSL (REDUNDANT)
-- -------------------------------------------------
-- -- UPDATE synapse.org_person_student_survey_link SET survey_link = 'http://skyfactor.com/' WHERE survey_link IS NOT NULL;


-- -------------------------------------------------
-- SAML/LDAP
-- -------------------------------------------------
Update synapse.person set auth_username = NULL where organization_id not in (-1, 2, 195, 217);
-- 		Org_ldap_config
Delete from synapse.org_ldap_config where org_id not in (-1, 2, 195, 217);
-- 		Org_saml_config
Delete from synapse.org_saml_config where org_id not in (-1, 2, 195, 217);
-- 		Org_auth_config
Delete from synapse.org_auth_config where org_id not in (-1, 2, 195, 217);

-- -------------------------------------------------
-- FTP
-- -------------------------------------------------
-- SANDBOX - ftp{orgId) -> ftp{orgId}-sandbox
-- update synapse.organization set ftp_user = concat(ftp_user,'-sandbox');
update synapse.organization set ftp_user = (concat(ftp_user,'-{env}'));

-- 	NON-Sandbox-FTP:
update synapse.organization set ftp_password = NULL;


-- -------------------------------------------------
-- CRONOFY SETTINGS
-- -------------------------------------------------
-- Calendar Configs will be completely invalid in lower envs since they target a different instance so might as well truncate
-- 3/30/2018 - truncating but is there value in having the dataset?
truncate synapse.org_cronofy_calendar;
truncate synapse.org_cronofy_history;

-- Deactivate Calendar Sync at Organization Level
update synapse.organization set calendar_sync = 0, modified_at = current_timestamp(), modified_by = 4 where calendar_sync = 1;


-- -------------------------------------------------
-- Notifications?
-- -------------------------------------------------
-- TO DO???
-- Clear out channels?
-- 3/30/2018 - clearing out but not sure this is best in the future? However de-id of recipient and bcc list might be tricky.
delete from synapse.notification_log where organization_id not in (-1, 2, 195, 217);



-- -------------------------------------------------
-- !!!FUTURE IMPROVEMENTS!!!
-- -------------------------------------------------
-- etc...
-- access_log? -- probably not
-- I'm sure there's others though but deadline is upon us.
-- Into the future!


-- -------------------------------------------------
-- SYSTEM STUFF
-- -------------------------------------------------
-- Pull from ENV that is being refreshed and replace

--  David.Warner@gmail PW change for lower ENVs
update synapse.person set password = '$2y$13$LJGuD/SDqWbcLmoplF1.s.VrNY4Id6apcYUaw09UeeTFNR3U0danS' where username = 'david.warner@gmail.com';

-- 	EBI_CONFIG (system table)
-- 	Mapworks_tools (system table) -- 3/30/2018 - Only in QA at this time.

