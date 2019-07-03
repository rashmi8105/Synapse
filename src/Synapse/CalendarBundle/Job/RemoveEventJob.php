<?php
namespace Synapse\CalendarBundle\Job;

use Synapse\CalendarBundle\Service\Impl\CalendarFactoryService;
use Synapse\JobBundle\Job\ContainerAwareQueueJob;

class RemoveEventJob extends ContainerAwareQueueJob
{
    const JOB_KEY = 'RemoveEventJob';

    /**
     * @var CalendarFactoryService
     */
    private $calendarFactoryService;

    /**
     * RemoveEventJob constructor.
     */
    public function __construct()
    {
        $this->queue = 'calendar';
        $this->setJobType(self::JOB_KEY);
        $this->setAction('desync_failed');
        $this->setRecipientType('creator');
        $this->setEventType('calendar_sync');
        $this->setNotificationReason('A calendar sync error occurred');
    }

    /**
     * To revoke cronofy access when faculty disables syncing.
     *
     * @param array $args
     * @return void
     */
    public function executeJob($args)
    {
        $this->calendarFactoryService = $this->getContainer()->get(CalendarFactoryService::SERVICE_KEY);

        $personId = (isset($args['personId'])) ? $args['personId'] : null;
        $organizationId = $args['organizationId'];
        $removeMafToPcs = (isset($args['removeMafToPcs'])) ? $args['removeMafToPcs'] : null;
        $syncStatus = $args['syncStatus'];
        $type = (isset($args['type'])) ? $args['type'] : '';
        $pcsRemove = (isset($args['pcsRemove'])) ? $args['pcsRemove'] : null;

        $this->calendarFactoryService->removeEvent($personId, $organizationId, $removeMafToPcs, $syncStatus, $type, $pcsRemove);
    }
}