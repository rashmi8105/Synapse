<?php
namespace Synapse\ReportsBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;

class StudentReport extends ReportsJob
{

    public function run($args)
    {
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $reportSec = $repositoryResolver->getRepository('SynapseReportsBundle:ReportSections');
        
        $reportRunningStatusRepo = $repositoryResolver->getRepository('SynapseReportsBundle:ReportsRunningStatus');
        $runningStatusObj = $reportRunningStatusRepo->findOneById($args['reportInstanceId']);
        $runningStatusObj->setStatus('IP');
        $reportRunningStatusRepo->update($runningStatusObj);
        $reportRunningStatusRepo->flush();
        $reportServiceObj = $this->getContainer()->get('reports_service'); 
        
        $reportRunningDto = unserialize($args['reportRunningDto']);
        $rawReportData = $args['reportData'];
        
        $loggedUserId = $args['userId'];
        $reportData = $reportServiceObj->getOurStudentsReport($reportRunningDto, $loggedUserId, $rawReportData);
        $reportData = json_encode($reportData);
        
        $runningStatusObj->setResponseJson($reportData);
        $runningStatusObj->setStatus('C');
        $reportRunningStatusRepo->update($runningStatusObj);
        $reportRunningStatusRepo->flush();
    }
}