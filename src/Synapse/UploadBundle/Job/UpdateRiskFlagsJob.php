<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\UploadBundle\Service\Impl\OrgCalcFlagsRiskService;

class UpdateRiskFlagsJob extends ContainerAwareJob
{
    /**
     * @var OrgCalcFlagsRiskService
     */
    private $orgCalcFlagRiskService;

    public function __construct()
    {
        $this->queue = 'riskflagjob';
    }

    public function run($args)
    {
        $this->orgCalcFlagRiskService = $this->getContainer()->get('org_calc_flags_risk__service');
        $this->orgCalcFlagRiskService->updateStudentRiskFlags(explode(',', $args['studentsToUpdate']), $args['orgId']);
    }
}