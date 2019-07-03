<?php

use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\SynapseConstant;

class OrgGroupFacultyRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     *
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
    }

    public function testGetGroupStaffList()
    {
        $this->specify("Verify the functionality of the method getGroupStaffList", function ($organizationId, $groupId, $isInternal, $expectedFacultyDetails) {

            $results = $this->orgGroupFacultyRepository->getGroupStaffList($organizationId, $groupId, $isInternal);
            verify($results)->equals($expectedFacultyDetails);

        }, ["examples" =>
            [
                //Case1: Test for Group Id 15 and isInternal true, returns single array
                [
                    2,
                    15,
                    true,
                    [
                        0 => [
                            "group_staff_id" => 16,
                            "staff_id" => 277,
                            "staff_firstname" => "Hampton",
                            "staff_lastname" => "Hollington",
                            "staff_is_invisible" => 0,
                            "staff_permissionset_id" => 4,
                            "staff_permissionset_name" => "Limited Access"
                        ]
                    ]
                ],
                //Case2: Test for Group Id 77 and isInternal false, returns multiple array
                [
                    2,
                    77,
                    false,
                    [
                        0 => [
                            "mapworks_internal_id" => 246,
                            "faculty_external_id" => "246",
                            "firstname" => "Mark",
                            "lastname" => "Wissinger",
                            "primary_email" => "Mark.Wissinger@ns2016.mapworks.com",
                            "is_invisible" => 0,
                            "permissionset_name" => "All Access"
                        ],
                        1 => [
                            "mapworks_internal_id" => 258,
                            "faculty_external_id" => "258",
                            "firstname" => "Sam",
                            "lastname" => "Carter",
                            "primary_email" => "Sam.Carter@ns2016.mapworks.com",
                            "is_invisible" => 0,
                            "permissionset_name" => "Aggregate Only"
                        ]
                    ]
                ],
                //Case2: Test for Group Id 31 and returns empty array
                [
                    2,
                    31,
                    false,
                    []
                ]
            ]
        ]);
    }
}