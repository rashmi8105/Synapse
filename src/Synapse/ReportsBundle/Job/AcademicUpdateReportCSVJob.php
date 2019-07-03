<?php

namespace Synapse\ReportsBundle\Job;

use Synapse\JobBundle\Job\ContainerAwareQueueJob;
use Synapse\ReportsBundle\Service\Impl\AcademicUpdateReportService;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;


class AcademicUpdateReportCSVJob extends ContainerAwareQueueJob
{

    const JOB_KEY = 'AcademicUpdateReportCSVJob';

    //services
    /**
     * @var AcademicUpdateReportService
     */
    private $academicUpdateReportService;


    /**
     * AcademicUpdateReportCSVJob constructor.
     */
    public function __construct()
    {
        $this->queue = ReportsConstants::REPORTS_QUEUE;
        $this->setJobType(self::JOB_KEY);
        $this->setAction('csv_generation_failed');
        $this->setRecipientType('creator');
        $this->setEventType('academic_update_report');
        $this->setNotificationReason('An error occurred while generating academic update report csv');

    }

    /**
     * This will get executed from the run method in ContainerAwareQueueJob
     *
     * @param $args
     */
    public function executeJob($args)
    {
        $searchAttributes = $args['searchAttributes'];
        $selectedAttributesCSV = $args['selectedAttributesCSV'];
        $academicUpdatesSearchAttributes = $args['academicUpdatesSearchAttributes'];
        $organizationId = $args['organizationId'];
        $loggedInUserId = $args['loggedInUserId'];
        $currentAcademicYearId = $args['currentAcademicYear'];

        $this->academicUpdateReportService = $this->getContainer()->get(AcademicUpdateReportService::SERVICE_KEY);
        $this->academicUpdateReportService->generateReportCSV($searchAttributes, $selectedAttributesCSV, $academicUpdatesSearchAttributes, $organizationId, $loggedInUserId, $currentAcademicYearId);
    }

}