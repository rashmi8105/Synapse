<?php
use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;

class OrgPermissionsetRepositoryTest extends Test
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
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
    }


    public function testGetPermissionSetsDataByUser()
    {
        $this->specify("Verify the functionality of the method getPermissionSetSataByUser", function ($userId, $expectedResults) {

            $results = $this->orgPermissionsetRepository->getPermissionSetsDataByUser($userId);

            // Verify the query result for profileBlocks, surveyBlocks and groups
            for ($i = 0; $i < count($expectedResults['profileBlocks']); $i++) {
                verify($results[0]['profileBlocks'][$i]['blockId'])->notEmpty();
                verify($results[0]['profileBlocks'][$i]['blockId'])->equals($expectedResults['profileBlocks'][$i]);
            }
            for ($i = 0; $i < count($expectedResults['surveyBlocks']); $i++) {
                verify($results[0]['surveyBlocks'][$i]['blockId'])->notEmpty();
                verify($results[0]['surveyBlocks'][$i]['blockId'])->equals($expectedResults['surveyBlocks'][$i]);
            }
            verify($results[0]['groups'])->equals($expectedResults['groups']);

        }, ["examples" =>
            [
                [4878750,
                    [
                        'profileBlocks' => [12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29],
                        'surveyBlocks' => [30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56],
                        'groups' => [369206 => "All Students"]
                    ]
                ],
                [4878751,
                    [
                        'profileBlocks' => [12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29],
                        'surveyBlocks' => [30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56],
                        'groups' => [369206 => "All Students"]
                    ]
                ],

            ]
        ]);
    }

    public function testGetDatablockInformationByPermissionsetId()
    {
        $this->specify("Verify the functionality of the method getPermissionSetDataByID", function ($permissionSetIds, $langId, $expectedResult) {

            $results = $this->orgPermissionsetRepository->getDatablockInformationByPermissionsetId($permissionSetIds, $langId);
            verify($results)->equals($expectedResult);
        }, ["examples" =>
            [
//                Example 1: Test getDatablockInformationByPermissionsetId for permissionset Id 100
                [100, 1,
                    [
                        [
                            'permissionTemplateId' => '100',
                            'organizationId' => '18',
                            'organizationLangId' => null,
                            'permissionTemplateName' => 'InputOnly',
                            'accessLevel' => [
                                'individualAndAggregate' => 1,
                                'aggregateOnly' => 0
                            ],
                            'coursesAccess' => [
                                'viewCourses' => '',
                                'createViewAcademicUpdate' => '',
                                'viewAllAcademicUpdateCourses' => '',
                                'viewAllFinalGrades' => ''
                            ],
                            'riskIndicator' => '1',
                            'intentToLeave' => '0',
                            'retentionCompletion' => '0',
                            'currentFutureIsq' => null,
                            'profileBlocks' => [
                                [
                                    'blockId' => 12,
                                    'blockName' => 'BasicStudentInfo',
                                    'blockSelection' => 1,
                                    'lastUpdated' => '2015-05-11 20:58:23'
                                ]
                            ],
                            'isp' => null,
                            'surveyBlocks' => [],
                            'features' => null,
                            'isq' => null,
                            'lastUpdated' => null,
                            'groups' => null
                        ]
                    ]
                ],
//                Example 2: Test getDatablockInformationByPermissionsetId for permissionset Id null
                [null, 1, []]
            ]
        ]);
    }


    // ToDo:  Find a way to test connections via courses.  There are only a few current courses, and the faculty in them are also in the All Students group.
    //  Also, it would be nice to be able to have a test that wouldn't expire.
    public function testCheckAccessToStudent()
    {
        $this->specify("Verify the functionality of the method checkAccessToStudent", function($facultyId, $studentId, $expectedResult){

            $result = $this->orgPermissionsetRepository->checkAccessToStudent($facultyId, $studentId);

            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                // Example 1: Faculty and student are both in group 370659 (Indoor Track and Field), where the faculty has permission set 1416, which has individual access.
                [4883106, 4879305, true],
                // Example 2: Faculty is in the parent group (370636, Stark Hall) of the student's group (370649, Stark 1); the faculty has permission set 1411, which has individual access.
                [4883175, 4879138, true],
                // Example 3: Faculty is in group 370659 (Indoor Track and Field), student is in groups 369206 and 370649 (All Students and Stark 1); these groups are unrelated.
                [4883106, 4879138, false],
                // Example 4: The only connection between this faculty and student is through an expired course (235882).
                [4883124, 4879776, false],
                // Example 5: Faculty and student are only connected via group 363075; this faculty member only has permission set 89, which only has aggregate access.
                [401375, 4747227, false],
                // Example 6: This faculty member has both an aggregate-only (101) and an individual (1323) permission set, but is only connected to this student via groups which have the aggregate permission set (parent group 352758).
                [178146, 151839, false]
            ]
        ]);
    }


    public function testDetermineWhetherUserHasIndividualAccess()
    {
        $this->specify("Verify the functionality of the method determineWhetherUserHasIndividualAccess", function($facultyId, $expectedResult){

            $result = $this->orgPermissionsetRepository->determineWhetherUserHasIndividualAccess($facultyId);

            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                // Example 1: This faculty member only has permission set 89, which is aggregate only.
                [401375, 0],
                // Example 2: This faculty member only has permission set 106, which gives individual access.
                [185246, 1],
                // Example 3: This faculty member has permission sets 101 and 1323, one of which gives individual access while the other is aggregate only.
                [178146, 1],
                // Example 4: This faculty member is not connected to any students.
                [4883124, null]
            ]
        ]);
    }


    public function testGetRiskAndIntentToLeavePermissions()
    {
        $this->specify("Verify the functionality of the method getRiskAndIntentToLeavePermissions", function($facultyId, $expectedResult){

            $result = $this->orgPermissionsetRepository->getRiskAndIntentToLeavePermissions($facultyId);

            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                // Example 1:  This faculty member only has permission set 106, which doesn't give permission to see risk or intent to leave.
                [185246,
                    [
                        'risk_indicator' => 0,
                        'intent_to_leave' => 0
                    ]
                ],
                // Example 2:  This faculty member only has permission set 537, which gives permission to see risk but not intent to leave.
                [1029244,
                    [
                        'risk_indicator' => 1,
                        'intent_to_leave' => 0
                    ]
                ],
                // Example 3:  This faculty member only has permission set 1246, which gives permission to see intent to leave but not risk.
                [143073,
                    [
                        'risk_indicator' => 0,
                        'intent_to_leave' => 1
                    ]
                ],
                // Example 4:  This faculty member has multiple permission sets; one of them gives permission to see risk and intent to leave.
                [161584,
                    [
                        'risk_indicator' => 1,
                        'intent_to_leave' => 1
                    ]
                ],
                // Example 5:  This faculty member only has permission set 89, which gives access to risk and intent to leave, but is aggregate only.
                [401375,
                    [
                        'risk_indicator' => null,
                        'intent_to_leave' => null
                    ]
                ],
                // Example 6:  This faculty member is not connected to any students.
                [4883124,
                    [
                        'risk_indicator' => null,
                        'intent_to_leave' => null
                    ]
                ]
            ]
        ]);
    }


    public function testGetCourseAndAcademicUpdatePermissions()
    {
        $this->specify("Verify the functionality of the method getCourseAndAcademicUpdatePermissions", function($facultyId, $expectedResult){

            $result = $this->orgPermissionsetRepository->getCourseAndAcademicUpdatePermissions($facultyId);

            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                // Example 1:  This faculty member only has permission set 103, which gives all course and academic update permissions.
                [132711,
                    [
                        'view_courses' => 1,
                        'create_view_academic_update' => 1,
                        'view_all_academic_update_courses' => 1,
                        'view_all_final_grades' => 1
                    ]
                ],
                // Example 2:  This faculty member only has permission set 1239, which has no course-related or academic-update-related permissions.
                [231441,
                    [
                        'view_courses' => 0,
                        'create_view_academic_update' => 0,
                        'view_all_academic_update_courses' => 0,
                        'view_all_final_grades' => 0
                    ]
                ],
                // Example 3:  This faculty member only has permission set 106, which gives permission to view courses but not academic updates.
                [185246,
                    [
                        'view_courses' => 1,
                        'create_view_academic_update' => 0,
                        'view_all_academic_update_courses' => 0,
                        'view_all_final_grades' => 0
                    ]
                ],
                // Example 4:  This faculty member only has permission set 1177, which gives permission to create and view academic updates for their own courses,
                // but not to view courses or other academic updates.
                [4842819,
                    [
                        'view_courses' => 0,
                        'create_view_academic_update' => 1,
                        'view_all_academic_update_courses' => 0,
                        'view_all_final_grades' => 0
                    ]
                ],
                // Example 5:  This faculty member only has permission set 519, which gives permission to view courses and to create and view academic updates for their own courses,
                // but not to view academic updates for other courses.
                [224518,
                    [
                        'view_courses' => 1,
                        'create_view_academic_update' => 1,
                        'view_all_academic_update_courses' => 0,
                        'view_all_final_grades' => 0
                    ]
                ],
                // Example 6:  This faculty member has permission sets 113 and 1202, both of which give permission to view courses and to view academic updates,
                // but not to create academic updates.  One of them allows viewing all final grades.
                [231532,
                    [
                        'view_courses' => 1,
                        'create_view_academic_update' => 0,
                        'view_all_academic_update_courses' => 1,
                        'view_all_final_grades' => 1
                    ]
                ]
            ]
        ]);
    }

    public function testGetStudentForStaff()
    {
        $this->specify("Verify the functionality of the method getStudentForStaff", function ($facultyId, $organizationId, $academicYearId, $studentIdArr, $resultSetCount) {
            $results = $this->orgPermissionsetRepository->getStudentsForStaff($facultyId, $academicYearId);
            $expectedStudentArrCount = 0;
            foreach ($studentIdArr as $studentId) {
                if (in_array($studentId, $results)) {
                    $expectedStudentArrCount++;
                }
            }
            verify($expectedStudentArrCount)->equals(count($studentIdArr));
            verify(count($results))->equals($resultSetCount);

        }, ["examples" =>
            [
                [4878750, 203, 158, [4878841,4878905], 998],
                [4878751, 203, 158, [4878841,4878905], 998],
                [4883106, 203, 158, [4878813,4878854,4879296,4879784,4879448], 50],
                [4883099, 203, 158, [4879839,4879854,4879863,4879886,4878845,4878868,4878909,4878932], 70],
                [4883097, 203, 158, [4879839,4879854,4879863,4879886,4878845,4878868,4878909,4878932,4878973], 140]
            ]
        ]);

    }

    public function testHasRetentionAndCompletionAccess(){

        $this->specify("Verify the functionality of the method hasRetentionAndCompletionAccess", function ($permissionSetIds , $expectedResult) {
            $result = $this->orgPermissionsetRepository->hasRetentionAndCompletionAccess($permissionSetIds);
            verify($result)->equals($expectedResult);;
        }, ["examples" =>
            [
                // retentionCompletion value for permission id 2 => 1 ,3 => 0
                [[2,3],true],
                [[2],true],
                [[3],false],
                [[],false]
            ]
        ]);
    }

    public function testGetRiskPermissionForFacultyAndStudents()
    {
        $this->specify("Verify the functionality of the method getRiskPermissionForFacultyAndStudents", function ($facultyId, $studentIds, $resultSet) {
            $results = $this->orgPermissionsetRepository->getRiskPermissionForFacultyAndStudents($facultyId, $studentIds);
            verify($results)->equals($resultSet);
        }, ["examples" =>
            [
                // get student risk mapping for faculty id 2
                [2, [5, 6, 7], [
                    5 => 1,
                    6 => 1,
                    7 => 1
                ]
                ],

                // no results for faculty id 3
                [3, [5, 6, 7], []],

                // no input students for faculty id 2, no results
                [2, [], []]
            ]
        ]);
    }

    public function hasRetentionAccessToStudents(){

        $this->specify("Verify the functionality of the method hasRetentionAccessToStudents", function ($facultyId, $studentIds, $expectedResult) {
            $result = $this->orgPermissionsetRepository->hasRetentionAccessToStudents($facultyId, $studentIds);
            verify($result)->equals($expectedResult);;
        }, ["examples" =>
            [
                //Faculty has access to students
                [2, [5, 6, 7, 8, 9, 10], [5, 6, 7, 8, 9, 10]],
                //Faculty does not have access to students
                [218, [5, 6, 7, 8, 9, 10], []],
                //faculty does have access to single student
                [2, [5], [5]],
                //Student Does not exist
                [2, [-1], []],
                //Faculty does not exist
                [null, [5, 6, 7, 8, 9, 10],[]],
                //Null for array, should never happen
                [null, null, []]
            ]
        ]);
    }
}