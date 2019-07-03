<?php

use Synapse\AcademicBundle\DTO\CourseFacultyDto;
use Synapse\AcademicBundle\Entity\OrgAcademicTerms;
use Synapse\AcademicBundle\Entity\OrgAcademicYear;
use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\AcademicBundle\EntityDto\CoordinatorCourseDto;
use Synapse\AcademicBundle\EntityDto\AcademicUpdateCourseDTO;
use Synapse\AcademicBundle\EntityDto\AcademicUpdateCourseListDTO;
use Synapse\AcademicBundle\EntityDto\CourseFacultyDTO as entityCourseFacultyDTO;
use Synapse\AcademicBundle\EntityDto\CourseFacultyListDTO;
use Synapse\AcademicBundle\EntityDto\CourseListDTO;
use Synapse\AcademicBundle\EntityDto\CourseStudentsDto;
use Synapse\AcademicBundle\EntityDto\FacultyDetailsDto;
use Synapse\AcademicBundle\EntityDto\SingleCourseDto;
use Synapse\AcademicBundle\EntityDto\StudentDetailsDto;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\AcademicBundle\Service\Impl\CourseService;
use Synapse\AcademicUpdateBundle\Entity\AcademicRecord;
use Synapse\AcademicUpdateBundle\Repository\AcademicRecordRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestCourseRepository;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgPermissionset;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\FacultyService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\StudentService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\UploadBundle\Service\Impl\CourseUploadService;

class CourseServiceTest extends \Codeception\Test\Unit
{
    use \Codeception\Specify;

    private $courseId = 1;

    private $studentId = 1;

    private $courseStudentArray = [
        'course_id' => 1,
        'student_id' => 1,
    ];
    private $facultyId = 1;
    private $courseFacultyArray = [
        'course_id' => 1,
        'faculty_id' => 1,
        'permissionset_name' => 'PartialAccess'
    ];

