<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160217113400 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("CREATE TABLE IF NOT EXISTS etldata.`person_intent_to_leave` (
                          `person_id` int(11) NOT NULL,
                          `intent_to_leave` int(11) DEFAULT NULL,
                          `intent_to_leave_update_date` datetime DEFAULT NULL,
                          PRIMARY KEY (`person_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->addSql("ALTER EVENT Survey_Risk_Event DISABLE;");

        $this->addSql("DROP PROCEDURE IF EXISTS `Intent_Leave_Calc`");

        $this->addSql("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Intent_Leave_Calc`()
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
                                    END");

        $this->addSql("DROP PROCEDURE IF EXISTS `Intent_Leave_Calc_all`");

        $this->addSql("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Intent_Leave_Calc_all`()
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
                                    END");

        $this->addSql("CALL Intent_Leave_Calc_all();");

        $this->addSql("ALTER EVENT Survey_Risk_Event ENABLE;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
