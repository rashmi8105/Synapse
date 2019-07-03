<?php
namespace Synapse\ReportsBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\ReportsBundle\Service\Impl\CompletionReportService;

class CompletionReportJob extends ReportsJob
{

    /**
     * Job file to generate the completion report
     * $args['userId'] - FAculty who is generating the report
     * $args['reportRunningDto'] -  Contains the filters the faculty has selected for generating the report
     * $args['requestjson'] - Json that is being sent from the UI,before it has been converted to DTO.
     *
     * @param $args
     * @return void
     */
    public function run($args)
    {
        $personId = $args['userId'];
        $reportRunningDto = $args['reportRunningDto'];
        $reportRunningDto = unserialize($reportRunningDto);
        $rawData = $args['requestjson'];
        $completionReportService  = $this->getContainer()->get(CompletionReportService::SERVICE_KEY);
        $completionReportService->generateReport($personId, $reportRunningDto, $rawData);
    }

}		
		