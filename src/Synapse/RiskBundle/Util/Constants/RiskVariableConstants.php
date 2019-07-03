<?php
namespace Synapse\RiskBundle\Util\Constants;

class RiskVariableConstants
{

    const RISK_VARIABLE_TYPE_CONTINUOUS = 'continuous';

    const RISK_VARIABLE_TYPE_CATEGORICAL = 'categorical';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SURVEY_QUESTION = 'SynapseSurveyBundle:SurveyQuestions';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SURVEY_FACTOR = 'SynapseSurveyBundle:Factor';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_QUESTION = 'SynapseCoreBundle:OrgQuestion';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const RISK_VARIABLE_RANGE = 'SynapseRiskBundle:RiskVariableRange';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const RISK_VARIABLE_CATEGORY = 'SynapseRiskBundle:RiskVariableCategory';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBI_QUESTION = 'SynapseCoreBundle:EbiQuestion';

    const ERR_RISK_RV_001 = 'Variable is assigned to model';

    const ERR_RISK_RV_002 = 'Risk Variable Not Found';

    const ERR_RISK_RV_003 = 'Not a valid resouce type';

    const ERR_RISK_RV_004 = 'Invalid Source';

    const ERR_RISK_RV_005 = 'Question Not Found';

    const ERR_RISK_RV_006 = 'Profile Not Found';

    const ERR_RISK_RV_007 = 'Survey Question Not Found';

    const ERR_RISK_RV_008 = 'Survey Factor Not Found';

    const ERR_RISK_RV_009 = 'ISP Not Found';

    const ERR_RISK_RV_010 = 'ISQ Not Found';

    const RISK_DIR = 'risk_uploads/';

    const SOURCE_TYPE = 'source_type';

    const PROFILE = 'profile';

    const SURVEYQUESTION = 'surveyquestion';

    const SURVEYFACTOR = 'surveyfactor';

    const ISP = 'isp';

    const ISQ = 'isq';

    const QUESTIONBANK = 'questionbank';

    /**
     * Upload Template Constants
     */
    const RISK_VAR_ID = 'Id';

    const RISK_VAR_RISKVARNAME = 'RiskVarName';

    const RISK_VAR_RISKVARTYPE = 'RiskVarType';

    const RISK_VAR_CALCULATED = 'Calculated';

    const RISK_VAR_SOURCETYPE = 'SourceType';

    const RISK_VAR_CAMPUSID = 'CampusID';

    const RISK_VAR_SOURCEID = 'SourceID';

    const RISK_VAR_CALTYPE = 'CalcType';

    const RISK_VAR_CALMIN = 'CalMin';

    const RISK_VAR_CALMAX = 'CalMax';

    const BUCKET_VALUE = 'bucket_value';

    const INVALID_CALC_TYPE = 'Invalid Calc Type';

    const SURVEY_ID = 'survey_id';

    const ERR_RISK_001 = 'ERRRISK001';
}