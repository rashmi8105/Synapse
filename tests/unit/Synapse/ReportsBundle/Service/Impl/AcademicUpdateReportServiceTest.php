<?php

namespace Synapse\ReportsBundle\Service\Impl;

use Codeception\Specify;
use Codeception\TestCase\Test;
use Faker\Provider\DateTime;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Service\Impl\JobService;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\SearchBundle\Repository\OrgSearchRepository;
use Synapse\SearchBundle\Service\Impl\SearchService;


class AcademicUpdateReportServiceTest extends Test
{
    use Specify;


    private $riskPermission = [
        [
            'student_id' => 11,
            'risk_flag' => 1
        ],
        [
            'student_id' => 10,
            'risk_flag' => 1
        ]
    ];

    private $noriskPermission = [
        [
            'student_id' => 11,
            'risk_flag' => 0
        ],
        [
            'student_id' => 10,
            'risk_flag' => 0
        ]
    ];

    private $listAcademicUpdateIds = [1, 2];

    private $studentList = [
        [
            'student_id' => 10,
            'first_name' => "teststudentname1",
            'last_name' => "teststudentlastname1",
        ],
        [
            'student_id' => 11,
            'first_name' => "teststudentname2",
            'last_name' => "teststudentlastname2",
        ]
    ];

    private $listAcademicUpdateDetails = [
        [
            'student_id' => 10,
            'student_first_name' => "teststudentname1",
            'student_last_name' => "teststudentlastname1",
            'faculty_first_name' => "facultyfirstname1",
            'faculty_last_name' => "facultylastname1",
            'academic_update_id' => 100,
            'course_name' => "testcourse",
            'created_at' => "2017-10-10",
            'by_request' => "",
            'failure_risk' => "red",
            'inprogress_grade' => "A",
            'absences' => 3,
            'student_status' => 1,
            'risk_text' => "red",
            'risk_imagename' => "someImage",
            'comment' => "testcomment",
            'term_name' => "termname",
            'term_id' => 2
        ],
        [
            'student_id' => 11,
            'student_first_name' => "teststudentname2",
            'student_last_name' => "teststudentlastname2",
            'faculty_first_name' => "facultyfirstname3",
            'faculty_last_name' => "facultylastname4",
            'academic_update_id' => 100,
            'course_name' => "testcourse",
            'created_at' => "2017-10-10",
            'by_request' => "",
            'failure_risk' => "red",
            'inprogress_grade' => "A",
            'absences' => 3,
            'student_status' => 1,
            'risk_text' => "green",
            'risk_imagename' => "someImage",
            'comment' => "testcomment",
            'term_name' => "termname",
            'term_id' => 2
        ]
    ];


