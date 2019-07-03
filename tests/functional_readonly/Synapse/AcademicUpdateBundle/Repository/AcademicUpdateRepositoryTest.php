<?php

use Codeception\TestCase\Test;

class AcademicUpdateRepositoryTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository
     */
    private $academicUpdateRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->academicUpdateRepository = $this->repositoryResolver->getRepository('SynapseAcademicUpdateBundle:AcademicUpdate');
    }


    public function testGetAcademicUpdateDetailsByIds()
    {

        $this->specify("Verify the functionality of the method getAcademicUpdateDetailsByIds", function ($academicUpdateIds, $expectedArrays) {
            $results = $this->academicUpdateRepository->getAcademicUpdateDetailsByIds($academicUpdateIds);

            foreach ($expectedArrays as $expectedArr) {
                verify($results)->contains($expectedArr);
            }
        }
            , [
                "examples" => [
                    [[3479299, 3479170, 3479169, 3479168, 3479167], [
                        [
                            'is_bypassed' => "",
                            'academic_update_id' => 3479167,
                            'student_id' => 4820227,
                            'student_firstname' => "Bryanna",
                            'student_lastname' => "Greene",
                            'academic_update_status' => "open",
                            'student_risk' => "",
                            'student_grade' => "",
                            'student_absences' => "",
                            'student_comments' => "",
                            'student_refer' => "",
                            'student_send' => "",
                            'course_section_id' => 20152020008,
                            'course_id' => 213559,
                            'subject_code' => "EIC",
                            'course_number' => 4007,
                            'course_name' => "Test Course",
                            'course_section_name' => 37,
                            'department_name' => "EIBN",
                            'academic_year_name' => "2015-2016 Academic Year",
                            'academic_term_name' => "Trad Spring 2016",
                            'student_status' => 1
                        ],
                        [
                            'is_bypassed' => "",
                            'academic_update_id' => 3479168,
                            'student_id' => 4820288,
                            'student_firstname' => "Desiree",
                            'student_lastname' => "Hopkins",
                            'academic_update_status' => "open",
                            'student_risk' => "",
                            'student_grade' => "",
                            'student_absences' => "",
                            'student_comments' => "",
                            'student_refer' => "",
                            'student_send' => "",
                            'course_section_id' => 20152020008,
                            'course_id' => 213559,
                            'subject_code' => "EIC",
                            'course_number' => 4007,
                            'course_name' => "Test Course",
                            'course_section_name' => 37,
                            'department_name' => "EIBN",
                            'academic_year_name' => "2015-2016 Academic Year",
                            'academic_term_name' => "Trad Spring 2016",
                            'student_status' => 1
                        ],
                        [
                            'is_bypassed' => "",
                            'academic_update_id' => 3479169,
                            'student_id' => 4820339,
                            'student_firstname' => "Lizbeth",
                            'student_lastname' => "Graves",
                            'academic_update_status' => "open",
                            'student_risk' => "",
                            'student_grade' => "",
                            'student_absences' => "",
                            'student_comments' => "",
                            'student_refer' => "",
                            'student_send' => "",
                            'course_section_id' => 20152020008,
                            'course_id' => 213559,
                            'subject_code' => "EIC",
                            'course_number' => 4007,
                            'course_name' => "Test Course",
                            'course_section_name' => 37,
                            'department_name' => "EIBN",
                            'academic_year_name' => "2015-2016 Academic Year",
                            'academic_term_name' => "Trad Spring 2016",
                            'student_status' => 1
                        ],
                        [
                            'is_bypassed' => "",
                            'academic_update_id' => 3479170,
                            'student_id' => 4820305,
                            'student_firstname' => "Isabela",
                            'student_lastname' => "Nunez",
                            'academic_update_status' => "open",
                            'student_risk' => "",
                            'student_grade' => "",
                            'student_absences' => "",
                            'student_comments' => "",
                            'student_refer' => "",
                            'student_send' => "",
                            'course_section_id' => 20152020008,
                            'course_id' => 213559,
                            'subject_code' => "EIC",
                            'course_number' => 4007,
                            'course_name' => "Test Course",
                            'course_section_name' => 37,
                            'department_name' => "EIBN",
                            'academic_year_name' => "2015-2016 Academic Year",
                            'academic_term_name' => "Trad Spring 2016",
                            'student_status' => 1
                        ]

                    ]]
                ]
            ]);
    }


    public function testGetStudentIdsForAcademicUpdate()
    {
        $this->specify(
            "Verify the functionality of the method getStudentIdsForAcademicUpdate",
            function ($organizationId, $academicUpdateRequestId, $expectedArray, $expectedResultCount) {
                $results = $this->academicUpdateRepository->getStudentIdsForAcademicUpdate(
                    $organizationId,
                    $academicUpdateRequestId
                );
                verify($results)->equals($expectedArray);
                verify(count($results))->equals($expectedResultCount);
            }
            ,
            [
                "examples" => [
                    [195, 2, [0 => 4613993, 1 => 4605393], 2],
                    [182, 1, [0 => 4543345, 1 => 4543346, 2 => 4543351], 3],
                    [195, 3, [0 => 4613993, 1 => 4605393], 2],
                ],
            ]
        );
    }


    public function testGetLatestAcademicUpdatesForCourse()
    {
        $this->specify(
            "Verify the functionality of the method getLatestAcademicUpdatesForCourse",
            function ($courseId, $organizationId, $studentIds, $isInternal, $expectedResult) {
                $results = $this->academicUpdateRepository->getLatestAcademicUpdatesForCourse(
                    $courseId, $organizationId, $studentIds, $isInternal
                );
                verify($results)->equals($expectedResult);
            }
            ,
            [
                "examples" => [
                    [
                        // return internal id of faulty and student when $isInternal = true
                        17515,
                        196,
                        [4627270],
                        true,
                        [
                            [
                                'student_id' => '4627270',
                                'faculty_id' => '4622490',
                                'course_id' => '2876',
                                'date_submitted' => '2015-09-17 22:08:08',
                                'failure_risk_level' => '',
                                'in_progress_grade' => '',
                                'absences' => '1',
                                'comment' => '',
                                'refer_for_assistance' => '',
                                'send_to_student' => '',
                                'final_grade' => '',
                                'academic_update_id' => '15145'
                            ]
                        ]
                    ],
                    [
                        // no academic updates for the course
                        17406,
                        196,
                        [],
                        false,
                        []
                    ],

                    [
                        // return external id for student and faculty when $isInternal = false
                        189747,
                        196,
                        [4874541, 4627110],
                        false,
                        [
                            [
                                'student_id' => '4874541',
                                'faculty_id' => '4622579',
                                'course_id' => '5022',
                                'date_submitted' => '2016-03-29 12:35:15',
                                'failure_risk_level' => 'Low',
                                'in_progress_grade' => '',
                                'absences' => '',
                                'comment' => '',
                                'refer_for_assistance' => '',
                                'send_to_student' => '',
                                'final_grade' => '',
                                'academic_update_id' => '1958398',
                            ],
                            [
                                'student_id' => '4627110',
                                'faculty_id' => '4622579',
                                'course_id' => '5022',
                                'date_submitted' => '2016-03-29 12:35:15',
                                'failure_risk_level' => 'Low',
                                'in_progress_grade' => '',
                                'absences' => '1',
                                'comment' => '',
                                'refer_for_assistance' => '',
                                'send_to_student' => '',
                                'final_grade' => '',
                                'academic_update_id' => '1958135'
                            ]
                        ]
                    ],
                    [
                        // Passing incorrect course id
                        -878,
                        196,
                        '',
                        true,
                        []
                    ],
                    [
                        // return list of all academic updates for a course
                        17494,
                        196,
                        '',
                        true,
                        [
                            [
                                'student_id' => '4627115',
                                'faculty_id' => '4622574',
                                'course_id' => '2870',
                                'date_submitted' => '2015-09-18 23:15:43',
                                'failure_risk_level' => 'Low',
                                'in_progress_grade' => '',
                                'absences' => '',
                                'comment' => '',
                                'refer_for_assistance' => '',
                                'send_to_student' => '',
                                'final_grade' => '',
                                'academic_update_id' => '18136'
                            ],
                            [
                                'student_id' => '4627257',
                                'faculty_id' => '4622574',
                                'course_id' => '2870',
                                'date_submitted' => '2015-09-18 23:15:43',
                                'failure_risk_level' => 'Low',
                                'in_progress_grade' => '',
                                'absences' => '',
                                'comment' => '',
                                'refer_for_assistance' => '',
                                'send_to_student' => '',
                                'final_grade' => '',
                                'academic_update_id' => '17649'
                            ],
                            [
                                'student_id' => '4627393',
                                'faculty_id' => '4622574',
                                'course_id' => '2870',
                                'date_submitted' => '2015-09-18 23:15:43',
                                'failure_risk_level' => 'Low',
                                'in_progress_grade' => '',
                                'absences' => '1',
                                'comment' => '',
                                'refer_for_assistance' => '',
                                'send_to_student' => '',
                                'final_grade' => '',
                                'academic_update_id' => '16312'
                            ]
                        ]

                    ],
                    [
                        // Passing incorrect organization id
                        189747,
                        -345,
                        '',
                        true,
                        []
                    ],
                    [
                        // all students belongs to that course specific to organization, not passing student ids
                        17515,
                        196,
                        '',
                        true,
                        [
                            [
                                'student_id' => '4627270',
                                'faculty_id' => '4622490',
                                'course_id' => '2876',
                                'date_submitted' => '2015-09-17 22:08:08',
                                'failure_risk_level' => '',
                                'in_progress_grade' => '',
                                'absences' => '1',
                                'comment' => '',
                                'refer_for_assistance' => '',
                                'send_to_student' => '',
                                'final_grade' => '',
                                'academic_update_id' => '15145'
                            ]
                        ]
                    ]
                ],
            ]
        );
    }


    public function testGetAcademicUpdateStudentHistory()
    {
        $this->specify("Get the student's academic update history", function ($organizationId, $courseId, $studentId, $orgAcademicYearId, $expectedResults) {
            $results = $this->academicUpdateRepository->getAcademicUpdateStudentHistory($organizationId, $courseId, $studentId, $orgAcademicYearId);
            verify($results)->equals($expectedResults);
        }, ['examples' => [
            //Student with one academic update history record in one course
            [
                84,
                185582,
                4874411,
                68,
                [
                    0 => [
                        'id' => 2050669,
                        'failure_risk_level' => 'High',
                        'update_date' => '2016-01-29 03:09:18',
                        'grade' => null,
                        'absence' => null,
                        'comment' => 'Poor attendance.',
                        'refer_for_assistance' => '1',
                        'send_to_student' => '1'
                    ]
                ]
            ],
            //Invalid organization ID
            [
                null,
                185582,
                4874411,
                68,
                [

                ]
            ],
            //Invalid course ID
            [
                84,
                null,
                4874411,
                68,
                [

                ]
            ],
            //Invalid student ID
            [
                84,
                185582,
                null,
                68,
                [

                ]
            ],
            //Invalid org academic year ID
            [
                84,
                185582,
                4874411,
                null,
                [

                ]
            ]
        ]]);
    }

    public function testGetAcademicUpdatesByOrg()
    {
        $this->specify("Get Academic Updates By Org", function ($organizationId, $expectedResults, $expectedCount) {
            $results = $this->academicUpdateRepository->getAcademicUpdatesByOrg($organizationId);
            verify(count($results))->equals($expectedCount);
            $validateResults = array_slice($results, 0, 2);
            verify($validateResults)->equals($expectedResults);
        }, ['examples' => [
            // Test0: Valid Data
            [
                197,
                [
                    0 => [
                        'UniqueCourseSectionID' => '1234A',
                        'StudentID' => '4802294',
                        'FailureRisk' => 'High',
                        'Absences' => null,
                        'Comments' => 'This is a test to see if shows up in the report',
                        'SentToStudent' => null,
                        'FinalGrade' => null,
                        'InProgressGrade' => 'A',
                        'referForAssistance' => null
                    ],
                    1 => [
                        'UniqueCourseSectionID' => '1234A',
                        'StudentID' => '4802294',
                        'FailureRisk' => null,
                        'Absences' => null,
                        'Comments' => null,
                        'SentToStudent' => null,
                        'FinalGrade' => null,
                        'InProgressGrade' => null,
                        'referForAssistance' => null
                    ]
                ],
                2
            ],
            // Test1: Valid data
            [
                204,
                [
                    0 => [
                        'UniqueCourseSectionID' => '19304',
                        'StudentID' => '4893126',
                        'FailureRisk' => 'Low',
                        'Absences' => 21,
                        'Comments' => 'Excellent',
                        'SentToStudent' => 1,
                        'FinalGrade' => null,
                        'InProgressGrade' => 'A',
                        'referForAssistance' => 1
                    ],
                    1 => [
                        'UniqueCourseSectionID' => '19304',
                        'StudentID' => '4893126',
                        'FailureRisk' => 'High',
                        'Absences' => 6,
                        'Comments' => 'Bad',
                        'SentToStudent' => 1,
                        'FinalGrade' => null,
                        'InProgressGrade' => 'P',
                        'referForAssistance' => 1
                    ]
                ],
                7
            ],
            // Test2: Invalid organization ID
            [
                -1,
                [],
                0
            ],
            // Test3: Organization ID as null will return empty result array
            [
                null,
                [],
                0
            ]
        ]]);
    }

    public function testGetAUFacultyAssigned()
    {
        $this->specify("get AU Faculty Assigned", function ($userType, $orgId, $facultyId, $expectedResults) {
            $results = $this->academicUpdateRepository->getAUFacultyAssigned($userType, $orgId, $facultyId);
            verify($results)->equals($expectedResults);
        }, ['examples' => [
            // Test0: userType is coordinator
            [
                'coordinator',
                204,
                -1,
                [
                    0 => [
                        'requestId' => 258,
                        'firstname' => 'Nixon',
                        'lastname' => 'Freeman'
                    ],
                    1 => [
                        'requestId' => 257,
                        'firstname' => 'Vihaan',
                        'lastname' => 'Ramos'
                    ]
                ]
            ],
            // Test1: userType is faculty
            [
                'faculty',
                204,
                4893111,
                [
                    0 => [
                        'requestId' => 258,
                        'firstname' => 'Nixon',
                        'lastname' => 'Freeman'
                    ]
                ]
            ],
            // Test2: Invalid organization ID
            [
                'faculty',
                -1,
                100,
                []
            ]
        ]]);
    }

    public function testGetAssingedFacultyInfoByRequest()
    {
        $this->specify("get assigned Faculty Info By Request", function ($academicRequestId, $expectedResults) {
            $results = $this->academicUpdateRepository->getAssingedFacultyInfoByRequest($academicRequestId);
            $validateResults = array_slice($results, 0, 2);
            verify($validateResults)->equals($expectedResults);
        }, ['examples' => [
            // Test0: au request is for org 202 - 250
            [
                250,
                [
                    0 => [
                        'personid' => 4892870,
                        'firstname' => 'Jaylen',
                        'lastname' => 'Moyer'
                    ]
                ]
            ],
            // Test1: au request is for org 182
            [
                150,
                [
                    0 => [
                        'personid' => 182027,
                        'firstname' => 'Liliana',
                        'lastname' => 'Walker'
                    ],
                    1 => [
                        'personid' => 256123,
                        'firstname' => 'Adrianna',
                        'lastname' => 'Ford'
                    ]
                ]
            ],
            // Test2: Invalid AU ID
            [
                -1,
                []
            ]
        ]]);
    }

    public function testGetAcademicUpdateUploadCount()
    {
        $this->specify("get Academic Update Upload Count", function ($academicRequestId, $expectedResults) {
            $results = $this->academicUpdateRepository->getAcademicUpdateUploadCount($academicRequestId);
            verify($results)->equals($expectedResults);
        }, ['examples' => [
            // Test0: au request is for org 202
            [
                202,
                1
            ],
            // Test1: au request is for org 182
            [
                182,
                323
            ],
            // Test2: Invalid Org Id
            [
                -1,
                0
            ]
        ]]);
    }

    public function testGetAssignedFacultiesByAcademicUpdate()
    {
        $this->specify("get Assigned Faculties By Academic Update", function ($academicUpdateId, $personId, $expectedResults) {
            $results = $this->academicUpdateRepository->getAssignedFacultiesByAcademicUpdate($academicUpdateId, $personId);
            verify($results)->equals($expectedResults);
        }, ['examples' => [
            // Test0: Valid data
            [
                77,
                4614703,
                [
                    0 => [
                        'person_id' => '4614703'
                    ]
                ]
            ],
            // Test1: Invalid AU
            [
                -1,
                4614703,
                []
            ],
            // Test2: Invalid Person Id
            [
                -1,
                -1,
                []
            ],
        ]]);
    }


    public function testGetAcademicUpdateRequestsByUser()
    {
        $this->specify(
            "Verify the functionality of the method getAcademicUpdateRequestsByUser",
            function ($userType, $orgId, $currentDate, $facultyId, $filters, $expectedResults) {
                $results = $this->academicUpdateRepository->getAcademicUpdateRequestsByUser($userType, $orgId, $currentDate, $facultyId, $filters);

                if ($filters['filter'] == "") {
                    foreach ($expectedResults as $expectedResult) {
                        $this->assertContains($expectedResult, $results);
                    }
                } else {
                    $this->assertEquals($expectedResults, $results);
                }

            }
            ,
            [
                "examples" => [
                    //Faculty User Type With Nothing submitted but status is closed - No Filter
                    ['faculty',
                        68,
                        '2017-11-09 16:35:05',
                        4737711,
                        [
                            "filter" => "",
                            "request" => ""
                        ],
                        [
                            [
                                "requestId" => 241,
                                "name" => "Some AU Name",
                                "description" => "This description is useless",
                                "requestCreated" => "2016-03-21 20:32:58",
                                "totalUpdates" => 1,
                                "requestDue" => "2016-03-23 23:59:59",
                                "completedTotal" => 0,
                                "status" => "open",
                                "requesterId" => 232178,
                                "requesterFirst" => "Nina",
                                "requesterLast" => "Weaver",
                                "pastDueDate" => 1
                            ],
                            [
                                "requestId" => 240,
                                "name" => "Some AU Name",
                                "description" => "This description is useless",
                                "requestCreated" => "2016-03-21 20:31:32",
                                "totalUpdates" => 3,
                                "requestDue" => "2016-03-23 23:59:59",
                                "completedTotal" => 1,
                                "status" => "open",
                                "requesterId" => 232178,
                                "requesterFirst" => "Nina",
                                "requesterLast" => "Weaver",
                                "pastDueDate" => 1
                            ],
                            [
                                "requestId" => 235,
                                "name" => "Some AU Name",
                                "description" => "This description is useless",
                                "requestCreated" => "2016-03-18 18:02:02",
                                "totalUpdates" => 4,
                                "requestDue" => "2016-03-23 23:59:59",
                                "completedTotal" => 4,
                                "status" => "closed",
                                "requesterId" => 232178,
                                "requesterFirst" => "Nina",
                                "requesterLast" => "Weaver",
                                "pastDueDate" => 1
                            ]
                        ]
                    ],
                    // Coordinator User Type With Status = Closed but mostly blank AUs - No Filter Covers ESPRJ-16501
                    ['coordinator',
                        68,
                        '2017-11-09 16:35:05',
                        232178,
                        [
                            "filter" => "",
                            "request" => ""
                        ],
                        [
                            [
                                "requestId" => 235,
                                "name" => "Some AU Name",
                                "description" => "This description is useless",
                                "requestCreated" => "2016-03-18 18:02:02",
                                "totalUpdates" => 44,
                                "requestDue" => "2016-03-23 23:59:59",
                                "completedTotal" => 43,
                                "status" => "closed",
                                "requesterId" => 232178,
                                "requesterFirst" => "Nina",
                                "requesterLast" => "Weaver",
                                "pastDueDate" => 1
                            ],
                            [
                                "requestId" => 196,
                                "name" => "Some AU Name",
                                "description" => "This description is useless",
                                "requestCreated" => "2016-02-10 18:12:34",
                                "totalUpdates" => 5,
                                "requestDue" => "2016-02-18 23:59:59",
                                "completedTotal" => 5,
                                "status" => "closed",
                                "requesterId" => 232178,
                                "requesterFirst" => "Nina",
                                "requesterLast" => "Weaver",
                                "pastDueDate" => 1
                            ],
                            [
                                "requestId" => 195,
                                "name" => "Some AU Name",
                                "description" => "This description is useless",
                                "requestCreated" => "2016-02-10 15:28:28",
                                "totalUpdates" => 6,
                                "requestDue" => "2016-02-18 23:59:59",
                                "completedTotal" => 5,
                                "status" => "open",
                                "requesterId" => 232178,
                                "requesterFirst" => "Nina",
                                "requesterLast" => "Weaver",
                                "pastDueDate" => 1
                            ]

                        ]
                    ],
                    // Coordinator User Type With Status = Closed but mostly blank AUs - Getting All with "all" filter
                    ['coordinator',
                        68,
                        '2017-11-09 16:35:05',
                        232178,
                        [
                            "filter" => "",
                            "request" => "all"
                        ],
                        [
                            [
                                "requestId" => 235,
                                "name" => "Some AU Name",
                                "description" => "This description is useless",
                                "requestCreated" => "2016-03-18 18:02:02",
                                "totalUpdates" => 44,
                                "requestDue" => "2016-03-23 23:59:59",
                                "completedTotal" => 43,
                                "status" => "closed",
                                "requesterId" => 232178,
                                "requesterFirst" => "Nina",
                                "requesterLast" => "Weaver",
                                "pastDueDate" => 1
                            ],
                            [
                                "requestId" => 196,
                                "name" => "Some AU Name",
                                "description" => "This description is useless",
                                "requestCreated" => "2016-02-10 18:12:34",
                                "totalUpdates" => 5,
                                "requestDue" => "2016-02-18 23:59:59",
                                "completedTotal" => 5,
                                "status" => "closed",
                                "requesterId" => 232178,
                                "requesterFirst" => "Nina",
                                "requesterLast" => "Weaver",
                                "pastDueDate" => 1
                            ],
                            [
                                "requestId" => 195,
                                "name" => "Some AU Name",
                                "description" => "This description is useless",
                                "requestCreated" => "2016-02-10 15:28:28",
                                "totalUpdates" => 6,
                                "requestDue" => "2016-02-18 23:59:59",
                                "completedTotal" => 5,
                                "status" => "open",
                                "requesterId" => 232178,
                                "requesterFirst" => "Nina",
                                "requesterLast" => "Weaver",
                                "pastDueDate" => 1
                            ],
                            [
                                "requestId" => 90,
                                "name" => "Some AU Name",
                                "description" => "This description is useless",
                                "requestCreated" => "2015-10-06 18:33:11",
                                "totalUpdates" => 2,
                                "requestDue" => "2015-10-13 18:33:11",
                                "completedTotal" => 2,
                                "status" => "closed",
                                "requesterId" => 202267,
                                "requesterFirst" => "Aylin",
                                "requesterLast" => "Bowman",
                                "pastDueDate" => 1
                            ],


                        ]
                    ],
                    // Coordinator User Type With Status = Closed but mostly blank AUs - Filtering by AU request name
                    ['coordinator',
                        68,
                        '2017-11-09 16:35:05',
                        232178,
                        [
                            "filter" => "Different AU Name",
                            "request" => "all"
                        ],
                        [
                            [
                                "requestId" => 253,
                                "name" => "Different AU Name",
                                "description" => "This description is useless",
                                "requestCreated" => "2016-04-15 20:44:55",
                                "totalUpdates" => 5,
                                "requestDue" => "2016-04-22 23:59:59",
                                "completedTotal" => 4,
                                "status" => "open",
                                "requesterId" => 232178,
                                "requesterFirst" => "Nina",
                                "requesterLast" => "Weaver",
                                "pastDueDate" => 1
                            ]
                        ]

                    ]

                ]
            ]);
    }

    public function testGetFacultyWithIncompleteAcademicUpdatesInRequest()
    {
        $this->specify("testGetFacultyWithIncompleteAcademicUpdatesInRequest", function ($expectedResults, $organizationId = null, $requestId = null, $currentDate = null) {
            $results = $this->academicUpdateRepository->getFacultyWithIncompleteAcademicUpdatesInRequest($organizationId, $requestId, $currentDate);
            verify($results)->equals($expectedResults);
        }, [
            'examples' =>
                [
                    //Request with faculty that haven't completed all of their academic updates in the request
                    [
                        [
                            0 =>
                                [
                                    'person_id_faculty_assigned' => '152059',
                                    'faculty_firstname' => 'Mariah',
                                    'faculty_lastname' => 'Cook',
                                    'faculty_email' => 'MapworksBetaUser00152059@mailinator.com',
                                    'total_updates' => '1',
                                    'request_name' => 'Some AU Name',
                                    'request_due_date' => '2015-10-19 06:16:46',
                                    'request_description' => 'This description is useless',
                                    'requester_person_id' => '202267',
                                    'request_email_subject' => 'This subject is useless',
                                    'request_email_optional_message' => 'Email optional message',
                                    'requester_firstname' => 'Aylin',
                                    'requester_lastname' => 'Bowman',
                                    'requester_email' => 'MapworksBetaUser00202267@mailinator.com'
                                ],
                            1 =>
                                [
                                    'person_id_faculty_assigned' => '152073',
                                    'faculty_firstname' => 'Isla',
                                    'faculty_lastname' => 'Richardson',
                                    'faculty_email' => 'MapworksBetaUser00152073@mailinator.com',
                                    'total_updates' => '1',
                                    'request_name' => 'Some AU Name',
                                    'request_due_date' => '2015-10-19 06:16:46',
                                    'request_description' => 'This description is useless',
                                    'requester_person_id' => '202267',
                                    'request_email_subject' => 'This subject is useless',
                                    'request_email_optional_message' => 'Email optional message',
                                    'requester_firstname' => 'Aylin',
                                    'requester_lastname' => 'Bowman',
                                    'requester_email' => 'MapworksBetaUser00202267@mailinator.com'
                                ],
                            2 =>
                                [
                                    'person_id_faculty_assigned' => '152368',
                                    'faculty_firstname' => 'Dahlia',
                                    'faculty_lastname' => 'Griffith',
                                    'faculty_email' => 'MapworksBetaUser00152368@mailinator.com',
                                    'total_updates' => '1',
                                    'request_name' => 'Some AU Name',
                                    'request_due_date' => '2015-10-19 06:16:46',
                                    'request_description' => 'This description is useless',
                                    'requester_person_id' => '202267',
                                    'request_email_subject' => 'This subject is useless',
                                    'request_email_optional_message' => 'Email optional message',
                                    'requester_firstname' => 'Aylin',
                                    'requester_lastname' => 'Bowman',
                                    'requester_email' => 'MapworksBetaUser00202267@mailinator.com',
                                ],
                        ],
                        68,
                        104,
                        '2015-09-01 00:00:00'
                    ],
                    //Invalid org
                    [
                        [],
                        null,
                        104,
                        '2017-01-01 00:00:00'
                    ],
                    //Invalid request ID
                    [
                        [],
                        68,
                        null,
                        '2017-01-01 00:00:00'
                    ],
                    //Invalid datetime
                    [
                        [],
                        68,
                        104
                    ]
                ]
        ]);
    }

}