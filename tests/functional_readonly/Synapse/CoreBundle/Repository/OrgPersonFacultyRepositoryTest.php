<?php

use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\SynapseConstant;

class OrgPersonFacultyRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     *
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     *
     * @var \Synapse\CoreBundle\Repository\OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    private $studentId = 4878808;

    private $organizationId = 203;


    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
    }

    public function testGetGroupCampusConnection()
    {
        $this->specify("Verify the functionality of the method getGroupCampusConnection", function ($expectedResultsSize, $expectedFacultyIds) {
            $currentDate = new \DateTime('now');
            $date = $currentDate->format('Y-m-d');
            $results = $this->orgPersonFacultyRepository->getGroupCampusConnection($this->studentId, $this->organizationId, $date);
            verify(count($results))->equals($expectedResultsSize);
            for ($i = 0; $i < count($expectedFacultyIds); $i++) {
                verify($results[$i]['faculty_id'])->notEmpty();
                verify($results[$i]['faculty_id'])->equals($expectedFacultyIds[$i]);

            }
        }, [
            "examples" => [
                [
                    5, [4878750, 4878751, 4883148, 4883150]
                ]
            ]
        ]);
    }

    public function testGetAllCampusConnectioDetailsForStudent()
    {
        $this->specify("Verify the functionality of the method getAllCampusConnectioDetailsForStudent", function ($expectedResultsSize, $expectedPersonId) {
            $currentDate = new \DateTime('now');
            $date = $currentDate->format('Y-m-d');
            $results = $this->orgPersonFacultyRepository->getAllCampusConnectioDetailsForStudent($this->studentId, $date);
            $results = array_slice($results, 0, 4);
            verify(count($results))->equals($expectedResultsSize);
            for ($i = 0; $i < count($expectedPersonId); $i++) {

                verify($results[$i]['person_id'])->notEmpty();
                verify($results[$i]['person_id'])->equals($expectedPersonId[$i]);
            }
        }, [
            "examples" => [
                [4, [4883097, 4883150, 4878751, 4883148]]
            ]

        ]);
    }


    public function testGetListOfGoogleCalendarSyncUsers()
    {
        $this->specify("Verify the functionality of the method getListOfGoogleCalendarSyncUsers", function ($organizationId, $expectedResultsSize) {

            $results = $this->orgPersonFacultyRepository->getListOfGoogleCalendarSyncUsers($organizationId);
            verify(count($results))->equals($expectedResultsSize);

        }, [
            "examples" => [
                [203, 0]
            ]

        ]);
    }

    public function testGetNonLoggedInFaculty()
    {
        $this->specify("testGetNonLoggedInFaculty", function ($expectedResult, $organizationId = null) {
            $functionResults = $this->orgPersonFacultyRepository->getNonLoggedInFaculty($organizationId);
            verify($functionResults)->equals($expectedResult);
        }, [
            'examples' => [
                //Organization with only active non-logged in faculty
                [
                    [
                        0 => [
                            'person_id' => '4824133'
                        ]
                    ],
                    31
                ],
                //Organization with both active and inactive non-logged in faculty. Inactives are not included in the list.
                [
                    [
                        0 => [
                            'person_id' => '161027'
                        ],
                        1 => [
                            'person_id' => '161139'
                        ],
                        2 => [
                            'person_id' => '4780047'
                        ]
                    ],
                    162
                ],
                //No passed organization ID
                [
                    []
                ],
                //Invalid organization ID
                [
                    [],
                    "This is not an integer."
                ]
            ]
        ]);
    }

    public function testGetNonLoggedInFacultyCount()
    {
        $this->specify("testGetNonLoggedInFacultyCount", function ($expectedResult, $organizationId = null) {
            $functionResults = $this->orgPersonFacultyRepository->getNonLoggedInFacultyCount($organizationId);
            verify($functionResults)->equals($expectedResult);
        }, [
            'examples' => [
                //Organization with only active non-logged in faculty
                [
                    1,
                    31
                ],
                //Organization with both active and inactive non-logged in faculty. Inactives are not included in the list.
                [
                    3,
                    162
                ],
                //No passed organization ID
                [
                    0
                ],
                //Invalid organization ID
                [
                    0,
                    "This is not an integer."
                ]
            ]
        ]);
    }
}