    public function testCreateAcademicUpdateReportCSV()
    {
        $this->specify("Test Create academic Update report csv", function ($saveSearchDto, $loggedUserId) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            $mockContainer = $this->getMock('Container', [
                'get'
            ]);

            $mockLogger = $this->getMock('Logger', [
                'debug'
            ]);


            $mockAcademicYearService = $this->getMock("AcademicYearService", ['getCurrentOrgAcademicYearId']);
            $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn(1);

            $mockJobService = $this->getMock("JobService", ['addJobToQueue']);
            $mockJobService->method('addJobToQueue')->willReturn(1);

            $mockContainer->method('get')->willReturnMap([

                [
                    AcademicYearService::SERVICE_KEY,
                    $mockAcademicYearService
                ],
                [
                    JobService::SERVICE_KEY,
                    $mockJobService
                ]
            ]);

            $academicUpdateReportService = new AcademicUpdateReportService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $academicUpdateReportService->createAcademicUpdateCSV($saveSearchDto, $loggedUserId);
            $this->assertEquals($result, SynapseConstant::DOWNLOAD_IN_PROGRESS_MESSAGE);
        }, [
            'examples' => [

                // Testing for intimating the user that the download is in progress and a notification will be generated once its is done.
                [
                    new SaveSearchDto(), 1, //This method does not do any processing . the csv generation happens in a job
                ]
            ]

        ]);
    }


    public function testGetStudentsForAcademicUpdateReport()
    {

        $this->specify("Test Create academic Update report csv", function ($saveSearchDto, $loggedUserId, $returnData) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            $mockContainer = $this->getMock('Container', [
                'get'
            ]);

            $mockLogger = $this->getMock('Logger', [
                'debug'
            ]);


            $mockReportsRepository = $this->getMock('ReportsRepository', [
                'getAllAcademicUpdateReportInformationBasedOnCriteria',
                'getAllAcademicUpdateReportForListedAcademicUpdateIds'
            ]);

            if ($returnData) {
                $mockReportsRepository->method('getAllAcademicUpdateReportInformationBasedOnCriteria')->willReturn($this->studentList);
            } else {
                $mockReportsRepository->method('getAllAcademicUpdateReportInformationBasedOnCriteria')->willReturn([]);
            }


            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    ReportsRepository::REPOSITORY_KEY,
                    $mockReportsRepository
                ],
            ]);

            $mockAcademicYearService = $this->getMock("AcademicYearService", ['getCurrentOrgAcademicYearId']);
            $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn(1);

            $mockJobService = $this->getMock("JobService", ['addJobToQueue']);
            $mockJobService->method('addJobToQueue')->willReturn(1);


            $mockSearchService = $this->getMock("SearchService", ['getStudentListBasedCriteria', 'getFilterCriteriaForAcademicUpdate']);
            $mockSearchService->method('getStudentListBasedCriteria')->willReturn(1);
            $mockSearchService->method('getFilterCriteriaForAcademicUpdate')->willReturn(1);


            $mockContainer->method('get')->willReturnMap([

                [
                    AcademicYearService::SERVICE_KEY,
                    $mockAcademicYearService
                ],
                [
                    JobService::SERVICE_KEY,
                    $mockJobService
                ], [
                    SearchService::SERVICE_KEY,
                    $mockSearchService
                ]
            ]);

            $academicUpdateReportService = new AcademicUpdateReportService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $academicUpdateReportService->getStudentsForAcademicUpdateReport($saveSearchDto, $loggedUserId);
            $this->assertInstanceOf('Synapse\SearchBundle\EntityDto\SearchDto', $result);
            $studentArray = $result->getSearchResult();
            foreach ($studentArray as $studentKey => $studentObject) {
                $this->assertInstanceOf('Synapse\SearchBundle\EntityDto\SearchResultListDto', $studentObject);
                $this->assertEquals($studentObject->getStudentId(), $this->studentList[$studentKey]['student_id']);
                $this->assertEquals($studentObject->getStudentFirstName(), $this->studentList[$studentKey]['first_name']);
                $this->assertEquals($studentObject->getStudentLastName(), $this->studentList[$studentKey]['last_name']);
            }

        }, [
            'examples' => [

                // Testing for getting the list of student  in the report
                [
                    new SaveSearchDto(), 1, $returnData = true //returns student data
                ],
                // Testing for getting the list of students - No students
                [
                    new SaveSearchDto(), 1, $returnData = false //returns empty array
                ]
            ]

        ]);

    }


    public function testGenerateReport()
    {
        $this->specify("Test AcademicUpdateReport", function ($saveSearchDto, $loggedUserId, $pageNumber, $recordsPerPage, $sortByFieldString, $permission) {


            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            $mockContainer = $this->getMock('Container', [
                'get'
            ]);

            $mockLogger = $this->getMock('Logger', [
                'debug'
            ]);

            $mockReportsRepository = $this->getMock('ReportsRepository', [
                'getAllAcademicUpdateReportInformationBasedOnCriteria',
                'getAllAcademicUpdateReportForListedAcademicUpdateIds'
            ]);

            $mockReportsRepository->method('getAllAcademicUpdateReportInformationBasedOnCriteria')->willReturnCallback(
                function ($organizationId, $currentAcademicYearId, $studentSelectSQL, $academicUpdateFilterCriteria, $sortByQueryString = '', $limit = '', $studentListFlag = false, $isCount = false, $participationFlag = true) {

                    if ($studentListFlag) {
                        return $this->studentList;
                    }

                    if ($isCount) {
                        return [
                            ['total_count' => count($this->listAcademicUpdateIds)]
                        ];
                    }

                    return $this->listAcademicUpdateIds;

                });

            $mockReportsRepository->method('getAllAcademicUpdateReportForListedAcademicUpdateIds')->willReturn($this->listAcademicUpdateDetails);

            $mockPersonObject = $this->getMock("Person", ['getFirstname', 'getLastname']);

            $mockPersonObject->method('getFirstname')->willReturn("person firstname");
            $mockPersonObject->method('getLastname')->willReturn("person lastname");
            $mockPersonRepository = $this->getMock('PersonRepository', ['find']);

            $mockPersonRepository->method('find')->willReturn($mockPersonObject);

            $mockOrgSearchRepository = $this->getMock('OrgSearchRepository', ['getRiskIntentData']);


            if ($permission) {

                $mockOrgSearchRepository->method('getRiskIntentData')->willReturn(
                    $this->riskPermission
                );
            } else {
                $mockOrgSearchRepository->method('getRiskIntentData')->willReturn(
                    $this->noriskPermission
                );
            }

            $mockDateUtilityService = $this->getMock('DateUtilityService', ['getOrganizationISOTimeZone']);

            $mockDateUtilityService->method('getOrganizationISOTimeZone')->willReturn('US/Central');

            $mockAcademicYearService = $this->getMock("AcademicYearService", ['getCurrentOrgAcademicYearId']);
            $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn(1);

            $mockSearchService = $this->getMock("SearchService", ['getStudentListBasedCriteria', 'getFilterCriteriaForAcademicUpdate']);
            $mockSearchService->method('getStudentListBasedCriteria')->willReturn(1);
            $mockSearchService->method('getFilterCriteriaForAcademicUpdate')->willReturn(1);

            $mockJobService = $this->getMock("JobService", ['addJobToQueue']);
            $mockJobService->method('addJobToQueue')->willReturn(1);


            $mockContainer->method('get')->willReturnMap([
                [
                    AcademicYearService::SERVICE_KEY,
                    $mockAcademicYearService
                ],
                [
                    DateUtilityService::SERVICE_KEY,
                    $mockDateUtilityService
                ],
                [
                    SearchService::SERVICE_KEY,
                    $mockSearchService
                ],
                [
                    JobService::SERVICE_KEY,
                    $mockJobService
                ]

            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    ReportsRepository::REPOSITORY_KEY,
                    $mockReportsRepository
                ],
                [
                    PersonRepository::REPOSITORY_KEY,
                    $mockPersonRepository
                ],
                [
                    OrgSearchRepository::REPOSITORY_KEY,
                    $mockOrgSearchRepository
                ]
            ]);

            $academicUpdateReportService = new AcademicUpdateReportService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $currentDate = new \DateTime();
            $result = $academicUpdateReportService->generateReport($saveSearchDto, $loggedUserId, $pageNumber, $recordsPerPage, $sortByFieldString, $currentDate);

            $this->assertEquals($result['total_records'], count($this->listAcademicUpdateIds));
            $this->assertEquals($result['report_info']['report_date'], $currentDate->format(SynapseConstant::DATE_FORMAT_WITH_TIMEZONE));
            $resultData = $result['report_data'];

            foreach ($resultData as $resultKey => $result) {
                $this->assertInstanceOf('Synapse\ReportsBundle\EntityDto\AcademicUpdateReportDto', $result);

                $this->assertEquals($result->getStudentId(), $this->listAcademicUpdateDetails[$resultKey]['student_id']);
                $this->assertEquals($result->getStudentLastName(), $this->listAcademicUpdateDetails[$resultKey]['student_last_name']);
                $this->assertEquals($result->getStudentFirstName(), $this->listAcademicUpdateDetails[$resultKey]['student_first_name']);
                $this->assertEquals($result->getCourseName(), $this->listAcademicUpdateDetails[$resultKey]['course_name']);
                $this->assertEquals($result->getFacultyFirstName(), $this->listAcademicUpdateDetails[$resultKey]['faculty_first_name']);
                $this->assertEquals($result->getFacultyLastName(), $this->listAcademicUpdateDetails[$resultKey]['faculty_last_name']);
                $this->assertEquals($result->getfailureRisk(), $this->listAcademicUpdateDetails[$resultKey]['failure_risk']);
                if ($permission) {
                    $this->assertEquals($result->getRiskText(), $this->listAcademicUpdateDetails[$resultKey]['risk_text']);
                } else {
                    $this->assertEquals($result->getRiskText(), "gray");
                }
            }
        }, [
            'examples' => [

                // will Return data with permission to view risk
                [
                    new SaveSearchDto(), 1, 1, 3, '', 1
                ],
                // will return data with no permission to view risk, in this case all the risk would be shown as gray
                [
                    new SaveSearchDto(), 1, 1, 3, '', 0
                ],
            ]

        ]);
    }

    public function testGenerateReportCSV()
    {
        $this->specify("Test AcademicUpdateReport", function ($searchAttributes, $selectedAttributeCsv, $academicUpdatesSearchAttributes, $organizationId, $loggedUserId, $currentAcademicYearId, $permission) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            $mockContainer = $this->getMock('Container', [
                'get'
            ]);

            $mockLogger = $this->getMock('Logger', [
                'debug'
            ]);

            $mockReportsRepository = $this->getMock('ReportsRepository', [
                'getAllAcademicUpdateReportInformationBasedOnCriteria',
                'getAllAcademicUpdateReportForListedAcademicUpdateIds'
            ]);

            $mockReportsRepository->method('getAllAcademicUpdateReportInformationBasedOnCriteria')->willReturnCallback(
                function ($organizationId, $currentAcademicYearId, $studentSelectSQL, $academicUpdateFilterCriteria, $sortByQueryString = '', $limit = '', $studentListFlag = false, $isCount = false, $participationFlag = true) {

                    if ($studentListFlag) {
                        return $this->studentList;
                    }

                    if ($isCount) {
                        return [
                            ['total_count' => count($this->listAcademicUpdateIds)]
                        ];
                    }

                    return $this->listAcademicUpdateIds;

                });

            $mockReportsRepository->method('getAllAcademicUpdateReportForListedAcademicUpdateIds')->willReturn($this->listAcademicUpdateDetails);

            $mockPersonRepository = $this->getMock('PersonRepository', ['find']);


            $mockOrgSearchRepository = $this->getMock('OrgSearchRepository', ['getRiskIntentData']);

            if ($permission) {

                $mockOrgSearchRepository->method('getRiskIntentData')->willReturn(
                    $this->riskPermission
                );
            } else {
                $mockOrgSearchRepository->method('getRiskIntentData')->willReturn(
                    $this->noriskPermission
                );
            }


            $mockAcademicYearService = $this->getMock("AcademicYearService", ['getCurrentOrgAcademicYearId']);
            $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn(1);

            $mockDateUtilityService = $this->getMock("dateUtilityService", ['getOrganizationISOTimeZone']);
            $mockDateUtilityService->method('getOrganizationISOTimeZone')->willReturn(1);

            $mockSearchService = $this->getMock("SearchService", ['getStudentListBasedCriteria', 'getFilterCriteriaForAcademicUpdate']);
            $mockSearchService->method('getStudentListBasedCriteria')->willReturn(1);
            $mockSearchService->method('getFilterCriteriaForAcademicUpdate')->willReturn(1);

            $mockCsvUtilityService = $this->getMock('CsvUtilityService', ['createCSVFileInTempFolder', 'writeToFile', 'getRowsToWrite', 'copyFileToDirectory']);
            $mockAlertNotificationService = $this->getMock('alertNotificationService', ['createCSVDownloadNotification']);


            $mockJobService = $this->getMock("JobService", ['addJobToQueue']);
            $mockJobService->method('addJobToQueue')->willReturn(1);


            $mockContainer->method('get')->willReturnMap([
                [
                    AcademicYearService::SERVICE_KEY,
                    $mockAcademicYearService
                ],
                [
                    SearchService::SERVICE_KEY,
                    $mockSearchService
                ],
                [
                    JobService::SERVICE_KEY,
                    $mockJobService
                ],
                [
                    DateUtilityService::SERVICE_KEY,
                    $mockDateUtilityService
                ],
                [
                    CSVUtilityService::SERVICE_KEY,
                    $mockCsvUtilityService
                ],
                [
                    AlertNotificationsService::SERVICE_KEY,
                    $mockAlertNotificationService
                ]

            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    ReportsRepository::REPOSITORY_KEY,
                    $mockReportsRepository
                ],
                [
                    PersonRepository::REPOSITORY_KEY,
                    $mockPersonRepository
                ],
                [
                    OrgSearchRepository::REPOSITORY_KEY,
                    $mockOrgSearchRepository
                ]
            ]);

            $academicUpdateReportService = new AcademicUpdateReportService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $academicUpdateReportService->generateReportCSV($searchAttributes, $selectedAttributeCsv, $academicUpdatesSearchAttributes, $organizationId, $loggedUserId, $currentAcademicYearId);
            $fileName = $result['file_name'];

            $fileNameArray = explode("-", $fileName);
            $this->assertEquals($fileNameArray[0], $organizationId);
            $this->assertEquals($fileNameArray[1], "academic");
            $this->assertEquals($fileNameArray[2], "update");


        }, [
            'examples' => [

                // Generating csv for organizationId =1 , Empty arrays are  passed as they would be used for repository methods , which are mocked anyways
                [
                    [], [], [], $organizationId = 1, 1, 1, $permission = 1
                ],
                // Generating csv for organizationId =2 ,Empty arrays are  passed as they wouule be used for repository methods , which are mocked anyways
                [
                    [], [], [], $organizationId = 2, 1, 1, $permission = 0
                ],

            ]

        ]);
    }


}