<?php
namespace Synapse\JobBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\CoreBundle\Service\Impl\MapworksActionService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Service\Impl\JobService;

abstract class ContainerAwareQueueJob extends ContainerAwareJob
{
    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $eventType;

    /**
     * @var string
     */
    private $jobType;

    /**
     * @var string
     */
    private $notificationReason;

    /**
     * @var string
     */
    private $recipientType;

    // executeJob will be implemented in derived class.
    abstract public function executeJob($args);

    /**
     * Set action for notification
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Set event type for notification
     *
     * @param string $eventType
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;
    }

    /**
     * Set Job Type
     *
     * @param string $jobType
     */
    public function setJobType($jobType)
    {
        $this->jobType = $jobType;
    }

    /**
     * Set notification reason
     *
     * @param string $notificationReason
     */
    public function setNotificationReason($notificationReason)
    {
        $this->notificationReason = $notificationReason;
    }

    /**
     * Set recipient type
     *
     * @param string $recipientType
     */
    public function setRecipientType($recipientType)
    {
        $this->recipientType = $recipientType;
    }

    /**
     * @param $args
     */
    public function run($args)
    {
        $jobService = $this->getContainer()->get(JobService::SERVICE_KEY);
        $mapworksActionService = $this->getContainer()->get(MapworksActionService::SERVICE_KEY);

        $jobNumber = $args['jobNumber'];
        $personId = isset($args['personId']) ? $args['personId'] : '';
        $organizationId = $args['organizationId'];
        $facultyId = null;

        // This check is required for organization specific jobs, where the faculty id will be null.
        if ($this->jobType != SynapseConstant::ORG_CALENDAR_JOB) {
            $facultyId = $personId;
        }

        try {
            $jobService->updateJobStatus($organizationId, $this->jobType, SynapseConstant::JOB_STATUS_INPROGRESS, $jobNumber, $facultyId);
            $this->executeJob($args);
            $jobService->updateJobStatus($organizationId, $this->jobType, SynapseConstant::JOB_STATUS_SUCCESS, $jobNumber, $facultyId);
        } catch (\Exception $exception) {
            $jobService->updateJobStatus($organizationId, $this->jobType, SynapseConstant::JOB_STATUS_FAILURE, $jobNumber, $facultyId, $exception->getMessage());
            $tokenValues['$$event_id$$'] = $jobNumber;
            $mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, $this->action, $this->recipientType, $this->eventType, $personId, $this->notificationReason, NULL, NULL, $tokenValues);
        } finally {
            //This must be the last thing to happen in the job so that, even if there is a job failure, the next queued job for that person is picked up.
            $jobService->enqueueQueuedJobs($personId);
        }
    }
}

