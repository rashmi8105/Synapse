<?php
namespace Synapse\JobBundle\Service\Impl;

use BCC\ResqueBundle\ContainerAwareJob;
use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Entity\JobStatusDescription;
use Synapse\JobBundle\Entity\OrgPersonJobQueue;
use Synapse\JobBundle\EntityDto\JobStatusDTO;
use Synapse\JobBundle\Entity\OrgPersonJobStatus;
use Synapse\JobBundle\Exception\ResqueJobRunDeniedException;
use Synapse\JobBundle\Repository\JobStatusDescriptionRepository;
use Synapse\JobBundle\Repository\JobTypeBlockedMappingRepository;
use Synapse\JobBundle\Repository\JobTypeRepository;
use Synapse\JobBundle\Repository\OrgPersonJobQueueRepository;
use Synapse\JobBundle\Repository\OrgPersonJobStatusRepository;


/**
 * @DI\Service("job_service")
 */
class JobService extends AbstractService
{
    const SERVICE_KEY = 'job_service';

    //scaffolding
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Resque
     */
    private $resque;

    // Repository

    /**
     * @var JobStatusDescriptionRepository
     */
    private $jobStatusDescriptionRepository;

    /**
     * @var JobTypeBlockedMappingRepository
     */
    private $jobTypeBlockedMapping;

    /**
     * @var JobTypeRepository
     */
    private $jobTypeRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgPersonJobQueue
     */
    private $orgPersonJobQueueRepository;

    /**
     * @var OrgPersonJobStatusRepository
     */
    private $orgPersonJobStatusRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;


