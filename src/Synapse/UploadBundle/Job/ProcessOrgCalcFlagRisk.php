<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;

class ProcessOrgCalcFlagRisk extends ContainerAwareJob
{

    public function run($args)
    {
        $modelId = $args['modelid'];
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        
        $orgRiskGroupModel = $repositoryResolver->getRepository('SynapseRiskBundle:OrgRiskGroupModel');
        $resque = $this->getContainer()->get('bcc_resque.resque');
        /**
         * Get all Risk Groups mapped with model
         */
        $riskGroups = $orgRiskGroupModel->getRiskGroupByModel($modelId);
        
        /**
         * Iterate row and start the AddOrgCalcFlagRisk Job
         */
        if ($riskGroups) {
            foreach ($riskGroups as $riskGroup) {
                
                $riskCalcArray = [];
                $riskCalcArray['modelid'] = $modelId;
                $riskCalcArray['groupid'] = $riskGroup['risk_group_id'];
                $riskCalcArray['orgid'] = $riskGroup['org_id'];
                
                $createObject = 'Synapse\UploadBundle\Job\AddOrgCalcFlagRisk';
                $job = new $createObject();
                
                $job->args = $riskCalcArray;
                $resque->enqueue($job, true);
            }
        }
    }
}
