<?php
/**
Author: Hai Deng
Function: ESPRJ-9344 for improve the predefined search performance at second level for orgId:128 University of Oklahoma.
 */
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160318105900 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $orgId = '$$orgId$$';
        $personIds = '$$personIds$$';
        $startDate = '$$startDate$$';
        $endDate = '$$endDate$$';
        $termsId = '$$termsId$$';

        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id, P.id AS student, P.firstname,P.lastname,P.risk_level,RL.risk_text,RL.image_name,IL.text AS intent_to_leave_text,IL.image_name AS intent_to_leave_image,ORGM.risk_model_id,P.last_activity,OPS.surveycohort AS student_cohort,(COUNT(DISTINCT (acl.id))) AS cnt,OPS.status,OPS.photo_url,pem.metadata_value AS class_level FROM person P JOIN org_person_student AS OPS ON OPS.organization_id = $orgId AND OPS.person_id = P.id AND OPS.deleted_at IS NULL LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_group_person_history RGPH ON RGPH.person_id = P.id LEFT JOIN org_risk_group_model ORGM ON ORGM.org_id = $orgId AND ORGM.risk_group_id = RGPH.risk_group_id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN activity_log acl ON (acl.person_id_student = P.id) LEFT JOIN person_ebi_metadata AS pem ON (pem.person_id = P.id AND pem.ebi_metadata_id IN (SELECT id FROM ebi_metadata WHERE meta_key = "ClassLevel")) WHERE P.organization_id = $orgId AND P.id IN ($personIds) AND EXISTS(SELECT OCS.id FROM org_course_student OCS JOIN org_courses OC ON OC.id = OCS.org_courses_id AND OC.deleted_at IS NULL AND OC.org_academic_terms_id IN ($termsId) WHERE OCS.person_id = P.id AND OCS.deleted_at IS NULL) AND EXISTS(SELECT id FROM academic_update AU WHERE AU.person_id_student = P.id AND AU.grade IN ("C", "D","F", "No Pass", "F/No Pass") AND AU.update_date BETWEEN "$startDate" AND "$endDate") GROUP BY P.id' WHERE  `query_key` = 'In-progress_Grade_Of_C_Or_Below';
CDATA;
        $this->addSql($query);

        /** Formatted Query --just for code review, the above compact one to save into DB due to space
        $query = "
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS
                    P.id,
                    P.id AS student,
                    P.firstname,
                    P.lastname,
                    P.risk_level,
                    RL.risk_text,
                    RL.image_name,
                    IL.text AS intent_to_leave_text,
                    IL.image_name AS intent_to_leave_image,
                    ORGM.risk_model_id,
                    P.last_activity,
                    OPS.surveycohort AS student_cohort,
                    (COUNT(DISTINCT (acl.id))) AS cnt,
                    OPS.status,
                    OPS.photo_url,
                    pem.metadata_value AS class_level
                FROM
                    person P
                        JOIN
                    org_person_student AS OPS ON OPS.organization_id = $orgId AND OPS.person_id = P.id AND OPS.deleted_at IS NULL
                        LEFT JOIN
                    risk_level AS RL ON P.risk_level = RL.id
                        LEFT JOIN
                    risk_group_person_history RGPH ON RGPH.person_id = P.id
                        LEFT JOIN
                    org_risk_group_model ORGM ON ORGM.org_id = $orgId AND ORGM.risk_group_id = RGPH.risk_group_id
                        LEFT JOIN
                    intent_to_leave AS IL ON P.intent_to_leave = IL.id
                        LEFT JOIN
                    activity_log acl ON (acl.person_id_student = P.id)
                        LEFT JOIN
                    person_ebi_metadata AS pem ON (pem.person_id = P.id
                        AND pem.ebi_metadata_id IN (SELECT
                            id
                        FROM
                            ebi_metadata
                        WHERE
                            meta_key = \"ClassLevel\"))
                WHERE
                        P.organization_id = $orgId
                        AND P.id IN ($personIds)
                        AND EXISTS(SELECT OCS.id FROM
                            org_course_student OCS
                            JOIN
                            org_courses OC ON OC.id = OCS.org_courses_id AND OC.deleted_at IS NULL AND OC.org_academic_terms_id IN ($termsId)
                                            WHERE OCS.person_id = P.id AND OCS.deleted_at IS NULL)
                        AND EXISTS(SELECT id FROM
                            academic_update AU WHERE AU.person_id_student = P.id AND AU.grade IN (\"C\", \"D\" ,\"F\", \"No Pass\", \"F/No Pass\")
                            AND AU.update_date BETWEEN \"$startDate\" AND \"$endDate\")
                GROUP BY P.id'
                WHERE  `query_key` = 'In-progress_Grade_Of_C_Or_Below';";

        $this->addSql($query);
         *
         * */
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
