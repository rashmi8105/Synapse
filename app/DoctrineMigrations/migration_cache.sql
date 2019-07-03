-- MySQL dump 10.13  Distrib 5.6.27, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: synapse
-- ------------------------------------------------------
-- Server version	5.6.27-0ubuntu0.14.04.1

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
-- Temporary view structure for view `AUDIT_DASHBOARD_0_ReceiveSurvey_Students_With_Survey_Responses`
--

DROP TABLE IF EXISTS `AUDIT_DASHBOARD_0_ReceiveSurvey_Students_With_Survey_Responses`;
/*!50001 DROP VIEW IF EXISTS `AUDIT_DASHBOARD_0_ReceiveSurvey_Students_With_Survey_Responses`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `AUDIT_DASHBOARD_0_ReceiveSurvey_Students_With_Survey_Responses` AS SELECT 
 1 AS `org_id`,
 1 AS `Number_of_Students`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `AUDIT_DASHBOARD_Failed_Uploads_By_Organization_Past_24_Hours`
--

DROP TABLE IF EXISTS `AUDIT_DASHBOARD_Failed_Uploads_By_Organization_Past_24_Hours`;
/*!50001 DROP VIEW IF EXISTS `AUDIT_DASHBOARD_Failed_Uploads_By_Organization_Past_24_Hours`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `AUDIT_DASHBOARD_Failed_Uploads_By_Organization_Past_24_Hours` AS SELECT 
 1 AS `organization_id`,
 1 AS `organization_name`,
 1 AS `Failed_Uploads`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `AUDIT_DASHBOARD_Failed_Uploads_By_Type_Past_24_Hours`
--

DROP TABLE IF EXISTS `AUDIT_DASHBOARD_Failed_Uploads_By_Type_Past_24_Hours`;
/*!50001 DROP VIEW IF EXISTS `AUDIT_DASHBOARD_Failed_Uploads_By_Type_Past_24_Hours`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `AUDIT_DASHBOARD_Failed_Uploads_By_Type_Past_24_Hours` AS SELECT 
 1 AS `upload_type`,
 1 AS `Failed_Uploads`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `AUDIT_DASHBOARD_Organization_Survey_Cohort_Groupings`
--

DROP TABLE IF EXISTS `AUDIT_DASHBOARD_Organization_Survey_Cohort_Groupings`;
/*!50001 DROP VIEW IF EXISTS `AUDIT_DASHBOARD_Organization_Survey_Cohort_Groupings`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `AUDIT_DASHBOARD_Organization_Survey_Cohort_Groupings` AS SELECT 
 1 AS `organization_id`,
 1 AS `campus_id`,
 1 AS `status`,
 1 AS `cohort_code`,
 1 AS `survey_id`,
 1 AS `wess_order_id`,
 1 AS `organization_name`,
 1 AS `open_date`,
 1 AS `close_date`,
 1 AS `People_in_Cohort`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `AUDIT_DASHBOARD_Student_With_Survey_Response_And_Null_Cohort`
--

DROP TABLE IF EXISTS `AUDIT_DASHBOARD_Student_With_Survey_Response_And_Null_Cohort`;
/*!50001 DROP VIEW IF EXISTS `AUDIT_DASHBOARD_Student_With_Survey_Response_And_Null_Cohort`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `AUDIT_DASHBOARD_Student_With_Survey_Response_And_Null_Cohort` AS SELECT 
 1 AS `organization_id`,
 1 AS `Wess_InsID`,
 1 AS `organization_name`,
 1 AS `students_with_responses_and_null_cohort`*/;
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
-- Temporary view structure for view `DASHBOARD_Student_Calculations`
--

DROP TABLE IF EXISTS `DASHBOARD_Student_Calculations`;
/*!50001 DROP VIEW IF EXISTS `DASHBOARD_Student_Calculations`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `DASHBOARD_Student_Calculations` AS SELECT 
 1 AS `Calculation Type`,
 1 AS `Calculated Students`,
 1 AS `Students With No Data`,
 1 AS `Flagged For Calculation`,
 1 AS `No Survey Data`,
 1 AS `Total Students`,
 1 AS `Calculated Percentage`,
 1 AS `No Data Percentage`,
 1 AS `Calculating Percentage`,
 1 AS `No Survey Data Percentage`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `DASHBOARD_Student_Surveys_By_Org`
--

DROP TABLE IF EXISTS `DASHBOARD_Student_Surveys_By_Org`;
/*!50001 DROP VIEW IF EXISTS `DASHBOARD_Student_Surveys_By_Org`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `DASHBOARD_Student_Surveys_By_Org` AS SELECT 
 1 AS `organization_id`,
 1 AS `campus_id`,
 1 AS `organization_name`,
 1 AS `Total number of Surveys Taken`,
 1 AS `Students having taken survey_id: 11`,
 1 AS `Students having taken survey_id: 12`,
 1 AS `Students having taken survey_id: 13`,
 1 AS `Students having taken survey_id: 14`,
 1 AS `Student Survey Eligibility`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `DASHBOARD_Students_With_Intent_To_Leave`
--

DROP TABLE IF EXISTS `DASHBOARD_Students_With_Intent_To_Leave`;
/*!50001 DROP VIEW IF EXISTS `DASHBOARD_Students_With_Intent_To_Leave`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `DASHBOARD_Students_With_Intent_To_Leave` AS SELECT 
 1 AS `org_id`,
 1 AS `person_id`,
 1 AS `survey_id`,
 1 AS `decimal_value`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `DASHBOARD_Upload_Status`
--

DROP TABLE IF EXISTS `DASHBOARD_Upload_Status`;
/*!50001 DROP VIEW IF EXISTS `DASHBOARD_Upload_Status`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `DASHBOARD_Upload_Status` AS SELECT 
 1 AS `organization id`,
 1 AS `organization name`,
 1 AS `upload tyoe`,
 1 AS `status`,
 1 AS `most recent upload date`,
 1 AS `uploaded file path`,
 1 AS `uploaded row count`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `Factor_Question_Constants`
--

DROP TABLE IF EXISTS `Factor_Question_Constants`;
/*!50001 DROP VIEW IF EXISTS `Factor_Question_Constants`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `Factor_Question_Constants` AS SELECT 
 1 AS `datum_type`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `Issues_Calculation`
--

DROP TABLE IF EXISTS `Issues_Calculation`;
/*!50001 DROP VIEW IF EXISTS `Issues_Calculation`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `Issues_Calculation` AS SELECT 
 1 AS `org_id`,
 1 AS `faculty_id`,
 1 AS `survey_id`,
 1 AS `issue_id`,
 1 AS `cohort`,
 1 AS `student_id`,
 1 AS `has_issue`,
 1 AS `name`,
 1 AS `icon`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `Issues_Datum`
--

DROP TABLE IF EXISTS `Issues_Datum`;
/*!50001 DROP VIEW IF EXISTS `Issues_Datum`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `Issues_Datum` AS SELECT 
 1 AS `org_id`,
 1 AS `faculty_id`,
 1 AS `survey_id`,
 1 AS `student_id`,
 1 AS `issue_id`,
 1 AS `cohort`,
 1 AS `type`,
 1 AS `source_id`,
 1 AS `source_value`,
 1 AS `modified_at`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `Issues_Factors`
--

DROP TABLE IF EXISTS `Issues_Factors`;
/*!50001 DROP VIEW IF EXISTS `Issues_Factors`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `Issues_Factors` AS SELECT 
 1 AS `org_id`,
 1 AS `student_id`,
 1 AS `survey_id`,
 1 AS `issue_id`,
 1 AS `cohort`,
 1 AS `factor_id`,
 1 AS `faculty_id`,
 1 AS `permitted_value`,
 1 AS `modified_at`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `Issues_Survey_Questions`
--

DROP TABLE IF EXISTS `Issues_Survey_Questions`;
/*!50001 DROP VIEW IF EXISTS `Issues_Survey_Questions`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `Issues_Survey_Questions` AS SELECT 
 1 AS `org_id`,
 1 AS `student_id`,
 1 AS `survey_id`,
 1 AS `issue_id`,
 1 AS `cohort`,
 1 AS `survey_question_id`,
 1 AS `ebi_question_id`,
 1 AS `faculty_id`,
 1 AS `permitted_value`,
 1 AS `modified_at`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `PART_Upload_Status_part_1`
--

DROP TABLE IF EXISTS `PART_Upload_Status_part_1`;
/*!50001 DROP VIEW IF EXISTS `PART_Upload_Status_part_1`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `PART_Upload_Status_part_1` AS SELECT 
 1 AS `organization_id`,
 1 AS `upload_type`,
 1 AS `status`,
 1 AS `most_recent_upload_date`*/;
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
  `reason` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
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
  `home_phone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `office_phone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
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
-- Temporary view structure for view `cur_org_aggregationcalc_risk_variable`
--

