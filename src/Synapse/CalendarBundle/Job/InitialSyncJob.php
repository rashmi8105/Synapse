<?php
namespace Synapse\CalendarBundle\Job;

use Synapse\CalendarBundle\Service\Impl\CalendarFactoryService;
use Synapse\JobBundle\Job\ContainerAwareQueueJob;

class InitialSyncJob extends ContainerAwareQueueJob
{
    const JOB_KEY = 'InitialSyncJob';

    /**
     * @var CalendarFactoryService
     */
    private $calendarFactoryService;

    /**
     * InitialSyncJob constructor.
     */
    public function __construct()
    {
        $this->queue = 'calendar';
        $this->setJobType(self::JOB_KEY);
        $this->setAction('initial_sync_failed');
        $this->setRecipientType('creator');
        $this->setEventType('calendar_sync');
        $this->setNotificationReason('A calendar sync error occurred');
    }

    /**
     * This will run when initial sync is called.
     *
     * @param array $args
     * @return void
     */
    public function executeJob($args)
    {
        $this->calendarFactoryService = $this->getContainer()->get(CalendarFactoryService::SERVICE_KEY);

        $personId = $args['personId'];
        $organizationId = $args['organizationId'];
        $loggedInPersonId = isset($args['loggedInPersonId']) ? $args['loggedInPersonId'] : $personId;
        $this->calendarFactoryService->initialSync($personId, $organizationId, $loggedInPersonId);
    }
}