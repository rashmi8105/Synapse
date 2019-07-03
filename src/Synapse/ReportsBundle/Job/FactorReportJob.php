<?php
namespace Synapse\ReportsBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;

class FactorReportJob extends ReportsJob
{

	public function run($args)
    {				
		$reportRunningStatusId = $args['reportInstanceId'];		
		$personId = $args['userId'];
		$surveyId = $args['surveyId'];
		$reportRunningDto = $args['reportRunningDto'];
		$reportRunningDto = unserialize($reportRunningDto);
		$factorReport = $this->getContainer()->get('factorreport_service');
		$factorReport->generateReport($reportRunningStatusId, $surveyId, $personId, $reportRunningDto);
    }
			
}		
		