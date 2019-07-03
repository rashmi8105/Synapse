<?php

namespace Synapse\ReportsBundle\Service\Impl;

use Codeception\Specify;
use Codeception\TestCase\Test;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\ReportsBundle\DAO\GroupResponseReportDAO;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\RestBundle\Utility\RestUtilityService;


class GroupResponseReportServiceTest extends Test
{
    use Specify;

    private $reportDataArray = [
        '0' =>
            [
                'group_name' => 'All Students',
                'org_group_id' => '346050',
                'external_id' => 'ALLSTUDENTS',
                'parent_group' => null,
                'student_id_cnt' => 1153,
                'responded' => 639,
                'response_rate' => 55
            ],
        '1' =>
            [
                'group_name' => 'Test Group 00377584',
                'org_group_id' => '377584',
                'external_id' => 'EXID00377584',
                'parent_group' => null,
                'student_id_cnt' => 1070,
                'responded' => 568,
                'response_rate' => 53
            ],
        '2' =>
            [
                'group_name' => 'Test Group 00377586',
                'org_group_id' => '377586',
                'external_id' => 'EXID00377586',
                'parent_group' => 'Test Group 00377584',
                'student_id_cnt' => 3,
                'responded' => 1,
                'response_rate' => 33
            ],
        '3' =>
            [
                'group_name' => 'Test Group 00377587',
                'org_group_id' => '377587',
                'external_id' => 'EXID00377587',
                'parent_group' => 'Test Group 00377584',
                'student_id_cnt' => 3,
                'responded' => 2,
                'response_rate' => 67
            ],
        '4' =>
            [
                'group_name' => 'Test Group 00377588',
                'org_group_id' => '377588',
                'external_id' => 'EXID00377588',
                'parent_group' => 'Test Group 00377584',
                'student_id_cnt' => 4,
                'responded' => 3,
                'response_rate' => 75
            ]
    ];
    private $currentDateObject;
    private $currentTime;

    public function testGenerateGroupResponseReport()
    {
        $this->currentDateObject = date('Y-12-31');
        $this->currentTime = time();
        $this->specify("Test Generate Group Response Report", function ($loggedInUserId, $academicYearId, $organizationId, $outputFormat, $pageNumber, $recordsPerPage, $sortBy, $errorType, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            $mockContainer = $this->getMock('Container', [
                'get'
            ]);

            $mockLogger = $this->getMock('Logger', [
                'debug'
            ]);

            $mockPersonRepository = $this->getMock("PersonRepository", ['find']);
            $mockReportsRepository = $this->getMock("ReportsRepository", ['find']);
            $mockRestUtilityService = $this->getMock("RestUtilityService", ['getSortColumnAndDirection']);
            $mockGroupResponseReportDAO = $this->getMock("GroupResponseReportDAO", ['getGroupStudentCountAndResponseRateByFaculty', 'getOverallCountGroupStudentCountAndResponseRateByFaculty']);
            $mockCsvUtilityService = $this->getMock("CsvUtilityService", ['generateCSV']);


            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    PersonRepository::REPOSITORY_KEY,
                    $mockPersonRepository
                ],
                [
                    ReportsRepository::REPOSITORY_KEY,
                    $mockReportsRepository
                ]
            ]);

            $mockContainer->method('get')->willReturnMap([
                [
                    RestUtilityService::SERVICE_KEY,
                    $mockRestUtilityService
                ],
                [
                    GroupResponseReportDAO::DAO_KEY,
                    $mockGroupResponseReportDAO
                ],
                [
                    CSVUtilityService::SERVICE_KEY,
                    $mockCsvUtilityService
                ]
            ]);

            $reportRunningStatusDto = $this->createReportRunningStatusDto($loggedInUserId, $organizationId, $academicYearId);

            $mockPerson = $this->getMock('Person', ['getId', 'getFirstname', 'getLastname']);
            $mockPerson->method('getId')->willReturn($loggedInUserId);
            $mockPerson->method('getFirstname')->willReturn('Test');
            $mockPerson->method('getLastname')->willReturn('User1');
            if ($loggedInUserId) {
                $mockPersonRepository->method('find')->willReturn($mockPerson);
            } else {
                $mockPersonRepository->method('find')->willReturn(null);
            }


            $mockReport = $this->getMock('Report', ['getId', 'getName', 'getShortCode']);
            $mockReport->method('getId')->willReturn($reportRunningStatusDto->getId());
            $mockReportSection = $reportRunningStatusDto->getReportSections();
            $mockReport->method('getName')->willReturn($mockReportSection['report_name']);
            $mockReport->method('getShortCode')->willReturn($reportRunningStatusDto->getShortCode());
            if ($errorType == 'report_not_existing') {
                $mockReportsRepository->method('find')->willReturn(null);
            } else {
                $mockReportsRepository->method('find')->willReturn($mockReport);
            }


            $mockGroupResponseReportDAO->method('getGroupStudentCountAndResponseRateByFaculty')->willReturn($this->reportDataArray);

            $overallCount = [
                [
                    'student_id_cnt' => 1153,
                    'responded' => 639
                ]
            ];

            $mockGroupResponseReportDAO->method('getOverallCountGroupStudentCountAndResponseRateByFaculty')->willReturn($overallCount);


