<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-10035 Fixing Intent to Leave Transaction syntax and soft deletion
 */
class Version20171121174653 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("DROP PROCEDURE IF EXISTS `Intent_Leave_Calc`;");
        $this->addSql("CREATE DEFINER =`synapsemaster`@`%` PROCEDURE `Intent_Leave_Calc`()
                            BEGIN
                        
                                SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
                        
                                TRUNCATE etldata.`person_intent_to_leave`;
                        
                                INSERT IGNORE INTO etldata.`person_intent_to_leave`
                                    SELECT
                                        sr.person_id,
                                        itl.id,
                                        sr.modified_at
                                    FROM
                                        survey_response sr
                                        INNER JOIN survey_questions sq
                                            ON sr.survey_questions_id = sq.id
                                               AND sq.qnbr = 4
                                        INNER JOIN (SELECT
                                                        person_id,
                                                        MAX(SRin.modified_at) AS modified_at
                                                    FROM
                                                        survey_response AS SRin
                                                        INNER JOIN survey_questions SQin
                                                            ON SRin.survey_questions_id = SQin.id
                                                               AND SQin.qnbr = 4
                                                    WHERE
                                                        SRin.modified_at > NOW() - INTERVAL 1 DAY
                                                    GROUP BY
                                                        person_id) sm
                                            ON sr.person_id = sm.person_id
                                               AND sr.modified_at = sm.modified_at
                                        INNER JOIN intent_to_leave itl
                                            ON sr.decimal_value BETWEEN itl.min_value AND itl.max_value
                                    WHERE
                                        itl.deleted_at IS NULL
                                        AND sq.deleted_at IS NULL
                                        AND sr.deleted_at IS NULL;
                        
                                UPDATE person p
                                    INNER JOIN etldata.`person_intent_to_leave` pitl
                                        ON p.id = pitl.person_id
                                SET
                                    p.intent_to_leave             = pitl.intent_to_leave,
                                    p.intent_to_leave_update_date = pitl.intent_to_leave_update_date
                                WHERE
                                    p.deleted_at IS NULL;
                        
                                CALL `Intent_Leave_Null_Fixer`();
                            END");
        $this->addSql("DROP PROCEDURE IF EXISTS `Intent_Leave_Calc_all`;");
        $this->addSql("CREATE DEFINER =`synapsemaster`@`%` PROCEDURE `Intent_Leave_Calc_all`()
                            BEGIN
                        
                                SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
                        
                                TRUNCATE etldata.`person_intent_to_leave`;
                        
                                INSERT IGNORE INTO etldata.`person_intent_to_leave`
                                    SELECT
                                        sr.person_id,
                                        itl.id,
                                        sr.modified_at
                                    FROM
                                        survey_response sr
                                        INNER JOIN survey_questions sq
                                            ON sr.survey_questions_id = sq.id
                                               AND sq.qnbr = 4
                                        INNER JOIN (SELECT
                                                        person_id,
                                                        MAX(SRin.modified_at) AS modified_at
                                                    FROM
                                                        survey_response AS SRin
                                                        INNER JOIN survey_questions SQin
                                                            ON SRin.survey_questions_id = SQin.id
                                                               AND SQin.qnbr = 4
                                                    GROUP BY
                                                        person_id) sm
                                            ON sr.person_id = sm.person_id
                                               AND sr.modified_at = sm.modified_at
                                        INNER JOIN intent_to_leave itl
                                            ON sr.decimal_value BETWEEN itl.min_value AND itl.max_value
                                    WHERE
                                        itl.deleted_at IS NULL
                                        AND sq.deleted_at IS NULL
                                        AND sr.deleted_at IS NULL;
                        
                                UPDATE person p
                                    INNER JOIN etldata.`person_intent_to_leave` pitl
                                        ON p.id = pitl.person_id
                                SET
                                    p.intent_to_leave             = pitl.intent_to_leave,
                                    p.intent_to_leave_update_date = pitl.intent_to_leave_update_date
                                WHERE
                                    p.deleted_at IS NULL;
                        
                                CALL `Intent_Leave_Null_Fixer`();
                            END");

        $this->addSql("DROP PROCEDURE IF EXISTS `Intent_Leave_Null_Fixer`;");
        $this->addSql("CREATE DEFINER =`synapsemaster`@`%` PROCEDURE `Intent_Leave_Null_Fixer`()
                            BEGIN
                        
                                -- Since this is fixing nulls and this is desirable even if the record is deleted
                                -- Not checking soft deletion
                                WHILE (SELECT
                                           1
                                       FROM
                                           person p
                                           INNER JOIN org_person_student ops
                                               ON p.organization_id = ops.organization_id AND p.id = ops.person_id
                                       WHERE
                                           intent_to_leave IS NULL
                                       LIMIT 1) = 1
                                DO
                                    UPDATE
                                        person AS per
                                        INNER JOIN (SELECT
                                                        p.id AS person_id
                                                    FROM
                                                        person p
                                                        INNER JOIN org_person_student ops
                                                            ON p.organization_id = ops.organization_id
                                                               AND p.id = ops.person_id
                                                    WHERE
                                                        p.intent_to_leave IS NULL
                                                    ORDER BY p.id
                                                    LIMIT 1000) AS t
                                            ON per.id = t.person_id
                                    SET
                                        per.intent_to_leave = 5,
                                        per.intent_to_leave_update_date = CURRENT_TIMESTAMP();
                        
                                END WHILE;
                        
                     
                            END");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
