<?php
namespace Synapse\RiskBundle\Util\Constants;

class RiskModelConstants
{

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const RISK_MODEL_MASTER = 'SynapseRiskBundle:RiskModelMaster';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const RISK_MODEL_LEVELS = 'SynapseRiskBundle:RiskModelLevels';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const RISK_LEVELS = 'SynapseRiskBundle:RiskLevels';

    const RISK_AMAZON_URL = 'https://ebi-synapse-bucket.s3.amazonaws.com/risk-uploads/';

    const RISK_DIR = 'risk_uploads/';

    const AMAZONSECRET = 'amazon_s3.secret';

    const CAMPUSID = 'CampusID';

    const RISKGROUPID = 'RiskGroupID';

    const MODELID = 'ModelID';

    const RISKVARNAME = 'RiskVarName';

    const WEIGHT = 'Weight';

    const COMMANDS = 'Commands';

    const YMD = 'Y/m/d';

    const ORGID = 'OrgID';
}