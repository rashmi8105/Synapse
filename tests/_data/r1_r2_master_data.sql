# Dump of table Client
# ------------------------------------------------------------

LOCK TABLES `Client` WRITE;
/*!40000 ALTER TABLE `Client` DISABLE KEYS */;

INSERT INTO `Client` (`id`, `random_id`, `redirect_uris`, `secret`, `allowed_grant_types`)
VALUES
	(1,'382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s','a:0:{}','3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480','a:1:{i:0;s:8:\"password\";}');

/*!40000 ALTER TABLE `Client` ENABLE KEYS */;
UNLOCK TABLES;

# Dump of table language_master
# ------------------------------------------------------------

LOCK TABLES `language_master` WRITE;
/*!40000 ALTER TABLE `language_master` DISABLE KEYS */;

INSERT INTO `language_master` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `langcode`, `langdescription`, `issystemdefault`)
VALUES
	(1,NULL,NULL,NULL,NULL,NULL,NULL,'en_US','US English',1);

/*!40000 ALTER TABLE `language_master` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table role
# ------------------------------------------------------------

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;

INSERT INTO `role` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `status`)
VALUES
	(1,NULL,NULL,NULL,NULL,NULL,NULL,'A'),
	(2,NULL,NULL,NULL,NULL,NULL,NULL,'A'),
	(3,NULL,NULL,NULL,NULL,NULL,NULL,'A');

/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table role_lang
# ------------------------------------------------------------

LOCK TABLES `role_lang` WRITE;
/*!40000 ALTER TABLE `role_lang` DISABLE KEYS */;

