CREATE DATABASE  IF NOT EXISTS `synapse_test` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `synapse_test`;
-- MySQL dump 10.13  Distrib 5.6.13, for Win32 (x86)
--
-- Host: localhost    Database: synapse_test
-- ------------------------------------------------------
-- Server version	5.6.23-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academic_update`
--

DROP TABLE IF EXISTS `academic_update`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_update` (
  `org_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_courses_id` int(11) NOT NULL,
  `person_id_student` int(11) NOT NULL,
  `academic_update_request_id` int(11) DEFAULT NULL,
  `update_type` enum('bulk','targeted','adhoc','ftp') DEFAULT NULL,
  `status` enum('open','closed','cancelled','saved') DEFAULT NULL,
  `person_id_faculty_responded` int(11) DEFAULT NULL,
  `request_date` timestamp NULL DEFAULT NULL,
  `due_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL,
  `failure_risk_level` varchar(10) DEFAULT NULL,
  `grade` varchar(10) DEFAULT NULL,
  `absence` int(11) DEFAULT NULL,
  `comment` varchar(300) DEFAULT NULL,
  `refer_for_assistance` tinyint(1) DEFAULT NULL,
  `send_to_student` tinyint(1) DEFAULT NULL,
  `is_upload` tinyint(1) DEFAULT NULL,
  `is_adhoc` tinyint(1) DEFAULT NULL,
  `final_grade` varchar(10) DEFAULT NULL,
  `is_submitted_without_change` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_academic_update_organization1_idx` (`org_id`),
  KEY `fk_academic_update_org_courses1_idx` (`org_courses_id`),
  KEY `fk_academic_update_academic_update_request1_idx` (`academic_update_request_id`),
  KEY `fk_academic_update_person2_idx` (`person_id_faculty_responded`),
  KEY `fk_academic_update_person3_idx` (`person_id_student`),
  CONSTRAINT `fk_academic_update_academic_update_request1` FOREIGN KEY (`academic_update_request_id`) REFERENCES `academic_update_request` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_org_courses1` FOREIGN KEY (`org_courses_id`) REFERENCES `org_courses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_person2` FOREIGN KEY (`person_id_faculty_responded`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_person3` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_update`
--

LOCK TABLES `academic_update` WRITE;
/*!40000 ALTER TABLE `academic_update` DISABLE KEYS */;
INSERT INTO `academic_update` VALUES (1,1,1,1,1,NULL,NULL,1,NULL,NULL,NULL,'High','F',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1,2,1,1,1,NULL,NULL,1,NULL,NULL,NULL,'High','D',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1,3,1,3,1,NULL,NULL,1,NULL,NULL,NULL,'High','F+',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1,4,1,2,1,NULL,NULL,1,NULL,NULL,NULL,'Low','A',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `academic_update` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_update_assigned_faculty`
--

DROP TABLE IF EXISTS `academic_update_assigned_faculty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_update_assigned_faculty` (
  `org_id` int(11) NOT NULL,
  `academic_update_id` int(11) NOT NULL,
  `person_id_faculty_assigned` int(11) NOT NULL,
  PRIMARY KEY (`person_id_faculty_assigned`,`org_id`,`academic_update_id`),
  KEY `fk_academic_update_assigned_faculty_person1_idx` (`person_id_faculty_assigned`),
  KEY `fk_academic_update_assigned_faculty_organization1_idx` (`org_id`),
  KEY `fk_academic_update_assigned_faculty_academic_update1_idx` (`academic_update_id`),
  CONSTRAINT `fk_academic_update_assigned_faculty_academic_update1` FOREIGN KEY (`academic_update_id`) REFERENCES `academic_update` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_assigned_faculty_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_assigned_faculty_person1` FOREIGN KEY (`person_id_faculty_assigned`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_update_assigned_faculty`
--

LOCK TABLES `academic_update_assigned_faculty` WRITE;
/*!40000 ALTER TABLE `academic_update_assigned_faculty` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_update_assigned_faculty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_update_request`
--

DROP TABLE IF EXISTS `academic_update_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_update_request` (
  `org_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `update_type` enum('bulk','targeted') DEFAULT NULL,
  `request_date` timestamp NULL DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` varchar(4000) DEFAULT NULL,
  `status` enum('open','closed','cancelled') DEFAULT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `due_date` timestamp NULL DEFAULT NULL,
  `subject` varchar(400) DEFAULT NULL,
  `email_optional_msg` varchar(2000) DEFAULT NULL,
  `select_course` enum('all,','individual','none') DEFAULT NULL,
  `select_student` enum('all,','individual','none') DEFAULT NULL,
  `select_faculty` enum('all,','individual','none') DEFAULT NULL,
  `select_group` enum('all,','individual','none') DEFAULT NULL,
  `select_metadata` enum('all,','individual','none') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_academic_update_request_organization1_idx` (`org_id`),
  KEY `fk_academic_update_request_person1_idx` (`person_id`),
  CONSTRAINT `fk_academic_update_request_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_request_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_update_request`
--

LOCK TABLES `academic_update_request` WRITE;
/*!40000 ALTER TABLE `academic_update_request` DISABLE KEYS */;
INSERT INTO `academic_update_request` VALUES (1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `academic_update_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_update_request_course`
--

DROP TABLE IF EXISTS `academic_update_request_course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_update_request_course` (
  `org_id` int(11) NOT NULL,
  `academic_update_request_id` int(11) NOT NULL,
  `org_courses_id` int(11) NOT NULL,
  PRIMARY KEY (`org_id`,`academic_update_request_id`,`org_courses_id`),
  KEY `fk_academic_update_request_course_org_courses1_idx` (`org_courses_id`),
  KEY `fk_academic_update_request_course_organization1_idx` (`org_id`),
  KEY `fk_academic_update_request_course_academic_update_request1_idx` (`academic_update_request_id`),
  CONSTRAINT `fk_academic_update_request_course_academic_update_request1` FOREIGN KEY (`academic_update_request_id`) REFERENCES `academic_update_request` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_request_course_org_courses1` FOREIGN KEY (`org_courses_id`) REFERENCES `org_courses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_request_course_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_update_request_course`
--

LOCK TABLES `academic_update_request_course` WRITE;
/*!40000 ALTER TABLE `academic_update_request_course` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_update_request_course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_update_request_faculty`
--

DROP TABLE IF EXISTS `academic_update_request_faculty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_update_request_faculty` (
  `org_id` int(11) NOT NULL,
  `academic_update_request_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  PRIMARY KEY (`org_id`,`academic_update_request_id`,`person_id`),
  KEY `fk_academic_update_request_faculty_person1_idx` (`person_id`),
  KEY `fk_academic_update_request_faculty_organization1_idx` (`org_id`),
  KEY `fk_academic_update_request_faculty_academic_update_request1_idx` (`academic_update_request_id`),
  CONSTRAINT `fk_academic_update_request_faculty_academic_update_request1` FOREIGN KEY (`academic_update_request_id`) REFERENCES `academic_update_request` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_request_faculty_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_request_faculty_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_update_request_faculty`
--

LOCK TABLES `academic_update_request_faculty` WRITE;
/*!40000 ALTER TABLE `academic_update_request_faculty` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_update_request_faculty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_update_request_group`
--

DROP TABLE IF EXISTS `academic_update_request_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_update_request_group` (
  `org_id` int(11) NOT NULL,
  `academic_update_request_id` int(11) NOT NULL,
  `org_group_id` int(11) NOT NULL,
  PRIMARY KEY (`org_id`,`academic_update_request_id`,`org_group_id`),
  KEY `fk_academic_update_request_group_org_group1_idx` (`org_group_id`),
  KEY `fk_academic_update_request_group_organization1_idx` (`org_id`),
  KEY `fk_academic_update_request_group_academic_update_request1_idx` (`academic_update_request_id`),
  CONSTRAINT `fk_academic_update_request_group_academic_update_request1` FOREIGN KEY (`academic_update_request_id`) REFERENCES `academic_update_request` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_request_group_org_group1` FOREIGN KEY (`org_group_id`) REFERENCES `org_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_request_group_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_update_request_group`
--

LOCK TABLES `academic_update_request_group` WRITE;
/*!40000 ALTER TABLE `academic_update_request_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_update_request_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_update_request_metadata`
--

DROP TABLE IF EXISTS `academic_update_request_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_update_request_metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_id` int(11) NOT NULL,
  `academic_update_request_id` int(11) NOT NULL,
  `ebi_metadata_id` int(11) NOT NULL,
  `org_metadata_id` int(11) NOT NULL,
  `search_value` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_academic_update_request_metadata_ebi_metadata1_idx` (`ebi_metadata_id`),
  KEY `fk_academic_update_request_metadata_org_metadata1_idx` (`org_metadata_id`),
  KEY `fk_academic_update_request_metadata_organization1_idx` (`org_id`),
  KEY `fk_academic_update_request_metadata_academic_update_request_idx` (`academic_update_request_id`),
  CONSTRAINT `fk_academic_update_request_metadata_academic_update_request1` FOREIGN KEY (`academic_update_request_id`) REFERENCES `academic_update_request` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_request_metadata_ebi_metadata1` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_request_metadata_org_metadata1` FOREIGN KEY (`org_metadata_id`) REFERENCES `org_metadata` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_request_metadata_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_update_request_metadata`
--

LOCK TABLES `academic_update_request_metadata` WRITE;
/*!40000 ALTER TABLE `academic_update_request_metadata` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_update_request_metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_update_request_student`
--

DROP TABLE IF EXISTS `academic_update_request_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_update_request_student` (
  `org_id` int(11) NOT NULL,
  `academic_update_request_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  PRIMARY KEY (`org_id`,`academic_update_request_id`,`person_id`),
  KEY `fk_academic_update_request_student_person1_idx` (`person_id`),
  KEY `fk_academic_update_request_student_organization1_idx` (`org_id`),
  KEY `fk_academic_update_request_student_academic_update_request1_idx` (`academic_update_request_id`),
  CONSTRAINT `fk_academic_update_request_student_academic_update_request1` FOREIGN KEY (`academic_update_request_id`) REFERENCES `academic_update_request` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_request_student_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academic_update_request_student_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_update_request_student`
--

LOCK TABLES `academic_update_request_student` WRITE;
/*!40000 ALTER TABLE `academic_update_request_student` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_update_request_student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `access_log`
--

DROP TABLE IF EXISTS `access_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `event` varchar(45) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `source_ip` varchar(20) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `user_token` varchar(255) DEFAULT NULL,
  `api_token` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_access_log_person1_idx` (`person_id`),
  KEY `fk_access_log_organization1_idx` (`organization_id`),
  CONSTRAINT `fk_access_log_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_access_log_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_log`
--

LOCK TABLES `access_log` WRITE;
/*!40000 ALTER TABLE `access_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `access_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accesstoken`
--

DROP TABLE IF EXISTS `accesstoken`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accesstoken` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expires_at` int(11) DEFAULT NULL,
  `scope` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_B39617F55F37A13B` (`token`),
  KEY `IDX_B39617F519EB6921` (`client_id`),
  KEY `IDX_B39617F5A76ED395` (`user_id`),
  CONSTRAINT `FK_B39617F519EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  CONSTRAINT `FK_B39617F5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accesstoken`
--

LOCK TABLES `accesstoken` WRITE;
/*!40000 ALTER TABLE `accesstoken` DISABLE KEYS */;
/*!40000 ALTER TABLE `accesstoken` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_category`
--

DROP TABLE IF EXISTS `activity_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `short_name` varchar(45) DEFAULT NULL,
  `is_active` binary(1) DEFAULT NULL,
  `display_seq` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `parent_activity_category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_activity_category_activity_category1_idx` (`parent_activity_category_id`),
  CONSTRAINT `fk_activity_category_activity_category1` FOREIGN KEY (`parent_activity_category_id`) REFERENCES `activity_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_category`
--

LOCK TABLES `activity_category` WRITE;
/*!40000 ALTER TABLE `activity_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_category_lang`
--

DROP TABLE IF EXISTS `activity_category_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_category_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_category_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_activity_reference_lang_activity_reference1_idx` (`activity_category_id`),
  KEY `fk_activity_reference_lang_language_master1_idx` (`language_id`),
  CONSTRAINT `fk_activity_reference_lang_activity_reference1` FOREIGN KEY (`activity_category_id`) REFERENCES `activity_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_reference_lang_language_master1` FOREIGN KEY (`language_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_category_lang`
--

LOCK TABLES `activity_category_lang` WRITE;
/*!40000 ALTER TABLE `activity_category_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_category_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id_faculty` int(11) NOT NULL,
  `person_id_student` int(11) DEFAULT NULL,
  `activity_type` varchar(1) DEFAULT NULL,
  `activity_date` datetime DEFAULT NULL,
  `referrals_id` int(11) DEFAULT NULL,
  `appointments_id` int(11) DEFAULT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `note_id` int(11) DEFAULT NULL,
  `contacts_id` int(11) DEFAULT NULL,
  `academic_update_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_activity_referrals1_idx` (`referrals_id`),
  KEY `fk_activity_appointments1_idx` (`appointments_id`),
  KEY `fk_activity_note1_idx` (`note_id`),
  KEY `fk_activity_contacts1_idx` (`contacts_id`),
  KEY `fk_activity_organization1_idx` (`organization_id`),
  KEY `fk_activity_person1_idx` (`person_id_faculty`),
  KEY `fk_activity_log_person1_idx` (`person_id_student`),
  KEY `fk_activity_log_academic_update1_idx` (`academic_update_id`),
  CONSTRAINT `fk_activity_appointments1` FOREIGN KEY (`appointments_id`) REFERENCES `appointments` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_contacts1` FOREIGN KEY (`contacts_id`) REFERENCES `contacts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_log_academic_update1` FOREIGN KEY (`academic_update_id`) REFERENCES `academic_update` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_log_person1` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_note1` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_person1` FOREIGN KEY (`person_id_faculty`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_referrals1` FOREIGN KEY (`referrals_id`) REFERENCES `referrals` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alert_notifications`
--

DROP TABLE IF EXISTS `alert_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alert_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `referrals_id` int(11) NOT NULL,
  `appointments_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `event` varchar(45) DEFAULT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `is_viewed` tinyint(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `org_search_id` int(11) DEFAULT NULL,
  `academic_update_org_id` int(11) DEFAULT NULL,
  `academic_update_id` int(11) DEFAULT NULL,
  `org_course_upload_file` varchar(100) DEFAULT NULL,
  `org_static_list_id` int(11) DEFAULT NULL,
  `org_announcements_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_alert_notifications_referrals1_idx` (`referrals_id`),
  KEY `fk_alert_notifications_appointments1_idx` (`appointments_id`),
  KEY `fk_alert_notifications_person1_idx` (`person_id`),
  KEY `fk_alert_notifications_organization1_idx` (`organization_id`),
  KEY `fk_alert_notifications_org_search1_idx` (`org_search_id`),
  KEY `fk_alert_notifications_academic_update1_idx` (`academic_update_id`),
  KEY `fk_alert_notifications_org_static_list1_idx` (`org_static_list_id`),
  KEY `fk_alert_notifications_org_announcements1_idx` (`org_announcements_id`),
  CONSTRAINT `fk_alert_notifications_academic_update1` FOREIGN KEY (`academic_update_id`) REFERENCES `academic_update` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_alert_notifications_appointments1` FOREIGN KEY (`appointments_id`) REFERENCES `appointments` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_alert_notifications_org_announcements1` FOREIGN KEY (`org_announcements_id`) REFERENCES `org_announcements` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_alert_notifications_org_search1` FOREIGN KEY (`org_search_id`) REFERENCES `org_search` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_alert_notifications_org_static_list1` FOREIGN KEY (`org_static_list_id`) REFERENCES `org_static_list` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_alert_notifications_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_alert_notifications_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_alert_notifications_referrals1` FOREIGN KEY (`referrals_id`) REFERENCES `referrals` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alert_notifications`
--

LOCK TABLES `alert_notifications` WRITE;
/*!40000 ALTER TABLE `alert_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `alert_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appointment_connection_info`
--

DROP TABLE IF EXISTS `appointment_connection_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointment_connection_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `type` varchar(1) DEFAULT NULL,
  `server_url` varchar(255) DEFAULT NULL,
  `server_port` varchar(45) DEFAULT NULL,
  `auth_key` varchar(255) DEFAULT NULL,
  `parameter1` varchar(1000) DEFAULT NULL,
  `parameter2` varchar(1000) DEFAULT NULL,
  `parameter3` varchar(1000) DEFAULT NULL,
  `parameter4` varchar(1000) DEFAULT NULL,
  `parameter5` varchar(1000) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_appointment_connection_info_organization1_idx` (`organization_id`),
  CONSTRAINT `fk_appointment_connection_info_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointment_connection_info`
--

LOCK TABLES `appointment_connection_info` WRITE;
/*!40000 ALTER TABLE `appointment_connection_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `appointment_connection_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appointment_recepient_and_status`
--

DROP TABLE IF EXISTS `appointment_recepient_and_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointment_recepient_and_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `appointments_id` int(11) NOT NULL,
  `person_id_faculty` int(11) DEFAULT NULL,
  `person_id_student` int(11) DEFAULT NULL,
  `has_attended` binary(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_appointment_recepient_organization1_idx` (`organization_id`),
  KEY `fk_appointment_recepient_appointments1_idx` (`appointments_id`),
  KEY `fk_appointment_recepient_person1_idx` (`person_id_faculty`),
  KEY `fk_appointment_recepient_and_status_person1_idx` (`person_id_student`),
  CONSTRAINT `fk_appointment_recepient_and_status_person1` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appointment_recepient_appointments1` FOREIGN KEY (`appointments_id`) REFERENCES `appointments` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appointment_recepient_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appointment_recepient_person1` FOREIGN KEY (`person_id_faculty`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointment_recepient_and_status`
--

LOCK TABLES `appointment_recepient_and_status` WRITE;
/*!40000 ALTER TABLE `appointment_recepient_and_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `appointment_recepient_and_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `type` varchar(1) DEFAULT NULL,
  `location` varchar(45) DEFAULT NULL,
  `title` varchar(1000) DEFAULT NULL,
  `description` varchar(5000) DEFAULT NULL,
  `start_date_time` datetime DEFAULT NULL,
  `end_date_time` datetime DEFAULT NULL,
  `attendees` text,
  `occurrence_id` varchar(255) DEFAULT NULL,
  `master_occurrence_id` varchar(255) DEFAULT NULL,
  `match_status` binary(1) DEFAULT NULL,
  `last_synced` datetime DEFAULT NULL,
  `activity_category_id` int(11) NOT NULL,
  `is_free_standing` tinyint(1) DEFAULT NULL,
  `person_id_proxy` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_appointments_organization1_idx` (`organization_id`),
  KEY `fk_appointments_activity_category1_idx` (`activity_category_id`),
  KEY `fk_appointments_person1_idx` (`person_id_proxy`),
  KEY `fk_appointments_person2_idx` (`person_id`),
  CONSTRAINT `fk_appointments_activity_category1` FOREIGN KEY (`activity_category_id`) REFERENCES `activity_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appointments_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appointments_person1` FOREIGN KEY (`person_id_proxy`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appointments_person2` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `authcode`
--

DROP TABLE IF EXISTS `authcode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `redirect_uri` longtext COLLATE utf8_unicode_ci NOT NULL,
  `expires_at` int(11) DEFAULT NULL,
  `scope` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_F1D7D1775F37A13B` (`token`),
  KEY `IDX_F1D7D17719EB6921` (`client_id`),
  KEY `IDX_F1D7D177A76ED395` (`user_id`),
  CONSTRAINT `FK_F1D7D17719EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  CONSTRAINT `FK_F1D7D177A76ED395` FOREIGN KEY (`user_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authcode`
--

LOCK TABLES `authcode` WRITE;
/*!40000 ALTER TABLE `authcode` DISABLE KEYS */;
/*!40000 ALTER TABLE `authcode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_sharing`
--

DROP TABLE IF EXISTS `calendar_sharing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_sharing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id_sharedby` int(11) NOT NULL,
  `person_id_sharedto` int(11) NOT NULL,
  `is_selected` tinyint(1) DEFAULT NULL,
  `shared_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_calendar_sharing_person1_idx` (`person_id_sharedby`),
  KEY `fk_calendar_sharing_person2_idx` (`person_id_sharedto`),
  KEY `fk_calendar_sharing_organization1_idx` (`organization_id`),
  CONSTRAINT `fk_calendar_sharing_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_calendar_sharing_person1` FOREIGN KEY (`person_id_sharedby`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_calendar_sharing_person2` FOREIGN KEY (`person_id_sharedto`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendar_sharing`
--

LOCK TABLES `calendar_sharing` WRITE;
/*!40000 ALTER TABLE `calendar_sharing` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar_sharing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `redirect_uris` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `secret` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `allowed_grant_types` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client`
--

LOCK TABLES `client` WRITE;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
/*!40000 ALTER TABLE `client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_info`
--

DROP TABLE IF EXISTS `contact_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address_1` varchar(200) DEFAULT NULL,
  `address_2` varchar(200) DEFAULT NULL,
  `city` varchar(200) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `primary_mobile` varchar(15) DEFAULT NULL,
  `alternate_mobile` varchar(15) DEFAULT NULL,
  `home_phone` varchar(15) DEFAULT NULL,
  `office_phone` varchar(15) DEFAULT NULL,
  `primary_email` varchar(200) DEFAULT NULL,
  `alternate_email` varchar(200) DEFAULT NULL,
  `primary_mobile_provider` varchar(45) DEFAULT NULL,
  `alternate_mobile_provider` varchar(45) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_info`
--

LOCK TABLES `contact_info` WRITE;
/*!40000 ALTER TABLE `contact_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_types`
--

DROP TABLE IF EXISTS `contact_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` binary(1) DEFAULT NULL,
  `display_seq` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `parent_contact_types_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_contact_types_contact_types1_idx` (`parent_contact_types_id`),
  CONSTRAINT `fk_contact_types_contact_types1` FOREIGN KEY (`parent_contact_types_id`) REFERENCES `contact_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_types`
--

LOCK TABLES `contact_types` WRITE;
/*!40000 ALTER TABLE `contact_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_types_lang`
--

DROP TABLE IF EXISTS `contact_types_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_types_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_types_id` int(11) NOT NULL,
  `language_master_id` int(11) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_contact_types_lang_contact_types1_idx` (`contact_types_id`),
  KEY `fk_contact_types_lang_language_master1_idx` (`language_master_id`),
  CONSTRAINT `fk_contact_types_lang_contact_types1` FOREIGN KEY (`contact_types_id`) REFERENCES `contact_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_types_lang_language_master1` FOREIGN KEY (`language_master_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_types_lang`
--

LOCK TABLES `contact_types_lang` WRITE;
/*!40000 ALTER TABLE `contact_types_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_types_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id_student` int(11) NOT NULL,
  `person_id_faculty` int(11) NOT NULL,
  `contact_types_id` int(11) NOT NULL,
  `activity_category_id` int(11) NOT NULL,
  `contact_date` datetime DEFAULT NULL,
  `note` text,
  `is_discussed` binary(1) DEFAULT NULL,
  `is_high_priority` binary(1) DEFAULT NULL,
  `is_reveal` binary(1) DEFAULT NULL,
  `is_leaving` binary(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `access_private` tinyint(4) DEFAULT NULL,
  `access_public` tinyint(4) DEFAULT NULL,
  `access_team` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_contacts_contact_types1_idx` (`contact_types_id`),
  KEY `fk_contacts_organization1_idx` (`organization_id`),
  KEY `fk_contacts_person1_idx` (`person_id_student`),
  KEY `fk_contacts_person2_idx` (`person_id_faculty`),
  KEY `fk_contacts_activity_category1_idx` (`activity_category_id`),
  CONSTRAINT `fk_contacts_activity_category1` FOREIGN KEY (`activity_category_id`) REFERENCES `activity_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contacts_contact_types1` FOREIGN KEY (`contact_types_id`) REFERENCES `contact_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contacts_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contacts_person1` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contacts_person2` FOREIGN KEY (`person_id_faculty`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts_teams`
--

DROP TABLE IF EXISTS `contacts_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contacts_id` int(11) NOT NULL,
  `teams_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_contacts_teams_contacts1_idx` (`contacts_id`),
  KEY `fk_contacts_teams_Teams1_idx` (`teams_id`),
  CONSTRAINT `fk_contacts_teams_Teams1` FOREIGN KEY (`teams_id`) REFERENCES `teams` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contacts_teams_contacts1` FOREIGN KEY (`contacts_id`) REFERENCES `contacts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts_teams`
--

LOCK TABLES `contacts_teams` WRITE;
/*!40000 ALTER TABLE `contacts_teams` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts_teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datablock_master`
--

DROP TABLE IF EXISTS `datablock_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datablock_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block_type` varchar(10) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `datablock_ui_id` int(11) NOT NULL,
  `image_filename` varchar(200) DEFAULT NULL COMMENT 'image_filename: This contains only the image file name and not the full path. ',
  PRIMARY KEY (`id`),
  KEY `fk_datablock_master_datablock_ui1_idx` (`datablock_ui_id`),
  CONSTRAINT `fk_datablock_master_datablock_ui1` FOREIGN KEY (`datablock_ui_id`) REFERENCES `datablock_ui` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datablock_master`
--

LOCK TABLES `datablock_master` WRITE;
/*!40000 ALTER TABLE `datablock_master` DISABLE KEYS */;
/*!40000 ALTER TABLE `datablock_master` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datablock_master_lang`
--

DROP TABLE IF EXISTS `datablock_master_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datablock_master_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datablock_id` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `datablock_desc` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_datablockname` (`datablock_desc`),
  KEY `fk_datablocklang_datablockid_idx` (`datablock_id`),
  KEY `fk_datablocklang_langid_idx` (`lang_id`),
  CONSTRAINT `fk_datablocklang_datablockid` FOREIGN KEY (`datablock_id`) REFERENCES `datablock_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_datablocklang_langid` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datablock_master_lang`
--

LOCK TABLES `datablock_master_lang` WRITE;
/*!40000 ALTER TABLE `datablock_master_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `datablock_master_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datablock_metadata`
--

DROP TABLE IF EXISTS `datablock_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datablock_metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datablock_id` int(11) NOT NULL,
  `ebi_metadata_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_datablock_metadata_datablock_master1_idx` (`datablock_id`),
  KEY `fk_datablock_metadata_ebi_metadata1_idx` (`ebi_metadata_id`),
  CONSTRAINT `fk_datablock_metadata_datablock_master1` FOREIGN KEY (`datablock_id`) REFERENCES `datablock_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_datablock_metadata_ebi_metadata1` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datablock_metadata`
--

LOCK TABLES `datablock_metadata` WRITE;
/*!40000 ALTER TABLE `datablock_metadata` DISABLE KEYS */;
/*!40000 ALTER TABLE `datablock_metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datablock_questions`
--

DROP TABLE IF EXISTS `datablock_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datablock_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datablock_id` int(11) NOT NULL,
  `type` enum('bank','survey','factor') DEFAULT NULL,
  `ebi_question_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `survey_questions_id` int(11) DEFAULT NULL,
  `factor_id` int(11) DEFAULT NULL,
  `red_low` decimal(6,3) DEFAULT NULL,
  `red_high` decimal(6,3) DEFAULT NULL,
  `yellow_low` decimal(6,3) DEFAULT NULL,
  `yellow_high` decimal(6,3) DEFAULT NULL,
  `green_low` decimal(6,3) DEFAULT NULL,
  `green_high` decimal(6,3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_datablock_questions_datablock_master1_idx` (`datablock_id`),
  KEY `fk_datablock_questions_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_datablock_questions_survey1_idx` (`survey_id`),
  KEY `fk_datablock_questions_factor1_idx` (`factor_id`),
  KEY `fk_datablock_questions_survey_questions1_idx` (`survey_questions_id`),
  CONSTRAINT `fk_datablock_questions_datablock_master1` FOREIGN KEY (`datablock_id`) REFERENCES `datablock_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_datablock_questions_ebi_question1` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_datablock_questions_factor1` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_datablock_questions_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_datablock_questions_survey_questions1` FOREIGN KEY (`survey_questions_id`) REFERENCES `survey_questions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datablock_questions`
--

LOCK TABLES `datablock_questions` WRITE;
/*!40000 ALTER TABLE `datablock_questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `datablock_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datablock_ui`
--

DROP TABLE IF EXISTS `datablock_ui`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datablock_ui` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(45) DEFAULT NULL,
  `ui_feature_name` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datablock_ui`
--

LOCK TABLES `datablock_ui` WRITE;
/*!40000 ALTER TABLE `datablock_ui` DISABLE KEYS */;
/*!40000 ALTER TABLE `datablock_ui` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_config`
--

DROP TABLE IF EXISTS `ebi_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) DEFAULT NULL,
  `value` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_config`
--

LOCK TABLES `ebi_config` WRITE;
/*!40000 ALTER TABLE `ebi_config` DISABLE KEYS */;
INSERT INTO `ebi_config` VALUES (1,'Coordinator_First_Password_Expiry_Hrs','0'),(2,'Coordinator_Support_Helpdesk_Email_Address','support@map-works.com'),(3,'Staff_Support_Helpdesk_Email_Address','support@map-works.com'),(4,'Coordinator_Activation_URL_Prefix','http://synapse-dev.mnv-tech.com/#/restPassword/'),(5,'Staff_Activation_URL_Prefix','http://synapse-dev.mnv-tech.com/#/restPassword/'),(6,'Staff_First_Password_Expiry_Hrs','24'),(7,'Coordinator_Reset_Password_Expiry_Hrs','48'),(8,'Staff_Reset_Password_Expiry_Hrs','48'),(9,'Student_Reset_Password_Expiry_Hrs','48');
/*!40000 ALTER TABLE `ebi_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_metadata`
--

DROP TABLE IF EXISTS `ebi_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meta_key` varchar(45) DEFAULT NULL,
  `definition_type` varchar(1) DEFAULT 's',
  `metadata_type` varchar(1) DEFAULT NULL,
  `no_of_decimals` int(11) DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT NULL,
  `min_range` decimal(15,4) DEFAULT NULL,
  `max_range` decimal(15,4) DEFAULT NULL,
  `entity` varchar(10) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `meta_group` varchar(2) DEFAULT NULL,
  `scope` varchar(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_metadata`
--

LOCK TABLES `ebi_metadata` WRITE;
/*!40000 ALTER TABLE `ebi_metadata` DISABLE KEYS */;
INSERT INTO `ebi_metadata` VALUES (1,'ACTComposite','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'ACTEnglish','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'ACTMath','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'ACTReading','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'ACTScience','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,'BirthYear','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'EntranceScore','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,'Gender','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,'HSGPA','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,'HSGradYear','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,'Major','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,'Race','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,'SATMath','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,'SATVerbal','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,'SATWriting','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,'SpringPersistence','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,'StudentNumHS','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,'StudentResidence','s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `ebi_metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_metadata_lang`
--

DROP TABLE IF EXISTS `ebi_metadata_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_metadata_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebi_metadata_id` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `meta_name` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `metadataid_idx` (`ebi_metadata_id`) USING BTREE,
  KEY `langid_idx` (`lang_id`) USING BTREE,
  CONSTRAINT `fk_metadatamasterlang_langid` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_metadatamasterlang_metadataid` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_metadata_lang`
--

LOCK TABLES `ebi_metadata_lang` WRITE;
/*!40000 ALTER TABLE `ebi_metadata_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_metadata_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_metadata_list_values`
--

DROP TABLE IF EXISTS `ebi_metadata_list_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_metadata_list_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebi_metadata_id` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `list_name` varchar(1000) DEFAULT NULL,
  `list_value` varchar(255) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `metadataid_idx` (`ebi_metadata_id`) USING BTREE,
  KEY `metadatalistvalues_langid_idx` (`lang_id`) USING BTREE,
  CONSTRAINT `fk_metadatalistvalues_langid` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_metadatalistvalues_metadataid` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_metadata_list_values`
--

LOCK TABLES `ebi_metadata_list_values` WRITE;
/*!40000 ALTER TABLE `ebi_metadata_list_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_metadata_list_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_permissionset`
--

DROP TABLE IF EXISTS `ebi_permissionset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_permissionset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `accesslevel_ind_agg` tinyint(4) DEFAULT NULL,
  `accesslevel_agg` tinyint(4) DEFAULT NULL,
  `risk_indicator` tinyint(4) DEFAULT NULL,
  `intent_to_leave` tinyint(4) DEFAULT NULL,
  `inactive_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_permissionset`
--

LOCK TABLES `ebi_permissionset` WRITE;
/*!40000 ALTER TABLE `ebi_permissionset` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_permissionset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_permissionset_datablock`
--

DROP TABLE IF EXISTS `ebi_permissionset_datablock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_permissionset_datablock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebi_permissionset_id` int(11) NOT NULL,
  `datablock_id` int(11) NOT NULL,
  `block_type` varchar(10) DEFAULT NULL,
  `timeframe_all` tinyint(1) DEFAULT NULL,
  `current_calendar` tinyint(1) DEFAULT NULL,
  `previous_period` tinyint(1) DEFAULT NULL,
  `next_period` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ebi_permissionset_datablock_ebi_permissionset1_idx` (`ebi_permissionset_id`),
  KEY `fk_ebi_permissionset_datablock_datablock_master1_idx` (`datablock_id`),
  CONSTRAINT `fk_ebi_permissionset_datablock_datablock_master1` FOREIGN KEY (`datablock_id`) REFERENCES `datablock_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebi_permissionset_datablock_ebi_permissionset1` FOREIGN KEY (`ebi_permissionset_id`) REFERENCES `ebi_permissionset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_permissionset_datablock`
--

LOCK TABLES `ebi_permissionset_datablock` WRITE;
/*!40000 ALTER TABLE `ebi_permissionset_datablock` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_permissionset_datablock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_permissionset_features`
--

DROP TABLE IF EXISTS `ebi_permissionset_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_permissionset_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebi_permissionset_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `private_create` tinyint(4) DEFAULT NULL,
  `public_create` tinyint(4) DEFAULT NULL,
  `public_view` tinyint(4) DEFAULT NULL,
  `team_view` tinyint(4) DEFAULT NULL,
  `team_create` tinyint(4) DEFAULT NULL,
  `receive_referral` tinyint(4) DEFAULT NULL,
  `timeframe_all` binary(1) DEFAULT NULL,
  `current_calendar` binary(1) DEFAULT NULL,
  `previous_period` binary(1) DEFAULT NULL,
  `next_period` binary(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ebi_permissionset_features_ebi_permissionset1_idx` (`ebi_permissionset_id`),
  KEY `fk_ebi_permissionset_features_feature_master1_idx` (`feature_id`),
  CONSTRAINT `fk_ebi_permissionset_features_ebi_permissionset1` FOREIGN KEY (`ebi_permissionset_id`) REFERENCES `ebi_permissionset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebi_permissionset_features_feature_master1` FOREIGN KEY (`feature_id`) REFERENCES `feature_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_permissionset_features`
--

LOCK TABLES `ebi_permissionset_features` WRITE;
/*!40000 ALTER TABLE `ebi_permissionset_features` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_permissionset_features` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_permissionset_lang`
--

DROP TABLE IF EXISTS `ebi_permissionset_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_permissionset_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebi_permissionset_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `permissionset_name` varchar(1000) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ebi_permissionset_lang_ebi_permissionset1_idx` (`ebi_permissionset_id`),
  KEY `fk_ebi_permissionset_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `fk_ebi_permissionset_lang_ebi_permissionset1` FOREIGN KEY (`ebi_permissionset_id`) REFERENCES `ebi_permissionset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebi_permissionset_lang_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_permissionset_lang`
--

LOCK TABLES `ebi_permissionset_lang` WRITE;
/*!40000 ALTER TABLE `ebi_permissionset_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_permissionset_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_question`
--

DROP TABLE IF EXISTS `ebi_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_key` varchar(45) DEFAULT NULL,
  `question_type_id` varchar(4) NOT NULL,
  `question_category_id` int(11) NOT NULL,
  `has_other` tinyint(1) DEFAULT NULL,
  `external_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ebi_question_question_type1_idx` (`question_type_id`),
  KEY `fk_ebi_question_question_category1_idx` (`question_category_id`),
  CONSTRAINT `fk_ebi_question_question_category1` FOREIGN KEY (`question_category_id`) REFERENCES `question_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebi_question_question_type1` FOREIGN KEY (`question_type_id`) REFERENCES `question_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_question`
--

LOCK TABLES `ebi_question` WRITE;
/*!40000 ALTER TABLE `ebi_question` DISABLE KEYS */;
INSERT INTO `ebi_question` VALUES (1,NULL,'1',1,NULL,NULL),(2,NULL,'1',1,NULL,NULL),(3,NULL,'1',1,NULL,NULL),(4,NULL,'1',1,NULL,NULL),(5,NULL,'1',1,NULL,NULL),(6,NULL,'1',1,NULL,NULL),(7,NULL,'1',1,NULL,NULL),(8,NULL,'1',1,NULL,NULL),(9,NULL,'1',1,NULL,NULL);
/*!40000 ALTER TABLE `ebi_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_question_options`
--

DROP TABLE IF EXISTS `ebi_question_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_question_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebi_question_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `option_value` varchar(45) DEFAULT NULL,
  `option_text` varchar(1000) DEFAULT NULL,
  `option_rpt` varchar(1000) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `external_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ebi_question_options_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_ebi_question_options_language_master1_idx` (`lang_id`),
  CONSTRAINT `fk_ebi_question_options_ebi_question1` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebi_question_options_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_question_options`
--

LOCK TABLES `ebi_question_options` WRITE;
/*!40000 ALTER TABLE `ebi_question_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_question_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_questions_lang`
--

DROP TABLE IF EXISTS `ebi_questions_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_questions_lang` (
  `ebi_question_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `question_text` varchar(3000) DEFAULT NULL,
  `question_rpt` varchar(3000) DEFAULT NULL,
  PRIMARY KEY (`ebi_question_id`,`lang_id`),
  KEY `fk_ebi_questions_lang_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_ebi_questions_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `fk_ebi_questions_lang_ebi_question1` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebi_questions_lang_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_questions_lang`
--

LOCK TABLES `ebi_questions_lang` WRITE;
/*!40000 ALTER TABLE `ebi_questions_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_questions_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_report_permissions`
--

DROP TABLE IF EXISTS `ebi_report_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_report_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebi_permissionset_id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `timeframe_all` binary(1) DEFAULT NULL,
  `current_calendar` binary(1) DEFAULT NULL,
  `previous_period` binary(1) DEFAULT NULL,
  `next_period` binary(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ebi_report_permissions_ebi_permissionset1_idx` (`ebi_permissionset_id`),
  KEY `fk_ebi_report_permissions_report_master1_idx` (`report_id`),
  CONSTRAINT `fk_ebi_report_permissions_ebi_permissionset1` FOREIGN KEY (`ebi_permissionset_id`) REFERENCES `ebi_permissionset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebi_report_permissions_report_master1` FOREIGN KEY (`report_id`) REFERENCES `report_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_report_permissions`
--

LOCK TABLES `ebi_report_permissions` WRITE;
/*!40000 ALTER TABLE `ebi_report_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_report_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_search`
--

DROP TABLE IF EXISTS `ebi_search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `query_key` varchar(45) DEFAULT NULL COMMENT 'query_key will be used to hold the category name for predefined searches',
  `search_type` varchar(1) DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT NULL,
  `query` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_search`
--

LOCK TABLES `ebi_search` WRITE;
/*!40000 ALTER TABLE `ebi_search` DISABLE KEYS */;
INSERT INTO `ebi_search` VALUES (1,'My_High_priority_students_Count','S',1,NULL);
/*!40000 ALTER TABLE `ebi_search` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_search_history`
--

DROP TABLE IF EXISTS `ebi_search_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_search_history` (
  `person_id` int(11) NOT NULL,
  `ebi_search_id` int(11) NOT NULL,
  `last_run` timestamp NULL DEFAULT NULL COMMENT 'The last_run column can be always updated, if exists, instead of adding new rows',
  PRIMARY KEY (`person_id`,`ebi_search_id`),
  KEY `fk_ebi_search_history_person1_idx` (`person_id`),
  KEY `fk_ebi_search_history_ebi_search1_idx` (`ebi_search_id`),
  CONSTRAINT `fk_ebi_search_history_ebi_search1` FOREIGN KEY (`ebi_search_id`) REFERENCES `ebi_search` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebi_search_history_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_search_history`
--

LOCK TABLES `ebi_search_history` WRITE;
/*!40000 ALTER TABLE `ebi_search_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_search_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_search_lang`
--

DROP TABLE IF EXISTS `ebi_search_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_search_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebi_search_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `description` varchar(2000) DEFAULT NULL,
  `sub_category_name` varchar(100) DEFAULT NULL COMMENT 'For predefined searches, description contains category name and sub_category_name contains the sub category name',
  PRIMARY KEY (`id`),
  KEY `fk_ebi_search_lang_ebi_search1_idx` (`ebi_search_id`),
  KEY `fk_ebi_search_lang_language_master1_idx` (`language_id`),
  CONSTRAINT `fk_ebi_search_lang_ebi_search1` FOREIGN KEY (`ebi_search_id`) REFERENCES `ebi_search` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebi_search_lang_language_master1` FOREIGN KEY (`language_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_search_lang`
--

LOCK TABLES `ebi_search_lang` WRITE;
/*!40000 ALTER TABLE `ebi_search_lang` DISABLE KEYS */;
INSERT INTO `ebi_search_lang` VALUES (1,1,1,'My High Priority Students Count for Dashboard',NULL);
/*!40000 ALTER TABLE `ebi_search_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_users`
--

DROP TABLE IF EXISTS `ebi_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email_address` varchar(200) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `mobile_number` varchar(15) DEFAULT NULL,
  `is_active` binary(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_users`
--

LOCK TABLES `ebi_users` WRITE;
/*!40000 ALTER TABLE `ebi_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_template`
--

DROP TABLE IF EXISTS `email_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_key` varchar(45) DEFAULT NULL,
  `is_active` binary(1) DEFAULT NULL,
  `from_email_address` varchar(255) DEFAULT NULL,
  `bcc_recipient_list` varchar(500) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_template`
--

LOCK TABLES `email_template` WRITE;
/*!40000 ALTER TABLE `email_template` DISABLE KEYS */;
INSERT INTO `email_template` VALUES (1,'Welcome_Email_Staff',NULL,'no-reply@mnv-tech.com','ramesh.kumhar@techmahindra.com,SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,tbooth@hindbra.in,devadossp@gmail.com, Amith.Kishore@TechMahindra.com',NULL,NULL,NULL,NULL,NULL,NULL),(2,'Forgot_Password_Staff',NULL,'no-reply@mnv-tech.com','ramesh.kumhar@techmahindra.com,SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,tbooth@hindbra.in,devadossp@gmail.com, Amith.Kishore@TechMahindra.com',NULL,NULL,NULL,NULL,NULL,NULL),(3,'MyAccount_Updated_Staff',NULL,'no-reply@mnv-tech.com','ramesh.kumhar@techmahindra.com,SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,tbooth@hindbra.in,devadossp@gmail.com, Amith.Kishore@TechMahindra.com',NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `email_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_template_lang`
--

DROP TABLE IF EXISTS `email_template_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_template_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_template_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `body` longtext,
  `subject` varchar(1000) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_email_notification_lang_email_notification1_idx` (`email_template_id`),
  KEY `fk_email_notification_lang_language_master1_idx` (`language_id`),
  CONSTRAINT `fk_email_notification_lang_email_notification1` FOREIGN KEY (`email_template_id`) REFERENCES `email_template` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_email_notification_lang_language_master1` FOREIGN KEY (`language_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_template_lang`
--

LOCK TABLES `email_template_lang` WRITE;
/*!40000 ALTER TABLE `email_template_lang` DISABLE KEYS */;
INSERT INTO `email_template_lang` VALUES (1,1,1,'Hi $$firstname$$,<br/>\nA MAP-Works password was successfully created for your account. <br/>\nIf this was not you or you believe this is an error, please contact MAP-Works support at support@map-works.com<br/>\nThank you from the MAP-Works team!','Welcome to Map-Works!!',NULL,NULL,NULL,NULL,NULL,NULL),(2,2,1,'<html>\n	<head>\n		<title></title>\n	</head>\n	<body>\n		<p style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\n			Hi <strong>$$firstname$$</strong>,</p>\n		<p style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\n			Please use the link below and follow the displayed instructions to create your new password. This link will expire after $$expire$$ hours.</p>\n		<p style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\n			$$token$$</p>\n		<p style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\n			If you believe that you received this email in error or if you have any questions, please contact MAP-works support at support@map-works.com.</p>\n		<p style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\n			<strong>Thank you from the MAP-Works team!</strong></p>\n	</body>\n</html>\n','Map-Works Account Reset Information',NULL,NULL,NULL,NULL,NULL,NULL),(3,3,1,'<html>\n	<head>\n		<title></title>\n	</head>\n	<body>\n		<p style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\n			Hi $$firstname$$,</p>\n		<p style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\n			An update to your MAP-Works account was successfully made. The following information was updated:</p>\n		<ul style=\"margin: 10px 0px 0px; padding-left: 22px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\n			<li>\n				$$mobilephone$$</li>\n			<li>\n				$$password$$</li>\n		</ul>\n		<p style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\n			If this was not you or you believe this is an error, please contact MAP-Works support at&nbsp;<a class=\"external-link\" href=\"mailto:support@map-works.com\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: none;\">support@map-works.com</a></p>\n		<p style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">\n			<strong>Thank you from the MAP-Works team!</strong></p>\n	</body>\n</html>\n!','Map-Works Account Update Notification',NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `email_template_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ext_log_entries`
--

DROP TABLE IF EXISTS `ext_log_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ext_log_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `logged_at` datetime NOT NULL,
  `object_id` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `object_class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `version` int(11) NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:array)',
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_class_lookup_idx` (`object_class`),
  KEY `log_date_lookup_idx` (`logged_at`),
  KEY `log_user_lookup_idx` (`username`),
  KEY `log_version_lookup_idx` (`object_id`,`object_class`,`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ext_log_entries`
--

LOCK TABLES `ext_log_entries` WRITE;
/*!40000 ALTER TABLE `ext_log_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `ext_log_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ext_translations`
--

DROP TABLE IF EXISTS `ext_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ext_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `locale` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `object_class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `foreign_key` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lookup_unique_idx` (`locale`,`object_class`,`field`,`foreign_key`),
  KEY `translations_lookup_idx` (`locale`,`object_class`,`foreign_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ext_translations`
--

LOCK TABLES `ext_translations` WRITE;
/*!40000 ALTER TABLE `ext_translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `ext_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factor`
--

DROP TABLE IF EXISTS `factor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `factor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('bank','survey') DEFAULT NULL,
  `survey_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_factor_survey1_idx` (`survey_id`),
  CONSTRAINT `fk_factor_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factor`
--

LOCK TABLES `factor` WRITE;
/*!40000 ALTER TABLE `factor` DISABLE KEYS */;
/*!40000 ALTER TABLE `factor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factor_lang`
--

DROP TABLE IF EXISTS `factor_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `factor_lang` (
  `factor_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`lang_id`,`factor_id`),
  KEY `fk_factors_lang_factors1_idx` (`factor_id`),
  CONSTRAINT `fk_factor_lang_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_factors_lang_factors1` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factor_lang`
--

LOCK TABLES `factor_lang` WRITE;
/*!40000 ALTER TABLE `factor_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `factor_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factor_questions`
--

DROP TABLE IF EXISTS `factor_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `factor_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `factor_id` int(11) NOT NULL,
  `ebi_question_id` int(11) NOT NULL,
  `survey_questions_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_factor_questions_factor1` (`factor_id`),
  KEY `fk_factor_questions_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_factor_questions_survey_questions1_idx` (`survey_questions_id`),
  CONSTRAINT `fk_factor_questions_ebi_question1` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_factor_questions_factor1` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_factor_questions_survey_questions1` FOREIGN KEY (`survey_questions_id`) REFERENCES `survey_questions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factor_questions`
--

LOCK TABLES `factor_questions` WRITE;
/*!40000 ALTER TABLE `factor_questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `factor_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feature_master`
--

DROP TABLE IF EXISTS `feature_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feature_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feature_master`
--

LOCK TABLES `feature_master` WRITE;
/*!40000 ALTER TABLE `feature_master` DISABLE KEYS */;
INSERT INTO `feature_master` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL),(2,NULL,NULL,NULL,NULL,NULL,NULL),(3,NULL,NULL,NULL,NULL,NULL,NULL),(4,NULL,NULL,NULL,NULL,NULL,NULL),(5,NULL,NULL,NULL,NULL,NULL,NULL),(6,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `feature_master` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feature_master_lang`
--

DROP TABLE IF EXISTS `feature_master_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feature_master_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feature_master_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `feature_name` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_feature_master_lang_feature_master1_idx` (`feature_master_id`),
  KEY `fk_feature_master_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `fk_feature_master_lang_feature_master1` FOREIGN KEY (`feature_master_id`) REFERENCES `feature_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_feature_master_lang_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feature_master_lang`
--

LOCK TABLES `feature_master_lang` WRITE;
/*!40000 ALTER TABLE `feature_master_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `feature_master_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ind_question`
--

DROP TABLE IF EXISTS `ind_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ind_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `question_type_id` varchar(4) NOT NULL,
  `question_category_id` int(11) NOT NULL,
  `has_other` tinyint(1) DEFAULT NULL,
  `external_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ind_question_question_type1_idx` (`question_type_id`),
  KEY `fk_ind_question_question_category1_idx` (`question_category_id`),
  KEY `fk_ind_question_survey1_idx` (`survey_id`),
  CONSTRAINT `fk_ind_question_question_category1` FOREIGN KEY (`question_category_id`) REFERENCES `question_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ind_question_question_type1` FOREIGN KEY (`question_type_id`) REFERENCES `question_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ind_question_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ind_question`
--

LOCK TABLES `ind_question` WRITE;
/*!40000 ALTER TABLE `ind_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `ind_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ind_question_options`
--

DROP TABLE IF EXISTS `ind_question_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ind_question_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ind_question_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `option_value` varchar(45) DEFAULT NULL,
  `option_text` varchar(1000) DEFAULT NULL,
  `option_rpt` varchar(1000) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `external_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ind_question_options_ind_question1_idx` (`ind_question_id`),
  KEY `fk_ind_question_options_language_master1_idx` (`lang_id`),
  CONSTRAINT `fk_ind_question_options_ind_question1` FOREIGN KEY (`ind_question_id`) REFERENCES `ind_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ind_question_options_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ind_question_options`
--

LOCK TABLES `ind_question_options` WRITE;
/*!40000 ALTER TABLE `ind_question_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `ind_question_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ind_questions_lang`
--

DROP TABLE IF EXISTS `ind_questions_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ind_questions_lang` (
  `ind_question_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `question_text` varchar(3000) DEFAULT NULL,
  `question_rpt` varchar(3000) DEFAULT NULL,
  PRIMARY KEY (`lang_id`,`ind_question_id`),
  KEY `fk_survey_questions_lang_language_master1_idx` (`lang_id`),
  KEY `fk_survey_questions_lang_ind_question1_idx` (`ind_question_id`),
  CONSTRAINT `fk_survey_questions_lang_ind_question1` FOREIGN KEY (`ind_question_id`) REFERENCES `ind_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_questions_lang_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ind_questions_lang`
--

LOCK TABLES `ind_questions_lang` WRITE;
/*!40000 ALTER TABLE `ind_questions_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `ind_questions_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intent_to_leave`
--

DROP TABLE IF EXISTS `intent_to_leave`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `intent_to_leave` (
  `id` int(11) NOT NULL,
  `text` varchar(10) DEFAULT NULL,
  `image_name` varchar(200) DEFAULT NULL,
  `color_hex` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intent_to_leave`
--

LOCK TABLES `intent_to_leave` WRITE;
/*!40000 ALTER TABLE `intent_to_leave` DISABLE KEYS */;
/*!40000 ALTER TABLE `intent_to_leave` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `isp`
--

DROP TABLE IF EXISTS `isp`;
/*!50001 DROP VIEW IF EXISTS `isp`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `isp` (
  `risk_model_id` tinyint NOT NULL,
  `source` tinyint NOT NULL,
  `risk_variable_id` tinyint NOT NULL,
  `variable_type` tinyint NOT NULL,
  `weight` tinyint NOT NULL,
  `org_metadata_id` tinyint NOT NULL,
  `metadata_value` tinyint NOT NULL,
  `bucket_value` tinyint NOT NULL,
  `person_id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `issue`
--

DROP TABLE IF EXISTS `issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `issue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `survey_questions_id` int(11) DEFAULT NULL,
  `factor_questions_id` int(11) DEFAULT NULL,
  `min` decimal(8,4) DEFAULT NULL,
  `max` decimal(8,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_issue_survey1_idx` (`survey_id`),
  KEY `fk_issue_survey_questions1_idx` (`survey_questions_id`),
  KEY `fk_issue_factor_questions1_idx` (`factor_questions_id`),
  CONSTRAINT `fk_issue_factor_questions1` FOREIGN KEY (`factor_questions_id`) REFERENCES `factor_questions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_issue_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_issue_survey_questions1` FOREIGN KEY (`survey_questions_id`) REFERENCES `survey_questions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `issue`
--

LOCK TABLES `issue` WRITE;
/*!40000 ALTER TABLE `issue` DISABLE KEYS */;
/*!40000 ALTER TABLE `issue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `issue_lang`
--

DROP TABLE IF EXISTS `issue_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `issue_lang` (
  `id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_issue_lang_issue1_idx` (`issue_id`),
  KEY `fk_issue_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `fk_issue_lang_issue1` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_issue_lang_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `issue_lang`
--

LOCK TABLES `issue_lang` WRITE;
/*!40000 ALTER TABLE `issue_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `issue_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `issue_options`
--

DROP TABLE IF EXISTS `issue_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `issue_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `issue_id` int(11) NOT NULL,
  `ebi_question_options_id` int(11) DEFAULT NULL,
  `ind_question_options_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_issue_options_issue1_idx` (`issue_id`),
  KEY `fk_issue_options_ebi_question_options1_idx` (`ebi_question_options_id`),
  KEY `fk_issue_options_ind_question_options1_idx` (`ind_question_options_id`),
  CONSTRAINT `fk_issue_options_ebi_question_options1` FOREIGN KEY (`ebi_question_options_id`) REFERENCES `ebi_question_options` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_issue_options_ind_question_options1` FOREIGN KEY (`ind_question_options_id`) REFERENCES `ind_question_options` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_issue_options_issue1` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `issue_options`
--

LOCK TABLES `issue_options` WRITE;
/*!40000 ALTER TABLE `issue_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `issue_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `language_master`
--

DROP TABLE IF EXISTS `language_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_code` varchar(10) DEFAULT NULL,
  `lang_description` varchar(45) DEFAULT NULL,
  `is_system_default` bit(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `language_master`
--

LOCK TABLES `language_master` WRITE;
/*!40000 ALTER TABLE `language_master` DISABLE KEYS */;
INSERT INTO `language_master` VALUES (1,'en_US','English (US)','',NULL,NULL,NULL,NULL,NULL,NULL),(2,'fr_CA','French (Canada)',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'es_MX','Spanish (Mexico)',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `language_master` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `market`
--

DROP TABLE IF EXISTS `market`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `market` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `market_description` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `market`
--

LOCK TABLES `market` WRITE;
/*!40000 ALTER TABLE `market` DISABLE KEYS */;
/*!40000 ALTER TABLE `market` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metadata_market`
--

DROP TABLE IF EXISTS `metadata_market`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_market` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebi_metadata_id` int(11) NOT NULL,
  `market_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_metadata_market_metadata_master1_idx` (`ebi_metadata_id`),
  KEY `fk_metadata_market_market1_idx` (`market_id`),
  CONSTRAINT `fk_metadata_market_market1` FOREIGN KEY (`market_id`) REFERENCES `market` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_metadata_market_metadata_master1` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='table stores all markets the specific metadata needs to address';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_market`
--

LOCK TABLES `metadata_market` WRITE;
/*!40000 ALTER TABLE `metadata_market` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_market` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration_versions`
--

DROP TABLE IF EXISTS `migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration_versions` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration_versions`
--

LOCK TABLES `migration_versions` WRITE;
/*!40000 ALTER TABLE `migration_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `note`
--

DROP TABLE IF EXISTS `note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id_student` int(11) NOT NULL,
  `person_id_faculty` int(11) NOT NULL,
  `activity_category_id` int(11) NOT NULL,
  `note` text,
  `note_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `access_private` tinyint(4) DEFAULT NULL,
  `access_public` tinyint(4) DEFAULT NULL,
  `access_team` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_note_organization1_idx` (`organization_id`),
  KEY `fk_note_person1_idx` (`person_id_student`),
  KEY `fk_note_person2_idx` (`person_id_faculty`),
  KEY `fk_note_activity_category1_idx` (`activity_category_id`),
  CONSTRAINT `fk_note_activity_category1` FOREIGN KEY (`activity_category_id`) REFERENCES `activity_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_person1` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_person2` FOREIGN KEY (`person_id_faculty`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `note`
--

LOCK TABLES `note` WRITE;
/*!40000 ALTER TABLE `note` DISABLE KEYS */;
/*!40000 ALTER TABLE `note` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `note_teams`
--

DROP TABLE IF EXISTS `note_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `note_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note_id` int(11) NOT NULL,
  `teams_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_note_teams_note1_idx` (`note_id`),
  KEY `fk_note_teams_teams1_idx` (`teams_id`),
  CONSTRAINT `fk_note_teams_note1` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_note_teams_teams1` FOREIGN KEY (`teams_id`) REFERENCES `teams` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `note_teams`
--

LOCK TABLES `note_teams` WRITE;
/*!40000 ALTER TABLE `note_teams` DISABLE KEYS */;
/*!40000 ALTER TABLE `note_teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_log`
--

DROP TABLE IF EXISTS `notification_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `email_key` varchar(45) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL,
  `recipient_list` varchar(500) DEFAULT NULL,
  `cc_list` varchar(500) DEFAULT NULL,
  `bcc_list` varchar(500) DEFAULT NULL,
  `subject` varchar(1000) DEFAULT NULL,
  `body` text,
  `status` varchar(1) DEFAULT NULL,
  `no_of_retries` int(11) DEFAULT NULL,
  `server_response` varchar(500) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notification_log_organization1_idx` (`organization_id`),
  CONSTRAINT `fk_notification_log_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_log`
--

LOCK TABLES `notification_log` WRITE;
/*!40000 ALTER TABLE `notification_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `office_hours`
--

DROP TABLE IF EXISTS `office_hours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `office_hours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `slot_type` varchar(1) DEFAULT NULL,
  `office_hours_series_id` int(11) NOT NULL,
  `appointments_id` int(11) NOT NULL,
  `location` varchar(45) DEFAULT NULL,
  `slot_start` datetime DEFAULT NULL,
  `slot_end` datetime DEFAULT NULL,
  `meeting_length` int(11) DEFAULT NULL,
  `standing_instructions` varchar(255) DEFAULT NULL,
  `person_id_proxy_created` int(11) DEFAULT NULL,
  `is_cancelled` tinyint(1) DEFAULT NULL,
  `person_id_proxy_cancelled` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_office_hours_organization1_idx` (`organization_id`),
  KEY `fk_office_hours_person1_idx` (`person_id`),
  KEY `fk_office_hours_person2_idx` (`person_id_proxy_created`),
  KEY `fk_office_hours_office_hours_series1_idx` (`office_hours_series_id`),
  KEY `fk_office_hours_appointments1_idx` (`appointments_id`),
  KEY `fk_office_hours_person3_idx` (`person_id_proxy_cancelled`),
  CONSTRAINT `fk_office_hours_appointments1` FOREIGN KEY (`appointments_id`) REFERENCES `appointments` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_office_hours_office_hours_series1` FOREIGN KEY (`office_hours_series_id`) REFERENCES `office_hours_series` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_office_hours_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_office_hours_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_office_hours_person2` FOREIGN KEY (`person_id_proxy_created`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_office_hours_person3` FOREIGN KEY (`person_id_proxy_cancelled`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `office_hours`
--

LOCK TABLES `office_hours` WRITE;
/*!40000 ALTER TABLE `office_hours` DISABLE KEYS */;
/*!40000 ALTER TABLE `office_hours` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `office_hours_series`
--

DROP TABLE IF EXISTS `office_hours_series`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `office_hours_series` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `days` varchar(7) DEFAULT NULL,
  `location` varchar(45) DEFAULT NULL,
  `slot_start` datetime DEFAULT NULL,
  `slot_end` datetime DEFAULT NULL,
  `meeting_length` int(11) DEFAULT NULL,
  `standing_instructions` varchar(255) DEFAULT NULL,
  `repeat_pattern` varchar(1) DEFAULT NULL,
  `repeat_every` int(11) DEFAULT NULL,
  `repetition_range` varchar(1) DEFAULT NULL,
  `repetition_occurrence` int(11) DEFAULT NULL,
  `person_id_proxy` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_office_hours_series_organization1_idx` (`organization_id`),
  KEY `fk_office_hours_series_person1_idx` (`person_id`),
  KEY `fk_office_hours_series_person2_idx` (`person_id_proxy`),
  CONSTRAINT `fk_office_hours_series_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_office_hours_series_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_office_hours_series_person2` FOREIGN KEY (`person_id_proxy`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `office_hours_series`
--

LOCK TABLES `office_hours_series` WRITE;
/*!40000 ALTER TABLE `office_hours_series` DISABLE KEYS */;
/*!40000 ALTER TABLE `office_hours_series` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_academic_terms`
--

DROP TABLE IF EXISTS `org_academic_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_academic_terms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_academic_year_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `name` varchar(120) DEFAULT NULL,
  `short_code` varchar(10) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_academicperiod_academicyearid` (`org_academic_year_id`),
  KEY `fk_academicperiod_organizationid` (`organization_id`),
  CONSTRAINT `fk_academicperiod_academicyearid` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_academicperiod_organizationid` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_academic_terms`
--

LOCK TABLES `org_academic_terms` WRITE;
/*!40000 ALTER TABLE `org_academic_terms` DISABLE KEYS */;
INSERT INTO `org_academic_terms` VALUES (1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `org_academic_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_academic_year`
--

DROP TABLE IF EXISTS `org_academic_year`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_academic_year` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `name` varchar(120) DEFAULT NULL,
  `year_id` varchar(10) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `relationship9` (`organization_id`),
  KEY `fk_org_academic_year_year1_idx` (`year_id`),
  CONSTRAINT `fk_org_academic_year_year1` FOREIGN KEY (`year_id`) REFERENCES `year` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `relationship9` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_academic_year`
--

LOCK TABLES `org_academic_year` WRITE;
/*!40000 ALTER TABLE `org_academic_year` DISABLE KEYS */;
INSERT INTO `org_academic_year` VALUES (1,1,'Spring','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `org_academic_year` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_announcements`
--

DROP TABLE IF EXISTS `org_announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_announcements` (
  `id` int(11) NOT NULL COMMENT 'Need surrogate primary key, since no combination primary key can be created',
  `org_id` int(11) NOT NULL,
  `display_type` enum('banner','alert bell') NOT NULL,
  `start_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `stop_datetime` timestamp NULL DEFAULT NULL,
  `creator_person_id` int(11) NOT NULL COMMENT 'ID of the person/coordinator who created this announcement',
  PRIMARY KEY (`id`),
  KEY `fk_org_announcements_organization1_idx` (`org_id`),
  KEY `fk_org_announcements_person1_idx` (`creator_person_id`),
  CONSTRAINT `fk_org_announcements_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_announcements_person1` FOREIGN KEY (`creator_person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_announcements`
--

LOCK TABLES `org_announcements` WRITE;
/*!40000 ALTER TABLE `org_announcements` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_announcements_lang`
--

DROP TABLE IF EXISTS `org_announcements_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_announcements_lang` (
  `id` int(11) NOT NULL,
  `org_announcements_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `message` varchar(300) DEFAULT NULL COMMENT 'Supporting multiple languages',
  PRIMARY KEY (`id`),
  KEY `fk_org_announcements_lang_org_announcements1_idx` (`org_announcements_id`),
  KEY `fk_org_announcements_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `fk_org_announcements_lang_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_announcements_lang_org_announcements1` FOREIGN KEY (`org_announcements_id`) REFERENCES `org_announcements` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_announcements_lang`
--

LOCK TABLES `org_announcements_lang` WRITE;
/*!40000 ALTER TABLE `org_announcements_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_announcements_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_calculated_risk_variables`
--

DROP TABLE IF EXISTS `org_calculated_risk_variables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_calculated_risk_variables` (
  `org_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `risk_variable_id` int(11) NOT NULL,
  `calc_bucket_value` int(11) DEFAULT NULL,
  `calc_weight` decimal(8,4) DEFAULT NULL,
  `risk_model_id` int(11) NOT NULL,
  `risk_source_value` decimal(12,4) DEFAULT NULL,
  PRIMARY KEY (`org_id`,`person_id`,`risk_variable_id`),
  KEY `fk_org_computed_risk_variables_person1_idx` (`person_id`),
  KEY `fk_org_computed_risk_variables_risk_variable1_idx` (`risk_variable_id`),
  KEY `fk_org_calculated_risk_variables_risk_model_master1_idx` (`risk_model_id`),
  CONSTRAINT `fk_org_calculated_risk_variables_risk_model_master1` FOREIGN KEY (`risk_model_id`) REFERENCES `risk_model_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_computed_risk_variables_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_computed_risk_variables_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_computed_risk_variables_risk_variable1` FOREIGN KEY (`risk_variable_id`) REFERENCES `risk_variable` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_calculated_risk_variables`
--

LOCK TABLES `org_calculated_risk_variables` WRITE;
/*!40000 ALTER TABLE `org_calculated_risk_variables` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_calculated_risk_variables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_campus_resource`
--

DROP TABLE IF EXISTS `org_campus_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_campus_resource` (
  `org_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `person_id` int(11) NOT NULL,
  `phone` varchar(45) NOT NULL,
  `email` varchar(120) NOT NULL,
  `location` varchar(45) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `description` varchar(300) DEFAULT NULL,
  `visible_to_student` enum('1','0') NOT NULL DEFAULT '1',
  `receive_referrals` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_campus_resource_person1_idx` (`person_id`),
  KEY `fk_campus_resource_organization1_idx` (`org_id`),
  CONSTRAINT `fk_campus_resource_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_campus_resource_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_campus_resource`
--

LOCK TABLES `org_campus_resource` WRITE;
/*!40000 ALTER TABLE `org_campus_resource` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_campus_resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_change_request`
--

DROP TABLE IF EXISTS `org_change_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_change_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id_requested_by` int(11) NOT NULL,
  `person_id_student` int(11) NOT NULL,
  `org_id_source` int(11) NOT NULL,
  `date_submitted` timestamp NULL DEFAULT NULL,
  `approval_status` enum('yes','no') DEFAULT NULL,
  `org_id_destination` int(11) NOT NULL,
  `date_approved` timestamp NULL DEFAULT NULL,
  `person_id_approved_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_org_change_request_person1_idx` (`person_id_requested_by`),
  KEY `fk_org_change_request_person2_idx` (`person_id_student`),
  KEY `fk_org_change_request_organization1_idx` (`org_id_source`),
  KEY `fk_org_change_request_organization2_idx` (`org_id_destination`),
  KEY `fk_org_change_request_person3_idx` (`person_id_approved_by`),
  CONSTRAINT `fk_org_change_request_organization1` FOREIGN KEY (`org_id_source`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_change_request_organization2` FOREIGN KEY (`org_id_destination`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_change_request_person1` FOREIGN KEY (`person_id_requested_by`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_change_request_person2` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_change_request_person3` FOREIGN KEY (`person_id_approved_by`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_change_request`
--

LOCK TABLES `org_change_request` WRITE;
/*!40000 ALTER TABLE `org_change_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_change_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_conflict`
--

DROP TABLE IF EXISTS `org_conflict`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_conflict` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `src_org_id` int(11) NOT NULL,
  `dst_org_id` int(11) NOT NULL,
  `owning_org_tier_code` enum('0','1','2','3') NOT NULL DEFAULT '0' COMMENT 'owning_org_tier_code indicates whether the org where the person record is present is solo or MC, before merging the campus',
  `record_type` enum('master','home','other') DEFAULT NULL COMMENT 'record_type: ENUM(''master'',''home'',''other'')',
  `status` enum('conflict','merged') NOT NULL DEFAULT 'conflict' COMMENT 'Status: ENUM(''conflict'', ''merged'')\nStatus indicates the status of the conflicting record. Merged status indicates that the records are merged and are soft deleted records.\n',
  PRIMARY KEY (`id`),
  KEY `fk_table1_org_person_faculty1_idx` (`faculty_id`),
  KEY `fk_table1_org_person_student1_idx` (`student_id`),
  KEY `fk_table1_organization1_idx` (`src_org_id`),
  KEY `fk_table1_organization2_idx` (`dst_org_id`),
  CONSTRAINT `fk_table1_org_person_faculty1` FOREIGN KEY (`faculty_id`) REFERENCES `org_person_faculty` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_table1_org_person_student1` FOREIGN KEY (`student_id`) REFERENCES `org_person_student` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_table1_organization1` FOREIGN KEY (`src_org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_table1_organization2` FOREIGN KEY (`dst_org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_conflict`
--

LOCK TABLES `org_conflict` WRITE;
/*!40000 ALTER TABLE `org_conflict` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_conflict` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_course_faculty`
--

DROP TABLE IF EXISTS `org_course_faculty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_course_faculty` (
  `organization_id` int(11) NOT NULL,
  `org_courses_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `org_permissionset_id` int(11) NOT NULL,
  PRIMARY KEY (`organization_id`,`org_courses_id`,`person_id`),
  KEY `fk_course_faculty_organization1_idx` (`organization_id`),
  KEY `fk_course_faculty_org_courses1_idx` (`org_courses_id`),
  KEY `fk_course_faculty_person1_idx` (`person_id`),
  KEY `fk_org_course_faculty_org_permissionset1_idx` (`org_permissionset_id`),
  CONSTRAINT `fk_course_faculty_org_courses1` FOREIGN KEY (`org_courses_id`) REFERENCES `org_courses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_course_faculty_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_course_faculty_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_course_faculty_org_permissionset1` FOREIGN KEY (`org_permissionset_id`) REFERENCES `org_permissionset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_course_faculty`
--

LOCK TABLES `org_course_faculty` WRITE;
/*!40000 ALTER TABLE `org_course_faculty` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_course_faculty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_course_student`
--

DROP TABLE IF EXISTS `org_course_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_course_student` (
  `organization_id` int(11) NOT NULL,
  `org_courses_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  PRIMARY KEY (`organization_id`,`org_courses_id`,`person_id`),
  KEY `fk_course_student_organization1_idx` (`organization_id`),
  KEY `fk_course_student_org_courses1_idx` (`org_courses_id`),
  KEY `fk_course_student_person1_idx` (`person_id`),
  CONSTRAINT `fk_course_student_org_courses1` FOREIGN KEY (`org_courses_id`) REFERENCES `org_courses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_course_student_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_course_student_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_course_student`
--

LOCK TABLES `org_course_student` WRITE;
/*!40000 ALTER TABLE `org_course_student` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_course_student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_courses`
--

DROP TABLE IF EXISTS `org_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `org_academic_year_id` int(11) NOT NULL,
  `org_academic_terms_id` int(11) NOT NULL,
  `course_section_id` varchar(15) DEFAULT NULL,
  `college_code` varchar(10) DEFAULT NULL,
  `dept_code` varchar(10) DEFAULT NULL,
  `subject_code` varchar(10) DEFAULT NULL,
  `course_number` varchar(10) DEFAULT NULL,
  `course_name` varchar(200) DEFAULT NULL,
  `section_number` varchar(10) DEFAULT NULL,
  `days_times` varchar(45) DEFAULT NULL,
  `location` varchar(45) DEFAULT NULL,
  `credit_hours` decimal(5,2) DEFAULT NULL,
  `external_id` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniquecoursesectionid` (`organization_id`,`course_section_id`),
  KEY `fk_org_courses_organization1_idx` (`organization_id`),
  KEY `fk_org_courses_org_academic_year1_idx` (`org_academic_year_id`),
  KEY `fk_org_courses_org_academic_terms1_idx` (`org_academic_terms_id`),
  KEY `idx_year` (`organization_id`,`org_academic_year_id`),
  KEY `idx_term` (`organization_id`,`org_academic_year_id`,`org_academic_terms_id`),
  KEY `idx_college` (`organization_id`,`org_academic_year_id`,`org_academic_terms_id`,`college_code`),
  KEY `idx_dept` (`organization_id`,`org_academic_year_id`,`org_academic_terms_id`,`college_code`,`dept_code`),
  CONSTRAINT `fk_org_courses_org_academic_terms1` FOREIGN KEY (`org_academic_terms_id`) REFERENCES `org_academic_terms` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_courses_org_academic_year1` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_courses_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_courses`
--

LOCK TABLES `org_courses` WRITE;
/*!40000 ALTER TABLE `org_courses` DISABLE KEYS */;
INSERT INTO `org_courses` VALUES (1,1,1,1,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `org_courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_documents`
--

DROP TABLE IF EXISTS `org_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_documents` (
  `org_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `description` varchar(400) DEFAULT NULL,
  `type` enum('link','file') DEFAULT NULL,
  `link` varchar(400) DEFAULT NULL,
  `file_path` varchar(200) DEFAULT NULL,
  `display_filename` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_org_documents_organization1_idx` (`org_id`),
  CONSTRAINT `fk_org_documents_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_documents`
--

LOCK TABLES `org_documents` WRITE;
/*!40000 ALTER TABLE `org_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_email_template`
--

DROP TABLE IF EXISTS `org_email_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_email_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_key` varchar(45) DEFAULT NULL,
  `organization_id` int(11) NOT NULL,
  `email_template_id` int(11) NOT NULL,
  `subject` varchar(1000) DEFAULT NULL,
  `body` longtext,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_org_email_notification_organization1_idx` (`organization_id`),
  KEY `fk_org_email_notification_email_notification1_idx` (`email_template_id`),
  CONSTRAINT `fk_org_email_notification_email_notification1` FOREIGN KEY (`email_template_id`) REFERENCES `email_template` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_email_notification_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_email_template`
--

LOCK TABLES `org_email_template` WRITE;
/*!40000 ALTER TABLE `org_email_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_email_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_features`
--

DROP TABLE IF EXISTS `org_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `feature_id` int(11) DEFAULT NULL,
  `is_enabled` binary(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_organizationfeatures_organizationid` (`organization_id`),
  KEY `fk_organizationfeature_featureid` (`feature_id`),
  CONSTRAINT `fk_organizationfeature_featureid` FOREIGN KEY (`feature_id`) REFERENCES `feature_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organizationfeatures_organizationid` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='																																																																												';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_features`
--

LOCK TABLES `org_features` WRITE;
/*!40000 ALTER TABLE `org_features` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_features` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_group`
--

DROP TABLE IF EXISTS `org_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `group_name` varchar(1000) DEFAULT NULL,
  `parent_group_id` int(11) DEFAULT NULL,
  `external_id` varchar(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `org_group.orgid_idx` (`organization_id`),
  KEY `org_group.groupid_idx` (`parent_group_id`),
  CONSTRAINT `fk_org_group_groupid` FOREIGN KEY (`parent_group_id`) REFERENCES `org_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_group_orgid` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_group`
--

LOCK TABLES `org_group` WRITE;
/*!40000 ALTER TABLE `org_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_group_faculty`
--

DROP TABLE IF EXISTS `org_group_faculty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_group_faculty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `org_group_id` int(11) DEFAULT NULL,
  `org_permissionset_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `is_invisible` binary(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `org_group_faculty.group_id_idx` (`org_group_id`),
  KEY `org_group_faculty.organization_id_idx` (`organization_id`),
  KEY `org_group_faculty.org_permissionset_id_idx` (`org_permissionset_id`),
  KEY `org_group_faculty,person_id_idx` (`person_id`),
  CONSTRAINT `fk_org_group_faculty_group_id` FOREIGN KEY (`org_group_id`) REFERENCES `org_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_group_faculty_org_permissionset_id` FOREIGN KEY (`org_permissionset_id`) REFERENCES `org_permissionset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_group_faculty_organization_id` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_group_faculty_person_id` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_group_faculty`
--

LOCK TABLES `org_group_faculty` WRITE;
/*!40000 ALTER TABLE `org_group_faculty` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_group_faculty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_group_students`
--

DROP TABLE IF EXISTS `org_group_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_group_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `org_group_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `org_group_students.orgid_idx` (`organization_id`),
  KEY `org_group_students.org_group_id_idx` (`org_group_id`),
  KEY `org_group_students.person_id_idx` (`person_id`),
  CONSTRAINT `fk_org_group_students_org_group_id` FOREIGN KEY (`org_group_id`) REFERENCES `org_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_group_students_orgid` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_group_students_person_id` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_group_students`
--

LOCK TABLES `org_group_students` WRITE;
/*!40000 ALTER TABLE `org_group_students` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_group_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_metadata`
--

DROP TABLE IF EXISTS `org_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `meta_key` varchar(45) DEFAULT NULL,
  `meta_name` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `definition_type` varchar(1) DEFAULT NULL,
  `metadata_type` varchar(1) DEFAULT NULL,
  `no_of_decimals` int(11) DEFAULT NULL,
  `is_required` binary(1) DEFAULT NULL,
  `min_range` decimal(15,4) DEFAULT NULL,
  `max_range` decimal(15,4) DEFAULT NULL,
  `entity` varchar(10) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `meta_group` varchar(2) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_index_org_key` (`organization_id`,`meta_key`),
  KEY `fk_org_metadata_organization1_idx` (`organization_id`),
  CONSTRAINT `fk_org_metadata_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_metadata`
--

LOCK TABLES `org_metadata` WRITE;
/*!40000 ALTER TABLE `org_metadata` DISABLE KEYS */;
INSERT INTO `org_metadata` VALUES (1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `org_metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_metadata_list_values`
--

DROP TABLE IF EXISTS `org_metadata_list_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_metadata_list_values` (
  `id` int(11) NOT NULL,
  `org_metadata_id` int(11) NOT NULL,
  `list_name` varchar(255) DEFAULT NULL,
  `list_value` varchar(255) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_org_metadata_list_values_org_metadata1_idx` (`org_metadata_id`),
  CONSTRAINT `fk_org_metadata_list_values_org_metadata1` FOREIGN KEY (`org_metadata_id`) REFERENCES `org_metadata` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_metadata_list_values`
--

LOCK TABLES `org_metadata_list_values` WRITE;
/*!40000 ALTER TABLE `org_metadata_list_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_metadata_list_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_permissionset`
--

DROP TABLE IF EXISTS `org_permissionset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_permissionset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `permissionset_name` varchar(1000) DEFAULT NULL,
  `is_archived` binary(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `accesslevel_ind_agg` tinyint(4) DEFAULT NULL,
  `accesslevel_agg` tinyint(4) DEFAULT NULL,
  `risk_indicator` tinyint(4) DEFAULT NULL,
  `intent_to_leave` tinyint(4) DEFAULT NULL,
  `view_courses` tinyint(4) DEFAULT NULL,
  `create_view_academic_update` tinyint(4) DEFAULT NULL,
  `view_all_academic_update_courses` tinyint(4) DEFAULT NULL,
  `view_all_final_grades` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_permissionset_organizationid` (`organization_id`),
  CONSTRAINT `fk_permissionset_organizationid` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='permissionset created by an organization';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_permissionset`
--

LOCK TABLES `org_permissionset` WRITE;
/*!40000 ALTER TABLE `org_permissionset` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_permissionset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_permissionset_datablock`
--

DROP TABLE IF EXISTS `org_permissionset_datablock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_permissionset_datablock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `org_permissionset_id` int(11) DEFAULT NULL,
  `datablock_id` int(11) DEFAULT NULL,
  `block_type` varchar(10) DEFAULT NULL,
  `timeframe_all` tinyint(1) DEFAULT NULL,
  `current_calendar` tinyint(1) DEFAULT NULL,
  `previous_period` tinyint(1) DEFAULT NULL,
  `next_period` tinyint(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_orgdashboarditem_organizationid` (`organization_id`),
  KEY `fk_orgdashboarditems_permissionset` (`org_permissionset_id`),
  CONSTRAINT `fk_orgdashboarditem_organizationid` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_orgdashboarditems_permissionset` FOREIGN KEY (`org_permissionset_id`) REFERENCES `org_permissionset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='dashboard items assigned to a permissionset';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_permissionset_datablock`
--

LOCK TABLES `org_permissionset_datablock` WRITE;
/*!40000 ALTER TABLE `org_permissionset_datablock` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_permissionset_datablock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_permissionset_features`
--

DROP TABLE IF EXISTS `org_permissionset_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_permissionset_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `org_permissionset_id` int(11) DEFAULT NULL,
  `feature_id` int(11) DEFAULT NULL,
  `private_create` tinyint(1) DEFAULT NULL,
  `public_create` tinyint(1) DEFAULT NULL,
  `public_view` tinyint(1) DEFAULT NULL,
  `team_view` tinyint(1) DEFAULT NULL,
  `team_create` tinyint(1) DEFAULT NULL,
  `receive_referral` tinyint(1) DEFAULT NULL,
  `timeframe_all` tinyint(1) DEFAULT NULL,
  `current_calendar` tinyint(1) DEFAULT NULL,
  `previous_period` tinyint(1) DEFAULT NULL,
  `next_period` tinyint(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_permissionfeature_permissionsetid` (`org_permissionset_id`),
  KEY `fk_permissionfeature_featureid` (`feature_id`),
  KEY `fk_permissionfeature_organizationid` (`organization_id`),
  CONSTRAINT `fk_permissionfeature_featureid` FOREIGN KEY (`feature_id`) REFERENCES `feature_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_permissionfeature_organizationid` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_permissionfeature_permissionsetid` FOREIGN KEY (`org_permissionset_id`) REFERENCES `org_permissionset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																				';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_permissionset_features`
--

LOCK TABLES `org_permissionset_features` WRITE;
/*!40000 ALTER TABLE `org_permissionset_features` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_permissionset_features` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_permissionset_metadata`
--

DROP TABLE IF EXISTS `org_permissionset_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_permissionset_metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `org_permissionset_id` int(11) NOT NULL,
  `org_metadata_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_org_permissionset_metadata_organization1_idx` (`organization_id`),
  KEY `fk_org_permissionset_metadata_org_permissionset1_idx` (`org_permissionset_id`),
  KEY `fk_org_permissionset_metadata_org_metadata1_idx` (`org_metadata_id`),
  CONSTRAINT `fk_org_permissionset_metadata_org_metadata1` FOREIGN KEY (`org_metadata_id`) REFERENCES `org_metadata` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_permissionset_metadata_org_permissionset1` FOREIGN KEY (`org_permissionset_id`) REFERENCES `org_permissionset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_permissionset_metadata_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_permissionset_metadata`
--

LOCK TABLES `org_permissionset_metadata` WRITE;
/*!40000 ALTER TABLE `org_permissionset_metadata` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_permissionset_metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_permissionset_question`
--

DROP TABLE IF EXISTS `org_permissionset_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_permissionset_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `org_permissionset_id` int(11) NOT NULL,
  `org_question_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_org_permissionset_question_organization1_idx` (`organization_id`),
  KEY `fk_org_permissionset_question_org_permissionset1_idx` (`org_permissionset_id`),
  KEY `fk_org_permissionset_question_org_question1_idx` (`org_question_id`),
  CONSTRAINT `fk_org_permissionset_question_org_permissionset1` FOREIGN KEY (`org_permissionset_id`) REFERENCES `org_permissionset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_permissionset_question_org_question1` FOREIGN KEY (`org_question_id`) REFERENCES `org_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_permissionset_question_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_permissionset_question`
--

LOCK TABLES `org_permissionset_question` WRITE;
/*!40000 ALTER TABLE `org_permissionset_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_permissionset_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_person_faculty`
--

DROP TABLE IF EXISTS `org_person_faculty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_person_faculty` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Master record values are the final values that have to be present for a person. Home record is used only for display in UI ',
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL COMMENT 'Status: possible values are : 0 (Inactive), 1/null (Active) and 2 (Conflict)',
  PRIMARY KEY (`id`),
  KEY `fk_org_person_faculty_organization1_idx` (`organization_id`),
  KEY `fk_org_person_faculty_person1_idx` (`person_id`),
  CONSTRAINT `fk_org_person_faculty_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_person_faculty_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_person_faculty`
--

LOCK TABLES `org_person_faculty` WRITE;
/*!40000 ALTER TABLE `org_person_faculty` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_person_faculty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_person_student`
--

DROP TABLE IF EXISTS `org_person_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_person_student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL COMMENT 'Master record values are the final values that have to be present for a person. Home record is used only for display in UI ',
  `person_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL COMMENT 'Status: possible values are : 0 (Inactive), 1/null (Active) and 2 (Conflict)',
  `photo_url` varchar(200) DEFAULT NULL,
  `receivesurvey` varchar(1) DEFAULT NULL,
  `surveycohort` varchar(45) DEFAULT NULL,
  `person_id_primary_connect` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_org_person_student_organization1_idx` (`organization_id`),
  KEY `fk_org_person_student_person1_idx` (`person_id`),
  KEY `fk_org_person_student_person2_idx` (`person_id_primary_connect`),
  CONSTRAINT `fk_org_person_student_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_person_student_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_person_student_person2` FOREIGN KEY (`person_id_primary_connect`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_person_student`
--

LOCK TABLES `org_person_student` WRITE;
/*!40000 ALTER TABLE `org_person_student` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_person_student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_question`
--

DROP TABLE IF EXISTS `org_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `question_key` varchar(45) DEFAULT NULL,
  `question_text` text,
  `question_type_id` varchar(4) NOT NULL,
  `question_category_id` int(11) NOT NULL,
  `external_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_org_question_organization1_idx` (`organization_id`),
  KEY `fk_org_question_question_type1_idx` (`question_type_id`),
  KEY `fk_org_question_question_category1_idx` (`question_category_id`),
  CONSTRAINT `fk_org_question_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_question_question_category1` FOREIGN KEY (`question_category_id`) REFERENCES `question_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_question_question_type1` FOREIGN KEY (`question_type_id`) REFERENCES `question_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_question`
--

LOCK TABLES `org_question` WRITE;
/*!40000 ALTER TABLE `org_question` DISABLE KEYS */;
INSERT INTO `org_question` VALUES (1,1,NULL,NULL,'1',1,NULL),(2,1,NULL,NULL,'1',1,NULL),(3,1,NULL,NULL,'1',1,NULL);
/*!40000 ALTER TABLE `org_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_question_options`
--

DROP TABLE IF EXISTS `org_question_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_question_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_question_id` int(11) NOT NULL,
  `option_name` varchar(45) DEFAULT NULL,
  `option_value` varchar(5000) DEFAULT NULL,
  `sequence` smallint(6) DEFAULT NULL,
  `organization_id` int(11) NOT NULL,
  `external_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_org_question_options_org_question1_idx` (`org_question_id`),
  KEY `fk_org_question_options_organization1_idx` (`organization_id`),
  CONSTRAINT `fk_org_question_options_org_question1` FOREIGN KEY (`org_question_id`) REFERENCES `org_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_question_options_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_question_options`
--

LOCK TABLES `org_question_options` WRITE;
/*!40000 ALTER TABLE `org_question_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_question_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_questions_response`
--

DROP TABLE IF EXISTS `org_questions_response`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_questions_response` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `org_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `org_academic_year_id` int(11) NOT NULL,
  `org_academic_terms_id` int(11) DEFAULT NULL,
  `org_questions_id` int(11) NOT NULL,
  `response_type` enum('decimal','char','charmax') DEFAULT NULL,
  `decimal_value` decimal(9,2) DEFAULT NULL,
  `char_value` varchar(500) DEFAULT NULL,
  `charmax_value` varchar(5000) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_survey_response_organization1` (`org_id`),
  KEY `fk_survey_response_person1_idx` (`person_id`),
  KEY `fk_survey_response_survey1_idx` (`survey_id`),
  KEY `fk_survey_response_org_questions1_idx` (`org_questions_id`),
  KEY `fk_survey_response_org_academic_year1_idx` (`org_academic_year_id`),
  KEY `fk_survey_response_org_academic_terms1_idx` (`org_academic_terms_id`),
  CONSTRAINT `fk_org_response_org_academic_terms1` FOREIGN KEY (`org_academic_terms_id`) REFERENCES `org_academic_terms` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_response_org_academic_year1` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_response_org_questions1` FOREIGN KEY (`org_questions_id`) REFERENCES `org_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_response_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_response_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_response_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_questions_response`
--

LOCK TABLES `org_questions_response` WRITE;
/*!40000 ALTER TABLE `org_questions_response` DISABLE KEYS */;
INSERT INTO `org_questions_response` VALUES (1,1,1,1,1,1,1,'decimal',2.00,NULL,NULL,'2015-02-16 00:00:00'),(2,1,1,1,1,1,2,'char',NULL,'abc',NULL,'2015-03-26 00:00:00'),(3,1,1,1,1,1,3,'decimal',12.00,NULL,NULL,NULL),(4,1,1,1,1,1,1,'decimal',2.00,NULL,NULL,'2015-03-19 00:00:00'),(5,1,2,1,1,1,1,'decimal',6.00,NULL,NULL,NULL),(6,1,2,1,1,1,2,'char',NULL,'def',NULL,'2015-04-01 00:00:00'),(7,1,2,1,1,1,2,'char',NULL,'xyz',NULL,'2015-03-15 00:00:00'),(8,1,1,1,1,1,1,'decimal',4.00,NULL,NULL,'2015-01-01 00:00:00');
/*!40000 ALTER TABLE `org_questions_response` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_report_permissions`
--

DROP TABLE IF EXISTS `org_report_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_report_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `org_permissionset_id` int(11) DEFAULT NULL,
  `report_id` int(11) DEFAULT NULL,
  `timeframe_all` tinyint(4) DEFAULT NULL,
  `current_calendar` tinyint(4) DEFAULT NULL,
  `previous_calendar` tinyint(4) DEFAULT NULL,
  `next_period` tinyint(4) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `org_report_permission.organization_id_idx` (`organization_id`),
  KEY `org_report_permission.permissionset_id_idx` (`org_permissionset_id`),
  KEY `org_report_permission.report_id_idx` (`report_id`),
  CONSTRAINT `fk_org_report_permission_organization_id` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_report_permission_permissionset_id` FOREIGN KEY (`org_permissionset_id`) REFERENCES `org_permissionset` (`organization_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_report_permission_report_id` FOREIGN KEY (`report_id`) REFERENCES `report_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_report_permissions`
--

LOCK TABLES `org_report_permissions` WRITE;
/*!40000 ALTER TABLE `org_report_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_report_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_risk_group`
--

DROP TABLE IF EXISTS `org_risk_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_risk_group` (
  `organization_id` int(11) NOT NULL,
  `risk_group_id` int(11) NOT NULL,
  PRIMARY KEY (`organization_id`,`risk_group_id`),
  KEY `fk_org_risk_group_organization1_idx` (`organization_id`),
  KEY `fk_org_risk_group_risk_group1_idx` (`risk_group_id`),
  CONSTRAINT `fk_org_risk_group_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_risk_group_risk_group1` FOREIGN KEY (`risk_group_id`) REFERENCES `risk_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_risk_group`
--

LOCK TABLES `org_risk_group` WRITE;
/*!40000 ALTER TABLE `org_risk_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_risk_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_risk_group_model`
--

DROP TABLE IF EXISTS `org_risk_group_model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_risk_group_model` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_id` int(11) NOT NULL,
  `risk_group_id` int(11) NOT NULL,
  `risk_model_id` int(11) DEFAULT NULL,
  `assignment_date` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_orgriskmodel_orgid` (`org_id`),
  KEY `fk_orgriskmodel_riskmodelid` (`risk_model_id`),
  KEY `fk_org_risk_group_model_risk_group1_idx` (`risk_group_id`),
  CONSTRAINT `fk_org_risk_group_model_risk_group1` FOREIGN KEY (`risk_group_id`) REFERENCES `risk_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_orgriskmodel_orgid` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_orgriskmodel_riskmodelid` FOREIGN KEY (`risk_model_id`) REFERENCES `risk_model_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_risk_group_model`
--

LOCK TABLES `org_risk_group_model` WRITE;
/*!40000 ALTER TABLE `org_risk_group_model` DISABLE KEYS */;
INSERT INTO `org_risk_group_model` VALUES (1,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `org_risk_group_model` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_riskval_calc_inputs`
--

DROP TABLE IF EXISTS `org_riskval_calc_inputs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_riskval_calc_inputs` (
  `org_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `is_riskval_calc_required` enum('y','n') DEFAULT 'n',
  PRIMARY KEY (`org_id`,`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_riskval_calc_inputs`
--

LOCK TABLES `org_riskval_calc_inputs` WRITE;
/*!40000 ALTER TABLE `org_riskval_calc_inputs` DISABLE KEYS */;
INSERT INTO `org_riskval_calc_inputs` VALUES (1,1,'y'),(1,2,'y'),(1,3,'y'),(1,4,'y');
/*!40000 ALTER TABLE `org_riskval_calc_inputs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_search`
--

DROP TABLE IF EXISTS `org_search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `query` varchar(5000) DEFAULT NULL,
  `json` varchar(3000) DEFAULT NULL,
  `edited_by_me` tinyint(1) DEFAULT NULL,
  `from_sharedtab` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_org_search_organization1_idx` (`organization_id`),
  KEY `fk_org_search_person1_idx` (`person_id`),
  CONSTRAINT `fk_org_search_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_search_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_search`
--

LOCK TABLES `org_search` WRITE;
/*!40000 ALTER TABLE `org_search` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_search` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_search_shared_by`
--

DROP TABLE IF EXISTS `org_search_shared_by`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_search_shared_by` (
  `org_search_id` int(11) NOT NULL,
  `person_id_shared_by` int(11) NOT NULL,
  `org_search_id_source` int(11) NOT NULL,
  `shared_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`org_search_id`,`person_id_shared_by`,`org_search_id_source`),
  KEY `fk_org_search_shared_by_org_search1_idx` (`org_search_id`),
  KEY `fk_org_search_shared_by_org_search2_idx` (`org_search_id_source`),
  KEY `fk_org_search_shared_by_person1_idx` (`person_id_shared_by`),
  CONSTRAINT `fk_org_search_shared_by_org_search1` FOREIGN KEY (`org_search_id`) REFERENCES `org_search` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_search_shared_by_org_search2` FOREIGN KEY (`org_search_id_source`) REFERENCES `org_search` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_search_shared_by_person1` FOREIGN KEY (`person_id_shared_by`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_search_shared_by`
--

LOCK TABLES `org_search_shared_by` WRITE;
/*!40000 ALTER TABLE `org_search_shared_by` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_search_shared_by` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_search_shared_with`
--

DROP TABLE IF EXISTS `org_search_shared_with`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_search_shared_with` (
  `org_search_id` int(11) NOT NULL,
  `person_id_sharedwith` int(11) NOT NULL,
  `org_search_id_dest` int(11) NOT NULL,
  `shared_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`org_search_id`,`person_id_sharedwith`,`org_search_id_dest`),
  KEY `fk_org_search_shared_org_search1_idx` (`org_search_id`),
  KEY `fk_org_search_shared_org_search2_idx` (`org_search_id_dest`),
  KEY `fk_org_search_shared_person2_idx` (`person_id_sharedwith`),
  CONSTRAINT `fk_org_search_shared_org_search1` FOREIGN KEY (`org_search_id`) REFERENCES `org_search` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_search_shared_org_search2` FOREIGN KEY (`org_search_id_dest`) REFERENCES `org_search` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_search_shared_person2` FOREIGN KEY (`person_id_sharedwith`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_search_shared_with`
--

LOCK TABLES `org_search_shared_with` WRITE;
/*!40000 ALTER TABLE `org_search_shared_with` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_search_shared_with` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_sites`
--

DROP TABLE IF EXISTS `org_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_sites` (
  `org_id` int(11) NOT NULL,
  `site_code` varchar(10) NOT NULL,
  `status` enum('ready','inprogress') DEFAULT NULL,
  `last_synchronized_on` timestamp NULL DEFAULT NULL,
  `person_id_requested_by` int(11) NOT NULL,
  PRIMARY KEY (`org_id`,`site_code`),
  KEY `fk_org_sites_sites1_idx` (`site_code`),
  KEY `fk_org_sites_person1_idx` (`person_id_requested_by`),
  CONSTRAINT `fk_org_sites_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_sites_person1` FOREIGN KEY (`person_id_requested_by`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_sites_sites1` FOREIGN KEY (`site_code`) REFERENCES `sites` (`site_code`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_sites`
--

LOCK TABLES `org_sites` WRITE;
/*!40000 ALTER TABLE `org_sites` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_static_list`
--

DROP TABLE IF EXISTS `org_static_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_static_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `name` varchar(300) NOT NULL,
  `description` varchar(2000) DEFAULT NULL,
  `person_id_shared_by` int(11) DEFAULT NULL,
  `shared_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_org_static_list_organization1_idx` (`org_id`),
  KEY `fk_org_static_list_person1_idx` (`person_id`),
  KEY `fk_org_static_list_person2_idx` (`person_id_shared_by`),
  CONSTRAINT `fk_org_static_list_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_static_list_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_static_list_person2` FOREIGN KEY (`person_id_shared_by`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_static_list`
--

LOCK TABLES `org_static_list` WRITE;
/*!40000 ALTER TABLE `org_static_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_static_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_static_list_students`
--

DROP TABLE IF EXISTS `org_static_list_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_static_list_students` (
  `org_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_static_list_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_org_static_list_students_org_static_list1_idx` (`org_static_list_id`),
  KEY `fk_org_static_list_students_person1_idx` (`person_id`),
  KEY `fk_org_static_list_students_organization1_idx` (`org_id`),
  CONSTRAINT `fk_org_static_list_students_org_static_list1` FOREIGN KEY (`org_static_list_id`) REFERENCES `org_static_list` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_static_list_students_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_static_list_students_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_static_list_students`
--

LOCK TABLES `org_static_list_students` WRITE;
/*!40000 ALTER TABLE `org_static_list_students` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_static_list_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_survey_report_access_history`
--

DROP TABLE IF EXISTS `org_survey_report_access_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_survey_report_access_history` (
  `org_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `year_id` varchar(10) NOT NULL,
  `cohort_code` int(11) NOT NULL,
  `last_accessed_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`org_id`,`person_id`,`survey_id`,`year_id`,`cohort_code`),
  KEY `fk_org_survey_report_access_history_organization1_idx` (`org_id`),
  KEY `fk_org_survey_report_access_history_person1_idx` (`person_id`),
  KEY `fk_org_survey_report_access_history_survey1_idx` (`survey_id`),
  KEY `fk_org_survey_report_access_history_year1_idx` (`year_id`),
  CONSTRAINT `fk_org_survey_report_access_history_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_survey_report_access_history_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_survey_report_access_history_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_org_survey_report_access_history_year1` FOREIGN KEY (`year_id`) REFERENCES `year` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_survey_report_access_history`
--

LOCK TABLES `org_survey_report_access_history` WRITE;
/*!40000 ALTER TABLE `org_survey_report_access_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_survey_report_access_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_users`
--

DROP TABLE IF EXISTS `org_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_organization_tier_users_organization1_idx` (`organization_id`),
  KEY `fk_organization_tier_users_person1_idx` (`person_id`),
  CONSTRAINT `fk_organization_tier_users_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organization_tier_users_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_users`
--

LOCK TABLES `org_users` WRITE;
/*!40000 ALTER TABLE `org_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organization`
--

DROP TABLE IF EXISTS `organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subdomain` varchar(45) DEFAULT NULL,
  `parent_organization_id` int(11) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  `time_zone` varchar(45) DEFAULT NULL,
  `website` varchar(500) DEFAULT NULL,
  `logo_file_name` varchar(500) DEFAULT NULL,
  `primary_color` varchar(45) DEFAULT NULL,
  `secondary_color` varchar(45) DEFAULT NULL,
  `ebi_confidentiality_statement` text,
  `custom_confidentiality_statement` text,
  `data_retention_days` int(11) DEFAULT NULL,
  `saas_tenant_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `inactivity_timeout` int(11) DEFAULT NULL,
  `academic_update_notification` tinyint(1) DEFAULT NULL,
  `refer_for_academic_assistance` tinyint(1) DEFAULT NULL,
  `send_to_student` tinyint(1) DEFAULT NULL,
  `ftp_user` varchar(45) DEFAULT NULL,
  `ftp_password` varchar(100) DEFAULT NULL,
  `ftp_home` varchar(200) DEFAULT NULL,
  `campus_id` varchar(10) DEFAULT NULL,
  `tier` enum('0','1','2','3') DEFAULT NULL,
  `external_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization`
--

LOCK TABLES `organization` WRITE;
/*!40000 ALTER TABLE `organization` DISABLE KEYS */;
INSERT INTO `organization` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `organization` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organization_lang`
--

DROP TABLE IF EXISTS `organization_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organization_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `organization_name` varchar(500) DEFAULT NULL,
  `nick_name` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_orglang_organizationid` (`organization_id`),
  KEY `fk_orglang_langid` (`lang_id`),
  CONSTRAINT `fk_orglang_langid` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_orglang_organizationid` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_lang`
--

LOCK TABLES `organization_lang` WRITE;
/*!40000 ALTER TABLE `organization_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `organization_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organization_role`
--

DROP TABLE IF EXISTS `organization_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organization_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_orgrole_organizationid` (`organization_id`),
  KEY `fk_orgrole_roleid` (`role_id`),
  KEY `fk_orgrole_personid` (`person_id`),
  CONSTRAINT `fk_orgrole_organizationid` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_orgrole_personid` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_orgrole_roleid` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_role`
--

LOCK TABLES `organization_role` WRITE;
/*!40000 ALTER TABLE `organization_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `organization_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `title` varchar(45) DEFAULT NULL,
  `date_of_birth` datetime DEFAULT NULL,
  `External_id` varchar(45) DEFAULT NULL,
  `organization_id` int(11) NOT NULL,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `confidentiality_stmt_accept_date` datetime DEFAULT NULL,
  `activation_token` varchar(100) DEFAULT NULL,
  `token_expiry_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `welcome_email_sent_date` datetime DEFAULT NULL,
  `risk_update_date` datetime DEFAULT NULL,
  `intent_to_leave` int(11) DEFAULT NULL,
  `intent_to_leave_update_date` datetime DEFAULT NULL,
  `risk_level` int(11) DEFAULT NULL,
  `last_contact_date` datetime DEFAULT NULL,
  `last_activity` varchar(255) DEFAULT NULL,
  `is_home_campus` varchar(1) DEFAULT NULL COMMENT 'is_home_campus: null/1 (default) : Home campus, ''0'' : NOT Home campus',
  PRIMARY KEY (`id`),
  KEY `fk_person_organization1_idx` (`organization_id`),
  KEY `fk_person_intent_to_leave1_idx` (`intent_to_leave`),
  KEY `fk_person_risk_level1_idx` (`risk_level`),
  CONSTRAINT `fk_person_intent_to_leave1` FOREIGN KEY (`intent_to_leave`) REFERENCES `intent_to_leave` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_risk_level1` FOREIGN KEY (`risk_level`) REFERENCES `risk_level` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person`
--

LOCK TABLES `person` WRITE;
/*!40000 ALTER TABLE `person` DISABLE KEYS */;
INSERT INTO `person` VALUES (1,'Kristopher','20151033N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(2,'Marissa','20151036N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(3,'Corey','20151039N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(4,'Steven','20151042N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(5,'Zachary','20151045N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL),(6,'Kelly','20151048N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL),(7,'Rebecca','20151051N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(8,'Katie','20151054N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(9,'Britt','20151057N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(10,'Bailey','20151060N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(11,'Brittany','20151063N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(12,'Alec','20151066N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(13,'Emily','20151069N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(14,'Holly','20151072N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(15,'Ethan','20151075N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(16,'Samantha','20151078N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(17,'Rebekah','20151081N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(18,'Christopher','20151084N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(19,'Summer','20151087N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL),(20,'Kaylyn','20151090N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(21,'Emily','20151093N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(22,'Kristina','20151096N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(23,'Denise','20151099N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(24,'Holly','20151102N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL),(25,'Angela','20151105N',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,NULL);
/*!40000 ALTER TABLE `person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_contact_info`
--

DROP TABLE IF EXISTS `person_contact_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_contact_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_personcontactinfo_personid` (`person_id`),
  KEY `fk_personcontactinfo_contactid` (`contact_id`),
  CONSTRAINT `fk_personcontactinfo_contactid` FOREIGN KEY (`contact_id`) REFERENCES `contact_info` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_personcontactinfo_personid` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_contact_info`
--

LOCK TABLES `person_contact_info` WRITE;
/*!40000 ALTER TABLE `person_contact_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_contact_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_ebi_metadata`
--

DROP TABLE IF EXISTS `person_ebi_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_ebi_metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `ebi_metadata_id` int(11) NOT NULL,
  `metadata_value` varchar(2000) DEFAULT NULL,
  `org_academic_year_id` int(11) DEFAULT NULL,
  `org_academic_terms_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `org_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_person_metadata_person1_idx` (`person_id`),
  KEY `fk_person_metadata_metadata_master1_idx` (`ebi_metadata_id`),
  KEY `fk_person_metadata_org_academic_year1_idx` (`org_academic_year_id`),
  KEY `fk_person_metadata_org_academic_periods1_idx` (`org_academic_terms_id`),
  KEY `fk_person_ebi_metadata_organization1_idx` (`org_id`),
  CONSTRAINT `fk_person_ebi_metadata_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_metadata_metadata_master1` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_metadata_org_academic_periods1` FOREIGN KEY (`org_academic_terms_id`) REFERENCES `org_academic_terms` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_metadata_org_academic_year1` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_metadata_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=365 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_ebi_metadata`
--

LOCK TABLES `person_ebi_metadata` WRITE;
/*!40000 ALTER TABLE `person_ebi_metadata` DISABLE KEYS */;
INSERT INTO `person_ebi_metadata` VALUES (1,1,9,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(2,1,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(3,1,6,'1994',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(4,1,11,'2013',NULL,NULL,NULL,NULL,NULL,'2015-03-11 00:00:00',NULL,NULL,1),(5,1,12,'Political Science',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(6,1,10,'3.36',NULL,NULL,NULL,NULL,NULL,'2015-01-20 00:00:00',NULL,NULL,1),(7,1,18,'2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(8,1,19,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(9,1,15,'490',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(10,1,16,'450',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(11,1,14,'470',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(12,1,7,'4',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(13,1,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(14,2,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(15,2,13,'5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(16,2,6,'1995',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(17,2,11,'2013',NULL,NULL,NULL,NULL,NULL,'2015-03-01 00:00:00',NULL,NULL,1),(18,2,12,'Biology',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(19,2,10,'4',NULL,NULL,NULL,NULL,NULL,'2015-02-04 00:00:00',NULL,NULL,1),(20,2,18,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(21,2,19,'5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(22,2,15,'550',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(23,2,16,'460',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(24,2,14,'690',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(25,2,7,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(26,2,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(27,3,9,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(28,3,13,'8',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(29,3,6,'1991',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(30,3,11,'2009',NULL,NULL,NULL,NULL,NULL,'2015-04-21 00:00:00',NULL,NULL,1),(31,3,12,'Biology',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(32,3,10,'2.79',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(33,3,18,'3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(34,3,19,'3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(35,3,15,'600',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(36,3,16,'620',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(37,3,14,'480',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(38,3,7,'6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(39,3,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(40,4,9,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(41,4,13,'8',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(42,4,6,'1994',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(43,4,11,'2013',NULL,NULL,NULL,NULL,NULL,'2015-06-01 00:00:00',NULL,NULL,1),(44,4,12,'Liberal Studies',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(45,4,10,'3.73',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(46,4,18,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(47,4,19,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(48,4,16,'560',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(49,4,14,'520',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(50,4,1,'25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(51,4,2,'24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(52,4,3,'24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(53,4,4,'29',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(54,4,5,'24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(55,4,7,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(56,4,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(57,5,9,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(58,5,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(59,5,6,'1995',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(60,5,11,'2013',NULL,NULL,NULL,NULL,NULL,'2015-03-15 00:00:00',NULL,NULL,1),(61,5,12,'Biology',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(62,5,10,'4',NULL,NULL,NULL,NULL,NULL,'2015-04-01 00:00:00',NULL,NULL,1),(63,5,18,'4',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(64,5,19,'2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(65,5,15,'580',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(66,5,16,'570',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(67,5,14,'570',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(68,5,7,'6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(69,5,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(70,6,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(71,6,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(72,6,6,'1995',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(73,6,11,'2013',NULL,NULL,NULL,NULL,NULL,'2015-02-01 00:00:00',NULL,NULL,1),(74,6,12,'Biology',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(75,6,10,'3.58',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(76,6,18,'8',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(77,6,19,'6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(78,6,15,'430',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(79,6,16,'540',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(80,6,14,'420',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(81,6,7,'3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(82,6,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(83,7,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(84,7,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(85,7,6,'1995',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(91,7,15,'590',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(92,7,16,'540',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(93,7,14,'650',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(94,7,1,'26',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(95,7,2,'29',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(96,7,3,'27',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(97,7,4,'19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(98,7,5,'27',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(99,7,7,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(100,7,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(101,8,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(102,8,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(103,8,6,'1995',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(109,8,1,'20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(110,8,2,'22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(111,8,3,'16',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(112,8,4,'21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(113,8,5,'19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(114,8,7,'5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(115,8,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(116,9,9,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(117,9,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(118,9,6,'1994',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(124,9,1,'23',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(125,9,2,'23',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(126,9,3,'21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(127,9,4,'21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(128,9,5,'25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(129,9,7,'6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(130,9,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(131,10,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(132,10,13,'5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(133,10,6,'1994',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(139,10,15,'420',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(140,10,16,'360',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(141,10,14,'410',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(142,10,7,'3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(143,10,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(144,11,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(145,11,13,'8',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(146,11,6,'1995',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(152,11,15,'450',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(153,11,16,'520',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(154,11,14,'510',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(155,11,7,'4',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(156,11,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(157,12,9,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(158,12,13,'5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(159,12,6,'1995',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(165,12,1,'20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(166,12,2,'22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(167,12,3,'20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(168,12,4,'19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(169,12,5,'18',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(170,12,7,'5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(171,12,17,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(172,13,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(173,13,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(174,13,6,'1995',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(180,13,16,'490',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(181,13,14,'440',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(182,13,1,'20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(183,13,2,'20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(184,13,3,'20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(185,13,4,'23',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(186,13,5,'18',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(187,13,7,'9',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(188,13,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(189,14,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(190,14,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(191,14,6,'1993',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(197,14,15,'350',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(198,14,16,'270',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(199,14,14,'410',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(200,14,7,'2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(201,14,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(202,15,9,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(203,15,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(204,15,6,'1994',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(210,15,1,'21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(211,15,2,'19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(212,15,3,'22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(213,15,4,'23',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(214,15,5,'19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(215,15,7,'5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(216,15,17,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(217,16,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(218,16,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(219,16,6,'1994',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(225,16,15,'580',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(226,16,16,'450',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(227,16,14,'540',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(228,16,7,'6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(229,16,17,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(230,17,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(231,17,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(232,17,6,'1989',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(238,17,1,'28',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(239,17,2,'31',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(240,17,3,'24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(241,17,5,'28',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(242,17,7,'8',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(243,17,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(244,18,9,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(245,18,13,'5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(246,18,6,'1994',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(252,18,1,'14',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(253,18,2,'16',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(254,18,3,'17',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(255,18,4,'12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(256,18,5,'11',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(257,18,7,'2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(258,18,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(259,19,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(260,19,13,'5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(261,19,6,'1994',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(267,19,1,'15',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(268,19,2,'15',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(269,19,3,'17',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(270,19,4,'13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(271,19,5,'15',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(272,19,7,'3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(273,19,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(274,20,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(275,20,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(276,20,6,'1994',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(282,20,15,'470',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(283,20,16,'460',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(284,20,14,'470',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(285,20,1,'19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(286,20,2,'20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(287,20,3,'18',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(288,20,4,'18',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(289,20,5,'19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(290,20,7,'4',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(291,20,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(292,21,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(293,21,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(294,21,6,'1995',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(300,21,15,'500',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(301,21,16,'430',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(302,21,14,'560',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(303,21,1,'24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(304,21,2,'21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(305,21,3,'27',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(306,21,4,'22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(307,21,5,'25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(308,21,7,'6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(309,21,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(310,22,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(311,22,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(312,22,6,'1995',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(318,22,1,'24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(319,22,2,'25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(320,22,3,'24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(321,22,5,'25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(322,22,7,'6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(323,22,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(324,23,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(325,23,13,'7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(326,23,6,'1994',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(332,23,15,'390',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(333,23,16,'310',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(334,23,14,'490',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(335,23,7,'4',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(336,23,17,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(337,24,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(338,24,13,'2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(339,24,6,'1995',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(345,24,15,'450',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(346,24,16,'410',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(347,24,14,'410',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(348,24,7,'3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(349,24,17,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(350,25,9,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(351,25,13,'4',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(352,25,6,'1994',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(358,25,15,'340',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(359,25,16,'380',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(360,25,14,'430',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(361,25,7,'3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(362,25,17,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(363,1,10,'3.21',NULL,NULL,NULL,NULL,NULL,'2015-03-16 00:00:00',NULL,NULL,1),(364,2,11,'2012',NULL,NULL,NULL,NULL,NULL,'2015-03-26 00:00:00',NULL,NULL,1);
/*!40000 ALTER TABLE `person_ebi_metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_org_metadata`
--

DROP TABLE IF EXISTS `person_org_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_org_metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `org_metadata_id` int(11) NOT NULL,
  `metadata_value` varchar(2000) DEFAULT NULL,
  `org_academic_year_id` int(11) NOT NULL,
  `org_academic_periods_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `org_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_person_org_metadata_person1_idx` (`person_id`),
  KEY `fk_person_org_metadata_org_metadata1_idx` (`org_metadata_id`),
  KEY `fk_person_org_metadata_org_academic_year1_idx` (`org_academic_year_id`),
  KEY `fk_person_org_metadata_org_academic_periods1_idx` (`org_academic_periods_id`),
  KEY `fk_person_org_metadata_organization1_idx` (`org_id`),
  CONSTRAINT `fk_person_org_metadata_org_academic_periods1` FOREIGN KEY (`org_academic_periods_id`) REFERENCES `org_academic_terms` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_org_metadata_org_academic_year1` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_org_metadata_org_metadata1` FOREIGN KEY (`org_metadata_id`) REFERENCES `org_metadata` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_org_metadata_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_org_metadata_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_org_metadata`
--

LOCK TABLES `person_org_metadata` WRITE;
/*!40000 ALTER TABLE `person_org_metadata` DISABLE KEYS */;
INSERT INTO `person_org_metadata` VALUES (1,3,1,'2',1,1,NULL,NULL,NULL,'2015-01-25 00:00:00',NULL,NULL,1),(2,3,1,'3',1,1,NULL,NULL,NULL,'2015-02-15 00:00:00',NULL,NULL,1),(3,6,3,'6',1,1,NULL,NULL,NULL,NULL,NULL,NULL,1),(4,5,3,'4',1,1,NULL,NULL,NULL,NULL,NULL,NULL,1),(5,6,2,'5.0',1,1,NULL,NULL,NULL,NULL,NULL,NULL,1),(6,5,2,'2.31',1,1,NULL,NULL,NULL,NULL,NULL,NULL,1),(7,2,4,'IIT',1,1,NULL,NULL,NULL,NULL,NULL,NULL,1),(8,3,4,'NIT',1,1,NULL,NULL,NULL,NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `person_org_metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_risk_level_history`
--

DROP TABLE IF EXISTS `person_risk_level_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_risk_level_history` (
  `person_id` int(11) NOT NULL,
  `date_captured` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `risk_model_id` int(11) NOT NULL,
  `risk_level` int(11) NOT NULL,
  `risk_score` decimal(6,4) DEFAULT NULL,
  `weighted_value` decimal(9,4) DEFAULT NULL,
  `maximum_weight_value` decimal(9,4) DEFAULT NULL,
  PRIMARY KEY (`person_id`,`date_captured`),
  KEY `fk_person_risk_level_history_person1_idx` (`person_id`),
  KEY `fk_person_risk_level_history_risk_model_master1_idx` (`risk_model_id`),
  KEY `fk_person_risk_level_history_risk_level1_idx` (`risk_level`),
  CONSTRAINT `fk_person_risk_level_history_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_risk_level_history_risk_level1` FOREIGN KEY (`risk_level`) REFERENCES `risk_level` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_risk_level_history_risk_model_master1` FOREIGN KEY (`risk_model_id`) REFERENCES `risk_model_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_risk_level_history`
--

LOCK TABLES `person_risk_level_history` WRITE;
/*!40000 ALTER TABLE `person_risk_level_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_risk_level_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!50001 DROP VIEW IF EXISTS `profile`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `profile` (
  `risk_model_id` tinyint NOT NULL,
  `source` tinyint NOT NULL,
  `risk_variable_id` tinyint NOT NULL,
  `variable_type` tinyint NOT NULL,
  `weight` tinyint NOT NULL,
  `ebi_metadata_id` tinyint NOT NULL,
  `metadata_value` tinyint NOT NULL,
  `bucket_value` tinyint NOT NULL,
  `person_id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `proxy_audit`
--

DROP TABLE IF EXISTS `proxy_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proxy_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proxy_log_id` int(11) NOT NULL,
  `action` enum('insert','update','delete') DEFAULT NULL,
  `resource` varchar(45) DEFAULT NULL,
  `json_text_old` varchar(4000) DEFAULT NULL,
  `json_text_new` varchar(4000) DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_proxy_audit_proxy_log1_idx` (`proxy_log_id`),
  CONSTRAINT `fk_proxy_audit_proxy_log1` FOREIGN KEY (`proxy_log_id`) REFERENCES `proxy_log` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proxy_audit`
--

LOCK TABLES `proxy_audit` WRITE;
/*!40000 ALTER TABLE `proxy_audit` DISABLE KEYS */;
/*!40000 ALTER TABLE `proxy_audit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proxy_log`
--

DROP TABLE IF EXISTS `proxy_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proxy_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `ebi_users_id` int(11) NOT NULL,
  `person_id_proxied_for` int(11) NOT NULL,
  `login_date_time` timestamp NULL DEFAULT NULL,
  `logoff_date_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_proxy_log_organization1_idx` (`organization_id`),
  KEY `fk_proxy_log_person1_idx` (`person_id`),
  KEY `fk_proxy_log_person2_idx` (`person_id_proxied_for`),
  KEY `fk_proxy_log_ebi_users1_idx` (`ebi_users_id`),
  CONSTRAINT `fk_proxy_log_ebi_users1` FOREIGN KEY (`ebi_users_id`) REFERENCES `ebi_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_proxy_log_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_proxy_log_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_proxy_log_person2` FOREIGN KEY (`person_id_proxied_for`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proxy_log`
--

LOCK TABLES `proxy_log` WRITE;
/*!40000 ALTER TABLE `proxy_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `proxy_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_category`
--

DROP TABLE IF EXISTS `question_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_category`
--

LOCK TABLES `question_category` WRITE;
/*!40000 ALTER TABLE `question_category` DISABLE KEYS */;
INSERT INTO `question_category` VALUES (1);
/*!40000 ALTER TABLE `question_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_category_lang`
--

DROP TABLE IF EXISTS `question_category_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_category_lang` (
  `question_category_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`question_category_id`,`lang_id`),
  KEY `fk_question_category_lang_question_category1_idx` (`question_category_id`),
  KEY `fk_question_category_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `fk_question_category_lang_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_question_category_lang_question_category1` FOREIGN KEY (`question_category_id`) REFERENCES `question_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_category_lang`
--

LOCK TABLES `question_category_lang` WRITE;
/*!40000 ALTER TABLE `question_category_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_category_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_type`
--

DROP TABLE IF EXISTS `question_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_type` (
  `id` varchar(4) NOT NULL,
  `type` enum('categorical','scaled','openended','numeric') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_type`
--

LOCK TABLES `question_type` WRITE;
/*!40000 ALTER TABLE `question_type` DISABLE KEYS */;
INSERT INTO `question_type` VALUES ('1',NULL);
/*!40000 ALTER TABLE `question_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_type_lang`
--

DROP TABLE IF EXISTS `question_type_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_type_lang` (
  `question_type_id` varchar(4) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`question_type_id`,`lang_id`),
  KEY `fk_question_types_lang_question_types1_idx` (`question_type_id`),
  KEY `fk_question_type_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `fk_question_type_lang_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_question_types_lang_question_types1` FOREIGN KEY (`question_type_id`) REFERENCES `question_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_type_lang`
--

LOCK TABLES `question_type_lang` WRITE;
/*!40000 ALTER TABLE `question_type_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_type_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referral_routing_rules`
--

DROP TABLE IF EXISTS `referral_routing_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referral_routing_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_category_id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) DEFAULT NULL,
  `is_primary_coordinator` tinyint(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_activity_reference_unassigned_activity_reference1_idx` (`activity_category_id`),
  KEY `fk_activity_reference_unassigned_organization1_idx` (`organization_id`),
  KEY `fk_activity_reference_unassigned_person1_idx` (`person_id`),
  CONSTRAINT `fk_activity_reference_unassigned_activity_reference1` FOREIGN KEY (`activity_category_id`) REFERENCES `activity_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_reference_unassigned_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_reference_unassigned_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referral_routing_rules`
--

LOCK TABLES `referral_routing_rules` WRITE;
/*!40000 ALTER TABLE `referral_routing_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `referral_routing_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referrals`
--

DROP TABLE IF EXISTS `referrals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referrals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id_student` int(11) NOT NULL,
  `person_id_faculty` int(11) NOT NULL,
  `activity_category_id` int(11) NOT NULL,
  `person_id_assigned_to` int(11) DEFAULT NULL,
  `note` text,
  `status` varchar(1) DEFAULT NULL,
  `is_leaving` binary(1) DEFAULT NULL,
  `is_discussed` binary(1) DEFAULT NULL,
  `referrer_permission` binary(1) DEFAULT NULL,
  `is_high_priority` binary(1) DEFAULT NULL,
  `notify_student` binary(1) DEFAULT NULL,
  `referral_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `access_private` tinyint(4) DEFAULT NULL,
  `access_public` tinyint(4) DEFAULT NULL,
  `access_team` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_referrals_organization1_idx` (`organization_id`),
  KEY `fk_referrals_person1_idx` (`person_id_student`),
  KEY `fk_referrals_person2_idx` (`person_id_faculty`),
  KEY `fk_referrals_activity_reference1_idx` (`activity_category_id`),
  KEY `fk_referrals_person3_idx` (`person_id_assigned_to`),
  CONSTRAINT `fk_referrals_activity_reference1` FOREIGN KEY (`activity_category_id`) REFERENCES `activity_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_referrals_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_referrals_person1` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_referrals_person2` FOREIGN KEY (`person_id_faculty`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_referrals_person3` FOREIGN KEY (`person_id_assigned_to`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referrals`
--

LOCK TABLES `referrals` WRITE;
/*!40000 ALTER TABLE `referrals` DISABLE KEYS */;
/*!40000 ALTER TABLE `referrals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referrals_interested_parties`
--

DROP TABLE IF EXISTS `referrals_interested_parties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referrals_interested_parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referrals_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_referrals_interested_parties_referrals1_idx` (`referrals_id`),
  KEY `fk_referrals_interested_parties_person1_idx` (`person_id`),
  CONSTRAINT `fk_referrals_interested_parties_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_referrals_interested_parties_referrals1` FOREIGN KEY (`referrals_id`) REFERENCES `referrals` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referrals_interested_parties`
--

LOCK TABLES `referrals_interested_parties` WRITE;
/*!40000 ALTER TABLE `referrals_interested_parties` DISABLE KEYS */;
/*!40000 ALTER TABLE `referrals_interested_parties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referrals_teams`
--

DROP TABLE IF EXISTS `referrals_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referrals_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referrals_id` int(11) NOT NULL,
  `teams_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_referrals_teams_referrals1_idx` (`referrals_id`),
  KEY `fk_referrals_teams_Teams1_idx` (`teams_id`),
  CONSTRAINT `fk_referrals_teams_Teams1` FOREIGN KEY (`teams_id`) REFERENCES `teams` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_referrals_teams_referrals1` FOREIGN KEY (`referrals_id`) REFERENCES `referrals` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referrals_teams`
--

LOCK TABLES `referrals_teams` WRITE;
/*!40000 ALTER TABLE `referrals_teams` DISABLE KEYS */;
/*!40000 ALTER TABLE `referrals_teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `refreshtoken`
--

DROP TABLE IF EXISTS `refreshtoken`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refreshtoken` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expires_at` int(11) DEFAULT NULL,
  `scope` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_7142379E5F37A13B` (`token`),
  KEY `IDX_7142379E19EB6921` (`client_id`),
  KEY `IDX_7142379EA76ED395` (`user_id`),
  CONSTRAINT `FK_7142379E19EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  CONSTRAINT `FK_7142379EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refreshtoken`
--

LOCK TABLES `refreshtoken` WRITE;
/*!40000 ALTER TABLE `refreshtoken` DISABLE KEYS */;
/*!40000 ALTER TABLE `refreshtoken` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `related_activities`
--

DROP TABLE IF EXISTS `related_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `related_activities` (
  `organization_id` int(11) NOT NULL,
  `activity_log_id` int(11) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `contacts_id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  KEY `fk_activities_related_activity_log1_idx` (`activity_log_id`),
  KEY `fk_activities_related_organization1_idx` (`organization_id`),
  KEY `fk_activities_related_contacts1_idx` (`contacts_id`),
  KEY `fk_activities_related_note1_idx` (`note_id`),
  CONSTRAINT `fk_activities_related_activity_log1` FOREIGN KEY (`activity_log_id`) REFERENCES `activity_log` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activities_related_contacts1` FOREIGN KEY (`contacts_id`) REFERENCES `contacts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activities_related_note1` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activities_related_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `related_activities`
--

LOCK TABLES `related_activities` WRITE;
/*!40000 ALTER TABLE `related_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `related_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_master`
--

DROP TABLE IF EXISTS `report_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reporting_group` varchar(100) DEFAULT NULL,
  `report_name` varchar(100) DEFAULT NULL,
  `physical_path` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_master`
--

LOCK TABLES `report_master` WRITE;
/*!40000 ALTER TABLE `report_master` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_master` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_group`
--

DROP TABLE IF EXISTS `risk_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_group_key` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_group`
--

LOCK TABLES `risk_group` WRITE;
/*!40000 ALTER TABLE `risk_group` DISABLE KEYS */;
INSERT INTO `risk_group` VALUES (1,'aa'),(2,'bb'),(3,'cc'),(4,'dd'),(5,'ee'),(6,'ff');
/*!40000 ALTER TABLE `risk_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_group_lang`
--

DROP TABLE IF EXISTS `risk_group_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_group_lang` (
  `risk_group_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `description` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`risk_group_id`,`lang_id`),
  KEY `fk_risk_group_lang_risk_group1_idx` (`risk_group_id`),
  KEY `fk_risk_group_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `fk_risk_group_lang_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_risk_group_lang_risk_group1` FOREIGN KEY (`risk_group_id`) REFERENCES `risk_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_group_lang`
--

LOCK TABLES `risk_group_lang` WRITE;
/*!40000 ALTER TABLE `risk_group_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_group_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_group_person_history`
--

DROP TABLE IF EXISTS `risk_group_person_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_group_person_history` (
  `person_id` int(11) NOT NULL,
  `risk_group_id` int(11) NOT NULL,
  `assignment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`person_id`,`risk_group_id`,`assignment_date`),
  KEY `fk_risk_group_person_history_person1_idx` (`person_id`),
  KEY `fk_risk_group_person_history_risk_group1_idx` (`risk_group_id`),
  CONSTRAINT `fk_risk_group_person_history_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_risk_group_person_history_risk_group1` FOREIGN KEY (`risk_group_id`) REFERENCES `risk_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_group_person_history`
--

LOCK TABLES `risk_group_person_history` WRITE;
/*!40000 ALTER TABLE `risk_group_person_history` DISABLE KEYS */;
INSERT INTO `risk_group_person_history` VALUES (1,1,'2015-05-22 11:19:06'),(2,1,'2015-06-01 08:47:30'),(3,1,'2015-06-01 10:21:55'),(4,1,'2015-06-01 10:21:55'),(5,1,'2015-06-01 10:21:55'),(6,1,'2015-06-01 10:21:55');
/*!40000 ALTER TABLE `risk_group_person_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_level`
--

DROP TABLE IF EXISTS `risk_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_text` varchar(10) DEFAULT NULL,
  `image_name` varchar(200) DEFAULT NULL,
  `color_hex` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_level`
--

LOCK TABLES `risk_level` WRITE;
/*!40000 ALTER TABLE `risk_level` DISABLE KEYS */;
INSERT INTO `risk_level` VALUES (1,'green','green','#00ff00'),(2,'yellow','yellow','#ffff00'),(3,'red','red','#ff4444'),(4,'red2','red2','#ff0000');
/*!40000 ALTER TABLE `risk_level` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_model_levels`
--

DROP TABLE IF EXISTS `risk_model_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_model_levels` (
  `risk_model_id` int(11) NOT NULL,
  `risk_level` int(11) NOT NULL,
  `min` decimal(6,4) DEFAULT NULL,
  `max` decimal(6,4) DEFAULT NULL,
  PRIMARY KEY (`risk_model_id`,`risk_level`),
  KEY `fk_risk_model_levels_risk_model_master1_idx` (`risk_model_id`),
  KEY `fk_risk_model_levels_risk_level1_idx` (`risk_level`),
  CONSTRAINT `fk_risk_model_levels_risk_level1` FOREIGN KEY (`risk_level`) REFERENCES `risk_level` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_risk_model_levels_risk_model_master1` FOREIGN KEY (`risk_model_id`) REFERENCES `risk_model_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_model_levels`
--

LOCK TABLES `risk_model_levels` WRITE;
/*!40000 ALTER TABLE `risk_model_levels` DISABLE KEYS */;
INSERT INTO `risk_model_levels` VALUES (1,1,5.2500,7.0000),(1,2,4.0000,5.2490),(1,3,2.5000,3.9990),(1,4,1.0000,2.4990);
/*!40000 ALTER TABLE `risk_model_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_model_master`
--

DROP TABLE IF EXISTS `risk_model_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_model_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `calculation_start_date` timestamp NULL DEFAULT NULL,
  `calculation_end_date` timestamp NULL DEFAULT NULL,
  `model_state` enum('Archived','Assigned','Unassigned','InProcess') DEFAULT NULL,
  `enrollment_date` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_model_master`
--

LOCK TABLES `risk_model_master` WRITE;
/*!40000 ALTER TABLE `risk_model_master` DISABLE KEYS */;
INSERT INTO `risk_model_master` VALUES (1,'iceCreamSandwich',NULL,NULL,'Assigned','2015-05-31 18:30:00',1,'2015-01-01 00:00:00',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `risk_model_master` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_model_weights`
--

DROP TABLE IF EXISTS `risk_model_weights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_model_weights` (
  `risk_model_id` int(11) NOT NULL,
  `risk_variable_id` int(11) NOT NULL,
  `weight` decimal(8,4) DEFAULT NULL,
  PRIMARY KEY (`risk_model_id`,`risk_variable_id`),
  KEY `fk_risk_model_bucket_risk_model_master1_idx` (`risk_model_id`),
  KEY `fk_risk_model_bucket_risk_variable1_idx` (`risk_variable_id`),
  CONSTRAINT `fk_risk_model_bucket_risk_model_master1` FOREIGN KEY (`risk_model_id`) REFERENCES `risk_model_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_risk_model_bucket_risk_variable1` FOREIGN KEY (`risk_variable_id`) REFERENCES `risk_variable` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_model_weights`
--

LOCK TABLES `risk_model_weights` WRITE;
/*!40000 ALTER TABLE `risk_model_weights` DISABLE KEYS */;
INSERT INTO `risk_model_weights` VALUES (1,1,200.0000),(1,2,100.0000),(1,3,50.0000),(1,4,250.0000),(1,5,125.0000),(1,6,1500.0000),(1,7,1300.0000),(1,8,1200.0000),(1,9,600.0000),(1,10,400.0000),(1,11,200.0000),(1,12,250.0000),(1,13,185.0000),(1,14,75.0000),(1,15,50.0000),(1,16,90.0000),(1,17,100.0000),(1,18,150.0000),(1,19,125.0000),(1,20,50.0000),(1,21,45.0000);
/*!40000 ALTER TABLE `risk_model_weights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_variable`
--

DROP TABLE IF EXISTS `risk_variable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_variable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_b_variable` varchar(100) DEFAULT NULL,
  `variable_type` enum('continuous','categorical') DEFAULT NULL,
  `ebi_metadata_id` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `org_metadata_id` int(11) DEFAULT NULL,
  `org_question_id` int(11) DEFAULT NULL,
  `ebi_question_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `survey_questions_id` int(11) DEFAULT NULL,
  `factor_id` int(11) DEFAULT NULL,
  `is_calculated` tinyint(1) DEFAULT NULL,
  `calc_type` enum('Most Recent','Sum','Average','Count','Academic Update') DEFAULT NULL,
  `calculation_start_date` timestamp NULL DEFAULT NULL,
  `calculation_end_date` timestamp NULL DEFAULT NULL,
  `is_archived` tinyint(1) DEFAULT NULL,
  `source` enum('profile','surveyquestion','surveyfactor','isp','isq','questionbank') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_risk_variable_ebi_metadata1_idx` (`ebi_metadata_id`),
  KEY `fk_risk_variable_org_metadata1_idx` (`org_metadata_id`),
  KEY `fk_risk_variable_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_risk_variable_survey1_idx` (`survey_id`),
  KEY `fk_risk_variable_organization1_idx` (`org_id`),
  KEY `fk_risk_variable_org_question1_idx` (`org_question_id`),
  KEY `fk_risk_variable_survey_questions1_idx` (`survey_questions_id`),
  KEY `fk_risk_variable_factor1_idx` (`factor_id`),
  CONSTRAINT `fk_risk_variable_ebi_metadata1` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_risk_variable_ebi_question1` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_risk_variable_factor1` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_risk_variable_org_metadata1` FOREIGN KEY (`org_metadata_id`) REFERENCES `org_metadata` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_risk_variable_org_question1` FOREIGN KEY (`org_question_id`) REFERENCES `org_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_risk_variable_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_risk_variable_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_risk_variable_survey_questions1` FOREIGN KEY (`survey_questions_id`) REFERENCES `survey_questions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_variable`
--

LOCK TABLES `risk_variable` WRITE;
/*!40000 ALTER TABLE `risk_variable` DISABLE KEYS */;
INSERT INTO `risk_variable` VALUES (1,'C_HSGradYear','continuous',11,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'Average','2015-01-09 18:30:00','2015-05-31 18:30:00',NULL,'profile'),(2,'C_HSGPA','continuous',10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'Academic Update','2014-12-31 18:30:00','2015-04-30 18:30:00',NULL,'profile'),(3,'C_NumHS','continuous',18,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'profile'),(4,'C_OnCampus','continuous',19,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'profile'),(5,'C_OffCampus',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,'C_Q004_1',NULL,NULL,NULL,NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'C_Q004_2','continuous',NULL,NULL,1,NULL,1,1,NULL,NULL,1,'Sum','2014-12-31 18:30:00','2015-03-31 18:30:00',NULL,'isp'),(8,'C_Q004_3','continuous',NULL,NULL,2,NULL,1,1,NULL,NULL,0,NULL,NULL,NULL,NULL,'isp'),(9,'C_Q004_4','continuous',NULL,NULL,NULL,1,1,1,NULL,NULL,1,'Sum','2015-02-04 18:30:00','2015-04-19 18:30:00',NULL,'isq'),(10,'C_Q004_5','categorical',NULL,NULL,NULL,2,1,1,NULL,NULL,1,'Count','2015-03-09 18:30:00','2015-05-14 18:30:00',NULL,'isq'),(11,'C_Q004_6','continuous',NULL,NULL,NULL,3,1,1,NULL,NULL,0,NULL,NULL,NULL,NULL,'isq'),(12,'C_Q004_7',NULL,NULL,NULL,NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,'C_Q005','continuous',NULL,NULL,3,NULL,2,1,NULL,NULL,0,NULL,NULL,NULL,NULL,'isp'),(14,'C_Q011','categorical',NULL,NULL,4,NULL,3,1,NULL,NULL,0,NULL,NULL,NULL,NULL,'isp'),(15,'C_Q012',NULL,NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,'C_Q013','continuous',NULL,NULL,NULL,NULL,5,1,1,NULL,0,NULL,NULL,NULL,NULL,'surveyquestion'),(17,'C_Q014','categorical',NULL,NULL,NULL,NULL,6,1,2,NULL,1,'Count','2014-12-31 18:30:00','2015-05-31 18:30:00',NULL,'surveyquestion'),(18,'C_Q015','categorical',NULL,NULL,NULL,NULL,7,1,3,NULL,0,NULL,NULL,NULL,NULL,'surveyquestion'),(19,'C_Q016','categorical',NULL,NULL,NULL,NULL,8,1,4,NULL,0,NULL,NULL,NULL,NULL,'surveyquestion'),(20,'C_Q017','continuous',NULL,NULL,NULL,NULL,9,1,5,NULL,1,'Academic Update','2015-01-20 18:30:00','2015-05-30 18:30:00',NULL,'surveyquestion'),(21,'C_Major','categorical',12,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'profile');
/*!40000 ALTER TABLE `risk_variable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_variable_category`
--

DROP TABLE IF EXISTS `risk_variable_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_variable_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_variable_id` int(11) DEFAULT NULL,
  `bucket_value` int(11) DEFAULT NULL,
  `option_value` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_risk_model_bucket_category_risk_variable1_idx` (`risk_variable_id`),
  CONSTRAINT `fk_risk_model_bucket_category_risk_variable1` FOREIGN KEY (`risk_variable_id`) REFERENCES `risk_variable` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_variable_category`
--

LOCK TABLES `risk_variable_category` WRITE;
/*!40000 ALTER TABLE `risk_variable_category` DISABLE KEYS */;
INSERT INTO `risk_variable_category` VALUES (1,21,3,'Chemistry'),(2,21,2,'Biology'),(3,21,1,'Undecided'),(4,14,4,'IIT'),(5,10,2,'def'),(6,18,3,'abcdef'),(7,19,2,'xyz');
/*!40000 ALTER TABLE `risk_variable_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_variable_range`
--

DROP TABLE IF EXISTS `risk_variable_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_variable_range` (
  `risk_variable_id` int(11) NOT NULL,
  `bucket_value` int(11) NOT NULL,
  `min` decimal(6,4) DEFAULT NULL,
  `max` decimal(6,4) DEFAULT NULL,
  PRIMARY KEY (`risk_variable_id`,`bucket_value`),
  KEY `fk_risk_model_bucket_range_risk_variable1_idx` (`risk_variable_id`),
  CONSTRAINT `fk_risk_model_bucket_range_risk_variable1` FOREIGN KEY (`risk_variable_id`) REFERENCES `risk_variable` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_variable_range`
--

LOCK TABLES `risk_variable_range` WRITE;
/*!40000 ALTER TABLE `risk_variable_range` DISABLE KEYS */;
INSERT INTO `risk_variable_range` VALUES (2,2,0.0000,1.9990),(2,3,2.0000,2.4990),(2,4,2.5000,3.4990),(2,5,3.5000,6.0000),(3,3,2.0000,4.0000),(3,4,1.0000,1.0000),(3,5,0.0000,0.0000),(4,5,0.0000,0.0000),(5,4,1.0000,1.0000),(6,1,1.0000,1.0000),(7,2,2.0000,2.2000),(7,3,2.3000,5.0000),(8,3,3.0000,3.0000),(9,4,7.0000,8.0000),(10,5,5.0000,5.0000),(11,6,6.0000,6.0000),(12,7,7.0000,7.0000),(13,1,1.0000,1.0000),(13,2,2.0000,2.0000),(13,3,3.0000,3.0000),(13,4,4.0000,4.0000),(13,5,5.0000,5.0000),(13,6,6.0000,6.0000),(13,7,7.0000,7.0000),(14,2,1.0000,2.9990),(14,3,3.0000,4.9990),(14,4,5.0000,5.9990),(14,5,6.0000,7.0000),(15,3,1.0000,4.9990),(15,4,5.0000,5.9990),(15,5,6.0000,7.0000),(16,3,1.0000,4.9990),(16,4,5.0000,5.9990),(16,5,6.0000,7.0000),(17,3,1.0000,4.9990),(17,4,5.0000,5.9990),(17,5,6.0000,7.0000),(18,3,1.0000,4.9990),(18,4,5.0000,5.9990),(18,5,6.0000,7.0000),(19,3,1.0000,4.9990),(19,4,5.0000,5.9990),(19,5,6.0000,7.0000),(20,3,1.0000,4.9990),(20,4,5.0000,5.9990),(20,5,6.0000,7.0000),(21,3,1.0000,4.9990),(21,4,5.0000,5.9990),(21,5,6.0000,7.0000);
/*!40000 ALTER TABLE `risk_variable_range` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_lang`
--

DROP TABLE IF EXISTS `role_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `role_name` varchar(45) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rolelang.roleid` (`role_id`),
  KEY `rolelang.langid` (`lang_id`),
  CONSTRAINT `rolelang.langid` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `rolelang.roleid` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_lang`
--

LOCK TABLES `role_lang` WRITE;
/*!40000 ALTER TABLE `role_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saas_coordinators`
--

DROP TABLE IF EXISTS `saas_coordinators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saas_coordinators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `saas_tenant_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `title` varchar(45) DEFAULT NULL,
  `email_address` varchar(100) DEFAULT NULL,
  `primary_mobile` varchar(15) DEFAULT NULL,
  `office_phone` varchar(15) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ebi_coordinators_ebi_tenant1_idx` (`saas_tenant_id`),
  KEY `fk_ebi_coordinators_role1_idx` (`role_id`),
  CONSTRAINT `fk_ebi_coordinators_ebi_tenant1` FOREIGN KEY (`saas_tenant_id`) REFERENCES `saas_tenant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebi_coordinators_role1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saas_coordinators`
--

LOCK TABLES `saas_coordinators` WRITE;
/*!40000 ALTER TABLE `saas_coordinators` DISABLE KEYS */;
/*!40000 ALTER TABLE `saas_coordinators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saas_hosting_group`
--

DROP TABLE IF EXISTS `saas_hosting_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saas_hosting_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hosting_group_name` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `db_name` varchar(100) DEFAULT NULL,
  `connect_string` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saas_hosting_group`
--

LOCK TABLES `saas_hosting_group` WRITE;
/*!40000 ALTER TABLE `saas_hosting_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `saas_hosting_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saas_tenant`
--

DROP TABLE IF EXISTS `saas_tenant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saas_tenant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subdomain` varchar(45) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  `time_zone` varchar(45) DEFAULT NULL,
  `website` varchar(500) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saas_tenant`
--

LOCK TABLES `saas_tenant` WRITE;
/*!40000 ALTER TABLE `saas_tenant` DISABLE KEYS */;
/*!40000 ALTER TABLE `saas_tenant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saas_tenant_hosting_groups`
--

DROP TABLE IF EXISTS `saas_tenant_hosting_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saas_tenant_hosting_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `saas_tenant_id` int(11) NOT NULL,
  `saas_hosting_group_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_saas_tenant_hosting_groups_saas_tenant1_idx` (`saas_tenant_id`),
  KEY `fk_saas_tenant_hosting_groups_saas_hosting_group1_idx` (`saas_hosting_group_id`),
  CONSTRAINT `fk_saas_tenant_hosting_groups_saas_hosting_group1` FOREIGN KEY (`saas_hosting_group_id`) REFERENCES `saas_hosting_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_saas_tenant_hosting_groups_saas_tenant1` FOREIGN KEY (`saas_tenant_id`) REFERENCES `saas_tenant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saas_tenant_hosting_groups`
--

LOCK TABLES `saas_tenant_hosting_groups` WRITE;
/*!40000 ALTER TABLE `saas_tenant_hosting_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `saas_tenant_hosting_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saas_tenant_lang`
--

DROP TABLE IF EXISTS `saas_tenant_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saas_tenant_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `saas_tenant_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `tenant_name` varchar(500) DEFAULT NULL,
  `nick_name` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ebi_tenant_lang_tenant1_idx` (`saas_tenant_id`),
  KEY `fk_ebi_tenant_lang_language_master1_idx` (`language_id`),
  CONSTRAINT `fk_ebi_tenant_lang_language_master1` FOREIGN KEY (`language_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ebi_tenant_lang_tenant1` FOREIGN KEY (`saas_tenant_id`) REFERENCES `saas_tenant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saas_tenant_lang`
--

LOCK TABLES `saas_tenant_lang` WRITE;
/*!40000 ALTER TABLE `saas_tenant_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `saas_tenant_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sites`
--

DROP TABLE IF EXISTS `sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sites` (
  `site_code` varchar(10) NOT NULL,
  `site_description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`site_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sites`
--

LOCK TABLES `sites` WRITE;
/*!40000 ALTER TABLE `sites` DISABLE KEYS */;
INSERT INTO `sites` VALUES ('SANDBOX','Sandbox Environment'),('TRAINING','Training Environment');
/*!40000 ALTER TABLE `sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student` (
  `id` int(11) DEFAULT NULL,
  `name` varchar(10) DEFAULT NULL,
  `marks` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student`
--

LOCK TABLES `student` WRITE;
/*!40000 ALTER TABLE `student` DISABLE KEYS */;
INSERT INTO `student` VALUES (1,'aa',10),(2,'bb',20),(3,'cc',30);
/*!40000 ALTER TABLE `student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_db_view_log`
--

DROP TABLE IF EXISTS `student_db_view_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_db_view_log` (
  `organization_id` int(11) NOT NULL,
  `person_id_faculty` int(11) NOT NULL,
  `person_id_student` int(11) NOT NULL,
  `last_viewed_on` datetime DEFAULT NULL,
  PRIMARY KEY (`organization_id`,`person_id_faculty`,`person_id_student`),
  KEY `fk_db_view_log_person1_idx` (`person_id_faculty`),
  KEY `fk_db_view_log_person2_idx` (`person_id_student`),
  KEY `fk_db_view_log_organization1_idx` (`organization_id`),
  CONSTRAINT `fk_db_view_log_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_db_view_log_person1` FOREIGN KEY (`person_id_faculty`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_db_view_log_person2` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_db_view_log`
--

LOCK TABLES `student_db_view_log` WRITE;
/*!40000 ALTER TABLE `student_db_view_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_db_view_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_task`
--

DROP TABLE IF EXISTS `student_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id_student` int(11) NOT NULL,
  `person_id_faculty` int(11) NOT NULL,
  `student_task_date` datetime DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `comments` text,
  `status` varchar(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_student_task_organization1_idx` (`organization_id`),
  KEY `fk_student_task_person1_idx` (`person_id_student`),
  KEY `fk_student_task_person2_idx` (`person_id_faculty`),
  CONSTRAINT `fk_student_task_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_student_task_person1` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_student_task_person2` FOREIGN KEY (`person_id_faculty`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_task`
--

LOCK TABLES `student_task` WRITE;
/*!40000 ALTER TABLE `student_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_task_teams`
--

DROP TABLE IF EXISTS `student_task_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_task_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_task_id` int(11) NOT NULL,
  `teams_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_student_task_teams_student_task1_idx` (`student_task_id`),
  KEY `fk_student_task_teams_teams1_idx` (`teams_id`),
  CONSTRAINT `fk_student_task_teams_student_task1` FOREIGN KEY (`student_task_id`) REFERENCES `student_task` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_student_task_teams_teams1` FOREIGN KEY (`teams_id`) REFERENCES `teams` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_task_teams`
--

LOCK TABLES `student_task_teams` WRITE;
/*!40000 ALTER TABLE `student_task_teams` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_task_teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey`
--

DROP TABLE IF EXISTS `survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `external_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey`
--

LOCK TABLES `survey` WRITE;
/*!40000 ALTER TABLE `survey` DISABLE KEYS */;
INSERT INTO `survey` VALUES (1,NULL);
/*!40000 ALTER TABLE `survey` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_branch`
--

DROP TABLE IF EXISTS `survey_branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_branch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `survey_pages_id_src` int(11) NOT NULL,
  `survey_pages_id_dst` int(11) NOT NULL,
  `ebi_question_id` int(11) NOT NULL,
  `ebi_options_id` int(11) NOT NULL,
  `description` varchar(400) DEFAULT NULL,
  `external_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_survey_branches_survey1_idx` (`survey_id`),
  KEY `fk_survey_branch_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_survey_branch_survey_pages1_idx` (`survey_pages_id_src`),
  KEY `fk_survey_branch_survey_pages2_idx` (`survey_pages_id_dst`),
  CONSTRAINT `fk_survey_branch_ebi_question1` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_branch_survey_pages1` FOREIGN KEY (`survey_pages_id_src`) REFERENCES `survey_pages` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_branch_survey_pages2` FOREIGN KEY (`survey_pages_id_dst`) REFERENCES `survey_pages` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_branches_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_branch`
--

LOCK TABLES `survey_branch` WRITE;
/*!40000 ALTER TABLE `survey_branch` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey_branch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_lang`
--

DROP TABLE IF EXISTS `survey_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_lang` (
  `survey_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`survey_id`,`lang_id`),
  KEY `fk_survey_lang_survey1_idx` (`survey_id`),
  KEY `fk_survey_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `fk_survey_lang_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_lang_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_lang`
--

LOCK TABLES `survey_lang` WRITE;
/*!40000 ALTER TABLE `survey_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_pages`
--

DROP TABLE IF EXISTS `survey_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `sequence` int(11) DEFAULT NULL,
  `set_completed` tinyint(1) DEFAULT NULL,
  `must_branch` tinyint(1) DEFAULT NULL,
  `external_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_survey_pages_survey1_idx` (`survey_id`),
  CONSTRAINT `fk_survey_pages_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_pages`
--

LOCK TABLES `survey_pages` WRITE;
/*!40000 ALTER TABLE `survey_pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_pages_lang`
--

DROP TABLE IF EXISTS `survey_pages_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_pages_lang` (
  `survey_pages_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`survey_pages_id`,`lang_id`),
  KEY `fk_survey_pages_lang_survey_pages1_idx` (`survey_pages_id`),
  KEY `fk_survey_pages_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `fk_survey_pages_lang_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_pages_lang_survey_pages1` FOREIGN KEY (`survey_pages_id`) REFERENCES `survey_pages` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_pages_lang`
--

LOCK TABLES `survey_pages_lang` WRITE;
/*!40000 ALTER TABLE `survey_pages_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey_pages_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_questions`
--

DROP TABLE IF EXISTS `survey_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `type` enum('bank','independent') DEFAULT NULL,
  `ebi_question_id` int(11) DEFAULT NULL,
  `ind_question_id` int(11) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `survey_sections_id` int(11) DEFAULT NULL,
  `qnbr` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_survey_questions_survey1_idx` (`survey_id`),
  KEY `fk_survey_questions_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_survey_questions_independent_question1_idx` (`ind_question_id`),
  KEY `fk_survey_questions_survey_sections1_idx` (`survey_sections_id`),
  CONSTRAINT `fk_survey_questions_ebi_question1` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_questions_independent_question1` FOREIGN KEY (`ind_question_id`) REFERENCES `ind_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_questions_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_questions_survey_sections1` FOREIGN KEY (`survey_sections_id`) REFERENCES `survey_sections` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_questions`
--

LOCK TABLES `survey_questions` WRITE;
/*!40000 ALTER TABLE `survey_questions` DISABLE KEYS */;
INSERT INTO `survey_questions` VALUES (1,1,NULL,1,NULL,NULL,NULL,NULL),(2,1,NULL,2,NULL,NULL,NULL,NULL),(3,1,NULL,3,NULL,NULL,NULL,NULL),(4,1,NULL,4,NULL,NULL,NULL,NULL),(5,1,NULL,5,NULL,NULL,NULL,NULL),(6,1,NULL,6,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `survey_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_response`
--

DROP TABLE IF EXISTS `survey_response`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_response` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `org_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `org_academic_year_id` int(11) NOT NULL,
  `org_academic_terms_id` int(11) DEFAULT NULL,
  `survey_questions_id` int(11) NOT NULL,
  `response_type` enum('decimal','char','charmax') DEFAULT NULL,
  `decimal_value` decimal(9,2) DEFAULT NULL,
  `char_value` varchar(500) DEFAULT NULL,
  `charmax_value` varchar(5000) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_survey_response_organization1` (`org_id`),
  KEY `fk_survey_response_person1_idx` (`person_id`),
  KEY `fk_survey_response_survey1_idx` (`survey_id`),
  KEY `fk_survey_response_survey_questions1_idx` (`survey_questions_id`),
  KEY `fk_survey_response_org_academic_year1_idx` (`org_academic_year_id`),
  KEY `fk_survey_response_org_academic_terms1_idx` (`org_academic_terms_id`),
  CONSTRAINT `fk_survey_response_org_academic_terms1` FOREIGN KEY (`org_academic_terms_id`) REFERENCES `org_academic_terms` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_response_org_academic_year1` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_response_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_response_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_response_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_response_survey_questions1` FOREIGN KEY (`survey_questions_id`) REFERENCES `survey_questions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_response`
--

LOCK TABLES `survey_response` WRITE;
/*!40000 ALTER TABLE `survey_response` DISABLE KEYS */;
INSERT INTO `survey_response` VALUES (1,1,1,1,1,1,1,'decimal',2.00,NULL,NULL,'2015-01-20 00:00:00'),(2,1,1,1,1,1,2,'char',NULL,'abc',NULL,'2015-01-10 00:00:00'),(3,1,1,1,1,1,3,'charmax',NULL,NULL,'abcdef',NULL),(4,1,2,1,1,1,1,'decimal',3.00,NULL,NULL,'2015-03-18 00:00:00'),(5,1,2,1,1,1,2,'char',NULL,'def',NULL,NULL),(6,1,2,1,1,1,4,'char',NULL,'xyz',NULL,NULL),(7,1,3,1,1,1,5,'decimal',4.00,NULL,NULL,'2015-01-31 00:00:00'),(8,1,3,1,1,1,1,'decimal',6.00,NULL,NULL,'2015-04-10 00:00:00'),(9,1,3,1,1,1,6,'charmax',NULL,NULL,'abcxyz',NULL),(10,1,1,1,1,1,2,'char',NULL,'ghi',NULL,'2015-03-15 00:00:00'),(11,1,3,1,1,1,5,'decimal',5.00,NULL,NULL,'2015-02-25 00:00:00'),(12,1,3,1,1,1,5,'decimal',10.00,NULL,NULL,'2015-01-01 00:00:00');
/*!40000 ALTER TABLE `survey_response` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_sections`
--

DROP TABLE IF EXISTS `survey_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `sequence` int(11) DEFAULT NULL,
  `survey_pages_id` int(11) NOT NULL,
  `external_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_survey_sections_survey1_idx` (`survey_id`),
  KEY `fk_survey_sections_survey_pages1_idx` (`survey_pages_id`),
  CONSTRAINT `fk_survey_sections_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_sections_survey_pages1` FOREIGN KEY (`survey_pages_id`) REFERENCES `survey_pages` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_sections`
--

LOCK TABLES `survey_sections` WRITE;
/*!40000 ALTER TABLE `survey_sections` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_sections_lang`
--

DROP TABLE IF EXISTS `survey_sections_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_sections_lang` (
  `survey_sections_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `description_hdr` varchar(2000) DEFAULT NULL,
  `description_dtl` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`survey_sections_id`,`lang_id`),
  KEY `fk_survey_sections_lang_survey_sections1_idx` (`survey_sections_id`),
  KEY `fk_survey_sections_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `fk_survey_sections_lang_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_sections_lang_survey_sections1` FOREIGN KEY (`survey_sections_id`) REFERENCES `survey_sections` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_sections_lang`
--

LOCK TABLES `survey_sections_lang` WRITE;
/*!40000 ALTER TABLE `survey_sections_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey_sections_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `surveymarker`
--

DROP TABLE IF EXISTS `surveymarker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveymarker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surveymarker`
--

LOCK TABLES `surveymarker` WRITE;
/*!40000 ALTER TABLE `surveymarker` DISABLE KEYS */;
/*!40000 ALTER TABLE `surveymarker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `surveymarker_lang`
--

DROP TABLE IF EXISTS `surveymarker_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveymarker_lang` (
  `surveymarker_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`surveymarker_id`,`lang_id`),
  KEY `fk_survey_marker_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `fk_survey_marker_lang_language_master1` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_survey_marker_lang_survey_marker1` FOREIGN KEY (`surveymarker_id`) REFERENCES `surveymarker` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surveymarker_lang`
--

LOCK TABLES `surveymarker_lang` WRITE;
/*!40000 ALTER TABLE `surveymarker_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `surveymarker_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `surveymarker_questions`
--

DROP TABLE IF EXISTS `surveymarker_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveymarker_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `surveymarker_id` int(11) NOT NULL,
  `type` enum('bank','survey','factor') DEFAULT NULL,
  `ebi_question_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `survey_questions_id` int(11) DEFAULT NULL,
  `factor_id` int(11) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `red_low` decimal(6,3) DEFAULT NULL,
  `red_high` decimal(6,3) DEFAULT NULL,
  `yellow_low` decimal(6,3) DEFAULT NULL,
  `yellow_high` decimal(6,3) DEFAULT NULL,
  `green_low` decimal(6,3) DEFAULT NULL,
  `green_high` decimal(6,3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_surveymarker_questions_surveymarker1_idx` (`surveymarker_id`),
  KEY `fk_surveymarker_questions_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_surveymarker_questions_survey1_idx` (`survey_id`),
  KEY `fk_surveymarker_questions_factor1_idx` (`factor_id`),
  KEY `fk_surveymarker_questions_survey_questions1_idx` (`survey_questions_id`),
  CONSTRAINT `fk_surveymarker_questions_ebi_question1` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_surveymarker_questions_factor1` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_surveymarker_questions_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_surveymarker_questions_survey_questions1` FOREIGN KEY (`survey_questions_id`) REFERENCES `survey_questions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_surveymarker_questions_surveymarker1` FOREIGN KEY (`surveymarker_id`) REFERENCES `surveymarker` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surveymarker_questions`
--

LOCK TABLES `surveymarker_questions` WRITE;
/*!40000 ALTER TABLE `surveymarker_questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `surveymarker_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_alerts`
--

DROP TABLE IF EXISTS `system_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `is_enabled` binary(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_system_alerts_organization1_idx` (`organization_id`),
  KEY `fk_system_alerts_person1_idx` (`person_id`),
  CONSTRAINT `fk_system_alerts_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_system_alerts_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_alerts`
--

LOCK TABLES `system_alerts` WRITE;
/*!40000 ALTER TABLE `system_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_alerts_lang`
--

DROP TABLE IF EXISTS `system_alerts_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_alerts_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `system_alerts_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` varchar(5000) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_system_alerts_lang_system_alerts1_idx` (`system_alerts_id`),
  KEY `fk_system_alerts_lang_language_master1_idx` (`language_id`),
  CONSTRAINT `fk_system_alerts_lang_language_master1` FOREIGN KEY (`language_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_system_alerts_lang_system_alerts1` FOREIGN KEY (`system_alerts_id`) REFERENCES `system_alerts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_alerts_lang`
--

LOCK TABLES `system_alerts_lang` WRITE;
/*!40000 ALTER TABLE `system_alerts_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_alerts_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `talking_points`
--

DROP TABLE IF EXISTS `talking_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `talking_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(1) DEFAULT NULL,
  `ebi_question_id` int(11) DEFAULT NULL,
  `ebi_metadata_id` int(11) DEFAULT NULL,
  `talking_points_type` varchar(1) DEFAULT NULL,
  `min_range` int(11) DEFAULT NULL,
  `max_range` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_talking_points_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_talking_points_ebi_metadata1_idx` (`ebi_metadata_id`),
  CONSTRAINT `fk_talking_points_ebi_metadata1` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_talking_points_ebi_question1` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `talking_points`
--

LOCK TABLES `talking_points` WRITE;
/*!40000 ALTER TABLE `talking_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `talking_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `talking_points_lang`
--

DROP TABLE IF EXISTS `talking_points_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `talking_points_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `talking_points_id` int(11) NOT NULL,
  `language_master_id` int(11) NOT NULL,
  `title` varchar(400) DEFAULT NULL,
  `description` varchar(5000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_talking_points_lang_talking_points1_idx` (`talking_points_id`),
  KEY `fk_talking_points_lang_language_master1_idx` (`language_master_id`),
  CONSTRAINT `fk_talking_points_lang_language_master1` FOREIGN KEY (`language_master_id`) REFERENCES `language_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_talking_points_lang_talking_points1` FOREIGN KEY (`talking_points_id`) REFERENCES `talking_points` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `talking_points_lang`
--

LOCK TABLES `talking_points_lang` WRITE;
/*!40000 ALTER TABLE `talking_points_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `talking_points_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_members`
--

DROP TABLE IF EXISTS `team_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `teams_id` int(11) NOT NULL,
  `person_id` int(11) DEFAULT NULL,
  `is_team_leader` binary(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_team_members_organization1_idx` (`organization_id`),
  KEY `fk_team_members_person1_idx` (`person_id`),
  KEY `fk_team_members_teams1_idx` (`teams_id`),
  CONSTRAINT `fk_team_members_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_members_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_members_teams1` FOREIGN KEY (`teams_id`) REFERENCES `teams` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_members`
--

LOCK TABLES `team_members` WRITE;
/*!40000 ALTER TABLE `team_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `team_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `team_name` varchar(1000) DEFAULT NULL,
  `team_description` varchar(5000) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Teams_organization1_idx` (`organization_id`),
  CONSTRAINT `fk_Teams_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upload_file_log`
--

DROP TABLE IF EXISTS `upload_file_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `upload_file_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `upload_type` enum('student','faculty','course','group','document','academicupdate','successmarker','surveyblock','riskmodel','riskvariable','riskmodelassoc') DEFAULT NULL,
  `upload_date` datetime DEFAULT NULL,
  `uploaded_columns` varchar(6000) DEFAULT NULL,
  `uploaded_row_count` int(11) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  `uploaded_file_path` varchar(500) DEFAULT NULL,
  `error_file_path` varchar(500) DEFAULT NULL,
  `job_number` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `error_count` int(11) DEFAULT NULL,
  `valid_row_count` int(11) DEFAULT NULL,
  `viewed` tinyint(1) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_upload_file_log_organization1_idx` (`organization_id`),
  KEY `fk_upload_file_log_person1_idx` (`person_id`),
  CONSTRAINT `fk_upload_file_log_organization1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_upload_file_log_person1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `upload_file_log`
--

LOCK TABLES `upload_file_log` WRITE;
/*!40000 ALTER TABLE `upload_file_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `upload_file_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wess_link`
--

DROP TABLE IF EXISTS `wess_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wess_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `year_id` varchar(10) DEFAULT NULL,
  `cohort_code` int(11) DEFAULT NULL,
  `status` enum('open','ready','launched','closed') DEFAULT NULL,
  `open_date` datetime DEFAULT NULL,
  `close_date` datetime DEFAULT NULL,
  `wess_survey_id` int(11) DEFAULT NULL,
  `wess_cohort_id` int(11) DEFAULT NULL,
  `wess_order_id` int(11) DEFAULT NULL,
  `wess_launchedflag` int(11) DEFAULT NULL,
  `wess_maporder_key` int(11) DEFAULT NULL,
  `wess_prod_year` varchar(4) DEFAULT NULL,
  `wess_cust_id` varchar(4) DEFAULT NULL,
  `wess_admin_link` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_wess_link_organization1` (`org_id`),
  KEY `fk_wess_link_survey1_idx` (`survey_id`),
  KEY `fk_wess_link_year1_idx` (`year_id`),
  CONSTRAINT `fk_wess_link_organization1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_wess_link_survey1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_wess_link_year1` FOREIGN KEY (`year_id`) REFERENCES `year` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wess_link`
--

LOCK TABLES `wess_link` WRITE;
/*!40000 ALTER TABLE `wess_link` DISABLE KEYS */;
/*!40000 ALTER TABLE `wess_link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `year`
--

DROP TABLE IF EXISTS `year`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `year` (
  `id` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `year`
--

LOCK TABLES `year` WRITE;
/*!40000 ALTER TABLE `year` DISABLE KEYS */;
INSERT INTO `year` VALUES ('1'),('2');
/*!40000 ALTER TABLE `year` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `isp`
--

/*!50001 DROP TABLE IF EXISTS `isp`*/;
/*!50001 DROP VIEW IF EXISTS `isp`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `isp` AS select `rmm`.`id` AS `risk_model_id`,`rv`.`source` AS `source`,`rv`.`id` AS `risk_variable_id`,`rv`.`variable_type` AS `variable_type`,`rmw`.`weight` AS `weight`,`rv`.`org_metadata_id` AS `org_metadata_id`,`emd`.`metadata_value` AS `metadata_value`,`rvr`.`bucket_value` AS `bucket_value`,`emd`.`person_id` AS `person_id` from ((((`risk_model_master` `rmm` join `risk_model_weights` `rmw` on((`rmw`.`risk_model_id` = `rmm`.`id`))) join `risk_variable` `rv` on((`rmw`.`risk_variable_id` = `rv`.`id`))) join `person_org_metadata` `emd` on((`emd`.`org_metadata_id` = `rv`.`org_metadata_id`))) join `risk_variable_range` `rvr` on(((`rvr`.`risk_variable_id` = `rv`.`id`) and (`emd`.`metadata_value` between `rvr`.`min` and `rvr`.`max`)))) union select `rmm`.`id` AS `risk_model_id`,`rv`.`source` AS `source`,`rv`.`id` AS `risk_variable_id`,`rv`.`variable_type` AS `variable_type`,`rmw`.`weight` AS `weight`,`rv`.`org_metadata_id` AS `org_metadata_id`,`emd`.`metadata_value` AS `metadata_value`,`rvr`.`bucket_value` AS `bucket_value`,`emd`.`person_id` AS `person_id` from ((((`risk_model_master` `rmm` join `risk_model_weights` `rmw` on((`rmw`.`risk_model_id` = `rmm`.`id`))) join `risk_variable` `rv` on((`rmw`.`risk_variable_id` = `rv`.`id`))) join `person_org_metadata` `emd` on((`emd`.`org_metadata_id` = `rv`.`org_metadata_id`))) join `risk_variable_category` `rvr` on(((`rvr`.`risk_variable_id` = `rv`.`id`) and (`emd`.`metadata_value` = `rvr`.`option_value`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `profile`
--

/*!50001 DROP TABLE IF EXISTS `profile`*/;
/*!50001 DROP VIEW IF EXISTS `profile`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `profile` AS select `rmm`.`id` AS `risk_model_id`,`rv`.`source` AS `source`,`rv`.`id` AS `risk_variable_id`,`rv`.`variable_type` AS `variable_type`,`rmw`.`weight` AS `weight`,`rv`.`ebi_metadata_id` AS `ebi_metadata_id`,`emd`.`metadata_value` AS `metadata_value`,`rvr`.`bucket_value` AS `bucket_value`,`emd`.`person_id` AS `person_id` from ((((`risk_model_master` `rmm` join `risk_model_weights` `rmw` on((`rmw`.`risk_model_id` = `rmm`.`id`))) join `risk_variable` `rv` on((`rmw`.`risk_variable_id` = `rv`.`id`))) join `person_ebi_metadata` `emd` on((`emd`.`ebi_metadata_id` = `rv`.`ebi_metadata_id`))) join `risk_variable_range` `rvr` on(((`rvr`.`risk_variable_id` = `rv`.`id`) and (`emd`.`metadata_value` between `rvr`.`min` and `rvr`.`max`)))) union select `rmm`.`id` AS `risk_model_id`,`rv`.`source` AS `source`,`rv`.`id` AS `risk_variable_id`,`rv`.`variable_type` AS `variable_type`,`rmw`.`weight` AS `weight`,`rv`.`ebi_metadata_id` AS `ebi_metadata_id`,`emd`.`metadata_value` AS `metadata_value`,`rvr`.`bucket_value` AS `bucket_value`,`emd`.`person_id` AS `person_id` from ((((`risk_model_master` `rmm` join `risk_model_weights` `rmw` on((`rmw`.`risk_model_id` = `rmm`.`id`))) join `risk_variable` `rv` on((`rmw`.`risk_variable_id` = `rv`.`id`))) join `person_ebi_metadata` `emd` on((`emd`.`ebi_metadata_id` = `rv`.`ebi_metadata_id`))) join `risk_variable_category` `rvr` on(((`rvr`.`risk_variable_id` = `rv`.`id`) and (`emd`.`metadata_value` = `rvr`.`option_value`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-06-04 20:04:46
