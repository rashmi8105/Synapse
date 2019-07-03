<?php
namespace Synapse\RiskBundle\Util\Constants;

class RiskErrorConstants
{

    /**
     * Risk Model Constants Goes here
     */
    const RISK_M_001 = "Values are overlapping";

    const RISK_M_002 = "Dates cannot be prior to today";

    const RISK_M_003 = "End date cannot be earlier than start date";

    /**
     * Message coming from entity
     */
    const RISK_M_004 = "Entity Validations";

    const RISK_M_005 = "Risk Model Not Found";

    const RISK_M_006 = "Risk model has begun to calculate so calculation start date cannot be changed";

    const RISK_M_007 = "Risk Model has begun to calculate so cutpoints cannot be changed";
    
    const RISK_M_008 = "min should not be greater than max";
    
    const RISK_M_009 = "Calculations on ModelID have ceased. Please select a different model or change the calculation end date.";
    
    const RISK_M_010 = "Selected model is not active. Make model active or select a different model.";
    
    const RISK_M_011 = "Combination of Campus, Risk Group, and Model already exists.";    

    const RISK_M_012 = "Missing a required field.";
    
    const RISK_M_013 = "Risk variable is already included in the model.";

    const RISK_M_015 = "Calculations have begun on model. No edits possible";
    
    const RISK_M_016 = "indicator doesnt exist in the system";
    
    const ERR_RISK_RG_001 = "min should not be greater than max";

    
    /**
     * Risk Grop Constansts
     */
    /**
     * 
     * Database related Error
     * 
     */
    const ERR_RISK_RG_002 = "Risk Group Not Found.";
    const ERR_RISK_RG_999 = "Something went wrong.. Please try again"; 
    const RISK_M_017 = "More than one Model assigned for ";
    
}