    /**
     * Job Service Constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger")
     *            })
     *
     * @param $repositoryResolver
     * @param $container
     * @param $logger
     */
    public function __construct($repositoryResolver, $container, $logger)
    {
        parent::__construct($repositoryResolver, $logger);

        //scaffolding
        $this->container = $container;
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        //Repository initialization
        $this->jobStatusDescriptionRepository = $this->repositoryResolver->getRepository(JobStatusDescriptionRepository::REPOSITORY_KEY);
        $this->jobTypeRepository = $this->repositoryResolver->getRepository(JobTypeRepository::REPOSITORY_KEY);
        $this->jobTypeBlockedMapping = $this->repositoryResolver->getRepository(JobTypeBlockedMappingRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgPersonJobStatusRepository = $this->repositoryResolver->getRepository(OrgPersonJobStatusRepository::REPOSITORY_KEY);
        $this->orgPersonJobQueueRepository = $this->repositoryResolver->getRepository(OrgPersonJobQueueRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }

    /**
     * Fetches the pending job actions for the requested job type. Will throw an exception if there are blocking jobs running.
     *
     * @param int $organizationId
     * @param string $jobType
     * @param int|null $personId
     * @param string $errorMessage
     * @param bool $personNullCheckRequired
     * @return array
     * @throws ResqueJobRunDeniedException
     */
    public function getUnBlockedPendingJobActions($organizationId, $jobType, $personId = null, $errorMessage = null, $personNullCheckRequired = true)
    {
        $pendingJobsActions = [];
        $jobTypeArray = [$jobType];
        $jobStatusToBeVerified = [SynapseConstant::JOB_STATUS_INPROGRESS, SynapseConstant::JOB_STATUS_QUEUED];

        // Check is any organization level job is in progress, in this case user will not be allowed to enqueue their jobs.
        $organizationLevelJobType = [SynapseConstant::ORG_CALENDAR_JOB];
        if (!$personNullCheckRequired) {
            $organizationLevelPendingJobs = $this->orgPersonJobStatusRepository->checkOrganizationPendingJobsByBlockMapping($organizationId, $organizationLevelJobType, $jobStatusToBeVerified, $personNullCheckRequired);
            if ($organizationLevelPendingJobs > 0) {
                throw new ResqueJobRunDeniedException($errorMessage);
            }
        }

        // Check any pending jobs for users
        $pendingJobsActions = $this->orgPersonJobStatusRepository->getJobActionForRequestedJob($organizationId, $jobTypeArray, $jobStatusToBeVerified, $personId);
        if (in_array('blocked', $pendingJobsActions)) {
            throw new ResqueJobRunDeniedException($errorMessage);
        }
        return $pendingJobsActions;
    }

    /**
     * Adds the requested job to the job queue
     *
     * @param int $organizationId
     * @param string $requestedJobType
     * @param ContainerAwareJob $requestedJob
     * @param int|null $personId
     * @param string|null $errorMessage
     * @return boolean
     * @throws SynapseValidationException
     */
    public function addJobToQueue($organizationId, $requestedJobType, $requestedJob, $personId = null, $errorMessage = null)
    {
        //TODO:: Change status 'Q' in org_person_job_status to something other than 'Q' so as to avoid cognitive dissonance problems with the org_person_job_queue table.
        $enqueuedJobStatus = $this->getUnBlockedPendingJobActions($organizationId, $requestedJobType, $personId, $errorMessage);

        $initializationFailedErrorMessage = "There was a problem initializing your job";

        $person = null;
        // Find Person Object
        if ($personId) {
            $person = $this->personRepository->find($personId, new SynapseValidationException($initializationFailedErrorMessage));
        }

        // Find Organization Object
        $organization = $this->organizationRepository->find($organizationId, new SynapseValidationException($initializationFailedErrorMessage));

        // Get Job Status Description
        $jobStatus = $this->jobStatusDescriptionRepository->findOneBy(['jobStatusDescription' => SynapseConstant::JOB_STATUS_QUEUED], new SynapseValidationException($initializationFailedErrorMessage));

        // Get Job Type
        $requestedJobType = $this->jobTypeRepository->findOneBy(['jobType' => $requestedJobType], new SynapseValidationException($initializationFailedErrorMessage));

        if (in_array('queued', $enqueuedJobStatus) && $personId) {
            $orgPersonJobQueue = new OrgPersonJobQueue();
            $orgPersonJobQueue->setOrganization($organization);
            $orgPersonJobQueue->setPerson($person);
            $orgPersonJobQueue->setJobType($requestedJobType);
            $orgPersonJobQueue->setJobQueuedInfo(serialize($requestedJob));
            $orgPersonJobQueue->setQueuedStatus(0);
            $this->orgPersonJobQueueRepository->persist($orgPersonJobQueue);
        } else {
            $jobNumber = $requestedJob->args['jobNumber'];
            $this->createOrgPersonJobStatus($organization, $jobStatus, $requestedJobType, $jobNumber, $person);
            $this->resque->enqueue($requestedJob, true);
        }
        return true;
    }

    /**
     * Update job status.
     *
     * @param int $organizationId
     * @param string $jobType
     * @param string $jobStatus
     * @param string $jobId
     * @param int|null $personId
     * @param string|null $failureMessage
     * @return boolean
     * @throws ResqueJobRunDeniedException
     */
    public function updateJobStatus($organizationId, $jobType, $jobStatus, $jobId, $personId = null, $failureMessage = null)
    {
        $jobTypeObject = $this->jobTypeRepository->findOneBy(['jobType' => $jobType]);
        if (empty($jobTypeObject)) {
            throw new ResqueJobRunDeniedException("There was an error fetching the job type.");
        }
        $jobTypeId = $jobTypeObject->getId();

        $parameters = [
            'organization' => $organizationId,
            'jobType' => $jobTypeId,
            'jobId' => $jobId
        ];
        if ($personId) {
            $parameters['person'] = $personId;
        }

        $orgPersonJobStatus = $this->orgPersonJobStatusRepository->findOneBy($parameters);
        if ($orgPersonJobStatus) {
            $jobStatusDescriptionObject = $this->jobStatusDescriptionRepository->findOneBy(['jobStatusDescription' => $jobStatus]);
            if (empty($jobStatusDescriptionObject)) {
                throw new ResqueJobRunDeniedException("There was an error fetching the job status.");
            }
            $orgPersonJobStatus->setJobStatus($jobStatusDescriptionObject);
            $orgPersonJobStatus->setFailureDescription($failureMessage);
            $this->orgPersonJobStatusRepository->flush();
        } else {
            throw new ResqueJobRunDeniedException(SynapseConstant::RESQUE_NO_JOB_FOUND);
        }
        return true;
    }

    /**
     * Get job status for organization/person.
     *
     * @param int $organizationId
     * @param null|int $personId
     * @param bool $errorFlag - To identify whether the execution should be stopped by throwing an exception or to be continued with return value.
     * @param string $errorMessage
     * @return JobStatusDTO
     * @throws ResqueJobRunDeniedException
     */
    public function getJobStatus($organizationId, $personId = null, $errorFlag = true, $errorMessage = '')
    {
        // Check any pending job for user/organization
        $jobStatusToBeVerified = [SynapseConstant::JOB_STATUS_INPROGRESS, SynapseConstant::JOB_STATUS_QUEUED];
        $isAnyPendingJobs = $this->orgPersonJobStatusRepository->checkPendingJobsByRole($organizationId, $personId, $jobStatusToBeVerified);

        // If any job is pending (queued or in progress) then the status will be In Progress. If Success or Failure then there is no pending job, so that will be considered as completed.
        $jobStatus = SynapseConstant::JOB_STATUS_COMPLETED;
        if ($isAnyPendingJobs) {
            if ($errorFlag) {
                throw new ResqueJobRunDeniedException($errorMessage);
            }
            $jobStatus = SynapseConstant::JOB_STATUS_INPROGRESS;
        }

        $jobStatusDTO = new JobStatusDTO();
        $jobStatusDTO->setPersonId($personId);
        $jobStatusDTO->setOrganizationId($organizationId);
        $jobStatusDTO->setJobStatus($jobStatus);
        return $jobStatusDTO;
    }

    /**
     * Creates an org_person_job_status object, and populates it with the specified values.
     *
     * @param Organization $organization
     * @param JobStatusDescription $jobStatus
     * @param $jobType $jobType
     * @param int $jobNumber
     * @param Person|null $person
     * @return OrgPersonJobStatus
     */
    public function createOrgPersonJobStatus($organization, $jobStatus, $jobType, $jobNumber, $person = null)
    {
        $orgPersonJobStatus = new OrgPersonJobStatus();
        $orgPersonJobStatus->setPerson($person);
        $orgPersonJobStatus->setOrganization($organization);
        $orgPersonJobStatus->setJobStatus($jobStatus);
        $orgPersonJobStatus->setJobType($jobType);
        $orgPersonJobStatus->setJobId($jobNumber);
        $this->orgPersonJobStatusRepository->persist($orgPersonJobStatus);
        return $orgPersonJobStatus;
    }

    /**
     * Enqueue the job
     *
     * @param int $personId
     * @return OrgPersonJobQueue
     */
    public function enqueueQueuedJobs($personId)
    {
        $jobToBeEnqueued = $this->orgPersonJobQueueRepository->findOneBy([
            'person' => $personId,
            'queuedStatus' => 0
        ], [
            'createdAt' => 'ASC'
        ]);
        if ($jobToBeEnqueued) {
            $person = $jobToBeEnqueued->getPerson();
            $organization = $jobToBeEnqueued->getPerson()->getOrganization();
            $jobToBeQueued = unserialize($jobToBeEnqueued->getJobQueuedInfo());
            $jobNumber = $jobToBeQueued->args['jobNumber'];
            $jobStatus = $this->jobStatusDescriptionRepository->findOneBy(['jobStatusDescription' => SynapseConstant::JOB_STATUS_QUEUED]);
            $this->createOrgPersonJobStatus($organization, $jobStatus, $jobToBeEnqueued->getJobType(), $jobNumber, $person);
            $this->resque->enqueue($jobToBeQueued, true);
            $jobToBeEnqueued->setQueuedStatus(1);
            $this->orgPersonJobQueueRepository->flush();
        }
        return $jobToBeEnqueued;
    }
}