INSERT INTO `role_lang` (`id`, `role_id`, `lang_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `role_name`)
VALUES
	(1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Primary coordinator'),
	(2,2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Technical coordinator'),
	(3,3,1,NULL,NULL,NULL,NULL,NULL,NULL,'Non Technical coordinator');

/*!40000 ALTER TABLE `role_lang` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table activity_reference
# ------------------------------------------------------------

LOCK TABLES `activity_reference` WRITE;
/*!40000 ALTER TABLE `activity_reference` DISABLE KEYS */;

INSERT INTO `activity_reference` (`id`, `short_name`, `is_active`, `display_seq`)
VALUES
	(1,'Academic Concerns',NULL,NULL),
	(2,'Personal Issue',NULL,NULL),
	(3,'Financial Issues',NULL,NULL);

/*!40000 ALTER TABLE `activity_reference` ENABLE KEYS */;
UNLOCK TABLES;

# Dump of table activity_reference_lang
# ------------------------------------------------------------

LOCK TABLES `activity_reference_lang` WRITE;
/*!40000 ALTER TABLE `activity_reference_lang` DISABLE KEYS */;

INSERT INTO `activity_reference_lang` (`id`, `language_master_id`, `activity_reference_id`, `heading`, `description`)
VALUES
	(1,1,1,'Academic Concerns',NULL),
	(2,1,2,'Personal Issue',NULL),
	(3,1,3,'Financial Issues',NULL);

/*!40000 ALTER TABLE `activity_reference_lang` ENABLE KEYS */;
UNLOCK TABLES;

# Dump of table ebi_config
# ------------------------------------------------------------

LOCK TABLES `ebi_config` WRITE;
/*!40000 ALTER TABLE `ebi_config` DISABLE KEYS */;

INSERT INTO `ebi_config` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `key`, `value`)
VALUES
	(1,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_First_Password_Expiry_Hrs','0'),
	(2,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Support_Helpdesk_Email_Address','support@map-works.com'),
	(3,NULL,NULL,NULL,NULL,NULL,NULL,'Staff_Support_Helpdesk_Email_Address','support@map-works.com'),
	(4,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activation_URL_Prefix','http://mapworks-beta.skyfactor.com/#/activatecoordinator/'),
	(5,NULL,NULL,NULL,NULL,NULL,NULL,'Staff_Activation_URL_Prefix','http://mapworks-beta.skyfactor.com/#/activate/'),
	(6,NULL,NULL,NULL,NULL,NULL,NULL,'Staff_First_Password_Expiry_Hrs','24'),
	(7,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Reset_Password_Expiry_Hrs','24'),
	(8,NULL,NULL,NULL,NULL,NULL,NULL,'Staff_Reset_Password_Expiry_Hrs','24'),
	(9,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Reset_Password_Expiry_Hrs','24'),
	(10,NULL,NULL,NULL,NULL,NULL,NULL,'System_URL','http://mapworks-beta.skyfactor.com/'),
	(11,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_ResetPwd_URL_Prefix','http://mapworks-beta.skyfactor.com/#/resetPassword/'),
	(12,NULL,NULL,NULL,NULL,NULL,NULL,'Staff_ResetPwd_URL_Prefix','http://mapworks-beta.skyfactor.com/#/resetPassword/'),
	(13,NULL,NULL,NULL,NULL,NULL,NULL,'StaffDashboard_AppointmentPage','http://mapworks-beta.skyfactor.com/#/stff');

/*!40000 ALTER TABLE `ebi_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table entity
# ------------------------------------------------------------

LOCK TABLES `entity` WRITE;
/*!40000 ALTER TABLE `entity` DISABLE KEYS */;

INSERT INTO `entity` (`id`, `entity_name`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(1,'organization',NULL,NULL,NULL,NULL,NULL,NULL),
	(2,'Student',NULL,NULL,NULL,NULL,NULL,NULL),
	(3,'Faculty',NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `entity` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table organization
# ------------------------------------------------------------

LOCK TABLES `organization` WRITE;
/*!40000 ALTER TABLE `organization` DISABLE KEYS */;

INSERT INTO `organization` (`id`,  `subdomain`, `status`, `website`, `parent_organization_id`, `time_zone`, `logo_file_name`, `primary_color`, `secondary_color`, `ebi_confidentiality_statement`, `custom_confidentiality_statement`)
VALUES
	(1,'Northwest','A','http://www.northwest-test.org',NULL,'India','data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAdxJREFUeNq0lj1Lw1AUhpOggrtfU9ytClpwEkFwVXDR4gdOiqhQHER/gVrwA0XFwUFRUavgP7AOXW1F66ptdPDrF7RDfQ+8gRiSJtH2wMMl7T3vuffk3nOihsNhpYTpYAT0ghZQDwrgDeTALbgAL24CqksAEV4FQzIHZMAd+OT/DUAcW/ksQZaAYReqchAfACdAA2tgz8nRspAZMAf6wRQ4s07QbA5RcA3uQchtVRYzOEfmpsEpmHcLMAw2wRXoY479Wo4+l2CdWr8CyFYPQBKMg7wS3PL0TVJLtwZY4Tj6R3F7kGoQMwM0gwjYAa8eAkXila5dnsBmjeJyFPeV8tk2x4jGS/QQ8KX6eemPoq3xsqSV8ltKtOWi1YH3Ejn387vqMOdDSoumVM6KZqn4Bk1utcpl5aqPAKL5JTt4Ah0V2EGnFEkJcAPaeB/KZTo1ExpLrdh0GQNEzTIuAbIgzpKreziqPvIvmZilZtY8RYscpQ/U/GPltcxIwdTULHV9EnSDYxaroCY+h6CLWoa9o52DRrDBcSJA+ZC0HIEesEAtx462BQbNIwaWPd6LzjkZ9ugxNhxfTT/GkquwGKZsTV8W0c7nOHNu+P2qcPpsCVFYYSC5oAmKP7sJ/AgwALtpZgbqLN7jAAAAAElFTkSuQmCC','#423131','#540521','EBI confidential statement','<br/><div><b><i></i></b><p><b><u><i><br/></i></u></b></p><p><b><u><i>hello</i></u></b></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p></div>');

/*!40000 ALTER TABLE `organization` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table organization_lang
# ------------------------------------------------------------

LOCK TABLES `organization_lang` WRITE;
/*!40000 ALTER TABLE `organization_lang` DISABLE KEYS */;

INSERT INTO `organization_lang` (`id`, `organization_id`, `lang_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `organization_name`, `nick_name`)
VALUES
	(1,1,1,NULL,NULL,NULL,'2014-10-13 15:18:47',NULL,NULL,'Northwest (test)','North westUniversity (test)');

/*!40000 ALTER TABLE `organization_lang` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table person
# ------------------------------------------------------------

LOCK TABLES `person` WRITE;
/*!40000 ALTER TABLE `person` DISABLE KEYS */;

INSERT INTO `person` (`id`,  `firstname`, `lastname`, `title`, `date_of_birth`, `external_id`, `username`, `password`, `activation_token`, `confidentiality_stmt_accept_date`, `organization_id`, `token_expiry_date`, `welcome_email_sent_date`)
VALUES
	(1,'Ramesh','Kumhar','Mr','0000-00-00','111111','ramesh.kumhar@techmahindra.com','$2y$13$f6bnaUYhaIO0qzJ0krqrIeUDnxJxWYYEyB3L6qDDK/1ln5CsHKEca','0d7bb70f71f58f0966429e41411d8b36','2015-02-17 12:01:02',1,NULL,'2015-02-17');

/*!40000 ALTER TABLE `person` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table contact_info
# ------------------------------------------------------------

LOCK TABLES `contact_info` WRITE;
/*!40000 ALTER TABLE `contact_info` DISABLE KEYS */;

INSERT INTO `contact_info` (`id`, `address_1`, `address_2`, `city`, `zip`, `state`, `country`, `primary_mobile`, `alternate_mobile`, `home_phone`, `office_phone`, `primary_email`, `alternate_email`, `primary_mobile_provider`, `alternate_mobile_provider`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(1,NULL,NULL,NULL,NULL,NULL,NULL,'9591900663',NULL,NULL,NULL,'ramesh.kumhar@techmahindra.com',NULL,'9224852114',NULL,NULL,NULL,NULL,'2015-02-17 12:34:01',NULL,NULL);

/*!40000 ALTER TABLE `contact_info` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table person_contact_info
# ------------------------------------------------------------

LOCK TABLES `person_contact_info` WRITE;
/*!40000 ALTER TABLE `person_contact_info` DISABLE KEYS */;

INSERT INTO `person_contact_info` (`person_id`, `contact_id`, `status`)
VALUES
	(1,1,'A');

/*!40000 ALTER TABLE `person_contact_info` ENABLE KEYS */;
UNLOCK TABLES;

# Dump of table AccessToken
# ------------------------------------------------------------

LOCK TABLES `AccessToken` WRITE;
/*!40000 ALTER TABLE `AccessToken` DISABLE KEYS */;

INSERT INTO `AccessToken` (`id`, `client_id`, `user_id`, `token`, `expires_at`, `scope`)
VALUES
	(1,1,1,'M2VmNGNmMWNkZjE2N2JjMjdiOTZhYzRhNjhmODFlZjMxMTMyZjVmOTQwMmQ1ZGM0ZDY0YTgyZDJmYzVjMGZjMA',1409263029,'user');

/*!40000 ALTER TABLE `AccessToken` ENABLE KEYS */;
UNLOCK TABLES;

# Dump of table RefreshToken
# ------------------------------------------------------------

LOCK TABLES `RefreshToken` WRITE;
/*!40000 ALTER TABLE `RefreshToken` DISABLE KEYS */;

INSERT INTO `RefreshToken` (`id`, `client_id`, `user_id`, `token`, `expires_at`, `scope`)
VALUES
	(1,1,1,'OGU3MTUwNTQxMzc4OTQwY2RlZjMxZTY5MmM5YTIxNzE2ZWU4NWEzODhmN2IxYTg2YjM2MzJmZTZiZmQ2NmM2ZA',1410469029,'user');

/*!40000 ALTER TABLE `RefreshToken` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table organization_role
# ------------------------------------------------------------

LOCK TABLES `organization_role` WRITE;
/*!40000 ALTER TABLE `organization_role` DISABLE KEYS */;

INSERT INTO `organization_role` (`id`, `role_id`, `person_id`, `organization_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(1,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `organization_role` ENABLE KEYS */;
UNLOCK TABLES;

# Dump of table metadata_master
# ------------------------------------------------------------

LOCK TABLES `metadata_master` WRITE;
/*!40000 ALTER TABLE `metadata_master` DISABLE KEYS */;

INSERT INTO `metadata_master` (`id`, `organization_id`, `entity_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `meta_key`, `definition_type`, `metadata_type`, `no_of_decimals`, `is_required`, `min_range`, `max_range`, `sequence`)
VALUES
	(1,1,NULL,NULL,'2014-09-02 19:16:24',NULL,'2014-09-03 04:52:03',NULL,NULL,'System_timezones','O','S',NULL,NULL,NULL,NULL,0);

/*!40000 ALTER TABLE `metadata_master` ENABLE KEYS */;
UNLOCK TABLES;

# Dump of table metadata_master_lang
# ------------------------------------------------------------

LOCK TABLES `metadata_master_lang` WRITE;
/*!40000 ALTER TABLE `metadata_master_lang` DISABLE KEYS */;

INSERT INTO `metadata_master_lang` (`id`, `metadata_id`, `lang_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `meta_name`, `meta_description`)
VALUES
	(1,1,1,NULL,'2014-09-02 19:16:24',NULL,'2014-09-02 19:16:24',NULL,NULL,'System_timezones','System_timezones');

/*!40000 ALTER TABLE `metadata_master_lang` ENABLE KEYS */;
UNLOCK TABLES;

/*
-- Query: select `metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence` from metadata_list_values where metadata_id=1 and deleted_at is 1
LIMIT 0, 1000

-- Date: 2015-02-17 18:31
*/
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Abidjan','Africa/Abidjan',1);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Accra','Africa/Accra',2);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Addis_Ababa','Africa/Addis_Ababa',3);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Algiers','Africa/Algiers',4);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Asmara','Africa/Asmara',5);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Asmera','Africa/Asmera',6);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Bamako','Africa/Bamako',7);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Bangui','Africa/Bangui',8);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Banjul','Africa/Banjul',9);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Bissau','Africa/Bissau',10);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Blantyre','Africa/Blantyre',11);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Brazzaville','Africa/Brazzaville',12);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Bujumbura','Africa/Bujumbura',13);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Cairo','Africa/Cairo',14);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Casablanca','Africa/Casablanca',15);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ceuta','Africa/Ceuta',16);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Conakry','Africa/Conakry',17);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Dakar','Africa/Dakar',18);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Dar_es_Salaam','Africa/Dar_es_Salaam',19);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Djibouti','Africa/Djibouti',20);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Douala','Africa/Douala',21);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'El_Aaiun','Africa/El_Aaiun',22);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Freetown','Africa/Freetown',23);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Gaborone','Africa/Gaborone',24);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Harare','Africa/Harare',25);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Johannesburg','Africa/Johannesburg   ',26);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Juba','Africa/Juba',27);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kampala','Africa/Kampala',28);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Khartoum','Africa/Khartoum',29);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Khartoum','Africa/Khartoum',30);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kinshasa','Africa/Kinshasa',31);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Lagos','Africa/Lagos',32);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Libreville','Africa/Libreville',33);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Lome','Africa/Lome',34);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Luanda','Africa/Luanda',35);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Lubumbashi','Africa/Lubumbashi',36);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Lusaka','Africa/Lusaka',37);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Malabo','Africa/Malabo',38);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Maputo','Africa/Maputo',39);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Maseru','Africa/Maseru',40);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Mbabane','Africa/Mbabane',41);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Mogadishu','Africa/Mogadishu',42);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Monrovia','Africa/Monrovia',43);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Nairobi','Africa/Nairobi',44);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ndjamena','Africa/Ndjamena',45);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ndjamena','Africa/Ndjamena',46);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Nouakchott','Africa/Nouakchott',47);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ouagadougou','Africa/Ouagadougou',48);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Porto-Novo','Africa/Porto-Novo',49);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Sao_Tome','Africa/Sao_Tome',50);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Timbuktu','Africa/Timbuktu',51);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tripoli','Africa/Tripoli',52);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tunis','Africa/Tunis',53);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Windhoek','Africa/Windhoek',54);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Adak','America/Adak',55);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Anchorage','America/Anchorage',56);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Anguilla','America/Anguilla',57);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Antigua','America/Antigua',58);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Araguaina','America/Araguaina',59);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Buenos_Aires','America/Argentina/Buenos_Aires',60);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Catamarca','America/Argentina/Catamarca',61);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'ComodRivadavia','America/Argentina/ComodRivadavia',62);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Cordoba','America/Argentina/Cordoba',63);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Jujuy','America/Argentina/Jujuy',64);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'La_Rioja','America/Argentina/La_Rioja',65);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Mendoza','America/Argentina/Mendoza',66);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Rio_Gallegos','America/Argentina/Rio_Gallegos    ',67);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Salta','America/Argentina/Salta',68);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'San_Juan','America/Argentina/San_Juan',69);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'San_Luis','America/Argentina/San_Luis',70);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tucuman','America/Argentina/Tucuman',71);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ushuaia','America/Argentina/Ushuaia',72);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Aruba','America/Aruba',73);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Asuncion','America/Asuncion',74);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Atikokan','America/Atikokan',75);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Atka','America/Atka',76);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Bahia','America/Bahia',77);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Bahia_Banderas','America/Bahia_Banderas',78);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Barbados','America/Barbados',79);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Belem','America/Belem',80);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Belize','America/Belize',81);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Blanc-Sablon','America/Blanc-Sablon',82);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Boa_Vista','America/Boa_Vista',83);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Bogota','America/Bogota',84);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Boise','America/Boise',85);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Buenos_Aires','America/Buenos_Aires',86);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Cambridge_Bay','America/Cambridge_Bay',87);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Campo_Grande','America/Campo_Grande',88);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Cancun','America/Cancun',89);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Caracas','America/Caracas',90);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Catamarca','America/Catamarca',91);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Cayenne','America/Cayenne',92);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Cayman','America/Cayman',93);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Chicago','America/Chicago',94);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Chihuahua','America/Chihuahua',95);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Coral_Harbour','America/Coral_Harbour',96);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Cordoba','America/Cordoba',97);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Costa_Rica','America/Costa_Rica',98);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Creston','America/Creston',99);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Cuiaba','America/Cuiaba',100);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Curacao','America/Curacao',101);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Danmarkshavn','America/Danmarkshavn',102);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Dawson','America/Dawson',103);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Dawson_Creek','America/Dawson_Creek',104);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Denver','America/Denver',105);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Detroit','America/Detroit',106);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Dominica','America/Dominica',107);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Edmonton','America/Edmonton',108);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Eirunepe','America/Eirunepe',109);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'El_Salvador','America/El_Salvador',110);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ensenada','America/Ensenada',111);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Fort_Wayne','America/Fort_Wayne  ',112);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Fortaleza','America/Fortaleza',113);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Glace_Bay','America/Glace_Bay',114);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Godthab','America/Godthab',115);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Goose_Bay','America/Goose_Bay',116);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Grand_Turk','America/Grand_Turk',117);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Grenada','America/Grenada',118);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Guadeloupe','America/Guadeloupe',119);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Guatemala','America/Guatemala',120);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Guayaquil','America/Guayaquil',121);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Guyana','America/Guyana',122);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Halifax','America/Halifax',123);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Havana','America/Havana',124);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Hermosillo','America/Hermosillo',125);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Indianapolis','America/Indiana/Indianapolis',126);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Knox','America/Indiana/Knox',127);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Marengo','America/Indiana/Marengo',128);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Petersburg','America/Indiana/Petersburg',129);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tell_City','America/Indiana/Tell_City',130);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Vevay','America/Indiana/Vevay',131);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Vincennes','America/Indiana/Vincennes',132);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Winamac','America/Indiana/Winamac',133);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Indianapolis','America/Indianapolis',134);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Inuvik','America/Inuvik',135);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Iqaluit','America/Iqaluit',136);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Jamaica','America/Jamaica',137);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Jujuy','America/Jujuy',138);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Juneau','America/Juneau',139);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Louisville','America/Kentucky/Louisville',140);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Monticello','America/Kentucky/Monticello',141);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Knox_IN','America/Knox_IN',142);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kralendijk','America/Kralendijk',143);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'La_Paz','America/La_Paz',144);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Lima','America/Lima',145);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Los_Angeles','America/Los_Angeles    ',146);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Louisville','America/Louisville',147);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Lower_Princes','America/Lower_Princes',148);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Maceio','America/Maceio',149);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Managua','America/Managua',150);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Manaus','America/Manaus',151);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Marigot','America/Marigot',152);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Martinique','America/Martinique',153);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Matamoros','America/Matamoros',154);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Mazatlan','America/Mazatlan',155);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Mendoza','America/Mendoza',156);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Menominee','America/Menominee',157);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Merida','America/Merida',158);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Metlakatla','America/Metlakatla',159);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Mexico_City','America/Mexico_City    ',160);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Miquelon','America/Miquelon',161);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Moncton','America/Moncton',162);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Monterrey','America/Monterrey',163);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Montevideo','America/Montevideo',164);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Montreal','America/Montreal',165);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Montserrat','America/Montserrat',166);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Nassau','America/Nassau',167);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'New_York','America/New_York',168);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Nipigon','America/Nipigon',169);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Nome','America/Nome',170);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Noronha','America/Noronha',171);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Beulah','America/North_Dakota/Beulah ',172);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Center','America/North_Dakota/Center ',173);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'New_Salem','America/North_Dakota/New_Salem',174);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ojinaga','America/Ojinaga',175);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Panama','America/Panama',176);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Pangnirtung','America/Pangnirtung    ',177);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Paramaribo','America/Paramaribo',178);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Phoenix','America/Phoenix',179);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Port-au-Prince','America/Port-au-Prince',180);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Port_of_Spain','America/Port_of_Spain',181);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Porto_Acre','America/Porto_Acre  ',182);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Porto_Velho','America/Porto_Velho',183);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Puerto_Rico','America/Puerto_Rico',184);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Rainy_River','America/Rainy_River',185);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Rankin_Inlet','America/Rankin_Inlet',186);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Recife','America/Recife',187);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Regina','America/Regina',188);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Resolute','America/Resolute',189);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Rio_Branco','America/Rio_Branco',190);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Rosario','America/Rosario',191);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Santa_Isabel','America/Santa_Isabel',192);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Santarem','America/Santarem',193);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Santiago','America/Santiago',194);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Santo_Domingo','America/Santo_Domingo',195);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Sao_Paulo ','America/Sao_Paulo   ',196);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Scoresbysund','America/Scoresbysund',197);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Shiprock','America/Shiprock',198);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Sitka','America/Sitka',199);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'St_Barthelemy','America/St_Barthelemy',200);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'St_Johns','America/St_Johns',201);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'St_Kitts','America/St_Kitts',202);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'St_Lucia','America/St_Lucia',203);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'St_Thomas','America/St_Thomas',204);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'St_Vincent','America/St_Vincent',205);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Swift_Current','America/Swift_Current',206);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tegucigalpa','America/Tegucigalpa    ',207);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Thule','America/Thule',208);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Thunder_Bay','America/Thunder_Bay',209);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tijuana','America/Tijuana',210);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Toronto','America/Toronto',211);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tortola','America/Tortola',212);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Vancouver','America/Vancouver',213);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Virgin','America/Virgin',214);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Whitehorse','America/Whitehorse  ',215);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Winnipeg','America/Winnipeg',216);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Yakutat','America/Yakutat    ',217);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Yellowknife','America/Yellowknife',218);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Casey','Antarctica/Casey',219);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Davis','Antarctica/Davis ',220);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'DumontDUrville','Antarctica/DumontDUrville',221);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Macquarie','Antarctica/Macquarie',222);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Mawson','Antarctica/Mawson',223);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'McMurdo','Antarctica/McMurdo',224);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Palmer','Antarctica/Palmer',225);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Rothera','Antarctica/Rothera',226);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'South_Pole','Antarctica/South_Pole',227);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Syowa','Antarctica/Syowa',228);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Troll','Antarctica/Troll',229);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Vostok','Antarctica/Vostok',230);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Longyearbyen','Arctic/Longyearbyen',231);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Aden','Asia/Aden',232);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Almaty','Asia/Almaty',233);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Amman','Asia/Amman',234);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Anadyr','Asia/Anadyr',235);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Aqtau','Asia/Aqtau',236);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Aqtobe','Asia/Aqtobe',237);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ashgabat','Asia/Ashgabat',238);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ashkhabad','Asia/Ashkhabad',239);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Baghdad','Asia/Baghdad',240);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Bahrain','Asia/Bahrain',241);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Baku  ','Asia/Baku   ',242);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Bangkok','Asia/Bangkok',243);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Beirut','Asia/Beirut',244);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Bishkek','Asia/Bishkek',245);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Brunei','Asia/Brunei',246);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Calcutta','Asia/Calcutta',247);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Chita','Asia/Chita   ',248);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Choibalsan','Asia/Choibalsan',249);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Chongqing','Asia/Chongqing',250);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Chungking','Asia/Chungking',251);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Colombo','Asia/Colombo',252);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Dacca','Asia/Dacca',253);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Damascus','Asia/Damascus',254);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Dhaka','Asia/Dhaka',255);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Dili','Asia/Dili',256);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Dubai','Asia/Dubai',257);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Dushanbe','Asia/Dushanbe',258);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Gaza','Asia/Gaza',259);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Harbin','Asia/Harbin',260);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Hebron','Asia/Hebron',261);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ho_Chi_Minh','Asia/Ho_Chi_Minh',262);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Hong_Kong','Asia/Hong_Kong',263);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Hovd','Asia/Hovd',264);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Irkutsk','Asia/Irkutsk',265);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Istanbul','Asia/Istanbul',266);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Jakarta','Asia/Jakarta',267);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Jayapura','Asia/Jayapura',268);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Jerusalem','Asia/Jerusalem',269);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kabul','Asia/Kabul',270);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kamchatka','Asia/Kamchatka',271);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Karachi','Asia/Karachi',272);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kashgar','Asia/Kashgar',273);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kathmandu','Asia/Kathmandu',274);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Katmandu','Asia/Katmandu',275);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Khandyga','Asia/Khandyga',276);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kolkata','Asia/Kolkata',277);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Krasnoyarsk','Asia/Krasnoyarsk',278);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kuala_Lumpur','Asia/Kuala_Lumpur',279);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kuching','Asia/Kuching',280);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kuwait','Asia/Kuwait',281);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Macao','Asia/Macao',282);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Macau','Asia/Macau',283);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Magadan','Asia/Magadan',284);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Makassar','Asia/Makassar',285);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Manila','Asia/Manila',286);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Muscat','Asia/Muscat',287);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Nicosia','Asia/Nicosia',288);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Novokuznetsk','Asia/Novokuznetsk',289);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Novosibirsk','Asia/Novosibirsk',290);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Omsk','Asia/Omsk',291);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Oral','Asia/Oral',292);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Phnom_Penh','Asia/Phnom_Penh',293);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Pontianak','Asia/Pontianak',294);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Pyongyang','Asia/Pyongyang',295);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Qatar','Asia/Qatar',296);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Qyzylorda','Asia/Qyzylorda',297);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Rangoon','Asia/Rangoon',298);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Riyadh','Asia/Riyadh',299);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Saigon','Asia/Saigon',300);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Samarkand','Asia/Samarkand',301);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Seoul','Asia/Seoul',302);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Shanghai','Asia/Shanghai',303);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Singapore','Asia/Singapore',304);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Srednekolymsk','Asia/Srednekolymsk',305);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Taipei','Asia/Taipei',306);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tashkent','Asia/Tashkent',307);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tbilisi','Asia/Tbilisi',308);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tehran','Asia/Tehran',309);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tel_Aviv','Asia/Tel_Aviv',310);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Thimbu','Asia/Thimbu',311);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Thimphu','Asia/Thimphu',312);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tokyo','Asia/Tokyo',313);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ujung_Pandang','Asia/Ujung_Pandang',314);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ulaanbaatar','Asia/Ulaanbaatar',315);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ulan_Bator','Asia/Ulan_Bator',316);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Urumqi','Asia/Urumqi',317);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ust-Nera','Asia/Ust-Nera',318);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Vientiane','Asia/Vientiane',319);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Vladivostok','Asia/Vladivostok',320);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Yakutsk','Asia/Yakutsk',321);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Yekaterinburg','Asia/Yekaterinburg',322);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Yerevan','Asia/Yerevan',323);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Azores','Atlantic/Azores',324);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Bermuda','Atlantic/Bermuda',325);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Canary','Atlantic/Canary',326);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Cape_Verde','Atlantic/Cape_Verde',327);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Faeroe','Atlantic/Faeroe',328);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Faroe','Atlantic/Faroe',329);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Jan_Mayen ','Atlantic/Jan_Mayen  ',330);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Madeira','Atlantic/Madeira',331);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Reykjavik','Atlantic/Reykjavik',332);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'South_Georgia','Atlantic/South_Georgia',333);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'St_Helena','Atlantic/St_Helena',334);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Stanley','Atlantic/Stanley',335);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'ACT','Australia/ACT',336);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Adelaide','Australia/Adelaide',337);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Brisbane','Australia/Brisbane',338);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Broken_Hill','Australia/Broken_Hill',339);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Canberra','Australia/Canberra',340);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Currie','Australia/Currie',341);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Darwin','Australia/Darwin',342);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Eucla','Australia/Eucla',343);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Hobart','Australia/Hobart',344);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'LHI','Australia/LHI',345);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Lindeman','Australia/Lindeman',346);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Lord_Howe','Australia/Lord_Howe',347);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Melbourne','Australia/Melbourne',348);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'North','Australia/North',349);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'NSW','Australia/NSW',350);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Perth','Australia/Perth',351);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Queensland','Australia/Queensland',352);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'South','Australia/South',353);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Sydney','Australia/Sydney',354);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tasmania','Australia/Tasmania',355);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Victoria','Australia/Victoria',356);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'West','Australia/West',357);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Yancowinna','Australia/Yancowinna',358);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Amsterdam','Europe/Amsterdam',359);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Andorra','Europe/Andorra',360);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Athens','Europe/Athens',361);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Belfast','Europe/Belfast',362);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Belgrade','Europe/Belgrade',363);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Berlin','Europe/Berlin',364);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Bratislava','Europe/Bratislava',365);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Brussels','Europe/Brussels',366);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Bucharest','Europe/Bucharest',367);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Budapest','Europe/Budapest',368);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Busingen','Europe/Busingen',369);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Chisinau','Europe/Chisinau',370);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Copenhagen','Europe/Copenhagen',371);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Dublin','Europe/Dublin',372);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Gibraltar','Europe/Gibraltar',373);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Guernsey','Europe/Guernsey',374);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Helsinki','Europe/Helsinki',375);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Isle_of_Man','Europe/Isle_of_Man',376);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Istanbul','Europe/Istanbul',377);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Jersey','Europe/Jersey',378);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kaliningrad','Europe/Kaliningrad',379);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kiev','Europe/Kiev',380);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Lisbon','Europe/Lisbon',381);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ljubljana','Europe/Ljubljana',382);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'London','Europe/London',383);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Luxembourg','Europe/Luxembourg',384);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Madrid','Europe/Madrid',385);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Malta','Europe/Malta',386);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Mariehamn','Europe/Mariehamn',387);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Minsk','Europe/Minsk',388);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Monaco','Europe/Monaco',389);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Moscow','Europe/Moscow',390);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Nicosia','Europe/Nicosia',391);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Oslo','Europe/Oslo',392);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Paris','Europe/Paris',393);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Podgorica','Europe/Podgorica',394);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Prague','Europe/Prague',395);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Riga','Europe/Riga',396);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Rome','Europe/Rome',397);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Samara','Europe/Samara',398);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'San_Marino','Europe/San_Marino',399);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Sarajevo','Europe/Sarajevo',400);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Simferopol','Europe/Simferopol',401);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Skopje','Europe/Skopje',402);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Sofia','Europe/Sofia',403);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Stockholm','Europe/Stockholm',404);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tallinn','Europe/Tallinn',405);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tirane','Europe/Tirane',406);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tiraspol','Europe/Tiraspol',407);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Uzhgorod','Europe/Uzhgorod',408);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Vaduz','Europe/Vaduz',409);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Vatican','Europe/Vatican',410);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Vienna','Europe/Vienna',411);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Vilnius','Europe/Vilnius',412);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Volgograd','Europe/Volgograd',413);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Warsaw','Europe/Warsaw',414);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Zagreb','Europe/Zagreb',415);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Zaporozhye','Europe/Zaporozhye',416);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Zurich','Europe/Zurich',417);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Antananarivo','Indian/Antananarivo',418);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Chagos','Indian/Chagos',419);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Christmas','Indian/Christmas',420);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Cocos','Indian/Cocos',421);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Comoro','Indian/Comoro',422);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kerguelen','Indian/Kerguelen',423);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Mahe','Indian/Mahe',424);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Maldives','Indian/Maldives',425);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Mauritius','Indian/Mauritius',426);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Mayotte','Indian/Mayotte',427);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Reunion','Indian/Reunion',428);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Apia','Pacific/Apia',429);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Auckland','Pacific/Auckland',430);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Chatham','Pacific/Chatham',431);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Chuuk','Pacific/Chuuk',432);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Easter','Pacific/Easter',433);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Efate','Pacific/Efate',434);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Enderbury','Pacific/Enderbury',435);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Fakaofo','Pacific/Fakaofo',436);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Fiji','Pacific/Fiji',437);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Funafuti','Pacific/Funafuti',438);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Galapagos','Pacific/Galapagos',439);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Gambier','Pacific/Gambier',440);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Guadalcanal','Pacific/Guadalcanal',441);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Guam','Pacific/Guam',442);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Honolulu','Pacific/Honolulu',443);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Johnston','Pacific/Johnston',444);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kiritimati','Pacific/Kiritimati',445);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kosrae','Pacific/Kosrae',446);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kwajalein','Pacific/Kwajalein',447);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Majuro','Pacific/Majuro',448);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Marquesas','Pacific/Marquesas',449);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Midway','Pacific/Midway',1);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Nauru','Pacific/Nauru',451);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Niue','Pacific/Niue',452);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Norfolk','Pacific/Norfolk',453);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Noumea','Pacific/Noumea',454);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Pago_Pago','Pacific/Pago_Pago',455);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Palau','Pacific/Palau',456);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Pitcairn','Pacific/Pitcairn',457);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Pohnpei','Pacific/Pohnpei',458);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Ponape','Pacific/Ponape',459);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Port_Moresby','Pacific/Port_Moresby',460);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Rarotonga','Pacific/Rarotonga',461);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Saipan','Pacific/Saipan',462);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Samoa','Pacific/Samoa',463);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tahiti','Pacific/Tahiti',464);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tarawa','Pacific/Tarawa',465);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Tongatapu','Pacific/Tongatapu',466);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Truk','Pacific/Truk',467);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Wake','Pacific/Wake',468);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Wallis','Pacific/Wallis',469);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Yap','Pacific/Yap',470);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Acre','Brazil/Acre',471);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'DeNoronha','Brazil/DeNoronha',472);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'East','Brazil/East',473);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'West','Brazil/West',474);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Atlantic','Canada/Atlantic',475);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Central','Canada/Central',476);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'East-Saskatchewan','Canada/East-Saskatchewan',477);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Eastern','Canada/Eastern',478);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Mountain','Canada/Mountain',479);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Newfoundland','Canada/Newfoundland',480);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Pacific','Canada/Pacific',481);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Saskatchewan','Canada/Saskatchewan',482);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Yukon','Canada/Yukon',483);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'CET','CET',484);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Continental','Chile/Continental',485);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'EasterIsland','Chile/EasterIsland',486);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'CST6CDT','CST6CDT',487);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Cuba','Cuba',488);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'EET','EET',489);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Egypt','Egypt',490);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Eire','Eire',491);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'EST','EST',492);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'EST5EDT','EST5EDT',493);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT','Etc/GMT',494);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT+0','Etc/GMT+0',495);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT+1','Etc/GMT+1',496);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT+10','Etc/GMT+10',497);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT+11','Etc/GMT+11',498);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT+12','Etc/GMT+12',499);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT+2','Etc/GMT+2',500);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT+3','Etc/GMT+3',501);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT+4','Etc/GMT+4',502);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT+5','Etc/GMT+5',503);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT+6','Etc/GMT+6',504);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT+7','Etc/GMT+7',505);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT+8','Etc/GMT+8',506);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT+9','Etc/GMT+9',507);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-0','Etc/GMT-0',508);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-1','Etc/GMT-1',509);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-10','Etc/GMT-10',510);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-11','Etc/GMT-11',511);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-12','Etc/GMT-12',512);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-13','Etc/GMT-13',513);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-14','Etc/GMT-14',514);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-2','Etc/GMT-2',515);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-3','Etc/GMT-3',516);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-4','Etc/GMT-4',517);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-5','Etc/GMT-5',518);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-6','Etc/GMT-6',519);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-7','Etc/GMT-7',520);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-8','Etc/GMT-8',521);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-9','Etc/GMT-9',522);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT0','Etc/GMT0',523);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Greenwich','Etc/Greenwich',524);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'UCT','Etc/UCT',525);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Universal','Etc/Universal',526);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'UTC','Etc/UTC',527);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Zulu','Etc/Zulu',528);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Factory','Factory',529);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GB','GB',530);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GB-Eire','GB-Eire',531);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT','GMT',532);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT+0','GMT+0',533);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT-0','GMT-0',534);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'GMT0','GMT0',535);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Greenwich','Greenwich',536);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Hongkong','Hongkong',537);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'HST','HST',538);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Iceland','Iceland',539);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Iran','Iran',540);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Israel','Israel',541);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Jamaica','Jamaica',542);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Japan','Japan',543);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Kwajalein','Kwajalein',544);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Libya','Libya',545);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'MET','MET',546);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'BajaNorte','Mexico/BajaNorte',547);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'BajaSur','Mexico/BajaSur',548);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'General','Mexico/General',549);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'MST','MST',550);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'MST7MDT','MST7MDT',551);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Navajo','Navajo',552);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'NZ','NZ',553);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'NZ-CHAT','NZ-CHAT',554);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Poland','Poland',555);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Portugal','Portugal',556);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'PRC','PRC',557);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'PST8PDT','PST8PDT',558);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'ROC','ROC',559);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'ROK','ROK',560);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Singapore','Singapore',561);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Turkey','Turkey',562);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'UCT','UCT',563);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Universal','Universal',564);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Alaska','US/Alaska',565);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Aleutian','US/Aleutian',566);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Arizona','US/Arizona',567);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Central','US/Central',568);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'East-Indiana','US/East-Indiana',569);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Eastern','US/Eastern',570);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Hawaii','US/Hawaii',571);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Indiana-Starke','US/Indiana-Starke',572);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Michigan','US/Michigan',573);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Mountain','US/Mountain',574);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Pacific','US/Pacific',575);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Pacific-New','US/Pacific-New',576);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Samoa','US/Samoa',577);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'  UTC',' UTC',578);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'W-SU','W-SU',579);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'WET','WET',580);
INSERT INTO `metadata_list_values` (`metadata_id`,`lang_id`,`list_name`,`list_value`,`sequence`) VALUES (1,1,'Zulu','Zulu',581);








