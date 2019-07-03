CREATE OR REPLACE
    ALGORITHM = MERGE
    DEFINER = `synapsemaster`@`%`
    SQL SECURITY INVOKER
VIEW person_riskmodel_calc_view AS
    SELECT
        orgm.org_id,
        rgph.person_id,
        orgm.risk_group_id,
        orgm.risk_model_id,
        RS_numerator(orgm.org_id, orgm.risk_group_id, rgph.person_id) AS RS_Numerator,
        RS_denominator(orgm.org_id, orgm.risk_group_id, rgph.person_id) AS RS_Denominator,
        (RS_numerator(orgm.org_id, orgm.risk_group_id, rgph.person_id) / RS_denominator(orgm.org_id, orgm.risk_group_id, rgph.person_id)) AS risk_score
    FROM
        risk_group_person_history AS rgph
        INNER JOIN person AS p
            ON p.id = rgph.person_id
        INNER JOIN org_risk_group_model orgm
            ON rgph.risk_group_id = orgm.risk_group_id
               AND orgm.org_id = p.organization_id
        INNER JOIN risk_model_master rmm
            ON orgm.risk_model_id = rmm.id
               AND NOW() BETWEEN rmm.calculation_start_date AND rmm.calculation_end_date
    WHERE
        risk_model_id IS NOT NULL
        AND orgm.deleted_at IS NULL
        AND p.deleted_at IS NULL
        AND rmm.deleted_at IS NULL;