DROP TABLE IF EXISTS `cur_org_aggregationcalc_risk_variable`;
/*!50001 DROP VIEW IF EXISTS `cur_org_aggregationcalc_risk_variable`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `cur_org_aggregationcalc_risk_variable` AS SELECT 
 1 AS `org_id`,
 1 AS `risk_group_id`,
 1 AS `person_id`,
 1 AS `risk_variable_id`,
 1 AS `risk_model_id`,
 1 AS `source`,
 1 AS `variable_type`,
 1 AS `weight`,
 1 AS `calculated_value`,
 1 AS `calc_type`*/;
SET character_set_client = @saved_cs_client;

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
  CONSTRAINT `FK_BD00028D1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_BD00028D25F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_BD00028D79F0E193` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`),
  CONSTRAINT `FK_BD00028DB3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_BD00028DBC88C1A3` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`),
  CONSTRAINT `FK_BD00028DCC63389E` FOREIGN KEY (`survey_questions_id`) REFERENCES `survey_questions` (`id`),
  CONSTRAINT `FK_BD00028DDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_BD00028DF9AE3580` FOREIGN KEY (`datablock_id`) REFERENCES `datablock_master` (`id`)
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebi_config`
--

LOCK TABLES `ebi_config` WRITE;
/*!40000 ALTER TABLE `ebi_config` DISABLE KEYS */;
INSERT INTO `ebi_config` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'System_Admin_URL','http://synapse-qa-admin.mnv-tech.com/'),(2,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Survey_Url','http://wess-dev-1.internal.skyfactor.com'),(3,NULL,NULL,NULL,NULL,NULL,NULL,'Email_Login_Landing_Page',NULL),(4,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Support_Helpdesk_Email_Address','support@map-works.com'),(5,NULL,NULL,NULL,NULL,NULL,NULL,'Student_ResetPwd_URL_Prefix',NULL),(6,NULL,NULL,NULL,NULL,NULL,NULL,'Course_Upload_Remove_Definition_ColumnName','Remove'),(7,NULL,NULL,NULL,NULL,NULL,NULL,'Course_Upload_Remove_Definition_Type','Text'),(8,NULL,NULL,NULL,NULL,NULL,NULL,'Course_Upload_Remove_Definition_Desc','\"Remove\" to be added to remove the record (case sensitive)'),(9,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_PrimaryConn_Definition_ColumnName','PrimaryConnect'),(10,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_PrimaryConn_Definition_Type','string'),(11,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_PrimaryConn_Definition_Desc','(Optional)Campus Faculty/StaffID for this student PrimaryDirectConnect'),(12,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_RiskGroup_Definition_ColumnName','RiskGroupID'),(13,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_RiskGroup_Definition_Type','number'),(14,NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_RiskGroup_Definition_Desc','Please see the Risk tab in Set Up for available risk groups and the risk group IDs'),(15,NULL,NULL,NULL,NULL,NULL,NULL,'Ebi_Upload_Dir','risk_uploads,talking_points,roaster_uploads,survey_uploads,talking_points_uploads,factor_uploads'),(16,NULL,NULL,NULL,NULL,NULL,NULL,'SubGroup_Upload_ParentGroup_ColumnName','Parent_Group_ID'),(17,NULL,NULL,NULL,NULL,NULL,NULL,'SubGroup_Upload_ParentGroup_ColumnType','Integer'),(18,NULL,NULL,NULL,NULL,NULL,NULL,'GroupFaculty_Upload_PermissionSet_ColumnName','Permission_Set'),(19,NULL,NULL,NULL,NULL,NULL,NULL,'GroupFaculty_Upload_PermissionSet_ColumnType','String'),(20,NULL,NULL,NULL,NULL,NULL,NULL,'GroupFaculty_Upload_PermissionSet_ColumnLength','100'),(21,NULL,NULL,NULL,NULL,NULL,NULL,'Upload_Queues','[\"q1\",\"q2\",\"q3\",\"q4\",\"q5\",\"q6\",\"q7\",\"q8\",\"q9\",\"q10\"]'),(22,NULL,'2015-12-16 17:07:06',NULL,'2015-12-16 17:07:06',NULL,NULL,'Disabled_TP_Orgs',NULL),(23,NULL,NULL,NULL,NULL,NULL,NULL,'Skyfactor_Admin_Activation_URL_Prefix','http://synapse-qa-admin.mnv-tech.com/#/createPassword/');
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
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `query_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
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
INSERT INTO `ebi_search` VALUES (2,NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_All_Interaction','E',1,'SELECT A.id as AppointmentId, N.id as NoteId,R.id as ReferralId,C.id as ContactId,AL.id as activity_log_id,AL.created_at as activity_date,AL.activity_type as activity_type,AL.person_id_faculty as activity_created_by_id,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text,C.contact_types_id as activity_contact_type_id,CTL.description as activity_contact_type_text,R.status as activity_referral_status,C.note as contactDescription,R.note as referralDescription,A.description as appointmentDescription,N.note as noteDescription FROM activity_log as AL LEFT JOIN Appointments as A ON AL.appointments_id = A.id LEFT JOIN note as N ON AL.note_id = N.id LEFT JOIN note_teams as NT ON N.id = NT.note_id LEFT JOIN contacts as C ON AL.contacts_id = C.id LEFT JOIN contacts_teams as CT ON C.id = CT.contacts_id LEFT JOIN referrals as R ON AL.referrals_id = R.id LEFT JOIN referrals_teams as RT ON R.id = RT.referrals_id LEFT JOIN activity_category as AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person as P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN contact_types as CONT ON C.contact_types_id = CONT.id WHERE AL.person_id_student = $$studentId$$ AND AL.organization_id = $$orgId$$ AND AL.activity_type IN ($$acivityArr$$) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND CASE WHEN AL.activity_type = \"C\" THEN CONT.parent_contact_types_id = 1 OR CONT.id =1 ELSE 1=1 END AND AL.id NOT IN( SELECT ALOG.id FROM related_activities as related LEFT JOIN activity_log as ALOG ON related.note_id = ALOG.note_id where related.note_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN( SELECT ALOG.id FROM related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) GROUP BY AL.id ORDER BY AL.created_at desc -- maxscale route to server slave1 '),(3,NULL,NULL,NULL,NULL,NULL,NULL,'Activity_All_Interaction','E',1,'SELECT A.id AS AppointmentId, N.id AS NoteId, R.id AS ReferralId, C.id AS ContactId, AL.id AS activity_log_id, AL.created_at AS activity_date, AL.activity_type AS activity_type, AL.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, C.contact_types_id AS activity_contact_type_id, CTL.description AS activity_contact_type_text, R.status AS activity_referral_status, C.note AS contactDescription, R.note AS referralDescription, A.description AS appointmentDescription, N.note AS noteDescription, AL.created_at as created_date, A.start_date_time as app_created_date, C.contact_date as contact_created_date, AL.activity_date as act_date FROM activity_log AS AL LEFT JOIN Appointments AS A ON AL.appointments_id = A.id LEFT JOIN note AS N ON AL.note_id = N.id LEFT JOIN note_teams AS NT ON N.id = NT.note_id LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN referrals AS R ON AL.referrals_id = R.id LEFT JOIN referrals_teams AS RT ON R.id = RT.referrals_id LEFT JOIN activity_category AS AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person AS P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id LEFT JOIN organization_role as orgr ON orgr.organization_id = AL.organization_id LEFT JOIN referral_routing_rules as rr ON rr.activity_category_id = R.activity_category_id WHERE AL.person_id_student = $$studentId$$ AND AL.organization_id = $$orgId$$ AND AL.activity_type IN ($$acivityArr$$) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND CASE WHEN AL.activity_type = \"C\" THEN CONT.parent_contact_types_id = 1 OR CONT.id = 1 ELSE 1 = 1 END AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.appointment_id = ALOG.appointments_id WHERE related.appointment_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.referral_id = ALOG.referrals_id WHERE related.referral_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.note_id = ALOG.note_id WHERE related.note_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.contacts_id = ALOG.contacts_id WHERE related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN AL.activity_type = \"N\" THEN CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id FROM note_teams WHERE note_id = N.id AND deleted_at IS NULL)) AND $$noteTeamAccess$$ = 1 ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $$faculty$$ ELSE N.access_public = 1 AND $$notePublicAccess$$ = 1 END END ELSE CASE WHEN AL.activity_type = \"C\" THEN CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id FROM contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL)) AND $$contactTeamAccess$$ = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $$faculty$$ ELSE C.access_public = 1 AND $$contactPublicAccess$$ = 1 END END ELSE CASE WHEN AL.activity_type = \"R\" THEN CASE WHEN R.access_team = 1 THEN RT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id FROM referrals_teams WHERE referrals_id = R.id AND deleted_at IS NULL)) AND (($$referralTeamAccess$$ = 1 and R.is_reason_routed = 0) OR ($$referralTeamAccessReasonRouted$$ = 1 and R.is_reason_routed = 1)) ELSE CASE WHEN R.access_private = 1 THEN R.person_id_faculty = $$faculty$$ ELSE R.access_public = 1 AND (($$referralPublicAccess$$ = 1 and R.is_reason_routed = 0) OR ($$referralPublicAccessReasonRouted$$ = 1 and R.is_reason_routed = 1)) END END OR R.person_id_assigned_to = $$faculty$$ OR R.person_id_faculty = $$faculty$$ OR orgr.person_id = $$faculty$$ and R.person_id_assigned_to is null AND orgr.role_id IN ($$roleIds$$) AND (rr.is_primary_coordinator = 1 AND rr.person_id IS NULL) ELSE CASE WHEN AL.activity_type = \"A\" THEN 1 = 1 ELSE 1 = 1 END END END END GROUP BY AL.id ORDER BY act_date DESC'),(4,NULL,NULL,NULL,NULL,NULL,NULL,'Activity_Email','E',1,'\nSELECT E.id AS activity_id, AL.id AS activity_log_id, E.created_at AS activity_date, E.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, E.email_subject AS activity_description, E.email_subject AS activity_email_subject, E.email_body AS activity_email_body FROM activity_log AS AL LEFT JOIN email AS E ON AL.email_id = E.id LEFT JOIN person AS P ON E.person_id_faculty = P.id LEFT JOIN activity_category AS AC ON E.activity_category_id = AC.id LEFT JOIN email_teams AS ET ON E.id = ET.email_id LEFT JOIN related_activities as RA ON E.id = RA.email_id LEFT JOIN activity_log AL1 ON RA.activity_log_id = AL1.id LEFT JOIN referrals AS R1 ON AL1.referrals_id = R1.id LEFT JOIN note AS N1 ON AL1.note_id = N1.id LEFT JOIN contacts AS C1 ON AL1.contacts_id = C1.id LEFT JOIN Appointments AS A1 ON AL1.appointments_id = A1.id LEFT JOIN email AS E1 ON AL1.appointments_id = E1.id WHERE E.person_id_student = $$studentId$$ AND E.deleted_at IS NULL AND (CASE WHEN AL1.activity_type IS NOT NULL AND ((AL1.activity_type = \"R\" AND R1.access_private = 1) OR (AL1.activity_type = \"C\" AND C1.access_private = 1) OR (AL1.activity_type = \"N\" AND N1.access_private = 1) OR (AL1.activity_type = \"E\" AND E1.access_private = 1)) THEN E.person_id_faculty = $$facultyId$$ ELSE CASE WHEN E.access_team = 1 THEN ET.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$facultyId$$ AND teams_id IN (SELECT teams_id from email_teams WHERE email_id = E.id AND deleted_at IS NULL)) AND $$teamAccess$$ = 1 ELSE CASE WHEN E.access_private = 1 THEN E.person_id_faculty = $$facultyId$$ ELSE E.access_public = 1 AND $$publicAccess$$ = 1 END END END OR E.person_id_faculty = $$facultyId$$) GROUP BY E.id order by E.created_at desc\n -- maxscale route to server slave1 ');
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
  `person_id` int(11) NOT NULL,
  `ebi_search_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `last_run` datetime DEFAULT NULL,
  PRIMARY KEY (`person_id`,`ebi_search_id`),
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
INSERT INTO `ebi_template` VALUES ('Pdf_AcademicUpdates_Header_Template','y'),('Pdf_CategoryType_Body_Template','y'),('Pdf_Course_Footer_Template','y'),('Pdf_Courses_Header_Template','y'),('Pdf_CoursesFaculty_Header_Template','y'),('Pdf_CoursesStudents_Header_Template','y'),('Pdf_CourseStudent_Footer_Template','y'),('Pdf_DateType_Body_Template','y'),('Pdf_Faculty_Header_Template','y'),('Pdf_GroupFaculty_Header_Template','y'),('Pdf_GroupStudent_Header_Template','y'),('Pdf_NumberType_Body_Template','y'),('Pdf_StringType_Body_Template','y'),('Pdf_Student_Footer_Template','y'),('Pdf_Student_Header_Template','y'),('Pdf_SubGroup_Footer_Template','y'),('Pdf_SubGroup_Header_Template','y'),('Pdf_TextType_Body_Template','y');
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
-- Temporary view structure for view `group_course_discriminator`
--

DROP TABLE IF EXISTS `group_course_discriminator`;
/*!50001 DROP VIEW IF EXISTS `group_course_discriminator`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `group_course_discriminator` AS SELECT 
 1 AS `association`*/;
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
INSERT INTO `migration_versions` VALUES ('20140729224036'),('20140729225635'),('20140729230250'),('20140729231026'),('20140729233858'),('20140729234955'),('20140731191946'),('20140804164058'),('20140805185717'),('20140818152930'),('20140818154234'),('20140826144814'),('20140826155008'),('20140828205229'),('20140828231414'),('20140902041125'),('20140905080440'),('20140908170845'),('20140909132358'),('20140911141907'),('20140911145102'),('20140912123326'),('20140918101509'),('20140918110133'),('20140919071650'),('20140922091249'),('20140924095105'),('20140925100825'),('20140925115027'),('20140926090013'),('20140926110935'),('20140930070613'),('20140930140236'),('20141008071840'),('20141013114649'),('20141014063455'),('20141014090811'),('20141018211438'),('20141019160524'),('20141021102624'),('20141022214202'),('20141024072511'),('20141024100649'),('20141024173420'),('20141027121006'),('20141028095528'),('20141028112856'),('20141028120250'),('20141029091232'),('20141029101208'),('20141029131639'),('20141029133752'),('20141030154643'),('20141030220739'),('20141103103711'),('20141105065017'),('20141110140850'),('20141110151243'),('20141117054130'),('20141117071030'),('20141118101230'),('20141124072948'),('20141124174128'),('20141125071407'),('20141202065337'),('20141202151544'),('20141203052022'),('20141215124432'),('20141217084859'),('20141217111515'),('20141219140752'),('20141222044424'),('20141222071555'),('20141222072807'),('20141222113621'),('20141223114036'),('20141223114859'),('20141223131147'),('20141224093848'),('20141224103116'),('20141231130951'),('20150101105233'),('20150102061610'),('20150105133005'),('20150105163800'),('20150204113759'),('20150204114823'),('20150204235054'),('20150205093603'),('20150206095410'),('20150212093729'),('20150213082352'),('20150213094045'),('20150216064337'),('20150216065341'),('20150216095322'),('20150223133712'),('20150224091638'),('20150224113148'),('20150224123706'),('20150225092423'),('20150226050641'),('20150226141830'),('20150303114949'),('20150310155857'),('20150312044209'),('20150319141555'),('20150320071335'),('20150320114341'),('20150320154639'),('20150323145008'),('20150327163207'),('20150329072515'),('20150331063618'),('20150331101820'),('20150331130418'),('20150401135946'),('20150403110531'),('20150405065621'),('20150406152225'),('20150408072706'),('20150408073332'),('20150408094552'),('20150413100837'),('20150415094212'),('20150417134014'),('20150420094639'),('20150422195401'),('20150423063739'),('20150423124544'),('20150424052218'),('20150424094408'),('20150427132701'),('20150427162221'),('20150429143237'),('20150430093931'),('20150430145426'),('20150430175619'),('20150504042602'),('20150504102328'),('20150504133456'),('20150504220150'),('20150506111729'),('20150506205053'),('20150508130511'),('20150508135446'),('20150508142629'),('20150511135852'),('20150511160405'),('20150511222206'),('20150511232551'),('20150512074755'),('20150512132734'),('20150512160016'),('20150513075038'),('20150513182646'),('20150513213415'),('20150514134921'),('20150514142302'),('20150515042040'),('20150518035112'),('20150518135644'),('20150519112425'),('20150520133919'),('20150522114239'),('20150522125700'),('20150525151435'),('20150528135607'),('20150529072040'),('20150601112737'),('20150601125952'),('20150602113522'),('20150602135352'),('20150602202230'),('20150603120314'),('20150603130058'),('20150603152914'),('20150604132235'),('20150604142628'),('20150604142629'),('20150605143914'),('20150605180022'),('20150605200154'),('20150608100922'),('20150608141745'),('20150608170922'),('20150609092340'),('20150609103524'),('20150609132543'),('20150610070638'),('20150610134334'),('20150610142420'),('20150611052615'),('20150611060455'),('20150611062722'),('20150611063934'),('20150611070433'),('20150611070944'),('20150611174758'),('20150612071231'),('20150612130920'),('20150612132312'),('20150612140439'),('20150615133529'),('20150616042202'),('20150616062835'),('20150616105058'),('20150616121115'),('20150616135537'),('20150616140732'),('20150617102207'),('20150617135858'),('20150617154909'),('20150618065429'),('20150618101525'),('20150618104157'),('20150618114453'),('20150618123735'),('20150618180022'),('20150618190035'),('20150619061106'),('20150619082424'),('20150619090656'),('20150622063226'),('20150622073038'),('20150622132512'),('20150623072833'),('20150623112606'),('20150623115030'),('20150623142519'),('20150625230922'),('20150626054313'),('20150626220702'),('20150626235220'),('20150627070035'),('20150627114123'),('20150627114458'),('20150627140226'),('20150628080543'),('20150628110757'),('20150628131058'),('20150628161058'),('20150628174010'),('20150629082256'),('20150630055354'),('20150630062332'),('20150630064839'),('20150630104022'),('20150630114853'),('20150630125402'),('20150630153305'),('20150630171122'),('20150701070419'),('20150701085116'),('20150701100911'),('20150701125411'),('20150701140621'),('20150701181000'),('20150701183324'),('20150701191010'),('20150703093010'),('20150703101605'),('20150703110010'),('20150703120922'),('20150703131022'),('20150703195522'),('20150704153010'),('20150704160000'),('20150706105528'),('20150706110454'),('20150707124111'),('20150707133432'),('20150708073354'),('20150708095013'),('20150708120922'),('20150708165210'),('20150709154032'),('20150710052041'),('20150710094907'),('20150710095825'),('20150710115959'),('20150710154700'),('20150710200419'),('20150711063328'),('20150711064303'),('20150711150122'),('20150711180122'),('20150711184109'),('20150712002522'),('20150712163522'),('20150713050947'),('20150713111826'),('20150713160122'),('20150713180922'),('20150714062826'),('20150714071505'),('20150714143559'),('20150715142100'),('20150721053753'),('20150721163753'),('20150722070400'),('20150722095926'),('20150722131130'),('20150729063748'),('20150729100620'),('20150729110928'),('20150729114130'),('20150729244431'),('20150730095428'),('20150731044041'),('20150731090316'),('20150731131034'),('20150803094443'),('20150803222843'),('20150804095231'),('20150804102341'),('20150804144844'),('20150804145947'),('20150804153458'),('20150805043557'),('20150805061044'),('20150805124139'),('20150805133325'),('20150805134719'),('20150805200139'),('20150806033800'),('20150806073649'),('20150806094619'),('20150806124822'),('20150806175812'),('20150806181744'),('20150806181935'),('20150806182629'),('20150807115827'),('20150807121811'),('20150810112744'),('20150810121832'),('20150810131546'),('20150810132818'),('20150810133358'),('20150810150705'),('20150810152046'),('20150810214640'),('20150811070115'),('20150811114425'),('20150811134602'),('20150812122518'),('20150812130913'),('20150812152433'),('20150813051602'),('20150813090121'),('20150813114302'),('20150813130046'),('20150813135605'),('20150813212902'),('20150813220354'),('20150814063553'),('20150814130808'),('20150817095951'),('20150817214322'),('20150818034549'),('20150818061010'),('20150818061429'),('20150818085704'),('20150818092646'),('20150818094826'),('20150818101747'),('20150818135647'),('20150818145735'),('20150818150438'),('20150818152412'),('20150818211145'),('20150819092233'),('20150820042247'),('20150820045221'),('20150820070312'),('20150820085942'),('20150820114312'),('20150820205022'),('20150820213851'),('20150821105411'),('20150821131027'),('20150821134657'),('20150821200618'),('20150821234410'),('20150822024841'),('20150822030642'),('20150824019999'),('20150824054114'),('20150824103647'),('20150824193211'),('20150825070444'),('20150825091220'),('20150825093347'),('20150825130406'),('20150825152530'),('20150825155523'),('20150825183446'),('20150825211142'),('20150825220528'),('20150826063456'),('20150826130231'),('20150826143454'),('20150826145956'),('20150826174450'),('20150827112633'),('20150827120444'),('20150827132617'),('20150827135232'),('20150827140456'),('20150828120706'),('20150828131326'),('20150828165456'),('20150828170445'),('20150828193315'),('20150828205105'),('20150831142312'),('20150831150242'),('20150831194022'),('20150901035024'),('20150901040944'),('20150901061840'),('20150901074036'),('20150901091601'),('20150902093725'),('20150902121442'),('20150902150336'),('20150902155752'),('20150903024315'),('20150903132437'),('20150903143242'),('20150903163639'),('20150904115203'),('20150904194223'),('20150907101253'),('20150907104837'),('20150907112956'),('20150907113556'),('20150907114128'),('20150907115123'),('20150907115402'),('20150907115623'),('20150907121515'),('20150907125059'),('20150907125825'),('20150907130008'),('20150907173242'),('20150908083758'),('20150908101806'),('20150908142430'),('20150908151213'),('20150908154408'),('20150908191335'),('20150909102216'),('20150909124242'),('20150909220603'),('20150910063053'),('20150910064432'),('20150910115377'),('20150910133945'),('20150910191811'),('20150910222345'),('20150913004538'),('20150914024848'),('20150915060904'),('20150915100331'),('20150915181758'),('20150916084034'),('20150916143633'),('20150916144326'),('20150916160453'),('20150918203409'),('20150921071414'),('20150921085225'),('20150921115016'),('20150921164014'),('20150922093245'),('20150922094028'),('20150922132159'),('20150922134534'),('20150922190009'),('20150923084433'),('20150923113640'),('20150923134042'),('20150923150432'),('20150923173536'),('20150923193541'),('20150923194837'),('20150923214747'),('20150924021556'),('20150924045315'),('20150924162915'),('20150925153542'),('20150928070540'),('20150928130210'),('20150928200002'),('20150929125820'),('20150929130851'),('20150929181808'),('20150929185911'),('20150929191823'),('20150930052321'),('20150930063356'),('20150930195038'),('20151001103032'),('20151002000505'),('20151002003635'),('20151002152355'),('20151005065151'),('20151007115528'),('20151007165835'),('20151008123514'),('20151008204141'),('20151008205944'),('20151008211516'),('20151008212057'),('20151008212545'),('20151009132258'),('20151009181936'),('20151012062545'),('20151012111020'),('20151012114325'),('20151012125150'),('20151012141009'),('20151013042935'),('20151014073645'),('20151014112259'),('20151014164308'),('20151015111944'),('20151015122314'),('20151015141132'),('20151015183553'),('20151016064427'),('20151016102219'),('20151016131049'),('20151016151445'),('20151018083942'),('20151019090548'),('20151020054153'),('20151020093450'),('20151020135630'),('20151020153526'),('20151020155234'),('20151020182414'),('20151020201045'),('20151020210246'),('20151021182931'),('20151022130400'),('20151023140226'),('20151026160104'),('20151027031414'),('20151027150800'),('20151028084555'),('20151028091941'),('20151029072346'),('20151029104751'),('20151030060023'),('20151030085030'),('20151102063725'),('20151102150958'),('20151102151515'),('20151102205413'),('20151102222131'),('20151103051320'),('20151103054035'),('20151103153638'),('20151104071329'),('20151104200846'),('20151105065227'),('20151105153320'),('20151109193311'),('20151119150536'),('20151119214353'),('20151123065211'),('20151123095458'),('20151123114307'),('20151123141331'),('20151124065258'),('20151124120925'),('20151126105927'),('20151127063330'),('20151130201447'),('20151201095057'),('20151201164552'),('20151201165743'),('20151201193521'),('20151201200059'),('20151202061209'),('20151202165630'),('20151202175447'),('20151202183637'),('20151203144626'),('20151203210847'),('20151207074056'),('20151208044553'),('20151208053313'),('20151208061423'),('20151209120834'),('20151209125713'),('20151209205644'),('20151210043603'),('20151210120145'),('20151211101613'),('20151211135121'),('20151214064659'),('20151215044048'),('20151216102734'),('20151216105155'),('20152210185911'),('20151211185035');
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
  `is_email_sent` enum('yes','no') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
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
-- Temporary view structure for view `org_calculated_risk_variables`
--

DROP TABLE IF EXISTS `org_calculated_risk_variables`;
/*!50001 DROP VIEW IF EXISTS `org_calculated_risk_variables`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `org_calculated_risk_variables` AS SELECT 
 1 AS `person_id`,
 1 AS `risk_variable_id`,
 1 AS `risk_group_id`,
 1 AS `risk_model_id`,
 1 AS `created_at`,
 1 AS `org_id`,
 1 AS `calc_bucket_value`,
 1 AS `calc_weight`,
 1 AS `risk_source_value`*/;
SET character_set_client = @saved_cs_client;

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
-- Temporary view structure for view `org_calculated_risk_variables_view`
--

DROP TABLE IF EXISTS `org_calculated_risk_variables_view`;
/*!50001 DROP VIEW IF EXISTS `org_calculated_risk_variables_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `org_calculated_risk_variables_view` AS SELECT 
 1 AS `org_id`,
 1 AS `risk_group_id`,
 1 AS `person_id`,
 1 AS `risk_variable_id`,
 1 AS `risk_model_id`,
 1 AS `source`,
 1 AS `variable_type`,
 1 AS `weight`,
 1 AS `calculated_value`,
 1 AS `bucket_value`*/;
SET character_set_client = @saved_cs_client;

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
-- Temporary view structure for view `org_course_faculty_student_permission_map`
--

DROP TABLE IF EXISTS `org_course_faculty_student_permission_map`;
/*!50001 DROP VIEW IF EXISTS `org_course_faculty_student_permission_map`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `org_course_faculty_student_permission_map` AS SELECT 
 1 AS `course_id`,
 1 AS `org_id`,
 1 AS `faculty_id`,
 1 AS `student_id`,
 1 AS `permissionset_id`*/;
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
-- Temporary view structure for view `org_faculty_student_permission_map`
--

DROP TABLE IF EXISTS `org_faculty_student_permission_map`;
/*!50001 DROP VIEW IF EXISTS `org_faculty_student_permission_map`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `org_faculty_student_permission_map` AS SELECT 
 1 AS `org_id`,
 1 AS `faculty_id`,
 1 AS `student_id`,
 1 AS `group_id`,
 1 AS `course_id`,
 1 AS `permissionset_id`*/;
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
-- Temporary view structure for view `org_group_faculty_student_permission_map`
--

DROP TABLE IF EXISTS `org_group_faculty_student_permission_map`;
/*!50001 DROP VIEW IF EXISTS `org_group_faculty_student_permission_map`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `org_group_faculty_student_permission_map` AS SELECT 
 1 AS `group_id`,
 1 AS `org_id`,
 1 AS `faculty_id`,
 1 AS `student_id`,
 1 AS `permissionset_id`*/;
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
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `cohort_code` int(11) DEFAULT NULL,
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
-- Temporary view structure for view `org_person_riskvariable`
--

DROP TABLE IF EXISTS `org_person_riskvariable`;
/*!50001 DROP VIEW IF EXISTS `org_person_riskvariable`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `org_person_riskvariable` AS SELECT 
 1 AS `org_id`,
 1 AS `person_id`,
 1 AS `risk_variable_id`,
 1 AS `source`,
 1 AS `variable_type`,
 1 AS `calc_type`,
 1 AS `risk_group_id`,
 1 AS `calculation_end_date`,
 1 AS `calculation_start_date`,
 1 AS `risk_model_id`,
 1 AS `weight`,
 1 AS `modified_at`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `org_person_riskvariable_datum`
--

DROP TABLE IF EXISTS `org_person_riskvariable_datum`;
/*!50001 DROP VIEW IF EXISTS `org_person_riskvariable_datum`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `org_person_riskvariable_datum` AS SELECT 
 1 AS `org_id`,
 1 AS `person_id`,
 1 AS `risk_variable_id`,
 1 AS `source_value`,
 1 AS `modified_at`,
 1 AS `created_at`,
 1 AS `scope`,
 1 AS `org_academic_year_id`,
 1 AS `org_academic_terms_id`*/;
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
-- Table structure for table `org_question`
--

DROP TABLE IF EXISTS `org_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) DEFAULT NULL,
  `question_type_id` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `question_key` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_text` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_CA58D9AD32C8A3DE` (`organization_id`),
  KEY `IDX_CA58D9ADF142426F` (`question_category_id`),
  KEY `IDX_CA58D9ADCB90598E` (`question_type_id`),
  CONSTRAINT `FK_CA58D9AD32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `FK_CA58D9ADCB90598E` FOREIGN KEY (`question_type_id`) REFERENCES `question_type` (`id`),
  CONSTRAINT `FK_CA58D9ADF142426F` FOREIGN KEY (`question_category_id`) REFERENCES `question_category` (`id`)
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
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER org_calc_move AFTER INSERT ON org_riskval_calc_inputs
                FOR EACH ROW
                BEGIN
                    INSERT INTO org_calc_flags_factor (org_id, person_id, calculated_at, created_at, modified_at) 
                    VALUES(NEW.org_id, NEW.person_id, '1910-10-10 10:10:10', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
                    INSERT INTO org_calc_flags_risk (org_id, person_id, calculated_at, created_at, modified_at) 
                    VALUES(NEW.org_id, NEW.person_id, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
                    INSERT INTO org_calc_flags_talking_point (org_id, person_id, calculated_at, created_at, modified_at) 
                    VALUES(NEW.org_id, NEW.person_id, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
                    INSERT INTO org_calc_flags_success_marker (org_id, person_id, calculated_at, created_at, modified_at) 
                    VALUES(NEW.org_id, NEW.person_id, '1910-10-10 10:10:10', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
                END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization`
--

LOCK TABLES `organization` WRITE;
/*!40000 ALTER TABLE `organization` DISABLE KEYS */;
INSERT INTO `organization` VALUES (-2,NULL,NULL,NULL,NULL,NULL,NULL,'ART user',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(-1,NULL,NULL,NULL,NULL,NULL,NULL,'Ebi User Org',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
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
INSERT INTO `person` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'David','Warner',NULL,NULL,'David123','david.warner@gmail.com','$2y$13$f6bnaUYhaIO0qzJ0krqrIeUDnxJxWYYEyB3L6qDDK/1ln5CsHKEca',NULL,NULL,-1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(200,NULL,NULL,NULL,NULL,NULL,NULL,'art','member',NULL,NULL,'Art123','art.member@gmail.com','$2y$13$GOZ4jneZjjgknooNxJEzwOBJ3URrsRmHVSgKudF0nsHnOb0F4/8ue',NULL,NULL,-2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `person_MD_talking_points_calculated`
--

DROP TABLE IF EXISTS `person_MD_talking_points_calculated`;
/*!50001 DROP VIEW IF EXISTS `person_MD_talking_points_calculated`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `person_MD_talking_points_calculated` AS SELECT 
 1 AS `org_id`,
 1 AS `person_id`,
 1 AS `talking_points_id`,
 1 AS `ebi_metadata_id`,
 1 AS `org_academic_year_id`,
 1 AS `org_academic_terms_id`,
 1 AS `response`,
 1 AS `source_modified_at`*/;
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
-- Temporary view structure for view `person_risk_level_calc`
--

DROP TABLE IF EXISTS `person_risk_level_calc`;
/*!50001 DROP VIEW IF EXISTS `person_risk_level_calc`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `person_risk_level_calc` AS SELECT 
 1 AS `org_id`,
 1 AS `person_id`,
 1 AS `risk_group_id`,
 1 AS `risk_model_id`,
 1 AS `weighted_value`,
 1 AS `maximum_weight_value`,
 1 AS `risk_score`,
 1 AS `risk_level`,
 1 AS `risk_text`,
 1 AS `image_name`,
 1 AS `color_hex`*/;
SET character_set_client = @saved_cs_client;

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
-- Temporary view structure for view `person_riskmodel_calc_view`
--

DROP TABLE IF EXISTS `person_riskmodel_calc_view`;
/*!50001 DROP VIEW IF EXISTS `person_riskmodel_calc_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `person_riskmodel_calc_view` AS SELECT 
 1 AS `org_id`,
 1 AS `person_id`,
 1 AS `risk_group_id`,
 1 AS `risk_model_id`,
 1 AS `RS_Numerator`,
 1 AS `RS_Denominator`,
 1 AS `risk_score`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `person_survey_talking_points_calculated`
--

DROP TABLE IF EXISTS `person_survey_talking_points_calculated`;
/*!50001 DROP VIEW IF EXISTS `person_survey_talking_points_calculated`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `person_survey_talking_points_calculated` AS SELECT 
 1 AS `org_id`,
 1 AS `person_id`,
 1 AS `talking_points_id`,
 1 AS `ebi_question_id`,
 1 AS `survey_id`,
 1 AS `response`,
 1 AS `source_modified_at`*/;
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
  `person_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2C4E0EEBB24851AE` (`referrals_id`),
  KEY `IDX_2C4E0EEB217BBB47` (`person_id`),
  CONSTRAINT `FK_2C4E0EEB217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2C4E0EEBB24851AE` FOREIGN KEY (`referrals_id`) REFERENCES `referrals` (`id`)
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
  CONSTRAINT `FK_91D6E5F51F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_91D6E5F525F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_91D6E5F579F0E193` FOREIGN KEY (`ebi_question_id`) REFERENCES `ebi_question` (`id`),
  CONSTRAINT `FK_91D6E5F5A6DF29BA` FOREIGN KEY (`survey_question_id`) REFERENCES `survey_questions` (`id`),
  CONSTRAINT `FK_91D6E5F5B3FE509D` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `FK_91D6E5F5BC88C1A3` FOREIGN KEY (`factor_id`) REFERENCES `factor` (`id`),
  CONSTRAINT `FK_91D6E5F5D823E37A` FOREIGN KEY (`section_id`) REFERENCES `report_sections` (`id`),
  CONSTRAINT `FK_91D6E5F5DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_section_elements`
--

LOCK TABLES `report_section_elements` WRITE;
/*!40000 ALTER TABLE `report_section_elements` DISABLE KEYS */;
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
  PRIMARY KEY (`id`),
  KEY `IDX_2BF6DAE5DE12AB56` (`created_by`),
  KEY `IDX_2BF6DAE525F94802` (`modified_by`),
  KEY `IDX_2BF6DAE51F6FA0AF` (`deleted_by`),
  KEY `fk_sections_reports1_idx` (`report_id`),
  CONSTRAINT `FK_2BF6DAE51F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2BF6DAE525F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_2BF6DAE54BD2A4C0` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`),
  CONSTRAINT `FK_2BF6DAE5DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_sections`
--

LOCK TABLES `report_sections` WRITE;
/*!40000 ALTER TABLE `report_sections` DISABLE KEYS */;
INSERT INTO `report_sections` VALUES (1,NULL,NULL,NULL,1,NULL,NULL,NULL,'Earning the Grades You Want',1,NULL),(2,NULL,NULL,NULL,1,NULL,NULL,NULL,'Connecting with Others',2,NULL),(3,NULL,NULL,NULL,1,NULL,NULL,NULL,'Paying for College',3,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'student-report','Student success report - Survey',NULL,NULL,'SUR-SSR','n'),(2,NULL,NULL,NULL,NULL,NULL,NULL,'Individual Response Report','See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions','y','n','SUR-IRR','n'),(3,NULL,NULL,NULL,NULL,NULL,NULL,'All Academic Updates Report','See all academic updates for your students.  Export to csv, perform individual or bulk actions','y','n','AU-R','n'),(6,NULL,NULL,NULL,NULL,NULL,NULL,'Group Response Report','Compare survey response rates for different groups.  Export to csv','y',NULL,'SUR-GRR','y'),(7,NULL,NULL,NULL,NULL,NULL,NULL,'Our Mapworks Activity','View statistics on faculty and student activity tracked in Mapworks for a given date range.  Export to pdf','y','y','MAR','n'),(8,NULL,NULL,NULL,NULL,NULL,NULL,'Survey Snapshot Report','See aggregated responses to all survey questions for a given survey and cohort.  Drill down to see individual students','y','n','SUR-SR','y'),(9,NULL,NULL,NULL,NULL,NULL,NULL,'Our Students Report','See Top Five issues, high-level survey data and demographics for a single survey and cohort.. Export to pdf','n','n','OSR','n'),(10,NULL,NULL,NULL,NULL,NULL,NULL,'Survey Factor Reports','See aggregated values of all survey factors for a given survey and cohort.  Drill down to see individual students','n','n','SUR-FR','n');
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
INSERT INTO `risk_level` VALUES (2,NULL,NULL,NULL,NULL,NULL,NULL,'gray','risk-level-icon-gray.png','#cccccc');
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
-- Table structure for table `survey`
--

DROP TABLE IF EXISTS `survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `external_id` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
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
  `upload_type` enum('A','C','F','G','S','SB','SM','T','TP','P','H','SL','RV','RM','RMA','CI','FA','GS','GF','S2G','OSR') COLLATE utf8_unicode_ci DEFAULT NULL,
  `upload_date` datetime DEFAULT NULL,
  `uploaded_columns` varchar(6000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uploaded_row_count` int(11) DEFAULT NULL,
  `status` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uploaded_file_path` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
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
-- Final view structure for view `AUDIT_DASHBOARD_0_ReceiveSurvey_Students_With_Survey_Responses`
--

/*!50001 DROP VIEW IF EXISTS `AUDIT_DASHBOARD_0_ReceiveSurvey_Students_With_Survey_Responses`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `AUDIT_DASHBOARD_0_ReceiveSurvey_Students_With_Survey_Responses` AS select `sr`.`org_id` AS `org_id`,count(distinct `sr`.`person_id`) AS `Number_of_Students` from (`survey_response` `sr` join `org_person_student` `ops` on(((`sr`.`person_id` = `ops`.`person_id`) and (`sr`.`org_id` = `ops`.`organization_id`)))) where (`ops`.`receivesurvey` = 0) group by `ops`.`organization_id` order by `ops`.`organization_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `AUDIT_DASHBOARD_Failed_Uploads_By_Organization_Past_24_Hours`
--

/*!50001 DROP VIEW IF EXISTS `AUDIT_DASHBOARD_Failed_Uploads_By_Organization_Past_24_Hours`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `AUDIT_DASHBOARD_Failed_Uploads_By_Organization_Past_24_Hours` AS select `ol`.`organization_id` AS `organization_id`,`ol`.`organization_name` AS `organization_name`,count(0) AS `Failed_Uploads` from (`upload_file_log` `ufl` join `organization_lang` `ol` on((`ol`.`organization_id` = `ufl`.`organization_id`))) where ((`ufl`.`status` = 'F') and (`ufl`.`created_at` > (now() - interval 1 day))) group by `ufl`.`organization_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `AUDIT_DASHBOARD_Failed_Uploads_By_Type_Past_24_Hours`
--

/*!50001 DROP VIEW IF EXISTS `AUDIT_DASHBOARD_Failed_Uploads_By_Type_Past_24_Hours`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `AUDIT_DASHBOARD_Failed_Uploads_By_Type_Past_24_Hours` AS select `upload_file_log`.`upload_type` AS `upload_type`,count(0) AS `Failed_Uploads` from `upload_file_log` where ((`upload_file_log`.`status` = 'F') and (`upload_file_log`.`created_at` > (now() - interval 1 day))) group by `upload_file_log`.`upload_type` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `AUDIT_DASHBOARD_Organization_Survey_Cohort_Groupings`
--

/*!50001 DROP VIEW IF EXISTS `AUDIT_DASHBOARD_Organization_Survey_Cohort_Groupings`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `AUDIT_DASHBOARD_Organization_Survey_Cohort_Groupings` AS select `ol`.`organization_id` AS `organization_id`,`o`.`campus_id` AS `campus_id`,`wl`.`status` AS `status`,`wl`.`cohort_code` AS `cohort_code`,`wl`.`survey_id` AS `survey_id`,`wl`.`wess_order_id` AS `wess_order_id`,`ol`.`organization_name` AS `organization_name`,`wl`.`open_date` AS `open_date`,`wl`.`close_date` AS `close_date`,count(distinct `ops`.`person_id`) AS `People_in_Cohort` from (((`organization_lang` `ol` join `wess_link` `wl` on((`wl`.`org_id` = `ol`.`organization_id`))) join `organization` `o` on((`o`.`id` = `ol`.`organization_id`))) join `org_person_student` `ops` on(((`wl`.`cohort_code` = `ops`.`surveycohort`) and (`o`.`id` = `ops`.`organization_id`)))) where (`ops`.`receivesurvey` <> 0) group by `wl`.`wess_order_id` order by `ol`.`organization_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `AUDIT_DASHBOARD_Student_With_Survey_Response_And_Null_Cohort`
--

/*!50001 DROP VIEW IF EXISTS `AUDIT_DASHBOARD_Student_With_Survey_Response_And_Null_Cohort`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `AUDIT_DASHBOARD_Student_With_Survey_Response_And_Null_Cohort` AS select `ops`.`organization_id` AS `organization_id`,`o`.`campus_id` AS `Wess_InsID`,`ol`.`organization_name` AS `organization_name`,count(distinct `sr`.`person_id`) AS `students_with_responses_and_null_cohort` from (((`org_person_student` `ops` join `survey_response` `sr` on(((`ops`.`person_id` = `sr`.`person_id`) and (`ops`.`organization_id` = `sr`.`org_id`)))) join `organization_lang` `ol` on((`ops`.`organization_id` = `ol`.`organization_id`))) join `organization` `o` on((`o`.`id` = `ol`.`organization_id`))) where isnull(`ops`.`surveycohort`) group by `o`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `DASHBOARD_Student_Calculations`
--

/*!50001 DROP VIEW IF EXISTS `DASHBOARD_Student_Calculations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `DASHBOARD_Student_Calculations` AS select 'Factor' AS `Calculation Type`,sum((case when (`org_calc_flags_factor`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) AS `Calculated Students`,sum((case when (`org_calc_flags_factor`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) AS `Students With No Data`,sum((case when isnull(`org_calc_flags_factor`.`calculated_at`) then 1 else 0 end)) AS `Flagged For Calculation`,sum((case when (`org_calc_flags_factor`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) AS `No Survey Data`,count(0) AS `Total Students`,concat(((sum((case when (`org_calc_flags_factor`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculated Percentage`,concat(((sum((case when (`org_calc_flags_factor`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) / count(0)) * 100),'%') AS `No Data Percentage`,concat(((sum((case when isnull(`org_calc_flags_factor`.`calculated_at`) then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculating Percentage`,concat(((sum((case when (`org_calc_flags_factor`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `No Survey Data Percentage` from `org_calc_flags_factor` union select 'Risk' AS `Risk`,sum((case when (`org_calc_flags_risk`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) AS `Calculated Students`,sum((case when (`org_calc_flags_risk`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) AS `Students With No Data`,sum((case when isnull(`org_calc_flags_risk`.`calculated_at`) then 1 else 0 end)) AS `Flagged For Calculation`,sum((case when (`org_calc_flags_risk`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) AS `No Survey Data`,count(0) AS `Total Students`,concat(((sum((case when (`org_calc_flags_risk`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculated Percentage`,concat(((sum((case when (`org_calc_flags_risk`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) / count(0)) * 100),'%') AS `No Data Percentage`,concat(((sum((case when isnull(`org_calc_flags_risk`.`calculated_at`) then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculating Percentage`,concat(((sum((case when (`org_calc_flags_risk`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `No Survey Data Percentage` from `org_calc_flags_risk` union select 'Success Marker' AS `Success Marker`,sum((case when (`org_calc_flags_success_marker`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) AS `Calculated Students`,sum((case when (`org_calc_flags_success_marker`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) AS `Students With No Data`,sum((case when isnull(`org_calc_flags_success_marker`.`calculated_at`) then 1 else 0 end)) AS `Flagged For Calculation`,sum((case when (`org_calc_flags_success_marker`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) AS `No Survey Data`,count(0) AS `Total Students`,concat(((sum((case when (`org_calc_flags_success_marker`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculated Percentage`,concat(((sum((case when (`org_calc_flags_success_marker`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) / count(0)) * 100),'%') AS `No Data Percentage`,concat(((sum((case when isnull(`org_calc_flags_success_marker`.`calculated_at`) then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculating Percentage`,concat(((sum((case when (`org_calc_flags_success_marker`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `No Survey Data Percentage` from `org_calc_flags_success_marker` union select 'Talking Points' AS `Talking Points`,sum((case when (`org_calc_flags_talking_point`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) AS `Calculated Students`,sum((case when (`org_calc_flags_talking_point`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) AS `Students With No Data`,sum((case when isnull(`org_calc_flags_talking_point`.`calculated_at`) then 1 else 0 end)) AS `Flagged For Calculation`,sum((case when (`org_calc_flags_talking_point`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) AS `No Survey Data`,count(0) AS `Total Students`,concat(((sum((case when (`org_calc_flags_talking_point`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculated Percentage`,concat(((sum((case when (`org_calc_flags_talking_point`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) / count(0)) * 100),'%') AS `No Data Percentage`,concat(((sum((case when isnull(`org_calc_flags_talking_point`.`calculated_at`) then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculating Percentage`,concat(((sum((case when (`org_calc_flags_talking_point`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `No Survey Data Percentage` from `org_calc_flags_talking_point` union select 'Student Reports' AS `Student Reports`,sum((case when (`sr`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) AS `Calculated Students`,sum((case when (`sr`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) AS `Students With No Data`,sum((case when isnull(`sr`.`calculated_at`) then 1 else 0 end)) AS `Flagged For Calculation`,sum((case when (`sr`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) AS `No Survey Data`,count(0) AS `Total Students`,concat(((sum((case when (`sr`.`calculated_at` > '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculated Percentage`,concat(((sum((case when (`sr`.`calculated_at` = '1900-01-01 00:00:00') then 1 else 0 end)) / count(0)) * 100),'%') AS `No Data Percentage`,concat(((sum((case when isnull(`sr`.`calculated_at`) then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculating Percentage`,concat(((sum((case when (`sr`.`calculated_at` = '1910-10-10 10:10:10') then 1 else 0 end)) / count(0)) * 100),'%') AS `No Survey Data Percentage` from `org_calc_flags_student_reports` `sr` where (`sr`.`modified_at` = (select max(`SRin`.`modified_at`) from `org_calc_flags_student_reports` `SRin` where ((`SRin`.`org_id` = `sr`.`org_id`) and (`SRin`.`person_id` = `sr`.`person_id`)))) union select 'Report PDF Generation' AS `Report PDF Generation`,sum((case when (`sr`.`file_name` is not null) then 1 else 0 end)) AS `Calculated Students`,NULL AS `Students With No Data`,sum((case when isnull(`sr`.`file_name`) then 1 else 0 end)) AS `Flagged For Calculation`,NULL AS `No Survey Data`,count(0) AS `Total Students`,concat(((sum((case when (`sr`.`file_name` is not null) then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculated Percentage`,NULL AS `No Data Percentage`,concat(((sum((case when isnull(`sr`.`file_name`) then 1 else 0 end)) / count(0)) * 100),'%') AS `Calculating Percentage`,NULL AS `No Survey Data Percentage` from `org_calc_flags_student_reports` `sr` where (`sr`.`modified_at` = (select max(`SRin`.`modified_at`) from `org_calc_flags_student_reports` `SRin` where ((`SRin`.`org_id` = `sr`.`org_id`) and (`SRin`.`person_id` = `sr`.`person_id`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `DASHBOARD_Student_Surveys_By_Org`
--

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

/*!50001 DROP VIEW IF EXISTS `Issues_Factors`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `Issues_Factors` AS select `pfc`.`organization_id` AS `org_id`,`pfc`.`person_id` AS `student_id`,`pfc`.`survey_id` AS `survey_id`,`iss`.`id` AS `issue_id`,`opssl`.`cohort` AS `cohort`,`pfc`.`factor_id` AS `factor_id`,`ISFS`.`faculty_id` AS `faculty_id`,`pfc`.`mean_value` AS `permitted_value`,`pfc`.`modified_at` AS `modified_at` from ((((((`org_faculty_student_permission_map` `ISFS` join `org_person_student_survey_link` `opssl` on(((`opssl`.`org_id` = `ISFS`.`org_id`) and (`opssl`.`person_id` = `ISFS`.`student_id`)))) join `person_factor_calculated` `pfc` on(((`ISFS`.`student_id` = `pfc`.`person_id`) and (`ISFS`.`org_id` = `pfc`.`organization_id`) and (`opssl`.`survey_id` = `pfc`.`survey_id`) and isnull(`pfc`.`deleted_at`)))) join `issue` `iss` on(((`iss`.`factor_id` = `pfc`.`factor_id`) and (`iss`.`survey_id` = `pfc`.`survey_id`) and isnull(`iss`.`deleted_at`)))) join `wess_link` `wl` on(((`wl`.`survey_id` = `pfc`.`survey_id`) and (`wl`.`org_id` = `pfc`.`organization_id`) and (`wl`.`cohort_code` = `opssl`.`cohort`) and (`wl`.`status` = 'closed')))) join `datablock_questions` `dq` on(((`dq`.`factor_id` = `pfc`.`factor_id`) and isnull(`dq`.`deleted_at`)))) join `org_permissionset_datablock` `opd` on(((`opd`.`organization_id` = `pfc`.`organization_id`) and (`opd`.`datablock_id` = `dq`.`datablock_id`) and (`opd`.`org_permissionset_id` = `ISFS`.`permissionset_id`) and isnull(`opd`.`deleted_at`)))) where (`pfc`.`id` = (select `fc`.`id` from `person_factor_calculated` `fc` where ((`fc`.`organization_id` = `pfc`.`organization_id`) and (`fc`.`person_id` = `pfc`.`person_id`) and (`fc`.`factor_id` = `pfc`.`factor_id`) and (`fc`.`survey_id` = `pfc`.`survey_id`)) order by `fc`.`modified_at` desc limit 1)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `Issues_Survey_Questions`
--

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
-- Final view structure for view `cur_org_aggregationcalc_risk_variable`
--

/*!50001 DROP VIEW IF EXISTS `cur_org_aggregationcalc_risk_variable`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY INVOKER */
/*!50001 VIEW `cur_org_aggregationcalc_risk_variable` AS select `OPRV`.`org_id` AS `org_id`,`OPRV`.`risk_group_id` AS `risk_group_id`,`OPRV`.`person_id` AS `person_id`,`OPRV`.`risk_variable_id` AS `risk_variable_id`,`OPRV`.`risk_model_id` AS `risk_model_id`,`OPRV`.`source` AS `source`,`OPRV`.`variable_type` AS `variable_type`,`OPRV`.`weight` AS `weight`,(`RISK_SCORE_AGGREGATED_RV`(`OPRV`.`org_id`,`OPRV`.`person_id`,`OPRV`.`risk_variable_id`,`OPRV`.`calc_type`,`OPRV`.`calculation_start_date`,`OPRV`.`calculation_end_date`) collate utf8_unicode_ci) AS `calculated_value`,`OPRV`.`calc_type` AS `calc_type` from `org_person_riskvariable` `OPRV` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `group_course_discriminator`
--

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
-- Final view structure for view `org_calculated_risk_variables`
--

/*!50001 DROP VIEW IF EXISTS `org_calculated_risk_variables`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY INVOKER */
/*!50001 VIEW `org_calculated_risk_variables` AS select `org_calculated_risk_variables_history`.`person_id` AS `person_id`,`org_calculated_risk_variables_history`.`risk_variable_id` AS `risk_variable_id`,`org_calculated_risk_variables_history`.`risk_group_id` AS `risk_group_id`,`org_calculated_risk_variables_history`.`risk_model_id` AS `risk_model_id`,`org_calculated_risk_variables_history`.`created_at` AS `created_at`,`org_calculated_risk_variables_history`.`org_id` AS `org_id`,`org_calculated_risk_variables_history`.`calc_bucket_value` AS `calc_bucket_value`,`org_calculated_risk_variables_history`.`calc_weight` AS `calc_weight`,`org_calculated_risk_variables_history`.`risk_source_value` AS `risk_source_value` from `org_calculated_risk_variables_history` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `org_calculated_risk_variables_view`
--

/*!50001 DROP VIEW IF EXISTS `org_calculated_risk_variables_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY INVOKER */
/*!50001 VIEW `org_calculated_risk_variables_view` AS select `RV`.`org_id` AS `org_id`,`RV`.`risk_group_id` AS `risk_group_id`,`RV`.`person_id` AS `person_id`,`RV`.`risk_variable_id` AS `risk_variable_id`,`RV`.`risk_model_id` AS `risk_model_id`,`RV`.`source` AS `source`,`RV`.`variable_type` AS `variable_type`,`RV`.`weight` AS `weight`,`RV`.`calculated_value` AS `calculated_value`,coalesce(`rvr`.`bucket_value`,`rvc`.`bucket_value`) AS `bucket_value` from ((`cur_org_aggregationcalc_risk_variable` `RV` left join `risk_variable_range` `rvr` on(((`rvr`.`risk_variable_id` = `RV`.`risk_variable_id`) and (cast(`RV`.`calculated_value` as decimal(13,4)) between `rvr`.`min` and `rvr`.`max`) and (`RV`.`variable_type` = 'continuous')))) left join `risk_variable_category` `rvc` on(((`rvc`.`risk_variable_id` = `RV`.`risk_variable_id`) and ((`RV`.`calculated_value` = `rvc`.`option_value`) or (cast(`RV`.`calculated_value` as signed) = `rvc`.`option_value`)) and (`RV`.`variable_type` = 'categorical')))) where ((`rvr`.`risk_variable_id` is not null) or (`rvc`.`risk_variable_id` is not null)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `org_course_faculty_student_permission_map`
--

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

/*!50001 DROP VIEW IF EXISTS `person_MD_talking_points_calculated`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY INVOKER */
/*!50001 VIEW `person_MD_talking_points_calculated` AS select `orc`.`org_id` AS `org_id`,`orc`.`person_id` AS `person_id`,`tp`.`id` AS `talking_points_id`,`pem`.`ebi_metadata_id` AS `ebi_metadata_id`,`pem`.`org_academic_year_id` AS `org_academic_year_id`,`pem`.`org_academic_terms_id` AS `org_academic_terms_id`,`tp`.`talking_points_type` AS `response`,`pem`.`modified_at` AS `source_modified_at` from ((`talking_points` `tp` join `person_ebi_metadata` `pem` on(((`tp`.`ebi_metadata_id` = `pem`.`ebi_metadata_id`) and (`pem`.`metadata_value` between `tp`.`min_range` and `tp`.`max_range`)))) join `org_calc_flags_talking_point` `orc` on((`pem`.`person_id` = `orc`.`person_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `person_risk_level_calc`
--

/*!50001 DROP VIEW IF EXISTS `person_risk_level_calc`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY INVOKER */
/*!50001 VIEW `person_risk_level_calc` AS select `PRC`.`org_id` AS `org_id`,`PRC`.`person_id` AS `person_id`,`PRC`.`risk_group_id` AS `risk_group_id`,`PRC`.`risk_model_id` AS `risk_model_id`,`PRC`.`RS_Numerator` AS `weighted_value`,`PRC`.`RS_Denominator` AS `maximum_weight_value`,`PRC`.`risk_score` AS `risk_score`,`RML`.`risk_level` AS `risk_level`,`RL`.`risk_text` AS `risk_text`,`RL`.`image_name` AS `image_name`,`RL`.`color_hex` AS `color_hex` from ((`person_riskmodel_calc_view` `PRC` left join `risk_model_levels` `RML` on(((`RML`.`risk_model_id` = `PRC`.`risk_model_id`) and (round(`PRC`.`risk_score`,4) between `RML`.`min` and `RML`.`max`)))) left join `risk_level` `RL` FORCE INDEX FOR JOIN (PRIMARY) on((`RL`.`id` = `RML`.`risk_level`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `person_riskmodel_calc_view`
--

/*!50001 DROP VIEW IF EXISTS `person_riskmodel_calc_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY INVOKER */
/*!50001 VIEW `person_riskmodel_calc_view` AS select `orgm`.`org_id` AS `org_id`,`rgph`.`person_id` AS `person_id`,`orgm`.`risk_group_id` AS `risk_group_id`,`orgm`.`risk_model_id` AS `risk_model_id`,`RS_numerator`(`orgm`.`org_id`,`orgm`.`risk_group_id`,`rgph`.`person_id`) AS `RS_Numerator`,`RS_denominator`(`orgm`.`org_id`,`orgm`.`risk_group_id`,`rgph`.`person_id`) AS `RS_Denominator`,(`RS_numerator`(`orgm`.`org_id`,`orgm`.`risk_group_id`,`rgph`.`person_id`) / `RS_denominator`(`orgm`.`org_id`,`orgm`.`risk_group_id`,`rgph`.`person_id`)) AS `risk_score` from (((`risk_group_person_history` `rgph` join `person` `ps` on((`ps`.`id` = `rgph`.`person_id`))) join `org_risk_group_model` `orgm` on(((`rgph`.`risk_group_id` = `orgm`.`risk_group_id`) and (`orgm`.`org_id` = `ps`.`organization_id`)))) join `risk_model_master` `rmm` on(((`orgm`.`risk_model_id` = `rmm`.`id`) and (now() between `rmm`.`calculation_start_date` and `rmm`.`calculation_end_date`)))) where (`orgm`.`risk_model_id` is not null) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `person_survey_talking_points_calculated`
--

/*!50001 DROP VIEW IF EXISTS `person_survey_talking_points_calculated`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`synapsemaster`@`%` SQL SECURITY INVOKER */
/*!50001 VIEW `person_survey_talking_points_calculated` AS select `orc`.`org_id` AS `org_id`,`orc`.`person_id` AS `person_id`,`tp`.`id` AS `talking_points_id`,`tp`.`ebi_question_id` AS `ebi_question_id`,`svr`.`survey_id` AS `survey_id`,`tp`.`talking_points_type` AS `response`,`svr`.`modified_at` AS `source_modified_at` from (((((`talking_points` `tp` join `survey_questions` `svq` on((`tp`.`ebi_question_id` = `svq`.`ebi_question_id`))) join `survey_response` `svr` on(((`svq`.`id` = `svr`.`survey_questions_id`) and ((case when (`svr`.`response_type` = 'decimal') then `svr`.`decimal_value` end) between `tp`.`min_range` and `tp`.`max_range`)))) join `org_calc_flags_talking_point` `orc` on(((`svr`.`person_id` = `orc`.`person_id`) and (`svr`.`org_id` = `orc`.`org_id`)))) join `org_person_student` `ops` on((`orc`.`person_id` = `ops`.`person_id`))) join `org_person_student_survey_link` `opssl` on(((`ops`.`surveycohort` = `opssl`.`cohort`) and (`opssl`.`survey_id` = `svr`.`survey_id`) and (`opssl`.`person_id` = `svr`.`person_id`)))) */;
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

-- Dump completed on 2015-12-16 17:21:17
