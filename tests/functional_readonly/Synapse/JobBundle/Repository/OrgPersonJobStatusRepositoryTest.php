<?php

use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Repository\OrgPersonJobStatusRepository;

class OrgPersonJobStatusRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var OrgPersonJobStatusRepository
     */
    private $orgPersonJobStatusRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->orgPersonJobStatusRepository = $this->repositoryResolver->getRepository(OrgPersonJobStatusRepository::REPOSITORY_KEY);
    }

    public function testCheckOrganizationPendingJobsByBlockMapping()
    {

        $this->specify("Verify the functionality of the method checkOrganizationPendingJobsByBlockMapping", function ($jobType, $jobStatus, $expectedCount) {
            $organizationId = 203;
            $results = $this->orgPersonJobStatusRepository->checkOrganizationPendingJobsByBlockMapping($organizationId, $jobType, $jobStatus, false);
            verify($results)->equals($expectedCount);
        }, [
            "examples" => [
                // Initial sync job with success status
                [['InitialSyncJob'], ['Success'], 4],
                // Initial Sync job which has failed
                [['InitialSyncJob'], ['Failure'], 4],
                // Wrong Job type
                [['InvalidJobType'], ['success'], 0],
                // Invalid Job status
                [['InitialSyncJob'], ['InvalidJobStatus'], 0],
                // In Progress for RemoveEvent Job
                [['RemoveEventJob'], ['In Progress'], 4],
            ]
        ]);
    }


    public function testGetJobActionForRequestedJob()
    {
        $this->specify("Verify the functionality of the method getJobActionForRequestedJob", function ($jobType, $jobStatus, $personId, $expectedResults) {
            $organizationId = 203;
            $results = $this->orgPersonJobStatusRepository->getJobActionForRequestedJob($organizationId, $jobType, $jobStatus, $personId);
            verify($results)->equals($expectedResults);
        }, [
            "examples" => [
                // Jobs blocked by InitialSyncJobs
                [
                    ['InitialSyncJob'], ['In Progress'], 4883145, ['blocked']
                ],
                // Jobs blocked by RemoveEventJob by passing no value for status
                [
                    ['RemoveEventJob'], [], 4879761, []
                ],
                // Jobs blocked by InitialSyncJobs for the person 4879761
                [
                    ['InitialSyncJob'], ['In Progress'], 4879761, ['blocked']
                ],
                // Empty Job value
                [
                    [], ['In Progress'], 4879761, []
                ],
                // Jobs blocked by InitialSyncJobs and the status in Queued.
                [
                    ['InitialSyncJob'], ['Queued'], 4879642, ['blocked']
                ],
                // Jobs blocked by RemoveEventJob
                [
                    ['RemoveEventJob'], ['In Progress'], 4879761, ['blocked', 'queued']
                ],
                // Empty person id.
                [
                    ['RemoveEventJob'], ['In Progress'], 0, []
                ],
            ]
        ]);
    }

    public function testCheckPendingJobsByRole()
    {
        $this->specify("Verify the functionality of the method checkPendingJobsByRole", function ($organizationId, $personId, $jobStatus, $expectedCount) {
            $results = $this->orgPersonJobStatusRepository->checkPendingJobsByRole($organizationId, $personId, $jobStatus);
            verify($results)->equals($expectedCount);
        }, [
            "examples" => [
                // Count the in progress job for person 4879761
                [203, 4879761, ['In Progress'], 2],
                // Missing organization id.
                ['', 4879761, ['In Progress'], 0],
                // Count the in progress and queued job for person 4879761
                [203, 4879761, ['In Progress', 'Queued'], 2],
                // Count the queued jobs for person 4879642
                [203, 4879642, ['Queued'], 1],
                //Invalid Person id
                [203, 'Invalid', ['Queued'], 0],
            ]
        ]);
    }

    public function testCheckPendingJobsByJobType()
    {
        $this->specify("Verify the functionality of the method checkPendingJobsByJobType", function ($jobType, $jobStatus, $organizationId, $personId, $expectedCount) {
            $results = $this->orgPersonJobStatusRepository->checkPendingJobsByJobType($organizationId, $personId, $jobType, $jobStatus);
            verify($results)->equals($expectedCount);
        }, [
            "examples" => [
                // Jobs blocked by RecurrentEventJob by passing no value for status
                [['RecurrentEventJob'], [], 203, 4879849, 0],
                // Jobs blocked by InitialSyncJobs
                [['InitialSyncJob'], ['In Progress'], 203, 4883145, 1],
                // Jobs blocked by InitialSyncJobs for the person 4879761
                [['RecurrentEventJob'], ['In Progress'], 203, 4879759, 0],
                // Empty Job value
                [[], ['In Progress', 'Queued'], 203, 4879761, 0],
                // Passing in valid organization id.
                [['InitialSyncJob'], ['Queued'], 'invalid', 4879642, 0],
                // Jobs blocked by RemoveEventJob
                [['RemoveEventJob'], ['In Progress'], 203, 4879761, 1],
                // Invalid person id.
                [['RemoveEventJob'], ['In Progress'], 203, 'invalid', 0],

            ]
        ]);
    }

    public function testGetInProgressJob()
    {
        $this->specify("Verify the functionality of the method getJobsByStatus", function ($personId, $jobStatus, $expectedCount) {
            $organizationId = 203;
            $results = $this->orgPersonJobStatusRepository->getJobsByStatus($organizationId, $personId, $jobStatus);
            verify(count($results))->equals($expectedCount);
        }, [
            "examples" => [
                // Test the In Progress Jobs
                [4883145, ['In Progress'], 1],
                // Test Success Jobs
                [4879691, ['Success'], 2],
                // Passing in valid person id
                ['', ['Queued', 'Success'], 0],
                // Test the In Progress Jobs
                [4879761, ['In Progress'], 2],
                // Test more than one Jobs Success, Failure
                [4879774, ['Success', 'Failure'], 2],
                // Passing invalid status
                [4879774, ['test', 'sample'], 0],
                // Test more than one Jobs Success, Queued
                [4879642, ['Queued', 'Success'], 2],
                // Passing empty status
                [4879774, [], 0],
            ]
        ]);
    }
}