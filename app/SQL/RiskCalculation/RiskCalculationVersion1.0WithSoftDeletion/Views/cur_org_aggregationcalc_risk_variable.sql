CREATE OR REPLACE
    ALGORITHM = MERGE
    DEFINER = `synapsemaster`@`%`
    SQL SECURITY INVOKER
VIEW cur_org_aggregationcalc_risk_variable AS
    SELECT
        OPRV.org_id,
        OPRV.risk_group_id,
        OPRV.person_id,
        OPRV.risk_variable_id,
        OPRV.risk_model_id,
        OPRV.source,
        OPRV.variable_type,
        OPRV.weight,
        risk_score_aggregated_RV(OPRV.org_id,
                                 OPRV.person_id,
                                 OPRV.risk_variable_id,
                                 OPRV.calc_type,
                                 OPRV.calculation_start_date,
                                 OPRV.calculation_end_date) COLLATE utf8_unicode_ci AS calculated_value,
        OPRV.calc_type AS calc_type
    FROM
        org_person_riskvariable AS OPRV;