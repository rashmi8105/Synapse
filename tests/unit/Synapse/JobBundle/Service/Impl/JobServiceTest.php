<?php

use Synapse\CalendarBundle\Job\InitialSyncJob;
use Synapse\CalendarBundle\Job\RemoveEventJob;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Entity\OrgPersonJobQueue;
use Synapse\JobBundle\Entity\JobStatusDescription;
use Synapse\JobBundle\Entity\JobType;
use Synapse\JobBundle\Entity\OrgPersonJobStatus;
use Synapse\JobBundle\EntityDto\JobStatusDTO;
use Synapse\JobBundle\Exception\ResqueJobRunDeniedException;
use Synapse\JobBundle\Repository\JobStatusDescriptionRepository;
use Synapse\JobBundle\Repository\JobTypeRepository;
use Synapse\JobBundle\Repository\OrgPersonJobStatusRepository;
use Synapse\JobBundle\Repository\OrgPersonJobQueueRepository;
use Synapse\JobBundle\Service\Impl\JobService;



class JobServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testGetUnBlockedPendingJobActions()
    {
        $this->specify("Test unblocked pending jobs", function ($organizationId, $personId, $jobTypeId, $personJobStatus, $pendingOrgJobs, $pendingJobs, $expectedResults) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockOrgPersonJobStatusRepository = $this->getMock('OrgPersonJobStatus', [
                'checkPendingJobsByJobType', 'getJobActionForRequestedJob', 'checkOrganizationPendingJobsByBlockMapping'
            ]);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrgPersonJobStatusRepository::REPOSITORY_KEY, $mockOrgPersonJobStatusRepository]
                ]);
            $mockOrgPersonJobStatusRepository->method('checkPendingJobsByJobType')->willReturn($personJobStatus);
            $mockOrgPersonJobStatusRepository->method('getJobActionForRequestedJob')->willReturn($pendingJobs);
            $errorMessage = SynapseConstant::RESQUE_JOB_CALENDAR_ERROR;
            if ($pendingOrgJobs) {
                $mockOrgPersonJobStatusRepository->method('checkOrganizationPendingJobsByBlockMapping')->willReturn($pendingOrgJobs);
                $errorMessage = SynapseConstant::ORG_PENDING_JOB_ERROR;
            }
            $jobService = new JobService($mockRepositoryResolver, $mockContainer, $mockLogger);

            try {
                $unblockedPendingJobs = $jobService->getUnBlockedPendingJobActions($organizationId, $jobTypeId, $personId, $errorMessage);
                $this->assertEquals($expectedResults, $unblockedPendingJobs);
            } catch (ResqueJobRunDeniedException $resqueException) {
                $this->assertEquals($expectedResults, $resqueException->getUserMessage());
            }

        },
            ['examples' =>
                [
                    // Initial Sync Job is In progress - will throw exception
                    [4891668, 203, 1, 1, 0, ['blocked', 'queued'], SynapseConstant::RESQUE_JOB_CALENDAR_ERROR],
                    // Initial Sync Job Completed, but blocked by few dependant jobs. - will throw exception
                    [4891668, 203, 1, 0, 0, ['blocked', 'queued'], SynapseConstant::RESQUE_JOB_CALENDAR_ERROR],
                    // Remove Event job completed, but blocked by dependant job.
                    [897878, 213, 2, 0, 0, ['blocked', 'queued'], SynapseConstant::RESQUE_JOB_CALENDAR_ERROR],
                    // Remove Event Job is In progress will throw exception
                    [4901835, 203, 2, 1, 0, ['blocked', 'queued'], SynapseConstant::RESQUE_JOB_CALENDAR_ERROR],
                    // Initial Sync is completed and no more pending jobs.
                    [4901835, 203, 1, 0, 0, ['queued'], ['queued']],
                    // Remove Event job is completed and no more pending jobs.
                    [4901835, 203, 2, 0, 0, ['queued'], ['queued']],
                    // Invalid Organization Id will throw an exception
                    [897878, 'invalidOrg', 1, 0, 0, ['blocked', 'queued'], SynapseConstant::RESQUE_JOB_CALENDAR_ERROR],
                    // Test with no organization id will throw an exception
                    [897878, '', 4, 1, 0, ['blocked', 'queued'], SynapseConstant::RESQUE_JOB_CALENDAR_ERROR],
                    // Invalid person id
                    ['invalidPerson', 213, 1, 0, 0, ['blocked'], SynapseConstant::RESQUE_JOB_CALENDAR_ERROR],
                    // No person id is passed will throw an exception.
                    ['', 213, 4, 1, 0, ['blocked'], SynapseConstant::RESQUE_JOB_CALENDAR_ERROR],
                    // Invalid Job type will throw an exception
                    [4901835, 203, 'invalidJobType', 1, 0, ['blocked', 'queued'], SynapseConstant::RESQUE_JOB_CALENDAR_ERROR],
                    // Organization level job is pending
                    [null, 203, 5, 1, 1, ['queued', 'blocked'], SynapseConstant::ORG_PENDING_JOB_ERROR]
                ]
            ]);

    }

    public function testAddJobToQueue()
    {
        $this->specify("Test a job to be added in a queue", function ($organizationId, $personId, $jobType, $job, $isPersonExists, $isOrganizationExists, $isJobTypeExist, $isJobStatusExist, $expectedResults) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockJobTypeRepository = $this->getMock('JobType', ['findOneBy']);
            $mockOrgPersonJobStatusRepository = $this->getMock('OrgPersonJobStatusRepository', [
                'getJobActionForRequestedJob',
                'checkPendingJobsByJobType',
                'persist',
                'checkOrganizationPendingJobsByBlockMapping',
                'getJobsByStatus'
            ]);
            $mockPersonRepository = $this->getMock('Person', ['find']);
            $mockOrganizationRepository = $this->getMock('Organization', ['find']);
            $mockJobStatusDescriptionRepository = $this->getMock('JobStatusDescription', ['findOneBy']);
            $mockOrgPersonJobStatusRepository->method('getJobActionForRequestedJob')->willReturn([]);
            $mockResque = $this->getMock('resque', ['enqueue']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [SynapseConstant::RESQUE_CLASS_KEY, $mockResque]
                ]);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [JobTypeRepository::REPOSITORY_KEY, $mockJobTypeRepository],
                    [OrgPersonJobStatusRepository::REPOSITORY_KEY, $mockOrgPersonJobStatusRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [JobStatusDescriptionRepository::REPOSITORY_KEY, $mockJobStatusDescriptionRepository]
                ]);
            if ($isPersonExists) {
                $person = new Person();
                $mockPersonRepository->method('find')->willReturn($person);
            }

            if ($isOrganizationExists) {
                $organization = new Organization();
                $mockOrganizationRepository->method('find')->willReturn($organization);
            }

            if ($isJobTypeExist) {
                $jobType = new JobType();
                $mockJobTypeRepository->method("findOneBy")->willReturn($jobType);
            }
            if ($isJobStatusExist) {
                $jobStatus = new JobStatusDescription();
                $mockJobStatusDescriptionRepository->method("findOneBy")->willReturn($jobStatus);
            } else {
                $mockJobStatusDescriptionRepository->method("findOneBy")->willReturn($isJobStatusExist);
            }

            $jobNumber = uniqid();
            $job->args = array(
                'jobNumber' => $jobNumber,
                'personId' => $personId,
                'orgId' => $organizationId,
            );
            try {
                $jobService = new JobService($mockRepositoryResolver, $mockContainer, $mockLogger);
                $addJobToQueue = $jobService->addJobToQueue($organizationId, $jobType, $job, $personId);
                $this->assertEquals(true, $addJobToQueue);
            } catch (SynapseException $synapseException) {
                $this->assertEquals($expectedResults, $synapseException->getMessage());
            }

        },
            ['examples' =>
                [
                    // Add a job with invalid person id - throw an exception
                    [
                        203,
                        'invalidPerson',
                        'InitialSyncJob',
                        new InitialSyncJob(),
                        false,
                        true,
                        true,
                        true,
                        "There was a problem initializing your job"
                    ],
                    // Enqueue RemoveEventJob when there is no pending jobs - will be enqueued.
                    [
                        203,
                        4891668,
                        'RemoveEventJob',
                        new RemoveEventJob(),
                        true,
                        true,
                        true,
                        true,
                        true
                    ],
                    // Add a job to invalid organization - throw the exception.
                    [
                        'invalidOrganization',
                        4891668,
                        'InitialSyncJob',
                        new InitialSyncJob(),
                        true,
                        false,
                        true,
                        true,
                        "There was a problem initializing your job"
                    ],
                    // Passing empty value in person id will throw an exception
                    [
                        203,
                        '',
                        'InitialSyncJob',
                        new InitialSyncJob(),
                        false,
                        true,
                        true,
                        true,
                        "There was a problem initializing your job"
                    ],
                    // Passing invalid job type should throw an exception.
                    [
                        203,
                        4891668,
                        'invalidJobType',
                        new InitialSyncJob(),
                        false,
                        true,
                        false,
                        true,
                        "There was a problem initializing your job"
                    ],
                    // Enqueue RemoveEventJob when there is no pending jobs - will be enqueued.
                    [
                        203,
                        4891668,
                        'InitialSyncJob',
                        new InitialSyncJob(),
                        true,
                        true,
                        true,
                        true,
                        true
                    ],
                ]
            ]);
    }

    public function testUpdateJobStatus()
    {
        $this->specify("Testing to update the job status", function ($organizationId, $personId, $jobType, $jobStatus, $jobId, $orgPersonJobStatus, $jobTypeObject, $jobStatusObject, $expectedResults) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockJobTypeRepository = $this->getMock('JobType', ['findOneBy']);
            $mockJobTypeRepositoryObject = $this->getMock('JobType', ['getJobTypeId']);
            $mockOrgPersonJobStatusRepository = $this->getMock('OrgPersonJobStatus', [
                'findOneBy', 'flush', 'setStatus'
            ]);
            $mockJobStatusDescriptionRepository = $this->getMock('JobStatusDescription', ['findOneBy']);
            $mockOrgPersonJobQueueRepository = $this->getMock('OrgPersonJobQueueRepository', ['findOneBy']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [JobTypeRepository::REPOSITORY_KEY, $mockJobTypeRepository],
                    [OrgPersonJobStatusRepository::REPOSITORY_KEY, $mockOrgPersonJobStatusRepository],
                    [JobStatusDescriptionRepository::REPOSITORY_KEY, $mockJobStatusDescriptionRepository],
                    [OrgPersonJobQueueRepository::REPOSITORY_KEY, $mockOrgPersonJobQueueRepository]
                ]);

            $mockJobTypeRepository->method('findOneBy')->willReturn($jobTypeObject);
            if ($jobTypeObject) {
                $mockJobTypeRepositoryObject->method('getJobTypeId')->willReturn($jobTypeObject->getId());
            }
            $mockJobStatusDescriptionRepository->method('findOneBy')->willReturn($jobStatusObject);
            $mockOrgPersonJobStatusRepository->method('findOneBy')->willReturn($orgPersonJobStatus);

            $jobService = new JobService($mockRepositoryResolver, $mockContainer, $mockLogger);
            try {
                $updateJobStatus = $jobService->updateJobStatus($organizationId, $jobType, $jobStatus, $jobId, $personId);
                $this->assertEquals($expectedResults, $updateJobStatus);
            } catch (ResqueJobRunDeniedException $resqueException) {
                $this->assertEquals($expectedResults, $resqueException->getUserMessage());
            }
        },
            ['examples' =>
                [
                    // Update the existing job status to in progress
                    [
                        203,
                        4891668,
                        'InitialSyncJob',
                        'In progress',
                        't8y980678',
                        new OrgPersonJobStatus(),
                        $this->createJobTypeObject('InitialSyncJob'),
                        $this->createJobStatusObject('In progress'),
                        true
                    ],
                    // Trying to update a job with invalid job type - will throw an exception.
                    [
                        203,
                        4891668,
                        'InvalidJobType',
                        'In progress',
                        't8y980678',
                        new OrgPersonJobStatus(),
                        0,
                        $this->createJobStatusObject('In progress'),
                        "There was an error fetching the job type."
                    ],
                    // Update existing job with wrong status - will throw an exception.
                    [
                        203,
                        4891668,
                        'InitialSyncJob',
                        'invalidJobStatus',
                        't8y980678',
                        new OrgPersonJobStatus(),
                        $this->createJobTypeObject('InitialSyncJob'),
                        0,
                        "There was an error fetching the job status."
                    ],
                    // Update a job with invalid organization id - will throw an exception.
                    [
                        'invalidOrganizationId',
                        4891668,
                        'InitialSyncJob',
                        'invalidJobStatus',
                        't8y980678',
                        0,
                        $this->createJobTypeObject('InitialSyncJob'),
                        $this->createJobStatusObject('In progress'),
                        SynapseConstant::RESQUE_NO_JOB_FOUND
                    ],
                    // Update a job with empty organization id - will throw an exception.
                    [
                        '',
                        4891668,
                        'InitialSyncJob',
                        'invalidJobStatus',
                        't8y980678',
                        0,
                        $this->createJobTypeObject('InitialSyncJob'),
                        $this->createJobStatusObject('In progress'),
                        SynapseConstant::RESQUE_NO_JOB_FOUND
                    ],
                    // Update a job with empty person id - will throw an exception.
                    [
                        203,
                        '',
                        'InitialSyncJob',
                        'invalidJobStatus',
                        't8y980678',
                        0,
                        $this->createJobTypeObject('InitialSyncJob'),
                        $this->createJobStatusObject('In progress'),
                        SynapseConstant::RESQUE_NO_JOB_FOUND
                    ],
                    // Update the existing job status to success
                    [
                        203,
                        4891668,
                        'InitialSyncJob',
                        'Success',
                        't8y980678',
                        new OrgPersonJobStatus(),
                        $this->createJobTypeObject('InitialSyncJob'),
                        $this->createJobStatusObject('Success'),
                        true
                    ],
                    // Update the existing job status to failed
                    [
                        203,
                        4891668,
                        'InitialSyncJob',
                        'Failure',
                        't8y980678',
                        new OrgPersonJobStatus(),
                        $this->createJobTypeObject('InitialSyncJob'),
                        $this->createJobStatusObject('Failure'),
                        true
                    ],
                    // trying to update the job where the job id is invalid - which will throw an exception.
                    [
                        203,
                        '',
                        'InitialSyncJob',
                        'In Progress',
                        'invalidJobId',
                        0,
                        $this->createJobTypeObject('InitialSyncJob'),
                        $this->createJobStatusObject('In progress'),
                        SynapseConstant::RESQUE_NO_JOB_FOUND
                    ],
                ]
            ]);
    }

    public function testGetJobStatus()
    {
        $this->specify("Test to get the status of job", function ($organizationId, $personId, $orgPersonJobStatus, $jobStatus, $errorFlag) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockOrgPersonJobStatusRepository = $this->getMock('OrgPersonJobStatusRepository', [
                'checkPendingJobsByRole'
            ]);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrgPersonJobStatusRepository::REPOSITORY_KEY, $mockOrgPersonJobStatusRepository],
                ]);
            $mockOrgPersonJobStatusRepository->method('checkPendingJobsByRole')->willReturn($orgPersonJobStatus);
            $jobService = new JobService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $errorMessage = SynapseConstant::RESQUE_JOB_CALENDAR_ERROR;
            try {
                $jobStatusOutput = $jobService->getJobStatus($organizationId, $personId, $errorFlag, $errorMessage);
                $expectedOutput = $this->createJobStatusDTO($personId, $organizationId, $jobStatus);
                $this->assertEquals($expectedOutput, $jobStatusOutput);
            } catch (ResqueJobRunDeniedException $resqueException) {
                $this->assertEquals(SynapseConstant::RESQUE_JOB_CALENDAR_ERROR, $resqueException->getUserMessage());
            }

        },
            ['examples' =>
                [
                    // Pending Job for the person and throw the exception
                    [203, 4891668, 1, "In Progress", true],
                    // Pending job for the person and doesn't show the exception, return the DTO.
                    [203, 4215846, 1, "In Progress", false],
                    // Get the status when there is no Pending job,
                    [203, 4891668, 0, "Success", false],
                    // There is no pending job, but the job which was completed is in failure status
                    [203, 4215846, 0, "Failure", false],
                    // Get the status of organization when there is no pending jobs.
                    [213, null, 0, "Success", false],
                    // Get the status of organization when there is pending jobs.
                    [213, null, 1, "In Progress", false],
                    // Get the status of organization when there is pending jobs and error flag is 1
                    [213, null, 1, "In Progress", true],
                ]
            ]);
    }

    public function testCreateOrgPersonJobStatus()
    {
        $this->specify("Test Create Person JobStatus", function ($personId, $jobStatus, $jobType, $jobNumber) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockOrgPersonJobStatusRepository = $this->getMock('OrgPersonJobStatusRepository', [
                'persist'
            ]);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrgPersonJobStatusRepository::REPOSITORY_KEY, $mockOrgPersonJobStatusRepository],
                ]);
            $jobService = new JobService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $person = $this->createPerson($personId);
            $organization = new Organization();
            $jobStatus = $this->createJobStatusObject($jobStatus);
            $jobType = $this->createJobTypeObject($jobType);
            $results = $jobService->createOrgPersonJobStatus($organization, $jobStatus, $jobType, $jobNumber, $person);
            $this->assertInstanceOf('Synapse\JobBundle\Entity\OrgPersonJobStatus', $results);
            $this->assertEquals($personId, $results->getPerson()->getId());
        },
            ['examples' =>
                [
                    // Create OrgPersonJobStatus with In Progress status
                    [
                        45123254,
                        'In Progress',
                        'InitialSync',
                        9989898,
                    ],
                    // Create OrgPersonJobStatus with Success status
                    [
                        45123254,
                        'Success',
                        'InitialSync',
                        9989898,
                    ],
                ]
            ]);
    }

    public function testEnqueueJob()
    {
        $this->specify("Test EnqueueJob", function ($personId, $jobQueueId) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockOrgPersonJobQueueRepository = $this->getMock('orgPersonJobQueueRepository', [
                'findOneBy', 'flush',
            ]);
            $mockPersonRepository = $this->getMock('personRepository', [
                'find'
            ]);
            $mockOrgPersonJobStatusRepository = $this->getMock('orgPersonJobStatusRepository', [
                'persist'
            ]);
            $mockOrganizationRepository = $this->getMock('organizationRepository', ['find']);
            $mockJobStatusDescriptionRepository = $this->getMock('jobStatusDescriptionRepository', ['findOneBy']);

            $mockResque = $this->getMock('resque', ['enqueue']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [SynapseConstant::RESQUE_CLASS_KEY, $mockResque]
                ]);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrgPersonJobQueueRepository::REPOSITORY_KEY, $mockOrgPersonJobQueueRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [JobStatusDescriptionRepository::REPOSITORY_KEY, $mockJobStatusDescriptionRepository],
                    [OrgPersonJobStatusRepository::REPOSITORY_KEY, $mockOrgPersonJobStatusRepository]
                ]);

            $orgPersonJobQueue = $this->createOrgPersonJobQueue($personId);
            $mockOrgPersonJobQueueRepository->method('findOneBy')->willReturn($orgPersonJobQueue);

            $jobService = new JobService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $results = $jobService->enqueueQueuedJobs($personId);
            $this->assertInstanceOf('Synapse\JobBundle\Entity\OrgPersonJobQueue', $results);
            $this->assertEquals($personId, $results->getPerson()->getId());
        },
            ['examples' =>
                [
                    // Enqueue the Job by passing valid person id
                    [8975640, 207],
                    // Enqueue the Job by passing in valid person id
                    ['', 209],
                    // Enqueue the Job by passing valid person id
                    [453678, 208],
                    // Enqueue the Job by passing valid person id
                    [4536778, 209],
                ]
            ]);
    }

    /**
     * @param int $personId
     * @param int $organizationId
     * @param string $jobStatus
     * @return JobStatusDTO
     */
    private function createJobStatusDTO($personId, $organizationId, $jobStatus)
    {
        $jobStatusDTO = new JobStatusDTO();
        $jobStatusDTO->setPersonId($personId);
        $jobStatusDTO->setOrganizationId($organizationId);
        if ($jobStatus == 'Success' || $jobStatus == 'Failure') {
            $jobStatus = 'Completed';
        } else {
            $jobStatus = 'In Progress';
        }
        $jobStatusDTO->setJobStatus($jobStatus);
        return $jobStatusDTO;
    }

    /**
     * Create Job Type Object
     *
     * @param string $jobType
     * @return JobType
     */
    private function createJobTypeObject($jobType)
    {
        $jobTypeObject = new JobType();
        $jobTypeObject->setJobType($jobType);
        return $jobTypeObject;
    }

    /**
     * Create Job Status Description object
     *
     * @param string $jobStatus
     * @return JobStatusDescription
     */
    private function createJobStatusObject($jobStatus)
    {
        $jobStatusDescriptionObject = new JobStatusDescription();
        $jobStatusDescriptionObject->setJobStatusDescription($jobStatus);
        return $jobStatusDescriptionObject;
    }

    private function createPerson($personId)
    {
        $person = new Person();
        $person->setId($personId);
        return $person;
    }

    /**
     * Creates OrgPersonJobQueue
     *
     * @param int $personId
     * @return OrgPersonJobQueue
     */
    private function createOrgPersonJobQueue($personId)
    {
        $orgPersonJobQueue = new OrgPersonJobQueue();
        $organization = new Organization();
        $orgPersonJobQueue->setOrganization($organization);
        $person = new Person();
        $person->setId($personId);
        $person->setOrganization($organization);
        $orgPersonJobQueue->setPerson($person);
        $job = new InitialSyncJob();
        $jobNumber = uniqid();
        $job->args = array(
            'jobNumber' => $jobNumber,
            'personId' => $personId
        );
        $orgPersonJobQueue->setJobQueuedInfo(serialize($job));
        return $orgPersonJobQueue;
    }

    /**
     * Creates OrgPersonJobStatus
     *
     * @param int $personId
     * @return OrgPersonJobQueue
     */
    private function creatOrgPersonJobStatus($personId)
    {

        $orgPersonJobQueue = new OrgPersonJobQueue();
        $organization = new Organization();
        $orgPersonJobQueue->setOrganization($organization);
        $person = new Person();
        $person->setId($personId);
        $person->setOrganization($organization);
        $orgPersonJobQueue->setPerson($person);
        $job = new InitialSyncJob();
        $jobNumber = uniqid();
        $job->args = array(
            'jobNumber' => $jobNumber,
            'personId' => $personId
        );
        $orgPersonJobQueue->setJobQueuedInfo(serialize($job));
        return $orgPersonJobQueue;
    }

}