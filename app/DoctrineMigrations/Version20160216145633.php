<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Fixing Intent to Leave procedure so that it will use a student's most recent answer to the intent to leave question.
 * The version of the procedure that this replaces only got the most recent value with the same survey_questions_id,
 * but since these are unique per survey, it was only using the value for the student's first survey.  I have removed
 * the part that gets the most recent value for the same survey_questions_id, as there should only be one record per
 * survey_questions_id per student (there is a unique index on it).
 */
class Version20160216145633 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER EVENT Survey_Risk_Event DISABLE;");

        $this->addSql("DROP PROCEDURE IF EXISTS `Intent_Leave_Calc`");

        $this->addSql("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Intent_Leave_Calc`()
                        BEGIN

                            SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

                            UPDATE person p
                            INNER JOIN
                            (
                               SELECT sr.person_id, sr.decimal_value, sr.org_id, sr.modified_at
                               FROM survey_response sr
                               INNER JOIN survey_questions sq ON sr.survey_questions_id = sq.id
                               WHERE sq.qnbr = 4
                               ORDER BY sr.modified_at DESC
                               LIMIT 1
                            ) AS person_intent_to_leave_value
                                ON p.id = person_intent_to_leave_value.person_id
                            INNER JOIN org_person_student ops
                                ON ops.person_id = p.id
                                AND ops.organization_id = person_intent_to_leave_value.org_id
                            INNER JOIN intent_to_leave itl
                                ON person_intent_to_leave_value.decimal_value BETWEEN itl.min_value AND itl.max_value
                            SET p.intent_to_leave = itl.id, p.intent_to_leave_update_date = person_intent_to_leave_value.modified_at;

                            Call `Intent_Leave_Null_Fixer`();

                        END");

        $this->addSql("ALTER EVENT Survey_Risk_Event ENABLE;");

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
