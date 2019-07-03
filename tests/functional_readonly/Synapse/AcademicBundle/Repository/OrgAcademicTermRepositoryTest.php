<?php
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;

class OrgAcademicTermRepositoryTest extends \Codeception\TestCase\Test
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
     * @var OrgAcademicTermRepository
     */
    private $orgAcademicTermRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgAcademicTermRepository = $this->repositoryResolver->getRepository(OrgAcademicTermRepository::REPOSITORY_KEY);
    }


    public function testGetAcademicTermDates()
    {
        $this->specify("Verify the functionality of the method getAcademicTermDates", function ($organizationId, $currentDate, $expectedResult) {
            $results = $this->orgAcademicTermRepository->getAcademicTermDates($currentDate, $organizationId);
            verify($results)->equals($expectedResult);
        }
            , [
                "examples" => [

                    // Test 01 - Valid organization and current date will return result array
                    [
                        99,
                        '2015-09-17 10:24:12',
                        [
                            0 =>
                                [
                                    'id' => 58,
                                    'endDate' => new \DateTime('2015-12-15 00:00:00'),
                                    'name' => 'Fall Semester'
                                ]
                        ]
                    ],
                    // Test 02 - Valid organization and current date will return multiple result array
                    [
                            203,
                            '2015-09-18 10:24:12',
                        [
                            0 =>
                                [
                                    'id' => 407,
                                    'endDate' => new \DateTime('2016-07-29 00:00:00'),
                                    'name' => 'Fall'
                                ],
                            1 =>
                                [
                                    'id' => 393,
                                    'endDate' => new \DateTime('2015-12-25 00:00:00'),
                                    'name' => 'Fall 2015'
                                ]
                        ]
                    ],

                    // Test 03 - Invalid current date will return empty result array
                    [
                        203,
                        '0000-00-00 00:00:00',
                        [],
                    ],
                    // Test 04 - Organization as null and valid current date will return empty result array
                    [
                        null,
                        '2015-09-18 10:24:12',
                        [],
                    ],
                    // Test 05 - Current date as null and valid organization will return empty result array
                    [
                        203,
                        null,
                        [],
                    ],
                    // Test 06 - Invalid organization and valid current date will return empty result array
                    [
                        -1,
                        '2015-09-18 10:24:12',
                        [],
                    ],
                    // Test 07 - Both organization and current date as null will return empty result array
                    [
                        null,
                        null,
                        [],
                    ]
                ]
            ]);
    }

    public function testGetAcademicTermsForYear()
    {
        $this->specify("Verify the functionality of the method getAcademicTermsForYear", function ($organizationAcademicYearId, $organizationId, $expectedResult) {
            $results = $this->orgAcademicTermRepository->getAcademicTermsForYear($organizationAcademicYearId, $organizationId);
            verify($results)->equals($expectedResult);
        }
            , [
                "examples" => [

                    // Test 01 - Valid organization academic year and organization will return result array
                    [
                        27,
                        99,
                        [
                            0 =>
                                [
                                    'name' => 'Fall Semester',
                                    'org_academic_term_id' => '58',
                                    'start_date' => '2015-08-17',
                                    'end_date' => '2015-12-15',
                                    'term_code' => 'Fall2015',
                                    'is_current_academic_term' => '0'
                                ],
                            1 =>
                                [
                                    'name' => 'Spring Semester',
                                    'org_academic_term_id' =>  '59',
                                    'start_date' => '2016-01-11',
                                    'end_date' => '2016-05-10',
                                    'term_code' => 'Spring2016',
                                    'is_current_academic_term' => '0'
                                ],
                            2 =>
                                [
                                    'name' => 'Summer Semester',
                                    'org_academic_term_id' =>  '60',
                                    'start_date' => '2016-05-09',
                                    'end_date' => '2016-08-09',
                                    'term_code' => 'Summer2016',
                                    'is_current_academic_term' => '0'
                                ]
                        ]
                    ],
                    // Test 02 - Valid organization and organization academic year as null will return empty result array
                    [
                        null,
                        99,
                        []
                    ],
                    // Test 03 - Valid organization academic year and organization as null will return empty result array
                    [
                        27,
                        null,
                        []
                    ],

                    // Test 04 - Both organization academic year and organization as null will return empty result array
                    [
                        null,
                        null,
                        []
                    ],
                    // Test 05 - Invalid organization academic year and valid organization will return empty result array
                    [
                        -1,
                        99,
                        []
                    ],
                    // Test 06 - Invalid organization and valid organization academic year will return empty result array
                    [
                        27,
                        -1,
                        []
                    ],
                    // Test 07 - Valid organization academic year and organization but the organization academic year does not map to the organization will return empty result array
                    [
                        156,
                        99,
                        []
                    ],
                    // Test 08 - Valid organization academic year and organization but the organization does not map to the organization academic year will return empty result array
                    [
                        27,
                        203,
                        []
                    ],

                ]
            ]);
    }
}