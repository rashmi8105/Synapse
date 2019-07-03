<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-11368 Found Error is Soft Deletion Compliance, Fixing It
 */
class Version20171130202338 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("CREATE OR REPLACE
                                ALGORITHM = MERGE
                                DEFINER = `synapsemaster`@`%`
                                SQL SECURITY INVOKER
                            VIEW synapse.org_person_riskvariable_datum AS
                                SELECT
                                    rvintersect.org_id,
                                    rvintersect.person_id,
                                    rvintersect.risk_variable_id,
                                    COALESCE( #--The value will be sourced from (the related) one of the following:
                                        pem.metadata_value, #--EBI metadatum
                                        pom.metadata_value, #--ORG metadatum
                                        oqr.decimal_value, #--ORG survey response (decimal)
                                        oqr.char_value, #--ORG survey response (char)
                                        oqr.charmax_value, #--ORG survey response (char) - exception?
                                        sr.decimal_value, #--survey_response (decimal)
                                        sr.char_value, #--survey_response (char)
                                        sr.charmax_value, #--survey_response (char) - exception?
                                        pfc.mean_value		#--person_factor (pre-calculated decimal)
                                    ) AS source_value,
                                    COALESCE( #--The modified timestamp will be sourced from (the related) one of the following:
                                        pem.modified_at,
                                        pom.modified_at,
                                        oqr.modified_at,
                                        sr.modified_at,
                                        pfc.modified_at,
                                        pem.created_at,
                                        pom.created_at,
                                        oqr.created_at,
                                        sr.created_at,
                                        pfc.created_at
                                    ) AS modified_at,
                                    COALESCE( #--The modified timestamp will be sourced from (the related) one of the following:
                                        pem.created_at,
                                        pom.created_at,
                                        oqr.created_at,
                                        sr.created_at,
                                        pfc.created_at
                                    ) AS created_at,
                                    em.scope AS scope,
                                    COALESCE(pem.org_academic_year_id,
                                             pom.org_academic_year_id) AS org_academic_year_id,
                                    COALESCE(pem.org_academic_terms_id,
                                             pom.org_academic_periods_id) AS org_academic_terms_id
                                FROM
                                    synapse.org_person_riskvariable AS rvintersect
                                    LEFT JOIN synapse.risk_variable AS rv
                                        ON rv.id = rvintersect.risk_variable_id
                                           AND rv.deleted_at IS NULL
                                    LEFT JOIN ebi_metadata em
                                        ON em.id = rv.ebi_metadata_id
                                           AND em.deleted_at IS NULL
                                    #--Value sourced from an EBI profile metadatum
                                    LEFT JOIN synapse.person_ebi_metadata pem
                                        ON pem.person_id = rvintersect.person_id
                                           AND pem.ebi_metadata_id = em.id
                                           AND (pem.modified_at > rv.calculation_start_date
                                                OR rv.calculation_start_date IS NULL
                                                OR em.scope IN ('Y' , 'T'))
                                           AND (pem.modified_at < rv.calculation_end_date
                                                OR rv.calculation_end_date IS NULL
                                                OR em.scope IN ('Y' , 'T'))
                                           AND pem.deleted_at IS NULL
                                    #--Value sourced from an ORG profile metadatum
                                    LEFT JOIN org_metadata AS om
                                        ON om.organization_id = rvintersect.org_id
                                           AND om.id = rv.org_metadata_id
                                           AND om.deleted_at IS NULL
                                    LEFT JOIN synapse.person_org_metadata pom
                                        ON pom.org_metadata_id = om.id
                                           AND pom.person_id = rvintersect.person_id
                                           AND (pom.modified_at > rv.calculation_start_date
                                                OR rv.calculation_start_date IS NULL)
                                           AND (pom.modified_at < rv.calculation_end_date
                                                OR rv.calculation_end_date IS NULL)
                                           AND pom.deleted_at IS NULL
                                    #--Value sourced from a survey question (org_question_response)
                                    LEFT JOIN synapse.org_question oq
                                        ON oq.organization_id = rvintersect.org_id
                                           AND oq.id = rv.org_question_id
                                           AND oq.deleted_at IS NULL
                                    LEFT JOIN synapse.org_question_response oqr
                                        ON oqr.org_id = rvintersect.org_id
                                           AND oqr.person_id = rvintersect.person_id
                                           AND oqr.org_question_id = oq.id
                                           AND (oqr.modified_at > rv.calculation_start_date
                                                OR rv.calculation_start_date IS NULL)
                                           AND (oqr.modified_at < rv.calculation_end_date
                                                OR rv.calculation_end_date IS NULL)
                                           AND oqr.deleted_at IS NULL
                                    #--Value sourced from a survey question (survey_response)
                                    LEFT JOIN synapse.survey_questions sq
                                        ON sq.id = rv.survey_questions_id
                                           AND sq.deleted_at IS NULL
                                    LEFT JOIN synapse.survey_response sr
                                        ON sr.org_id = rvintersect.org_id
                                           AND sr.person_id = rvintersect.person_id
                                           AND sr.survey_questions_id = sq.id
                                           AND (sr.modified_at > rv.calculation_start_date
                                                OR rv.calculation_start_date IS NULL)
                                           AND (sr.modified_at < rv.calculation_end_date
                                                OR rv.calculation_end_date IS NULL)
                                           AND sr.deleted_at IS NULL
                                    #--Value sourced from person_factors
                                    LEFT JOIN synapse.factor factor
                                        ON factor.id = rv.factor_id
                                            AND factor.deleted_at IS NULL
                                    LEFT JOIN synapse.person_factor_calculated pfc
                                        ON pfc.organization_id = rvintersect.org_id
                                           AND pfc.person_id = rvintersect.person_id
                                           AND pfc.factor_id = factor.id
                                           AND pfc.survey_id = rv.survey_id
                                           AND (pfc.modified_at > rv.calculation_start_date
                                                OR rv.calculation_start_date IS NULL)
                                           AND (pfc.modified_at < rv.calculation_end_date
                                                OR  rv.calculation_end_date IS NULL)
                                           AND pfc.deleted_at IS NULL;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
