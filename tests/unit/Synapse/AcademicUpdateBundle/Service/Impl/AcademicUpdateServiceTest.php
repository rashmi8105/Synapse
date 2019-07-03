<?php

use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use \Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\AcademicUpdateBundle\Entity\AcademicRecord;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdate;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest;
use Synapse\AcademicUpdateBundle\EntityDto\CourseAdhocAcademicUpdateDTO;
use Synapse\AcademicUpdateBundle\EntityDto\IndividualAcademicUpdateDTO;
use Synapse\AcademicUpdateBundle\EntityDto\StudentAcademicUpdatesDTO;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestRepository;
use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateCreateService;
use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateService;
use \Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Entity\Person;
use \Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;

use Synapse\CoreBundle\Exception\SynapseValidationException;

class AcademicUpdateServiceTest extends \Codeception\Test\Unit
{
    use\Codeception\Specify;

    public function testGetLatestCourseAcademicUpdates()
    {
        $this->specify("Test to Get latest academic updates for a specific course/students", function ($courseId, $organizationId, $studentIds, $academicUpdates, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockAcademicUpdateRepository = $this->getMock('AcademicUpdateRepository', array('getLatestAcademicUpdatesForCourse'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        AcademicUpdateRepository::REPOSITORY_KEY,
                        $mockAcademicUpdateRepository
                    ]
                ]);

            $mockAcademicUpdateRepository->method('getLatestAcademicUpdatesForCourse')->willReturn($academicUpdates);

            $academicUpdateService = new AcademicUpdateService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $academicUpdateService->getLatestCourseAcademicUpdates($courseId, $organizationId, $studentIds);
            $this->assertEquals($results, $expectedResult);
        }, [
            'examples' => [
                [
                    // Course with academic updates for specific students
                    '100401',
                    203,
                    [4950084],
                    [
                        [
                            'student_id' => '4950084',
                            'faculty_id' => '4878750',
                            'course_id' => '100401',
                            'date_submitted' => '2016-09-26 15:27:35',
                            'failure_risk_level' => 'Low',
                            'in_progress_grade' => 'A',
                            'absences' => 6,
                            'comment' => 'Kurt is a good student',
                            'refer_for_assistance' => 0,
                            'send_to_student' => 1,
                            'final_grade' => '',
                            'academic_update_id' => '4748273'
                        ]
                    ],
                    $this->setAcademicUpdates([4950084])
                ],
                [
                    // Course with no academic updates for all students in the organization
                    235919,
                    203,
                    [],
                    [],
                    []
                ],
                [
                    // Course with academic updates for all students
                    '100401',
                    203,
                    [],
                    [
                        [
                            'student_id' => '4897394',
                            'faculty_id' => '4878750',
                            'course_id' => '100401',
                            'date_submitted' => '2016-09-26 15:37:00',
                            'failure_risk_level' => 'Low',
                            'in_progress_grade' => 'B',
                            'absences' => 1,
                            'comment' => 'test email',
                            'refer_for_assistance' => 0,
                            'send_to_student' => 1,
                            'final_grade' => '',
                            'academic_update_id' => '4748331'
                        ],
                        [
                            'student_id' => '4950084',
                            'faculty_id' => '4878750',
                            'course_id' => '100401',
                            'date_submitted' => '2016-09-26 15:27:35',
                            'failure_risk_level' => 'Low',
                            'in_progress_grade' => 'A',
                            'absences' => 6,
                            'comment' => 'Kurt is a good student',
                            'refer_for_assistance' => 0,
                            'send_to_student' => 1,
                            'final_grade' => '',
                            'academic_update_id' => '4748273'
                        ]
                    ],
                    $this->setAcademicUpdates([4897394, 4950084])
                ],
                [
                    // Course with no academic updates for specific students in the organization
                    235873,
                    203,
                    [
                        4878853,
                        4878865,
                        4878910,
                        4878947
                    ],
                    [],
                    []
                ],
            ]
        ]);
    }

    private function setAcademicUpdates($studentIds)
    {
        $academicUpdates = [
            '4950084' => [
                'course_id' => '100401',
                'student_id' => '4950084',
                'faculty_id' => '4878750',
                'date_submitted' => '2016-09-26 15:27:35',
                'in_progress_grade' => 'A',
                'absences' => 6,
                'comment' => 'Kurt is a good student',
                'refer_for_assistance' => 0,
                'send_to_student' => 1,
                'final_grade' => '',
                'academic_update_id' => '',
                'failure_risk_level' => 'Low',
            ],
            '4897394' => [
                'student_id' => '4897394',
                'faculty_id' => '4878750',
                'course_id' => '100401',
                'date_submitted' => '2016-09-26 15:37:00',
                'failure_risk_level' => 'Low',
                'in_progress_grade' => 'B',
                'absences' => 1,
                'comment' => 'test email',
                'refer_for_assistance' => 0,
                'send_to_student' => 1,
                'final_grade' => '',
                'academic_update_id' => ''
            ],
            'invalid' => [
                'student_id' => 'invalid',
                'faculty_id' => '4878750',
                'course_id' => '100401',
                'date_submitted' => '2016-09-26 15:37:00',
                'failure_risk_level' => 'Low',
                'in_progress_grade' => 'B',
                'absences' => 1,
                'comment' => 'test email',
                'refer_for_assistance' => 0,
                'send_to_student' => 1,
                'final_grade' => '',
                'academic_update_id' => ''
            ],
            'invalidfaculty' => [
                'student_id' => '4897394',
                'faculty_id' => 'invalid',
                'course_id' => '100401',
                'date_submitted' => '2016-09-26 15:37:00',
                'failure_risk_level' => 'Low',
                'in_progress_grade' => 'B',
                'absences' => 1,
                'comment' => 'test email',
                'refer_for_assistance' => 0,
                'send_to_student' => 1,
                'final_grade' => '',
                'academic_update_id' => ''
            ],
            'invalidCourse' => [
                'student_id' => '4897394',
                'faculty_id' => '4878750',
                'course_id' => 'invalidCourse',
                'date_submitted' => '2016-09-26 15:37:00',
                'failure_risk_level' => 'Low',
                'in_progress_grade' => 'B',
                'absences' => 1,
                'comment' => 'test email',
                'refer_for_assistance' => 0,
                'send_to_student' => 1,
                'final_grade' => '',
                'academic_update_id' => ''
            ]
        ];
        $courseAdhocAcademicUpdateDTO = new CourseAdhocAcademicUpdateDTO();
        foreach ($studentIds as $student) {
            $courseAdhocAcademicUpdateDTO->setCourseId($academicUpdates[$student]['course_id']);
            $studentAcademicUpdatesDTO = new StudentAcademicUpdatesDTO;
            $studentAcademicUpdatesDTO->setStudentId($academicUpdates[$student]['student_id']);
            $individualAcademicUpdateDTOs = [];
            $individualAcademicUpdateDTO = new IndividualAcademicUpdateDTO;
            $individualAcademicUpdateDTO->setFacultyIdSubmitted($academicUpdates[$student]['faculty_id']);
            $submittedDate = new \DateTime($academicUpdates[$student]['date_submitted']);
            $individualAcademicUpdateDTO->setDateSubmitted($submittedDate);
            $individualAcademicUpdateDTO->setFailureRiskLevel($academicUpdates[$student]['failure_risk_level']);
            $individualAcademicUpdateDTO->setInProgressGrade($academicUpdates[$student]['in_progress_grade']);
            $individualAcademicUpdateDTO->setAbsences($academicUpdates[$student]['absences']);
            $individualAcademicUpdateDTO->setComment($academicUpdates[$student]['comment']);
            $individualAcademicUpdateDTO->setReferForAssistance($academicUpdates[$student]['refer_for_assistance']);
            $individualAcademicUpdateDTO->setSendToStudent($academicUpdates[$student]['send_to_student']);
            $individualAcademicUpdateDTO->setAcademicUpdateId($academicUpdates[$student]['academic_update_id']);
            $individualAcademicUpdateDTOs[] = $individualAcademicUpdateDTO;

            $studentAcademicUpdatesDTO->setAcademicUpdates($individualAcademicUpdateDTOs);
            $studentAcademicUpdatesDTOs[] = $studentAcademicUpdatesDTO;
        }
        $courseAdhocAcademicUpdateDTO->setStudentsWithAcademicUpdates($studentAcademicUpdatesDTOs);
        return $courseAdhocAcademicUpdateDTO;
    }


    public function testBuildAcademicUpdateObject()
    {

        $this->specify("build academic update object", function ($academicUpdate, $facultyPersonObject, $courseObject, $studentPersonObject, $loggedInUserObject, $academicUpdateRequest, $updateType, $isUpload, $isAdhoc) {

            $this->validStudents = [
                4897394,
                4950084,
            ];

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));


            $mockAcademicUpdateRepository = $this->getMock('AcademicUpdateRepository', ['persist']);


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [AcademicUpdateRepository::REPOSITORY_KEY, $mockAcademicUpdateRepository]

                ]);

            $mockContainer->method('get')->willReturn(1);
            $academicUpdateService = new AcademicUpdateService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $academicUpdateService->buildAcademicUpdateObject($academicUpdate, $facultyPersonObject, $courseObject, $studentPersonObject, $loggedInUserObject, $academicUpdateRequest, $updateType = null, $isUpload = false, $isAdhoc = false);
            $this->assertEquals($result->getAbsence(), $academicUpdate->getAbsences());
            $this->assertEquals($result->getComment(), $academicUpdate->getComment());
            $this->assertEquals($result->getIsAdhoc(), $isAdhoc);
            $this->assertEquals($result->getFailureRiskLevel(), $academicUpdate->getFailureRiskLevel());
            $this->assertEquals($result->getFinalGrade(), $academicUpdate->getFinalGrade());
            $this->assertEquals($result->getUpdateType(), $updateType);
            $this->assertEquals($result->getPersonStudent()->getFirstname(), $studentPersonObject->getFirstName());
            $this->assertEquals($result->getPersonFacultyResponded()->getFirstname(), $facultyPersonObject->getFirstName());
            $this->assertEquals($result->getOrgCourses()->getCourseName(), $courseObject->getCourseName());
            $this->assertEquals($result->getAcademicUpdateRequest()->getName(), $academicUpdateRequest->getName());

        },
            [
                'examples' =>
                    [

                        // Test 1  , checking if the grades get populated
                        [
                            $this->createIndividualAcademicUpdate("Comment1", "High", 1, "A", "F"),
                            $this->createEntity("faculty"),
                            $this->createEntity("course"),
                            $this->createEntity("student"),
                            $this->createEntity("loggedInUser"),
                            $this->createEntity("academicUpdateRequest"),
                            "adhoc",
                            true,
                            true
                        ],
                        // Test 2  , same functionality another set of data
                        [
                            $this->createIndividualAcademicUpdate("Comment2", "Low", 1, "B", "C"),
                            $this->createEntity("faculty"),
                            $this->createEntity("course"),
                            $this->createEntity("student"),
                            $this->createEntity("loggedInUser"),
                            $this->createEntity("academicUpdateRequest"),
                            "bulk",
                            true,
                            false
                        ],

                    ]
            ]
        );
    }

    private function createEntity($type)
    {
        $mockOrganizationObject = $this->getMock('Synapse\CoreBundle\Entity\Organization', ['getId']);
        $mockOrganizationObject->method('getId')->willReturn(1);

        switch ($type) {
            case "student" :
            case "faculty" :
            case "loggedInUser" :
                $personEntity = new Person();
                $personEntity->setFirstname($type);
                $personEntity->setOrganization($mockOrganizationObject);
                return $personEntity;
                break;
            case "course":
                $courseEntity = new OrgCourses();
                $courseEntity->setCourseName("test");
                return $courseEntity;
                break;
            case "academicUpdateRequest":
                $academicUpdateRequest = new AcademicUpdateRequest();
                $academicUpdateRequest->setName("testacademicupdate");
                return $academicUpdateRequest;
                break;
        }
    }

    private function createIndividualAcademicUpdate($comment, $failureRisk, $absence, $finalGrade, $inProgressGrade)
    {

        $individualAcademicUpdateDto = new IndividualAcademicUpdateDTO();
        $individualAcademicUpdateDto->setComment($comment);
        $individualAcademicUpdateDto->setFailureRiskLevel($failureRisk);
        $individualAcademicUpdateDto->setAbsences($absence);
        $individualAcademicUpdateDto->setFinalGrade($finalGrade);
        $individualAcademicUpdateDto->setInProgressGrade($inProgressGrade);
        return $individualAcademicUpdateDto;
    }


    public function testBuildAcademicRecordObject()
    {

        $this->specify("build Academic Record object", function ($academicRecordEntity, $inProgressGrade = null, $failureRiskLevel = null, $absences = null, $comment = null, $finalGrade = null) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockDateUtilityService = $this->getMock('dateUtilityService', ['adjustOrganizationDateTimeStringToUtcDateTimeObject']);
            $organizationId = 1;


            $mockRepositoryResolver->method('getRepository')->willReturn(1);
            $mockContainer->method('get')->willReturnMap(
                [
                    [DateUtilityService::SERVICE_KEY, $mockDateUtilityService]
                ]
            );


            $mockAcademicUpdate = $this->getMock('academicUpdate', ['getDateSubmitted', 'getInProgressGrade', 'getFailureRiskLevel', 'getAbsences', 'getComment', 'getFinalGrade']);
            $mockDatetime = $this->getMock('dateTime', ['format']);
            $mockAcademicUpdate->expects($this->any())->method('getDateSubmitted')->willReturn($mockDatetime);

            $mockDateUtilityService->expects($this->any())->method('adjustOrganizationDateTimeStringToUtcDateTimeObject')->willReturn('');

            $mockAcademicUpdate->expects($this->any())->method('getInProgressGrade')->willReturn($inProgressGrade);
            $mockAcademicUpdate->expects($this->any())->method('getFailureRiskLevel')->willReturn($failureRiskLevel);
            $mockAcademicUpdate->expects($this->any())->method('getAbsences')->willReturn($absences);
            $mockAcademicUpdate->expects($this->any())->method('getComment')->willReturn($comment);
            $mockAcademicUpdate->expects($this->any())->method('getFinalGrade')->willReturn($finalGrade);

            $mockDatetime->expects($this->any())->method('format')->willReturn('');
            $academicUpdateService = new AcademicUpdateService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $academicUpdateService->buildAcademicRecordObject($organizationId, $mockAcademicUpdate, $academicRecordEntity);

            $this->assertEquals($result->getAbsence(), $absences);
            $this->assertEquals($result->getComment(), $comment);
            $this->assertEquals($result->getFailureRiskLevel(), $failureRiskLevel);
            $this->assertEquals($result->getInProgressGrade(), $inProgressGrade);
            $this->assertEquals($result->getFinalGrade(), $finalGrade);

        },
            [
                'examples' =>
                    [

                        // Test 1  , checking if the grades get populated
                        [
                            new AcademicRecord(),
                            "A",
                            "High",
                            1,
                            "comment",
                            "F"

                        ],
                        // Test 2  , same functionality another set of data
                        [
                            new AcademicRecord(),
                            "B",
                            "Low",
                            1,
                            "comment2",
                            "C"
                        ],
                        // Test 3  , Null in progress grade
                        [
                            new AcademicRecord(),
                            null,
                            "Low",
                            1,
                            "comment2",
                            "C"
                        ],
                        // Test 4  , Null failure risk level
                        [
                            new AcademicRecord(),
                            "F",
                            null,
                            1,
                            "comment2",
                            "C"
                        ],
                        // Test 5  , Null absences
                        [
                            new AcademicRecord(),
                            "F",
                            "Low",
                            null,
                            "comment2",
                            "C"
                        ],
                        // Test 6  , Null comment
                        [
                            new AcademicRecord(),
                            "A",
                            "Low",
                            1,
                            null,
                            "C"
                        ],
                        // Test 7  , Null final grade
                        [
                            new AcademicRecord(),
                            "F",
                            "Low",
                            1,
                            "comment2"
                        ],
                        // Test 8  , Two null values
                        [
                            new AcademicRecord(),
                            "C",
                            "Low",
                            1
                        ],
                        // Test 9  , Three null values
                        [
                            new AcademicRecord(),
                            "D",
                            "Low"
                        ],
                        // Test 10  , Four null values
                        [
                            new AcademicRecord(),
                            "D"
                        ],
                        // Test 11  , No values passed
                        [
                            new AcademicRecord()
                        ],
                        // Test 12  , same functionality another set of data
                        [
                            new AcademicRecord(),
                            "F",
                            "High",
                            1,
                            "comment3",
                            "C"
                        ],

                    ]
            ]
        );
    }


    public function testFulfillOpenAcademicUpdateRequestsForStudentAndCourse()
    {
        $this->specify("Test FulfillOpenAcademicUpdateRequestsForStudentAndCourse ", function ($courseId, $studentId, $academicUpdate, $facultyPersonObject, $loggedInUserObject, $notifyStudent, $isInternal, $hasPermissionToUpdateAcademicUpdate, $existingAcademicUpdate, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockAcademicUpdateCreateService = $this->getMock('AcademicUpdateCreateService', ['canUserUpdateAcademicUpdate', 'checkToCloseAcademicUpdateRequest']);
            if ($hasPermissionToUpdateAcademicUpdate) {
                $mockAcademicUpdateCreateService->method('canUserUpdateAcademicUpdate')->willReturn(true);
            } else {
                $mockAcademicUpdateCreateService->method('canUserUpdateAcademicUpdate')->willReturn(false);
            }

            $academicUpdateEntity = new AcademicUpdate();

            $mockAcademicUpdateRepository = $this->getMock('AcademicUpdateRepository', ['find']);
            $mockAcademicUpdateRepository->method('find')->willReturn($academicUpdateEntity);

            $mockAcademicUpdateRequestRepository = $this->getMock('AcademicUpdateRequestRepository', ['find', 'getAcademicUpdatesInOpenRequestsForStudent', 'flush']);

            if ($existingAcademicUpdate && $hasPermissionToUpdateAcademicUpdate) {
                $mockAcademicUpdateRequestRepository->method('find')->willReturn(new AcademicUpdateRequest());
            } else {
                $mockAcademicUpdateRequestRepository->method('find')->willReturn(null);
            }

            if ($existingAcademicUpdate && $hasPermissionToUpdateAcademicUpdate) {
                $mockAcademicUpdateRequestRepository->method('getAcademicUpdatesInOpenRequestsForStudent')->willReturn([
                    [
                        'academic_update_id' => 1,
                        'academic_update_request_id' => 1,
                    ]
                ]);
            } else {
                $mockAcademicUpdateRequestRepository->method('getAcademicUpdatesInOpenRequestsForStudent')->willReturn([]);
            }

            $mockAcademicUpdateRequestRepository->method('flush')->willReturn(true);

            $mockContainer->method('get')->willReturnMap([
                [AcademicUpdateCreateService::SERVICE_KEY, $mockAcademicUpdateCreateService]
            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [AcademicUpdateRepository::REPOSITORY_KEY, $mockAcademicUpdateRepository],
                [AcademicUpdateRequestRepository::REPOSITORY_KEY, $mockAcademicUpdateRequestRepository],

            ]);

            $mockContainer->method('get')->willReturn(1);
            $academicUpdateService = new AcademicUpdateService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $academicUpdateService->fulfillOpenAcademicUpdateRequestsForStudentAndCourse($courseId, $studentId, $academicUpdate, $facultyPersonObject, $loggedInUserObject, $notifyStudent, $isInternal);
            $this->assertEquals($result, $expectedResult);
        },
            [
                'examples' =>
                    [
                        // Test 1  , when Inprogess Grade is Present, will return true
                        [
                            1,
                            1,
                            $this->createIndividualAcademicUpdate("Comment1", "High", 1, "A", "F"),
                            $this->createEntity('faculty'),
                            $this->createEntity('loggedInUser'),
                            false,
                            $isInternal = true,
                            $hasPermissionToUpdateAcademicUpdate = true,
                            $existinAcademicUpdate = true,
                            $expectedResult = true
                        ],
                        // Test 2  , when Inprogess Grade is not present, will return true.
                        [
                            1,
                            1,
                            $this->createIndividualAcademicUpdate("Comment1", "High", 1, "A", null),
                            $this->createEntity('faculty'),
                            $this->createEntity('loggedInUser'),
                            false,
                            $isInternal = true,
                            $hasPermissionToUpdateAcademicUpdate = true,
                            $existingAcademicUpdate = true,
                            $expectedResult = true
                        ],
                        // Test 3  , when Inprogess Grade is  present, but there is no existing academic update, will return false
                        [
                            1,
                            1,
                            $this->createIndividualAcademicUpdate("Comment1", "High", 1, "A", "F"),
                            $this->createEntity('faculty'),
                            $this->createEntity('loggedInUser'),
                            false,
                            $isInternal = true,
                            $hasPermissionToUpdateAcademicUpdate = true,
                            $existingAcademicUpdate = false,
                            $expectedResult = false
                        ],
                        // Test 4  , when Inprogess Grade is Present, but has no permission, will return false
                        [
                            1,
                            1,
                            $this->createIndividualAcademicUpdate("Comment1", "High", 1, "A", "F"),
                            $this->createEntity('faculty'),
                            $this->createEntity('loggedInUser'),
                            false,
                            $isInternal = true,
                            $hasPermissionToUpdateAcademicUpdate = false,
                            $existingAcademicUpdate = true,
                            $expectedResult = false
                        ],
                        // Test 5  , when Inprogess Grade is Present, and its v2 api, will return true
                        [
                            1,
                            1,
                            $this->createIndividualAcademicUpdate("Comment1", "High", 1, "A", "F"),
                            $this->createEntity('faculty'),
                            $this->createEntity('loggedInUser'),
                            false,
                            $isInternal = false,
                            $hasPermissionToUpdateAcademicUpdate = true,
                            $existingAcademicUpdate = true,
                            $expectedResult = true
                        ],
                        // Test 6  , when Inprogess Grade is not present, and its v2 api, will return true
                        [
                            1,
                            1,
                            $this->createIndividualAcademicUpdate("Comment1", "High", 1, "A", null),
                            $this->createEntity('faculty'),
                            $this->createEntity('loggedInUser'),
                            false,
                            $isInternal = false,
                            $hasPermissionToUpdateAcademicUpdate = true,
                            $existingAcademicUpdate = true,
                            $expectedResult = true
                        ]
                    ]
            ]
        );
    }


    public function testGetAcademicUpdateRequestById()
    {
        //This unit test also covers private functions formatIndividualAcademicUpdateRequestAsJSON, getAcademicUpdateRequestFilterCriteria, getRequestAttributesStudent,
        //getRequestAttributesFaculties, getRequestAttributesGroups, getRequestAttributesCourses, getRequestAttributesProfile, getRequestAttributesStaticList.
        $this->specify("test getAcademicUpdateRequestById", function ($expectedResults, $index1, $index2, $mockOrganizationId = null,
                                                                      $mockAcademicUpdateRequestId = null,
                                                                      $mockUserType = null, $mockPageNumber = null, $mockLoggedInUserId = null, $mockFilter = null,
                                                                      $mockRecordCount = null, $mockCount = null, $mockAcademicUpdateRequestData = null,
                                                                      $mockNonParticipantCount = null, $returnCoordinatorObject = false, $mockCanViewAbsences = null,
                                                                      $mockCanViewInProgressGrade = null, $mockCanViewComments = null, $requestCompletionPercentage = null, $selectedStudents = null,
                                                                      $selectedStudentIds = null, $selectedFaculty = null, $selectedFacultyIds = null,
                                                                      $selectedGroups = null, $selectedGroupIds = null, $selectedCourses = null, $selectedCourseIds = null,
                                                                      $selectedStaticLists = null, $selectedStaticListIds = null, $selectedProfileItemIds = null) {

            //TODO:: This function needs cases covering filters other than the student filter.
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockAcademicUpdateRequestRepository = $this->getMock('academicUpdateRequestRepository',
                [
                    'findOneBy',
                    'getAcademicUpdatesCountByRequestId',
                    'getAllAcademicUpdateRequestDetailsByIdForFaculty',
                    'getAllAcademicUpdateRequestDetailsById',
                    'getSelectedStudentsByRequest',
                    'getSelectedFacultyByRequest',
                    'getSelectedGroupByRequest',
                    'getSelectedCourseByRequest',
                    'getSelectedStaticListByRequest',
                    'getSelectedProfileByRequest',
                    'getAcademicUpdateRequestCompletionStatistics'

                ]
            );
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['findOneBy']);
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find']);

            $mockAcademicYearService = $this->getMock('academicYearService', ['getCurrentOrgAcademicYearId']);
            $mockDateUtilityService = $this->getMock('dateUtilityService', ['getFormattedDateTimeForOrganization']);

            $mockAcademicUpdateRequestObject = $this->getMock('academicUpdateRequest',
                [
                    'getSelectStudent',
                    'getSelectFaculty',
                    'getSelectGroup',
                    'getSelectCourse',
                    'getSelectStaticList',
                    'getId'

                ]
            );

            $mockStudentDTO = $this->getMock('studentDTO', ['setIsAll', 'setSelectedStudentIds']);
            $mockStaffDTO = $this->getMock('facultyDTO', ['setIsAll', 'setSelectedStaffIds']);
            $mockCourseDTO = $this->getMock('courseDTO', ['setIsAll', 'setSelectedCourseIds']);
            $mockGroupDTO = $this->getMock('groupDTO', ['setIsAll', 'setSelectedGroupIds']);
            $mockStaticListDTO = $this->getMock('staticListDTO', ['setIsAll', 'setSelectedStaticIds']);
            $mockOrganizationObject = $this->getMock('organizationDetails',
                [
                    'getCanViewAbsences',
                    'getCanViewInProgressGrade',
                    'getCanViewComments'
                ]
            );

            if ($returnCoordinatorObject) {
                $mockOrganizationRoleObject = $this->getMock('organizationRole', []);
            } else {
                $mockOrganizationRoleObject = null;
            }

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        AcademicUpdateRequestRepository::REPOSITORY_KEY,
                        $mockAcademicUpdateRequestRepository
                    ],
                    [
                        OrganizationRoleRepository::REPOSITORY_KEY,
                        $mockOrganizationRoleRepository
                    ],
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        AcademicYearService::SERVICE_KEY,
                        $mockAcademicYearService
                    ],
                    [
                        DateUtilityService::SERVICE_KEY,
                        $mockDateUtilityService
                    ]
                ]);

            $mockAcademicUpdateRequestRepository->method('findOneBy')->willReturn($mockAcademicUpdateRequestObject);
            $mockAcademicUpdateRequestRepository->method('getAcademicUpdateRequestCompletionStatistics')->willReturn($requestCompletionPercentage);
            $mockAcademicUpdateRequestObject->method('getId')->willReturn('');
            $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn(1);

            if ($returnCoordinatorObject) {
                $mockOrganizationRoleRepository->method('findOneBy')->willReturn($mockOrganizationRoleObject);
            }

            if (is_array($expectedResults) && $index1 && $index2) {
                $mockAcademicUpdateRequestRepository->expects($this->at($index1))->method('getAcademicUpdatesCountByRequestId')->willReturn($mockCount);
                if ($returnCoordinatorObject) {
                    $mockAcademicUpdateRequestRepository->method('getAllAcademicUpdateRequestDetailsById')->willReturn($mockAcademicUpdateRequestData);
                } else {
                    $mockAcademicUpdateRequestRepository->method('getAllAcademicUpdateRequestDetailsByIdForFaculty')->willReturn($mockAcademicUpdateRequestData);
                }
                $mockAcademicUpdateRequestRepository->expects($this->at($index2))->method('getAcademicUpdatesCountByRequestId')->willReturn($mockNonParticipantCount);

                $mockOrganizationRepository->method('find')->willReturn($mockOrganizationObject);

                $mockOrganizationObject->method('getCanViewAbsences')->willReturn($mockCanViewAbsences);
                $mockOrganizationObject->method('getCanViewInProgressGrade')->willReturn($mockCanViewInProgressGrade);
                $mockOrganizationObject->method('getCanViewComments')->willReturn($mockCanViewComments);

                $mockDateUtilityService->method('getFormattedDateTimeForOrganization')->willReturn('2017-11-11 00:00:00');

                if ($selectedStudentIds && $selectedStudents) {
                    $mockStudentDTO->method('setIsAll')->willReturn('');
                    $mockStudentDTO->method('setSelectedStudentIds')->willReturn('');
                    $mockAcademicUpdateRequestObject->method('getSelectStudent')->willReturn($selectedStudents);
                    $mockAcademicUpdateRequestRepository->method('getSelectedStudentsByRequest')->willReturn($selectedStudentIds);
                }

                if ($selectedFacultyIds && $selectedFaculty) {
                    $mockStaffDTO->method('setIsAll')->willReturn('');
                    $mockStaffDTO->method('setSelectedStaffIds')->willReturn('');
                    $mockAcademicUpdateRequestObject->method('getSelectedFaculty')->willReturn($selectedFaculty);
                    $mockAcademicUpdateRequestRepository->method('getSelectedFacultyByRequest')->willReturn($selectedFacultyIds);
                }

                if ($selectedCourseIds && $selectedCourses) {
                    $mockCourseDTO->method('setIsAll')->willReturn('');
                    $mockCourseDTO->method('setSelectedCourseIds')->willReturn('');
                    $mockAcademicUpdateRequestObject->method('getSelectCourse')->willReturn($selectedCourses);
                    $mockAcademicUpdateRequestRepository->method('getSelectedCourseByRequest')->willReturn($selectedCourseIds);
                }

                if ($selectedGroupIds && $selectedGroups) {
                    $mockGroupDTO->method('setIsAll')->willReturn('');
                    $mockGroupDTO->method('setSelectedGroupIds')->willReturn('');
                    $mockAcademicUpdateRequestObject->method('getSelectGroup')->willReturn($selectedGroups);
                    $mockAcademicUpdateRequestRepository->method('getSelectedGroupByRequest')->willReturn($selectedGroupIds);
                }

                if ($selectedStaticListIds && $selectedStaticLists) {
                    $mockStaticListDTO->method('setIsAll')->willReturn('');
                    $mockStaticListDTO->method('setSelectedStaticIds')->willReturn('');
                    $mockAcademicUpdateRequestObject->method('getSelectStaticList')->willReturn($selectedStaticLists);
                    $mockAcademicUpdateRequestRepository->method('getSelectedStaticListByRequest')->willReturn($selectedStaticListIds);
                }

                if ($selectedProfileItemIds) {
                    $mockAcademicUpdateRequestRepository->method('getSelectedProfileByRequest')->willReturn($selectedProfileItemIds);
                }
            }

            try {
                $academicUpdateService = new AcademicUpdateService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $functionResults = $academicUpdateService->getAcademicUpdateRequestByIdAsJSON($mockOrganizationId, $mockAcademicUpdateRequestId, $mockUserType, $mockFilter, $mockLoggedInUserId, $mockPageNumber, $mockRecordCount);
                $this->assertEquals($expectedResults, $functionResults);
            } catch (SynapseException $exception) {
                $this->assertEquals($expectedResults, $exception->getMessage());

            }

        },
            [
                'examples' =>
                    [
                        //Example 0 - Nothing passed in.
                        [
                            //Expected results
                            'You do not have coordinator access.',
                            // index 1
                            null,
                            // index 2
                            null

                        ],
                        //Example 1 - Only the organization ID
                        [
                            // expected result
                            'You do not have coordinator access.',
                            // index 1
                            null,
                            // index 2
                            null,
                            // organization ID
                            1
                        ],
                        //Example 2 - Org and request IDs only.
                        [
                            // expected result
                            'You do not have coordinator access.',
                            // index 1
                            null,
                            // index 2
                            null,
                            // organization ID
                            1,
                            // academic update request ID
                            1
                        ],
                        //Example 3 - Invalid user type
                        [
                            // expected result
                            'You do not have coordinator access.',
                            // index 1
                            null,
                            // index 2
                            null,
                            // organization ID
                            1,
                            // academic update request ID
                            1,
                            // user type
                            'Turkey'
                        ],
                        //Example 4 - All students filter academic update request. All view permission enabled. Closed academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(true, '', ''),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => true,
                                        'can_view_absences' => true,
                                        'can_view_comments' => true,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233457,
                                                                        'student_firstname' => 'Team',
                                                                        'student_lastname' => 'Member',
                                                                        'student_risk' => 'Low',
                                                                        'student_grade' => 'A',
                                                                        'student_absences' => '',
                                                                        'student_comments' => 'You be here.',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'closed'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 100,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(1),
                            // non-participant count
                            0,
                            // is coordinator
                            false,
                            // can view absences
                            true,
                            // can view in progress grades
                            true,
                            // can view comments
                            true,
                            // Completion percentage
                            ['completion_percentage' => 100],
                            // selected student filter
                            'all',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 5 - Specific students filter academic update request. All view permission enabled. Closed academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(false, '123,234,345,456,567', null),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => true,
                                        'can_view_absences' => true,
                                        'can_view_comments' => true,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233457,
                                                                        'student_firstname' => 'Team',
                                                                        'student_lastname' => 'Member',
                                                                        'student_risk' => 'Low',
                                                                        'student_grade' => 'A',
                                                                        'student_absences' => '',
                                                                        'student_comments' => 'You be here.',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'closed'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 100,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(1),
                            // non-participant count
                            0,
                            // is coordinator
                            null,
                            // can view absences
                            true,
                            // can view in progress grades
                            true,
                            // can view comments
                            true,
                            // Completion percentage
                            ['completion_percentage' => 100],
                            // selected student filter
                            'bob',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 6 - All students filter academic update request. Specific view permission enabled. Closed academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(true, '', ''),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => true,
                                        'can_view_absences' => false,
                                        'can_view_comments' => false,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233457,
                                                                        'student_firstname' => 'Team',
                                                                        'student_lastname' => 'Member',
                                                                        'student_risk' => 'Low',
                                                                        'student_grade' => 'A',
                                                                        'student_absences' => '',
                                                                        'student_comments' => 'You be here.',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'closed'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 100,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(1),
                            // non-participant count
                            0,
                            // is coordinator
                            false,
                            // can view absences
                            false,
                            // can view in progress grades
                            true,
                            // can view comments
                            false,
                            // Completion percentage
                            ['completion_percentage' => 100],
                            // selected student filter
                            'all',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 7 - Specific students filter academic update request. Specific view permission enabled. Closed academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(false, '123,234,345,456,567', null),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => false,
                                        'can_view_absences' => true,
                                        'can_view_comments' => true,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233457,
                                                                        'student_firstname' => 'Team',
                                                                        'student_lastname' => 'Member',
                                                                        'student_risk' => 'Low',
                                                                        'student_grade' => 'A',
                                                                        'student_absences' => '',
                                                                        'student_comments' => 'You be here.',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'closed'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 100,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(1),
                            // non-participant count
                            0,
                            // is coordinator
                            null,
                            // can view absences
                            true,
                            // can view in progress grades
                            false,
                            // can view comments
                            true,
                            // Completion percentage
                            ['completion_percentage' => 100],
                            // selected student filter
                            'bob',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 8 - All students filter academic update request. No view permission enabled. Closed academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(true, '', ''),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => false,
                                        'can_view_absences' => false,
                                        'can_view_comments' => false,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233457,
                                                                        'student_firstname' => 'Team',
                                                                        'student_lastname' => 'Member',
                                                                        'student_risk' => 'Low',
                                                                        'student_grade' => 'A',
                                                                        'student_absences' => '',
                                                                        'student_comments' => 'You be here.',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'closed'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 100,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(1),
                            // non-participant count
                            0,
                            // is coordinator
                            false,
                            // can view absences
                            false,
                            // can view in progress grades
                            false,
                            // can view comments
                            false,
                            // Completion percentage
                            ['completion_percentage' => 100],
                            // selected student filter
                            'all',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 9 - Specific students filter academic update request. No view permission enabled. Closed academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(false, '123,234,345,456,567', null),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => false,
                                        'can_view_absences' => false,
                                        'can_view_comments' => false,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233457,
                                                                        'student_firstname' => 'Team',
                                                                        'student_lastname' => 'Member',
                                                                        'student_risk' => 'Low',
                                                                        'student_grade' => 'A',
                                                                        'student_absences' => '',
                                                                        'student_comments' => 'You be here.',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'closed'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 100,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(1),
                            // non-participant count
                            0,
                            // is coordinator
                            null,
                            // can view absences
                            false,
                            // can view in progress grades
                            false,
                            // can view comments
                            false,
                            // Completion percentage
                            ['completion_percentage' => 100],
                            // selected student filter
                            'bob',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 10 - All students filter academic update request. All view permission enabled. Saved academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(true, '', ''),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => true,
                                        'can_view_absences' => true,
                                        'can_view_comments' => true,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233456,
                                                                        'student_firstname' => 'Saved',
                                                                        'student_lastname' => 'Person',
                                                                        'student_risk' => 'Low',
                                                                        'student_grade' => '',
                                                                        'student_absences' => '',
                                                                        'student_comments' => 'I am saved. Do not submit me',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'saved'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 0,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(3),
                            // non-participant count
                            0,
                            // is coordinator
                            false,
                            // can view absences
                            true,
                            // can view in progress grades
                            true,
                            // can view comments
                            true,
                            // Completion percentage
                            ['completion_percentage' => 0],
                            // selected student filter
                            'all',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 11 - Specific students filter academic update request. All view permission enabled. Saved academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(false, '123,234,345,456,567', null),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => true,
                                        'can_view_absences' => true,
                                        'can_view_comments' => true,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233456,
                                                                        'student_firstname' => 'Saved',
                                                                        'student_lastname' => 'Person',
                                                                        'student_risk' => 'Low',
                                                                        'student_grade' => '',
                                                                        'student_absences' => '',
                                                                        'student_comments' => 'I am saved. Do not submit me',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'saved'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 0,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(3),
                            // non-participant count
                            0,
                            // is coordinator
                            null,
                            // can view absences
                            true,
                            // can view in progress grades
                            true,
                            // can view comments
                            true,
                            // Completion percentage
                            ['completion_percentage' => 0],
                            // selected student filter
                            'bob',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 12 - All students filter academic update request. Specific view permission enabled. Saved academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(true, '', ''),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => true,
                                        'can_view_absences' => false,
                                        'can_view_comments' => false,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233456,
                                                                        'student_firstname' => 'Saved',
                                                                        'student_lastname' => 'Person',
                                                                        'student_risk' => 'Low',
                                                                        'student_grade' => '',
                                                                        'student_absences' => '',
                                                                        'student_comments' => 'I am saved. Do not submit me',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'saved'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 0,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(3),
                            // non-participant count
                            0,
                            // is coordinator
                            false,
                            // can view absences
                            false,
                            // can view in progress grades
                            true,
                            // can view comments
                            false,
                            // Completion percentage
                            ['completion_percentage' => 0],
                            // selected student filter
                            'all',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 13 - Specific students filter academic update request. Specific view permission enabled. Saved academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(false, '123,234,345,456,567', null),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => false,
                                        'can_view_absences' => true,
                                        'can_view_comments' => true,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233456,
                                                                        'student_firstname' => 'Saved',
                                                                        'student_lastname' => 'Person',
                                                                        'student_risk' => 'Low',
                                                                        'student_grade' => '',
                                                                        'student_absences' => '',
                                                                        'student_comments' => 'I am saved. Do not submit me',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'saved'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 0,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(3),
                            // non-participant count
                            0,
                            // is coordinator
                            null,
                            // can view absences
                            true,
                            // can view in progress grades
                            false,
                            // can view comments
                            true,
                            // Completion percentage
                            ['completion_percentage' => 0],
                            // selected student filter
                            'bob',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 14 - All students filter academic update request. No view permission enabled. Saved academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(true, '', ''),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => false,
                                        'can_view_absences' => false,
                                        'can_view_comments' => false,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233456,
                                                                        'student_firstname' => 'Saved',
                                                                        'student_lastname' => 'Person',
                                                                        'student_risk' => 'Low',
                                                                        'student_grade' => '',
                                                                        'student_absences' => '',
                                                                        'student_comments' => 'I am saved. Do not submit me',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'saved'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 0,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(3),
                            // non-participant count
                            0,
                            // is coordinator
                            false,
                            // can view absences
                            false,
                            // can view in progress grades
                            false,
                            // can view comments
                            false,
                            // Completion percentage
                            ['completion_percentage' => 0],
                            // selected student filter
                            'all',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 15 - Specific students filter academic update request. No view permission enabled. Saved academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(false, '123,234,345,456,567', null),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => false,
                                        'can_view_absences' => false,
                                        'can_view_comments' => false,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233456,
                                                                        'student_firstname' => 'Saved',
                                                                        'student_lastname' => 'Person',
                                                                        'student_risk' => 'Low',
                                                                        'student_grade' => '',
                                                                        'student_absences' => '',
                                                                        'student_comments' => 'I am saved. Do not submit me',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'saved'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 0,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(3),
                            // non-participant count
                            0,
                            // is coordinator
                            null,
                            // can view absences
                            false,
                            // can view in progress grades
                            false,
                            // can view comments
                            false,
                            // Completion percentage
                            ['completion_percentage' => 0],
                            // selected student filter
                            'bob',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 16 - All students filter academic update request. All view permission enabled. Open academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(true, '', ''),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => true,
                                        'can_view_absences' => true,
                                        'can_view_comments' => true,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233458,
                                                                        'student_firstname' => 'Not A',
                                                                        'student_lastname' => 'Team Member',
                                                                        'student_risk' => '',
                                                                        'student_grade' => '',
                                                                        'student_absences' => '',
                                                                        'student_comments' => '',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'open'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 0,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(2),
                            // non-participant count
                            0,
                            // is coordinator
                            false,
                            // can view absences
                            true,
                            // can view in progress grades
                            true,
                            // can view comments
                            true,
                            // Completion percentage
                            ['completion_percentage' => 0],
                            // selected student filter
                            'all',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 17 - Specific students filter academic update request. All view permission enabled. Open academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(false, '123,234,345,456,567', null),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => true,
                                        'can_view_absences' => true,
                                        'can_view_comments' => true,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233458,
                                                                        'student_firstname' => 'Not A',
                                                                        'student_lastname' => 'Team Member',
                                                                        'student_risk' => '',
                                                                        'student_grade' => '',
                                                                        'student_absences' => '',
                                                                        'student_comments' => '',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'open'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 0,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(2),
                            // non-participant count
                            0,
                            // is coordinator
                            null,
                            // can view absences
                            true,
                            // can view in progress grades
                            true,
                            // can view comments
                            true,
                            // Completion percentage
                            ['completion_percentage' => 0],
                            // selected student filter
                            'bob',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 18 - All students filter academic update request. Specific view permission enabled. Open academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(true, '', ''),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => true,
                                        'can_view_absences' => false,
                                        'can_view_comments' => false,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233458,
                                                                        'student_firstname' => 'Not A',
                                                                        'student_lastname' => 'Team Member',
                                                                        'student_risk' => '',
                                                                        'student_grade' => '',
                                                                        'student_absences' => '',
                                                                        'student_comments' => '',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'open'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 0,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(2),
                            // non-participant count
                            0,
                            // is coordinator
                            false,
                            // can view absences
                            false,
                            // can view in progress grades
                            true,
                            // can view comments
                            false,
                            // Completion percentage
                            ['completion_percentage' => 0],
                            // selected student filter
                            'all',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 19 - Specific students filter academic update request. Specific view permission enabled. Open academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(false, '123,234,345,456,567', null),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => false,
                                        'can_view_absences' => true,
                                        'can_view_comments' => true,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233458,
                                                                        'student_firstname' => 'Not A',
                                                                        'student_lastname' => 'Team Member',
                                                                        'student_risk' => '',
                                                                        'student_grade' => '',
                                                                        'student_absences' => '',
                                                                        'student_comments' => '',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'open'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 0,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(2),
                            // non-participant count
                            0,
                            // is coordinator
                            null,
                            // can view absences
                            true,
                            // can view in progress grades
                            false,
                            // can view comments
                            true,
                            // Completion percentage
                            ['completion_percentage' => 0],
                            // selected student filter
                            'bob',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 20 - All students filter academic update request. No view permission enabled. Open academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(true, '', ''),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => false,
                                        'can_view_absences' => false,
                                        'can_view_comments' => false,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233458,
                                                                        'student_firstname' => 'Not A',
                                                                        'student_lastname' => 'Team Member',
                                                                        'student_risk' => '',
                                                                        'student_grade' => '',
                                                                        'student_absences' => '',
                                                                        'student_comments' => '',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'open'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 0,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(2),
                            // non-participant count
                            0,
                            // is coordinator
                            false,
                            // can view absences
                            false,
                            // can view in progress grades
                            false,
                            // can view comments
                            false,
                            // Completion percentage
                            ['completion_percentage' => 0],
                            // selected student filter
                            'all',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 21 - Specific students filter academic update request. No view permission enabled. Open academic update in request.
                        [
                            // expected results
                            [
                                'total_records' => 1,
                                'total_pages' => 1,
                                'records_per_page' => 1,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    [
                                        'request_id' => 123,
                                        'request_name' => 'Unit Test Request',
                                        'request_description' => 'A request used for a unit test',
                                        'request_status' => 'open',
                                        'request_due' => '2017-11-11 00:00:00',
                                        'request_created' => '2017-11-11 00:00:00',
                                        'request_from_firstname' => 'Software',
                                        'request_from_lastname' => 'Developer',
                                        'request_attributes' =>
                                            [
                                                'students' => $this->generateStudentsDTO(false, '123,234,345,456,567', null),
                                                'staff' => $this->generateStaffDTO(),
                                                'groups' => $this->generateGroupsDTO(),
                                                'courses' => $this->generateCoursesDTO(),
                                                'profile' =>
                                                    [
                                                        'selected_ebi_ids' => '',
                                                        'selected_isp_ids' => ''
                                                    ],
                                                'static_list' => $this->generateStaticListDTO()
                                            ],
                                        'can_view_in_progress_grade' => false,
                                        'can_view_absences' => false,
                                        'can_view_comments' => false,
                                        'request_from' =>
                                            [
                                                'firstname' => 'Software',
                                                'lastname' => 'Developer'
                                            ],
                                        'request_details' =>
                                            [
                                                0 =>
                                                    [
                                                        'subject_course' => 'UNIT101',
                                                        'department_name' => 'Software Development',
                                                        'academic_year_name' => 'Year of the Jobs',
                                                        'academic_term_name' => 'Term of the Gates',
                                                        'course_section_name' => 'TEST',
                                                        'course_id' => '1234321',
                                                        'student_details' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'academic_update_id' => 456,
                                                                        'student_id' => 1233458,
                                                                        'student_firstname' => 'Not A',
                                                                        'student_lastname' => 'Team Member',
                                                                        'student_risk' => '',
                                                                        'student_grade' => '',
                                                                        'student_absences' => '',
                                                                        'student_comments' => '',
                                                                        'student_refer' => 0,
                                                                        'student_send' => false,
                                                                        'is_bypassed' => false,
                                                                        'student_status' => 'active',
                                                                        'academic_update_status' => 'open'
                                                                    ]
                                                            ]
                                                    ]
                                            ],
                                        'request_complete_status' => 0,

                                    ]
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            1,
                            // count
                            1,
                            // request data
                            $this->generateMockAcademicUpdateRequestData(2),
                            // non-participant count
                            0,
                            // is coordinator
                            null,
                            // can view absences
                            false,
                            // can view in progress grades
                            false,
                            // can view comments
                            false,
                            // Completion percentage
                            ['completion_percentage' => 0],
                            // selected student filter
                            'bob',
                            // selected student IDs
                            [
                                ['person_id' => 123],
                                ['person_id' => 234],
                                ['person_id' => 345],
                                ['person_id' => 456],
                                ['person_id' => 567]
                            ]
                        ],
                        //Example 22 - Specific students filter academic update request. No view permission enabled. No academic updates in request.
                        [
                            // expected results
                            [
                                'total_records' => 0,
                                'total_pages' => 0,
                                'records_per_page' => 25,
                                'current_page' => 1,
                                'non_participant_count' => 0,
                                'data' =>
                                    []
                            ],
                            // index 1
                            1,
                            // index 2
                            3,
                            // organization ID
                            1,
                            // request ID
                            1,
                            // usertype
                            'faculty',
                            // page number
                            1,
                            // logged in user ID
                            1,
                            // filter
                            '',
                            // record count
                            0,
                            // count
                            0,
                            // request data
                            null,
                            // non-participant count
                            0,
                            // is coordinator
                            null,
                            // can view absences
                            false,
                            // can view in progress grades
                            false,
                            // can view comments
                            false,
                            // Completion percentage
                            ['completion_percentage' => 0],
                        ],
                    ]
            ]
        );
    }


    private function generateMockAcademicUpdateRequestData($index = null)
    {
        $academicUpdateRequest = [
            [
                'request_id' => 123,
                'request_name' => 'Unit Test Request',
                'request_description' => 'A request used for a unit test',
                'request_created' => '2017-11-16 00:00:00',
                'request_due' => '2020-11-16 00:00:00',
                'request_status' => 'open',
                'request_from_firstname' => 'Software',
                'request_from_lastname' => 'Developer',
                'is_bypassed' => false,
                'academic_update_id' => 456,
                'student_id' => 1233456,
                'student_external_id' => 'TeamMember101',
                'student_firstname' => 'Team',
                'student_lastname' => 'Member',
                'academic_update_status' => 'closed',
                'student_risk' => 'High',
                'student_grade' => 'F',
                'student_absences' => 9,
                'student_comments' => 'Where on earth are you?',
                'student_send' => true,
                'student_refer' => true,
                'student_status' => 'active',
                'course_section_id' => 'UnitTest101',
                'course_id' => '1234321',
                'subject_code' => 'UNIT',
                'course_number' => '101',
                'course_name' => 'Unit Testing 101',
                'course_section_name' => 'TEST',
                'department_name' => 'Software Development',
                'academic_year_name' => 'Year of the Jobs',
                'academic_term_name' => 'Term of the Gates',
            ],
            [
                'request_id' => 123,
                'request_name' => 'Unit Test Request',
                'request_description' => 'A request used for a unit test',
                'request_created' => '2017-11-16 00:00:00',
                'request_due' => '2020-11-16 00:00:00',
                'request_status' => 'open',
                'request_from_firstname' => 'Software',
                'request_from_lastname' => 'Developer',
                'is_bypassed' => false,
                'academic_update_id' => 456,
                'student_id' => 1233457,
                'student_external_id' => 'TeamMember102',
                'student_firstname' => 'Team',
                'student_lastname' => 'Member',
                'academic_update_status' => 'closed',
                'student_risk' => 'Low',
                'student_grade' => 'A',
                'student_absences' => '',
                'student_comments' => 'You be here.',
                'student_send' => '',
                'student_refer' => '',
                'student_status' => 'active',
                'course_section_id' => 'UnitTest101',
                'course_id' => '1234321',
                'subject_code' => 'UNIT',
                'course_number' => '101',
                'course_name' => 'Unit Testing 101',
                'course_section_name' => 'TEST',
                'department_name' => 'Software Development',
                'academic_year_name' => 'Year of the Jobs',
                'academic_term_name' => 'Term of the Gates',

            ],
            [
                'request_id' => 123,
                'request_name' => 'Unit Test Request',
                'request_description' => 'A request used for a unit test',
                'request_created' => '2017-11-16 00:00:00',
                'request_due' => '2020-11-16 00:00:00',
                'request_status' => 'open',
                'request_from_firstname' => 'Software',
                'request_from_lastname' => 'Developer',
                'is_bypassed' => false,
                'academic_update_id' => 456,
                'student_id' => 1233458,
                'student_external_id' => 'NotATeamMember101',
                'student_firstname' => 'Not A',
                'student_lastname' => 'Team Member',
                'academic_update_status' => 'open',
                'student_risk' => '',
                'student_grade' => '',
                'student_absences' => '',
                'student_comments' => '',
                'student_send' => '',
                'student_refer' => '',
                'student_status' => 'active',
                'course_section_id' => 'UnitTest101',
                'course_id' => '1234321',
                'subject_code' => 'UNIT',
                'course_number' => '101',
                'course_name' => 'Unit Testing 101',
                'course_section_name' => 'TEST',
                'department_name' => 'Software Development',
                'academic_year_name' => 'Year of the Jobs',
                'academic_term_name' => 'Term of the Gates',

            ],
            [
                'request_id' => 123,
                'request_name' => 'Unit Test Request',
                'request_description' => 'A request used for a unit test',
                'request_created' => '2017-11-16 00:00:00',
                'request_due' => '2020-11-16 00:00:00',
                'request_status' => 'open',
                'request_from_firstname' => 'Software',
                'request_from_lastname' => 'Developer',
                'is_bypassed' => false,
                'academic_update_id' => 456,
                'student_id' => 1233456,
                'student_external_id' => 'SavedPerson',
                'student_firstname' => 'Saved',
                'student_lastname' => 'Person',
                'academic_update_status' => 'saved',
                'student_risk' => 'Low',
                'student_grade' => '',
                'student_absences' => '',
                'student_comments' => 'I am saved. Do not submit me',
                'student_send' => '',
                'student_refer' => '',
                'student_status' => 'active',
                'course_section_id' => 'UnitTest101',
                'course_id' => '1234321',
                'subject_code' => 'UNIT',
                'course_number' => '101',
                'course_name' => 'Unit Testing 101',
                'course_section_name' => 'TEST',
                'department_name' => 'Software Development',
                'academic_year_name' => 'Year of the Jobs',
                'academic_term_name' => 'Term of the Gates',

            ],
            [
                'request_id' => 123,
                'request_name' => 'Unit Test Request',
                'request_description' => 'A request used for a unit test',
                'request_created' => '2017-11-16 00:00:00',
                'request_due' => '2020-11-16 00:00:00',
                'request_status' => 'open',
                'request_from_firstname' => 'Software',
                'request_from_lastname' => 'Developer',
                'is_bypassed' => false,
                'academic_update_id' => 456,
                'student_id' => 1233456,
                'student_external_id' => 'TeamMember101',
                'student_firstname' => 'Team',
                'student_lastname' => 'Member',
                'academic_update_status' => 'closed',
                'student_risk' => 'High',
                'student_grade' => 'F',
                'student_absences' => 9,
                'student_comments' => 'Where on earth are you?',
                'student_send' => true,
                'student_refer' => true,
                'student_status' => 'active',
                'course_section_id' => 'UnitTest101',
                'course_id' => '1234321',
                'subject_code' => 'UNIT',
                'course_number' => '101',
                'course_name' => 'Unit Testing 101',
                'course_section_name' => 'TEST',
                'department_name' => 'Software Development',
                'academic_year_name' => 'Year of the Jobs',
                'academic_term_name' => 'Term of the Gates',

            ]
        ];

        if ($index) {
            $return = [$academicUpdateRequest[$index]];
        } else {
            $return = $academicUpdateRequest;
        }

        return $return;
    }

    private function generateStudentsDTO($isAll = null, $selectedStudentIds = null, $studentIds = null)
    {
        $studentsDTO = new \Synapse\AcademicUpdateBundle\EntityDto\StudentsDto();

        $studentsDTO->setIsAll($isAll);
        $studentsDTO->setSelectedStudentIds($selectedStudentIds);
        $studentsDTO->setStudentIds($studentIds);

        return $studentsDTO;
    }

    private function generateStaffDTO($isAll = null, $selectedStaffIds = null)
    {
        $staffDTO = new \Synapse\AcademicUpdateBundle\EntityDto\StaffDto();

        $staffDTO->setIsAll($isAll);
        $staffDTO->setSelectedStaffIds($selectedStaffIds);

        return $staffDTO;
    }

    private function generateStaticListDTO($isAll = null, $selectedStaticIds = null)
    {
        $staticListDto = new \Synapse\AcademicUpdateBundle\EntityDto\StaticListDto();

        $staticListDto->setIsAll($isAll);
        $staticListDto->setSelectedStaticIds($selectedStaticIds);

        return $staticListDto;
    }

    private function generateGroupsDTO($isAll = null, $selectedGroupIds = null)
    {
        $groupsDto = new \Synapse\AcademicUpdateBundle\EntityDto\GroupsDto();

        $groupsDto->setIsAll($isAll);
        $groupsDto->setSelectedGroupIds($selectedGroupIds);

        return $groupsDto;
    }

    private function generateCoursesDTO($isAll = null, $selectedCourseIds = null)
    {
        $coursesDto = new \Synapse\AcademicUpdateBundle\EntityDto\CoursesDto();

        $coursesDto->setIsAll($isAll);
        $coursesDto->setSelectedCourseIds($selectedCourseIds);

        return $coursesDto;
    }


    public function testCreateAcademicUpdateRequestReminderOrCancellationNotifications()
    {
        $this->specify("Test to Get latest academic updates for a specific course/students", function ($reminderListArray, $organization, $requestId, $loggedInUserId, $type, $expectedErrorMessage) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockAlertNotificationService = $this->getMock('AlertNotificationService', ['createNotification']);

            $mockAlertNotificationService->method('createNotification')->willReturn(1);

            $mockAcademicUpdateRepository = $this->getMock('AcademicUpdateRepository', ['findOneBy']);
            $mockPersonRepository = $this->getMock('AcademicUpdateRepository', ['find']);

            $mockPersonRepository->method('find')->willReturnCallback(function ($personId) {

                if ($personId == "invalid") {
                    throw new SynapseValidationException("Coordinator Not Found");
                }
                $personEntity = new Person();
                $personEntity->setId($personId);
                $personEntity->setFirstname('firstname');
                $personEntity->setLastname('lastname');
                return $personEntity;

            });

            $academicUpdate = new AcademicUpdate();
            if ($requestId != "invalid") {
                $mockAcademicUpdateRepository->method('findOneBy')->willReturn($academicUpdate);
            } else {
                $mockAcademicUpdateRepository->method('findOneBy')->willThrowException(new SynapseValidationException('Academic Update Not Found'));
            }


            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    AcademicUpdateRepository::REPOSITORY_KEY,
                    $mockAcademicUpdateRepository
                ],
                [
                    PersonRepository::REPOSITORY_KEY,
                    $mockPersonRepository
                ]
            ]);

            $mockContainer->method('get')->willReturnMap([[
                AlertNotificationsService::SERVICE_KEY,
                $mockAlertNotificationService
            ]]);

            try {

                $academicUpdateServiceObject = new AcademicUpdateService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $academicUpdateServiceObject->createAcademicUpdateRequestReminderOrCancellationNotifications($reminderListArray, $organization, $requestId, $loggedInUserId, $type);
                $this->assertTrue($result);
            } catch (\Exception $e) {
                $this->assertEquals($e->getMessage(), $expectedErrorMessage);
            }

        },
            [
                'examples' => [
                    // Correct data , successfully create notification
                    [[
                        ['person_id_faculty_assigned' => 1],
                        ['person_id_faculty_assigned' => 2]
                    ], 1, 1, 1, 'academic-updates-reminder', null],
                    // Correct data , successfully create notification
                    [
                        [
                            ['person_id_faculty_assigned' => 1],
                            ['person_id_faculty_assigned' => 2]
                        ]
                        , 1, 1, 1, 'academic-updates-cancelled', null],
                    // Incorrect request id , Throws Exception
                    [[], 1, 'invalid', 1, 'test', 'Academic Update Not Found'],
                    // Incorrect logged In user , Throws Exception
                    [[], 1, 1, 'invalid', 'test', 'Coordinator Not Found']
                ]
            ]
        );
    }
}

