<?php
namespace Synapse\ReportsBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;


class SurveySnapshotReportJob extends ReportsJob
{

    /*
     * Section JSON Generators
     */

	public function run($args)
    {
		$reportRunningStatusId = $args['reportInstanceId'];				
		$personId = $args['userId'];
		$surveyId = $args['surveyId'];		
		$reportRunningDto = $args['reportRunningDto'];
		$reportRunningDto = unserialize($reportRunningDto);
		
		$reportService = $this->getContainer()->get('surveysnapshot_service');
		
		$reportService->generateReport( $reportRunningStatusId, $surveyId, $personId, $reportRunningDto);
    }
}