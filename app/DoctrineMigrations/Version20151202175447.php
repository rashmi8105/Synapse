<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151202175447 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('ALTER TABLE `synapse`.`org_academic_terms` 
        CHANGE COLUMN `start_date` `start_date` DATE NOT NULL ,
        CHANGE COLUMN `end_date` `end_date` DATE NOT NULL ;');
        $this->addSQL('ALTER TABLE `synapse`.`org_academic_year` 
        CHANGE COLUMN `start_date` `start_date` DATE NOT NULL ,
        CHANGE COLUMN `end_date` `end_date` DATE NOT NULL ;');
        $this->addSQL('DROP TABLE IF EXISTS `issue_calculated_students`;');
        $this->addSQL('DROP TABLE IF EXISTS `issue_staff_student_mapping`;');
        $this->addSQL('DROP TABLE IF EXISTS `issues_calculation_input`;');
        $this->addSQL('DROP TABLE IF EXISTS `issues_temp_calc_den`;');
        $this->addSQL('DROP TABLE IF EXISTS `issues_temp_calc_done`;');
        $this->addSQL('DROP TABLE IF EXISTS `issues_temp_calc_num`;');
        $this->addSQL('DROP TABLE IF EXISTS `issues_temp_calc_perm`;');
        $this->addSQL('DROP TABLE IF EXISTS `faculty_student_mapping`;');
        $this->addSQL('DROP TABLE IF EXISTS `staff_student_mapping`;');
        $this->addSQL('DROP PROCEDURE IF EXISTS `IssueCalcSet`;');
        $this->addSQL('DROP PROCEDURE IF EXISTS `IssueCalcDenominator`;');
        $this->addSQL('DROP PROCEDURE IF EXISTS `IssueCalcNumerator`;');
        $this->addSQL('DROP PROCEDURE IF EXISTS `IssueCalcStudentStaffMapping`;');
        $this->addSQL('DROP PROCEDURE IF EXISTS `IssueCalcPermissions`;');
        $this->addSQL('DROP PROCEDURE IF EXISTS `Issues_Calc`;');
        $this->addSQL('DROP PROCEDURE IF EXISTS `IssueCalcTemptTables`;');
        $this->addSQL('DROP PROCEDURE IF EXISTS `IssueCalculation`;');
        $this->addSQL('DROP TABLE IF EXISTS `synapse`.`org_top5_issues_calculated_values`;');
        $this->addSQL('DROP TABLE IF EXISTS `org_calc_flags_issues_faculty`;');
        $this->addSQL('truncate `issue_lang`;');
        $this->addSQL('truncate `issue_options`;');
        $this->addSQL('DELETE FROM `issue`;');
        $this->addSQL('ALTER TABLE `issue` AUTO_INCREMENT = 1;');

        $this->addSQL("INSERT INTO `issue` 
        VALUES (1,NULL,NULL,NULL,11,431,NULL,'2015-09-12 23:38:20','2015-10-17 17:39:03',NULL,NULL,NULL,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (2,NULL,NULL,NULL,12,NULL,NULL,'2015-09-13 00:25:58','2015-09-13 00:25:58','2015-09-13 00:36:09',NULL,NULL,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (3,NULL,NULL,NULL,11,287,NULL,'2015-09-13 00:28:12','2015-10-17 17:40:34',NULL,NULL,NULL,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (4,NULL,NULL,NULL,11,293,NULL,'2015-09-13 00:29:44','2015-10-17 17:41:06',NULL,NULL,NULL,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (5,NULL,NULL,NULL,11,232,NULL,'2015-09-13 00:37:00','2015-10-17 17:41:43',NULL,NULL,NULL,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (6,NULL,NULL,NULL,11,NULL,2,'2015-09-12 23:12:43','2015-09-12 23:12:43',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (7,NULL,NULL,NULL,11,NULL,3,'2015-09-12 23:13:08','2015-09-12 23:13:08',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (8,NULL,NULL,NULL,11,NULL,3,'2015-09-12 23:13:29','2015-09-12 23:13:29','2015-09-13 00:36:09',1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (9,NULL,NULL,NULL,11,NULL,5,'2015-09-12 23:14:03','2015-09-12 23:14:03',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (10,NULL,NULL,NULL,11,NULL,6,'2015-09-12 23:14:37','2015-09-12 23:14:37',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (11,NULL,NULL,NULL,11,NULL,7,'2015-09-12 23:15:48','2015-09-12 23:15:48',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (12,NULL,NULL,NULL,11,NULL,8,'2015-09-12 23:16:17','2015-09-12 23:16:17',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (13,NULL,NULL,NULL,11,NULL,9,'2015-09-12 23:16:48','2015-09-12 23:16:48',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (14,NULL,NULL,NULL,11,NULL,10,'2015-09-12 23:17:21','2015-09-12 23:17:21',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (15,NULL,NULL,NULL,11,NULL,11,'2015-09-12 23:17:49','2015-09-12 23:17:49',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (16,NULL,NULL,NULL,11,NULL,12,'2015-09-12 23:18:28','2015-09-12 23:18:28',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (17,NULL,NULL,NULL,11,NULL,13,'2015-09-12 23:19:29','2015-09-12 23:19:29',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (18,NULL,NULL,NULL,11,NULL,13,'2015-09-12 23:19:59','2015-09-12 23:19:59','2015-10-17 17:45:30',1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (19,NULL,NULL,NULL,11,NULL,14,'2015-09-12 23:20:31','2015-09-12 23:20:31',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (20,NULL,NULL,NULL,11,NULL,15,'2015-09-12 23:21:08','2015-09-12 23:21:08',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (21,NULL,NULL,NULL,11,NULL,16,'2015-09-12 23:21:40','2015-09-12 23:21:40',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (22,NULL,NULL,NULL,11,NULL,17,'2015-09-12 23:22:18','2015-09-12 23:22:18',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (23,NULL,NULL,NULL,11,NULL,18,'2015-09-12 23:22:47','2015-09-12 23:22:47',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (24,NULL,NULL,NULL,11,NULL,19,'2015-09-12 23:23:16','2015-09-12 23:23:16',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (25,NULL,NULL,NULL,11,NULL,20,'2015-09-12 23:23:45','2015-09-12 23:23:45',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (26,NULL,NULL,NULL,11,NULL,21,'2015-09-12 23:24:23','2015-09-12 23:24:23',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (27,NULL,NULL,NULL,11,NULL,22,'2015-09-12 23:24:46','2015-09-12 23:24:46',NULL,1.0000,3.9999,'2015-09-11',NULL,'dash-module-issue-courses-icon.png',NULL),
        (28,NULL,NULL,NULL,12,649,NULL,'2015-10-15 11:01:43','2015-10-15 11:02:35',NULL,NULL,NULL,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (29,NULL,NULL,NULL,12,474,NULL,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,NULL,NULL,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (30,NULL,NULL,NULL,12,505,NULL,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,NULL,NULL,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (31,NULL,NULL,NULL,12,511,NULL,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,NULL,NULL,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (32,NULL,NULL,NULL,13,863,NULL,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,NULL,NULL,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (33,NULL,NULL,NULL,13,688,NULL,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,NULL,NULL,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (34,NULL,NULL,NULL,13,719,NULL,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,NULL,NULL,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (35,NULL,NULL,NULL,13,725,NULL,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,NULL,NULL,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (36,NULL,NULL,NULL,14,1077,NULL,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,NULL,NULL,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (37,NULL,NULL,NULL,14,902,NULL,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,NULL,NULL,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (38,NULL,NULL,NULL,14,933,NULL,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,NULL,NULL,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (39,NULL,NULL,NULL,14,939,NULL,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,NULL,NULL,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (40,NULL,NULL,NULL,12,NULL,2,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (41,NULL,NULL,NULL,12,NULL,3,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (42,NULL,NULL,NULL,12,NULL,5,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (43,NULL,NULL,NULL,12,NULL,6,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (44,NULL,NULL,NULL,12,NULL,7,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (45,NULL,NULL,NULL,12,NULL,8,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (46,NULL,NULL,NULL,12,NULL,9,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (47,NULL,NULL,NULL,12,NULL,10,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (48,NULL,NULL,NULL,12,NULL,11,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (49,NULL,NULL,NULL,12,NULL,12,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (50,NULL,NULL,NULL,12,NULL,13,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (51,NULL,NULL,NULL,12,NULL,14,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (52,NULL,NULL,NULL,12,NULL,15,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (53,NULL,NULL,NULL,12,NULL,16,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (54,NULL,NULL,NULL,12,NULL,17,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (55,NULL,NULL,NULL,12,NULL,18,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (56,NULL,NULL,NULL,12,NULL,19,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (57,NULL,NULL,NULL,12,NULL,20,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (58,NULL,NULL,NULL,12,NULL,21,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (59,NULL,NULL,NULL,12,NULL,22,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (60,NULL,NULL,NULL,13,NULL,2,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (61,NULL,NULL,NULL,13,NULL,3,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (62,NULL,NULL,NULL,13,NULL,5,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (63,NULL,NULL,NULL,13,NULL,6,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (64,NULL,NULL,NULL,13,NULL,7,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (65,NULL,NULL,NULL,13,NULL,8,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (66,NULL,NULL,NULL,13,NULL,9,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (67,NULL,NULL,NULL,13,NULL,10,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (68,NULL,NULL,NULL,13,NULL,11,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (69,NULL,NULL,NULL,13,NULL,12,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (70,NULL,NULL,NULL,13,NULL,13,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (71,NULL,NULL,NULL,13,NULL,14,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (72,NULL,NULL,NULL,13,NULL,15,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (73,NULL,NULL,NULL,13,NULL,16,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (74,NULL,NULL,NULL,13,NULL,17,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (75,NULL,NULL,NULL,13,NULL,18,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (76,NULL,NULL,NULL,13,NULL,19,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (77,NULL,NULL,NULL,13,NULL,20,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (78,NULL,NULL,NULL,13,NULL,21,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (79,NULL,NULL,NULL,13,NULL,22,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (80,NULL,NULL,NULL,14,NULL,2,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (81,NULL,NULL,NULL,14,NULL,3,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (82,NULL,NULL,NULL,14,NULL,5,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (83,NULL,NULL,NULL,14,NULL,6,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (84,NULL,NULL,NULL,14,NULL,7,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (85,NULL,NULL,NULL,14,NULL,8,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (86,NULL,NULL,NULL,14,NULL,9,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (87,NULL,NULL,NULL,14,NULL,10,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (88,NULL,NULL,NULL,14,NULL,11,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (89,NULL,NULL,NULL,14,NULL,12,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (90,NULL,NULL,NULL,14,NULL,13,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (91,NULL,NULL,NULL,14,NULL,14,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (92,NULL,NULL,NULL,14,NULL,15,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (93,NULL,NULL,NULL,14,NULL,16,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (94,NULL,NULL,NULL,14,NULL,17,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (95,NULL,NULL,NULL,14,NULL,18,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (96,NULL,NULL,NULL,14,NULL,19,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (97,NULL,NULL,NULL,14,NULL,20,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (98,NULL,NULL,NULL,14,NULL,21,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL),
        (99,NULL,NULL,NULL,14,NULL,22,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,1.0000,3.9999,NULL,NULL,'dash-module-issue-courses-icon.png',NULL);");


        $this->addSQL("INSERT INTO `issue_lang` VALUES (1,NULL,NULL,NULL,1,1,'2015-09-12 23:38:20','2015-09-12 23:38:20',NULL,'Plan to study 5 hours or fewer a week'),
        (2,NULL,NULL,NULL,2,1,'2015-09-13 00:25:58','2015-09-13 00:25:58',NULL,'Struggling in at least 2 courses'),
        (3,NULL,NULL,NULL,3,1,'2015-09-13 00:28:12','2015-09-13 00:28:12',NULL,'Missed 2 or more classes'),
        (4,NULL,NULL,NULL,4,1,'2015-09-13 00:29:44','2015-09-13 00:29:44',NULL,'Not committed to continuing'),
        (5,NULL,NULL,NULL,5,1,'2015-09-13 00:37:00','2015-09-13 00:37:00',NULL,'Struggling in at least 2 courses'),
        (6,NULL,NULL,NULL,6,1,'2015-09-12 23:12:43','2015-09-12 23:12:43',NULL,'Low communication skills'),
        (7,NULL,NULL,NULL,7,1,'2015-09-12 23:13:08','2015-09-12 23:13:08',NULL,'Low analytical skills'),
        (8,NULL,NULL,NULL,8,1,'2015-09-12 23:13:29','2015-09-12 23:13:29',NULL,'Low self-discipline'),
        (9,NULL,NULL,NULL,9,1,'2015-09-12 23:14:03','2015-09-12 23:14:03',NULL,'Low time management'),
        (10,NULL,NULL,NULL,10,1,'2015-09-12 23:14:37','2015-09-12 23:14:37',NULL,'Not confident about finances'),
        (11,NULL,NULL,NULL,11,1,'2015-09-12 23:15:48','2015-09-12 23:15:48',NULL,'Low basic academic skills'),
        (12,NULL,NULL,NULL,12,1,'2015-09-12 23:16:17','2015-09-12 23:16:17',NULL,'Low advanced academic behaviors'),
        (13,NULL,NULL,NULL,13,1,'2015-09-12 23:16:48','2015-09-12 23:16:48',NULL,'Low academic self-efficacy'),
        (14,NULL,NULL,NULL,14,1,'2015-09-12 23:17:21','2015-09-12 23:17:21',NULL,'Low academic resiliency'),
        (15,NULL,NULL,NULL,15,1,'2015-09-12 23:17:49','2015-09-12 23:17:49',NULL,'Low peer connections'),
        (16,NULL,NULL,NULL,16,1,'2015-09-12 23:18:28','2015-09-12 23:18:28',NULL,'Homesick (separation)'),
        (17,NULL,NULL,NULL,17,1,'2015-09-12 23:19:29','2015-09-12 23:19:29',NULL,'Homesick (distressed)'),
        (18,NULL,NULL,NULL,18,1,'2015-09-12 23:19:59','2015-09-12 23:19:59',NULL,'Homesick (distressed)'),
        (19,NULL,NULL,NULL,19,1,'2015-09-12 23:20:32','2015-09-12 23:20:32',NULL,'Low academic integration'),
        (20,NULL,NULL,NULL,20,1,'2015-09-12 23:21:08','2015-09-12 23:21:08',NULL,'Low social integration'),
        (21,NULL,NULL,NULL,21,1,'2015-09-12 23:21:40','2015-09-12 23:21:40',NULL,'Low satisfaction with the institution'),
        (22,NULL,NULL,NULL,22,1,'2015-09-12 23:22:18','2015-09-12 23:22:18',NULL,'Low social aspects (on-campus living)'),
        (23,NULL,NULL,NULL,23,1,'2015-09-12 23:22:47','2015-09-12 23:22:47',NULL,'Low living environment (on-campus)'),
        (24,NULL,NULL,NULL,24,1,'2015-09-12 23:23:16','2015-09-12 23:23:16',NULL,'Low roommate relationships (on-campus)'),
        (25,NULL,NULL,NULL,25,1,'2015-09-12 23:23:45','2015-09-12 23:23:45',NULL,'Low living environment (off-campus)'),
        (26,NULL,NULL,NULL,26,1,'2015-09-12 23:24:23','2015-09-12 23:24:23',NULL,'Test Anxiety'),
        (27,NULL,NULL,NULL,27,1,'2015-09-12 23:24:46','2015-09-12 23:24:46',NULL,'Low advanced study skills'),
        (28,NULL,NULL,NULL,28,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Plan to study 5 hours or fewer a week'),
        (29,NULL,NULL,NULL,29,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Struggling in at least 2 courses'),
        (30,NULL,NULL,NULL,30,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Missed 2 or more classes'),
        (31,NULL,NULL,NULL,31,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Not committed to continuing'),
        (32,NULL,NULL,NULL,32,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Plan to study 5 hours or fewer a week'),
        (33,NULL,NULL,NULL,33,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Struggling in at least 2 courses'),
        (34,NULL,NULL,NULL,34,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Missed 2 or more classes'),
        (35,NULL,NULL,NULL,35,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Not committed to continuing'),
        (36,NULL,NULL,NULL,36,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Plan to study 5 hours or fewer a week'),
        (37,NULL,NULL,NULL,37,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Struggling in at least 2 courses'),
        (38,NULL,NULL,NULL,38,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Missed 2 or more classes'),
        (39,NULL,NULL,NULL,39,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Not committed to continuing'),
        (40,NULL,NULL,NULL,40,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low communication skills'),
        (41,NULL,NULL,NULL,41,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low analytical skills'),
        (42,NULL,NULL,NULL,42,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low time management'),
        (43,NULL,NULL,NULL,43,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Not confident about finances'),
        (44,NULL,NULL,NULL,44,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low basic academic skills'),
        (45,NULL,NULL,NULL,45,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low advanced academic behaviors'),
        (46,NULL,NULL,NULL,46,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low academic self-efficacy'),
        (47,NULL,NULL,NULL,47,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low academic resiliency'),
        (48,NULL,NULL,NULL,48,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low peer connections'),
        (49,NULL,NULL,NULL,49,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Homesick (separation)'),
        (50,NULL,NULL,NULL,50,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Homesick (distressed)'),
        (51,NULL,NULL,NULL,51,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low academic integration'),
        (52,NULL,NULL,NULL,52,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low social integration'),
        (53,NULL,NULL,NULL,53,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low satisfaction with the institution'),
        (54,NULL,NULL,NULL,54,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low social aspects (on-campus living)'),
        (55,NULL,NULL,NULL,55,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low living environment (on-campus)'),
        (56,NULL,NULL,NULL,56,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low roommate relationships (on-campus)'),
        (57,NULL,NULL,NULL,57,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low living environment (off-campus)'),
        (58,NULL,NULL,NULL,58,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Test Anxiety'),
        (59,NULL,NULL,NULL,59,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low advanced study skills'),
        (60,NULL,NULL,NULL,60,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low communication skills'),
        (61,NULL,NULL,NULL,61,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low analytical skills'),
        (62,NULL,NULL,NULL,62,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low time management'),
        (63,NULL,NULL,NULL,63,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Not confident about finances'),
        (64,NULL,NULL,NULL,64,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low basic academic skills'),
        (65,NULL,NULL,NULL,65,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low advanced academic behaviors'),
        (66,NULL,NULL,NULL,66,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low academic self-efficacy'),
        (67,NULL,NULL,NULL,67,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low academic resiliency'),
        (68,NULL,NULL,NULL,68,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low peer connections'),
        (69,NULL,NULL,NULL,69,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Homesick (separation)'),
        (70,NULL,NULL,NULL,70,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Homesick (distressed)'),
        (71,NULL,NULL,NULL,71,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low academic integration'),
        (72,NULL,NULL,NULL,72,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low social integration'),
        (73,NULL,NULL,NULL,73,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low satisfaction with the institution'),
        (74,NULL,NULL,NULL,74,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low social aspects (on-campus living)'),
        (75,NULL,NULL,NULL,75,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low living environment (on-campus)'),
        (76,NULL,NULL,NULL,76,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low roommate relationships (on-campus)'),
        (77,NULL,NULL,NULL,77,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low living environment (off-campus)'),
        (78,NULL,NULL,NULL,78,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Test Anxiety'),
        (79,NULL,NULL,NULL,79,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low advanced study skills'),
        (80,NULL,NULL,NULL,80,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low communication skills'),
        (81,NULL,NULL,NULL,81,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low analytical skills'),
        (82,NULL,NULL,NULL,82,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low time management'),
        (83,NULL,NULL,NULL,83,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Not confident about finances'),
        (84,NULL,NULL,NULL,84,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low basic academic skills'),
        (85,NULL,NULL,NULL,85,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low advanced academic behaviors'),
        (86,NULL,NULL,NULL,86,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low academic self-efficacy'),
        (87,NULL,NULL,NULL,87,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low academic resiliency'),
        (88,NULL,NULL,NULL,88,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low peer connections'),
        (89,NULL,NULL,NULL,89,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Homesick (separation)'),
        (90,NULL,NULL,NULL,90,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Homesick (distressed)'),
        (91,NULL,NULL,NULL,91,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low academic integration'),
        (92,NULL,NULL,NULL,92,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low social integration'),
        (93,NULL,NULL,NULL,93,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low satisfaction with the institution'),
        (94,NULL,NULL,NULL,94,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low social aspects (on-campus living)'),
        (95,NULL,NULL,NULL,95,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low living environment (on-campus)'),
        (96,NULL,NULL,NULL,96,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low roommate relationships (on-campus)'),
        (97,NULL,NULL,NULL,97,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low living environment (off-campus)'),
        (98,NULL,NULL,NULL,98,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Test Anxiety'),
        (99,NULL,NULL,NULL,99,1,'2015-11-01 10:00:00','2015-11-01 10:00:00',NULL,'Low advanced study skills');");

        $this->addSQL("INSERT INTO `synapse`.`issue_options` (`issue_id`, `ebi_question_options_id`, `created_at`, `modified_at`) 
        VALUES 
        ('1', (select id from ebi_question_options where external_id='1846460'), NOW(), NOW()),
        ('1', (select id from ebi_question_options where external_id='1846461'), NOW(), NOW()),
        ('3', (select id from ebi_question_options where external_id='1846506'), NOW(), NOW()),
        ('3', (select id from ebi_question_options where external_id='1846507'), NOW(), NOW()),
        ('3', (select id from ebi_question_options where external_id='1846508'), NOW(), NOW()),
        ('4', (select id from ebi_question_options where external_id='1863335'), NOW(), NOW()),
        ('4', (select id from ebi_question_options where external_id='1863336'), NOW(), NOW()),
        ('4', (select id from ebi_question_options where external_id='1863337'), NOW(), NOW()),
        ('5', (select id from ebi_question_options where external_id='1846445'), NOW(), NOW()),
        ('5', (select id from ebi_question_options where external_id='1846446'), NOW(), NOW()),
        ('5', (select id from ebi_question_options where external_id='1846447'), NOW(), NOW()),
        ('5', (select id from ebi_question_options where external_id='1846448'), NOW(), NOW()),
        ('5', (select id from ebi_question_options where external_id='1846449'), NOW(), NOW()),
        ('28', (select id from ebi_question_options where external_id='1847971'), NOW(), NOW()),
        ('28', (select id from ebi_question_options where external_id='1847972'), NOW(), NOW()),
        ('29', (select id from ebi_question_options where external_id='1847956'), NOW(), NOW()),
        ('29', (select id from ebi_question_options where external_id='1847957'), NOW(), NOW()),
        ('29', (select id from ebi_question_options where external_id='1847958'), NOW(), NOW()),
        ('29', (select id from ebi_question_options where external_id='1847959'), NOW(), NOW()),
        ('29', (select id from ebi_question_options where external_id='1847960'), NOW(), NOW()),
        ('30', (select id from ebi_question_options where external_id='1848017'), NOW(), NOW()),
        ('30', (select id from ebi_question_options where external_id='1848018'), NOW(), NOW()),
        ('30', (select id from ebi_question_options where external_id='1848019'), NOW(), NOW()),
        ('31', (select id from ebi_question_options where external_id='1862119'), NOW(), NOW()),
        ('31', (select id from ebi_question_options where external_id='1862120'), NOW(), NOW()),
        ('31', (select id from ebi_question_options where external_id='1862121'), NOW(), NOW()),
        ('32', (select id from ebi_question_options where external_id='1849482'), NOW(), NOW()),
        ('32', (select id from ebi_question_options where external_id='1849483'), NOW(), NOW()),
        ('33', (select id from ebi_question_options where external_id='1849467'), NOW(), NOW()),
        ('33', (select id from ebi_question_options where external_id='1849468'), NOW(), NOW()),
        ('33', (select id from ebi_question_options where external_id='1849469'), NOW(), NOW()),
        ('33', (select id from ebi_question_options where external_id='1849470'), NOW(), NOW()),
        ('33', (select id from ebi_question_options where external_id='1849471'), NOW(), NOW()),
        ('34', (select id from ebi_question_options where external_id='1849528'), NOW(), NOW()),
        ('34', (select id from ebi_question_options where external_id='1849529'), NOW(), NOW()),
        ('34', (select id from ebi_question_options where external_id='1849530'), NOW(), NOW()),
        ('35', (select id from ebi_question_options where external_id='1863943'), NOW(), NOW()),
        ('35', (select id from ebi_question_options where external_id='1863944'), NOW(), NOW()),
        ('35', (select id from ebi_question_options where external_id='1863945'), NOW(), NOW()),
        ('36', (select id from ebi_question_options where external_id='1850993'), NOW(), NOW()),
        ('36', (select id from ebi_question_options where external_id='1850994'), NOW(), NOW()),
        ('37', (select id from ebi_question_options where external_id='1850978'), NOW(), NOW()),
        ('37', (select id from ebi_question_options where external_id='1850979'), NOW(), NOW()),
        ('37', (select id from ebi_question_options where external_id='1850980'), NOW(), NOW()),
        ('37', (select id from ebi_question_options where external_id='1850981'), NOW(), NOW()),
        ('37', (select id from ebi_question_options where external_id='1850982'), NOW(), NOW()),
        ('38', (select id from ebi_question_options where external_id='1851039'), NOW(), NOW()),
        ('38', (select id from ebi_question_options where external_id='1851040'), NOW(), NOW()),
        ('38', (select id from ebi_question_options where external_id='1851041'), NOW(), NOW()),
        ('39', (select id from ebi_question_options where external_id='1862727'), NOW(), NOW()),
        ('39', (select id from ebi_question_options where external_id='1862728'), NOW(), NOW()),
        ('39', (select id from ebi_question_options where external_id='1862729'), NOW(), NOW());");


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
