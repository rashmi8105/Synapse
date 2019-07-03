-- MySQL dump 10.13  Distrib 5.6.33, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: synapse
-- ------------------------------------------------------
-- Server version	5.6.33-0ubuntu0.14.04.1

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
