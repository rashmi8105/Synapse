#--This view may perform poorly without the criteria push-down trick
CREATE OR REPLACE
    ALGORITHM = MERGE
    DEFINER = `synapsemaster`@`%`
    SQL SECURITY INVOKER
VIEW person_risk_level_calc AS
    SELECT
        PRC.org_id,
        PRC.person_id,
        PRC.risk_group_id,
        PRC.risk_model_id,
        PRC.RS_Numerator AS weighted_value,
        PRC.RS_Denominator AS maximum_weight_value,
        PRC.risk_score,
        RML.risk_level,
        RL.risk_text,
        RL.image_name,
        RL.color_hex
    FROM
        person_riskmodel_calc_view AS PRC
        LEFT JOIN risk_model_levels AS RML
            ON RML.risk_model_id = PRC.risk_model_id
               AND PRC.risk_score >= RML.min
               #--non-inclusive range below, so there are no gaps
               AND PRC.risk_score < RML.max
               AND RML.deleted_at IS NULL
        LEFT JOIN risk_level AS RL FORCE INDEX FOR JOIN (PRIMARY)
            ON RL.id = RML.risk_level
               AND RL.deleted_at IS NULL;