            $groupResponseReportService = new GroupResponseReportService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $result = $groupResponseReportService->generateGroupResponseReport($reportRunningStatusDto, $loggedInUserId, $organizationId, $outputFormat, $pageNumber, $recordsPerPage, $sortBy);

                if ($outputFormat != 'csv') {
                    $result['report_info']['report_date'] = $this->currentDateObject;
                } else {
                    $result['file_name'] = "59-group-response-" . $this->currentTime . ".csv";
                }
                $this->assertEquals($result, $expectedResult);
            } catch (\Exception $e) {

                $this->assertEquals($e->getMessage(), $expectedResult);
            }
        }, [
                'examples' =>
                    [
                        //case 1 : For valid user ans organization , returns valid report data
                        [
                            183086,
                            351,
                            59,
                            null,
                            1,
                            25,
                            'group_name',
                            null,
                            [
                                'total_students' => 1153,
                                'responded' => 639,
                                'responded_percentage' => 55.0,
                                'total_records' => 5,
                                'records_per_page' => 25,
                                'current_page' => 1,
                                'total_pages' => 1.0,
                                'search_attributes' =>
                                    [
                                        'retention_date' =>
                                            [
                                                'academic_year_id' => 351,
                                                'start_date' => '2017-04-01',
                                                'end_date' => '2017-07-02',
                                                'academic_year_name' => '2017-2018',
                                            ],
                                        'risk_date' =>
                                            [
                                                'start_date' => '2017-04-01',
                                                'end_date' => '2017-07-02'
                                            ],
                                        'survey_filter' =>
                                            [
                                                'survey_id' => 19,
                                                'academic_year_name' => '2017-18 Academic Year',
                                                'org_academic_year_id' => 351,
                                                'year_id' => '201718',
                                                'survey_name' => 'Transition One',
                                                'cohort' => 1,
                                                'cohort_Name' => 'Survey Cohort 1'
                                            ],
                                        'filter_sort' => ''
                                    ],
                                'report_data' => $this->reportDataArray,
                                'report_sections' =>
                                    [
                                        'report_name' => 'Group Response Report',
                                        'reportId' => 6,
                                        'short_code' => 'SUR-GRR'
                                    ],
                                'report_info' =>
                                    [
                                        'report_id' => 6,
                                        'report_name' => 'Group Response Report',
                                        'short_code' => 'SUR-GRR',
                                        'report_instance_id' => '',
                                        'report_date' => $this->currentDateObject,
                                        'report_by' =>
                                            [
                                                'first_name' => 'Test',
                                                'last_name' => 'User1'
                                            ]
                                    ]
                            ]
                        ],
                        //case 2 : For invalid user , throws access denied exception
                        [
                            null,
                            351,
                            59,
                            null,
                            1,
                            25,
                            'group_name',
                            null,
                            'The user does not exist within Mapworks'
                        ],
                        // case 3 : For non-existing report id , throws synapse validation exception
                        [
                            667744,
                            351,
                            59,
                            null,
                            1,
                            25,
                            'group_name',
                            'report_not_existing',
                            'Report not found'
                        ],
                        // case 4 : For output format = csv , returns csv file.
                        [
                            455563,
                            351,
                            59,
                            'csv',
                            1,
                            25,
                            'group_name',
                            null,
                            [
                                'file_name' => "59-group-response-" . $this->currentTime . ".csv"
                            ]
                        ]
                    ]
            ]
        );
    }

    private function createReportRunningStatusDto($personId, $organizationId, $academicYearId)
    {

        $reportSections['report_name'] = "Group Response Report";
        $reportSections['reportId'] = 6;
        $reportSections['short_code'] = "SUR-GRR";

        $retentionDate['academic_year_id'] = $academicYearId;
        $retentionDate['start_date'] = "2017-04-01";
        $retentionDate['end_date'] = "2017-07-02";
        $retentionDate['academic_year_name'] = "2017-2018";

        $riskDate['start_date'] = "2017-04-01";
        $riskDate['end_date'] = "2017-07-02";

        $searchAttributes['retention_date'] = $retentionDate;
        $searchAttributes['risk_date'] = $riskDate;
        $surveyFilter = [
            "survey_id" => 19,
            "academic_year_name" => "2017-18 Academic Year",
            "org_academic_year_id" => $academicYearId,
            "year_id" => "201718",
            "survey_name" => "Transition One",
            "cohort" => 1,
            "cohort_Name" => "Survey Cohort 1"
        ];

        $searchAttributes['survey_filter'] = $surveyFilter;
        $searchAttributes['filter_sort'] = '';


        $reportRunningStatusDto = new ReportRunningStatusDto();
        $reportRunningStatusDto->setId(6);
        $reportRunningStatusDto->setReportId(6);
        $reportRunningStatusDto->setShortCode('SUR-GRR');
        $reportRunningStatusDto->setStatus('IP');
        $reportRunningStatusDto->setPersonId($personId);
        $reportRunningStatusDto->setOrganizationId($organizationId);
        $reportRunningStatusDto->setSearchAttributes($searchAttributes);
        $reportRunningStatusDto->setReportSections($reportSections);
        return $reportRunningStatusDto;
    }
}