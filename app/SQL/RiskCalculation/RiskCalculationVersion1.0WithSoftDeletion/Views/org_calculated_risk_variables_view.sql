#--This view may perform poorly without the criteria push-down trick
CREATE OR REPLACE
    ALGORITHM = MERGE
    SQL SECURITY INVOKER
VIEW org_calculated_risk_variables_view AS
    SELECT
        RV.org_id,
        RV.risk_group_id,
        RV.person_id,
        RV.risk_variable_id,
        RV.risk_model_id,
        RV.source,
        RV.variable_type,
        RV.weight,
        RV.calculated_value,
        COALESCE(rvr.bucket_value, rvc.bucket_value) AS bucket_value
    FROM cur_org_aggregationcalc_risk_variable AS RV
        #--For levels specified by ranges
        LEFT JOIN risk_variable_range rvr
            ON rvr.risk_variable_id = RV.risk_variable_id
               AND RV.calculated_value >= rvr.min
               #--non-inclusive range below, so there are no gaps
               AND RV.calculated_value < rvr.max
               AND RV.variable_type = 'continuous'
        -- rvr has no deleted_at columns
        #--For levels specified by categories
        LEFT JOIN risk_variable_category rvc
            ON rvc.risk_variable_id = RV.risk_variable_id
               AND (RV.calculated_value = rvc.option_value OR CAST(RV.calculated_value AS SIGNED) = rvc.option_value)
               AND RV.variable_type = 'categorical'
               AND rvc.deleted_at IS NULL
    WHERE
        rvr.risk_variable_id IS NOT NULL
        OR rvc.risk_variable_id IS NOT NULL;