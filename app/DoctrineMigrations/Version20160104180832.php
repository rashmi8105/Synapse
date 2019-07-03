<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160104180832 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSQL("CREATE OR REPLACE 
        ALGORITHM = MERGE
        DEFINER = `synapsemaster`@`%` 
        SQL SECURITY DEFINER
        VIEW `Issues_Factors` AS
            SELECT 
                    pfc.organization_id as org_id,
                    pfc.person_id as student_id,
                    pfc.survey_id,
                    iss.id AS issue_id,
                    opssl.cohort AS cohort,
                    pfc.factor_id AS factor_id,
                    ISFS.faculty_id AS faculty_id,
                    pfc.mean_value AS permitted_value,
                    pfc.modified_at
                 
            
            FROM
                org_faculty_student_permission_map ISFS
            INNER JOIN org_person_student_survey_link opssl On opssl.org_id = ISFS.org_id AND opssl.person_id = ISFS.student_id 
            INNER JOIN person_factor_calculated pfc ON ISFS.student_id = pfc.person_id
                AND ISFS.org_id = pfc.organization_id and opssl.survey_id = pfc.survey_id and pfc.deleted_at is null
            INNER JOIN issue AS iss ON iss.factor_id = pfc.factor_id AND iss.survey_id = pfc.survey_id and iss.deleted_at is null
            INNER JOIN wess_link as wl On wl.survey_id = pfc.survey_id and wl.org_id = pfc.organization_id and wl.cohort_code = opssl.cohort And wl.status = 'closed' 
            INNER JOIN datablock_questions AS dq On dq.factor_id = pfc.factor_id AND dq.deleted_at is null
            INNER JOIN org_permissionset_datablock AS opd
                ON opd.organization_id = pfc.organization_id 
                AND opd.datablock_id = dq.datablock_id
                AND opd.org_permissionset_id = ISFS.permissionset_id
                AND opd.deleted_at IS NULL
            WHERE 
                pfc.id = (
                    select id from person_factor_calculated as fc
                    where
                        fc.organization_id = pfc.organization_id 
                        AND fc.person_id = pfc.person_id 
                        AND fc.factor_id = pfc.factor_id 
                        AND fc.survey_id = pfc.survey_id 
                        AND fc.deleted_at is null
                        ORDER BY modified_at DESC LIMIT 1
                )
        ;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
