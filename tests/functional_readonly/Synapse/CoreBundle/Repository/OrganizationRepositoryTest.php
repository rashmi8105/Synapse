<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;

class OrganizationRepositoryTest extends Test
{
    use Codeception\Specify;


    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;


    // Repositories

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
    }

    public function testCountAndLastUpdateOfOrgPersonFaculty()
    {
        $this->specify("Test the mechanism for getting the count and lastupdate date of faculties based an organization", function ($organizationId, $expectedResult) {
            $result = $this->organizationRepository->getCountAndLastUpdateOfOrgPersonFaculty($organizationId);
            verify($result)->equals($expectedResult);
        }, ["examples" =>
            [
                // Example1: Test faculty count and lastupdate date are returning and valid for the Organization 203
                [203,
                    [
                        'faculty_count' => 438,
                        'modifiedAt' => '2016-02-03 21:34:16'
                    ]
                ],

                // Example2: Test faculty count zero and lastupdate date null when Organization null
                [null,
                    [
                        'faculty_count' => 0,
                        'modifiedAt' => null
                    ]
                ]
            ]
        ]);
    }

    public function testCountAndLastUpdateOfOrgPersonStudent()
    {
        $this->specify("Test the mechanism for getting the count and lastupdate date of students based an organization", function ($organizationId, $expectedResult) {
            $result = $this->organizationRepository->getCountAndLastUpdateOfOrgPersonStudent($organizationId);
            verify($result)->equals($expectedResult);
        }, ["examples" =>
            [
                // Example1: Test student count and lastupdate date are returning and valid for the Organization 203
                [203,
                    [
                        'student_count' => 1050,
                        'modifiedAt' => '2016-06-20 19:42:56'
                    ]
                ],

                // Example2: Test faculty count zero and lastupdate date null when Organization null
                [null,
                    [
                        'student_count' => 0,
                        'modifiedAt' => null
                    ]
                ]
            ]
        ]);
    }

}