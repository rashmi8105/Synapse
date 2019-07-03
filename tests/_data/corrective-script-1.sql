SET SQL_SAFE_UPDATES = 0;



# Admin user id conflict fix
# ------------------------------------------------------------

SET @david_user_id := (select id from person where username = 'david.warner@gmail.com');
DELETE FROM `synapse`.`organization_role` WHERE `person_id`=@david_user_id and organization_id='-1';
DELETE FROM `synapse`.`person_contact_info` WHERE `person_id`=@david_user_id;
DELETE FROM `synapse`.`AccessToken` WHERE `user_id`=@david_user_id;
DELETE FROM `synapse`.`RefreshToken` WHERE `user_id`=@david_user_id;
DELETE FROM `synapse`.`person` WHERE `username`='david.warner@gmail.com';
DELETE FROM `synapse`.`Client` where `random_id` = '14tx5vbsnois4ggg0ok0c4gog8kg0ww488gwkg88044cog4884';
DELETE FROM `synapse`.`contact_info` WHERE `primary_email`='david.warner@gmail.com';

/*
* Include refresh_token as additional grant_type 
*/
UPDATE `synapse`.`Client` SET `allowed_grant_types`='a:2:{i:0;s:8:\"password\";i:1;s:13:\"refresh_token\";}' WHERE `random_id`='382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s';


 /* 
Person david.warner@gmail.com/Mapworks Admin record to be created
*/

INSERT INTO `Client` (`random_id`,`redirect_uris`,`secret`,`allowed_grant_types`) VALUES ('14tx5vbsnois4ggg0ok0c4gog8kg0ww488gwkg88044cog4884','a:0:{}','4v5p8idswhs0404owsws48gwwccc4wksw4c8s80wcocwskockg','a:2:{i:0;s:8:\"password\";i:1;s:13:\"refresh_token\";}');

INSERT INTO `person` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`firstname`,`lastname`,`title`,`date_of_birth`,`external_id`,`username`,`password`,`activation_token`,`confidentiality_stmt_accept_date`,`organization_id`,`token_expiry_date`,`welcome_email_sent_date`,`risk_level`,`risk_update_date`,`intent_to_leave`,`intent_to_leave_update_date`,`last_contact_date`,`last_activity`,`record_type`)
VALUES               (NULL,NULL,NULL,NULL,NULL,NULL,'David','Warner',NULL,NULL,'David123','david.warner@gmail.com','$2y$13$f6bnaUYhaIO0qzJ0krqrIeUDnxJxWYYEyB3L6qDDK/1ln5CsHKEca',NULL,NULL,-1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

SET @roleId := (select role_id from role_lang where role_name = 'Mapworks Admin');
SET @personId := (select id from person where username = 'david.warner@gmail.com');
SET @orgId := (select organization_id from person where username = 'david.warner@gmail.com');
						
INSERT INTO `organization_role` (`role_id`,`person_id`,`organization_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`) VALUES (@roleId,@personId,-1,NULL,NULL,NULL,NULL,NULL,NULL);

INSERT INTO `contact_info` (`address_1`,`address_2`,`city`,`zip`,`state`,`country`,`primary_mobile`,`alternate_mobile`,`home_phone`,`office_phone`,`primary_email`,`alternate_email`,`primary_mobile_provider`,`alternate_mobile_provider`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'9591900663',NULL,NULL,NULL,'david.warner@gmail.com',NULL,'9224852114',NULL,NULL,NULL,NULL,'2014-10-15 12:34:01',NULL,NULL);
SET @contactId := (select max(id) from contact_info);

INSERT INTO `person_contact_info` (`person_id`,`contact_id`,`status`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES (@personId,@contactId,'A',NULL,NULL,NULL,NULL,NULL,NULL);