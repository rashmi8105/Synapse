#--Intersection of org, person, and riskvariable
CREATE OR REPLACE
    ALGORITHM = MERGE
    DEFINER = `synapsemaster`@`%`
    SQL SECURITY INVOKER
VIEW synapse.org_person_riskvariable AS
    SELECT
        ocfr.org_id,
        rgph.person_id,
        rv.id AS risk_variable_id,
        rv.source,
        rv.variable_type,
        rv.calc_type,
        rgph.risk_group_id,
        rv.calculation_end_date,
        rv.calculation_start_date,
        rmm.id AS risk_model_id,
        rmw.weight,
        GREATEST(IFNULL(rgph.assignment_date, 0),
                 IFNULL(ocfr.modified_at, 0),
                 IFNULL(ocfr.created_at, 0),
                 IFNULL(orgm.modified_at, 0),
                 IFNULL(orgm.created_at, 0),
                 IFNULL(rmm.modified_at, 0),
                 IFNULL(rmm.created_at, 0),
                 IFNULL(rv.modified_at, 0),
                 IFNULL(rv.created_at, 0)) AS modified_at
    FROM
        synapse.risk_group_person_history rgph
        INNER JOIN synapse.org_calc_flags_risk ocfr
            ON rgph.person_id = ocfr.person_id
        INNER JOIN synapse.org_risk_group_model orgm
            ON rgph.risk_group_id = orgm.risk_group_id
               AND orgm.org_id = ocfr.org_id
        INNER JOIN synapse.risk_model_master rmm
            ON orgm.risk_model_id = rmm.id
               AND NOW() BETWEEN rmm.calculation_start_date AND rmm.calculation_end_date
        INNER JOIN synapse.risk_model_weights rmw
            ON rmw.risk_model_id = rmm.id
        INNER JOIN synapse.risk_variable rv
            ON rmw.risk_variable_id = rv.id
    WHERE
        orgm.deleted_at IS NULL
        AND ocfr.deleted_at IS NULL
        AND rmm.deleted_at IS NULL
        AND rv.deleted_at IS NULL;