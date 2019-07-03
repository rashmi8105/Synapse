<?php

use Codeception\TestCase\Test;
use Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository;

class OrgCourseFacultyRepositoryTest extends Test
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
     * @var OrgCourseFacultyRepository
     */
    private $orgCourseFacultyRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgCourseFacultyRepository = $this->repositoryResolver->getRepository(OrgCourseFacultyRepository::REPOSITORY_KEY);
    }

    public function testGetCourseFacultyForOrganization()
    {
        $this->specify("Verify the functionality of the method getCourseFacultyForOrganization", function ($organizationId, $expectedResults) {
            $results = $this->orgCourseFacultyRepository->getCourseFacultyForOrganization($organizationId);
            $index = 0;
            if (count($results) > 0) {
                foreach ($results as $result) {
                    $this->assertEquals($expectedResults[0][$index], $result[0]->getId());
                    $this->assertEquals($expectedResults[1][$index], $result[0]->getCourse()->getId());
                    $this->assertEquals($expectedResults[2][$index], $result[0]->getPerson()->getId());
                    $this->assertEquals($expectedResults[3][0], $result[0]->getOrganization()->getId());
                    $index++;
                }
            } else {
                verify($results)->equals($expectedResults);
            }
        }, ["examples" =>
            [
                // Test 01 - Valid organization will return courses, faculties for the organization
               [
                   204,
                [
                    [
                        195431, 196733, 196734
                    ],
                    [
                        248441, 249824, 249825
                    ],
                    [
                        4893111, 4893139, 4893111
                    ],
                    [
                        204
                    ]
                ]
               ],
                // Test 02 - Organization id as null will return empty result array
                [
                    null,
                    []
                ],
                // Test 03 - Invalid organization will return empty result array
                [
                    -1,
                    []
                ],

            ]
        ]);
    }

    public function testGetFacultyPermission()
    {
        $this->specify("Verify the functionality of the method getFacultyPermission", function ($organizationId, $facultyId, $expectedResults, $expectedCount) {
            $results = $this->orgCourseFacultyRepository->getFacultyPermission($organizationId, $facultyId);
            verify($results)->equals($expectedResults);
            verify(count($results))->equals($expectedCount);
        }, ["examples" =>
            [
                // Test 01 - Valid organization and faculty will return permissions for faculty
                [
                    99,
                    227447,
                    [
                        0 =>[
                            'permissionSetId' => "588",
                            'permissionsetName' => "CourseOnly",
                        ]
                    ],
                    1
                ],
                // Test 02 - Organization id as null and valid faculty will return empty result array
                [
                    null,
                    227447,
                    [],
                    0
                ],
                // Test 03 - Invalid organization and valid faculty will return empty result array
                [
                    -1,
                    227447,
                    [],
                    0
                ],
                // Test 04 - valid organization and invalid faculty will return empty result array
                [
                    99,
                    -227447,
                    [],
                    0
                ],
                // Test 05 - Organization and faculty as null will return empty result array
                [
                    null,
                    null,
                    [],
                    0
                ],
                // Test 06 - Valid for organization_id = 203 and faculty_id = 4883124 will return permissions for faculty
                [
                    203,
                    4883124,
                    [
                        0 =>[
                            'permissionSetId' => "1416",
                            'permissionsetName' => "CourseOnly",
                        ]
                    ],
                    1
                ]
            ]
        ]);
    }

    public function testGetFacultiesForCourse()
    {
        $this->specify("Verify the functionality of the method getFacultiesForCourse", function ($courseId, $expectedResults, $expectedCount) {
            $results = $this->orgCourseFacultyRepository->getFacultiesForCourse($courseId);
            verify($results)->equals($expectedResults);
            verify(count($results))->equals($expectedCount);
        }, ["examples" =>
            [
                // Test 01 - Valid course will return faculties for course
                [
                    4013,
                    [
                        0 =>[
                            'facultyId' => "227447",
                            'courseId' => 4013,
                        ]
                    ],
                    1
                ],
                // Test 02 - course id as null will return empty result array
                [
                    null,
                    [],
                    0
                ],
                // Test 03 - Invalid course id will return empty result array
                [
                    -1,
                    [],
                    0
                ]
            ]
        ]);
    }

    public function testGetCoursesForStaff()
    {
        $this->specify("Verify the functionality of the method getCoursesForStaff", function ($staffId, $currentDate, $expectedResults, $expectedCount) {
            $results = $this->orgCourseFacultyRepository->getCoursesForStaff($staffId, $currentDate);
            verify($results)->equals($expectedResults);
            verify(count($results))->equals($expectedCount);
        }, ["examples" =>
            [
                // Test 01 - Valid staff id and current date will return courses for staff
                [
                    227447,
                    new \DateTime('2015-09-17 10:24:12'),
                    [
                        0 =>[
                            'facultyId' => "227447",
                            'courseId' => 4013,
                        ],
                        1 =>[
                            'facultyId' => "227447",
                            'courseId' => 4027,
                        ],
                        2 =>[
                            'facultyId' => "227447",
                            'courseId' => 4043,
                        ],
                        3 =>[
                            'facultyId' => "227447",
                            'courseId' => 4057,
                        ]
                    ],
                    4
                ],
                // Test 02 - staff id as null and valid current date will return empty result array
                [
                    null,
                    new \DateTime('2015-09-17 10:24:12'),
                    [],
                    0
                ],
                // Test 03 - Invalid staff id and valid current date will return empty result array
                [
                    -1,
                    new \DateTime('2015-09-17 10:24:12'),
                    [],
                    0
                ],
                // Test 04 - Both faculty and date are null will return empty result array
                [
                    null,
                    null,
                    [],
                    0
                ],
                // Test 05 - Valid faculty and date is null will return empty result array
                [
                    227447,
                    null,
                    [],
                    0
                ],
                // Test 06 - Valid faculty and future date is passed in will return empty result array
                [
                    227447,
                    new \DateTime('2020-09-17 00:00:00'),
                    [],
                    0
                ],
                // Test 07 - Valid faculty and the date now() is passed in will return empty result array
                [
                    227447,
                    new \DateTime('now'),
                    [],
                    0
                ],
                //Test 08 - Valid but deleted faculty and valid date will return empty result array
                [
                    4799548,
                    new \DateTime('2014-07-07 07:15:15'),
                    [],
                    0
                ]
            ]
        ]);
    }

    public function testListFacultyCourses()
    {
        $this->specify("Verify the functionality of the method listFacultyCourses", function ($personId, $organizationId, $currentDate, $yearId, $expectedResults, $expectedCount) {
            $results = $this->orgCourseFacultyRepository->listFacultyCourses($personId, $organizationId, $currentDate, $yearId);
            verify(count($results))->equals($expectedCount);
            $results = array_slice($results, 0, 3);
            verify($results)->equals($expectedResults);
        }, ["examples" =>
            [
                // Test 01 - Valid person id, organization id, year id and current date will return courses for faculty
                [
                    227447,
                    99,
                    '2015-09-17 10:24:12',
                    '201516',
                    [
                        0 =>[
                            'course_id' => 4013,
                            'courseName' => 'Test Course',
                            'staff_permissionset_id' => 588,
                            'PermissionSet' => 'CourseOnly',
                            'course_section_id' => 'FA2015ABC10001',
                            'section_number' => '33',
                            'term_name' => 'Fall Semester',
                            'term_code' => 'Fall2015',
                            'academic_year' => '2015-2016 Academic Year',
                        ],
                        1 =>[
                            'course_id' => 4027,
                            'courseName' => 'Test Course',
                            'staff_permissionset_id' => 588,
                            'PermissionSet' => 'CourseOnly',
                            'course_section_id' => 'FA2015ABC10002',
                            'section_number' => '34',
                            'term_name' => 'Fall Semester',
                            'term_code' => 'Fall2015',
                            'academic_year' => '2015-2016 Academic Year',
                        ],
                        2 =>[
                            'course_id' => 4043,
                            'courseName' => 'Test Course',
                            'staff_permissionset_id' => 588,
                            'PermissionSet' => 'CourseOnly',
                            'course_section_id' => 'FA2015ABC10401',
                            'section_number' => '33',
                            'term_name' => 'Fall Semester',
                            'term_code' => 'Fall2015',
                            'academic_year' => '2015-2016 Academic Year',
                        ],
                    ],
                    7
                ],
                // Test 02 - Invalid person id and valid organization id, current date and year id will return empty result array
                [
                   -1,
                    99,
                    '2015-09-17 10:24:12',
                    '201516',
                    [],
                    0
                ],
                // Test 03 - Invalid organization id and valid person id and current date will return empty result array
                [
                    227447,
                    -1,
                    '2015-09-17 10:24:12',
                    '201516',
                    [],
                    0
                ],
                // Test 04 - Invalid year id and valid person id, organization id and current date will return empty result array
                [
                    227447,
                    99,
                    '2015-09-17 10:24:12',
                    '-1',
                    [],
                    0
                ],
                // Test 05 - All parameters as null will return empty result array
                [
                    null,
                    null,
                    null,
                    null,
                    [],
                    0
                ],
                // Test 06 - Person id as null and valid organization id, current date, year id will return empty result array
                [
                    null,
                    99,
                    '2015-09-17 10:24:12',
                    '201516',
                    [],
                    0
                ],
                // Test 07 - Organization id as null and valid person id, current date, year id will return empty result array
                [
                    227447,
                    null,
                    '2015-09-17 10:24:12',
                    '201516',
                    [],
                    0
                ],
                // Test 08 - Current date as null and valid person id, organization id, year id will return empty result array
                [
                    227447,
                    99,
                    null,
                    '201516',
                    [
                        0 =>[
                            'course_id' => 4013,
                            'courseName' => 'Test Course',
                            'staff_permissionset_id' => 588,
                            'PermissionSet' => 'CourseOnly',
                            'course_section_id' => 'FA2015ABC10001',
                            'section_number' => '33',
                            'term_name' => 'Fall Semester',
                            'term_code' => 'Fall2015',
                            'academic_year' => '2015-2016 Academic Year',
                        ],
                        1 =>[
                            'course_id' => 4027,
                            'courseName' => 'Test Course',
                            'staff_permissionset_id' => 588,
                            'PermissionSet' => 'CourseOnly',
                            'course_section_id' => 'FA2015ABC10002',
                            'section_number' => '34',
                            'term_name' => 'Fall Semester',
                            'term_code' => 'Fall2015',
                            'academic_year' => '2015-2016 Academic Year',
                        ],
                        2 =>[
                            'course_id' => 4043,
                            'courseName' => 'Test Course',
                            'staff_permissionset_id' => 588,
                            'PermissionSet' => 'CourseOnly',
                            'course_section_id' => 'FA2015ABC10401',
                            'section_number' => '33',
                            'term_name' => 'Fall Semester',
                            'term_code' => 'Fall2015',
                            'academic_year' => '2015-2016 Academic Year',
                        ],
                    ],
                    7
                ],
                // Test 09 - Year id as null and valid person id, organization id, current date return empty result array
                [
                    227447,
                    99,
                    '2015-09-17 10:24:12',
                    null,
                    [],
                    0
                ],
                // Test 10 - Faculty that is not in the given organization and valid organization id, current date, year id will return empty result array
                [
                    4883124,
                    99,
                    '2015-09-17 10:24:12',
                    '201516',
                    [],
                    0
                ],
                // Test 11 - Future date is given and valid person id, organization id, year id will return empty result array
                [
                    4883124,
                    99,
                    '2025-10-17 00:00:00',
                    '201516',
                    [],
                    0
                ],
                // Test 12 - Future year id is given and valid person id, organization id, current date will return empty result array
                [
                    4883124,
                    99,
                    '2015-09-17 10:24:12',
                    '202122',
                    [],
                    0
                ],
                // Test 13 - Date does not happen in the year id and valid person id, organization id, year id will return empty result array
                [
                    4883124,
                    99,
                    '2016-09-17 10:24:12',
                    '201516',
                    [],
                    0
                ],
            ]
        ]);
    }

    public function testGetPermissionsByFacultyCourse()
    {
        $this->specify("Verify the functionality of the method getPermissionsByFacultyCourse", function ($coursesSelected, $facultyId, $organizationId, $expectedResults, $expectedCount) {
            $results = $this->orgCourseFacultyRepository->getPermissionsByFacultyCourse($coursesSelected, $facultyId, $organizationId);
            verify($results)->equals($expectedResults);
            verify(count($results))->equals($expectedCount);
        }, ["examples" =>
            [
                // Test 01 - Valid courses, faculty id and organization id will return permissions for faculty
                [
                    '4013, 4012',
                    227447,
                    99,
                    [
                        0 =>[
                            'org_permissionset_id' => "588",
                        ]
                    ],
                    1
                ],
                // Test 02 - Invalid courses and valid faculty id and organization id will return empty result array
                [
                    '-4013, -4012',
                    227447,
                    99,
                    [],
                    0
                ],
                // Test 03 - invalid faculty id and valid courses and organization id will return empty result array
                [
                    '4013, 4012',
                    -227447,
                    99,
                    [],
                    0
                ],
                // Test 04 - invalid organization id and valid courses and faculty id will return empty result array
                [
                    '4013, 4012',
                    227447,
                    -99,
                    [],
                    0
                ],
                // Test 05 - Course, faculty id and organization id as null will return empty result array
                [
                    null,
                    null,
                    null,
                    [],
                    0
                ],
                // Test 06 - Course as null and valid faculty id and organization id will return empty result array
                [
                    null,
                    227447,
                    99,
                    [],
                    0
                ],
                // Test 07 - Faculty id as null and valid course and organization id will return empty result array
                [
                    '4013, 4012',
                    null,
                    99,
                    [],
                    0
                ],
                // Test 08 - Organization id as null and valid course and faculty id will return empty result array
                [
                    '4013, 4012',
                    227447,
                    null,
                    [],
                    0
                ],
                // Test 09 - Faculty that are in multiple courses will return permissions for faculty
                [
                    '5082,5106,5109,5111,5133,5137,160531',
                    113764,
                    99,
                    [
                        0 =>[
                            'org_permissionset_id' => "588",
                        ]
                    ],
                    1
                ],
                // Test 10 - Faculty that is not in given courses will return empty result array
                [
                    '5082,5106,5109,5111,5133,5137,160531',
                    227447,
                    99,
                    [],
                    0
                ],
            ]
        ]);
    }

    public function testGetCourseFacultyCountByOrganization()
    {
        $this->specify("Verify the functionality of the method getCourseFacultyCountByOrganization", function ($organizationId, $expectedResults) {
            $results = $this->orgCourseFacultyRepository->getCourseFacultyCountByOrganization($organizationId);
            verify($results)->equals($expectedResults);
        }, ["examples" =>
            [
                // Test 01 - Valid organization id will return faculty count for the course based on organization
                [
                    99,
                    "564"
                ],
                // Test 02 - Valid organization id will return faculty count for the course based on organization
                [
                    203,
                    "40"
                ],
                // Test 03 - Invalid organization id will return 0 faculty count for the course based on organization
                [
                    -203,
                    "0"
                ],
                // Test 04 - Organization id as null will return 0 faculty count for the course based on organization
                [
                    null,
                    "0"
                ]
            ]
        ]);
    }
}
