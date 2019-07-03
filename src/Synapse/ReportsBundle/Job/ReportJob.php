<?php
namespace Synapse\ReportsBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;


class ReportJob extends ReportsJob
{
    /**
     * Generically Instantiates a Job for a Report
     * REQUIRES $args to have 'reportInstanceId' and 'service' keys
     * REQUIRES generateReport function from Report Service container
     *
     * @param array $args
     */
    public function run($args)
    {

        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $reportInstanceId = $args['reportInstanceId'];

        $reportRunningStatusRepo = $repositoryResolver->getRepository('SynapseReportsBundle:ReportsRunningStatus');
        $runningStatusObj = $reportRunningStatusRepo->findOneById($reportInstanceId);
        $runningStatusObj->setStatus('IP');
        $reportRunningStatusRepo->update($runningStatusObj);
        $reportRunningStatusRepo->flush();
        $reportServiceObj = $this->getContainer()->get($args['service']);

        $reportRunningDto = unserialize($args['reportRunningDto']);

        $reportServiceObj->generateReport($reportInstanceId, $reportRunningDto);

    }
}