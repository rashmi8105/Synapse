<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class AddOrgCalcFlagRisk extends ContainerAwareJob
{

    public function run($args)
    {
        $modelId = $args['modelid'];
        $groupId = $args['groupid'];
        $organizationId = $args['orgid'];
        
        $orgCalcFlagRiskService = $this->getContainer()->get('org_calc_flags_risk__service');
        /**
         * Find Out All the Associated with given group 
         */
        $orgCalcFlagRiskService->addStudentsToRiskFlagCalculation($groupId,$organizationId);
    }
}
