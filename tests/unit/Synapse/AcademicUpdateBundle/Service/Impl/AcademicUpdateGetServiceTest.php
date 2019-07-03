<?php

use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateGetService;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateHistoryDto;
use Synapse\AcademicUpdateBundle\EntityDto\StudentHistoryDetailsDto;
use Synapse\CoreBundle\Exception\SynapseException;

class AcademicUpdateGetServiceTest extends \Codeception\Test\Unit
{
    use\Codeception\Specify;

    public function testGetAcademicUpdateStudentHistory()
    {
        $this->specify("", function($expectedResults, $organizationId = null, $courseId = null,
                                                $studentId = null, $mockOrgAcademicYearId = null,
                                                $mockAcademicUpdateData = null, $containsUpdateDate = false){

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockAcademicYearService = $this->getMock('academicYearService', ['getCurrentOrgAcademicYearId']);
            $mockDateUtilityService = $this->getMock('dateUtilityService', ['adjustDateTimeToOrganizationTimezone']);

            $mockAcademicUpdateRepository = $this->getMock('academicUpdateRepository', ['getAcademicUpdateStudentHistory']);
            $mockOrgCoursesRepository = $this->getMock('orgCoursesRepository', ['findOneBy']);

            $mockOrgCoursesObject = $this->getMock('orgCourses', ['getCourseName']);

            $mockContainer->method('get')->willReturnMap([
                [AcademicYearService::SERVICE_KEY, $mockAcademicYearService],
                [DateUtilityService::SERVICE_KEY, $mockDateUtilityService]
            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [AcademicUpdateRepository::REPOSITORY_KEY, $mockAcademicUpdateRepository],
                [OrgCoursesRepository::REPOSITORY_KEY, $mockOrgCoursesRepository]
            ]);

            $mockOrgCoursesRepository->method('findOneBy')->willReturn($mockOrgCoursesObject);

            if ($mockOrgAcademicYearId) {
                $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn($mockOrgAcademicYearId);
            } else {
                $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willThrowException(new SynapseValidationException('No currently active academic year'));
            }

            $mockAcademicUpdateRepository->method('getAcademicUpdateStudentHistory')->willReturn($mockAcademicUpdateData);

            if ($containsUpdateDate) {
                $mockDateUtilityService->method('adjustDateTimeToOrganizationTimezone')->willReturn('');
            }

            if ($mockAcademicUpdateData) {
                $mockOrgCoursesObject->method('getCourseName')->willReturn('Course Name');
            }

            $academicUpdateGetService = new AcademicUpdateGetService($mockRepositoryResolver, $mockLogger, $mockContainer);

            try {
                $results = $academicUpdateGetService->getAcademicUpdateStudentHistory($organizationId, $courseId, $studentId);
                $this->assertEquals($expectedResults, $results);
            } catch (SynapseException $e ){
                $this->assertEquals($expectedResults, $e->getMessage());
            }


        }, [
            'examples' =>
                [
                    //No active org academic year
                    [
                        'No currently active academic year',
                        1,
                        1,
                        1
                    ],
                    //No academic update data returned
                    [
                        $this->generateAcademicUpdateHistoryDto(),
                        1,
                        1,
                        1,
                        1,
                        []
                    ],
                    //Academic Updates for user
                    [
                        $this->generateAcademicUpdateHistoryDto(1, 1, 1, 'Course Name', $this->generateStudentHIstoryDetailsDtoArray()),
                        1,
                        1,
                        1,
                        1,
                        $this->generateAcademicUpdateHistoryArray(),
                        true

                    ],
                    //Null organization, course, and student Ids.
                    [
                        'No currently active academic year'
                    ]
                ]
            ]
        );
    }

