CREATE OR REPLACE
    ALGORITHM = MERGE
    DEFINER = `synapsemaster`@`%`
    SQL SECURITY INVOKER
VIEW org_calculated_risk_variables AS
    SELECT
        *
    FROM
        org_calculated_risk_variables_history;