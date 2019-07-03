-- MySQL dump 10.13  Distrib 5.5.49, for debian-linux-gnu (x86_64)
--
-- Host: synapse-training-functional-testing-db-4.ccwh8pl3dfjy.us-east-1.rds.amazonaws.com    Database: synapse
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
-- Temporary table structure for view `AUDIT_DASHBOARD_Survey_Completion_Status`
--

DROP TABLE IF EXISTS `AUDIT_DASHBOARD_Survey_Completion_Status`;
/*!50001 DROP VIEW IF EXISTS `AUDIT_DASHBOARD_Survey_Completion_Status`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `AUDIT_DASHBOARD_Survey_Completion_Status` (
  `survey_completion_status` tinyint NOT NULL,
  `survey_opt_out_status` tinyint NOT NULL,
  `Has_Responses` tinyint NOT NULL,
  `valid_combination` tinyint NOT NULL,
  `needs_manual_intervention` tinyint NOT NULL,
  `student_survey_link_count` tinyint NOT NULL,
  `org_id` tinyint NOT NULL,
  `date_last_updated` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `AccessToken`
--

DROP TABLE IF EXISTS `AccessToken`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AccessToken` (
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
  CONSTRAINT `FK_B39617F519EB6921` FOREIGN KEY (`client_id`) REFERENCES `Client` (`id`),
  CONSTRAINT `FK_B39617F5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AccessToken`
--

LOCK TABLES `AccessToken` WRITE;
/*!40000 ALTER TABLE `AccessToken` DISABLE KEYS */;
/*!40000 ALTER TABLE `AccessToken` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Appointments`
--

DROP TABLE IF EXISTS `Appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `activity_category_id` int(11) DEFAULT NULL,
  `person_id_proxy` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date_time` datetime DEFAULT NULL,
  `end_date_time` datetime DEFAULT NULL,
  `attendees` longtext COLLATE utf8_unicode_ci,
  `occurrence_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `master_occurrence_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `match_status` tinyint(1) DEFAULT NULL,
  `last_synced` datetime DEFAULT NULL,
  `is_free_standing` tinyint(1) DEFAULT NULL,
  `source` enum('S','G','E') COLLATE utf8_unicode_ci DEFAULT NULL,
  `exchange_appointment_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `exchange_master_appointment_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_appointment_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_master_appointment_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_private` tinyint(1) DEFAULT NULL,
  `access_public` tinyint(1) DEFAULT NULL,
  `access_team` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7270A98232C8A3DE` (`organization_id`),
  KEY `IDX_7270A982217BBB47` (`person_id`),
  KEY `IDX_7270A9821CC8F7EE` (`activity_category_id`),
  KEY `IDX_7270A9829B12DB9` (`person_id_proxy`),
  KEY `start_date_time_idx` (`start_date_time`),
  KEY `end_date_time_idx` (`end_date_time`),
  CONSTRAINT `FK_7270A9821CC8F7EE` FOREIGN KEY (`activity_category_id`) REFERENCES `activity_category` (`id`),
  CONSTRAINT `FK_7270A982217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_7270A98232C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_7270A9829B12DB9` FOREIGN KEY (`person_id_proxy`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Appointments`
--

LOCK TABLES `Appointments` WRITE;
/*!40000 ALTER TABLE `Appointments` DISABLE KEYS */;
/*!40000 ALTER TABLE `Appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AuthCode`
--

DROP TABLE IF EXISTS `AuthCode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuthCode` (
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
  CONSTRAINT `FK_F1D7D17719EB6921` FOREIGN KEY (`client_id`) REFERENCES `Client` (`id`),
  CONSTRAINT `FK_F1D7D177A76ED395` FOREIGN KEY (`user_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AuthCode`
--

LOCK TABLES `AuthCode` WRITE;
/*!40000 ALTER TABLE `AuthCode` DISABLE KEYS */;
/*!40000 ALTER TABLE `AuthCode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Client`
--

DROP TABLE IF EXISTS `Client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `redirect_uris` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `secret` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `allowed_grant_types` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Client`
--

LOCK TABLES `Client` WRITE;
/*!40000 ALTER TABLE `Client` DISABLE KEYS */;
INSERT INTO `Client` VALUES (1,'14tx5vbsnois4ggg0ok0c4gog8kg0ww488gwkg88044cog4884','a:0:{}','4v5p8idswhs0404owsws48gwwccc4wksw4c8s80wcocwskockg','a:2:{i:0;s:8:\"password\";i:1;s:13:\"refresh_token\";}'),(2,'2y0ku7f5748wwwskkk84o00ssgwsgkokks8ogs08ckscckcskg','a:0:{}','365m9433y94w40wccow04g8wwkscccg00gsw44skgw0448c8k4','a:2:{i:0;s:8:\"password\";i:1;s:13:\"refresh_token\";}');
/*!40000 ALTER TABLE `Client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `DASHBOARD_Student_Calculations`
--

DROP TABLE IF EXISTS `DASHBOARD_Student_Calculations`;
/*!50001 DROP VIEW IF EXISTS `DASHBOARD_Student_Calculations`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `DASHBOARD_Student_Calculations` (
  `Calculation Type` tinyint NOT NULL,
  `Calculated Students` tinyint NOT NULL,
  `Students With No Data` tinyint NOT NULL,
  `Flagged For Calculation` tinyint NOT NULL,
  `Never Calculated` tinyint NOT NULL,
  `Total Students` tinyint NOT NULL,
  `Calculated Percentage` tinyint NOT NULL,
  `No Data Percentage` tinyint NOT NULL,
  `Calculating Percentage` tinyint NOT NULL,
  `Never Calculated Percentage` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `DASHBOARD_Student_Surveys_By_Org`
--

DROP TABLE IF EXISTS `DASHBOARD_Student_Surveys_By_Org`;
/*!50001 DROP VIEW IF EXISTS `DASHBOARD_Student_Surveys_By_Org`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `DASHBOARD_Student_Surveys_By_Org` (
  `organization_id` tinyint NOT NULL,
  `campus_id` tinyint NOT NULL,
  `organization_name` tinyint NOT NULL,
  `Total number of Surveys Taken` tinyint NOT NULL,
  `Students having taken survey_id: 11` tinyint NOT NULL,
  `Students having taken survey_id: 12` tinyint NOT NULL,
  `Students having taken survey_id: 13` tinyint NOT NULL,
  `Students having taken survey_id: 14` tinyint NOT NULL,
  `Student Survey Eligibility` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `DASHBOARD_Students_With_Intent_To_Leave`
--

DROP TABLE IF EXISTS `DASHBOARD_Students_With_Intent_To_Leave`;
/*!50001 DROP VIEW IF EXISTS `DASHBOARD_Students_With_Intent_To_Leave`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `DASHBOARD_Students_With_Intent_To_Leave` (
  `org_id` tinyint NOT NULL,
  `person_id` tinyint NOT NULL,
  `survey_id` tinyint NOT NULL,
  `decimal_value` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `DASHBOARD_Upload_Status`
--

DROP TABLE IF EXISTS `DASHBOARD_Upload_Status`;
/*!50001 DROP VIEW IF EXISTS `DASHBOARD_Upload_Status`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `DASHBOARD_Upload_Status` (
  `organization id` tinyint NOT NULL,
  `organization name` tinyint NOT NULL,
  `upload tyoe` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `most recent upload date` tinyint NOT NULL,
  `uploaded file path` tinyint NOT NULL,
  `uploaded row count` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `Factor_Question_Constants`
--

DROP TABLE IF EXISTS `Factor_Question_Constants`;
/*!50001 DROP VIEW IF EXISTS `Factor_Question_Constants`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `Factor_Question_Constants` (
  `datum_type` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `Issues_Calculation`
--

DROP TABLE IF EXISTS `Issues_Calculation`;
/*!50001 DROP VIEW IF EXISTS `Issues_Calculation`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `Issues_Calculation` (
  `org_id` tinyint NOT NULL,
  `faculty_id` tinyint NOT NULL,
  `survey_id` tinyint NOT NULL,
  `issue_id` tinyint NOT NULL,
  `cohort` tinyint NOT NULL,
  `student_id` tinyint NOT NULL,
  `has_issue` tinyint NOT NULL,
  `name` tinyint NOT NULL,
  `icon` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `Issues_Datum`
--

DROP TABLE IF EXISTS `Issues_Datum`;
/*!50001 DROP VIEW IF EXISTS `Issues_Datum`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `Issues_Datum` (
  `org_id` tinyint NOT NULL,
  `faculty_id` tinyint NOT NULL,
  `survey_id` tinyint NOT NULL,
  `student_id` tinyint NOT NULL,
  `issue_id` tinyint NOT NULL,
  `cohort` tinyint NOT NULL,
  `type` tinyint NOT NULL,
  `source_id` tinyint NOT NULL,
  `source_value` tinyint NOT NULL,
  `modified_at` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `Issues_Factors`
--

DROP TABLE IF EXISTS `Issues_Factors`;
/*!50001 DROP VIEW IF EXISTS `Issues_Factors`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `Issues_Factors` (
  `org_id` tinyint NOT NULL,
  `student_id` tinyint NOT NULL,
  `survey_id` tinyint NOT NULL,
  `issue_id` tinyint NOT NULL,
  `cohort` tinyint NOT NULL,
  `factor_id` tinyint NOT NULL,
  `faculty_id` tinyint NOT NULL,
  `permitted_value` tinyint NOT NULL,
  `modified_at` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `Issues_Survey_Questions`
--

DROP TABLE IF EXISTS `Issues_Survey_Questions`;
/*!50001 DROP VIEW IF EXISTS `Issues_Survey_Questions`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `Issues_Survey_Questions` (
  `org_id` tinyint NOT NULL,
  `student_id` tinyint NOT NULL,
  `survey_id` tinyint NOT NULL,
  `issue_id` tinyint NOT NULL,
  `cohort` tinyint NOT NULL,
  `survey_question_id` tinyint NOT NULL,
  `ebi_question_id` tinyint NOT NULL,
  `faculty_id` tinyint NOT NULL,
  `permitted_value` tinyint NOT NULL,
  `modified_at` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `PART_Upload_Status_part_1`
--

DROP TABLE IF EXISTS `PART_Upload_Status_part_1`;
/*!50001 DROP VIEW IF EXISTS `PART_Upload_Status_part_1`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `PART_Upload_Status_part_1` (
  `organization_id` tinyint NOT NULL,
  `upload_type` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `most_recent_upload_date` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `RefreshToken`
--

DROP TABLE IF EXISTS `RefreshToken`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RefreshToken` (
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
  CONSTRAINT `FK_7142379E19EB6921` FOREIGN KEY (`client_id`) REFERENCES `Client` (`id`),
  CONSTRAINT `FK_7142379EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `RefreshToken`
--

LOCK TABLES `RefreshToken` WRITE;
/*!40000 ALTER TABLE `RefreshToken` DISABLE KEYS */;
/*!40000 ALTER TABLE `RefreshToken` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Teams`
--

DROP TABLE IF EXISTS `Teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `team_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `team_description` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_57030D5C32C8A3DE` (`organization_id`),
  CONSTRAINT `FK_57030D5C32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Teams`
--

LOCK TABLES `Teams` WRITE;
/*!40000 ALTER TABLE `Teams` DISABLE KEYS */;
/*!40000 ALTER TABLE `Teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_update`
--

DROP TABLE IF EXISTS `academic_update`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_update` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `org_courses_id` int(11) DEFAULT NULL,
  `academic_update_request_id` int(11) DEFAULT NULL,
  `person_id_faculty_responded` int(11) DEFAULT NULL,
  `person_id_student` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `update_type` enum('bulk','targeted','adhoc','ftp') COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('open','closed','cancelled','saved') COLLATE utf8_unicode_ci DEFAULT NULL,
  `request_date` datetime DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `failure_risk_level` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `grade` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `absence` int(11) DEFAULT NULL,
  `comment` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `refer_for_assistance` tinyint(1) DEFAULT NULL,
  `send_to_student` tinyint(1) DEFAULT NULL,
  `is_upload` tinyint(1) DEFAULT NULL,
  `is_adhoc` tinyint(1) DEFAULT NULL,
  `final_grade` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_submitted_without_change` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_73DF4B2ADE12AB56` (`created_by`),
  KEY `IDX_73DF4B2A25F94802` (`modified_by`),
  KEY `IDX_73DF4B2A1F6FA0AF` (`deleted_by`),
  KEY `fk_academic_update_org_courses1_idx` (`org_courses_id`),
  KEY `fk_academic_update_academic_update_request1_idx` (`academic_update_request_id`),
  KEY `fk_academic_update_person2_idx` (`person_id_faculty_responded`),
  KEY `fk_academic_update_person3_idx` (`person_id_student`),
  KEY `fk_academic_update_organization1_idx` (`org_id`,`person_id_student`,`modified_at`,`org_courses_id`),
  KEY `student-grade` (`person_id_student`,`grade`),
  CONSTRAINT `FK_73DF4B2A1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_73DF4B2A25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_73DF4B2A5F056556` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_73DF4B2A7C751C40` FOREIGN KEY (`org_courses_id`) REFERENCES `org_courses` (`id`),
  CONSTRAINT `FK_73DF4B2A9170E9C9` FOREIGN KEY (`person_id_faculty_responded`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_73DF4B2ACA3D7B42` FOREIGN KEY (`academic_update_request_id`) REFERENCES `academic_update_request` (`id`),
  CONSTRAINT `FK_73DF4B2ADE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_73DF4B2AF4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_update`
--

LOCK TABLES `academic_update` WRITE;
/*!40000 ALTER TABLE `academic_update` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_update` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_update_assigned_faculty`
--

DROP TABLE IF EXISTS `academic_update_assigned_faculty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_update_assigned_faculty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id_faculty_assigned` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `academic_update_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_academic_update_assigned_faculty_person1_idx` (`person_id_faculty_assigned`),
  KEY `fk_academic_update_assigned_faculty_organization1_idx` (`org_id`),
  KEY `fk_academic_update_assigned_faculty_academic_update1_idx` (`academic_update_id`),
  CONSTRAINT `FK_DE7DDC162B8D6DFB` FOREIGN KEY (`person_id_faculty_assigned`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_DE7DDC16567A5FFE` FOREIGN KEY (`academic_update_id`) REFERENCES `academic_update` (`id`),
  CONSTRAINT `FK_DE7DDC16F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `update_type` enum('bulk','targeted') COLLATE utf8_unicode_ci DEFAULT NULL,
  `request_date` datetime DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(4000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('open','closed','cancelled') COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `subject` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_optional_msg` mediumtext COLLATE utf8_unicode_ci,
  `select_course` enum('all','individual','none') COLLATE utf8_unicode_ci DEFAULT NULL,
  `select_student` enum('all','individual','none') COLLATE utf8_unicode_ci DEFAULT NULL,
  `select_faculty` enum('all','individual','none') COLLATE utf8_unicode_ci DEFAULT NULL,
  `select_group` enum('all','individual','none') COLLATE utf8_unicode_ci DEFAULT NULL,
  `select_metadata` enum('all','individual','none') COLLATE utf8_unicode_ci DEFAULT NULL,
  `select_static_list` enum('all','individual','none') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3C7F051ADE12AB56` (`created_by`),
  KEY `IDX_3C7F051A25F94802` (`modified_by`),
  KEY `IDX_3C7F051A1F6FA0AF` (`deleted_by`),
  KEY `fk_academic_update_request_organization1_idx` (`org_id`),
  KEY `fk_academic_update_request_person1_idx` (`person_id`),
  CONSTRAINT `FK_3C7F051A1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3C7F051A217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3C7F051A25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3C7F051ADE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3C7F051AF4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_update_request`
--

LOCK TABLES `academic_update_request` WRITE;
/*!40000 ALTER TABLE `academic_update_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_update_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_update_request_course`
--

DROP TABLE IF EXISTS `academic_update_request_course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_update_request_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_courses_id` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `academic_update_request_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_academic_update_request_course_org_courses1_idx` (`org_courses_id`),
  KEY `fk_academic_update_request_course_organization1_idx` (`org_id`),
  KEY `fk_academic_update_request_course_academic_update_request1_idx` (`academic_update_request_id`),
  CONSTRAINT `FK_BDB1EA0E7C751C40` FOREIGN KEY (`org_courses_id`) REFERENCES `org_courses` (`id`),
  CONSTRAINT `FK_BDB1EA0ECA3D7B42` FOREIGN KEY (`academic_update_request_id`) REFERENCES `academic_update_request` (`id`),
  CONSTRAINT `FK_BDB1EA0EF4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `academic_update_request_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_academic_update_request_faculty_person1_idx` (`person_id`),
  KEY `fk_academic_update_request_faculty_organization1_idx` (`org_id`),
  KEY `fk_academic_update_request_faculty_academic_update_request1_idx` (`academic_update_request_id`),
  CONSTRAINT `FK_423869E9217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_423869E9CA3D7B42` FOREIGN KEY (`academic_update_request_id`) REFERENCES `academic_update_request` (`id`),
  CONSTRAINT `FK_423869E9F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_group_id` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `academic_update_request_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_academic_update_request_group_org_group1_idx` (`org_group_id`),
  KEY `fk_academic_update_request_group_organization1_idx` (`org_id`),
  KEY `fk_academic_update_request_group_academic_update_request1_idx` (`academic_update_request_id`),
  CONSTRAINT `FK_9378AAEA82FB49A4` FOREIGN KEY (`org_group_id`) REFERENCES `org_group` (`id`),
  CONSTRAINT `FK_9378AAEACA3D7B42` FOREIGN KEY (`academic_update_request_id`) REFERENCES `academic_update_request` (`id`),
  CONSTRAINT `FK_9378AAEAF4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `ebi_metadata_id` int(11) DEFAULT NULL,
  `org_metadata_id` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `academic_update_request_id` int(11) DEFAULT NULL,
  `search_value` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_academic_update_request_metadata_ebi_metadata1_idx` (`ebi_metadata_id`),
  KEY `fk_academic_update_request_metadata_org_metadata1_idx` (`org_metadata_id`),
  KEY `fk_academic_update_request_metadata_organization1_idx` (`org_id`),
  KEY `fk_academic_update_request_metadata_academic_update_request_idx` (`academic_update_request_id`),
  CONSTRAINT `FK_7942D0EB4012B3BF` FOREIGN KEY (`org_metadata_id`) REFERENCES `org_metadata` (`id`),
  CONSTRAINT `FK_7942D0EBBB49FE75` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`),
  CONSTRAINT `FK_7942D0EBCA3D7B42` FOREIGN KEY (`academic_update_request_id`) REFERENCES `academic_update_request` (`id`),
  CONSTRAINT `FK_7942D0EBF4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_update_request_metadata`
--

LOCK TABLES `academic_update_request_metadata` WRITE;
/*!40000 ALTER TABLE `academic_update_request_metadata` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_update_request_metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_update_request_static_list`
--

DROP TABLE IF EXISTS `academic_update_request_static_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_update_request_static_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_static_list_id` int(11) DEFAULT NULL,
  `academic_update_request_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D9E4575FDE12AB56` (`created_by`),
  KEY `IDX_D9E4575F25F94802` (`modified_by`),
  KEY `IDX_D9E4575F1F6FA0AF` (`deleted_by`),
  KEY `fk_academic_update_request_static_list_organization1_idx` (`organization_id`),
  KEY `fk_academic_update_request_static_list_academic_update_requ_idx` (`academic_update_request_id`),
  KEY `fk_academic_update_request_static_list_org_static_list1_idx` (`org_static_list_id`),
  CONSTRAINT `FK_D9E4575F1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_D9E4575F25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_D9E4575F32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_D9E4575FAD199442` FOREIGN KEY (`org_static_list_id`) REFERENCES `org_static_list` (`id`),
  CONSTRAINT `FK_D9E4575FCA3D7B42` FOREIGN KEY (`academic_update_request_id`) REFERENCES `academic_update_request` (`id`),
  CONSTRAINT `FK_D9E4575FDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_update_request_static_list`
--

LOCK TABLES `academic_update_request_static_list` WRITE;
/*!40000 ALTER TABLE `academic_update_request_static_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_update_request_static_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_update_request_student`
--

DROP TABLE IF EXISTS `academic_update_request_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_update_request_student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `academic_update_request_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_academic_update_request_student_person1_idx` (`person_id`),
  KEY `fk_academic_update_request_student_organization1_idx` (`org_id`),
  KEY `fk_academic_update_request_student_academic_update_request1_idx` (`academic_update_request_id`),
  CONSTRAINT `FK_E28DA699217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E28DA699CA3D7B42` FOREIGN KEY (`academic_update_request_id`) REFERENCES `academic_update_request` (`id`),
  CONSTRAINT `FK_E28DA699F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `event` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `source_ip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `browser` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EF7F351032C8A3DE` (`organization_id`),
  KEY `IDX_EF7F3510217BBB47` (`person_id`),
  CONSTRAINT `FK_EF7F3510217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_EF7F351032C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_log`
--

LOCK TABLES `access_log` WRITE;
/*!40000 ALTER TABLE `access_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `access_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_category`
--

DROP TABLE IF EXISTS `activity_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `short_name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `display_seq` int(11) DEFAULT NULL,
  `parent_activity_category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_activity_category_activity_category1_idx` (`parent_activity_category_id`),
  CONSTRAINT `FK_A646A9CFB8DBAE1C` FOREIGN KEY (`parent_activity_category_id`) REFERENCES `activity_category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `activity_category_id` int(11) DEFAULT NULL,
  `language_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BB4037E11CC8F7EE` (`activity_category_id`),
  KEY `IDX_BB4037E182F1BAF4` (`language_id`),
  CONSTRAINT `FK_BB4037E11CC8F7EE` FOREIGN KEY (`activity_category_id`) REFERENCES `activity_category` (`id`),
  CONSTRAINT `FK_BB4037E182F1BAF4` FOREIGN KEY (`language_id`) REFERENCES `language_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `person_id_faculty` int(11) DEFAULT NULL,
  `person_id_student` int(11) DEFAULT NULL,
  `referrals_id` int(11) DEFAULT NULL,
  `appointments_id` int(11) DEFAULT NULL,
  `note_id` int(11) DEFAULT NULL,
  `contacts_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `activity_type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activity_date` datetime DEFAULT NULL,
  `reason` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FD06F647DE12AB56` (`created_by`),
  KEY `IDX_FD06F64725F94802` (`modified_by`),
  KEY `IDX_FD06F6471F6FA0AF` (`deleted_by`),
  KEY `IDX_FD06F64732C8A3DE` (`organization_id`),
  KEY `IDX_FD06F647FFB0AA26` (`person_id_faculty`),
  KEY `IDX_FD06F6475F056556` (`person_id_student`),
  KEY `IDX_FD06F647B24851AE` (`referrals_id`),
  KEY `IDX_FD06F64723F542AE` (`appointments_id`),
  KEY `IDX_FD06F64726ED0855` (`note_id`),
  KEY `IDX_FD06F647719FB48E` (`contacts_id`),
  KEY `IDX_FD06F647A832C1C9` (`email_id`),
  CONSTRAINT `FK_FD06F6471F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_FD06F64723F542AE` FOREIGN KEY (`appointments_id`) REFERENCES `Appointments` (`id`),
  CONSTRAINT `FK_FD06F64725F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_FD06F64726ED0855` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`),
  CONSTRAINT `FK_FD06F64732C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_FD06F6475F056556` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_FD06F647719FB48E` FOREIGN KEY (`contacts_id`) REFERENCES `contacts` (`id`),
  CONSTRAINT `FK_FD06F647A832C1C9` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`),
  CONSTRAINT `FK_FD06F647B24851AE` FOREIGN KEY (`referrals_id`) REFERENCES `referrals` (`id`),
  CONSTRAINT `FK_FD06F647DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_FD06F647FFB0AA26` FOREIGN KEY (`person_id_faculty`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_reference`
--

DROP TABLE IF EXISTS `activity_reference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_reference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `short_name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyblob,
  `display_seq` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_reference`
--

LOCK TABLES `activity_reference` WRITE;
/*!40000 ALTER TABLE `activity_reference` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_reference` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_reference_lang`
--

DROP TABLE IF EXISTS `activity_reference_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_reference_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language_master_id` int(11) DEFAULT NULL,
  `activity_reference_id` int(11) DEFAULT NULL,
  `heading` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DAC2A8A2D5D3A0FB` (`language_master_id`),
  KEY `IDX_DAC2A8A21FFD1CE8` (`activity_reference_id`),
  CONSTRAINT `FK_DAC2A8A21FFD1CE8` FOREIGN KEY (`activity_reference_id`) REFERENCES `activity_reference` (`id`),
  CONSTRAINT `FK_DAC2A8A2D5D3A0FB` FOREIGN KEY (`language_master_id`) REFERENCES `language_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_reference_lang`
--

LOCK TABLES `activity_reference_lang` WRITE;
/*!40000 ALTER TABLE `activity_reference_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_reference_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_reference_unassigned`
--

DROP TABLE IF EXISTS `activity_reference_unassigned`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_reference_unassigned` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `activity_reference_id` int(11) DEFAULT NULL,
  `is_primary_coordinator` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_18D1E598217BBB47` (`person_id`),
  KEY `IDX_18D1E59832C8A3DE` (`organization_id`),
  KEY `IDX_18D1E5981FFD1CE8` (`activity_reference_id`),
  CONSTRAINT `FK_18D1E5981FFD1CE8` FOREIGN KEY (`activity_reference_id`) REFERENCES `activity_reference` (`id`),
  CONSTRAINT `FK_18D1E598217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_18D1E59832C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_reference_unassigned`
--

LOCK TABLES `activity_reference_unassigned` WRITE;
/*!40000 ALTER TABLE `activity_reference_unassigned` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_reference_unassigned` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alert_notification_referral`
--

DROP TABLE IF EXISTS `alert_notification_referral`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alert_notification_referral` (
  `alert_notification_id` int(11) NOT NULL,
  `referral_history_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`alert_notification_id`),
  KEY `fk_alert_notification_referral_referral_history_id` (`referral_history_id`),
  KEY `fk_alert_notification_referral_created_by` (`created_by`),
  KEY `fk_alert_notification_referral_modified_by` (`modified_by`),
  KEY `fk_alert_notification_referral_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_alert_notification_referral_alert_notification_id` FOREIGN KEY (`alert_notification_id`) REFERENCES `alert_notifications` (`id`),
  CONSTRAINT `fk_alert_notification_referral_created_by` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_alert_notification_referral_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_alert_notification_referral_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_alert_notification_referral_referral_history_id` FOREIGN KEY (`referral_history_id`) REFERENCES `referral_history` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alert_notification_referral`
--

LOCK TABLES `alert_notification_referral` WRITE;
/*!40000 ALTER TABLE `alert_notification_referral` DISABLE KEYS */;
/*!40000 ALTER TABLE `alert_notification_referral` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alert_notifications`
--

DROP TABLE IF EXISTS `alert_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alert_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `referrals_id` int(11) DEFAULT NULL,
  `appointments_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `event` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_viewed` tinyint(1) DEFAULT NULL,
  `reason` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `org_search_id` int(11) DEFAULT NULL,
  `org_course_upload_file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `academic_update_id` int(11) DEFAULT NULL,
  `org_static_list_id` int(11) DEFAULT NULL,
  `org_announcements_id` int(11) DEFAULT NULL,
  `reports_running_status_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_16328CC532C8A3DE` (`organization_id`),
  KEY `IDX_16328CC5B24851AE` (`referrals_id`),
  KEY `IDX_16328CC523F542AE` (`appointments_id`),
  KEY `IDX_16328CC5217BBB47` (`person_id`),
  KEY `fk_alert_notifications_org_search1_idx` (`org_search_id`),
  KEY `fk_alert_notifications_academic_update1_idx` (`academic_update_id`),
  KEY `IDX_D012795CAD199442` (`org_static_list_id`),
  KEY `IDX_D012795C52CCF843` (`org_announcements_id`),
  KEY `is_viewed_idx` (`is_viewed`),
  KEY `fk_alert_notifications_report_running_status1_idx` (`reports_running_status_id`),
  CONSTRAINT `FK_16328CC5217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_16328CC523F542AE` FOREIGN KEY (`appointments_id`) REFERENCES `Appointments` (`id`),
  CONSTRAINT `FK_16328CC532C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_16328CC5B24851AE` FOREIGN KEY (`referrals_id`) REFERENCES `referrals` (`id`),
  CONSTRAINT `FK_D012795C52CCF843` FOREIGN KEY (`org_announcements_id`) REFERENCES `org_announcements` (`id`),
  CONSTRAINT `FK_D012795C567A5FFE` FOREIGN KEY (`academic_update_id`) REFERENCES `academic_update` (`id`),
  CONSTRAINT `FK_D012795C5C787CFB` FOREIGN KEY (`org_search_id`) REFERENCES `org_search` (`id`),
  CONSTRAINT `FK_D012795C7FDAE991` FOREIGN KEY (`reports_running_status_id`) REFERENCES `reports_running_status` (`id`),
  CONSTRAINT `FK_D012795CAD199442` FOREIGN KEY (`org_static_list_id`) REFERENCES `org_static_list` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `type` enum('G','E') COLLATE utf8_unicode_ci DEFAULT NULL,
  `server_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `server_port` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parameter1` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parameter2` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parameter3` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parameter4` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parameter5` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75A882CDDE12AB56` (`created_by`),
  KEY `IDX_75A882CD25F94802` (`modified_by`),
  KEY `IDX_75A882CD1F6FA0AF` (`deleted_by`),
  KEY `fk_appointment_connection_info_organization1_idx` (`organization_id`),
  CONSTRAINT `FK_75A882CD1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_75A882CD25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_75A882CD32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_75A882CDDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `organization_id` int(11) DEFAULT NULL,
  `appointments_id` int(11) DEFAULT NULL,
  `person_id_faculty` int(11) DEFAULT NULL,
  `person_id_student` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `has_attended` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CF90B68332C8A3DE` (`organization_id`),
  KEY `IDX_CF90B68323F542AE` (`appointments_id`),
  KEY `IDX_CF90B683FFB0AA26` (`person_id_faculty`),
  KEY `IDX_CF90B6835F056556` (`person_id_student`),
  CONSTRAINT `FK_CF90B68323F542AE` FOREIGN KEY (`appointments_id`) REFERENCES `Appointments` (`id`),
  CONSTRAINT `FK_CF90B68332C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_CF90B6835F056556` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_CF90B683FFB0AA26` FOREIGN KEY (`person_id_faculty`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointment_recepient_and_status`
--

LOCK TABLES `appointment_recepient_and_status` WRITE;
/*!40000 ALTER TABLE `appointment_recepient_and_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `appointment_recepient_and_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appointments_teams`
--

DROP TABLE IF EXISTS `appointments_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointments_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `appointments_id` int(11) DEFAULT NULL,
  `teams_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_53B92262DE12AB56` (`created_by`),
  KEY `IDX_53B9226225F94802` (`modified_by`),
  KEY `IDX_53B922621F6FA0AF` (`deleted_by`),
  KEY `fk_appointments_teams_appointments1_idx` (`appointments_id`),
  KEY `fk_appointments_teams_teams1_idx` (`teams_id`),
  CONSTRAINT `FK_53B922621F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_53B9226223F542AE` FOREIGN KEY (`appointments_id`) REFERENCES `Appointments` (`id`),
  CONSTRAINT `FK_53B9226225F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_53B92262D6365F12` FOREIGN KEY (`teams_id`) REFERENCES `Teams` (`id`),
  CONSTRAINT `FK_53B92262DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments_teams`
--

LOCK TABLES `appointments_teams` WRITE;
/*!40000 ALTER TABLE `appointments_teams` DISABLE KEYS */;
/*!40000 ALTER TABLE `appointments_teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_trail`
--

DROP TABLE IF EXISTS `audit_trail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `route` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `method` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `request` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:json_array)',
  `unit_of_work` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:json_array)',
  `status` enum('SUCCESS','FAIL') COLLATE utf8_unicode_ci DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `audited_at` datetime NOT NULL,
  `proxy_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B523E178DE12AB56` (`created_by`),
  KEY `IDX_B523E17825F94802` (`modified_by`),
  KEY `IDX_B523E1781F6FA0AF` (`deleted_by`),
  KEY `IDX_B523E178217BBB47` (`person_id`),
  KEY `IDX_B523E1788D40DF5C` (`proxy_by`),
  CONSTRAINT `FK_B523E1781F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B523E178217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B523E17825F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B523E1788D40DF5C` FOREIGN KEY (`proxy_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B523E178DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_trail`
--

LOCK TABLES `audit_trail` WRITE;
/*!40000 ALTER TABLE `audit_trail` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_trail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_sharing`
--

DROP TABLE IF EXISTS `calendar_sharing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_sharing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `person_id_sharedby` int(11) DEFAULT NULL,
  `person_id_sharedto` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `shared_on` datetime DEFAULT NULL,
  `is_selected` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CCF697AF32C8A3DE` (`organization_id`),
  KEY `IDX_CCF697AFBF1A33A5` (`person_id_sharedby`),
  KEY `IDX_CCF697AF57563323` (`person_id_sharedto`),
  CONSTRAINT `FK_CCF697AF32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_CCF697AF57563323` FOREIGN KEY (`person_id_sharedto`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_CCF697AFBF1A33A5` FOREIGN KEY (`person_id_sharedby`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendar_sharing`
--

LOCK TABLES `calendar_sharing` WRITE;
/*!40000 ALTER TABLE `calendar_sharing` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar_sharing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_info`
--

DROP TABLE IF EXISTS `contact_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address_1` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_2` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `primary_mobile` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alternate_mobile` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `home_phone` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `office_phone` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `primary_email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alternate_email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `primary_mobile_provider` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alternate_mobile_provider` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `primary_email_idx` (`primary_email`(15))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_info`
--

LOCK TABLES `contact_info` WRITE;
/*!40000 ALTER TABLE `contact_info` DISABLE KEYS */;
INSERT INTO `contact_info` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'9591900663',NULL,NULL,NULL,'david.warner@gmail.com',NULL,'9224852114',NULL,NULL,NULL,NULL,'2014-10-15 12:34:01',NULL,NULL),(2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'art.member@gmail.com',NULL,NULL,NULL,NULL,'2015-12-16 17:15:04',NULL,'2015-12-16 17:15:04',NULL,NULL);
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
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `display_seq` int(11) DEFAULT NULL,
  `parent_contact_types_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_741A993FA00BDDF3` (`parent_contact_types_id`),
  CONSTRAINT `FK_741A993FA00BDDF3` FOREIGN KEY (`parent_contact_types_id`) REFERENCES `contact_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `contact_types_id` int(11) DEFAULT NULL,
  `language_master_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D5F7EF541497AFC0` (`contact_types_id`),
  KEY `IDX_D5F7EF54D5D3A0FB` (`language_master_id`),
  CONSTRAINT `FK_D5F7EF541497AFC0` FOREIGN KEY (`contact_types_id`) REFERENCES `contact_types` (`id`),
  CONSTRAINT `FK_D5F7EF54D5D3A0FB` FOREIGN KEY (`language_master_id`) REFERENCES `language_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `organization_id` int(11) DEFAULT NULL,
  `person_id_student` int(11) DEFAULT NULL,
  `person_id_faculty` int(11) DEFAULT NULL,
  `contact_types_id` int(11) DEFAULT NULL,
  `activity_category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `contact_date` datetime NOT NULL,
  `note` longtext COLLATE utf8_unicode_ci NOT NULL,
  `is_discussed` tinyint(1) DEFAULT NULL,
  `is_high_priority` tinyint(1) DEFAULT NULL,
  `is_reveal` tinyint(1) DEFAULT NULL,
  `is_leaving` tinyint(1) DEFAULT NULL,
  `access_private` tinyint(1) DEFAULT NULL,
  `access_public` tinyint(1) DEFAULT NULL,
  `access_team` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3340157332C8A3DE` (`organization_id`),
  KEY `IDX_334015735F056556` (`person_id_student`),
  KEY `IDX_33401573FFB0AA26` (`person_id_faculty`),
  KEY `IDX_334015731497AFC0` (`contact_types_id`),
  KEY `IDX_334015731CC8F7EE` (`activity_category_id`),
  CONSTRAINT `FK_334015731497AFC0` FOREIGN KEY (`contact_types_id`) REFERENCES `contact_types` (`id`),
  CONSTRAINT `FK_334015731CC8F7EE` FOREIGN KEY (`activity_category_id`) REFERENCES `activity_category` (`id`),
  CONSTRAINT `FK_3340157332C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_334015735F056556` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_33401573FFB0AA26` FOREIGN KEY (`person_id_faculty`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `contacts_id` int(11) DEFAULT NULL,
  `teams_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8475AAAD719FB48E` (`contacts_id`),
  KEY `IDX_8475AAADD6365F12` (`teams_id`),
  CONSTRAINT `FK_8475AAAD719FB48E` FOREIGN KEY (`contacts_id`) REFERENCES `contacts` (`id`),
  CONSTRAINT `FK_8475AAADD6365F12` FOREIGN KEY (`teams_id`) REFERENCES `Teams` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts_teams`
--

LOCK TABLES `contacts_teams` WRITE;
/*!40000 ALTER TABLE `contacts_teams` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts_teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cur_org_calculated_risk_variable`
--

DROP TABLE IF EXISTS `cur_org_calculated_risk_variable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cur_org_calculated_risk_variable` (
  `org_id` int(11) DEFAULT NULL,
  `risk_model_id` bigint(20) DEFAULT NULL,
  `source` varchar(14) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `risk_variable_id` bigint(20) DEFAULT NULL,
  `variable_type` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `weight` decimal(8,4) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `SourceValue` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `bucket_value` int(11) DEFAULT NULL,
  `person_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cur_org_calculated_risk_variable`
--

LOCK TABLES `cur_org_calculated_risk_variable` WRITE;
/*!40000 ALTER TABLE `cur_org_calculated_risk_variable` DISABLE KEYS */;
/*!40000 ALTER TABLE `cur_org_calculated_risk_variable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datablock_master`
--

DROP TABLE IF EXISTS `datablock_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datablock_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datablock_ui_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `block_type` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C5DA18E9DE12AB56` (`created_by`),
  KEY `IDX_C5DA18E925F94802` (`modified_by`),
  KEY `IDX_C5DA18E91F6FA0AF` (`deleted_by`),
  KEY `datablockr_datablockuiid_idx` (`datablock_ui_id`),
  CONSTRAINT `FK_C5DA18E91F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_C5DA18E925F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_C5DA18E94351304E` FOREIGN KEY (`datablock_ui_id`) REFERENCES `datablock_ui` (`id`),
  CONSTRAINT `FK_C5DA18E9DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `datablock_desc` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EAD6BD4AF9AE3580` (`datablock_id`),
  KEY `IDX_EAD6BD4AB213FA4` (`lang_id`),
  CONSTRAINT `FK_EAD6BD4AB213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_EAD6BD4AF9AE3580` FOREIGN KEY (`datablock_id`) REFERENCES `datablock_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `datablock_id` int(11) DEFAULT NULL,
  `ebi_metadata_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4B799CFFDE12AB56` (`created_by`),
  KEY `IDX_4B799CFF25F94802` (`modified_by`),
  KEY `IDX_4B799CFF1F6FA0AF` (`deleted_by`),
  KEY `IDX_4B799CFFF9AE3580` (`datablock_id`),
  KEY `IDX_4B799CFFBB49FE75` (`ebi_metadata_id`),
  CONSTRAINT `FK_4B799CFF1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_4B799CFF25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_4B799CFFBB49FE75` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`),
  CONSTRAINT `FK_4B799CFFDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_4B799CFFF9AE3580` FOREIGN KEY (`datablock_id`) REFERENCES `datablock_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `datablock_id` int(11) DEFAULT NULL,
  `question_bank_id` int(11) DEFAULT NULL,
  `ebi_question_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `survey_questions_id` int(11) DEFAULT NULL,
  `factor_id` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `red_low` decimal(6,3) DEFAULT NULL,
  `red_high` decimal(6,3) DEFAULT NULL,
  `yellow_low` decimal(6,3) DEFAULT NULL,
  `yellow_high` decimal(6,3) DEFAULT NULL,
  `green_low` decimal(6,3) DEFAULT NULL,
  `green_high` decimal(6,3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BD00028DDE12AB56` (`created_by`),
  KEY `IDX_BD00028D25F94802` (`modified_by`),
  KEY `IDX_BD00028D1F6FA0AF` (`deleted_by`),
  KEY `fk_datablock_questions_survey1_idx` (`survey_id`),
  KEY `fk_datablock_questions_datablock_master1_idx` (`datablock_id`),
  KEY `fk_datablock_questions_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_datablock_questions_survey_questions1_idx` (`survey_questions_id`),
  KEY `fk_datablock_questions_factor1_idx` (`factor_id`,`deleted_at`,`datablock_id`),
  KEY `permfunc` (`ebi_question_id`,`deleted_at`,`datablock_id`),
  KEY `fk_datablock_questions_question_bank_id` (`question_bank_id`),
  CONSTRAINT `FK_BD00028D1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_BD00028D25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_BD00028D79F0E193` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`),
  CONSTRAINT `FK_BD00028DB3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_BD00028DBC88C1A3` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`),
  CONSTRAINT `FK_BD00028DCC63389E` FOREIGN KEY (`survey_questions_id`) REFERENCES `survey_questions` (`id`),
  CONSTRAINT `FK_BD00028DDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_BD00028DF9AE3580` FOREIGN KEY (`datablock_id`) REFERENCES `datablock_master` (`id`),
  CONSTRAINT `fk_datablock_questions_question_bank_id` FOREIGN KEY (`question_bank_id`) REFERENCES `question_bank` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `key` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ui_feature_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `key` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_config`
--

LOCK TABLES `ebi_config` WRITE;
/*!40000 ALTER TABLE `ebi_config` DISABLE KEYS */;
INSERT INTO `ebi_config` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'System_Admin_URL','http://synapse-qa-admin.mnv-tech.com/'),(2,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Survey_Url','http://wess-dev-1.internal.skyfactor.com'),(3,NULL,NULL,NULL,NULL,NULL,NULL,'Email_Login_Landing_Page','#/login'),(4,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Support_Helpdesk_Email_Address','support@map-works.com'),(5,NULL,NULL,NULL,NULL,NULL,'2016-12-13 19:15:06','Student_ResetPwd_URL_Prefix',NULL),(6,NULL,NULL,NULL,NULL,NULL,NULL,'Course_Upload_Remove_Definition_ColumnName','Remove'),(7,NULL,NULL,NULL,NULL,NULL,NULL,'Course_Upload_Remove_Definition_Type','Text'),(8,NULL,NULL,NULL,NULL,NULL,NULL,'Course_Upload_Remove_Definition_Desc','\"Remove\" to be added to remove the record'),(9,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_PrimaryConn_Definition_ColumnName','PrimaryConnect'),(10,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_PrimaryConn_Definition_Type','string'),(11,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_PrimaryConn_Definition_Desc','(Optional)Campus Faculty/StaffID for this student PrimaryDirectConnect'),(12,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_RiskGroup_Definition_ColumnName','RiskGroupID'),(13,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_RiskGroup_Definition_Type','number'),(14,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_RiskGroup_Definition_Desc','Please see the Risk tab in Set Up for available risk groups and the risk group IDs'),(15,NULL,NULL,NULL,NULL,NULL,NULL,'Ebi_Upload_Dir','risk_uploads,talking_points,roaster_uploads,survey_uploads,talking_points_uploads,factor_uploads,reports_master'),(16,NULL,NULL,NULL,NULL,NULL,NULL,'SubGroup_Upload_ParentGroup_ColumnName','Parent_Group_ID'),(17,NULL,NULL,NULL,NULL,NULL,NULL,'SubGroup_Upload_ParentGroup_ColumnType','Integer'),(18,NULL,NULL,NULL,NULL,NULL,NULL,'GroupFaculty_Upload_PermissionSet_ColumnName','Permission_Set'),(19,NULL,NULL,NULL,NULL,NULL,NULL,'GroupFaculty_Upload_PermissionSet_ColumnType','String'),(20,NULL,NULL,NULL,NULL,NULL,NULL,'GroupFaculty_Upload_PermissionSet_ColumnLength','100'),(21,NULL,NULL,NULL,NULL,NULL,NULL,'Upload_Queues','[\"q1\",\"q2\",\"q3\",\"q4\",\"q5\",\"q6\",\"q7\",\"q8\",\"q9\",\"q10\"]'),(22,NULL,'2015-12-16 17:07:06',NULL,'2015-12-16 17:07:06',NULL,NULL,'Disabled_TP_Orgs',NULL),(23,NULL,NULL,NULL,NULL,NULL,NULL,'Skyfactor_Admin_Activation_URL_Prefix','http://synapse-qa-admin.mnv-tech.com/#/createPassword/'),(24,NULL,NULL,NULL,NULL,NULL,NULL,'Longitudinal_Student_Management','0'),(25,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Omniscience','0'),(26,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Course_List_Page','#/student-course-list');
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `meta_key` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `definition_type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `metadata_type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `no_of_decimals` int(11) DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT NULL,
  `min_range` decimal(15,4) DEFAULT NULL,
  `max_range` decimal(15,4) DEFAULT NULL,
  `entity` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `meta_group` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `scope` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('active','archived') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_69B3B8EEDE12AB56` (`created_by`),
  KEY `IDX_69B3B8EE25F94802` (`modified_by`),
  KEY `IDX_69B3B8EE1F6FA0AF` (`deleted_by`),
  KEY `EM_metakey` (`meta_key`),
  KEY `modified` (`modified_at`,`created_at`),
  CONSTRAINT `FK_69B3B8EE1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_69B3B8EE25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_69B3B8EEDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_metadata`
--

LOCK TABLES `ebi_metadata` WRITE;
/*!40000 ALTER TABLE `ebi_metadata` DISABLE KEYS */;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `ebi_metadata_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `meta_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_description` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_50297B15DE12AB56` (`created_by`),
  KEY `IDX_50297B1525F94802` (`modified_by`),
  KEY `IDX_50297B151F6FA0AF` (`deleted_by`),
  KEY `IDX_50297B15B213FA4` (`lang_id`),
  KEY `IDX_50297B15BB49FE75` (`ebi_metadata_id`),
  CONSTRAINT `FK_50297B151F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_50297B1525F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_50297B15B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_50297B15BB49FE75` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`),
  CONSTRAINT `FK_50297B15DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_metadata_lang`
--

LOCK TABLES `ebi_metadata_lang` WRITE;
/*!40000 ALTER TABLE `ebi_metadata_lang` DISABLE KEYS */;
INSERT INTO `ebi_metadata_lang` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Cohort Names',NULL);
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `ebi_metadata_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `list_name` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `list_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2C2774C3DE12AB56` (`created_by`),
  KEY `IDX_2C2774C325F94802` (`modified_by`),
  KEY `IDX_2C2774C31F6FA0AF` (`deleted_by`),
  KEY `IDX_2C2774C3B213FA4` (`lang_id`),
  KEY `IDX_2C2774C3BB49FE75` (`ebi_metadata_id`),
  KEY `metadata_listname_idx` (`list_name`(250)),
  CONSTRAINT `FK_2C2774C31F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2C2774C325F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2C2774C3B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_2C2774C3BB49FE75` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`),
  CONSTRAINT `FK_2C2774C3DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `is_active` tinyint(1) DEFAULT NULL,
  `risk_indicator` tinyint(1) DEFAULT NULL,
  `intent_to_leave` tinyint(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `accesslevel_agg` tinyint(1) DEFAULT NULL,
  `accesslevel_ind_agg` tinyint(1) DEFAULT NULL,
  `inactive_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `ebi_permissionset_id` int(11) DEFAULT NULL,
  `datablock_id` int(11) DEFAULT NULL,
  `timeframe_all` tinyint(1) DEFAULT NULL,
  `current_calendar` tinyint(1) DEFAULT NULL,
  `previous_period` tinyint(1) DEFAULT NULL,
  `block_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1156882127C1FF01` (`ebi_permissionset_id`),
  KEY `IDX_11568821F9AE3580` (`datablock_id`),
  CONSTRAINT `FK_1156882127C1FF01` FOREIGN KEY (`ebi_permissionset_id`) REFERENCES `ebi_permissionset` (`id`),
  CONSTRAINT `FK_11568821F9AE3580` FOREIGN KEY (`datablock_id`) REFERENCES `datablock_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `ebi_permissionset_id` int(11) DEFAULT NULL,
  `feature_id` int(11) DEFAULT NULL,
  `private_create` tinyint(1) DEFAULT NULL,
  `team_create` tinyint(1) DEFAULT NULL,
  `team_view` tinyint(1) DEFAULT NULL,
  `public_create` tinyint(1) DEFAULT NULL,
  `public_view` tinyint(1) DEFAULT NULL,
  `timeframe_all` tinyint(1) DEFAULT NULL,
  `current_calendar` tinyint(1) DEFAULT NULL,
  `previous_period` tinyint(1) DEFAULT NULL,
  `next_period` tinyint(1) DEFAULT NULL,
  `receive_referral` tinyint(1) DEFAULT NULL,
  `reason_referral_private_create` tinyint(1) DEFAULT NULL,
  `reason_referral_team_create` tinyint(1) DEFAULT NULL,
  `reason_referral_team_view` tinyint(1) DEFAULT NULL,
  `reason_referral_public_create` tinyint(1) DEFAULT NULL,
  `reason_referral_public_view` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_29B6BC9027C1FF01` (`ebi_permissionset_id`),
  KEY `IDX_29B6BC9060E4B879` (`feature_id`),
  CONSTRAINT `FK_29B6BC9027C1FF01` FOREIGN KEY (`ebi_permissionset_id`) REFERENCES `ebi_permissionset` (`id`),
  CONSTRAINT `FK_29B6BC9060E4B879` FOREIGN KEY (`feature_id`) REFERENCES `feature_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `language_id` int(11) DEFAULT NULL,
  `ebi_permissionset_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `permissionset_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6C7D6BF982F1BAF4` (`language_id`),
  KEY `IDX_6C7D6BF927C1FF01` (`ebi_permissionset_id`),
  CONSTRAINT `FK_6C7D6BF927C1FF01` FOREIGN KEY (`ebi_permissionset_id`) REFERENCES `ebi_permissionset` (`id`),
  CONSTRAINT `FK_6C7D6BF982F1BAF4` FOREIGN KEY (`language_id`) REFERENCES `language_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `question_type_id` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `question_key` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `has_other` tinyint(1) DEFAULT NULL,
  `external_id` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `on_success_marker_page` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9050C5B4DE12AB56` (`created_by`),
  KEY `IDX_9050C5B425F94802` (`modified_by`),
  KEY `IDX_9050C5B41F6FA0AF` (`deleted_by`),
  KEY `fk_ebi_question_question_type1_idx` (`question_type_id`),
  KEY `fk_ebi_question_question_category1_idx` (`question_category_id`),
  KEY `IDX_EFD305F1CB90598E` (`question_type_id`),
  CONSTRAINT `FK_9050C5B41F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9050C5B425F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9050C5B4CB90598E` FOREIGN KEY (`question_type_id`) REFERENCES `question_type` (`id`),
  CONSTRAINT `FK_9050C5B4DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9050C5B4F142426F` FOREIGN KEY (`question_category_id`) REFERENCES `question_category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_question`
--

LOCK TABLES `ebi_question` WRITE;
/*!40000 ALTER TABLE `ebi_question` DISABLE KEYS */;
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
  `ebi_question_id` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `option_text` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `option_value` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B56C5C6CDE12AB56` (`created_by`),
  KEY `IDX_B56C5C6C25F94802` (`modified_by`),
  KEY `IDX_B56C5C6C1F6FA0AF` (`deleted_by`),
  KEY `fk_ebi_question_options_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_ebi_question_options_language_master1_idx` (`lang_id`),
  CONSTRAINT `FK_B56C5C6C1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B56C5C6C25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B56C5C6C79F0E193` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`),
  CONSTRAINT `FK_B56C5C6CB213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_B56C5C6CDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebi_question_id` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `question_text` longtext COLLATE utf8_unicode_ci,
  `question_rpt` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_CA5C32DE12AB56` (`created_by`),
  KEY `IDX_CA5C3225F94802` (`modified_by`),
  KEY `IDX_CA5C321F6FA0AF` (`deleted_by`),
  KEY `fk_ebi_questions_lang_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_ebi_questions_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `FK_CA5C321F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_CA5C3225F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_CA5C3279F0E193` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`),
  CONSTRAINT `FK_CA5C32B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_CA5C32DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_questions_lang`
--

LOCK TABLES `ebi_questions_lang` WRITE;
/*!40000 ALTER TABLE `ebi_questions_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_questions_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_search`
--

DROP TABLE IF EXISTS `ebi_search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(31) COLLATE utf8_unicode_ci DEFAULT NULL,
  `query_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `search_type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT NULL,
  `query` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_search`
--

LOCK TABLES `ebi_search` WRITE;
/*!40000 ALTER TABLE `ebi_search` DISABLE KEYS */;
INSERT INTO `ebi_search` VALUES (2,NULL,'Coordinator_Activity_All_Interaction',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'E',1,'SELECT A.id as AppointmentId, N.id as NoteId,R.id as ReferralId,C.id as ContactId,AL.id as activity_log_id,AL.created_at as activity_date,AL.activity_type as activity_type,AL.person_id_faculty as activity_created_by_id,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text,C.contact_types_id as activity_contact_type_id,CTL.description as activity_contact_type_text,R.status as activity_referral_status,C.note as contactDescription,R.note as referralDescription,A.description as appointmentDescription,N.note as noteDescription FROM activity_log as AL LEFT JOIN Appointments as A ON AL.appointments_id = A.id LEFT JOIN note as N ON AL.note_id = N.id LEFT JOIN note_teams as NT ON N.id = NT.note_id LEFT JOIN contacts as C ON AL.contacts_id = C.id LEFT JOIN contacts_teams as CT ON C.id = CT.contacts_id LEFT JOIN referrals as R ON AL.referrals_id = R.id LEFT JOIN referrals_teams as RT ON R.id = RT.referrals_id LEFT JOIN activity_category as AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person as P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN contact_types as CONT ON C.contact_types_id = CONT.id WHERE AL.person_id_student = $$studentId$$ AND AL.organization_id = $$orgId$$ AND AL.activity_type IN ($$acivityArr$$) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND CASE WHEN AL.activity_type = \"C\" THEN CONT.parent_contact_types_id = 1 OR CONT.id =1 ELSE 1=1 END AND AL.id NOT IN( SELECT ALOG.id FROM related_activities as related LEFT JOIN activity_log as ALOG ON related.note_id = ALOG.note_id where related.note_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN( SELECT ALOG.id FROM related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) GROUP BY AL.id ORDER BY AL.created_at desc -- maxscale route to server slave1 '),(3,NULL,'Activity_All_Interaction',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'E',1,'SELECT A.id AS AppointmentId, N.id AS NoteId, R.id AS ReferralId, C.id AS ContactId, AL.id AS activity_log_id, AL.created_at AS activity_date, AL.activity_type AS activity_type, AL.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, C.contact_types_id AS activity_contact_type_id, CTL.description AS activity_contact_type_text, R.status AS activity_referral_status, C.note AS contactDescription, R.note AS referralDescription, A.description AS appointmentDescription, N.note AS noteDescription, AL.created_at as created_date, A.start_date_time as app_created_date, C.contact_date as contact_created_date, AL.activity_date as act_date FROM activity_log AS AL LEFT JOIN Appointments AS A ON AL.appointments_id = A.id LEFT JOIN note AS N ON AL.note_id = N.id LEFT JOIN note_teams AS NT ON N.id = NT.note_id LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN referrals AS R ON AL.referrals_id = R.id LEFT JOIN referrals_teams AS RT ON R.id = RT.referrals_id LEFT JOIN activity_category AS AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person AS P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id LEFT JOIN organization_role as orgr ON orgr.organization_id = AL.organization_id LEFT JOIN referral_routing_rules as rr ON rr.activity_category_id = R.activity_category_id WHERE AL.person_id_student = $$studentId$$ AND AL.organization_id = $$orgId$$ AND AL.activity_type IN ($$acivityArr$$) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND CASE WHEN AL.activity_type = \"C\" THEN CONT.parent_contact_types_id = 1 OR CONT.id = 1 ELSE 1 = 1 END AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.appointment_id = ALOG.appointments_id WHERE related.appointment_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.referral_id = ALOG.referrals_id WHERE related.referral_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.note_id = ALOG.note_id WHERE related.note_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.contacts_id = ALOG.contacts_id WHERE related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN AL.activity_type = \"N\" THEN CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id FROM note_teams WHERE note_id = N.id AND deleted_at IS NULL)) AND $$noteTeamAccess$$ = 1 ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $$faculty$$ ELSE N.access_public = 1 AND $$notePublicAccess$$ = 1 END END ELSE CASE WHEN AL.activity_type = \"C\" THEN CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id FROM contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL)) AND $$contactTeamAccess$$ = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$faculty$$ ELSE C.access_public = 1 AND $$contactPublicAccess$$ = 1 END END ELSE CASE WHEN AL.activity_type = \"R\" THEN CASE WHEN R.access_team = 1 THEN RT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id FROM referrals_teams WHERE referrals_id = R.id AND deleted_at IS NULL)) AND (($$referralTeamAccess$$ = 1 and R.is_reason_routed = 0) OR ($$referralTeamAccessReasonRouted$$ = 1 and R.is_reason_routed = 1)) ELSE CASE WHEN R.access_private = 1 THEN R.person_id_faculty = $$faculty$$ ELSE R.access_public = 1 AND (($$referralPublicAccess$$ = 1 and R.is_reason_routed = 0) OR ($$referralPublicAccessReasonRouted$$ = 1 and R.is_reason_routed = 1)) END END OR R.person_id_assigned_to = $$faculty$$ OR R.person_id_faculty = $$faculty$$ OR orgr.person_id = $$faculty$$ and R.person_id_assigned_to is null AND orgr.role_id IN ($$roleIds$$) AND (rr.is_primary_coordinator = 1 AND rr.person_id IS NULL) ELSE CASE WHEN AL.activity_type = \"A\" THEN 1 = 1 ELSE 1 = 1 END END END END GROUP BY AL.id ORDER BY act_date DESC'),(4,NULL,'Activity_Email',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'E',1,'\nSELECT E.id AS activity_id, AL.id AS activity_log_id, E.created_at AS activity_date, E.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, E.email_subject AS activity_description, E.email_subject AS activity_email_subject, E.email_body AS activity_email_body FROM activity_log AS AL LEFT JOIN email AS E ON AL.email_id = E.id LEFT JOIN person AS P ON E.person_id_faculty = P.id LEFT JOIN activity_category AS AC ON E.activity_category_id = AC.id LEFT JOIN email_teams AS ET ON E.id = ET.email_id LEFT JOIN related_activities as RA ON E.id = RA.email_id LEFT JOIN activity_log AL1 ON RA.activity_log_id = AL1.id LEFT JOIN referrals AS R1 ON AL1.referrals_id = R1.id LEFT JOIN note AS N1 ON AL1.note_id = N1.id LEFT JOIN contacts AS C1 ON AL1.contacts_id = C1.id LEFT JOIN Appointments AS A1 ON AL1.appointments_id = A1.id LEFT JOIN email AS E1 ON AL1.appointments_id = E1.id WHERE E.person_id_student = $$studentId$$ AND E.deleted_at IS NULL AND (CASE WHEN AL1.activity_type IS NOT NULL AND ((AL1.activity_type = \"R\" AND R1.access_private = 1) OR (AL1.activity_type = \"C\" AND C1.access_private = 1) OR (AL1.activity_type = \"N\" AND N1.access_private = 1) OR (AL1.activity_type = \"E\" AND E1.access_private = 1)) THEN E.person_id_faculty = $$facultyId$$ ELSE CASE WHEN E.access_team = 1 THEN ET.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$facultyId$$ AND teams_id IN (SELECT teams_id from email_teams WHERE email_id = E.id AND deleted_at IS NULL)) AND $$teamAccess$$ = 1 ELSE CASE WHEN E.access_private = 1 THEN E.person_id_faculty = $$facultyId$$ ELSE E.access_public = 1 AND $$publicAccess$$ = 1 END END END OR E.person_id_faculty = $$facultyId$$) GROUP BY E.id order by E.created_at desc\n -- maxscale route to server slave1 ');
/*!40000 ALTER TABLE `ebi_search` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_search_criteria`
--

DROP TABLE IF EXISTS `ebi_search_criteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_search_criteria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebi_search_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `table_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `operator` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `join_condition` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_486A32643849DC27` (`ebi_search_id`),
  CONSTRAINT `FK_486A32643849DC27` FOREIGN KEY (`ebi_search_id`) REFERENCES `ebi_search` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_search_criteria`
--

LOCK TABLES `ebi_search_criteria` WRITE;
/*!40000 ALTER TABLE `ebi_search_criteria` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_search_criteria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_search_history`
--

DROP TABLE IF EXISTS `ebi_search_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_search_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `ebi_search_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `last_run` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8608E3D3DE12AB56` (`created_by`),
  KEY `IDX_8608E3D325F94802` (`modified_by`),
  KEY `IDX_8608E3D31F6FA0AF` (`deleted_by`),
  KEY `IDX_8608E3D3217BBB47` (`person_id`),
  KEY `IDX_8608E3D33849DC27` (`ebi_search_id`),
  CONSTRAINT `FK_8608E3D31F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8608E3D3217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8608E3D325F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8608E3D33849DC27` FOREIGN KEY (`ebi_search_id`) REFERENCES `ebi_search` (`id`),
  CONSTRAINT `FK_8608E3D3DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `ebi_search_id` int(11) DEFAULT NULL,
  `language_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sub_category_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DC486273849DC27` (`ebi_search_id`),
  KEY `IDX_DC4862782F1BAF4` (`language_id`),
  CONSTRAINT `FK_DC486273849DC27` FOREIGN KEY (`ebi_search_id`) REFERENCES `ebi_search` (`id`),
  CONSTRAINT `FK_DC4862782F1BAF4` FOREIGN KEY (`language_id`) REFERENCES `language_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_search_lang`
--

LOCK TABLES `ebi_search_lang` WRITE;
/*!40000 ALTER TABLE `ebi_search_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_search_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_template`
--

DROP TABLE IF EXISTS `ebi_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_template` (
  `key` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` enum('y','n') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_template`
--

LOCK TABLES `ebi_template` WRITE;
/*!40000 ALTER TABLE `ebi_template` DISABLE KEYS */;
INSERT INTO `ebi_template` VALUES ('Pdf_AcademicUpdates_Header_Template','y'),('Pdf_CategoryType_Body_Template','y'),('Pdf_Course_Footer_Template','y'),('Pdf_Courses_Header_Template','y'),('Pdf_CoursesFaculty_Header_Template','y'),('Pdf_CoursesStudents_Header_Template','y'),('Pdf_CourseStudent_Footer_Template','y'),('Pdf_DateType_Body_Template','y'),('Pdf_Faculty_Header_Template','y'),('Pdf_GroupFaculty_Header_Template','y'),('Pdf_GroupName_Body_Template','y'),('Pdf_GroupStudent_ExplanatoryNotes_Template','y'),('Pdf_GroupStudent_Header_Template','y'),('Pdf_NumberType_Body_Template','y'),('Pdf_StringType_Body_Template','y'),('Pdf_Student_Footer_Template','y'),('Pdf_Student_Header_Template','y'),('Pdf_SubGroup_Footer_Template','y'),('Pdf_SubGroup_Header_Template','y'),('Pdf_TextType_Body_Template','y');
/*!40000 ALTER TABLE `ebi_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_template_lang`
--

DROP TABLE IF EXISTS `ebi_template_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_template_lang` (
  `ebi_template_key` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `lang_id` int(11) NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`ebi_template_key`,`lang_id`),
  KEY `fk_ebi_template_lang_language_master1_idx` (`lang_id`),
  KEY `fk_ebi_template_key_idx` (`ebi_template_key`),
  CONSTRAINT `FK_5E2527B11644997F` FOREIGN KEY (`ebi_template_key`) REFERENCES `ebi_template` (`key`),
  CONSTRAINT `FK_5E2527B1B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_template_lang`
--

LOCK TABLES `ebi_template_lang` WRITE;
/*!40000 ALTER TABLE `ebi_template_lang` DISABLE KEYS */;
INSERT INTO `ebi_template_lang` VALUES ('Pdf_GroupName_Body_Template',1,NULL,'<div id=\'outerContainer\'>\n                <div class=\'align1 subHeadingDiv\'>\n                    <div class=\'columnNameContainer details\'><p class=\'idHeading\'>$$column_name$$</p></div>\n                    <div class=\'columnNameContainer dataTypeContainer\'><p>$$description$$</p></div>\n                </div>\n                <div class=\'align1 userInfo\'><p class=\'userInfoHeading\'>Upload Information</p>\n                    <div class=\'horizontalDottedLine\'></div>\n                </div>\n                <div class=\'align1 userInfoDetails\'>\n                    <div class=\'columnNameContainer columnNameContainer2\'><p><span class=\'italicStyler\'>Column Name:</span>\n                    <span class=\'boldStyler\'>$$column_name$$</span></p></div>\n                    <br>\n                    <div class=\'columnNameContainer dataTypeContainer\'><p><span class=\'italicStyler\'>Data Type:</span>Text</p></div>\n                </div>\n            </div>'),('Pdf_GroupStudent_ExplanatoryNotes_Template',1,NULL,'<!DOCTYPE HTML>\n                                        <html>\n                                        <head>\n                                            <title></title>\n                                            <style>\n                                                .container {\n                                                    padding: 60px 50px;\n                                                }\n                                        \n                                                p, body {\n                                                    margin: 0;\n                                                    color: #003366;\n                                                }\n                                        \n                                                #outerContainer {\n                                                    float: left;\n                                                    width: 100%;\n                                                    box-sizing: border-box;\n                                                }\n                                        \n                                                #outerContainer .align1 {\n                                                    float: left;\n                                                    width: 100%;\n                                                }\n                                        \n                                                #outerContainer .columnNameContainer {\n                                                    float: left;\n                                                    display: inline-block;\n                                                }\n                                        \n                                                #outerContainer .heading {\n                                                    font-weight: bold;\n                                                    font-size: 22px;\n                                                }\n                                        \n                                                #outerContainer .headingDiv {\n                                                    margin-bottom: 30px;\n                                                }\n                                        \n                                                #outerContainer .subHeadingDiv {\n                                                    margin-bottom: 15px;\n                                                    float: left;\n                                                    width: 100%;\n                                                }\n                                        \n                                                #outerContainer .userInfo {\n                                                    margin-bottom: 5px;\n                                                }\n                                        \n                                                #outerContainer .userInfoDetails {\n                                                    height: auto;\n                                                    margin-bottom: 15px;\n                                                }\n                                        \n                                                #outerContainer .subHeading {\n                                                    font-weight: bold;\n                                                    font-size: 18px;\n                                                }\n                                        \n                                                #outerContainer .idHeading {\n                                                    font-weight: bold;\n                                                    font-size: 18px;\n                                                }\n                                        \n                                                #outerContainer .horizontalLine {\n                                                    background-color: #ccc;\n                                                    width: 100%;\n                                                    height: 2px;\n                                                }\n                                        \n                                                #outerContainer .horizontalDottedLine {\n                                                    border-bottom: dotted;\n                                                    border-width: 4px;\n                                                    color: #ccc;\n                                                }\n                                        \n                                                #outerContainer .boldStyler {\n                                                    font-weight: bold;\n                                                }\n                                        \n                                                #outerContainer .columnNameContainer2 {\n                                                    min-width: 30%;\n                                                    width: auto;\n                                                    height: auto;\n                                                    padding: 0px 10px;\n                                                }\n                                        \n                                                #outerContainer .details {\n                                                    min-width: 30%;\n                                                    width: auto;\n                                                    height: auto;\n                                                }\n                                        \n                                                #outerContainer .dataTypeContainer {\n                                                    width: 68%;\n                                                    height: auto;\n                                                }\n                                        \n                                                #outerContainer .userInfoHeading {\n                                                    margin-bottom: 3px;\n                                                }\n                                        \n                                                #outerContainer .validvalues {\n                                                    padding: 0px 10px;\n                                                }\n                                        \n                                                #outerContainer .italicStyler {\n                                                    font-style: italic;\n                                                }\n                                        \n                                                .wrapper {\n                                                    width: 100%;\n                                                    box-sizing: border-box;\n                                                    padding-top: 30px;\n                                                }\n                                        \n                                                .wrapper .header {\n                                                    padding-bottom: 10px;\n                                                }\n                                        \n                                                .inner-content {\n                                                    margin-bottom: 15px;\n                                                }\n                                        \n                                                .content ul {\n                                                    padding: 0px 40px 0px 40px;\n                                                }\n                                        \n                                                .table-data {\n                                                    margin: 30px 0px 15px 0px;\n                                                    width: 100%;\n                                                }\n                                        \n                                                .table-data th {\n                                                    color: #003366;\n                                                    text-align: left;\n                                                }\n                                        \n                                                .table-data th {\n                                                    padding: 5px;\n                                                }\n                                        \n                                                .table-data td {\n                                                    padding: 5px;\n                                                }\n                                        \n                                                .table-data td a {\n                                                    color: #003366;\n                                                }\n                                        \n                                                .table-data tr:nth-child(even) {\n                                                    background: #e0f3ff;\n                                                }\n                                        \n                                                .table-data tr:nth-child(odd) {\n                                                    background: #FFF;\n                                                }\n                                                table {\n                                                        page-break-inside: avoid;\n                                                    }\n                                        \n                                                @media print {\n                                                    .table-data th:nth-child(5) {\n                                                        min-width: 130px;\n                                                    }\n                                        \n                                                    p {\n                                                        page-break-inside: avoid;\n                                                    }\n                                        \n                                                    table {\n                                                        page-break-inside: avoid;\n                                                    }\n                                                }\n                                            </style>\n                                        </head>\n                                        <body>\n                                        <hr/>\n                                        <div class=\"validvalues align1\"><p></p>\n                                            <ul class=\"valueslist\"></ul>\n                                        </div>\n                                        <div id=\"outerContainer\">\n                                            <div class=\"columnNameContainer details\"><p class=\"idHeading\">Explanatory Notes</p></div>\n                                        </div>\n                                        <div class=\"wrapper\">\n                                            <div class=\"content\">\n                                                <div class=\"inner-content\">Mapworks 3.3 introduced this new format for uploading students into groups. The new\n                                                    Format applies to both FTP and files uploaded through the setup webpage for groups.\n                                                    In the new format, each student should appear on only one row. First, there are four columns to identify the\n                                                    student:\n                                                </div>\n                                                <ul>\n                                                    <li>ExternalID (required)</li>\n                                                    <li>Firstname (optional, for readability)</li>\n                                                    <li>Lastname (optional, for readability)</li>\n                                                    <li>PrimaryEmail (optional, for readability)</li>\n                                                </ul>\n                                                <p>These are followed one column per top-level group, for as many top-level groups as you have defined. Each\n                                                    column name contains the group ID of the top-level group. The ALLSTUDENTS group is not included, since that\n                                                    is automatically maintained by the system.</p>\n                                            </div>\n                                            <div class=\"content\">\n                                                <div class=\"inner-content\">For each student row, here is what can be in each cell under a Top-Level Group\n                                                    column:\n                                                </div>\n                                                <ul>\n                                                    <li>The cell can be\n                                                        <b>empty</b>. This means there is no change to the student\'s membership in any of the groups under this\n                                                        top-level group.\n                                                    </li>\n                                                    <li>The cell can include\n                                                        <b>one or more group ID\'s</b>. If there is more than one, they need to be separated by semicolons. These\n                                                        can be the group ID of the top-level group or any of its subgroups. This will add the student to the\n                                                        groups. The effect is cumulative &#45; it does not remove students from other groups they are in under\n                                                        this hierarchy.\n                                                    </li>\n                                                    <li>The cell can also include\n                                                        <b>#clear</b>, either by itself, or along with the group names. If #clear is present, then the student\n                                                        will be removed from all groups under the top-level group, as well as added to any groups named in the\n                                                        cell.\n                                                    </li>\n                                                </ul>\n                                                <p>The example below illustrates a campus with three top-level groups: ResLife, Major and Athletics. </p>\n                                                <p>The ResLife group hierarchy consists of ResLife at the top, with areas, halls and floors underneath.</p>\n                                            </div>\n                                            <table class=\"table-data\" border=\"1\">\n                                                <tr>\n                                                    <th>ExternalID</th>\n                                                    <th>FirstName</th>\n                                                    <th>LastName</th>\n                                                    <th>PrimaryEmail</th>\n                                                    <th style=\"min-width:130px;\">ResLife</th>\n                                                    <th>Major</th>\n                                                    <th>Athletics</th>\n                                                </tr>\n                                                <tr>\n                                                    <td>A091873</td>\n                                                    <td>John</td>\n                                                    <td>Smith</td>\n                                                    <td>\n                                                        <a href=\"#\">Smith@northstate.edu</a>\n                                                    </td>\n                                                    <td>#clear;Jones 1</td>\n                                                    <td>#clear;Voice;Painting</td>\n                                                    <td>Baseball</td>\n                                                </tr>\n                                                <tr>\n                                                    <td>A091874</td>\n                                                    <td>Tulsi</td>\n                                                    <td>Able</td>\n                                                    <td>\n                                                        <a href=\"#\">Able@northstate.edu</a>\n                                                    </td>\n                                                    <td>#clear; Smith 1</td>\n                                                    <td></td>\n                                                    <td></td>\n                                                </tr>\n                                                <tr>\n                                                    <td>A091875</td>\n                                                    <td>LaDeitra</td>\n                                                    <td>Baker</td>\n                                                    <td>\n                                                        <a href=\"#\">Baker@northstate.edu</a>\n                                                    </td>\n                                                    <td>#clear</td>\n                                                    <td>#clear;CompSci</td>\n                                                    <td>Baseball</td>\n                                                </tr>\n                                                <tr>\n                                                    <td>A091876</td>\n                                                    <td>James</td>\n                                                    <td>Charlie</td>\n                                                    <td>\n                                                        <a href=\"#\">Charlie@northstate.edu</a>\n                                                    </td>\n                                                    <td>#clear;Smith 2</td>\n                                                    <td>#clear;Civil Eng</td>\n                                                    <td></td>\n                                                </tr>\n                                                <tr>\n                                                    <td>A091877</td>\n                                                    <td>Sudhakar</td>\n                                                    <td>Delta</td>\n                                                    <td>\n                                                        <a href=\"#\">Delta@northstate.edu</a>\n                                                    </td>\n                                                    <td></td>\n                                                    <td></td>\n                                                    <td>#clear;Quidditch</td>\n                                                </tr>\n                                            </table>\n                                            <div class=\"content\">\n                                                <div class=\"inner-content\">In our example,\n                                                </div>\n                                                <ul>\n                                                    <li>Student John Smith is cleared from any ResLife groups he is in, then added to Jones 1, which is a\n                                                        subgroup under ResLife. Jones 1 can be at any level under ResLife.\n                                                    </li>\n                                                    <li>Student John Smith is cleared from any Major groups he is in, then added to both the Voice group and\n                                                        Painting group, which are subgroups of Major.\n                                                    </li>\n                                                    <li>Student John Smith is added to the Baseball subgroup of Athletics. This is in addition to any other\n                                                        athletic groups he is a member of.\n                                                    </li>\n                                                    <li>Student Tulsi Able is cleared from any ResLife groups she is in, then added to Smith 1. Since the Major\n                                                        and Athletics columns are blank, there is no change to her group membership for Major, Athletics, or\n                                                        their subgroups.\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                        </div>\n                                        </body>\n                                        </html>');
/*!40000 ALTER TABLE `ebi_template_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebi_users`
--

DROP TABLE IF EXISTS `ebi_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebi_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_address` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` varbinary(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_53649663DE12AB56` (`created_by`),
  KEY `IDX_5364966325F94802` (`modified_by`),
  KEY `IDX_536496631F6FA0AF` (`deleted_by`),
  CONSTRAINT `FK_536496631F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_5364966325F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_53649663DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_users`
--

LOCK TABLES `ebi_users` WRITE;
/*!40000 ALTER TABLE `ebi_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebi_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email`
--

DROP TABLE IF EXISTS `email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `person_id_student` int(11) DEFAULT NULL,
  `person_id_faculty` int(11) DEFAULT NULL,
  `activity_category_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `email_subject` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_body` longtext COLLATE utf8_unicode_ci,
  `email_bcc_list` longtext COLLATE utf8_unicode_ci COMMENT 'BCC faculty list in comma separated format',
  `access_private` tinyint(1) DEFAULT NULL,
  `access_public` tinyint(1) DEFAULT NULL,
  `access_team` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E7927C74DE12AB56` (`created_by`),
  KEY `IDX_E7927C7425F94802` (`modified_by`),
  KEY `IDX_E7927C741F6FA0AF` (`deleted_by`),
  KEY `fk_email_organization1` (`organization_id`),
  KEY `fk_email_person1` (`person_id_student`),
  KEY `fk_email_person2` (`person_id_faculty`),
  KEY `fk_email_activity_category1` (`activity_category_id`),
  CONSTRAINT `FK_E7927C741CC8F7EE` FOREIGN KEY (`activity_category_id`) REFERENCES `activity_category` (`id`),
  CONSTRAINT `FK_E7927C741F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E7927C7425F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E7927C7432C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_E7927C745F056556` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E7927C74DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E7927C74FFB0AA26` FOREIGN KEY (`person_id_faculty`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email`
--

LOCK TABLES `email` WRITE;
/*!40000 ALTER TABLE `email` DISABLE KEYS */;
/*!40000 ALTER TABLE `email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_teams`
--

DROP TABLE IF EXISTS `email_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `email_id` int(11) DEFAULT NULL,
  `teams_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B8F63D7DE12AB56` (`created_by`),
  KEY `IDX_B8F63D725F94802` (`modified_by`),
  KEY `IDX_B8F63D71F6FA0AF` (`deleted_by`),
  KEY `fk_email_teams_email1` (`email_id`),
  KEY `fk_email_teams_teams1` (`teams_id`),
  CONSTRAINT `FK_B8F63D71F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B8F63D725F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B8F63D7A832C1C9` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`),
  CONSTRAINT `FK_B8F63D7D6365F12` FOREIGN KEY (`teams_id`) REFERENCES `Teams` (`id`),
  CONSTRAINT `FK_B8F63D7DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_teams`
--

LOCK TABLES `email_teams` WRITE;
/*!40000 ALTER TABLE `email_teams` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_template`
--

DROP TABLE IF EXISTS `email_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `email_key` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` int(11) DEFAULT NULL,
  `from_email_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bcc_recipient_list` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_template`
--

LOCK TABLES `email_template` WRITE;
/*!40000 ALTER TABLE `email_template` DISABLE KEYS */;
INSERT INTO `email_template` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_Notification',1,'no-reply@mapworks.com',''),(2,NULL,NULL,NULL,NULL,NULL,NULL,'Faculty_Upload_Notification',1,'no-reply@mapworks.com',''),(3,NULL,NULL,NULL,NULL,NULL,NULL,'Email_Login_Url',1,'no-reply@mapworks.com',''),(4,NULL,NULL,NULL,NULL,NULL,NULL,'Forgot_Password_Student',NULL,'no-reply@mapworks.com',''),(5,NULL,NULL,NULL,NULL,NULL,NULL,'Sucessful_Password_Reset_Student',1,'no-reply@mapworks.com',''),(6,NULL,NULL,NULL,NULL,NULL,NULL,'Welcome_To_Mapworks',1,'no-reply@mapworks.com',''),(7,NULL,NULL,NULL,NULL,NULL,NULL,'Academic_Update_Request_Staff_Closed',1,'no-reply@mapworks.com',''),(8,NULL,NULL,NULL,NULL,NULL,NULL,'Email_Notification_Staff_to_Student',1,'no-reply@mapworks.com',''),(9,NULL,NULL,NULL,NULL,NULL,NULL,'Email_PDF_Report_Student',NULL,'no-reply@mapworks.com',''),(10,NULL,NULL,NULL,NULL,NULL,NULL,'Welcome_Email_Skyfactor_Admin_User',NULL,'no-reply@mapworks.com',NULL),(11,NULL,NULL,NULL,NULL,NULL,NULL,'Group_Student_Upload_Notification',1,'no-reply@mapworks.com',NULL),(12,NULL,NULL,NULL,NULL,NULL,NULL,'Group_Faculty_Upload_Notification',1,'no-reply@mapworks.com',NULL),(13,NULL,NULL,NULL,NULL,NULL,NULL,'Group_Upload_Notification',1,'no-reply@mapworks.com',NULL),(14,NULL,NULL,NULL,NULL,NULL,NULL,'Static_List_Upload_Notification',1,'no-reply@mapworks.com',NULL);
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
  `email_template_id` int(11) DEFAULT NULL,
  `language_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `body` longtext COLLATE utf8_unicode_ci,
  `subject` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_16F0BDA7131A730F` (`email_template_id`),
  KEY `IDX_16F0BDA782F1BAF4` (`language_id`),
  CONSTRAINT `FK_16F0BDA7131A730F` FOREIGN KEY (`email_template_id`) REFERENCES `email_template` (`id`),
  CONSTRAINT `FK_16F0BDA782F1BAF4` FOREIGN KEY (`language_id`) REFERENCES `language_master` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_template_lang`
--

LOCK TABLES `email_template_lang` WRITE;
/*!40000 ALTER TABLE `email_template_lang` DISABLE KEYS */;
INSERT INTO `email_template_lang` VALUES (4,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n<div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>\nHi $$firstname$$,<br/></br>\n        \nPlease use the link below and follow the displayed instructions to create your new password. This link will expire after $$Reset_Password_Expiry_Hrs$$ hours.<br />\n<br/>\n<a href=\'$$activation_token$$\'>$$activation_token$$</a><br/><br/>\n        \nIf you believe that you received this email in error or if you have any questions,please contact Mapworks support at <span style=\'color: #99ccff;\'>$$Support_Helpdesk_Email_Address$$</span>.<br/><br/>\n<p>Thank you.</br></p>\n<p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"$$Skyfactor_Mapworks_logo$$\"/><br/></p>\n<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>\n</div>\n</html>','How to reset your Mapworks password'),(5,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'<html>\n <div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>\n Hi $$firstname$$,<br/><br/>\n        \n Your Mapworks password has been changed. If you believe this is an error, please contact Mapworks support at &nbsp;<a href=\"mailto:$$Support_Helpdesk_Email_Address$$\" class=\"external-link\" rel=\"nofollow\">$$Support_Helpdesk_Email_Address$$</a>\n <br/><br/>\n<p>Thank you.</br></p>\n<p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"$$Skyfactor_Mapworks_logo$$\"/><br/></p>\n<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>\n        \n </div>\n </html>','Mapworks password reset'),(8,9,1,NULL,NULL,NULL,NULL,NULL,NULL,'<!DOCTYPE html>\n<html>\n<body>\n<style>\n	body {\n		background: none repeat scroll 0 0# f4f4f4;\n	}\n	div {\n		display: block;\n		padding: 15px;\n		width: 100%;\n	}\n	p {\n		font - family: helvetica, arial, verdana, san - serif;\n		font - size: 13px;\n		color: #333;\n	}\n</style>\n<div>\n	<p>Hi $$studentname$$,</p>\n	<p>Your Student report is now available. Please click the link below to access and view your results.</p>\n    <p><a href =\"$$pdf_report$$\">Report view</a><p>\n	<p>If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href=\"mailto:support@map-works.com\">support@map-works.com</a>.</p>\n	<p>Thank you.</br>\n	<img src=\"$$Skyfactor_Mapworks_logo$$\" alt =\"Skyfactor Mapworks logo\" title =\"Skyfactor Mapworks logo\" /><p>\n</div>\n</body>\n</html>','Student Report'),(9,10,1,NULL,NULL,NULL,NULL,NULL,NULL,'<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n <html xmlns=\"http://www.w3.org/1999/xhtml\">\n	<head>\n		<style>\n		body {\n    background: none repeat scroll 0 0 #f4f4f4;\n	\n}\n		table {\n    padding: 21px;\n    width: 799px;\n	font-family: helvetica,arial,verdana,san-serif;\n	font-size:13px;\n	color:#333;\n	}\n		</style>\n	</head>\n	<body>\n	\n		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">\n			<tbody>\n			\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi $$firstname$$,</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Welcome to Skyfactor. Use the link below to create your password and login to user management. This link will expire in 24 hours.</td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\">\n					<td> <a href=\"$$activation_token$$\">$$activation_token$$</a> </td>\n				</tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\">\n					<td> If you believe that you received this email in error or if you have any questions, please contact Mapworks support at  <a href=\"mailto:$$Support_Helpdesk_Email_Address$$\">$$Support_Helpdesk_Email_Address$$</a> </td>\n				</tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor team.<br/><img src=\"$$Skyfactor_Mapworks_logo$$\" alt=\"Skyfactor Mapworks logo\" title=\"Skyfactor Mapworks logo\" /></td></tr>\n				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>\n			\n			</tbody>\n		</table>\n	</body>\n</html>','Welcome to Skyfactor'),(10,11,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4;} table {padding: 21px; width: 799px; font-family: helvetica,arial,verdana,san-serif; font-size:13px; color:#333; }</style></head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$user_first_name$$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your student group upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>','Mapworks student group upload has finished'),(11,12,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4;} table {padding: 21px; width: 799px; font-family: helvetica,arial,verdana,san-serif; font-size:13px; color:#333; }</style></head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$user_first_name$$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your faculty group upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>','Mapworks faculty group upload has finished'),(12,13,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4;} table {padding: 21px; width: 799px; font-family: helvetica,arial,verdana,san-serif; font-size:13px; color:#333; }</style></head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$user_first_name$$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your group upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>','Mapworks group upload has finished'),(13,14,1,NULL,NULL,NULL,NULL,NULL,NULL,'<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4;} table {padding: 21px; width: 799px; font-family: helvetica,arial,verdana,san-serif; font-size:13px; color:#333; }</style></head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$user_first_name$$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your static list upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>','Mapworks static list upload has finished');
/*!40000 ALTER TABLE `email_template_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entity`
--

DROP TABLE IF EXISTS `entity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entity`
--

LOCK TABLES `entity` WRITE;
/*!40000 ALTER TABLE `entity` DISABLE KEYS */;
/*!40000 ALTER TABLE `entity` ENABLE KEYS */;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_ED38EC00DE12AB56` (`created_by`),
  KEY `IDX_ED38EC0025F94802` (`modified_by`),
  KEY `IDX_ED38EC001F6FA0AF` (`deleted_by`),
  KEY `fk_factor_survey1_idx` (`survey_id`),
  CONSTRAINT `FK_ED38EC001F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_ED38EC0025F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_ED38EC00B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_ED38EC00DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `IDX_312D3370DE12AB56` (`created_by`),
  KEY `IDX_312D337025F94802` (`modified_by`),
  KEY `IDX_312D33701F6FA0AF` (`deleted_by`),
  KEY `IDX_312D3370B213FA4` (`lang_id`),
  KEY `fk_factors_lang_factors1_idx` (`factor_id`),
  CONSTRAINT `FK_312D33701F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_312D337025F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_312D3370B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_312D3370BC88C1A3` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`),
  CONSTRAINT `FK_312D3370DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `factor_id` int(11) DEFAULT NULL,
  `ebi_question_id` int(11) DEFAULT NULL,
  `survey_questions_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2F1D0828DE12AB56` (`created_by`),
  KEY `IDX_2F1D082825F94802` (`modified_by`),
  KEY `IDX_2F1D08281F6FA0AF` (`deleted_by`),
  KEY `fk_factor_questions_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_factor_questions_survey_questions1_idx` (`survey_questions_id`),
  KEY `fk_factor_questions_factor1` (`factor_id`,`ebi_question_id`),
  KEY `modified` (`modified_at`,`created_at`),
  CONSTRAINT `FK_2F1D08281F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2F1D082825F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2F1D082879F0E193` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`),
  CONSTRAINT `FK_2F1D0828BC88C1A3` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`),
  CONSTRAINT `FK_2F1D0828CC63389E` FOREIGN KEY (`survey_questions_id`) REFERENCES `survey_questions` (`id`),
  CONSTRAINT `FK_2F1D0828DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feature_master`
--

LOCK TABLES `feature_master` WRITE;
/*!40000 ALTER TABLE `feature_master` DISABLE KEYS */;
INSERT INTO `feature_master` VALUES (7,NULL,NULL,NULL,NULL,NULL,NULL),(8,NULL,NULL,NULL,NULL,NULL,NULL);
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
  `feature_master_id` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `feature_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C2AF353CA5AC1B83` (`feature_master_id`),
  KEY `IDX_C2AF353CB213FA4` (`lang_id`),
  CONSTRAINT `FK_C2AF353CA5AC1B83` FOREIGN KEY (`feature_master_id`) REFERENCES `feature_master` (`id`),
  CONSTRAINT `FK_C2AF353CB213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feature_master_lang`
--

LOCK TABLES `feature_master_lang` WRITE;
/*!40000 ALTER TABLE `feature_master_lang` DISABLE KEYS */;
INSERT INTO `feature_master_lang` VALUES (7,7,1,NULL,NULL,NULL,NULL,NULL,NULL,'Email'),(8,8,1,NULL,NULL,NULL,NULL,NULL,NULL,'Primary Campus Connection Referral Routing');
/*!40000 ALTER TABLE `feature_master_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `group_course_discriminator`
--

DROP TABLE IF EXISTS `group_course_discriminator`;
/*!50001 DROP VIEW IF EXISTS `group_course_discriminator`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `group_course_discriminator` (
  `association` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ind_question`
--

DROP TABLE IF EXISTS `ind_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ind_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `question_type_id` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_category_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `has_other` tinyint(1) DEFAULT NULL,
  `external_id` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6ABE9142DE12AB56` (`created_by`),
  KEY `IDX_6ABE914225F94802` (`modified_by`),
  KEY `IDX_6ABE91421F6FA0AF` (`deleted_by`),
  KEY `fk_ind_question_question_category1_idx` (`question_category_id`),
  KEY `fk_ind_question_survey1_idx` (`survey_id`),
  KEY `fk_ind_question_question_type1_idx` (`question_type_id`),
  CONSTRAINT `FK_6ABE91421F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_6ABE914225F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_6ABE9142B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_6ABE9142CB90598E` FOREIGN KEY (`question_type_id`) REFERENCES `question_type` (`id`),
  CONSTRAINT `FK_6ABE9142DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_6ABE9142F142426F` FOREIGN KEY (`question_category_id`) REFERENCES `question_category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ind_question`
--

LOCK TABLES `ind_question` WRITE;
/*!40000 ALTER TABLE `ind_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `ind_question` ENABLE KEYS */;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `question_text` varchar(3000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_rpt` varchar(3000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ind_question_id`,`lang_id`),
  KEY `IDX_80610F30DE12AB56` (`created_by`),
  KEY `IDX_80610F3025F94802` (`modified_by`),
  KEY `IDX_80610F301F6FA0AF` (`deleted_by`),
  KEY `fk_survey_questions_lang_language_master1_idx` (`lang_id`),
  KEY `fk_survey_questions_lang_ind_question1_idx` (`ind_question_id`),
  CONSTRAINT `FK_80610F301F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_80610F3025F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_80610F3051DCB924` FOREIGN KEY (`ind_question_id`) REFERENCES `ind_question` (`id`),
  CONSTRAINT `FK_80610F30B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_80610F30DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `text` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `color_hex` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `min_value` decimal(8,4) DEFAULT NULL,
  `max_value` decimal(8,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CB20FE3ADE12AB56` (`created_by`),
  KEY `IDX_CB20FE3A25F94802` (`modified_by`),
  KEY `IDX_CB20FE3A1F6FA0AF` (`deleted_by`),
  CONSTRAINT `FK_CB20FE3A1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_CB20FE3A25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_CB20FE3ADE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intent_to_leave`
--

LOCK TABLES `intent_to_leave` WRITE;
/*!40000 ALTER TABLE `intent_to_leave` DISABLE KEYS */;
INSERT INTO `intent_to_leave` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'darkgray','leave-intent-not-stated.png','#cccccc',99.0000,99.0000);
/*!40000 ALTER TABLE `intent_to_leave` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `issue`
--

DROP TABLE IF EXISTS `issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `issue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `survey_questions_id` int(11) DEFAULT NULL,
  `factor_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `min` decimal(8,4) DEFAULT NULL,
  `max` decimal(8,4) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_12AD233EDE12AB56` (`created_by`),
  KEY `IDX_12AD233E25F94802` (`modified_by`),
  KEY `IDX_12AD233E1F6FA0AF` (`deleted_by`),
  KEY `fk_issue_survey1_idx` (`survey_id`),
  KEY `fk_issue_survey_questions1_idx` (`survey_questions_id`),
  KEY `fk_issue_factor1_idx` (`factor_id`),
  KEY `survey_question` (`survey_questions_id`,`survey_id`),
  KEY `survey_delete` (`survey_id`,`deleted_at`),
  CONSTRAINT `FK_12AD233E1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_12AD233E25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_12AD233EB3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_12AD233EBC88C1A3` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`),
  CONSTRAINT `FK_12AD233ECC63389E` FOREIGN KEY (`survey_questions_id`) REFERENCES `survey_questions` (`id`),
  CONSTRAINT `FK_12AD233EDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `issue_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_69EE4CAEDE12AB56` (`created_by`),
  KEY `IDX_69EE4CAE25F94802` (`modified_by`),
  KEY `IDX_69EE4CAE1F6FA0AF` (`deleted_by`),
  KEY `fk_issue_lang_issue1_idx` (`issue_id`),
  KEY `fk_issue_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `FK_69EE4CAE1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_69EE4CAE25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_69EE4CAE5E7AA58C` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`id`),
  CONSTRAINT `FK_69EE4CAEB213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_69EE4CAEDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `issue_id` int(11) DEFAULT NULL,
  `ebi_question_options_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75CDBF66DE12AB56` (`created_by`),
  KEY `IDX_75CDBF6625F94802` (`modified_by`),
  KEY `IDX_75CDBF661F6FA0AF` (`deleted_by`),
  KEY `fk_issue_options_issue1_idx` (`issue_id`),
  KEY `fk_issue_options_ebi_question_options1_idx` (`ebi_question_options_id`),
  KEY `issue-option` (`issue_id`,`ebi_question_options_id`),
  CONSTRAINT `FK_75CDBF661F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_75CDBF6625F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_75CDBF665E7AA58C` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`id`),
  CONSTRAINT `FK_75CDBF667586C8EA` FOREIGN KEY (`ebi_question_options_id`) REFERENCES `ebi_question_options` (`id`),
  CONSTRAINT `FK_75CDBF66DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `issue_options`
--

LOCK TABLES `issue_options` WRITE;
/*!40000 ALTER TABLE `issue_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `issue_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `issues_student_staff_mapping`
--

DROP TABLE IF EXISTS `issues_student_staff_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `issues_student_staff_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `issues_input_id` int(11) NOT NULL,
  `org_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `calculated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `issues_student_staff_mapping_org_id` (`org_id`),
  KEY `issues_student_staff_mapping_staff_id` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `issues_student_staff_mapping`
--

LOCK TABLES `issues_student_staff_mapping` WRITE;
/*!40000 ALTER TABLE `issues_student_staff_mapping` DISABLE KEYS */;
/*!40000 ALTER TABLE `issues_student_staff_mapping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `language_master`
--

DROP TABLE IF EXISTS `language_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `langcode` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `langdescription` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `issystemdefault` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `language_master`
--

LOCK TABLES `language_master` WRITE;
/*!40000 ALTER TABLE `language_master` DISABLE KEYS */;
INSERT INTO `language_master` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'en_US','US English',1);
/*!40000 ALTER TABLE `language_master` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,'1. --> event_risk_calcinsert','2015-12-16 16:18:46'),(2,'2. --> CreateTemptables2','2015-12-16 16:18:46'),(3,'3. <-- CreateTemptables2','2015-12-16 16:18:46'),(4,'4. Count Got from org_riskval_calc_inputs = 0','2015-12-16 16:18:46'),(5,'2. --> CreateTemptables2','2015-12-16 16:19:43'),(6,'1. --> event_risk_calcinsert','2015-12-16 16:19:43'),(7,'3. <-- CreateTemptables2','2015-12-16 16:19:43'),(8,'4. Count Got from org_riskval_calc_inputs = 0','2015-12-16 16:19:43'),(9,'2. --> CreateTemptables2','2015-12-16 16:19:51'),(10,'1. --> event_risk_calcinsert','2015-12-16 16:19:51'),(11,'3. <-- CreateTemptables2','2015-12-16 16:19:51'),(12,'4. Count Got from org_riskval_calc_inputs = 0','2015-12-16 16:19:51'),(13,'51. --> get all the student id and orgId','2015-12-16 16:32:17'),(14,'51. --> get all the student id and orgId','2015-12-16 16:37:17'),(15,'51. --> get all the student id and orgId','2015-12-16 16:42:17'),(16,'21. --> CreateTemptables','2015-12-16 16:45:18'),(17,'22. --> get all the staff id and orgId','2015-12-16 16:45:19'),(18,'51. --> get all the student id and orgId','2015-12-16 16:47:18'),(19,'51. --> get all the student id and orgId','2015-12-16 16:52:17'),(20,'51. --> get all the student id and orgId','2015-12-16 16:57:17'),(21,'51. --> get all the student id and orgId','2015-12-16 17:02:17'),(22,'51. --> get all the student id and orgId','2015-12-16 17:07:17');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metadata_list_values`
--

DROP TABLE IF EXISTS `metadata_list_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_list_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `metadata_id` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `list_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `list_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_45093F6FDC9EE959` (`metadata_id`),
  KEY `IDX_45093F6FB213FA4` (`lang_id`),
  CONSTRAINT `FK_45093F6FB213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_45093F6FDC9EE959` FOREIGN KEY (`metadata_id`) REFERENCES `metadata_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_list_values`
--

LOCK TABLES `metadata_list_values` WRITE;
/*!40000 ALTER TABLE `metadata_list_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_list_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metadata_master`
--

DROP TABLE IF EXISTS `metadata_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `meta_key` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `definition_type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `metadata_type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `no_of_decimals` int(11) DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT NULL,
  `min_range` decimal(15,4) DEFAULT NULL,
  `max_range` decimal(15,4) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8E57C01A32C8A3DE` (`organization_id`),
  KEY `IDX_8E57C01A81257D5D` (`entity_id`),
  CONSTRAINT `FK_8E57C01A32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_8E57C01A81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_master`
--

LOCK TABLES `metadata_master` WRITE;
/*!40000 ALTER TABLE `metadata_master` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_master` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metadata_master_lang`
--

DROP TABLE IF EXISTS `metadata_master_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata_master_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `metadata_id` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `meta_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FCC218ACDC9EE959` (`metadata_id`),
  KEY `IDX_FCC218ACB213FA4` (`lang_id`),
  CONSTRAINT `FK_FCC218ACB213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_FCC218ACDC9EE959` FOREIGN KEY (`metadata_id`) REFERENCES `metadata_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata_master_lang`
--

LOCK TABLES `metadata_master_lang` WRITE;
/*!40000 ALTER TABLE `metadata_master_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata_master_lang` ENABLE KEYS */;
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
INSERT INTO `migration_versions` VALUES ('20140729224036'),('20140729225635'),('20140729230250'),('20140729231026'),('20140729233858'),('20140729234955'),('20140731191946'),('20140804164058'),('20140805185717'),('20140818152930'),('20140818154234'),('20140826144814'),('20140826155008'),('20140828205229'),('20140828231414'),('20140902041125'),('20140905080440'),('20140908170845'),('20140909132358'),('20140911141907'),('20140911145102'),('20140912123326'),('20140918101509'),('20140918110133'),('20140919071650'),('20140922091249'),('20140924095105'),('20140925100825'),('20140925115027'),('20140926090013'),('20140926110935'),('20140930070613'),('20140930140236'),('20141008071840'),('20141013114649'),('20141014063455'),('20141014090811'),('20141018211438'),('20141019160524'),('20141021102624'),('20141022214202'),('20141024072511'),('20141024100649'),('20141024173420'),('20141027121006'),('20141028095528'),('20141028112856'),('20141028120250'),('20141029091232'),('20141029101208'),('20141029131639'),('20141029133752'),('20141030154643'),('20141030220739'),('20141103103711'),('20141105065017'),('20141110140850'),('20141110151243'),('20141117054130'),('20141117071030'),('20141118101230'),('20141124072948'),('20141124174128'),('20141125071407'),('20141202065337'),('20141202151544'),('20141203052022'),('20141215124432'),('20141217084859'),('20141217111515'),('20141219140752'),('20141222044424'),('20141222071555'),('20141222072807'),('20141222113621'),('20141223114036'),('20141223114859'),('20141223131147'),('20141224093848'),('20141224103116'),('20141231130951'),('20150101105233'),('20150102061610'),('20150105133005'),('20150105163800'),('20150204113759'),('20150204114823'),('20150204235054'),('20150205093603'),('20150206095410'),('20150212093729'),('20150213082352'),('20150213094045'),('20150216064337'),('20150216065341'),('20150216095322'),('20150223133712'),('20150224091638'),('20150224113148'),('20150224123706'),('20150225092423'),('20150226050641'),('20150226141830'),('20150303114949'),('20150310155857'),('20150312044209'),('20150319141555'),('20150320071335'),('20150320114341'),('20150320154639'),('20150323145008'),('20150327163207'),('20150329072515'),('20150331063618'),('20150331101820'),('20150331130418'),('20150401135946'),('20150403110531'),('20150405065621'),('20150406152225'),('20150408072706'),('20150408073332'),('20150408094552'),('20150413100837'),('20150415094212'),('20150417134014'),('20150420094639'),('20150422195401'),('20150423063739'),('20150423124544'),('20150424052218'),('20150424094408'),('20150427132701'),('20150427162221'),('20150429143237'),('20150430093931'),('20150430145426'),('20150430175619'),('20150504042602'),('20150504102328'),('20150504133456'),('20150504220150'),('20150506111729'),('20150506205053'),('20150508130511'),('20150508135446'),('20150508142629'),('20150511135852'),('20150511160405'),('20150511222206'),('20150511232551'),('20150512074755'),('20150512132734'),('20150512160016'),('20150513075038'),('20150513182646'),('20150513213415'),('20150514134921'),('20150514142302'),('20150515042040'),('20150518035112'),('20150518135644'),('20150519112425'),('20150520133919'),('20150522114239'),('20150522125700'),('20150525151435'),('20150528135607'),('20150529072040'),('20150601112737'),('20150601125952'),('20150602113522'),('20150602135352'),('20150602202230'),('20150603120314'),('20150603130058'),('20150603152914'),('20150604132235'),('20150604142628'),('20150604142629'),('20150605143914'),('20150605180022'),('20150605200154'),('20150608100922'),('20150608141745'),('20150608170922'),('20150609092340'),('20150609103524'),('20150609132543'),('20150610070638'),('20150610134334'),('20150610142420'),('20150611052615'),('20150611060455'),('20150611062722'),('20150611063934'),('20150611070433'),('20150611070944'),('20150611174758'),('20150612071231'),('20150612130920'),('20150612132312'),('20150612140439'),('20150615133529'),('20150616042202'),('20150616062835'),('20150616105058'),('20150616121115'),('20150616135537'),('20150616140732'),('20150617102207'),('20150617135858'),('20150617154909'),('20150618065429'),('20150618101525'),('20150618104157'),('20150618114453'),('20150618123735'),('20150618180022'),('20150618190035'),('20150619061106'),('20150619082424'),('20150619090656'),('20150622063226'),('20150622073038'),('20150622132512'),('20150623072833'),('20150623112606'),('20150623115030'),('20150623142519'),('20150625230922'),('20150626054313'),('20150626220702'),('20150626235220'),('20150627070035'),('20150627114123'),('20150627114458'),('20150627140226'),('20150628080543'),('20150628110757'),('20150628131058'),('20150628161058'),('20150628174010'),('20150629082256'),('20150630055354'),('20150630062332'),('20150630064839'),('20150630104022'),('20150630114853'),('20150630125402'),('20150630153305'),('20150630171122'),('20150701070419'),('20150701085116'),('20150701100911'),('20150701125411'),('20150701140621'),('20150701181000'),('20150701183324'),('20150701191010'),('20150703093010'),('20150703101605'),('20150703110010'),('20150703120922'),('20150703131022'),('20150703195522'),('20150704153010'),('20150704160000'),('20150706105528'),('20150706110454'),('20150707124111'),('20150707133432'),('20150708073354'),('20150708095013'),('20150708120922'),('20150708165210'),('20150709154032'),('20150710052041'),('20150710094907'),('20150710095825'),('20150710115959'),('20150710154700'),('20150710200419'),('20150711063328'),('20150711064303'),('20150711150122'),('20150711180122'),('20150711184109'),('20150712002522'),('20150712163522'),('20150713050947'),('20150713111826'),('20150713160122'),('20150713180922'),('20150714062826'),('20150714071505'),('20150714143559'),('20150715142100'),('20150717205840'),('20150721053753'),('20150721163753'),('20150722070400'),('20150722095926'),('20150722131130'),('20150729063748'),('20150729100620'),('20150729110928'),('20150729114130'),('20150729244431'),('20150730095428'),('20150731044041'),('20150731090316'),('20150731131034'),('20150803094443'),('20150803222843'),('20150804095231'),('20150804102341'),('20150804144844'),('20150804145947'),('20150804153458'),('20150805043557'),('20150805061044'),('20150805124139'),('20150805133325'),('20150805134719'),('20150805200139'),('20150806033800'),('20150806073649'),('20150806094619'),('20150806124822'),('20150806175812'),('20150806181744'),('20150806181935'),('20150806182629'),('20150807115827'),('20150807121811'),('20150810112744'),('20150810121832'),('20150810131546'),('20150810132818'),('20150810133358'),('20150810150705'),('20150810152046'),('20150810214640'),('20150811070115'),('20150811114425'),('20150811134602'),('20150812122518'),('20150812130913'),('20150812152433'),('20150813051602'),('20150813090121'),('20150813114302'),('20150813130046'),('20150813135605'),('20150813212902'),('20150813220354'),('20150814063553'),('20150814130808'),('20150817095951'),('20150817214322'),('20150818034549'),('20150818061010'),('20150818061429'),('20150818085704'),('20150818092646'),('20150818094826'),('20150818101747'),('20150818135647'),('20150818145735'),('20150818150438'),('20150818152412'),('20150818211145'),('20150819092233'),('20150820042247'),('20150820045221'),('20150820070312'),('20150820085942'),('20150820114312'),('20150820205022'),('20150820213851'),('20150821105411'),('20150821131027'),('20150821134657'),('20150821200618'),('20150821234410'),('20150822024841'),('20150822030642'),('20150824019999'),('20150824054114'),('20150824103647'),('20150824193211'),('20150825070444'),('20150825091220'),('20150825093347'),('20150825130406'),('20150825152530'),('20150825155523'),('20150825183446'),('20150825211142'),('20150825220528'),('20150826063456'),('20150826130231'),('20150826143454'),('20150826145956'),('20150826174450'),('20150827112633'),('20150827120444'),('20150827132617'),('20150827135232'),('20150827140456'),('20150828120706'),('20150828131326'),('20150828165456'),('20150828170445'),('20150828193315'),('20150828205105'),('20150831142312'),('20150831150242'),('20150831194022'),('20150901035024'),('20150901040944'),('20150901061840'),('20150901074036'),('20150901091601'),('20150902093725'),('20150902121442'),('20150902150336'),('20150902155752'),('20150903024315'),('20150903132437'),('20150903143242'),('20150903163639'),('20150904115203'),('20150904194223'),('20150907101253'),('20150907104837'),('20150907112956'),('20150907113556'),('20150907114128'),('20150907115123'),('20150907115402'),('20150907115623'),('20150907121515'),('20150907125059'),('20150907125825'),('20150907130008'),('20150907173242'),('20150908083758'),('20150908101806'),('20150908142430'),('20150908151213'),('20150908154408'),('20150908191335'),('20150909102216'),('20150909124242'),('20150909220603'),('20150910063053'),('20150910064432'),('20150910115377'),('20150910133945'),('20150910191811'),('20150910222345'),('20150913004538'),('20150914024848'),('20150915004126'),('20150915060904'),('20150915100331'),('20150915181758'),('20150916084034'),('20150916143633'),('20150916144326'),('20150916160453'),('20150917145536'),('20150918203409'),('20150921071414'),('20150921085225'),('20150921115016'),('20150921164014'),('20150922093245'),('20150922094028'),('20150922132159'),('20150922134534'),('20150922190009'),('20150923084433'),('20150923113640'),('20150923134042'),('20150923150432'),('20150923173536'),('20150923193541'),('20150923194837'),('20150923214747'),('20150924021556'),('20150924045315'),('20150924162915'),('20150925153542'),('20150928070540'),('20150928130210'),('20150928200002'),('20150929125820'),('20150929130851'),('20150929181808'),('20150929185911'),('20150929191823'),('20150930052321'),('20150930063356'),('20150930195038'),('20151001103032'),('20151002000505'),('20151002003635'),('20151002152355'),('20151005065151'),('20151007115528'),('20151007165835'),('20151008123514'),('20151008204141'),('20151008205944'),('20151008211516'),('20151008212057'),('20151008212545'),('20151009132258'),('20151009181936'),('20151012062545'),('20151012111020'),('20151012114325'),('20151012125150'),('20151012141009'),('20151013042935'),('20151014073645'),('20151014112259'),('20151014145100'),('20151014164308'),('20151015111944'),('20151015122314'),('20151015141132'),('20151015183553'),('20151016064427'),('20151016102219'),('20151016131049'),('20151016151445'),('20151018083942'),('20151019090548'),('20151020054153'),('20151020093450'),('20151020135630'),('20151020153526'),('20151020155234'),('20151020182414'),('20151020201045'),('20151020210246'),('20151021182931'),('20151022130400'),('20151023140226'),('20151026160104'),('20151027031414'),('20151027150800'),('20151028084555'),('20151028091941'),('20151029072346'),('20151029104751'),('20151030060023'),('20151030085030'),('20151102063725'),('20151102150958'),('20151102151515'),('20151102205413'),('20151102222131'),('20151103051320'),('20151103054035'),('20151103153638'),('20151104071329'),('20151104200846'),('20151105065227'),('20151105153320'),('20151109164402'),('20151109193311'),('20151119150536'),('20151119214353'),('20151123065211'),('20151123095458'),('20151123114307'),('20151123141331'),('20151124065258'),('20151124120925'),('20151126105927'),('20151127063330'),('20151130201447'),('20151201095057'),('20151201164552'),('20151201165743'),('20151201193521'),('20151201200059'),('20151202061209'),('20151202165630'),('20151202175447'),('20151202183637'),('20151203144626'),('20151203210847'),('20151207074056'),('20151208044553'),('20151208053313'),('20151208061423'),('20151208174846'),('20151209120834'),('20151209125713'),('20151209205644'),('20151210043603'),('20151210120145'),('20151211101613'),('20151211135121'),('20151211185035'),('20151211185648'),('20151214064659'),('20151215044048'),('20151216102734'),('20151216105155'),('20151217101107'),('20151222042905'),('20151222080000'),('20151223030124'),('20151223154447'),('20151228071017'),('20151231090000'),('20152210185911'),('20160104180832'),('20160105030000'),('20160105090445'),('20160105121320'),('20160106200146'),('20160106200605'),('20160106200727'),('20160107080000'),('20160108085242'),('20160108140852'),('20160111070326'),('20160111105658'),('20160111155230'),('20160111190548'),('20160113050807'),('20160114120000'),('20160114140000'),('20160114162335'),('20160118130859'),('20160119091248'),('20160119091620'),('20160119100526'),('20160119140605'),('20160120041413'),('20160120051308'),('20160120063010'),('20160120121426'),('20160121062202'),('20160121115910'),('20160122144510'),('20160125193105'),('20160126051308'),('20160126051309'),('20160126162502'),('20160128130404'),('20160203120255'),('20160211154809'),('20160211160420'),('20160216124059'),('20160216145633'),('20160217113400'),('20160217173000'),('20160218214822'),('20160222090026'),('20160224135943'),('20160229125205'),('20160229141224'),('20160229163703'),('20160301004823'),('20160302095700'),('20160302130400'),('20160302132700'),('20160302153300'),('20160303215949'),('20160303224638'),('20160304201721'),('20160304214920'),('20160307223153'),('20160309053054'),('20160309124854'),('20160309200331'),('20160314062757'),('20160315100306'),('20160315130400'),('20160315193409'),('20160316130041'),('20160317072044'),('20160318090500'),('20160318102500'),('20160318105900'),('20160321215010'),('20160322094300'),('20160322105325'),('20160323173542'),('20160324140348'),('20160325171048'),('20160329065947'),('20160331093904'),('20160405084740'),('20160405103939'),('20160405104327'),('20160405105409'),('20160405132848'),('20160405165157'),('20160405185303'),('20160406085924'),('20160406105524'),('20160406141338'),('20160406142119'),('20160407195738'),('20160411134000'),('20160411171822'),('20160412130456'),('20160412132542'),('20160412164120'),('20160414192003'),('20160419092749'),('20160419150700'),('20160422144804'),('20160425144400'),('20160429194142'),('20160503035650'),('20160504181446'),('20160506134748'),('20160510070203'),('20160510172331'),('20160511143900'),('20160512161800'),('20160512192352'),('20160513182436'),('20160517102023'),('20160517135716'),('20160517135723'),('20160517203858'),('20160518165039'),('20160524070428'),('20160527123243'),('20160601182600'),('20160601191222'),('20160601192750'),('20160603143000'),('20160603195213'),('20160608202903'),('20160609063745'),('20160609072937'),('20160609095857'),('20160612140251'),('20160617062749'),('20160620182127'),('20160622151921'),('20160622165500'),('20160622194239'),('20160622211209'),('20160628134149'),('20160701130100'),('20160706201841'),('20160706215002'),('20160707174033'),('20160707174055'),('20160707190850'),('20160712151029'),('20160712155435'),('20160713152911'),('20160718201309'),('20160720190943'),('20160726170444'),('20160727143708'),('20160729203147'),('20160815163733'),('20160822062524'),('20160822140604'),('20160824152207'),('20160825155425'),('20160830173328'),('20160831073340'),('20160902050150'),('20160902051210'),('20160907023240'),('20160918093038'),('20160918093044'),('20160918111928'),('20160920052243'),('20160920052247'),('20160920053647'),('20160920060642'),('20160920065318'),('20160921183142'),('20160922102014'),('20160922105037'),('20160924105108'),('20160926073555'),('20160927115634'),('20160927144029'),('20161003152656'),('20161026142135');
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
  `organization_id` int(11) DEFAULT NULL,
  `person_id_student` int(11) DEFAULT NULL,
  `person_id_faculty` int(11) DEFAULT NULL,
  `activity_category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `note` longtext COLLATE utf8_unicode_ci NOT NULL,
  `note_date` datetime NOT NULL,
  `access_private` tinyint(1) DEFAULT NULL,
  `access_public` tinyint(1) DEFAULT NULL,
  `access_team` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CFBDFA1432C8A3DE` (`organization_id`),
  KEY `IDX_CFBDFA145F056556` (`person_id_student`),
  KEY `IDX_CFBDFA14FFB0AA26` (`person_id_faculty`),
  KEY `IDX_CFBDFA141CC8F7EE` (`activity_category_id`),
  CONSTRAINT `FK_CFBDFA141CC8F7EE` FOREIGN KEY (`activity_category_id`) REFERENCES `activity_category` (`id`),
  CONSTRAINT `FK_CFBDFA1432C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_CFBDFA145F056556` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_CFBDFA14FFB0AA26` FOREIGN KEY (`person_id_faculty`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `note_id` int(11) DEFAULT NULL,
  `teams_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F3D2818F26ED0855` (`note_id`),
  KEY `IDX_F3D2818FD6365F12` (`teams_id`),
  CONSTRAINT `FK_F3D2818F26ED0855` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`),
  CONSTRAINT `FK_F3D2818FD6365F12` FOREIGN KEY (`teams_id`) REFERENCES `Teams` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `organization_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `sent_date` date DEFAULT NULL,
  `email_key` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `recipient_list` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cc_list` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bcc_list` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body` longtext COLLATE utf8_unicode_ci,
  `status` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `no_of_retries` int(11) DEFAULT NULL,
  `server_response` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_ED15DF232C8A3DE` (`organization_id`),
  CONSTRAINT `FK_ED15DF232C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `person_id_proxy_created` int(11) DEFAULT NULL,
  `office_hours_series_id` int(11) DEFAULT NULL,
  `appointments_id` int(11) DEFAULT NULL,
  `person_id_proxy_cancelled` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `slot_type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slot_start` datetime DEFAULT NULL,
  `slot_end` datetime DEFAULT NULL,
  `meeting_length` int(11) DEFAULT NULL,
  `standing_instructions` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_cancelled` tinyint(1) DEFAULT NULL,
  `source` enum('S','G','E') COLLATE utf8_unicode_ci DEFAULT 'S',
  `exchange_appointment_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_appointment_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_synced` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_83411E0D32C8A3DE` (`organization_id`),
  KEY `IDX_83411E0D217BBB47` (`person_id`),
  KEY `IDX_83411E0D7D2061B4` (`person_id_proxy_created`),
  KEY `IDX_83411E0DD2D1B0CE` (`office_hours_series_id`),
  KEY `IDX_83411E0D23F542AE` (`appointments_id`),
  KEY `IDX_83411E0D3AF00C37` (`person_id_proxy_cancelled`),
  KEY `exchange_appointment_id_idx` (`exchange_appointment_id`,`last_synced`),
  KEY `google_appointment_id_idx` (`google_appointment_id`,`last_synced`),
  CONSTRAINT `FK_83411E0D217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_83411E0D23F542AE` FOREIGN KEY (`appointments_id`) REFERENCES `Appointments` (`id`),
  CONSTRAINT `FK_83411E0D32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_83411E0D3AF00C37` FOREIGN KEY (`person_id_proxy_cancelled`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_83411E0D7D2061B4` FOREIGN KEY (`person_id_proxy_created`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_83411E0DD2D1B0CE` FOREIGN KEY (`office_hours_series_id`) REFERENCES `office_hours_series` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `person_id_proxy` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `days` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slot_start` datetime DEFAULT NULL,
  `slot_end` datetime DEFAULT NULL,
  `meeting_length` int(11) DEFAULT NULL,
  `standing_instructions` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `repeat_pattern` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `repeat_every` int(11) DEFAULT NULL,
  `repetition_range` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `repetition_occurrence` int(11) DEFAULT NULL,
  `repeat_monthly_on` int(11) DEFAULT NULL,
  `exchange_master_appointment_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_master_appointment_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_synced` datetime DEFAULT NULL,
  `include_stat_sun` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1578CA932C8A3DE` (`organization_id`),
  KEY `IDX_1578CA9217BBB47` (`person_id`),
  KEY `IDX_1578CA99B12DB9` (`person_id_proxy`),
  KEY `exchange_master_appointment_id_idx` (`exchange_master_appointment_id`,`last_synced`),
  KEY `google_master_appointment_id_idx` (`google_master_appointment_id`,`last_synced`),
  CONSTRAINT `FK_1578CA9217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1578CA932C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_1578CA99B12DB9` FOREIGN KEY (`person_id_proxy`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `org_academic_year_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `name` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `term_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75DF84EFDE12AB56` (`created_by`),
  KEY `IDX_75DF84EF25F94802` (`modified_by`),
  KEY `IDX_75DF84EF1F6FA0AF` (`deleted_by`),
  KEY `fk_academicperiod_organizationid` (`organization_id`),
  KEY `fk_academicperiod_academicyearid` (`org_academic_year_id`),
  KEY `org_academic_end` (`organization_id`,`org_academic_year_id`,`end_date`),
  KEY `last_term` (`id`,`organization_id`,`end_date`,`start_date`,`deleted_at`),
  CONSTRAINT `FK_75DF84EF1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_75DF84EF25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_75DF84EF32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_75DF84EFDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_75DF84EFF3B0CE4A` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_academic_terms`
--

LOCK TABLES `org_academic_terms` WRITE;
/*!40000 ALTER TABLE `org_academic_terms` DISABLE KEYS */;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `name` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `year_id` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A4C0972DDE12AB56` (`created_by`),
  KEY `IDX_A4C0972D25F94802` (`modified_by`),
  KEY `IDX_A4C0972D1F6FA0AF` (`deleted_by`),
  KEY `fk_org_academic_year_year1_idx` (`year_id`),
  KEY `relationship9` (`organization_id`),
  CONSTRAINT `FK_A4C0972D1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_A4C0972D25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_A4C0972D32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_A4C0972D40C1FEA7` FOREIGN KEY (`year_id`) REFERENCES `year` (`id`),
  CONSTRAINT `FK_A4C0972DDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_academic_year`
--

LOCK TABLES `org_academic_year` WRITE;
/*!40000 ALTER TABLE `org_academic_year` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_academic_year` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_announcements`
--

DROP TABLE IF EXISTS `org_announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) NOT NULL,
  `creator_person_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `display_type` enum('banner','alert bell') COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `stop_datetime` datetime NOT NULL,
  `message_duration` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8CD85A91DE12AB56` (`created_by`),
  KEY `IDX_8CD85A9125F94802` (`modified_by`),
  KEY `IDX_8CD85A911F6FA0AF` (`deleted_by`),
  KEY `fk_org_announcements_organization1_idx` (`org_id`),
  KEY `fk_org_announcements_person1_idx` (`creator_person_id`),
  CONSTRAINT `FK_8CD85A911F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8CD85A9125F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8CD85A91D895820F` FOREIGN KEY (`creator_person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8CD85A91DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8CD85A91F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_announcements_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `message` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3056A6D0DE12AB56` (`created_by`),
  KEY `IDX_3056A6D025F94802` (`modified_by`),
  KEY `IDX_3056A6D01F6FA0AF` (`deleted_by`),
  KEY `fk_org_announcements_lang_org_announcements1_idx` (`org_announcements_id`),
  KEY `fk_org_announcements_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `FK_3056A6D01F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3056A6D025F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3056A6D052CCF843` FOREIGN KEY (`org_announcements_id`) REFERENCES `org_announcements` (`id`),
  CONSTRAINT `FK_3056A6D0B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_3056A6D0DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_announcements_lang`
--

LOCK TABLES `org_announcements_lang` WRITE;
/*!40000 ALTER TABLE `org_announcements_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_announcements_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_auth_config`
--

DROP TABLE IF EXISTS `org_auth_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_auth_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `campus_portal_student_enabled` tinyint(1) DEFAULT NULL,
  `campus_portal_student_key` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `campus_portal_staff_enabled` tinyint(1) DEFAULT NULL,
  `campus_portal_staff_key` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ldap_student_enabled` tinyint(1) DEFAULT NULL,
  `ldap_staff_enabled` tinyint(1) DEFAULT NULL,
  `saml_student_enabled` tinyint(1) DEFAULT NULL,
  `saml_staff_enabled` tinyint(1) DEFAULT NULL,
  `campus_portal_login_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `campus_portal_logout_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_68A5D278DE12AB56` (`created_by`),
  KEY `IDX_68A5D27825F94802` (`modified_by`),
  KEY `IDX_68A5D2781F6FA0AF` (`deleted_by`),
  KEY `fk_org_auth_config_organization1_idx` (`org_id`),
  CONSTRAINT `FK_68A5D2781F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_68A5D27825F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_68A5D278DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_68A5D278F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_auth_config`
--

LOCK TABLES `org_auth_config` WRITE;
/*!40000 ALTER TABLE `org_auth_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_auth_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_calc_flags_factor`
--

DROP TABLE IF EXISTS `org_calc_flags_factor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_calc_flags_factor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `calculated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `org_person_idx` (`org_id`,`person_id`),
  KEY `id_idx` (`id`),
  KEY `person_idx` (`person_id`),
  KEY `created_at_idx` (`created_at`),
  KEY `modified_at_idx` (`modified_at`),
  KEY `calculated_at_idx` (`calculated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_calc_flags_factor`
--

LOCK TABLES `org_calc_flags_factor` WRITE;
/*!40000 ALTER TABLE `org_calc_flags_factor` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_calc_flags_factor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_calc_flags_risk`
--

DROP TABLE IF EXISTS `org_calc_flags_risk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_calc_flags_risk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `calculated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `org_person_idx` (`org_id`,`person_id`),
  KEY `created_at_idx` (`created_at`),
  KEY `modified_at_idx` (`modified_at`),
  KEY `person_idx` (`person_id`,`calculated_at`),
  KEY `calculated_at_idx` (`calculated_at`,`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_calc_flags_risk`
--

LOCK TABLES `org_calc_flags_risk` WRITE;
/*!40000 ALTER TABLE `org_calc_flags_risk` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_calc_flags_risk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_calc_flags_student_reports`
--

DROP TABLE IF EXISTS `org_calc_flags_student_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_calc_flags_student_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `calculated_at` datetime DEFAULT NULL,
  `report_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `in_progress_email_sent` tinyint(1) NOT NULL DEFAULT '0',
  `completion_email_sent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_idx` (`org_id`,`person_id`,`survey_id`),
  KEY `IDX_F4644A69DE12AB56` (`created_by`),
  KEY `IDX_F4644A6925F94802` (`modified_by`),
  KEY `IDX_F4644A691F6FA0AF` (`deleted_by`),
  KEY `IDX_F4644A69F4837C1B` (`org_id`),
  KEY `IDX_F4644A69217BBB47` (`person_id`),
  KEY `IDX_F4644A694BD2A4C0` (`report_id`),
  KEY `IDX_F4644A69B3FE509D` (`survey_id`),
  KEY `calcat` (`calculated_at`),
  CONSTRAINT `FK_F4644A691F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_F4644A69217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_F4644A6925F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_F4644A694BD2A4C0` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`),
  CONSTRAINT `FK_F4644A69B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_F4644A69DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_F4644A69F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_calc_flags_student_reports`
--

LOCK TABLES `org_calc_flags_student_reports` WRITE;
/*!40000 ALTER TABLE `org_calc_flags_student_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_calc_flags_student_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_calc_flags_success_marker`
--

DROP TABLE IF EXISTS `org_calc_flags_success_marker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_calc_flags_success_marker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `calculated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `org_person_idx` (`org_id`,`person_id`),
  KEY `id_idx` (`id`),
  KEY `person_idx` (`person_id`),
  KEY `created_at_idx` (`created_at`),
  KEY `modified_at_idx` (`modified_at`),
  KEY `calculated_at_idx` (`calculated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_calc_flags_success_marker`
--

LOCK TABLES `org_calc_flags_success_marker` WRITE;
/*!40000 ALTER TABLE `org_calc_flags_success_marker` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_calc_flags_success_marker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_calc_flags_talking_point`
--

DROP TABLE IF EXISTS `org_calc_flags_talking_point`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_calc_flags_talking_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `calculated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `org_person_idx` (`org_id`,`person_id`),
  KEY `id_idx` (`id`),
  KEY `person_idx` (`person_id`),
  KEY `created_at_idx` (`created_at`),
  KEY `modified_at_idx` (`modified_at`),
  KEY `calculated_at_idx` (`calculated_at`,`org_id`,`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_calc_flags_talking_point`
--

LOCK TABLES `org_calc_flags_talking_point` WRITE;
/*!40000 ALTER TABLE `org_calc_flags_talking_point` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_calc_flags_talking_point` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_calculated_risk_variables_history`
--

DROP TABLE IF EXISTS `org_calculated_risk_variables_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_calculated_risk_variables_history` (
  `person_id` int(11) NOT NULL,
  `risk_variable_id` int(11) NOT NULL,
  `risk_group_id` int(11) NOT NULL,
  `risk_model_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '1950-01-01 01:01:01',
  `org_id` int(11) NOT NULL,
  `calc_bucket_value` int(11) DEFAULT NULL,
  `calc_weight` decimal(8,4) DEFAULT NULL,
  `risk_source_value` decimal(12,4) DEFAULT NULL,
  PRIMARY KEY (`person_id`,`risk_variable_id`,`risk_group_id`,`risk_model_id`,`created_at`),
  KEY `IDX_93D7B9DDF4837C1B` (`org_id`),
  KEY `fk_org_computed_risk_variables_risk_variable1_idx` (`risk_variable_id`),
  KEY `fk_org_calculated_risk_variables_risk_model_master1_idx` (`risk_model_id`),
  KEY `fk_group` (`risk_group_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_calculated_risk_variables_history`
--

LOCK TABLES `org_calculated_risk_variables_history` WRITE;
/*!40000 ALTER TABLE `org_calculated_risk_variables_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_calculated_risk_variables_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_campus_resource`
--

DROP TABLE IF EXISTS `org_campus_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_campus_resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visible_to_student` enum('1','0') COLLATE utf8_unicode_ci DEFAULT NULL,
  `receive_referals` enum('1','0') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E234A1AEDE12AB56` (`created_by`),
  KEY `IDX_E234A1AE25F94802` (`modified_by`),
  KEY `IDX_E234A1AE1F6FA0AF` (`deleted_by`),
  KEY `fk_campus_resource_organization1_idx` (`org_id`),
  KEY `fk_campus_resource_person1_idx` (`person_id`),
  CONSTRAINT `FK_E234A1AE1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E234A1AE217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E234A1AE25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E234A1AEDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E234A1AEF4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id_source` int(11) DEFAULT NULL,
  `org_id_destination` int(11) DEFAULT NULL,
  `person_id_requested_by` int(11) DEFAULT NULL,
  `person_id_student` int(11) DEFAULT NULL,
  `person_id_approved_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  `date_approved` datetime DEFAULT NULL,
  `approval_status` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C2A5FB6DDE12AB56` (`created_by`),
  KEY `IDX_C2A5FB6D25F94802` (`modified_by`),
  KEY `IDX_C2A5FB6D1F6FA0AF` (`deleted_by`),
  KEY `fk_org_change_request_person1_idx` (`person_id_requested_by`),
  KEY `fk_org_change_request_person2_idx` (`person_id_student`),
  KEY `fk_org_change_request_organization1_idx` (`org_id_source`),
  KEY `fk_org_change_request_organization2_idx` (`org_id_destination`),
  KEY `fk_org_change_request_person3_idx` (`person_id_approved_by`),
  CONSTRAINT `FK_C2A5FB6D1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_C2A5FB6D25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_C2A5FB6D2FE12506` FOREIGN KEY (`org_id_destination`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_C2A5FB6D40C2D6CC` FOREIGN KEY (`org_id_source`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_C2A5FB6D5F056556` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_C2A5FB6DB36C36F4` FOREIGN KEY (`person_id_approved_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_C2A5FB6DDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_C2A5FB6DFA81244C` FOREIGN KEY (`person_id_requested_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_change_request`
--

LOCK TABLES `org_change_request` WRITE;
/*!40000 ALTER TABLE `org_change_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_change_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_cohort_name`
--

DROP TABLE IF EXISTS `org_cohort_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_cohort_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `org_academic_year_id` int(11) NOT NULL,
  `cohort` int(11) NOT NULL,
  `cohort_name` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cohort` (`organization_id`,`org_academic_year_id`,`cohort`),
  KEY `fk_organization_id_idx` (`organization_id`),
  KEY `fk_org_academic_year_id_idx` (`org_academic_year_id`),
  CONSTRAINT `fk_org_academic_year_id` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_organization_id` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=623 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_cohort_name`
--

LOCK TABLES `org_cohort_name` WRITE;
/*!40000 ALTER TABLE `org_cohort_name` DISABLE KEYS */;
INSERT INTO `org_cohort_name` VALUES (20,214,170,1,'Survey Cohort 1',-6,-6,NULL,'2016-04-14 17:06:12','2016-04-14 17:06:12',NULL),(82,214,171,1,'Survey Cohort 1',-6,-6,NULL,'2016-04-14 17:06:12','2016-04-14 17:06:12',NULL),(186,214,170,2,'Survey Cohort 2',-6,-6,NULL,'2016-04-14 17:06:12','2016-04-14 17:06:12',NULL),(248,214,171,2,'Survey Cohort 2',-6,-6,NULL,'2016-04-14 17:06:12','2016-04-14 17:06:12',NULL),(373,214,170,3,'Survey Cohort 3',-6,-6,NULL,'2016-04-14 17:06:12','2016-04-14 17:06:12',NULL),(435,214,171,3,'Survey Cohort 3',-6,-6,NULL,'2016-04-14 17:06:12','2016-04-14 17:06:12',NULL),(560,214,170,4,'Survey Cohort 4',-6,-6,NULL,'2016-04-14 17:06:12','2016-04-14 17:06:12',NULL),(622,214,171,4,'Survey Cohort 4',-6,-6,NULL,'2016-04-14 17:06:12','2016-04-14 17:06:12',NULL);
/*!40000 ALTER TABLE `org_cohort_name` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_conflict`
--

DROP TABLE IF EXISTS `org_conflict`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_conflict` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `src_org_id` int(11) DEFAULT NULL,
  `dst_org_id` int(11) DEFAULT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `record_type` enum('master','home','other') COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('conflict','merged') COLLATE utf8_unicode_ci DEFAULT NULL,
  `owning_org_tier_code` enum('0','3') COLLATE utf8_unicode_ci DEFAULT NULL,
  `merge_type` enum('O','N','S','H','M') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_31D50D4CDE12AB56` (`created_by`),
  KEY `IDX_31D50D4C25F94802` (`modified_by`),
  KEY `IDX_31D50D4C1F6FA0AF` (`deleted_by`),
  KEY `fk_table1_org_person_faculty1_idx` (`faculty_id`),
  KEY `fk_table1_org_person_student1_idx` (`student_id`),
  KEY `fk_table1_organization1_idx` (`src_org_id`),
  KEY `fk_table1_organization2_idx` (`dst_org_id`),
  CONSTRAINT `FK_31D50D4C1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_31D50D4C25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_31D50D4C4A6EE8E0` FOREIGN KEY (`dst_org_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_31D50D4C680CAB68` FOREIGN KEY (`faculty_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_31D50D4CCB944F1A` FOREIGN KEY (`student_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_31D50D4CDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_31D50D4CEE195E00` FOREIGN KEY (`src_org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `org_courses_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `org_permissionset_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1660B3F4DE12AB56` (`created_by`),
  KEY `IDX_1660B3F425F94802` (`modified_by`),
  KEY `IDX_1660B3F41F6FA0AF` (`deleted_by`),
  KEY `fk_course_faculty_organization1_idx` (`organization_id`),
  KEY `fk_course_faculty_org_courses1_idx` (`org_courses_id`),
  KEY `fk_course_faculty_person1_idx` (`person_id`),
  KEY `fk_org_course_faculty_org_permissionset1_idx` (`org_permissionset_id`),
  KEY `deleted_at_idx` (`deleted_at`),
  KEY `org_person_delete_idx` (`org_permissionset_id`,`person_id`,`deleted_at`),
  KEY `course-person` (`organization_id`,`org_courses_id`,`person_id`,`org_permissionset_id`,`deleted_at`),
  KEY `person-course` (`person_id`,`organization_id`,`org_courses_id`,`org_permissionset_id`,`deleted_at`),
  CONSTRAINT `FK_1660B3F41F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1660B3F4217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1660B3F425F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1660B3F432C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_1660B3F47ABB76BC` FOREIGN KEY (`org_permissionset_id`) REFERENCES `org_permissionset` (`id`),
  CONSTRAINT `FK_1660B3F47C751C40` FOREIGN KEY (`org_courses_id`) REFERENCES `org_courses` (`id`),
  CONSTRAINT `FK_1660B3F4DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_course_faculty`
--

LOCK TABLES `org_course_faculty` WRITE;
/*!40000 ALTER TABLE `org_course_faculty` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_course_faculty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `org_course_faculty_student_permission_map`
--

DROP TABLE IF EXISTS `org_course_faculty_student_permission_map`;
/*!50001 DROP VIEW IF EXISTS `org_course_faculty_student_permission_map`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `org_course_faculty_student_permission_map` (
  `course_id` tinyint NOT NULL,
  `org_id` tinyint NOT NULL,
  `faculty_id` tinyint NOT NULL,
  `student_id` tinyint NOT NULL,
  `permissionset_id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `org_course_student`
--

DROP TABLE IF EXISTS `org_course_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_course_student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `org_courses_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B6D57C84DE12AB56` (`created_by`),
  KEY `IDX_B6D57C8425F94802` (`modified_by`),
  KEY `IDX_B6D57C841F6FA0AF` (`deleted_by`),
  KEY `fk_course_student_organization1_idx` (`organization_id`),
  KEY `fk_course_student_person1_idx` (`person_id`),
  KEY `deleted_at_idx` (`deleted_at`),
  KEY `course-person` (`organization_id`,`org_courses_id`,`person_id`,`deleted_at`),
  KEY `person-course` (`person_id`,`organization_id`,`org_courses_id`,`deleted_at`),
  KEY `fk_course_student_org_courses1_idx` (`org_courses_id`,`organization_id`,`deleted_at`),
  CONSTRAINT `FK_B6D57C841F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B6D57C84217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B6D57C8425F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B6D57C8432C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_B6D57C847C751C40` FOREIGN KEY (`org_courses_id`) REFERENCES `org_courses` (`id`),
  CONSTRAINT `FK_B6D57C84DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) NOT NULL,
  `org_academic_year_id` int(11) NOT NULL,
  `org_academic_terms_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `course_section_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `college_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dept_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `course_number` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `course_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `section_number` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `days_times` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_hours` decimal(5,2) DEFAULT NULL,
  `externalId` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniquecoursesectionid` (`organization_id`,`course_section_id`),
  KEY `IDX_DADA0E82DE12AB56` (`created_by`),
  KEY `IDX_DADA0E8225F94802` (`modified_by`),
  KEY `IDX_DADA0E821F6FA0AF` (`deleted_by`),
  KEY `fk_org_courses_org_academic_year1_idx` (`org_academic_year_id`),
  KEY `fk_org_courses_org_academic_terms1_idx` (`org_academic_terms_id`),
  KEY `idx_year` (`org_academic_year_id`),
  KEY `idx_term` (`org_academic_terms_id`),
  KEY `idx_college` (`college_code`),
  KEY `idx_dept` (`dept_code`),
  KEY `fk_org_courses_organization1_idx` (`organization_id`,`deleted_at`),
  KEY `course_org` (`id`,`organization_id`,`org_academic_terms_id`,`deleted_at`),
  CONSTRAINT `FK_DADA0E821F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_DADA0E8225F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_DADA0E8232C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_DADA0E828D7CC0D2` FOREIGN KEY (`org_academic_terms_id`) REFERENCES `org_academic_terms` (`id`),
  CONSTRAINT `FK_DADA0E82DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_DADA0E82F3B0CE4A` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_courses`
--

LOCK TABLES `org_courses` WRITE;
/*!40000 ALTER TABLE `org_courses` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_documents`
--

DROP TABLE IF EXISTS `org_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `title` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` enum('link','file') COLLATE utf8_unicode_ci DEFAULT NULL,
  `link` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_path` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `display_filename` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9BCF6EDADE12AB56` (`created_by`),
  KEY `IDX_9BCF6EDA25F94802` (`modified_by`),
  KEY `IDX_9BCF6EDA1F6FA0AF` (`deleted_by`),
  KEY `fk_org_documents_organization1_idx` (`org_id`),
  CONSTRAINT `FK_9BCF6EDA1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9BCF6EDA25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9BCF6EDADE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9BCF6EDAF4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_documents`
--

LOCK TABLES `org_documents` WRITE;
/*!40000 ALTER TABLE `org_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `org_faculty_student_permission_map`
--

DROP TABLE IF EXISTS `org_faculty_student_permission_map`;
/*!50001 DROP VIEW IF EXISTS `org_faculty_student_permission_map`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `org_faculty_student_permission_map` (
  `org_id` tinyint NOT NULL,
  `faculty_id` tinyint NOT NULL,
  `student_id` tinyint NOT NULL,
  `group_id` tinyint NOT NULL,
  `course_id` tinyint NOT NULL,
  `permissionset_id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

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
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `private` tinyint(1) DEFAULT NULL,
  `connected` tinyint(1) DEFAULT NULL,
  `team` tinyint(1) DEFAULT NULL,
  `default_access` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C36F4CF032C8A3DE` (`organization_id`),
  KEY `IDX_C36F4CF060E4B879` (`feature_id`),
  CONSTRAINT `FK_C36F4CF032C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_C36F4CF060E4B879` FOREIGN KEY (`feature_id`) REFERENCES `feature_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `organization_id` int(11) DEFAULT NULL,
  `parent_group_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `group_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `external_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_938DB7A732C8A3DE` (`organization_id`),
  KEY `IDX_938DB7A761997596` (`parent_group_id`),
  KEY `parent_group_id_IDX` (`parent_group_id`),
  CONSTRAINT `FK_938DB7A732C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_938DB7A761997596` FOREIGN KEY (`parent_group_id`) REFERENCES `org_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `org_permissionset_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `org_group_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `is_invisible` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_120C44437ABB76BC` (`org_permissionset_id`),
  KEY `IDX_120C444332C8A3DE` (`organization_id`),
  KEY `IDX_120C444382FB49A4` (`org_group_id`),
  KEY `IDX_120C4443217BBB47` (`person_id`),
  KEY `deleted_at_idx` (`deleted_at`),
  KEY `group-person` (`organization_id`,`org_group_id`,`person_id`,`deleted_at`),
  KEY `person-group` (`person_id`,`organization_id`,`org_group_id`,`deleted_at`),
  KEY `org_person_group_delete` (`organization_id`,`person_id`,`org_group_id`,`deleted_at`),
  KEY `org_person_delete_idx` (`person_id`,`organization_id`,`org_permissionset_id`,`deleted_at`),
  KEY `PG_perm` (`organization_id`,`person_id`,`org_group_id`,`deleted_at`,`org_permissionset_id`),
  CONSTRAINT `FK_120C4443217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_120C444332C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_120C44437ABB76BC` FOREIGN KEY (`org_permissionset_id`) REFERENCES `org_permissionset` (`id`),
  CONSTRAINT `FK_120C444382FB49A4` FOREIGN KEY (`org_group_id`) REFERENCES `org_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_group_faculty`
--

LOCK TABLES `org_group_faculty` WRITE;
/*!40000 ALTER TABLE `org_group_faculty` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_group_faculty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `org_group_faculty_student_permission_map`
--

DROP TABLE IF EXISTS `org_group_faculty_student_permission_map`;
/*!50001 DROP VIEW IF EXISTS `org_group_faculty_student_permission_map`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `org_group_faculty_student_permission_map` (
  `group_id` tinyint NOT NULL,
  `org_id` tinyint NOT NULL,
  `faculty_id` tinyint NOT NULL,
  `student_id` tinyint NOT NULL,
  `permissionset_id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `org_group_students`
--

DROP TABLE IF EXISTS `org_group_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_group_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) DEFAULT NULL,
  `org_group_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A46C1796217BBB47` (`person_id`),
  KEY `IDX_A46C179682FB49A4` (`org_group_id`),
  KEY `IDX_A46C179632C8A3DE` (`organization_id`),
  KEY `deleted_at_idx` (`deleted_at`),
  KEY `group-student` (`organization_id`,`org_group_id`,`person_id`,`deleted_at`),
  KEY `student-group` (`person_id`,`organization_id`,`org_group_id`,`deleted_at`),
  CONSTRAINT `FK_A46C1796217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_A46C179632C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_A46C179682FB49A4` FOREIGN KEY (`org_group_id`) REFERENCES `org_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_group_students`
--

LOCK TABLES `org_group_students` WRITE;
/*!40000 ALTER TABLE `org_group_students` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_group_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_group_tree`
--

DROP TABLE IF EXISTS `org_group_tree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_group_tree` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ancestor_group_id` int(11) NOT NULL,
  `descendant_group_id` int(11) NOT NULL,
  `path_length` smallint(6) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_ancestor_group_id_IDX` (`ancestor_group_id`),
  KEY `FK_descendant_group_id_IDX` (`descendant_group_id`),
  KEY `IDX_ancestor_descendant` (`ancestor_group_id`,`descendant_group_id`,`deleted_at`),
  KEY `FK_1B384A68DE12AB56` (`created_by`),
  KEY `FK_1B384A6825F94802` (`modified_by`),
  KEY `FK_1B384A681F6FA0AF` (`deleted_by`),
  CONSTRAINT `FK_1B384A681F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1B384A6825F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1B384A68582DCD11` FOREIGN KEY (`ancestor_group_id`) REFERENCES `org_group` (`id`),
  CONSTRAINT `FK_1B384A68DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1B384A68FCA5FABF` FOREIGN KEY (`descendant_group_id`) REFERENCES `org_group` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=284543 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_group_tree`
--

LOCK TABLES `org_group_tree` WRITE;
/*!40000 ALTER TABLE `org_group_tree` DISABLE KEYS */;
INSERT INTO `org_group_tree` VALUES (65674,370421,370421,0,NULL,'2016-03-29 17:10:07',NULL,'2016-03-29 17:10:07',NULL,NULL),(65679,370426,370426,0,NULL,'2016-03-31 19:47:39',NULL,'2016-03-31 19:47:49',NULL,NULL),(65680,370427,370427,0,NULL,'2016-03-31 19:48:26',NULL,'2016-03-31 19:48:26',NULL,NULL),(65681,370428,370428,0,NULL,'2016-03-31 19:48:53',NULL,'2016-03-31 19:48:53',NULL,'2016-03-31 19:49:10'),(65682,370429,370429,0,NULL,'2016-03-31 19:49:47',NULL,'2016-03-31 19:49:47',NULL,NULL),(65683,370430,370430,0,NULL,'2016-03-31 19:50:30',NULL,'2016-03-31 19:50:30',NULL,NULL),(65684,370431,370431,0,NULL,'2016-03-31 19:51:09',NULL,'2016-03-31 19:51:09',NULL,NULL),(65685,370432,370432,0,NULL,'2016-03-31 19:52:04',NULL,'2016-03-31 19:52:04',NULL,NULL),(65686,370433,370433,0,NULL,'2016-03-31 19:52:30',NULL,'2016-03-31 19:53:08',NULL,NULL),(65687,370434,370434,0,NULL,'2016-03-31 19:52:56',NULL,'2016-03-31 19:52:56',NULL,NULL),(65688,370435,370435,0,NULL,'2016-03-31 19:59:59',NULL,'2016-03-31 19:59:59',NULL,NULL),(65689,370436,370436,0,NULL,'2016-03-31 20:01:07',NULL,'2016-03-31 20:01:07',NULL,NULL),(65690,370437,370437,0,NULL,'2016-03-31 20:02:05',NULL,'2016-03-31 20:02:05',NULL,NULL),(65691,370438,370438,0,NULL,'2016-03-31 20:02:47',NULL,'2016-03-31 20:02:47',NULL,NULL),(65692,370439,370439,0,NULL,'2016-03-31 20:03:35',NULL,'2016-03-31 20:03:35',NULL,NULL),(65693,370440,370440,0,NULL,'2016-03-31 20:04:33',NULL,'2016-03-31 20:04:33',NULL,NULL),(65694,370441,370441,0,NULL,'2016-03-31 20:05:11',NULL,'2016-03-31 20:05:11',NULL,NULL),(65695,370442,370442,0,NULL,'2016-03-31 20:06:03',NULL,'2016-03-31 20:06:03',NULL,NULL),(65696,370443,370443,0,NULL,'2016-03-31 20:06:39',NULL,'2016-03-31 20:06:39',NULL,NULL),(65697,370444,370444,0,NULL,'2016-03-31 20:07:25',NULL,'2016-03-31 20:07:25',NULL,NULL),(65698,370445,370445,0,NULL,'2016-03-31 20:08:05',NULL,'2016-03-31 20:10:42',NULL,NULL),(65699,370446,370446,0,NULL,'2016-03-31 20:08:45',NULL,'2016-03-31 20:10:25',NULL,NULL),(65700,370447,370447,0,NULL,'2016-03-31 20:09:33',NULL,'2016-03-31 20:10:09',NULL,NULL),(65701,370448,370448,0,NULL,'2016-03-31 20:11:58',NULL,'2016-03-31 20:11:58',NULL,NULL),(65702,370449,370449,0,NULL,'2016-03-31 20:17:31',NULL,'2016-03-31 20:17:31',NULL,NULL),(65703,370450,370450,0,NULL,'2016-03-31 20:18:23',NULL,'2016-03-31 20:18:23',NULL,NULL),(65704,370451,370451,0,NULL,'2016-03-31 20:19:35',NULL,'2016-03-31 20:19:35',NULL,NULL),(65705,370452,370452,0,NULL,'2016-03-31 20:20:23',NULL,'2016-03-31 20:20:23',NULL,NULL),(65706,370453,370453,0,NULL,'2016-03-31 20:24:11',NULL,'2016-03-31 20:24:11',NULL,NULL),(65707,370454,370454,0,NULL,'2016-03-31 20:25:01',NULL,'2016-03-31 20:25:01',NULL,NULL),(65708,370455,370455,0,NULL,'2016-03-31 20:25:42',NULL,'2016-03-31 20:25:42',NULL,NULL),(65709,370456,370456,0,NULL,'2016-04-01 12:20:44',NULL,'2016-04-01 12:20:44',NULL,NULL),(65710,370457,370457,0,NULL,'2016-04-01 12:21:20',NULL,'2016-04-01 12:21:20',NULL,NULL),(65711,370458,370458,0,NULL,'2016-04-01 12:23:24',NULL,'2016-04-01 12:23:24',NULL,NULL),(65712,370459,370459,0,NULL,'2016-04-01 12:26:11',NULL,'2016-04-01 12:26:11',NULL,NULL),(65713,370460,370460,0,NULL,'2016-04-01 12:27:03',NULL,'2016-04-01 12:27:03',NULL,NULL),(65714,370461,370461,0,NULL,'2016-04-01 12:28:30',NULL,'2016-04-01 12:28:30',NULL,NULL),(65715,370462,370462,0,NULL,'2016-04-01 12:29:20',NULL,'2016-04-01 12:29:20',NULL,NULL),(65716,370463,370463,0,NULL,'2016-04-01 12:30:13',NULL,'2016-04-01 12:30:13',NULL,NULL),(65717,370464,370464,0,NULL,'2016-04-01 12:31:11',NULL,'2016-04-01 12:31:11',NULL,NULL),(65718,370465,370465,0,NULL,'2016-04-01 12:31:50',NULL,'2016-04-01 12:31:50',NULL,NULL),(65719,370466,370466,0,NULL,'2016-04-01 12:32:33',NULL,'2016-04-01 12:32:33',NULL,NULL),(65720,370467,370467,0,NULL,'2016-04-01 12:35:30',NULL,'2016-04-01 12:35:30',NULL,NULL),(65721,370468,370468,0,NULL,'2016-04-01 12:36:14',NULL,'2016-04-01 12:36:14',NULL,NULL),(65722,370469,370469,0,NULL,'2016-04-01 12:37:02',NULL,'2016-04-01 12:37:02',NULL,NULL),(65723,370470,370470,0,NULL,'2016-04-01 13:48:50',NULL,'2016-04-01 13:48:50',NULL,NULL),(65724,370471,370471,0,NULL,'2016-04-01 13:50:03',NULL,'2016-04-01 13:50:03',NULL,NULL),(65725,370472,370472,0,NULL,'2016-04-01 13:51:30',NULL,'2016-04-01 13:51:30',NULL,NULL),(65726,370473,370473,0,NULL,'2016-04-01 13:52:32',NULL,'2016-04-01 13:52:32',NULL,NULL),(65727,370474,370474,0,NULL,'2016-04-01 13:55:17',NULL,'2016-04-01 13:55:17',NULL,NULL),(65728,370475,370475,0,NULL,'2016-04-01 13:56:23',NULL,'2016-04-01 13:56:23',NULL,NULL),(65729,370476,370476,0,NULL,'2016-04-01 13:57:25',NULL,'2016-04-01 13:57:25',NULL,NULL),(65730,370477,370477,0,NULL,'2016-04-01 13:58:35',NULL,'2016-04-01 13:58:35',NULL,NULL),(65731,370478,370478,0,NULL,'2016-04-01 13:59:19',NULL,'2016-04-01 13:59:19',NULL,NULL),(65732,370479,370479,0,NULL,'2016-04-01 14:00:05',NULL,'2016-04-01 14:00:05',NULL,NULL),(65733,370480,370480,0,NULL,'2016-04-01 14:01:01',NULL,'2016-04-01 14:01:01',NULL,NULL),(65734,370481,370481,0,NULL,'2016-04-01 14:02:36',NULL,'2016-04-01 14:02:36',NULL,NULL),(65735,370482,370482,0,NULL,'2016-04-01 14:04:01',NULL,'2016-04-01 14:04:01',NULL,NULL),(65736,370483,370483,0,NULL,'2016-04-01 14:07:55',NULL,'2016-04-01 14:07:55',NULL,NULL),(65737,370484,370484,0,NULL,'2016-04-01 14:27:25',NULL,'2016-04-01 14:27:25',NULL,NULL),(65738,370485,370485,0,NULL,'2016-04-01 14:28:15',NULL,'2016-04-01 14:28:15',NULL,NULL),(65739,370486,370486,0,NULL,'2016-04-01 14:29:00',NULL,'2016-04-01 14:29:00',NULL,NULL),(65740,370487,370487,0,NULL,'2016-04-01 14:38:21',NULL,'2016-04-01 14:38:21',NULL,NULL),(65741,370488,370488,0,NULL,'2016-04-01 14:39:45',NULL,'2016-04-01 14:39:45',NULL,NULL),(65742,370489,370489,0,NULL,'2016-04-01 14:40:25',NULL,'2016-04-01 14:40:25',NULL,NULL),(65743,370490,370490,0,NULL,'2016-04-01 14:41:09',NULL,'2016-04-01 14:41:09',NULL,NULL),(65744,370491,370491,0,NULL,'2016-04-01 14:41:49',NULL,'2016-04-01 14:41:49',NULL,NULL),(65745,370492,370492,0,NULL,'2016-04-01 17:21:53',NULL,'2016-04-01 17:21:53',NULL,NULL),(65746,370493,370493,0,NULL,'2016-04-01 17:22:34',NULL,'2016-04-01 17:22:34',NULL,NULL),(65747,370494,370494,0,NULL,'2016-04-01 17:25:20',NULL,'2016-04-01 17:25:20',NULL,NULL),(65748,370495,370495,0,NULL,'2016-04-01 18:10:39',NULL,'2016-04-01 18:10:39',NULL,NULL),(194210,370426,370427,1,NULL,'2016-03-31 19:48:26',NULL,'2016-03-31 19:48:26',NULL,NULL),(194211,370426,370428,1,NULL,'2016-03-31 19:48:53',NULL,'2016-03-31 19:48:53',NULL,'2016-03-31 19:49:10'),(194212,370426,370429,1,NULL,'2016-03-31 19:49:47',NULL,'2016-03-31 19:49:47',NULL,NULL),(194213,370426,370430,1,NULL,'2016-03-31 19:50:30',NULL,'2016-03-31 19:50:30',NULL,NULL),(194214,370427,370431,1,NULL,'2016-03-31 19:51:09',NULL,'2016-03-31 19:51:09',NULL,NULL),(194215,370427,370432,1,NULL,'2016-03-31 19:52:04',NULL,'2016-03-31 19:52:04',NULL,NULL),(194216,370427,370433,1,NULL,'2016-03-31 19:52:30',NULL,'2016-03-31 19:53:08',NULL,NULL),(194217,370427,370434,1,NULL,'2016-03-31 19:52:56',NULL,'2016-03-31 19:52:56',NULL,NULL),(194218,370429,370435,1,NULL,'2016-03-31 19:59:59',NULL,'2016-03-31 19:59:59',NULL,NULL),(194219,370429,370436,1,NULL,'2016-03-31 20:01:07',NULL,'2016-03-31 20:01:07',NULL,NULL),(194220,370429,370437,1,NULL,'2016-03-31 20:02:05',NULL,'2016-03-31 20:02:05',NULL,NULL),(194221,370429,370438,1,NULL,'2016-03-31 20:02:47',NULL,'2016-03-31 20:02:47',NULL,NULL),(194222,370429,370439,1,NULL,'2016-03-31 20:03:35',NULL,'2016-03-31 20:03:35',NULL,NULL),(194223,370429,370440,1,NULL,'2016-03-31 20:04:33',NULL,'2016-03-31 20:04:33',NULL,NULL),(194224,370429,370441,1,NULL,'2016-03-31 20:05:11',NULL,'2016-03-31 20:05:11',NULL,NULL),(194225,370429,370442,1,NULL,'2016-03-31 20:06:03',NULL,'2016-03-31 20:06:03',NULL,NULL),(194226,370429,370443,1,NULL,'2016-03-31 20:06:39',NULL,'2016-03-31 20:06:39',NULL,NULL),(194227,370429,370444,1,NULL,'2016-03-31 20:07:25',NULL,'2016-03-31 20:07:25',NULL,NULL),(194228,370429,370445,1,NULL,'2016-03-31 20:08:05',NULL,'2016-03-31 20:10:42',NULL,NULL),(194229,370429,370446,1,NULL,'2016-03-31 20:08:45',NULL,'2016-03-31 20:10:25',NULL,NULL),(194230,370429,370447,1,NULL,'2016-03-31 20:09:33',NULL,'2016-03-31 20:10:09',NULL,NULL),(194231,370430,370448,1,NULL,'2016-03-31 20:11:58',NULL,'2016-03-31 20:11:58',NULL,NULL),(194232,370430,370449,1,NULL,'2016-03-31 20:17:31',NULL,'2016-03-31 20:17:31',NULL,NULL),(194233,370430,370450,1,NULL,'2016-03-31 20:18:23',NULL,'2016-03-31 20:18:23',NULL,NULL),(194234,370451,370452,1,NULL,'2016-03-31 20:20:23',NULL,'2016-03-31 20:20:23',NULL,NULL),(194235,370451,370453,1,NULL,'2016-03-31 20:24:11',NULL,'2016-03-31 20:24:11',NULL,NULL),(194236,370451,370454,1,NULL,'2016-03-31 20:25:01',NULL,'2016-03-31 20:25:01',NULL,NULL),(194237,370451,370455,1,NULL,'2016-03-31 20:25:42',NULL,'2016-03-31 20:25:42',NULL,NULL),(194238,370456,370457,1,NULL,'2016-04-01 12:21:20',NULL,'2016-04-01 12:21:20',NULL,NULL),(194239,370456,370458,1,NULL,'2016-04-01 12:23:24',NULL,'2016-04-01 12:23:24',NULL,NULL),(194240,370456,370459,1,NULL,'2016-04-01 12:26:11',NULL,'2016-04-01 12:26:11',NULL,NULL),(194241,370456,370460,1,NULL,'2016-04-01 12:27:03',NULL,'2016-04-01 12:27:03',NULL,NULL),(194242,370456,370461,1,NULL,'2016-04-01 12:28:30',NULL,'2016-04-01 12:28:30',NULL,NULL),(194243,370456,370462,1,NULL,'2016-04-01 12:29:20',NULL,'2016-04-01 12:29:20',NULL,NULL),(194244,370456,370463,1,NULL,'2016-04-01 12:30:13',NULL,'2016-04-01 12:30:13',NULL,NULL),(194245,370456,370464,1,NULL,'2016-04-01 12:31:11',NULL,'2016-04-01 12:31:11',NULL,NULL),(194246,370456,370465,1,NULL,'2016-04-01 12:31:50',NULL,'2016-04-01 12:31:50',NULL,NULL),(194247,370456,370466,1,NULL,'2016-04-01 12:32:33',NULL,'2016-04-01 12:32:33',NULL,NULL),(194248,370456,370467,1,NULL,'2016-04-01 12:35:30',NULL,'2016-04-01 12:35:30',NULL,NULL),(194249,370456,370468,1,NULL,'2016-04-01 12:36:14',NULL,'2016-04-01 12:36:14',NULL,NULL),(194250,370456,370469,1,NULL,'2016-04-01 12:37:02',NULL,'2016-04-01 12:37:02',NULL,NULL),(194251,370470,370471,1,NULL,'2016-04-01 13:50:03',NULL,'2016-04-01 13:50:03',NULL,NULL),(194252,370471,370472,1,NULL,'2016-04-01 13:51:30',NULL,'2016-04-01 13:51:30',NULL,NULL),(194253,370472,370473,1,NULL,'2016-04-01 13:52:32',NULL,'2016-04-01 13:52:32',NULL,NULL),(194254,370472,370474,1,NULL,'2016-04-01 13:55:17',NULL,'2016-04-01 13:55:17',NULL,NULL),(194255,370472,370475,1,NULL,'2016-04-01 13:56:23',NULL,'2016-04-01 13:56:23',NULL,NULL),(194256,370472,370476,1,NULL,'2016-04-01 13:57:25',NULL,'2016-04-01 13:57:25',NULL,NULL),(194257,370472,370477,1,NULL,'2016-04-01 13:58:35',NULL,'2016-04-01 13:58:35',NULL,NULL),(194258,370472,370478,1,NULL,'2016-04-01 13:59:19',NULL,'2016-04-01 13:59:19',NULL,NULL),(194259,370472,370479,1,NULL,'2016-04-01 14:00:05',NULL,'2016-04-01 14:00:05',NULL,NULL),(194260,370472,370480,1,NULL,'2016-04-01 14:01:01',NULL,'2016-04-01 14:01:01',NULL,NULL),(194261,370470,370481,1,NULL,'2016-04-01 14:02:36',NULL,'2016-04-01 14:02:36',NULL,NULL),(194262,370481,370482,1,NULL,'2016-04-01 14:04:01',NULL,'2016-04-01 14:04:01',NULL,NULL),(194263,370482,370483,1,NULL,'2016-04-01 14:07:55',NULL,'2016-04-01 14:07:55',NULL,NULL),(194264,370482,370484,1,NULL,'2016-04-01 14:27:25',NULL,'2016-04-01 14:27:25',NULL,NULL),(194265,370482,370485,1,NULL,'2016-04-01 14:28:15',NULL,'2016-04-01 14:28:15',NULL,NULL),(194266,370482,370486,1,NULL,'2016-04-01 14:29:00',NULL,'2016-04-01 14:29:00',NULL,NULL),(194267,370481,370487,1,NULL,'2016-04-01 14:38:21',NULL,'2016-04-01 14:38:21',NULL,NULL),(194268,370487,370488,1,NULL,'2016-04-01 14:39:45',NULL,'2016-04-01 14:39:45',NULL,NULL),(194269,370487,370489,1,NULL,'2016-04-01 14:40:25',NULL,'2016-04-01 14:40:25',NULL,NULL),(194270,370487,370490,1,NULL,'2016-04-01 14:41:09',NULL,'2016-04-01 14:41:09',NULL,NULL),(194271,370487,370491,1,NULL,'2016-04-01 14:41:49',NULL,'2016-04-01 14:41:49',NULL,NULL),(194272,370492,370493,1,NULL,'2016-04-01 17:22:34',NULL,'2016-04-01 17:22:34',NULL,NULL),(194273,370492,370494,1,NULL,'2016-04-01 17:25:20',NULL,'2016-04-01 17:25:20',NULL,NULL),(194274,370492,370495,1,NULL,'2016-04-01 18:10:39',NULL,'2016-04-01 18:10:39',NULL,NULL),(233383,370426,370431,2,NULL,'2016-03-31 19:51:09',NULL,'2016-03-31 19:51:09',NULL,NULL),(233384,370426,370432,2,NULL,'2016-03-31 19:52:04',NULL,'2016-03-31 19:52:04',NULL,NULL),(233385,370426,370433,2,NULL,'2016-03-31 19:52:30',NULL,'2016-03-31 19:53:08',NULL,NULL),(233386,370426,370434,2,NULL,'2016-03-31 19:52:56',NULL,'2016-03-31 19:52:56',NULL,NULL),(233387,370426,370435,2,NULL,'2016-03-31 19:59:59',NULL,'2016-03-31 19:59:59',NULL,NULL),(233388,370426,370436,2,NULL,'2016-03-31 20:01:07',NULL,'2016-03-31 20:01:07',NULL,NULL),(233389,370426,370437,2,NULL,'2016-03-31 20:02:05',NULL,'2016-03-31 20:02:05',NULL,NULL),(233390,370426,370438,2,NULL,'2016-03-31 20:02:47',NULL,'2016-03-31 20:02:47',NULL,NULL),(233391,370426,370439,2,NULL,'2016-03-31 20:03:35',NULL,'2016-03-31 20:03:35',NULL,NULL),(233392,370426,370440,2,NULL,'2016-03-31 20:04:33',NULL,'2016-03-31 20:04:33',NULL,NULL),(233393,370426,370441,2,NULL,'2016-03-31 20:05:11',NULL,'2016-03-31 20:05:11',NULL,NULL),(233394,370426,370442,2,NULL,'2016-03-31 20:06:03',NULL,'2016-03-31 20:06:03',NULL,NULL),(233395,370426,370443,2,NULL,'2016-03-31 20:06:39',NULL,'2016-03-31 20:06:39',NULL,NULL),(233396,370426,370444,2,NULL,'2016-03-31 20:07:25',NULL,'2016-03-31 20:07:25',NULL,NULL),(233397,370426,370445,2,NULL,'2016-03-31 20:08:05',NULL,'2016-03-31 20:10:42',NULL,NULL),(233398,370426,370446,2,NULL,'2016-03-31 20:08:45',NULL,'2016-03-31 20:10:25',NULL,NULL),(233399,370426,370447,2,NULL,'2016-03-31 20:09:33',NULL,'2016-03-31 20:10:09',NULL,NULL),(233400,370426,370448,2,NULL,'2016-03-31 20:11:58',NULL,'2016-03-31 20:11:58',NULL,NULL),(233401,370426,370449,2,NULL,'2016-03-31 20:17:31',NULL,'2016-03-31 20:17:31',NULL,NULL),(233402,370426,370450,2,NULL,'2016-03-31 20:18:23',NULL,'2016-03-31 20:18:23',NULL,NULL),(233403,370470,370472,2,NULL,'2016-04-01 13:51:30',NULL,'2016-04-01 13:51:30',NULL,NULL),(233404,370471,370473,2,NULL,'2016-04-01 13:52:32',NULL,'2016-04-01 13:52:32',NULL,NULL),(233405,370471,370474,2,NULL,'2016-04-01 13:55:17',NULL,'2016-04-01 13:55:17',NULL,NULL),(233406,370471,370475,2,NULL,'2016-04-01 13:56:23',NULL,'2016-04-01 13:56:23',NULL,NULL),(233407,370471,370476,2,NULL,'2016-04-01 13:57:25',NULL,'2016-04-01 13:57:25',NULL,NULL),(233408,370471,370477,2,NULL,'2016-04-01 13:58:35',NULL,'2016-04-01 13:58:35',NULL,NULL),(233409,370471,370478,2,NULL,'2016-04-01 13:59:19',NULL,'2016-04-01 13:59:19',NULL,NULL),(233410,370471,370479,2,NULL,'2016-04-01 14:00:05',NULL,'2016-04-01 14:00:05',NULL,NULL),(233411,370471,370480,2,NULL,'2016-04-01 14:01:01',NULL,'2016-04-01 14:01:01',NULL,NULL),(233412,370470,370482,2,NULL,'2016-04-01 14:04:01',NULL,'2016-04-01 14:04:01',NULL,NULL),(233413,370481,370483,2,NULL,'2016-04-01 14:07:55',NULL,'2016-04-01 14:07:55',NULL,NULL),(233414,370481,370484,2,NULL,'2016-04-01 14:27:25',NULL,'2016-04-01 14:27:25',NULL,NULL),(233415,370481,370485,2,NULL,'2016-04-01 14:28:15',NULL,'2016-04-01 14:28:15',NULL,NULL),(233416,370481,370486,2,NULL,'2016-04-01 14:29:00',NULL,'2016-04-01 14:29:00',NULL,NULL),(233417,370470,370487,2,NULL,'2016-04-01 14:38:21',NULL,'2016-04-01 14:38:21',NULL,NULL),(233418,370481,370488,2,NULL,'2016-04-01 14:39:45',NULL,'2016-04-01 14:39:45',NULL,NULL),(233419,370481,370489,2,NULL,'2016-04-01 14:40:25',NULL,'2016-04-01 14:40:25',NULL,NULL),(233420,370481,370490,2,NULL,'2016-04-01 14:41:09',NULL,'2016-04-01 14:41:09',NULL,NULL),(233421,370481,370491,2,NULL,'2016-04-01 14:41:49',NULL,'2016-04-01 14:41:49',NULL,NULL),(284527,370470,370473,3,NULL,'2016-04-01 13:52:32',NULL,'2016-04-01 13:52:32',NULL,NULL),(284528,370470,370474,3,NULL,'2016-04-01 13:55:17',NULL,'2016-04-01 13:55:17',NULL,NULL),(284529,370470,370475,3,NULL,'2016-04-01 13:56:23',NULL,'2016-04-01 13:56:23',NULL,NULL),(284530,370470,370476,3,NULL,'2016-04-01 13:57:25',NULL,'2016-04-01 13:57:25',NULL,NULL),(284531,370470,370477,3,NULL,'2016-04-01 13:58:35',NULL,'2016-04-01 13:58:35',NULL,NULL),(284532,370470,370478,3,NULL,'2016-04-01 13:59:19',NULL,'2016-04-01 13:59:19',NULL,NULL),(284533,370470,370479,3,NULL,'2016-04-01 14:00:05',NULL,'2016-04-01 14:00:05',NULL,NULL),(284534,370470,370480,3,NULL,'2016-04-01 14:01:01',NULL,'2016-04-01 14:01:01',NULL,NULL),(284535,370470,370483,3,NULL,'2016-04-01 14:07:55',NULL,'2016-04-01 14:07:55',NULL,NULL),(284536,370470,370484,3,NULL,'2016-04-01 14:27:25',NULL,'2016-04-01 14:27:25',NULL,NULL),(284537,370470,370485,3,NULL,'2016-04-01 14:28:15',NULL,'2016-04-01 14:28:15',NULL,NULL),(284538,370470,370486,3,NULL,'2016-04-01 14:29:00',NULL,'2016-04-01 14:29:00',NULL,NULL),(284539,370470,370488,3,NULL,'2016-04-01 14:39:45',NULL,'2016-04-01 14:39:45',NULL,NULL),(284540,370470,370489,3,NULL,'2016-04-01 14:40:25',NULL,'2016-04-01 14:40:25',NULL,NULL),(284541,370470,370490,3,NULL,'2016-04-01 14:41:09',NULL,'2016-04-01 14:41:09',NULL,NULL),(284542,370470,370491,3,NULL,'2016-04-01 14:41:49',NULL,'2016-04-01 14:41:49',NULL,NULL);
/*!40000 ALTER TABLE `org_group_tree` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_ldap_config`
--

DROP TABLE IF EXISTS `org_ldap_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_ldap_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `type` enum('AD','LDAP') COLLATE utf8_unicode_ci DEFAULT NULL,
  `staff_hostname` longtext COLLATE utf8_unicode_ci,
  `staff_user_base_domain` longtext COLLATE utf8_unicode_ci,
  `staff_username_attribute` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `staff_initial_user` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `staff_initial_password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_initial_user` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_initial_password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_user_base_domain` longtext COLLATE utf8_unicode_ci,
  `student_username_attribute` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_hostname` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_22545809DE12AB56` (`created_by`),
  KEY `IDX_2254580925F94802` (`modified_by`),
  KEY `IDX_225458091F6FA0AF` (`deleted_by`),
  KEY `fk_org_ldap_config_organization1_idx` (`org_id`),
  CONSTRAINT `FK_225458091F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2254580925F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_22545809DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_22545809F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_ldap_config`
--

LOCK TABLES `org_ldap_config` WRITE;
/*!40000 ALTER TABLE `org_ldap_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_ldap_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_metadata`
--

DROP TABLE IF EXISTS `org_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `meta_key` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_description` longtext COLLATE utf8_unicode_ci,
  `definition_type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `metadata_type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `no_of_decimals` int(11) DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT NULL,
  `min_range` decimal(15,4) DEFAULT NULL,
  `max_range` decimal(15,4) DEFAULT NULL,
  `entity` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `meta_group` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('active','archived') COLLATE utf8_unicode_ci DEFAULT NULL,
  `scope` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_index_org_key` (`organization_id`,`meta_key`),
  KEY `fk_org_metadata_organization1_idx` (`organization_id`),
  KEY `modified` (`modified_at`,`created_at`),
  CONSTRAINT `FK_33BBA4F732C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_metadata`
--

LOCK TABLES `org_metadata` WRITE;
/*!40000 ALTER TABLE `org_metadata` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_metadata_list_values`
--

DROP TABLE IF EXISTS `org_metadata_list_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_metadata_list_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_metadata_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `list_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `list_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EDD17EBE4012B3BF` (`org_metadata_id`),
  KEY `list_name_idx` (`list_name`),
  CONSTRAINT `FK_EDD17EBE4012B3BF` FOREIGN KEY (`org_metadata_id`) REFERENCES `org_metadata` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `permissionset_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_archived` tinyint(1) DEFAULT NULL,
  `accesslevel_ind_agg` tinyint(1) DEFAULT NULL,
  `accesslevel_agg` tinyint(1) DEFAULT NULL,
  `risk_indicator` tinyint(1) DEFAULT NULL,
  `intent_to_leave` tinyint(1) DEFAULT NULL,
  `view_courses` tinyint(1) DEFAULT NULL,
  `create_view_academic_update` tinyint(1) DEFAULT NULL,
  `view_all_academic_update_courses` tinyint(1) DEFAULT NULL,
  `view_all_final_grades` tinyint(1) DEFAULT NULL,
  `current_future_isq` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FD169C2A32C8A3DE` (`organization_id`),
  KEY `permissionset_name_idx` (`permissionset_name`),
  KEY `accesslevel_ind_agg_idx` (`accesslevel_ind_agg`),
  KEY `risk_indicator_idx` (`risk_indicator`),
  KEY `intent_to_leave_idx` (`intent_to_leave`),
  CONSTRAINT `FK_FD169C2A32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `org_permissionset_id` int(11) DEFAULT NULL,
  `datablock_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `block_type` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timeframe_all` tinyint(1) DEFAULT NULL,
  `current_calendar` tinyint(1) DEFAULT NULL,
  `previous_period` tinyint(1) DEFAULT NULL,
  `next_period` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E34ECC407ABB76BC` (`org_permissionset_id`),
  KEY `IDX_E34ECC40F9AE3580` (`datablock_id`),
  KEY `IDX_E34ECC4032C8A3DE` (`organization_id`),
  KEY `block_type_idx` (`block_type`),
  KEY `opdb` (`org_permissionset_id`,`datablock_id`,`organization_id`,`deleted_at`),
  KEY `org_opdb` (`organization_id`,`datablock_id`,`org_permissionset_id`,`deleted_at`),
  CONSTRAINT `FK_E34ECC4032C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_E34ECC407ABB76BC` FOREIGN KEY (`org_permissionset_id`) REFERENCES `org_permissionset` (`id`),
  CONSTRAINT `FK_E34ECC40F9AE3580` FOREIGN KEY (`datablock_id`) REFERENCES `datablock_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `feature_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `org_permissionset_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `timeframe_all` tinyint(1) DEFAULT NULL,
  `current_calendar` tinyint(1) DEFAULT NULL,
  `previous_period` tinyint(1) DEFAULT NULL,
  `next_period` tinyint(1) DEFAULT NULL,
  `private_create` tinyint(1) DEFAULT NULL,
  `team_create` tinyint(1) DEFAULT NULL,
  `team_view` tinyint(1) DEFAULT NULL,
  `public_create` tinyint(1) DEFAULT NULL,
  `public_view` tinyint(1) DEFAULT NULL,
  `receive_referral` tinyint(1) DEFAULT NULL,
  `reason_referral_private_create` tinyint(1) DEFAULT NULL,
  `reason_referral_team_create` tinyint(1) DEFAULT NULL,
  `reason_referral_team_view` tinyint(1) DEFAULT NULL,
  `reason_referral_public_create` tinyint(1) DEFAULT NULL,
  `reason_referral_public_view` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_53F293C360E4B879` (`feature_id`),
  KEY `IDX_53F293C332C8A3DE` (`organization_id`),
  KEY `IDX_53F293C37ABB76BC` (`org_permissionset_id`),
  CONSTRAINT `FK_53F293C332C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_53F293C360E4B879` FOREIGN KEY (`feature_id`) REFERENCES `feature_master` (`id`),
  CONSTRAINT `FK_53F293C37ABB76BC` FOREIGN KEY (`org_permissionset_id`) REFERENCES `org_permissionset` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `organization_id` int(11) DEFAULT NULL,
  `org_permissionset_id` int(11) DEFAULT NULL,
  `org_metadata_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A3267BC432C8A3DE` (`organization_id`),
  KEY `IDX_A3267BC47ABB76BC` (`org_permissionset_id`),
  KEY `IDX_A3267BC44012B3BF` (`org_metadata_id`),
  CONSTRAINT `FK_A3267BC432C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_A3267BC44012B3BF` FOREIGN KEY (`org_metadata_id`) REFERENCES `org_metadata` (`id`),
  CONSTRAINT `FK_A3267BC47ABB76BC` FOREIGN KEY (`org_permissionset_id`) REFERENCES `org_permissionset` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `organization_id` int(11) DEFAULT NULL,
  `org_permissionset_id` int(11) DEFAULT NULL,
  `org_question_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `cohort_code` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5AC5069E32C8A3DE` (`organization_id`),
  KEY `IDX_5AC5069E7ABB76BC` (`org_permissionset_id`),
  KEY `IDX_5AC5069E82ABAC59` (`org_question_id`),
  KEY `IDX_5AC5069EB3FE509D` (`survey_id`),
  CONSTRAINT `FK_5AC5069E32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_5AC5069E7ABB76BC` FOREIGN KEY (`org_permissionset_id`) REFERENCES `org_permissionset` (`id`),
  CONSTRAINT `FK_5AC5069E82ABAC59` FOREIGN KEY (`org_question_id`) REFERENCES `org_question` (`id`),
  CONSTRAINT `FK_5AC5069EB3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_key` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `maf_to_pcs_is_active` enum('y','n','i') COLLATE utf8_unicode_ci DEFAULT NULL,
  `pcs_to_maf_is_active` enum('y','n') COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_client_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_email_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_p12_filename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_page_token` varchar(130) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_sync_token` varchar(130) COLLATE utf8_unicode_ci DEFAULT NULL,
  `msexchange_sync_state` varchar(130) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3C3D059132C8A3DE` (`organization_id`),
  KEY `IDX_3C3D0591217BBB47` (`person_id`),
  KEY `deleted_at_idx` (`deleted_at`),
  KEY `FK_3C3D059132C8A3DE_idx` (`organization_id`,`person_id`,`deleted_at`),
  KEY `FK_3C3D0591217BBB47_idx` (`person_id`,`organization_id`,`deleted_at`),
  CONSTRAINT `FK_3C3D0591217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3C3D059132C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_person_faculty`
--

LOCK TABLES `org_person_faculty` WRITE;
/*!40000 ALTER TABLE `org_person_faculty` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_person_faculty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `org_person_riskvariable`
--

DROP TABLE IF EXISTS `org_person_riskvariable`;
/*!50001 DROP VIEW IF EXISTS `org_person_riskvariable`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `org_person_riskvariable` (
  `org_id` tinyint NOT NULL,
  `person_id` tinyint NOT NULL,
  `risk_variable_id` tinyint NOT NULL,
  `source` tinyint NOT NULL,
  `variable_type` tinyint NOT NULL,
  `calc_type` tinyint NOT NULL,
  `risk_group_id` tinyint NOT NULL,
  `calculation_end_date` tinyint NOT NULL,
  `calculation_start_date` tinyint NOT NULL,
  `risk_model_id` tinyint NOT NULL,
  `weight` tinyint NOT NULL,
  `modified_at` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `org_person_riskvariable_datum`
--

DROP TABLE IF EXISTS `org_person_riskvariable_datum`;
/*!50001 DROP VIEW IF EXISTS `org_person_riskvariable_datum`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `org_person_riskvariable_datum` (
  `org_id` tinyint NOT NULL,
  `person_id` tinyint NOT NULL,
  `risk_variable_id` tinyint NOT NULL,
  `source_value` tinyint NOT NULL,
  `modified_at` tinyint NOT NULL,
  `created_at` tinyint NOT NULL,
  `scope` tinyint NOT NULL,
  `org_academic_year_id` tinyint NOT NULL,
  `org_academic_terms_id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `org_person_student`
--

DROP TABLE IF EXISTS `org_person_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_person_student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo_url` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `receivesurvey` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `surveycohort` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_key` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `person_id_primary_connect` int(11) DEFAULT NULL,
  `is_home_campus` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_privacy_policy_accepted` enum('y','n') COLLATE utf8_unicode_ci DEFAULT 'n',
  `privacy_policy_accepted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9C88CAE1DE12AB56` (`created_by`),
  KEY `IDX_9C88CAE125F94802` (`modified_by`),
  KEY `IDX_9C88CAE11F6FA0AF` (`deleted_by`),
  KEY `IDX_9C88CAE18661B904` (`person_id_primary_connect`),
  KEY `deleted_at_idx` (`deleted_at`),
  KEY `fk_org_person_student_organization1` (`organization_id`,`person_id`,`deleted_at`),
  KEY `fk_org_person_student_person1` (`person_id`,`organization_id`,`deleted_at`),
  CONSTRAINT `FK_9C88CAE11F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9C88CAE1217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9C88CAE125F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9C88CAE132C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_9C88CAE18661B904` FOREIGN KEY (`person_id_primary_connect`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9C88CAE1DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_person_student`
--

LOCK TABLES `org_person_student` WRITE;
/*!40000 ALTER TABLE `org_person_student` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_person_student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_person_student_cohort`
--

DROP TABLE IF EXISTS `org_person_student_cohort`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_person_student_cohort` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `org_academic_year_id` int(11) DEFAULT NULL,
  `cohort` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cohort_unique_index` (`organization_id`,`person_id`,`org_academic_year_id`),
  KEY `fk_org_person_student_cohort_organization1` (`organization_id`),
  KEY `fk_org_person_student_cohort_person1` (`person_id`),
  KEY `fk_org_person_student_cohort_org_academic_year_id1` (`org_academic_year_id`),
  KEY `org_person_student_cohort_covering_index` (`organization_id`,`org_academic_year_id`,`person_id`,`deleted_at`),
  KEY `FK_492F13D6DE12AB56` (`created_by`),
  KEY `FK_492F13D625F94802` (`modified_by`),
  KEY `FK_492F13D61F6FA0AF` (`deleted_by`),
  CONSTRAINT `FK_492F13D61F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_492F13D6217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_492F13D625F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_492F13D632C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_492F13D6DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_492F13D6F3B0CE4A` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=601 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_person_student_cohort`
--

LOCK TABLES `org_person_student_cohort` WRITE;
/*!40000 ALTER TABLE `org_person_student_cohort` DISABLE KEYS */;
INSERT INTO `org_person_student_cohort` VALUES (1,214,1026,170,1,NULL,'2016-03-29 17:51:54',NULL,'2016-03-29 19:35:22',NULL,NULL),(2,214,1027,170,1,NULL,'2016-03-29 17:51:54',NULL,'2016-03-29 19:35:24',NULL,NULL),(3,214,1029,170,1,NULL,'2016-03-29 17:51:56',NULL,'2016-03-29 19:35:26',NULL,NULL),(4,214,1028,170,1,NULL,'2016-03-29 17:51:57',NULL,'2016-03-29 19:35:25',NULL,NULL),(5,214,1030,170,1,NULL,'2016-03-29 17:51:59',NULL,'2016-03-29 19:35:29',NULL,NULL),(6,214,1031,170,1,NULL,'2016-03-29 17:52:00',NULL,'2016-03-29 19:35:29',NULL,NULL),(7,214,1032,170,1,NULL,'2016-03-29 17:52:02',NULL,'2016-03-29 19:35:31',NULL,NULL),(8,214,1033,170,1,NULL,'2016-03-29 17:52:04',NULL,'2016-03-29 19:35:33',NULL,NULL),(9,214,1034,170,1,NULL,'2016-03-29 17:52:05',NULL,'2016-03-29 19:35:35',NULL,NULL),(10,214,1035,170,1,NULL,'2016-03-29 17:52:09',NULL,'2016-03-29 19:35:38',NULL,NULL),(11,214,1036,170,1,NULL,'2016-03-29 17:52:09',NULL,'2016-03-29 19:35:40',NULL,NULL),(12,214,1038,170,1,NULL,'2016-03-29 17:52:14',NULL,'2016-03-29 19:35:45',NULL,NULL),(13,214,1037,170,1,NULL,'2016-03-29 17:52:14',NULL,'2016-03-29 19:35:43',NULL,NULL),(14,214,1039,170,1,NULL,'2016-03-29 17:52:19',NULL,'2016-03-29 19:35:50',NULL,NULL),(15,214,1040,170,1,NULL,'2016-03-29 17:52:20',NULL,'2016-03-29 19:35:50',NULL,NULL),(16,214,1041,170,1,NULL,'2016-03-29 17:52:24',NULL,'2016-03-29 19:35:56',NULL,NULL),(17,214,1042,170,1,NULL,'2016-03-29 17:52:25',NULL,'2016-03-29 19:35:56',NULL,NULL),(18,214,1043,170,1,NULL,'2016-03-29 17:52:29',NULL,'2016-03-29 19:36:02',NULL,NULL),(19,214,1044,170,1,NULL,'2016-03-29 17:52:31',NULL,'2016-03-29 19:36:02',NULL,NULL),(20,214,1045,170,1,NULL,'2016-03-29 17:52:35',NULL,'2016-03-29 19:36:08',NULL,NULL),(21,214,1046,170,1,NULL,'2016-03-29 17:52:38',NULL,'2016-03-29 19:36:11',NULL,NULL),(22,214,1047,170,1,NULL,'2016-03-29 17:52:42',NULL,'2016-03-29 19:36:16',NULL,NULL),(23,214,1048,170,1,NULL,'2016-03-29 17:52:45',NULL,'2016-03-29 19:36:18',NULL,NULL),(24,214,1049,170,1,NULL,'2016-03-29 17:52:49',NULL,'2016-03-29 19:36:24',NULL,NULL),(25,214,1050,170,1,NULL,'2016-03-29 17:52:52',NULL,'2016-03-29 19:36:27',NULL,NULL),(26,214,1051,170,1,NULL,'2016-03-29 17:52:55',NULL,'2016-03-29 19:36:31',NULL,NULL),(27,214,1052,170,1,NULL,'2016-03-29 17:53:00',NULL,'2016-03-29 19:36:36',NULL,NULL),(28,214,1053,170,1,NULL,'2016-03-29 17:53:04',NULL,'2016-03-29 19:36:40',NULL,NULL),(29,214,1054,170,1,NULL,'2016-03-29 17:53:11',NULL,'2016-03-29 19:36:45',NULL,NULL),(30,214,1055,170,1,NULL,'2016-03-29 17:53:13',NULL,'2016-03-29 19:36:48',NULL,NULL),(31,214,1056,170,1,NULL,'2016-03-29 17:53:19',NULL,'2016-03-29 19:36:54',NULL,NULL),(32,214,1057,170,1,NULL,'2016-03-29 17:53:21',NULL,'2016-03-29 19:36:58',NULL,NULL),(33,214,1059,170,1,NULL,'2016-03-29 17:53:29',NULL,'2016-03-29 19:37:08',NULL,NULL),(34,214,1058,170,1,NULL,'2016-03-29 17:53:29',NULL,'2016-03-29 19:37:06',NULL,NULL),(35,214,1060,170,1,NULL,'2016-03-29 17:53:38',NULL,'2016-03-29 19:37:17',NULL,NULL),(36,214,1061,170,1,NULL,'2016-03-29 17:53:39',NULL,'2016-03-29 19:37:17',NULL,NULL),(37,214,1062,170,1,NULL,'2016-03-29 17:53:46',NULL,'2016-03-29 19:37:27',NULL,NULL),(38,214,1063,170,1,NULL,'2016-03-29 17:53:50',NULL,'2016-03-29 19:37:30',NULL,NULL),(39,214,1064,170,1,NULL,'2016-03-29 17:53:57',NULL,'2016-03-29 19:37:39',NULL,NULL),(40,214,1065,170,1,NULL,'2016-03-29 17:54:00',NULL,'2016-03-29 19:37:41',NULL,NULL),(41,214,1066,170,1,NULL,'2016-03-29 17:54:07',NULL,'2016-03-29 19:37:51',NULL,NULL),(42,214,1067,170,1,NULL,'2016-03-29 17:54:13',NULL,'2016-03-29 19:37:55',NULL,NULL),(43,214,1068,170,1,NULL,'2016-03-29 17:54:18',NULL,'2016-03-29 19:38:04',NULL,NULL),(44,214,1069,170,1,NULL,'2016-03-29 17:54:24',NULL,'2016-03-29 19:38:07',NULL,NULL),(45,214,1070,170,1,NULL,'2016-03-29 17:54:29',NULL,'2016-03-29 19:38:17',NULL,NULL),(46,214,1071,170,1,NULL,'2016-03-29 17:54:37',NULL,'2016-03-29 19:38:21',NULL,NULL),(47,214,1072,170,1,NULL,'2016-03-29 17:54:40',NULL,'2016-03-29 19:38:30',NULL,NULL),(48,214,1073,170,1,NULL,'2016-03-29 17:54:51',NULL,'2016-03-29 19:38:37',NULL,NULL),(49,214,1074,170,1,NULL,'2016-03-29 17:54:51',NULL,'2016-03-29 19:38:43',NULL,NULL),(50,214,1076,170,1,NULL,'2016-03-29 17:55:05',NULL,'2016-03-29 19:38:58',NULL,NULL),(51,214,1075,170,1,NULL,'2016-03-29 17:55:06',NULL,'2016-03-29 19:38:53',NULL,NULL),(52,214,1077,170,1,NULL,'2016-03-29 17:55:19',NULL,'2016-03-29 19:39:14',NULL,NULL),(53,214,1078,170,1,NULL,'2016-03-29 17:55:19',NULL,'2016-03-29 19:39:09',NULL,NULL),(54,214,1079,170,1,NULL,'2016-03-29 17:55:32',NULL,'2016-03-29 19:39:29',NULL,NULL),(55,214,1080,170,1,NULL,'2016-03-29 17:55:35',NULL,'2016-03-29 19:39:25',NULL,NULL),(56,214,1081,170,1,NULL,'2016-03-29 17:55:45',NULL,'2016-03-29 19:39:43',NULL,NULL),(57,214,1082,170,1,NULL,'2016-03-29 17:55:48',NULL,'2016-03-29 19:39:40',NULL,NULL),(58,214,1083,170,1,NULL,'2016-03-29 17:55:50',NULL,'2016-03-29 19:39:46',NULL,NULL),(59,214,1085,170,1,NULL,'2016-03-29 17:55:52',NULL,'2016-03-29 19:39:48',NULL,NULL),(60,214,1086,170,1,NULL,'2016-03-29 17:55:54',NULL,'2016-03-29 19:39:51',NULL,NULL),(61,214,1087,170,1,NULL,'2016-03-29 17:55:58',NULL,'2016-03-29 19:39:54',NULL,NULL),(62,214,1088,170,1,NULL,'2016-03-29 17:56:01',NULL,'2016-03-29 19:40:00',NULL,NULL),(63,214,1084,170,1,NULL,'2016-03-29 17:56:01',NULL,'2016-03-29 19:39:59',NULL,NULL),(64,214,1089,170,1,NULL,'2016-03-29 17:56:05',NULL,'2016-03-29 19:40:05',NULL,NULL),(65,214,1091,170,1,NULL,'2016-03-29 17:56:09',NULL,'2016-03-29 19:40:09',NULL,NULL),(66,214,1092,170,1,NULL,'2016-03-29 17:56:14',NULL,'2016-03-29 19:40:14',NULL,NULL),(67,214,1090,170,1,NULL,'2016-03-29 17:56:14',NULL,'2016-03-29 19:40:15',NULL,NULL),(68,214,1093,170,1,NULL,'2016-03-29 17:56:19',NULL,'2016-03-29 19:40:20',NULL,NULL),(69,214,1095,170,1,NULL,'2016-03-29 17:56:25',NULL,'2016-03-29 19:40:26',NULL,NULL),(70,214,1094,170,1,NULL,'2016-03-29 17:56:29',NULL,'2016-03-29 19:40:32',NULL,NULL),(71,214,1096,170,1,NULL,'2016-03-29 17:56:30',NULL,'2016-03-29 19:40:33',NULL,NULL),(72,214,1098,170,1,NULL,'2016-03-29 17:56:34',NULL,'2016-03-29 19:40:36',NULL,NULL),(73,214,1099,170,1,NULL,'2016-03-29 17:56:36',NULL,'2016-03-29 19:40:39',NULL,NULL),(74,214,1097,170,1,NULL,'2016-03-29 17:56:36',NULL,'2016-03-29 19:40:40',NULL,NULL),(75,214,1100,170,1,NULL,'2016-03-29 17:56:39',NULL,'2016-03-29 19:40:42',NULL,NULL),(76,214,1102,170,1,NULL,'2016-03-29 17:56:42',NULL,'2016-03-29 19:40:45',NULL,NULL),(77,214,1101,170,1,NULL,'2016-03-29 17:56:42',NULL,'2016-03-29 19:40:47',NULL,NULL),(78,214,1103,170,1,NULL,'2016-03-29 17:56:45',NULL,'2016-03-29 19:40:48',NULL,NULL),(79,214,1105,170,1,NULL,'2016-03-29 17:56:48',NULL,'2016-03-29 19:40:52',NULL,NULL),(80,214,1104,170,1,NULL,'2016-03-29 17:56:49',NULL,'2016-03-29 19:40:55',NULL,NULL),(81,214,1106,170,1,NULL,'2016-03-29 17:56:53',NULL,'2016-03-29 19:40:57',NULL,NULL),(82,214,1107,170,1,NULL,'2016-03-29 17:56:56',NULL,'2016-03-29 19:41:04',NULL,NULL),(83,214,1108,170,1,NULL,'2016-03-29 17:56:57',NULL,'2016-03-29 19:41:02',NULL,NULL),(84,214,1110,170,1,NULL,'2016-03-29 17:57:02',NULL,'2016-03-29 19:41:07',NULL,NULL),(85,214,1109,170,1,NULL,'2016-03-29 17:57:04',NULL,'2016-03-29 19:41:13',NULL,NULL),(86,214,1111,170,1,NULL,'2016-03-29 17:57:08',NULL,'2016-03-29 19:41:14',NULL,NULL),(87,214,1112,170,1,NULL,'2016-03-29 17:57:13',NULL,'2016-03-29 19:41:23',NULL,NULL),(88,214,1113,170,1,NULL,'2016-03-29 17:57:14',NULL,'2016-03-29 19:41:21',NULL,NULL),(89,214,1115,170,1,NULL,'2016-03-29 17:57:21',NULL,'2016-03-29 19:41:28',NULL,NULL),(90,214,1114,170,1,NULL,'2016-03-29 17:57:22',NULL,'2016-03-29 19:41:34',NULL,NULL),(91,214,1116,170,1,NULL,'2016-03-29 17:57:29',NULL,'2016-03-29 19:41:37',NULL,NULL),(92,214,1117,170,1,NULL,'2016-03-29 17:57:30',NULL,'2016-03-29 19:41:44',NULL,NULL),(93,214,1118,170,1,NULL,'2016-03-29 17:57:36',NULL,'2016-03-29 19:41:45',NULL,NULL),(94,214,1119,170,1,NULL,'2016-03-29 17:57:40',NULL,'2016-03-29 19:41:56',NULL,NULL),(95,214,1120,170,1,NULL,'2016-03-29 17:57:43',NULL,'2016-03-29 19:41:54',NULL,NULL),(96,214,1121,170,1,NULL,'2016-03-29 17:57:49',NULL,'2016-03-29 19:42:06',NULL,NULL),(97,214,1122,170,1,NULL,'2016-03-29 17:57:51',NULL,'2016-03-29 19:42:04',NULL,NULL),(98,214,1123,170,1,NULL,'2016-03-29 17:57:59',NULL,'2016-03-29 19:42:17',NULL,NULL),(99,214,1124,170,1,NULL,'2016-03-29 17:58:00',NULL,'2016-03-29 19:42:14',NULL,NULL),(100,214,1126,170,1,NULL,'2016-03-29 17:58:10',NULL,'2016-03-29 19:42:25',NULL,NULL),(101,214,1125,170,1,NULL,'2016-03-29 17:58:12',NULL,'2016-03-29 19:42:30',NULL,NULL),(102,214,1127,170,1,NULL,'2016-03-29 17:58:19',NULL,'2016-03-29 19:42:34',NULL,NULL),(103,214,1128,170,1,NULL,'2016-03-29 17:58:22',NULL,'2016-03-29 19:42:43',NULL,NULL),(104,214,1129,170,1,NULL,'2016-03-29 17:58:28',NULL,'2016-03-29 19:42:44',NULL,NULL),(105,214,1130,170,1,NULL,'2016-03-29 17:58:35',NULL,'2016-03-29 19:42:58',NULL,NULL),(106,214,1131,170,1,NULL,'2016-03-29 17:58:37',NULL,'2016-03-29 19:42:55',NULL,NULL),(107,214,1132,170,1,NULL,'2016-03-29 17:58:46',NULL,'2016-03-29 19:43:11',NULL,NULL),(108,214,1133,170,1,NULL,'2016-03-29 17:58:48',NULL,'2016-03-29 19:43:07',NULL,NULL),(109,214,1134,170,1,NULL,'2016-03-29 17:58:57',NULL,'2016-03-29 19:43:25',NULL,NULL),(110,214,1135,170,1,NULL,'2016-03-29 17:58:58',NULL,'2016-03-29 19:43:20',NULL,NULL),(111,214,1136,170,1,NULL,'2016-03-29 17:59:10',NULL,'2016-03-29 19:43:39',NULL,NULL),(112,214,1137,170,1,NULL,'2016-03-29 17:59:10',NULL,'2016-03-29 19:43:33',NULL,NULL),(113,214,1138,170,1,NULL,'2016-03-29 17:59:20',NULL,'2016-03-29 19:43:52',NULL,NULL),(114,214,1139,170,1,NULL,'2016-03-29 17:59:23',NULL,'2016-03-29 19:43:48',NULL,NULL),(115,214,1141,170,1,NULL,'2016-03-29 17:59:34',NULL,'2016-03-29 19:44:02',NULL,NULL),(116,214,1140,170,1,NULL,'2016-03-29 17:59:35',NULL,'2016-03-29 19:44:09',NULL,NULL),(117,214,1143,170,1,NULL,'2016-03-29 17:59:39',NULL,'2016-03-29 19:44:12',NULL,NULL),(118,214,1144,170,1,NULL,'2016-03-29 17:59:42',NULL,'2016-03-29 19:44:15',NULL,NULL),(119,214,1145,170,1,NULL,'2016-03-29 17:59:45',NULL,'2016-03-29 19:44:18',NULL,NULL),(120,214,1142,170,1,NULL,'2016-03-29 17:59:45',NULL,'2016-03-29 19:44:16',NULL,NULL),(121,214,1146,170,1,NULL,'2016-03-29 17:59:49',NULL,'2016-03-29 19:44:22',NULL,NULL),(122,214,1148,170,1,NULL,'2016-03-29 17:59:52',NULL,'2016-03-29 19:44:26',NULL,NULL),(123,214,1147,170,1,NULL,'2016-03-29 17:59:56',NULL,'2016-03-29 19:44:29',NULL,NULL),(124,214,1149,170,1,NULL,'2016-03-29 17:59:56',NULL,'2016-03-29 19:44:31',NULL,NULL),(125,214,1150,170,1,NULL,'2016-03-29 18:00:01',NULL,'2016-03-29 19:44:36',NULL,NULL),(126,214,1152,170,1,NULL,'2016-03-29 18:00:07',NULL,'2016-03-29 19:44:42',NULL,NULL),(127,214,1151,170,1,NULL,'2016-03-29 18:00:08',NULL,'2016-03-29 19:44:43',NULL,NULL),(128,214,1153,170,1,NULL,'2016-03-29 18:00:13',NULL,'2016-03-29 19:44:48',NULL,NULL),(129,214,1155,170,1,NULL,'2016-03-29 18:00:19',NULL,'2016-03-29 19:44:54',NULL,NULL),(130,214,1154,170,1,NULL,'2016-03-29 18:00:21',NULL,'2016-03-29 19:44:59',NULL,NULL),(131,214,1156,170,1,NULL,'2016-03-29 18:00:24',NULL,'2016-03-29 19:45:01',NULL,NULL),(132,214,1157,170,1,NULL,'2016-03-29 18:00:26',NULL,'2016-03-29 19:45:05',NULL,NULL),(133,214,1159,170,1,NULL,'2016-03-29 18:00:28',NULL,'2016-03-29 19:45:07',NULL,NULL),(134,214,1158,170,1,NULL,'2016-03-29 18:00:30',NULL,'2016-03-29 19:45:09',NULL,NULL),(135,214,1160,170,1,NULL,'2016-03-29 18:00:31',NULL,'2016-03-29 19:45:09',NULL,NULL),(136,214,1162,170,1,NULL,'2016-03-29 18:00:34',NULL,'2016-03-29 19:45:13',NULL,NULL),(137,214,1161,170,1,NULL,'2016-03-29 18:00:36',NULL,'2016-03-29 19:45:16',NULL,NULL),(138,214,1163,170,1,NULL,'2016-03-29 18:00:37',NULL,'2016-03-29 19:45:16',NULL,NULL),(139,214,1164,170,1,NULL,'2016-03-29 18:00:41',NULL,'2016-03-29 19:45:21',NULL,NULL),(140,214,1165,170,1,NULL,'2016-03-29 18:00:44',NULL,'2016-03-29 19:45:24',NULL,NULL),(141,214,1166,170,1,NULL,'2016-03-29 18:00:46',NULL,'2016-03-29 19:45:25',NULL,NULL),(142,214,1168,170,1,NULL,'2016-03-29 18:00:50',NULL,'2016-03-29 19:45:31',NULL,NULL),(143,214,1167,170,1,NULL,'2016-03-29 18:00:51',NULL,'2016-03-29 19:45:33',NULL,NULL),(144,214,1169,170,1,NULL,'2016-03-29 18:00:55',NULL,'2016-03-29 19:45:37',NULL,NULL),(145,214,1170,170,1,NULL,'2016-03-29 18:00:58',NULL,'2016-03-29 19:45:41',NULL,NULL),(146,214,1171,170,1,NULL,'2016-03-29 18:01:01',NULL,'2016-03-29 19:45:43',NULL,NULL),(147,214,1172,170,1,NULL,'2016-03-29 18:01:06',NULL,'2016-03-29 19:45:50',NULL,NULL),(148,214,1173,170,1,NULL,'2016-03-29 18:01:07',NULL,'2016-03-29 19:45:49',NULL,NULL),(149,214,1175,170,1,NULL,'2016-03-29 18:01:13',NULL,'2016-03-29 19:45:56',NULL,NULL),(150,214,1174,170,1,NULL,'2016-03-29 18:01:15',NULL,'2016-03-29 19:46:00',NULL,NULL),(151,214,1176,170,1,NULL,'2016-03-29 18:01:20',NULL,'2016-03-29 19:46:04',NULL,NULL),(152,214,1177,170,1,NULL,'2016-03-29 18:01:24',NULL,'2016-03-29 19:46:10',NULL,NULL),(153,214,1178,170,1,NULL,'2016-03-29 18:01:26',NULL,'2016-03-29 19:46:11',NULL,NULL),(154,214,1180,170,1,NULL,'2016-03-29 18:01:33',NULL,'2016-03-29 19:46:19',NULL,NULL),(155,214,1179,170,1,NULL,'2016-03-29 18:01:33',NULL,'2016-03-29 19:46:20',NULL,NULL),(156,214,1181,170,1,NULL,'2016-03-29 18:01:41',NULL,'2016-03-29 19:46:27',NULL,NULL),(157,214,1182,170,1,NULL,'2016-03-29 18:01:45',NULL,'2016-03-29 19:46:33',NULL,NULL),(158,214,1183,170,1,NULL,'2016-03-29 18:01:50',NULL,'2016-03-29 19:46:37',NULL,NULL),(159,214,1184,170,1,NULL,'2016-03-29 18:01:55',NULL,'2016-03-29 19:46:45',NULL,NULL),(160,214,1185,170,1,NULL,'2016-03-29 18:01:59',NULL,'2016-03-29 19:46:46',NULL,NULL),(161,214,1186,170,1,NULL,'2016-03-29 18:02:06',NULL,'2016-03-29 19:46:57',NULL,NULL),(162,214,1187,170,1,NULL,'2016-03-29 18:02:07',NULL,'2016-03-29 19:46:55',NULL,NULL),(163,214,1189,170,1,NULL,'2016-03-29 18:02:16',NULL,'2016-03-29 19:47:06',NULL,NULL),(164,214,1188,170,1,NULL,'2016-03-29 18:02:17',NULL,'2016-03-29 19:47:09',NULL,NULL),(165,214,1190,170,1,NULL,'2016-03-29 18:02:26',NULL,'2016-03-29 19:47:17',NULL,NULL),(166,214,1191,170,1,NULL,'2016-03-29 18:02:27',NULL,'2016-03-29 19:47:23',NULL,NULL),(167,214,1192,170,1,NULL,'2016-03-29 18:02:36',NULL,'2016-03-29 19:47:28',NULL,NULL),(168,214,1193,170,1,NULL,'2016-03-29 18:02:39',NULL,'2016-03-29 19:47:36',NULL,NULL),(169,214,1194,170,1,NULL,'2016-03-29 18:02:45',NULL,'2016-03-29 19:47:39',NULL,NULL),(170,214,1195,170,1,NULL,'2016-03-29 18:02:51',NULL,'2016-03-29 19:47:50',NULL,NULL),(171,214,1196,170,1,NULL,'2016-03-29 18:02:56',NULL,'2016-03-29 19:47:50',NULL,NULL),(172,214,1197,170,1,NULL,'2016-03-29 18:03:04',NULL,'2016-03-29 19:48:05',NULL,NULL),(173,214,1198,170,1,NULL,'2016-03-29 18:03:05',NULL,'2016-03-29 19:48:02',NULL,NULL),(174,214,1199,170,1,NULL,'2016-03-29 18:03:19',NULL,'2016-03-29 19:48:19',NULL,NULL),(175,214,1200,170,1,NULL,'2016-03-29 18:03:19',NULL,'2016-03-29 19:48:16',NULL,NULL),(176,214,1202,170,1,NULL,'2016-03-29 18:03:31',NULL,'2016-03-29 19:48:29',NULL,NULL),(177,214,1201,170,1,NULL,'2016-03-29 18:03:32',NULL,'2016-03-29 19:48:35',NULL,NULL),(178,214,1204,170,1,NULL,'2016-03-29 18:03:37',NULL,'2016-03-29 19:48:39',NULL,NULL),(179,214,1205,170,1,NULL,'2016-03-29 18:03:39',NULL,'2016-03-29 19:48:41',NULL,NULL),(180,214,1203,170,1,NULL,'2016-03-29 18:03:42',NULL,'2016-03-29 19:48:41',NULL,NULL),(181,214,1206,170,1,NULL,'2016-03-29 18:03:42',NULL,'2016-03-29 19:48:44',NULL,NULL),(182,214,1207,170,1,NULL,'2016-03-29 18:03:45',NULL,'2016-03-29 19:48:47',NULL,NULL),(183,214,1209,170,1,NULL,'2016-03-29 18:03:49',NULL,'2016-03-29 19:48:51',NULL,NULL),(184,214,1208,170,1,NULL,'2016-03-29 18:03:53',NULL,'2016-03-29 19:48:55',NULL,NULL),(185,214,1210,170,1,NULL,'2016-03-29 18:03:53',NULL,'2016-03-29 19:48:56',NULL,NULL),(186,214,1211,170,1,NULL,'2016-03-29 18:03:58',NULL,'2016-03-29 19:49:01',NULL,NULL),(187,214,1213,170,1,NULL,'2016-03-29 18:04:03',NULL,'2016-03-29 19:49:06',NULL,NULL),(188,214,1212,170,1,NULL,'2016-03-29 18:04:04',NULL,'2016-03-29 19:49:09',NULL,NULL),(189,214,1214,170,1,NULL,'2016-03-29 18:04:07',NULL,'2016-03-29 19:49:11',NULL,NULL),(190,214,1215,170,1,NULL,'2016-03-29 18:04:10',NULL,'2016-03-29 19:49:14',NULL,NULL),(191,214,1216,170,1,NULL,'2016-03-29 18:04:13',NULL,'2016-03-29 19:49:17',NULL,NULL),(192,214,1217,170,1,NULL,'2016-03-29 18:04:16',NULL,'2016-03-29 19:49:20',NULL,NULL),(193,214,1218,170,1,NULL,'2016-03-29 18:04:19',NULL,'2016-03-29 19:49:24',NULL,NULL),(194,214,1219,170,1,NULL,'2016-03-29 18:04:23',NULL,'2016-03-29 19:49:28',NULL,NULL),(195,214,1220,170,1,NULL,'2016-03-29 18:04:27',NULL,'2016-03-29 19:49:32',NULL,NULL),(196,214,1221,170,1,NULL,'2016-03-29 18:04:30',NULL,'2016-03-29 19:49:37',NULL,NULL),(197,214,1222,170,1,NULL,'2016-03-29 18:04:35',NULL,'2016-03-29 19:49:42',NULL,NULL),(198,214,1223,170,1,NULL,'2016-03-29 18:04:40',NULL,'2016-03-29 19:49:47',NULL,NULL),(199,214,1224,170,1,NULL,'2016-03-29 18:04:44',NULL,'2016-03-29 19:49:52',NULL,NULL),(200,214,1225,170,1,NULL,'2016-03-29 18:04:48',NULL,'2016-03-29 19:49:57',NULL,NULL),(201,214,1027,171,2,NULL,'2016-03-29 18:45:29',NULL,'2016-03-31 17:54:15',NULL,NULL),(202,214,1026,171,1,NULL,'2016-03-29 18:45:29',NULL,'2016-03-29 18:45:29',NULL,NULL),(203,214,1029,171,2,NULL,'2016-03-29 18:45:31',NULL,'2016-03-31 17:54:15',NULL,NULL),(204,214,1028,171,2,NULL,'2016-03-29 18:45:32',NULL,'2016-03-31 17:54:11',NULL,NULL),(205,214,1030,171,2,NULL,'2016-03-29 18:45:33',NULL,'2016-03-31 17:54:15',NULL,NULL),(206,214,1031,171,2,NULL,'2016-03-29 18:45:36',NULL,'2016-03-31 17:54:12',NULL,NULL),(207,214,1032,171,2,NULL,'2016-03-29 18:45:37',NULL,'2016-03-31 17:54:16',NULL,NULL),(208,214,1033,171,2,NULL,'2016-03-29 18:45:40',NULL,'2016-03-31 17:54:12',NULL,NULL),(209,214,1034,171,2,NULL,'2016-03-29 18:45:41',NULL,'2016-03-31 17:54:16',NULL,NULL),(210,214,1035,171,2,NULL,'2016-03-29 18:45:44',NULL,'2016-03-31 17:54:12',NULL,NULL),(211,214,1036,171,2,NULL,'2016-03-29 18:45:45',NULL,'2016-03-31 17:54:16',NULL,NULL),(212,214,1037,171,2,NULL,'2016-03-29 18:45:49',NULL,'2016-03-31 17:54:12',NULL,NULL),(213,214,1038,171,2,NULL,'2016-03-29 18:45:51',NULL,'2016-03-31 17:54:17',NULL,NULL),(214,214,1040,171,2,NULL,'2016-03-29 18:45:55',NULL,'2016-03-31 17:54:12',NULL,NULL),(215,214,1039,171,2,NULL,'2016-03-29 18:45:56',NULL,'2016-03-31 17:54:17',NULL,NULL),(216,214,1042,171,2,NULL,'2016-03-29 18:46:02',NULL,'2016-03-31 17:54:13',NULL,NULL),(217,214,1041,171,2,NULL,'2016-03-29 18:46:02',NULL,'2016-03-31 17:54:17',NULL,NULL),(218,214,1044,171,2,NULL,'2016-03-29 18:46:08',NULL,'2016-03-31 17:54:13',NULL,NULL),(219,214,1043,171,2,NULL,'2016-03-29 18:46:09',NULL,'2016-03-31 17:54:18',NULL,NULL),(220,214,1045,171,2,NULL,'2016-03-29 18:46:15',NULL,'2016-03-31 17:54:18',NULL,NULL),(221,214,1046,171,2,NULL,'2016-03-29 18:46:16',NULL,'2016-03-31 17:54:13',NULL,NULL),(222,214,1047,171,2,NULL,'2016-03-29 18:46:24',NULL,'2016-03-31 17:54:18',NULL,NULL),(223,214,1048,171,2,NULL,'2016-03-29 18:46:24',NULL,'2016-03-31 17:54:13',NULL,NULL),(224,214,1049,171,2,NULL,'2016-03-29 18:46:32',NULL,'2016-03-31 17:54:19',NULL,NULL),(225,214,1050,171,2,NULL,'2016-03-29 18:46:33',NULL,'2016-03-31 17:54:14',NULL,NULL),(226,214,1051,171,2,NULL,'2016-03-29 18:46:40',NULL,'2016-03-31 17:54:19',NULL,NULL),(227,214,1052,171,2,NULL,'2016-03-29 18:46:41',NULL,'2016-03-31 17:54:14',NULL,NULL),(228,214,1053,171,2,NULL,'2016-03-29 18:46:48',NULL,'2016-03-31 17:54:19',NULL,NULL),(229,214,1054,171,2,NULL,'2016-03-29 18:46:51',NULL,'2016-03-31 17:54:14',NULL,NULL),(230,214,1055,171,2,NULL,'2016-03-29 18:46:57',NULL,'2016-03-31 17:54:20',NULL,NULL),(231,214,1056,171,2,NULL,'2016-03-29 18:47:01',NULL,'2016-03-31 17:54:15',NULL,NULL),(232,214,1057,171,2,NULL,'2016-03-29 18:47:08',NULL,'2016-03-31 17:54:20',NULL,NULL),(233,214,1058,171,2,NULL,'2016-03-29 18:47:13',NULL,'2016-03-31 17:54:15',NULL,NULL),(234,214,1059,171,2,NULL,'2016-03-29 18:47:17',NULL,'2016-03-31 17:54:21',NULL,NULL),(235,214,1061,171,2,NULL,'2016-03-29 18:47:24',NULL,'2016-03-31 17:54:15',NULL,NULL),(236,214,1060,171,2,NULL,'2016-03-29 18:47:27',NULL,'2016-03-31 17:54:21',NULL,NULL),(237,214,1063,171,2,NULL,'2016-03-29 18:47:37',NULL,'2016-03-31 17:54:16',NULL,NULL),(238,214,1062,171,2,NULL,'2016-03-29 18:47:37',NULL,'2016-03-31 17:54:18',NULL,NULL),(239,214,1065,171,2,NULL,'2016-03-29 18:47:48',NULL,'2016-03-31 17:54:16',NULL,NULL),(240,214,1064,171,2,NULL,'2016-03-29 18:47:49',NULL,'2016-03-31 17:54:18',NULL,NULL),(241,214,1066,171,2,NULL,'2016-03-29 18:48:01',NULL,'2016-03-31 17:54:18',NULL,NULL),(242,214,1067,171,2,NULL,'2016-03-29 18:48:02',NULL,'2016-03-31 17:54:13',NULL,NULL),(243,214,1068,171,2,NULL,'2016-03-29 18:48:14',NULL,'2016-03-31 17:54:18',NULL,NULL),(244,214,1069,171,2,NULL,'2016-03-29 18:48:15',NULL,'2016-03-31 17:54:13',NULL,NULL),(245,214,1070,171,2,NULL,'2016-03-29 18:48:28',NULL,'2016-03-31 17:54:18',NULL,NULL),(246,214,1071,171,2,NULL,'2016-03-29 18:48:29',NULL,'2016-03-31 17:54:13',NULL,NULL),(247,214,1072,171,2,NULL,'2016-03-29 18:48:41',NULL,'2016-03-31 17:54:18',NULL,NULL),(248,214,1073,171,2,NULL,'2016-03-29 18:48:44',NULL,'2016-03-31 17:54:13',NULL,NULL),(249,214,1074,171,2,NULL,'2016-03-29 18:48:54',NULL,'2016-03-31 17:54:19',NULL,NULL),(250,214,1075,171,2,NULL,'2016-03-29 18:49:00',NULL,'2016-03-31 17:54:14',NULL,NULL),(251,214,1076,171,2,NULL,'2016-03-29 18:49:11',NULL,'2016-03-31 17:54:19',NULL,NULL),(252,214,1078,171,2,NULL,'2016-03-29 18:49:16',NULL,'2016-03-31 17:54:14',NULL,NULL),(253,214,1077,171,2,NULL,'2016-03-29 18:49:27',NULL,'2016-03-31 17:54:19',NULL,NULL),(254,214,1080,171,2,NULL,'2016-03-29 18:49:33',NULL,'2016-03-31 17:54:14',NULL,NULL),(255,214,1079,171,2,NULL,'2016-03-29 18:49:42',NULL,'2016-03-31 17:54:19',NULL,NULL),(256,214,1082,171,2,NULL,'2016-03-29 18:49:49',NULL,'2016-03-31 17:54:14',NULL,NULL),(257,214,1081,171,2,NULL,'2016-03-29 18:49:56',NULL,'2016-03-31 17:54:19',NULL,NULL),(258,214,1083,171,2,NULL,'2016-03-29 18:50:01',NULL,'2016-03-31 17:54:19',NULL,NULL),(259,214,1085,171,2,NULL,'2016-03-29 18:50:03',NULL,'2016-03-31 17:54:20',NULL,NULL),(260,214,1084,171,2,NULL,'2016-03-29 18:50:05',NULL,'2016-03-31 17:54:14',NULL,NULL),(261,214,1086,171,2,NULL,'2016-03-29 18:50:06',NULL,'2016-03-31 17:54:20',NULL,NULL),(262,214,1087,171,2,NULL,'2016-03-29 18:50:10',NULL,'2016-03-31 17:54:20',NULL,NULL),(263,214,1088,171,2,NULL,'2016-03-29 18:50:13',NULL,'2016-03-31 17:54:20',NULL,NULL),(264,214,1089,171,2,NULL,'2016-03-29 18:50:17',NULL,'2016-03-31 17:54:21',NULL,NULL),(265,214,1090,171,2,NULL,'2016-03-29 18:50:21',NULL,'2016-03-31 17:54:15',NULL,NULL),(266,214,1091,171,2,NULL,'2016-03-29 18:50:22',NULL,'2016-03-31 17:54:21',NULL,NULL),(267,214,1092,171,2,NULL,'2016-03-29 18:50:28',NULL,'2016-03-31 17:54:21',NULL,NULL),(268,214,1093,171,2,NULL,'2016-03-29 18:50:33',NULL,'2016-03-31 17:54:21',NULL,NULL),(269,214,1095,171,2,NULL,'2016-03-29 18:50:39',NULL,'2016-03-31 17:54:22',NULL,NULL),(270,214,1094,171,2,NULL,'2016-03-29 18:50:39',NULL,'2016-03-31 17:54:15',NULL,NULL),(271,214,1098,171,2,NULL,'2016-03-29 18:50:43',NULL,'2016-03-31 17:54:24',NULL,NULL),(272,214,1096,171,2,NULL,'2016-03-29 18:50:46',NULL,'2016-03-31 17:54:22',NULL,NULL),(273,214,1099,171,2,NULL,'2016-03-29 18:50:46',NULL,'2016-03-31 17:54:24',NULL,NULL),(274,214,1100,171,2,NULL,'2016-03-29 18:50:48',NULL,'2016-03-31 17:54:25',NULL,NULL),(275,214,1102,171,1,NULL,'2016-03-29 18:50:51',NULL,'2016-03-29 18:50:51',NULL,NULL),(276,214,1097,171,2,NULL,'2016-03-29 18:50:53',NULL,'2016-03-31 17:54:22',NULL,NULL),(277,214,1103,171,2,NULL,'2016-03-29 18:50:55',NULL,'2016-03-31 17:54:25',NULL,NULL),(278,214,1105,171,2,NULL,'2016-03-29 18:50:59',NULL,'2016-03-31 17:54:25',NULL,NULL),(279,214,1101,171,1,NULL,'2016-03-29 18:51:00',NULL,'2016-03-29 18:51:00',NULL,NULL),(280,214,1106,171,2,NULL,'2016-03-29 18:51:04',NULL,'2016-03-31 17:54:25',NULL,NULL),(281,214,1108,171,2,NULL,'2016-03-29 18:51:09',NULL,'2016-03-31 17:54:26',NULL,NULL),(282,214,1104,171,2,NULL,'2016-03-29 18:51:09',NULL,'2016-03-31 17:54:23',NULL,NULL),(283,214,1110,171,2,NULL,'2016-03-29 18:51:14',NULL,'2016-03-31 17:54:26',NULL,NULL),(284,214,1107,171,2,NULL,'2016-03-29 18:51:17',NULL,'2016-03-31 17:54:23',NULL,NULL),(285,214,1111,171,2,NULL,'2016-03-29 18:51:21',NULL,'2016-03-31 17:54:26',NULL,NULL),(286,214,1109,171,2,NULL,'2016-03-29 18:51:27',NULL,'2016-03-31 17:54:24',NULL,NULL),(287,214,1113,171,2,NULL,'2016-03-29 18:51:28',NULL,'2016-03-31 17:54:27',NULL,NULL),(288,214,1115,171,2,NULL,'2016-03-29 18:51:36',NULL,'2016-03-31 17:54:27',NULL,NULL),(289,214,1112,171,2,NULL,'2016-03-29 18:51:37',NULL,'2016-03-31 17:54:24',NULL,NULL),(290,214,1116,171,2,NULL,'2016-03-29 18:51:44',NULL,'2016-03-31 17:54:27',NULL,NULL),(291,214,1114,171,2,NULL,'2016-03-29 18:51:48',NULL,'2016-03-31 17:54:25',NULL,NULL),(292,214,1118,171,2,NULL,'2016-03-29 18:51:53',NULL,'2016-03-31 17:54:27',NULL,NULL),(293,214,1117,171,1,NULL,'2016-03-29 18:51:58',NULL,'2016-03-29 18:51:58',NULL,NULL),(294,214,1120,171,2,NULL,'2016-03-29 18:52:02',NULL,'2016-03-31 17:54:28',NULL,NULL),(295,214,1119,171,2,NULL,'2016-03-29 18:52:10',NULL,'2016-03-31 17:54:25',NULL,NULL),(296,214,1122,171,2,NULL,'2016-03-29 18:52:12',NULL,'2016-03-31 17:54:28',NULL,NULL),(297,214,1121,171,2,NULL,'2016-03-29 18:52:21',NULL,'2016-03-31 17:54:25',NULL,NULL),(298,214,1124,171,2,NULL,'2016-03-29 18:52:22',NULL,'2016-03-31 17:54:28',NULL,NULL),(299,214,1126,171,2,NULL,'2016-03-29 18:52:32',NULL,'2016-03-31 17:54:29',NULL,NULL),(300,214,1123,171,2,NULL,'2016-03-29 18:52:33',NULL,'2016-03-31 17:54:22',NULL,NULL),(301,214,1127,171,2,NULL,'2016-03-29 18:52:42',NULL,'2016-03-31 17:54:29',NULL,NULL),(302,214,1125,171,2,NULL,'2016-03-29 18:52:47',NULL,'2016-03-31 17:54:23',NULL,NULL),(303,214,1129,171,2,NULL,'2016-03-29 18:52:53',NULL,'2016-03-31 17:54:30',NULL,NULL),(304,214,1128,171,2,NULL,'2016-03-29 18:53:00',NULL,'2016-03-31 17:54:23',NULL,NULL),(305,214,1131,171,2,NULL,'2016-03-29 18:53:04',NULL,'2016-03-31 17:54:30',NULL,NULL),(306,214,1130,171,2,NULL,'2016-03-29 18:53:15',NULL,'2016-03-31 17:54:23',NULL,NULL),(307,214,1133,171,2,NULL,'2016-03-29 18:53:16',NULL,'2016-03-31 17:54:31',NULL,NULL),(308,214,1135,171,2,NULL,'2016-03-29 18:53:28',NULL,'2016-03-31 17:54:26',NULL,NULL),(309,214,1132,171,2,NULL,'2016-03-29 18:53:29',NULL,'2016-03-31 17:54:23',NULL,NULL),(310,214,1137,171,2,NULL,'2016-03-29 18:53:42',NULL,'2016-03-31 17:54:27',NULL,NULL),(311,214,1134,171,2,NULL,'2016-03-29 18:53:43',NULL,'2016-03-31 17:54:23',NULL,NULL),(312,214,1139,171,2,NULL,'2016-03-29 18:53:57',NULL,'2016-03-31 17:54:27',NULL,NULL),(313,214,1136,171,2,NULL,'2016-03-29 18:53:57',NULL,'2016-03-31 17:54:23',NULL,NULL),(314,214,1141,171,2,NULL,'2016-03-29 18:54:12',NULL,'2016-03-31 17:54:27',NULL,NULL),(315,214,1138,171,2,NULL,'2016-03-29 18:54:12',NULL,'2016-03-31 17:54:24',NULL,NULL),(316,214,1142,171,2,NULL,'2016-03-29 18:54:27',NULL,'2016-03-31 17:54:27',NULL,NULL),(317,214,1140,171,2,NULL,'2016-03-29 18:54:30',NULL,'2016-03-31 17:54:24',NULL,NULL),(318,214,1143,171,2,NULL,'2016-03-29 18:54:34',NULL,'2016-03-31 17:54:28',NULL,NULL),(319,214,1144,171,2,NULL,'2016-03-29 18:54:37',NULL,'2016-03-31 17:54:28',NULL,NULL),(320,214,1145,171,2,NULL,'2016-03-29 18:54:39',NULL,'2016-03-31 17:54:28',NULL,NULL),(321,214,1147,171,2,NULL,'2016-03-29 18:54:40',NULL,'2016-03-31 17:54:27',NULL,NULL),(322,214,1146,171,2,NULL,'2016-03-29 18:54:43',NULL,'2016-03-31 17:54:28',NULL,NULL),(323,214,1148,171,1,NULL,'2016-03-29 18:54:48',NULL,'2016-03-29 18:54:48',NULL,NULL),(324,214,1149,171,2,NULL,'2016-03-29 18:54:52',NULL,'2016-03-31 17:54:29',NULL,NULL),(325,214,1151,171,2,NULL,'2016-03-29 18:54:54',NULL,'2016-03-31 17:54:27',NULL,NULL),(326,214,1150,171,2,NULL,'2016-03-29 18:54:58',NULL,'2016-03-31 17:54:29',NULL,NULL),(327,214,1152,171,2,NULL,'2016-03-29 18:55:03',NULL,'2016-03-31 17:54:29',NULL,NULL),(328,214,1153,171,2,NULL,'2016-03-29 18:55:09',NULL,'2016-03-31 17:54:30',NULL,NULL),(329,214,1154,171,2,NULL,'2016-03-29 18:55:10',NULL,'2016-03-31 17:54:27',NULL,NULL),(330,214,1157,171,2,NULL,'2016-03-29 18:55:14',NULL,'2016-03-31 17:54:32',NULL,NULL),(331,214,1155,171,2,NULL,'2016-03-29 18:55:15',NULL,'2016-03-31 17:54:30',NULL,NULL),(332,214,1159,171,2,NULL,'2016-03-29 18:55:16',NULL,'2016-03-31 17:54:33',NULL,NULL),(333,214,1160,171,2,NULL,'2016-03-29 18:55:18',NULL,'2016-03-31 17:54:33',NULL,NULL),(334,214,1156,171,2,NULL,'2016-03-29 18:55:21',NULL,'2016-03-31 17:54:30',NULL,NULL),(335,214,1162,171,2,NULL,'2016-03-29 18:55:22',NULL,'2016-03-31 17:54:33',NULL,NULL),(336,214,1163,171,2,NULL,'2016-03-29 18:55:25',NULL,'2016-03-31 17:54:33',NULL,NULL),(337,214,1158,171,2,NULL,'2016-03-29 18:55:28',NULL,'2016-03-31 17:54:31',NULL,NULL),(338,214,1164,171,2,NULL,'2016-03-29 18:55:29',NULL,'2016-03-31 17:54:33',NULL,NULL),(339,214,1166,171,2,NULL,'2016-03-29 18:55:34',NULL,'2016-03-31 17:54:33',NULL,NULL),(340,214,1161,171,2,NULL,'2016-03-29 18:55:35',NULL,'2016-03-31 17:54:31',NULL,NULL),(341,214,1168,171,2,NULL,'2016-03-29 18:55:39',NULL,'2016-03-31 17:54:34',NULL,NULL),(342,214,1165,171,2,NULL,'2016-03-29 18:55:44',NULL,'2016-03-31 17:54:31',NULL,NULL),(343,214,1169,171,2,NULL,'2016-03-29 18:55:45',NULL,'2016-03-31 17:54:34',NULL,NULL),(344,214,1167,171,2,NULL,'2016-03-29 18:55:52',NULL,'2016-03-31 17:54:32',NULL,NULL),(345,214,1171,171,2,NULL,'2016-03-29 18:55:52',NULL,'2016-03-31 17:54:34',NULL,NULL),(346,214,1173,171,2,NULL,'2016-03-29 18:55:59',NULL,'2016-03-31 17:54:34',NULL,NULL),(347,214,1170,171,2,NULL,'2016-03-29 18:56:01',NULL,'2016-03-31 17:54:32',NULL,NULL),(348,214,1175,171,2,NULL,'2016-03-29 18:56:05',NULL,'2016-03-31 17:54:35',NULL,NULL),(349,214,1172,171,2,NULL,'2016-03-29 18:56:10',NULL,'2016-03-31 17:54:32',NULL,NULL),(350,214,1176,171,2,NULL,'2016-03-29 18:56:13',NULL,'2016-03-31 17:54:35',NULL,NULL),(351,214,1178,171,2,NULL,'2016-03-29 18:56:20',NULL,'2016-03-31 17:54:36',NULL,NULL),(352,214,1174,171,2,NULL,'2016-03-29 18:56:20',NULL,'2016-03-31 17:54:33',NULL,NULL),(353,214,1180,171,2,NULL,'2016-03-29 18:56:27',NULL,'2016-03-31 17:54:37',NULL,NULL),(354,214,1177,171,1,NULL,'2016-03-29 18:56:31',NULL,'2016-03-29 18:56:31',NULL,NULL),(355,214,1181,171,2,NULL,'2016-03-29 18:56:36',NULL,'2016-03-31 17:54:37',NULL,NULL),(356,214,1179,171,1,NULL,'2016-03-29 18:56:41',NULL,'2016-03-29 18:56:41',NULL,NULL),(357,214,1183,171,2,NULL,'2016-03-29 18:56:45',NULL,'2016-03-31 17:54:38',NULL,NULL),(358,214,1182,171,2,NULL,'2016-03-29 18:56:54',NULL,'2016-03-31 17:54:33',NULL,NULL),(359,214,1185,171,2,NULL,'2016-03-29 18:56:55',NULL,'2016-03-31 17:54:39',NULL,NULL),(360,214,1187,171,2,NULL,'2016-03-29 18:57:05',NULL,'2016-03-31 17:54:39',NULL,NULL),(361,214,1184,171,2,NULL,'2016-03-29 18:57:06',NULL,'2016-03-31 17:54:33',NULL,NULL),(362,214,1189,171,2,NULL,'2016-03-29 18:57:16',NULL,'2016-03-31 17:54:40',NULL,NULL),(363,214,1186,171,2,NULL,'2016-03-29 18:57:18',NULL,'2016-03-31 17:54:34',NULL,NULL),(364,214,1190,171,1,NULL,'2016-03-29 18:57:28',NULL,'2016-03-29 18:57:28',NULL,NULL),(365,214,1188,171,2,NULL,'2016-03-29 18:57:30',NULL,'2016-03-31 17:54:34',NULL,NULL),(366,214,1192,171,2,NULL,'2016-03-29 18:57:39',NULL,'2016-03-31 17:54:41',NULL,NULL),(367,214,1191,171,2,NULL,'2016-03-29 18:57:43',NULL,'2016-03-31 17:54:34',NULL,NULL),(368,214,1194,171,2,NULL,'2016-03-29 18:57:50',NULL,'2016-03-31 17:54:42',NULL,NULL),(369,214,1193,171,1,NULL,'2016-03-29 18:57:57',NULL,'2016-03-29 18:57:57',NULL,NULL),(370,214,1196,171,2,NULL,'2016-03-29 18:58:02',NULL,'2016-03-31 17:54:43',NULL,NULL),(371,214,1195,171,2,NULL,'2016-03-29 18:58:12',NULL,'2016-03-31 17:54:32',NULL,NULL),(372,214,1198,171,2,NULL,'2016-03-29 18:58:14',NULL,'2016-03-31 17:54:44',NULL,NULL),(373,214,1197,171,2,NULL,'2016-03-29 18:58:27',NULL,'2016-03-31 17:54:32',NULL,NULL),(374,214,1200,171,2,NULL,'2016-03-29 18:58:28',NULL,'2016-03-31 17:54:45',NULL,NULL),(375,214,1202,171,2,NULL,'2016-03-29 18:58:42',NULL,'2016-03-31 17:54:45',NULL,NULL),(376,214,1199,171,1,NULL,'2016-03-29 18:58:42',NULL,'2016-03-29 18:58:42',NULL,NULL),(377,214,1203,171,2,NULL,'2016-03-29 18:58:55',NULL,'2016-03-31 17:54:46',NULL,NULL),(378,214,1201,171,2,NULL,'2016-03-29 18:58:59',NULL,'2016-03-31 17:54:32',NULL,NULL),(379,214,1204,171,2,NULL,'2016-03-29 18:59:03',NULL,'2016-03-31 17:54:37',NULL,NULL),(380,214,1205,171,2,NULL,'2016-03-29 18:59:05',NULL,'2016-03-31 17:54:37',NULL,NULL),(381,214,1208,171,1,NULL,'2016-03-29 18:59:08',NULL,'2016-03-29 18:59:08',NULL,NULL),(382,214,1206,171,2,NULL,'2016-03-29 18:59:08',NULL,'2016-03-31 17:54:37',NULL,NULL),(383,214,1207,171,2,NULL,'2016-03-29 18:59:12',NULL,'2016-03-31 17:54:37',NULL,NULL),(384,214,1209,171,2,NULL,'2016-03-29 18:59:17',NULL,'2016-03-31 17:54:38',NULL,NULL),(385,214,1210,171,2,NULL,'2016-03-29 18:59:23',NULL,'2016-03-31 17:54:38',NULL,NULL),(386,214,1212,171,2,NULL,'2016-03-29 18:59:24',NULL,'2016-03-31 17:54:37',NULL,NULL),(387,214,1211,171,2,NULL,'2016-03-29 18:59:26',NULL,'2016-03-31 17:54:39',NULL,NULL),(388,214,1213,171,1,NULL,'2016-03-29 18:59:29',NULL,'2016-03-29 18:59:29',NULL,NULL),(389,214,1214,171,2,NULL,'2016-03-29 18:59:32',NULL,'2016-03-31 17:54:39',NULL,NULL),(390,214,1215,171,2,NULL,'2016-03-29 18:59:35',NULL,'2016-03-31 17:54:40',NULL,NULL),(391,214,1216,171,2,NULL,'2016-03-29 18:59:38',NULL,'2016-03-31 17:54:40',NULL,NULL),(392,214,1217,171,2,NULL,'2016-03-29 18:59:41',NULL,'2016-03-31 17:54:41',NULL,NULL),(393,214,1218,171,2,NULL,'2016-03-29 18:59:45',NULL,'2016-03-31 17:54:41',NULL,NULL),(394,214,1219,171,2,NULL,'2016-03-29 18:59:49',NULL,'2016-03-31 17:54:42',NULL,NULL),(395,214,1220,171,2,NULL,'2016-03-29 18:59:53',NULL,'2016-03-31 17:54:43',NULL,NULL),(396,214,1221,171,2,NULL,'2016-03-29 18:59:58',NULL,'2016-03-31 17:54:44',NULL,NULL),(397,214,1222,171,2,NULL,'2016-03-29 19:00:03',NULL,'2016-03-31 17:54:45',NULL,NULL),(398,214,1223,171,2,NULL,'2016-03-29 19:00:09',NULL,'2016-03-31 17:54:45',NULL,NULL),(399,214,1224,171,2,NULL,'2016-03-29 19:00:14',NULL,'2016-03-31 17:54:46',NULL,NULL),(400,214,1225,171,2,NULL,'2016-03-29 19:00:19',NULL,'2016-03-31 17:54:46',NULL,NULL),(401,214,1346,171,1,NULL,'2016-03-29 19:53:03',NULL,'2016-03-29 19:53:03',NULL,NULL),(402,214,1345,171,1,NULL,'2016-03-29 19:53:03',NULL,'2016-03-29 19:53:03',NULL,NULL),(403,214,1347,171,1,NULL,'2016-03-29 19:53:05',NULL,'2016-03-29 19:53:05',NULL,NULL),(404,214,1348,171,1,NULL,'2016-03-29 19:53:07',NULL,'2016-03-29 19:53:07',NULL,NULL),(405,214,1349,171,1,NULL,'2016-03-29 19:53:08',NULL,'2016-03-29 19:53:08',NULL,NULL),(406,214,1350,171,1,NULL,'2016-03-29 19:53:10',NULL,'2016-03-29 19:53:10',NULL,NULL),(407,214,1351,171,1,NULL,'2016-03-29 19:53:11',NULL,'2016-03-29 19:53:11',NULL,NULL),(408,214,1352,171,1,NULL,'2016-03-29 19:53:15',NULL,'2016-03-29 19:53:15',NULL,NULL),(409,214,1353,171,1,NULL,'2016-03-29 19:53:16',NULL,'2016-03-29 19:53:16',NULL,NULL),(410,214,1354,171,1,NULL,'2016-03-29 19:53:19',NULL,'2016-03-29 19:53:19',NULL,NULL),(411,214,1355,171,1,NULL,'2016-03-29 19:53:21',NULL,'2016-03-29 19:53:21',NULL,NULL),(412,214,1356,171,1,NULL,'2016-03-29 19:53:25',NULL,'2016-03-29 19:53:25',NULL,NULL),(413,214,1357,171,1,NULL,'2016-03-29 19:53:27',NULL,'2016-03-29 19:53:27',NULL,NULL),(414,214,1358,171,1,NULL,'2016-03-29 19:53:32',NULL,'2016-03-29 19:53:32',NULL,NULL),(415,214,1359,171,1,NULL,'2016-03-29 19:53:32',NULL,'2016-03-29 19:53:32',NULL,NULL),(416,214,1361,171,1,NULL,'2016-03-29 19:53:38',NULL,'2016-03-29 19:53:38',NULL,NULL),(417,214,1360,171,1,NULL,'2016-03-29 19:53:39',NULL,'2016-03-29 19:53:39',NULL,NULL),(418,214,1362,171,1,NULL,'2016-03-29 19:53:45',NULL,'2016-03-29 19:53:45',NULL,NULL),(419,214,1363,171,1,NULL,'2016-03-29 19:53:46',NULL,'2016-03-29 19:53:46',NULL,NULL),(420,214,1364,171,1,NULL,'2016-03-29 19:53:53',NULL,'2016-03-29 19:53:53',NULL,NULL),(421,214,1365,171,1,NULL,'2016-03-29 19:53:54',NULL,'2016-03-29 19:53:54',NULL,NULL),(422,214,1366,171,1,NULL,'2016-03-29 19:54:01',NULL,'2016-03-29 19:54:01',NULL,NULL),(423,214,1367,171,1,NULL,'2016-03-29 19:54:02',NULL,'2016-03-29 19:54:02',NULL,NULL),(424,214,1368,171,1,NULL,'2016-03-29 19:54:10',NULL,'2016-03-29 19:54:10',NULL,NULL),(425,214,1369,171,1,NULL,'2016-03-29 19:54:12',NULL,'2016-03-29 19:54:12',NULL,NULL),(426,214,1370,171,1,NULL,'2016-03-29 19:54:18',NULL,'2016-03-29 19:54:18',NULL,NULL),(427,214,1371,171,1,NULL,'2016-03-29 19:54:21',NULL,'2016-03-29 19:54:21',NULL,NULL),(428,214,1372,171,1,NULL,'2016-03-29 19:54:27',NULL,'2016-03-29 19:54:27',NULL,NULL),(429,214,1373,171,1,NULL,'2016-03-29 19:54:30',NULL,'2016-03-29 19:54:30',NULL,NULL),(430,214,1374,171,1,NULL,'2016-03-29 19:54:37',NULL,'2016-03-29 19:54:37',NULL,NULL),(431,214,1375,171,1,NULL,'2016-03-29 19:54:41',NULL,'2016-03-29 19:54:41',NULL,NULL),(432,214,1376,171,1,NULL,'2016-03-29 19:54:47',NULL,'2016-03-29 19:54:47',NULL,NULL),(433,214,1377,171,1,NULL,'2016-03-29 19:54:53',NULL,'2016-03-29 19:54:53',NULL,NULL),(434,214,1378,171,1,NULL,'2016-03-29 19:54:58',NULL,'2016-03-29 19:54:58',NULL,NULL),(435,214,1379,171,1,NULL,'2016-03-29 19:55:05',NULL,'2016-03-29 19:55:05',NULL,NULL),(436,214,1380,171,1,NULL,'2016-03-29 19:55:09',NULL,'2016-03-29 19:55:09',NULL,NULL),(437,214,1381,171,1,NULL,'2016-03-29 19:55:20',NULL,'2016-03-29 19:55:20',NULL,NULL),(438,214,1382,171,1,NULL,'2016-03-29 19:55:22',NULL,'2016-03-29 19:55:22',NULL,NULL),(439,214,1383,171,1,NULL,'2016-03-29 19:55:33',NULL,'2016-03-29 19:55:33',NULL,NULL),(440,214,1384,171,1,NULL,'2016-03-29 19:55:36',NULL,'2016-03-29 19:55:36',NULL,NULL),(441,214,1385,171,1,NULL,'2016-03-29 19:55:48',NULL,'2016-03-29 19:55:48',NULL,NULL),(442,214,1386,171,1,NULL,'2016-03-29 19:55:49',NULL,'2016-03-29 19:55:49',NULL,NULL),(443,214,1387,171,1,NULL,'2016-03-29 19:56:02',NULL,'2016-03-29 19:56:02',NULL,NULL),(444,214,1388,171,1,NULL,'2016-03-29 19:56:03',NULL,'2016-03-29 19:56:03',NULL,NULL),(445,214,1389,171,1,NULL,'2016-03-29 19:56:18',NULL,'2016-03-29 19:56:18',NULL,NULL),(446,214,1390,171,1,NULL,'2016-03-29 19:56:18',NULL,'2016-03-29 19:56:18',NULL,NULL),(447,214,1391,171,1,NULL,'2016-03-29 19:56:32',NULL,'2016-03-29 19:56:32',NULL,NULL),(448,214,1392,171,1,NULL,'2016-03-29 19:56:36',NULL,'2016-03-29 19:56:36',NULL,NULL),(449,214,1393,171,1,NULL,'2016-03-29 19:56:47',NULL,'2016-03-29 19:56:47',NULL,NULL),(450,214,1394,171,1,NULL,'2016-03-29 19:56:53',NULL,'2016-03-29 19:56:53',NULL,NULL),(451,214,1395,171,1,NULL,'2016-03-29 19:57:04',NULL,'2016-03-29 19:57:04',NULL,NULL),(452,214,1396,171,1,NULL,'2016-03-29 19:57:09',NULL,'2016-03-29 19:57:09',NULL,NULL),(453,214,1397,171,1,NULL,'2016-03-29 19:57:22',NULL,'2016-03-29 19:57:22',NULL,NULL),(454,214,1398,171,1,NULL,'2016-03-29 19:57:28',NULL,'2016-03-29 19:57:28',NULL,NULL),(455,214,1399,171,1,NULL,'2016-03-29 19:57:39',NULL,'2016-03-29 19:57:39',NULL,NULL),(456,214,1400,171,1,NULL,'2016-03-29 19:57:45',NULL,'2016-03-29 19:57:45',NULL,NULL),(457,214,1401,171,1,NULL,'2016-03-29 19:57:55',NULL,'2016-03-29 19:57:55',NULL,NULL),(458,214,1403,171,1,NULL,'2016-03-29 19:58:01',NULL,'2016-03-29 19:58:01',NULL,NULL),(459,214,1402,171,1,NULL,'2016-03-29 19:58:03',NULL,'2016-03-29 19:58:03',NULL,NULL),(460,214,1404,171,1,NULL,'2016-03-29 19:58:04',NULL,'2016-03-29 19:58:04',NULL,NULL),(461,214,1405,171,1,NULL,'2016-03-29 19:58:07',NULL,'2016-03-29 19:58:07',NULL,NULL),(462,214,1407,171,1,NULL,'2016-03-29 19:58:11',NULL,'2016-03-29 19:58:11',NULL,NULL),(463,214,1408,171,1,NULL,'2016-03-29 19:58:15',NULL,'2016-03-29 19:58:15',NULL,NULL),(464,214,1409,171,1,NULL,'2016-03-29 19:58:20',NULL,'2016-03-29 19:58:20',NULL,NULL),(465,214,1406,171,1,NULL,'2016-03-29 19:58:20',NULL,'2016-03-29 19:58:20',NULL,NULL),(466,214,1410,171,1,NULL,'2016-03-29 19:58:25',NULL,'2016-03-29 19:58:25',NULL,NULL),(467,214,1412,171,1,NULL,'2016-03-29 19:58:31',NULL,'2016-03-29 19:58:31',NULL,NULL),(468,214,1413,171,1,NULL,'2016-03-29 19:58:38',NULL,'2016-03-29 19:58:38',NULL,NULL),(469,214,1411,171,1,NULL,'2016-03-29 19:58:39',NULL,'2016-03-29 19:58:39',NULL,NULL),(470,214,1414,171,1,NULL,'2016-03-29 19:58:44',NULL,'2016-03-29 19:58:44',NULL,NULL),(471,214,1415,171,1,NULL,'2016-03-29 19:58:45',NULL,'2016-03-29 19:58:45',NULL,NULL),(472,214,1416,171,1,NULL,'2016-03-29 19:58:48',NULL,'2016-03-29 19:58:48',NULL,NULL),(473,214,1417,171,1,NULL,'2016-03-29 19:58:51',NULL,'2016-03-29 19:58:51',NULL,NULL),(474,214,1418,171,1,NULL,'2016-03-29 19:58:51',NULL,'2016-03-29 19:58:51',NULL,NULL),(475,214,1419,171,1,NULL,'2016-03-29 19:58:55',NULL,'2016-03-29 19:58:55',NULL,NULL),(476,214,1421,171,1,NULL,'2016-03-29 19:58:59',NULL,'2016-03-29 19:58:59',NULL,NULL),(477,214,1420,171,1,NULL,'2016-03-29 19:58:59',NULL,'2016-03-29 19:58:59',NULL,NULL),(478,214,1422,171,1,NULL,'2016-03-29 19:59:03',NULL,'2016-03-29 19:59:03',NULL,NULL),(479,214,1423,171,1,NULL,'2016-03-29 19:59:07',NULL,'2016-03-29 19:59:07',NULL,NULL),(480,214,1424,171,1,NULL,'2016-03-29 19:59:08',NULL,'2016-03-29 19:59:08',NULL,NULL),(481,214,1426,171,1,NULL,'2016-03-29 19:59:13',NULL,'2016-03-29 19:59:13',NULL,NULL),(482,214,1425,171,1,NULL,'2016-03-29 19:59:16',NULL,'2016-03-29 19:59:16',NULL,NULL),(483,214,1427,171,1,NULL,'2016-03-29 19:59:19',NULL,'2016-03-29 19:59:19',NULL,NULL),(484,214,1428,171,1,NULL,'2016-03-29 19:59:25',NULL,'2016-03-29 19:59:25',NULL,NULL),(485,214,1429,171,1,NULL,'2016-03-29 19:59:27',NULL,'2016-03-29 19:59:27',NULL,NULL),(486,214,1431,171,1,NULL,'2016-03-29 19:59:34',NULL,'2016-03-29 19:59:34',NULL,NULL),(487,214,1430,171,1,NULL,'2016-03-29 19:59:36',NULL,'2016-03-29 19:59:36',NULL,NULL),(488,214,1432,171,1,NULL,'2016-03-29 19:59:42',NULL,'2016-03-29 19:59:42',NULL,NULL),(489,214,1433,171,1,NULL,'2016-03-29 19:59:47',NULL,'2016-03-29 19:59:47',NULL,NULL),(490,214,1434,171,1,NULL,'2016-03-29 19:59:51',NULL,'2016-03-29 19:59:51',NULL,NULL),(491,214,1435,171,1,NULL,'2016-03-29 19:59:58',NULL,'2016-03-29 19:59:58',NULL,NULL),(492,214,1436,171,1,NULL,'2016-03-29 20:00:00',NULL,'2016-03-29 20:00:00',NULL,NULL),(493,214,1438,171,1,NULL,'2016-03-29 20:00:09',NULL,'2016-03-29 20:00:09',NULL,NULL),(494,214,1437,171,1,NULL,'2016-03-29 20:00:10',NULL,'2016-03-29 20:00:10',NULL,NULL),(495,214,1439,171,1,NULL,'2016-03-29 20:00:20',NULL,'2016-03-29 20:00:20',NULL,NULL),(496,214,1440,171,1,NULL,'2016-03-29 20:00:25',NULL,'2016-03-29 20:00:25',NULL,NULL),(497,214,1441,171,1,NULL,'2016-03-29 20:00:33',NULL,'2016-03-29 20:00:33',NULL,NULL),(498,214,1442,171,1,NULL,'2016-03-29 20:00:37',NULL,'2016-03-29 20:00:37',NULL,NULL),(499,214,1443,171,1,NULL,'2016-03-29 20:00:44',NULL,'2016-03-29 20:00:44',NULL,NULL),(500,214,1444,171,1,NULL,'2016-03-29 20:00:49',NULL,'2016-03-29 20:00:49',NULL,NULL),(501,214,1445,171,1,NULL,'2016-03-29 20:00:55',NULL,'2016-03-29 20:00:55',NULL,NULL),(502,214,1446,171,1,NULL,'2016-03-29 20:01:03',NULL,'2016-03-29 20:01:03',NULL,NULL),(503,214,1447,171,1,NULL,'2016-03-29 20:01:06',NULL,'2016-03-29 20:01:06',NULL,NULL),(504,214,1449,171,1,NULL,'2016-03-29 20:01:17',NULL,'2016-03-29 20:01:17',NULL,NULL),(505,214,1448,171,1,NULL,'2016-03-29 20:01:19',NULL,'2016-03-29 20:01:19',NULL,NULL),(506,214,1450,171,1,NULL,'2016-03-29 20:01:31',NULL,'2016-03-29 20:01:31',NULL,NULL),(507,214,1451,171,1,NULL,'2016-03-29 20:01:35',NULL,'2016-03-29 20:01:35',NULL,NULL),(508,214,1452,171,1,NULL,'2016-03-29 20:01:45',NULL,'2016-03-29 20:01:45',NULL,NULL),(509,214,1453,171,1,NULL,'2016-03-29 20:01:49',NULL,'2016-03-29 20:01:49',NULL,NULL),(510,214,1454,171,1,NULL,'2016-03-29 20:02:00',NULL,'2016-03-29 20:02:00',NULL,NULL),(511,214,1455,171,1,NULL,'2016-03-29 20:02:06',NULL,'2016-03-29 20:02:06',NULL,NULL),(512,214,1456,171,1,NULL,'2016-03-29 20:02:17',NULL,'2016-03-29 20:02:17',NULL,NULL),(513,214,1457,171,1,NULL,'2016-03-29 20:02:23',NULL,'2016-03-29 20:02:23',NULL,NULL),(514,214,1458,171,1,NULL,'2016-03-29 20:02:32',NULL,'2016-03-29 20:02:32',NULL,NULL),(515,214,1459,171,1,NULL,'2016-03-29 20:02:37',NULL,'2016-03-29 20:02:37',NULL,NULL),(516,214,1460,171,1,NULL,'2016-03-29 20:02:48',NULL,'2016-03-29 20:02:48',NULL,NULL),(517,214,1461,171,1,NULL,'2016-03-29 20:02:57',NULL,'2016-03-29 20:02:57',NULL,NULL),(518,214,1463,171,1,NULL,'2016-03-29 20:03:03',NULL,'2016-03-29 20:03:03',NULL,NULL),(519,214,1462,171,1,NULL,'2016-03-29 20:03:02',NULL,'2016-03-29 20:03:02',NULL,NULL),(520,214,1464,171,1,NULL,'2016-03-29 20:03:05',NULL,'2016-03-29 20:03:05',NULL,NULL),(521,214,1466,171,1,NULL,'2016-03-29 20:03:09',NULL,'2016-03-29 20:03:09',NULL,NULL),(522,214,1467,171,1,NULL,'2016-03-29 20:03:13',NULL,'2016-03-29 20:03:13',NULL,NULL),(523,214,1465,171,1,NULL,'2016-03-29 20:03:17',NULL,'2016-03-29 20:03:17',NULL,NULL),(524,214,1468,171,1,NULL,'2016-03-29 20:03:18',NULL,'2016-03-29 20:03:18',NULL,NULL),(525,214,1469,171,1,NULL,'2016-03-29 20:03:23',NULL,'2016-03-29 20:03:23',NULL,NULL),(526,214,1471,171,1,NULL,'2016-03-29 20:03:29',NULL,'2016-03-29 20:03:29',NULL,NULL),(527,214,1470,171,1,NULL,'2016-03-29 20:03:34',NULL,'2016-03-29 20:03:34',NULL,NULL),(528,214,1472,171,1,NULL,'2016-03-29 20:03:35',NULL,'2016-03-29 20:03:35',NULL,NULL),(529,214,1474,171,1,NULL,'2016-03-29 20:03:40',NULL,'2016-03-29 20:03:40',NULL,NULL),(530,214,1473,171,1,NULL,'2016-03-29 20:03:41',NULL,'2016-03-29 20:03:41',NULL,NULL),(531,214,1475,171,1,NULL,'2016-03-29 20:03:42',NULL,'2016-03-29 20:03:42',NULL,NULL),(532,214,1477,171,1,NULL,'2016-03-29 20:03:45',NULL,'2016-03-29 20:03:45',NULL,NULL),(533,214,1476,171,1,NULL,'2016-03-29 20:03:47',NULL,'2016-03-29 20:03:47',NULL,NULL),(534,214,1478,171,1,NULL,'2016-03-29 20:03:48',NULL,'2016-03-29 20:03:48',NULL,NULL),(535,214,1480,171,1,NULL,'2016-03-29 20:03:52',NULL,'2016-03-29 20:03:52',NULL,NULL),(536,214,1479,171,1,NULL,'2016-03-29 20:03:54',NULL,'2016-03-29 20:03:54',NULL,NULL),(537,214,1481,171,1,NULL,'2016-03-29 20:03:57',NULL,'2016-03-29 20:03:57',NULL,NULL),(538,214,1482,171,1,NULL,'2016-03-29 20:04:01',NULL,'2016-03-29 20:04:01',NULL,NULL),(539,214,1483,171,1,NULL,'2016-03-29 20:04:02',NULL,'2016-03-29 20:04:02',NULL,NULL),(540,214,1485,171,1,NULL,'2016-03-29 20:04:08',NULL,'2016-03-29 20:04:08',NULL,NULL),(541,214,1484,171,1,NULL,'2016-03-29 20:04:09',NULL,'2016-03-29 20:04:09',NULL,NULL),(542,214,1486,171,1,NULL,'2016-03-29 20:04:14',NULL,'2016-03-29 20:04:14',NULL,NULL),(543,214,1487,171,1,NULL,'2016-03-29 20:04:17',NULL,'2016-03-29 20:04:17',NULL,NULL),(544,214,1488,171,1,NULL,'2016-03-29 20:04:21',NULL,'2016-03-29 20:04:21',NULL,NULL),(545,214,1489,171,1,NULL,'2016-03-29 20:04:27',NULL,'2016-03-29 20:04:27',NULL,NULL),(546,214,1490,171,1,NULL,'2016-03-29 20:04:28',NULL,'2016-03-29 20:04:28',NULL,NULL),(547,214,1491,171,1,NULL,'2016-03-29 20:04:35',NULL,'2016-03-29 20:04:35',NULL,NULL),(548,214,1492,171,1,NULL,'2016-03-29 20:04:35',NULL,'2016-03-29 20:04:35',NULL,NULL),(549,214,1493,171,1,NULL,'2016-03-29 20:04:44',NULL,'2016-03-29 20:04:44',NULL,NULL),(550,214,1494,171,1,NULL,'2016-03-29 20:04:45',NULL,'2016-03-29 20:04:45',NULL,NULL),(551,214,1495,171,1,NULL,'2016-03-29 20:04:51',NULL,'2016-03-29 20:04:51',NULL,NULL),(552,214,1496,171,1,NULL,'2016-03-29 20:04:56',NULL,'2016-03-29 20:04:56',NULL,NULL),(553,214,1497,171,1,NULL,'2016-03-29 20:05:00',NULL,'2016-03-29 20:05:00',NULL,NULL),(554,214,1498,171,1,NULL,'2016-03-29 20:05:07',NULL,'2016-03-29 20:05:07',NULL,NULL),(555,214,1499,171,1,NULL,'2016-03-29 20:05:09',NULL,'2016-03-29 20:05:09',NULL,NULL),(556,214,1500,171,1,NULL,'2016-03-29 20:05:19',NULL,'2016-03-29 20:05:19',NULL,NULL),(557,214,1501,171,1,NULL,'2016-03-29 20:05:19',NULL,'2016-03-29 20:05:19',NULL,NULL),(558,214,1503,171,1,NULL,'2016-03-29 20:05:32',NULL,'2016-03-29 20:05:32',NULL,NULL),(559,214,1502,171,1,NULL,'2016-03-29 20:05:34',NULL,'2016-03-29 20:05:34',NULL,NULL),(560,214,1504,171,1,NULL,'2016-03-29 20:05:42',NULL,'2016-03-29 20:05:42',NULL,NULL),(561,214,1505,171,1,NULL,'2016-03-29 20:05:47',NULL,'2016-03-29 20:05:47',NULL,NULL),(562,214,1506,171,1,NULL,'2016-03-29 20:05:53',NULL,'2016-03-29 20:05:53',NULL,NULL),(563,214,1507,171,1,NULL,'2016-03-29 20:06:01',NULL,'2016-03-29 20:06:01',NULL,NULL),(564,214,1508,171,1,NULL,'2016-03-29 20:06:05',NULL,'2016-03-29 20:06:05',NULL,NULL),(565,214,1509,171,1,NULL,'2016-03-29 20:06:14',NULL,'2016-03-29 20:06:14',NULL,NULL),(566,214,1510,171,1,NULL,'2016-03-29 20:06:17',NULL,'2016-03-29 20:06:17',NULL,NULL),(567,214,1511,171,1,NULL,'2016-03-29 20:06:27',NULL,'2016-03-29 20:06:27',NULL,NULL),(568,214,1512,171,1,NULL,'2016-03-29 20:06:30',NULL,'2016-03-29 20:06:30',NULL,NULL),(569,214,1513,171,1,NULL,'2016-03-29 20:06:42',NULL,'2016-03-29 20:06:42',NULL,NULL),(570,214,1514,171,1,NULL,'2016-03-29 20:06:43',NULL,'2016-03-29 20:06:43',NULL,NULL),(571,214,1516,171,1,NULL,'2016-03-29 20:06:56',NULL,'2016-03-29 20:06:56',NULL,NULL),(572,214,1515,171,1,NULL,'2016-03-29 20:06:59',NULL,'2016-03-29 20:06:59',NULL,NULL),(573,214,1517,171,1,NULL,'2016-03-29 20:07:12',NULL,'2016-03-29 20:07:12',NULL,NULL),(574,214,1518,171,1,NULL,'2016-03-29 20:07:16',NULL,'2016-03-29 20:07:16',NULL,NULL),(575,214,1519,171,1,NULL,'2016-03-29 20:07:26',NULL,'2016-03-29 20:07:26',NULL,NULL),(576,214,1520,171,1,NULL,'2016-03-29 20:07:32',NULL,'2016-03-29 20:07:32',NULL,NULL),(577,214,1521,171,1,NULL,'2016-03-29 20:07:42',NULL,'2016-03-29 20:07:42',NULL,NULL),(578,214,1522,171,1,NULL,'2016-03-29 20:07:50',NULL,'2016-03-29 20:07:50',NULL,NULL),(579,214,1523,171,1,NULL,'2016-03-29 20:07:56',NULL,'2016-03-29 20:07:56',NULL,NULL),(580,214,1524,171,1,NULL,'2016-03-29 20:07:56',NULL,'2016-03-29 20:07:56',NULL,NULL),(581,214,1525,171,1,NULL,'2016-03-29 20:08:00',NULL,'2016-03-29 20:08:00',NULL,NULL),(582,214,1527,171,1,NULL,'2016-03-29 20:08:04',NULL,'2016-03-29 20:08:04',NULL,NULL),(583,214,1528,171,1,NULL,'2016-03-29 20:08:07',NULL,'2016-03-29 20:08:07',NULL,NULL),(584,214,1526,171,1,NULL,'2016-03-29 20:08:11',NULL,'2016-03-29 20:08:11',NULL,NULL),(585,214,1529,171,1,NULL,'2016-03-29 20:08:12',NULL,'2016-03-29 20:08:12',NULL,NULL),(586,214,1530,171,1,NULL,'2016-03-29 20:08:15',NULL,'2016-03-29 20:08:15',NULL,NULL),(587,214,1531,171,1,NULL,'2016-03-29 20:08:18',NULL,'2016-03-29 20:08:18',NULL,NULL),(588,214,1532,171,1,NULL,'2016-03-29 20:08:21',NULL,'2016-03-29 20:08:21',NULL,NULL),(589,214,1533,171,1,NULL,'2016-03-29 20:08:24',NULL,'2016-03-29 20:08:24',NULL,NULL),(590,214,1534,171,1,NULL,'2016-03-29 20:08:27',NULL,'2016-03-29 20:08:27',NULL,NULL),(591,214,1535,171,1,NULL,'2016-03-29 20:08:30',NULL,'2016-03-29 20:08:30',NULL,NULL),(592,214,1536,171,1,NULL,'2016-03-29 20:08:34',NULL,'2016-03-29 20:08:34',NULL,NULL),(593,214,1537,171,1,NULL,'2016-03-29 20:08:38',NULL,'2016-03-29 20:08:38',NULL,NULL),(594,214,1538,171,1,NULL,'2016-03-29 20:08:43',NULL,'2016-03-29 20:08:43',NULL,NULL),(595,214,1539,171,1,NULL,'2016-03-29 20:08:47',NULL,'2016-03-29 20:08:47',NULL,NULL),(596,214,1540,171,1,NULL,'2016-03-29 20:08:52',NULL,'2016-03-29 20:08:52',NULL,NULL),(597,214,1541,171,1,NULL,'2016-03-29 20:08:58',NULL,'2016-03-29 20:08:58',NULL,NULL),(598,214,1542,171,1,NULL,'2016-03-29 20:09:03',NULL,'2016-03-29 20:09:03',NULL,NULL),(599,214,1543,171,1,NULL,'2016-03-29 20:09:09',NULL,'2016-03-29 20:09:09',NULL,NULL),(600,214,1544,171,1,NULL,'2016-03-29 20:09:14',NULL,'2016-03-29 20:09:14',NULL,NULL);
/*!40000 ALTER TABLE `org_person_student_cohort` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_person_student_survey`
--

DROP TABLE IF EXISTS `org_person_student_survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_person_student_survey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `receive_survey` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `survey_unique_index` (`organization_id`,`person_id`,`survey_id`),
  KEY `fk_org_person_student_survey_survey1_idx` (`survey_id`),
  KEY `fk_org_person_student_survey_organization1_idx` (`organization_id`),
  KEY `fk_org_person_student_survey_person1_idx` (`person_id`),
  KEY `org_person_student_survey_covering_index` (`organization_id`,`survey_id`,`person_id`,`deleted_at`),
  KEY `FK_37C84941DE12AB56` (`created_by`),
  KEY `FK_37C8494125F94802` (`modified_by`),
  KEY `FK_37C849411F6FA0AF` (`deleted_by`),
  CONSTRAINT `FK_37C849411F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_37C84941217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_37C8494125F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_37C8494132C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_37C84941B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_37C84941DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=619565 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_person_student_survey`
--

LOCK TABLES `org_person_student_survey` WRITE;
/*!40000 ALTER TABLE `org_person_student_survey` DISABLE KEYS */;
INSERT INTO `org_person_student_survey` VALUES (618181,214,1225,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618182,214,1080,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618183,214,1086,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618184,214,1133,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618185,214,1049,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618186,214,1124,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618187,214,1070,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618188,214,1199,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618189,214,1066,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618190,214,1189,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618191,214,1072,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618192,214,1076,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618193,214,1115,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618194,214,1213,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618195,214,1201,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618196,214,1134,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618197,214,1197,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618198,214,1097,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618199,214,1069,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618200,214,1062,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618201,214,1218,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618202,214,1065,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618203,214,1116,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618204,214,1221,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618205,214,1113,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618206,214,1107,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618207,214,1046,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618208,214,1141,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618209,214,1091,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618210,214,1054,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618211,214,1142,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618212,214,1184,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618213,214,1071,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618214,214,1048,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618215,214,1174,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618216,214,1087,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618217,214,1177,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618218,214,1145,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618219,214,1094,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618220,214,1137,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618221,214,1151,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618222,214,1089,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618223,214,1146,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618224,214,1100,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618225,214,1077,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618226,214,1150,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618227,214,1168,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618228,214,1081,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618229,214,1120,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618230,214,1078,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618231,214,1171,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618232,214,1157,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618233,214,1161,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618234,214,1158,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618235,214,1033,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618236,214,1088,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618237,214,1130,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618238,214,1156,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618239,214,1056,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618240,214,1160,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618241,214,1187,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618242,214,1126,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618243,214,1037,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618244,214,1040,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618245,214,1063,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618246,214,1122,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618247,214,1045,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618248,214,1092,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618249,214,1132,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618250,214,1031,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618251,214,1178,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618252,214,1095,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618253,214,1123,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618254,214,1148,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618255,214,1051,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618256,214,1109,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618257,214,1055,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618258,214,1073,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618259,214,1188,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618260,214,1140,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618261,214,1163,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618262,214,1129,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618263,214,1175,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618264,214,1155,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618265,214,1101,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618266,214,1103,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618267,214,1096,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618268,214,1111,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618269,214,1060,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618270,214,1135,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618271,214,1121,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618272,214,1170,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618273,214,1043,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618274,214,1034,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618275,214,1195,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618276,214,1064,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618277,214,1026,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618278,214,1053,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618279,214,1154,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618280,214,1176,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618281,214,1035,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618282,214,1212,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618283,214,1153,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618284,214,1215,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618285,214,1030,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618286,214,1068,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618287,214,1167,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618288,214,1098,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618289,214,1216,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618290,214,1084,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618291,214,1220,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618292,214,1185,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618293,214,1222,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618294,214,1117,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618295,214,1169,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618296,214,1041,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618297,214,1211,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618298,214,1114,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618299,214,1058,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618300,214,1042,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618301,214,1044,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618302,214,1202,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618303,214,1118,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618304,214,1032,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618305,214,1079,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618306,214,1223,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618307,214,1206,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618308,214,1193,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618309,214,1210,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618310,214,1165,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618311,214,1207,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618312,214,1106,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618313,214,1029,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618314,214,1209,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618315,214,1191,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618316,214,1186,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618317,214,1179,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618318,214,1182,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618319,214,1050,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618320,214,1038,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618321,214,1224,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618322,214,1061,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618323,214,1027,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618324,214,1159,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618325,214,1125,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618326,214,1075,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618327,214,1047,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618328,214,1112,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618329,214,1200,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618330,214,1119,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618331,214,1083,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618332,214,1085,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618333,214,1139,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618334,214,1144,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618335,214,1164,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618336,214,1205,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618337,214,1104,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618338,214,1074,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618339,214,1203,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618340,214,1093,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618341,214,1057,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618342,214,1162,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618343,214,1128,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618344,214,1028,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618345,214,1067,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618346,214,1219,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618347,214,1183,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618348,214,1090,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618349,214,1172,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618350,214,1173,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618351,214,1190,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618352,214,1166,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618353,214,1136,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618354,214,1217,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618355,214,1181,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618356,214,1214,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618357,214,1152,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618358,214,1204,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618359,214,1039,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618360,214,1036,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618361,214,1108,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618362,214,1149,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618363,214,1099,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618364,214,1082,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618365,214,1052,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618366,214,1196,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618367,214,1105,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618368,214,1180,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618369,214,1127,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618370,214,1131,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618371,214,1208,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618372,214,1059,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618373,214,1102,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618374,214,1110,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618375,214,1198,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618376,214,1192,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618377,214,1138,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618378,214,1194,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618379,214,1147,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618380,214,1143,11,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618381,214,1225,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618382,214,1080,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618383,214,1086,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618384,214,1133,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618385,214,1049,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618386,214,1124,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618387,214,1070,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618388,214,1199,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618389,214,1066,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618390,214,1189,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618391,214,1072,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618392,214,1076,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618393,214,1115,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618394,214,1213,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618395,214,1201,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618396,214,1134,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618397,214,1197,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618398,214,1097,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618399,214,1069,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618400,214,1062,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618401,214,1218,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618402,214,1065,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618403,214,1116,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618404,214,1221,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618405,214,1113,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618406,214,1107,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618407,214,1046,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618408,214,1141,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618409,214,1091,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618410,214,1054,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618411,214,1142,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618412,214,1184,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618413,214,1071,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618414,214,1048,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618415,214,1174,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618416,214,1087,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618417,214,1177,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618418,214,1145,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618419,214,1094,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618420,214,1137,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618421,214,1151,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618422,214,1089,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618423,214,1146,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618424,214,1100,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618425,214,1077,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618426,214,1150,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618427,214,1168,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618428,214,1081,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618429,214,1120,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618430,214,1078,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618431,214,1171,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618432,214,1157,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618433,214,1161,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618434,214,1158,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618435,214,1033,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618436,214,1088,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618437,214,1130,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618438,214,1156,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618439,214,1056,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618440,214,1160,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618441,214,1187,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618442,214,1126,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618443,214,1037,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618444,214,1040,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618445,214,1063,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618446,214,1122,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618447,214,1045,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618448,214,1092,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618449,214,1132,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618450,214,1031,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618451,214,1178,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618452,214,1095,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618453,214,1123,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618454,214,1148,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618455,214,1051,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618456,214,1109,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618457,214,1055,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618458,214,1073,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618459,214,1188,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618460,214,1140,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618461,214,1163,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618462,214,1129,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618463,214,1175,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618464,214,1155,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618465,214,1101,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618466,214,1103,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618467,214,1096,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618468,214,1111,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618469,214,1060,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618470,214,1135,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618471,214,1121,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618472,214,1170,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618473,214,1043,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618474,214,1034,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618475,214,1195,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618476,214,1064,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618477,214,1026,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618478,214,1053,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618479,214,1154,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618480,214,1176,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618481,214,1035,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618482,214,1212,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618483,214,1153,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618484,214,1215,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618485,214,1030,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618486,214,1068,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618487,214,1167,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618488,214,1098,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618489,214,1216,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618490,214,1084,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618491,214,1220,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618492,214,1185,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618493,214,1222,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618494,214,1117,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618495,214,1169,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618496,214,1041,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618497,214,1211,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618498,214,1114,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618499,214,1058,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618500,214,1042,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618501,214,1044,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618502,214,1202,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618503,214,1118,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618504,214,1032,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618505,214,1079,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618506,214,1223,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618507,214,1206,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618508,214,1193,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618509,214,1210,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618510,214,1165,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618511,214,1207,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618512,214,1106,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618513,214,1029,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618514,214,1209,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618515,214,1191,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618516,214,1186,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618517,214,1179,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618518,214,1182,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618519,214,1050,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618520,214,1038,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618521,214,1224,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618522,214,1061,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618523,214,1027,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618524,214,1159,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618525,214,1125,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618526,214,1075,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618527,214,1047,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618528,214,1112,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618529,214,1200,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618530,214,1119,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618531,214,1083,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618532,214,1085,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618533,214,1139,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618534,214,1144,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618535,214,1164,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618536,214,1205,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618537,214,1104,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618538,214,1074,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618539,214,1203,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618540,214,1093,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618541,214,1057,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618542,214,1162,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618543,214,1128,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618544,214,1028,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618545,214,1067,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618546,214,1219,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618547,214,1183,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618548,214,1090,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618549,214,1172,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618550,214,1173,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618551,214,1190,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618552,214,1166,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618553,214,1136,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618554,214,1217,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618555,214,1181,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618556,214,1214,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618557,214,1152,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618558,214,1204,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618559,214,1039,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618560,214,1036,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618561,214,1108,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618562,214,1149,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618563,214,1099,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618564,214,1082,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618565,214,1052,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618566,214,1196,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618567,214,1105,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618568,214,1180,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618569,214,1127,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618570,214,1131,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618571,214,1208,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618572,214,1059,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618573,214,1102,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618574,214,1110,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618575,214,1198,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618576,214,1192,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618577,214,1138,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618578,214,1194,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618579,214,1147,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618580,214,1143,12,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618581,214,1225,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618582,214,1080,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618583,214,1086,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618584,214,1133,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618585,214,1049,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618586,214,1124,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618587,214,1070,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618588,214,1199,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618589,214,1066,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618590,214,1189,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618591,214,1072,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618592,214,1076,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618593,214,1115,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618594,214,1213,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618595,214,1201,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618596,214,1134,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618597,214,1197,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618598,214,1097,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618599,214,1069,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618600,214,1062,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618601,214,1218,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618602,214,1065,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618603,214,1116,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618604,214,1221,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618605,214,1113,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618606,214,1107,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618607,214,1046,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618608,214,1141,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618609,214,1091,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618610,214,1054,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618611,214,1142,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618612,214,1184,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618613,214,1071,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618614,214,1048,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618615,214,1174,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618616,214,1087,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618617,214,1145,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618618,214,1094,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618619,214,1137,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618620,214,1151,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618621,214,1089,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618622,214,1146,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618623,214,1100,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618624,214,1077,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618625,214,1150,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618626,214,1168,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618627,214,1081,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618628,214,1120,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618629,214,1078,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618630,214,1171,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618631,214,1157,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618632,214,1161,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618633,214,1158,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618634,214,1033,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618635,214,1088,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618636,214,1130,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618637,214,1156,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618638,214,1056,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618639,214,1160,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618640,214,1187,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618641,214,1126,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618642,214,1037,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618643,214,1040,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618644,214,1063,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618645,214,1122,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618646,214,1045,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618647,214,1092,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618648,214,1132,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618649,214,1031,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618650,214,1178,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618651,214,1095,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618652,214,1123,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618653,214,1148,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618654,214,1051,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618655,214,1109,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618656,214,1055,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618657,214,1073,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618658,214,1188,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618659,214,1140,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618660,214,1163,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618661,214,1129,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618662,214,1175,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618663,214,1155,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618664,214,1101,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618665,214,1103,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618666,214,1096,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618667,214,1111,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618668,214,1060,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618669,214,1135,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618670,214,1121,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618671,214,1170,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618672,214,1043,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618673,214,1034,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618674,214,1195,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618675,214,1064,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618676,214,1026,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618677,214,1053,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618678,214,1154,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618679,214,1176,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618680,214,1035,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618681,214,1212,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618682,214,1153,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618683,214,1215,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618684,214,1030,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618685,214,1068,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618686,214,1167,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618687,214,1098,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618688,214,1216,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618689,214,1084,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618690,214,1220,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618691,214,1185,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618692,214,1222,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618693,214,1117,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618694,214,1169,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618695,214,1041,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618696,214,1211,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618697,214,1114,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618698,214,1058,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618699,214,1042,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618700,214,1044,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618701,214,1202,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618702,214,1118,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618703,214,1032,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618704,214,1079,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618705,214,1223,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618706,214,1206,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618707,214,1193,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618708,214,1210,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618709,214,1165,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618710,214,1207,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618711,214,1106,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618712,214,1029,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618713,214,1209,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618714,214,1191,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618715,214,1186,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618716,214,1182,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618717,214,1050,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618718,214,1038,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618719,214,1224,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618720,214,1061,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618721,214,1027,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618722,214,1159,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618723,214,1125,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618724,214,1075,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618725,214,1047,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618726,214,1112,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618727,214,1200,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618728,214,1119,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618729,214,1083,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618730,214,1085,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618731,214,1139,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618732,214,1144,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618733,214,1164,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618734,214,1205,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618735,214,1104,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618736,214,1074,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618737,214,1203,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618738,214,1093,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618739,214,1057,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618740,214,1162,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618741,214,1128,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618742,214,1028,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618743,214,1067,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618744,214,1219,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618745,214,1183,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618746,214,1090,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618747,214,1172,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618748,214,1173,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618749,214,1190,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618750,214,1166,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618751,214,1136,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618752,214,1217,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618753,214,1181,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618754,214,1214,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618755,214,1152,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618756,214,1204,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618757,214,1039,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618758,214,1036,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618759,214,1108,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618760,214,1149,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618761,214,1099,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618762,214,1082,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618763,214,1052,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618764,214,1196,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618765,214,1105,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618766,214,1180,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618767,214,1127,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618768,214,1131,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618769,214,1208,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618770,214,1059,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618771,214,1102,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618772,214,1110,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618773,214,1198,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618774,214,1192,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618775,214,1138,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618776,214,1194,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618777,214,1147,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618778,214,1143,13,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618779,214,1225,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618780,214,1080,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618781,214,1086,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618782,214,1133,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618783,214,1049,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618784,214,1124,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618785,214,1070,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618786,214,1199,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618787,214,1066,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618788,214,1189,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618789,214,1072,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618790,214,1076,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618791,214,1115,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618792,214,1213,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618793,214,1201,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618794,214,1134,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618795,214,1197,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618796,214,1097,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618797,214,1069,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618798,214,1062,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618799,214,1218,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618800,214,1065,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618801,214,1116,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618802,214,1221,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618803,214,1113,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618804,214,1107,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618805,214,1046,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618806,214,1141,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618807,214,1091,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618808,214,1054,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618809,214,1142,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618810,214,1184,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618811,214,1071,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618812,214,1048,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618813,214,1174,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618814,214,1087,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618815,214,1145,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618816,214,1094,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618817,214,1137,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618818,214,1151,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618819,214,1089,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618820,214,1146,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618821,214,1100,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618822,214,1077,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618823,214,1150,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618824,214,1168,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618825,214,1081,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618826,214,1120,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618827,214,1078,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618828,214,1171,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618829,214,1157,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618830,214,1161,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618831,214,1158,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618832,214,1033,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618833,214,1088,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618834,214,1130,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618835,214,1156,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618836,214,1056,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618837,214,1160,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618838,214,1187,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618839,214,1126,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618840,214,1037,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618841,214,1040,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618842,214,1063,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618843,214,1122,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618844,214,1045,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618845,214,1092,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618846,214,1132,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618847,214,1031,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618848,214,1178,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618849,214,1095,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618850,214,1123,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618851,214,1148,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618852,214,1051,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618853,214,1109,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618854,214,1055,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618855,214,1073,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618856,214,1188,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618857,214,1140,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618858,214,1163,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618859,214,1129,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618860,214,1175,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618861,214,1155,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618862,214,1101,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618863,214,1103,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618864,214,1096,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618865,214,1111,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618866,214,1060,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618867,214,1135,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618868,214,1121,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618869,214,1170,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618870,214,1043,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618871,214,1034,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618872,214,1195,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618873,214,1064,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618874,214,1026,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618875,214,1053,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618876,214,1154,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618877,214,1176,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618878,214,1035,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618879,214,1212,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618880,214,1153,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618881,214,1215,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618882,214,1030,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618883,214,1068,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618884,214,1167,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618885,214,1098,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618886,214,1216,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618887,214,1084,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618888,214,1220,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618889,214,1185,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618890,214,1222,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618891,214,1117,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618892,214,1169,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618893,214,1041,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618894,214,1211,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618895,214,1114,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618896,214,1058,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618897,214,1042,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618898,214,1044,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618899,214,1202,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618900,214,1118,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618901,214,1032,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618902,214,1079,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618903,214,1223,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618904,214,1206,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618905,214,1193,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618906,214,1210,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618907,214,1165,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618908,214,1207,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618909,214,1106,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618910,214,1029,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618911,214,1209,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618912,214,1191,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618913,214,1186,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618914,214,1182,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618915,214,1050,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618916,214,1038,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618917,214,1224,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618918,214,1061,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618919,214,1027,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618920,214,1159,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618921,214,1125,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618922,214,1075,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618923,214,1047,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618924,214,1112,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618925,214,1200,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618926,214,1119,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618927,214,1083,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618928,214,1085,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618929,214,1139,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618930,214,1144,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618931,214,1164,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618932,214,1205,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618933,214,1104,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618934,214,1074,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618935,214,1203,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618936,214,1093,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618937,214,1057,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618938,214,1162,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618939,214,1128,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618940,214,1028,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618941,214,1067,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618942,214,1219,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618943,214,1183,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618944,214,1090,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618945,214,1172,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618946,214,1173,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618947,214,1190,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618948,214,1166,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618949,214,1136,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618950,214,1217,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618951,214,1181,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618952,214,1214,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618953,214,1152,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618954,214,1204,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618955,214,1039,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618956,214,1036,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618957,214,1108,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618958,214,1149,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618959,214,1099,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618960,214,1082,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618961,214,1052,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618962,214,1196,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618963,214,1105,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618964,214,1180,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618965,214,1127,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618966,214,1131,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618967,214,1208,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618968,214,1059,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618969,214,1102,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618970,214,1110,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618971,214,1198,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618972,214,1192,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618973,214,1138,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618974,214,1194,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618975,214,1147,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618976,214,1143,14,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618977,214,1225,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618978,214,1080,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618979,214,1086,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618980,214,1133,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618981,214,1049,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618982,214,1124,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618983,214,1070,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618984,214,1066,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618985,214,1189,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618986,214,1072,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618987,214,1076,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618988,214,1115,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618989,214,1201,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618990,214,1134,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618991,214,1197,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618992,214,1097,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618993,214,1069,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618994,214,1062,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618995,214,1218,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618996,214,1065,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618997,214,1116,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618998,214,1221,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(618999,214,1113,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619000,214,1107,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619001,214,1046,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619002,214,1141,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619003,214,1091,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619004,214,1054,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619005,214,1142,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619006,214,1184,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619007,214,1071,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619008,214,1048,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619009,214,1174,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619010,214,1087,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619011,214,1145,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619012,214,1094,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619013,214,1137,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619014,214,1151,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619015,214,1089,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619016,214,1146,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619017,214,1100,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619018,214,1077,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619019,214,1150,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619020,214,1168,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619021,214,1081,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619022,214,1120,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619023,214,1078,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619024,214,1171,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619025,214,1157,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619026,214,1161,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619027,214,1158,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619028,214,1033,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619029,214,1088,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619030,214,1130,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619031,214,1156,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619032,214,1056,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619033,214,1160,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619034,214,1187,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619035,214,1126,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619036,214,1037,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619037,214,1040,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619038,214,1063,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619039,214,1122,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619040,214,1045,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619041,214,1092,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619042,214,1132,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619043,214,1031,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619044,214,1178,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619045,214,1095,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619046,214,1123,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619047,214,1051,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619048,214,1109,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619049,214,1055,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619050,214,1073,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619051,214,1188,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619052,214,1140,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619053,214,1163,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619054,214,1129,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619055,214,1175,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619056,214,1155,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619057,214,1103,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619058,214,1096,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619059,214,1111,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619060,214,1060,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619061,214,1135,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619062,214,1121,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619063,214,1170,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619064,214,1043,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619065,214,1034,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619066,214,1195,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619067,214,1064,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619068,214,1053,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619069,214,1154,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619070,214,1176,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619071,214,1035,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619072,214,1212,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619073,214,1153,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619074,214,1215,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619075,214,1030,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619076,214,1068,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619077,214,1167,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619078,214,1098,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619079,214,1216,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619080,214,1084,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619081,214,1220,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619082,214,1185,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619083,214,1222,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619084,214,1169,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619085,214,1041,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619086,214,1211,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619087,214,1114,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619088,214,1058,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619089,214,1042,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619090,214,1044,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619091,214,1202,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619092,214,1118,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619093,214,1032,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619094,214,1079,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619095,214,1223,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619096,214,1206,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619097,214,1210,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619098,214,1165,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619099,214,1207,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619100,214,1106,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619101,214,1029,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619102,214,1209,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619103,214,1191,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619104,214,1186,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619105,214,1182,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619106,214,1050,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619107,214,1038,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619108,214,1224,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619109,214,1061,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619110,214,1027,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619111,214,1159,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619112,214,1125,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619113,214,1075,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619114,214,1047,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619115,214,1112,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619116,214,1200,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619117,214,1119,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619118,214,1083,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619119,214,1085,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619120,214,1139,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619121,214,1144,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619122,214,1164,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619123,214,1205,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619124,214,1104,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619125,214,1074,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619126,214,1203,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619127,214,1093,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619128,214,1057,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619129,214,1162,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619130,214,1128,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619131,214,1028,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619132,214,1067,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619133,214,1219,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619134,214,1183,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619135,214,1090,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619136,214,1172,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619137,214,1173,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619138,214,1166,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619139,214,1136,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619140,214,1217,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619141,214,1181,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619142,214,1214,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619143,214,1152,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619144,214,1204,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619145,214,1039,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619146,214,1036,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619147,214,1108,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619148,214,1149,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619149,214,1099,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619150,214,1082,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619151,214,1052,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619152,214,1196,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619153,214,1105,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619154,214,1180,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619155,214,1127,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619156,214,1131,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619157,214,1059,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619158,214,1110,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619159,214,1198,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619160,214,1192,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619161,214,1138,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619162,214,1194,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619163,214,1147,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619164,214,1143,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619165,214,1544,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619166,214,1398,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619167,214,1405,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619168,214,1450,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619169,214,1368,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619170,214,1441,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619171,214,1390,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619172,214,1520,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619173,214,1386,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619174,214,1506,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619175,214,1391,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619176,214,1395,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619177,214,1432,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619178,214,1532,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619179,214,1522,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619180,214,1455,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619181,214,1518,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619182,214,1420,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619183,214,1387,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619184,214,1382,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619185,214,1537,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619186,214,1383,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619187,214,1434,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619188,214,1540,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619189,214,1431,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619190,214,1428,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619191,214,1365,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619192,214,1458,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619193,214,1410,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619194,214,1373,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619195,214,1460,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619196,214,1505,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619197,214,1389,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619198,214,1367,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619199,214,1496,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619200,214,1407,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619201,214,1498,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619202,214,1466,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619203,214,1411,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619204,214,1454,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619205,214,1465,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619206,214,1409,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619207,214,1467,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619208,214,1418,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619209,214,1397,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619210,214,1471,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619211,214,1485,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619212,214,1401,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619213,214,1438,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619214,214,1396,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619215,214,1488,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619216,214,1474,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619217,214,1484,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619218,214,1482,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619219,214,1352,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619220,214,1408,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619221,214,1451,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619222,214,1479,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619223,214,1375,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619224,214,1477,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619225,214,1504,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619226,214,1443,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619227,214,1356,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619228,214,1358,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619229,214,1381,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619230,214,1439,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619231,214,1364,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619232,214,1412,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619233,214,1453,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619234,214,1350,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619235,214,1495,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619236,214,1414,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619237,214,1444,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619238,214,1468,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619239,214,1370,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619240,214,1430,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619241,214,1374,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619242,214,1392,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619243,214,1509,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619244,214,1461,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619245,214,1480,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619246,214,1447,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619247,214,1492,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619248,214,1476,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619249,214,1423,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619250,214,1421,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619251,214,1417,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619252,214,1429,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619253,214,1380,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619254,214,1452,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619255,214,1442,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619256,214,1491,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619257,214,1362,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619258,214,1353,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619259,214,1515,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619260,214,1384,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619261,214,1345,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619262,214,1372,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619263,214,1470,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619264,214,1493,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619265,214,1354,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619266,214,1526,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619267,214,1473,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619268,214,1534,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619269,214,1349,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619270,214,1388,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619271,214,1489,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619272,214,1415,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619273,214,1535,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619274,214,1402,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619275,214,1539,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619276,214,1503,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619277,214,1541,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619278,214,1437,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619279,214,1486,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619280,214,1361,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619281,214,1531,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619282,214,1435,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619283,214,1377,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619284,214,1360,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619285,214,1363,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619286,214,1519,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619287,214,1436,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619288,214,1351,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619289,214,1399,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619290,214,1542,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619291,214,1527,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619292,214,1513,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619293,214,1530,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619294,214,1487,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619295,214,1528,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619296,214,1424,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619297,214,1347,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619298,214,1529,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619299,214,1511,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619300,214,1507,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619301,214,1500,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619302,214,1502,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619303,214,1369,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619304,214,1357,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619305,214,1543,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619306,214,1379,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619307,214,1346,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619308,214,1475,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619309,214,1446,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619310,214,1394,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619311,214,1366,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619312,214,1433,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619313,214,1517,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619314,214,1440,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619315,214,1403,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619316,214,1404,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619317,214,1456,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619318,214,1464,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619319,214,1481,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619320,214,1525,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619321,214,1425,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619322,214,1393,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619323,214,1521,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619324,214,1413,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619325,214,1376,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619326,214,1478,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619327,214,1448,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619328,214,1348,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619329,214,1385,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619330,214,1538,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619331,214,1501,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619332,214,1406,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619333,214,1494,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619334,214,1490,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619335,214,1508,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619336,214,1483,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619337,214,1457,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619338,214,1536,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619339,214,1499,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619340,214,1533,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619341,214,1472,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619342,214,1524,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619343,214,1359,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619344,214,1355,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619345,214,1426,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619346,214,1469,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619347,214,1416,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619348,214,1400,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619349,214,1371,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619350,214,1514,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619351,214,1422,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619352,214,1497,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619353,214,1445,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619354,214,1449,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619355,214,1523,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619356,214,1378,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619357,214,1419,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619358,214,1427,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619359,214,1516,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619360,214,1510,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619361,214,1459,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619362,214,1512,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619363,214,1462,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619364,214,1463,15,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619365,214,1544,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619366,214,1398,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619367,214,1405,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619368,214,1450,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619369,214,1368,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619370,214,1441,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619371,214,1390,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619372,214,1520,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619373,214,1386,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619374,214,1506,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619375,214,1391,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619376,214,1395,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619377,214,1432,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619378,214,1532,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619379,214,1522,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619380,214,1455,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619381,214,1518,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619382,214,1420,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619383,214,1387,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619384,214,1382,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619385,214,1537,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619386,214,1383,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619387,214,1434,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619388,214,1540,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619389,214,1431,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619390,214,1428,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619391,214,1365,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619392,214,1458,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619393,214,1410,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619394,214,1373,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619395,214,1460,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619396,214,1505,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619397,214,1389,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619398,214,1367,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619399,214,1496,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619400,214,1407,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619401,214,1498,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619402,214,1466,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619403,214,1411,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619404,214,1454,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619405,214,1465,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619406,214,1409,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619407,214,1467,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619408,214,1418,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619409,214,1397,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619410,214,1471,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619411,214,1485,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619412,214,1401,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619413,214,1438,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619414,214,1396,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619415,214,1488,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619416,214,1474,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619417,214,1484,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619418,214,1482,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619419,214,1352,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619420,214,1408,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619421,214,1451,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619422,214,1479,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619423,214,1375,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619424,214,1477,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619425,214,1504,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619426,214,1443,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619427,214,1356,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619428,214,1358,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619429,214,1381,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619430,214,1439,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619431,214,1364,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619432,214,1412,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619433,214,1453,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619434,214,1350,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619435,214,1495,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619436,214,1414,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619437,214,1444,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619438,214,1468,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619439,214,1370,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619440,214,1430,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619441,214,1374,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619442,214,1392,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619443,214,1509,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619444,214,1461,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619445,214,1480,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619446,214,1447,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619447,214,1492,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619448,214,1476,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619449,214,1423,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619450,214,1421,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619451,214,1417,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619452,214,1429,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619453,214,1380,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619454,214,1452,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619455,214,1442,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619456,214,1491,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619457,214,1362,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619458,214,1353,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619459,214,1515,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619460,214,1384,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619461,214,1345,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619462,214,1372,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619463,214,1470,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619464,214,1493,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619465,214,1354,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619466,214,1526,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619467,214,1473,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619468,214,1534,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619469,214,1349,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619470,214,1388,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619471,214,1489,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619472,214,1415,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619473,214,1535,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619474,214,1402,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619475,214,1539,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619476,214,1503,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619477,214,1541,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619478,214,1437,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619479,214,1486,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619480,214,1361,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619481,214,1531,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619482,214,1435,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619483,214,1377,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619484,214,1360,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619485,214,1363,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619486,214,1519,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619487,214,1436,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619488,214,1351,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619489,214,1399,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619490,214,1542,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619491,214,1527,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619492,214,1513,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619493,214,1530,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619494,214,1487,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619495,214,1528,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619496,214,1424,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619497,214,1347,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619498,214,1529,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619499,214,1511,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619500,214,1507,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619501,214,1500,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619502,214,1502,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619503,214,1369,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619504,214,1357,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619505,214,1543,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619506,214,1379,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619507,214,1346,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619508,214,1475,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619509,214,1446,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619510,214,1394,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619511,214,1366,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619512,214,1433,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619513,214,1517,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619514,214,1440,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619515,214,1403,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619516,214,1404,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619517,214,1456,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619518,214,1464,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619519,214,1481,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619520,214,1525,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619521,214,1425,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619522,214,1393,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619523,214,1521,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619524,214,1413,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619525,214,1376,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619526,214,1478,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619527,214,1448,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619528,214,1348,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619529,214,1385,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619530,214,1538,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619531,214,1501,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619532,214,1406,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619533,214,1494,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619534,214,1490,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619535,214,1508,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619536,214,1483,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619537,214,1457,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619538,214,1536,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619539,214,1499,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619540,214,1533,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619541,214,1472,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619542,214,1524,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619543,214,1359,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619544,214,1355,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619545,214,1426,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619546,214,1469,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619547,214,1416,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619548,214,1400,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619549,214,1371,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619550,214,1514,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619551,214,1422,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619552,214,1497,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619553,214,1445,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619554,214,1449,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619555,214,1523,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619556,214,1378,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619557,214,1419,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619558,214,1427,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619559,214,1516,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619560,214,1510,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619561,214,1459,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619562,214,1512,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619563,214,1462,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL),(619564,214,1463,16,1,-25,-25,NULL,'2016-04-14 17:06:36','2016-04-14 17:06:36',NULL);
/*!40000 ALTER TABLE `org_person_student_survey` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_person_student_survey_link`
--

DROP TABLE IF EXISTS `org_person_student_survey_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_person_student_survey_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `org_academic_year_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cohort` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `survey_link` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `survey_assigned_date` datetime DEFAULT NULL,
  `survey_completion_date` datetime DEFAULT NULL,
  `survey_completion_status` enum('Assigned','InProgress','CompletedMandatory','CompletedAll') COLLATE utf8_unicode_ci DEFAULT NULL,
  `survey_opt_out_status` enum('Yes','No') COLLATE utf8_unicode_ci DEFAULT NULL,
  `Has_Responses` enum('Yes','No') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No',
  `receivesurvey` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `IDX_E7BDE868DE12AB56` (`created_by`),
  KEY `IDX_E7BDE86825F94802` (`modified_by`),
  KEY `IDX_E7BDE8681F6FA0AF` (`deleted_by`),
  KEY `fk_org_person_student_survey_link_org_academic_year1_idx` (`org_academic_year_id`),
  KEY `fk_org_person_student_survey_link_organization1_idx` (`org_id`),
  KEY `fk_org_person_student_survey_link_person1_idx` (`person_id`),
  KEY `completion_status` (`survey_completion_status`),
  KEY `fk_org_person_student_survey_link_survey1_idx` (`survey_id`,`org_id`,`person_id`),
  CONSTRAINT `FK_E7BDE8681F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E7BDE868217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E7BDE86825F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E7BDE868B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_E7BDE868DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E7BDE868F3B0CE4A` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`),
  CONSTRAINT `FK_E7BDE868F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_person_student_survey_link`
--

LOCK TABLES `org_person_student_survey_link` WRITE;
/*!40000 ALTER TABLE `org_person_student_survey_link` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_person_student_survey_link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_person_student_year`
--

DROP TABLE IF EXISTS `org_person_student_year`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_person_student_year` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `org_academic_year_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_1B384A68DE12AB57` (`created_by`),
  KEY `FK_1B384A6825F94803` (`modified_by`),
  KEY `FK_1B384A681F6FA0B0` (`deleted_by`),
  KEY `FK_1B384A68FCA5FAC0` (`organization_id`),
  KEY `FK_1B384A68FCA5FAC1` (`org_academic_year_id`),
  KEY `IDX_person_org_year_del` (`person_id`,`organization_id`,`org_academic_year_id`,`deleted_at`),
  CONSTRAINT `FK_1B384A681F6FA0B0` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1B384A6825F94803` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1B384A68582DCD12` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1B384A68DE12AB57` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1B384A68FCA5FAC0` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_1B384A68FCA5FAC1` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_person_student_year`
--

LOCK TABLES `org_person_student_year` WRITE;
/*!40000 ALTER TABLE `org_person_student_year` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_person_student_year` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_question`
--

DROP TABLE IF EXISTS `org_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `question_type_id` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `cohort` int(11) DEFAULT NULL,
  `question_text` longtext COLLATE utf8_unicode_ci,
  `external_id` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `question_key` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CA58D9AD32C8A3DE` (`organization_id`),
  KEY `IDX_CA58D9ADF142426F` (`question_category_id`),
  KEY `IDX_CA58D9ADCB90598E` (`question_type_id`),
  KEY `fk_org_question_survey_id` (`survey_id`),
  CONSTRAINT `FK_CA58D9AD32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_CA58D9ADCB90598E` FOREIGN KEY (`question_type_id`) REFERENCES `question_type` (`id`),
  CONSTRAINT `FK_CA58D9ADF142426F` FOREIGN KEY (`question_category_id`) REFERENCES `question_category` (`id`),
  CONSTRAINT `fk_org_question_survey_id` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_question`
--

LOCK TABLES `org_question` WRITE;
/*!40000 ALTER TABLE `org_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_question_branch`
--

DROP TABLE IF EXISTS `org_question_branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_question_branch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `org_question_id` int(11) DEFAULT NULL,
  `survey_question_id` int(11) DEFAULT NULL,
  `ebi_question_options_id` int(11) DEFAULT NULL,
  `branch_type` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  KEY `survey_id` (`survey_id`),
  KEY `survey_question_id` (`survey_question_id`),
  KEY `ebi_question_options_id` (`ebi_question_options_id`),
  KEY `org_question_id` (`org_question_id`),
  CONSTRAINT `org_question_branch_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `org_question_branch_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `org_question_branch_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `org_question_branch_ibfk_4` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `org_question_branch_ibfk_5` FOREIGN KEY (`survey_question_id`) REFERENCES `survey_questions` (`id`),
  CONSTRAINT `org_question_branch_ibfk_6` FOREIGN KEY (`ebi_question_options_id`) REFERENCES `ebi_question_options` (`id`),
  CONSTRAINT `org_question_branch_ibfk_7` FOREIGN KEY (`org_question_id`) REFERENCES `org_question` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_question_branch`
--

LOCK TABLES `org_question_branch` WRITE;
/*!40000 ALTER TABLE `org_question_branch` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_question_branch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_question_options`
--

DROP TABLE IF EXISTS `org_question_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_question_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_question_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `option_name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `option_value` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E816D5D182ABAC59` (`org_question_id`),
  KEY `IDX_E816D5D132C8A3DE` (`organization_id`),
  CONSTRAINT `FK_E816D5D132C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_E816D5D182ABAC59` FOREIGN KEY (`org_question_id`) REFERENCES `org_question` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_question_options`
--

LOCK TABLES `org_question_options` WRITE;
/*!40000 ALTER TABLE `org_question_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_question_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_question_response`
--

DROP TABLE IF EXISTS `org_question_response`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_question_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `org_academic_year_id` int(11) DEFAULT NULL,
  `org_academic_terms_id` int(11) DEFAULT NULL,
  `org_question_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `response_type` enum('decimal','char','charmax') COLLATE utf8_unicode_ci DEFAULT NULL,
  `decimal_value` decimal(9,2) DEFAULT NULL,
  `char_value` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `charmax_value` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `org_question_options_id` int(11) DEFAULT NULL,
  `multi_response_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_response_key` (`org_id`,`survey_id`,`person_id`,`org_question_id`,`multi_response_id`),
  KEY `IDX_BC4BDC15DE12AB56` (`created_by`),
  KEY `IDX_BC4BDC1525F94802` (`modified_by`),
  KEY `IDX_BC4BDC151F6FA0AF` (`deleted_by`),
  KEY `fk_org_question_response_org_question1_idx` (`org_question_id`),
  KEY `fk_org_question_response_person1_idx` (`person_id`),
  KEY `fk_org_question_response_survey1_idx` (`survey_id`),
  KEY `fk_org_question_response_org_academic_year1_idx` (`org_academic_year_id`),
  KEY `fk_org_question_response_org_academic_terms1_idx` (`org_academic_terms_id`),
  KEY `modified` (`modified_at`,`created_at`),
  KEY `created` (`created_at`),
  KEY `fk_org_question_response_organization1_idx` (`org_id`,`person_id`,`org_question_id`,`modified_at`),
  KEY `latestsurvey` (`org_id`,`person_id`,`modified_at`,`survey_id`,`org_question_id`),
  KEY `org_question_options_id` (`org_question_options_id`),
  CONSTRAINT `FK_BC4BDC151F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_BC4BDC15217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_BC4BDC1525F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_BC4BDC1582ABAC59` FOREIGN KEY (`org_question_id`) REFERENCES `org_question` (`id`),
  CONSTRAINT `FK_BC4BDC158D7CC0D2` FOREIGN KEY (`org_academic_terms_id`) REFERENCES `org_academic_terms` (`id`),
  CONSTRAINT `FK_BC4BDC15B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_BC4BDC15DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_BC4BDC15F3B0CE4A` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`),
  CONSTRAINT `FK_BC4BDC15F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `org_question_response_ibfk_1` FOREIGN KEY (`org_question_options_id`) REFERENCES `org_question_options` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_question_response`
--

LOCK TABLES `org_question_response` WRITE;
/*!40000 ALTER TABLE `org_question_response` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_question_response` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_report_permissions`
--

DROP TABLE IF EXISTS `org_report_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_report_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `org_permissionset_id` int(11) DEFAULT NULL,
  `report_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `timeframe_all` tinyint(1) DEFAULT NULL,
  `current_calendar` tinyint(1) DEFAULT NULL,
  `previous_calendar` tinyint(1) DEFAULT NULL,
  `next_period` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D210DC54DE12AB56` (`created_by`),
  KEY `IDX_D210DC5425F94802` (`modified_by`),
  KEY `IDX_D210DC541F6FA0AF` (`deleted_by`),
  KEY `fk_org_report_permission_organization_id` (`organization_id`),
  KEY `fk_org_report_permission_report_id` (`report_id`),
  KEY `fk_org_report_permission_permissionset_id` (`org_permissionset_id`),
  CONSTRAINT `FK_D210DC541F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_D210DC5425F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_D210DC5432C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_D210DC544BD2A4C0` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`),
  CONSTRAINT `FK_D210DC547ABB76BC` FOREIGN KEY (`org_permissionset_id`) REFERENCES `org_permissionset` (`id`),
  CONSTRAINT `FK_D210DC54DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_report_permissions`
--

LOCK TABLES `org_report_permissions` WRITE;
/*!40000 ALTER TABLE `org_report_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_report_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_risk_group_model`
--

DROP TABLE IF EXISTS `org_risk_group_model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_risk_group_model` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `risk_model_id` int(11) DEFAULT NULL,
  `risk_group_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `assignment_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6B2BABA4DE12AB56` (`created_by`),
  KEY `IDX_6B2BABA425F94802` (`modified_by`),
  KEY `IDX_6B2BABA41F6FA0AF` (`deleted_by`),
  KEY `fk_orgriskmodel_riskmodelid` (`risk_model_id`),
  KEY `fk_org_risk_group_model_risk_group1_idx` (`risk_group_id`),
  KEY `fk_orgriskmodel_orgid` (`org_id`,`risk_group_id`,`risk_model_id`),
  CONSTRAINT `FK_6B2BABA4187D9A28` FOREIGN KEY (`risk_group_id`) REFERENCES `risk_group` (`id`),
  CONSTRAINT `FK_6B2BABA41F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_6B2BABA425F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_6B2BABA49F5CF488` FOREIGN KEY (`risk_model_id`) REFERENCES `risk_model_master` (`id`),
  CONSTRAINT `FK_6B2BABA4DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_6B2BABA4F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_risk_group_model`
--

LOCK TABLES `org_risk_group_model` WRITE;
/*!40000 ALTER TABLE `org_risk_group_model` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_risk_group_model` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_riskval_calc_inputs`
--

DROP TABLE IF EXISTS `org_riskval_calc_inputs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_riskval_calc_inputs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `is_riskval_calc_required` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `is_success_marker_calc_reqd` enum('y','n') COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_talking_point_calc_reqd` enum('y','n') COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_factor_calc_reqd` enum('y','n') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fk_org_riskval_calc_inputs_organization1_idx` (`org_id`,`person_id`),
  KEY `IDX_B95C1D80DE12AB56` (`created_by`),
  KEY `IDX_B95C1D8025F94802` (`modified_by`),
  KEY `IDX_B95C1D801F6FA0AF` (`deleted_by`),
  KEY `fk_org_riskval_calc_inputs_person1_idx` (`person_id`),
  KEY `stale_risk` (`is_riskval_calc_required`,`modified_at`),
  KEY `stale_smark` (`is_success_marker_calc_reqd`,`modified_at`),
  KEY `stale_tpoint` (`is_talking_point_calc_reqd`,`modified_at`),
  KEY `stale_factor` (`is_factor_calc_reqd`,`modified_at`),
  CONSTRAINT `FK_B95C1D801F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B95C1D80217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B95C1D8025F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B95C1D80DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_B95C1D80F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_riskval_calc_inputs`
--

LOCK TABLES `org_riskval_calc_inputs` WRITE;
/*!40000 ALTER TABLE `org_riskval_calc_inputs` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_riskval_calc_inputs` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`synapsemaster`@`%`*/ /*!50003 TRIGGER org_calc_update AFTER UPDATE ON org_riskval_calc_inputs
          FOR EACH ROW
          BEGIN
            UPDATE org_calc_flags_risk SET calculated_at = NULL, modified_at = CURRENT_TIMESTAMP WHERE org_id = NEW.org_id AND person_id = NEW.person_id;
            UPDATE org_calc_flags_talking_point SET calculated_at = NULL, modified_at = CURRENT_TIMESTAMP  WHERE org_id = NEW.org_id AND person_id = NEW.person_id;
          END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `org_saml_config`
--

DROP TABLE IF EXISTS `org_saml_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_saml_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `entity_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `federation_metadata` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sso_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `public_key_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logout_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_34537430DE12AB56` (`created_by`),
  KEY `IDX_3453743025F94802` (`modified_by`),
  KEY `IDX_345374301F6FA0AF` (`deleted_by`),
  KEY `fk_org_saml_config_organization1_idx` (`org_id`),
  CONSTRAINT `FK_345374301F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3453743025F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_34537430DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_34537430F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_saml_config`
--

LOCK TABLES `org_saml_config` WRITE;
/*!40000 ALTER TABLE `org_saml_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_saml_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_search`
--

DROP TABLE IF EXISTS `org_search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `query` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `json` varchar(3000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shared_on` datetime DEFAULT NULL,
  `from_sharedtab` int(11) DEFAULT NULL,
  `edited_by_me` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_17B29620DE12AB56` (`created_by`),
  KEY `IDX_17B2962025F94802` (`modified_by`),
  KEY `IDX_17B296201F6FA0AF` (`deleted_by`),
  KEY `IDX_17B2962032C8A3DE` (`organization_id`),
  KEY `IDX_17B29620217BBB47` (`person_id`),
  CONSTRAINT `FK_17B296201F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_17B29620217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_17B2962025F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_17B2962032C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_17B29620DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_search`
--

LOCK TABLES `org_search` WRITE;
/*!40000 ALTER TABLE `org_search` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_search` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_search_shared`
--

DROP TABLE IF EXISTS `org_search_shared`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_search_shared` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_search_id_source` int(11) DEFAULT NULL,
  `person_id_sharedby` int(11) DEFAULT NULL,
  `person_id_sharedwith` int(11) DEFAULT NULL,
  `org_search_id_dest` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fk_org_search_shared_unique` (`org_search_id_source`,`person_id_sharedby`,`person_id_sharedwith`,`org_search_id_dest`),
  KEY `IDX_49F4EBA8DE12AB56` (`created_by`),
  KEY `IDX_49F4EBA825F94802` (`modified_by`),
  KEY `IDX_49F4EBA81F6FA0AF` (`deleted_by`),
  KEY `IDX_49F4EBA8F459207D` (`org_search_id_source`),
  KEY `IDX_49F4EBA8BF1A33A5` (`person_id_sharedby`),
  KEY `IDX_49F4EBA85EA2D51A` (`person_id_sharedwith`),
  KEY `IDX_49F4EBA887A20C50` (`org_search_id_dest`),
  CONSTRAINT `FK_49F4EBA81F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_49F4EBA825F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_49F4EBA85EA2D51A` FOREIGN KEY (`person_id_sharedwith`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_49F4EBA887A20C50` FOREIGN KEY (`org_search_id_dest`) REFERENCES `org_search` (`id`),
  CONSTRAINT `FK_49F4EBA8BF1A33A5` FOREIGN KEY (`person_id_sharedby`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_49F4EBA8DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_49F4EBA8F459207D` FOREIGN KEY (`org_search_id_source`) REFERENCES `org_search` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_search_shared`
--

LOCK TABLES `org_search_shared` WRITE;
/*!40000 ALTER TABLE `org_search_shared` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_search_shared` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_search_shared_by`
--

DROP TABLE IF EXISTS `org_search_shared_by`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_search_shared_by` (
  `org_search_id` int(11) NOT NULL,
  `org_search_id_source` int(11) NOT NULL,
  `person_id_shared_by` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `shared_on` datetime DEFAULT NULL,
  PRIMARY KEY (`org_search_id`,`org_search_id_source`,`person_id_shared_by`),
  KEY `IDX_36826091DE12AB56` (`created_by`),
  KEY `IDX_3682609125F94802` (`modified_by`),
  KEY `IDX_368260911F6FA0AF` (`deleted_by`),
  KEY `fk_org_search_shared_by_org_search1_idx` (`org_search_id`),
  KEY `fk_org_search_shared_by_org_search2_idx` (`org_search_id_source`),
  KEY `fk_org_search_shared_by_person1_idx` (`person_id_shared_by`),
  CONSTRAINT `FK_368260911F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3682609125F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_368260915C787CFB` FOREIGN KEY (`org_search_id`) REFERENCES `org_search` (`id`),
  CONSTRAINT `FK_36826091D85DD618` FOREIGN KEY (`person_id_shared_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_36826091DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_36826091F459207D` FOREIGN KEY (`org_search_id_source`) REFERENCES `org_search` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `org_search_id_dest` int(11) NOT NULL,
  `person_id_sharedwith` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `shared_on` datetime DEFAULT NULL,
  PRIMARY KEY (`org_search_id`,`org_search_id_dest`,`person_id_sharedwith`),
  KEY `IDX_17EAAF3BDE12AB56` (`created_by`),
  KEY `IDX_17EAAF3B25F94802` (`modified_by`),
  KEY `IDX_17EAAF3B1F6FA0AF` (`deleted_by`),
  KEY `fk_org_search_shared_org_search1_idx` (`org_search_id`),
  KEY `fk_org_search_shared_org_search2` (`org_search_id_dest`),
  KEY `fk_org_search_shared_person2` (`person_id_sharedwith`),
  CONSTRAINT `FK_17EAAF3B1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_17EAAF3B25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_17EAAF3B5C787CFB` FOREIGN KEY (`org_search_id`) REFERENCES `org_search` (`id`),
  CONSTRAINT `FK_17EAAF3B5EA2D51A` FOREIGN KEY (`person_id_sharedwith`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_17EAAF3B87A20C50` FOREIGN KEY (`org_search_id_dest`) REFERENCES `org_search` (`id`),
  CONSTRAINT `FK_17EAAF3BDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_search_shared_with`
--

LOCK TABLES `org_search_shared_with` WRITE;
/*!40000 ALTER TABLE `org_search_shared_with` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_search_shared_with` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_static_list`
--

DROP TABLE IF EXISTS `org_static_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_static_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `name` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `person_id_shared_by` int(11) DEFAULT NULL,
  `shared_on` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_43EAFB4BDE12AB56` (`created_by`),
  KEY `IDX_43EAFB4B25F94802` (`modified_by`),
  KEY `IDX_43EAFB4B1F6FA0AF` (`deleted_by`),
  KEY `fk_org_staticlist_organization1_idx` (`organization_id`),
  KEY `fk_staticlist_person1_idx` (`person_id`),
  CONSTRAINT `FK_43EAFB4B1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_43EAFB4B217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_43EAFB4B25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_43EAFB4B32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_43EAFB4BDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `org_static_list_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5A73DC8FDE12AB56` (`created_by`),
  KEY `IDX_5A73DC8F25F94802` (`modified_by`),
  KEY `IDX_5A73DC8F1F6FA0AF` (`deleted_by`),
  KEY `fk_org_staticlist_organization1_idx` (`organization_id`),
  KEY `fk_staticlist_person1_idx` (`person_id`),
  KEY `fk_staticlist_org_static_list_id1_idx` (`org_static_list_id`),
  CONSTRAINT `FK_5A73DC8F1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_5A73DC8F217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_5A73DC8F25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_5A73DC8F32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_5A73DC8FAD199442` FOREIGN KEY (`org_static_list_id`) REFERENCES `org_static_list` (`id`),
  CONSTRAINT `FK_5A73DC8FDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `year_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `cohort_code` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `last_accessed_on` datetime DEFAULT NULL,
  `student_id` int(11) NOT NULL,
  PRIMARY KEY (`org_id`,`person_id`,`student_id`,`survey_id`,`year_id`,`cohort_code`),
  KEY `IDX_3B610FEDDE12AB56` (`created_by`),
  KEY `IDX_3B610FED25F94802` (`modified_by`),
  KEY `IDX_3B610FED1F6FA0AF` (`deleted_by`),
  KEY `IDX_3B610FEDF4837C1B` (`org_id`),
  KEY `IDX_3B610FED217BBB47` (`person_id`),
  KEY `IDX_3B610FEDB3FE509D` (`survey_id`),
  KEY `IDX_3B610FED40C1FEA7` (`year_id`),
  KEY `IDX_3B610FEDCB944F1A` (`student_id`),
  CONSTRAINT `FK_3B610FED1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3B610FED217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3B610FED25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3B610FED40C1FEA7` FOREIGN KEY (`year_id`) REFERENCES `year` (`id`),
  CONSTRAINT `FK_3B610FEDB3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_3B610FEDCB944F1A` FOREIGN KEY (`student_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3B610FEDDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3B610FEDF4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_survey_report_access_history`
--

LOCK TABLES `org_survey_report_access_history` WRITE;
/*!40000 ALTER TABLE `org_survey_report_access_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_survey_report_access_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_talking_points`
--

DROP TABLE IF EXISTS `org_talking_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_talking_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `talking_points_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `org_academic_year_id` int(11) DEFAULT NULL,
  `org_academic_terms_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `response` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `source_modified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_861DCA16DE12AB56` (`created_by`),
  KEY `IDX_861DCA1625F94802` (`modified_by`),
  KEY `IDX_861DCA161F6FA0AF` (`deleted_by`),
  KEY `IDX_861DCA1632C8A3DE` (`organization_id`),
  KEY `IDX_861DCA16217BBB47` (`person_id`),
  KEY `IDX_861DCA16CDC12E8B` (`talking_points_id`),
  KEY `IDX_861DCA16B3FE509D` (`survey_id`),
  KEY `OPTM` (`organization_id`,`person_id`,`talking_points_id`,`modified_at`),
  KEY `org_academic_year_id` (`org_academic_year_id`),
  KEY `org_academic_terms_id` (`org_academic_terms_id`),
  KEY `source_modified_at` (`source_modified_at`),
  CONSTRAINT `FK_861DCA161F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_861DCA16217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_861DCA1625F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_861DCA1632C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_861DCA16B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_861DCA16CDC12E8B` FOREIGN KEY (`talking_points_id`) REFERENCES `talking_points` (`id`),
  CONSTRAINT `FK_861DCA16DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `org_talking_points_ibfk_1` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`),
  CONSTRAINT `org_talking_points_ibfk_2` FOREIGN KEY (`org_academic_terms_id`) REFERENCES `org_academic_terms` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_talking_points`
--

LOCK TABLES `org_talking_points` WRITE;
/*!40000 ALTER TABLE `org_talking_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `org_talking_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_users`
--

DROP TABLE IF EXISTS `org_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EACE568BDE12AB56` (`created_by`),
  KEY `IDX_EACE568B25F94802` (`modified_by`),
  KEY `IDX_EACE568B1F6FA0AF` (`deleted_by`),
  KEY `fk_organization_tier_users_organization1_idx` (`organization_id`),
  KEY `fk_organization_tier_users_person1_idx` (`person_id`),
  CONSTRAINT `FK_EACE568B1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_EACE568B217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_EACE568B25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_EACE568B32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_EACE568BDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `subdomain` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_organization_id` int(11) DEFAULT NULL,
  `time_zone` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logo_file_name` longtext COLLATE utf8_unicode_ci,
  `primary_color` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `secondary_color` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ebi_confidentiality_statement` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_confidentiality_statement` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inactivity_timeout` int(11) DEFAULT NULL,
  `ftp_user` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ftp_password` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ftp_home` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `academic_update_notification` tinyint(1) DEFAULT NULL,
  `refer_for_academic_assistance` tinyint(1) DEFAULT NULL,
  `send_to_student` tinyint(1) DEFAULT NULL,
  `campus_id` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tier` enum('0','1','2','3') COLLATE utf8_unicode_ci DEFAULT NULL,
  `external_id` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pcs` enum('G','E') COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_ldap_saml_enabled` tinyint(1) DEFAULT NULL,
  `can_view_in_progress_grade` tinyint(1) DEFAULT NULL,
  `can_view_absences` tinyint(1) DEFAULT NULL,
  `can_view_comments` tinyint(1) DEFAULT NULL,
  `old_timezone` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization`
--

LOCK TABLES `organization` WRITE;
/*!40000 ALTER TABLE `organization` DISABLE KEYS */;
INSERT INTO `organization` VALUES (-2,NULL,NULL,NULL,NULL,NULL,NULL,'ART user',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(-1,NULL,NULL,NULL,NULL,NULL,NULL,'Ebi User Org',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
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
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `organization_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nick_name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_800BAFF32C8A3DE` (`organization_id`),
  KEY `IDX_800BAFFB213FA4` (`lang_id`),
  CONSTRAINT `FK_800BAFF32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_800BAFFB213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_lang`
--

LOCK TABLES `organization_lang` WRITE;
/*!40000 ALTER TABLE `organization_lang` DISABLE KEYS */;
INSERT INTO `organization_lang` VALUES (1,-1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ebi User Org','Ebi User Org',NULL);
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
  `role_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6E60B4F7D60322AC` (`role_id`),
  KEY `IDX_6E60B4F7217BBB47` (`person_id`),
  KEY `IDX_6E60B4F732C8A3DE` (`organization_id`),
  CONSTRAINT `FK_6E60B4F7217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_6E60B4F732C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_6E60B4F7D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_role`
--

LOCK TABLES `organization_role` WRITE;
/*!40000 ALTER TABLE `organization_role` DISABLE KEYS */;
INSERT INTO `organization_role` VALUES (1,1,1,-1,NULL,NULL,NULL,NULL,NULL,NULL);
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
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `firstname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `external_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `username` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activation_token` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `confidentiality_stmt_accept_date` datetime DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `token_expiry_date` datetime DEFAULT NULL,
  `welcome_email_sent_date` date DEFAULT NULL,
  `risk_level` int(11) DEFAULT NULL,
  `risk_update_date` datetime DEFAULT NULL,
  `intent_to_leave` int(11) DEFAULT NULL,
  `intent_to_leave_update_date` datetime DEFAULT NULL,
  `last_contact_date` datetime DEFAULT NULL,
  `last_activity` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `record_type` enum('home','master','both') COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_username` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_locked` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  PRIMARY KEY (`id`),
  KEY `IDX_34DCD17632C8A3DE` (`organization_id`),
  KEY `IDX_34DCD176DE12AB56` (`created_by`),
  KEY `IDX_34DCD17625F94802` (`modified_by`),
  KEY `IDX_34DCD1761F6FA0AF` (`deleted_by`),
  KEY `IDX_34DCD176CB20FE3A` (`intent_to_leave`),
  KEY `fk_person_risk_level1_idx` (`risk_level`),
  KEY `username_idx` (`username`),
  KEY `deleted_at_idx` (`deleted_at`),
  KEY `firstname_idx` (`firstname`(15)),
  KEY `lastname_idx` (`lastname`(15)),
  KEY `activation_token_idx` (`activation_token`(8)),
  CONSTRAINT `FK_34DCD1761F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_34DCD17625F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_34DCD17632C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_34DCD176CB20FE3A` FOREIGN KEY (`intent_to_leave`) REFERENCES `intent_to_leave` (`id`),
  CONSTRAINT `FK_34DCD176DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_34DCD176EB88056C` FOREIGN KEY (`risk_level`) REFERENCES `risk_level` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person`
--

LOCK TABLES `person` WRITE;
/*!40000 ALTER TABLE `person` DISABLE KEYS */;
INSERT INTO `person` VALUES (-86,NULL,NULL,NULL,NULL,NULL,NULL,'EBIZEN','3041',NULL,NULL,'ebizen3041remediation',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-25,NULL,'2016-06-16 19:48:22',NULL,'2016-06-16 19:48:22',NULL,NULL,'Migration','Scripts',NULL,NULL,'skyfactor.migration_scripts','hai.deng@macmillan.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-24,NULL,'2016-06-16 19:48:22',NULL,'2016-06-16 19:48:22',NULL,NULL,'TalkingPointCalc','StoredProcedure',NULL,NULL,'skyfactor.sproc.talking_point_calc','joshua.stark@macmillan.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-23,NULL,'2016-06-16 19:48:22',NULL,'2016-06-16 19:48:22',NULL,NULL,'SurveyDataTransfer','StoredProcedure',NULL,NULL,'skyfactor.sproc.survey_data_transfer','joshua.oryall@macmillan.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-22,NULL,'2016-06-16 19:48:22',NULL,'2016-06-16 19:48:22',NULL,NULL,'SuccessMarkerCalc','StoredProcedure',NULL,NULL,'skyfactor.sproc.success_marker_calc','joshua.stark@macmillan.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-20,NULL,'2016-06-16 19:48:22',NULL,'2016-06-16 19:48:22',NULL,NULL,'ReportCalc','StoredProcedure',NULL,NULL,'skyfactor.sproc.report_calc','joshua.stark@macmillan.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-19,NULL,'2016-06-16 19:48:22',NULL,'2016-06-16 19:48:22',NULL,NULL,'OrgRiskFactorCalculation','StoredProcedure',NULL,NULL,'skyfactor.sproc.org_risk_factor_calculation','joshua.stark@macmillan.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-18,NULL,'2016-06-16 19:48:22',NULL,'2016-06-16 19:48:22',NULL,NULL,'IssuesCalcTempTables','StoredProcedure',NULL,NULL,'skyfactor.sproc.issues_calc_temp_tables','joshua.stark@macmillan.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-17,NULL,'2016-06-16 19:48:22',NULL,'2016-06-16 19:48:22',NULL,NULL,'ISQDataTransfer','StoredProcedure',NULL,NULL,'skyfactor.sproc.isq_data_transfer','joshua.oryall@macmillan.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-16,NULL,'2016-06-16 19:48:22',NULL,'2016-06-16 19:48:22',NULL,NULL,'IntentLeaveNullFixer','StoredProcedure',NULL,NULL,'skyfactor.sproc.intent_leave','joshua.stark@macmillan.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-15,NULL,'2016-06-16 19:48:22',NULL,'2016-06-16 19:48:22',NULL,NULL,'IntentLeaveCalc','StoredProcedure',NULL,NULL,'skyfactor.sproc.intent_leave_calc','joshua.stark@macmillan.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-14,NULL,'2016-06-16 19:48:22',NULL,'2016-06-16 19:48:22',NULL,NULL,'IntentLeaveCalcAll','StoredProcedure',NULL,NULL,'skyfactor.sproc.intent_leave_calc_all','joshua.stark@macmillan.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-13,NULL,'2016-06-16 19:48:22',NULL,'2016-06-16 19:48:22',NULL,NULL,'FixDatumSrcTs','StoredProcedure',NULL,NULL,'skyfactor.sproc.fix_datum_src_ts','joshua.stark@macmillan.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-11,NULL,'2016-06-16 19:48:22',NULL,'2016-06-16 19:48:22',NULL,NULL,'FactorCalc','StoredProcedure',NULL,NULL,'skyfactor.sproc.factor_calc','joshua.stark@macmillan.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-10,NULL,'2016-06-16 19:48:22',NULL,'2016-06-16 19:48:22',NULL,NULL,'Talend','User',NULL,NULL,'skyfactor.joryall','joshua.oryall@macmillan.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-6,NULL,NULL,NULL,NULL,NULL,NULL,'Hai','Deng','Data Migration Engineer',NULL,'hdeng1','haizi.deng4@mailinator.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(-5,NULL,NULL,NULL,NULL,NULL,NULL,'Hai','Deng',NULL,NULL,'hdeng2','haizi.deng5@mailinator.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y'),(1,NULL,NULL,NULL,'2015-06-23 20:06:41',NULL,NULL,'Ramesh','Kumhar','Mr',NULL,'111111','ramesh.kumhar@techmahindra.com','$2y$13$mGqsFSsF550QR8.MR9Y85u1wmlN0js6vRi.UkqLE.9x5gBCC8ucfe','0d7bb70f71f58f0966429e41411d8b36','2015-02-17 12:01:02',1,NULL,'2015-02-17',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'y');
/*!40000 ALTER TABLE `person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `person_MD_talking_points_calculated`
--

DROP TABLE IF EXISTS `person_MD_talking_points_calculated`;
/*!50001 DROP VIEW IF EXISTS `person_MD_talking_points_calculated`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `person_MD_talking_points_calculated` (
  `org_id` tinyint NOT NULL,
  `person_id` tinyint NOT NULL,
  `talking_points_id` tinyint NOT NULL,
  `ebi_metadata_id` tinyint NOT NULL,
  `org_academic_year_id` tinyint NOT NULL,
  `org_academic_terms_id` tinyint NOT NULL,
  `response` tinyint NOT NULL,
  `source_modified_at` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `person_contact_info`
--

DROP TABLE IF EXISTS `person_contact_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_contact_info` (
  `personcontactinfoid` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `status` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`personcontactinfoid`),
  KEY `IDX_7853E5217BBB47` (`person_id`),
  KEY `IDX_7853E5E7A1254A` (`contact_id`),
  CONSTRAINT `FK_7853E5217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_7853E5E7A1254A` FOREIGN KEY (`contact_id`) REFERENCES `contact_info` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_contact_info`
--

LOCK TABLES `person_contact_info` WRITE;
/*!40000 ALTER TABLE `person_contact_info` DISABLE KEYS */;
INSERT INTO `person_contact_info` VALUES (1,1,1,'A',NULL,NULL,NULL,NULL,NULL,NULL),(2,2,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `ebi_metadata_id` int(11) DEFAULT NULL,
  `org_academic_year_id` int(11) DEFAULT NULL,
  `org_academic_terms_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `metadata_value` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8ABD58A3DE12AB56` (`created_by`),
  KEY `IDX_8ABD58A325F94802` (`modified_by`),
  KEY `IDX_8ABD58A31F6FA0AF` (`deleted_by`),
  KEY `IDX_8ABD58A3217BBB47` (`person_id`),
  KEY `IDX_8ABD58A3BB49FE75` (`ebi_metadata_id`),
  KEY `IDX_8ABD58A3F3B0CE4A` (`org_academic_year_id`),
  KEY `IDX_8ABD58A38D7CC0D2` (`org_academic_terms_id`),
  KEY `PEM_Person_ebimetaperson` (`ebi_metadata_id`,`person_id`),
  KEY `JC_8ABD58A3217BBB47` (`person_id`,`ebi_metadata_id`),
  CONSTRAINT `FK_8ABD58A31F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8ABD58A3217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8ABD58A325F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8ABD58A38D7CC0D2` FOREIGN KEY (`org_academic_terms_id`) REFERENCES `org_academic_terms` (`id`),
  CONSTRAINT `FK_8ABD58A3BB49FE75` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`),
  CONSTRAINT `FK_8ABD58A3DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8ABD58A3F3B0CE4A` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_ebi_metadata`
--

LOCK TABLES `person_ebi_metadata` WRITE;
/*!40000 ALTER TABLE `person_ebi_metadata` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_ebi_metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_entity`
--

DROP TABLE IF EXISTS `person_entity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_entity` (
  `Person_id` int(11) NOT NULL,
  `Entity_id` int(11) NOT NULL,
  PRIMARY KEY (`Person_id`,`Entity_id`),
  KEY `IDX_928D74DEA38A39E4` (`Person_id`),
  KEY `IDX_928D74DE3D4FFFE` (`Entity_id`),
  CONSTRAINT `FK_928D74DE3D4FFFE` FOREIGN KEY (`Entity_id`) REFERENCES `entity` (`id`),
  CONSTRAINT `FK_928D74DEA38A39E4` FOREIGN KEY (`Person_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_entity`
--

LOCK TABLES `person_entity` WRITE;
/*!40000 ALTER TABLE `person_entity` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_entity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_factor_calculated`
--

DROP TABLE IF EXISTS `person_factor_calculated`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_factor_calculated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `factor_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `mean_value` decimal(13,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `org_person_factor_uniq_idx` (`organization_id`,`person_id`,`survey_id`,`factor_id`,`modified_at`),
  KEY `IDX_E03AD201DE12AB56` (`created_by`),
  KEY `IDX_E03AD20125F94802` (`modified_by`),
  KEY `IDX_E03AD2011F6FA0AF` (`deleted_by`),
  KEY `fk_person_factor_calculated_person1_idx` (`person_id`),
  KEY `fk_person_factor_calculated_organization_idx` (`organization_id`),
  KEY `fk_person_factor_calculated_factor1_idx` (`factor_id`),
  KEY `survey_id` (`survey_id`),
  KEY `modified` (`modified_at`,`created_at`),
  CONSTRAINT `FK_E03AD2011F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E03AD201217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E03AD20125F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E03AD20132C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_E03AD201BC88C1A3` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`),
  CONSTRAINT `FK_E03AD201DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `person_factor_calculated_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_factor_calculated`
--

LOCK TABLES `person_factor_calculated` WRITE;
/*!40000 ALTER TABLE `person_factor_calculated` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_factor_calculated` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_metadata`
--

DROP TABLE IF EXISTS `person_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) DEFAULT NULL,
  `metadata_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `metadata_value` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DB529123A38A39E4` (`person_id`),
  KEY `IDX_DB5291235A02668E` (`metadata_id`),
  CONSTRAINT `FK_DB5291235A02668E` FOREIGN KEY (`metadata_id`) REFERENCES `metadata_list_values` (`id`),
  CONSTRAINT `FK_DB529123A38A39E4` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_DB529123DC9EE959` FOREIGN KEY (`metadata_id`) REFERENCES `metadata_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_metadata`
--

LOCK TABLES `person_metadata` WRITE;
/*!40000 ALTER TABLE `person_metadata` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_org_metadata`
--

DROP TABLE IF EXISTS `person_org_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_org_metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `org_metadata_id` int(11) DEFAULT NULL,
  `org_academic_year_id` int(11) DEFAULT NULL,
  `org_academic_periods_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `metadata_value` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D0B544BADE12AB56` (`created_by`),
  KEY `IDX_D0B544BA25F94802` (`modified_by`),
  KEY `IDX_D0B544BA1F6FA0AF` (`deleted_by`),
  KEY `IDX_D0B544BA217BBB47` (`person_id`),
  KEY `IDX_D0B544BA4012B3BF` (`org_metadata_id`),
  KEY `IDX_D0B544BAF3B0CE4A` (`org_academic_year_id`),
  KEY `IDX_D0B544BADF88FD95` (`org_academic_periods_id`),
  KEY `JC_D0B544BA217BBB47` (`person_id`,`org_metadata_id`),
  CONSTRAINT `FK_D0B544BA1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_D0B544BA217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_D0B544BA25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_D0B544BA4012B3BF` FOREIGN KEY (`org_metadata_id`) REFERENCES `org_metadata` (`id`),
  CONSTRAINT `FK_D0B544BADE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_D0B544BADF88FD95` FOREIGN KEY (`org_academic_periods_id`) REFERENCES `org_academic_terms` (`id`),
  CONSTRAINT `FK_D0B544BAF3B0CE4A` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_org_metadata`
--

LOCK TABLES `person_org_metadata` WRITE;
/*!40000 ALTER TABLE `person_org_metadata` DISABLE KEYS */;
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
  `date_captured` datetime NOT NULL,
  `risk_level` int(11) DEFAULT NULL,
  `risk_model_id` int(11) DEFAULT NULL,
  `risk_score` decimal(6,4) DEFAULT NULL,
  `weighted_value` decimal(9,4) DEFAULT NULL,
  `maximum_weight_value` decimal(9,4) DEFAULT NULL,
  PRIMARY KEY (`person_id`,`date_captured`),
  KEY `fk_person_risk_level_history_risk_model_master1_idx` (`risk_model_id`),
  KEY `fk_person_risk_level_history_risk_level1_idx` (`risk_level`),
  KEY `captured` (`date_captured`),
  CONSTRAINT `FK_FB00ACB9217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_FB00ACB99F5CF488` FOREIGN KEY (`risk_model_id`) REFERENCES `risk_model_master` (`id`),
  CONSTRAINT `FK_FB00ACB9EB88056C` FOREIGN KEY (`risk_level`) REFERENCES `risk_level` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_risk_level_history`
--

LOCK TABLES `person_risk_level_history` WRITE;
/*!40000 ALTER TABLE `person_risk_level_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_risk_level_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `person_survey_talking_points_calculated`
--

DROP TABLE IF EXISTS `person_survey_talking_points_calculated`;
/*!50001 DROP VIEW IF EXISTS `person_survey_talking_points_calculated`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `person_survey_talking_points_calculated` (
  `org_id` tinyint NOT NULL,
  `person_id` tinyint NOT NULL,
  `talking_points_id` tinyint NOT NULL,
  `ebi_question_id` tinyint NOT NULL,
  `survey_id` tinyint NOT NULL,
  `response` tinyint NOT NULL,
  `source_modified_at` tinyint NOT NULL
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `proxy_log_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `action` enum('insert','update','delete') COLLATE utf8_unicode_ci DEFAULT NULL,
  `resource` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `json_text_old` varchar(4000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `json_text_new` varchar(4000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4B645554DE12AB56` (`created_by`),
  KEY `IDX_4B64555425F94802` (`modified_by`),
  KEY `IDX_4B6455541F6FA0AF` (`deleted_by`),
  KEY `fk_proxy_audit_proxy_log1_idx` (`proxy_log_id`),
  CONSTRAINT `FK_4B6455541F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_4B64555425F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_4B645554AF617D59` FOREIGN KEY (`proxy_log_id`) REFERENCES `proxy_log` (`id`),
  CONSTRAINT `FK_4B645554DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `ebi_users_id` int(11) DEFAULT NULL,
  `person_id_proxied_for` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `login_date_time` datetime DEFAULT NULL,
  `logoff_date_time` datetime DEFAULT NULL,
  `organization_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7582DEABDE12AB56` (`created_by`),
  KEY `IDX_7582DEAB25F94802` (`modified_by`),
  KEY `IDX_7582DEAB1F6FA0AF` (`deleted_by`),
  KEY `fk_proxy_log_person2_idx` (`person_id_proxied_for`),
  KEY `fk_proxy_log_ebi_users1_idx` (`ebi_users_id`),
  KEY `fk_proxy_log_organization1_idx` (`organization_id`),
  KEY `fk_proxy_log_person1_idx` (`person_id`),
  CONSTRAINT `FK_7582DEAB1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_7582DEAB217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_7582DEAB25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_7582DEAB32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_7582DEAB411438C7` FOREIGN KEY (`ebi_users_id`) REFERENCES `ebi_users` (`id`),
  CONSTRAINT `FK_7582DEABCF46066F` FOREIGN KEY (`person_id_proxied_for`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_7582DEABDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proxy_log`
--

LOCK TABLES `proxy_log` WRITE;
/*!40000 ALTER TABLE `proxy_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `proxy_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_bank`
--

DROP TABLE IF EXISTS `question_bank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_bank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intro_text` longtext,
  `text` longtext NOT NULL,
  `question_type` varchar(15) NOT NULL,
  `on_success_marker_page` tinyint(4) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_question_bank_created_by` (`created_by`),
  KEY `fk_question_bank_modified_by` (`modified_by`),
  KEY `fk_question_bank_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_question_bank_created_by` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_question_bank_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_question_bank_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_bank`
--

LOCK TABLES `question_bank` WRITE;
/*!40000 ALTER TABLE `question_bank` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_bank` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_bank_map`
--

DROP TABLE IF EXISTS `question_bank_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_bank_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `question_bank_id` int(11) NOT NULL,
  `ebi_question_id` int(11) DEFAULT NULL,
  `survey_question_id` int(11) DEFAULT NULL,
  `external_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qbm_unique` (`survey_id`,`question_bank_id`),
  KEY `fk_question_bank_map_question_bank_id` (`question_bank_id`),
  KEY `fk_question_bank_map_ebi_question_id` (`ebi_question_id`),
  KEY `fk_question_bank_map_survey_question_id` (`survey_question_id`),
  KEY `fk_question_bank_map_created_by` (`created_by`),
  KEY `fk_question_bank_map_modified_by` (`modified_by`),
  KEY `fk_question_bank_map_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_question_bank_map_created_by` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_question_bank_map_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_question_bank_map_ebi_question_id` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`),
  CONSTRAINT `fk_question_bank_map_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_question_bank_map_question_bank_id` FOREIGN KEY (`question_bank_id`) REFERENCES `question_bank` (`id`),
  CONSTRAINT `fk_question_bank_map_survey_id` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `fk_question_bank_map_survey_question_id` FOREIGN KEY (`survey_question_id`) REFERENCES `survey_questions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_bank_map`
--

LOCK TABLES `question_bank_map` WRITE;
/*!40000 ALTER TABLE `question_bank_map` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_bank_map` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_category`
--

DROP TABLE IF EXISTS `question_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_category`
--

LOCK TABLES `question_category` WRITE;
/*!40000 ALTER TABLE `question_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_category_lang`
--

DROP TABLE IF EXISTS `question_category_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_category_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_category_id` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_22D33462F142426F` (`question_category_id`),
  KEY `IDX_22D33462B213FA4` (`lang_id`),
  CONSTRAINT `FK_22D33462B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_22D33462F142426F` FOREIGN KEY (`question_category_id`) REFERENCES `question_category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `id` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_type`
--

LOCK TABLES `question_type` WRITE;
/*!40000 ALTER TABLE `question_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_type_lang`
--

DROP TABLE IF EXISTS `question_type_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_type_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_type_id` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EFD305F1B213FA4` (`lang_id`),
  KEY `FK_EFD305F1CB90598E` (`question_type_id`),
  CONSTRAINT `FK_EFD305F1B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_EFD305F1CB90598E` FOREIGN KEY (`question_type_id`) REFERENCES `question_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_type_lang`
--

LOCK TABLES `question_type_lang` WRITE;
/*!40000 ALTER TABLE `question_type_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_type_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referral_history`
--

DROP TABLE IF EXISTS `referral_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referral_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referral_id` int(11) NOT NULL,
  `action` enum('create','update','close','reopen','reassign','interested party') COLLATE utf8_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `person_id_assigned_to` int(11) DEFAULT NULL,
  `activity_category_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `status` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_leaving` tinyint(1) DEFAULT NULL,
  `is_discussed` tinyint(1) DEFAULT NULL,
  `referrer_permission` tinyint(1) DEFAULT NULL,
  `is_high_priority` tinyint(1) DEFAULT NULL,
  `notify_student` tinyint(1) DEFAULT NULL,
  `access_private` tinyint(1) DEFAULT NULL,
  `access_public` tinyint(1) DEFAULT NULL,
  `access_team` tinyint(1) DEFAULT NULL,
  `is_reason_routed` tinyint(1) DEFAULT NULL,
  `user_key` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_referral_history_referral_id` (`referral_id`),
  KEY `fk_referral_history_created_by` (`created_by`),
  KEY `fk_referral_history_person_id_assigned_to` (`person_id_assigned_to`),
  KEY `fk_referral_history_activity_category_id` (`activity_category_id`),
  CONSTRAINT `fk_referral_history_activity_category_id` FOREIGN KEY (`activity_category_id`) REFERENCES `activity_category` (`id`),
  CONSTRAINT `fk_referral_history_created_by` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_referral_history_person_id_assigned_to` FOREIGN KEY (`person_id_assigned_to`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_referral_history_referral_id` FOREIGN KEY (`referral_id`) REFERENCES `referrals` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referral_history`
--

LOCK TABLES `referral_history` WRITE;
/*!40000 ALTER TABLE `referral_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `referral_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referral_routing_rules`
--

DROP TABLE IF EXISTS `referral_routing_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referral_routing_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `activity_category_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `is_primary_coordinator` tinyint(1) DEFAULT NULL,
  `is_primary_campus_connection` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D954FA99DE12AB56` (`created_by`),
  KEY `IDX_D954FA9925F94802` (`modified_by`),
  KEY `IDX_D954FA991F6FA0AF` (`deleted_by`),
  KEY `fk_referral_routing_rules_activity_category_id_idx` (`activity_category_id`),
  KEY `fk_referral_routing_rules_organization_id_idx` (`organization_id`),
  KEY `fk_referral_routing_rules_person_id_idx` (`person_id`),
  CONSTRAINT `FK_D954FA991CC8F7EE` FOREIGN KEY (`activity_category_id`) REFERENCES `activity_category` (`id`),
  CONSTRAINT `FK_D954FA991F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_D954FA99217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_D954FA9925F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_D954FA9932C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_D954FA99DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `person_id_faculty` int(11) DEFAULT NULL,
  `person_id_student` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `activity_category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `status` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_leaving` tinyint(1) DEFAULT NULL,
  `is_discussed` tinyint(1) DEFAULT NULL,
  `referrer_permission` tinyint(1) DEFAULT NULL,
  `is_high_priority` tinyint(1) DEFAULT NULL,
  `notify_student` tinyint(1) DEFAULT NULL,
  `access_private` tinyint(1) DEFAULT NULL,
  `access_public` tinyint(1) DEFAULT NULL,
  `access_team` tinyint(1) DEFAULT NULL,
  `referral_date` datetime DEFAULT NULL,
  `person_id_assigned_to` int(11) DEFAULT NULL,
  `is_reason_routed` tinyint(1) DEFAULT '0',
  `user_key` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1B7DC896FFB0AA26` (`person_id_faculty`),
  KEY `IDX_1B7DC8965F056556` (`person_id_student`),
  KEY `IDX_1B7DC89632C8A3DE` (`organization_id`),
  KEY `IDX_1B7DC8961CC8F7EE` (`activity_category_id`),
  KEY `IDX_1B7DC89674215258` (`person_id_assigned_to`),
  CONSTRAINT `FK_1B7DC8961CC8F7EE` FOREIGN KEY (`activity_category_id`) REFERENCES `activity_category` (`id`),
  CONSTRAINT `FK_1B7DC89632C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_1B7DC8965F056556` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1B7DC89674215258` FOREIGN KEY (`person_id_assigned_to`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1B7DC896FFB0AA26` FOREIGN KEY (`person_id_faculty`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `referrals_id` int(11) DEFAULT NULL,
  `referral_history_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `user_key` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2C4E0EEBB24851AE` (`referrals_id`),
  KEY `IDX_2C4E0EEB217BBB47` (`person_id`),
  KEY `fk_referrals_interested_parties_referral_history_id` (`referral_history_id`),
  CONSTRAINT `FK_2C4E0EEB217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2C4E0EEBB24851AE` FOREIGN KEY (`referrals_id`) REFERENCES `referrals` (`id`),
  CONSTRAINT `fk_referrals_interested_parties_referral_history_id` FOREIGN KEY (`referral_history_id`) REFERENCES `referral_history` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `referrals_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `Teams_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8786AC502F403D44` (`Teams_id`),
  KEY `IDX_8786AC50B24851AE` (`referrals_id`),
  CONSTRAINT `FK_8786AC502F403D44` FOREIGN KEY (`Teams_id`) REFERENCES `Teams` (`id`),
  CONSTRAINT `FK_8786AC50B24851AE` FOREIGN KEY (`referrals_id`) REFERENCES `referrals` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referrals_teams`
--

LOCK TABLES `referrals_teams` WRITE;
/*!40000 ALTER TABLE `referrals_teams` DISABLE KEYS */;
/*!40000 ALTER TABLE `referrals_teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `related_activities`
--

DROP TABLE IF EXISTS `related_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `related_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `activity_log_id` int(11) DEFAULT NULL,
  `contacts_id` int(11) DEFAULT NULL,
  `note_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `referral_id` int(11) DEFAULT NULL,
  `email_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3F3CA755DE12AB56` (`created_by`),
  KEY `IDX_3F3CA75525F94802` (`modified_by`),
  KEY `IDX_3F3CA7551F6FA0AF` (`deleted_by`),
  KEY `IDX_3F3CA75532C8A3DE` (`organization_id`),
  KEY `IDX_3F3CA755B811BD86` (`activity_log_id`),
  KEY `IDX_3F3CA755719FB48E` (`contacts_id`),
  KEY `IDX_3F3CA75526ED0855` (`note_id`),
  KEY `IDX_3F3CA755E5B533F9` (`appointment_id`),
  KEY `IDX_3F3CA7553CCAA4B7` (`referral_id`),
  KEY `IDX_3F3CA755A832C1C9` (`email_id`),
  CONSTRAINT `FK_3F3CA7551F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3F3CA75525F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3F3CA75526ED0855` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`),
  CONSTRAINT `FK_3F3CA75532C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_3F3CA7553CCAA4B7` FOREIGN KEY (`referral_id`) REFERENCES `referrals` (`id`),
  CONSTRAINT `FK_3F3CA755719FB48E` FOREIGN KEY (`contacts_id`) REFERENCES `contacts` (`id`),
  CONSTRAINT `FK_3F3CA755A832C1C9` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`),
  CONSTRAINT `FK_3F3CA755B811BD86` FOREIGN KEY (`activity_log_id`) REFERENCES `activity_log` (`id`),
  CONSTRAINT `FK_3F3CA755DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3F3CA755E5B533F9` FOREIGN KEY (`appointment_id`) REFERENCES `Appointments` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `related_activities`
--

LOCK TABLES `related_activities` WRITE;
/*!40000 ALTER TABLE `related_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `related_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_bucket_range`
--

DROP TABLE IF EXISTS `report_bucket_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_bucket_range` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `element_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `value` decimal(8,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F214B74BDE12AB56` (`created_by`),
  KEY `IDX_F214B74B25F94802` (`modified_by`),
  KEY `IDX_F214B74B1F6FA0AF` (`deleted_by`),
  KEY `fk_report_element_buckets_report_elements1_idx` (`element_id`),
  CONSTRAINT `FK_F214B74B1F1F2A24` FOREIGN KEY (`element_id`) REFERENCES `report_element_buckets` (`id`),
  CONSTRAINT `FK_F214B74B1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_F214B74B25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_F214B74BDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_bucket_range`
--

LOCK TABLES `report_bucket_range` WRITE;
/*!40000 ALTER TABLE `report_bucket_range` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_bucket_range` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_calc_history`
--

DROP TABLE IF EXISTS `report_calc_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_calc_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `report_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_23CCB636DE12AB56` (`created_by`),
  KEY `IDX_23CCB63625F94802` (`modified_by`),
  KEY `IDX_23CCB6361F6FA0AF` (`deleted_by`),
  KEY `fk_report_calc_history_org1_idx` (`org_id`),
  KEY `fk_report_calc_history_survey1_idx` (`survey_id`),
  KEY `fk_report_calc_history_reports1_idx` (`report_id`),
  KEY `fk_report_calc_history_person1_idx` (`person_id`),
  CONSTRAINT `FK_23CCB6361F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_23CCB636217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_23CCB63625F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_23CCB6364BD2A4C0` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`),
  CONSTRAINT `FK_23CCB636B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_23CCB636DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_23CCB636F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_calc_history`
--

LOCK TABLES `report_calc_history` WRITE;
/*!40000 ALTER TABLE `report_calc_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_calc_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_calculated_values`
--

DROP TABLE IF EXISTS `report_calculated_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_calculated_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) NOT NULL,
  `person_id` int(11) DEFAULT NULL,
  `report_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `element_bucket_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `element_id` int(11) DEFAULT NULL,
  `calculated_value` decimal(8,4) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_key` (`org_id`,`person_id`,`survey_id`,`report_id`,`section_id`,`element_id`),
  KEY `IDX_CB8DFCDCDE12AB56` (`created_by`),
  KEY `IDX_CB8DFCDC25F94802` (`modified_by`),
  KEY `IDX_CB8DFCDC1F6FA0AF` (`deleted_by`),
  KEY `IDX_CB8DFCDCF4837C1B` (`org_id`),
  KEY `fk_report_calculated_values_reports1_idx` (`report_id`),
  KEY `fk_report_calculated_values_report_sections1_idx` (`section_id`),
  KEY `fk_report_calculated_values_report_element_buckets1_idx` (`element_bucket_id`),
  KEY `fk_report_calculated_values_person1_idx` (`person_id`),
  KEY `IDX_CB8DFCDC1F1F2A24` (`element_id`),
  KEY `IDX_CB8DFCDCB3FE509D` (`survey_id`),
  CONSTRAINT `FK_CB8DFCDC1F1F2A24` FOREIGN KEY (`element_id`) REFERENCES `report_section_elements` (`id`),
  CONSTRAINT `FK_CB8DFCDC1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_CB8DFCDC217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_CB8DFCDC25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_CB8DFCDC2CC57666` FOREIGN KEY (`element_bucket_id`) REFERENCES `report_element_buckets` (`id`),
  CONSTRAINT `FK_CB8DFCDC4BD2A4C0` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`),
  CONSTRAINT `FK_CB8DFCDCB3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_CB8DFCDCD823E37A` FOREIGN KEY (`section_id`) REFERENCES `report_sections` (`id`),
  CONSTRAINT `FK_CB8DFCDCDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_CB8DFCDCF4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_calculated_values`
--

LOCK TABLES `report_calculated_values` WRITE;
/*!40000 ALTER TABLE `report_calculated_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_calculated_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_element_buckets`
--

DROP TABLE IF EXISTS `report_element_buckets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_element_buckets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `element_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `bucket_name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bucket_text` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `range_min` decimal(8,4) DEFAULT NULL,
  `range_max` decimal(8,4) DEFAULT NULL,
  `is_choices` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FD533799DE12AB56` (`created_by`),
  KEY `IDX_FD53379925F94802` (`modified_by`),
  KEY `IDX_FD5337991F6FA0AF` (`deleted_by`),
  KEY `fk_report_element_buckets_report_elements1_idx` (`element_id`),
  CONSTRAINT `FK_FD5337991F1F2A24` FOREIGN KEY (`element_id`) REFERENCES `report_section_elements` (`id`),
  CONSTRAINT `FK_FD5337991F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_FD53379925F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_FD533799DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_element_buckets`
--

LOCK TABLES `report_element_buckets` WRITE;
/*!40000 ALTER TABLE `report_element_buckets` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_element_buckets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_run_details`
--

DROP TABLE IF EXISTS `report_run_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_run_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `report_instance_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `survey_qnbr` int(11) DEFAULT NULL,
  `response_json` longtext COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9B5CD0A2DE12AB56` (`created_by`),
  KEY `IDX_9B5CD0A225F94802` (`modified_by`),
  KEY `IDX_9B5CD0A21F6FA0AF` (`deleted_by`),
  KEY `IDX_9B5CD0A28B49D915` (`report_instance_id`),
  KEY `IDX_9B5CD0A2D823E37A` (`section_id`),
  KEY `IDX_9B5CD0A21E27F6BF` (`question_id`),
  KEY `IDX_9B5CD0A2217BBB47` (`person_id`),
  CONSTRAINT `FK_9B5CD0A21E27F6BF` FOREIGN KEY (`question_id`) REFERENCES `survey_questions` (`id`),
  CONSTRAINT `FK_9B5CD0A21F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9B5CD0A2217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9B5CD0A225F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9B5CD0A28B49D915` FOREIGN KEY (`report_instance_id`) REFERENCES `reports_running_status` (`id`),
  CONSTRAINT `FK_9B5CD0A2D823E37A` FOREIGN KEY (`section_id`) REFERENCES `report_sections` (`id`),
  CONSTRAINT `FK_9B5CD0A2DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_run_details`
--

LOCK TABLES `report_run_details` WRITE;
/*!40000 ALTER TABLE `report_run_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_run_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_section_elements`
--

DROP TABLE IF EXISTS `report_section_elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_section_elements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `factor_id` int(11) DEFAULT NULL,
  `question_bank_id` int(11) DEFAULT NULL,
  `survey_question_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `source_type` enum('F','Q','E') COLLATE utf8_unicode_ci DEFAULT NULL,
  `icon_file_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `ebi_question_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_91D6E5F5DE12AB56` (`created_by`),
  KEY `IDX_91D6E5F525F94802` (`modified_by`),
  KEY `IDX_91D6E5F51F6FA0AF` (`deleted_by`),
  KEY `fk_report_elements_report_sections1_idx` (`section_id`),
  KEY `fk_report_elements_factor1_idx` (`factor_id`),
  KEY `fk_report_elements_survey_questions1_idx` (`survey_question_id`),
  KEY `IDX_91D6E5F5B3FE509D` (`survey_id`),
  KEY `IDX_91D6E5F579F0E193` (`ebi_question_id`),
  KEY `fk_report_section_elements_question_bank_id` (`question_bank_id`),
  CONSTRAINT `FK_91D6E5F51F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_91D6E5F525F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_91D6E5F579F0E193` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`),
  CONSTRAINT `FK_91D6E5F5A6DF29BA` FOREIGN KEY (`survey_question_id`) REFERENCES `survey_questions` (`id`),
  CONSTRAINT `FK_91D6E5F5B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_91D6E5F5BC88C1A3` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`),
  CONSTRAINT `FK_91D6E5F5D823E37A` FOREIGN KEY (`section_id`) REFERENCES `report_sections` (`id`),
  CONSTRAINT `FK_91D6E5F5DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_report_section_elements_question_bank_id` FOREIGN KEY (`question_bank_id`) REFERENCES `question_bank` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_section_elements`
--

LOCK TABLES `report_section_elements` WRITE;
/*!40000 ALTER TABLE `report_section_elements` DISABLE KEYS */;
INSERT INTO `report_section_elements` VALUES (1,NULL,NULL,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,'Purpose','Mapworks is a holistic approach to student success and retention, providing a platform of information that faculty and staff use to identify at-risk students early in the term. It also allows faculty and staff the ability to coordinate interventions with at-risk students by providing the power of real-time analytics, strategic communications, and differentiated user interfacing, with integrated statistical testing and outcomes reporting.',NULL,NULL,NULL,NULL),(2,NULL,NULL,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,'Rationale',NULL,NULL,NULL,NULL,NULL),(3,NULL,NULL,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,'Process','The Mapworks process includes combining data from the institution with information from the students. Using that information, Mapworks uses real-time analytics to provide information directly to the students as well as to the faculty and staff working with the students.',NULL,NULL,NULL,NULL),(4,NULL,NULL,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,'Graphic',NULL,NULL,NULL,NULL,NULL),(5,NULL,NULL,NULL,11,NULL,NULL,NULL,NULL,NULL,NULL,'Referrals',NULL,NULL,NULL,NULL,NULL),(6,NULL,NULL,NULL,11,NULL,NULL,NULL,NULL,NULL,NULL,'Appointments',NULL,NULL,NULL,NULL,NULL),(7,NULL,NULL,NULL,11,NULL,NULL,NULL,NULL,NULL,NULL,'Contacts',NULL,NULL,NULL,NULL,NULL),(8,NULL,NULL,NULL,11,NULL,NULL,NULL,NULL,NULL,NULL,'Interaction Contacts',NULL,NULL,NULL,NULL,NULL),(9,NULL,NULL,NULL,11,NULL,NULL,NULL,NULL,NULL,NULL,'Notes',NULL,NULL,NULL,NULL,NULL),(10,NULL,NULL,NULL,11,NULL,NULL,NULL,NULL,NULL,NULL,'Academic Updates',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `report_section_elements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_sections`
--

DROP TABLE IF EXISTS `report_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `report_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` smallint(6) DEFAULT NULL,
  `section_query` longtext COLLATE utf8_unicode_ci,
  `retention_tracking_type` enum('required','optional','none') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2BF6DAE5DE12AB56` (`created_by`),
  KEY `IDX_2BF6DAE525F94802` (`modified_by`),
  KEY `IDX_2BF6DAE51F6FA0AF` (`deleted_by`),
  KEY `fk_sections_reports1_idx` (`report_id`),
  CONSTRAINT `FK_2BF6DAE51F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2BF6DAE525F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2BF6DAE54BD2A4C0` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`),
  CONSTRAINT `FK_2BF6DAE5DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_sections`
--

LOCK TABLES `report_sections` WRITE;
/*!40000 ALTER TABLE `report_sections` DISABLE KEYS */;
INSERT INTO `report_sections` VALUES (1,NULL,NULL,NULL,1,NULL,NULL,NULL,'Earning the Grades You Want',1,NULL,NULL),(2,NULL,NULL,NULL,1,NULL,NULL,NULL,'Connecting with Others',2,NULL,NULL),(3,NULL,NULL,NULL,1,NULL,NULL,NULL,'Paying for College',3,NULL,NULL),(4,NULL,NULL,NULL,16,NULL,NULL,NULL,'What is Mapworks?',1,NULL,'none'),(5,NULL,NULL,NULL,16,NULL,NULL,NULL,'Risk Profile',2,NULL,'optional'),(6,NULL,NULL,NULL,16,NULL,NULL,NULL,'GPA by Risk',3,NULL,'optional'),(7,NULL,NULL,NULL,16,NULL,NULL,NULL,'Intent to Leave and Persistence',4,NULL,'required'),(8,NULL,NULL,NULL,16,NULL,NULL,NULL,'Persistence and Retention by Risk',5,NULL,'required'),(9,NULL,NULL,NULL,16,NULL,NULL,NULL,'Top Factors with Correlation to Persistence and Retention',6,NULL,'required'),(10,NULL,NULL,NULL,16,NULL,NULL,NULL,'Top Factors with Correlation to GPA',7,NULL,'optional'),(11,NULL,NULL,NULL,16,NULL,NULL,NULL,'Activity Overview',8,NULL,'optional');
/*!40000 ALTER TABLE `report_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_tips`
--

DROP TABLE IF EXISTS `report_tips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_tips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `title` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1700ACF3DE12AB56` (`created_by`),
  KEY `IDX_1700ACF325F94802` (`modified_by`),
  KEY `IDX_1700ACF31F6FA0AF` (`deleted_by`),
  KEY `fk_report_tips_report_sections1_idx` (`section_id`),
  CONSTRAINT `FK_1700ACF31F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1700ACF325F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1700ACF3D823E37A` FOREIGN KEY (`section_id`) REFERENCES `report_sections` (`id`),
  CONSTRAINT `FK_1700ACF3DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_tips`
--

LOCK TABLES `report_tips` WRITE;
/*!40000 ALTER TABLE `report_tips` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_tips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_batch_job` enum('y','n') COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_coordinator_report` enum('y','n') COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`),
  KEY `IDX_F11FA745DE12AB56` (`created_by`),
  KEY `IDX_F11FA74525F94802` (`modified_by`),
  KEY `IDX_F11FA7451F6FA0AF` (`deleted_by`),
  CONSTRAINT `FK_F11FA7451F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_F11FA74525F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_F11FA745DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'student-report','Student success report - Survey',NULL,NULL,'SUR-SSR','n'),(2,NULL,NULL,NULL,NULL,NULL,NULL,'Individual Response Report','See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions','y','n','SUR-IRR','n'),(3,NULL,NULL,NULL,NULL,NULL,NULL,'All Academic Updates Report','See all academic updates for your students.  Export to csv, perform individual or bulk actions','y','n','AU-R','n'),(6,NULL,NULL,NULL,NULL,NULL,NULL,'Group Response Report','Compare survey response rates for different groups.  Export to csv','y',NULL,'SUR-GRR','y'),(7,NULL,NULL,NULL,NULL,NULL,NULL,'Our Mapworks Activity','View statistics on faculty and student activity tracked in Mapworks for a given date range.  Export to pdf','y','y','MAR','n'),(8,NULL,NULL,NULL,NULL,NULL,NULL,'Survey Snapshot Report','See aggregated responses to all survey questions for a given survey and cohort.  Drill down to see individual students','y','n','SUR-SR','y'),(9,NULL,NULL,NULL,NULL,NULL,NULL,'Our Students Report','See Top Five issues, high-level survey data and demographics for a single survey and cohort.. Export to pdf','n','n','OSR','n'),(10,NULL,NULL,NULL,NULL,NULL,NULL,'Survey Factor Reports','See aggregated values of all survey factors for a given survey and cohort.  Drill down to see individual students','n','n','SUR-FR','n'),(11,NULL,NULL,NULL,NULL,NULL,NULL,'Profile Snapshot Report','See aggregated profile data for a given student population and academic year. Drill down to see individual students and export to csv.','y','n','PRO-SR','y'),(12,NULL,NULL,NULL,NULL,NULL,NULL,'Faculty/Staff Usage Report','Identify faculty/staff members and their activity. Export to csv.','y','y','FUR','n'),(13,NULL,NULL,NULL,NULL,NULL,NULL,'Persistence and Retention Report','View persistence and retention by retention tracking group and by risk.  Export to csv, print to pdf.','y','y','PRR','n'),(14,NULL,NULL,NULL,NULL,NULL,NULL,'Completion Report','View completion rates of one to six years by retention tracking group and by risk.  Export to csv, print to pdf.','y','y','CR','n'),(15,NULL,NULL,NULL,NULL,NULL,NULL,'GPA Report','See aggregate view of GPA by term and by risk.  Can print to pdf, and download the data table as CSV','n','y','GPA','y'),(16,NULL,NULL,NULL,NULL,NULL,NULL,'Executive Summary Report','See key statistics on effectiveness: persistence/retention, GPA, activity, and more. Print to pdf.','y','y','EXEC','y');
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_running_status`
--

DROP TABLE IF EXISTS `reports_running_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_running_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `report_id` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `is_viewed` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT NULL,
  `filtered_student_ids` longtext COLLATE utf8_unicode_ci,
  `filter_criteria` longtext COLLATE utf8_unicode_ci,
  `report_custom_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('Q','IP','C','F') COLLATE utf8_unicode_ci DEFAULT NULL,
  `response_json` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_80023648DE12AB56` (`created_by`),
  KEY `IDX_8002364825F94802` (`modified_by`),
  KEY `IDX_800236481F6FA0AF` (`deleted_by`),
  KEY `IDX_800236484BD2A4C0` (`report_id`),
  KEY `IDX_80023648F4837C1B` (`org_id`),
  KEY `IDX_80023648217BBB47` (`person_id`),
  CONSTRAINT `FK_800236481F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_80023648217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8002364825F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_800236484BD2A4C0` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`),
  CONSTRAINT `FK_80023648DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_80023648F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_running_status`
--

LOCK TABLES `reports_running_status` WRITE;
/*!40000 ALTER TABLE `reports_running_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_running_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_template`
--

DROP TABLE IF EXISTS `reports_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `report_id` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `filter_criteria` longtext COLLATE utf8_unicode_ci NOT NULL,
  `template_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C275D4A5DE12AB56` (`created_by`),
  KEY `IDX_C275D4A525F94802` (`modified_by`),
  KEY `IDX_C275D4A51F6FA0AF` (`deleted_by`),
  KEY `IDX_C275D4A5F4837C1B` (`org_id`),
  KEY `fk_report_templates_reports1_idx` (`report_id`),
  KEY `fk_report_templates_person1_idx` (`person_id`),
  CONSTRAINT `FK_C275D4A51F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_C275D4A5217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_C275D4A525F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_C275D4A54BD2A4C0` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`),
  CONSTRAINT `FK_C275D4A5DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_C275D4A5F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_template`
--

LOCK TABLES `reports_template` WRITE;
/*!40000 ALTER TABLE `reports_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_calc_tracking_table`
--

DROP TABLE IF EXISTS `risk_calc_tracking_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_calc_tracking_table` (
  `person_id` int(11) NOT NULL,
  `last_update_ts` datetime DEFAULT NULL,
  `most_recent_survey_question_id` int(11) DEFAULT NULL,
  `last_seen_survey_question_id` int(11) DEFAULT NULL,
  `survey_id` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  PRIMARY KEY (`org_id`,`person_id`,`survey_id`),
  KEY `org_person` (`org_id`,`person_id`),
  KEY `person` (`person_id`),
  KEY `survey_id` (`survey_id`),
  KEY `last_seen` (`last_seen_survey_question_id`),
  KEY `most_recent` (`most_recent_survey_question_id`),
  KEY `last_update` (`last_update_ts`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_calc_tracking_table`
--

LOCK TABLES `risk_calc_tracking_table` WRITE;
/*!40000 ALTER TABLE `risk_calc_tracking_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_calc_tracking_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_calc_tracking_table_ISQ`
--

DROP TABLE IF EXISTS `risk_calc_tracking_table_ISQ`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_calc_tracking_table_ISQ` (
  `person_id` int(11) NOT NULL,
  `last_update_ts` datetime DEFAULT NULL,
  `most_recent_org_question_id` int(11) DEFAULT NULL,
  `last_seen_org_question_id` int(11) DEFAULT NULL,
  `survey_id` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  PRIMARY KEY (`org_id`,`person_id`,`survey_id`),
  KEY `org_person` (`org_id`,`person_id`),
  KEY `person` (`person_id`),
  KEY `survey_id` (`survey_id`),
  KEY `last_seen` (`last_seen_org_question_id`),
  KEY `most_recent` (`most_recent_org_question_id`),
  KEY `last_update` (`last_update_ts`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_calc_tracking_table_ISQ`
--

LOCK TABLES `risk_calc_tracking_table_ISQ` WRITE;
/*!40000 ALTER TABLE `risk_calc_tracking_table_ISQ` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_calc_tracking_table_ISQ` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_group`
--

DROP TABLE IF EXISTS `risk_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `risk_group_key` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1CA28DBADE12AB56` (`created_by`),
  KEY `IDX_1CA28DBA25F94802` (`modified_by`),
  KEY `IDX_1CA28DBA1F6FA0AF` (`deleted_by`),
  CONSTRAINT `FK_1CA28DBA1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1CA28DBA25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1CA28DBADE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_group`
--

LOCK TABLES `risk_group` WRITE;
/*!40000 ALTER TABLE `risk_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_group_lang`
--

DROP TABLE IF EXISTS `risk_group_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_group_lang` (
  `lang_id` int(11) NOT NULL,
  `risk_group_id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`lang_id`,`risk_group_id`),
  KEY `fk_risk_group_lang_risk_group1_idx` (`risk_group_id`),
  KEY `fk_risk_group_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `FK_15E35E79187D9A28` FOREIGN KEY (`risk_group_id`) REFERENCES `risk_group` (`id`),
  CONSTRAINT `FK_15E35E79B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `person_id` int(11) DEFAULT NULL,
  `risk_group_id` int(11) DEFAULT NULL,
  `assignment_date` datetime DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fk_risk_group_person_history_person1_idx` (`person_id`,`risk_group_id`),
  KEY `fk_risk_group_person_history_risk_group1_idx` (`risk_group_id`),
  CONSTRAINT `FK_72A1565C187D9A28` FOREIGN KEY (`risk_group_id`) REFERENCES `risk_group` (`id`),
  CONSTRAINT `FK_72A1565C217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_group_person_history`
--

LOCK TABLES `risk_group_person_history` WRITE;
/*!40000 ALTER TABLE `risk_group_person_history` DISABLE KEYS */;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `risk_text` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `color_hex` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `report_sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5FE16A7DDE12AB56` (`created_by`),
  KEY `IDX_5FE16A7D25F94802` (`modified_by`),
  KEY `IDX_5FE16A7D1F6FA0AF` (`deleted_by`),
  CONSTRAINT `FK_5FE16A7D1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_5FE16A7D25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_5FE16A7DDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_level`
--

LOCK TABLES `risk_level` WRITE;
/*!40000 ALTER TABLE `risk_level` DISABLE KEYS */;
INSERT INTO `risk_level` VALUES (2,NULL,NULL,NULL,NULL,NULL,NULL,'gray','risk-level-icon-gray.png','#cccccc',5);
/*!40000 ALTER TABLE `risk_level` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_model_levels`
--

DROP TABLE IF EXISTS `risk_model_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_model_levels` (
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `risk_model_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `risk_level` int(11) NOT NULL,
  `min` decimal(6,4) DEFAULT NULL,
  `max` decimal(6,4) DEFAULT NULL,
  PRIMARY KEY (`risk_model_id`,`risk_level`),
  KEY `IDX_A07B4CECDE12AB56` (`created_by`),
  KEY `IDX_A07B4CEC25F94802` (`modified_by`),
  KEY `IDX_A07B4CEC1F6FA0AF` (`deleted_by`),
  KEY `fk_risk_model_levels_risk_level1_idx` (`risk_level`,`risk_model_id`),
  CONSTRAINT `FK_A07B4CEC1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_A07B4CEC25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_A07B4CEC9F5CF488` FOREIGN KEY (`risk_model_id`) REFERENCES `risk_model_master` (`id`),
  CONSTRAINT `FK_A07B4CECDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_A07B4CECEB88056C` FOREIGN KEY (`risk_level`) REFERENCES `risk_level` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_model_levels`
--

LOCK TABLES `risk_model_levels` WRITE;
/*!40000 ALTER TABLE `risk_model_levels` DISABLE KEYS */;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `model_state` enum('Archived','Assigned','Unassigned','InProcess') COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `calculation_start_date` datetime DEFAULT NULL,
  `calculation_end_date` datetime DEFAULT NULL,
  `enrollment_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_12588B23DE12AB56` (`created_by`),
  KEY `IDX_12588B2325F94802` (`modified_by`),
  KEY `IDX_12588B231F6FA0AF` (`deleted_by`),
  CONSTRAINT `FK_12588B231F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_12588B2325F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_12588B23DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_model_master`
--

LOCK TABLES `risk_model_master` WRITE;
/*!40000 ALTER TABLE `risk_model_master` DISABLE KEYS */;
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
  KEY `fk_risk_model_bucket_risk_variable1_idx` (`risk_variable_id`),
  CONSTRAINT `FK_D73A24E3296E76DF` FOREIGN KEY (`risk_variable_id`) REFERENCES `risk_variable` (`id`),
  CONSTRAINT `FK_D73A24E39F5CF488` FOREIGN KEY (`risk_model_id`) REFERENCES `risk_model_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_model_weights`
--

LOCK TABLES `risk_model_weights` WRITE;
/*!40000 ALTER TABLE `risk_model_weights` DISABLE KEYS */;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `ebi_metadata_id` int(11) DEFAULT NULL,
  `org_metadata_id` int(11) DEFAULT NULL,
  `ebi_question_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `org_question_id` int(11) DEFAULT NULL,
  `survey_questions_id` int(11) DEFAULT NULL,
  `factor_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `risk_b_variable` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `variable_type` enum('continuous','categorical') COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_calculated` tinyint(1) DEFAULT NULL,
  `calc_type` enum('Most Recent','Sum','Average','Count','Academic Update') COLLATE utf8_unicode_ci DEFAULT NULL,
  `calculation_start_date` datetime DEFAULT NULL,
  `calculation_end_date` datetime DEFAULT NULL,
  `is_archived` tinyint(1) DEFAULT NULL,
  `source` enum('profile','surveyquestion','surveyfactor','isp','isq','questionbank') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2A64C4E2DE12AB56` (`created_by`),
  KEY `IDX_2A64C4E225F94802` (`modified_by`),
  KEY `IDX_2A64C4E21F6FA0AF` (`deleted_by`),
  KEY `fk_risk_variable_ebi_metadata1_idx` (`ebi_metadata_id`),
  KEY `fk_risk_variable_org_metadata1_idx` (`org_metadata_id`),
  KEY `fk_risk_variable_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_risk_variable_survey1_idx` (`survey_id`),
  KEY `fk_risk_variable_organization1_idx` (`org_id`),
  KEY `fk_risk_variable_org_question1_idx` (`org_question_id`),
  KEY `fk_risk_variable_survey_questions1_idx` (`survey_questions_id`),
  KEY `fk_risk_variable_factor1_idx` (`factor_id`),
  KEY `RV_agg` (`calc_type`,`calculation_start_date`,`calculation_end_date`),
  CONSTRAINT `FK_2A64C4E21F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2A64C4E225F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2A64C4E24012B3BF` FOREIGN KEY (`org_metadata_id`) REFERENCES `org_metadata` (`id`),
  CONSTRAINT `FK_2A64C4E279F0E193` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`),
  CONSTRAINT `FK_2A64C4E282ABAC59` FOREIGN KEY (`org_question_id`) REFERENCES `org_question` (`id`),
  CONSTRAINT `FK_2A64C4E2B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_2A64C4E2BB49FE75` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`),
  CONSTRAINT `FK_2A64C4E2BC88C1A3` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`),
  CONSTRAINT `FK_2A64C4E2CC63389E` FOREIGN KEY (`survey_questions_id`) REFERENCES `survey_questions` (`id`),
  CONSTRAINT `FK_2A64C4E2DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2A64C4E2F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_variable`
--

LOCK TABLES `risk_variable` WRITE;
/*!40000 ALTER TABLE `risk_variable` DISABLE KEYS */;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `risk_variable_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `bucket_value` int(11) DEFAULT NULL,
  `option_value` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1B6EF2A7DE12AB56` (`created_by`),
  KEY `IDX_1B6EF2A725F94802` (`modified_by`),
  KEY `IDX_1B6EF2A71F6FA0AF` (`deleted_by`),
  KEY `fk_risk_model_bucket_category_risk_variable1_idx` (`risk_variable_id`,`option_value`(4),`bucket_value`),
  CONSTRAINT `FK_1B6EF2A71F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1B6EF2A725F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_1B6EF2A7296E76DF` FOREIGN KEY (`risk_variable_id`) REFERENCES `risk_variable` (`id`),
  CONSTRAINT `FK_1B6EF2A7DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_variable_category`
--

LOCK TABLES `risk_variable_category` WRITE;
/*!40000 ALTER TABLE `risk_variable_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_variable_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_variable_range`
--

DROP TABLE IF EXISTS `risk_variable_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_variable_range` (
  `bucket_value` int(11) NOT NULL,
  `risk_variable_id` int(11) NOT NULL,
  `min` decimal(16,4) DEFAULT NULL,
  `max` decimal(16,4) DEFAULT NULL,
  PRIMARY KEY (`bucket_value`,`risk_variable_id`),
  KEY `fk_risk_model_bucket_range_risk_variable1_idx` (`risk_variable_id`,`min`,`max`),
  CONSTRAINT `FK_DF38E125296E76DF` FOREIGN KEY (`risk_variable_id`) REFERENCES `risk_variable` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_variable_range`
--

LOCK TABLES `risk_variable_range` WRITE;
/*!40000 ALTER TABLE `risk_variable_range` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_variable_range` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `riskfactorcalc`
--

DROP TABLE IF EXISTS `riskfactorcalc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `riskfactorcalc` (
  `person_id` bigint(20) DEFAULT NULL,
  `risk_model_id` bigint(20) DEFAULT NULL,
  `Numerator` decimal(40,4) DEFAULT NULL,
  `Denominator` decimal(30,4) DEFAULT NULL,
  `Risk_Score` decimal(48,8) DEFAULT NULL,
  `risk_level` int(11) DEFAULT NULL,
  `risk_text` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `color_hex` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `riskfactorcalc`
--

LOCK TABLES `riskfactorcalc` WRITE;
/*!40000 ALTER TABLE `riskfactorcalc` DISABLE KEYS */;
/*!40000 ALTER TABLE `riskfactorcalc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'A'),(2,NULL,NULL,NULL,NULL,NULL,NULL,'A');
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
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `role_name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8FB6F6F6D60322AC` (`role_id`),
  KEY `IDX_8FB6F6F6B213FA4` (`lang_id`),
  CONSTRAINT `FK_8FB6F6F6B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_8FB6F6F6D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_lang`
--

LOCK TABLES `role_lang` WRITE;
/*!40000 ALTER TABLE `role_lang` DISABLE KEYS */;
INSERT INTO `role_lang` VALUES (1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Mapworks Admin'),(2,2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Skyfactor Admin');
/*!40000 ALTER TABLE `role_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saml_sso_state`
--

DROP TABLE IF EXISTS `saml_sso_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saml_sso_state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saml_sso_state`
--

LOCK TABLES `saml_sso_state` WRITE;
/*!40000 ALTER TABLE `saml_sso_state` DISABLE KEYS */;
/*!40000 ALTER TABLE `saml_sso_state` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_db_view_log`
--

DROP TABLE IF EXISTS `student_db_view_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_db_view_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `person_id_faculty` int(11) DEFAULT NULL,
  `person_id_student` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `last_viewed_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A0599E8B32C8A3DE` (`organization_id`),
  KEY `IDX_A0599E8BFFB0AA26` (`person_id_faculty`),
  KEY `IDX_A0599E8B5F056556` (`person_id_student`),
  CONSTRAINT `FK_A0599E8B32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_A0599E8B5F056556` FOREIGN KEY (`person_id_student`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_A0599E8BFFB0AA26` FOREIGN KEY (`person_id_faculty`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_db_view_log`
--

LOCK TABLES `student_db_view_log` WRITE;
/*!40000 ALTER TABLE `student_db_view_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_db_view_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `success_marker`
--

DROP TABLE IF EXISTS `success_marker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `success_marker` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `sequence` smallint(6) NOT NULL,
  `needs_color_calculated` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_success_marker_created_by` (`created_by`),
  KEY `fk_success_marker_modified_by` (`modified_by`),
  KEY `fk_success_marker_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_success_marker_created_by` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `success_marker`
--

LOCK TABLES `success_marker` WRITE;
/*!40000 ALTER TABLE `success_marker` DISABLE KEYS */;
INSERT INTO `success_marker` VALUES (5,'Academic',1,1,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(6,'Behaviors and Activities',2,1,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(7,'Financial Means',3,1,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(8,'Performance and Expectations',4,1,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(9,'Socio-Emotional',5,1,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(10,'Student Populations',6,0,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(11,'Student Topics',7,0,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL);
/*!40000 ALTER TABLE `success_marker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `success_marker_calculated`
--

DROP TABLE IF EXISTS `success_marker_calculated`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `success_marker_calculated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `surveymarker_questions_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `color` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `org_person_marker_uniq_idx` (`organization_id`,`person_id`,`surveymarker_questions_id`,`modified_at`),
  KEY `IDX_9FD161E5DE12AB56` (`created_by`),
  KEY `IDX_9FD161E525F94802` (`modified_by`),
  KEY `IDX_9FD161E51F6FA0AF` (`deleted_by`),
  KEY `fk_success_marker_calculated_person1_idx` (`person_id`),
  KEY `fk_success_marker_calculated_organization1_idx` (`organization_id`),
  KEY `fk_success_marker_calculated_surveymarker_questions1_idx` (`surveymarker_questions_id`),
  KEY `org_person_modified` (`modified_at`,`organization_id`,`person_id`),
  CONSTRAINT `FK_9FD161E51F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9FD161E5217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9FD161E525F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_9FD161E532C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_9FD161E57F16624C` FOREIGN KEY (`surveymarker_questions_id`) REFERENCES `surveymarker_questions` (`id`),
  CONSTRAINT `FK_9FD161E5DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `success_marker_calculated`
--

LOCK TABLES `success_marker_calculated` WRITE;
/*!40000 ALTER TABLE `success_marker_calculated` DISABLE KEYS */;
/*!40000 ALTER TABLE `success_marker_calculated` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `success_marker_color`
--

DROP TABLE IF EXISTS `success_marker_color`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `success_marker_color` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `color` enum('red','yellow','green') NOT NULL,
  `base_value` smallint(6) NOT NULL,
  `min_value` decimal(6,3) NOT NULL,
  `max_value` decimal(6,3) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_success_marker_color_created_by` (`created_by`),
  KEY `fk_success_marker_color_modified_by` (`modified_by`),
  KEY `fk_success_marker_color_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_success_marker_color_created_by` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_color_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_color_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `success_marker_color`
--

LOCK TABLES `success_marker_color` WRITE;
/*!40000 ALTER TABLE `success_marker_color` DISABLE KEYS */;
INSERT INTO `success_marker_color` VALUES (1,'red',1,1.000,1.750,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(2,'yellow',2,1.751,2.249,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(3,'green',3,2.250,3.000,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL);
/*!40000 ALTER TABLE `success_marker_color` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `success_marker_topic`
--

DROP TABLE IF EXISTS `success_marker_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `success_marker_topic` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `success_marker_id` smallint(6) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_success_marker_topic_success_marker_id` (`success_marker_id`),
  KEY `fk_success_marker_topic_created_by` (`created_by`),
  KEY `fk_success_marker_topic_modified_by` (`modified_by`),
  KEY `fk_success_marker_topic_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_success_marker_topic_created_by` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_topic_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_topic_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_topic_success_marker_id` FOREIGN KEY (`success_marker_id`) REFERENCES `success_marker` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `success_marker_topic`
--

LOCK TABLES `success_marker_topic` WRITE;
/*!40000 ALTER TABLE `success_marker_topic` DISABLE KEYS */;
INSERT INTO `success_marker_topic` VALUES (1,'Academic Integration',5,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(2,'Academic Resiliency',5,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(3,'Academic Self-Efficacy',5,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(4,'Advanced Study Skills',5,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(5,'Analytical Skills',5,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(6,'Chosen a Major',5,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(7,'Commitment to a Major',5,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(8,'Commitment to Earning a Degree',5,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(9,'Communication Skills',5,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(10,'Course Difficulties',5,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(11,'Selected a Career Path',5,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(12,'Advanced Academic Behaviors',6,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(13,'Basic Academic Behaviors',6,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(14,'Class Attendance',6,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(15,'Family Interference with Coursework',6,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(16,'Number of Study Hours Per Week',6,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(17,'Number of Work Hours Per Week',6,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(18,'Self-Discipline',6,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(19,'Student Organization Involvement',6,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(20,'Time Management',6,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(21,'Work Interference with Coursework',6,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(22,'Financial Means',7,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(23,'Expected Cumulative GPA Upon Completion/Graduation',8,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(24,'Expected Grades this Term',8,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(25,'High School GPA (Self-Reported)',8,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(26,'Parents\'/Guardians\' Educational Level',8,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(27,'Commitment to the Institution',9,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(28,'Homesickness: Distressed',9,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(29,'Homesickness: Separation',9,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(30,'Institutional Choice',9,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(31,'Living Environment (Off Campus)',9,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(32,'Living Environment (On Campus)',9,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(33,'On-Campus Living: Roommates',9,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(34,'On-Campus Living: Social Aspects',9,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(35,'Peer Connections',9,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(36,'Satisfaction with Institution',9,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(37,'Social Integration',9,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(38,'Active Military or Veteran',10,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(39,'External Commitments',10,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(40,'Fraternity/Sorority Member',10,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(41,'Off-Campus Student',10,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(42,'Student Athlete',10,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(43,'Transfer Student',10,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(44,'Academic Major Evaluation',11,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(45,'Academic/Career Planning',11,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(46,'Post-Graduation/Completion Plans',11,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL),(47,'Test Anxiety (Stressors)',11,'2016-12-13 19:11:24','2016-12-13 19:11:24',NULL,-25,-25,NULL);
/*!40000 ALTER TABLE `success_marker_topic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `success_marker_topic_detail`
--

DROP TABLE IF EXISTS `success_marker_topic_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `success_marker_topic_detail` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `topic_id` smallint(6) NOT NULL,
  `factor_id` int(11) DEFAULT NULL,
  `ebi_question_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_success_marker_topic_detail_topic_id` (`topic_id`),
  KEY `fk_success_marker_topic_detail_factor_id` (`factor_id`),
  KEY `fk_success_marker_topic_detail_ebi_question_id` (`ebi_question_id`),
  KEY `fk_success_marker_topic_detail_created_by` (`created_by`),
  KEY `fk_success_marker_topic_detail_modified_by` (`modified_by`),
  KEY `fk_success_marker_topic_detail_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_success_marker_topic_detail_created_by` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_topic_detail_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_topic_detail_ebi_question_id` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`),
  CONSTRAINT `fk_success_marker_topic_detail_factor_id` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`),
  CONSTRAINT `fk_success_marker_topic_detail_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_topic_detail_topic_id` FOREIGN KEY (`topic_id`) REFERENCES `success_marker_topic` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `success_marker_topic_detail`
--

LOCK TABLES `success_marker_topic_detail` WRITE;
/*!40000 ALTER TABLE `success_marker_topic_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `success_marker_topic_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `success_marker_topic_detail_color`
--

DROP TABLE IF EXISTS `success_marker_topic_detail_color`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `success_marker_topic_detail_color` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `topic_detail_id` smallint(6) NOT NULL,
  `color` enum('red','yellow','green') NOT NULL,
  `min_value` decimal(6,3) NOT NULL,
  `max_value` decimal(6,3) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_success_marker_topic_detail_color_topic_detail_id` (`topic_detail_id`),
  KEY `fk_success_marker_topic_detail_color_created_by` (`created_by`),
  KEY `fk_success_marker_topic_detail_color_modified_by` (`modified_by`),
  KEY `fk_success_marker_topic_detail_color_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_success_marker_topic_detail_color_created_by` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_topic_detail_color_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_topic_detail_color_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_topic_detail_color_topic_detail_id` FOREIGN KEY (`topic_detail_id`) REFERENCES `success_marker_topic_detail` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `success_marker_topic_detail_color`
--

LOCK TABLES `success_marker_topic_detail_color` WRITE;
/*!40000 ALTER TABLE `success_marker_topic_detail_color` DISABLE KEYS */;
/*!40000 ALTER TABLE `success_marker_topic_detail_color` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `success_marker_topic_representative`
--

DROP TABLE IF EXISTS `success_marker_topic_representative`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `success_marker_topic_representative` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `topic_id` smallint(6) NOT NULL,
  `representative_detail_id` smallint(6) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `smtr_unique` (`topic_id`),
  KEY `fk_success_marker_topic_representative_detail_id` (`representative_detail_id`),
  KEY `fk_success_marker_topic_representative_created_by` (`created_by`),
  KEY `fk_success_marker_topic_representative_modified_by` (`modified_by`),
  KEY `fk_success_marker_topic_representative_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_success_marker_topic_representative_created_by` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_topic_representative_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_topic_representative_detail_id` FOREIGN KEY (`representative_detail_id`) REFERENCES `success_marker_topic_detail` (`id`),
  CONSTRAINT `fk_success_marker_topic_representative_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `fk_success_marker_topic_representative_topic_id` FOREIGN KEY (`topic_id`) REFERENCES `success_marker_topic` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `success_marker_topic_representative`
--

LOCK TABLES `success_marker_topic_representative` WRITE;
/*!40000 ALTER TABLE `success_marker_topic_representative` DISABLE KEYS */;
/*!40000 ALTER TABLE `success_marker_topic_representative` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey`
--

DROP TABLE IF EXISTS `survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `external_id` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `included_in_persist_midyear_reporting` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey`
--

LOCK TABLES `survey` WRITE;
/*!40000 ALTER TABLE `survey` DISABLE KEYS */;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `survey_question_id` int(11) DEFAULT NULL,
  `ebi_question_options_id` int(11) DEFAULT NULL,
  `survey_pages_id` int(11) DEFAULT NULL,
  `branch_to_survey_pages_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  KEY `survey_id` (`survey_id`),
  KEY `survey_question_id` (`survey_question_id`),
  KEY `ebi_question_options_id` (`ebi_question_options_id`),
  KEY `survey_pages_id` (`survey_pages_id`),
  KEY `branch_to_survey_pages_id` (`branch_to_survey_pages_id`),
  CONSTRAINT `survey_branch_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `survey_branch_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `survey_branch_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `survey_branch_ibfk_4` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `survey_branch_ibfk_5` FOREIGN KEY (`survey_question_id`) REFERENCES `survey_questions` (`id`),
  CONSTRAINT `survey_branch_ibfk_6` FOREIGN KEY (`ebi_question_options_id`) REFERENCES `ebi_question_options` (`id`),
  CONSTRAINT `survey_branch_ibfk_7` FOREIGN KEY (`survey_pages_id`) REFERENCES `survey_pages` (`id`),
  CONSTRAINT `survey_branch_ibfk_8` FOREIGN KEY (`branch_to_survey_pages_id`) REFERENCES `survey_pages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_branch`
--

LOCK TABLES `survey_branch` WRITE;
/*!40000 ALTER TABLE `survey_branch` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey_branch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_branch_lang`
--

DROP TABLE IF EXISTS `survey_branch_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_branch_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_branch_id` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `branch_description` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `deleted_by` (`deleted_by`),
  KEY `survey_branch_id` (`survey_branch_id`),
  KEY `lang_id` (`lang_id`),
  CONSTRAINT `survey_branch_lang_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `survey_branch_lang_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `survey_branch_lang_ibfk_3` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `survey_branch_lang_ibfk_4` FOREIGN KEY (`survey_branch_id`) REFERENCES `survey_branch` (`id`),
  CONSTRAINT `survey_branch_lang_ibfk_5` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_branch_lang`
--

LOCK TABLES `survey_branch_lang` WRITE;
/*!40000 ALTER TABLE `survey_branch_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey_branch_lang` ENABLE KEYS */;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`survey_id`,`lang_id`),
  KEY `IDX_F7E084A9DE12AB56` (`created_by`),
  KEY `IDX_F7E084A925F94802` (`modified_by`),
  KEY `IDX_F7E084A91F6FA0AF` (`deleted_by`),
  KEY `fk_survey_lang_survey1_idx` (`survey_id`),
  KEY `fk_survey_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `FK_F7E084A91F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_F7E084A925F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_F7E084A9B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_F7E084A9B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_F7E084A9DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `set_completed` tinyint(1) DEFAULT NULL,
  `must_branch` tinyint(1) DEFAULT NULL,
  `external_id` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2C04174DDE12AB56` (`created_by`),
  KEY `IDX_2C04174D25F94802` (`modified_by`),
  KEY `IDX_2C04174D1F6FA0AF` (`deleted_by`),
  KEY `fk_survey_pages_survey1_idx` (`survey_id`),
  CONSTRAINT `FK_2C04174D1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2C04174D25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2C04174DB3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_2C04174DDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `description` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`survey_pages_id`,`lang_id`),
  KEY `IDX_3FB9C4D5DE12AB56` (`created_by`),
  KEY `IDX_3FB9C4D525F94802` (`modified_by`),
  KEY `IDX_3FB9C4D51F6FA0AF` (`deleted_by`),
  KEY `fk_survey_pages_lang_survey_pages1_idx` (`survey_pages_id`),
  KEY `fk_survey_pages_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `FK_3FB9C4D51CED9B00` FOREIGN KEY (`survey_pages_id`) REFERENCES `survey_pages` (`id`),
  CONSTRAINT `FK_3FB9C4D51F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3FB9C4D525F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_3FB9C4D5B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_3FB9C4D5DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `ebi_question_id` int(11) DEFAULT NULL,
  `org_question_id` int(11) DEFAULT NULL,
  `survey_sections_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `qnbr` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ind_question_id` int(11) DEFAULT NULL,
  `cohort_code` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2F8A16F8DE12AB56` (`created_by`),
  KEY `IDX_2F8A16F825F94802` (`modified_by`),
  KEY `IDX_2F8A16F81F6FA0AF` (`deleted_by`),
  KEY `fk_survey_questions_survey1_idx` (`survey_id`),
  KEY `fk_survey_questions_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_survey_questions_survey_sections1_idx` (`survey_sections_id`),
  KEY `fk_survey_questions_org_question1_idx` (`org_question_id`),
  KEY `idx_sequence` (`sequence`),
  KEY `IDX_2F8A16F851DCB924` (`ind_question_id`),
  KEY `qnbr` (`qnbr`),
  CONSTRAINT `FK_2F8A16F81F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2F8A16F825F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2F8A16F851DCB924` FOREIGN KEY (`ind_question_id`) REFERENCES `ind_question` (`id`),
  CONSTRAINT `FK_2F8A16F879F0E193` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`),
  CONSTRAINT `FK_2F8A16F8B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_2F8A16F8DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2F8A16F8EF81D9E1` FOREIGN KEY (`survey_sections_id`) REFERENCES `survey_sections` (`id`),
  CONSTRAINT `survey_questions_ibfk_1` FOREIGN KEY (`org_question_id`) REFERENCES `org_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_questions`
--

LOCK TABLES `survey_questions` WRITE;
/*!40000 ALTER TABLE `survey_questions` DISABLE KEYS */;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `org_academic_year_id` int(11) DEFAULT NULL,
  `org_academic_terms_id` int(11) DEFAULT NULL,
  `survey_questions_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `response_type` enum('decimal','char','charmax') COLLATE utf8_unicode_ci DEFAULT NULL,
  `decimal_value` decimal(9,2) DEFAULT NULL,
  `char_value` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `charmax_value` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_628C4DDCDE12AB56` (`created_by`),
  KEY `IDX_628C4DDC25F94802` (`modified_by`),
  KEY `IDX_628C4DDC1F6FA0AF` (`deleted_by`),
  KEY `fk_survey_response_survey1_idx` (`survey_id`),
  KEY `fk_survey_response_org_academic_year1_idx` (`org_academic_year_id`),
  KEY `fk_survey_response_org_academic_terms1_idx` (`org_academic_terms_id`),
  KEY `fk_survey_response_person1` (`person_id`,`survey_id`),
  KEY `modified` (`modified_at`,`created_at`),
  KEY `created` (`created_at`),
  KEY `fk_survey_response_organization1` (`org_id`,`person_id`,`survey_questions_id`,`modified_at`),
  KEY `latestsurvey` (`org_id`,`person_id`,`modified_at`,`survey_id`,`survey_questions_id`),
  KEY `fk_survey_response_survey_questions1_idx` (`survey_questions_id`,`person_id`),
  CONSTRAINT `FK_628C4DDC1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_628C4DDC217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_628C4DDC25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_628C4DDC8D7CC0D2` FOREIGN KEY (`org_academic_terms_id`) REFERENCES `org_academic_terms` (`id`),
  CONSTRAINT `FK_628C4DDCB3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_628C4DDCCC63389E` FOREIGN KEY (`survey_questions_id`) REFERENCES `survey_questions` (`id`),
  CONSTRAINT `FK_628C4DDCDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_628C4DDCF3B0CE4A` FOREIGN KEY (`org_academic_year_id`) REFERENCES `org_academic_year` (`id`),
  CONSTRAINT `FK_628C4DDCF4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_response`
--

LOCK TABLES `survey_response` WRITE;
/*!40000 ALTER TABLE `survey_response` DISABLE KEYS */;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `survey_pages_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `external_id` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_776105BFDE12AB56` (`created_by`),
  KEY `IDX_776105BF25F94802` (`modified_by`),
  KEY `IDX_776105BF1F6FA0AF` (`deleted_by`),
  KEY `fk_survey_sections_survey1_idx` (`survey_id`),
  KEY `fk_survey_sections_survey_pages1_idx` (`survey_pages_id`),
  CONSTRAINT `FK_776105BF1CED9B00` FOREIGN KEY (`survey_pages_id`) REFERENCES `survey_pages` (`id`),
  CONSTRAINT `FK_776105BF1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_776105BF25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_776105BFB3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_776105BFDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `description_hdr` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description_dtl` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`survey_sections_id`,`lang_id`),
  KEY `IDX_E00ED5A8DE12AB56` (`created_by`),
  KEY `IDX_E00ED5A825F94802` (`modified_by`),
  KEY `IDX_E00ED5A81F6FA0AF` (`deleted_by`),
  KEY `fk_survey_sections_lang_survey_sections1_idx` (`survey_sections_id`),
  KEY `fk_survey_sections_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `FK_E00ED5A81F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E00ED5A825F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E00ED5A8B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_E00ED5A8DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E00ED5A8EF81D9E1` FOREIGN KEY (`survey_sections_id`) REFERENCES `survey_sections` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4873E5ADE12AB56` (`created_by`),
  KEY `IDX_4873E5A25F94802` (`modified_by`),
  KEY `IDX_4873E5A1F6FA0AF` (`deleted_by`),
  CONSTRAINT `FK_4873E5A1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_4873E5A25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_4873E5ADE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `surveymarker_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8E6D6480DE12AB56` (`created_by`),
  KEY `IDX_8E6D648025F94802` (`modified_by`),
  KEY `IDX_8E6D64801F6FA0AF` (`deleted_by`),
  KEY `fk_survey_marker_lang_survey_marker1_idx` (`surveymarker_id`),
  KEY `fk_survey_marker_lang_language_master1_idx` (`lang_id`),
  CONSTRAINT `FK_8E6D64801F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8E6D648025F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8E6D6480357363AC` FOREIGN KEY (`surveymarker_id`) REFERENCES `surveymarker` (`id`),
  CONSTRAINT `FK_8E6D6480B213FA4` FOREIGN KEY (`lang_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_8E6D6480DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `surveymarker_id` int(11) DEFAULT NULL,
  `ebi_question_id` int(11) DEFAULT NULL,
  `survey_questions_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `factor_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `red_low` decimal(6,3) DEFAULT NULL,
  `red_high` decimal(6,3) DEFAULT NULL,
  `yellow_low` decimal(6,3) DEFAULT NULL,
  `yellow_high` decimal(6,3) DEFAULT NULL,
  `green_low` decimal(6,3) DEFAULT NULL,
  `green_high` decimal(6,3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_223450D9DE12AB56` (`created_by`),
  KEY `IDX_223450D925F94802` (`modified_by`),
  KEY `IDX_223450D91F6FA0AF` (`deleted_by`),
  KEY `fk_surveymarker_questions_surveymarker1_idx` (`surveymarker_id`),
  KEY `fk_surveymarker_questions_ebi_question1_idx` (`ebi_question_id`),
  KEY `fk_surveymarker_questions_survey1_idx` (`survey_id`),
  KEY `fk_surveymarker_questions_factor1_idx` (`factor_id`),
  KEY `fk_surveymarker_questions_survey_questions1_idx` (`survey_questions_id`),
  CONSTRAINT `FK_223450D91F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_223450D925F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_223450D9357363AC` FOREIGN KEY (`surveymarker_id`) REFERENCES `surveymarker` (`id`),
  CONSTRAINT `FK_223450D979F0E193` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`),
  CONSTRAINT `FK_223450D9B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_223450D9BC88C1A3` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`),
  CONSTRAINT `FK_223450D9CC63389E` FOREIGN KEY (`survey_questions_id`) REFERENCES `survey_questions` (`id`),
  CONSTRAINT `FK_223450D9DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `person_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E7F475AA217BBB47` (`person_id`),
  KEY `IDX_E7F475AA32C8A3DE` (`organization_id`),
  CONSTRAINT `FK_E7F475AA217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E7F475AA32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_alerts`
--

LOCK TABLES `system_alerts` WRITE;
/*!40000 ALTER TABLE `system_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `talking_points`
--

DROP TABLE IF EXISTS `talking_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `talking_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `ebi_question_id` int(11) DEFAULT NULL,
  `ebi_metadata_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `talking_points_type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `min_range` int(11) DEFAULT NULL,
  `max_range` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8F281C4DDE12AB56` (`created_by`),
  KEY `IDX_8F281C4D25F94802` (`modified_by`),
  KEY `IDX_8F281C4D1F6FA0AF` (`deleted_by`),
  KEY `IDX_8F281C4D79F0E193` (`ebi_question_id`),
  KEY `IDX_8F281C4DBB49FE75` (`ebi_metadata_id`),
  KEY `tp_type` (`talking_points_type`,`ebi_metadata_id`,`deleted_at`),
  CONSTRAINT `FK_8F281C4D1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8F281C4D25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_8F281C4D79F0E193` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`),
  CONSTRAINT `FK_8F281C4DBB49FE75` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`),
  CONSTRAINT `FK_8F281C4DDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `talking_points_id` int(11) DEFAULT NULL,
  `language_master_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `title` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5CC5137ADE12AB56` (`created_by`),
  KEY `IDX_5CC5137A25F94802` (`modified_by`),
  KEY `IDX_5CC5137A1F6FA0AF` (`deleted_by`),
  KEY `IDX_5CC5137ACDC12E8B` (`talking_points_id`),
  KEY `IDX_5CC5137AD5D3A0FB` (`language_master_id`),
  CONSTRAINT `FK_5CC5137A1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_5CC5137A25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_5CC5137ACDC12E8B` FOREIGN KEY (`talking_points_id`) REFERENCES `talking_points` (`id`),
  CONSTRAINT `FK_5CC5137AD5D3A0FB` FOREIGN KEY (`language_master_id`) REFERENCES `language_master` (`id`),
  CONSTRAINT `FK_5CC5137ADE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `person_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `teams_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `is_team_leader` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BAD9A3C8217BBB47` (`person_id`),
  KEY `IDX_BAD9A3C832C8A3DE` (`organization_id`),
  KEY `IDX_BAD9A3C8D6365F12` (`teams_id`),
  CONSTRAINT `FK_BAD9A3C8217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_BAD9A3C832C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_BAD9A3C8D6365F12` FOREIGN KEY (`teams_id`) REFERENCES `Teams` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_members`
--

LOCK TABLES `team_members` WRITE;
/*!40000 ALTER TABLE `team_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `team_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upload_file_log`
--

DROP TABLE IF EXISTS `upload_file_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `upload_file_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `upload_type` enum('A','C','F','G','S','SB','SM','T','TP','P','H','SL','RV','RM','RMA','CI','FA','GS','GF','S2G','OSR','SRE','SRT') COLLATE utf8_unicode_ci DEFAULT NULL,
  `upload_date` datetime DEFAULT NULL,
  `uploaded_columns` text CHARACTER SET utf8,
  `uploaded_row_count` int(11) DEFAULT NULL,
  `status` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uploaded_file_path` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uploaded_file_hash` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `error_file_path` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `job_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
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
  KEY `IDX_E70B1E39DE12AB56` (`created_by`),
  KEY `IDX_E70B1E3925F94802` (`modified_by`),
  KEY `IDX_E70B1E391F6FA0AF` (`deleted_by`),
  CONSTRAINT `FK_E70B1E391F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E70B1E3925F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_E70B1E39DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cohort_code` int(11) DEFAULT NULL,
  `wess_survey_id` int(11) DEFAULT NULL,
  `wess_cohort_id` int(11) DEFAULT NULL,
  `wess_order_id` int(11) DEFAULT NULL,
  `wess_launchedflag` int(11) DEFAULT NULL,
  `wess_maporder_key` int(11) DEFAULT NULL,
  `wess_prod_year` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wess_cust_id` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('open','ready','launched','closed') COLLATE utf8_unicode_ci DEFAULT NULL,
  `open_date` datetime DEFAULT NULL,
  `close_date` datetime DEFAULT NULL,
  `year_id` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wess_admin_link` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_175DBF43DE12AB56` (`created_by`),
  KEY `IDX_175DBF4325F94802` (`modified_by`),
  KEY `IDX_175DBF431F6FA0AF` (`deleted_by`),
  KEY `fk_wess_link_survey1_idx` (`survey_id`),
  KEY `fk_wess_link_year1_idx` (`year_id`),
  KEY `fk_wess_link_organization1` (`org_id`,`year_id`,`status`,`survey_id`),
  KEY `org_survey_cohort` (`org_id`,`survey_id`,`cohort_code`),
  CONSTRAINT `FK_175DBF431F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_175DBF4325F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_175DBF4340C1FEA7` FOREIGN KEY (`year_id`) REFERENCES `year` (`id`),
  CONSTRAINT `FK_175DBF43B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_175DBF43DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_175DBF43F4837C1B` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BB827337DE12AB56` (`created_by`),
  KEY `IDX_BB82733725F94802` (`modified_by`),
  KEY `IDX_BB8273371F6FA0AF` (`deleted_by`),
  CONSTRAINT `FK_BB8273371F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_BB82733725F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_BB827337DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `year`
--

LOCK TABLES `year` WRITE;
/*!40000 ALTER TABLE `year` DISABLE KEYS */;
/*!40000 ALTER TABLE `year` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'synapse'
--
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
/*!50106 DROP EVENT IF EXISTS `IssueCalculationEvent` */;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8 */ ;;
/*!50003 SET character_set_results = utf8 */ ;;
/*!50003 SET collation_connection  = utf8_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'UTC' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`synapsemaster`@`%`*/ /*!50106 EVENT `IssueCalculationEvent` ON SCHEDULE EVERY 1 HOUR STARTS '2015-10-15 10:02:38' ON COMPLETION NOT PRESERVE DISABLE DO BEGIN
            CALL IssueCalculation();
        END */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
/*!50106 DROP EVENT IF EXISTS `Survey_Risk_Event` */;;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8 */ ;;
/*!50003 SET character_set_results = utf8 */ ;;
/*!50003 SET collation_connection  = utf8_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'UTC' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`synapsemaster`@`%`*/ /*!50106 EVENT `Survey_Risk_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:01:30' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
                           SET @startTime = NOW();
                           CALL Academic_Update_Grade_Fixer();
                           CALL survey_data_transfer();
                           CALL isq_data_transfer();
                           CALL Factor_Calc(DATE_ADD(NOW(), INTERVAL 140 second), 60);
                           CALL Report_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 60);
                           CALL Intent_Leave_Calc();
                           CALL Talking_Point_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 100);
                           CALL org_RiskFactorCalculation(DATE_ADD(@startTime, INTERVAL 14 minute), 30);
                        END */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;
/*!50106 SET TIME_ZONE= @save_time_zone */ ;

--
-- Dumping routines for database 'synapse'
--
/*!50003 DROP FUNCTION IF EXISTS `GetGroupStudentCount` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` FUNCTION `GetGroupStudentCount`(GivenID  VARCHAR(1024)) RETURNS varchar(1024) CHARSET latin1
    DETERMINISTIC
BEGIN
        
    DECLARE rv,q,queue,queue_children,front_id VARCHAR(1024);
    DECLARE queue_length,pos INT;
        
    SET rv = '';
    SET queue = GivenID;
    SET queue_length = 1;
        
    WHILE queue_length > 0 DO
        SET front_id = queue;
        IF queue_length = 1 THEN
            SET queue = '';
        ELSE
            SET pos = LOCATE(',',queue) + 1;
            SET q = SUBSTR(queue,pos);
            SET queue = q;
        END IF;
        SET queue_length = queue_length - 1;
        
        SELECT IFNULL(qc,'') INTO queue_children
        FROM (SELECT GROUP_CONCAT(id) qc
        FROM org_group WHERE parent_group_id = front_id) A;
        
        IF LENGTH(queue_children) = 0 THEN
            IF LENGTH(queue) = 0 THEN
                SET queue_length = 0;
            END IF;
        ELSE
            IF LENGTH(rv) = 0 THEN
                SET rv = queue_children;
            ELSE
                SET rv = CONCAT(rv,',',queue_children);
            END IF;
            IF LENGTH(queue) = 0 THEN
                SET queue = queue_children;
            ELSE
                SET queue = CONCAT(queue,',',queue_children);
            END IF;
            SET queue_length = LENGTH(queue) - LENGTH(REPLACE(queue,',','')) + 1;
        END IF;
    END WHILE;
        
	set @cnt := (SELECT count(*) FROM org_group_students WHERE deleted_at IS NULL AND org_group_id IN (rv));
        
    RETURN @cnt;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `GetSubGroupCount` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` FUNCTION `GetSubGroupCount`(GivenID  VARCHAR(1024)) RETURNS varchar(1024) CHARSET latin1
    DETERMINISTIC
BEGIN

    DECLARE rv,q,queue,queue_children,front_id VARCHAR(1024);
    DECLARE queue_length,pos INT;

    SET rv = '';
    SET queue = GivenID;		
    SET queue_length = 1;

    WHILE queue_length > 0 DO
        SET front_id = queue;
        IF queue_length = 1 THEN
            SET queue = '';
        ELSE
            SET pos = LOCATE(',',queue) + 1;
            SET q = SUBSTR(queue,pos);
            SET queue = q;
        END IF;
        SET queue_length = queue_length - 1;

        SELECT IFNULL(qc,'') INTO queue_children
        FROM (SELECT GROUP_CONCAT(id) qc
        FROM org_group WHERE parent_group_id = front_id) A;

        IF LENGTH(queue_children) = 0 THEN
            IF LENGTH(queue) = 0 THEN
                SET queue_length = 0;
            END IF;
        ELSE
            IF LENGTH(rv) = 0 THEN
                SET rv = queue_children;
            ELSE
                SET rv = CONCAT(rv,',',queue_children);
            END IF;
            IF LENGTH(queue) = 0 THEN
                SET queue = queue_children;
            ELSE
                SET queue = CONCAT(queue,',',queue_children);
            END IF;
            SET queue_length = LENGTH(queue) - LENGTH(REPLACE(queue,',','')) + 1;
        END IF;
    END WHILE;
	
    if LENGTH(rv) > 0 THEN
		SET rv := (select length(rv) - length(replace(rv, ',', '')));
        IF rv != 0 THEN
					SET rv = rv + 1 ;
				ELSE
			SET rv = 1;
		END IF;
		RETURN rv ;
	ELSE 
		return 0;
	END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `GetSubGroupFacultyCount` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` FUNCTION `GetSubGroupFacultyCount`(GivenID  VARCHAR(1024)) RETURNS varchar(1024) CHARSET latin1
    DETERMINISTIC
BEGIN
        
    DECLARE rv,q,queue,queue_children,front_id VARCHAR(1024);
    DECLARE queue_length,pos INT;
        
    SET rv = '';
    SET queue = GivenID;
    SET queue_length = 1;
        
    WHILE queue_length > 0 DO
        SET front_id = queue;
        IF queue_length = 1 THEN
            SET queue = '';
        ELSE
            SET pos = LOCATE(',',queue) + 1;
            SET q = SUBSTR(queue,pos);
            SET queue = q;
        END IF;
        SET queue_length = queue_length - 1;
        
        SELECT IFNULL(qc,'') INTO queue_children
        FROM (SELECT GROUP_CONCAT(id) qc
        FROM org_group WHERE parent_group_id = front_id) A;
        
        IF LENGTH(queue_children) = 0 THEN
            IF LENGTH(queue) = 0 THEN
                SET queue_length = 0;
            END IF;
        ELSE
            IF LENGTH(rv) = 0 THEN
                SET rv = queue_children;
            ELSE
                SET rv = CONCAT(rv,',',queue_children);
            END IF;
            IF LENGTH(queue) = 0 THEN
                SET queue = queue_children;
            ELSE
                SET queue = CONCAT(queue,',',queue_children);
            END IF;
            SET queue_length = LENGTH(queue) - LENGTH(REPLACE(queue,',','')) + 1;
        END IF;
    END WHILE;
        
	set @cnt := (SELECT count(*) FROM org_group_faculty WHERE deleted_at IS NULL AND org_group_id IN (rv));
        
    RETURN @cnt;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `get_most_recent_ISQ` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` FUNCTION `get_most_recent_ISQ`(the_org_id INT, the_person_id INT, the_survey_id INT) RETURNS int(11)
    READS SQL DATA
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN
            
            RETURN  (SELECT org_question_id 
                FROM org_question_response oqr
                WHERE
                (oqr.org_id, oqr.person_id, oqr.survey_id)
                = (the_org_id, the_person_id, the_survey_id)
                ORDER BY modified_at DESC
                LIMIT 1);
        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `get_most_recent_survey` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` FUNCTION `get_most_recent_survey`(the_org_id INT, the_person_id INT) RETURNS int(11)
    READS SQL DATA
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN 
           
            RETURN  (SELECT survey_id 
                FROM survey_response sr
                WHERE
                (sr.org_id, sr.person_id)
                = (the_org_id, the_person_id)
                ORDER BY modified_at DESC
                LIMIT 1);
            END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `get_most_recent_survey_ISQ` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` FUNCTION `get_most_recent_survey_ISQ`(the_org_id INT, the_person_id INT) RETURNS int(11)
    READS SQL DATA
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN
            
            RETURN  (SELECT survey_id 
                FROM org_question_response oqr
                WHERE
                (oqr.org_id, oqr.person_id)
                = (the_org_id, the_person_id)
                ORDER BY modified_at DESC
                LIMIT 1);
        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `get_most_recent_survey_question` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` FUNCTION `get_most_recent_survey_question`(the_org_id INT, the_person_id INT, the_survey_id INT) RETURNS int(11)
    READS SQL DATA
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN

            RETURN  (SELECT survey_questions_id 
                FROM survey_response sr
                WHERE
                (sr.org_id, sr.person_id, sr.survey_id)
                = (the_org_id, the_person_id, the_survey_id)
                ORDER BY modified_at DESC
                LIMIT 1);
         END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `risk_score_aggregated_RV` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` FUNCTION `risk_score_aggregated_RV`(the_org_id INT, the_person_id INT, the_RV_id INT, agg_type VARCHAR(32), the_start_date DATETIME, the_end_date DATETIME) RETURNS varchar(255) CHARSET utf8
    READS SQL DATA
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN  
        #--Optimization (use the last value generated if it matches parameters)
            IF(the_org_id=@cache_RSaggRV_org_id AND the_person_id=@cache_RSaggRV_person_id AND the_RV_id=@cache_RSaggRV_RV_id AND @cache_RSaggRV_ts=NOW(6)+0) THEN
                RETURN @cache_RSaggRV_ret;
            END IF;
            SET @cache_RSaggRV_org_id=the_org_id, @cache_RSaggRV_person_id=the_person_id, @cache_RSaggRV_RV_id=the_RV_id, @cache_RSaggRV_ts=NOW(6)+0;
            #--SET @cache_miss=@cache_miss+1;
        
        IF(agg_type IS NULL) THEN
            SET @cache_RSaggRV_ret=(
                SELECT RD.source_value AS calculated_value
                FROM org_person_riskvariable_datum AS RD
                WHERE
                    RD.org_id=the_org_id 
                    AND RD.person_id=the_person_id 
                    AND RD.risk_variable_id=the_RV_id
                ORDER BY modified_at DESC, created_at DESC
                LIMIT 1
            );
        ELSEIF(agg_type='Sum') THEN
            SET @cache_RSaggRV_ret=(
                SELECT SUM(RD.source_value) AS calculated_value
                FROM org_person_riskvariable_datum AS RD
                WHERE
                    RD.org_id=the_org_id 
                    AND RD.person_id=the_person_id 
                    AND RD.risk_variable_id=the_RV_id
                GROUP BY RD.person_id, RD.risk_variable_id 
                #LIMIT 1
            );
        ELSEIF(agg_type='Count') THEN
            SET @cache_RSaggRV_ret=(
                SELECT COUNT(RD.source_value) AS calculated_value
                FROM org_person_riskvariable_datum AS RD
                WHERE
                    RD.org_id=the_org_id 
                    AND RD.person_id=the_person_id 
                    AND RD.risk_variable_id=the_RV_id
                GROUP BY RD.person_id, RD.risk_variable_id 
                #LIMIT 1
            );
        ELSEIF(agg_type='Average') THEN
            SET @cache_RSaggRV_ret=(
                SELECT AVG(RD.source_value) AS calculated_value
                FROM org_person_riskvariable_datum AS RD
                WHERE
                    RD.org_id=the_org_id 
                    AND RD.person_id=the_person_id 
                    AND RD.risk_variable_id=the_RV_id
                GROUP BY RD.person_id, RD.risk_variable_id 
                #LIMIT 1
            );
        ELSEIF(agg_type='Most Recent') THEN
            
            SET @cache_RSaggRV_ret= (SELECT step.source_value AS calculated_value FROM (
                    SELECT RD.source_value, COALESCE(oat.end_date, oay.end_date) as end_date,
                    COALESCE(DATEDIFF(oat.end_date, oat.start_date), DATEDIFF(oay.end_date, oay.start_date)) as length, RD.modified_at, RD.created_at
                    FROM org_person_riskvariable_datum AS RD
                    LEFT JOIN org_academic_year oay ON oay.id = RD.org_academic_year_id
                    LEFT JOIN org_academic_terms oat ON oat.id = RD.org_academic_terms_id
                    WHERE
                        RD.org_id=the_org_id 
                        AND RD.person_id=the_person_id 
                        AND RD.risk_variable_id=the_RV_id
                        AND (
                        (oay.id is null AND oat.id is null) OR
                        ((oat.end_date BETWEEN the_start_date AND the_end_date) AND RD.scope = 'T') OR
                        ((oay.end_date BETWEEN the_start_date AND the_end_date) AND RD.scope = 'Y')
                        )) as step
                    ORDER BY step.end_date DESC, step.length DESC, step.modified_at DESC, step.created_at DESC
                    LIMIT 1);
        ELSEIF(agg_type='Academic Update') THEN
            SET @cache_RSaggRV_ret=(
                #--TODO: resolve created_at vs. modified_at to audit/time-series dimensions
                SELECT COUNT(*) AS calculated_value
                FROM (
                    SELECT DISTINCT au.org_courses_id, au.failure_risk_level, au.grade
                    FROM academic_update AS au
                    INNER JOIN (
                        SELECT au_in.org_courses_id, au_in.org_id, au_in.person_id_student, max(au_in.modified_at) as modified_at
                        FROM academic_update AS au_in
                        INNER JOIN org_person_riskvariable AS RD
                            ON (RD.org_id,  RD.person_id) 
                            = (au_in.org_id, au_in.person_id_student)
                        LEFT JOIN risk_variable AS RV
                            ON RV.id=RD.risk_variable_id
                        WHERE 
                            RD.risk_variable_id = the_RV_id
                            AND (au_in.org_id,  au_in.person_id_student)
                                = (the_org_id,  the_person_id)
                            AND (au_in.failure_risk_level IS NOT NULL OR au_in.grade IS NOT NULL)
                            AND au_in.modified_at BETWEEN RV.calculation_start_date and RV.calculation_end_date
                        GROUP BY au_in.org_courses_id
                    ) AS au_mid
                        ON au.org_courses_id = au_mid.org_courses_id
                        AND au.modified_at = au_mid.modified_at
                        AND (au_mid.org_id, au_mid.person_id_student)
                            = (au.org_id,   au.person_id_student)
                ) AS most_recent
                WHERE upper(failure_risk_level)='HIGH' OR upper(grade) IN ('D','F','F/No Pass')
            );

        ELSE 
            SET @cache_RSaggRV_ret=NULL;
        END IF;
        
        RETURN @cache_RSaggRV_ret;
    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `RS_denominator` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` FUNCTION `RS_denominator`(the_org_id INT, the_group_id INT, the_person_id INT) RETURNS decimal(18,9)
    READS SQL DATA
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN  
	DECLARE RShitormiss BOOL;
	#--Optimization (use the last value generated if it matches parameters)
		SET RShitormiss=RS_setcache(the_org_id, the_group_id, the_person_id);
    
		RETURN @cache_RSdenom_ret;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `RS_numerator` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` FUNCTION `RS_numerator`(the_org_id INT, the_group_id INT, the_person_id INT) RETURNS decimal(18,9)
    READS SQL DATA
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN  
	DECLARE RShitormiss BOOL;
	#--Optimization (use the last value generated if it matches parameters)
		SET RShitormiss=RS_setcache(the_org_id, the_group_id, the_person_id);
	
		RETURN @cache_RSnumer_ret;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `RS_setcache` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` FUNCTION `RS_setcache`(the_org_id INT, the_group_id INT, the_person_id INT) RETURNS tinyint(1)
    READS SQL DATA
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN  

	#--Optimization (use the last value generated if it matches parameters)
		IF(the_person_id=@cache_RS_person_id AND the_org_id=@cache_RS_org_id AND the_group_id=@cache_RS_group_id AND @cache_RS_ts=NOW(6)+0) THEN
			RETURN TRUE;
		END IF;
		SET @cache_RS_person_id=the_person_id, @cache_RS_org_id=the_org_id, @cache_RS_group_id=the_group_id, @cache_RS_ts=NOW(6)+0;

		SELECT SUM(bucket_value*weight),  SUM(weight)
        INTO @cache_RSnumer_ret, @cache_RSdenom_ret
        FROM org_calculated_risk_variables_view 
        WHERE 
			org_id=the_org_id
            AND risk_group_id=the_group_id
            AND person_id=the_person_id
		;
	RETURN FALSE;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `safe_index_builder` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` FUNCTION `safe_index_builder`(tName varchar(255), theIndex varchar(255), theColumns varchar(255), isAdd bool, isUnique bool, setIgnore bool) RETURNS varchar(255) CHARSET latin1
    READS SQL DATA
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN
			SET @SQLreturn = '';
	        SET @indexExists = false;
	        SET @sIgnore = '';
	        SET @unq = '';
	        SET @REGEX = 'DROP |;|DELETE |ALTER |CREATE |DISABLE |ENABLE |TRUNCATE |UPDATE |MERGE |INSERT |FROM |SELECT |[/*#]|--|UNION ';
            
	        IF (select tNAME REGEXP @REGEX) OR (select theIndex REGEXP @REGEX) OR (select theColumns REGEXP @REGEX) THEN
				return @SQLreturn;
            END IF;

				IF setIgnore THEN
					SET @sIgnore = 'IGNORE ';
	            END IF;
	            
	            IF isUnique THEN
					SET @unq = 'UNIQUE ';
	            END IF;
	        
	        	IF (SELECT 1 FROM information_schema.statistics WHERE `table_schema` =  DATABASE() AND `table_name` = tName AND `index_name` = theIndex LIMIT 1) THEN
	                    SET @indexExists = true;
				ELSE     
						SET @indexExists = false;
				END IF;
	            
	            
	            IF (isAdd AND @indexExists) THEN 
					SET @SQLreturn = CONCAT('ALTER ', @sIgnore, 'TABLE `', DATABASE(), '`.`', tName, '` DROP INDEX `',  theIndex, '`, ','ADD ', @unq, 'INDEX `', theIndex, '` ', theColumns, ';');
	            ELSEIF (isAdd AND !@indexExists) THEN
					SET @SQLreturn = CONCAT('ALTER ', @sIgnore, 'TABLE `', DATABASE(), '`.`', tName, '` ADD ', @unq ,'INDEX `', theIndex, '` ', theColumns, ';');
	            ELSEIF (!isAdd AND @indexExists) then
					SET @SQLreturn = CONCAT('ALTER ', @sIgnore, 'TABLE `', DATABASE(), '`.`', tName, '` DROP INDEX `',  theIndex, '`;');
	            ELSE
					SET @SQLreturn = '';
	            END IF;
	                    
				RETURN @SQLreturn;
       
        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Academic_Update_Grade_Fixer` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Academic_Update_Grade_Fixer`()
BEGIN
                            UPDATE academic_update SET grade = 'F', modified_at = NOW() WHERE grade = 'F/No Pass';
                            UPDATE academic_update SET grade = 'P', modified_at = NOW() WHERE grade = 'Pass';
                        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Factor_Calc` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Factor_Calc`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
BEGIN


                                DECLARE the_ts TIMESTAMP;
                                SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

                                -- Finding the last time this calculation was ran from the risk_calc_tracking table
                                IF (SELECT 1 from synapse.risk_calc_tracking_table LIMIT 1) > 0 THEN
                                    SET @lastupdate = (SELECT max(last_update_ts) FROM synapse.risk_calc_tracking_table);
                                    SET @lastupdateISQ = (select max(last_update_ts) FROM synapse.risk_calc_tracking_table_ISQ);
                                ELSE
                                    SET @lastupdate = '1900-01-01 00:00:00';
                                    SET @lastupdateISQ = '1900-01-01 00:00:00';
                                end IF;


                                -- STARTING PROCESS OF FINDING NEW SURVEY RESPONSES
                                -- SETS THE APPROPRIATE PEOPLE TO CALCULATE FACTORS
                                CALL Factor_Find_Survey_Responses(@lastupdate);

                                -- STARTING PROCESS OF FINDING NEW SURVEY RESPONSES ISQs
                                -- SETS THE APPROPRIATE PEOPLE TO CALCULATE FACTORS
                                CALL Factor_Find_Survey_Responses_ISQ(@lastupdateISQ);

                                -- START ACTUAL FACTOR CALCULATION PROCESS
                                -- CYCLE THROUGH UNTIL DEADLINE OR NO REMAINING NULL FLAGS
                                WHILE
                                    (NOW() < deadline
                                    AND (SELECT 1
                                        FROM org_calc_flags_factor
                                        WHERE calculated_at IS NULL
                                        LIMIT 1) > 0)
                                DO

                                    SET the_ts=NOW();

                                    -- Setting a chunk to process
                                    UPDATE
                                        org_calc_flags_factor
                                    SET
                                        calculated_at=the_ts
                                    WHERE
                                        calculated_at IS NULL
                                    ORDER BY modified_at ASC
                                    LIMIT chunksize;

                                    -- calculating factors for chunk
                                    REPLACE INTO person_factor_calculated(organization_id, person_id, factor_id, survey_id, mean_value, created_at, modified_at)
                                        SELECT straight_join
                                            sr.org_id,
                                            sr.person_id,
                                            fq.factor_id,
                                            sr.survey_id,
                                            avg(sr.decimal_value) as mean_value,
                                            the_ts,
                                            the_ts
                                        FROM
                                            org_calc_flags_factor ocff
                                            INNER JOIN survey_response AS sr ON sr.org_id = ocff.org_id
                                                AND sr.person_id = ocff.person_id
                                            INNER JOIN survey_questions sq ON sq.id = sr.survey_questions_id
                                            INNER JOIN factor_questions fq ON fq.ebi_question_id = sq.ebi_question_id
                                        WHERE
                                            factor_id IS NOT NULL
                                            AND sr.survey_id = GET_MOST_RECENT_SURVEY(sr.org_id, sr.person_id)
                                            AND ocff.calculated_at = the_ts
                                            AND FLOOR(sr.decimal_value) != 99
                                        GROUP BY sr.org_id, sr.person_id, fq.factor_id, sr.survey_id;

                                    -- inserting student report flags to calculate for chunk
                                    INSERT INTO org_calc_flags_student_reports(org_id, person_id, survey_id, created_at, modified_at, calculated_at, file_name)
                                        SELECT
                                            ocff.org_id,
                                            ocff.person_id,
                                            GET_MOST_RECENT_SURVEY(ocff.org_id, ocff.person_id) as survey_id,
                                            the_ts,
                                            the_ts,
                                            NULL,
                                            NULL
                                        FROM
                                            org_calc_flags_factor AS ocff
                                        WHERE
                                            ocff.calculated_at = the_ts
                                    ON DUPLICATE KEY UPDATE calculated_at = NULL, file_name = NULL, modified_at = the_ts;

                                    -- setting talking point flags to calculate for chunk
                                    UPDATE
                                        org_calc_flags_talking_point ocftp
                                        INNER JOIN org_calc_flags_factor ocff ON ocff.org_id = ocftp.org_id
                                            AND ocff.person_id = ocftp.person_id
                                    SET
                                        ocftp.calculated_at= NULL,
                                        ocftp.modified_at = the_ts
                                    WHERE
                                        ocff.calculated_at = the_ts;

                                    -- setting risk flags to calculate for chunk
                                    UPDATE
                                        org_calc_flags_risk ocfr
                                        INNER JOIN org_calc_flags_factor ocff ON ocff.org_id = ocfr.org_id
                                            AND ocff.person_id = ocfr.person_id
                                    SET
                                        ocfr.calculated_at= NULL,
                                        ocfr.modified_at = the_ts
                                    WHERE
                                        ocff.calculated_at = the_ts;

                                    -- setting all successful flags to calculated with chunk timestamp (ie, not 99)
                                    UPDATE
                                        org_calc_flags_factor ocff
                                        INNER JOIN
                                            (SELECT straight_join
                                                sr.org_id,
                                                sr.person_id
                                            FROM
                                                org_calc_flags_factor ocff
                                                    INNER JOIN survey_response AS sr ON sr.org_id = ocff.org_id
                                                        AND sr.person_id = ocff.person_id
                                                    INNER JOIN survey_questions svq ON svq.id=sr.survey_questions_id
                                                    INNER JOIN factor_questions fq ON fq.ebi_question_id=svq.ebi_question_id
                                            WHERE
                                                factor_id IS NOT NULL
                                                AND ocff.calculated_at = the_ts
                                                AND FLOOR(sr.decimal_value) != 99) AS calc
                                        ON calc.org_id = ocff.org_id
                                            AND calc.person_id = ocff.person_id
                                    SET
                                        calculated_at = the_ts,
                                        modified_at = the_ts;

                                    -- marking all unsuccesful flags to have the 1900 flag for calculated and not valid
                                    UPDATE
                                        org_calc_flags_factor ocff
                                    SET
                                        ocff.calculated_at = '1900-01-01 00:00:00',
                                        ocff.modified_at = the_ts
                                    WHERE
                                        ocff.calculated_at = the_ts
                                        AND ocff.modified_at <> ocff.calculated_at;

                                END WHILE;
                        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Factor_Find_Survey_Responses` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Factor_Find_Survey_Responses`(last_update DATETIME)
BEGIN
                                SET @lastupdate = last_update;

                                -- inserting any new person/survey into tracking table that have occurred since last run
                                INSERT IGNORE INTO synapse.risk_calc_tracking_table(org_id, person_id, survey_id)
                                    (SELECT
                                        org_id,
                                        person_id,
                                        survey_id
                                    FROM
                                        synapse.survey_response
                                    WHERE
                                        synapse.survey_response.modified_at > @lastupdate
                                    GROUP BY org_id, person_id, survey_id);

                                SET @maxMod = (select max(modified_at) FROM synapse.survey_response);

                                -- Finding most recent response and updating everything in table to have the survey question id
                                UPDATE
                                    synapse.risk_calc_tracking_table
                                SET
                                    most_recent_survey_question_id = GET_MOST_RECENT_SURVEY_QUESTION(org_id, person_id, survey_id),
                                    last_update_ts=@maxMod
                                WHERE
                                    last_update_ts<@maxMod
                                    OR last_update_ts IS NULL;

                                -- If last seen survey question by risk_calc_tracking_table is different than current question
                                    -- trigger Factor Calculation
                                    -- set last seen question to most recent
                                    -- update modified_at date
                                UPDATE
                                    org_calc_flags_factor AS ocff
                                    INNER JOIN synapse.risk_calc_tracking_table rctt ON rctt.org_id = ocff.org_id AND rctt.person_id = ocff.person_id
                                        AND (rctt.most_recent_survey_question_id <> rctt.last_seen_survey_question_id
                                            OR rctt.last_seen_survey_question_id is null)
                                SET
                                    rctt.last_seen_survey_question_id = rctt.most_recent_survey_question_id,
                                    ocff.calculated_at = null,
                                    ocff.modified_at = CURRENT_TIMESTAMP();

                                -- Clean up all completed Surveys from risk_calc_tracking table for performance gain
                                DELETE
                                    rctt
                                FROM
                                    synapse.risk_calc_tracking_table rctt
                                    INNER JOIN org_person_student_survey_link opssl ON rctt.org_id = opssl.org_id
                                        AND rctt.person_id = opssl.person_id
                                        AND rctt.survey_id = opssl.survey_id
                                WHERE
                                    opssl.survey_completion_status = 'CompletedAll';

                         END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Factor_Find_Survey_Responses_ISQ` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Factor_Find_Survey_Responses_ISQ`(last_update_ISQ DATETIME)
BEGIN
                                SET @lastupdateISQ = last_update_ISQ;

                                -- inserting any new person/survey into tracking table that have occurred since last run
                                INSERT IGNORE INTO synapse.risk_calc_tracking_table_ISQ(org_id, person_id, survey_id)
                                    (SELECT
                                        org_id,
                                        person_id,
                                        survey_id
                                    FROM
                                        synapse.org_question_response
                                    WHERE
                                        synapse.org_question_response.modified_at > @lastupdateISQ
                                    GROUP BY org_id, person_id,survey_id);

                                SET @maxISQ = (SELECT max(modified_at) FROM synapse.org_question_response);

                                -- Finding most recent response and updating everything in table to have the survey question id
                                UPDATE
                                    synapse.risk_calc_tracking_table_ISQ
                                SET
                                    most_recent_org_question_id = GET_MOST_RECENT_ISQ(org_id, person_id, survey_id),
                                    last_update_ts=@maxISQ
                                WHERE
                                    last_update_ts<@maxISQ
                                    OR last_update_ts IS NULL;

                                -- If last seen survey question by risk_calc_tracking_table is different than current question
                                    -- trigger Factor Calculation
                                    -- set last seen question to most recent
                                    -- update modified_at date
                                UPDATE
                                    org_calc_flags_risk ocfr
                                    INNER JOIN synapse.risk_calc_tracking_table_ISQ rctt ON rctt.org_id = ocfr.org_id
                                        AND rctt.person_id = ocfr.person_id
                                        AND (rctt.most_recent_org_question_id <> rctt.last_seen_org_question_id
                                            OR rctt.last_seen_org_question_id IS NULL)
                                SET
                                    rctt.last_seen_org_question_id = rctt.most_recent_org_question_id,
                                    ocfr.calculated_at = NULL,
                                    ocfr.modified_at = CURRENT_TIMESTAMP();

                                -- Clean up all completed Surveys from risk_calc_tracking table for performance gain
                                DELETE
                                    rctt
                                FROM
                                    synapse.risk_calc_tracking_table_ISQ AS rctt
                                    INNER JOIN org_person_student_survey_link opssl ON rctt.org_id = opssl.org_id
                                        AND rctt.person_id = opssl.person_id
                                        AND rctt.survey_id = opssl.survey_id
                                WHERE
                                    opssl.survey_completion_status = 'CompletedAll';

                        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Faculty_Data_Dump` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Faculty_Data_Dump`(
IN orgId int(11),
IN slimit int(11),
IN soffset int(11)
)
BEGIN
START TRANSACTION;
SELECT
        IFNULL(p.external_id,"") AS ExternalId,
        IFNULL(p.auth_username,"") AS AuthUsername,
        IFNULL(p.firstname,"") AS Firstname,
        IFNULL(p.lastname,"") AS Lastname,
        IFNULL(p.title,"") AS Title,
        IFNULL(op.status, 1) AS IsActive,
        IFNULL(op.auth_key,"") AS FacultyAuthKey,
        IFNULL(ci.address_1,"") AS Address1,
        IFNULL(ci.address_2,"") AS Address2,
        IFNULL(ci.city,"") AS City,
        IFNULL(ci.state,"") AS State,
        IFNULL(ci.country,"") AS Country,
        IFNULL(ci.zip,"") AS Zip,
        IFNULL(ci.primary_mobile,"") AS PrimaryMobile,
        IFNULL(ci.alternate_mobile,"") AS AlternateMobile,
        IFNULL(ci.home_phone,"") AS HomePhone,
        IFNULL(ci.primary_email,"") AS PrimaryEmail,
        IFNULL(ci.alternate_email,"") AS AlternateEmail,
        IFNULL(ci.primary_mobile_provider,"") AS PrimaryMobileProvider,
        IFNULL(ci.alternate_mobile_provider,"") AS AlternateMobileProvider
FROM org_person_faculty as op
JOIN person as p on p.id = op.person_id
LEFT JOIN person_contact_info pci on pci.person_id = p.id
LEFT JOIN contact_info ci on ci.id = pci.contact_id
WHERE op.organization_id = orgId AND op.deleted_at IS NULL
LIMIT slimit OFFSET soffset;
COMMIT;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `fix_datum_src_ts` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `fix_datum_src_ts`()
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN
            UPDATE survey_response
            SET created_at = modified_at
            WHERE created_at IS NULL;
            
            UPDATE org_question_response
            SET created_at = modified_at
            WHERE created_at IS NULL;
        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Intent_Leave_Calc` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Intent_Leave_Calc`()
BEGIN

                        SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

                        TRUNCATE etldata.`person_intent_to_leave`;

                        INSERT IGNORE INTO etldata.`person_intent_to_leave`
                        SELECT
                            sr.person_id, itl.id, sr.modified_at
                        FROM
                            survey_response sr
                        JOIN survey_questions sq ON sr.survey_questions_id = sq.id AND sq.qnbr = 4
                        JOIN (SELECT person_id,
                                    MAX(SRin.modified_at) modified_at
                                FROM
                                    survey_response AS SRin
                                JOIN survey_questions SQin ON SRin.survey_questions_id = SQin.id AND SQin.qnbr = 4
                                WHERE SRin.modified_at >  NOW() - INTERVAL 1 DAY
                                GROUP BY person_id) sm on sr.person_id = sm.person_id AND sr.modified_at = sm.modified_at
                        INNER JOIN
                        intent_to_leave itl ON sr.decimal_value BETWEEN itl.min_value AND itl.max_value;

                        UPDATE person p
                        JOIN
                            etldata.`person_intent_to_leave` pitl ON p.id = pitl.person_id
                        SET
                            p.intent_to_leave = pitl.intent_to_leave,
                            p.intent_to_leave_update_date = pitl.intent_to_leave_update_date;

                        Call `Intent_Leave_Null_Fixer`();
                                    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Intent_Leave_Calc_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Intent_Leave_Calc_all`()
BEGIN

                        SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

                        TRUNCATE etldata.`person_intent_to_leave`;

                        INSERT IGNORE INTO etldata.`person_intent_to_leave`
                        SELECT
                            sr.person_id, itl.id, sr.modified_at
                        FROM
                            survey_response sr
                        JOIN survey_questions sq ON sr.survey_questions_id = sq.id AND sq.qnbr = 4
                        JOIN (SELECT person_id,
                                    MAX(SRin.modified_at) modified_at
                                FROM
                                    survey_response AS SRin
                                JOIN survey_questions SQin ON SRin.survey_questions_id = SQin.id AND SQin.qnbr = 4
                                GROUP BY person_id) sm on sr.person_id = sm.person_id AND sr.modified_at = sm.modified_at
                        INNER JOIN
                        intent_to_leave itl ON sr.decimal_value BETWEEN itl.min_value AND itl.max_value;

                        UPDATE person p
                        JOIN
                            etldata.`person_intent_to_leave` pitl ON p.id = pitl.person_id
                        SET
                            p.intent_to_leave = pitl.intent_to_leave,
                            p.intent_to_leave_update_date = pitl.intent_to_leave_update_date;

                        Call `Intent_Leave_Null_Fixer`();
                                    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Intent_Leave_Null_Fixer` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Intent_Leave_Null_Fixer`()
BEGIN
 
          WHILE (select 1 from person p INNER JOIN org_person_student ops On p.organization_id = ops.organization_id AND p.id = ops.person_id where intent_to_leave is null LIMIT 1) = 1 DO
          update person as per INNER JOIN (select p.id as person_id FROM person p INNER JOIN org_person_student ops 
            On p.organization_id = ops.organization_id AND p.id = ops.person_id where p.intent_to_leave is null ORDER BY p.id LIMIT 1000) as t On per.id = t.person_id  SET per.intent_to_leave = 5, per.intent_to_leave_update_date = CURRENT_TIMESTAMP();
            
          END WHILE;


        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `isq_data_transfer` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `isq_data_transfer`()
BEGIN
                        -- Main insert of new survey responses
                        INSERT IGNORE INTO synapse.org_question_response
                        (org_id,
                        person_id,
                        survey_id,
                        org_academic_year_id,
                        org_academic_terms_id,
                        org_question_id,
                        modified_at,
                        response_type,
                        decimal_value,
                        char_value,
                        charmax_value,
                        multi_response_id,
                        org_question_options_id)
                        (SELECT
                        org_id,
                        person_id,
                        survey_id,
                        org_academic_year_id,
                        org_academic_terms_id,
                        org_question_id,
                        modified_at,
                        response_type,
                        decimal_value,
                        char_value,
                        charmax_value,
                        multi_response_id,
                        org_question_options_id
                        FROM etldata.org_question_response WHERE modified_at >= (SELECT MAX(modified_at) FROM synapse.org_question_response));

                        -- Update questions that have been reanswered since the last update time
                        -- Set org_calc_flags_risk 
                        -- if questions are re-answered
                        -- IF YOU DO NOT WANT TO TRIGGER FLAGS COMMENT OUT BELOW
                        UPDATE synapse.org_question_response oqr
                        INNER JOIN synapse.org_calc_flags_risk ocr
                        ON oqr.person_id  = ocr.person_id
                        and oqr.org_id = ocr.org_id
                        INNER JOIN etldata.org_question_response eoqr 
                        ON oqr.person_id = eoqr.person_id
                        AND oqr.org_id = eoqr.org_id
                        AND oqr.survey_id = eoqr.survey_id
                        AND oqr.org_question_id = eoqr.org_question_id
                        AND oqr.multi_response_id = eoqr.multi_response_id
                        SET
                        oqr.decimal_value = eoqr.decimal_value,
                        oqr.char_value = eoqr.char_value,
                        oqr.charmax_value = eoqr.charmax_value,
                        oqr.modified_at = NOW(),
                        ocr.calculated_at = NULL,
                        ocr.modified_at = NOW()
                        WHERE (oqr.decimal_value <> eoqr.decimal_value OR
                        oqr.char_value <> eoqr.char_value OR
                        oqr.charmax_value <> eoqr.charmax_value )
                        AND oqr.modified_at <= eoqr.modified_at
                        AND eoqr.modified_at > (SELECT MAX(last_update_ts) FROM etldata.last_response_update LIMIT 1);

                        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `IssueCalcTempTables` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `IssueCalcTempTables`()
BEGIN
DROP TABLE IF EXISTS issues_temp_calc_perm;
CREATE TABLE `issues_temp_calc_perm` (
`org_id` int(11) DEFAULT NULL,
`staff_id` int(11) DEFAULT NULL,
`student_id` int(11) DEFAULT NULL,
INDEX `org_staff_student` (`org_id` ASC, `staff_id` ASC, `student_id` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS issues_temp_calc_num;
CREATE TABLE `issues_temp_calc_num` (
`org_id` int(11) DEFAULT NULL,
`staff_id` int(11) DEFAULT NULL,
`survey_id` int(11) DEFAULT NULL,
`issue_id` int(11) DEFAULT NULL,
`cohort_code` int(11) DEFAULT NULL,
`student_id` int(11) DEFAULT NULL,
`survey_questions_id` int(11) DEFAULT NULL,
`factor_id` int(11) DEFAULT NULL,
`response_type` varchar(50) DEFAULT NULL,
`decimal_value` decimal(10,4) DEFAULT NULL,
`char_value` int(11) DEFAULT NULL,
`charmax_value` int(11) DEFAULT NULL,
INDEX `org_staff_student_survey_factor` (`org_id` ASC, `staff_id` ASC, `student_id` ASC, `survey_id` ASC, `factor_id` ASC),
INDEX `org_staff_student_survey_issue` (`org_id` ASC, `staff_id` ASC, `student_id` ASC, `survey_id` ASC, `issue_id` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS issues_temp_calc_den;
CREATE TABLE `issues_temp_calc_den` (
`org_id` int(11) DEFAULT NULL,
`issue_id` int(11) DEFAULT NULL,
`count_students` int(11) DEFAULT NULL,
`staff_id` int(11) DEFAULT NULL, 
INDEX  `org_staff_issue` (`org_id` ASC, `staff_id` ASC, `issue_id` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS issues_temp_calc_done;
CREATE TABLE `issues_temp_calc_done` (
`org_id` int(11) DEFAULT NULL,
`staff_id` int(11) DEFAULT NULL,
INDEX `org_staff` (`org_id` ASC, `staff_id` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `org_RiskFactorCalculation` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `org_RiskFactorCalculation`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN
            DECLARE the_ts TIMESTAMP;
            #--DECLARE chunksize INT UNSIGNED DEFAULT 25;

            #--Fix source data timestamps
            CALL fix_datum_src_ts();

            #--Sacrifice some temporal precision for reduced resource contention
            SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

            WHILE(
                NOW() < deadline
                AND (SELECT 1 FROM org_calc_flags_risk WHERE calculated_at IS NULL LIMIT 1) > 0
            ) DO
                SET the_ts=NOW();

            #--Carve out a limited chunk to materialize
                TRUNCATE etldata.`tmp_ocfr_chunk`;

                INSERT IGNORE INTO etldata.`tmp_ocfr_chunk`
                SELECT id from org_calc_flags_risk
                WHERE calculated_at IS NULL
                ORDER BY modified_at ASC
                LIMIT chunksize;

                UPDATE org_calc_flags_risk ocfr
                JOIN etldata.`tmp_ocfr_chunk` toc on ocfr.id = toc.id
                SET calculated_at=the_ts, modified_at = the_ts;

            #--Materialize the intermediate view
                INSERT IGNORE INTO org_calculated_risk_variables_history (person_id, risk_variable_id, risk_group_id, risk_model_id, created_at, org_id, calc_bucket_value, calc_weight, risk_source_value)
                SELECT
                    OCRV.person_id,
                    OCRV.risk_variable_id,
                    OCRV.risk_group_id,
                    OCRV.risk_model_id,
                    the_ts AS created_at,
                    OCRV.org_id,
                    bucket_value AS calc_bucket_value,
                    bucket_value*weight AS calc_weight,
                    calculated_value AS risk_source_value
                FROM org_calculated_risk_variables_view AS OCRV
                INNER JOIN (
                    SELECT person_id FROM org_calc_flags_risk
                    WHERE
                        calculated_at=the_ts
                ) AS stale
                    ON stale.person_id=OCRV.person_id
                ;

            #--Materialize the risk score view
                INSERT IGNORE INTO person_risk_level_history(person_id,date_captured,risk_model_id,risk_level,risk_score,weighted_value,maximum_weight_value)
                SELECT
                    prlc.person_id,
                    the_ts,
                    prlc.risk_model_id,
                    prlc.risk_level,
                    prlc.risk_score,
                    prlc.weighted_value,
                    prlc.maximum_weight_value
                FROM person_risk_level_calc AS prlc
                INNER JOIN (
                    SELECT person_id FROM org_calc_flags_risk
                    WHERE
                        calculated_at=the_ts
                ) AS stale
                    ON stale.person_id=prlc.person_id
                #--WHERE prlc.risk_score IS NOT NULL
                ;


            #--Update the redundant person value for risk score
                #-- WE SHOULD JUST USE THE person_risk_level_history table...USING THE LATEST VALUE INSTEAD OF STORING IT AGAIN IN THE person TABLE
                UPDATE person P
                INNER JOIN person_risk_level_history AS PRH
                    ON P.id=PRH.person_id
                    AND PRH.date_captured=the_ts
                SET
                    P.risk_level=PRH.risk_level,
                    P.risk_update_date=the_ts
                where not PRH.risk_score <=> (select risk_score from person_risk_level_history pr where pr.person_id = P.id ORDER BY date_captured DESC LIMIT 1, 1) ;


            #--Set magic dates for blank scores (lame)
                UPDATE org_calc_flags_risk AS OCFR
                LEFT JOIN person_risk_level_history AS PRLH
                    ON PRLH.person_id=OCFR.person_id
                    AND PRLH.date_captured=the_ts
                SET OCFR.calculated_at='1900-01-01 00:00:00'
                WHERE OCFR.calculated_at=the_ts AND PRLH.weighted_value IS NULL
                ;
            END WHILE;

        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Report_Calc` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Report_Calc`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
BEGIN
        DECLARE the_ts TIMESTAMP;
        SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
        
        
        
        WHILE(NOW() < deadline 
        AND (SELECT 1 FROM org_calc_flags_student_reports WHERE calculated_at IS NULL LIMIT 1) > 0)
        DO
            
            SET the_ts=NOW();       
            
            UPDATE org_calc_flags_student_reports
            SET calculated_at=the_ts
            WHERE calculated_at IS NULL
            ORDER BY modified_at ASC
            LIMIT chunksize;
            
            
            
                
                    REPLACE INTO report_calculated_values (org_id,person_id,report_id,section_id,element_id,element_bucket_id,survey_id,calculated_value,created_at,modified_at)
                              select pfc.organization_id, pfc.person_id, rs.report_id, rse.section_id,reb.element_id,reb.id as element_bucket_id,pfc.survey_id,pfc.mean_value as calc_value, the_ts, the_ts
                                  from reports r
                                    inner join report_sections rs on rs.report_id = r.id
                                    inner join report_section_elements rse on rse.section_id = rs.id and rse.source_type='F' and rse.factor_id is not null
                                    inner join report_element_buckets reb on reb.element_id = rse.id
                                    inner join person_factor_calculated pfc on pfc.factor_id = rse.factor_id
                                    inner join org_calc_flags_student_reports orc ON pfc.person_id = orc.person_id and pfc.organization_id = orc.org_id
                                    where pfc.survey_id = get_most_recent_survey(orc.org_id, orc.person_id) 
                                    and pfc.modified_at = (select modified_at from person_factor_calculated as fc
                                        where fc.organization_id = pfc.organization_id 
                                        AND fc.person_id = pfc.person_id 
                                        AND fc.factor_id = pfc.factor_id 
                                        AND fc.survey_id = pfc.survey_id 
                                        ORDER BY modified_at DESC LIMIT 1)
                                    and r.name='student-report'
                                    and pfc.mean_value between reb.range_min and reb.range_max 
                                    and orc.calculated_at = the_ts
                                  group by pfc.organization_id, person_id,report_id,section_id,element_id,survey_id
                                union all
                                select sr.org_id, sr.person_id, rs.report_id, rse.section_id,reb.element_id,reb.id as element_bucket_id,sr.survey_id,sr.decimal_value as calc_value, the_ts, the_ts
                                  from reports r
                                    inner join report_sections rs on rs.report_id = r.id
                                    inner join report_section_elements rse on rse.section_id = rs.id and rse.source_type='Q' and rse.survey_question_id is not null
                                    inner join report_element_buckets reb on reb.element_id = rse.id
                                    inner join survey_response sr on sr.survey_questions_id=rse.survey_question_id and sr.decimal_value is not null
                                    inner join org_calc_flags_student_reports orc ON sr.person_id = orc.person_id and sr.org_id = orc.org_id
                                    and sr.survey_id = get_most_recent_survey(orc.org_id, orc.person_id) 
                                    and r.name='student-report'
                                    and sr.decimal_value between reb.range_min and reb.range_max
                                    and orc.calculated_at = the_ts
                                  group by sr.org_id, sr.person_id,rs.report_id,rse.section_id,reb.element_id,sr.survey_id;
                              
                            
                    set @reportId := (select id from reports where name='student-report');
            
            
                    UPDATE org_calc_flags_student_reports SET survey_id = get_most_recent_survey(org_id, person_id), report_id = @reportId, modified_at = the_ts 
                      where calculated_at = the_ts;
                                                            
                        insert into report_calc_history (report_id,org_id,person_id,survey_id,created_at,modified_at) 
                        (select report_id, org_id, person_id, survey_id, the_ts, the_ts from org_calc_flags_student_reports where calculated_at = the_ts);     
            
                update org_calc_flags_student_reports orcf
                LEFT JOIN report_calculated_values rcv ON rcv.org_id = orcf.org_id 
                AND rcv.person_id = orcf.person_id  AND rcv.survey_id = orcf.survey_id AND rcv.report_id = orcf.report_id
                set orcf.calculated_at = '1900-01-01 00:00:00', orcf.modified_at = the_ts 
                WHERE orcf.calculated_at = the_ts AND (rcv.survey_id is null OR rcv.person_id is null
                OR rcv.report_id is null OR rcv.section_id is null OR rcv.element_id is null);
                
                    
            set @reportID := null;
            
            
            

        end WHILE;
                
    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Student_Data_Dump` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Student_Data_Dump`(
        IN orgId int(11),
        IN slimit int(11),
        IN soffset int(11)
        )
BEGIN
    SET group_concat_max_len = 1000000;
    DROP TEMPORARY TABLE IF EXISTS temppivot;

    DROP TEMPORARY TABLE IF EXISTS listofmetacols;
    CREATE TEMPORARY TABLE listofmetacols (`meta_key` VARCHAR(50) NOT NULL, `em_id` int(11) NULL, `om_id` int(11) NULL, PRIMARY KEY (`meta_key`));
    DROP TEMPORARY TABLE IF EXISTS listofpeeps;
    CREATE TEMPORARY TABLE listofpeeps (`person_id` int(11) NOT NULL,PRIMARY KEY (`person_id`));

    #--This can populate first without actually having panaramic photo errors
    INSERT INTO listofmetacols
        SELECT    meta_key, em.id AS em_id, NULL AS om_id
        FROM      ebi_metadata em
        WHERE     deleted_at IS NULL
        UNION ALL
        SELECT    meta_key, NULL AS em_id, om.id AS om_id
        FROM      org_metadata om
        WHERE     organization_id = orgId and deleted_at IS NULL
    ;

    #--This can also populate first without actually having panaramic photo errors
    INSERT INTO listofpeeps
    (
    select op.person_id AS person_id
    from org_person_student op
    where op.organization_id = orgId AND op.deleted_at IS NULL
    limit slimit offset soffset
    );

    SET @tempsql = NULL;
    SELECT CONCAT("CREATE TEMPORARY TABLE `temppivot` (`person_id` int(11) NOT NULL,`year_id` CHAR(6) NOT NULL,`term_id` varchar(12) NOT NULL,", Group_concat("`", meta_key, "` text COLLATE utf8_unicode_ci DEFAULT NULL"), ",PRIMARY KEY (`person_id`, `year_id`, `term_id`))")
    INTO   @tempsql
    FROM   listofmetacols AS md;
    PREPARE stmt FROM @tempsql;
    EXECUTE stmt;

    START TRANSACTION READ ONLY;

    SELECT Group_concat(DISTINCT Concat( '
        MAX(IF(md.meta_key = ''', meta_key, ''', md.metadata_value, "")) AS `', meta_key , '`' ) )
    INTO   @sql
    FROM listofmetacols AS md;
    SET @sql = concat('INSERT INTO `temppivot` SELECT person_id, year_id, term_id, ',
    @sql, '
    FROM (
        (SELECT STRAIGHT_JOIN
        LP.person_id,
        IFNULL(oay.year_id,"") AS year_id,
        IFNULL(oat.term_code,"") AS term_id,
        LM.meta_key,
        IFNULL(COALESCE(pem.metadata_value, pom.metadata_value),"") AS metadata_value
    #--Construct the base product
    FROM listofpeeps AS LP
    CROSS JOIN listofmetacols AS LM
    #--Attach EBI metadata
    LEFT JOIN person_ebi_metadata AS pem
        ON pem.person_id = LP.person_id
    AND pem.ebi_metadata_id = LM.em_id
    #--Attach ORG metadata
    LEFT JOIN person_org_metadata AS pom
        ON pom.person_id = LP.person_id
    AND pom.org_metadata_id = LM.om_id
    #--Add bonus data
    LEFT JOIN org_academic_year AS oay
        ON oay.id = COALESCE(pem.org_academic_year_id, pom.org_academic_year_id)
    LEFT JOIN org_academic_terms AS oat
        ON oat.id = COALESCE(pem.org_academic_terms_id, pom.org_academic_periods_id)
    #--Order it...on second thought...order last so we can use names
    #--ORDER BY person_id, oay.year_id, oat.term_code ASC
    )
    union
    ( select person_id ,oay.year_id,"" as term_id,"" as meta_key,"" as meta_val from org_person_student_cohort as opc
left join org_academic_year as oay ON opc.org_academic_year_id = oay.id where opc.organization_id = ',orgId,')
    )
    AS
      md GROUP BY 1,2,3 #--the numbers designate columns in the result'
    );


    PREPARE stmt FROM @sql;
    EXECUTE stmt;



    SET @metalist = NULL;
    SELECT CONCAT("ExternalId,AuthUsername,Firstname,Lastname,Title,RecordType,StudentPhoto,IsActive,SurveyCohort,TransitiononeReceiveSurvey,CheckuponeReceiveSurvey,TransitiontwoReceiveSurvey,CheckuptwoReceiveSurvey,YearId,TermId,PrimaryConnect,RiskGroupId,StudentAuthKey,IsPrivacyPolicyAccepted,PrivacyPolicyAcceptedDate,Address1,Address2,City,State,Country,Zip,PrimaryMobile,AlternateMobile,HomePhone,PrimaryEmail,AlternateEmail,PrimaryMobileProvider,AlternateMobileProvider,", Group_concat("`", meta_key, "`"))
    INTO   @metalist
    FROM   listofmetacols AS md;

    SET @resultsql = CONCAT('
    SELECT distinct ',@metalist,' FROM (SELECT

    distinct
        IFNULL(p.external_id,"") AS ExternalId,
        IFNULL(p.auth_username,"") AS AuthUsername,
        IFNULL(p.firstname,"") AS Firstname,
        IFNULL(p.lastname,"") AS Lastname,
        IFNULL(pinfo.title,"") AS Title,
        IFNULL(pinfo.record_type,"") AS RecordType,
        IFNULL(op.photo_url, "") AS StudentPhoto,
        IFNULL(op.status, 0) AS IsActive,
        IFNULL(surveyResponse.cohort, "") AS SurveyCohort,

        TransitiononeReceiveSurvey,
        CheckuponeReceiveSurvey,
        TransitiontwoReceiveSurvey,
        CheckuptwoReceiveSurvey,

        surveyResponse.year_id AS YearId,
        surveyResponse.term_id AS TermId,
        IFNULL(pc.external_id, "") AS PrimaryConnect,
        IFNULL(rh.risk_group_id, "") AS RiskGroupId,
        IFNULL(opinfo.auth_key,"") AS StudentAuthKey,
        IFNULL(op.is_privacy_policy_accepted,"") AS IsPrivacyPolicyAccepted,
        IFNULL(op.privacy_policy_accepted_date, "") AS PrivacyPolicyAcceptedDate,
        IFNULL(ci.address_1,"") AS Address1,
        IFNULL(ci.address_2,"") AS Address2,
        IFNULL(ci.city,"") AS City,
        IFNULL(ci.state,"") AS State,
        IFNULL(ci.country,"") AS Country,
        IFNULL(ci.zip,"") AS Zip,
        IFNULL(ci.primary_mobile,"") AS PrimaryMobile,
        IFNULL(ci.alternate_mobile,"") AS AlternateMobile,
        IFNULL(ci.home_phone,"") AS HomePhone,
        IFNULL(cinfo.primary_email,"") AS PrimaryEmail,
        IFNULL(ci.alternate_email,"") AS AlternateEmail,
        IFNULL(ci.primary_mobile_provider,"") AS PrimaryMobileProvider,
        IFNULL(ci.alternate_mobile_provider,"") AS AlternateMobileProvider,
        innie.*
    from

     (SELECT
        TransitiononeReceiveSurvey,
            CheckuponeReceiveSurvey,
            TransitiontwoReceiveSurvey,
            CheckuptwoReceiveSurvey,
            person_id,
            year_id,
            organization_id,
            "" AS term_id,
            ifnull(cohort, "") as cohort
    FROM
        (SELECT
        SUM(TransitiononeReceiveSurvey) AS TransitiononeReceiveSurvey,
            SUM(CheckuponeReceiveSurvey) AS CheckuponeReceiveSurvey,
            SUM(TransitiontwoReceiveSurvey) AS TransitiontwoReceiveSurvey,
            SUM(CheckuptwoReceiveSurvey) AS CheckuptwoReceiveSurvey,
            person_id,
            year_id,
            survey_Response.organization_id,
            cohort
    FROM
        (SELECT
        IF(SL.survey_id IN (SELECT
                    MIN(id)
                FROM
                    survey
                WHERE
                    year_id IS NOT NULL
                GROUP BY year_id), opss1.receive_survey, NULL) AS TransitiononeReceiveSurvey,
            IF(SL.survey_id IN (SELECT
                    MIN(id) + 1
                FROM
                    survey
                WHERE
                    year_id IS NOT NULL
                GROUP BY year_id), opss1.receive_survey, NULL) AS CheckuponeReceiveSurvey,
            IF(SL.survey_id IN (SELECT
                    MAX(id) - 1
                FROM
                    survey
                WHERE
                    year_id IS NOT NULL
                GROUP BY year_id), opss1.receive_survey, NULL) AS TransitiontwoReceiveSurvey,
            IF(SL.survey_id IN (SELECT
                    MAX(id)
                FROM
                    survey
                WHERE
                    year_id IS NOT NULL
                GROUP BY year_id), opss1.receive_survey, NULL) AS CheckuptwoReceiveSurvey,
            ops.person_id,
            org_academic_year.year_id,
            org_academic_year.organization_id AS organization_id,
            opsc.cohort
    FROM
        org_person_student AS ops
    CROSS JOIN org_academic_year ON org_academic_year.organization_id = ops.organization_id AND org_academic_year.deleted_at IS NULL
    LEFT JOIN org_person_student_survey AS opss1 ON opss1.person_id = ops.person_id
        AND opss1.deleted_at IS NULL
    LEFT JOIN survey AS s ON (opss1.survey_id = s.id)
        AND org_academic_year.year_id = s.year_id
    LEFT JOIN survey_lang AS SL ON s.id = SL.survey_id
    LEFT JOIN org_person_student_cohort opsc ON opsc.person_id = ops.person_id
        AND opsc.org_academic_year_id = org_academic_year.id AND opsc.deleted_at IS NULL
    WHERE
        ops.organization_id = ',orgId,'
        ) AS survey_Response
    GROUP BY person_id , year_id) AS SR UNION SELECT
        NULL AS a,
            NULL AS b,
            NULL AS c,
            NULL AS d,
            person_id,
            "" AS e,
            organization_id,
            "" AS f,
            "" AS g
    FROM
        org_person_student
    WHERE
        organization_id = ',orgId,'
        AND org_person_student.deleted_at IS NULL
        UNION SELECT
        NULL,
            NULL,
            NULL,
            NULL,
            person_id,
            org_academic_year.year_id,
            org_person_student.organization_id,
            org_academic_terms.term_code,
            ""
    FROM
        org_person_student
    CROSS JOIN org_academic_terms ON org_academic_terms.organization_id = org_person_student.organization_id AND org_academic_terms.deleted_at IS NULL
    INNER JOIN org_academic_year ON org_academic_terms.org_academic_year_id = org_academic_year.id AND org_academic_year.deleted_at IS NULL

    WHERE
        org_person_student.organization_id = ',orgId,'
-- ORDER BY person_id , year_id

) as surveyResponse

    left join listofpeeps l on l.person_id = surveyResponse.person_id


     left join person p on    p.deleted_at IS NULL and p.id = l.person_id and p.id = surveyResponse.person_id
     left join temppivot AS innie on innie.person_id = p.id and surveyResponse.term_id = innie.term_id and surveyResponse.year_id = innie.year_id
     left join org_person_student op on op.deleted_at IS NULL and
 op.person_id = l.person_id

     left join org_academic_year as oay on    oay.deleted_at IS NULL and ((surveyResponse.year_id = oay.year_id) and (oay.organization_id = op.organization_id ))
     left join org_person_student_cohort as opsc ON       opsc.deleted_at IS NULL and ((p.id  = opsc.person_id) and (p.organization_id =  opsc.organization_id) and (oay.id = opsc.org_academic_year_id ) and (surveyResponse.term_id = "")) and surveyResponse.cohort = opsc.cohort

     left join person pinfo on pinfo.deleted_at IS NULL and pinfo.id = innie.person_id AND innie.year_id="" AND innie.term_id="" AND (innie.year_id="" or innie.year_id is null) #--We do this so that some facts only appear on the non-year/non-term row
     left join person_contact_info pci on     pci.deleted_at IS NULL and pci.person_id = pinfo.id
     left join contact_info ci on ci.id = pci.contact_id
     left join person_contact_info pcinfo on pcinfo.deleted_at IS NULL and pcinfo.person_id = p.id
     left join contact_info cinfo on cinfo.id = pcinfo.contact_id
     left join org_person_student opinfo on opinfo.person_id = pinfo.id
     left join person as pc on    pc.deleted_at IS NULL AND pc.id = op.person_id_primary_connect
     left join risk_group_person_history as rh on rh.person_id = (SELECT rgh.person_id FROM risk_group_person_history as rgh WHERE rgh.person_id = p.id ORDER BY rgh.assignment_date DESC LIMIT 1)
      where
p.external_id != "" #-- remove rows that do not have external_ids

AND
(
!(TransitiononeReceiveSurvey IS NULL AND CheckuponeReceiveSurvey IS NULL AND TransitiontwoReceiveSurvey IS NULL AND CheckuptwoReceiveSurvey IS NULL
AND innie.person_id IS NULL)
)
     ORDER BY p.firstname, p.lastname, ExternalId, YearId, TermId ASC
    ) AS results
    ');

    PREPARE stmt FROM @resultsql;
    EXECUTE stmt;

    COMMIT;

    SET group_concat_max_len = 1024;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Success_Marker_Calc` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Success_Marker_Calc`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
BEGIN
        DECLARE the_ts TIMESTAMP;
        SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
        
        WHILE(
        NOW() < deadline
        AND (SELECT 1 FROM org_calc_flags_success_marker WHERE calculated_at IS NULL LIMIT 1) > 0
        ) DO
            SET the_ts=NOW(); 
        
            UPDATE org_calc_flags_success_marker
            SET calculated_at=the_ts
            WHERE calculated_at IS NULL
            ORDER BY modified_at ASC
            LIMIT chunksize;        
      
            INSERT IGNORE INTO success_marker_calculated(organization_id,person_id,surveymarker_questions_id, color, created_at, modified_at)
                SELECT 
                    ocfsm.org_id,
                    pfc.person_id,
                    smq.id,
                    CASE WHEN pfc.mean_value BETWEEN red_low AND red_high THEN 'red'
                         WHEN pfc.mean_value BETWEEN yellow_low AND yellow_high THEN 'yellow'
                         WHEN pfc.mean_value between green_low AND green_high THEN 'green' end AS color, 
                    the_ts,
                    the_ts
                FROM 
                    surveymarker_questions AS smq
                        INNER JOIN person_factor_calculated pfc ON smq.factor_id=pfc.factor_id
                            AND (pfc.mean_value BETWEEN red_low AND red_high 
                                OR pfc.mean_value BETWEEN yellow_low AND yellow_high 
                                OR pfc.mean_value BETWEEN green_low AND green_high) 
                            AND smq.survey_id = pfc.survey_id
                            AND smq.ebi_question_id IS NULL 
                            AND smq.survey_questions_id IS NULL
                            AND smq.factor_id IS NOT NULL
                        INNER JOIN org_calc_flags_success_marker AS ocfsm ON pfc.person_id=ocfsm.person_id 
                            AND pfc.organization_id=ocfsm.org_id
                            AND ocfsm.calculated_at = the_ts
                            AND pfc.survey_id = get_most_recent_survey(ocfsm.org_id, pfc.person_id)
                            AND pfc.modified_at = (
                                SELECT 
                                    modified_at 
                                FROM 
                                    person_factor_calculated AS fc
                                WHERE 
                                    fc.organization_id = pfc.organization_id 
                                    AND fc.person_id = pfc.person_id 
                                    AND fc.factor_id = pfc.factor_id 
                                    AND fc.survey_id = pfc.survey_id 
                                ORDER BY modified_at DESC LIMIT 1)
                GROUP BY org_id, person_id, id
            UNION
                SELECT 
                    ocfsm.org_id,
                    svr.person_id,
                    smq.id,
                    CASE WHEN svr.decimal_value BETWEEN red_low AND red_high THEN 'red'
                        WHEN svr.decimal_value BETWEEN yellow_low AND yellow_high THEN 'yellow'
                        WHEN svr.decimal_value BETWEEN green_low AND green_high THEN 'green' END AS color,
                    the_ts,
                    the_ts
                FROM 
                    surveymarker_questions smq 
                        INNER JOIN survey_questions svq ON smq.ebi_question_id=svq.ebi_question_id
                            AND svq.survey_id = smq.survey_id
                        INNER JOIN survey_response svr ON svq.id=svr.survey_questions_id
                            AND (svr.decimal_value BETWEEN red_low AND red_high
                                OR svr.decimal_value BETWEEN yellow_low AND yellow_high 
                                OR svr.decimal_value BETWEEN green_low AND green_high)
                            AND svr.survey_id = svq.survey_id
                            AND smq.ebi_question_id IS NOT NULL 
                            AND smq.factor_id IS NULL
                        INNER JOIN org_calc_flags_success_marker ocfsm ON svr.person_id=ocfsm.person_id
                            AND ocfsm.calculated_at = the_ts
                WHERE 
                    svr.survey_id = get_most_recent_survey(ocfsm.org_id, svr.person_id)
                GROUP BY 
                    ocfsm.org_id, svr.person_id, smq.id;

            UPDATE org_calc_flags_success_marker ocfsm 
                LEFT JOIN success_marker_calculated AS smc ON smc.organization_id = ocfsm.org_id 
                    AND smc.person_id = ocfsm.person_id 
            SET 
                ocfsm.calculated_at = '1900-01-01 00:00:00', 
                ocfsm.modified_at = the_ts 
            WHERE 
                (smc.modified_at != the_ts OR smc.modified_at IS NULL) 
                AND ocfsm.calculated_at = the_ts;
            
            UPDATE org_calc_flags_success_marker ocfsm 
                LEFT JOIN success_marker_calculated AS smc ON smc.organization_id = ocfsm.org_id 
                    AND smc.person_id = ocfsm.person_id 
            SET 
                ocfsm.calculated_at = the_ts, 
                ocfsm.modified_at = the_ts 
            WHERE 
                smc.modified_at = the_ts;
        END WHILE;
    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `survey_data_transfer` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `survey_data_transfer`()
BEGIN

                        SELECT NOW() INTO @current;

                        -- Main insert of new survey responses
                        INSERT IGNORE INTO synapse.survey_response
                        (SELECT * FROM etldata.survey_response WHERE modified_at > (SELECT MAX(modified_at) FROM synapse.survey_response));

                        -- Cleanup insert for responses missed in down windows
                        INSERT IGNORE INTO synapse.survey_response (survey_questions_id, org_id, person_id, survey_id, org_academic_year_id, org_academic_terms_id, modified_at, response_type, decimal_value, char_value, charmax_value) (
                        SELECT ers.survey_questions_id, ers.org_id, ers.person_id, ers.survey_id, ers.org_academic_year_id, ers.org_academic_terms_id, @current, ers.response_type, ers.decimal_value, ers.char_value, ers.charmax_value
                        FROM etldata.survey_response ers
                            LEFT OUTER JOIN synapse.survey_response sr ON
                            (ers.person_id , ers.org_id, ers.survey_id, ers.survey_questions_id) =
                            (sr.person_id , sr.org_id, sr.survey_id, sr.survey_questions_id)
                        WHERE
                            sr.id IS NULL
                            AND ers.modified_at >= (@current - INTERVAL 12 HOUR)
                        );

                        -- Update Has_Responses values to match Synapse survey responses
                        UPDATE synapse.org_person_student_survey_link opssl
                        LEFT JOIN synapse.survey_response sr ON sr.person_id = opssl.person_id AND sr.survey_id = opssl.survey_id
                        SET opssl.Has_Responses = 'Yes'
                        WHERE sr.id IS NOT NULL AND opssl.Has_Responses = 'No';

                        -- Update questions that have been reanswered since the last update time
                        -- Set org_calc_flags_factor calculation flag so if questions are re-answered
                        -- you do not end up with missed calculations risk, talking points, 
                        -- student reports, and success markers
                        -- IF YOU DO NOT WANT TO TRIGGER CALCULATION FLAGS COMMENT OUT QUERY BELOW
                        UPDATE synapse.survey_response sr
                        INNER JOIN org_calc_flags_factor ocff 
                        ON sr.person_id = ocff.person_id
                        AND sr.org_id = ocff.org_id
                        INNER JOIN etldata.survey_response ers 
                        ON ers.person_id = sr.person_id
                        AND ers.org_id = sr.org_id
                        AND ers.survey_id = sr.survey_id
                        AND ers.survey_questions_id = sr.survey_questions_id
                        SET
                            sr.decimal_value = ers.decimal_value,
                            sr.char_value = ers.char_value,
                            sr.charmax_value = ers.charmax_value,
                            sr.modified_at = @current,
                            ocff.calculated_at = NULL,
                            ocff.modified_at = @current
                        WHERE
                            (sr.decimal_value <> ers.decimal_value
                            OR sr.char_value <> ers.char_value
                            OR sr.charmax_value <> ers.charmax_value)
                            AND sr.modified_at <= ers.modified_at
                            AND ers.modified_at > (SELECT MAX(last_update_ts) FROM etldata.last_response_update LIMIT 1);

                        -- Update the last update time to now
                        UPDATE etldata.last_response_update SET last_update_ts = @current;
                        END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Talking_Point_Calc` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Talking_Point_Calc`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN
			DECLARE timeVar DATETIME;
			SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
		        
		    WHILE(
				NOW() < deadline
		        AND (select 1 from org_calc_flags_talking_point where calculated_at IS NULL LIMIT 1) > 0
			) DO
		        
		        SET timeVar = CURRENT_TIMESTAMP();
		        
		        #--Carve out a chunk of work to do
		        UPDATE org_calc_flags_talking_point
		        SET 
					calculated_at=timeVar,
		            modified_at=timeVar
		        WHERE calculated_at IS NULL
		        AND deleted_at is NULL
		        LIMIT chunksize
		        ;
		        
		        
		        #--Sourced from surveys
		        insert into org_talking_points(organization_id, person_id, talking_points_id, survey_id, response, source_modified_at, created_at, modified_at)
		        #--EXPLAIN
		        SELECT 
		            pc.org_id,
		            pc.person_id,
		            pc.talking_points_id,
		            pc.survey_id,
		            pc.response,
		            pc.source_modified_at,
		            timeVar,
		            timeVar
		        FROM
		            person_survey_talking_points_calculated pc
				INNER JOIN org_calc_flags_talking_point AS O
					ON (O.org_id,	O.person_id)
		            = (pc.org_id,	pc.person_id)
		        LEFT JOIN org_talking_points otp_out
					ON (otp_out.organization_id, otp_out.person_id, otp_out.talking_points_id, otp_out.survey_id)
					= (pc.org_id, pc.person_id, pc.talking_points_id, pc.survey_id)
		            AND pc.response <=> otp_out.response
		            AND otp_out.source_modified_at = (
						SELECT MAX(otp_in.source_modified_at)
		                FROM org_talking_points otp_in
		                INNER JOIN talking_points tp on otp_in.talking_points_id = tp.id
		                WHERE 
							otp_out.organization_id = otp_in.organization_id 
							AND otp_out.person_id = otp_in.person_id
		                    AND pc.ebi_question_id = tp.ebi_question_id
		                    AND otp_out.survey_id = otp_in.survey_id
		                    AND tp.deleted_at is null
		                    AND otp_in.deleted_at is null
					)
		        WHERE
		            otp_out.organization_id IS NULL #--Get only pc entries with no corresponding otp_out
		            AND pc.response IS NOT NULL
		            AND O.calculated_at=timeVar
		            AND otp_out.deleted_at IS NULL
		        ;
		        
				
		        #--Sourced from metadata
		        insert into org_talking_points(organization_id, person_id, talking_points_id, org_academic_year_id, org_academic_terms_id, response, source_modified_at, created_at, modified_at)
		        #--EXPLAIN
		        SELECT 
		            pc.org_id,
		            pc.person_id,
		            pc.talking_points_id,
		            pc.org_academic_year_id, 
		            pc.org_academic_terms_id,
		            pc.response,
		            pc.source_modified_at,
		            timeVar,
		            timeVar
		        FROM
		            person_MD_talking_points_calculated pc
				INNER JOIN org_calc_flags_talking_point AS O
					ON (O.org_id,	O.person_id)
		            = (pc.org_id,	pc.person_id)
		        LEFT JOIN org_talking_points otp_out
					ON (otp_out.organization_id, otp_out.person_id, otp_out.talking_points_id)
					= (pc.org_id, pc.person_id, pc.talking_points_id) AND
		             ((pc.org_academic_year_id, pc.org_academic_terms_id) = (otp_out.org_academic_year_id, otp_out.org_academic_terms_id) 
		             OR (pc.org_academic_year_id is null AND otp_out.org_academic_year_id is null AND pc.org_academic_terms_id is null AND otp_out.org_academic_terms_id is null) 
		             OR (pc.org_academic_terms_id is null AND otp_out.org_academic_terms_id is null AND pc.org_academic_year_id = otp_out.org_academic_year_id))
		            AND pc.response <=> otp_out.response
		            AND otp_out.source_modified_at = (
						SELECT MAX(otp_in.source_modified_at)
		                FROM org_talking_points otp_in
		                INNER JOIN talking_points tp on otp_in.talking_points_id = tp.id
		                WHERE 
							otp_out.organization_id = otp_in.organization_id 
							AND otp_out.person_id = otp_in.person_id
		                    AND pc.ebi_metadata_id = tp.ebi_metadata_id
		                    AND otp_out.org_academic_year_id <=> otp_in.org_academic_year_id
		                    AND otp_out.org_academic_terms_id <=> otp_in.org_academic_terms_id
		                    AND tp.deleted_at is null
		                    AND otp_in.deleted_at is null
					)
		        WHERE
		            otp_out.organization_id IS NULL #--Get only pc entries with no corresponding otp_out
		            AND pc.response IS NOT NULL
		            AND O.calculated_at=timeVar
		            AND otp_out.deleted_at IS NULL
		        ;
		        
				
				UPDATE org_calc_flags_talking_point orf
		        LEFT JOIN org_talking_points AS tp 
					ON tp.organization_id = orf.org_id
					AND tp.person_id = orf.person_id 
		            AND tp.modified_at = timeVar
				SET 
					orf.calculated_at = '1900-01-01 00:00:00',
					orf.modified_at = timeVar
				WHERE
					orf.calculated_at = timeVar
					AND tp.organization_id IS NULL
		            AND orf.deleted_at is null
		            AND tp.deleted_at is null#--These got no value out of calculation
		        ;

			END WHILE;
		    
		END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `AUDIT_DASHBOARD_Survey_Completion_Status`
--

/*!50001 DROP TABLE IF EXISTS `AUDIT_DASHBOARD_Survey_Completion_Status`*/;
/*!50001 DROP VIEW IF EXISTS `AUDIT_DASHBOARD_Survey_Completion_Status`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `AUDIT_DASHBOARD_Survey_Completion_Status` AS select `org_person_student_survey_link`.`survey_completion_status` AS `survey_completion_status`,`org_person_student_survey_link`.`survey_opt_out_status` AS `survey_opt_out_status`,`org_person_student_survey_link`.`Has_Responses` AS `Has_Responses`,(case when ((`org_person_student_survey_link`.`survey_completion_status` = 'Assigned') and (`org_person_student_survey_link`.`survey_opt_out_status` = 'Yes') and (`org_person_student_survey_link`.`Has_Responses` = 'No')) then 'Yes' when ((`org_person_student_survey_link`.`survey_completion_status` = 'Assigned') and (`org_person_student_survey_link`.`survey_opt_out_status` = 'No') and (`org_person_student_survey_link`.`Has_Responses` = 'No')) then 'Yes' when ((`org_person_student_survey_link`.`survey_completion_status` = 'CompletedMandatory') and (`org_person_student_survey_link`.`survey_opt_out_status` = 'No') and (`org_person_student_survey_link`.`Has_Responses` = 'Yes')) then 'Yes' when ((`org_person_student_survey_link`.`survey_completion_status` = 'CompletedMandatory') and (`org_person_student_survey_link`.`survey_opt_out_status` = 'Yes') and (`org_person_student_survey_link`.`Has_Responses` = 'Yes')) then 'Yes' when ((`org_person_student_survey_link`.`survey_completion_status` = 'CompletedAll') and (`org_person_student_survey_link`.`survey_opt_out_status` = 'No') and (`org_person_student_survey_link`.`Has_Responses` = 'Yes')) then 'Yes' when ((`org_person_student_survey_link`.`survey_completion_status` = 'CompletedAll') and (`org_person_student_survey_link`.`survey_opt_out_status` = 'Yes') and (`org_person_student_survey_link`.`Has_Responses` = 'Yes')) then 'Yes' else 'No' end) AS `valid_combination`,(case when ((`org_person_student_survey_link`.`survey_completion_status` = 'Assigned') and (`org_person_student_survey_link`.`survey_opt_out_status` = 'Yes') and (`org_person_student_survey_link`.`Has_Responses` = 'No')) then 'No' when ((`org_person_student_survey_link`.`survey_completion_status` = 'Assigned') and (`org_person_student_survey_link`.`survey_opt_out_status` = 'No') and (`org_person_student_survey_link`.`Has_Responses` = 'No')) then 'No' when ((`org_person_student_survey_link`.`survey_completion_status` = 'CompletedMandatory') and (`org_person_student_survey_link`.`survey_opt_out_status` = 'No') and (`org_person_student_survey_link`.`Has_Responses` = 'Yes')) then 'No' when ((`org_person_student_survey_link`.`survey_completion_status` = 'CompletedMandatory') and (`org_person_student_survey_link`.`survey_opt_out_status` = 'Yes') and (`org_person_student_survey_link`.`Has_Responses` = 'Yes')) then 'No' when ((`org_person_student_survey_link`.`survey_completion_status` = 'CompletedAll') and (`org_person_student_survey_link`.`survey_opt_out_status` = 'No') and (`org_person_student_survey_link`.`Has_Responses` = 'Yes')) then 'No' when ((`org_person_student_survey_link`.`survey_completion_status` = 'CompletedAll') and (`org_person_student_survey_link`.`survey_opt_out_status` = 'Yes') and (`org_person_student_survey_link`.`Has_Responses` = 'Yes')) then 'No' when (`org_person_student_survey_link`.`modified_at` < (now() - interval 1 hour)) then 'Yes' else 'No' end) AS `needs_manual_intervention`,count(0) AS `student_survey_link_count`,group_concat(distinct `org_person_student_survey_link`.`org_id` order by `org_person_student_survey_link`.`org_id` ASC separator ',') AS `org_id`,max(`org_person_student_survey_link`.`modified_at`) AS `date_last_updated` from `org_person_student_survey_link` group by `org_person_student_survey_link`.`survey_completion_status`,`org_person_student_survey_link`.`survey_opt_out_status`,`org_person_student_survey_link`.`Has_Responses` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `DASHBOARD_Student_Calculations`
--

/*!50001 DROP TABLE IF EXISTS `DASHBOARD_Student_Calculations`*/;
/*!50001 DROP VIEW IF EXISTS `DASHBOARD_Student_Calculations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `DASHBOARD_Student_Calculations` AS select 'Factor' AS `Calculation Type`,sum((case when (`org_calc_flags_factor`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) AS `Calculated Students`,sum((case when (`org_calc_flags_factor`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) AS `Students With No Data`,sum((case when isnull(`org_calc_flags_factor`.`calculated_at`) then 1 else 0 end)) AS `Flagged For Calculation`,sum((case when (`org_calc_flags_factor`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) AS `Never Calculated`,count(0) AS `Total Students`,concat(((sum((case when (`org_calc_flags_factor`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculated Percentage`,concat(((sum((case when (`org_calc_flags_factor`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) / count(0)) * 100),'%') AS `No Data Percentage`,concat(((sum((case when isnull(`org_calc_flags_factor`.`calculated_at`) then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculating Percentage`,concat(((sum((case when (`org_calc_flags_factor`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `Never Calculated Percentage` from `org_calc_flags_factor` union select 'Risk' AS `Calculation Type`,sum((case when (`org_calc_flags_risk`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) AS `Calculated Students`,sum((case when (`org_calc_flags_risk`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) AS `Students With No Data`,sum((case when isnull(`org_calc_flags_risk`.`calculated_at`) then 1 else 0 end)) AS `Flagged For Calculation`,sum((case when (`org_calc_flags_risk`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) AS `Never Calculated`,count(0) AS `Total Students`,concat(((sum((case when (`org_calc_flags_risk`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculated Percentage`,concat(((sum((case when (`org_calc_flags_risk`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) / count(0)) * 100),'%') AS `No Data Percentage`,concat(((sum((case when isnull(`org_calc_flags_risk`.`calculated_at`) then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculating Percentage`,concat(((sum((case when (`org_calc_flags_risk`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `Never Calculated Percentage` from `org_calc_flags_risk` union select 'Talking Points' AS `Calculation Type`,sum((case when (`org_calc_flags_talking_point`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) AS `Calculated Students`,sum((case when (`org_calc_flags_talking_point`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) AS `Students With No Data`,sum((case when isnull(`org_calc_flags_talking_point`.`calculated_at`) then 1 else 0 end)) AS `Flagged For Calculation`,sum((case when (`org_calc_flags_talking_point`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) AS `Never Calculated`,count(0) AS `Total Students`,concat(((sum((case when (`org_calc_flags_talking_point`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculated Percentage`,concat(((sum((case when (`org_calc_flags_talking_point`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) / count(0)) * 100),'%') AS `No Data Percentage`,concat(((sum((case when isnull(`org_calc_flags_talking_point`.`calculated_at`) then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculating Percentage`,concat(((sum((case when (`org_calc_flags_talking_point`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `Never Calculated Percentage` from `org_calc_flags_talking_point` union select 'Student Reports' AS `Calculation Type`,sum((case when (`sr`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) AS `Calculated Students`,sum((case when (`sr`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) AS `Students With No Data`,sum((case when isnull(`sr`.`calculated_at`) then 1 else 0 end)) AS `Flagged For Calculation`,sum((case when (`sr`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) AS `Never Calculated`,count(0) AS `Total Students`,concat(((sum((case when (`sr`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculated Percentage`,concat(((sum((case when (`sr`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) / count(0)) * 100),'%') AS `No Data Percentage`,concat(((sum((case when isnull(`sr`.`calculated_at`) then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculating Percentage`,concat(((sum((case when (`sr`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `Never Calculated Percentage` from `org_calc_flags_student_reports` `sr` union select 'Report PDF Generation' AS `Calculation Type`,sum((case when (`sr`.`file_name` is not null) then 1 else 0 end)) AS `Calculated Students`,sum((case when ((`sr`.`calculated_at` = '1900-01-01 00:00:00') and isnull(`sr`.`file_name`)) then 1 else 0 end)) AS `Students With No Data`,sum((case when isnull(`sr`.`file_name`) then 1 else 0 end)) AS `Flagged For Calculation`,NULL AS `Never Calculated`,count(0) AS `Total Students`,concat(((sum((case when (`sr`.`file_name` is not null) then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculated Percentage`,concat(((sum((case when ((`sr`.`calculated_at` = '1900-01-01 00:00:00') and isnull(`sr`.`file_name`)) then 1 else 0 end)) / count(0)) * 100),'%') AS `No Data Percentage`,concat(((sum((case when isnull(`sr`.`file_name`) then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculating Percentage`,NULL AS `Never Calculated Percentage` from `org_calc_flags_student_reports` `sr` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `DASHBOARD_Student_Surveys_By_Org`
--

/*!50001 DROP TABLE IF EXISTS `DASHBOARD_Student_Surveys_By_Org`*/;
/*!50001 DROP VIEW IF EXISTS `DASHBOARD_Student_Surveys_By_Org`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `DASHBOARD_Student_Surveys_By_Org` AS select `organization_lang`.`organization_id` AS `organization_id`,`organization`.`campus_id` AS `campus_id`,`organization_lang`.`organization_name` AS `organization_name`,count(`sr`.`person_id`) AS `Total number of Surveys Taken`,sum((case when (`sr`.`survey_id` = 11) then 1 else 0 end)) AS `Students having taken survey_id: 11`,sum((case when (`sr`.`survey_id` = 12) then 1 else 0 end)) AS `Students having taken survey_id: 12`,sum((case when (`sr`.`survey_id` = 13) then 1 else 0 end)) AS `Students having taken survey_id: 13`,sum((case when (`sr`.`survey_id` = 14) then 1 else 0 end)) AS `Students having taken survey_id: 14`,sum((case when ((`org_person_student`.`receivesurvey` = 1) or isnull(`org_person_student`.`receivesurvey`)) then 1 else 0 end)) AS `Student Survey Eligibility` from (((`organization_lang` left join `organization` on((`organization_lang`.`organization_id` = `organization`.`id`))) left join `org_person_student` on(((`organization`.`id` = `org_person_student`.`organization_id`) and (`organization_lang`.`organization_id` = `org_person_student`.`organization_id`) and (isnull(`org_person_student`.`receivesurvey`) or (`org_person_student`.`receivesurvey` = 1))))) left join `DASHBOARD_Students_With_Intent_To_Leave` `sr` on(((`sr`.`person_id` = `org_person_student`.`person_id`) and (`organization`.`id` = `sr`.`org_id`)))) where ((`organization`.`campus_id` is not null) and (`organization_lang`.`organization_id` <> 181) and (`organization_lang`.`organization_id` <> 195) and (`organization_lang`.`organization_id` <> 196) and (`organization_lang`.`organization_id` <> 198) and (`organization_lang`.`organization_id` <> 200) and (`organization_lang`.`organization_id` <> 201) and (`organization_lang`.`organization_id` <> 2) and (`organization_lang`.`organization_id` <> 199) and (`organization_lang`.`organization_id` <> 3) and (`organization_lang`.`organization_id` <> 194) and (`organization_lang`.`organization_id` <> 197)) group by `organization`.`id` order by `organization_lang`.`organization_name`,`sr`.`survey_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `DASHBOARD_Students_With_Intent_To_Leave`
--

/*!50001 DROP TABLE IF EXISTS `DASHBOARD_Students_With_Intent_To_Leave`*/;
/*!50001 DROP VIEW IF EXISTS `DASHBOARD_Students_With_Intent_To_Leave`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `DASHBOARD_Students_With_Intent_To_Leave` AS select `ssr`.`org_id` AS `org_id`,`ssr`.`person_id` AS `person_id`,`ssr`.`survey_id` AS `survey_id`,`ssr`.`decimal_value` AS `decimal_value` from (`survey_response` `ssr` join `survey_questions` `sq` on((`sq`.`id` = `ssr`.`survey_questions_id`))) where (`sq`.`qnbr` = 4) group by `ssr`.`org_id`,`ssr`.`person_id`,`ssr`.`survey_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `DASHBOARD_Upload_Status`
--

/*!50001 DROP TABLE IF EXISTS `DASHBOARD_Upload_Status`*/;
/*!50001 DROP VIEW IF EXISTS `DASHBOARD_Upload_Status`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `DASHBOARD_Upload_Status` AS select `ulf`.`organization_id` AS `organization id`,`ol`.`organization_name` AS `organization name`,`ulf`.`upload_type` AS `upload tyoe`,`ulf`.`status` AS `status`,`ulf2`.`most_recent_upload_date` AS `most recent upload date`,`ulf`.`uploaded_file_path` AS `uploaded file path`,`ulf`.`uploaded_row_count` AS `uploaded row count` from ((`upload_file_log` `ulf` join `PART_Upload_Status_part_1` `ulf2` on(((`ulf`.`organization_id` = `ulf2`.`organization_id`) and (`ulf`.`upload_type` = `ulf2`.`upload_type`) and (`ulf`.`upload_date` = `ulf2`.`most_recent_upload_date`) and (`ulf`.`status` = `ulf2`.`status`)))) join `organization_lang` `ol` on((`ol`.`organization_id` = `ulf`.`organization_id`))) order by `ol`.`organization_name`,`ulf`.`upload_type`,`ulf`.`status` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `Factor_Question_Constants`
--

/*!50001 DROP TABLE IF EXISTS `Factor_Question_Constants`*/;
/*!50001 DROP VIEW IF EXISTS `Factor_Question_Constants`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `Factor_Question_Constants` AS select 'Factor' AS `datum_type` union select 'Question' AS `datum_type` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `Issues_Calculation`
--

/*!50001 DROP TABLE IF EXISTS `Issues_Calculation`*/;
/*!50001 DROP VIEW IF EXISTS `Issues_Calculation`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `Issues_Calculation` AS select `theID`.`org_id` AS `org_id`,`theID`.`faculty_id` AS `faculty_id`,`theID`.`survey_id` AS `survey_id`,`theID`.`issue_id` AS `issue_id`,`theID`.`cohort` AS `cohort`,`theID`.`student_id` AS `student_id`,ifnull(((`theID`.`source_value` between `iss`.`min` and `iss`.`max`) or (cast(`theID`.`source_value` as unsigned) = `eqo`.`option_value`)),0) AS `has_issue`,`issl`.`name` AS `name`,`iss`.`icon` AS `icon` from ((((`Issues_Datum` `theID` join `issue` `iss` on((`iss`.`id` = `theID`.`issue_id`))) left join `issue_lang` `issl` on((`iss`.`id` = `issl`.`issue_id`))) left join `issue_options` `issO` on((`iss`.`id` = `issO`.`issue_id`))) left join `ebi_question_options` `eqo` on((`eqo`.`id` = `issO`.`ebi_question_options_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `Issues_Datum`
--

/*!50001 DROP TABLE IF EXISTS `Issues_Datum`*/;
/*!50001 DROP VIEW IF EXISTS `Issues_Datum`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `Issues_Datum` AS select `ofs`.`organization_id` AS `org_id`,`ofs`.`person_id` AS `faculty_id`,coalesce(`ISQ`.`survey_id`,`ISF`.`survey_id`) AS `survey_id`,coalesce(`ISQ`.`student_id`,`ISF`.`student_id`) AS `student_id`,coalesce(`ISQ`.`issue_id`,`ISF`.`issue_id`) AS `issue_id`,coalesce(`ISQ`.`cohort`,`ISF`.`cohort`) AS `cohort`,`CU`.`datum_type` AS `type`,coalesce(`ISQ`.`survey_question_id`,`ISF`.`factor_id`) AS `source_id`,coalesce(`ISQ`.`permitted_value`,`ISF`.`permitted_value`) AS `source_value`,coalesce(`ISQ`.`modified_at`,`ISF`.`modified_at`) AS `modified_at` from (((`org_person_faculty` `ofs` join `Factor_Question_Constants` `CU`) left join `Issues_Survey_Questions` `ISQ` on(((`CU`.`datum_type` = 'Question') and (`ofs`.`person_id` = `ISQ`.`faculty_id`) and (`ofs`.`organization_id` = `ISQ`.`org_id`)))) left join `Issues_Factors` `ISF` on(((`CU`.`datum_type` = 'Factor') and (`ofs`.`person_id` = `ISF`.`faculty_id`) and (`ofs`.`organization_id` = `ISF`.`org_id`)))) where ((`ISQ`.`permitted_value` is not null) or (`ISF`.`permitted_value` is not null)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `Issues_Factors`
--

/*!50001 DROP TABLE IF EXISTS `Issues_Factors`*/;
/*!50001 DROP VIEW IF EXISTS `Issues_Factors`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `Issues_Factors` AS select `pfc`.`organization_id` AS `org_id`,`pfc`.`person_id` AS `student_id`,`pfc`.`survey_id` AS `survey_id`,`iss`.`id` AS `issue_id`,`opssl`.`cohort` AS `cohort`,`pfc`.`factor_id` AS `factor_id`,`ISFS`.`faculty_id` AS `faculty_id`,`pfc`.`mean_value` AS `permitted_value`,`pfc`.`modified_at` AS `modified_at` from ((((((`org_faculty_student_permission_map` `ISFS` join `org_person_student_survey_link` `opssl` on(((`opssl`.`org_id` = `ISFS`.`org_id`) and (`opssl`.`person_id` = `ISFS`.`student_id`)))) join `person_factor_calculated` `pfc` on(((`ISFS`.`student_id` = `pfc`.`person_id`) and (`ISFS`.`org_id` = `pfc`.`organization_id`) and (`opssl`.`survey_id` = `pfc`.`survey_id`) and isnull(`pfc`.`deleted_at`)))) join `issue` `iss` on(((`iss`.`factor_id` = `pfc`.`factor_id`) and (`iss`.`survey_id` = `pfc`.`survey_id`) and isnull(`iss`.`deleted_at`)))) join `wess_link` `wl` on(((`wl`.`survey_id` = `pfc`.`survey_id`) and (`wl`.`org_id` = `pfc`.`organization_id`) and (`wl`.`cohort_code` = `opssl`.`cohort`) and (`wl`.`status` = 'closed')))) join `datablock_questions` `dq` on(((`dq`.`factor_id` = `pfc`.`factor_id`) and isnull(`dq`.`deleted_at`)))) join `org_permissionset_datablock` `opd` on(((`opd`.`organization_id` = `pfc`.`organization_id`) and (`opd`.`datablock_id` = `dq`.`datablock_id`) and (`opd`.`org_permissionset_id` = `ISFS`.`permissionset_id`) and isnull(`opd`.`deleted_at`)))) where (`pfc`.`id` = (select `fc`.`id` from `person_factor_calculated` `fc` where ((`fc`.`organization_id` = `pfc`.`organization_id`) and (`fc`.`person_id` = `pfc`.`person_id`) and (`fc`.`factor_id` = `pfc`.`factor_id`) and (`fc`.`survey_id` = `pfc`.`survey_id`) and isnull(`fc`.`deleted_at`)) order by `fc`.`modified_at` desc limit 1)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `Issues_Survey_Questions`
--

/*!50001 DROP TABLE IF EXISTS `Issues_Survey_Questions`*/;
/*!50001 DROP VIEW IF EXISTS `Issues_Survey_Questions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `Issues_Survey_Questions` AS select `sr`.`org_id` AS `org_id`,`sr`.`person_id` AS `student_id`,`opssl`.`survey_id` AS `survey_id`,`iss`.`id` AS `issue_id`,`opssl`.`cohort` AS `cohort`,`sq`.`id` AS `survey_question_id`,`sq`.`ebi_question_id` AS `ebi_question_id`,`ISFS`.`faculty_id` AS `faculty_id`,`sr`.`decimal_value` AS `permitted_value`,`sr`.`modified_at` AS `modified_at` from ((((((((`org_faculty_student_permission_map` `ISFS` join `org_person_student_survey_link` `opssl` on(((`ISFS`.`org_id` = `opssl`.`org_id`) and (`opssl`.`person_id` = `ISFS`.`student_id`)))) join `survey_response` `sr` FORCE INDEX (`fk_survey_response_organization1`) on(((`ISFS`.`student_id` = `sr`.`person_id`) and (`ISFS`.`org_id` = `sr`.`org_id`) and (`opssl`.`survey_id` = `sr`.`survey_id`) and isnull(`sr`.`deleted_at`)))) join `issue` `iss` on(((`iss`.`survey_questions_id` = `sr`.`survey_questions_id`) and (`iss`.`survey_id` = `sr`.`survey_id`) and isnull(`iss`.`deleted_at`)))) join `survey_questions` `sq` on(((`sr`.`survey_questions_id` = `sq`.`id`) and (`sq`.`survey_id` = `sr`.`survey_id`) and isnull(`sq`.`deleted_at`)))) join `ebi_question` `eq` on(((`sq`.`ebi_question_id` = `eq`.`id`) and isnull(`eq`.`deleted_at`)))) join `datablock_questions` `dq` USE INDEX (`permfunc`) on(((`dq`.`ebi_question_id` = `eq`.`id`) and isnull(`dq`.`deleted_at`)))) join `org_permissionset_datablock` `opd` on(((`opd`.`organization_id` = `sr`.`org_id`) and (`opd`.`datablock_id` = `dq`.`datablock_id`) and (`opd`.`org_permissionset_id` = `ISFS`.`permissionset_id`) and isnull(`opd`.`deleted_at`)))) join `wess_link` `wl` on(((`wl`.`survey_id` = `opssl`.`survey_id`) and (`opssl`.`cohort` = `wl`.`cohort_code`) and (`wl`.`org_id` = `sr`.`org_id`) and (`wl`.`status` = 'closed')))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `PART_Upload_Status_part_1`
--

/*!50001 DROP TABLE IF EXISTS `PART_Upload_Status_part_1`*/;
/*!50001 DROP VIEW IF EXISTS `PART_Upload_Status_part_1`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `PART_Upload_Status_part_1` AS select `upload_file_log`.`organization_id` AS `organization_id`,`upload_file_log`.`upload_type` AS `upload_type`,`upload_file_log`.`status` AS `status`,max(`upload_file_log`.`upload_date`) AS `most_recent_upload_date` from `upload_file_log` where (`upload_file_log`.`status` in ('F','Q')) group by `upload_file_log`.`organization_id`,`upload_file_log`.`upload_type`,`upload_file_log`.`status` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `group_course_discriminator`
--

/*!50001 DROP TABLE IF EXISTS `group_course_discriminator`*/;
/*!50001 DROP VIEW IF EXISTS `group_course_discriminator`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `group_course_discriminator` AS select 'group' AS `association` union select 'course' AS `association` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `org_course_faculty_student_permission_map`
--

/*!50001 DROP TABLE IF EXISTS `org_course_faculty_student_permission_map`*/;
/*!50001 DROP VIEW IF EXISTS `org_course_faculty_student_permission_map`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `org_course_faculty_student_permission_map` AS select `OC`.`id` AS `course_id`,`OC`.`organization_id` AS `org_id`,`OCF`.`person_id` AS `faculty_id`,`OCS`.`person_id` AS `student_id`,`OCF`.`org_permissionset_id` AS `permissionset_id` from (((`org_course_faculty` `OCF` USE INDEX (`person-course`) join `org_courses` `OC` on(((`OC`.`id` = `OCF`.`org_courses_id`) and (`OC`.`organization_id` = `OCF`.`organization_id`) and isnull(`OC`.`deleted_at`)))) join `org_academic_terms` `OAT` FORCE INDEX (`last_term`) FORCE INDEX (PRIMARY) on(((`OAT`.`id` = `OC`.`org_academic_terms_id`) and (`OAT`.`organization_id` = `OC`.`organization_id`) and (`OAT`.`end_date` >= cast(now() as date)) and (`OAT`.`start_date` <= cast(now() as date)) and isnull(`OAT`.`deleted_at`)))) join `org_course_student` `OCS` FORCE INDEX (`course-person`) FORCE INDEX (`person-course`) on(((`OCS`.`org_courses_id` = `OC`.`id`) and (`OCS`.`organization_id` = `OC`.`organization_id`) and isnull(`OCS`.`deleted_at`)))) where isnull(`OCF`.`deleted_at`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `org_faculty_student_permission_map`
--

/*!50001 DROP TABLE IF EXISTS `org_faculty_student_permission_map`*/;
/*!50001 DROP VIEW IF EXISTS `org_faculty_student_permission_map`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `org_faculty_student_permission_map` AS select `OPF`.`organization_id` AS `org_id`,`OPF`.`person_id` AS `faculty_id`,coalesce(`OGM`.`student_id`,`OCM`.`student_id`) AS `student_id`,`OGM`.`group_id` AS `group_id`,`OCM`.`course_id` AS `course_id`,coalesce(`OGM`.`permissionset_id`,`OCM`.`permissionset_id`) AS `permissionset_id` from (((`org_person_faculty` `OPF` join `group_course_discriminator` `GCD`) left join `org_group_faculty_student_permission_map` `OGM` on((((`OGM`.`org_id`,`OGM`.`faculty_id`) = (`OPF`.`organization_id`,`OPF`.`person_id`)) and (`GCD`.`association` = 'group')))) left join `org_course_faculty_student_permission_map` `OCM` on((((`OCM`.`org_id`,`OCM`.`faculty_id`) = (`OPF`.`organization_id`,`OPF`.`person_id`)) and (`GCD`.`association` = 'course')))) where (((`OGM`.`group_id` is not null) or (`OCM`.`course_id` is not null)) and isnull(`OPF`.`deleted_at`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `org_group_faculty_student_permission_map`
--

/*!50001 DROP TABLE IF EXISTS `org_group_faculty_student_permission_map`*/;
/*!50001 DROP VIEW IF EXISTS `org_group_faculty_student_permission_map`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `org_group_faculty_student_permission_map` AS select `OG`.`id` AS `group_id`,`OG`.`organization_id` AS `org_id`,`OGF`.`person_id` AS `faculty_id`,`OGS`.`person_id` AS `student_id`,`OGF`.`org_permissionset_id` AS `permissionset_id` from ((`org_group_faculty` `OGF` FORCE INDEX (`PG_perm`) join `org_group` `OG` on(((`OG`.`id` = `OGF`.`org_group_id`) and (`OG`.`organization_id` = `OGF`.`organization_id`) and isnull(`OG`.`deleted_at`)))) join `org_group_students` `OGS` FORCE INDEX (`group-student`) FORCE INDEX (`student-group`) on(((`OGS`.`org_group_id` = `OG`.`id`) and (`OGS`.`organization_id` = `OG`.`organization_id`) and isnull(`OGS`.`deleted_at`)))) where isnull(`OGF`.`deleted_at`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `org_person_riskvariable`
--

/*!50001 DROP TABLE IF EXISTS `org_person_riskvariable`*/;
/*!50001 DROP VIEW IF EXISTS `org_person_riskvariable`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY INVOKER */
/*!50001 VIEW `org_person_riskvariable` AS select `orgc`.`org_id` AS `org_id`,`rgph`.`person_id` AS `person_id`,`rv`.`id` AS `risk_variable_id`,`rv`.`source` AS `source`,`rv`.`variable_type` AS `variable_type`,`rv`.`calc_type` AS `calc_type`,`rgph`.`risk_group_id` AS `risk_group_id`,`rv`.`calculation_end_date` AS `calculation_end_date`,`rv`.`calculation_start_date` AS `calculation_start_date`,`rmm`.`id` AS `risk_model_id`,`rmw`.`weight` AS `weight`,greatest(ifnull(`rgph`.`assignment_date`,0),ifnull(`orgc`.`modified_at`,0),ifnull(`orgc`.`created_at`,0),ifnull(`orgm`.`modified_at`,0),ifnull(`orgm`.`created_at`,0),ifnull(`rmm`.`modified_at`,0),ifnull(`rmm`.`created_at`,0),ifnull(`rv`.`modified_at`,0),ifnull(`rv`.`created_at`,0)) AS `modified_at` from (((((`risk_group_person_history` `rgph` join `org_calc_flags_risk` `orgc` on((`rgph`.`person_id` = `orgc`.`person_id`))) join `org_risk_group_model` `orgm` on(((`rgph`.`risk_group_id` = `orgm`.`risk_group_id`) and (`orgm`.`org_id` = `orgc`.`org_id`)))) join `risk_model_master` `rmm` on(((`orgm`.`risk_model_id` = `rmm`.`id`) and (now() between `rmm`.`calculation_start_date` and `rmm`.`calculation_end_date`)))) join `risk_model_weights` `rmw` on((`rmw`.`risk_model_id` = `rmm`.`id`))) join `risk_variable` `rv` on((`rmw`.`risk_variable_id` = `rv`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `org_person_riskvariable_datum`
--

/*!50001 DROP TABLE IF EXISTS `org_person_riskvariable_datum`*/;
/*!50001 DROP VIEW IF EXISTS `org_person_riskvariable_datum`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY INVOKER */
/*!50001 VIEW `org_person_riskvariable_datum` AS select `rvintersect`.`org_id` AS `org_id`,`rvintersect`.`person_id` AS `person_id`,`rvintersect`.`risk_variable_id` AS `risk_variable_id`,coalesce(`emd`.`metadata_value`,`omd`.`metadata_value`,`oqr`.`decimal_value`,`oqr`.`char_value`,`oqr`.`charmax_value`,`svr`.`decimal_value`,`svr`.`char_value`,`svr`.`charmax_value`,`pfc`.`mean_value`) AS `source_value`,coalesce(`emd`.`modified_at`,`omd`.`modified_at`,`oqr`.`modified_at`,`svr`.`modified_at`,`pfc`.`modified_at`,`emd`.`created_at`,`omd`.`created_at`,`oqr`.`created_at`,`svr`.`created_at`,`pfc`.`created_at`) AS `modified_at`,coalesce(`emd`.`created_at`,`omd`.`created_at`,`oqr`.`created_at`,`svr`.`created_at`,`pfc`.`created_at`) AS `created_at`,`ebidef`.`scope` AS `scope`,coalesce(`emd`.`org_academic_year_id`,`omd`.`org_academic_year_id`) AS `org_academic_year_id`,coalesce(`emd`.`org_academic_terms_id`,`omd`.`org_academic_periods_id`) AS `org_academic_terms_id` from ((((((((((`org_person_riskvariable` `rvintersect` left join `risk_variable` `rv` on((`rv`.`id` = `rvintersect`.`risk_variable_id`))) left join `ebi_metadata` `ebidef` on((`ebidef`.`id` = `rv`.`ebi_metadata_id`))) left join `person_ebi_metadata` `emd` on((((`emd`.`person_id`,`emd`.`ebi_metadata_id`) = (`rvintersect`.`person_id`,`rv`.`ebi_metadata_id`)) and ((`emd`.`modified_at` > `rv`.`calculation_start_date`) or isnull(`rv`.`calculation_start_date`) or (`ebidef`.`scope` in ('Y','T'))) and ((`emd`.`modified_at` < `rv`.`calculation_end_date`) or isnull(`rv`.`calculation_end_date`) or (`ebidef`.`scope` in ('Y','T')))))) left join `org_metadata` `omddef` on(((`omddef`.`organization_id`,`omddef`.`id`) = (`rvintersect`.`org_id`,`rv`.`org_metadata_id`)))) left join `person_org_metadata` `omd` on((((`omd`.`org_metadata_id`,`omd`.`person_id`) = (`omddef`.`id`,`rvintersect`.`person_id`)) and ((`omd`.`modified_at` > `rv`.`calculation_start_date`) or isnull(`rv`.`calculation_start_date`)) and ((`omd`.`modified_at` < `rv`.`calculation_end_date`) or isnull(`rv`.`calculation_end_date`))))) left join `org_question` `oq` on(((`oq`.`organization_id`,`oq`.`id`) = (`rvintersect`.`org_id`,`rv`.`org_question_id`)))) left join `org_question_response` `oqr` on((((`oqr`.`org_id`,`oqr`.`person_id`,`oqr`.`org_question_id`) = (`rvintersect`.`org_id`,`rvintersect`.`person_id`,`oq`.`id`)) and ((`oqr`.`modified_at` > `rv`.`calculation_start_date`) or isnull(`rv`.`calculation_start_date`)) and ((`oqr`.`modified_at` < `rv`.`calculation_end_date`) or isnull(`rv`.`calculation_end_date`))))) left join `survey_questions` `svq` on((`svq`.`ebi_question_id` = `rv`.`ebi_question_id`))) left join `survey_response` `svr` on((((`svr`.`org_id`,`svr`.`person_id`,`svr`.`survey_questions_id`) = (`rvintersect`.`org_id`,`rvintersect`.`person_id`,`rv`.`survey_questions_id`)) and ((`svr`.`modified_at` > `rv`.`calculation_start_date`) or isnull(`rv`.`calculation_start_date`)) and ((`svr`.`modified_at` < `rv`.`calculation_end_date`) or isnull(`rv`.`calculation_end_date`))))) left join `person_factor_calculated` `pfc` on((((`pfc`.`organization_id`,`pfc`.`person_id`,`pfc`.`factor_id`,`pfc`.`survey_id`) = (`rvintersect`.`org_id`,`rvintersect`.`person_id`,`rv`.`factor_id`,`rv`.`survey_id`)) and ((`pfc`.`modified_at` > `rv`.`calculation_start_date`) or isnull(`rv`.`calculation_start_date`)) and ((`pfc`.`modified_at` < `rv`.`calculation_end_date`) or isnull(`rv`.`calculation_end_date`))))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `person_MD_talking_points_calculated`
--

/*!50001 DROP TABLE IF EXISTS `person_MD_talking_points_calculated`*/;
/*!50001 DROP VIEW IF EXISTS `person_MD_talking_points_calculated`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY INVOKER */
/*!50001 VIEW `person_MD_talking_points_calculated` AS select `orc`.`org_id` AS `org_id`,`orc`.`person_id` AS `person_id`,`tp`.`id` AS `talking_points_id`,`pem`.`ebi_metadata_id` AS `ebi_metadata_id`,`pem`.`org_academic_year_id` AS `org_academic_year_id`,`pem`.`org_academic_terms_id` AS `org_academic_terms_id`,`tp`.`talking_points_type` AS `response`,`pem`.`modified_at` AS `source_modified_at` from ((`talking_points` `tp` join `person_ebi_metadata` `pem` on(((`tp`.`ebi_metadata_id` = `pem`.`ebi_metadata_id`) and (`pem`.`metadata_value` between `tp`.`min_range` and `tp`.`max_range`)))) join `org_calc_flags_talking_point` `orc` on((`pem`.`person_id` = `orc`.`person_id`))) where (isnull(`tp`.`deleted_at`) and isnull(`pem`.`deleted_at`) and isnull(`orc`.`deleted_at`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `person_survey_talking_points_calculated`
--

/*!50001 DROP TABLE IF EXISTS `person_survey_talking_points_calculated`*/;
/*!50001 DROP VIEW IF EXISTS `person_survey_talking_points_calculated`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY INVOKER */
/*!50001 VIEW `person_survey_talking_points_calculated` AS select `orc`.`org_id` AS `org_id`,`orc`.`person_id` AS `person_id`,`tp`.`id` AS `talking_points_id`,`tp`.`ebi_question_id` AS `ebi_question_id`,`svr`.`survey_id` AS `survey_id`,`tp`.`talking_points_type` AS `response`,`svr`.`modified_at` AS `source_modified_at` from (((((`talking_points` `tp` join `survey_questions` `svq` on((`tp`.`ebi_question_id` = `svq`.`ebi_question_id`))) join `survey_response` `svr` on(((`svq`.`id` = `svr`.`survey_questions_id`) and ((case when (`svr`.`response_type` = 'decimal') then `svr`.`decimal_value` end) between `tp`.`min_range` and `tp`.`max_range`)))) join `org_calc_flags_talking_point` `orc` on(((`svr`.`person_id` = `orc`.`person_id`) and (`svr`.`org_id` = `orc`.`org_id`)))) join `org_person_student` `ops` on((`orc`.`person_id` = `ops`.`person_id`))) join `org_person_student_survey_link` `opssl` on(((`ops`.`surveycohort` = `opssl`.`cohort`) and (`opssl`.`survey_id` = `svr`.`survey_id`) and (`opssl`.`person_id` = `svr`.`person_id`) and (`opssl`.`org_id` = `svr`.`org_id`)))) where (isnull(`tp`.`deleted_at`) and isnull(`svq`.`deleted_at`) and isnull(`svr`.`deleted_at`) and isnull(`orc`.`deleted_at`) and isnull(`ops`.`deleted_at`) and isnull(`opssl`.`deleted_at`)) */;
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

-- Dump completed on 2016-12-13 22:48:51
