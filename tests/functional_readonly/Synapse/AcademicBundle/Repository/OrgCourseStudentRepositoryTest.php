<?php

use Codeception\TestCase\Test;

class OrgCourseStudentRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\AcademicBundle\Repository\OrgCourseStudentRepository
     */
    private $orgCourseStudentRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgCourseStudentRepository = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgCourseStudent');
    }

    public function testGetStudentsByCourse()
    {
        $this->specify("Verify the functionality of the method getStudentsByCourse", function ($courseId, $expectedCount, $expectedResults) {
            $results = $this->orgCourseStudentRepository->getStudentsByCourse($courseId);
            verify(count($results))->equals($expectedCount);
            for ($i = 0; $i < count($results); $i++) {
                verify($results[$i]['studentId'])->notEmpty();
                verify($results[$i]['studentId'])->equals($expectedResults[$i]);
            }
        }, ["examples" =>
            [
                [248238, 7, [4773440, 4773260, 4773550, 4773419, 4773445, 4773567, 4773381]],
                [235941, 16, [4879713, 4879834, 4879715, 4879789, 4879112, 4879230, 4879113, 4879186, 4879313, 4879410, 4879319, 4879406, 4878849, 4878905, 4878981, 4878907]],
                [157927, 1, [4595025]],
                [157925, 3, [179, 4597588, 4628422]]
            ]
        ]);
    }

    public function testGetStudentsInAnyCurrentCourse()
    {
        $this->markTestSkipped("Failing, most likely due to timeline constraints stated below as a TODO");
        // TODO: When the current date moves outside of the current academic term for org 777 or or 9, this test will fail.
        $this->specify("Verify the functionality of the method getStudentsInAnyCurrentCourse", function ($expectedResultsSize, $expectedIds, $orgId) {

            $results = $this->orgCourseStudentRepository->getStudentsInAnyCurrentCourse($orgId);
            verify(count($results))->equals($expectedResultsSize);
            $results = array_slice($results, 0, 5);
            for ($i = 0; $i < count($expectedIds); $i++) {
                verify($results[$i]['student_id'])->notEmpty();
                verify($results[$i]['student_id'])->equals($expectedIds[$i]);
            }
        }, ["examples" =>
            [
                [777, [1005823, 4652823, 751376, 196407, 751507], 132],
                [9, [4556659, 4559163, 4556768, 4556982, 4895219], 191]
            ]
        ]);

    }

    public function testGetCourseStudentDumpDataForOrganization()
    {
        $this->specify("", function ($expectedResult, $organizationId = null) {
            $functionResults = $this->orgCourseStudentRepository->getCourseStudentDumpDataForOrganization($organizationId);
            verify($functionResults)->equals($expectedResult);
        }, ['examples' => [
            //Valid organization
            [
                [
                    0 => [
                        'StudentId' => '4556008',
                        'UniqueCourseSectionId' => '20141510051'
                    ],
                    1 => [
                        'StudentId' => '4556009',
                        'UniqueCourseSectionId' => '20141510051'
                    ]
                ],
                3
            ],
            //No Organization
            [
                []
            ],
            //Invalid Organization
            [
                [],
                "I AM NOT AN INTEGER"
            ]
        ]]);
    }

    public function testGetCoursesForStudent()
    {
        $this->specify("Verify the functionality of the method getCoursesForStudent", function ($studentIds, $datetime, $expectedResults, $expectedCount) {
            $results = $this->orgCourseStudentRepository->getCoursesForStudent($studentIds, $datetime);
            verify(count($expectedResults))->equals($expectedCount);
            $results = array_slice($results, 0, 4);
            verify($results)->equals($expectedResults);
        }, ["examples" =>
            [
                // Test 01 - Valid students id and date time will return courses for the student
                [
                    [280276,374126,967355],
                    new \DateTime('2015-09-17 10:24:12'),
                    [
                        0 =>[
                            'person_id' => "280276",
                            'org_courses_id' => "3311",
                        ],
                        1 =>[
                            'person_id' => "280276",
                            'org_courses_id' => "3520",
                        ],
                        2 =>[
                            'person_id' => "280276",
                            'org_courses_id' => "4456",
                        ],
                        3 =>[
                            'person_id' => "374126",
                            'org_courses_id' => "3311",
                        ]
                    ],
                    4
                ],
                // Test 02 - Invalid students id and valid date time will return empty result array
                [
                    [-280276,-374126],
                    new \DateTime('2015-09-17 10:24:12'),
                    [],
                    0
                ],
                // Test 03 - Valid students id and invalid date time will return empty result array
                [
                    [280276, 374126],
                    new \DateTime('0000-09-17 10:24:12'),
                    [],
                    0
                ],
                // Test 04 - Students id as null and valid date time will return empty result array
                [
                    [null, null],
                    new \DateTime('2015-09-17 10:24:12'),
                    [],
                    0
                ],
                // Test 05 - All values are given as null will return empty result array
                [
                    [null, null],
                    null,
                    [],
                    0
                ]
            ]
        ]);
    }

    public function testGetStudentsByCourses()
    {
        $this->specify("Verify the functionality of the method getStudentsByCourses", function ($organizationCoursesIds, $expectedResults, $expectedCount) {
            $results = $this->orgCourseStudentRepository->getStudentsByCourses($organizationCoursesIds);
            verify(count($expectedResults))->equals($expectedCount);
            $results = array_slice($results, 0, 4);
            verify($results)->equals($expectedResults);
        }, ["examples" =>
            [
                // Test 01 - Valid organization courses id will return students id
                [
                    [4013, 4014],
                    [
                        0 => "4613074",
                        1 => "4612539",
                        2 => "4613052",
                        3 => "4613213",
                    ],
                    4
                ],
                // Test 02 - Invalid organization courses id will return empty result array
                [
                    [-4013, -4014],
                    [],
                    0
                ],
                // Test 03 - Organization courses id as null will return empty result array
                [
                    [null, null],
                    [],
                    0
                ]
            ]
        ]);
    }

    public function testGetCourseStudentCountByOrganization()
    {
        $this->specify("Verify the functionality of the method getCourseStudentCountByOrganization", function ($organizationId, $expectedResults) {
            $results = $this->orgCourseStudentRepository->getCourseStudentCountByOrganization($organizationId);
            verify($results)->equals($expectedResults);
        }, ["examples" =>
            [
                // Test 01 - Valid organization id will return count of students for all courses by organization
                [
                   99,
                    "6006",
                ],
                // Test 02 - Invalid organization id will return empty result array
                [
                    -99,
                    0
                ],
                // Test 03 - Organization id as null will return empty result array
                [
                    null,
                    0
                ]
            ]
        ]);
    }
}