    /**
     * @param int|null $organizationId
     * @param int|null $studentId
     * @param int|null $courseId
     * @param string|null $courseName
     * @param StudentHistoryDetailsDto[]|null $studentHistoryDetailsDtoArray
     * @return AcademicUpdateHistoryDto
     */
    private function generateAcademicUpdateHistoryDto($organizationId = null, $studentId = null, $courseId = null, $courseName = null, $studentHistoryDetailsDtoArray = null)
    {
        $academicUpdateHistoryDto = new AcademicUpdateHistoryDto();

        $academicUpdateHistoryDto->setOrganizationId($organizationId);
        $academicUpdateHistoryDto->setStudentId($studentId);
        $academicUpdateHistoryDto->setCourseId($courseId);
        $academicUpdateHistoryDto->setCourseName($courseName);
        $academicUpdateHistoryDto->setAcademicUpdateHistory($studentHistoryDetailsDtoArray);

        return $academicUpdateHistoryDto;
    }

    /**
     * @param int|null $indexToRemove
     * @return StudentHistoryDetailsDto[]
     */
    private function generateStudentHIstoryDetailsDtoArray($indexToRemove = null)
    {
        $failedInProgressGradeWithCommentStudentHistoryDetailsDto = new StudentHistoryDetailsDto();
        $failedInProgressGradeWithCommentStudentHistoryDetailsDto->setDate('');
        $failedInProgressGradeWithCommentStudentHistoryDetailsDto->setGrade('F');
        $failedInProgressGradeWithCommentStudentHistoryDetailsDto->setComments('Student failed the first three tests.');

        $dInProgressGradeWithAbsencesStudentHistoryDetailsDto = new StudentHistoryDetailsDto();
        $dInProgressGradeWithAbsencesStudentHistoryDetailsDto->setDate('');
        $dInProgressGradeWithAbsencesStudentHistoryDetailsDto->setGrade('D');
        $dInProgressGradeWithAbsencesStudentHistoryDetailsDto->setAbsences(10);

        $failedInProgressGradeWithAbsencesAndReferStudentHistoryDetailsDto = new StudentHistoryDetailsDto();
        $failedInProgressGradeWithAbsencesAndReferStudentHistoryDetailsDto->setDate('');
        $failedInProgressGradeWithAbsencesAndReferStudentHistoryDetailsDto->setGrade('F');
        $failedInProgressGradeWithAbsencesAndReferStudentHistoryDetailsDto->setAbsences(15);
        $failedInProgressGradeWithAbsencesAndReferStudentHistoryDetailsDto->setAcademicAssistRefer(true);


        $studentHistoryDetailsArray = [
            $failedInProgressGradeWithCommentStudentHistoryDetailsDto,
            $dInProgressGradeWithAbsencesStudentHistoryDetailsDto,
            $failedInProgressGradeWithAbsencesAndReferStudentHistoryDetailsDto
        ];

        if($indexToRemove) {
            unset($studentHistoryDetailsArray[$indexToRemove]);
        }

        return $studentHistoryDetailsArray;
    }

    /**
     * @param int|null $indexToRemove
     * @return array
     */
    private function generateAcademicUpdateHistoryArray($indexToRemove = null)
    {
        $academicUpdateArray = [
            [
                'update_date' => '2016-03-01 00:00:00',
                'failure_risk_level' => '',
                'grade' => 'F',
                'absence' => '',
                'comment' => 'Student failed the first three tests.',
                'refer_for_assistance' => '',
                'send_to_student' => ''
            ],
            [
                'update_date' => '2016-04-07 00:00:00',
                'failure_risk_level' => '',
                'grade' => 'D',
                'absence' => 10,
                'comment' => '',
                'refer_for_assistance' => '',
                'send_to_student' => ''
            ],
            [
                'update_date' => '2016-05-01 00:00:00',
                'failure_risk_level' => '',
                'grade' => 'F',
                'absence' => 15,
                'comment' => '',
                'refer_for_assistance' => true,
                'send_to_student' => ''
            ],
        ];

        if ($indexToRemove) {
            unset($academicUpdateArray[$indexToRemove]);
        }

        return $academicUpdateArray;
    }
}