<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151023140226 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
	$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

	$this->addSql('CREATE DATABASE IF NOT EXISTS etldata;');

	$this->addSql('CREATE TABLE IF NOT EXISTS `etldata`.`survey_response` (
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
  `response_type` enum(\'decimal\',\'char\',\'charmax\') COLLATE utf8_unicode_ci DEFAULT NULL,
  `decimal_value` decimal(9,2) DEFAULT NULL,
  `char_value` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `charmax_value` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modat` (`modified_at`),
  KEY `person_id` (`person_id`),
  KEY `idx_org_person_question` (`org_id`,`person_id`,`survey_id`,`survey_questions_id`,`modified_at`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');

	$this->addSql('CREATE TABLE  IF NOT EXISTS `etldata`.`org_question_response` (
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
  `response_type` enum(\'decimal\',\'char\',\'charmax\') COLLATE utf8_unicode_ci DEFAULT NULL,
  `decimal_value` decimal(9,2) DEFAULT NULL,
  `char_value` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `charmax_value` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modat` (`modified_at`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');


	$this->addSql('CREATE TABLE  IF NOT EXISTS `etldata`.`survey_ETL_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `synapse_survey_id` int(11) DEFAULT NULL,
  `WESS_SurvID` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;');

	$this->addSql('CREATE TABLE IF NOT EXISTS  `etldata`.`survey_ISQ_ETL_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `synapse_survey_id` int(11) DEFAULT NULL,
  `WESS_SurvID` int(11) DEFAULT NULL,
  `synapse_org_question_id` int(11) DEFAULT NULL,
  `WESS_OrderQuestionID` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `synsurvid` (`synapse_survey_id`,`WESS_SurvID`),
  KEY `wesssruvid` (`WESS_SurvID`,`synapse_survey_id`),
  KEY `synqid` (`synapse_org_question_id`,`WESS_OrderQuestionID`),
  KEY `wessqid` (`WESS_OrderQuestionID`,`synapse_org_question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;');


	$this->addSql('CREATE TABLE IF NOT EXISTS  `etldata`.`survey_question_ETL_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `synapse_survey_question_id` int(11) DEFAULT NULL,
  `WESS_QuestionID` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `synapse_survey_id` int(11) DEFAULT NULL,
  `WESS_SurvID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;');



        $this->addSql('CREATE TABLE IF NOT EXISTS `etldata`.`last_response_update`
                        (
                            id INT PRIMARY KEY AUTO_INCREMENT,
                            last_update_person_id INT,
                            last_update_ts DATETIME
                        );');

        $this->addSql('INSERT INTO etldata.last_response_update (last_update_ts) VALUES (\'2015-10-08 00:00:00\');');

    	$this->addSql('DROP PROCEDURE IF EXISTS `survey_data_transfer`; ');
        $this->addSql('DROP EVENT IF EXISTS `trigger_survey_data_transfer`;');

        $this->addSql('CREATE DEFINER=`synapsemaster`@`%` PROCEDURE survey_data_transfer() 
                        BEGIN 
                        INSERT IGNORE INTO synapse.survey_response 
                        (SELECT * FROM etldata.survey_response WHERE modified_at > (SELECT MAX(modified_at) FROM synapse.survey_response));

                        UPDATE synapse.survey_response sr
                        LEFT OUTER JOIN
                        etldata.survey_response ers ON 
                        (ers.person_id , ers.org_id, ers.survey_id, ers.survey_questions_id) = 
                        (sr.person_id , sr.org_id, sr.survey_id, sr.survey_questions_id) 
                        SET 
                            sr.decimal_value = ers.decimal_value,
                            sr.char_value = ers.char_value,
                            sr.charmax_value = ers.charmax_value, 
                            sr.modified_at = NOW()
                        WHERE
                            (sr.decimal_value <> ers.decimal_value
                                OR sr.char_value <> ers.char_value
                                OR sr.charmax_value <> ers.charmax_value)
                                AND sr.modified_at <= ers.modified_at
                                AND ers.modified_at > (SELECT MAX(last_update_ts) FROM etldata.last_response_update LIMIT 1);


                        UPDATE etldata.last_response_update SET last_update_ts = NOW();
                        END
                        ');



 $this->addSql('ALTER TABLE synapse.org_person_student_survey_link
                        ADD COLUMN `Has_Responses` enum(\'Yes\', \'No\') NOT NULL DEFAULT \'No\';');

 $this->addSql('ALTER TABLE synapse.org_person_student_survey_link
                        ADD COLUMN `receivesurvey` INT NOT NULL DEFAULT 1');

        $this->addSql('DROP PROCEDURE IF EXISTS `survey_data_transfer`');

        $this->addSql('CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `survey_data_transfer`()
                        BEGIN
                        INSERT IGNORE INTO synapse.survey_response
                        (SELECT * FROM etldata.survey_response WHERE modified_at > (SELECT MAX(modified_at) FROM synapse.survey_response));

                        UPDATE synapse.org_person_student_survey_link opssl
                        LEFT JOIN synapse.survey_response sr ON sr.person_id = opssl.person_id AND sr.survey_id = opssl.survey_id
                        SET opssl.Has_Responses = \'Yes\'
                        WHERE sr.id IS NOT NULL AND opssl.Has_Responses = \'No\';

                        UPDATE synapse.survey_response sr
                        LEFT OUTER JOIN
                        etldata.survey_response ers ON
                        (ers.person_id , ers.org_id, ers.survey_id, ers.survey_questions_id) =
                        (sr.person_id , sr.org_id, sr.survey_id, sr.survey_questions_id)
                        SET
                            sr.decimal_value = ers.decimal_value,
                            sr.char_value = ers.char_value,
                            sr.charmax_value = ers.charmax_value,
                            sr.modified_at = NOW()
                        WHERE
                            (sr.decimal_value <> ers.decimal_value
                                OR sr.char_value <> ers.char_value
                                OR sr.charmax_value <> ers.charmax_value)
                                AND sr.modified_at <= ers.modified_at
                                AND ers.modified_at > (SELECT MAX(last_update_ts) FROM etldata.last_response_update LIMIT 1);


                        UPDATE etldata.last_response_update SET last_update_ts = NOW();
                        END');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
