<?php
namespace Synapse\CalendarBundle\Job;

use Synapse\CalendarBundle\Service\Impl\CronofyWrapperService;
use Synapse\JobBundle\Job\ContainerAwareQueueJob;

class RecurrentEventJob extends ContainerAwareQueueJob
{
    const JOB_KEY = 'RecurrentEventJob';

    /**
     * @var CronofyWrapperService
     */
    private $cronofyWrapperService;

    /**
     * RecurrentEventJob constructor.
     */
    public function __construct()
    {
        $this->queue = 'calendar';
        $this->setJobType(self::JOB_KEY);
        $this->setAction('event_sync_failed');
        $this->setRecipientType('creator');
        $this->setEventType('calendar_sync');
        $this->setNotificationReason('A calendar sync error occurred');
    }

    /**
     * To sync office hours series to external calendar.
     *
     * @param array $args
     * @return void
     */
    public function executeJob($args)
    {
        $this->cronofyWrapperService = $this->getContainer()->get(CronofyWrapperService::SERVICE_KEY);

        $officeHourSeriesId = $args['officeHourSeriesId'];
        $action = $args['action'];
        $personId = $args['personId'];
        $organizationId = $args['organizationId'];
        $currentTime = new \DateTime('now');
        $this->cronofyWrapperService->syncOfficeHourSeries($officeHourSeriesId, $organizationId, $personId, $action, $currentTime);
    }
}