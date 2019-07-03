# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.40-0ubuntu0.14.04.1)
# Database: synapse
# Generation Time: 2014-12-17 18:09:08 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

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


# Dump of table access_log
# ------------------------------------------------------------



# Dump of table AccessToken
# ------------------------------------------------------------

LOCK TABLES `AccessToken` WRITE;
/*!40000 ALTER TABLE `AccessToken` DISABLE KEYS */;

INSERT INTO `AccessToken` (`id`, `client_id`, `user_id`, `token`, `expires_at`, `scope`)
VALUES
	(1,1,1,'M2VmNGNmMWNkZjE2N2JjMjdiOTZhYzRhNjhmODFlZjMxMTMyZjVmOTQwMmQ1ZGM0ZDY0YTgyZDJmYzVjMGZjMA',1409263029,'user');

/*!40000 ALTER TABLE `AccessToken` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table activity_category
# ------------------------------------------------------------

LOCK TABLES `activity_category` WRITE;
/*!40000 ALTER TABLE `activity_category` DISABLE KEYS */;

INSERT INTO `activity_category` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `short_name`, `is_active`, `display_seq`, `parent_activity_category_id`)
VALUES
	(1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic Issues',NULL,NULL,NULL),
	(2,NULL,NULL,NULL,NULL,NULL,NULL,'Personal Issues',NULL,NULL,NULL),
	(3,NULL,NULL,NULL,NULL,NULL,NULL,'Financial Issues',NULL,NULL,NULL),
	(4,NULL,NULL,NULL,NULL,NULL,NULL,'MAP-Works Issues',NULL,NULL,NULL),
	(19,NULL,NULL,NULL,NULL,NULL,NULL,'Class attendance concern',NULL,NULL,1),
	(20,NULL,NULL,NULL,NULL,NULL,NULL,'Class attendance positive',NULL,NULL,1),
	(21,NULL,NULL,NULL,NULL,NULL,NULL,'Academic performance concern',NULL,NULL,1),
	(22,NULL,NULL,NULL,NULL,NULL,NULL,'Academic performance positive',NULL,NULL,1),
	(23,NULL,NULL,NULL,NULL,NULL,NULL,'Registration positive',NULL,NULL,1),
	(24,NULL,NULL,NULL,NULL,NULL,NULL,'Registration concern',NULL,NULL,1),
	(25,NULL,NULL,NULL,NULL,NULL,NULL,'Academic skills',NULL,NULL,1),
	(26,NULL,NULL,NULL,NULL,NULL,NULL,'Academic major exploration/selection',NULL,NULL,1),
	(27,NULL,NULL,NULL,NULL,NULL,NULL,'Academic action meeting',NULL,NULL,1),
	(28,NULL,NULL,NULL,NULL,NULL,NULL,'Academic success planning',NULL,NULL,1),
	(29,NULL,NULL,NULL,NULL,NULL,NULL,'Missing required meetings / activities',NULL,NULL,1),
	(30,NULL,NULL,NULL,NULL,NULL,NULL,'Attended meeting / activities',NULL,NULL,1),
	(31,NULL,NULL,NULL,NULL,NULL,NULL,'Other academic concerns',NULL,NULL,1),
	(32,NULL,NULL,NULL,NULL,NULL,NULL,'Living environment concern',NULL,NULL,2),
	(33,NULL,NULL,NULL,NULL,NULL,NULL,'Living environment positive',NULL,NULL,2),
	(34,NULL,NULL,NULL,NULL,NULL,NULL,'Relationships concern',NULL,NULL,2),
	(35,NULL,NULL,NULL,NULL,NULL,NULL,'Relationships positive',NULL,NULL,2),
	(36,NULL,NULL,NULL,NULL,NULL,NULL,'Social connections concern',NULL,NULL,2),
	(37,NULL,NULL,NULL,NULL,NULL,NULL,'Social connections positive',NULL,NULL,2),
	(38,NULL,NULL,NULL,NULL,NULL,NULL,'Medical / mental health',NULL,NULL,2),
	(39,NULL,NULL,NULL,NULL,NULL,NULL,'Short term',NULL,NULL,3),
	(40,NULL,NULL,NULL,NULL,NULL,NULL,'Long term',NULL,NULL,3),
	(41,NULL,NULL,NULL,NULL,NULL,NULL,'Positive financial',NULL,NULL,3),
	(42,NULL,NULL,NULL,NULL,NULL,NULL,'MAP-Works related issues',NULL,NULL,4);

/*!40000 ALTER TABLE `activity_category` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table activity_category_lang
# ------------------------------------------------------------

LOCK TABLES `activity_category_lang` WRITE;
/*!40000 ALTER TABLE `activity_category_lang` DISABLE KEYS */;

INSERT INTO `activity_category_lang` (`id`, `activity_category_id`, `language_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `description`)
VALUES
	(1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic Issues'),
	(2,2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Personal Issues'),
	(3,3,1,NULL,NULL,NULL,NULL,NULL,NULL,'Financial Issues'),
	(4,4,1,NULL,NULL,NULL,NULL,NULL,NULL,'MAP-Works Issues'),
	(5,19,1,NULL,NULL,NULL,NULL,NULL,NULL,'Class attendance concern'),
	(6,20,1,NULL,NULL,NULL,NULL,NULL,NULL,'Class attendance positive'),
	(7,21,1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic performance concern'),
	(8,22,1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic performance positive'),
	(9,23,1,NULL,NULL,NULL,NULL,NULL,NULL,'Registration positive'),
	(10,24,1,NULL,NULL,NULL,NULL,NULL,NULL,'Registration concern'),
	(11,25,1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic skills'),
	(12,26,1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic major exploration/selection'),
	(13,27,1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic action meeting'),
	(14,28,1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic success planning'),
	(15,29,1,NULL,NULL,NULL,NULL,NULL,NULL,'Missing required meetings / activities'),
	(16,30,1,NULL,NULL,NULL,NULL,NULL,NULL,'Attended meeting / activities'),
	(17,31,1,NULL,NULL,NULL,NULL,NULL,NULL,'Other academic concerns'),
	(18,32,1,NULL,NULL,NULL,NULL,NULL,NULL,'Living environment concern'),
	(19,33,1,NULL,NULL,NULL,NULL,NULL,NULL,'Living environment positive'),
	(20,34,1,NULL,NULL,NULL,NULL,NULL,NULL,'Relationships concern'),
	(21,35,1,NULL,NULL,NULL,NULL,NULL,NULL,'Relationships positive'),
	(22,36,1,NULL,NULL,NULL,NULL,NULL,NULL,'Social connections concern'),
	(23,37,1,NULL,NULL,NULL,NULL,NULL,NULL,'Social connections positive'),
	(24,38,1,NULL,NULL,NULL,NULL,NULL,NULL,'Medical / mental health'),
	(25,39,1,NULL,NULL,NULL,NULL,NULL,NULL,'Short term'),
	(26,40,1,NULL,NULL,NULL,NULL,NULL,NULL,'Long term'),
	(27,41,1,NULL,NULL,NULL,NULL,NULL,NULL,'Positive financial'),
	(28,42,1,NULL,NULL,NULL,NULL,NULL,NULL,'MAP-Works related issues');

/*!40000 ALTER TABLE `activity_category_lang` ENABLE KEYS */;
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


# Dump of table activity_reference_unassigned
# ------------------------------------------------------------



# Dump of table appointment_recepient_and_status
# ------------------------------------------------------------



# Dump of table Appointments
# ------------------------------------------------------------

LOCK TABLES `Appointments` WRITE;
/*!40000 ALTER TABLE `Appointments` DISABLE KEYS */;

INSERT INTO `Appointments` (`id`, `organization_id`, `person_id`, `activity_category_id`, `person_id_proxy`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `type`, `location`, `title`, `description`, `start_date_time`, `end_date_time`, `attendees`, `occurrence_id`, `master_occurrence_id`, `match_status`, `last_synced`, `is_free_standing`)
VALUES
	(1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'S','Test','Test','Test','2014-12-17 16:02:44','2014-12-17 16:02:44','a:0:{}',NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `Appointments` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table AuthCode
# ------------------------------------------------------------



# Dump of table calendar_sharing
# ------------------------------------------------------------

LOCK TABLES `calendar_sharing` WRITE;
/*!40000 ALTER TABLE `calendar_sharing` DISABLE KEYS */;

INSERT INTO `calendar_sharing` (`id`, `organization_id`, `person_id_sharedby`, `person_id_sharedto`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `shared_on`, `is_selected`)
VALUES
	(21,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-12-17 16:47:09',NULL);

/*!40000 ALTER TABLE `calendar_sharing` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table Client
# ------------------------------------------------------------

LOCK TABLES `Client` WRITE;
/*!40000 ALTER TABLE `Client` DISABLE KEYS */;

INSERT INTO `Client` (`id`, `random_id`, `redirect_uris`, `secret`, `allowed_grant_types`)
VALUES
	(1,'382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s','a:0:{}','3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480','a:1:{i:0;s:8:\"password\";}');

/*!40000 ALTER TABLE `Client` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table contact_info
# ------------------------------------------------------------

LOCK TABLES `contact_info` WRITE;
/*!40000 ALTER TABLE `contact_info` DISABLE KEYS */;

INSERT INTO `contact_info` (`id`, `address_1`, `address_2`, `city`, `zip`, `state`, `country`, `primary_mobile`, `alternate_mobile`, `home_phone`, `office_phone`, `primary_email`, `alternate_email`, `primary_mobile_provider`, `alternate_mobile_provider`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(1,NULL,NULL,NULL,NULL,NULL,NULL,'9591900663',NULL,NULL,NULL,'ramesh.kumhar@techmahindra.com',NULL,'9224852114',NULL,NULL,NULL,NULL,'2014-10-15 12:34:01',NULL,NULL),
	(2,NULL,NULL,NULL,NULL,NULL,NULL,'7781900665',NULL,NULL,NULL,'bipinbihari.pradhan@techmahindra.com',NULL,'9224852114',NULL,NULL,NULL,NULL,'2014-10-15 12:34:01',NULL,NULL),
	(3,NULL,NULL,NULL,NULL,NULL,NULL,'77819440665',NULL,NULL,NULL,'devadoss.poornachari@techmahindra.com',NULL,'9224852114',NULL,NULL,NULL,NULL,'2014-10-15 12:34:01',NULL,NULL),
	(4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5491b3df0639d@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 18:16:26',NULL,'2014-12-17 16:48:31',NULL,NULL),
	(5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5490a26fad566@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 21:21:51',NULL,'2014-12-16 21:21:51',NULL,NULL),
	(6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5491b3e114292@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 21:21:53',NULL,'2014-12-17 16:48:33',NULL,NULL),
	(7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5490a68795823@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 21:39:20',NULL,'2014-12-16 21:39:20',NULL,NULL),
	(8,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5490a68906d5c@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 21:39:21',NULL,'2014-12-16 21:39:21',NULL,NULL),
	(9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5490a9d28016b@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 21:53:22',NULL,'2014-12-16 21:53:22',NULL,NULL),
	(10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5490a9d3f0183@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 21:53:24',NULL,'2014-12-16 21:53:24',NULL,NULL),
	(11,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5490b51834467@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 22:41:28',NULL,'2014-12-16 22:41:28',NULL,NULL),
	(12,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5490b5198a2f4@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 22:41:29',NULL,'2014-12-16 22:41:29',NULL,NULL),
	(13,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5490b7062eb22@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 22:49:42',NULL,'2014-12-16 22:49:42',NULL,NULL),
	(14,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5490b7077acb5@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 22:49:43',NULL,'2014-12-16 22:49:43',NULL,NULL),
	(15,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5490bb08c6987@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 23:06:49',NULL,'2014-12-16 23:06:49',NULL,NULL),
	(16,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5490bb0a12d9f@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 23:06:50',NULL,'2014-12-16 23:06:50',NULL,NULL),
	(27,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5490bbc04cf25@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 23:09:52',NULL,'2014-12-16 23:09:52',NULL,NULL),
	(28,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5490bbc1981a6@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-16 23:09:53',NULL,'2014-12-16 23:09:53',NULL,NULL),
	(39,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5491a255e4060@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 15:33:42',NULL,'2014-12-17 15:33:42',NULL,NULL),
	(40,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5491a257290eb@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 15:33:43',NULL,'2014-12-17 15:33:43',NULL,NULL),
	(51,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5491a32f2fe18@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 15:37:19',NULL,'2014-12-17 15:37:19',NULL,NULL),
	(52,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5491a3305da26@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 15:37:20',NULL,'2014-12-17 15:37:20',NULL,NULL),
	(65,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5491a4278d42b@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 15:41:27',NULL,'2014-12-17 15:41:27',NULL,NULL),
	(66,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5491a428c347b@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 15:41:28',NULL,'2014-12-17 15:41:28',NULL,NULL),
	(79,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5491a9b25bb8d@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 16:05:06',NULL,'2014-12-17 16:05:06',NULL,NULL),
	(80,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5491a9b3a8d51@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 16:05:07',NULL,'2014-12-17 16:05:07',NULL,NULL),
	(93,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5491abb57084f@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 16:13:41',NULL,'2014-12-17 16:13:41',NULL,NULL),
	(94,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5491abb6a2b3d@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 16:13:42',NULL,'2014-12-17 16:13:42',NULL,NULL),
	(107,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5491af95bbd1a@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 16:30:13',NULL,'2014-12-17 16:30:13',NULL,NULL),
	(108,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5491af96e7dee@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 16:30:15',NULL,'014-12-17 16:30:15',NULL,NULL),
	(121,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5491b005958de@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 16:32:05',NULL,'2014-12-17 16:32:05',NULL,NULL),
	(122,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5491b006cbd82@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 16:32:06',NULL,'2014-12-17 16:32:06',NULL,NULL),
	(135,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5491b175dbf38@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 16:38:14',NULL,'2014-12-17 16:38:14',NULL,NULL),
	(136,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5491b1771845c@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 16:38:15',NULL,'2014-12-17 16:38:15',NULL,NULL),
	(149,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'facultyjobtest5491b3a0502db@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 16:47:28',NULL,'2014-12-17 16:47:28',NULL,NULL),
	(150,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'studentjobtest5491b3a18c313@mnv-tech.com',NULL,NULL,NULL,NULL,'2014-12-17 16:47:29',NULL,'2014-12-17 16:47:29',NULL,NULL);

/*!40000 ALTER TABLE `contact_info` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table contact_types
# ------------------------------------------------------------

LOCK TABLES `contact_types` WRITE;
/*!40000 ALTER TABLE `contact_types` DISABLE KEYS */;

INSERT INTO `contact_types` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `is_active`, `display_seq`, `parent_contact_types_id`)
VALUES
	(1,NULL,NULL,NULL,NULL,NULL,NULL,1,1,NULL),
	(2,NULL,NULL,NULL,NULL,NULL,NULL,1,2,NULL),
	(3,NULL,NULL,NULL,NULL,NULL,NULL,1,1,1),
	(4,NULL,NULL,NULL,NULL,NULL,NULL,1,2,1),
	(5,NULL,NULL,NULL,NULL,NULL,NULL,1,3,1),
	(6,NULL,NULL,NULL,NULL,NULL,NULL,1,4,1),
	(7,NULL,NULL,NULL,NULL,NULL,NULL,1,5,1),
	(8,NULL,NULL,NULL,NULL,NULL,NULL,1,6,1),
	(9,NULL,NULL,NULL,NULL,NULL,NULL,1,7,1),
	(10,NULL,NULL,NULL,NULL,NULL,NULL,1,8,1),
	(11,NULL,NULL,NULL,NULL,NULL,NULL,1,9,1),
	(12,NULL,NULL,NULL,NULL,NULL,NULL,1,1,2),
	(13,NULL,NULL,NULL,NULL,NULL,NULL,1,2,2),
	(14,NULL,NULL,NULL,NULL,NULL,NULL,1,3,2),
	(15,NULL,NULL,NULL,NULL,NULL,NULL,1,4,2),
	(16,NULL,NULL,NULL,NULL,NULL,NULL,1,5,2);

/*!40000 ALTER TABLE `contact_types` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table contact_types_lang
# ------------------------------------------------------------

LOCK TABLES `contact_types_lang` WRITE;
/*!40000 ALTER TABLE `contact_types_lang` DISABLE KEYS */;

INSERT INTO `contact_types_lang` (`id`, `contact_types_id`, `language_master_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `description`)
VALUES
	(1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Interaction'),
	(2,2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Non-interaction'),
	(3,3,1,NULL,NULL,NULL,NULL,NULL,NULL,'In person meeting'),
	(4,4,1,NULL,NULL,NULL,NULL,NULL,NULL,'Phone conversation'),
	(5,5,1,NULL,NULL,NULL,NULL,NULL,NULL,'Email from student'),
	(6,6,1,NULL,NULL,NULL,NULL,NULL,NULL,'Phone message received from student'),
	(7,7,1,NULL,NULL,NULL,NULL,NULL,NULL,'Message received via social networking site'),
	(8,8,1,NULL,NULL,NULL,NULL,NULL,NULL,'Written communication from student'),
	(9,9,1,NULL,NULL,NULL,NULL,NULL,NULL,'Group meeting'),
	(10,10,1,NULL,NULL,NULL,NULL,NULL,NULL,'Appointment'),
	(11,11,1,NULL,NULL,NULL,NULL,NULL,NULL,'Other interaction'),
	(12,12,1,NULL,NULL,NULL,NULL,NULL,NULL,'Email to student'),
	(13,13,1,NULL,NULL,NULL,NULL,NULL,NULL,'Phone message left for student'),
	(14,14,1,NULL,NULL,NULL,NULL,NULL,NULL,'Message sent via social networking site'),
	(15,15,1,NULL,NULL,NULL,NULL,NULL,NULL,'Written communication to student'),
	(16,16,1,NULL,NULL,NULL,NULL,NULL,NULL,'Other non-interaction');

/*!40000 ALTER TABLE `contact_types_lang` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table contacts
# ------------------------------------------------------------



# Dump of table contacts_teams
# ------------------------------------------------------------



# Dump of table datablock_master
# ------------------------------------------------------------

LOCK TABLES `datablock_master` WRITE;
/*!40000 ALTER TABLE `datablock_master` DISABLE KEYS */;

INSERT INTO `datablock_master` (`id`, `datablock_ui_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `block_type`)
VALUES
	(1,1,0,NULL,NULL,NULL,NULL,NULL,'profile'),
	(2,2,0,NULL,NULL,NULL,NULL,NULL,'profile'),
	(3,3,0,NULL,NULL,NULL,NULL,NULL,'profile'),
	(4,4,0,NULL,NULL,NULL,NULL,NULL,'profile'),
	(5,5,0,NULL,NULL,NULL,NULL,NULL,'profile'),
	(6,6,0,NULL,NULL,NULL,NULL,NULL,'profile'),
	(7,7,0,NULL,NULL,NULL,NULL,NULL,'survey'),
	(8,8,0,NULL,NULL,NULL,NULL,NULL,'survey'),
	(9,9,0,NULL,NULL,NULL,NULL,NULL,'survey'),
	(10,10,0,NULL,NULL,NULL,NULL,NULL,'survey'),
	(11,11,0,NULL,NULL,NULL,NULL,NULL,'survey'),
	(12,12,0,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `datablock_master` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table datablock_master_lang
# ------------------------------------------------------------

LOCK TABLES `datablock_master_lang` WRITE;
/*!40000 ALTER TABLE `datablock_master_lang` DISABLE KEYS */;

INSERT INTO `datablock_master_lang` (`id`, `datablock_id`, `lang_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `datablock_desc`)
VALUES
	(1,1,1,0,NULL,NULL,NULL,NULL,NULL,NULL),
	(2,2,1,0,NULL,NULL,NULL,NULL,NULL,NULL),
	(3,3,1,0,NULL,NULL,NULL,NULL,NULL,NULL),
	(4,4,1,0,NULL,NULL,NULL,NULL,NULL,NULL),
	(5,5,1,0,NULL,NULL,NULL,NULL,NULL,NULL),
	(6,6,1,0,NULL,NULL,NULL,NULL,NULL,NULL),
	(7,7,1,0,NULL,NULL,NULL,NULL,NULL,NULL),
	(8,8,1,0,NULL,NULL,NULL,NULL,NULL,NULL),
	(9,9,1,0,NULL,NULL,NULL,NULL,NULL,NULL),
	(10,10,1,0,NULL,NULL,NULL,NULL,NULL,NULL),
	(11,11,1,0,NULL,NULL,NULL,NULL,NULL,NULL),
	(12,12,1,0,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `datablock_master_lang` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table datablock_questions
# ------------------------------------------------------------



# Dump of table datablock_ui
# ------------------------------------------------------------

LOCK TABLES `datablock_ui` WRITE;
/*!40000 ALTER TABLE `datablock_ui` DISABLE KEYS */;

INSERT INTO `datablock_ui` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `key`, `ui_feature_name`)
VALUES
	(1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(8,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(11,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(12,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `datablock_ui` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ebi_config
# ------------------------------------------------------------

LOCK TABLES `ebi_config` WRITE;
/*!40000 ALTER TABLE `ebi_config` DISABLE KEYS */;

INSERT INTO `ebi_config` (`created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `key`, `value`)
VALUES
	(NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_First_Password_Expiry_Hrs','0'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Support_Helpdesk_Email_Address','support@map-works.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Staff_Support_Helpdesk_Email_Address','support@map-works.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activation_URL_Prefix','http://synapse-dev.mnv-tech.com/#/activate/'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Staff_Activation_URL_Prefix','http://synapse-dev.mnv-tech.com/#/activate/'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Staff_First_Password_Expiry_Hrs','24'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Reset_Password_Expiry_Hrs','24'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Staff_Reset_Password_Expiry_Hrs','24'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Student_Reset_Password_Expiry_Hrs','24'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'System_URL','http://synapse-dev.mnv-tech.com/'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_ResetPwd_URL_Prefix','http://synapse-dev.mnv-tech.com/#/resetPassword/'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Staff_ResetPwd_URL_Prefix','http://synapse-dev.mnv-tech.com/#/resetPassword/'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'StaffDashboard_AppointmentPage','http://synapse-dev.mnv-tech.com/#/stff'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Support_Helpdesk_Phone_Number','(888) MAP-WORKS (888-862-7967)'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Training_Site_URL','http://mapworks-training.skyfactor.com');

/*!40000 ALTER TABLE `ebi_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ebi_permissionset
# ------------------------------------------------------------

LOCK TABLES `ebi_permissionset` WRITE;
/*!40000 ALTER TABLE `ebi_permissionset` DISABLE KEYS */;

INSERT INTO `ebi_permissionset` (`id`, `is_active`, `risk_indicator`, `intent_to_leave`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `accesslevel_agg`, `accesslevel_ind_agg`, `inactive_date`)
VALUES
	(153,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,1,1,NULL),
	(268,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,1,1,NULL);

/*!40000 ALTER TABLE `ebi_permissionset` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ebi_permissionset_datablock
# ------------------------------------------------------------



# Dump of table ebi_permissionset_features
# ------------------------------------------------------------



# Dump of table ebi_permissionset_lang
# ------------------------------------------------------------

LOCK TABLES `ebi_permissionset_lang` WRITE;
/*!40000 ALTER TABLE `ebi_permissionset_lang` DISABLE KEYS */;

INSERT INTO `ebi_permissionset_lang` (`id`, `language_id`, `ebi_permissionset_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `permissionset_name`)
VALUES
	(287,1,153,NULL,NULL,NULL,NULL,NULL,NULL,'Testdjsofjasof '),
	(288,1,268,NULL,NULL,NULL,NULL,NULL,NULL,'Testosidfjaofij');

/*!40000 ALTER TABLE `ebi_permissionset_lang` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ebi_question
# ------------------------------------------------------------



# Dump of table ebi_question_options
# ------------------------------------------------------------



# Dump of table ebi_questions_lang
# ------------------------------------------------------------



# Dump of table email_template
# ------------------------------------------------------------



INSERT INTO `email_template` (`created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `email_key`, `is_active`, `from_email_address`, `bcc_recipient_list`)
VALUES
	(NULL,NULL,NULL,NULL,NULL,NULL,'Welcome_Email_Staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Forgot_Password_Staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'MyAccount_Updated_Staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Create_Password_Coordinator',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Forgot_Password_Coordinator',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Welcome_Email_Coordinator',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Sucessful_Password_Reset_Staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Sucessful_Password_Reset_Coordinator',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Appointment_Book_Staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Appointment_Update_Staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Appointment_Cancel_Staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Add_Delegate',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Remove_Delegate',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'Referral_Assign_to_staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com'),
    (NULL,NULL,NULL,NULL,NULL,NULL,'Forgot_Password_Student',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');



# Dump of table email_template_lang
# ------------------------------------------------------------


SET @welcome_staff := (SELECT id FROM email_template where email_key='Welcome_Email_Staff');
SET @Forgot_Password_Staff := (SELECT id FROM email_template where email_key='Forgot_Password_Staff');
SET @MyAccount_Updated_Staff := (SELECT id FROM email_template where email_key='MyAccount_Updated_Staff');
SET @Create_Password_Coordinator := (SELECT id FROM email_template where email_key='Create_Password_Coordinator');
SET @Forgot_Password_Coordinator := (SELECT id FROM email_template where email_key='Forgot_Password_Coordinator');
SET @Welcome_Email_Coordinator := (SELECT id FROM email_template where email_key='Welcome_Email_Coordinator');
SET @Sucessful_Password_Reset_Staff := (SELECT id FROM email_template where email_key='Sucessful_Password_Reset_Staff');
SET @Sucessful_Password_Reset_Coordinator := (SELECT id FROM email_template where email_key='Sucessful_Password_Reset_Coordinator');
SET @Appointment_Book_Staff := (SELECT id FROM email_template where email_key='Appointment_Book_Staff');
SET @Appointment_Update_Staff := (SELECT id FROM email_template where email_key='Appointment_Update_Staff');
SET @Appointment_Cancel_Staff := (SELECT id FROM email_template where email_key='Appointment_Cancel_Staff');
SET @Add_Delegate := (SELECT id FROM email_template where email_key='Add_Delegate');
SET @Remove_Delegate := (SELECT id FROM email_template where email_key='Remove_Delegate');
SET @Referral_Assign_to_staff := (SELECT id FROM email_template where email_key='Referral_Assign_to_staff');
SET @Appointment_Remainder := (SELECT id FROM email_template where email_key='Appointment_Remainder');
SET @Forgot_Password_Student := (SELECT id FROM email_template where email_key='Forgot_Password_Student');


INSERT INTO `email_template_lang` (`email_template_id`, `language_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `body`, `subject`)
VALUES
	(@welcome_staff,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/><br/>\n\nA MAP-Works password was successfully created for your account.If this was not you or you believe this is an error,\nplease contact MAP-Works support at &nbsp;<a class=\"external-link\" href=\"mailto:$$Support_Helpdesk_Email_Address$$\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">$$Support_Helpdesk_Email_Address$$</a><br/><br/>\n\nWe\'re very happy to have you on board, and are here to support you!<br/><br/>\nThank you from the MAP-Works team!\n\n</div>\n</html>','MAP-Works password created [Welcome_Email_Staff]'),
	(@Forgot_Password_Staff,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/></br>\n\nPlease use the link below and follow the displayed instructions to create your new password. This link will expire after $$Reset_Password_Expiry_Hrs$$ hours.<br />\n<br/>\n$$activation_token$$<br/><br/>\n\nIf you believe that you received this email in error or if you have any questions,please contact MAP-works support at <span style=\"color: #99ccff;\">$$Support_Helpdesk_Email_Address$$</span>.<br/><br/>\nThank you from the MAP-Works team!\n </div>\n</html>\n\n','MAP-Works - how to reset your password [Forgot_Password_Staff]'),
	(@MyAccount_Updated_Staff,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n 	\n 		<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\n 			Hi $$firstname$$,<br/><br/>\n 		\n 			An update to your MAP-Works account was successfully made. The following information was updated: <br/><br/> \n 		\n 			$$Updated_MyAccount_fields$$\n 		<br/>\n 		\n 			If this was not you or you believe this is an error, please contact MAP-Works support at&nbsp;<a class=\"external-link\" href=\"mailto:support@map-works.com\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">support@map-works.com</a></p>\n 		\n 			<br>Thank you from the MAP-Works team!</br>\n 	</div>\n </html>\n ','MAP-Works profile updated [MyAccount_Updated_Staff]'),
	(@Create_Password_Coordinator,1,NULL,NULL,NULL,NULL,NULL,NULL,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<!--[if lte IE 7]> <html class=\"ie7\">\n<style>\n    .ie7 div.forIE7{ margin-left:0px !important;}\n</style>\n <![endif]-->\n<html>\n<head>\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge, chrome=1\" />\n\n</head>\n\n<body>\n	<table cellpadding=\"58\" height=\"337px\" style=\"font-family:helvetica,arial,verdana,san-serif;font-weight:normal;width:900px; text-align:center;\">\n		<tr bgcolor=\"#eeeeee\" style=\"width:900px;padding:0px;\"><td style=\"width:900px;padding:0px;\" >\n		<table style=\"text-align:center;\" style=\"width:100%\">\n		<tr>\n		<td>\n		<table style=\"padding-top:58px;\" style=\"width:100%\">\n		<tr>\n		<td>\n		<div style=\"text-align:center;padding-top:58px;font-size: 33px;height:80px;width:900px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;color:#000000\">\n			<br>Welcome to MAP-Works.\n		</div>\n		</td>\n		</tr>\n		</table>\n		</td></tr>\n		<tr>\n		<td>\n		<div style=\"text-align:center;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;color:#333333;font-size: 16px;height:16px;padding-top:10px;\">\n			Use the link below to create your password and start using MAP-Works.\n		</div>\n		</td></tr>\n		<tr><table  cellpadding=\"40\" style=\"width:100%\">\n		<td>\n		<div style=\"text-align:center;color: #000000;font-weight:normal;font-size: 20px;\">\n		\n		<div class=\"forIE7\" style=\"margin-left: 322px;\">\n		\n		<table style=\"border-radius:2px;width:175px;font-size:20px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;text-align:center;padding:15px 0px\">\n		<tbody><tr><td style=\"background-color:#4673a7; height:60px;\"><div style=\"line-height:60px; text-decoration: none; vertical-align: middle;\"><a href=\"$$activation_token$$\" style=\"background-color: #4673a7; color: #ffffff;display: block;height: 60px;text-decoration: none;width:175px \"target=\"_blank\">Sign In Now</a></div></td></tr></tbody></table>\n		</div>\n		\n			<div style=\"margin-left:auto; margin-right:auto;width:100%;font-size: 14px;height:14px;padding-bottom:47px;font-family:helvetica,arial,verdana,san-serif;font-weight:medium;color:#333333;link:#1e73d5;padding-top:10px;\">\n				<span>Use this link to <a target=\"_blank\" style=\"link:#1e73d5;\" href=\"$$activation_token$$\">sign in.</a><span>  \n			</div>\n		</div>\n		</td></table></tr></table>\n		</td></tr>\n\n		<tr><td>\n		<div style=\"text-align:left;margin-left:10px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;\n			margin-right:18px;font-size: 13px;color: #333333;margin-top:30px;link:#1e73d5;font-weight:normal;\" >\n				<p>Thank you for participating in the spring 2015 pilot. We look forward to hearing your feedback as\n				it will inform future releases of our new student retention and success solution.\n				\n				<br><br>\n				If you have any questions, please contact us here.<br>\n				<a href=\"mailto:$$Support_Helpdesk_Email_Address$$\" style=\"link:#1e73d5;\">$$Support_Helpdesk_Email_Address$$</a> \n				<br><br>\n				Sincerely,\n				<div style=\"text-align:left;font-weight:bold;font-size: 14px;color:#333333\" >\n					<b>The EBI MAP-Works Client Services Team</b> \n					</p>\n				</div>\n		</div>\n		</td></tr>\n\n	</table>\n	</body>\n</html>\n','Map-Works - SignIn Instructions [Create_Password_Coordinator]'),
	(@Forgot_Password_Coordinator,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/></br>\n\nPlease use the link below and follow the displayed instructions to create your new password. This link will expire after $$Reset_Password_Expiry_Hrs$$ hours.<br />\n<br/>\n$$activation_token$$<br/><br/>\n\nIf you believe that you received this email in error or if you have any questions,please contact MAP-works support at <span style=\"color: #99ccff;\">$$Support_Helpdesk_Email_Address$$</span>.<br/><br/>\nThank you from the MAP-Works team!\n </div>\n</html>\n\n','MAP-Works - how to reset your password [Forgot_Password_Coordinator]'),
	(@Welcome_Email_Coordinator,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/><br/>\n\nA MAP-Works password was successfully created for your account. If this was not you or you believe this is an error,\nplease contact MAP-Works support at &nbsp;<a class=\"external-link\" href=\"mailto:support@map-works.com\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">support@map-works.com</a><br/><br/>\n\nWe\'re very happy to have you on board, and are here to support you!<br/><br/>\nThank you from the MAP-Works team!\n\n</div>\n</html>','MAP-Works password created [Welcome_Email_Coordinator]'),
	(@Sucessful_Password_Reset_Staff,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/><br/>\n\nYour MAP-Works password has been changed. If this was not you or you believe this is an error, please contact MAP-Works support at &nbsp;<a class=\"external-link\" href=\"mailto:support@map-works.com\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: none;\">support@map-works.com</a>\n<br/><br/>\nThank you from the MAP-Works team!\n\n</div>\n</html>','MAP-Works password reset [Sucessful_Password_Reset_Staff]'),
	(@Sucessful_Password_Reset_Coordinator,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/><br/>\n\nYour MAP-Works password has been changed. If this was not you or you believe this is an error, please contact MAP-Works support at &nbsp;<a class=\"external-link\" href=\"mailto:support@map-works.com\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">support@map-works.com</a>\n<br/><br/>\nThank you from the MAP-Works team!\n\n</div>\n</html>','MAP-Works password reset [Sucessful_Password_Reset_Coordinator]'),
	(@Appointment_Book_Staff,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">Dear $$firstname$$:<br/><br/>An appointment has been booked with $$staff_firstname$$ on $$app_datetime$$. To view the appointment details,<br/>please log in to your MAP-Works dashboard and visit <a class=\"external-link\" href=\"$$student_dashboard$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155);text-decoration: underline;\">MAP-Works student dashboard view appointment module</a>.<br/><br/>Best regards,<br/><br/>EBI MAP-Works<br/>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</div></html>','MAP-Works appointment booked [Appointment_Book_Staff]'),
	(@Appointment_Update_Staff,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color:rgb(255, 255, 255);\">Dear $$firstname$$:<br/><br/>A booked appointment with $$staff_firstname$$ has been modified. The appointment is now scheduled for $$app_datetime$$.<br/>To view the modified appointment details, please log in to your MAP-Works dashboard and visit <a class=\"external-link\" href=\"$$student_dashboard$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">MAP-Works student dashboard view appointment module</a>.<br/><br/>Best regards,<br/><br/>EBI MAP-Works<br/>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</div></html>','MAP-Works appointment modified [Appointment_Update_Staff]'),
	(@Appointment_Cancel_Staff,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color:rgb(255, 255, 255);\">Dear $$firstname$$:<br/><br/>Your booked appointment with $$staff_firstname$$ on $$app_datetime$$ has been cancelled.<br/>,To book a new appointment, please log in to your MAP-Works dashboard and visit <a class=\"external-link\" href=\"$$student_dashboard$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">MAP-Works student dashboard view appointment module</a>.<br/><br/>Best regards,<br/><br/>EBI MAP-Works<br/>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</div></html>','MAP-Works appointment cancelled [Appointment_Cancel_Staff]'),
	(@Add_Delegate,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\r\n  <div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\r\n    Dear $$fullname$$,\r\n    <br/>\r\n    <br/>\r\n    \r\n      You have been added as a delegate user for $$delegater_name$$\'s calendar.\r\n    <br/>\r\n    <br/>\r\n    Best regards,\r\n    <br/>\r\n    <br/>\r\n   EBI MAP-Works\r\n       <br/>\r\n   This email confirmation is an auto-generated message. Replies to automated messages are not monitored.\r\n  </div>\r\n</html>','MAP-Works added as a deligate [Added_As_Deligate]'),
	(@Remove_Delegate,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\r\n  <div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\r\n    Dear $$fullname$$,\r\n    <br/>\r\n    <br/>\r\n    \r\n      You have been removed as a delegate user for $$delegater_name$$\'s calendar.\r\n    <br/>\r\n    <br/>\r\n    Best regards,\r\n    <br/>\r\n    <br/>\r\n   EBI MAP-Works\r\n       <br/>\r\n   This email confirmation is an auto-generated message. Replies to automated messages are not monitored.\r\n  </div>\r\n</html>','MAP-Works removed as a delegate  [Removed_As_Deligate]'),
	(@Referral_Assign_to_staff,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\r\n	<head>\r\n		<style>\r\n		body {\r\n    background: none repeat scroll 0 0 #f4f4f4;\r\n	\r\n}\r\n		table {\r\n    padding: 21px;\r\n    width: 799px;\r\n	font-family: helvetica,arial,verdana,san-serif;\r\n	font-size:13px;\r\n	color:#333;\r\n	}\r\n		</style>\r\n	</head>\r\n	<body>\r\n	\r\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r\n			<tbody>\r\n			\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$firstname$$:</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A referral was recently assigned to you in MAP-Works. Please sign in to your account to view and take action on this referral.</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI MAP-Works</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r\n			\r\n			</tbody>\r\n		</table>\r\n	</body>\r\n</html>','MAP-Works - You have a new referral [Referral_Assign_to_staff]'),
	(@Appointment_Remainder,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n    <head>\n        <style>\n			 body {\n				background: none repeat scroll 0 0;	\n			\n			}\n			table {\n				padding: 21px;\n				width: 799px;\n				font-family: Helvetica,Arial,Verdana,San-serif;\n				font-size:13px;\n				color:#333;\n			}\n   </style>\n    </head>\n    <body>\n        <table cellpadding=\"10\" cellspacing=\"0\">\n            <tbody>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td>Dear $$student_name$$:</td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td style=\"line-height: 1.6;\">This is a reminder that you have an appointment with $$staff_name$$ on $$app_datetime$$. <br/><br/> To view the appointment details, please log in to your MAP-Works dashboard and visit\n					<a style=\"color: #0033CC;\" href=\"$$student_dashboard$$\">Mapworks student dashboard view appointment module</a>.\n					</td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td>Best regards,\n                        <br/>EBI MAP-Works\n                    </td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td><span style=\"font-size:11px; color: #575757; line-height: 120%; text-decoration: none;\">This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</span></td>\n                </tr>\n            </tbody>\n        </table>\n    </body>\n</html>\n','MAP-Works appointment reminder'),
    (@Forgot_Password_Student,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\nHi $$firstname$$,<br/></br>\n\nPlease use the link below and follow the displayed instructions to create your new password. This link will expire after $$Reset_Password_Expiry_Hrs$$ hours.<br />\n<br/>\n$$activation_token$$<br/><br/>\n\nIf you believe that you received this email in error or if you have any questions,please contact MAP-works support at <span style=\"color: #99ccff;\">$$Support_Helpdesk_Email_Address$$</span>.<br/><br/>\nThank you from the MAP-Works team!\n </div>\n</html>\n\n','MAP-Works - how to reset your password [Forgot_Password_Student]');



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


# Dump of table ext_log_entries
# ------------------------------------------------------------



# Dump of table ext_translations
# ------------------------------------------------------------



# Dump of table feature_master
# ------------------------------------------------------------

LOCK TABLES `feature_master` WRITE;
/*!40000 ALTER TABLE `feature_master` DISABLE KEYS */;

INSERT INTO `feature_master` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(1,NULL,NULL,NULL,NULL,NULL,NULL),
	(2,NULL,NULL,NULL,NULL,NULL,NULL),
	(3,NULL,NULL,NULL,NULL,NULL,NULL),
	(4,NULL,NULL,NULL,NULL,NULL,NULL),
	(5,NULL,NULL,NULL,NULL,NULL,NULL),
	(6,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `feature_master` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table feature_master_lang
# ------------------------------------------------------------

LOCK TABLES `feature_master_lang` WRITE;
/*!40000 ALTER TABLE `feature_master_lang` DISABLE KEYS */;

INSERT INTO `feature_master_lang` (`id`, `feature_master_id`, `lang_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `feature_name`)
VALUES
	(1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Referrals'),
	(2,2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Notes'),
	(3,3,1,NULL,NULL,NULL,NULL,NULL,NULL,'Log Contacts'),
	(4,4,1,NULL,NULL,NULL,NULL,NULL,NULL,'Booking'),
	(5,5,1,NULL,NULL,NULL,NULL,NULL,NULL,'Student Referrals'),
	(6,6,1,NULL,NULL,NULL,NULL,NULL,NULL,'Reason Routing');

/*!40000 ALTER TABLE `feature_master_lang` ENABLE KEYS */;
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


# Dump of table metadata_list_values
# ------------------------------------------------------------

LOCK TABLES `metadata_list_values` WRITE;
/*!40000 ALTER TABLE `metadata_list_values` DISABLE KEYS */;

INSERT INTO `metadata_list_values` (`id`, `metadata_id`, `lang_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `list_name`, `list_value`, `sequence`)
VALUES
	(1,1,1,NULL,'2014-09-03 04:21:13',NULL,'2014-09-03 04:21:13',NULL,NULL,'India','India',0),
	(2,1,1,NULL,'2014-09-03 04:21:13',NULL,'2014-09-03 04:21:13',NULL,NULL,'Asia','Asia',0),
	(3,1,1,NULL,'2014-09-03 04:21:13',NULL,'2014-09-03 04:21:13',NULL,NULL,'Pacific','Canada/Pacific',0),
	(4,1,1,NULL,'2014-09-03 04:21:13',NULL,'2014-09-03 04:21:13',NULL,NULL,'Pacific','US/Pacific',0);

/*!40000 ALTER TABLE `metadata_list_values` ENABLE KEYS */;
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


# Dump of table migration_versions
# ------------------------------------------------------------



# Dump of table note
# ------------------------------------------------------------



# Dump of table note_teams
# ------------------------------------------------------------



# Dump of table notification_log
# ------------------------------------------------------------



# Dump of table office_hours
# ------------------------------------------------------------



# Dump of table office_hours_series
# ------------------------------------------------------------



# Dump of table org_features
# ------------------------------------------------------------

LOCK TABLES `org_features` WRITE;
/*!40000 ALTER TABLE `org_features` DISABLE KEYS */;

INSERT INTO `org_features` (`id`, `organization_id`, `feature_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `private`, `connected`, `team`, `default_access`)
VALUES
	(1,1,1,NULL,'2014-09-12 11:06:36',NULL,'2014-10-13 11:27:24',NULL,NULL,NULL,1,NULL,NULL),
	(2,1,2,NULL,'2014-09-12 11:06:36',NULL,'2014-10-13 11:20:50',NULL,NULL,NULL,1,NULL,NULL),
	(3,1,3,NULL,'2014-09-12 11:06:36',NULL,'2014-10-13 07:56:15',NULL,NULL,NULL,1,NULL,NULL),
	(4,1,4,NULL,'2014-09-12 11:06:36',NULL,'2014-10-07 09:13:16',NULL,NULL,NULL,1,NULL,NULL),
	(5,1,5,NULL,'2014-09-12 11:06:36',NULL,'2014-10-13 14:07:52',NULL,NULL,NULL,1,NULL,NULL),
	(6,1,6,NULL,'2014-09-12 11:06:36',NULL,'2014-10-13 14:09:27',NULL,NULL,NULL,1,NULL,NULL),
	(7,1,7,NULL,'2014-09-12 11:06:36',NULL,'2014-10-13 14:09:27',NULL,NULL,NULL,1,NULL,NULL);
	(8,1,8,NULL,'2014-09-12 11:06:36',NULL,'2014-10-13 14:09:27',NULL,NULL,NULL,1,NULL,NULL);

/*!40000 ALTER TABLE `org_features` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table org_group
# ------------------------------------------------------------

LOCK TABLES `org_group` WRITE;
/*!40000 ALTER TABLE `org_group` DISABLE KEYS */;

INSERT INTO `org_group` (`id`, `organization_id`, `parent_group_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `group_name`)
VALUES
	(1,1,NULL,NULL,'2014-09-12 12:56:56',NULL,'2014-09-13 13:56:56',NULL,'2014-09-16 10:55:19','Resident Assistants');

/*!40000 ALTER TABLE `org_group` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table org_group_faculty
# ------------------------------------------------------------

LOCK TABLES `org_group_faculty` WRITE;
/*!40000 ALTER TABLE `org_group_faculty` DISABLE KEYS */;

INSERT INTO `org_group_faculty` (`id`, `org_permissionset_id`, `organization_id`, `org_group_id`, `person_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `is_invisible`)
VALUES
	(379,1,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `org_group_faculty` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table org_group_students
# ------------------------------------------------------------

LOCK TABLES `org_group_students` WRITE;
/*!40000 ALTER TABLE `org_group_students` DISABLE KEYS */;

INSERT INTO `org_group_students` (`id`, `person_id`, `org_group_id`, `organization_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(1,2,1,1,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `org_group_students` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table org_metadata
# ------------------------------------------------------------

LOCK TABLES `org_metadata` WRITE;
/*!40000 ALTER TABLE `org_metadata` DISABLE KEYS */;

INSERT INTO `org_metadata` (`id`, `organization_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `meta_key`, `meta_name`, `meta_description`, `definition_type`, `metadata_type`, `no_of_decimals`, `is_required`, `min_range`, `max_range`, `entity`, `sequence`, `meta_group`)
VALUES
	(1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Age','Age','Student Age','O','N',NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `org_metadata` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table org_metadata_list_values
# ------------------------------------------------------------



# Dump of table org_permissionset
# ------------------------------------------------------------

#LOCK TABLES `org_permissionset` WRITE;
#/*!40000 ALTER TABLE `org_permissionset` DISABLE KEYS */;

#INSERT INTO `org_permissionset` (`id`, `organization_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `permissionset_name`, `is_archived`, `accesslevel_ind_agg`, `accesslevel_agg`, `risk_indicator`, `intent_to_leave`) VALUES	(1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Permission Alpha',NULL,1,NULL,NULL,NULL),	(2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Permission Charlie',NULL,1,NULL,NULL,NULL),	(150,1,NULL,NULL,NULL,NULL,NULL,NULL,'Test',NULL,1,NULL,NULL,NULL);
#/*!40000 ALTER TABLE `org_permissionset` ENABLE KEYS */;
#UNLOCK TABLES;

# Dump of table org_permissionset_datablock
# ------------------------------------------------------------

LOCK TABLES `org_permissionset_datablock` WRITE;
/*!40000 ALTER TABLE `org_permissionset_datablock` DISABLE KEYS */;

INSERT INTO `org_permissionset_datablock` (`id`, `org_permissionset_id`, `datablock_id`, `organization_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `block_type`, `timeframe_all`, `current_calendar`, `previous_period`, `next_period`)
VALUES
	(302,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'profile',NULL,NULL,NULL,NULL),
	(303,1,7,1,NULL,NULL,NULL,NULL,NULL,NULL,'survey',NULL,NULL,NULL,NULL),
	(304,1,8,1,NULL,NULL,NULL,NULL,NULL,NULL,'survey',NULL,NULL,NULL,NULL);
	
/*
 --  Fix to add the block type 
*/	

UPDATE `org_permissionset_datablock` SET `block_type`='profile' WHERE `id`='302';
UPDATE `org_permissionset_datablock` SET `block_type`='survey' WHERE `id`='303';
UPDATE `org_permissionset_datablock` SET `block_type`='survey' WHERE `id`='304';


	

/*!40000 ALTER TABLE `org_permissionset_datablock` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table org_permissionset_features
# ------------------------------------------------------------

LOCK TABLES `org_permissionset_features` WRITE;
/*!40000 ALTER TABLE `org_permissionset_features` DISABLE KEYS */;

INSERT INTO `org_permissionset_features` (`id`, `feature_id`, `organization_id`, `org_permissionset_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `timeframe_all`, `current_calendar`, `previous_period`, `next_period`, `private_create`, `team_create`, `team_view`, `public_create`, `public_view`, `receive_referral`)
VALUES
	(141,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,1,1,1),
	(142,2,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,1,1,1),
	(143,3,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,1,1,1),
	(144,4,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,1,1,1),
	(145,5,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,1,1,1),
	(146,7,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,1,1,1);

/*!40000 ALTER TABLE `org_permissionset_features` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table org_permissionset_metadata
# ------------------------------------------------------------

LOCK TABLES `org_permissionset_metadata` WRITE;
/*!40000 ALTER TABLE `org_permissionset_metadata` DISABLE KEYS */;

INSERT INTO `org_permissionset_metadata` (`id`, `organization_id`, `org_permissionset_id`, `org_metadata_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(155,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `org_permissionset_metadata` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table org_permissionset_question
# ------------------------------------------------------------

LOCK TABLES `org_permissionset_question` WRITE;
/*!40000 ALTER TABLE `org_permissionset_question` DISABLE KEYS */;

INSERT INTO `org_permissionset_question` (`id`, `organization_id`, `org_permissionset_id`, `org_question_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(1,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `org_permissionset_question` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table org_person_faculty
# ------------------------------------------------------------

LOCK TABLES `org_person_faculty` WRITE;
/*!40000 ALTER TABLE `org_person_faculty` DISABLE KEYS */;

INSERT INTO `org_person_faculty` (`id`, `organization_id`, `person_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(1,1,4,NULL,'2014-12-16 18:16:26',NULL,'2014-12-16 18:16:26',NULL,NULL),
	(2,1,5,NULL,'2014-12-16 21:21:51',NULL,'2014-12-16 21:21:51',NULL,NULL),
	(3,1,7,NULL,'2014-12-16 21:39:20',NULL,'2014-12-16 21:39:20',NULL,NULL),
	(4,1,9,NULL,'2014-12-16 21:53:22',NULL,'2014-12-16 21:53:22',NULL,NULL),
	(5,1,11,NULL,'2014-12-16 22:41:28',NULL,'2014-12-16 22:41:28',NULL,NULL),
	(6,1,13,NULL,'2014-12-16 22:49:42',NULL,'2014-12-16 22:49:42',NULL,NULL),
	(7,1,15,NULL,'2014-12-16 23:06:49',NULL,'2014-12-16 23:06:49',NULL,NULL),
	(14,1,27,NULL,'2014-12-16 23:09:52',NULL,'2014-12-16 23:09:52',NULL,NULL),
	(21,1,39,NULL,'2014-12-17 15:33:42',NULL,'2014-12-17 15:33:42',NULL,NULL),
	(28,1,51,NULL,'2014-12-17 15:37:19',NULL,'2014-12-17 15:37:19',NULL,NULL),
	(35,1,65,NULL,'2014-12-17 15:41:27',NULL,'2014-12-17 15:41:27',NULL,NULL),
	(42,1,79,NULL,'2014-12-17 16:05:06',NULL,'2014-12-17 16:05:06',NULL,NULL),
	(49,1,93,NULL,'2014-12-17 16:13:41',NULL,'2014-12-17 16:13:41',NULL,NULL),
	(56,1,107,NULL,'2014-12-17 16:30:13',NULL,'2014-12-17 16:30:13',NULL,NULL),
	(63,1,121,NULL,'2014-12-17 16:32:05',NULL,'2014-12-17 16:32:05',NULL,NULL),
	(70,1,135,NULL,'2014-12-17 16:38:14',NULL,'2014-12-17 16:38:14',NULL,NULL),
	(77,1,149,NULL,'2014-12-17 16:47:28',NULL,'2014-12-17 16:47:28',NULL,NULL);

/*!40000 ALTER TABLE `org_person_faculty` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table org_person_student
# ------------------------------------------------------------

LOCK TABLES `org_person_student` WRITE;
/*!40000 ALTER TABLE `org_person_student` DISABLE KEYS */;

INSERT INTO `org_person_student` (`id`, `organization_id`, `person_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(1,1,6,NULL,'2014-12-16 21:21:53',NULL,'2014-12-16 21:21:53',NULL,NULL),
	(2,1,8,NULL,'2014-12-16 21:39:21',NULL,'2014-12-16 21:39:21',NULL,NULL),
	(3,1,10,NULL,'2014-12-16 21:53:24',NULL,'2014-12-16 21:53:24',NULL,NULL),
	(4,1,12,NULL,'2014-12-16 22:41:29',NULL,'2014-12-16 22:41:29',NULL,NULL),
	(5,1,14,NULL,'2014-12-16 22:49:43',NULL,'2014-12-16 22:49:43',NULL,NULL),
	(6,1,16,NULL,'2014-12-16 23:06:50',NULL,'2014-12-16 23:06:50',NULL,NULL),
	(7,1,28,NULL,'2014-12-16 23:09:53',NULL,'2014-12-16 23:09:53',NULL,NULL),
	(8,1,40,NULL,'2014-12-17 15:33:43',NULL,'2014-12-17 15:33:43',NULL,NULL),
	(9,1,52,NULL,'2014-12-17 15:37:20',NULL,'2014-12-17 15:37:20',NULL,NULL),
	(10,1,66,NULL,'2014-12-17 15:41:28',NULL,'2014-12-17 15:41:28',NULL,NULL),
	(11,1,80,NULL,'2014-12-17 16:05:07',NULL,'2014-12-17 16:05:07',NULL,NULL),
	(12,1,94,NULL,'2014-12-17 16:13:42',NULL,'2014-12-17 16:13:42',NULL,NULL),
	(13,1,108,NULL,'2014-12-17 16:30:15',NULL,'2014-12-17 16:30:15',NULL,NULL),
	(14,1,122,NULL,'2014-12-17 16:32:07',NULL,'2014-12-17 16:32:07',NULL,NULL),
	(15,1,136,NULL,'2014-12-17 16:38:15',NULL,'2014-12-17 16:38:15',NULL,NULL),
	(16,1,150,NULL,'2014-12-17 16:47:29',NULL,'2014-12-17 16:47:29',NULL,NULL);

/*!40000 ALTER TABLE `org_person_student` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table org_question
# ------------------------------------------------------------

LOCK TABLES `org_question` WRITE;
/*!40000 ALTER TABLE `org_question` DISABLE KEYS */;

INSERT INTO `org_question` (`id`, `organization_id`, `question_type_id`, `question_category_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `question_key`, `question_text`)
VALUES
	(1,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'q1','What is your name ?'),
	(2,1,2,1,NULL,NULL,NULL,NULL,NULL,NULL,'q1','What is your favorite color ?');

/*!40000 ALTER TABLE `org_question` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table org_question_options
# ------------------------------------------------------------



# Dump of table organization
# ------------------------------------------------------------

LOCK TABLES `organization` WRITE;
/*!40000 ALTER TABLE `organization` DISABLE KEYS */;

INSERT INTO `organization` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `subdomain`, `status`, `website`, `parent_organization_id`, `time_zone`, `logo_file_name`, `primary_color`, `secondary_color`, `ebi_confidentiality_statement`, `custom_confidentiality_statement`)
VALUES
	(1,NULL,NULL,NULL,'2014-10-14 10:02:48',NULL,NULL,'Northwest','A','http://www.northwest.org',NULL,'Pacific','data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAdxJREFUeNq0lj1Lw1AUhpOggrtfU9ytClpwEkFwVXDR4gdOiqhQHER/gVrwA0XFwUFRUavgP7AOXW1F66ptdPDrF7RDfQ+8gRiSJtH2wMMl7T3vuffk3nOihsNhpYTpYAT0ghZQDwrgDeTALbgAL24CqksAEV4FQzIHZMAd+OT/DUAcW/ksQZaAYReqchAfACdAA2tgz8nRspAZMAf6wRQ4s07QbA5RcA3uQchtVRYzOEfmpsEpmHcLMAw2wRXoY479Wo4+l2CdWr8CyFYPQBKMg7wS3PL0TVJLtwZY4Tj6R3F7kGoQMwM0gwjYAa8eAkXila5dnsBmjeJyFPeV8tk2x4jGS/QQ8KX6eemPoq3xsqSV8ltKtOWi1YH3Ejn387vqMOdDSoumVM6KZqn4Bk1utcpl5aqPAKL5JTt4Ah0V2EGnFEkJcAPaeB/KZTo1ExpLrdh0GQNEzTIuAbIgzpKreziqPvIvmZilZtY8RYscpQ/U/GPltcxIwdTULHV9EnSDYxaroCY+h6CLWoa9o52DRrDBcSJA+ZC0HIEesEAtx462BQbNIwaWPd6LzjkZ9ugxNhxfTT/GkquwGKZsTV8W0c7nOHNu+P2qcPpsCVFYYSC5oAmKP7sJ/AgwALtpZgbqLN7jAAAAAElFTkSuQmCC','#423131','#540521','EBI confidential statement','<br/><div><b><i></i></b><p><b><u><i><br/></i></u></b></p><p><b><u><i>hello</i></u></b></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p><p><br/></p></div>');

/*!40000 ALTER TABLE `organization` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table organization_lang
# ------------------------------------------------------------

LOCK TABLES `organization_lang` WRITE;
/*!40000 ALTER TABLE `organization_lang` DISABLE KEYS */;

INSERT INTO `organization_lang` (`organization_id`, `lang_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `organization_name`, `nick_name`)
VALUES
	(1,1,NULL,NULL,NULL,'2014-10-13 15:18:47',NULL,NULL,'Northwest','North westUniversity');

/*!40000 ALTER TABLE `organization_lang` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table organization_role
# ------------------------------------------------------------

LOCK TABLES `organization_role` WRITE;
/*!40000 ALTER TABLE `organization_role` DISABLE KEYS */;

INSERT INTO `organization_role` (`role_id`, `person_id`, `organization_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(1,1,1,NULL,NULL,NULL,NULL,NULL,NULL),
	(1,2,1,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `organization_role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table person
# ------------------------------------------------------------

LOCK TABLES `person` WRITE;
/*!40000 ALTER TABLE `person` DISABLE KEYS */;

INSERT INTO `person` (`id`,  `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `firstname`, `lastname`, `title`, `date_of_birth`, `external_id`, `username`, `password`, `activation_token`, `confidentiality_stmt_accept_date`, `organization_id`, `token_expiry_date`, `welcome_email_sent_date`)
VALUES
	(1,NULL,NULL,NULL,'2014-10-15 07:44:53',NULL,NULL,'Ramesh','Kumhar','Mr','0000-00-00','123456','ramesh.kumhar@techmahindra.com','$2y$13$f6bnaUYhaIO0qzJ0krqrIeUDnxJxWYYEyB3L6qDDK/1ln5CsHKEca','0d7bb70f71f58f0966429e41411d8b36','2014-10-14 12:01:02',1,NULL,'2014-10-15'),
	(2,NULL,NULL,NULL,'2014-10-15 07:44:53',NULL,NULL,'Bipin','P','Mr','0000-00-00','123456','bipinbihari.pradhan@techmahindra.com','$2y$13$f6bnaUYhaIO0qzJ0krqrIeUDnxJxWYYEyB3L6qDDK/1ln5CsHKEca','0d7bb70f71f58f0966429e41411d8b36','2014-10-14 12:01:02',1,NULL,'2014-10-15'),
	(3,NULL,NULL,NULL,'2014-10-15 07:44:53',NULL,NULL,'Devadoss','P','Mr','0000-00-00','123456','devadoss.poornachari@techmahindra.com','$2y$13$f6bnaUYhaIO0qzJ0krqrIeUDnxJxWYYEyB3L6qDDK/1ln5CsHKEca','0d7bb70f71f58f0966429e41411d8b36','2014-10-14 12:01:02',1,NULL,'2014-10-15'),
	(4,NULL,'2014-12-16 18:16:26',NULL,'2014-12-16 18:16:26',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest549076f97cd58@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(5,NULL,'2014-12-16 21:21:51',NULL,'2014-12-16 21:21:51',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5490a26fad566@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(6,NULL,'2014-12-16 21:21:53',NULL,'2014-12-16 21:21:53',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5490a27102010@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(7,NULL,'2014-12-16 21:39:20',NULL,'2014-12-16 21:39:20',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5490a68795823@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(8,NULL,'2014-12-16 21:39:21',NULL,'2014-12-16 21:39:21',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5490a68906d5c@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(9,NULL,'2014-12-16 21:53:22',NULL,'2014-12-16 21:53:22',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5490a9d28016b@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(10,NULL,'2014-12-16 21:53:24',NULL,'2014-12-16 21:53:24',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5490a9d3f0183@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(11,NULL,'2014-12-16 22:41:28',NULL,'2014-12-16 22:41:28',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5490b51834467@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(12,NULL,'2014-12-16 22:41:29',NULL,'2014-12-16 22:41:29',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5490b5198a2f4@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(13,NULL,'2014-12-16 22:49:42',NULL,'2014-12-16 22:49:42',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5490b7062eb22@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(14,NULL,'2014-12-16 22:49:43',NULL,'2014-12-16 22:49:43',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5490b7077acb5@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(15,NULL,'2014-12-16 23:06:49',NULL,'2014-12-16 23:06:49',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5490bb08c6987@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(16,NULL,'2014-12-16 23:06:50',NULL,'2014-12-16 23:06:50',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5490bb0a12d9f@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(27,NULL,'2014-12-16 23:09:52',NULL,'2014-12-16 23:09:52',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5490bbc04cf25@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(28,NULL,'2014-12-16 23:09:53',NULL,'2014-12-16 23:09:53',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5490bbc1981a6@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(39,NULL,'2014-12-17 15:33:42',NULL,'2014-12-17 15:33:42',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5491a255e4060@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(40,NULL,'2014-12-17 15:33:43',NULL,'2014-12-17 15:33:43',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5491a257290eb@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(51,NULL,'2014-12-17 15:37:19',NULL,'2014-12-17 15:37:19',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5491a32f2fe18@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(52,NULL,'2014-12-17 15:37:20',NULL,'2014-12-17 15:37:20',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5491a3305da26@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(65,NULL,'2014-12-17 15:41:27',NULL,'2014-12-17 15:41:27',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5491a4278d42b@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(66,NULL,'2014-12-17 15:41:28',NULL,'2014-12-17 15:41:28',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5491a428c347b@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(79,NULL,'2014-12-17 16:05:06',NULL,'2014-12-17 16:05:06',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5491a9b25bb8d@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(80,NULL,'2014-12-17 16:05:07',NULL,'2014-12-17 16:05:07',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5491a9b3a8d51@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(93,NULL,'2014-12-17 16:13:41',NULL,'2014-12-17 16:13:41',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5491abb57084f@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(94,NULL,'2014-12-17 16:13:42',NULL,'2014-12-17 16:13:42',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5491abb6a2b3d@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(107,NULL,'2014-12-17 16:30:13',NULL,'2014-12-17 16:30:13',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5491af95bbd1a@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(108,NULL,'2014-12-17 16:30:15',NULL,'2014-12-17 16:30:15',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5491af96e7dee@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(121,NULL,'2014-12-17 16:32:05',NULL,'2014-12-17 16:32:05',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5491b005958de@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(122,NULL,'2014-12-17 16:32:06',NULL,'2014-12-17 16:32:06',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5491b006cbd82@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(135,NULL,'2014-12-17 16:38:14',NULL,'2014-12-17 16:38:14',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5491b175dbf38@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(136,NULL,'2014-12-17 16:38:15',NULL,'2014-12-17 16:38:15',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5491b1771845c@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(149,NULL,'2014-12-17 16:47:28',NULL,'2014-12-17 16:47:28',NULL,NULL,'Test','User',NULL,NULL,'CreateFacultyJobTest','facultyjobtest5491b3a0502db@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL),
	(150,NULL,'2014-12-17 16:47:29',NULL,'2014-12-17 16:47:29',NULL,NULL,'Test','User',NULL,NULL,'CreateStudentJobTest','studentjobtest5491b3a18c313@mnv-tech.com',NULL,NULL,NULL,1,NULL,NULL);

/*!40000 ALTER TABLE `person` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table person_contact_info
# ------------------------------------------------------------

LOCK TABLES `person_contact_info` WRITE;
/*!40000 ALTER TABLE `person_contact_info` DISABLE KEYS */;

INSERT INTO `person_contact_info` (`person_id`, `contact_id`, `status`)
VALUES
	(1,1,'A'),
	(2,2,'A'),
	(3,3,'A'),
	(4,4,NULL),
	(5,5,NULL),
	(6,6,NULL),
	(7,7,NULL),
	(8,8,NULL),
	(9,9,NULL),
	(10,10,NULL),
	(11,11,NULL),
	(12,12,NULL),
	(13,13,NULL),
	(14,14,NULL),
	(15,15,NULL),
	(16,16,NULL),
	(27,27,NULL),
	(28,28,NULL),
	(39,39,NULL),
	(40,40,NULL),
	(51,51,NULL),
	(52,52,NULL),
	(65,65,NULL),
	(66,66,NULL),
	(79,79,NULL),
	(80,80,NULL),
	(93,93,NULL),
	(94,94,NULL),
	(107,107,NULL),
	(108,108,NULL),
	(121,121,NULL),
	(122,122,NULL),
	(135,135,NULL),
	(136,136,NULL),
	(149,149,NULL),
	(150,150,NULL);

/*!40000 ALTER TABLE `person_contact_info` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table person_entity
# ------------------------------------------------------------

LOCK TABLES `person_entity` WRITE;
/*!40000 ALTER TABLE `person_entity` DISABLE KEYS */;

INSERT INTO `person_entity` (`Person_id`, `Entity_id`)
VALUES
	(1,3),
	(2,3),
	(3,3),
	(4,3),
	(5,3),
	(6,2),
	(7,3),
	(8,2),
	(9,3),
	(10,2),
	(11,3),
	(12,2),
	(13,3),
	(14,2),
	(15,3),
	(16,2),
	(27,3),
	(28,2),
	(39,3),
	(40,2),
	(51,3),
	(52,2),
	(65,3),
	(66,2),
	(79,3),
	(80,2),
	(93,3),
	(94,2),
	(107,3),
	(108,2),
	(121,3),
	(122,2),
	(135,3),
	(136,2),
	(149,3),
	(150,2);

/*!40000 ALTER TABLE `person_entity` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table person_metadata
# ------------------------------------------------------------



# Dump of table question_category
# ------------------------------------------------------------

LOCK TABLES `question_category` WRITE;
/*!40000 ALTER TABLE `question_category` DISABLE KEYS */;

INSERT INTO `question_category` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(1,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `question_category` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table question_category_lang
# ------------------------------------------------------------

LOCK TABLES `question_category_lang` WRITE;
/*!40000 ALTER TABLE `question_category_lang` DISABLE KEYS */;

INSERT INTO `question_category_lang` (`id`, `question_category_id`, `lang_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `description`)
VALUES
	(1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Question Category 1');

/*!40000 ALTER TABLE `question_category_lang` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table question_type
# ------------------------------------------------------------

LOCK TABLES `question_type` WRITE;
/*!40000 ALTER TABLE `question_type` DISABLE KEYS */;

INSERT INTO `question_type` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(1,NULL,NULL,NULL,NULL,NULL,NULL),
	(2,NULL,NULL,NULL,NULL,NULL,NULL),
	(3,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `question_type` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table question_type_lang
# ------------------------------------------------------------

LOCK TABLES `question_type_lang` WRITE;
/*!40000 ALTER TABLE `question_type_lang` DISABLE KEYS */;

INSERT INTO `question_type_lang` (`id`, `question_type_id`, `lang_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `description`)
VALUES
	(1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Question Type 1'),
	(2,2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Question Type 2'),
	(3,3,1,NULL,NULL,NULL,NULL,NULL,NULL,'Question Type 3');

/*!40000 ALTER TABLE `question_type_lang` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table referrals
# ------------------------------------------------------------



# Dump of table referrals_interested_parties
# ------------------------------------------------------------



# Dump of table referrals_teams
# ------------------------------------------------------------



# Dump of table RefreshToken
# ------------------------------------------------------------

LOCK TABLES `RefreshToken` WRITE;
/*!40000 ALTER TABLE `RefreshToken` DISABLE KEYS */;

INSERT INTO `RefreshToken` (`id`, `client_id`, `user_id`, `token`, `expires_at`, `scope`)
VALUES
	(1,1,1,'OGU3MTUwNTQxMzc4OTQwY2RlZjMxZTY5MmM5YTIxNzE2ZWU4NWEzODhmN2IxYTg2YjM2MzJmZTZiZmQ2NmM2ZA',1410469029,'user');

/*!40000 ALTER TABLE `RefreshToken` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table role
# ------------------------------------------------------------

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;

INSERT INTO `role` (`created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `status`)
VALUES
	(NULL,NULL,NULL,NULL,NULL,NULL,'A'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'A'),
	(NULL,NULL,NULL,NULL,NULL,NULL,'A');

/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table role_lang
# ------------------------------------------------------------

LOCK TABLES `role_lang` WRITE;
/*!40000 ALTER TABLE `role_lang` DISABLE KEYS */;

INSERT INTO `role_lang` (`role_id`, `lang_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `role_name`)
VALUES
	(2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Primary coordinator'),
	(3,1,NULL,NULL,NULL,NULL,NULL,NULL,'Technical coordinator'),
	(4,1,NULL,NULL,NULL,NULL,NULL,NULL,'Non Technical coordinator');

/*!40000 ALTER TABLE `role_lang` ENABLE KEYS */;
UNLOCK TABLES;







# Dump of table system_alerts
# ------------------------------------------------------------



# Dump of table team_members
# ------------------------------------------------------------

LOCK TABLES `team_members` WRITE;
/*!40000 ALTER TABLE `team_members` DISABLE KEYS */;

INSERT INTO `team_members` (`id`, `person_id`, `organization_id`, `teams_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `is_team_leader`)
VALUES
	(1,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,1),
	(2,2,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(3,3,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(4,1,1,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(5,2,1,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(6,3,1,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(105,1,1,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(106,2,1,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(107,3,1,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `team_members` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table Teams
# ------------------------------------------------------------

LOCK TABLES `Teams` WRITE;
/*!40000 ALTER TABLE `Teams` DISABLE KEYS */;

INSERT INTO `Teams` (`id`, `organization_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `team_name`, `team_description`)
VALUES
	(1,1,NULL,'2014-09-13 13:21:03',NULL,NULL,NULL,NULL,'QATeam',NULL),
	(2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Dev Team',NULL),
	(3,1,NULL,NULL,NULL,NULL,NULL,NULL,'Front End Team',NULL),
	(4,1,NULL,NULL,NULL,NULL,NULL,NULL,'A Team',NULL);

/*!40000 ALTER TABLE `Teams` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table upload_file_log
# ------------------------------------------------------------

LOCK TABLES `upload_file_log` WRITE;
/*!40000 ALTER TABLE `upload_file_log` DISABLE KEYS */;

INSERT INTO `upload_file_log` (`id`, `organization_id`, `person_id`, `upload_type`, `upload_date`, `uploaded_columns`, `uploaded_row_count`, `status`, `uploaded_file_path`, `error_file_path`, `job_number`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `error_count`, `valid_row_count`, `viewed`, `group_id`)
VALUES
	(1,1,1,'S','2014-12-16 21:21:03','ExternalId,Firstname,Lastname,PrimaryEmail',5,'S',NULL,NULL,'1',NULL,NULL,NULL,'2014-12-17 16:48:33',NULL,NULL,0,69,NULL,NULL);

/*!40000 ALTER TABLE `upload_file_log` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table risk_model_master
# ------------------------------------------------------------

LOCK TABLES `risk_model_master` WRITE;
/*!40000 ALTER TABLE `risk_model_master` DISABLE KEYS */;

/*INSERT INTO `risk_model_master` (id,created_by,modified_by,deleted_by,created_at,modified_at,deleted_at,risk_key,effective_from,effective_to,status) VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'O',NULL,NULL,'A');*/

INSERT INTO `risk_model_master` (id,created_by,modified_by,deleted_by,created_at,modified_at,deleted_at,name,calculation_start_date,calculation_end_date,enrollment_date,model_state) VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'First',NULL,NULL,NULL,'Assigned');


/*!40000 ALTER TABLE `risk_model_master` ENABLE KEYS */;
UNLOCK TABLES;

/*
# Dump of table risk_model_master_lang
# ------------------------------------------------------------
LOCK TABLES `risk_model_master_lang` WRITE;
/*!40000 ALTER TABLE `risk_model_master_lang` DISABLE KEYS */;

INSERT INTO `risk_model_master_lang` (id,created_by,modified_by,deleted_by,risk_model_id,lang_id,created_at,modified_at,deleted_at,risk_model_name) VALUES (1,NULL,NULL,NULL,1,1,NULL,NULL,NULL,'riskModel1');

/*!40000 ALTER TABLE `risk_model_master_lang` ENABLE KEYS */;
UNLOCK TABLES;
*/

# Dump of table risk_model_levels
# ------------------------------------------------------------
LOCK TABLES `risk_model_levels` WRITE;
/*!40000 ALTER TABLE `risk_model_levels` DISABLE KEYS */;
/* modifed in risk refactoring */
#INSERT INTO `risk_model_levels` (id,created_by,modified_by,deleted_by,risk_model_id,created_at,modified_at,deleted_at,risk_level,risk_text,min,max,image_name) VALUES (1,NULL,NULL,NULL,1,NULL,NULL,NULL,'1','red2',NULL,NULL,'#c70009'),(2,NULL,NULL,NULL,1,NULL,NULL,NULL,'2','red',NULL,NULL,'#f72d35'),(3,NULL,NULL,NULL,1,NULL,NULL,NULL,'3','yellow',NULL,NULL,'#fec82a'),(4,NULL,NULL,NULL,1,NULL,NULL,NULL,'4','green',NULL,NULL,'#95cd3c'),(5,NULL,NULL,NULL,1,NULL,NULL,NULL,'5','No risk',NULL,NULL,'#cccccc');

INSERT INTO `risk_model_levels` 
(created_by,modified_by,deleted_by,risk_model_id,created_at,modified_at,deleted_at,risk_level,min,max) VALUES 
(NULL,NULL,NULL,1,NULL,NULL,NULL,'1', 1, 2.0),
(NULL,NULL,NULL,1,NULL,NULL,NULL,'2',2.1, 3.0),
(NULL,NULL,NULL,1,NULL,NULL,NULL,'3',3.1, 4.0),
(NULL,NULL,NULL,1,NULL,NULL,NULL,'4',4.1, 5.0);

/*!40000 ALTER TABLE `risk_model_levels` ENABLE KEYS */;
UNLOCK TABLES;





# Dump of table person
# ------------------------------------------------------------
LOCK TABLES `person` WRITE;
/*!40000 ALTER TABLE `person` DISABLE KEYS */;

#INSERT INTO `person` (id,created_by,created_at,modified_by,modified_at,deleted_by,deleted_at,firstname,lastname,title,date_of_birth,external_id,username,password,activation_token,confidentiality_stmt_accept_date,organization_id,token_expiry_date,welcome_email_sent_date,risk_level,risk_update_date,intent_to_leave,intent_to_leave_update_date,last_contact_date,risk_model_id)  VALUES (201,NULL,NULL,NULL,NULL,NULL,NULL,'Deav','Doss',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2014-10-14 15:18:47',1,NULL,'2014-10-13 15:18:47',1),(202,NULL,NULL,NULL,NULL,NULL,NULL,'Hari','Suthakar',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2014-10-14 15:18:47',1,NULL,'2014-10-13 15:18:47',1),(203,NULL,NULL,NULL,NULL,NULL,NULL,'Saravanan','Gopal',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,'2014-10-14 15:18:47',1,NULL,'2014-10-13 15:18:47',1),(204,NULL,NULL,NULL,NULL,NULL,NULL,'Peppin','Shaju',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,'2014-10-14 15:18:47',1,NULL,'2014-10-13 15:18:47',1),(205,NULL,NULL,NULL,NULL,NULL,NULL,'Prreth','Raj',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'2014-10-14 15:18:47',1,NULL,'2014-10-13 15:18:47',1),(206,NULL,NULL,NULL,NULL,NULL,NULL,'Bipin','Bipi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'2014-10-14 15:18:47',1,NULL,'2014-10-13 15:18:47',1),(207,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'2014-10-14 15:18:47',NULL,NULL,'2014-10-13 15:18:47',1);

INSERT INTO `person` (id,created_by,created_at,modified_by,modified_at,deleted_by,deleted_at,firstname,lastname,title,date_of_birth,external_id,username,password,activation_token,confidentiality_stmt_accept_date,organization_id,token_expiry_date,welcome_email_sent_date,risk_level,risk_update_date,intent_to_leave,intent_to_leave_update_date,last_contact_date)  VALUES 
(201,NULL,NULL,NULL,NULL,NULL,NULL,'Deav','Doss',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2014-10-14 15:18:47',1,NULL,'2014-10-13 15:18:47'),
(202,NULL,NULL,NULL,NULL,NULL,NULL,'Hari','Suthakar',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2014-10-14 15:18:47',1,NULL,'2014-10-13 15:18:47'),
(203,NULL,NULL,NULL,NULL,NULL,NULL,'Saravanan','Gopal',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,'2014-10-14 15:18:47',1,NULL,'2014-10-13 15:18:47'),
(204,NULL,NULL,NULL,NULL,NULL,NULL,'Peppin','Shaju',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,'2014-10-14 15:18:47',1,NULL,'2014-10-13 15:18:47'),
(205,NULL,NULL,NULL,NULL,NULL,NULL,'Prreth','Raj',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'2014-10-14 15:18:47',1,NULL,'2014-10-13 15:18:47'),
(206,NULL,NULL,NULL,NULL,NULL,NULL,'Bipin','Bipi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'2014-10-14 15:18:47',1,NULL,'2014-10-13 15:18:47'),
(207,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'2014-10-14 15:18:47',NULL,NULL,'2014-10-13 15:18:47');

/*!40000 ALTER TABLE `person` ENABLE KEYS */;
UNLOCK TABLES;

# Dump of table org_permissionset
# ------------------------------------------------------------
LOCK TABLES `org_permissionset` WRITE;
/*!40000 ALTER TABLE `org_permissionset` DISABLE KEYS */;

INSERT INTO `org_permissionset` (id,organization_id,created_by,created_at,modified_by,modified_at,deleted_by,deleted_at,permissionset_name,is_archived,accesslevel_ind_agg,accesslevel_agg,risk_indicator,intent_to_leave,view_courses) VALUES (1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Permission Alpha',NULL,1,NULL,NULL,1,1),(2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Permission Charlie',NULL,1,NULL,NULL,1,NULL);

/*!40000 ALTER TABLE `org_permissionset` ENABLE KEYS */;
UNLOCK TABLES;

# Dump of table alert_notifications
# ------------------------------------------------------------
LOCK TABLES `alert_notifications` WRITE;
/*!40000 ALTER TABLE `alert_notifications` DISABLE KEYS */;

INSERT INTO `alert_notifications` (id,organization_id, referrals_id,appointments_id,person_id,created_by,created_at,modified_by,modified_at,deleted_by,deleted_at,event,is_viewed,reason ) VALUES 
(1,1,NULL,2,1,NULL,'2014-08-12 10:30:00',NULL,NULL,NULL,NULL,'Appointment_Created',0,'Class attendance concern'),
(2,1,NULL,21,1,NULL,'2014-12-12 11:00:00',NULL,NULL,NULL,NULL,'Appointment_Created',0,'Registration positive '),
(3,1,NULL,23,1,NULL,'2014-12-10 11:00:00',NULL,NULL,NULL,NULL,'Appointment_Created',0,'Short term'),
(4,1,1,NULL,1,NULL,'2014-12-24 12:00:00',NULL,NULL,NULL,NULL,'Referral_Assigned',0,'Short term'),
(5,1,2,NULL,1,NULL,'2014-12-26 10:30:00',NULL,NULL,NULL,NULL,'Referral_Assigned',0,'Short term');

/*!40000 ALTER TABLE `alert_notifications` ENABLE KEYS */;
UNLOCK TABLES;

insert into synapse.email_template(email_key,from_email_address,bcc_recipient_list)values('Appointment_Reminder_Staff_to_Student','no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

insert into synapse.email_template_lang(email_template_id,language_id,body,subject)values(15,1,'<!DOCTYPE html>
<html>
<body>
Dear $$student_name$$: <br/>
This is a reminder that you have an appointment with $$staff_name$$ on $$app_datetime$$. To view the appointment details, please log in to your MAP-Works dashboard and visit $$student_dashboard$$. 
Best regards, 
EBI MAP-Works 
This email confirmation is an auto-generated message. Replies to automated messages are not monitored.
</body>
</html>
','MAP-Works appointment reminder [Appointment_reminder_Student]');

UPDATE `synapse`.`email_template_lang` SET `email_template_id`='12' WHERE `id`='12';

UPDATE `synapse`.`email_template` SET `email_key`='Appointment_Book_Staff_to_Student' WHERE `id`='9';
UPDATE `synapse`.`email_template` SET `email_key`='Appointment_Update_staff_to_Student' WHERE `id`='10';
UPDATE `synapse`.`email_template` SET `email_key`='Appointment_Cancel_Staff_to_Student' WHERE `id`='11';

INSERT INTO `synapse`.`org_person_faculty`(`id`,`organization_id`,`person_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`)VALUES(NULL,1,3,NULL,'2015-01-12 07:44:53',NULL,'2015-01-12 07:44:53',NULL,NULL);

UPDATE `synapse`.`person` SET `risk_level`='1', `risk_update_date`='2015-01-22 08:06:05', `last_contact_date`='2015-01-21 08:06:05' WHERE `id`='1';
UPDATE `synapse`.`person` SET `risk_level`='2', `risk_update_date`='2015-01-22 08:06:05', `last_contact_date`='2015-01-21 08:06:05' WHERE `id`='2';
UPDATE `synapse`.`person` SET `risk_level`='3', `risk_update_date`='2015-01-22 08:06:05', `last_contact_date`='2015-01-21 08:06:05' WHERE `id`='3';
UPDATE `synapse`.`person` SET `risk_level`='4', `risk_update_date`='2015-01-22 08:06:05', `last_contact_date`='2015-01-21 08:06:05' WHERE `id`='4';
#UPDATE `synapse`.`person` SET `risk_level`='5', `risk_update_date`='2015-01-22 08:06:05', `last_contact_date`='2015-01-21 08:06:05', `risk_model_id`='1' WHERE `id`='5';

/*
-- Logins_count view
*/

create view Logins_count AS
                Select person_id, count(id) as cnt
    from access_log
    where event = 'Login'
    group by person_id;


-- Dumping data for table `ebi_search`
--

LOCK TABLES `ebi_search` WRITE;
/*!40000 ALTER TABLE `ebi_search` DISABLE KEYS */;

/*
-- ebi_search table insert
*/
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'My_Team_Interactions_count_Groupby_Teams','E',1,'select tm.teams_id, t.team_name, count(al.id) as numbers, \'interaction\' as activity from Teams t, team_members tm, activity_log al where tm.teams_id = t.id and tm.organization_id = t.organization_id and al.organization_id = tm.organization_id and al.person_id_faculty = tm.person_id and al.activity_type in (\'R\',\'C\',\'N\',\'A\') and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and tm.teams_id in (SELECT teams_id FROM team_members where is_team_leader = 1 and person_id = \'$$loggedUserId$$\'  and deleted_at IS NULL) and tm.organization_id = \'$$organizationId$$\' and t.deleted_at IS NULL and tm.deleted_at IS NULL and al.deleted_at IS NULL group by t.team_name');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'My_Team_Open_Referrals_count_Groupby_Teams','E',1,'select tm.teams_id, t.team_name, count(al.id) as numbers, \'openreferrals\' as activity from Teams t, team_members tm, activity_log al, referrals r where tm.teams_id = t.id and tm.organization_id = t.organization_id and al.organization_id = tm.organization_id and al.person_id_faculty = tm.person_id and al.activity_type = \'R\' and r.id = al.referrals_id and r.status = \'O\' and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and tm.teams_id in (SELECT teams_id FROM team_members where is_team_leader = 1 and person_id = \'$$loggedUserId$$\' and deleted_at IS NULL) and tm.organization_id = \'$$organizationId$$\' and t.deleted_at IS NULL and tm.deleted_at IS NULL and al.deleted_at IS NULL group by t.team_name');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'My_Team_Logins_Count_Groupby_Teams','E',1,'select tm.teams_id, t.team_name, count(al.id) as numbers, \'logins\' as activity from Teams t, team_members tm, activity_log al where tm.teams_id = t.id and tm.organization_id = t.organization_id and al.organization_id = tm.organization_id and al.person_id_faculty = tm.person_id and al.activity_type = \'L\' and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and tm.teams_id in (SELECT teams_id FROM team_members where is_team_leader = 1 and person_id = \'$$loggedUserId$$\' and deleted_at IS NULL) and tm.organization_id = \'$$organizationId$$\' and t.deleted_at IS NULL and tm.deleted_at IS NULL and al.deleted_at IS NULL group by t.team_name');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'My_Team_List_with_OpenReferrals_and_otherActivities','E',1,'(select \'openreferrals\' as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id,pa.firstname as team_member_firstname,pa.lastname as team_member_lastname, pa.username as primary_email,al.person_id_student as student_id,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.firstname ELSE \'\' END) as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE \'\' END) as student_lastname,\'O\' as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, \'\' as reason_id, al.reason as reason_text,r.status,gs.person_id from activity_log al left join referrals r on r.id = al.referrals_id left outer join org_group_students gs on (al.person_id_student = gs.person_id and gs.org_group_id in (select distinct org_group_id from org_group_faculty where person_id in ($$personId$$) and deleted_at IS NULL )) left join person pa on pa.id = al.person_id_faculty left join person p on p.id = al.person_id_student where al.activity_type in (\'R\') and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and al.person_id_faculty in ($$teamMemberId$$) and al.deleted_at IS NULL and al.organization_id = \'$$organizationId$$\' group by al.id) union (select \'interactions\' as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id, pa.firstname as team_member_firstname,pa.lastname as team_member_lastname, pa.username as primary_email,al.person_id_student as student_id,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.firstname ELSE \'\' END) as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE \'\' END) as student_lastname, al.activity_type as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, \'\' as reason_id, al.reason as reason_text,\'\' as status,gs.person_id from activity_log al left outer join org_group_students gs on (al.person_id_student = gs.person_id and gs.org_group_id in (select distinct org_group_id from org_group_faculty where person_id in ($$personId$$) and deleted_at IS NULL )) left join person pa on pa.id = al.person_id_faculty left join person p on p.id = al.person_id_student where al.activity_type in (\'A\',\'C\',\'N\',\'L\') and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and al.person_id_faculty in ($$teamMemberId$$) and al.deleted_at IS NULL and al.organization_id = \'$$organizationId$$\' group by al.id) order by activity_date');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'My_Team_List_without_Referrals','E',1,'select \'interactions\' as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id,pa.firstname as team_member_firstname,pa.lastname as team_member_lastname, pa.username as primary_email,al.person_id_student as student_id, (CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.firstname ELSE \'\' END) as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE \'\' END) as student_lastname,al.activity_type as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, \'\' as reason_id, al.reason as reason_text,r.status,gs.person_id from activity_log al left join referrals r on r.id = al.referrals_id left outer join org_group_students gs on (al.person_id_student = gs.person_id and gs.org_group_id in (select distinct org_group_id from org_group_faculty where person_id in ($$personId$$) and deleted_at IS NULL )) left join person pa on pa.id = al.person_id_faculty left join person p on p.id = al.person_id_student where al.activity_type in (\'A\',\'C\',\'N\',\'R\') and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and al.person_id_faculty in ($$teamMemberId$$) and al.deleted_at IS NULL and al.organization_id = \'$$organizationId$$\'  group by al.id order by activity_date');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'My_Team_List_with_only_Logins','E',1,'select \'logins\' as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id,pa.firstname as team_member_firstname,pa.lastname as team_member_lastname, ci.primary_email, \'\' as student_id, \'\' as student_firstname,\'\' as student_lastname,al.activity_type as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, \'\' as activity_id, \'\' as reason_id, \'-\' as reason_text,\'\' as status from activity_log al left join person pa on pa.id = al.person_id_faculty left join person_contact_info pci on pci.person_id = al.person_id_faculty left join contact_info ci on ci.id = pci.contact_id where al.activity_type in (\'L\') and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and al.person_id_faculty in ($$teamMemberId$$) and al.deleted_at IS NULL and al.organization_id = \'$$organizationId$$\' order by activity_date');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'My_Team_List_with_only_OpenReferrals','E',1,'select \'openreferrals\' as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id,pa.firstname as team_member_firstname,pa.lastname as team_member_lastname, pa.username as primary_email,al.person_id_student as student_id, (CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.firstname ELSE \'\' END) as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE \'\' END) as student_lastname,\'O\' as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, \'\' as reason_id, al.reason as reason_text,r.status,gs.person_id from activity_log al left join referrals r on r.id = al.referrals_id left outer join org_group_students gs on (al.person_id_student = gs.person_id and gs.org_group_id in (select distinct org_group_id from org_group_faculty where person_id in ($$personId$$) and deleted_at IS NULL )) left join person pa on pa.id = al.person_id_faculty left join person p on p.id = al.person_id_student where al.activity_type in (\'R\') and r.status = \'O\' and al.activity_date between \'$$fromDate$$\' and \'$$toDate$$\' and al.person_id_faculty in ($$teamMemberId$$) and al.deleted_at IS NULL and al.organization_id = \'$$organizationId$$\' group by al.id order by activity_date');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'My_High_priority_students_Count','E',1,'select count(per.id) as highCount from person per where per.id in \n(select distinct person_id from org_group_students where org_group_id in (select org_group_id from org_group_faculty where person_id = $$personId$$ and deleted_at is null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null)) and deleted_at is null union select distinct person_id from org_course_student where org_courses_id in (select org_courses_id from org_course_faculty where person_id = $$personId$$ and deleted_at is null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null) and org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now()))) and deleted_at is null) and per.last_contact_date < per.risk_update_date and per.risk_level in ($$risklevel$$) and per.deleted_at is null');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'My_Total_Students_Count_Groupby_Risk','E',1,'select p.risk_level, count(p.id) as totalStudentsHighPriority, rml.risk_text, rml.image_name, rml.color_hex from person p, risk_level rml where p.id in (select distinct person_id from org_group_students where org_group_id in (select org_group_id from org_group_faculty where person_id = $$personId$$ and deleted_at is null and org_permissionset_id in (select id from org_permissionset where risk_indicator = 1 and deleted_at is null)) and deleted_at is null union select distinct person_id from org_course_student where org_courses_id in (select org_courses_id from org_course_faculty where person_id = $$personId$$ and deleted_at is null and org_permissionset_id in (select id from org_permissionset where risk_indicator = 1 and deleted_at is null) and org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now()))) and deleted_at is null) and rml.id = p.risk_level and p.deleted_at is null group by p.risk_level');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'My_High_priority_students_List','E',1,'select p.id,p.firstname, p.lastname,p.risk_level,il.image_name as intent_imagename,il.text as intent_text,rl.image_name as risk_imagename,rl.risk_text,p.intent_to_leave as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity,ps.status, il.color_hex as intent_color, rl.color_hex as risk_color, (CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($$personId$$) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then \"1\" else \"0\" end) as risk_flag ,(CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($$personId$$) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then \"1\" else \"0\" end) as intent_flag from person p join risk_level rl on (p.risk_level = rl.id) left join intent_to_leave il on (p.intent_to_leave = il.id) left join org_person_student as ps on p.id=ps.person_id left outer join Logins_count lc on (lc.person_id = p.id) where (p.id in ( select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = ($$personId$$) and deleted_at is null and org_permissionset_id in(select id from org_permissionset op where accesslevel_ind_agg = 1 and deleted_at is null) ) and ogs.deleted_at is null UNION select distinct person_id from org_course_student ocs where ocs.org_courses_id in (select org_courses_id from org_course_faculty where person_id = ($$personId$$) and deleted_at is null and org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now()))) and ocs.deleted_at is null ) ) and p.last_contact_date < p.risk_update_date and p.risk_level in ($$risklevel$$) and p.deleted_at is null order by p.risk_level desc ,p.lastname,p.firstname;');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'My_Total_students_List','E',1,'select p.id,p.firstname, p.lastname,p.risk_level,il.image_name as intent_imagename,il.text as intent_text,rl.image_name as risk_imagename,rl.risk_text,p.intent_to_leave as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity,ps.status, il.color_hex as intent_color, rl.color_hex as risk_color, (CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($$personId$$) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then \"1\" else \"0\" end) as risk_flag ,(CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($$personId$$) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then \"1\" else \"0\" end) as intent_flag from person p join risk_level rl on (p.risk_level = rl.id) left join intent_to_leave il on (p.intent_to_leave = il.id) left join org_person_student as ps on p.id=ps.person_id left outer join Logins_count lc on (lc.person_id = p.id) where (p.id in ( select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = ($$personId$$) and deleted_at is null and org_permissionset_id in(select id from org_permissionset op where accesslevel_ind_agg = 1 and deleted_at is null) ) and ogs.deleted_at is null UNION select distinct person_id from org_course_student ocs where ocs.org_courses_id in (select org_courses_id from org_course_faculty where person_id = ($$personId$$) and deleted_at is null and org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now()))) and ocs.deleted_at is null ) ) and p.risk_level in ($$risklevel$$) and p.deleted_at is null order by p.risk_level desc ,p.lastname,p.firstname;');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'My_Total_students_List_By_RiskLevel','E',1,'select p.id,p.firstname, p.lastname,p.risk_level,il.image_name as intent_imagename,il.text as intent_text,rl.image_name as risk_imagename,rl.risk_text,p.intent_to_leave as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity,ps.status, il.color_hex as intent_color, rl.color_hex as risk_color, (CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($$personId$$) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then \"1\" else \"0\" end) as risk_flag ,(CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($$personId$$) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($$personId$$) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then \"1\" else \"0\" end) as intent_flag from person p join risk_level rl on (p.risk_level = rl.id) left join intent_to_leave il on (p.intent_to_leave = il.id) left join org_person_student as ps on p.id=ps.person_id left outer join Logins_count lc on (lc.person_id = p.id) where (p.id in ( select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = ($$personId$$) and deleted_at is null and org_permissionset_id in(select id from org_permissionset op where accesslevel_ind_agg = 1 and deleted_at is null) ) and ogs.deleted_at is null UNION select distinct person_id from org_course_student ocs where ocs.org_courses_id in (select org_courses_id from org_course_faculty where person_id = ($$personId$$) and deleted_at is null and org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now()))) and ocs.deleted_at is null ) ) and rl.risk_text = \"$$riskLevel$$\" and p.deleted_at is null order by p.risk_level desc ,p.lastname,p.firstname;');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'My_Open_Referrals_Received_List','E',1,'select r.id as \'referral_id\',r.person_id_student, p.firstname,p.lastname, p.risk_level,p.intent_to_leave,rml.image_name,rml.risk_text,lc.cnt as login_cnt,p.cohert,p.last_activity FROM referrals r join person p on (r.person_id_student = p.id ) left join risk_level rml on (p.risk_level = rml.id) left outer join Logins_count lc on (lc.person_id = r.person_id_student) where  r.deleted_at IS NULL AND r.status = \'O\' and r.person_id_assigned_to = $$personid$$');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'My_Open_Referrals_Sent_List','E',1,'select r.id as \'referral_id\',r.person_id_student, p.firstname, p.lastname, p.risk_level, p.intent_to_leave,rml.image_name, rml.risk_text,lc.cnt as login_cnt, p.cohert, p.last_activity  FROM referrals r  join person p on (r.person_id_student = p.id ) LEFT join risk_level rml on (p.risk_level = rml.id) left outer join Logins_count lc on (lc.person_id = r.person_id_student)  where  r.deleted_at IS NULL AND (r.status = \'O\' or r.status=\'R\') and r.person_id_faculty = $$personid$$');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Profile_Datablock_Info','E',1,'select mm.id,mm.metadata_type,dml.datablock_desc as blockdesc,mml.meta_name,pm.metadata_value as myanswer from datablock_master dm join datablock_master_lang dml ON dm.id = dml.datablock_id JOIN  datablock_metadata dmd ON dmd.datablock_id = dm.id JOIN ebi_metadata mm ON dmd.ebi_metadata_id = mm.id JOIN ebi_metadata_lang mml ON mml.ebi_metadata_id = mm.id JOIN person_ebi_metadata pm ON pm.ebi_metadata_id = mm.id  where mml.lang_id=$$lang$$ AND dm.block_type=\"profile\" AND pm.person_id = $$studentid$$ AND dm.id IN($$datablockpermission$$) AND mm.deleted_at IS NULL AND dm.deleted_at IS NULL');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Profile_ISP_Info','E',1,'select mm.id,mm.metadata_type,mm.meta_name,pm.metadata_value as myanswer from  org_metadata mm JOIN person_org_metadata pm ON pm.org_metadata_id = mm.id  where mm.definition_type=\"O\" AND pm.person_id = $$studentid$$ AND mm.id IN($$isppermission$$) AND mm.deleted_at IS NULL');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activity_All','E',1,'SELECT A.id AS AppointmentId, N.id AS NoteId, R.id AS ReferralId, C.id AS ContactId, AL.id AS activity_log_id, AL.created_at AS activity_date, AL.activity_type AS activity_type, AL.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, C.contact_types_id AS activity_contact_type_id, CTL.description AS activity_contact_type_text, R.status AS activity_referral_status, C.note AS contactDescription, R.note AS referralDescription, A.description AS appointmentDescription, N.note AS noteDescription FROM activity_log AS AL LEFT JOIN Appointments AS A ON AL.appointments_id = A.id LEFT JOIN note AS N ON AL.note_id = N.id LEFT JOIN note_teams AS NT ON N.id = NT.note_id LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN referrals AS R ON AL.referrals_id = R.id LEFT JOIN referrals_teams AS RT ON R.id = RT.referrals_id LEFT JOIN activity_category AS AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person AS P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id WHERE AL.person_id_student = $$studentId$$ AND AL.organization_id = $$orgId$$ AND AL.activity_type IN ($$acivityArr$$) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.note_id = ALOG.note_id WHERE related.note_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.contacts_id = ALOG.contacts_id WHERE related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN AL.activity_type = \"N\" THEN CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id FROM note_teams WHERE note_id = N.id AND deleted_at IS NULL)) AND $$noteTeamAccess$$ = 1 ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $$faculty$$ ELSE N.access_public = 1 AND $$notePublicAccess$$ = 1 END END ELSE CASE WHEN AL.activity_type = \"C\" THEN CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id FROM contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL)) AND $$contactTeamAccess$$ = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$faculty$$ ELSE C.access_public = 1 AND $$contactPublicAccess$$ = 1 END END ELSE CASE WHEN AL.activity_type = \"R\" THEN CASE WHEN R.access_team = 1 THEN RT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id FROM referrals_teams WHERE referrals_id = R.id AND deleted_at IS NULL)) AND $$referralTeamAccess$$ = 1 ELSE CASE WHEN R.access_private = 1 THEN R.person_id_faculty = $$faculty$$ ELSE R.access_public = 1 AND $$referralPublicAccess$$ = 1 END END ELSE CASE WHEN AL.activity_type = \"A\" THEN 1 = 1 ELSE 1 =1 END END END END GROUP BY AL.id ORDER BY AL.created_at DESC');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activity_Note','E',1,'SELECT N.id AS activity_id, AL.id AS activity_log_id, N.created_at AS activity_date, N.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, N.note AS activity_description FROM activity_log AS AL LEFT JOIN note AS N ON AL.note_id = N.id LEFT JOIN person AS P ON N.person_id_faculty = P.id LEFT JOIN activity_category AS AC ON N.activity_category_id = AC.id LEFT JOIN note_teams AS NT ON N.id = NT.note_id WHERE AL.person_id_student = $$studentId$$ /*Student id in request parameter */ AND AL.deleted_at IS NULL AND N.deleted_at IS NULL AND CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id from note_teams WHERE note_id = N.id AND deleted_at IS NULL))AND $$teamAccess$$ = 1 /* logged in person id*/ ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $$faculty$$ /* logged in person id*/ ELSE N.access_public = 1 AND $$publicAccess$$ = 1 END END GROUP BY N.id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activity_Contact','E',1,'SELECT C.id AS activity_id, AL.id AS activity_log_id, C.created_at AS activity_date, C.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, C.note AS activity_description, C.contact_types_id AS activity_contact_type_id, CTL.description AS activity_contact_type_text FROM activity_log AS AL LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN person AS P ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category AS AC ON C.activity_category_id = AC.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id WHERE C.person_id_student = $$studentId$$ AND C.deleted_at IS NULL AND CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$facultyId$$ AND teams_id IN (SELECT teams_id from contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL))AND $$teamAccess$$ = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$facultyId$$ /* logged in person id*/ ELSE C.access_public = 1 AND $$publicAccess$$ = 1 END END GROUP BY C.id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activity_Referral','E',1,'SELECT R.id AS activity_id, AL.id AS activity_log_id, R.created_at AS activity_date, R.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, R.note AS activity_description, R.status AS activity_referral_status FROM activity_log AS AL LEFT JOIN referrals AS R ON AL.referrals_id = R.id LEFT JOIN person AS P ON R.person_id_faculty = P.id LEFT JOIN activity_category AS AC ON R.activity_category_id = AC.id LEFT JOIN referrals_teams AS RT ON R.id = RT.referrals_id WHERE R.person_id_student = $$studentId$$ /* Student id in request parameter */ AlND R.deleted_at IS NULL AND CASE WHEN access_team = 1 THEN RT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id from referrals_teams WHERE referrals_id = R.id AND deleted_at IS NULL))AND $$teamAccess$$ = 1 ELSE CASE WHEN access_private = 1 THEN R.person_id_faculty = $$faculty$$ ELSE R.access_public = 1 AND $$publicAccess$$ = 1 END END GROUP BY R.id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activity_Contact_Interaction','E',1,'SELECT C.id AS activity_id, AL.id AS activity_log_id, C.created_at AS activity_date, C.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, C.note AS activity_description, C.contact_types_id AS activity_contact_type_id, CTL.description AS activity_contact_type_text FROM activity_log AS AL LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN person AS P ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category AS AC ON C.activity_category_id = AC.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id WHERE C.person_id_student = $$studentId$$ /* Student id in request parameter */ AND (CONT.parent_contact_types_id = 1 OR CONT.parent_contact_types_id IS NULL)/* is interaction */ AND C.deleted_at IS NULL AND CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$facultyId$$ AND teams_id IN (SELECT teams_id from contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL))AND $$teamAccess$$ = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$facultyId$$ /* logged in person id*/ ELSE C.access_public = 1 AND $$publicAccess$$ = 1 END END GROUP BY C.id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activity_Count','E',1,'SELECT AL.activity_type AS activity_type FROM activity_log AS AL LEFT JOIN Appointments AS A ON AL.appointments_id = A.id LEFT JOIN note AS N ON AL.note_id = N.id LEFT JOIN note_teams AS NT ON N.id = NT.note_id LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN referrals AS R ON AL.referrals_id = R.id LEFT JOIN referrals_teams AS RT ON R.id = RT.referrals_id LEFT JOIN activity_category AS AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person AS P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id WHERE AL.person_id_student = $$studentId$$ AND AL.organization_id = $$orgId$$ AND AL.activity_type IN ($$acivityArr$$) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND CASE WHEN AL.activity_type = \"N\" THEN CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id from note_teams WHERE note_id = N.id AND deleted_at IS NULL))AND $$noteTeamAccess$$ = 1 ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $$faculty$$ ELSE N.access_public = 1 AND $$notePublicAccess$$ = 1 END END ELSE CASE WHEN AL.activity_type = \"C\" THEN CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id from contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL))AND $$contactTeamAccess$$ = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$faculty$$ ELSE C.access_public = 1 AND $$contactPublicAccess$$ = 1 END END ELSE CASE WHEN AL.activity_type = \"R\" THEN CASE WHEN R.access_team = 1 THEN RT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id from referrals_teams WHERE referrals_id = R.id AND deleted_at IS NULL))AND $$referralTeamAccess$$ = 1 ELSE CASE WHEN R.access_private = 1 THEN R.person_id_faculty = $$faculty$$ ELSE R.access_public = 1 AND $$referralPublicAccess$$ = 1 END END ELSE CASE WHEN AL.activity_type = \"A\" THEN 1 = 1 ELSE 1 =1 END END END END GROUP BY AL.id ORDER BY AL.created_at DESC');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activity_Contact_Int_Count','E',1,'SELECT COUNT(DISTINCT(C.id)) AS cnt FROM activity_log AS AL LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN person AS P ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category AS AC ON C.activity_category_id = AC.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id WHERE C.person_id_student = $$studentId$$ /* Student id in request parameter */ AND (CONT.parent_contact_types_id = 1 OR CONT.parent_contact_types_id IS NULL)/* is interaction */ AND C.deleted_at IS NULL AND CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$facultyId$$ AND teams_id IN (SELECT teams_id FROM contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL)) AND $$teamAccess$$ = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$facultyId$$ /* logged in person id*/ ELSE C.access_public = 1 AND $$publicAccess$$ = 1 END END');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_All','E',1,'SELECT A.id as AppointmentId, N.id as NoteId,R.id as ReferralId,C.id  as ContactId,AL.id as activity_log_id,AL.created_at as activity_date,AL.activity_type  as activity_type,AL.person_id_faculty as activity_created_by_id,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text,C.contact_types_id as activity_contact_type_id,CTL.description  as activity_contact_type_text,R.status as activity_referral_status,C.note as contactDescription,R.note as referralDescription,A.description as appointmentDescription,N.note as noteDescription FROM activity_log as AL LEFT JOIN Appointments as A ON AL.appointments_id = A.id LEFT JOIN note as N ON AL.note_id = N.id LEFT JOIN note_teams  as NT ON N.id = NT.note_id LEFT JOIN contacts as C ON AL.contacts_id = C.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id LEFT JOIN referrals as R ON AL.referrals_id = R.id LEFT JOIN referrals_teams  as RT ON R.id = RT.referrals_id LEFT JOIN activity_category as AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person as P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id WHERE AL.person_id_student = $$studentId$$ AND AL.organization_id = $$orgId$$ AND AL.activity_type IN ($$acivityArr$$) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND  AL.id NOT IN( SELECT ALOG.id  FROM  related_activities as related  LEFT JOIN activity_log as ALOG ON related.note_id = ALOG.note_id where related.note_id IS NOT NULL  AND related.deleted_at IS NULL  AND ALOG.deleted_at IS NULL)  AND  AL.id NOT IN( SELECT ALOG.id  FROM  related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL  AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) GROUP BY AL.id ORDER BY AL.created_at desc');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_Note','E',1,'SELECT N.id as activity_id,AL.id as activity_log_id,  N.created_at as  activity_date,  N.person_id_faculty as activity_created_by_id , P.firstname as activity_created_by_first_name, P.lastname as activity_created_by_last_name, AC.id as activity_reason_id, AC.short_name as activity_reason_text, N.note as activity_description FROM activity_log as AL  LEFT JOIN note as N ON  AL.note_id = N.id LEFT JOIN person as P ON  N.person_id_faculty = P.id LEFT JOIN activity_category as AC ON N.activity_category_id  = AC.id LEFT JOIN note_teams  as NT ON N.id = NT.note_id WHERE AL.person_id_student = $$studentId$$ /*Student id in request parameter */ AND AL.deleted_at IS NULL AND N.deleted_at IS NULL AND AL.activity_type = \"N\"  GROUP BY N.id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_Contact','E',1,'SELECT  C.id as activity_id, AL.id as activity_log_id, C.created_at as  activity_date, C.person_id_faculty as activity_created_by_id , P.firstname as activity_created_by_first_name, P.lastname as activity_created_by_last_name, AC.id as activity_reason_id, AC.short_name as activity_reason_text, C.note as activity_description, C.contact_types_id  as activity_contact_type_id,CTL.description  as activity_contact_type_text FROM activity_log as AL LEFT JOIN contacts as C ON  AL.contacts_id = C.id LEFT JOIN person as P  ON C.person_id_faculty = P.id  LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category as AC ON C.activity_category_id  =  AC.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id  WHERE C.person_id_student  = $$studentId$$ AND C.deleted_at  IS NULL  GROUP BY C.id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_Referral','E',1,'SELECT R.id as activity_id,AL.id as activity_log_id,R.created_at as  activity_date,R.person_id_faculty as activity_created_by_id ,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text, R.note as activity_description,R.status as activity_referral_status FROM activity_log as AL LEFT JOIN referrals as R ON  AL.referrals_id = R.id LEFT JOIN person as P ON  R.person_id_faculty = P.id LEFT JOIN activity_category as AC ON R.activity_category_id  =  AC.id LEFT JOIN referrals_teams  as RT ON R.id = RT.referrals_id  WHERE R.person_id_student = $$studentId$$ /* Student id in request parameter */ AND R.deleted_at IS NULL GROUP BY R.id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_Contact_Interaction','E',1,'SELECT C.id as activity_id, AL.id as activity_log_id,C.created_at as  activity_date,C.person_id_faculty as activity_created_by_id ,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text,C.note as activity_description,C.contact_types_id  as activity_contact_type_id,CTL.description  as activity_contact_type_text FROM activity_log as AL LEFT JOIN contacts as C ON  AL.contacts_id = C.id LEFT JOIN person as P  ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category as AC ON C.activity_category_id  =  AC.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id LEFT JOIN contact_types  as CONT ON C.contact_types_id = CONT.id WHERE C.person_id_student  = $$studentId$$ /* Student id in request parameter */ AND (CONT.parent_contact_types_id = 1 OR CONT.parent_contact_types_id IS NULL) /* is interaction */ AND C.deleted_at  IS NULL GROUP BY C.id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_Count','E',1,'SELECT  AL.activity_type as activity_type FROM activity_log as AL LEFT JOIN Appointments as A ON AL.appointments_id = A.id LEFT JOIN note as N ON AL.note_id = N.id LEFT JOIN note_teams  as NT ON N.id = NT.note_id LEFT JOIN contacts as C ON AL.contacts_id = C.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id LEFT JOIN referrals as R ON AL.referrals_id = R.id LEFT JOIN referrals_teams  as RT ON R.id = RT.referrals_id LEFT JOIN activity_category as AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person as P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id WHERE AL.person_id_student = $$studentId$$ AND AL.organization_id = $$orgId$$ AND AL.activity_type IN ($$acivityArr$$) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL GROUP BY AL.id ORDER BY AL.created_at desc');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_Contact_Int_Count','E',1,'SELECT COUNT(DISTINCT(C.id)) as cnt FROM activity_log as AL LEFT JOIN contacts as C ON  AL.contacts_id = C.id LEFT JOIN person as P  ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category as AC ON C.activity_category_id  =  AC.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id LEFT JOIN contact_types  as CONT ON C.contact_types_id = CONT.id WHERE C.person_id_student  = $$studentId$$ /* Student id in request parameter */ AND (CONT.parent_contact_types_id = 1 OR CONT.parent_contact_types_id IS NULL) /* is interaction */ AND C.deleted_at  IS NULL AND  AL.id NOT IN( SELECT ALOG.id  FROM  related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL)');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'All_My_Student','P',1,'SELECT OC.org_academic_year_id, OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OC.org_academic_year_id = $$yearId$$ AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id ');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'My_Primary_Campus_Connection','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OC.deleted_at IS NULL AND OPS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND OPS.person_id_primary_connect = $$facultyId$$ AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Class_Level','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN person_ebi_metadata AS PEM ON PEM.person_id = OGS.person_id WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND PEM.deleted_At IS NULL AND PEM.ebi_metadata_id IN (SELECT id FROM ebi_metadata WHERE meta_key=\"Class Level\") GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'At_Risk','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OC.org_academic_year_id = $$yearId$$ AND P.risk_level IN(1,2) AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'High_Priority_Students','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND P.risk_level IN(1,2) AND OPS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND OGS.person_id IN ( $$personIds$$ ) AND (P.risk_update_date IS NOT NULL AND P.risk_update_date > P.last_contact_date)');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Respondents_To_Current_Survey','P',1,'SELECT SR.id AS srid, OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN survey_response AS SR ON SR.person_id = OGS.person_id WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND SR.org_id = $$orgId$$ AND SR.org_academic_year_id = $$yearId$$ AND OC.org_academic_year_id = $$yearId$$ AND OGS.person_id IN ( $$personIds$$ ) AND SR.survey_id IN (SELECT DISTINCT(survey_id) FROM wess_link WHERE year_id = $$yearText$$ AND status= \"launched\") GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Non_Respondents_To_Current_Survey','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OC.org_academic_year_id = $$yearId$$ AND OGS.person_id IN ( $$personIds$$ ) AND OGS.person_id NOT IN (SELECT DISTINCT(person_id) FROM survey_response AS SR WHERE SR.org_id = $$orgId$$ AND SR.org_academic_year_id = $$yearId$$ AND SR.survey_id IN (SELECT DISTINCT(survey_id) FROM wess_link WHERE year_id = $$yearText$$ AND status= \"launched\")) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Accessed_Current_Survey_Report','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN org_survey_report_access_history AS SR ON SR.person_id = OGS.person_id WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OC.org_academic_year_id = $$yearId$$ AND SR.org_id = $$orgId$$ AND SR.year_id = $$yearText$$ AND OGS.person_id IN ( $$personIds$$ ) AND SR.survey_id IN (SELECT DISTINCT(survey_id) FROM wess_link WHERE year_id = $$yearText$$ AND status= \"launched\") AND SR.deleted_at IS NULL GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Not_Accessed_Current_Survey_Report','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OC.org_academic_year_id = $$yearId$$ AND OGS.person_id IN ( $$personIds$$ ) AND OGS.person_id NOT IN (SELECT DISTINCT(person_id) FROM org_survey_report_access_history AS SR WHERE SR.org_id = $$orgId$$ AND SR.year_id = $$yearText$$ AND SR.survey_id IN (SELECT DISTINCT(survey_id) FROM wess_link WHERE year_id = $$yearText$$ AND status= \"launched\" AND SR.deleted_at IS NULL)) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'High_Intent_To_Leave','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OGS.person_id IN ( $$personIds$$ ) AND P.intent_to_leave = 1 GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'At_Risk_Of_Failure','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND AU.failure_risk_level = \"high\" AND AU.update_date BETWEEN \"$$startDate$$\" AND \"$$endDate$$\" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Missed_3_Classes','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND AU.absence > 2 AND AU.update_date BETWEEN \"$$startDate$$\" AND \"$$endDate$$\" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'In-progress_Grade_Of_C_Or_Below','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND ASCII(AU.grade) >= 67 AND AU.update_date BETWEEN \"$$startDate$$\" AND \"$$endDate$$\" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Final_Grade_Of_C_Or_Below','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND ASCII(AU.final_grade) >= 67 AND AU.update_date BETWEEN \"$$startDate$$\" AND \"$$endDate$$\" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'In-progress_Grade_Of_D_Or_Below','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND ASCII(AU.grade) >= 68 AND AU.update_date BETWEEN \"$$startDate$$\" AND \"$$endDate$$\" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Final_Grade_Of_D_Or_Below','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND ASCII(AU.final_grade) >= 68 AND AU.update_date BETWEEN \"$$startDate$$\" AND \"$$endDate$$\" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY OGS.person_id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Students_With_More_Than_One_In-progress_Grade_Of_D_Or_Below','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url, AU.grade, count(AU.person_id_student) AS t FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND ASCII(AU.grade) >= 68 AND AU.update_date BETWEEN \"$$startDate$$\" AND \"$$endDate$$\" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY AU.person_id_student HAVING t > 1');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Students_With_More_Than_One_Final_Grade_Of_D_Or_Below','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url, AU.final_grade, count(AU.person_id_student) AS t FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN academic_update AU ON (AU.person_id_student = OGS.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND ASCII(AU.final_grade) >= 68 AND AU.update_date BETWEEN \"$$startDate$$\" AND \"$$endDate$$\" AND OGS.person_id IN ( $$personIds$$ ) GROUP BY AU.person_id_student HAVING t > 1 ');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Interaction_Activity','P',1,'SELECT C.person_id_faculty AS faculty, C.person_id_student AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM activity_log AS AL LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN person AS P ON C.person_id_student = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category AS AC ON C.activity_category_id = AC.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = C.person_id_student LEFT JOIN org_course_student AS OCS ON OCS.person_id = C.person_id_student LEFT JOIN org_courses AS OC ON (OCS.org_courses_id = OC.id) LEFT JOIN Logins_count LC ON (LC.person_id = C.person_id_student) WHERE C.person_id_faculty = $$facultyId$$ AND (CONT.parent_contact_types_id = 1 OR CONT.id = 1) AND C.deleted_at IS NULL AND OC.org_academic_year_id = $$yearId$$ AND C.person_id_student IN ( $$personIds$$ ) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.contacts_id = ALOG.contacts_id WHERE related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$facultyId$$)/* logged in person id*/ ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$facultyId$$ /* logged in person id*/ ELSE C.access_public = 1 END END GROUP BY C.id');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Non-interaction_Activity','P',1,'SELECT C.person_id_faculty AS faculty, C.person_id_student AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM activity_log AS AL LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN person AS P ON C.person_id_student = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category AS AC ON C.activity_category_id = AC.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = C.person_id_student LEFT JOIN org_course_student AS OCS ON OCS.person_id = C.person_id_student LEFT JOIN org_courses AS OC ON (OCS.org_courses_id = OC.id) LEFT JOIN Logins_count LC ON (LC.person_id = C.person_id_student) WHERE C.person_id_faculty = $$facultyId$$ AND (CONT.parent_contact_types_id = 2 OR CONT.id = 2) AND C.deleted_at IS NULL AND OC.org_academic_year_id = $$yearId$$ AND C.person_id_student IN ( $$personIds$$ ) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.contacts_id = ALOG.contacts_id WHERE related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$facultyId$$)/* logged in person id*/ ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$facultyId$$ /* logged in person id*/ ELSE C.access_public = 1 END END GROUP BY C.id ');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Have_Not_Been_Reviewed','P',1,'SELECT OGF.person_id AS faculty, OGS.person_id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM synapse.org_course_faculty AS OGF LEFT JOIN org_course_student AS OGS ON (OGF.org_courses_id = OGS.org_courses_id AND OGF.organization_id = OGS.organization_id) LEFT JOIN org_courses as OC ON (OGF.org_courses_id = OC.id) LEFT JOIN person AS P ON OGS.person_id = P.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = OGS.person_id LEFT JOIN Logins_count LC ON (LC.person_id = OGS.person_id) LEFT JOIN student_db_view_log SDV ON (SDV.person_id_student = OGS.person_id AND SDV.person_id_faculty = OGF.person_id) WHERE OGF.person_id IN ( $$facultyIds$$ ) AND OGF.organization_id = $$orgId$$ AND OGS.organization_id = $$orgId$$ AND OC.org_academic_year_id = $$yearId$$ AND P.deleted_at IS NULL AND OGF.deleted_at IS NULL AND OGS.deleted_at IS NULL AND OGS.person_id IN ( $$personIds$$ ) AND SDV.organization_id = $$orgId$$ AND SDV.last_viewed_on > P.risk_update_date');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_All_Interaction','E',1,'SELECT A.id as AppointmentId, N.id as NoteId,R.id as ReferralId,C.id as ContactId,AL.id as activity_log_id,AL.created_at as activity_date,AL.activity_type as activity_type,AL.person_id_faculty as activity_created_by_id,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text,C.contact_types_id as activity_contact_type_id,CTL.description as activity_contact_type_text,R.status as activity_referral_status,C.note as contactDescription,R.note as referralDescription,A.description as appointmentDescription,N.note as noteDescription FROM activity_log as AL LEFT JOIN Appointments as A ON AL.appointments_id = A.id LEFT JOIN note as N ON AL.note_id = N.id LEFT JOIN note_teams as NT ON N.id = NT.note_id LEFT JOIN contacts as C ON AL.contacts_id = C.id LEFT JOIN contacts_teams as CT ON C.id = CT.contacts_id LEFT JOIN referrals as R ON AL.referrals_id = R.id LEFT JOIN referrals_teams as RT ON R.id = RT.referrals_id LEFT JOIN activity_category as AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person as P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN contact_types as CONT ON C.contact_types_id = CONT.id WHERE AL.person_id_student = $$studentId$$ AND AL.organization_id = $$orgId$$ AND AL.activity_type IN ($$acivityArr$$) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND CASE WHEN AL.activity_type = \"C\" THEN CONT.parent_contact_types_id = 1 OR CONT.id =1 ELSE 1=1 END AND AL.id NOT IN( SELECT ALOG.id FROM related_activities as related LEFT JOIN activity_log as ALOG ON related.note_id = ALOG.note_id where related.note_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN( SELECT ALOG.id FROM related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) GROUP BY AL.id ORDER BY AL.created_at desc');
INSERT INTO `ebi_search` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activity_All_Interaction','E',1,'SELECT A.id AS AppointmentId, N.id AS NoteId, R.id AS ReferralId, C.id AS ContactId, AL.id AS activity_log_id, AL.created_at AS activity_date, AL.activity_type AS activity_type, AL.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, C.contact_types_id AS activity_contact_type_id, CTL.description AS activity_contact_type_text, R.status AS activity_referral_status, C.note AS contactDescription, R.note AS referralDescription, A.description AS appointmentDescription, N.note AS noteDescription FROM activity_log AS AL LEFT JOIN Appointments AS A ON AL.appointments_id = A.id LEFT JOIN note AS N ON AL.note_id = N.id LEFT JOIN note_teams AS NT ON N.id = NT.note_id LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN referrals AS R ON AL.referrals_id = R.id LEFT JOIN referrals_teams AS RT ON R.id = RT.referrals_id LEFT JOIN activity_category AS AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person AS P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id WHERE AL.person_id_student = $$studentId$$ AND AL.organization_id = $$orgId$$ AND AL.activity_type IN ($$acivityArr$$) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND CASE WHEN AL.activity_type = \"C\" THEN CONT.parent_contact_types_id = 1 OR CONT.id =1 ELSE 1=1 END AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.note_id = ALOG.note_id WHERE related.note_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.contacts_id = ALOG.contacts_id WHERE related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN AL.activity_type = \"N\" THEN CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id FROM note_teams WHERE note_id = N.id AND deleted_at IS NULL)) AND $$noteTeamAccess$$ = 1 ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $$faculty$$ ELSE N.access_public = 1 AND $$notePublicAccess$$ = 1 END END ELSE CASE WHEN AL.activity_type = \"C\" THEN CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id FROM contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL)) AND $$contactTeamAccess$$ = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$faculty$$ ELSE C.access_public = 1 AND $$contactPublicAccess$$ = 1 END END ELSE CASE WHEN AL.activity_type = \"R\" THEN CASE WHEN R.access_team = 1 THEN RT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id FROM referrals_teams WHERE referrals_id = R.id AND deleted_at IS NULL)) AND $$referralTeamAccess$$ = 1 ELSE CASE WHEN R.access_private = 1 THEN R.person_id_faculty = $$faculty$$ ELSE R.access_public = 1 AND $$referralPublicAccess$$ = 1 END END ELSE CASE WHEN AL.activity_type = \"A\" THEN 1 = 1 ELSE 1 =1 END END END END GROUP BY AL.id ORDER BY AL.created_at DESC');



/*!40000 ALTER TABLE `ebi_search` ENABLE KEYS */;
UNLOCK TABLES;


	

--
-- Dumping data for table `ebi_search_lang`
--

LOCK TABLES `ebi_search_lang` WRITE;
/*!40000 ALTER TABLE `ebi_search_lang` DISABLE KEYS */;
/*
-- ebi_search_lang table insert
*/

INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Get my teams recent activities');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (2,2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Get my teams recent open referrals activities');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (3,3,1,NULL,NULL,NULL,NULL,NULL,NULL,'Get my teams recent login activities');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (4,4,1,NULL,NULL,NULL,NULL,NULL,NULL,'Get my team all activities in detail');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (5,5,1,NULL,NULL,NULL,NULL,NULL,NULL,'Get my team interactions in detail');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (6,6,1,NULL,NULL,NULL,NULL,NULL,NULL,'Get my team login in detail');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (7,7,1,NULL,NULL,NULL,NULL,NULL,NULL,'Get my team open referrals in detail');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (8,14,1,NULL,NULL,NULL,NULL,NULL,NULL,'High priority students');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (9,15,1,NULL,NULL,NULL,NULL,NULL,NULL,'Total Students Count Groupby Risk');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (10,16,1,NULL,NULL,NULL,NULL,NULL,NULL,'My_High_priority_students_List');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (11,17,1,NULL,NULL,NULL,NULL,NULL,NULL,'My Total Students List');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (12,18,1,NULL,NULL,NULL,NULL,NULL,NULL,'My Total Students List By RiskLevel');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (13,19,1,NULL,NULL,NULL,NULL,NULL,NULL,'My Open Referrals Received List');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (14,20,1,NULL,NULL,NULL,NULL,NULL,NULL,'My Open Referrals Sent List');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (15,21,1,NULL,NULL,NULL,NULL,NULL,NULL,'To Get Student Profile Databalock');
INSERT INTO `ebi_search_lang` (`id`,`ebi_search_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (16,22,1,NULL,NULL,NULL,NULL,NULL,NULL,'To Get Student Profile ISPs');

/*!40000 ALTER TABLE `ebi_search_lang` ENABLE KEYS */;
UNLOCK TABLES;


INSERT INTO `ebi_question` VALUES (1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'key 1',0,'Q101'),(2,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'key 2',0,'Q102'),(3,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'key 3',0,'Q103'),(4,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'key 4',0,'Q104'),(5,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'key 5',0,'Q105'),(6,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'key 6',0,'Q106'),(7,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'key 7',0,'Q107');


INSERT INTO `talking_points` VALUES (1,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'S','W',1,4),(2,NULL,NULL,NULL,2,NULL,NULL,NULL,NULL,'S','W',1,4),(3,NULL,NULL,NULL,3,NULL,NULL,NULL,NULL,'S','W',1,4),(4,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'S','S',5,8),(5,NULL,NULL,NULL,2,NULL,NULL,NULL,NULL,'S','S',5,8),(6,NULL,NULL,NULL,3,NULL,NULL,NULL,NULL,'S','S',5,8),(7,NULL,NULL,NULL,4,NULL,NULL,NULL,NULL,'S','W',1,4),(8,NULL,NULL,NULL,5,NULL,NULL,NULL,NULL,'S','W',1,4),(9,NULL,NULL,NULL,6,NULL,NULL,NULL,NULL,'S','S',5,8),(10,NULL,NULL,NULL,7,NULL,NULL,NULL,NULL,'S','S',5,8);

INSERT INTO `talking_points_lang` VALUES (1,NULL,NULL,NULL,1,1,NULL,NULL,NULL,'Spring Transition: Commitment to Institution','Not committed to return next term. Strong predictor of attrition. Discuss future plans to determine academic goals.'),(2,NULL,NULL,NULL,2,1,NULL,NULL,NULL,'Spring Transition: Communications Skills','Reports poor communication skills. Refer to learning resources.'),(3,NULL,NULL,NULL,3,1,NULL,NULL,NULL,'Spring Transition: Class Attendance','Lorem ipsm dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'),(4,NULL,NULL,NULL,4,1,NULL,NULL,NULL,'Fall Check-Up: Basic Academic Behaviors','Reports good basic academic behaviours(i.e., talking good notes and turning in requited homework). Strong predictor of academic performance. Student typically overestimate these skills; may still need some improvement. Ask about grades on assignments/tests.'),(5,NULL,NULL,NULL,5,1,NULL,NULL,NULL,'Fall Mid-Terms - Fall Mid-Term Grades - Green','Little to no risk for poor academic performance(not deficient in any course).'),(6,NULL,NULL,NULL,6,1,NULL,NULL,NULL,'Fall Check-Up: Basic Academic Behaviours','Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna'),(7,NULL,NULL,NULL,7,1,NULL,NULL,NULL,'Spring Transition: Other Academic concerns','Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.Ut enim ad minim veniam, quis nostrud exercitation'),(8,NULL,NULL,NULL,8,1,NULL,NULL,NULL,'Spring Transition: Academic Skills','Strong predictor of attrition. Discuss future plans to determine academic skills'),(9,NULL,NULL,NULL,9,1,NULL,NULL,NULL,'Fall Check-Up: Academic Skills','Good academic performance through skills exploration.  may still need some improvement'),(10,NULL,NULL,NULL,10,1,NULL,NULL,NULL,'Fall Mid-Terms - Academic Performance','No risk for poor academic performance(not deficient in any course).');

INSERT INTO `org_talking_points` VALUES (1,NULL,NULL,NULL,1,2,1,1,'2014-12-24 14:30:00',NULL,NULL,'3'),(2,NULL,NULL,NULL,1,2,2,2,'2014-12-24 14:40:00',NULL,NULL,'2'),(3,NULL,NULL,NULL,1,2,3,3,'2014-12-24 16:40:00',NULL,NULL,'3'),(4,NULL,NULL,NULL,1,2,4,4,'2014-12-27 14:40:00',NULL,NULL,'7'),(5,NULL,NULL,NULL,1,2,5,5,'2014-12-30 14:40:00',NULL,NULL,'6'),(6,NULL,NULL,NULL,1,2,6,6,'2014-12-31 11:40:00',NULL,NULL,'6'),(7,NULL,NULL,NULL,1,2,7,7,'2015-01-01 10:20:00',NULL,NULL,'4'),(8,NULL,NULL,NULL,1,2,8,8,'2015-01-01 09:20:00',NULL,NULL,'3'),(9,NULL,NULL,NULL,1,2,9,9,'2015-01-02 09:20:00',NULL,NULL,'6'),(10,NULL,NULL,NULL,1,2,10,10,'2015-01-02 09:10:00',NULL,NULL,'7');

INSERT INTO `synapse`.`survey` (`id`,`external_id`) VALUES ('1','1647');
INSERT INTO `synapse`.`survey` (`id`,`external_id`) VALUES ('2','1648');
INSERT INTO `synapse`.`survey` (`id`,`external_id`) VALUES ('3','1649');
INSERT INTO `synapse`.`survey` (`id`,`external_id`) VALUES ('4','1650');
INSERT INTO `synapse`.`survey` (`id`) VALUES ('5');
INSERT INTO `synapse`.`survey` (`id`) VALUES ('6');
INSERT INTO `synapse`.`survey` (`id`) VALUES ('7');
INSERT INTO `synapse`.`survey` (`id`) VALUES ('8');
INSERT INTO `synapse`.`survey` (`id`) VALUES ('9');
INSERT INTO `synapse`.`survey` (`id`) VALUES ('10');






/*
* -- ESPRJ-600 defect fix
*/

UPDATE `synapse`.`email_template_lang` SET `body`='<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\n<head>\n    <title>Email</title>\n</head>\n<body>\n<center>\n    <table align=\"center\">\n        <tr>\n            <th style=\"padding:0px;margin:0px;\">\n               \n               <table  style=\"font-family:helvetica,arial,verdana,san-serif;font-weight:normal;width:800px; height=337px;text-align:center;padding:0px;\">\n               <tr bgcolor=\"#eeeeee\" style=\"width:800px;padding:0px;height:337px;\">\n               <td style=\"width:800px;padding:0px;height:337px;\">\n               <table style=\"text-align:center;width:100%\">\n               <tr>\n                    <td style=\"padding:0px;\">\n                    <table style=\"margin-top:56px;width:100%\">\n		<tr>\n		<td style=\"text-align:center;padding:0px;font-size:33px;height:80px;width:800px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;color:#000000\">\n					<br>Welcome to MAP-Works.		\n		</td>\n		</tr>\n		</table>\n                    </td>\n               </tr>\n               <tr style=\"margin:0px;padding:0px;\">\n		<td style=\"text-align:center;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;color:#333333;font-size: 16px;height:16px;padding-top:8px;\">\n        			Use the link below to create your password and start using MAP-Works.\n		\n		</td></tr>\n           <tr style=\"margin:0px;padding:0px;\"><td style=\"margin:0px;padding:0px;\">\n        \n<table align=\"center\"> \n  <tr style=\"margin:0px;padding:0px;\">\n    <th style=\"margin:0px;padding:0px;\">\n           <table cellpadding=\"36\" style=\"width:100%\">\n        <tr>\n		<td align=\"center\" style=\"text-align:center;color:#000000;font-weight:normal;font-size: 20px;\">		\n		          <table style=\"border-radius:2px;width:175px;font-size:20px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;text-align:center;display: block;margin: 0 auto;padding:0px 0px\">\n		<tr>\n        <td style=\"background-color:#4673a7; height:58px;border-radius:2px;line-height:21px;text-decoration:none ;vertical-align:middle;\">        \n        <a href=\"$$activation_token$$\" style=\"outline-offset:19px !important;background-color: #4673a7; color: #ffffff;display: block;text-decoration: none;width:175px \"target=\"_blank\"><span style=\"text-decoration: none !important;\">Sign In Now</span></a>\n        </td></tr>\n        <tr valign=\"top\" style=\"height:33px;\">\n        <td style=\"margin-left:auto; margin-right:auto;width:100%;font-size: 14px;height:14px;padding-bottom:7px;font-family:helvetica,arial,verdana,san-serif;font-weight:medium;color:#333333;link:#1e73d5;padding-top:8px;\">       \n				<span>Use this link to <a target=\"_blank\" style=\"link:#1e73d5;\" href=\"$$activation_token$$\">sign in.</a></span>  \n			\n        </td></tr>\n        \n        </table>\n		</td></tr>\n        \n        </table>\n        </th>\n    \n  </tr>\n \n</table>\n       </td></tr>\n</table>\n               </td>\n               </tr>\n               <tr valign=\"top\">\n<td >\n<table>\n<tr>\n<td valign=\"top\" align=\"center\">\n<div style=\"text-align:left;margin-left:30px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;\n			margin-right:18px;font-size: 13px;color: #333333;margin-top:30px;link:#1e73d5;font-weight:normal;\" >\n				Thank you for participating in the spring 2015 pilot. We look forward to hearing your feedback as\n				it will inform future releases of our new student retention and success solution.\n				\n				<br><br>\n				If you have any questions, please contact us here.<br>\n				<a href=\"mailto:$$Support_Helpdesk_Email_Address$$\" style=\"link:#1e73d5;\">$$Support_Helpdesk_Email_Address$$</a> \n				<br><br>\n				Sincerely,\n				<div style=\"text-align:left;font-weight:bold;font-size: 14px;color:#333333\" >\n					<b>The EBI MAP-Works Client Services Team</b> \n					\n				</div>\n                </div>\n</td>\n</tr>\n</table>\n</td>\n</tr>\n               </table>\n               \n            </th>\n            \n        </tr>\n        \n    </table>\n    </center>\n</body>\n</html>\n' WHERE `id`='4';



/*
* Fix for ESPRJ-1030
*/
UPDATE `synapse`.`email_template_lang` SET `body`='<html>\r\n	<head>\r\n		<style>\r\n		body {\r\n    background: none repeat scroll 0 0 #f4f4f4;\r\n	\r\n}\r\n		table {\r\n    padding: 21px;\r\n    width: 799px;\r\n	font-family: helvetica,arial,verdana,san-serif;\r\n	font-size:13px;\r\n	color:#333;\r\n	}\r\n		</style>\r\n	</head>\r\n	<body>\r\n	\r\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r\n			<tbody>\r\n			\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi $$firstname$$:</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A referral was recently assigned to you in MAP-Works. Please sign in to your account to view and take action on this referral.</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Mapworks team!</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r\n			\r\n			</tbody>\r\n		</table>\r\n	</body>\r\n</html>', `subject`='You have a new Mapworks referral\r\n\r\n' WHERE `id`='14';




INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('HIGH_RISK_DEFINITION_RISK_LEVEL', '1,2');
INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('TOTALSTUDENTS_RISK_LEVEL', '1,2,3,4');

/* Priority Students -  removed hard code risk levels and handled in the ebi config */


/*
* No Jira tciket - Appointment Reminder email dashboard url change
* 4 Feb 2014 by Subash
*/

UPDATE `synapse`.`email_template_lang` SET `body`='<html>\n    <head>\n        <style>\n			 body {\n				background: none repeat scroll 0 0;	\n			\n			}\n			table {\n				padding: 21px;\n				width: 799px;\n				font-family: Helvetica,Arial,Verdana,San-serif;\n				font-size:13px;\n				color:#333;\n			}\n   </style>\n    </head>\n    <body>\n        <table cellpadding=\"10\" cellspacing=\"0\">\n            <tbody>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td>Dear $$student_name$$:</td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td style=\"line-height: 1.6;\">This is a reminder that you have an appointment with $$staff_name$$ on $$app_datetime$$. <br/><br/> To view the appointment details, please log in to your MAP-Works dashboard and visit\n					<a style=\"color: #0033CC;\" href=\"$$student_dashboard$$\">Mapworks student dashboard view appointment module</a>.\n					</td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td>Best regards,\n                        <br/>EBI MAP-Works\n                    </td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td><span style=\"font-size:11px; color: #575757; line-height: 120%; text-decoration: none;\">This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</span></td>\n                </tr>\n            </tbody>\n        </table>\n    </body>\n</html>\n' WHERE `id`='15';



# Dumping data for table `intent_to_leave` ESPRJ 150
# ------------------------------------------------------------
 LOCK TABLES `intent_to_leave` WRITE;
 /*!40000 ALTER TABLE `intent_to_leave` DISABLE KEYS */;

 INSERT INTO `intent_to_leave` VALUES 
 	(1,NULL,NULL,NULL,NULL,NULL,NULL,'red','leave-intent-leave-stated.png','#c70009'),
 	(2,NULL,NULL,NULL,NULL,NULL,NULL,'yellow','leave-intent-leave-implied.png','#fec82a'),
 	(3,NULL,NULL,NULL,NULL,NULL,NULL,'green','leave-intent-stay-stated.png','#95cd3c'),
 	(4,NULL,NULL,NULL,NULL,NULL,NULL,'gray','leave-intent-not-stated.png','#cccccc');

/*!40000 ALTER TABLE `intent_to_leave` ENABLE KEYS */;
UNLOCK TABLES;
--
-- Dumping data for table `risk_level` ESPRJ 150
-- Modified in risk refactoring
--
INSERT INTO `risk_level` VALUES 
(1,NULL,NULL,NULL,NULL,NULL,NULL,'red2','risk-level-icon-r2.png','#c70009'),
(2,NULL,NULL,NULL,NULL,NULL,NULL,'red','risk-level-icon-r1.png','#f72d35'),
(3,NULL,NULL,NULL,NULL,NULL,NULL,'yellow','risk-level-icon-y.png','#fec82a'),
(4,NULL,NULL,NULL,NULL,NULL,NULL,'green','risk-level-icon-g.png','#95cd3c');


INSERT INTO `org_search` (`id`,`created_by`,`modified_by`,`deleted_by`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`name`,`query`,`json`,`shared_on`) VALUES (1,NULL,NULL,NULL,1,1,'2015-02-13 18:16:26',NULL,NULL,'Search1',NULL,NULL,NULL);


INSERT INTO `org_academic_year` (`id`, `created_by`, `modified_by`, `deleted_by`, `organization_id`, `created_at`, `modified_at`, `deleted_at`, `name`, `year_id`, `start_date`, `end_date`) 
VALUES (1,NULL,NULL,NULL,1,NULL,NULL,NULL,'Academic year','201415','2014-02-16','2015-02-16'),
(2,NULL,NULL,NULL,1,NULL,NULL,NULL,'Educational Year','202627','2015-02-16','2016-02-16');

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
(NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Staff_ReferralPage','http://synapse-dev.mnv-tech.com/#/team-interactions');

INSERT INTO `email_template` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `email_key`, `is_active`, `from_email_address`, `bcc_recipient_list`)
VALUES
(NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Referral_InterestedParties_Staff',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`, `email_template_id`, `language_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `body`, `subject`)
VALUES
(NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\r\n	<head>\r\n		<style>\r\n		body {\r\n    background: none repeat scroll 0 0 #f4f4f4;\r\n	\r\n}\r\n		table {\r\n    padding: 21px;\r\n    width: 799px;\r\n	font-family: helvetica,arial,verdana,san-serif;\r\n	font-size:13px;\r\n	color:#333;\r\n	}\r\n		</style>\r\n	</head>\r\n	<body>\r\n	\r\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r\n			<tbody>\r\n			\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$staff_firstname$$:</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A faculty/staff member has referred $$firstname$$ to a campus resource through the Mapworks system and added you as an interested party. To view the referral details, please log in to Mapworks and visit <a class="external-link" href="$$staff_referralpage$$" target="_blank" style="color: rgb(41, 114, 155); text-decoration: underline;">MAP-Works student dashboard view appointment module</a>. If you have any questions, please contact ($$coordinator_name$$, $$coordinator_email$$, $$coordinator_title$$ ). </td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI MAP-Works</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r\n			\r\n			</tbody>\r\n		</table>\r\n	</body>\r\n</html>','Interested party on a Mapworks referral');

/*
-- Query: SELECT * FROM synapse.year
LIMIT 0, 1000

-- Date: 2015-02-20 16:12
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
* Include refresh_token as additional grant_type 18th Feb 2015
*/
UPDATE `synapse`.`Client` SET `allowed_grant_types`='a:2:{i:0;s:8:\"password\";i:1;s:13:\"refresh_token\";}' WHERE `id`='1';

/* Insert in org_person_student for student status */
INSERT INTO `synapse`.`org_person_student` (`organization_id`,`person_id`,`status`) VALUES ('1','1','1');
INSERT INTO `synapse`.`org_person_student` (`organization_id`,`person_id`,`status`) VALUES ('1','2','1');


/*
* End of test script
*/

/*
-- Query: select * from ebi_metadata where id = 1
LIMIT 0, 1000
Master data for datablock
-- Date: 2015-02-25 12:48
*/
INSERT INTO `ebi_config` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ebi_Lang','1');

INSERT INTO `ebi_metadata` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`meta_key`,`definition_type`,`metadata_type`,`no_of_decimals`,`is_required`,`min_range`,`max_range`,`entity`,`sequence`,`meta_group`,`scope`) VALUES (1,NULL,NULL,NULL,'2015-02-25 04:27:28','2015-02-25 04:27:28',NULL,'Pamela Edwards','E','T',0,NULL,0.0000,0.0000,NULL,1,NULL,NULL);
/*
# migration script added value for id 1, hence duplicate record issue created. So commented below item.
#INSERT INTO `ebi_metadata_lang` (`id`,`created_by`,`modified_by`,`deleted_by`,`lang_id`,`ebi_metadata_id`,`created_at`,`modified_at`,`deleted_at`,`meta_name`,`meta_description`) VALUES (1,NULL,NULL,NULL,1,1,'2015-02-25 04:27:28','2015-02-25 04:27:28',NULL,'Pamela Edwards','Gloria Mcdonald');
*/
INSERT INTO `ebi_metadata` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`meta_key`,`definition_type`,`metadata_type`,`no_of_decimals`,`is_required`,`min_range`,`max_range`,`entity`,`sequence`,`meta_group`,`scope`) VALUES (2,NULL,NULL,NULL,'2015-02-25 04:27:28','2015-02-25 04:27:28',NULL,'Pamela Edwards1','E','T',0,NULL,0.0000,0.0000,NULL,1,NULL,NULL);

INSERT INTO `ebi_metadata_lang` (`id`,`created_by`,`modified_by`,`deleted_by`,`lang_id`,`ebi_metadata_id`,`created_at`,`modified_at`,`deleted_at`,`meta_name`,`meta_description`) VALUES (2,NULL,NULL,NULL,1,2,'2015-02-25 04:27:28','2015-02-25 04:27:28',NULL,'Pamela Edwards1','Gloria Mcdonald1');

INSERT INTO `ebi_metadata` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`meta_key`,`definition_type`,`metadata_type`,`no_of_decimals`,`is_required`,`min_range`,`max_range`,`entity`,`sequence`,`meta_group`,`scope`) VALUES (3,NULL,NULL,NULL,'2015-02-25 04:27:28','2015-02-25 04:27:28',NULL,'Pamela Edwards12','E','T',0,NULL,0.0000,0.0000,NULL,1,NULL,NULL);

INSERT INTO `ebi_metadata_lang` (`id`,`created_by`,`modified_by`,`deleted_by`,`lang_id`,`ebi_metadata_id`,`created_at`,`modified_at`,`deleted_at`,`meta_name`,`meta_description`) VALUES (3,NULL,NULL,NULL,1,3,'2015-02-25 04:27:28','2015-02-25 04:27:28',NULL,'Pamela Edwards12','Gloria Mcdonald12');

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

update `email_template_lang` set email_template_id = 15, language_id = 1, body = '<html>\n    <head>\n        <style>\n			 body {\n				background: none repeat scroll 0 0;	\n			\n			}\n			table {\n				padding: 21px;\n				width: 799px;\n				font-family: Helvetica,Arial,Verdana,San-serif;\n				font-size:13px;\n				color:#333;\n			}\n   </style>\n    </head>\n    <body>\n        <table cellpadding=\"10\" cellspacing=\"0\">\n            <tbody>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td>Dear $$student_name$$:</td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td style=\"line-height: 1.6;\">This is a reminder that you have an appointment with $$staff_name$$ on $$app_datetime$$. <br/><br/> To view the appointment details, please log in to your Mapworks dashboard and visit\n					<a style=\"color: #0033CC;\" href=\"$$student_dashboard$$\">Mapworks student dashboard view appointment module</a>.\n					</td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td>Best regards,\n                        <br/>EBI Mapworks\n                    </td>\n                </tr>\n                <tr style=\"background:#fff;border-collapse:collapse;\">\n                    <td><span style=\"font-size:11px; color: #575757; line-height: 120%; text-decoration: none;\">This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</span></td>\n                </tr>\n            </tbody>\n        </table>\n    </body>\n</html>\n', subject = 'Mapworks appointment reminder' where id = 15;

--
-- Dumping data for table `org_academic_terms`
--

INSERT INTO `org_academic_terms` (`id`, `created_by`, `modified_by`, `deleted_by`, `organization_id`, `org_academic_year_id`, `created_at`, `modified_at`, `deleted_at`, `name`, `start_date`, `end_date`, `term_code`) 
VALUES (1,NULL,NULL,NULL,1,1,NULL,NULL,NULL,'Term 1','2015-02-16','2015-06-16',0),
(2,NULL,NULL,NULL,1,1,NULL,NULL,NULL,'Term 2','2015-06-17','2016-02-16',0);

--
-- Dumping data for table `org_courses`
--

INSERT INTO `org_courses` (`id`, `created_by`, `modified_by`, `deleted_by`, `organization_id`, `org_academic_year_id`, `org_academic_terms_id`, `created_at`, `modified_at`, `deleted_at`, `course_section_id`, `college_code`, `dept_code`, `subject_code`, `course_number`, `course_name`, `section_number`, `days_times`, `location`, `credit_hours`, `externalId`) 
VALUES (1,NULL,NULL,NULL,1,1,1,NULL,NULL,NULL,'SEC A','IIT','IT','SEC001','0087','Computer Networks','SEC 1','12','Banglore',12.00,'7545'),
(2,NULL,NULL,NULL,1,1,2,NULL,NULL,NULL,'SEC B','RMK','CSE','SEC987','6755','Science','SEC 2','12','Banglore',12.00,'7845');

--
-- Dumping data for table `org_course_faculty`
--
INSERT INTO `org_course_faculty` (`id`, `created_by`, `modified_by`, `deleted_by`, `organization_id`, `org_courses_id`, `person_id`, `org_permissionset_id`, `created_at`, `modified_at`, `deleted_at`) 
VALUES (1,NULL,NULL,NULL,1,1,1,1,NULL,NULL,NULL),
(2,NULL,NULL,NULL,1,2,1,1,NULL,NULL,NULL);

--
-- Dumping data for table `org_course_student`
--
INSERT INTO `org_course_student` (`id`, `created_by`, `modified_by`, `deleted_by`, `organization_id`, `org_courses_id`, `person_id`, `created_at`, `modified_at`, `deleted_at`) 
VALUES (1,NULL,NULL,NULL,1,1,1,NULL,NULL,NULL),
(2,NULL,NULL,NULL,1,2,1,NULL,NULL,NULL);


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
 * data for success markers 
*/
/*
-- Query: SELECT * FROM synapse.factor
-- Date: 2015-04-20 12:06
*/
INSERT INTO `factor` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`created_at`,`modified_at`,`deleted_at`,`type`) VALUES (1,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL);
INSERT INTO `factor` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`created_at`,`modified_at`,`deleted_at`,`type`) VALUES (2,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL);
INSERT INTO `factor` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`created_at`,`modified_at`,`deleted_at`,`type`) VALUES (3,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL);
INSERT INTO `factor` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`created_at`,`modified_at`,`deleted_at`,`type`) VALUES (4,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL);


/*
-- Query: SELECT * FROM synapse.factor_lang
-- Date: 2015-04-20 12:06
*/
INSERT INTO `factor_lang` (`factor_id`,`lang_id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`name`) VALUES (1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Factor 1');
INSERT INTO `factor_lang` (`factor_id`,`lang_id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`name`) VALUES (2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Factor 2');
INSERT INTO `factor_lang` (`factor_id`,`lang_id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`name`) VALUES (3,1,NULL,NULL,NULL,NULL,NULL,NULL,'Factor 3');
INSERT INTO `factor_lang` (`factor_id`,`lang_id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`name`) VALUES (4,1,NULL,NULL,NULL,NULL,NULL,NULL,'Factor 4');

INSERT INTO `surveymarker` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`sequence`) VALUES (1,NULL,NULL,NULL,'2015-04-02 05:06:07','2015-04-02 05:06:07',NULL,1);
INSERT INTO `surveymarker` (`id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`sequence`) VALUES (2,NULL,NULL,NULL,'2015-04-02 05:06:17','2015-04-02 05:06:17',NULL,2);

INSERT INTO `surveymarker_lang` (`id`,`created_by`,`modified_by`,`deleted_by`,`lang_id`,`surveymarker_id`,`created_at`,`modified_at`,`deleted_at`,`name`) VALUES (1,NULL,NULL,NULL,1,1,'2015-04-02 05:06:07','2015-04-02 05:06:07',NULL,'Survey Marker');
INSERT INTO `surveymarker_lang` (`id`,`created_by`,`modified_by`,`deleted_by`,`lang_id`,`surveymarker_id`,`created_at`,`modified_at`,`deleted_at`,`name`) VALUES (2,NULL,NULL,NULL,1,2,'2015-04-02 05:06:17','2015-04-02 05:06:17',NULL,'Survey Marker2');

/*
-- Query: SELECT * FROM synapse.surveymarker_questions
-- Date: 2015-04-20 10:46
*/
INSERT INTO `surveymarker_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`surveymarker_id`,`ebi_question_id`,`survey_questions_id`,`survey_id`,`factor_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (1,NULL,NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'bank',1,1.000,2.000,3.000,4.000,5.000,6.000);
INSERT INTO `surveymarker_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`surveymarker_id`,`ebi_question_id`,`survey_questions_id`,`survey_id`,`factor_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (2,NULL,NULL,NULL,1,2,NULL,NULL,NULL,NULL,'2015-04-13 18:39:55',NULL,'bank',4,1.000,3.000,4.000,6.000,7.000,10.000);
INSERT INTO `surveymarker_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`surveymarker_id`,`ebi_question_id`,`survey_questions_id`,`survey_id`,`factor_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (3,NULL,NULL,NULL,1,3,NULL,NULL,NULL,NULL,'2015-04-13 18:40:08',NULL,'bank',2,2.000,4.000,6.000,8.000,10.000,12.000);
INSERT INTO `surveymarker_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`surveymarker_id`,`ebi_question_id`,`survey_questions_id`,`survey_id`,`factor_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (4,NULL,NULL,NULL,1,NULL,NULL,1,1,NULL,'2015-04-13 18:40:08',NULL,'factor',1,1.000,4.000,5.000,7.000,8.000,10.000);
INSERT INTO `surveymarker_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`surveymarker_id`,`ebi_question_id`,`survey_questions_id`,`survey_id`,`factor_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (5,NULL,NULL,NULL,1,NULL,NULL,1,2,NULL,'2015-04-13 18:39:55',NULL,'factor',3,1.000,3.000,4.000,8.000,9.000,10.000);
INSERT INTO `surveymarker_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`surveymarker_id`,`ebi_question_id`,`survey_questions_id`,`survey_id`,`factor_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (6,NULL,NULL,NULL,1,4,NULL,NULL,NULL,NULL,'2015-04-13 18:36:00',NULL,'bank',5,1.000,3.000,4.000,6.000,7.000,10.000);
INSERT INTO `surveymarker_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`surveymarker_id`,`ebi_question_id`,`survey_questions_id`,`survey_id`,`factor_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (7,NULL,NULL,NULL,1,5,NULL,NULL,NULL,NULL,'2015-04-13 18:36:00',NULL,'bank',6,1.000,2.000,3.000,5.000,6.000,10.000);
INSERT INTO `surveymarker_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`surveymarker_id`,`ebi_question_id`,`survey_questions_id`,`survey_id`,`factor_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (8,NULL,NULL,NULL,1,6,NULL,NULL,NULL,NULL,'2015-04-13 18:36:00',NULL,'bank',7,1.000,3.000,4.000,6.000,7.000,10.000);
INSERT INTO `surveymarker_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`surveymarker_id`,`ebi_question_id`,`survey_questions_id`,`survey_id`,`factor_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (9,NULL,NULL,NULL,2,4,NULL,NULL,NULL,NULL,NULL,NULL,'bank',1,1.000,3.000,4.000,6.000,7.000,10.000);
INSERT INTO `surveymarker_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`surveymarker_id`,`ebi_question_id`,`survey_questions_id`,`survey_id`,`factor_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (10,NULL,NULL,NULL,2,5,NULL,NULL,NULL,NULL,NULL,NULL,'bank',2,1.000,3.000,4.000,6.000,7.000,10.000);
INSERT INTO `surveymarker_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`surveymarker_id`,`ebi_question_id`,`survey_questions_id`,`survey_id`,`factor_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (11,NULL,NULL,NULL,2,6,NULL,NULL,NULL,NULL,NULL,NULL,'bank',3,1.000,3.000,4.000,6.000,7.000,10.000);
INSERT INTO `surveymarker_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`surveymarker_id`,`ebi_question_id`,`survey_questions_id`,`survey_id`,`factor_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (12,NULL,NULL,NULL,2,NULL,NULL,1,3,NULL,NULL,NULL,'factor',4,1.000,3.000,4.000,6.000,7.000,10.000);
INSERT INTO `surveymarker_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`surveymarker_id`,`ebi_question_id`,`survey_questions_id`,`survey_id`,`factor_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (13,NULL,NULL,NULL,2,NULL,NULL,1,4,NULL,NULL,NULL,'factor',5,1.000,4.000,5.000,8.000,8.000,10.000);


/*
-- Subash

-- Date: 2015-04-07 10:28
*/
INSERT INTO `ebi_config` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Academic_Update_View_URL','http://synapse-dev.mnv-tech.com/');


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



/*
 * data for datablock_questions   Date: 2015-04-07 07:52
*/
INSERT INTO `datablock_questions` (`id`,`datablock_id`,`ebi_question_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`survey_id`,`survey_questions_id`,`factor_id`,`type`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (1,7,1,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'bank',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `datablock_questions` (`id`,`datablock_id`,`ebi_question_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`survey_id`,`survey_questions_id`,`factor_id`,`type`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (2,8,1,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'bank',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `datablock_questions` (`id`,`datablock_id`,`ebi_question_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`survey_id`,`survey_questions_id`,`factor_id`,`type`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (3,8,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,1,'factor',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `datablock_questions` (`id`,`datablock_id`,`ebi_question_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`survey_id`,`survey_questions_id`,`factor_id`,`type`,`red_low`,`red_high`,`yellow_low`,`yellow_high`,`green_low`,`green_high`) VALUES (4,7,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'bank',NULL,NULL,NULL,NULL,NULL,NULL);

/*


/*
 * data for survey_lang   Date: 2015-04-07 07:52
*/
INSERT INTO `survey_lang` (`survey_id`,`lang_id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`name`) VALUES (1,1,NULL,NULL,NULL,NULL,NULL,NULL,'First Survey');
INSERT INTO `survey_lang` (`survey_id`,`lang_id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`name`) VALUES (2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Second Survey');
INSERT INTO `survey_lang` (`survey_id`,`lang_id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`name`) VALUES (3,1,NULL,NULL,NULL,NULL,NULL,NULL,'Third Survey');

/*-- Query: select * from email_template where id = 18LIMIT 0, 1000Subash-- Date: 2015-04-07 10:32*/

INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Academic_Update_Notification_Student',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

/*-- Query: select * from email_template_lang where id = 19LIMIT 0, 1000Subash-- Date: 2015-04-07 10:33*/

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<!DOCTYPE html>\r\n<html>\r\n\r\n<body>\r\n<p> Hi $$studentname$$ </p>
<p>An Academic Update was created for you. View it now</body></html>','Academic Update Notification');


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


/*-- FOR ESPRJ 1746 - Creating academic year, academic terms, course, course student and faculty Saravanan -- Date: 2015-04-10 */
INSERT INTO `org_academic_year` (`organization_id`, `name`, `year_id`, `start_date`, `end_date`) VALUES ('1', 'Educational Year', '201516', '2015-04-06', '2016-03-30');

SET @ayid := (SELECT MAX(id) FROM org_academic_year);

INSERT INTO `org_academic_terms` (`organization_id`, `org_academic_year_id`, `name`, `start_date`, `end_date`, `term_code`) VALUES ('1', @ayid, 'Term 3', '2015-04-06', '2015-12-31', '0');

SET @atid := (SELECT MAX(id) FROM org_academic_terms);

INSERT INTO `org_courses` (`organization_id`, `org_academic_year_id`, `org_academic_terms_id`, `course_section_id`, `college_code`, `dept_code`, `subject_code`, `course_number`, `course_name`, `section_number`, `days_times`, `location`, `credit_hours`, `externalId`) VALUES ('1', @ayid, @atid, 'SEC C', 'MMG', 'ECOM', 'ECON', '101', 'Economics', '123456', '30', 'Chennai', '25', '7985');

SET @courseid := (SELECT MAX(id) FROM org_courses);

INSERT INTO `org_course_faculty` (`organization_id`, `org_courses_id`, `person_id`, `org_permissionset_id`) VALUES ('1', @courseid, '4', '1');
INSERT INTO `synapse`.`org_course_faculty` (`organization_id`, `org_courses_id`, `person_id`, `org_permissionset_id`) VALUES ('1', @courseid, '5', '1');

INSERT INTO `org_course_student` (`organization_id`, `org_courses_id`, `person_id`) VALUES ('1', @courseid, '6');
INSERT INTO `org_course_student` (`organization_id`, `org_courses_id`, `person_id`) VALUES ('1', @courseid, '8');

INSERT INTO `org_group` (`organization_id`, `created_at`, `modified_at`, `group_name`) VALUES ('1', '2015-04-06', '2015-04-06', 'Test Group');


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


INSERT INTO `org_group_students` (`person_id`, `org_group_id`, `organization_id`) VALUES ('6', '2', '1');

INSERT INTO `person_org_metadata` (`person_id`, `org_metadata_id`, `metadata_value`) VALUES ('6', '1', '20');

INSERT INTO `person_ebi_metadata` (`person_id`, `ebi_metadata_id`, `metadata_value`) VALUES ('6', '1', '20');

UPDATE `person` SET `password`='$2y$13$f6bnaUYhaIO0qzJ0krqrIeUDnxJxWYYEyB3L6qDDK/1ln5CsHKEca' WHERE `id`='4';

UPDATE `organization` SET `send_to_student`='1' WHERE `id`='1';

UPDATE `synapse`.`org_permissionset` SET `create_view_academic_update`='1' WHERE `id`='1';

UPDATE `contact_info` SET `primary_email`='studentjobtest5491b3e114292@mnv-tech.com' WHERE `id`='6';

/*-- FOR ESPRJ 1850,1851,1852,1853 - Listing student surveys, list factors and ques response for the student,survey comparision  Ajinkya -- Date: 2015-04-20 */

UPDATE `org_person_student` SET `surveycohort` = '1' WHERE `person_id` = '6';


/*
-- Query: SELECT * FROM synapse.ind_question
-- Date: 2015-04-20 10:38
*/
INSERT INTO `ind_question` (`id`,`created_by`,`modified_by`,`deleted_by`,`question_type_id`,`question_category_id`,`survey_id`,`created_at`,`modified_at`,`deleted_at`,`has_other`,`external_id`) VALUES (1,NULL,NULL,NULL,1,1,1,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `ind_question` (`id`,`created_by`,`modified_by`,`deleted_by`,`question_type_id`,`question_category_id`,`survey_id`,`created_at`,`modified_at`,`deleted_at`,`has_other`,`external_id`) VALUES (2,NULL,NULL,NULL,1,1,1,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `ind_question` (`id`,`created_by`,`modified_by`,`deleted_by`,`question_type_id`,`question_category_id`,`survey_id`,`created_at`,`modified_at`,`deleted_at`,`has_other`,`external_id`) VALUES (3,NULL,NULL,NULL,1,1,1,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `ind_question` (`id`,`created_by`,`modified_by`,`deleted_by`,`question_type_id`,`question_category_id`,`survey_id`,`created_at`,`modified_at`,`deleted_at`,`has_other`,`external_id`) VALUES (4,NULL,NULL,NULL,1,1,1,NULL,NULL,NULL,NULL,NULL);

/*
-- Query: SELECT * FROM synapse.ind_questions_lang
-- Date: 2015-04-20 10:38
*/
INSERT INTO `ind_questions_lang` (`ind_question_id`,`lang_id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`question_text`,`question_rpt`) VALUES (1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ind Question 1');
INSERT INTO `ind_questions_lang` (`ind_question_id`,`lang_id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`question_text`,`question_rpt`) VALUES (2,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ind Question 2');
INSERT INTO `ind_questions_lang` (`ind_question_id`,`lang_id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`question_text`,`question_rpt`) VALUES (3,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ind Question 3');
INSERT INTO `ind_questions_lang` (`ind_question_id`,`lang_id`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`question_text`,`question_rpt`) VALUES (4,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ind Question 4');

/*
-- Query: SELECT * FROM synapse.factor_questions
LIMIT 0, 1000

-- Date: 2015-04-20 11:00
*/
INSERT INTO `factor_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`factor_id`,`ebi_question_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`) VALUES (1,NULL,NULL,NULL,1,NULL,3,NULL,NULL,NULL);
INSERT INTO `factor_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`factor_id`,`ebi_question_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`) VALUES (2,NULL,NULL,NULL,2,NULL,4,NULL,NULL,NULL);
INSERT INTO `factor_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`factor_id`,`ebi_question_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`) VALUES (3,NULL,NULL,NULL,1,1,NULL,NULL,NULL,NULL);
INSERT INTO `factor_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`factor_id`,`ebi_question_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`) VALUES (4,NULL,NULL,NULL,1,2,NULL,NULL,NULL,NULL);


/*
-- Query: SELECT * FROM synapse.survey_questions
-- Date: 2015-04-20 10:16
*/
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (1,NULL,NULL,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,1,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (2,NULL,NULL,NULL,1,NULL,2,NULL,NULL,NULL,NULL,NULL,2,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (3,NULL,NULL,NULL,1,NULL,3,NULL,NULL,NULL,NULL,NULL,3,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (4,NULL,NULL,NULL,1,NULL,4,NULL,NULL,NULL,NULL,NULL,4,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (5,NULL,NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,5,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (6,NULL,NULL,NULL,1,2,NULL,NULL,NULL,NULL,NULL,NULL,6,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (7,NULL,NULL,NULL,2,1,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (8,NULL,NULL,NULL,2,2,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (9,NULL,NULL,NULL,2,NULL,3,NULL,NULL,NULL,NULL,NULL,3,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (10,NULL,NULL,NULL,2,NULL,4,NULL,NULL,NULL,NULL,NULL,4,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (11,NULL,NULL,NULL,2,3,NULL,NULL,NULL,NULL,NULL,NULL,5,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (12,NULL,NULL,NULL,2,4,NULL,NULL,NULL,NULL,NULL,NULL,6,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (13,NULL,NULL,NULL,3,1,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (14,NULL,NULL,NULL,3,2,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (15,NULL,NULL,NULL,3,3,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (16,NULL,NULL,NULL,3,4,NULL,NULL,NULL,NULL,NULL,NULL,4,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (17,NULL,NULL,NULL,3,NULL,3,NULL,NULL,NULL,NULL,NULL,5,NULL);
INSERT INTO `survey_questions` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`ebi_question_id`,`ind_question_id`,`survey_sections_id`,`created_at`,`modified_at`,`deleted_at`,`type`,`sequence`,`qnbr`) VALUES (18,NULL,NULL,NULL,3,NULL,4,NULL,NULL,NULL,NULL,NULL,6,NULL);

/*
-- Query: SELECT * FROM synapse.survey_response
-- Date: 2015-04-20 10:19
*/
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (1,NULL,NULL,NULL,1,6,1,NULL,NULL,1,NULL,NULL,NULL,'decimal',11.00,NULL,NULL);
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (2,NULL,NULL,NULL,1,6,1,NULL,NULL,2,NULL,NULL,NULL,'char',NULL,'fsdfs',NULL);
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (3,NULL,NULL,NULL,1,6,1,NULL,NULL,3,NULL,NULL,NULL,'decimal',1.00,NULL,NULL);
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (4,NULL,NULL,NULL,1,6,1,NULL,NULL,4,NULL,NULL,NULL,'char',NULL,'fsdfsdfdsf',NULL);
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (5,NULL,NULL,NULL,1,6,1,NULL,NULL,5,NULL,NULL,NULL,'decimal',3.00,NULL,NULL);
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (6,NULL,NULL,NULL,1,6,1,NULL,NULL,6,NULL,NULL,NULL,'charmax',NULL,NULL,'fsdfsdfsdfsdfsfsdfsddffs');
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (7,NULL,NULL,NULL,1,6,2,NULL,NULL,7,NULL,NULL,NULL,'decimal',4.00,NULL,NULL);
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (8,NULL,NULL,NULL,1,6,2,NULL,NULL,8,NULL,NULL,NULL,'charmax',NULL,NULL,'fsdfsdfsdfsdfsdfsdf');
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (9,NULL,NULL,NULL,1,6,2,NULL,NULL,9,NULL,NULL,NULL,'decimal',5.00,NULL,NULL);
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (10,NULL,NULL,NULL,1,6,2,NULL,NULL,10,NULL,NULL,NULL,'charmax',NULL,NULL,'fsdfsdfsdfsdfsdf');
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (11,NULL,NULL,NULL,1,6,2,NULL,NULL,11,NULL,NULL,NULL,'decimal',8.00,NULL,NULL);
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (12,NULL,NULL,NULL,1,6,2,NULL,NULL,12,NULL,NULL,NULL,'char',NULL,'fsdfsdfsdf',NULL);
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (13,NULL,NULL,NULL,1,6,3,NULL,NULL,13,NULL,NULL,NULL,'char',NULL,'sdffsdfsdf',NULL);
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (14,NULL,NULL,NULL,1,6,3,NULL,NULL,14,NULL,NULL,NULL,'charmax',NULL,NULL,'fsdfsdfsdfsdfsd');
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (15,NULL,NULL,NULL,1,6,3,NULL,NULL,15,NULL,NULL,NULL,'charmax',NULL,NULL,'fsdfsdffsdfsdf');
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (16,NULL,NULL,NULL,1,6,3,NULL,NULL,16,NULL,NULL,NULL,'char',NULL,'fsdfsdfsd',NULL);
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (17,NULL,NULL,NULL,1,6,3,NULL,NULL,17,NULL,NULL,NULL,'charmax',NULL,NULL,'fsdfsdfsdfsdfsdf');
INSERT INTO `survey_response` (`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`survey_id`,`org_academic_year_id`,`org_academic_terms_id`,`survey_questions_id`,`created_at`,`modified_at`,`deleted_at`,`response_type`,`decimal_value`,`char_value`,`charmax_value`) VALUES (18,NULL,NULL,NULL,1,6,3,NULL,NULL,18,NULL,NULL,NULL,'char',NULL,'fsdfsdfsdf',NULL);


/*
-- Query: SELECT * FROM synapse.wess_link
-- Date: 2015-04-20 10:32
*/
INSERT INTO `wess_link` (`created_by`,`modified_by`,`deleted_by`,`org_id`,`survey_id`,`created_at`,`modified_at`,`deleted_at`,`cohort_code`,`wess_survey_id`,`wess_cohort_id`,`wess_order_id`,`wess_launchedflag`,`wess_maporder_key`,`wess_prod_year`,`wess_cust_id`,`status`,`open_date`,`close_date`,`year_id`,`wess_admin_link`) VALUES (NULL,NULL,NULL,1,1,NULL,'2015-04-29 11:26:38',NULL,1,2,1,11,1,NULL,2015,NULL,'open','2015-04-24 22:46:11','2015-04-24 22:46:11','202627','firstLink');
INSERT INTO `wess_link` (`created_by`,`modified_by`,`deleted_by`,`org_id`,`survey_id`,`created_at`,`modified_at`,`deleted_at`,`cohort_code`,`wess_survey_id`,`wess_cohort_id`,`wess_order_id`,`wess_launchedflag`,`wess_maporder_key`,`wess_prod_year`,`wess_cust_id`,`status`,`open_date`,`close_date`,`year_id`,`wess_admin_link`) VALUES (NULL,NULL,NULL,1,2,NULL,NULL,NULL,1,2,1,12,NULL,NULL,2015,NULL,NULL,'2015-06-03 05:06:07',NULL,'202627','secondLink');
INSERT INTO `wess_link` (`created_by`,`modified_by`,`deleted_by`,`org_id`,`survey_id`,`created_at`,`modified_at`,`deleted_at`,`cohort_code`,`wess_survey_id`,`wess_cohort_id`,`wess_order_id`,`wess_launchedflag`,`wess_maporder_key`,`wess_prod_year`,`wess_cust_id`,`status`,`open_date`,`close_date`,`year_id`,`wess_admin_link`) VALUES (NULL,NULL,NULL,1,3,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2015-08-04 05:06:07',NULL,202627,'thirdLink');
INSERT INTO `wess_link` (`created_by`,`modified_by`,`deleted_by`,`org_id`,`survey_id`,`created_at`,`modified_at`,`deleted_at`,`cohort_code`,`wess_survey_id`,`wess_cohort_id`,`wess_order_id`,`wess_launchedflag`,`wess_maporder_key`,`wess_prod_year`,`wess_cust_id`,`status`,`open_date`,`close_date`,`year_id`,`wess_admin_link`) VALUES (NULL,NULL,NULL,1,4,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'launched','2015-12-05 05:06:07',NULL,202627,'fourthLink');

/*
-- Query: SELECT * FROM synapse.ebi_questions_lang
-- Date: 2015-04-20 15:24
*/
INSERT INTO `ebi_questions_lang` (`id`,`ebi_question_id`,`lang_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`question_text`,`question_rpt`) VALUES (1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'First question','First question');
INSERT INTO `ebi_questions_lang` (`id`,`ebi_question_id`,`lang_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`question_text`,`question_rpt`) VALUES (2,2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Second question','Second question');
INSERT INTO `ebi_questions_lang` (`id`,`ebi_question_id`,`lang_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`question_text`,`question_rpt`) VALUES (3,3,1,NULL,NULL,NULL,NULL,NULL,NULL,'Third question','Third question');

/*
-- ESPRJ - 1573 - Update button link as per UI URL. Sends an email reminder to all faculty who still have request responses pending.
-- Date: 2015-04-22 13:40 By Saravanan
*/
UPDATE `ebi_config` SET `value`='http://synapse-dev.mnv-tech.com/#/academic-updates/update/' WHERE `key`='Academic_Update_Reminder_to_Faculty';

/*
-- Query: SELECT * FROM synapse.ebi_metadata
-- Date: 2015-04-22 14:21
*/

INSERT INTO `ebi_metadata` (`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`meta_key`,`definition_type`,`metadata_type`,`no_of_decimals`,`is_required`,`min_range`,`max_range`,`entity`,`sequence`,`meta_group`,`scope`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Age','E','N',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `ebi_metadata` (`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`meta_key`,`definition_type`,`metadata_type`,`no_of_decimals`,`is_required`,`min_range`,`max_range`,`entity`,`sequence`,`meta_group`,`scope`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Education','E','S',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `ebi_metadata` (`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`,`meta_key`,`definition_type`,`metadata_type`,`no_of_decimals`,`is_required`,`min_range`,`max_range`,`entity`,`sequence`,`meta_group`,`scope`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'DOB','E','D',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*
-- Query: SELECT * FROM synapse.person_ebi_metadata
-- Date: 2015-04-22 14:21
*/
INSERT INTO `person_ebi_metadata` (`created_by`,`modified_by`,`deleted_by`,`person_id`,`ebi_metadata_id`,`org_academic_year_id`,`org_academic_terms_id`,`created_at`,`modified_at`,`deleted_at`,`metadata_value`) VALUES (NULL,NULL,NULL,2,4,NULL,NULL,NULL,NULL,NULL,'80');
INSERT INTO `person_ebi_metadata` (`created_by`,`modified_by`,`deleted_by`,`person_id`,`ebi_metadata_id`,`org_academic_year_id`,`org_academic_terms_id`,`created_at`,`modified_at`,`deleted_at`,`metadata_value`) VALUES (NULL,NULL,NULL,2,4,NULL,NULL,NULL,NULL,NULL,'90');
INSERT INTO `person_ebi_metadata` (`created_by`,`modified_by`,`deleted_by`,`person_id`,`ebi_metadata_id`,`org_academic_year_id`,`org_academic_terms_id`,`created_at`,`modified_at`,`deleted_at`,`metadata_value`) VALUES (NULL,NULL,NULL,2,5,NULL,NULL,NULL,NULL,NULL,'BCA');
INSERT INTO `person_ebi_metadata` (`created_by`,`modified_by`,`deleted_by`,`person_id`,`ebi_metadata_id`,`org_academic_year_id`,`org_academic_terms_id`,`created_at`,`modified_at`,`deleted_at`,`metadata_value`) VALUES (NULL,NULL,NULL,2,5,NULL,NULL,NULL,NULL,NULL,'MCA');
INSERT INTO `person_ebi_metadata` (`created_by`,`modified_by`,`deleted_by`,`person_id`,`ebi_metadata_id`,`org_academic_year_id`,`org_academic_terms_id`,`created_at`,`modified_at`,`deleted_at`,`metadata_value`) VALUES (NULL,NULL,NULL,2,6,NULL,NULL,NULL,NULL,NULL,'2014-12-16');
INSERT INTO `person_ebi_metadata` (`created_by`,`modified_by`,`deleted_by`,`person_id`,`ebi_metadata_id`,`org_academic_year_id`,`org_academic_terms_id`,`created_at`,`modified_at`,`deleted_at`,`metadata_value`) VALUES (NULL,NULL,NULL,2,6,NULL,NULL,NULL,NULL,NULL,'2015-12-16');

/*
-- Query: SELECT * FROM synapse.person_org_metadata
-- Date: 2015-04-22 14:21
*/
INSERT INTO `person_org_metadata` (`created_by`,`modified_by`,`deleted_by`,`person_id`,`org_metadata_id`,`org_academic_year_id`,`org_academic_periods_id`,`created_at`,`modified_at`,`deleted_at`,`metadata_value`) VALUES (NULL,NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,'30');
INSERT INTO `person_org_metadata` (`created_by`,`modified_by`,`deleted_by`,`person_id`,`org_metadata_id`,`org_academic_year_id`,`org_academic_periods_id`,`created_at`,`modified_at`,`deleted_at`,`metadata_value`) VALUES (NULL,NULL,NULL,2,1,NULL,NULL,NULL,NULL,NULL,'40');
INSERT INTO `person_org_metadata` (`created_by`,`modified_by`,`deleted_by`,`person_id`,`org_metadata_id`,`org_academic_year_id`,`org_academic_periods_id`,`created_at`,`modified_at`,`deleted_at`,`metadata_value`) VALUES (NULL,NULL,NULL,2,2,NULL,NULL,NULL,NULL,NULL,'BCA');
INSERT INTO `person_org_metadata` (`created_by`,`modified_by`,`deleted_by`,`person_id`,`org_metadata_id`,`org_academic_year_id`,`org_academic_periods_id`,`created_at`,`modified_at`,`deleted_at`,`metadata_value`) VALUES (NULL,NULL,NULL,2,2,NULL,NULL,NULL,NULL,NULL,'MCA');
INSERT INTO `person_org_metadata` (`created_by`,`modified_by`,`deleted_by`,`person_id`,`org_metadata_id`,`org_academic_year_id`,`org_academic_periods_id`,`created_at`,`modified_at`,`deleted_at`,`metadata_value`) VALUES (NULL,NULL,NULL,2,3,NULL,NULL,NULL,NULL,NULL,'2014-12-16');
INSERT INTO `person_org_metadata` (`created_by`,`modified_by`,`deleted_by`,`person_id`,`org_metadata_id`,`org_academic_year_id`,`org_academic_periods_id`,`created_at`,`modified_at`,`deleted_at`,`metadata_value`) VALUES (NULL,NULL,NULL,2,3,NULL,NULL,NULL,NULL,NULL,'2015-12-16');

/*
-- Query: SELECT * FROM synapse.org_metadata
-- Date: 2015-04-22 14:21
*/
INSERT INTO `org_metadata` (`organization_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`meta_key`,`meta_name`,`meta_description`,`definition_type`,`metadata_type`,`no_of_decimals`,`is_required`,`min_range`,`max_range`,`entity`,`sequence`,`meta_group`) VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'Education','Education','Education','O','S',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `org_metadata` (`organization_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`meta_key`,`meta_name`,`meta_description`,`definition_type`,`metadata_type`,`no_of_decimals`,`is_required`,`min_range`,`max_range`,`entity`,`sequence`,`meta_group`) VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'DOB','DOB','DOB','O','D',NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*
-- Query: SELECT * FROM synapse.org_course_student
-- Date: 2015-04-22 14:21
*/
INSERT INTO `org_course_student` (`created_by`,`modified_by`,`deleted_by`,`organization_id`,`org_courses_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`) VALUES (NULL,NULL,NULL,1,1,1,NULL,NULL,NULL);
INSERT INTO `org_course_student` (`created_by`,`modified_by`,`deleted_by`,`organization_id`,`org_courses_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`) VALUES (NULL,NULL,NULL,1,2,1,NULL,NULL,NULL);
INSERT INTO `org_course_student` (`created_by`,`modified_by`,`deleted_by`,`organization_id`,`org_courses_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`) VALUES (NULL,NULL,NULL,1,3,6,NULL,NULL,NULL);
INSERT INTO `org_course_student` (`created_by`,`modified_by`,`deleted_by`,`organization_id`,`org_courses_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`) VALUES (NULL,NULL,NULL,1,3,8,NULL,NULL,NULL);
INSERT INTO `org_course_student` (`created_by`,`modified_by`,`deleted_by`,`organization_id`,`org_courses_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`) VALUES (NULL,NULL,NULL,1,1,2,NULL,NULL,NULL);

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
</html>', `subject`='Mapworks Academic Update upload has finished' WHERE `email_template_id`= @templateid;

/* 27 APR 2015 - Academic Update Create  email template - END */


/*
* permission  fix for activity stream
*/

/*
-- Query: SELECT * FROM synapse.ebi_search
LIMIT 0, 1000

-- Date: 2015-04-28 09:40
*/



/*
* date: 30/04/2015 
* 2073 Referral_InterestedParties_Staff_Closed
*/
INSERT INTO `email_template` (`id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `email_key`, `is_active`, `from_email_address`, `bcc_recipient_list`)
VALUES
(NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Referral_InterestedParties_Staff_Closed',NULL,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`, `email_template_id`, `language_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `body`, `subject`)
VALUES
(NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\r\n	<head>\r\n		<style>\r\n		body {\r\n    background: none repeat scroll 0 0 #f4f4f4;\r\n	\r\n}\r\n		table {\r\n    padding: 21px;\r\n    width: 799px;\r\n	font-family: helvetica,arial,verdana,san-serif;\r\n	font-size:13px;\r\n	color:#333;\r\n	}\r\n		</style>\r\n	</head>\r\n	<body>\r\n	\r\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r\n			<tbody>\r\n			\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$staff_firstname$$:</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>A referral that you were watching in Mapworks has recently been closed. Please sign in to your account to view this referral.</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Mapworks team!</td></tr>\r\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r\n			\r\n			</tbody>\r\n		</table>\r\n	</body>\r\n</html>','Interested party on a Mapworks referral');






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
		<td style=" padding:5px; background-color: #4F9BD9; border: 1px solid #428BCA !important; border-collapse:collapse"><p style="font-weight: bold; font-size: 14px; color:#fff;">View and complete this academic update request on MAP-Works &gt;</p></td>
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



update synapse.person set risk_level=4 where risk_level>4;

/* end risk re-factoring end */




INSERT INTO risk_model_master (created_at, modified_at, deleted_at, name, calculation_start_date, calculation_end_date, model_state, enrollment_date, created_by, modified_by, deleted_by) VALUES ("2015-05-11 06:43:55", "2015-05-11 06:43:55", NULL, "RiskModel_TestCase_A", "2015-05-10 00:00:00", "2016-05-11 00:00:00","Unassigned", "2015-12-20 00:00:00", NULL, NULL, NULL);
SET @model := (SELECT max(id) FROM risk_model_master);

INSERT INTO risk_level (created_at, modified_at, deleted_at, created_by, modified_by, deleted_by, risk_text, image_name, color_hex) VALUES ("2015-05-11 06:43:55", "2015-05-11 06:43:55", NULL, NULL, NULL, NULL, "red2", NULL, NULL);
 
SET @levelid := (SELECT max(id) FROM risk_level);
 
INSERT INTO risk_model_levels ( min, max, risk_model_id, risk_level) VALUES ( 1, 2.0, @model, @levelid);



INSERT INTO risk_level (created_at, modified_at, deleted_at, created_by, modified_by, deleted_by, risk_text, image_name, color_hex) VALUES ("2015-05-11 06:43:55", "2015-05-11 06:43:55", NULL, NULL, NULL, NULL, "red", NULL, NULL);
 
SET @levelid := (SELECT max(id) FROM risk_level);
 
INSERT INTO risk_model_levels ( min, max, risk_model_id, risk_level) VALUES ( 2.1, 3.0, @model, @levelid);

INSERT INTO risk_level (created_at, modified_at, deleted_at, created_by, modified_by, deleted_by, risk_text, image_name, color_hex) VALUES ("2015-05-11 06:43:55", "2015-05-11 06:43:55", NULL, NULL, NULL, NULL, "yellow", NULL, NULL);
 
SET @levelid := (SELECT max(id) FROM risk_level);
 
INSERT INTO risk_model_levels ( min, max, risk_model_id, risk_level) VALUES ( 3.1, 4.0, @model, @levelid);


INSERT INTO risk_level (created_at, modified_at, deleted_at, created_by, modified_by, deleted_by, risk_text, image_name, color_hex) VALUES ("2015-05-11 06:43:55", "2015-05-11 06:43:55", NULL, NULL, NULL, NULL, "green", NULL, NULL);
 
SET @levelid := (SELECT max(id) FROM risk_level);
 
INSERT INTO risk_model_levels ( min, max, risk_model_id, risk_level) VALUES ( 4.1, 5.0, @model, @levelid);




INSERT INTO ebi_config(`key`,`value`) values('Risk_Source_Types', 'profile, surveyquestion, surveyfactor, ISP, ISQ, questionbank');

INSERT INTO `risk_variable` (`ebi_metadata_id`,`survey_id`,`org_id`,`risk_b_variable`,`variable_type`,`is_calculated`,`calc_type`,`is_archived`,`source`) VALUES (1,1,1,'C_HSGradYear','continuous',0,'Sum',false,'profile');

INSERT INTO `risk_variable_range` (`bucket_value`, `risk_variable_id`, `min`, `max`) VALUES ('1', '1', '1.1', '1.2');
INSERT INTO `risk_variable_range` (`bucket_value`, `risk_variable_id`, `min`, `max`) VALUES ('2', '1', '1.3', '1.5');
INSERT INTO `risk_variable_range` (`bucket_value`, `risk_variable_id`, `min`, `max`) VALUES ('3', '1', '2', '2.5');
INSERT INTO `risk_variable_range` (`bucket_value`, `risk_variable_id`, `min`, `max`) VALUES ('4', '1', '3.1', '3.3');
INSERT INTO `risk_variable_range` (`bucket_value`, `risk_variable_id`, `min`, `max`) VALUES ('5', '1', '3.8', '4');
INSERT INTO `risk_variable_range` (`bucket_value`, `risk_variable_id`, `min`, `max`) VALUES ('6', '1', '4.1', '4.5');
INSERT INTO `risk_variable_range` (`bucket_value`, `risk_variable_id`, `min`, `max`) VALUES ('7', '1', '5', '7');

UPDATE `synapse`.`organization` SET `campus_id`='ORG1' WHERE `id`='1';





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
* Fix For ESPRJ 3440 
*/
SET @Final_Grade_Of_C_Or_Below := (select id from  ebi_search where query_key ='Final_Grade_Of_C_Or_Below');
UPDATE `ebi_search_lang` SET `ebi_search_id` = @Final_Grade_Of_C_Or_Below WHERE `sub_category_name` = 'Final grade of C or below';


INSERT INTO `ebi_config` (`created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `key`, `value`) VALUES  (NULL, NULL, NULL, NULL, NULL, NULL, 'Audit_Entities', '[\"Synapse\\\\CoreBundle\\\\Entity\\\\OrgFeatures\"]');

/*
-- ESPRJ - 1783 ,1893, 1784, 1785 - Multi Campus Hierarchy 

-- Date:  15/5/2015
*/

INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Send_Invitation_to_User',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>
Welcome to Mapworks. Use the link below to create your password and start using Mapworks. $$activation_token$$
If you believe that you received this email in error or if you have any questions, please contact Mapworks support at support@mapworks.com.
Thank you from the Skyfactor Mapworks team.
[Skyfactor Mapworks logo].
</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>','Welcome to Mapworks');


INSERT INTO `ebi_config` (`key`, `value`) VALUES ('MultiCampus_Change_Request', 'http://synapse-dev.mnv-tech.com/');

INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Accept_Change_Request',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>
Welcome to Mapworks. Use the link below to create your password and start using Mapworks. 
If you believe that you received this email in error or if you have any questions, please contact Mapworks support at support@mapworks.com.
Thank you from the Skyfactor Mapworks team.
[Skyfactor Mapworks logo].
</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>','Change Requested Accepted');

INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Deny_Change_Request',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>
Welcome to Mapworks. Use the link below to create your password and start using Mapworks. 
If you believe that you received this email in error or if you have any questions, please contact Mapworks support at support@mapworks.com.
Thank you from the Skyfactor Mapworks team.
[Skyfactor Mapworks logo].
</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>','Change Request Denied');

INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activate_Email',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>
Welcome to Mapworks. Use the link below to create your password and start using Mapworks. $$activation_token$$
If you believe that you received this email in error or if you have any questions, please contact Mapworks support at support@mapworks.com.
Thank you from the Skyfactor Mapworks team.
[Skyfactor Mapworks logo].
</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>','Activate Email');


INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Deactivate_Email',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>
Welcome to Mapworks. Email Deactivated. 
If you believe that you received this email in error or if you have any questions, please contact Mapworks support at support@mapworks.com.
Thank you from the Skyfactor Mapworks team.
[Skyfactor Mapworks logo].
</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>','Deactivate Email');

/*
* ESPRJ-2721 - 5/5/2015
*/
UPDATE `synapse`.`email_template` SET `email_key`='Forgot_Password_Staff' WHERE `email_key`='Forgot_Password_Faculty';

/* 
* ESPRJ - 2781
  Date: 2015-05-14
*/
UPDATE `synapse`.`ebi_search` SET `query`='select mm.id,mm.metadata_type,dml.datablock_desc as blockdesc,mml.meta_name,pm.metadata_value as myanswer from datablock_master dm join datablock_master_lang dml ON dm.id = dml.datablock_id JOIN  datablock_metadata dmd ON dmd.datablock_id = dm.id JOIN ebi_metadata mm ON dmd.ebi_metadata_id = mm.id JOIN ebi_metadata_lang mml ON mml.ebi_metadata_id = mm.id JOIN person_ebi_metadata pm ON pm.ebi_metadata_id = mm.id  where mml.lang_id=$$lang$$ AND dm.block_type="profile" AND pm.person_id = $$studentid$$ AND dm.id IN($$datablockpermission$$) AND mm.deleted_at IS NULL AND dm.deleted_at IS NULL AND dml.deleted_at IS NULL AND dmd.deleted_at IS NULL AND pm.deleted_at IS NULL AND mml.deleted_at IS NULL' WHERE `query_key` ='Student_Profile_Datablock_Info';

UPDATE `synapse`.`ebi_search` SET `query`='select mm.id,mm.metadata_type,mm.meta_name,pm.metadata_value as myanswer from  org_metadata mm JOIN person_org_metadata pm ON pm.org_metadata_id = mm.id  where mm.definition_type="O" AND pm.person_id = $$studentid$$ AND mm.id IN($$isppermission$$) AND mm.deleted_at IS NULL AND pm.deleted_at IS NULL' WHERE `query_key`='Student_Profile_ISP_Info';


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
-- ESPRJ - 2121 - As a faculty/staff user, I get an email notification when a student books.

-- Date:  20-05-2015 1.45pm By Saravanan
*/

INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Appointment_Book_Student_to_Staff',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\r 	<head>\r 		<style>\r 		body {\r     background: none repeat scroll 0 0 #f4f4f4;\r 	\r }\r 		table {\r     padding: 21px;\r     width: 799px;\r 	font-family: helvetica,arial,verdana,san-serif;\r 	font-size:13px;\r 	color:#333;\r 	}\r 		</style>\r 	</head>\r 	<body>\r 	\r 		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r 			<tbody>\r 			\r 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$staff_name$$:</td></tr>\r 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>An appointment has been booked with $$student_name$$  \r 				on $$app_datetime$$. To view the appointment details,\r 				please log in to your Mapworks dashboard and visit <a class=\"external-link\" href=\"$$staff_dashboard$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155);text-decoration: underline;\">Mapworks student dashboard view appointment module</a>.</td></tr>\r 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/><img src=\"$$Skyfactor_Mapworks_logo$$\" alt=\"Skyfactor Mapworks logo\" title=\"Skyfactor Mapworks logo\" /></td></tr>\r 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\r 			\r 			</tbody>\r 		</table>\r</body>\r</html>','Mapworks appointment booked');

/*
-- ESPRJ - 2121 - As a faculty/staff user, I get an email notification when a student cancel an appointment.

-- Date:  20-05-2015 1.45pm By Saravanan
*/

INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Appointment_Cancel_Student_to_Staff',1,'no-reply@mapworks.com','SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');

SET @mmid := (SELECT MAX(id) FROM email_template);

INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html> <head>\r <style>\r body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}\r </style>\r </head>\r <body>\r <table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\r <tbody>\r <tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$staff_name$$:</td></tr>\r <tr style=\"background:#fff;border-collapse:collapse;\"><td>Your booked appointment with $$student_name$$ on $$app_datetime$$ has been cancelled. To book a new appointment, please log in to your Mapworks dashboard and visit \r <a class=\"external-link\" href=\"$$staff_dashboard$$\" target=\"_blank\" style=\"color: rgb(41, 114, 155);text-decoration: underline;\">\r Mapworks faculty dashboard view appointment module</a>.</td></tr>\r <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/><img src=\"$$Skyfactor_Mapworks_logo$$\" alt=\"Skyfactor Mapworks logo\" title=\"Skyfactor Mapworks logo\" /></td></tr>\r <tr style=\"background:#fff;border-collapse:collapse;\"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>','Mapworks appointment cancelled');

/*
-- ESPRJ - 161 Student Campus Connections
-- Query: SELECT * FROM synapse.org_group_faculty
*/
INSERT INTO `org_group_faculty` (`org_permissionset_id`,`organization_id`,`org_group_id`,`person_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_invisible`) VALUES (1,1,2,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*
-- ESPRJ Campus connection
*/

INSERT INTO `org_group_faculty` (`org_permissionset_id`, `person_id`, `org_group_id`, `organization_id`) VALUES ('1','4', '2', '1');

/*
-- ESPRJ Office hours slot for a faculty for student to book an appointment
*/

INSERT INTO `office_hours` (`organization_id`, `person_id`, `slot_type`, `location`, `slot_start`, `slot_end`) VALUES ('1', '4', 'I', 'My Test Location', NOW() + INTERVAL 3 HOUR, NOW() + INTERVAL 4 HOUR);

/*
* ESPRJ-2854
*/
INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('System_Admin_URL', 'http://synapse-qa-admin.mnv-tech.com/');


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
<p><img src="https://synapse-qa.mnv-tech.com/images/Skyfactor-Mapworks-login.png"/><br/></p>
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
<p><img src="https://synapse-qa.mnv-tech.com/images/Skyfactor-Mapworks-login.png"/><br/></p>
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
<p><img src="https://synapse-qa.mnv-tech.com/images/Skyfactor-Mapworks-login.png"/><br/></p>
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
<p><img src="https://synapse-qa.mnv-tech.com/images/Skyfactor-Mapworks-login.png"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
				
 </div>
 </html>' WHERE `email_template_id`=@emtid;
 
 /* 
Person david.warner@gmail.com/Mapworks Admin record to be created
*/

INSERT INTO `Client` (`id`,`random_id`,`redirect_uris`,`secret`,`allowed_grant_types`) VALUES (3,'14tx5vbsnois4ggg0ok0c4gog8kg0ww488gwkg88044cog4884','a:0:{}','4v5p8idswhs0404owsws48gwwccc4wksw4c8s80wcocwskockg','a:2:{i:0;s:8:\"password\";i:1;s:13:\"refresh_token\";}');

INSERT INTO `person` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`firstname`,`lastname`,`title`,`date_of_birth`,`external_id`,`username`,`password`,`activation_token`,`confidentiality_stmt_accept_date`,`organization_id`,`token_expiry_date`,`welcome_email_sent_date`,`risk_level`,`risk_update_date`,`intent_to_leave`,`intent_to_leave_update_date`,`last_contact_date`,`cohert`,`last_activity`,`record_type`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'David','Warner',NULL,NULL,'David123','david.warner@gmail.com','$2y$13$f6bnaUYhaIO0qzJ0krqrIeUDnxJxWYYEyB3L6qDDK/1ln5CsHKEca',NULL,NULL,-1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

SET @roleId := (select role_id from role_lang where role_name = 'Mapworks Admin');
SET @personId := (select id from person where username = 'david.warner@gmail.com');
SET @orgId := (select organization_id from person where username = 'david.warner@gmail.com');
						
INSERT INTO `organization_role` (`role_id`,`person_id`,`organization_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`) VALUES (@roleId,@personId,-1,NULL,NULL,NULL,NULL,NULL,NULL);

INSERT INTO `contact_info` (`address_1`,`address_2`,`city`,`zip`,`state`,`country`,`primary_mobile`,`alternate_mobile`,`home_phone`,`office_phone`,`primary_email`,`alternate_email`,`primary_mobile_provider`,`alternate_mobile_provider`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'9591900663',NULL,NULL,NULL,'david.warner@gmail.com',NULL,'9224852114',NULL,NULL,NULL,NULL,'2014-10-15 12:34:01',NULL,NULL);
SET @contactId := (select max(id) from contact_info);

INSERT INTO `person_contact_info` (`person_id`,`contact_id`,`status`,`created_by`,`modified_by`,`deleted_by`,`created_at`,`modified_at`,`deleted_at`) VALUES (@personId,@contactId,'A',NULL,NULL,NULL,NULL,NULL,NULL);

/*
* Test case fix for "PasswordServiceTest" - Email template not found fix. Body and subject to be updated.
*/
INSERT INTO `email_template` (`email_key`, `is_active`, `from_email_address`) VALUES('Coordinator_Reset_Password_Expiry_Hrs', 1, 'no-reply@mapworks.com');
SET @emailId := (select max(id) from email_template);
INSERT INTO `email_template_lang` (`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (@emailId, 1, NULL, NULL, NULL, NULL, NULL, NULL, '<HTML></HTML>','MAP-Works');

INSERT INTO `email_template` (`email_key`, `is_active`, `from_email_address`) VALUES('Staff_Reset_Password_Expiry_Hrs', 1, 'no-reply@mapworks.com');
SET @emailId := (select max(id) from email_template);
INSERT INTO `email_template_lang` (`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (@emailId, 1, NULL, NULL, NULL, NULL, NULL, NULL, '<HTML></HTML>','MAP-Works');

INSERT INTO `email_template` (`email_key`, `is_active`, `from_email_address`) VALUES('StudentReset_Password_Expiry_Hrs', 1, 'no-reply@mapworks.com');
SET @emailId := (select max(id) from email_template);
INSERT INTO `email_template_lang` (`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (@emailId, 1, NULL, NULL, NULL, NULL, NULL, NULL, '<HTML></HTML>','MAP-Works');

INSERT INTO `email_template` (`email_key`, `is_active`, `from_email_address`) VALUES('Forgot_Password_Coordinator', 1, 'no-reply@mapworks.com');
SET @emailId := (select max(id) from email_template);
INSERT INTO `email_template_lang` (`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (@emailId, 1, NULL, NULL, NULL, NULL, NULL, NULL, '<HTML></HTML>','MAP-Works');

INSERT INTO `email_template` (`email_key`, `is_active`, `from_email_address`) VALUES('Create_Password_Coordinator', 1, 'no-reply@mapworks.com');
SET @emailId := (select max(id) from email_template);
INSERT INTO `email_template_lang` (`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (@emailId, 1, NULL, NULL, NULL, NULL, NULL, NULL, '<HTML></HTML>','MAP-Works');

INSERT INTO `email_template` (`email_key`, `is_active`, `from_email_address`) VALUES('MyAccount_Updated_Staff', 1, 'no-reply@mapworks.com');
SET @emailId := (select max(id) from email_template);
INSERT INTO `email_template_lang` (`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (@emailId, 1, NULL, NULL, NULL, NULL, NULL, NULL, '<HTML></HTML>','MAP-Works');

/*
* ebi_config update
*/
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'AWS_Key','AKIAJJWBI4AF5T4VLVSA');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'AWS_Secret','6gHgrpMsa1Ty6ntBFloJ0WKOWY54GmLYGpzVz+zF');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'AWS_Region','us-east-1');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'AWS_Bucket','mapworks-development');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'AWS_Get_Expire_Time','600');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'AWS_Put_Expire_Time','1800');

INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Gateway_Associate_URL','https://gateway-dev.mnv-tech.com/v1/associate');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Gateway_Associate_Form_URL','https://gateway-dev.mnv-tech.com/v1/association_launch');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Gateway_Verify_URL','https://gateway-dev.mnv-tech.com/v1/verify_token');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Gateway_Staff_Landing_page','http://synapse-dev.mnv-tech.com/#/overview');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Gateway_Student_Landing_page','http://synapse-dev.mnv-tech.com/#/studentprofile');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Gateway_Key', '40bc7738-a9ae-4072-906a-a35cca4d908c');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Gateway_Secret', '1d0fd92f-f081-43c0-9515-c1f9eab1c325');

/*
* Email Template Lang fix for the key "Academic_Update_Request_Staff_Closed"
*/
SET @emtid := (SELECT id FROM email_template where email_key="Academic_Update_Request_Staff_Closed");
INSERT INTO `email_template_lang` (`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (@emtid, 1, NULL, NULL, NULL, NULL, NULL, NULL, '<HTML></HTML>','MAP-Works');

/*
* Email Template Lang fix for the key "Welcome_To_Mapworks"
*/
SET @emtid := (SELECT id FROM email_template where email_key="Welcome_To_Mapworks");
INSERT INTO `email_template_lang` (`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (@emtid, 1, NULL, NULL, NULL, NULL, NULL, NULL, '<HTML></HTML>','MAP-Works');


/*
* Update organization_role table for get coordinator to set role id is 2
*/
UPDATE `organization_role` SET `role_id`='2' WHERE `person_id`='2';

/*
* Data for CreateStudentJobTest functional test case fix
*/
INSERT INTO `ebi_metadata` (`meta_key`, `definition_type`, `metadata_type`) VALUES ('YearID', 'E', 'S');
INSERT INTO `ebi_metadata` (`meta_key`, `definition_type`, `metadata_type`) VALUES ('TermID', 'E', 'N');
INSERT INTO `ebi_metadata` (`meta_key`, `definition_type`, `metadata_type`) VALUES ('SurveyCohort', 'E', 'N');
INSERT INTO `ebi_metadata` (`meta_key`, `definition_type`, `metadata_type`) VALUES ('ReceiveSurvey', 'E', 'N');

/*
* Data for faculty access to student - Student service test case fix
*/
INSERT INTO `org_group_faculty` (`org_permissionset_id`, `organization_id`, `org_group_id`, `person_id`) VALUES ('1', '1', '2', '1');
INSERT INTO `org_group_students` (`person_id`, `org_group_id`, `organization_id`) VALUES ('8', '2', '1');

/*
* Test case - No attendees for appointment fix
*/
INSERT INTO `appointment_recepient_and_status` (`organization_id`, `appointments_id`, `person_id_faculty`, `person_id_student`) VALUES ('1', '1', '1', '8');

/*
* insert ClassLevel metadata for search api test case to pass
*/
insert into ebi_metadata (`meta_key`, `definition_type`, `metadata_type`, 
`no_of_decimals`, `is_required`, `min_range`, `max_range`, `entity`, `sequence`, `meta_group`, `scope`, `status`)
values ( 'ClassLevel', 'E', 'S', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'N', NULL);
select max(id) into @ebi_metadata_id from ebi_metadata ;
insert into ebi_metadata_list_values (
`lang_id`, `ebi_metadata_id`, `list_name`, `list_value`, `sequence`)
values ('1', @ebi_metadata_id, '1st Year/Freshman', '0', '0');

/*
* insert to get StudentSurveyServiceTest fixed
*/
insert into org_person_student_survey_link set org_id=1, person_id =6, survey_id=1, cohort=1,org_academic_year_id=2;

/*
* Update organization_role table to set person to coordinator for test case UserServiceTest
*/
UPDATE `organization_role` SET `role_id`='3' WHERE `person_id`='1';

/*
-- Query: SELECT * FROM synapse.ebi_template_lang
-- Date: 2015-11-30 15:55
*/

/*
 --  fix for missing contact info
*/
INSERT INTO `contact_info` (`id`, `address_1`, `address_2`, `city`, `zip`, `state`, `country`, `primary_mobile`, `alternate_mobile`, `home_phone`, `office_phone`, `primary_email`, `alternate_email`, `primary_mobile_provider`, `alternate_mobile_provider`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(1,NULL,NULL,NULL,NULL,NULL,NULL,'9591900663',NULL,NULL,NULL,'ramesh.kumhar@techmahindra.com',NULL,'9224852114',NULL,NULL,NULL,NULL,'2014-10-15 12:34:01',NULL,NULL);


SET @id := (select id from language_master where langcode = 'en_US');

INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_AcademicUpdates_Header_Template',@id,NULL,'<!doctype html>\n <html>\n 	<head>\n 		<title></title>\n 		<style>\n 		    .container{\n 				padding:60px 50px;\n 			}\n 			p,body{\n 				margin:0;\n 				color:#003366;\n 			}\n 			#outerContainer{\n 				float:left;\n 				width:100%;\n 				box-sizing:border-box;\n 			}\n 			#outerContainer .align1{\n 				float:left;\n 				width:100%;\n 			}\n 			#outerContainer .columnNameContainer{\n 				float:left;\n 				display:inline-block;\n 			}\n 			#outerContainer .heading{\n 				font-weight:bold;\n 				font-size:22px;\n 			}\n 			#outerContainer .headingDiv{\n 				margin-bottom:30px;\n 			}\n 			#outerContainer .subHeadingDiv{\n 				margin-bottom:15px;\n 				float: left;\n 				width: 100%;\n 			}\n 			#outerContainer .userInfo{\n 				margin-bottom:5px;\n 			}\n 			#outerContainer .userInfoDetails{\n 				height:auto;\n 				margin-bottom:15px;\n 			}\n 			#outerContainer .subHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .idHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .horizontalLine{\n 				background-color: #ccc;\n 				width:100%;\n 				height:2px;\n 			}\n 			#outerContainer .horizontalDottedLine{\n 			    border-bottom: dotted;\n 				border-width: 4px; \n 				color:#ccc;\n 			}\n 			#outerContainer .boldStyler{\n 				font-weight:bold;\n 			}\n 			#outerContainer .columnNameContainer2{\n 				min-width:30%;\n 				width:auto;\n 				height:auto;\n 				padding:0px 10px;\n 			}\n 			#outerContainer .details{\n 				min-width:30%;\n 				width:auto;\n 				height:auto;\n 			}\n 			#outerContainer .dataTypeContainer{\n 				width:58%;\n 				height:auto;\n 			}\n 			#outerContainer .userInfoHeading{\n 				margin-bottom:3px;\n 			}\n 			#outerContainer .validvalues{\n 				padding:0px 10px;\n 			}\n 			#outerContainer .italicStyler{\n 				font-style:italic;\n 			}\n 		</style>\n 	</head>\n 	<body>\n 	<div class=\"container\">\n 		<div id=\"outerContainer\">\n 			<div class=\"align1 headingDiv\">\n 				<p class=\"heading\">MAP-Works: Academic Updates File Data Definitions<span><img src=\"\" /></span></p>\n 			</div>\n 			<div class=\"subHeadingDiv\">\n 				<p class=\"subHeading\">General Academic Updates Information</p>\n 				<div class=\"horizontalLine\"></div>\n 			</div>\n 		</div>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_CategoryType_Body_Template',@id,NULL,'<div id=\"outerContainer\">\n 			<div class=\"align1 subHeadingDiv\">\n 				<div class=\"columnNameContainer details\"><p class=\"idHeading\">$$column_name$$ &nbsp;<span style=\"font-style:italic;color:#666;font-size:16px;\"> $$required$$</span></p></div>\n 				<div class=\"columnNameContainer dataTypeContainer\"> <p>$$description$$</p></div>\n 			</div>\n 			<div class=\"align1 userInfo\">\n 				<p class=\"userInfoHeading\">Upload Information</p>\n 				<div class=\"horizontalDottedLine\"></div>\n 			</div>\n 			<div class=\"align1 userInfoDetails\">\n 				<div class=\"columnNameContainer columnNameContainer2\"><p><span class=\"italicStyler\">Column Name:</span> <span class=\"boldStyler\">$$column_name$$</span></p></div>\n 				<div class=\"columnNameContainer dataTypeContainer\">\n 					<p><span class=\"italicStyler\">Data Type:</span>Category</p>\n 				</div>\n 			</div>\n 			<div class=\"validvalues align1\">\n 				<p>Valid Values:</p>\n 				<ul class=\"valueslist\">\n 					$$value_list$$\n 				</ul>\n 			</div>\n 			</div>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_Course_Footer_Template',@id,NULL,'</div>  \n   </body>\n</html>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_Courses_Header_Template',@id,NULL,'<!doctype html>\n <html>\n 	<head>\n 		<title></title>\n 		<style>\n 			.container{\n 				padding:60px 50px;\n 			}\n 			p,body{\n 				margin:0;\n 				color:#003366;\n 			}\n 			#outerContainer{\n 				float:left;\n 				width:100%;\n 				box-sizing:border-box;\n 			}\n 			#outerContainer .align1{\n 				float:left;\n 				width:100%;\n 			}\n 			#outerContainer .columnNameContainer{\n 				float:left;\n 				display:inline-block;\n 			}\n 			#outerContainer .heading{\n 				font-weight:bold;\n 				font-size:22px;\n 			}\n 			#outerContainer .headingDiv{\n 				margin-bottom:30px;\n 			}\n 			#outerContainer .subHeadingDiv{\n 				margin-bottom:15px;\n 				float: left;\n 				width: 100%;\n 			}\n 			#outerContainer .userInfo{\n 				margin-bottom:5px;\n 			}\n 			#outerContainer .userInfoDetails{\n 				height:auto;\n 				margin-bottom:15px;\n 			}\n 			#outerContainer .subHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .idHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .horizontalLine{\n 				background-color: #ccc;\n 				width:100%;\n 				height:2px;\n 			}\n 			#outerContainer .horizontalDottedLine{\n 			    border-bottom: dotted;\n 				border-width: 4px; \n 				color:#ccc;\n 			}\n 			#outerContainer .boldStyler{\n 				font-weight:bold;\n 			}\n 			#outerContainer .columnNameContainer2{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 				padding:0px 10px;\n 			}\n 			#outerContainer .details{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 			}\n 			#outerContainer .dataTypeContainer{\n 				width:58%;\n 				height:auto;\n 			}\n 			#outerContainer .userInfoHeading{\n 				margin-bottom:3px;\n 			}\n 			#outerContainer .validvalues{\n 				padding:0px 10px;\n 			}\n 			#outerContainer .italicStyler{\n 				font-style:italic;\n 			}\n 		</style>\n 	</head>\n 	<body>\n 	<div class=\"container\">\n 		<div id=\"outerContainer\">\n 			<div class=\"align1 headingDiv\">\n 				<p class=\"heading\">MAP-Works: Courses and Sections File Data Definitions<span><img src=\"\" /></span></p>\n 			</div>\n 			<div class=\"subHeadingDiv\">\n 				<p class=\"subHeading\">General Courses and Sections Information</p>\n 				<div class=\"horizontalLine\"></div>\n 			</div>\n 		</div>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_CoursesFaculty_Header_Template',@id,NULL,'<!doctype html>\n <html>\n 	<head>\n 		<title></title>\n 		<style>\n 			.container{\n 				padding:60px 50px;\n 			}\n 			p,body{\n 				margin:0;\n 				color:#003366;\n 			}\n 			#outerContainer{\n 				float:left;\n 				width:100%;\n 				box-sizing:border-box;\n 			}\n 			#outerContainer .align1{\n 				float:left;\n 				width:100%;\n 			}\n 			#outerContainer .columnNameContainer{\n 				float:left;\n 				display:inline-block;\n 			}\n 			#outerContainer .heading{\n 				font-weight:bold;\n 				font-size:22px;\n 			}\n 			#outerContainer .headingDiv{\n 				margin-bottom:30px;\n 			}\n 			#outerContainer .subHeadingDiv{\n 				margin-bottom:15px;\n 				float: left;\n 				width: 100%;\n 			}\n 			#outerContainer .userInfo{\n 				margin-bottom:5px;\n 			}\n 			#outerContainer .userInfoDetails{\n 				height:auto;\n 				margin-bottom:15px;\n 			}\n 			#outerContainer .subHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .idHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .horizontalLine{\n 				background-color: #ccc;\n 				width:100%;\n 				height:2px;\n 			}\n 			#outerContainer .horizontalDottedLine{\n 			    border-bottom: dotted;\n 				border-width: 4px; \n 				color:#ccc;\n 			}\n 			#outerContainer .boldStyler{\n 				font-weight:bold;\n 			}\n 			#outerContainer .columnNameContainer2{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 				padding:0px 10px;\n 			}\n 			#outerContainer .details{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 			}\n 			#outerContainer .dataTypeContainer{\n 				width:58%;\n 				height:auto;\n 			}\n 			#outerContainer .userInfoHeading{\n 				margin-bottom:3px;\n 			}\n 			#outerContainer .validvalues{\n 				padding:0px 10px;\n 			}\n 			#outerContainer .italicStyler{\n 				font-style:italic;\n 			}\n 		</style>\n 	</head>\n 	<body>\n 	<div class=\"container\">\n 		<div id=\"outerContainer\">\n 			<div class=\"align1 headingDiv\">\n 				<p class=\"heading\">MAP-Works: Course Faculty/Staff File Data Definitions<span><img src=\"\" /></span></p>\n 			</div>\n 			<div class=\"subHeadingDiv\">\n 				<p class=\"subHeading\">General Course Faculty/Staff Information</p>\n 				<div class=\"horizontalLine\"></div>\n 			</div>\n 		</div>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_CoursesStudents_Header_Template',@id,NULL,'<!doctype html>\n <html>\n 	<head>\n 		<title></title>\n 		<style>\n 			.container{\n 				padding:60px 50px;\n 			}\n 			p,body{\n 				margin:0;\n 				color:#003366;\n 			}\n 			#outerContainer{\n 				float:left;\n 				width:100%;\n 				box-sizing:border-box;\n 			}\n 			#outerContainer .align1{\n 				float:left;\n 				width:100%;\n 			}\n 			#outerContainer .columnNameContainer{\n 				float:left;\n 				display:inline-block;\n 			}\n 			#outerContainer .heading{\n 				font-weight:bold;\n 				font-size:22px;\n 			}\n 			#outerContainer .headingDiv{\n 				margin-bottom:30px;\n 			}\n 			#outerContainer .subHeadingDiv{\n 				margin-bottom:15px;\n 				float: left;\n 				width: 100%;\n 			}\n 			#outerContainer .userInfo{\n 				margin-bottom:5px;\n 			}\n 			#outerContainer .userInfoDetails{\n 				height:auto;\n 				margin-bottom:15px;\n 			}\n 			#outerContainer .subHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .idHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .horizontalLine{\n 				background-color: #ccc;\n 				width:100%;\n 				height:2px;\n 			}\n 			#outerContainer .horizontalDottedLine{\n 			    border-bottom: dotted;\n 				border-width: 4px; \n 				color:#ccc;\n 			}\n 			#outerContainer .boldStyler{\n 				font-weight:bold;\n 			}\n 			#outerContainer .columnNameContainer2{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 				padding:0px 10px;\n 			}\n 			#outerContainer .details{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 			}\n 			#outerContainer .dataTypeContainer{\n 				width:58%;\n 				height:auto;\n 			}\n 			#outerContainer .userInfoHeading{\n 				margin-bottom:3px;\n 			}\n 			#outerContainer .validvalues{\n 				padding:0px 10px;\n 			}\n 			#outerContainer .italicStyler{\n 				font-style:italic;\n 			}\n 		</style>\n 	</head>\n 	<body>\n 	<div class=\"container\">\n 		<div id=\"outerContainer\">\n 			<div class=\"align1 headingDiv\">\n 				<p class=\"heading\">MAP-Works: Course Students File Data Definitions<span><img src=\"\" /></span></p>\n 			</div>\n 			<div class=\"subHeadingDiv\">\n 				<p class=\"subHeading\">General Course Students Information</p>\n 				<div class=\"horizontalLine\"></div>\n 			</div>\n 		</div>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_CourseStudent_Footer_Template',@id,NULL,'</div>  \n   </body>\n</html>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_DateType_Body_Template',@id,NULL,'<div id=\"outerContainer\">\n 			<div class=\"align1 subHeadingDiv\">\n 				<div class=\"columnNameContainer details\"><p class=\"idHeading\">$$column_name$$ &nbsp;<span style=\"font-style:italic;color:#666;font-size:16px;\"> $$required$$</span></p></div>\n 				<div class=\"columnNameContainer dataTypeContainer\"> <p>$$description$$</p></div>\n 			</div>\n 			<div class=\"align1 userInfo\">\n 				<p class=\"userInfoHeading\">Upload Information</p>\n 				<div class=\"horizontalDottedLine\"></div>\n 			</div>\n 			<div class=\"align1 userInfoDetails\">\n 				<div class=\"columnNameContainer columnNameContainer2\"><p><span class=\"italicStyler\">Column Name:</span> <span class=\"boldStyler\">$$column_name$$</span></p></div>\n 				<div class=\"columnNameContainer dataTypeContainer\">\n 					<p><span class=\"italicStyler\">Data Type:</span>Date</p>\n 				</div>\n 			</div></div>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_Faculty_Header_Template',@id,NULL,'<!doctype html>\n <html>\n 	<head>\n 		<title></title>\n 		<style>\n 			.container{\n 				padding:60px 50px;\n 			}\n 			p,body{\n 				margin:0;\n 				color:#003366;\n 			}\n 			#outerContainer{\n 				float:left;\n 				width:100%;\n 				box-sizing:border-box;\n 			}\n 			#outerContainer .align1{\n 				float:left;\n 				width:100%;\n 			}\n 			#outerContainer .columnNameContainer{\n 				float:left;\n 				display:inline-block;\n 			}\n 			#outerContainer .heading{\n 				font-weight:bold;\n 				font-size:22px;\n 			}\n 			#outerContainer .headingDiv{\n 				margin-bottom:30px;\n 			}\n 			#outerContainer .subHeadingDiv{\n 				margin-bottom:15px;\n 				float: left;\n 				width: 100%;\n 			}\n 			#outerContainer .userInfo{\n 				margin-bottom:5px;\n 			}\n 			#outerContainer .userInfoDetails{\n 				height:auto;\n 				margin-bottom:15px;\n 			}\n 			#outerContainer .subHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .idHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .horizontalLine{\n 				background-color: #ccc;\n 				width:100%;\n 				height:2px;\n 			}\n 			#outerContainer .horizontalDottedLine{\n 			    border-bottom: dotted;\n 				border-width: 4px; \n 				color:#ccc;\n 			}\n 			#outerContainer .boldStyler{\n 				font-weight:bold;\n 			}\n 			#outerContainer .columnNameContainer2{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 				padding:0px 10px;\n 			}\n 			#outerContainer .details{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 			}\n 			#outerContainer .dataTypeContainer{\n 				width:58%;\n 				height:auto;\n 			}\n 			#outerContainer .userInfoHeading{\n 				margin-bottom:3px;\n 			}\n 			#outerContainer .validvalues{\n 				padding:0px 10px;\n 			}\n 			#outerContainer .italicStyler{\n 				font-style:italic;\n 			}\n 		</style>\n 	</head>\n 	<body>\n 	<div class=\"container\">\n 		<div id=\"outerContainer\">\n 			<div class=\"align1 headingDiv\">\n 				<p class=\"heading\">MAP-Works: Faculty/Staff File Data Definitions<span><img src=\"\" /></span></p>\n 			</div>\n 			<div class=\"subHeadingDiv\">\n 				<p class=\"subHeading\">General Faculty/Staff Information</p>\n 				<div class=\"horizontalLine\"></div>\n 			</div>\n 		</div>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_GroupFaculty_Header_Template',@id,NULL,'<!doctype html>\n <html>\n 	<head>\n 		<title></title>\n 		<style>\n 			.container{\n 				padding:60px 50px;\n 			}\n 			p,body{\n 				margin:0;\n 				color:#003366;\n 			}\n 			#outerContainer{\n 				float:left;\n 				width:100%;\n 				box-sizing:border-box;\n 			}\n 			#outerContainer .align1{\n 				float:left;\n 				width:100%;\n 			}\n 			#outerContainer .columnNameContainer{\n 				float:left;\n 				display:inline-block;\n 			}\n 			#outerContainer .heading{\n 				font-weight:bold;\n 				font-size:22px;\n 			}\n 			#outerContainer .headingDiv{\n 				margin-bottom:30px;\n 			}\n 			#outerContainer .subHeadingDiv{\n 				margin-bottom:15px;\n 				float: left;\n 				width: 100%;\n 			}\n 			#outerContainer .userInfo{\n 				margin-bottom:5px;\n 			}\n 			#outerContainer .userInfoDetails{\n 				height:auto;\n 				margin-bottom:15px;\n 			}\n 			#outerContainer .subHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .idHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .horizontalLine{\n 				background-color: #ccc;\n 				width:100%;\n 				height:2px;\n 			}\n 			#outerContainer .horizontalDottedLine{\n 			    border-bottom: dotted;\n 				border-width: 4px; \n 				color:#ccc;\n 			}\n 			#outerContainer .boldStyler{\n 				font-weight:bold;\n 			}\n 			#outerContainer .columnNameContainer2{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 				padding:0px 10px;\n 			}\n 			#outerContainer .details{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 			}\n 			#outerContainer .dataTypeContainer{\n 				width:68%;\n 				height:auto;\n 			}\n 			#outerContainer .userInfoHeading{\n 				margin-bottom:3px;\n 			}\n 			#outerContainer .validvalues{\n 				padding:0px 10px;\n 			}\n 			#outerContainer .italicStyler{\n 				font-style:italic;\n 			}\n 		</style>\n 	</head>\n 	<body>\n 	<div class=\"container\">\n 		<div id=\"outerContainer\">\n 			<div class=\"align1 headingDiv\">\n 				<p class=\"heading\">MAP-Works: Group Membership - Faculty/Staff file definitions <span><img src=\"\" /></span></p>\n 			</div>\n 			<div class=\"subHeadingDiv\">\n 				<p class=\"subHeading\">General Groups Faculty/Staff Information</p>\n 				<div class=\"horizontalLine\"></div>\n 			</div>\n 		</div>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_GroupStudent_Header_Template',@id,NULL,'<!doctype html>\n <html>\n 	<head>\n 		<title></title>\n 		<style>\n 			.container{\n 				padding:60px 50px;\n 			}\n 			p,body{\n 				margin:0;\n 				color:#003366;\n 			}\n 			#outerContainer{\n 				float:left;\n 				width:100%;\n 				box-sizing:border-box;\n 			}\n 			#outerContainer .align1{\n 				float:left;\n 				width:100%;\n 			}\n 			#outerContainer .columnNameContainer{\n 				float:left;\n 				display:inline-block;\n 			}\n 			#outerContainer .heading{\n 				font-weight:bold;\n 				font-size:22px;\n 			}\n 			#outerContainer .headingDiv{\n 				margin-bottom:30px;\n 			}\n 			#outerContainer .subHeadingDiv{\n 				margin-bottom:15px;\n 				float: left;\n 				width: 100%;\n 			}\n 			#outerContainer .userInfo{\n 				margin-bottom:5px;\n 			}\n 			#outerContainer .userInfoDetails{\n 				height:auto;\n 				margin-bottom:15px;\n 			}\n 			#outerContainer .subHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .idHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .horizontalLine{\n 				background-color: #ccc;\n 				width:100%;\n 				height:2px;\n 			}\n 			#outerContainer .horizontalDottedLine{\n 			    border-bottom: dotted;\n 				border-width: 4px; \n 				color:#ccc;\n 			}\n 			#outerContainer .boldStyler{\n 				font-weight:bold;\n 			}\n 			#outerContainer .columnNameContainer2{\n 			    min-width:30%;\n                width:auto;\n 				height:auto;\n 				padding:0px 10px;\n 			}\n 			#outerContainer .details{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 			}\n 			#outerContainer .dataTypeContainer{\n 				width:68%;\n 				height:auto;\n 			}\n 			#outerContainer .userInfoHeading{\n 				margin-bottom:3px;\n 			}\n 			#outerContainer .validvalues{\n 				padding:0px 10px;\n 			}\n 			#outerContainer .italicStyler{\n 				font-style:italic;\n 			}\n 		</style>\n 	</head>\n 	<body>\n 	<div class=\"container\">\n 		<div id=\"outerContainer\">\n 			<div class=\"align1 headingDiv\">\n 				<p class=\"heading\">MAP-Works: Group Membership - Student file definitions <span><img src=\"\" /></span></p>\n 			</div>\n 			<div class=\"subHeadingDiv\">\n 				<p class=\"subHeading\">General Groups Students Information</p>\n 				<div class=\"horizontalLine\"></div>\n 			</div>\n 		</div>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_NumberType_Body_Template',@id,NULL,'<div id=\"outerContainer\">\n 			<div class=\"align1 subHeadingDiv\">\n 				<div class=\"columnNameContainer details\"><p class=\"idHeading\">$$column_name$$ &nbsp;<span style=\"font-style:italic;color:#666;font-size:16px;\"> $$required$$</span></p></div>\n 				<div class=\"columnNameContainer dataTypeContainer\"> <p>$$description$$</p></div>\n 			</div>\n 			<div class=\"align1 userInfo\">\n 				<p class=\"userInfoHeading\">Upload Information</p>\n 				<div class=\"horizontalDottedLine\"></div>\n 			</div>\n 			<div class=\"align1 userInfoDetails\">\n 				<div class=\"columnNameContainer columnNameContainer2\"><p><span class=\"italicStyler\">Column Name:</span> <span class=\"boldStyler\">$$column_name$$</span></p></div>\n 				<div class=\"columnNameContainer dataTypeContainer\">\n 					<p><span class=\"italicStyler\">Data Type:</span>Numbers Only</p>\n 				</div>\n 			</div>\n 			<div class=\"validvalues align1\">\n 				<p style=\"margin-bottom : 15px;\">$$valid_values$$</p>\n 			</div>\n 			</div>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_StringType_Body_Template',@id,NULL,'<div id=\"outerContainer\">\n 			<div class=\"align1 subHeadingDiv\">\n 				<div class=\"columnNameContainer details\"><p class=\"idHeading\">$$column_name$$ &nbsp;<span style=\"font-style:italic;color:#666;font-size:16px;\"> $$required$$</span></p></div>\n 				<div class=\"columnNameContainer dataTypeContainer\"> <p>$$description$$</p></div>\n 			</div>\n 			<div class=\"align1 userInfo\">\n 				<p class=\"userInfoHeading\">Upload Information</p>\n 				<div class=\"horizontalDottedLine\"></div>\n 			</div>\n 			<div class=\"align1 userInfoDetails\">\n 				<div class=\"columnNameContainer columnNameContainer2\"><p><span class=\"italicStyler\">Column Name:</span> <span class=\"boldStyler\">$$column_name$$</span></p></div>\n 				<div class=\"columnNameContainer dataTypeContainer\">\n 					<p><span class=\"italicStyler\">Data Type:</span>Letters and Numbers</p>\n 					<p>(Max Length: $$length$$ characters)</p>\n 				</div>\n 			</div></div>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_Student_Footer_Template',@id,NULL,'</div>  \n                        </body>\n                        </html>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_Student_Header_Template',@id,NULL,'<!doctype html>\n <html>\n 	<head>\n 		<title></title>\n 		<style>\n 			.container{\n 				padding:60px 50px;\n 			}\n 			p,body{\n 				margin:0;\n 				color:#003366;\n 			}\n 			#outerContainer{\n 				float:left;\n 				width:100%;\n 				box-sizing:border-box;\n 			}\n 			#outerContainer .align1{\n 				float:left;\n 				width:100%;\n 			}\n 			#outerContainer .columnNameContainer{\n 				float:left;\n 				display:inline-block;\n 			}\n 			#outerContainer .heading{\n 				font-weight:bold;\n 				font-size:22px;\n 			}\n 			#outerContainer .headingDiv{\n 				margin-bottom:30px;\n 			}\n 			#outerContainer .subHeadingDiv{\n 				margin-bottom:15px;\n 				float: left;\n 				width: 100%;\n 			}\n 			#outerContainer .userInfo{\n 				margin-bottom:5px;\n 			}\n 			#outerContainer .userInfoDetails{\n 				height:auto;\n 				margin-bottom:15px;\n 			}\n 			#outerContainer .subHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .idHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .horizontalLine{\n 				background-color: #ccc;\n 				width:100%;\n 				height:2px;\n 			}\n 			#outerContainer .horizontalDottedLine{\n 			    border-bottom: dotted;\n 				border-width: 4px; \n 				color:#ccc;\n 			}\n 			#outerContainer .boldStyler{\n 				font-weight:bold;\n 			}\n 			#outerContainer .columnNameContainer2{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 				padding:0px 10px;\n 			}\n 			#outerContainer .details{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 			}\n 			#outerContainer .dataTypeContainer{\n 				width:58%;\n 				height:auto;\n 			}\n 			#outerContainer .userInfoHeading{\n 				margin-bottom:3px;\n 			}\n 			#outerContainer .validvalues{\n 				padding:0px 10px;\n 			}\n 			#outerContainer .italicStyler{\n 				font-style:italic;\n 			}\n 			#outerContainer .valueslist{\n 				list-style-type: none;\n 			}\n 		</style>\n 	</head>\n 	<body>\n 	<div class=\"container\">\n 		<div id=\"outerContainer\">\n 			<div class=\"align1 headingDiv\">\n 				<p class=\"heading\">MAP-Works: Student File Data Definitions<span><img src=\"\" /></span></p>\n 			</div>\n 			<div class=\"subHeadingDiv\">\n 				<p class=\"subHeading\">General Student Information</p>\n 				<div class=\"horizontalLine\"></div>\n 			</div>\n 		</div>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_SubGroup_Footer_Template',@id,NULL,'<p></p>\n	</div>  \n   </body>\n</html>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_SubGroup_Header_Template',@id,NULL,'<!doctype html>\n <html>\n 	<head>\n 		<title></title>\n 		<style>\n 			.container{\n 				padding:60px 50px;\n 			}\n 			p,body{\n 				margin:0;\n 				color:#003366;\n 			}\n 			#outerContainer{\n 				float:left;\n 				width:100%;\n 				box-sizing:border-box;\n 			}\n 			#outerContainer .align1{\n 				float:left;\n 				width:100%;\n 			}\n 			#outerContainer .columnNameContainer{\n 				float:left;\n 				display:inline-block;\n 			}\n 			#outerContainer .heading{\n 				font-weight:bold;\n 				font-size:22px;\n 			}\n 			#outerContainer .headingDiv{\n 				margin-bottom:30px;\n 			}\n 			#outerContainer .subHeadingDiv{\n 				margin-bottom:15px;\n 				float: left;\n 				width: 100%;\n 			}\n 			#outerContainer .userInfo{\n 				margin-bottom:5px;\n 			}\n 			#outerContainer .userInfoDetails{\n 				height:auto;\n 				margin-bottom:15px;\n 			}\n 			#outerContainer .subHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .idHeading{\n 				font-weight:bold;\n 				font-size:18px;\n 			}\n 			#outerContainer .horizontalLine{\n 				background-color: #ccc;\n 				width:100%;\n 				height:2px;\n 			}\n 			#outerContainer .horizontalDottedLine{\n 			    border-bottom: dotted;\n 				border-width: 4px; \n 				color:#ccc;\n 			}\n 			#outerContainer .boldStyler{\n 				font-weight:bold;\n 			}\n 			#outerContainer .columnNameContainer2{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 				padding:0px 10px;\n 			}\n 			#outerContainer .details{\n 				min-width:30%;\n                width:auto;\n 				height:auto;\n 			}\n 			#outerContainer .dataTypeContainer{\n 				width:68%;\n 				height:auto;\n 			}\n 			#outerContainer .userInfoHeading{\n 				margin-bottom:3px;\n 			}\n 			#outerContainer .validvalues{\n 				padding:0px 10px;\n 			}\n 			#outerContainer .italicStyler{\n 				font-style:italic;\n 			}\n 		</style>\n 	</head>\n 	<body>\n 	<div class=\"container\">\n 		<div id=\"outerContainer\">\n 			<div class=\"align1 headingDiv\">\n 				<p class=\"heading\">MAP-Works: Sub-Groups Creation File Definitions<span><img src=\"\" /></span></p>\n 			</div>\n 			<div class=\"subHeadingDiv\">\n 				<p class=\"subHeading\">General Sub-Groups Information</p>\n 				<div class=\"horizontalLine\"></div>\n 			</div>\n 		</div>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_TextType_Body_Template',@id,NULL,' <div id=\"outerContainer\"> <div class=\"align1 subHeadingDiv\"> <div class=\"columnNameContainer details\"><p class=\"idHeading\">$$column_name$$ &nbsp;<span style=\"font-style:italic;color:#666;font-size:16px;\"> $$required$$</span></p></div> <div class=\"columnNameContainer dataTypeContainer\"> <p>$$description$$</p></div> </div> <div class=\"align1 userInfo\"> <p class=\"userInfoHeading\">Upload Information</p> <div class=\"horizontalDottedLine\"></div> </div> <div class=\"align1 userInfoDetails\"> <div class=\"columnNameContainer columnNameContainer2\"><p><span class=\"italicStyler\">Column Name:</span> <span class=\"boldStyler\">$$column_name$$</span></p></div> <div class=\"columnNameContainer dataTypeContainer\"> <p><span class=\"italicStyler\">Data Type:</span>Text</p> <p>(Max Length: $$length$$ characters)</p> </div></div> <div class=\"validvalues align1\"> <p>$$optionalTitle$$</p> <ul class=\"valueslist\"> $$optional$$ </ul> </div> </div>');


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



INSERT INTO datablock_questions (datablock_id, ebi_question_id, survey_id, type) VALUES ('10', '1', '1', 'survey');
INSERT INTO datablock_questions (datablock_id, ebi_question_id, type) VALUES ('11', '2', 'survey');


INSERT INTO org_permissionset_datablock (org_permissionset_id, datablock_id, organization_id, block_type) VALUES ('1', '10', '1', 'survey');
INSERT INTO org_permissionset_datablock (org_permissionset_id, datablock_id, organization_id, block_type) VALUES ('1', '11', '1', 'survey');

INSERT INTO question_type (id) VALUES ('D');
INSERT INTO question_type (id) VALUES ('Q');


INSERT INTO ebi_question (question_type_id, question_category_id, question_key, external_id) VALUES ('D', '1', 'key 8', 'Q108');
INSERT INTO ebi_question (question_type_id, question_category_id, question_key, external_id) VALUES ('D', '1', 'key 9', 'Q109');
INSERT INTO ebi_question (question_type_id, question_category_id, question_key, external_id) VALUES ('Q', '1', 'key 10', 'Q110');
INSERT INTO ebi_question (question_type_id, question_category_id, question_key, external_id) VALUES ('Q', '1', 'key 11', 'Q111');


INSERT INTO ebi_questions_lang (ebi_question_id, lang_id, question_text, question_rpt) VALUES ('8', '1', '<b>In an average week, how many hours do you spend towards your work responsibilities?</b>', 'In an average week, how many hours do you spend towards your work responsibilities?');
INSERT INTO ebi_questions_lang (ebi_question_id, lang_id, question_text, question_rpt) VALUES ('9', '1', '<b>How many courses are you taking?</b>', 'How many courses are you taking?');
INSERT INTO ebi_questions_lang (ebi_question_id, lang_id, question_text, question_rpt) VALUES ('10', '1', 'Attends class?', 'Attends class?');
INSERT INTO ebi_questions_lang (ebi_question_id, lang_id, question_text, question_rpt) VALUES ('11', '1', 'Takes good notes in class?', 'Takes good notes in class?');


INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('8', '(1) Not at all', '1', '1');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('8', '(2)', '2', '2');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('8', '(3)', '3', '3');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('8', '(4) Half the time', '4', '4');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('8', '(5)', '5', '5');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('8', '(6)', '6', '6');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('8', '(7) Always', '7', '7');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('9', '(1) Not at all', '1', '1');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('9', '(2)', '2', '2');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('9', '(3)', '3', '3');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('9', '(4) Half the time', '4', '4');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('9', '(5)', '5', '5');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('9', '(6)', '6', '6');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('9', '(7) Always', '7', '7');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('10', 'None', '1', '1');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('10', '1 to 5 hours', '2', '2');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('10', '6 to 10 hours', '3', '3');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('10', '11 to 15 hours', '4', '4');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('10', '16 to 20 hours', '5', '5');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('10', '21 to 25 hours', '6', '6');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('10', '26 to 30 hours', '7', '7');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('10', '31 to 35 hours', '8', '8');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('10', '36 to 40 hours', '9', '9');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('11', 'No courses', '0', '1');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('11', '1 course', '1', '2');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('11', '2 courses', '2', '3');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('11', '3 courses', '3', '4');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('11', '4 courses', '4', '5');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('11', '5 courses', '5', '6');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('11', 'More than 5 courses', '6', '7');




INSERT INTO datablock_questions (datablock_id, ebi_question_id, type) VALUES ('10', '8', 'survey');
INSERT INTO datablock_questions (datablock_id, ebi_question_id, type) VALUES ('10', '9', 'survey');
INSERT INTO datablock_questions (datablock_id, ebi_question_id, type) VALUES ('10', '10', 'survey');
INSERT INTO datablock_questions (datablock_id, ebi_question_id, type) VALUES ('10', '11', 'survey');



INSERT INTO survey_questions (survey_id, ebi_question_id) VALUES ('1', '8');
INSERT INTO survey_questions (survey_id, ebi_question_id) VALUES ('1', '9');
INSERT INTO survey_questions (survey_id, ebi_question_id) VALUES ('1', '10');
INSERT INTO survey_questions (survey_id, ebi_question_id) VALUES ('1', '11');

INSERT INTO org_person_student_survey_link (org_id, person_id, org_academic_year_id, survey_id, cohort) VALUES ('1', '2', '1', '1', '1');
INSERT INTO org_person_student_survey_link (org_id, person_id, org_academic_year_id, survey_id, cohort) VALUES ('1', '8', '1', '1', '1');


INSERT INTO survey_response (org_id, person_id, survey_id, survey_questions_id, response_type, decimal_value) VALUES ('1', '2', '1', '19', 'decimal', '1.00');
INSERT INTO survey_response (org_id, person_id, survey_id, survey_questions_id, response_type, decimal_value) VALUES ('1', '8', '1', '19', 'decimal', '4.00');
INSERT INTO survey_response (org_id, person_id, survey_id, survey_questions_id, response_type, decimal_value) VALUES ('1', '2', '1', '20', 'decimal', '5.00');
INSERT INTO survey_response (org_id, person_id, survey_id, survey_questions_id, response_type, decimal_value) VALUES ('1', '8', '1', '20', 'decimal', '7.00');
INSERT INTO survey_response (org_id, person_id, survey_id, survey_questions_id, response_type, decimal_value) VALUES ('1', '2', '1', '21', 'decimal', '2.00');
INSERT INTO survey_response (org_id, person_id, survey_id, survey_questions_id, response_type, decimal_value) VALUES ('1', '8', '1', '21', 'decimal', '3.00');
INSERT INTO survey_response (org_id, person_id, survey_id, survey_questions_id, response_type, decimal_value) VALUES ('1', '2', '1', '22', 'decimal', '6.00');
INSERT INTO survey_response (org_id, person_id, survey_id, survey_questions_id, response_type, decimal_value) VALUES ('1', '8', '1', '22', 'decimal', '7.00');

INSERT INTO `factor` (`id`,`created_by`,`modified_by`,`deleted_by`,`survey_id`,`created_at`,`modified_at`,`deleted_at`,`type`) VALUES (1,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL);
INSERT INTO `synapse`.`person_factor_calculated` (`organization_id`, `person_id`, `factor_id`, `survey_id`, `modified_at`, `mean_value`) VALUES ('1', '1', '1', '1', '2015-11-29 06:24:37', '4.6667');
INSERT INTO `synapse`.`person_factor_calculated` (`organization_id`, `person_id`, `factor_id`, `survey_id`, `modified_at`, `mean_value`) VALUES ('1', '2', '1', '1', '2015-11-29 06:24:37', '6.5000');
INSERT INTO `synapse`.`person_factor_calculated` (`organization_id`, `person_id`, `factor_id`, `survey_id`, `modified_at`, `mean_value`) VALUES ('1', '8', '1', '1', '2015-11-29 06:24:37', '4.6667');
INSERT INTO `synapse`.`person_factor_calculated` (`organization_id`, `person_id`, `factor_id`, `survey_id`, `modified_at`, `mean_value`) VALUES ('1', '4', '1', '1', '2015-11-29 06:24:37', '4.6667');


INSERT INTO question_type (id) VALUES ('LA');
INSERT INTO question_type (id) VALUES ('SA');


INSERT INTO ebi_question (question_type_id, question_category_id, question_key, external_id) VALUES ('LA', '1', 'key 12', 'Q112');
INSERT INTO ebi_question (question_type_id, question_category_id, question_key, external_id) VALUES ('SA', '1', 'key 13', 'Q113');


INSERT INTO ebi_questions_lang (ebi_question_id, lang_id, question_text, question_rpt) VALUES ('12', '1', 'Please describe why you plan to leave the institution.', 'Please describe why you plan to leave the institution.');
INSERT INTO ebi_questions_lang (ebi_question_id, question_text, question_rpt) VALUES ('13', 'Please identify the course in which you are having the most difficulty (e.g., English 101).', 'Please identify the course in which you are having the most difficulty (e.g., English 101).');


INSERT INTO datablock_questions (datablock_id, ebi_question_id, type) VALUES ('10', '12', 'survey');
INSERT INTO datablock_questions (datablock_id, ebi_question_id, type) VALUES ('10', '13', 'survey');


INSERT INTO survey_questions (survey_id, ebi_question_id) VALUES ('1', '12');
INSERT INTO survey_questions (survey_id, ebi_question_id) VALUES ('1', '13');


INSERT INTO survey_response (org_id, person_id, survey_id, survey_questions_id, response_type, charmax_value) VALUES ('1', '2', '1', '23', 'charmax', 'test value');
INSERT INTO survey_response (org_id, person_id, survey_id, survey_questions_id, response_type, charmax_value) VALUES ('1', '8', '1', '23', 'charmax', 'looking for higher studeis');
INSERT INTO survey_response (org_id, person_id, survey_id, survey_questions_id, response_type, char_value) VALUES ('1', '2', '1', '24', 'char', 'English');
INSERT INTO survey_response (org_id, person_id, survey_id, survey_questions_id, response_type, char_value) VALUES ('1', '2', '1', '24', 'char', 'Maths');


UPDATE survey_questions SET qnbr='1001' WHERE id='19';
UPDATE survey_questions SET qnbr='1002' WHERE id='20';
UPDATE survey_questions SET qnbr='1003' WHERE id='21';
UPDATE survey_questions SET qnbr='1004' WHERE id='22';
UPDATE survey_questions SET qnbr='1005' WHERE id='23';
UPDATE survey_questions SET qnbr='1006' WHERE id='24';


INSERT INTO question_type (id) VALUES ('NA');

INSERT INTO ebi_question (question_type_id, question_category_id, question_key, external_id) VALUES ('NA', '1', 'key 14', 'Q114');


INSERT INTO ebi_questions_lang (ebi_question_id, lang_id, question_text, question_rpt) VALUES ('14', '1', 'Racial or ethnic organizations?', 'Racial or ethnic organizations?');
UPDATE ebi_questions_lang SET lang_id='1' WHERE id='9';

INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('14', '(1) Not at all', '1', '1');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('14', '(2)', '2', '2');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('14', '(3)', '3', '3');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('14', '(4) Moderately', '4', '4');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('14', '(5)', '5', '5');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('14', '(6)', '6', '6');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('14', '(7) Extremely', '7', '7');
INSERT INTO ebi_question_options (ebi_question_id, option_text, option_value, sequence) VALUES ('14', 'Not applicable', '8', '8');

INSERT INTO datablock_questions (datablock_id, ebi_question_id, type) VALUES ('10', '14', 'survey');

INSERT INTO survey_questions (survey_id, ebi_question_id, qnbr) VALUES ('1', '14', '1007');

INSERT INTO survey_response (org_id, person_id, survey_id, survey_questions_id, response_type, decimal_value) VALUES ('1', '2', '1', '25', 'decimal', '1.00');
INSERT INTO survey_response (org_id, person_id, survey_id, survey_questions_id, response_type, decimal_value) VALUES ('1', '8', '1', '25', 'decimal', '12.00');

/* Deleted the duplicate ClassLevel */
DELETE FROM  ebi_metadata_list_values  WHERE  id ='18';
DELETE FROM  ebi_metadata  WHERE  id ='37';

UPDATE `organization_lang` SET `lang_id`='1' WHERE `organization_id`='-1';

UPDATE contact_info SET primary_email='bipinbihari.pradhan@techmahindra.com' WHERE id='2';

INSERT INTO  office_hours (organization_id, person_id, slot_type, location, slot_start, slot_end, source) VALUES ('1', '4', 'I', 'Location', DATE_ADD(NOW(), INTERVAL 2 HOUR), DATE_ADD(curdate(),INTERVAL 1 DAY), 'S');

INSERT INTO contact_info (id, primary_mobile, primary_email, modified_at) VALUES ('4', '9895472315', 'test@mailinator.com', '2014-10-15 12:34:01');
INSERT INTO contact_info (id, primary_mobile, primary_email, modified_at) VALUES ('5', '8874521036', 'sample@mailinator.com', '2014-10-15 12:34:01');

ALTER TABLE ebi_question_options
ADD COLUMN option_rpt VARCHAR(1000) NULL AFTER sequence,
ADD COLUMN external_id VARCHAR(45) NULL AFTER option_rpt;

INSERT INTO `org_features` (`id`, `organization_id`, `feature_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`, `private`, `connected`, `team`, `default_access`)
VALUES (8,1,8,NULL,'2014-09-12 11:06:36',NULL,'2014-10-13 14:09:27',NULL,NULL,NULL,1,NULL,NULL);

INSERT INTO `org_person_faculty` (`organization_id`, `person_id`, `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`, `deleted_at`)
VALUES
	(1,1,NULL,'2014-12-16 18:16:26',NULL,'2014-12-16 18:16:26',NULL,NULL);
	
update person set external_id = "external_id-8" where id=8;

/**
-- Updated permission set set risk indicator - 1
**/
update `org_permissionset` set `risk_indicator` = 1 where id = 1 and organization_id = 1;

INSERT INTO organization (id, subdomain, status, time_zone, campus_id, tier) VALUES (128, 'pmt', 'A', 'Pacific', 'PMT', '1');
INSERT INTO organization_lang (organization_id, lang_id, organization_name, nick_name) VALUES ('128', '1', 'Primary Tier', 'Primary Tier');


INSERT INTO organization (id, subdomain, status, time_zone, campus_id, tier) VALUES (129, 'st', 'A', 'Pacific', 'ST', '2');
INSERT INTO organization_lang (organization_id, lang_id, organization_name, nick_name) VALUES ('129', '1', 'Secondary Tier', 'Secondary Tier');


INSERT INTO organization (id, subdomain, status, time_zone, campus_id, tier) VALUES (130, 'campus', 'A', 'Pacific', 'CAMPUS', '3');
INSERT INTO organization_lang (organization_id, lang_id, organization_name, nick_name) VALUES ('130', '1', 'Campus', 'Campus');

INSERT INTO person (id,firstname, lastname, external_id, username, password, organization_id) VALUES (240,'Robert', 'Gruz', 'ROBERT', 'robert@mailinator.com', '$2y$13$f6bnaUYhaIO0qzJ0krqrIeUDnxJxWYYEyB3L6qDDK/1ln5CsHKEca', '128');
INSERT INTO org_users (organization_id, person_id) VALUES ('128', '240');


INSERT INTO person (id, firstname, lastname, external_id, username, password, organization_id) VALUES (241,'John', 'Mathew', 'JOHN', 'mathew@mailinator.com', '$2y$13$f6bnaUYhaIO0qzJ0krqrIeUDnxJxWYYEyB3L6qDDK/1ln5CsHKEca', '129');
INSERT INTO org_users (organization_id, person_id) VALUES ('129', '241');

UPDATE organization SET parent_organization_id='128' WHERE id='129';
UPDATE organization SET parent_organization_id='129' WHERE id='130';

INSERT INTO person (id, firstname, lastname, external_id, username, password, organization_id) VALUES (242,'Robin', 'Lenin', 'ROB', 'robin@mailinator.com', '$2y$13$f6bnaUYhaIO0qzJ0krqrIeUDnxJxWYYEyB3L6qDDK/1ln5CsHKEca', '130');

INSERT INTO contact_info (primary_mobile, primary_email, primary_mobile_provider) VALUES ('547895654', 'robert@mailinator.com', '95421364');
INSERT INTO contact_info (primary_mobile, primary_email, primary_mobile_provider) VALUES ('8874521036', 'mathew@mailinator.com', '8874521036');
INSERT INTO contact_info (primary_mobile, primary_email, primary_mobile_provider) VALUES ('8874521036', 'robin@mailinator.com', '8874521036');


INSERT INTO person_contact_info (person_id, contact_id) VALUES ('240', '57');
INSERT INTO person_contact_info (person_id, contact_id) VALUES ('241', '58');
INSERT INTO person_contact_info (person_id, contact_id) VALUES ('242', '59');

INSERT INTO org_person_faculty (organization_id, person_id) VALUES ('128', '240');
INSERT INTO org_person_faculty (organization_id, person_id) VALUES ('129', '241');
INSERT INTO org_person_faculty (organization_id, person_id) VALUES ('130', '242');

INSERT INTO organization_role (role_id, person_id, organization_id) VALUES ('1', '242', '130');

INSERT INTO `org_question_response` ( `org_id`, `person_id`, `survey_id`, `org_question_id`, `response_type`, `char_value`) VALUES ( '1', '6', '1', '1', 'char', 'name');

INSERT INTO organization (id, subdomain, status, time_zone, tier) VALUES ('131', 'solocampus', 'A', 'Pacific', '0');
INSERT INTO organization_lang (organization_id, lang_id, organization_name, nick_name) VALUES ('131', '1', 'Solo Campus', 'Solo Campus');

INSERT INTO organization (id, subdomain, status, parent_organization_id, time_zone, tier) VALUES ('132', 'tier2', 'A', '128', 'Pacific', '2');
INSERT INTO organization_lang (organization_id, lang_id, organization_name, nick_name) VALUES ('132', '1', 'Tier2', 'Tier2');

INSERT INTO organization (id, subdomain, status, parent_organization_id, campus_id, tier) VALUES ('133', 'boston', 'A', '132', 'BOST', '3');
UPDATE organization SET campus_id='TIER' WHERE id='132';
UPDATE organization SET campus_id='SOLO' WHERE id='131';

INSERT INTO organization_lang (organization_id, lang_id, organization_name, nick_name) VALUES ('133', '1', 'Boston', 'Boston');

INSERT INTO person (id, firstname, lastname, external_id, username, organization_id) VALUES ('243', 'Siju', 'Adem', 'SHIJU', 'shiju@mailinator.com', '133');

INSERT INTO org_change_request (id, org_id_source, org_id_destination, person_id_requested_by, person_id_student) VALUES (9,'130', '133', '242', '243');
INSERT INTO org_person_student (id, organization_id, person_id, is_privacy_policy_accepted) VALUES (9,'1', '243', 'n');

INSERT INTO contact_info (id, primary_mobile, primary_email, primary_mobile_provider) VALUES (17, '8874521031', 'shiju@mailinator.com', '8874521031');
INSERT INTO person_contact_info (person_id, contact_id) VALUES ('243', '17');

/*
-- dummy data for academic update in course deletion
*/
INSERT INTO `academic_update` (`created_by`,`modified_by`,`deleted_by`,`org_id`,`org_courses_id`,`academic_update_request_id`,`person_id_faculty_responded`,`person_id_student`,`created_at`,`modified_at`,`deleted_at`,`update_type`,`status`,`request_date`,`due_date`,`update_date`,`failure_risk_level`,`grade`,`absence`,`comment`,`refer_for_assistance`,`send_to_student`,`is_upload`,`is_adhoc`,`final_grade`,`is_submitted_without_change`) VALUES (NULL,NULL,NULL,1,1,NULL,2,6,NULL,NULL,NULL,'adhoc','closed',NULL,NULL,NULL,'low','A',1,NULL,NULL,NULL,NULL,NULL,'A',NULL);

/* insert in AU */
INSERT INTO `academic_update_request` 
(`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`update_type`,`request_date`,`name`,`description`,`status`
,`start_date`,`end_date`,`due_date`,`subject`,`email_optional_msg`,`select_course`,`select_student`,`select_faculty`,`select_group`,`select_metadata`,`select_static_list`) 
VALUES 
(1,NULL,NULL,NULL,1,1,'2015-12-28 07:18:08','2015-12-28 07:18:08',NULL,'targeted','2015-12-28 07:18:08','test cases AU','test cases AU', 'open',NULL,NULL,'2015-12-31 23:59:59','test cases AU','test cases AU','individual','individual','individual','individual',NULL,'individual');

INSERT INTO `academic_update` 
(`id`,`created_by`,`modified_by`,`deleted_by`,`org_id`,`org_courses_id`,`academic_update_request_id`,`person_id_faculty_responded`,`person_id_student`,`created_at`,`modified_at`,`deleted_at`,`update_type`,`status`,`request_date`,`due_date`,`update_date`,`failure_risk_level`,`grade`,`absence`,`comment`,`refer_for_assistance`,`send_to_student`,`is_upload`,`is_adhoc`,`final_grade`,`is_submitted_without_change`)
 VALUES 
(1,NULL,NULL,NULL,1,1,1,1,6,'2015-12-28 07:18:08','2015-12-28 07:33:01',NULL,'targeted','open','2015-12-28 07:18:08','2015-12-31 23:59:59','2015-12-28 07:33:01','low','A',NULL,'B',NULL,1,NULL,NULL,NULL,0);

INSERT INTO `academic_update_request_faculty` (`id`,`person_id`,`org_id`,`academic_update_request_id`) VALUES (1,1,1,1);

INSERT INTO `academic_update_request_course` (`id`,`org_courses_id`,`org_id`,`academic_update_request_id`) VALUES (1,1,1,1);

INSERT INTO `academic_update_request_group` (`id`,`org_group_id`,`org_id`,`academic_update_request_id`) VALUES (1,1,1,1);

INSERT INTO `academic_update_request_student` (`id`,`person_id`,`org_id`,`academic_update_request_id`) VALUES (1,6,1,1);

INSERT INTO `academic_update_assigned_faculty` (`id`,`person_id_faculty_assigned`,`org_id`,`academic_update_id`) VALUES (1,1,1,1);

/* create to delete AU */
insert into academic_update (id,org_id,org_courses_id) values ('2','1','1');
insert into academic_update_request (id,org_id,person_id) values ('2','1','1');

/* insert for help bundle */
insert into org_documents (id, org_id, title, description, `type`)
values (1, 1,'Title document : 12', 'Description document : 266', 'file');

/*
	Test data for IRR report change academic year as per curent year
*/
update org_person_student_survey_link set org_academic_year_id = (select id from org_academic_year oay where oay.organization_id = 1 and oay.deleted_at is null and date(now()) between start_date and end_date and year_id = '201516' and deleted_at is null), survey_completion_status = 'CompletedAll' where org_id = 1 and person_id = 6 and survey_id = 1 and cohort = 1;

/*
-- test data for AU report
*/
INSERT INTO `academic_update` (`created_by`,`modified_by`,`deleted_by`,`org_id`,`org_courses_id`,`academic_update_request_id`,`person_id_faculty_responded`,`person_id_student`,`created_at`,`modified_at`,`deleted_at`,`update_type`,`status`,`request_date`,`due_date`,`update_date`,`failure_risk_level`,`grade`,`absence`,`comment`,`refer_for_assistance`,`send_to_student`,`is_upload`,`is_adhoc`,`final_grade`,`is_submitted_without_change`) VALUES (NULL,NULL,NULL,1,3,NULL,2,6,NULL,NULL,NULL,'adhoc','closed',NULL,NULL,now(),'high','B',2,NULL,NULL,NULL,NULL,NULL,'C',NULL);
/*
-- Query: select * from referral_routing_rules where organization_id = 1
-- Date: 2016-01-06 12:20
*/
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,19,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,20,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,21,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,22,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,23,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,24,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,25,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,26,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,27,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,28,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,29,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,30,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,31,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,32,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,33,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,34,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,35,1,NULL,'2016-01-06 06:47:07','2016-01-06 06:47:07',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,36,1,NULL,'2016-01-06 06:47:08','2016-01-06 06:47:08',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,37,1,NULL,'2016-01-06 06:47:08','2016-01-06 06:47:08',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,38,1,NULL,'2016-01-06 06:47:08','2016-01-06 06:47:08',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,39,1,NULL,'2016-01-06 06:47:08','2016-01-06 06:47:08',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,40,1,NULL,'2016-01-06 06:47:08','2016-01-06 06:47:08',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,41,1,NULL,'2016-01-06 06:47:08','2016-01-06 06:47:08',NULL,1,NULL);
INSERT INTO `referral_routing_rules` (`created_by`,`modified_by`,`deleted_by`,`activity_category_id`,`organization_id`,`person_id`,`created_at`,`modified_at`,`deleted_at`,`is_primary_coordinator`,`is_primary_campus_connection`) VALUES (NULL,NULL,NULL,42,1,NULL,'2016-01-06 06:47:08','2016-01-06 06:47:08',NULL,1,NULL);