    public function testGetStudentIdsInCourse()
    {
        $this->specify("Test to get the list of student id's specific to course", function ($courseId, $organizationId, $studentIds, $externalCourseId, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockOrgCourseRepository = $this->getMock('OrgCoursesRepository', array('getAllStudentsInCourse'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgCoursesRepository::REPOSITORY_KEY,
                        $mockOrgCourseRepository
                    ]
                ]);

            if (empty($studentIds)) {
                $courseStudents = [];
            } else {
                foreach ($studentIds as $studentId) {
                    $courseStudents[] = ["student_id" => $studentId];
                }
            }
            $mockOrgCourseRepository->method('getAllStudentsInCourse')->willReturn($courseStudents);

            $courseService = new CourseService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $courseService->getStudentIdsInCourse($courseId, $organizationId, $externalCourseId);
            $this->assertEquals($results, $expectedResult);

        }, [
            'examples' => [
                // No students for a course
                [
                    257925,
                    203,
                    [],
                    100433,
                    $this->getCourseStudents(100433)
                ],
                // Invalid organization passed
                [
                    257921,
                    -98,
                    [],
                    100431,
                    $this->getCourseStudents(100431)
                ],
                // List of students for a specific course
                [
                    235860,
                    203,
                    [
                        4879701,
                        4879100,
                        4879301,
                        4878818
                    ],
                    100070,
                    $this->getCourseStudents(100070)
                ],
                // Invalid course for the organization
                [
                    2343,
                    203,
                    [],
                    10025,
                    $this->getCourseStudents(10025)
                ],
                [
                    // List of students for a course
                    235859,
                    203,
                    [
                        4879709,
                        4879886,
                        4879108,
                        4879284,
                        4879308,
                        4879470,
                        4878820,
                        4878965
                    ],
                    100061,
                    $this->getCourseStudents(100061)
                ]
            ]
        ]);
    }

    public function testListCoursesForStudent()
    {
        $this->specify("", function ($expectedResults, $mockStudentId = null, $mockLoggedInUserId = null, $mockOrganizationId = null, $mockYearString = null,
                                     $hasOrganizationAccess = null, $hasStudentAccess = null, $viewCoursesAccess = null, $createViewAcademicUpdatesAccess = null,
                                     $viewAllAcademicUpdateCoursesFlag = null, $viewAllFinalGrades = null, $mockStudentCourseData = null, $orgCourseFacultyList = null,
                                     $mockAcademicRecordDataArray = null) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockRbacManager = $this->getMock('rbacManager',
                [
                    'checkAccessToOrganizationUsingPersonId',
                    'assertPermissionToEngageWithStudents',
                    'hasStudentAccess'
                ]
            );
            $mockOrgAcademicYearRepository = $this->getMock('orgAcademicYearRepository', ['getCurrentAcademicYear']);
            $mockOrgCoursesRepository = $this->getMock('orgCoursesRepository', ['getCoursesForStudent', 'getFacultyList']);
            $mockAcademicRecordRepository = $this->getMock('academicRecordRepository', ['findOneBy']);
            $mockDateUtilityService = $this->getMock('dateUtilityService', ['adjustDateTimeToOrganizationTimezone']);

            $mockContainer->method('get')->willReturnMap(
                [
                    [SynapseConstant::TINYRBAC_MANAGER, $mockRbacManager],
                    [DateUtilityService::SERVICE_KEY, $mockDateUtilityService]
                ]
            );

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [OrgAcademicYearRepository::REPOSITORY_KEY, $mockOrgAcademicYearRepository],
                    [OrgCoursesRepository::REPOSITORY_KEY, $mockOrgCoursesRepository],
                    [AcademicRecordRepository::REPOSITORY_KEY, $mockAcademicRecordRepository]
                ]
            );

            if ($hasOrganizationAccess) {
                $mockRbacManager->method('checkAccessToOrganizationUsingPersonId')->willReturn(true);
            } else {
                $mockRbacManager->method('checkAccessToOrganizationUsingPersonId')->willThrowException(new AccessDeniedException("Unauthorized access to organization: $mockOrganizationId"));
            }

            if ($hasStudentAccess) {
                $mockRbacManager->method('assertPermissionToEngageWithStudents')->willReturn(true);
            } else {
                $mockRbacManager->method('assertPermissionToEngageWithStudents')->willThrowException(new AccessDeniedException('This student is a non participant for the current academic year'));
            }

            if (!is_string($expectedResults)) {
                $mockRbacManager->expects($this->at(2))->method('hasStudentAccess')->willReturn($viewCoursesAccess);
                $mockRbacManager->expects($this->at(3))->method('hasStudentAccess')->willReturn($createViewAcademicUpdatesAccess);
                $mockRbacManager->expects($this->at(4))->method('hasStudentAccess')->willReturn($viewAllAcademicUpdateCoursesFlag);
                $mockRbacManager->expects($this->at(5))->method('hasStudentAccess')->willReturn($viewAllFinalGrades);
            }

            if ($mockYearString == 'current') {
                $mockOrgAcademicYearRepository->method('getCurrentAcademicYear')->willReturn(['year_id' => '201718']);
            }

            $mockOrgCoursesRepository->method('getCoursesForStudent')->willReturn($mockStudentCourseData);
            $mockOrgCoursesRepository->method('getFacultyList')->willReturn($orgCourseFacultyList);

            for ($index = 0; $index < count($mockAcademicRecordDataArray); $index++) {
                $mockAcademicRecordRepository->expects($this->at($index))->method('findOneBy')->willReturn($mockAcademicRecordDataArray[$index]);

                if ($mockAcademicRecordDataArray[$index]) {
                    $updateDate = $mockAcademicRecordDataArray[$index]->getUpdateDate();
                    $mockDateUtilityService->method('adjustDateTimeToOrganizationTimezone')->willReturn($updateDate);
                }
            }

            $courseService = new CourseService($mockRepositoryResolver, $mockLogger, $mockContainer);


            try {
                $results = $courseService->listCoursesForStudent($mockStudentId, $mockLoggedInUserId, $mockOrganizationId, $mockYearString);
                $this->assertEquals($expectedResults, $results);
            } catch (SynapseException $e) {
                $this->assertEquals($expectedResults, $e->getMessage());
            }

        }, [
            'examples' => [
                //Logged in person is not in the same organization as the requested student
                [
                    'Unauthorized access to organization: 2',
                    1,
                    1,
                    2,
                    'all'
                ],
                //Logged in person does not have access to the student.
                [
                    'This student is a non participant for the current academic year',
                    1,
                    1,
                    1,
                    'all',
                    true
                ],
                //Logged in person does not have view courses permission for the specified course
                [
                    $this->buildCoordinatorCourseDTO(1, 0, []),
                    1,
                    1,
                    1,
                    'all',
                    true,
                    true,
                    false,
                    true,
                    true,
                    true
                ],
                //Logged in user has access to everything
                [
                    $this->buildCoordinatorCourseDTO(1, 3, "all_access", true, true, true),
                    1,
                    1,
                    1,
                    'all',
                    true,
                    true,
                    true,
                    true,
                    true,
                    true,
                    $this->buildStudentCourseData(),
                    $this->buildFacultyList(),
                    $this->buildAcademicRecordMockData()
                ],
                //Logged in user does not have permission to final grades, but has permission to everything else.
                [
                    $this->buildCoordinatorCourseDTO(1, 3, "no_final_grade_permissions", true, true, false),
                    1,
                    1,
                    1,
                    'all',
                    true,
                    true,
                    true,
                    true,
                    true,
                    false,
                    $this->buildStudentCourseData(),
                    $this->buildFacultyList(),
                    $this->buildAcademicRecordMockData()
                ],
                //Logged in user has access to everything, filtering on the current year only.
                [
                    $this->buildCoordinatorCourseDTO(1, 2, "current_year", true, true, true),
                    1,
                    1,
                    1,
                    'current',
                    true,
                    true,
                    true,
                    true,
                    true,
                    true,
                    $this->buildStudentCourseData(2),
                    $this->buildFacultyList(),
                    $this->buildAcademicRecordMockData(2)
                ],
            ]
        ]);
    }

    /**
     * @param int $studentId
     * @param int $courseCount
     * @param string|null $courseListIndicator
     * @param boolean|null $createViewAcademicUpdate
     * @param boolean|null $viewAllAcademicUpdateCourses
     * @param boolean|null $viewAllFinalGrades
     * @return CoordinatorCourseDto
     */
    private function buildCoordinatorCourseDTO($studentId, $courseCount, $courseListIndicator = null, $createViewAcademicUpdate = null, $viewAllAcademicUpdateCourses = null, $viewAllFinalGrades = null)
    {
        $coordinatorCourseDTO = new CoordinatorCourseDto();
        $coordinatorCourseDTO->setTotalCourse($courseCount);
        $coordinatorCourseDTO->setStudentId($studentId);
        $coordinatorCourseDTO->setCreateViewAcademicUpdate($createViewAcademicUpdate);
        $coordinatorCourseDTO->setViewAllAcademicUpdateCourses($viewAllAcademicUpdateCourses);
        $coordinatorCourseDTO->setViewAllFinalGrades($viewAllFinalGrades);


        switch ($courseListIndicator) {

            case "no_final_grade_permissions":
                $currentYearCourseListDto = new AcademicUpdateCourseListDTO();
                $currentYearCourseListDto->setYear('201718');
                $currentYearCourseListDto->setTerm('Current Term');
                $currentYearCourseListDto->setCollege('COLLEGE');
                $currentYearCourseListDto->setDepartment('DEPARTMENT');
                $currentYearCourseListDto->setCurrentOrFutureTerm('1');

                $courseOneDto = new AcademicUpdateCourseDTO();
                $courseOneDto->setCourseId(123);
                $courseOneDto->setUniqueCourseSectionId('1234');
                $courseOneDto->setSubjectCourse('SUBJECT123');
                $courseOneDto->setSectionId('1');
                $courseOneDto->setCourseTitle('COURSE NAME');
                $courseOneDto->setTime('DAYS_TIMES');
                $courseOneDto->setLocation('LOCATION');
                $courseOneDto->setInProgressGrade('F');
                $courseOneDto->setInProgressGradeUpdateDate(new Datetime('2017-01-01 00:00:00'));
                $courseOneDto->setDateStamp(new Datetime('2017-01-01 00:00:00'));

                $facultyDetailsDtoOne = new FacultyDetailsDto();
                $facultyDetailsDtoOne->setFacultyId(125);
                $facultyDetailsDtoOne->setFacultyName('P.H. Punit');

                $facultyDetailsDtoTwo = new FacultyDetailsDto();
                $facultyDetailsDtoTwo->setFacultyId(126);
                $facultyDetailsDtoTwo->setFacultyName('Codec Eption');

                $courseOneFacultyDetails = [
                    $facultyDetailsDtoOne,
                    $facultyDetailsDtoTwo
                ];

                $courseOneDto->setFacultyDetails($courseOneFacultyDetails);

                $courseTwoDto = new AcademicUpdateCourseDTO();
                $courseTwoDto->setCourseId(124);
                $courseTwoDto->setUniqueCourseSectionId('1235');
                $courseTwoDto->setSubjectCourse('SUBJECT124');
                $courseTwoDto->setSectionId('1');
                $courseTwoDto->setCourseTitle('COURSE NAME');
                $courseTwoDto->setTime('DAYS_TIMES');
                $courseTwoDto->setLocation('LOCATION');

                $facultyDetailsDtoOne = new FacultyDetailsDto();
                $facultyDetailsDtoOne->setFacultyId(123);
                $facultyDetailsDtoOne->setFacultyName('Dev Elopment');

                $facultyDetailsDtoTwo = new FacultyDetailsDto();
                $facultyDetailsDtoTwo->setFacultyId(124);
                $facultyDetailsDtoTwo->setFacultyName('Tes Ting');

                $courseTwoFacultyDetails = [
                    $facultyDetailsDtoOne,
                    $facultyDetailsDtoTwo
                ];

                $courseTwoDto->setFacultyDetails($courseTwoFacultyDetails);

                $courseListArray = [
                    $courseOneDto,
                    $courseTwoDto
                ];

                $currentYearCourseListDto->setCourse($courseListArray);

                $previousYearCourseListDto = new AcademicUpdateCourseListDTO();
                $previousYearCourseListDto->setYear('201617');
                $previousYearCourseListDto->setTerm('Past Term');
                $previousYearCourseListDto->setCollege('COLLEGE');
                $previousYearCourseListDto->setDepartment('DEPARTMENT');
                $previousYearCourseListDto->setCurrentOrFutureTerm('0');

                $courseThreeDto = new AcademicUpdateCourseDTO();
                $courseThreeDto->setCourseId(101);
                $courseThreeDto->setUniqueCourseSectionId('101');
                $courseThreeDto->setSubjectCourse('SUBJECT101');
                $courseThreeDto->setSectionId('1');
                $courseThreeDto->setCourseTitle('COURSE NAME');
                $courseThreeDto->setTime('DAYS_TIMES');
                $courseThreeDto->setLocation('LOCATION');
                $courseThreeDto->setDateStamp(new Datetime('2017-01-01 00:00:00'));

                $facultyDetailsDtoOne = new FacultyDetailsDto();
                $facultyDetailsDtoOne->setFacultyId(126);
                $facultyDetailsDtoOne->setFacultyName('N.T. Year');

                $courseThreeFacultyDetails = [
                    $facultyDetailsDtoOne
                ];

                $courseThreeDto->setFacultyDetails($courseThreeFacultyDetails);

                $previousYearCourseListDto->setCourse([$courseThreeDto]);

                $courseList = [
                    $currentYearCourseListDto,
                    $previousYearCourseListDto
                ];
                break;
            case "all_access":
                $currentYearCourseListDto = new AcademicUpdateCourseListDTO();
                $currentYearCourseListDto->setYear('201718');
                $currentYearCourseListDto->setTerm('Current Term');
                $currentYearCourseListDto->setCollege('COLLEGE');
                $currentYearCourseListDto->setDepartment('DEPARTMENT');
                $currentYearCourseListDto->setCurrentOrFutureTerm('1');

                $courseOneDto = new AcademicUpdateCourseDTO();
                $courseOneDto->setCourseId(123);
                $courseOneDto->setUniqueCourseSectionId('1234');
                $courseOneDto->setSubjectCourse('SUBJECT123');
                $courseOneDto->setSectionId('1');
                $courseOneDto->setCourseTitle('COURSE NAME');
                $courseOneDto->setTime('DAYS_TIMES');
                $courseOneDto->setLocation('LOCATION');
                $courseOneDto->setInProgressGrade('F');
                $courseOneDto->setInProgressGradeUpdateDate(new Datetime('2017-01-01 00:00:00'));
                $courseOneDto->setDateStamp(new Datetime('2017-01-01 00:00:00'));

                $facultyDetailsDtoOne = new FacultyDetailsDto();
                $facultyDetailsDtoOne->setFacultyId(125);
                $facultyDetailsDtoOne->setFacultyName('P.H. Punit');

                $facultyDetailsDtoTwo = new FacultyDetailsDto();
                $facultyDetailsDtoTwo->setFacultyId(126);
                $facultyDetailsDtoTwo->setFacultyName('Codec Eption');

                $courseOneFacultyDetails = [
                    $facultyDetailsDtoOne,
                    $facultyDetailsDtoTwo
                ];

                $courseOneDto->setFacultyDetails($courseOneFacultyDetails);

                $courseTwoDto = new AcademicUpdateCourseDTO();
                $courseTwoDto->setCourseId(124);
                $courseTwoDto->setUniqueCourseSectionId('1235');
                $courseTwoDto->setSubjectCourse('SUBJECT124');
                $courseTwoDto->setSectionId('1');
                $courseTwoDto->setCourseTitle('COURSE NAME');
                $courseTwoDto->setTime('DAYS_TIMES');
                $courseTwoDto->setLocation('LOCATION');

                $facultyDetailsDtoOne = new FacultyDetailsDto();
                $facultyDetailsDtoOne->setFacultyId(123);
                $facultyDetailsDtoOne->setFacultyName('Dev Elopment');

                $facultyDetailsDtoTwo = new FacultyDetailsDto();
                $facultyDetailsDtoTwo->setFacultyId(124);
                $facultyDetailsDtoTwo->setFacultyName('Tes Ting');

                $courseTwoFacultyDetails = [
                    $facultyDetailsDtoOne,
                    $facultyDetailsDtoTwo
                ];

                $courseTwoDto->setFacultyDetails($courseTwoFacultyDetails);

                $courseListArray = [
                    $courseOneDto,
                    $courseTwoDto
                ];

                $currentYearCourseListDto->setCourse($courseListArray);

                $previousYearCourseListDto = new AcademicUpdateCourseListDTO();
                $previousYearCourseListDto->setYear('201617');
                $previousYearCourseListDto->setTerm('Past Term');
                $previousYearCourseListDto->setCollege('COLLEGE');
                $previousYearCourseListDto->setDepartment('DEPARTMENT');
                $previousYearCourseListDto->setCurrentOrFutureTerm('0');

                $courseThreeDto = new AcademicUpdateCourseDTO();
                $courseThreeDto->setCourseId(101);
                $courseThreeDto->setUniqueCourseSectionId('101');
                $courseThreeDto->setSubjectCourse('SUBJECT101');
                $courseThreeDto->setSectionId('1');
                $courseThreeDto->setCourseTitle('COURSE NAME');
                $courseThreeDto->setTime('DAYS_TIMES');
                $courseThreeDto->setLocation('LOCATION');
                $courseThreeDto->setFinalGrade('F');
                $courseThreeDto->setFinalGradeUpdateDate(new Datetime('2017-01-01 00:00:00'));
                $courseThreeDto->setDateStamp(new Datetime('2017-01-01 00:00:00'));

                $facultyDetailsDtoOne = new FacultyDetailsDto();
                $facultyDetailsDtoOne->setFacultyId(126);
                $facultyDetailsDtoOne->setFacultyName('N.T. Year');

                $courseThreeFacultyDetails = [
                    $facultyDetailsDtoOne
                ];

                $courseThreeDto->setFacultyDetails($courseThreeFacultyDetails);

                $previousYearCourseListDto->setCourse([$courseThreeDto]);

                $courseList = [
                    $currentYearCourseListDto,
                    $previousYearCourseListDto
                ];
                break;
            case "current_year":
                $currentYearCourseListDto = new AcademicUpdateCourseListDTO();
                $currentYearCourseListDto->setYear('201718');
                $currentYearCourseListDto->setTerm('Current Term');
                $currentYearCourseListDto->setCollege('COLLEGE');
                $currentYearCourseListDto->setDepartment('DEPARTMENT');
                $currentYearCourseListDto->setCurrentOrFutureTerm('1');

                $courseOneDto = new AcademicUpdateCourseDTO();
                $courseOneDto->setCourseId(123);
                $courseOneDto->setUniqueCourseSectionId('1234');
                $courseOneDto->setSubjectCourse('SUBJECT123');
                $courseOneDto->setSectionId('1');
                $courseOneDto->setCourseTitle('COURSE NAME');
                $courseOneDto->setTime('DAYS_TIMES');
                $courseOneDto->setLocation('LOCATION');
                $courseOneDto->setInProgressGrade('F');
                $courseOneDto->setInProgressGradeUpdateDate(new Datetime('2017-01-01 00:00:00'));
                $courseOneDto->setDateStamp(new Datetime('2017-01-01 00:00:00'));

                $facultyDetailsDtoOne = new FacultyDetailsDto();
                $facultyDetailsDtoOne->setFacultyId(125);
                $facultyDetailsDtoOne->setFacultyName('P.H. Punit');

                $facultyDetailsDtoTwo = new FacultyDetailsDto();
                $facultyDetailsDtoTwo->setFacultyId(126);
                $facultyDetailsDtoTwo->setFacultyName('Codec Eption');

                $courseOneFacultyDetails = [
                    $facultyDetailsDtoOne,
                    $facultyDetailsDtoTwo
                ];

                $courseOneDto->setFacultyDetails($courseOneFacultyDetails);

                $courseTwoDto = new AcademicUpdateCourseDTO();
                $courseTwoDto->setCourseId(124);
                $courseTwoDto->setUniqueCourseSectionId('1235');
                $courseTwoDto->setSubjectCourse('SUBJECT124');
                $courseTwoDto->setSectionId('1');
                $courseTwoDto->setCourseTitle('COURSE NAME');
                $courseTwoDto->setTime('DAYS_TIMES');
                $courseTwoDto->setLocation('LOCATION');

                $facultyDetailsDtoOne = new FacultyDetailsDto();
                $facultyDetailsDtoOne->setFacultyId(123);
                $facultyDetailsDtoOne->setFacultyName('Dev Elopment');

                $facultyDetailsDtoTwo = new FacultyDetailsDto();
                $facultyDetailsDtoTwo->setFacultyId(124);
                $facultyDetailsDtoTwo->setFacultyName('Tes Ting');

                $courseTwoFacultyDetails = [
                    $facultyDetailsDtoOne,
                    $facultyDetailsDtoTwo
                ];

                $courseTwoDto->setFacultyDetails($courseTwoFacultyDetails);

                $courseListArray = [
                    $courseOneDto,
                    $courseTwoDto
                ];

                $currentYearCourseListDto->setCourse($courseListArray);

                $courseList = [
                    $currentYearCourseListDto
                ];
                break;
            default:
                $courseList = [];
        }

        $coordinatorCourseDTO->setCourseListTable($courseList);

        return $coordinatorCourseDTO;

    }

    /**
     * @param int|null $indexToRemove
     * @return array
     */
    private function buildStudentCourseData($indexToRemove = null)
    {
        $studentCourseData = [
            [
                'college_code' => 'COLLEGE',
                'dept_code' => 'DEPARTMENT',
                'org_academic_year_id' => 1,
                'org_academic_terms_id' => 2,
                'year_id' => '201718',
                'year_name' => 'Current Year',
                'term_name' => 'Current Term',
                'term_code' => 'CURTERM',
                'org_course_id' => 123,
                'subject_code' => 'SUBJECT',
                'course_number' => '123',
                'section_number' => '1',
                'course_section_id' => '1234',
                'course_name' => 'COURSE NAME',
                'location' => 'LOCATION',
                'days_times' => 'DAYS_TIMES',
                'start_date' => '2017-01-01 00:00:00',
                'end_date' => '2017-06-01 00:00:00',
                'current_or_future_term_course' => '1'
            ],
            [
                'college_code' => 'COLLEGE',
                'dept_code' => 'DEPARTMENT',
                'org_academic_year_id' => 1,
                'org_academic_terms_id' => 2,
                'year_id' => '201718',
                'year_name' => 'Current Year',
                'term_name' => 'Current Term',
                'term_code' => 'CURTERM',
                'org_course_id' => 124,
                'subject_code' => 'SUBJECT',
                'course_number' => '124',
                'section_number' => '1',
                'course_section_id' => '1235',
                'course_name' => 'COURSE NAME',
                'location' => 'LOCATION',
                'days_times' => 'DAYS_TIMES',
                'start_date' => '2017-01-01 00:00:00',
                'end_date' => '2017-06-01 00:00:00',
                'current_or_future_term_course' => '1'
            ],
            [
                'college_code' => 'COLLEGE',
                'dept_code' => 'DEPARTMENT',
                'org_academic_year_id' => 2,
                'org_academic_terms_id' => 3,
                'year_id' => '201617',
                'year_name' => 'Past Year',
                'term_name' => 'Past Term',
                'term_code' => 'PASTTERM',
                'org_course_id' => 101,
                'subject_code' => 'SUBJECT',
                'course_number' => '101',
                'section_number' => '1',
                'course_section_id' => '101',
                'course_name' => 'COURSE NAME',
                'location' => 'LOCATION',
                'days_times' => 'DAYS_TIMES',
                'start_date' => '2016-01-01 00:00:00',
                'end_date' => '2016-06-01 00:00:00',
                'current_or_future_term_course' => '0'
            ]
        ];

        if ($indexToRemove) {
            unset($studentCourseData[$indexToRemove]);
            return $studentCourseData;
        } else {
            return $studentCourseData;
        }
    }

    /**
     * @param int|null $indexToRemove
     * @return array
     */
    private function buildFacultyList($indexToRemove = null)
    {
        $facultyList = [
            [
                'personId' => 123,
                'personFirstName' => 'Dev',
                'personLastName' => 'Elopment',
                'id' => 124
            ],
            [
                'personId' => 124,
                'personFirstName' => 'Tes',
                'personLastName' => 'Ting',
                'id' => 124
            ],
            [
                'personId' => 125,
                'personFirstName' => 'P.H.',
                'personLastName' => 'Punit',
                'id' => 123
            ],
            [
                'personId' => 126,
                'personFirstName' => 'Codec',
                'personLastName' => 'Eption',
                'id' => 123
            ],
            [
                'personId' => 126,
                'personFirstName' => 'N.T.',
                'personLastName' => 'Year',
                'id' => 101
            ]
        ];

        if ($indexToRemove) {
            unset($facultyList[$indexToRemove]);
            return $facultyList;
        } else {
            return $facultyList;
        }
    }

    /**
     * @param null $indexToRemove
     * @return AcademicRecord[]|AcademicRecord|null
     */
    private function buildAcademicRecordMockData($indexToRemove = null)
    {
        $academicRecordArray = [
            $this->getAcademicRecordObject(1, 123, 1, new DateTime('2017-01-01 00:00:00'), null, 'F'),
            null,
            $this->getAcademicRecordObject(1, 101, 1, new DateTime('2017-01-01 00:00:00'), null, null, 'F'),
        ];

        if ($indexToRemove) {
            unset($academicRecordArray[$indexToRemove]);
            return $academicRecordArray;
        } else {
            return $academicRecordArray;
        }
    }

    /**
     * @param int $studentId
     * @param int $courseId
     * @param int $organizationId
     * @param DateTime|null $updateDate
     * @param int|null $absences
     * @param string|null $inProgressGrade
     * @param string|null $finalGrade
     * @param string|null $comment
     * @param string|null $failureRiskLevel
     * @return AcademicRecord
     */
    private function getAcademicRecordObject($studentId, $courseId, $organizationId, $updateDate = null, $absences = null, $inProgressGrade = null, $finalGrade = null, $comment = null, $failureRiskLevel = null)
    {
        $academicRecord = new AcademicRecord();
        $academicRecord->setPersonStudent($studentId);
        $academicRecord->setOrgCourses($courseId);
        $academicRecord->setOrganization($organizationId);
        $academicRecord->setUpdateDate($updateDate);
        $academicRecord->setAbsence($absences);
        $academicRecord->setInProgressGrade($inProgressGrade);
        $academicRecord->setFinalGrade($finalGrade);
        $academicRecord->setComment($comment);
        $academicRecord->setFailureRiskLevel($failureRiskLevel);

        if ($absences) {
            $academicRecord->setAbsenceUpdateDate($updateDate);
        }

        if ($inProgressGrade) {
            $academicRecord->setInProgressGradeUpdateDate($updateDate);
        }

        if ($finalGrade) {
            $academicRecord->setFinalGradeUpdateDate($updateDate);
        }

        if ($comment) {
            $academicRecord->setCommentUpdateDate($updateDate);
        }

        if ($failureRiskLevel) {
            $academicRecord->setFailureRiskLevelUpdateDate($updateDate);
        }


        return $academicRecord;
    }


    /**
     * Create CourseDTO
     *
     * @param array|int $courseId
     * @return array|CourseStudentDto
     */
    private function getCourseStudents($courseId)
    {
        $courseStudents = [
            '100433' => [],
            '100431' => [],
            '100070' => [4879701, 4879100, 4879301, 4878818],
            '10025' => [],
            '100061' => [4879709, 4879886, 4879108, 4879284, 4879308, 4879470, 4878820, 4878965]
        ];
        if (empty($courseStudents[$courseId])) {
            $courseStudentsDto = [];
        } else {
            $courseStudentsDto = new CourseStudentsDto();
            $courseStudentsDto->setCourseId($courseId);
            $courseStudentsDto->setStudents($courseStudents[$courseId]);
        }
        return $courseStudentsDto;
    }

    private $courseDataForCreate = [
        "1" => [
            "year_id" => "201718",
            "term_id" => "2",
            "college_code" => "Hall",
            "department_code" => "Fame",
            "subject_code" => "HOF",
            "course_number" => "2121",
            "course_name" => "Members101",
            "section_number" => "2",
            "course_section_id" => "14",
            "location" => "CantonOhio",
            "days_times" => "Every day, 9am-8pm",
            "credit_hours" => 10
        ],
        "2" => [
            "year_id" => "201718",
            "term_id" => "", // Invalid Term Id
            "college_code" => "Hall",
            "department_code" => "Fame",
            "subject_code" => "HOF",
            "course_number" => "2121",
            "course_name" => "Members101",
            "section_number" => "2",
            "course_section_id" => "15",
            "location" => "CantonOhio",
            "days_times" => "Every day, 9am-8pm",
            "credit_hours" => 10
        ],
        "3" => [
            "year_id" => "201718",
            "term_id" => "2",
            "college_code" => "", // Invalid College Code - Required
            "department_code" => "Fame",
            "subject_code" => "HOF",
            "course_number" => "2121",
            "course_name" => "Members101",
            "section_number" => "2",
            "course_section_id" => "16",
            "location" => "CantonOhio",
            "days_times" => "Every day, 9am-8pm",
            "credit_hours" => 10
        ],
        "4" => [
            "year_id" => "201718",
            "term_id" => "2",
            "college_code" => "Hall",
            "department_code" => "Fame",
            "subject_code" => "HOF",
            "course_number" => "2121",
            "course_name" => "Members101",
            "section_number" => "2",
            "course_section_id" => "17",
            "location" => "CantonOhio",
            "days_times" => "Every day, 9am-8pm",
            "credit_hours" => 100 // Invalid Optional data
        ]

    ];

    // tests function CreateCourses
    public function testCreateCourses()
    {

        $this->specify("Create Courses", function ($courseArray, $invalidValueForRequired, $errorMessage, $errorType, $expectedResult) {

            $this->error = $errorMessage;
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);
            $mockContainer = $this->getMock('Container', ['get']);
            $mockContainerForEntityValidationService = $this->getMock('Container', ['get']);

            //Mocking Repositories
            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['findOneBy']);
            $mockOrgAcademicTermRepository = $this->getMock('OrgAcademicTermRepository', ['findOneBy']);
            $mockOrgCoursesRepository = $this->getMock('OrgCoursesRepository', ['persist', 'findOneBy']);

            //Mocking Services
            $mockOrgService = $this->getMock('OrgService', ['find']);
            $mockPersonService = $this->getMock('PersonService', ['find']);
            $mockDataProcessingUtilityService = $this->getMock('DataProcessingUtilityService', ['convertCamelCasedStringToUnderscoredString']);

            if ($invalidValueForRequired != 'year_id') {
                $mockOrgAcademicYearRepository->method('findOneBy')->willReturn(new OrgAcademicYear());
            }

            if ($invalidValueForRequired != 'term_id') {
                $mockOrgAcademicTermRepository->method('findOneBy')->willReturn(new OrgAcademicTerms());
            }

            $mockValidator = $this->getMock('Validator', ['validate']);
            if ($errorType == "optional") {
                $mockValidator->method('validate')->willReturnCallback(function ($doctrineEntity, $test = null, $validationGroup) {
                    if ($validationGroup == "required") {
                        return [];
                    } else {
                        return $this->arrayOfErrorObjects($this->error);
                    }
                });
            }

            if ($errorType == "required") {
                $mockValidator->method('validate')->willReturnCallback(function ($doctrineEntity, $test = null, $validationGroup) {
                    if ($validationGroup == "required") {
                        return $this->arrayOfErrorObjects($this->error);
                    } else {
                        return [];
                    }
                });
            }

            $mockOrgCoursesRepository->method('findOneBy')->willReturn(null);

            $mockDataProcessingUtilityService->method('convertCamelCasedStringToUnderscoredString')->willReturn($invalidValueForRequired);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    OrgAcademicYearRepository::REPOSITORY_KEY,
                    $mockOrgAcademicYearRepository
                ],
                [
                    OrgAcademicTermRepository::REPOSITORY_KEY,
                    $mockOrgAcademicTermRepository
                ],
                [
                    OrgCoursesRepository::REPOSITORY_KEY,
                    $mockOrgCoursesRepository
                ]
            ]);

            $mockContainerForEntityValidationService->method('get')->willReturnMap([
                [
                    SynapseConstant::VALIDATOR,
                    $mockValidator

                ]
            ]);

            // we are not mocking the entity validation service here as all the error processing logic is in here which we want to execute and not mock, and this does not use any database calls
            $entityValidationService = new EntityValidationService($mockRepositoryResolver, $mockLogger, $mockContainerForEntityValidationService);

            $mockContainer->method('get')->willReturnMap([
                [
                    OrganizationService::SERVICE_KEY,
                    $mockOrgService
                ],
                [
                    PersonService::SERVICE_KEY,
                    $mockPersonService
                ],
                [
                    EntityValidationService::SERVICE_KEY,
                    $entityValidationService
                ],
                [
                    DataProcessingUtilityService::SERVICE_KEY,
                    $mockDataProcessingUtilityService
                ]
            ]);

            $courseListDTO = $this->setCourseArrayInCourseListDto($this->setCourseDto($courseArray));
            $courseObject = $this->setCourseObject($courseListDTO);

            $mockOrgCoursesRepository->method('persist')->willReturn($courseObject);

            $courseService = new CourseService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $courseService->createCourses($courseListDTO, new Organization(), new Person());
            $this->assertEquals($result, $expectedResult);
        }, [
                'examples' => [
                    // data with no error
                    [
                        $this->courseDataForCreate["1"],
                        '',
                        [],
                        '',
                        [
                            'data' => [
                                'created_count' => 1,
                                'created_records' => [
                                    "0" => $this->setCourseDto($this->courseDataForCreate["1"])
                                ]
                            ],
                            'errors' => [
                                'error_count' => 0,
                                'error_records' => []
                            ]
                        ]
                    ],
                    // invalid term id
                    [
                        $this->courseDataForCreate["2"],
                        'term_id',
                        ['TermId' => "Term Id " . $this->courseDataForCreate['2']['term_id'] . " is not valid for this organization."],
                        'required',
                        [
                            'data' => [
                                'created_count' => 0,
                                'created_records' => []
                            ],
                            'errors' => [
                                'error_count' => 1,
                                'error_records' => [
                                    "0" => $this->getErrorArray($this->courseDataForCreate["2"], 'TermId')
                                ]
                            ]
                        ]
                    ],
                    // invalid college_code
                    [
                        $this->courseDataForCreate["3"],
                        'college_code',
                        ['CollegeCode' => "College Code " . $this->courseDataForCreate['3']['college_code'] . " is not valid for this organization."],
                        'required',
                        [
                            'data' => [
                                'created_count' => 0,
                                'created_records' => []
                            ],
                            'errors' => [
                                'error_count' => 1,
                                'error_records' => [
                                    "0" => $this->getErrorArray($this->courseDataForCreate["3"], 'CollegeCode')
                                ]
                            ]
                        ]
                    ],
                    // invalid credit_hours - optional data
                    [
                        $this->courseDataForCreate["4"],
                        'credit_hours',
                        ['CreditHours' => "Credit Hours " . $this->courseDataForCreate['4']['credit_hours'] . " is not valid for this organization."],
                        'optional',
                        [
                            'data' => [
                                'created_count' => 1,
                                'created_records' => [
                                    "0" => $this->setCourseDto($this->courseDataForCreate["4"])
                                ]
                            ],
                            'errors' => [
                                'error_count' => 1,
                                'error_records' => [
                                    "0" => $this->getErrorArray($this->courseDataForCreate["4"], 'CreditHours')
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
    }

    private function getErrorArray($courseData, $key = null)
    {
        $returnArray = [];

        $returnArray['year_id'] = $courseData['year_id'];
        $returnArray['term_id'] = $courseData['term_id'];
        $returnArray['college_code'] = $courseData['college_code'];
        $returnArray['department_code'] = $courseData['department_code'];
        $returnArray['subject_code'] = $courseData['subject_code'];
        $returnArray['course_number'] = $courseData['course_number'];
        $returnArray['course_name'] = $courseData['course_name'];
        $returnArray['course_section_id'] = $courseData['course_section_id'];
        $returnArray['location'] = $courseData['location'];
        $returnArray['days_times'] = $courseData['days_times'];
        $returnArray['credit_hours'] = $courseData['credit_hours'];

        if ($key) {
            $setKey = strtolower(preg_replace('/([A-Z])/', '_$1', lcfirst($key)));
            $errorValue = $courseData[$setKey];
            $error['value'] = $errorValue;
            $showName = ucfirst(preg_replace('/([A-Z])/', ' $1', lcfirst($key)));
            $error['message'] = "$showName $errorValue is not valid for this organization.";
            $returnArray[$setKey] = $error;
        }

        return $returnArray;
    }

    private function setCourseDto($courseData)
    {
        $courseDTO = new \Synapse\AcademicBundle\EntityDto\CourseDTO();
        $courseDTO->setYearId($courseData['year_id']);
        $courseDTO->setTermId($courseData['term_id']);
        $courseDTO->setCollegeCode($courseData['college_code']);
        $courseDTO->setDepartmentCode($courseData['department_code']);
        $courseDTO->setSubjectCode($courseData['subject_code']);
        $courseDTO->setCourseNumber($courseData['course_number']);
        $courseDTO->setCourseName($courseData['course_name']);
        $courseDTO->setSectionNumber($courseData['section_number']);
        $courseDTO->setCourseSectionId($courseData['course_section_id']);
        $courseDTO->setLocation($courseData['location']);
        $courseDTO->setDaysTimes($courseData['days_times']);
        $courseDTO->setCreditHours($courseData['credit_hours']);

        return $courseDTO;
    }

    private function setCourseArrayInCourseListDto($courseDTO)
    {
        $courseListDTO = new CourseListDTO();
        $courseListDTO->setCourseList([$courseDTO]);
        return $courseListDTO;
    }


    private function arrayOfErrorObjects($errorArray)
    {
        $returnArray  = [];
        foreach ($errorArray as $errorKey => $error) {
            $mockErrorObject  = $this->getMock('ErrorObject', ['getPropertyPath', 'getMessage']);
            $mockErrorObject ->method('getPropertyPath')->willReturn($errorKey);
            $mockErrorObject ->method('getMessage')->willReturn($error);
            $returnArray[] = $mockErrorObject ;
        }
        return $returnArray;
    }


    private function setCourseObject($courseListDTO)
    {
        $courseList = $courseListDTO->getCourseList();
        $courseDTO = $courseList[0];
        $currentDateTime = new \DateTime();
        $organizationAcademicYear = new OrgAcademicYear();
        $organizationAcademicTerm = new OrgAcademicTerms();
        $loggedInUser = new Person();

        //creating new course
        $courseObject = new OrgCourses();

        $courseObject->setOrganization(new Organization());
        $courseObject->setOrgAcademicYear($organizationAcademicYear);
        $courseObject->setOrgAcademicTerms($organizationAcademicTerm);

        $courseObject->setCreatedAt($currentDateTime);
        $courseObject->setCreatedBy($loggedInUser);
        $courseObject->setModifiedBy($loggedInUser);
        $courseObject->setModifiedAt($currentDateTime);

        $courseObject->setCollegeCode($courseDTO->getCollegeCode());
        $courseObject->setCourseName($courseDTO->getCourseName());
        $courseObject->setCourseSectionId($courseDTO->getCourseSectionId());
        $courseObject->setCourseNumber($courseDTO->getCourseNumber());
        $courseObject->setCreditHours($courseDTO->getCreditHours());
        $courseObject->setDaysTimes($courseDTO->getDaysTimes());
        $courseObject->setDeptCode($courseDTO->getDepartmentCode());
        $courseObject->setSubjectCode($courseDTO->getSubjectCode());
        $courseObject->setSectionNumber($courseDTO->getSectionNumber());
        $courseObject->setExternalId($courseDTO->getCourseSectionId());
        return $courseObject;
    }

    public function testGetCoursesByRoleAsCSV()
    {
        $this->specify("Test to get courses by role as CSV ", function ($personId, $organizationId, $userType, $year, $term, $college, $department, $filter, $export, $allCourses, $expectedResult) {


            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Mocking Manager service
            $managerService = $this->getMock('Manager', ['checkAccessToOrganization','hasAccess']);
            $managerService->method('checkAccessToOrganization')->willReturn(true);
            if($allCourses && $userType == 'faculty') {
                $managerService->method('hasAccess')->willReturn(true);
            }
            $mockAcademicYearService = $this->getMock('AcademicYearService', array('getCurrentOrganizationAcademicYearYearID'));
            $mockCSVUtilityService = $this->getMock('CSVUtilityService', array('generateCSV'));

            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', array('findOneBy'));
            $mockOrgCourseRepository = $this->getMock('OrgCoursesRepository', array('getCoursesForOrganization', 'getCoursesForFaculty', 'getCountOfFacultyInCourse', 'getCountOfStudentInCourse'));

            $mockContainer->method('get')->willReturnMap([
                [Manager::SERVICE_KEY, $managerService],
                [AcademicYearService::SERVICE_KEY, $mockAcademicYearService],
                [CSVUtilityService::SERVICE_KEY,$mockCSVUtilityService]
            ]);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrganizationRoleRepository::REPOSITORY_KEY,
                        $mockOrganizationRoleRepository
                    ],
                    [
                        OrgCoursesRepository::REPOSITORY_KEY,
                        $mockOrgCourseRepository
                    ]
                ]);
            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        CSVUtilityService::SERVICE_KEY,
                        $mockCSVUtilityService
                    ]
                ]);
            $mockCSVUtilityService->method('generateCSV')->willReturn($expectedResult);
            if ($year == 'current') {
                $mockAcademicYearService->method('getCurrentOrganizationAcademicYearYearID')->willReturn($year);
            }
            if ($personId) {
                $mockOrgAcademicRole = $this->getMock('OrganizationRole', ['getId']);
            } else {
                $mockOrgAcademicRole = null;
            }
            $mockOrganizationRoleRepository->method('findOneBy')->willReturn($mockOrgAcademicRole);
            $courseDetail = [
                '0' =>
                    [
                        'college_code' => $college,
                        'dept_code' => 'a8',
                        'org_academic_year_id' => 192,
                        'org_academic_terms_id' => 493,
                        'year_id' => 201617,
                        'year_name' => '2016-2017',
                        'term_name' => $term,
                        'term_code' => 'Spring2017',
                        'org_course_id' => 373418,
                        'subject_code' => 'a2',
                        'course_number' => 'a3',
                        'section_number' => 'a4',
                        'course_section_id' => 'a1',
                        'course_name' => 'a5',
                        'location' => '',
                        'days_times' => 12,
                        'create_view_academic_update' => 1,
                        'view_all_academic_update_courses' => 1
                    ]

            ];
            $mockOrgCourseRepository->method('getCoursesForOrganization')->willReturn($courseDetail);
            $mockOrgCourseRepository->method('getCoursesForFaculty')->willReturn($courseDetail);
            $facultyCount = [
                '0' => [
                    'course_id' => 373418,
                    'faculty_count' => 20
                ]
            ];

            $studentCount = [
                '0' => [
                    'course_id' => 373418,
                    'student_count' => 20
                ]
            ];
            $mockOrgCourseRepository->method('getCountOfFacultyInCourse')->willReturn($facultyCount);
            $mockOrgCourseRepository->method('getCountOfStudentInCourse')->willReturn($studentCount);
            try {
                $courseService = new CourseService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $courseService->getCoursesByRoleAsCSV($personId, $organizationId, $userType, $year, $term, $college, $department, $filter, $export, $allCourses);
                $this->assertEquals($results, $expectedResult);
            } catch (\Synapse\CoreBundle\Exception\SynapseException $e) {

                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                //User type coordinator csv download test
                [
                    257925,
                    203,
                    'coordinator',
                    201617,
                    'term_first',
                    'college_a2',
                    'dept1',
                    '',
                    'courses',
                    false,
                    "203-257925coordinator201617term_firstcollege_a2dept1csvcourses-list-course.csv"
                ],
                //User type faculty csv download test
                [
                    257925,
                    203,
                    'faculty',
                    201617,
                    'term_first',
                    'college_a2',
                    'dept1',
                    '',
                    'courses',
                    false,
                    "203-257925faculty201617term_firstcollege_a2dept1csvcourses-list-course.csv"
                ],
                //User type faculty, with current year set test
                [
                    257925,
                    203,
                    'faculty',
                    'current',
                    'term_first',
                    'college_a2',
                    'dept1',
                    '',
                    'courses',
                    false,
                    '203-257925facultycurrentterm_firstcollege_a2dept1csvcourses-list-course.csv'
                ],
                //User type faculty, no valid user AccessDeniedException test
                [
                    '',
                    203,
                    'coordinator',
                    201617,
                    'term_first',
                    'college_a2',
                    'dept1',
                    '',
                    'courses',
                    true,
                    "You do not have access to the organization's courses as a coordinator"
                ],
                //User type faculty csv download test permission check
                [
                    257925,
                    203,
                    'faculty',
                    201617,
                    'term_first',
                    'college_a2',
                    'dept1',
                    '',
                    'courses',
                    true,
                    "203-257925faculty201617term_firstcollege_a2dept1csvcourses-list-course.csv"
                ],
                //User type Coordinator doing a Student Download
                [
                    257925,
                    203,
                    'coordinator',
                    201617,
                    'term_first',
                    'college_a2',
                    'dept1',
                    '',
                    'students',
                    false,
                    "203-257925coordinator201617term_firstcollege_a2dept1csvcourses-list-course.csv"
                ],
                //User type Coordinator doing a Faculty Download
                [
                    257925,
                    203,
                    'coordinator',
                    201617,
                    'term_first',
                    'college_a2',
                    'dept1',
                    '',
                    'staff',
                    false,
                    "203-257925coordinator201617term_firstcollege_a2dept1csvcourses-list-course.csv"
                ],
                //User type Coordinator doing a Everything Download
                [
                    257925,
                    203,
                    'coordinator',
                    201617,
                    'term_first',
                    'college_a2',
                    'dept1',
                    '',
                    'everything',
                    false,
                    "203-257925coordinator201617term_firstcollege_a2dept1csvcourses-list-course.csv"
                ],



            ]
        ]);
    }


    public function testDeleteCourse()
    {
        $this->specify("Test to delete the specified course", function ($courseInternalId, $errorType, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockOrgCourseRepository = $this->getMock('OrgCoursesRepository', ['find', 'persist', 'delete']);
            $mockAcademicUpdateRepository = $this->getMock('AcademicUpdateRepository', ['findBy']);
            $mockAcademicUpdateRequestCourseRepository = $this->getMock('AcademicUpdateRequestCourseRepository', ['findBy']);
            $mockOrgCourseStudentRepository = $this->getMock('OrgCourseStudentRepository', ['findBy', 'delete']);
            $mockOrgCourseFacultyRepository = $this->getMock('OrgCourseFacultyRepository', ['findBy', 'delete']);

            if ($errorType == 'course_not_found') {
                $mockOrgCourseRepository->method('find')->will($this->throwException(new SynapseValidationException('Course not found')));
            } else {
                $mockOrganization = $this->getMock('Organization', ['getId']);
                $mockOrgCourse = $this->getMock('OrgCourses', ['getOrganization', 'getCourseSectionId', 'getExternalId', 'setCourseSectionId', 'setExternalId']);
                $mockOrgCourse->method('getOrganization')->willReturn($mockOrganization);
                $mockOrgCourse->method('getCourseSectionId')->willReturn('100001');
                $mockOrgCourse->method('getExternalId')->willReturn('100001');
                $mockOrgCourseRepository->method('find')->willReturn($mockOrgCourse);
            }

            $mockManager = $this->getMock('Manager', ['checkAccessToOrganization']);
            if ($errorType == 'access_denied') {
                $mockManager->method('checkAccessToOrganization')->will($this->throwException(new AccessDeniedException('Access Denied')));
            }

            if ($errorType == 'cant_remove') {
                $mockAcademicUpdate = $this->getMock('AcademicUpdate', ['getId']);
                $mockAcademicUpdateRepository->method('findBy')->willReturn($mockAcademicUpdate);
            }

            $mockOrgCourseStudent = $this->getMock('OrgCourseStudent', ['getId']);
            $mockOrgCourseStudentRepository->method('findBy')->willReturn([$mockOrgCourseStudent]);

            $mockOrgCourseFaculty = $this->getMock('OrgCourseFaculty', ['getId']);
            $mockOrgCourseFacultyRepository->method('findBy')->willReturn([$mockOrgCourseFaculty]);

            $mockCourseUploadService = $this->getMock('CourseUploadService', ['updateDataFile']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgCoursesRepository::REPOSITORY_KEY,
                        $mockOrgCourseRepository
                    ],
                    [
                        AcademicUpdateRepository::REPOSITORY_KEY,
                        $mockAcademicUpdateRepository
                    ],
                    [
                        AcademicUpdateRequestCourseRepository::REPOSITORY_KEY,
                        $mockAcademicUpdateRequestCourseRepository
                    ],
                    [
                        OrgCourseStudentRepository::REPOSITORY_KEY,
                        $mockOrgCourseStudentRepository
                    ],
                    [
                        OrgCourseFacultyRepository::REPOSITORY_KEY,
                        $mockOrgCourseFacultyRepository
                    ]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        SynapseConstant::TINYRBAC_MANAGER,
                        $mockManager
                    ],
                    [
                        CourseUploadService::SERVICE_KEY,
                        $mockCourseUploadService
                    ]
                ]);

            try {
                $courseService = new CourseService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $courseService->deleteCourse($courseInternalId);

                $this->assertEquals($results, $expectedResult);

            } catch (SynapseException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test0: Case for Course not exist, throws validation exception
                [
                    257925,
                    "course_not_found",
                    "Course not found"
                ],
                // Test1: Case for access to organization, throws access denied exception
                [
                    257925,
                    "access_denied",
                    "Access Denied"
                ],
                // Test2: When academic update exists, cannot remove course as throwing Can't remove as academic updates are submitted for course
                [
                    257925,
                    "cant_remove",
                    "Can't remove as academic updates are submitted for course"
                ],
                // Test3: Case for successfully deleting course, returns course internal id
                [
                    257925,
                    "",
                    257925
                ],
            ]
        ]);
    }


    public function testAddStudentsToCourses()
    {
        $this->specify("Test to add students to courses", function ($organizationId, $loggedInUser, $courseStudentListDTO, $isInternal, $errorType, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Mock Repositories
            $mockPersonRepository = $this->getMock('PersonRepository', ['find', 'findOneBy']);
            $mockOrgCourseStudentRepository = $this->getMock('OrgCourseStudentRepository', ['findOneBy', 'persist']);
            $mockOrgCourseFacultyRepository = $this->getMock('OrgCourseFacultyRepository', ['findOneBy']);
            $mockOrgCourseRepository = $this->getMock('OrgCourseRepository', ['findOneBy']);
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['findOneBy']);

            // Mock Services
            $mockDataProcessingUtilityService = $this->getMock('dataProcessingUtilityService', ['setErrorMessageOrValueInArray']);
            $mockStudentService = $this->getMock('studentService', ['isPersonAStudent']);
            $mockEntityValidationService = $this->getMock('entityValidationService',['validateDoctrineEntity']);
            $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();

            $mockPerson = $this->getPersonInstance($this->studentId);

            $mockDataProcessingUtilityService->method('setErrorMessageOrValueInArray')->willReturnCallback(function ($records, $errorArray) {
                $responseArray = [];
                foreach ($records as $key => $value) {
                    if (array_key_exists($key, $errorArray)) {
                        $responseArray[$key]['value'] = $value;
                        $responseArray[$key]['message'] = $errorArray[$key];
                    } else {
                        $responseArray[$key] = $value;
                    }
                }
                return $responseArray;
            });

            if ($errorType == 'invalid_course') {
                $dataProcessingExceptionHandler->addErrors($expectedResult, 'course_id');
                $mockOrgCourseRepository->method('findOneBy')->willThrowException($dataProcessingExceptionHandler);
            } else {
                $mockOrgCourse = $this->getCourseInstance($this->courseId);
                $mockOrgCourseRepository->method('findOneBy')->willReturn($mockOrgCourse);
            }

            if ($errorType == 'invalid_person') {
                $dataProcessingExceptionHandler->addErrors($expectedResult, 'student_id');
                $mockPersonRepository->method('findOneBy')->willThrowException($dataProcessingExceptionHandler);
            } else {
                $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);
            }

            if ($errorType == 'invalid_student') {
                $dataProcessingExceptionHandler->addErrors($expectedResult, 'student_id');
                $mockOrgPersonStudentRepository->method('findOneBy')->willThrowException($dataProcessingExceptionHandler);
            } else {
                $mockOrgPersonStudent = $this->getMock('OrgPersonStudent', ['getId']);
                $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockOrgPersonStudent);
            }

            if ($errorType == 'is_not_faculty_in_course') {
                $mockOrgCourseFaculty = $this->getMock('OrgCourseFaculty', ['getId']);
                $mockOrgCourseFacultyRepository->method('findOneBy')->willReturn($mockOrgCourseFaculty);
            } else {
                $mockOrgCourseFacultyRepository->method('findOneBy')->willReturn(null);
            }

            if ($errorType == 'is_not_already_in_course') {
                $dataProcessingExceptionHandler->addErrors($expectedResult, 'student_id');
                $mockEntityValidationService->method('validateDoctrineEntity')->willThrowException($dataProcessingExceptionHandler);
            } else {
                $mockEntityValidationService->method('validateDoctrineEntity')->willReturn(null);
            }

            $mockPersonRepository->method('find')->willReturn($mockPerson);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        OrgCourseStudentRepository::REPOSITORY_KEY,
                        $mockOrgCourseStudentRepository
                    ],
                    [
                        OrgCourseFacultyRepository::REPOSITORY_KEY,
                        $mockOrgCourseFacultyRepository
                    ],
                    [
                        OrgCoursesRepository::REPOSITORY_KEY,
                        $mockOrgCourseRepository
                    ],
                    [
                        OrgPersonStudentRepository::REPOSITORY_KEY,
                        $mockOrgPersonStudentRepository
                    ]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        DataProcessingUtilityService::SERVICE_KEY,
                        $mockDataProcessingUtilityService
                    ],
                    [
                        StudentService::SERVICE_KEY,
                        $mockStudentService
                    ],
                    [
                        EntityValidationService::SERVICE_KEY,
                        $mockEntityValidationService
                    ]
                ]);

            try {
                $courseService = new CourseService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $courseService->addStudentsToCourses($organizationId, $loggedInUser, $courseStudentListDTO, $isInternal);

                $courseStudentsDto = $courseStudentListDTO->getCourseStudentList();
                $expectedResultArray = $this->buildResponseArray($courseStudentsDto[0], $errorType, $expectedResult);

                $this->assertEquals($results, $expectedResultArray);

            } catch (SynapseException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test0: Case for Course not exist, throws validation exception, When is internal = true
                [
                    1,
                    $this->getPersonInstance(),
                    $this->setCourseStudentListDTO($this->courseStudentArray),
                    true,
                    "invalid_course",
                    "Course ID 1 is not valid at the organization."
                ],
                // Test1: Case for Course not exist, returns error array, When is internal = false
                [
                    1,
                    $this->getPersonInstance(),
                    $this->setCourseStudentListDTO($this->courseStudentArray),
                    false,
                    "invalid_course",
                    "Course ID 1 is not valid at the organization."
                ],
                // Test2: Case for invalid_person, throws validation exception, When is internal = true
                [
                    1,
                    $this->getPersonInstance(),
                    $this->setCourseStudentListDTO($this->courseStudentArray),
                    true,
                    "invalid_person",
                    "Person ID 1 is not valid at the organization."
                ],
                // Test3: Case for invalid_person, returns error array, When is internal = false
                [
                    1,
                    $this->getPersonInstance(),
                    $this->setCourseStudentListDTO($this->courseStudentArray),
                    false,
                    "invalid_person",
                    "Person ID 1 is not valid at the organization."
                ],
                // Test4: Case for invalid_student, throws validation exception, When is internal = true
                [
                    1,
                    $this->getPersonInstance(),
                    $this->setCourseStudentListDTO($this->courseStudentArray),
                    true,
                    "invalid_student",
                    "Student 1 is not valid at this course."
                ],
                // Test5: Case for invalid_student, returns error array, When is internal = false
                [
                    1,
                    $this->getPersonInstance(),
                    $this->setCourseStudentListDTO($this->courseStudentArray),
                    false,
                    "invalid_student",
                    "Student 1 is not valid at this course."
                ],
                // Test6: Case for is_not_as_faculty_in_course, throws validation exception, When is internal = true
               [
                    1,
                    $this->getPersonInstance(),
                    $this->setCourseStudentListDTO($this->courseStudentArray),
                    true,
                    "is_not_faculty_in_course",
                    "Student ID 1 is already in the course as a faculty."
                ],
                // Test7: Case for student already in course as faculty, returns error array, When is internal = false
                [
                    1,
                    $this->getPersonInstance(),
                    $this->setCourseStudentListDTO($this->courseStudentArray),
                    false,
                    "is_not_faculty_in_course",
                    "Student ID 1 is already in the course as a faculty."
                ],
                // Test8: Case for student already_in_course, throws validation exception, When is internal = true
                [
                    1,
                    $this->getPersonInstance(),
                    $this->setCourseStudentListDTO($this->courseStudentArray),
                    true,
                    "is_not_already_in_course",
                    "Student ID 1 is already in the course as a faculty."
                ],
                // Test9: Case for student already added in course, returns error array, When is internal = false
                [
                    1,
                    $this->getPersonInstance(),
                    $this->setCourseStudentListDTO($this->courseStudentArray),
                    false,
                    "is_not_already_in_course",
                    "Student ID 1 is already in the course as a faculty."
                ],
                // Test10: Case for studenadded in course
                [
                    1,
                    $this->getPersonInstance(),
                    $this->setCourseStudentListDTO($this->courseStudentArray),
                    false,
                    "",
                    ""
                ]
            ]
        ]);
    }

    public function testIsStudentInCourseAsFaculty()
    {
        $this->specify("Test is student in course as faculty", function ($studentId, $courseId, $errorType, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Mock Repository
            $mockOrgCourseFacultyRepository = $this->getMock('OrgCourseFacultyRepository', ['findOneBy']);
            if ($errorType == 'valid') {
                $mockPerson = $this->getPersonInstance('1');
            } else {
                $mockPerson = null;
            }
            $mockOrgCourseFacultyRepository->method('findOneBy')->willReturn($mockPerson);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgCourseFacultyRepository::REPOSITORY_KEY,
                        $mockOrgCourseFacultyRepository
                    ],
                ]);

            $courseService = new CourseService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $courseService->isStudentInCourseAsFaculty($studentId, $courseId);

            $this->assertEquals($results, $expectedResult);

        }, [
            'examples' => [
                // Test0: Case when student is not faculty for course
                [
                    $this->courseId,
                    $this->studentId,
                    "invalid",
                    false
                ],
                // Test1: Case when student is a faculty for course
                [
                    $this->courseId,
                    $this->studentId,
                    "valid",
                    true
                ]
            ]
        ]);
    }

    public function testIsStudentInCourse()
    {
        $this->specify("Test is student already added in course", function ($studentId, $courseId, $errorType, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Mock Repository
            $mockOrgCourseStudentRepository = $this->getMock('orgCourseStudentRepository', ['findOneBy']);
            if ($errorType == 'valid') {
                $mockPerson = $this->getPersonInstance('1');
            } else {
                $mockPerson = null;
            }
            $mockOrgCourseStudentRepository->method('findOneBy')->willReturn($mockPerson);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgCourseStudentRepository::REPOSITORY_KEY,
                        $mockOrgCourseStudentRepository
                    ],
                ]);

            $courseService = new CourseService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $courseService->isStudentInCourse($studentId, $courseId);

            $this->assertEquals($results, $expectedResult);

        }, [
            'examples' => [
                // Test0: Case when student is not added in course
                [
                    $this->courseId,
                    $this->studentId,
                    "invalid",
                    false
                ],
                // Test1: Case when student already added in course
                [
                    $this->courseId,
                    $this->studentId,
                    "valid",
                    true
                ]
            ]
        ]);
    }

    public function testGetFacultyByCourse()
    {
        $this->specify("Test to get the faculty by course", function ($courseExternalId, $organizationId, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));
            // Mock Repositories
            $mockOrgCourseRepository = $this->getMock('OrgCoursesRepository', ['getSingleCourseFacultiesDetails']);
            $mockOrgCourse = $this->getMock('OrgCourses', ['getExternalId', 'getId']);
            $mockOrgCourse->method('getExternalId')->willReturn($courseExternalId);
            $mockOrgCourse->method('getId')->willReturn(1);
            $mockOrgCourseRepository->method('getSingleCourseFacultiesDetails')->willReturn($expectedResult);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgCoursesRepository::REPOSITORY_KEY,
                        $mockOrgCourseRepository
                    ]
                ]);
            $courseObject = $this->getOrgCourseInstance($courseExternalId);
            $courseService = new CourseService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $courseService->getFacultyByCourse($organizationId, $courseObject);
            $expectedResultDto = $this->getCourseFacultyDto($courseExternalId, $expectedResult);
            $this->assertEquals($results, $expectedResultDto);
        }, [
            'examples' => [
                // Test0: CourseId has no faculty as it returns blank faculty list array
                [
                    257921,
                    203,
                    [],
                ],
                // Test1: Returns list of faculties
                [
                    257921,
                    203,
                    [
                        [
                            'firstname' => 'FirstName1',
                            'lastname' => 'LastName1',
                            'primary_email' => 'MapworksUser001@mailnator.com',
                            'external_id' => 'X10001',
                            'permissionset' => 'Course Only',
                        ],
                        [
                            'firstname' => 'FirstName2',
                            'lastname' => 'LastName2',
                            'primary_email' => 'MapworksUser002@mailnator.com',
                            'external_id' => 'X10002',
                            'permissionset' => 'Course Only',
                        ]
                    ],
                ],
            ]
        ]);
    }

    private function setOrganization($campusId = 1)
    {
        $organization = new \Synapse\CoreBundle\Entity\Organization();
        $organization->setExternalId('ABC123');
        $organization->setCampusId($campusId);
        return $organization;
    }

    private function getPersonInstance($externalId = '123')
    {
        $person = new \Synapse\CoreBundle\Entity\Person();
        $person->setExternalId($externalId);
        $person->setOrganization($this->setOrganization());
        return $person;
    }

    private function getCourseInstance($externalId = 'X10001')
    {
        $course = new \Synapse\AcademicBundle\Entity\OrgCourses();
        $course->setExternalId($externalId);
        $course->setCourseSectionId($externalId);
        return $course;
    }

    private function setCourseStudentListDTO($courseData, $returnCourseStudentsDto = false)
    {
        $courseStudentsDto = new \Synapse\AcademicBundle\EntityDto\CourseStudentsDto();
        $courseStudentsDto->setCourseId($courseData['course_id']);
        $courseStudentsDto->setStudentId($courseData['student_id']);
        if ($returnCourseStudentsDto) {
            return $courseStudentsDto;
        }

        $courseStudentListDTO = new \Synapse\AcademicBundle\EntityDto\CourseStudentListDTO();
        $courseStudentListDTO->setCourseStudentList([$courseStudentsDto]);
        return $courseStudentListDTO;
    }

    private function buildResponseArray($courseStudentsDto, $errorType, $errorMessage)
    {
        $responseArray = $errorMessageArray = $errorArray = [];
        $courseArray['course_id'] = $courseStudentsDto->getCourseId();
        $courseArray['student_id'] = $courseStudentsDto->getStudentId();
        if ($errorType == 'invalid_course') {
            $errorMessageArray['course_id'] = $errorMessage;
        }

        if ($errorType == 'invalid_person' || $errorType == 'invalid_student' || $errorType == 'is_not_faculty_in_course' || $errorType == 'is_not_already_in_course') {
            $errorMessageArray['student_id'] = $errorMessage;
        }

        if (!empty($errorType)) {
            $errorArray[] = $this->buildCourseResponseArray($courseArray, $errorMessageArray);
            $createdArray = [];
        } else {
            $createdArray[] = $courseArray;
        }

        $responseArray['data']['created_count'] = count($createdArray);
        $responseArray['data']['created_records'] = $createdArray;
        $responseArray['errors'] = $errorArray;
        return $responseArray;
    }

    private function buildCourseResponseArray($courseArray, $errorMessageArray = [])
    {
        $responseArray = [];
        foreach ($courseArray as $key => $value) {
            if (array_key_exists($key, $errorMessageArray)) {
                $responseArray[$key]['value'] = $value;
                $responseArray[$key]['message'] = $errorMessageArray[$key];
            } else {
                $responseArray[$key] = $value;
            }
        }

        return $responseArray;
    }

    private function getOrgCourseInstance($courseExternalId)
    {
        $orgCourses = new OrgCourses();
        $orgCourses->setCourseSectionId($courseExternalId);
        $orgCourses->setCourseName('Course Name');
        $orgCourses->setCourseNumber('Course Number');
        $orgCourses->setSectionNumber('Section Number');
        $orgCourses->setSubjectCode('Subject Code');
        $orgCourses->setExternalId($courseExternalId);

        return $orgCourses;
    }

    private function getCourseFacultyDto($courseExternalId, $facultyArray)
    {
        $courseFacultyDto = new CourseFacultyDto();
        $courseFacultyDto->setCourseExternalId($courseExternalId);
        $courseFacultyDto->setFacultyList($facultyArray);

        return $courseFacultyDto;
    }

    // tests function UpdateCourses
    public function testUpdateCourses()
    {
        $this->specify("Update Courses", function ($courseArray, $invalidValueForRequired, $errorMessage, $errorType, $expectedResult) {
            $this->error = $errorMessage;
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);
            $mockContainer = $this->getMock('Container', ['get']);
            $mockContainerForEntityValidationService = $this->getMock('Container', ['get']);

            //Mocking Repositories
            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['findOneBy']);
            $mockOrgAcademicTermRepository = $this->getMock('OrgAcademicTermRepository', ['findOneBy']);
            $mockOrgCoursesRepository = $this->getMock('OrgCoursesRepository', ['persist', 'findOneBy']);

            //Mocking Services
            $mockEntityValidationService = $this->getMock('EntityValidationService', ['throwErrorIfContains', 'validateDoctrineEntity']);
            $mockDataProcessingUtilityService = $this->getMock('DataProcessingUtilityService', ['convertCamelCasedStringToUnderscoredString']);

            if ($invalidValueForRequired) {
                $mockEntityValidationService->method('throwErrorIfContains')->willReturnCallback(function ($errorObject, $errorType = null) {
                    if ($errorObject->doesErrorHandlerContainError($errorType)) {
                        throw $errorObject;
                    }
                    return $errorObject;
                });
            }

            if ($invalidValueForRequired != 'year_id') {
                $mockOrgAcademicYearRepository->method('findOneBy')->willReturn(new OrgAcademicYear());
            }

            if ($invalidValueForRequired != 'term_id') {
                $mockOrgAcademicTermRepository->method('findOneBy')->willReturn(new OrgAcademicTerms());
            }

            $mockValidator = $this->getMock('Validator', ['validate']);
            if (is_null($errorType)) {
                $mockValidator->method('validate')->willReturnCallback(function ($doctrineEntity, $test = null, $validationGroup) {
                    if ($validationGroup == "required") {
                        return [];
                    } else {
                        return $this->arrayOfErrorObjects($this->error);
                    }
                });
            }

            if ($errorType == "required") {
                $mockValidator->method('validate')->willReturnCallback(function ($doctrineEntity, $test = null, $validationGroup) {
                    if ($validationGroup == "required") {
                        return $this->arrayOfErrorObjects($this->error);
                    } else {
                        return [];
                    }
                });
            }

            $mockOrgCourse = null;
            $personObject = new Person();
            if ($errorType == 'course_section_id') {
                $mockOrgCoursesRepository->method('findOneBy')->willReturn(null);
            } else {
                $mockOrgCourse = $this->getMock('OrgCourses', [
                    'getId',
                    'setOrganization',
                    'getOrganization',
                    'setOrgAcademicYear',
                    'getOrgAcademicYear',
                    'setOrgAcademicTerms',
                    'getOrgAcademicTerms',
                    'setCourseSectionId',
                    'getCourseSectionId',
                    'setCollegeCode',
                    'getCollegeCode',
                    'setDeptCode',
                    'getDeptCode',
                    'setSubjectCode',
                    'getSubjectCode',
                    'setCourseNumber',
                    'getCourseNumber',
                    'setCourseName',
                    'getCourseName',
                    'setSectionNumber',
                    'getSectionNumber',
                    'setDaysTimes',
                    'getDaysTimes',
                    'setLocation',
                    'getLocation',
                    'setCreditHours',
                    'getCreditHours',
                    'setCreatedAt',
                    'setCreatedBy',
                    'setModifiedAt',
                    'setModifiedBy'
                ]);

                $mockOrgCourse->method('getId')->willReturn(14);
                $organizationObject = new Organization();
                $mockOrgCourse->method('setOrganization')->willReturn($organizationObject);
                $mockOrgCourse->method('getOrganization')->willReturn($organizationObject);
                $orgAcademicYear = new OrgAcademicYear();
                $mockOrgCourse->method('setOrgAcademicYear')->willReturn($orgAcademicYear);
                $mockOrgCourse->method('getOrgAcademicYear')->willReturn($courseArray['year_id']);
                $orgAcademicTerms = new OrgAcademicTerms();
                $mockOrgCourse->method('setOrgAcademicTerms')->willReturn($orgAcademicTerms);
                $mockOrgCourse->method('getOrgAcademicTerms')->willReturn($courseArray['term_id']);
                $mockOrgCourse->method('setCourseSectionId')->willReturn($courseArray['course_section_id']);
                $mockOrgCourse->method('getCourseSectionId')->willReturn($courseArray['course_section_id']);
                $mockOrgCourse->method('setCollegeCode')->willReturn($courseArray['college_code']);
                $mockOrgCourse->method('getCollegeCode')->willReturn($courseArray['college_code']);
                $mockOrgCourse->method('setDeptCode')->willReturn($courseArray['department_code']);
                $mockOrgCourse->method('setDeptCode')->willReturn($courseArray['department_code']);
                $mockOrgCourse->method('setSubjectCode')->willReturn($courseArray['subject_code']);
                $mockOrgCourse->method('getSubjectCode')->willReturn($courseArray['subject_code']);
                $mockOrgCourse->method('setCourseNumber')->willReturn($courseArray['course_number']);
                $mockOrgCourse->method('getCourseNumber')->willReturn($courseArray['course_number']);
                $mockOrgCourse->method('setCourseName')->willReturn($courseArray['course_name']);
                $mockOrgCourse->method('getCourseName')->willReturn($courseArray['course_name']);
                $mockOrgCourse->method('setSectionNumber')->willReturn($courseArray['section_number']);
                $mockOrgCourse->method('getSectionNumber')->willReturn($courseArray['section_number']);
                $mockOrgCourse->method('setDaysTimes')->willReturn($courseArray['days_times']);
                $mockOrgCourse->method('getDaysTimes')->willReturn($courseArray['days_times']);
                $mockOrgCourse->method('setLocation')->willReturn($courseArray['location']);
                $mockOrgCourse->method('getLocation')->willReturn($courseArray['location']);
                $mockOrgCourse->method('setCreditHours')->willReturn($courseArray['credit_hours']);
                $mockOrgCourse->method('getCreditHours')->willReturn($courseArray['credit_hours']);
                $mockOrgCourse->method('setSectionNumber')->willReturn($courseArray['section_number']);
                $mockOrgCourse->method('getSectionNumber')->willReturn($courseArray['section_number']);
                $mockOrgCourse->method('setCreatedAt')->willReturn(new DateTime());

                $mockOrgCourse->method('setCreatedBy')->willReturn($personObject);
                $mockOrgCourse->method('setModifiedAt')->willReturn(new DateTime());
                $mockOrgCourse->method('setModifiedBy')->willReturn($personObject);

                $mockOrgCoursesRepository->method('findOneBy')->willReturn($mockOrgCourse);

            }

            $mockDataProcessingUtilityService->method('convertCamelCasedStringToUnderscoredString')->willReturn($invalidValueForRequired);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    OrgAcademicYearRepository::REPOSITORY_KEY,
                    $mockOrgAcademicYearRepository
                ],
                [
                    OrgAcademicTermRepository::REPOSITORY_KEY,
                    $mockOrgAcademicTermRepository
                ],
                [
                    OrgCoursesRepository::REPOSITORY_KEY,
                    $mockOrgCoursesRepository
                ]
            ]);

            $mockContainerForEntityValidationService->method('get')->willReturnMap([
                [
                    SynapseConstant::VALIDATOR,
                    $mockValidator
                ]
            ]);

            // we are not mocking the entity validation service here as all the error processing logic is in here which we want to execute and not mock, and this does not use any database calls
            $entityValidationService = new EntityValidationService($mockRepositoryResolver, $mockLogger, $mockContainerForEntityValidationService);

            $mockContainer->method('get')->willReturnMap([
                [
                    EntityValidationService::SERVICE_KEY,
                    $entityValidationService
                ],
                [
                    DataProcessingUtilityService::SERVICE_KEY,
                    $mockDataProcessingUtilityService
                ]
            ]);

            $courseListDTO = $this->setCourseArrayInCourseListDto($this->setCourseDto($courseArray));
            $mockOrgCoursesRepository->method('persist')->willReturn($mockOrgCourse);

            $courseService = new CourseService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $courseService->updateCourses($courseListDTO, new Organization(), $personObject);
            $this->assertEquals($result, $expectedResult);
        }, [
                'examples' => [
                    // data with no error
                    [
                        $this->courseDataForCreate["1"],
                        '',
                        [],
                        '',
                        [
                            'data' => [
                                'updated_count' => 1,
                                'updated_records' => [
                                    "0" => $this->setCourseDto($this->courseDataForCreate["1"])
                                ]
                            ],
                            'errors' => [
                                'error_count' => 0,
                                'error_records' => []
                            ]
                        ]
                    ],
                    // invalid course_section_id
                    [
                        $this->courseDataForCreate["1"],
                        'course_section_id',
                        ['CourseSectionId' => "Course Section Id " . $this->courseDataForCreate['1']['course_section_id'] . " is not valid for this organization."],
                        'required',
                        [
                            'data' => [
                                'updated_count' => 0,
                                'updated_records' => []
                            ],
                            'errors' => [
                                'error_count' => 1,
                                'error_records' => [
                                    "0" => $this->getErrorArray($this->courseDataForCreate["1"], 'CourseSectionId')
                                ]
                            ]
                        ]
                    ],
                    // invalid term id
                    [
                        $this->courseDataForCreate["2"],
                        'term_id',
                        ['TermId' => "Term Id " . $this->courseDataForCreate['2']['term_id'] . " is not valid for this organization."],
                        'required',
                        [
                            'data' => [
                                'updated_count' => 0,
                                'updated_records' => []
                            ],
                            'errors' => [
                                'error_count' => 1,
                                'error_records' => [
                                    "0" => $this->getErrorArray($this->courseDataForCreate["2"], 'TermId')
                                ]
                            ]
                        ]
                    ],
                    // invalid college_code
                    [
                        $this->courseDataForCreate["3"],
                        'college_code',
                        ['CollegeCode' => "College Code " . $this->courseDataForCreate['3']['college_code'] . " is not valid for this organization."],
                        'required',
                        [
                            'data' => [
                                'updated_count' => 0,
                                'updated_records' => []
                            ],
                            'errors' => [
                                'error_count' => 1,
                                'error_records' => [
                                    "0" => $this->getErrorArray($this->courseDataForCreate["3"], 'CollegeCode')
                                ]
                            ]
                        ]
                    ],
                    // invalid credit_hours - optional data
                    [
                        $this->courseDataForCreate["4"],
                        'credit_hours',
                        ['CreditHours' => "Credit Hours " . $this->courseDataForCreate['4']['credit_hours'] . " is not valid for this organization."],
                        null,
                        [
                            'data' => [
                                'updated_count' => 1,
                                'updated_records' => [
                                    "0" => $this->setCourseDto($this->courseDataForCreate["4"])
                                ]
                            ],
                            'errors' => [
                                'error_count' => 1,
                                'error_records' => [
                                    "0" => $this->getErrorArray($this->courseDataForCreate["4"], 'CreditHours')
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
    }

    public function testAddFacultyToCourses()
    {
        $this->specify("Test to add faculty to courses", function ($courseFacultyListDTO, $isInternal, $errorType, $isActive, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockContainerForEntityValidationService = $this->getMock('Container', ['get']);

            // Mock Repositories
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy']);
            $mockOrgCoursesRepository = $this->getMock('OrgCoursesRepository', ['findOneBy']);
            $mockOrgCourseStudentRepository = $this->getMock('OrgCourseStudentRepository', ['findOneBy']);
            $mockOrgPermissionsetRepository = $this->getMock('OrgPermissionsetRepository', ['findOneBy']);
            $mockOrgCourseFacultyRepository = $this->getMock('OrgCourseFacultyRepository', ['findOneBy', 'persist']);

            // Mock Services
            $mockDataProcessingUtilityService = $this->getMock('DataProcessingUtilityService', ['setErrorMessageOrValueInArray']);
            $mockFacultyService = $this->getMock('FacultyService', ['isPersonAFaculty', 'isFacultyActive']);

            $mockValidator = $this->getMock('Validator', ['validate']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        OrgCoursesRepository::REPOSITORY_KEY,
                        $mockOrgCoursesRepository
                    ],
                    [
                        OrgCourseStudentRepository::REPOSITORY_KEY,
                        $mockOrgCourseStudentRepository
                    ],
                    [
                        OrgCourseFacultyRepository::REPOSITORY_KEY,
                        $mockOrgCourseFacultyRepository
                    ],
                    [
                        OrgPermissionsetRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetRepository
                    ]
                ]);

            $mockContainerForEntityValidationService->method('get')->willReturnMap([
                [
                    SynapseConstant::VALIDATOR,
                    $mockValidator
                ]
            ]);

            // we are not mocking the entity validation service here as all the error processing logic is in here which we want to execute and not mock, and this does not use any database calls
            $entityValidationService = new EntityValidationService($mockRepositoryResolver, $mockLogger, $mockContainerForEntityValidationService);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        EntityValidationService::SERVICE_KEY,
                        $entityValidationService
                    ],
                    [
                        DataProcessingUtilityService::SERVICE_KEY,
                        $mockDataProcessingUtilityService
                    ],
                    [
                        FacultyService::SERVICE_KEY,
                        $mockFacultyService
                    ]
                ]);

            $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();

            $mockPerson = $this->getPersonInstance($this->facultyId);

            if ($errorType == 'invalid_course') {
                $dataProcessingExceptionHandler->addErrors("Course ID 1 is not valid at this organization.", 'course_id', 'required');
                $mockOrgCoursesRepository->method('findOneBy')->will($this->throwException($dataProcessingExceptionHandler));
            } else {
                $mockOrgCourse = $this->getMock('Synapse\AcademicBundle\Entity\OrgCourses', [
                    'getId'
                ]);
                $mockOrgCourse->method('getId')->willReturn(14);
                $mockOrgCoursesRepository->method('findOneBy')->willReturn($mockOrgCourse);
            }

            if ($errorType == 'invalid_person') {
                $dataProcessingExceptionHandler->addErrors("Person ID 1 is not valid at the organization.", 'faculty_id', 'required');
                $mockOrgCoursesRepository->method('findOneBy')->will($this->throwException($dataProcessingExceptionHandler));
            } else {
                $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);
            }

            if ($errorType == 'invalid_faculty') {
                $dataProcessingExceptionHandler->addErrors("Faculty ID 1 is not valid at the organization.", 'faculty_id', 'required');
                $mockOrgCoursesRepository->method('findOneBy')->will($this->throwException($dataProcessingExceptionHandler));
            } else {
                $mockFacultyService->method('isPersonAFaculty')->willReturn(true);
            }

            $mockFacultyService->method('isFacultyActive')->willReturn($isActive);

            if ($errorType == 'invalid_permissionset_name') {
                $dataProcessingExceptionHandler->addErrors("Permissionset Name Invalid-Permissionset-Name is not valid at the organization.", 'permissionset_name');
                $mockOrgPermissionsetRepository->method('findOneBy')->will($this->throwException($dataProcessingExceptionHandler));
            } else {
                $mockOrgPermissionSet = new OrgPermissionset();
                $mockOrgPermissionsetRepository->method('findOneBy')->willReturn($mockOrgPermissionSet);
            }

            $mockDataProcessingUtilityService->method('setErrorMessageOrValueInArray')->willReturnCallback(function ($records, $errorArray) {
                $responseArray = [];
                foreach ($records as $key => $value) {
                    if (array_key_exists($key, $errorArray)) {
                        $responseArray[$key]['value'] = $value;
                        $responseArray[$key]['message'] = $errorArray[$key];
                    } else {
                        $responseArray[$key] = $value;
                    }
                }
                return $responseArray;
            });

            if ($errorType == 'is_faculty_in_course') {
                $dataProcessingExceptionHandler->addErrors("Faculty ID 1 already exist at this course 1 .", 'faculty_id', 'required');
                $mockOrgCoursesRepository->method('findOneBy')->will($this->throwException($dataProcessingExceptionHandler));
            } else {
                $mockOrgCourseFacultyRepository->method('findOneBy')->willReturn(false);
            }

            if ($errorType == 'is_faculty_in_course_as_student') {
                $dataProcessingExceptionHandler->addErrors("Faculty ID 1 is already in the course as a student.", 'faculty_id', 'required');
                $mockOrgCoursesRepository->method('findOneBy')->will($this->throwException($dataProcessingExceptionHandler));
            } else {
                $mockOrgCourseStudentRepository->method('findOneBy')->willReturn(false);
            }

            try {
                $courseService = new CourseService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $courseService->addFacultyToCourses($courseFacultyListDTO, $this->setOrganization(), $this->getPersonInstance(), $isInternal);

                $courseFacultyDto = $courseFacultyListDTO->getCourseFacultyList();
                $expectedResultArray = $this->buildCourseFacultyResponseArray($courseFacultyDto[0], $errorType, $expectedResult);
                $this->assertEquals($results, $expectedResultArray);

            } catch (SynapseException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test0: Case for Course does not exist, throws validation exception, When is internal = true
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    true,
                    "invalid_course",
                    true,
                    "Course ID 1 is not valid at this organization."
                ],
                // Test1: Case for Course does not exist, returns error array, When is internal = false
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    false,
                    "invalid_course",
                    true,
                    "Course ID 1 is not valid at this organization."
                ],
                // Test2: Case for invalid_person, throws validation exception, When is internal = true
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    true,
                    "invalid_person",
                    false,
                    "Person ID 1 is not valid at the organization."
                ],
                // Test3: Case for invalid_person, returns error array, When is internal = false
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    false,
                    "invalid_person",
                    true,
                    "Person ID 1 is not valid at the organization."
                ],
                // Test4: Case for invalid_faculty, throws validation exception, When is internal = true
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    true,
                    "invalid_faculty",
                    false,
                    "Faculty ID 1 is not valid at the organization."
                ],
                // Test5: Case for invalid_faculty, returns error array, When is internal = false
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    false,
                    "invalid_faculty",
                    false,
                    "Faculty ID 1 is not valid at the organization."
                ],
                // Test6: Case for is_not_as_faculty_in_course, throws validation exception, When is internal = true
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    true,
                    "is_faculty_in_course",
                    true,
                    "Faculty ID 1 already exist at this course 1 ."
                ],
                // Test7: Case for is_not_as_faculty_in_course, returns error array, When is internal = false
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    false,
                    "is_faculty_in_course",
                    true,
                    "Faculty ID 1 already exist at this course 1 ."
                ],
                // Test8: Case for faculty already in course as a student, throws validation exception, When is internal = true
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    true,
                    "is_faculty_in_course_as_student",
                    true,
                    "Faculty ID 1 is already in the course as a student."
                ],
                // Test9: Case for faculty already in course as a student, returns error array, When is internal = false
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    false,
                    "is_faculty_in_course_as_student",
                    true,
                    "Faculty ID 1 is already in the course as a student."
                ],
                // Test10: Case for faculty added in course
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    false,
                    "",
                    true,
                    ""
                ],
                // Test11: Case for invalid_permissionset_name, throws validation exception, When is internal = true
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    true,
                    "invalid_permissionset_name",
                    true,
                    "Permissionset Name Invalid-Permissionset-Name is not valid at the organization."
                ],
                // Test12: Case for invalid_permissionset_name, returns error array, When is internal = false
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    false,
                    "invalid_permissionset_name",
                    true,
                    "Permissionset Name Invalid-Permissionset-Name is not valid at the organization."
                ],
                // Test13: Case for valid permissionset_name, When is internal = true
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    true,
                    "",
                    true,
                    ""
                ],
                // Test14: Case for valid permissionset_name, When is internal = false
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    false,
                    "",
                    true,
                    ""
                ],
                // Test15: Case  faculty is not active
                [
                    $this->setCourseListDTO($this->courseFacultyArray),
                    true,
                    "faculty_id",
                    false,
                    "Faculty ID 1 is not active."
                ]
            ]
        ]);
    }

    private function setCourseListDTO($courseData, $returnCourseStudentsDto = false)
    {
        $courseFacultyDTO = new entityCourseFacultyDTO();
        $courseFacultyDTO->setCourseId($courseData['course_id']);
        $courseFacultyDTO->setFacultyId($courseData['faculty_id']);
        $courseFacultyDTO->setPermissionsetName($courseData['permissionset_name']);
        if ($returnCourseStudentsDto) {
            return $courseFacultyDTO;
        }

        $courseFacultyListDTO = new CourseFacultyListDTO();
        $courseFacultyListDTO->setCourseFacultyList([$courseFacultyDTO]);
        return $courseFacultyListDTO;
    }

    private function buildCourseFacultyResponseArray($courseFacultyDto, $errorType, $errorMessage)
    {
        $responseArray = $errorMessageArray = $errorArray = [];
        $courseArray['course_id'] = $courseFacultyDto->getCourseId();
        $courseArray['faculty_id'] = $courseFacultyDto->getFacultyId();
        $courseArray['permissionset_name'] = $courseFacultyDto->getPermissionsetName();

        if ($errorType == 'invalid_permissionset_name') {
            $errorMessageArray['permissionset_name'] = $errorMessage;
        }

        if ($errorType == 'invalid_course') {
            $errorMessageArray['course_id'] = $errorMessage;
        }

        if ($errorType == 'invalid_person' || $errorType == 'invalid_faculty' || $errorType == 'is_faculty_in_course' || $errorType == 'is_faculty_in_course_as_student') {
            $errorMessageArray['faculty_id'] = $errorMessage;
        }

        if (!empty($errorType)) {
            $errorArray[] = $this->buildCourseResponseArray($courseArray, $errorMessageArray);
            $createdArray = [];
        } else {
            $createdArray[] = $courseArray;
        }

        $responseArray['data']['created_count'] = count($createdArray);
        $responseArray['data']['created_records'] = $createdArray;
        $responseArray['errors']['error_count'] = count($errorArray);
        $responseArray['errors']['error_records'] = $errorArray;
        return $responseArray;
    }

    public function testIsFacultyInCourse()
    {
        $this->specify("Test is faculty already added in course", function ($facultyId, $courseId, $errorType, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Mock Repository
            $mockOrgCourseFacultyRepository = $this->getMock('orgCourseFacultyRepository', ['findOneBy']);
            if ($errorType == 'valid') {
                $mockPerson = $this->getPersonInstance('1');
            } else {
                $mockPerson = null;
            }

            $mockOrgCourseFacultyRepository->method('findOneBy')->willReturn($mockPerson);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgCourseFacultyRepository::REPOSITORY_KEY,
                        $mockOrgCourseFacultyRepository
                    ],
                ]);

            $courseService = new CourseService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $courseService->isFacultyInCourse($facultyId, $courseId);
            $this->assertEquals($results, $expectedResult);
        }, [
            'examples' => [
                // Test0: Case when faculty is not added in course
                [
                    $this->courseId,
                    $this->facultyId,
                    "invalid",
                    false
                ],
                // Test1: Case when faculty already added in course
                [
                    $this->courseId,
                    $this->facultyId,
                    "valid",
                    true
                ]
            ]
        ]);
    }

    public function testIsFacultyInCourseAsStudent()
    {
        $this->specify("Test is faculty in course as student", function ($facultyId, $courseId, $errorType, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Mock Repository
            $mockOrgCourseStudentRepository = $this->getMock('OrgCourseStudentRepository', ['findOneBy']);
            if ($errorType == 'valid') {
                $mockPerson = $this->getPersonInstance('1');
            } else {
                $mockPerson = null;
            }

            $mockOrgCourseStudentRepository->method('findOneBy')->willReturn($mockPerson);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgCourseStudentRepository::REPOSITORY_KEY,
                        $mockOrgCourseStudentRepository
                    ],
                ]);

            $courseService = new CourseService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $courseService->isFacultyInCourseAsStudent($facultyId, $courseId);
            $this->assertEquals($results, $expectedResult);
        }, [
            'examples' => [
                // Test0: Case when faculty is not student for course
                [
                    $this->courseId,
                    $this->facultyId,
                    "invalid",
                    false
                ],
                // Test1: Case when faculty is a student for course
                [
                    $this->courseId,
                    $this->facultyId,
                    "valid",
                    true
                ]
            ]
        ]);
    }

    private $facultyDetailsArray = [
        0 => [
            "faculty_id" => "1001",
            "firstname" => "First Name",
            "lastname" => "Last Name",
            "primary_email" => "faculty@mailinator.com",
            "org_permission_set" => "1001",
            "external_id" => "1001"
        ]
    ];

    private $studentDetailsArray = [
        0 => [
            "student_id" => "2001",
            "firstname" => "First Name",
            "lastname" => "Last Name",
            "primary_email" => "student@mailinator.com",
            "academic_updates" => false,
            "external_id" => "2001",
            "student_status" => "1"
        ]
    ];

    private $courseDetailsExpectedResultArray = [
        0 => [
            "total_students" => 1,
            "total_faculties" => 1,
            "course_id" => "9001",
            "course_name" => "Course Name",
            "subject_code" => "Subject CodeCourse Number",
            "section_number" => "Section Number",
            "faculty_details" => [
                0 => [
                    "faculty_id" => "1001",
                    "first_name" => "First Name",
                    "last_name" => "Last Name",
                    "email" => "faculty@mailinator.com",
                    "permissionset_id" => "1001",
                    "id" => "1001"
                ]
            ],
            "student_details" => [
                0 => [
                    "student_id" => "2001",
                    "first_name" => "First Name",
                    "last_name" => "Last Name",
                    "email" => "student@mailinator.com",
                    "academic_updates" => false,
                    "id" => "2001",
                    "student_status" => "1"
                ]
            ]
        ]
    ];

    public function testGetCourseDetails()
    {
        $this->specify("Test to Get Course Details", function ($type, $viewMode, $userId, $courseId, $organizationId, $expectedError, $facultyDetailsArray, $studentDetailsArray, $expectedResult, $skipKey = null) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Mocking Manager service
            $managerService = $this->getMock('Manager', ['checkAccessToOrganization', 'hasAccess']);
            $managerService->method('checkAccessToOrganization')->willReturn(true);

            // Mock Repositories
            $mockOrgCourseRepository = $this->getMock('OrgCoursesRepository', ['findOneBy', 'getSingleCourseFacultiesDetails', 'getParticipantStudentsInCourse']);
            $mockOrgCourseFacultyRepository = $this->getMock('OrgCourseFacultyRepository', ['findOneBy']);
            $mockAcademicUpdateRepository = $this->getMock('AcademicUpdateRepository', ['findBy']);
            $mockAcademicRecordRepository = $this->getMock('academicRecordRepository', ['findOneBy']);

            $mockAcademicUpdateRepository->method('findBy')->willReturn(null);

            if ($courseId != -1) {
                $mockOrgCourse = $this->getOrgCourseInstance($courseId);
                $mockOrgCourseRepository->method('findOneBy')->willReturn($mockOrgCourse);
            } else {
                $mockOrgCourseRepository->method('findOneBy')->willReturn(null);
            }

            // Mock OrgCourseFaculty
            $mockOrgCourseFaculty = $this->getMock('OrgCourseFaculty', ['getId']);
            if ($userId != -1) {
                $mockOrgCourseFacultyRepository->method('findOneBy')->willReturn($mockOrgCourseFaculty);
            } else {
                $mockOrgCourseFacultyRepository->method('findOneBy')->willReturn(null);
            }

            if ($skipKey == 'faculty_id') {
                $this->facultyDetailsArray[0]['faculty_id'] = null;
            }
            $mockOrgCourseRepository->method('getSingleCourseFacultiesDetails')->willReturn([$this->facultyDetailsArray[0]]);

            $mockOrgCourseRepository->method('getParticipantStudentsInCourse')->willReturn([$this->studentDetailsArray[0]]);

            $mockAcademicRecordRepository->method('findOneBy')->willReturn(null);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgCoursesRepository::REPOSITORY_KEY,
                        $mockOrgCourseRepository
                    ],
                    [
                        OrgCourseFacultyRepository::REPOSITORY_KEY,
                        $mockOrgCourseFacultyRepository
                    ],
                    [
                        AcademicUpdateRepository::REPOSITORY_KEY,
                        $mockAcademicUpdateRepository
                    ],
                    [
                        AcademicRecordRepository::REPOSITORY_KEY,
                        $mockAcademicRecordRepository
                    ]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        Manager::SERVICE_KEY,
                        $managerService
                    ]
                ]);
            try {
                $courseService = new CourseService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $courseService->getCourseDetails($type, $viewMode, $userId, $courseId, $organizationId);
                $this->assertEquals($expectedResult, $results);
            } catch (SynapseException $e) {
                $this->assertEquals($expectedError, $e->getMessage());
            }
        }, [
            'examples' => [
                // Test0: Invalid course id will throw validation exception
                [
                    'coordinator',
                    'json',
                    4883133,
                    -1,
                    203,
                    'Course not found',
                    [],
                    [],
                    [],
                ],
                // Test1: Invalid person id will throw validation exception
                [
                    'faculty',
                    'json',
                    -1,
                    2986565,
                    203,
                    'Faculty not found',
                    [],
                    [],
                    [],
                ],
                // Test2: Returns list of faculty and student
                [
                    'coordinator',
                    'json',
                    '8001',
                    '9001',
                    '203',
                    '',
                    [$this->facultyDetailsArray[0]],
                    [$this->studentDetailsArray[0]],
                    $this->getExpectedResultDto($this->courseDetailsExpectedResultArray[0]),
                    null
                ],
                // Test4: Returns list of faculty without faculty_id key
                [
                    'coordinator',
                    'json',
                    '8001',
                    '9001',
                    '203',
                    '',
                    [$this->facultyDetailsArray[0]],
                    [$this->studentDetailsArray[0]],
                    $this->getExpectedResultDto($this->courseDetailsExpectedResultArray[0], 'faculty_id'),
                    'faculty_id'
                ]
            ]
        ]);
    }


    private function getExpectedResultDto($expectedResultArray, $skipKey = null)
    {
        $singleCourseDto = new SingleCourseDto();
        $singleCourseDto->setTotalFaculties($expectedResultArray['total_faculties']);
        $singleCourseDto->setTotalStudents($expectedResultArray['total_students']);
        $singleCourseDto->setCourseId($expectedResultArray['course_id']);
        $singleCourseDto->setCourseName($expectedResultArray['course_name']);
        $singleCourseDto->setSubjectCode($expectedResultArray['subject_code']);
        $singleCourseDto->setSectionNumber($expectedResultArray['section_number']);

        foreach ($expectedResultArray['faculty_details'] as $faculty) {
            $facultyDetailsDto = new FacultyDetailsDto();
            if ($skipKey == 'faculty_id') {
                $facultyDetailsDto->setFacultyId(null);
            } else {
                $facultyDetailsDto->setFacultyId($faculty['faculty_id']);
            }
            $facultyDetailsDto->setFirstName($faculty['first_name']);
            $facultyDetailsDto->setLastName($faculty['last_name']);
            $facultyDetailsDto->setEmail($faculty['email']);
            $facultyDetailsDto->setPermissionsetId($faculty['permissionset_id']);
            $facultyDetailsDto->setId($faculty['id']);
            $facultyDetails[] = $facultyDetailsDto;
        }

        foreach ($expectedResultArray['student_details'] as $student) {
            $studentDetailsDto = new StudentDetailsDto();
            $studentDetailsDto->setStudentId($student['student_id']);
            $studentDetailsDto->setFirstName($student['first_name']);
            $studentDetailsDto->setLastName($student['last_name']);
            $studentDetailsDto->setEmail($student['email']);
            $studentDetailsDto->setId($student['id']);
            $studentDetailsDto->setAcademicUpdates($student['academic_updates']);
            $studentDetailsDto->setStudentStatus($student['student_status']);
            $studentDetails[] = $studentDetailsDto;
        }

        $singleCourseDto->setFacultyDetails($facultyDetails);
        $singleCourseDto->setStudentDetails($studentDetails);
        return $singleCourseDto;
    }
}