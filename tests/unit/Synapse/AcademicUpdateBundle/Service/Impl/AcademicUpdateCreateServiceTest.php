<?php
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDetailsDto;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDetailsStudentDto;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDto;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestRepository;
use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateCreateService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\SynapseConstant;


class AcademicUpdateCreateServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;
    
    private $academicUpdateSaveArray = [
        "1" => [
            "request_id" => "1",
            "request_name" => "AUR1",
            "request_description" => "AUR1 Desc",
            "request_created" => "09/26/2017",
            "request_due" => "09/30/2017",
            "request_complete_status" => 0,
            "save_type" => "save",
            "request_details" => [
                [
                    "subject_course" => "subject",
                    "department_name" => "department",
                    "academic_year_name" => "201718",
                    "academic_term_name" => "2",
                    "course_section_name" => "course section",
                    "student_details" => [
                        [
                            "academic_update_id" => 1,
                            "student_id" => 1,
                            "student_firstname" => "testau1",
                            "student_lastname" => "Lastname",
                            "student_risk" => "High",
                            "student_grade" => "P",
                            "student_absences" => 2,
                            "student_comments" => "",
                            "student_refer" => false,
                            "student_send" => false,
                            "is_bypassed" => false,
                            "student_academic_assist_refer" => false
                        ]
                    ],
                    "indexCount" => 0
                ]
            ]
        ],
        "2" => [
            "request_id" => "2",
            "request_name" => "AUR2",
            "request_description" => "AUR2 Desc",
            "request_created" => "09/26/2017",
            "request_due" => "09/30/2017",
            "request_complete_status" => 0,
            "save_type" => "save",
            "request_details" => [
                [
                    "subject_course" => "subject",
                    "department_name" => "department",
                    "academic_year_name" => "201718",
                    "academic_term_name" => "2",
                    "course_section_name" => "course section",
                    "student_details" => [
                        [
                            "academic_update_id" => 2,
                            "student_id" => 2,
                            "student_firstname" => "testau2",
                            "student_lastname" => "Lastname",
                            "student_risk" => null,
                            "student_grade" => null,
                            "student_absences" => null,
                            "student_comments" => null,
                            "student_refer" => false,
                            "student_send" => false,
                            "is_bypassed" => false,
                            "student_academic_assist_refer" => false
                        ]
                    ],
                    "indexCount" => 0
                ]
            ]
        ],
        "3" => [
            "request_id" => "2",
            "request_name" => "AUR2",
            "request_description" => "AUR2 Desc",
            "request_created" => "09/26/2017",
            "request_due" => "09/30/2017",
            "request_complete_status" => 0,
            "save_type" => "save",
            "request_details" => [
                [
                    "subject_course" => "subject",
                    "department_name" => "department",
                    "academic_year_name" => "201718",
                    "academic_term_name" => "2",
                    "course_section_name" => "course section",
                    "student_details" => [
                        [
                            "academic_update_id" => 2,
                            "student_id" => 2,
                            "student_firstname" => "testau2",
                            "student_lastname" => "Lastname",
                            "student_risk" => "Hi", // invalid
                            "student_grade" => null,
                            "student_absences" => null,
                            "student_comments" => null,
                            "student_refer" => false,
                            "student_send" => false,
                            "is_bypassed" => false,
                            "student_academic_assist_refer" => false
                        ]
                    ],
                    "indexCount" => 0
                ]
            ]
        ],
        "4" => [
            "request_id" => "2",
            "request_name" => "AUR2",
            "request_description" => "AUR2 Desc",
            "request_created" => "09/26/2017",
            "request_due" => "09/30/2017",
            "request_complete_status" => 0,
            "save_type" => "save",
            "request_details" => [
                [
                    "subject_course" => "subject",
                    "department_name" => "department",
                    "academic_year_name" => "201718",
                    "academic_term_name" => "2",
                    "course_section_name" => "course section",
                    "student_details" => [
                        [
                            "academic_update_id" => 2,
                            "student_id" => 2,
                            "student_firstname" => "testau2",
                            "student_lastname" => "Lastname",
                            "student_risk" => "High",
                            "student_grade" => "P11", // invalid
                            "student_absences" => null,
                            "student_comments" => null,
                            "student_refer" => false,
                            "student_send" => false,
                            "is_bypassed" => false,
                            "student_academic_assist_refer" => false
                        ]
                    ],
                    "indexCount" => 0
                ]
            ]
        ],
        "5" => [
            "request_id" => "2",
            "request_name" => "AUR2",
            "request_description" => "AUR2 Desc",
            "request_created" => "09/26/2017",
            "request_due" => "09/30/2017",
            "request_complete_status" => 0,
            "save_type" => "save",
            "request_details" => [
                [
                    "subject_course" => "subject",
                    "department_name" => "department",
                    "academic_year_name" => "201718",
                    "academic_term_name" => "2",
                    "course_section_name" => "course section",
                    "student_details" => [
                        [
                            "academic_update_id" => 2,
                            "student_id" => 2,
                            "student_firstname" => "testau2",
                            "student_lastname" => "Lastname",
                            "student_risk" => "High",
                            "student_grade" => "A",
                            "student_absences" => 1000, // invalid
                            "student_comments" => null,
                            "student_refer" => false,
                            "student_send" => false,
                            "is_bypassed" => false,
                            "student_academic_assist_refer" => false
                        ]
                    ],
                    "indexCount" => 0
                ]
            ]
        ]
    ];

    private function setAcademicUpdateDto($inputArray)
    {
        $academicUpdateDto = new AcademicUpdateDto();
        $academicUpdateDto->setRequestId($inputArray['request_id']);
        $academicUpdateDto->setRequestName($inputArray['request_name']);
        $academicUpdateDto->setRequestDescription($inputArray['request_description']);
        $academicUpdateDto->setRequestCreated($inputArray['request_created']);
        $academicUpdateDto->setSaveType($inputArray['save_type']);

        $requestDetailsArray = [];
        foreach ($inputArray['request_details'] as $requestDetails) {
            $academicUpdateDetailsDto = new AcademicUpdateDetailsDto();
            $academicUpdateDetailsDto->setSubjectCourse($requestDetails['subject_course']);
            $academicUpdateDetailsDto->setDepartmentName($requestDetails['department_name']);
            $academicUpdateDetailsDto->setAcademicYearName($requestDetails['academic_year_name']);
            $academicUpdateDetailsDto->setAcademicTermName($requestDetails['academic_term_name']);
            $academicUpdateDetailsDto->setCourseSectionName($requestDetails['course_section_name']);

            $studentDetailsArray = [];
            foreach ($requestDetails['student_details'] as $studentDetails) {
                $academicUpdateDetailsStudentDto = new AcademicUpdateDetailsStudentDto();
                $academicUpdateDetailsStudentDto->setAcademicUpdateId($studentDetails['academic_update_id']);
                $academicUpdateDetailsStudentDto->setStudentId($studentDetails['student_id']);
                $academicUpdateDetailsStudentDto->setStudentFirstname($studentDetails['student_firstname']);
                $academicUpdateDetailsStudentDto->setStudentLastname($studentDetails['student_lastname']);
                $academicUpdateDetailsStudentDto->setStudentRisk($studentDetails['student_risk']);
                $academicUpdateDetailsStudentDto->setStudentGrade($studentDetails['student_grade']);
                $academicUpdateDetailsStudentDto->setStudentAbsences($studentDetails['student_absences']);
                $academicUpdateDetailsStudentDto->setStudentComments($studentDetails['student_comments']);
                $academicUpdateDetailsStudentDto->setStudentAcademicAssistRefer($studentDetails['student_refer']);
                $academicUpdateDetailsStudentDto->setStudentSend($studentDetails['student_send']);
                $academicUpdateDetailsStudentDto->setIsBypassed($studentDetails['is_bypassed']);
                $academicUpdateDetailsStudentDto->setStudentAcademicAssistRefer($studentDetails['student_academic_assist_refer']);
                $studentDetailsArray [] = $academicUpdateDetailsStudentDto;
            }
            $academicUpdateDetailsDto->setStudentDetails($studentDetailsArray);
            $requestDetailsArray[] = $academicUpdateDetailsDto;
        }

        $academicUpdateDto->setRequestDetails($requestDetailsArray);
        return $academicUpdateDto;
    }

    public function testSaveAcademicUpdateUnderRequest()
    {
        $this->specify("Test Save Academic Update Under Request", function ($inputArray, $errorType, $expectedExceptionClass, $expectedExceptionMessage, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockContainerForEntityValidationService = $this->getMock('Container', ['get']);

            // Mock Repository
            $mockPersonRepository = $this->getMock('PersonRepository', ['find']);
            $mockAcademicUpdateRepository = $this->getMock('academicUpdateRepository', ['find', 'getAssignedFacultiesByAcademicUpdate', 'flush']);
            $mockAcademicUpdateRequestRepository = $this->getMock('academicUpdateRequestRepository', ['find', 'getAcademicUpdatesInOpenRequestsForStudent']);

            // Mock Service
            $mockAcademicYearService = $this->getMock('academicYearService', ['getCurrentOrgAcademicYearId']);
            $mockDataProcessingUtilityService = $this->getMock('DataProcessingUtilityService', ['setErrorMessageOrValueInArray']);

            $mockResqueService = $this->getMockBuilder('BCC\ResqueBundle\Resque')
                ->disableOriginalConstructor()
                ->setMethods([])
                ->getMock();

            $mockValidator = $this->getMock('Validator', ['validate']);
            $mockContainerForEntityValidationService->method('get')->willReturnMap([
                [
                    SynapseConstant::VALIDATOR,
                    $mockValidator
                ]
            ]);

            $entityValidationService = new EntityValidationService($mockRepositoryResolver, $mockLogger, $mockContainerForEntityValidationService);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        AcademicUpdateRepository::REPOSITORY_KEY,
                        $mockAcademicUpdateRepository
                    ],
                    [
                        AcademicUpdateRequestRepository::REPOSITORY_KEY,
                        $mockAcademicUpdateRequestRepository
                    ],
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        AcademicYearService::SERVICE_KEY,
                        $mockAcademicYearService
                    ],
                    [
                        SynapseConstant::RESQUE_CLASS_KEY,
                        $mockResqueService
                    ],
                    [
                        EntityValidationService::SERVICE_KEY,
                        $entityValidationService
                    ]
                ]);

            $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();

            $mockAcademicUpdate = null;
            $mockAcademicUpdateRequest = null;
            if ($errorType == 'invalid_au_id') {
                $mockAcademicUpdateRepository->method('find')->willThrowException(new SynapseValidationException("Academic Update ID 1 is not valid at the organization."));
            } elseif ($errorType == 'invalid_student_id') {
                $mockPersonRepository->method('find')->willThrowException(new SynapseValidationException("Student ID 1 is not valid at the organization."));
            } elseif ($errorType == 'invalid_risk') {
                $mockAcademicUpdateRepository->method('find')->willThrowException(new SynapseValidationException(
                    [
                        [
                            "failureRiskLevel" => "Not a valid value for failure risk level, valid values are 'High' or 'Low'"
                        ]
                    ]
                ));
            } elseif ($errorType == 'invalid_grade') {
                $mockAcademicUpdateRepository->method('find')->willThrowException(new SynapseValidationException(
                    [
                        [
                            "grade" => "Not a valid value for grade, valid values are 'A', 'B', 'C', 'D' , 'F', 'P', 'Pass'"
                        ]
                    ]
                ));
            } elseif ($errorType == 'invalid_absence') {
                $mockAcademicUpdateRepository->method('find')->willThrowException(new SynapseValidationException(
                    [
                        [
                            "absence" => "absence value should not be greater than 99"
                        ]
                    ]
                ));
            } else {
                $mockStudent = new Person();
                $mockPersonRepository->method('find')->willReturn($mockStudent);


                $mockAcademicUpdate = $this->getMock('AcademicUpdate', [
                    'getId',
                    'setOrgCourses',
                    'getOrgCourses',
                    'setUpdateDate',
                    'setFailureRiskLevel',
                    'setGrade',
                    'setAbsence',
                    'setComment',
                    'setReferForAssistance',
                    'setSendToStudent',
                    'setIsBypassed',
                    'setPersonFacultyResponded',
                    'setStatus',
                    'getAcademicUpdateRequest'
                ]);


                $mockOrgCourse = $this->getMock('OrgCourses', ['getId', 'getCourseName']);
                $mockOrgCourse->method('getId')->willReturn(1);
                $mockOrgCourse->method('getCourseName')->willReturn('Course Name');
                $mockAcademicUpdate->method('getOrgCourses')->willReturn($mockOrgCourse);

                $mockAcademicUpdateRepository->method('find')->willReturn($mockAcademicUpdate);

                $mockAcademicUpdateRequest = new AcademicUpdateRequest();
                $mockAcademicUpdateRequestRepository->method('find')->willReturn($mockAcademicUpdateRequest);
                $mockResqueService->method('enqueue')->willReturn(1);
            }

            $loggedInPerson = $this->getPersonInstance();
            $academicUpdateDto = $this->setAcademicUpdateDto($inputArray);

            try {
                $academicUpdateCreateService = new AcademicUpdateCreateService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $academicUpdateCreateService->saveAcademicUpdateUnderRequest($academicUpdateDto, $loggedInPerson);
                $this->assertEquals($expectedResult, $results);
            } catch (SynapseException $exception) {
                verify($exception)->isInstanceOf($expectedExceptionClass);
                verify($exception->getMessage())->equals($expectedExceptionMessage);
            }
        }, [
            'examples' => [
                // Test0: invalid AU Id
                [
                    $this->academicUpdateSaveArray[1],
                    'invalid_academic_update_id',
                    '\Synapse\CoreBundle\Exception\SynapseValidationException',
                    'Academic Update ID 1 is not valid at the organization.',
                    1
                ],
                // Test1: Valid Update
                [
                    $this->academicUpdateSaveArray[1],
                    null,
                    null,
                    null,
                    1
                ],
                // Test2: risk, comments, grade set to null
                [
                    $this->academicUpdateSaveArray[2],
                    null,
                    null,
                    null,
                    1
                ],
                // Test3: invalid risk
                [
                    $this->academicUpdateSaveArray[3],
                    'invalid_risk',
                    '\Synapse\CoreBundle\Exception\SynapseValidationException',
                    [
                        [
                            "failureRiskLevel" => "Not a valid value for failure risk level, valid values are 'High' or 'Low'"
                        ]
                    ],
                    1
                ],
                // Test4: invalid grade
                [
                    $this->academicUpdateSaveArray[4],
                    'invalid_grade',
                    '\Synapse\CoreBundle\Exception\SynapseValidationException',
                    [
                        [
                            "grade" => "Not a valid value for grade, valid values are 'A', 'B', 'C', 'D' , 'F', 'P', 'Pass'"
                        ]
                    ],
                    1
                ],
                // Test5: invalid absence
                [
                    $this->academicUpdateSaveArray[5],
                    'invalid_absence',
                    '\Synapse\CoreBundle\Exception\SynapseValidationException',
                    [
                        [
                            "absence" => "absence value should not be greater than 99"
                        ]
                    ],
                    1
                ],
                // Test6: invalid Student Id
                [
                    $this->academicUpdateSaveArray[1],
                    'invalid_student_id',
                    '\Synapse\CoreBundle\Exception\SynapseValidationException',
                    'Student ID 1 is not valid at the organization.',
                    1
                ]
            ]
        ]);
    }

    private function getPersonInstance()
    {
        $organization = new Organization();
        $organization->setSendToStudent(true);
        $person = new Person();
        $person->setId(123);
        $person->setOrganization($organization);
        return $person;
    }
}