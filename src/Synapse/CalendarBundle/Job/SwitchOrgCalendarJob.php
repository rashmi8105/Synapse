<?php
namespace Synapse\CalendarBundle\Job;

use Synapse\CalendarBundle\Service\Impl\CalendarFactoryService;
use Synapse\JobBundle\Job\ContainerAwareQueueJob;

class SwitchOrgCalendarJob extends ContainerAwareQueueJob
{
    const JOB_KEY = 'SwitchOrgCalendarJob';

    /**
     * @var CalendarFactoryService
     */
    private $calendarFactoryService;

    /**
     * SwitchOrgCalendarJob constructor.
     */
    public function __construct()
    {
        $this->queue = 'calendar';
        $this->setJobType(self::JOB_KEY);
        $this->setAction('organization_sync_failed');
        $this->setRecipientType('creator');
        $this->setEventType('calendar_sync');
        $this->setNotificationReason('A calendar sync error occurred');
    }

    /**
     * This function will be called to send a notification to all faculties
     * and change their calendar settings when the sync status is changed by the coordinator
     *
     * @param array $args
     * @return void
     */
    public function executeJob($args)
    {
        $this->calendarFactoryService = $this->getContainer()->get(CalendarFactoryService::SERVICE_KEY);

        $organizationId = $args['organizationId'];
        $event = (isset($args['event'])) ? $args['event'] : null;
        $type = $args['type'];
        $pcsRemove = $args['pcsRemove'];

        $this->calendarFactoryService->removeEventWithDisableCalendar($organizationId, $event, $type, $pcsRemove);
    }
}