/*
-- Query: select * from email_template
LIMIT 0, 1000

-- Date: 2015-02-17 11:25
*/
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'Welcome_Email_Staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (2,NULL,NULL,NULL,NULL,NULL,NULL,'Forgot_Password_Faculty',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (3,NULL,NULL,NULL,NULL,NULL,NULL,'MyAccount_Updated_Staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (4,NULL,NULL,NULL,NULL,NULL,NULL,'Create_Password_Coordinator',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (5,NULL,NULL,NULL,NULL,NULL,NULL,'Forgot_Password_Coordinator',NULL,'no-reply@mapworks.com','');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (6,NULL,NULL,NULL,NULL,NULL,NULL,'Welcome_Email_Coordinator',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (7,NULL,NULL,NULL,NULL,NULL,NULL,'Sucessful_Password_Reset_Staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (8,NULL,NULL,NULL,NULL,NULL,NULL,'Sucessful_Password_Reset_Coordinator',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (9,NULL,NULL,NULL,NULL,NULL,NULL,'Appointment_Book_Staff_to_Student',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,SR00361602@TechMahindra.com,MK00361563@techmahindra.com');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (10,NULL,NULL,NULL,NULL,NULL,NULL,'Appointment_Update_staff_to_Student',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,SR00361602@TechMahindra.com');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (11,NULL,NULL,NULL,NULL,NULL,NULL,'Appointment_Cancel_Staff_to_Student',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,SR00361602@TechMahindra.com');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (12,NULL,NULL,NULL,NULL,NULL,NULL,'Add_Delegate',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (13,NULL,NULL,NULL,NULL,NULL,NULL,'Remove_Delegate',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (14,NULL,NULL,NULL,NULL,NULL,NULL,'Referral_Assign_to_staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');
INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (15,NULL,NULL,NULL,NULL,NULL,NULL,'Appointment_Reminder_Staff_to_Student',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com,MK00361563@techmahindra.com');


/*
-- Query: select * from email_template_lang
LIMIT 0, 1000

-- Date: 2015-02-17 11:25
*/
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/><br/>\n\nA MAP-Works password was successfully created for your account.If this was not you or you believe this is an error,\nplease contact MAP-Works support at &nbsp;<a class=\"external-link\" href=\"mailto:$$Support_Helpdesk_Email_Address$$\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">$$Support_Helpdesk_Email_Address$$</a><br/><br/>\n\nWe\'re very happy to have you on board, and are here to support you!<br/><br/>\nThank you from the MAP-Works team!\n\n</div>\n</html>','MAP-Works password created');
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (2,2,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/></br>\n\nPlease use the link below and follow the displayed instructions to create your new password. This link will expire after $$Reset_Password_Expiry_Hrs$$ hours.<br />\n<br/>\n$$activation_token$$<br/><br/>\n\nIf you believe that you received this email in error or if you have any questions,please contact MAP-works support at <span style=\"color: #99ccff;\">$$Support_Helpdesk_Email_Address$$</span>.<br/><br/>\nThank you from the MAP-Works team!\n </div>\n</html>\n\n','MAP-Works - how to reset your password');
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (3,3,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n 	\n 		<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\n 			Hi $$firstname$$,<br/><br/>\n 		\n 			An update to your MAP-Works account was successfully made. The following information was updated: <br/><br/> \n 		\n 			$$Updated_MyAccount_fields$$\n 		<br/>\n 		\n 			If this was not you or you believe this is an error, please contact MAP-Works support at&nbsp;<a class=\"external-link\" href=\"mailto:support@map-works.com\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">support@map-works.com</a></p>\n 		\n 			<br>Thank you from the MAP-Works team!</br>\n 	</div>\n </html>\n ','MAP-Works profile updated');
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (4,4,1,NULL,NULL,NULL,NULL,NULL,NULL,'<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\n<head>\n    <title>Email</title>\n</head>\n<body>\n<center>\n    <table align=\"center\">\n        <tr>\n            <th style=\"padding:0px;margin:0px;\">\n               \n               <table  style=\"font-family:helvetica,arial,verdana,san-serif;font-weight:normal;width:800px; height=337px;text-align:center;padding:0px;\">\n               <tr bgcolor=\"#eeeeee\" style=\"width:800px;padding:0px;height:337px;\">\n               <td style=\"width:800px;padding:0px;height:337px;\">\n               <table style=\"text-align:center;width:100%\">\n               <tr>\n                    <td style=\"padding:0px;\">\n                    <table style=\"margin-top:56px;width:100%\">\n		<tr>\n		<td style=\"text-align:center;padding:0px;font-size:33px;height:80px;width:800px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;color:#000000\">\n					<br>Welcome to MAP-Works.		\n		</td>\n		</tr>\n		</table>\n                    </td>\n               </tr>\n               <tr style=\"margin:0px;padding:0px;\">\n		<td style=\"text-align:center;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;color:#333333;font-size: 16px;height:16px;padding-top:8px;\">\n        			Use the link below to create your password and start using MAP-Works.\n		\n		</td></tr>\n           <tr style=\"margin:0px;padding:0px;\"><td style=\"margin:0px;padding:0px;\">\n        \n<table align=\"center\"> \n  <tr style=\"margin:0px;padding:0px;\">\n    <th style=\"margin:0px;padding:0px;\">\n           <table cellpadding=\"36\" style=\"width:100%\">\n        <tr>\n		<td align=\"center\" style=\"text-align:center;color:#000000;font-weight:normal;font-size: 20px;\">		\n		          <table style=\"border-radius:2px;width:175px;font-size:20px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;text-align:center;display: block;margin: 0 auto;padding:0px 0px\">\n		<tr>\n        <td style=\"background-color:#4673a7; height:58px;border-radius:2px;line-height:21px;text-decoration:none ;vertical-align:middle;\">        \n        <a href=\"$$activation_token$$\" style=\"outline-offset:19px !important;background-color: #4673a7; color: #ffffff;display: block;text-decoration: none;width:175px \"target=\"_blank\"><span style=\"text-decoration: none !important;\">Sign In Now</span></a>\n        </td></tr>\n        <tr valign=\"top\" style=\"height:33px;\">\n        <td style=\"margin-left:auto; margin-right:auto;width:100%;font-size: 14px;height:14px;padding-bottom:7px;font-family:helvetica,arial,verdana,san-serif;font-weight:medium;color:#333333;link:#1e73d5;padding-top:8px;\">       \n				<span>Use this link to <a target=\"_blank\" style=\"link:#1e73d5;\" href=\"$$activation_token$$\">sign in.</a></span>  \n			\n        </td></tr>\n        \n        </table>\n		</td></tr>\n        \n        </table>\n        </th>\n    \n  </tr>\n \n</table>\n       </td></tr>\n</table>\n               </td>\n               </tr>\n               <tr valign=\"top\">\n<td >\n<table>\n<tr>\n<td valign=\"top\" align=\"center\">\n<div style=\"text-align:left;margin-left:30px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;\n			margin-right:18px;font-size: 13px;color: #333333;margin-top:30px;link:#1e73d5;font-weight:normal;\" >\n				Thank you for participating in the spring 2015 pilot. We look forward to hearing your feedback as\n				it will inform future releases of our new student retention and success solution.\n				\n				<br><br>\n				If you have any questions, please contact us here.<br>\n				<a href=\"mailto:$$Support_Helpdesk_Email_Address$$\" style=\"link:#1e73d5;\">$$Support_Helpdesk_Email_Address$$</a> \n				<br><br>\n				Sincerely,\n				<div style=\"text-align:left;font-weight:bold;font-size: 14px;color:#333333\" >\n					<b>The EBI MAP-Works Client Services Team</b> \n					\n				</div>\n                </div>\n</td>\n</tr>\n</table>\n</td>\n</tr>\n               </table>\n               \n            </th>\n            \n        </tr>\n        \n    </table>\n    </center>\n</body>\n</html>\n','Map-Works - SignIn Instructions');
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (5,5,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/></br>\n\nPlease use the link below and follow the displayed instructions to create your new password. This link will expire after $$Reset_Password_Expiry_Hrs$$ hours.<br />\n<br/>\n$$activation_token$$<br/><br/>\n\nIf you believe that you received this email in error or if you have any questions,please contact MAP-works support at <span style=\"color: #99ccff;\">$$Support_Helpdesk_Email_Address$$</span>.<br/><br/>\nThank you from the MAP-Works team!\n </div>\n</html>\n\n','MAP-Works - how to reset your password');
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (6,6,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/><br/>\n\nA MAP-Works password was successfully created for your account. If this was not you or you believe this is an error,\nplease contact MAP-Works support at &nbsp;<a class=\"external-link\" href=\"mailto:support@map-works.com\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">support@map-works.com</a><br/><br/>\n\nWe\'re very happy to have you on board, and are here to support you!<br/><br/>\nThank you from the MAP-Works team!\n\n</div>\n</html>','MAP-Works password created');
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (7,7,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/><br/>\n\nYour MAP-Works password has been changed. If this was not you or you believe this is an error, please contact MAP-Works support at &nbsp;<a class=\"external-link\" href=\"mailto:support@map-works.com\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: none;\">support@map-works.com</a>\n<br/><br/>\nThank you from the MAP-Works team!\n\n</div>\n</html>','MAP-Works password reset');
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (8,8,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/><br/>\n\nYour MAP-Works password has been changed. If this was not you or you believe this is an error, please contact MAP-Works support at &nbsp;<a class=\"external-link\" href=\"mailto:support@map-works.com\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">support@map-works.com</a>\n<br/><br/>\nThank you from the MAP-Works team!\n\n</div>\n</html>','MAP-Works password reset');
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (9,9,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n	<head>\n		<style>\n		body {\n    background: none repeat scroll 0 0 #f4f4f4;\n	\n}\n		table {\n    padding: 21px;\n    width: 799px;\n	font-family: helvetica,arial,verdana,san-serif;\n	font-size:13px;\n	color:#333;\n	}\n		</style>\n	</head>\n	<body>\n	\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\n			<tbody>\n			\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$student_name$$:</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>An appointment has been booked with $$staff_name$$  \n				on $$app_datetime$$. To view the appointment details,\n				please log in to your MAP-Works dashboard and visit <a class=\"external-link\" href=\"$$student_dashboard$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155);text-decoration: underline;\">MAP-Works student dashboard view appointment module</a>.</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI MAP-Works</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\n			\n			</tbody>\n		</table>\n	</body>\n</html>','MAP-Works appointment booked');
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (10,10,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n	<head>\n		<style>\n		body {\n    background: none repeat scroll 0 0 #f4f4f4;\n	\n}\n		table {\n    padding: 21px;\n    width: 799px;\n	font-family: helvetica,arial,verdana,san-serif;\n	font-size:13px;\n	color:#333;\n	}\n		</style>\n	</head>\n	<body>\n	\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\n			<tbody>\n			\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$student_name$$:</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A booked appointment with $$staff_name$$\n				has been modified. The appointment is now scheduled for $$app_datetime$$. To view the modified appointment details,\n				please log in to your MAP-Works dashboard and visit <a class=\"external-link\" href=\"$$student_dashboard$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">MAP-Works student dashboard view appointment module</a>.</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI MAP-Works</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\n			\n			</tbody>\n		</table>\n	</body>\n</html>','MAP-Works appointment modified');
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (11,11,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n	<head>\n		<style>\n		body {\n    background: none repeat scroll 0 0 #f4f4f4;\n	\n}\n		table {\n    padding: 21px;\n    width: 799px;\n	font-family: helvetica,arial,verdana,san-serif;\n	font-size:13px;\n	color:#333;\n	}\n		</style>\n	</head>\n	<body>\n	\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\n			<tbody>\n			\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$student_name$$:</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Your booked appointment with \n				$$staff_name$$ on $$app_datetime$$ has been cancelled.\n				To book a new appointment, please log in to your MAP-Works dashboard and visit <a class=\"external-link\" href=\"$$student_dashboard$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">MAP-Works student dashboard view appointment module</a>.</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI MAP-Works</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\n			\n			</tbody>\n		</table>\n	</body>\n</html>','MAP-Works appointment cancelled');
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (12,12,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n	<head>\n		<style>\n		body {\n    background: none repeat scroll 0 0 #f4f4f4;\n	\n}\n		table {\n    padding: 21px;\n    width: 799px;\n	font-family: helvetica,arial,verdana,san-serif;\n	font-size:13px;\n	color:#333;\n	}\n		</style>\n	</head>\n	<body>\n	\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\n			<tbody>\n			\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$fullname$$:</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>You have been added as a delegate user for $$delegater_name$$\'s calendar.</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI MAP-Works</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\n			\n			</tbody>\n		</table>\n	</body>\n</html>','MAP-Works Delegate Added');
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (13,13,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n	<head>\n		<style>\n		body {\n    background: none repeat scroll 0 0 #f4f4f4;\n	\n}\n		table {\n    padding: 21px;\n    width: 799px;\n	font-family: helvetica,arial,verdana,san-serif;\n	font-size:13px;\n	color:#333;\n	}\n		</style>\n	</head>\n	<body>\n	\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\n			<tbody>\n			\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$fullname$$:</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>You have been removed as a delegate user for $$delegater_name$$\'s calendar.</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI MAP-Works</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\n			\n			</tbody>\n		</table>\n	</body>\n</html>','MAP-Works Delegate removed');
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (14,14,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\r\n	<head>\r\n		<style>\r\n		body {\r\n    background: none repeat scroll 0 0 #f4f4f4;\r\n	\r\n}\r\n		table {\r\n    padding: 21px;\r\n    width: 799px;\r\n	font-family: helvetica,arial,verdana,san-serif;\r\n	font-size:13px;\r\n	color:#333;\r\n	}\r\n		</style>\r\n	</head>\r\n	<body>\r\n	\r\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r\n			<tbody>\r\n			\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi $$firstname$$:</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A referral was recently assigned to you in MAP-Works. Please sign in to your account to view and take action on this referral.</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Mapworks team!</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r\n			\r\n			</tbody>\r\n		</table>\r\n	</body>\r\n</html>','You have a new Mapworks referral\r\n\r\n');
INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (15,15,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n    <head>\n        <style>\n			 body {\n				background: none repeat scroll 0 0;	\n			\n			}\n			table {\n				padding: 21px;\n				width: 799px;\n				font-family: Helvetica,Arial,Verdana,San-serif;\n				font-size:13px;\n				color:#333;\n			}\n   </style>\n    </head>\n    <body>\n        <table cellpadding=\"10\" cellspacing=\"0\">\n            <tbody>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td>Dear $$student_name$$:</td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td style=\"line-height: 1.6;\">This is a reminder that you have an appointment with $$staff_name$$ on $$app_datetime$$. <br/><br/> To view the appointment details, please log in to your MAP-Works dashboard and visit\n					<a style=\"color: #0033CC;\" href=\"$$student_dashboard$$\">Mapworks student dashboard view appointment module</a>.\n					</td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td>Best regards,\n                        <br/>EBI MAP-Works\n                    </td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td><span style=\"font-size:11px; color: #575757; line-height: 120%; text-decoration: none;\">This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</span></td>\n                </tr>\n            </tbody>\n        </table>\n    </body>\n</html>\n','MAP-Works appointment reminder');


