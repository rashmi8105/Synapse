<?php
namespace Synapse\ReportsBundle\Service\Impl;

use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\OrganizationlangService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\SearchBundle\Service\Impl\SearchService;

class CompletionReportsServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    private $aggregateCompletionVariablesWithRisk = [
        [
            'years_from_retention_track' => 0,
            'risk_level_text' => 'green',
            'risk_level' => 4,
            'numerator_count' => 0,
            'denominator_count' => 2
        ],
        [
            'years_from_retention_track' => 0,
            'risk_level_text' => 'yellow',
            'risk_level' => 3,
            'numerator_count' => 0,
            'denominator_count' => 2
        ],
        [
            'years_from_retention_track' => 0,
            'risk_level_text' => 'red',
            'risk_level' => 2,
            'numerator_count' => 0,
            'denominator_count' => 5
        ],
        [
            'years_from_retention_track' => 0,
            'risk_level_text' => 'red2',
            'risk_level' => 1,
            'numerator_count' => 0,
            'denominator_count' => 2
        ],
        [
            'years_from_retention_track' => 1,
            'risk_level_text' => 'green',
            'risk_level' => 4,
            'numerator_count' => 1,
            'denominator_count' => 2
        ],
        [
            'years_from_retention_track' => 1,
            'risk_level_text' => 'yellow',
            'risk_level' => 3,
            'numerator_count' => 1,
            'denominator_count' => 5
        ],
        [
            'years_from_retention_track' => 1,
            'risk_level_text' => 'red',
            'risk_level' => 2,
            'numerator_count' => 1,
            'denominator_count' => 4
        ],
        [
            'years_from_retention_track' => 1,
            'risk_level_text' => 'red2',
            'risk_level' => 1,
            'numerator_count' => 1,
            'denominator_count' => 6
        ],
        [
            'years_from_retention_track' => 2,
            'risk_level_text' => 'green',
            'risk_level' => 4,
            'numerator_count' => 2,
            'denominator_count' => 3
        ],
        [
            'years_from_retention_track' => 2,
            'risk_level_text' => 'yellow',
            'risk_level' => 3,
            'numerator_count' => 2,
            'denominator_count' => 4
        ],
        [
            'years_from_retention_track' => 2,
            'risk_level_text' => 'red',
            'risk_level' => 2,
            'numerator_count' => 2,
            'denominator_count' => 5
        ],
        [
            'years_from_retention_track' => 2,
            'risk_level_text' => 'red2',
            'risk_level' => 1,
            'numerator_count' => 2,
            'denominator_count' => 3
        ],
        [
            'years_from_retention_track' => 3,
            'risk_level_text' => 'green',
            'risk_level' => 4,
            'numerator_count' => 3,
            'denominator_count' => 4
        ],
        [
            'years_from_retention_track' => 3,
            'risk_level_text' => 'yellow',
            'risk_level' => 3,
            'numerator_count' => 3,
            'denominator_count' => 6
        ],
        [
            'years_from_retention_track' => 3,
            'risk_level_text' => 'red',
            'risk_level' => 2,
            'numerator_count' => 3,
            'denominator_count' => 5
        ],
        [
            'years_from_retention_track' => 3,
            'risk_level_text' => 'red2',
            'risk_level' => 1,
            'numerator_count' => 3,
            'denominator_count' => 4
        ],
        [
            'years_from_retention_track' => 4,
            'risk_level_text' => 'green',
            'risk_level' => 4,
            'numerator_count' => 4,
            'denominator_count' => 5
        ],
        [
            'years_from_retention_track' => 4,
            'risk_level_text' => 'yellow',
            'risk_level' => 3,
            'numerator_count' => 4,
            'denominator_count' => 1
        ],
        [
            'years_from_retention_track' => 4,
            'risk_level_text' => 'red',
            'risk_level' => 2,
            'numerator_count' => 4,
            'denominator_count' => 5
        ],
        [
            'years_from_retention_track' => 4,
            'risk_level_text' => 'red2',
            'risk_level' => 1,
            'numerator_count' => 4,
            'denominator_count' => 2
        ],
        [
            'years_from_retention_track' => 5,
            'risk_level_text' => 'green',
            'risk_level' => 4,
            'numerator_count' => 5,
            'denominator_count' => 6
        ],
        [
            'years_from_retention_track' => 5,
            'risk_level_text' => 'yellow',
            'risk_level' => 3,
            'numerator_count' => 5,
            'denominator_count' => 3
        ],
        [
            'years_from_retention_track' => 5,
            'risk_level_text' => 'red',
            'risk_level' => 2,
            'numerator_count' => 5,
            'denominator_count' => 2
        ],
        [
            'years_from_retention_track' => 5,
            'risk_level_text' => 'red2',
            'risk_level' => 1,
            'numerator_count' => 5,
            'denominator_count' => 4
        ]

    ];

    public function testGenerateReport()
    {
        $this->specify("Test GenerateReport function", function ($organizationId, $personId, $academicYearId, $expectedResult) {

            $rawData = [];
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            $mockContainer = $this->getMock('Container', [
                'get'
            ]);
            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);

            // Initializing AlertNotificationsService to be mocked
            $mockAlertNotificationService = $this->getMock('AlertNotificationsService', array('createNotification'));

            // Initializing OrganizationlangService to be mocked
            $mockOrganizationLangService = $this->getMock('OrganizationlangService', array('getOrganization'));

            // Initializing SearchService to be mocked
            $mockSearchService = $this->getMock('SearchService', array('getRiskDates'));

            // Initializing Serializer to be mocked
            $mockSerializer = $this->getMock('Serializer', array('serialize'));

            // Initializing OrganizationRepository to be mocked
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', array('findOneBy'));

            // Initializing OrgAcademicYearRepository to be mocked
            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', array('getCurrentAcademicYear','find'));

            // Initializing OrgPersonStudentRetentionTrackingGroupRepository to be mocked
            $mockOrgPersonStudentRetentionTrackingGroupRepository = $this->getMock('OrgPersonStudentRetentionTrackingGroupRepository', array('areStudentsAssignedToThisRetentionTrackingYear'));

            // Initializing PersonRepository to be mocked
            $mockPersonRepository = $this->getMock('PersonRepository',array('find'));

            // Initializing ReportsRepository to be mocked
            $mockReportsRepository = $this->getMock('ReportsRepository', array('getAggregatedCompletionVariablesWithRisk'));

            // Initializing ReportsRunningStatusRepository to be mocked
            $mockReportsRunningStatusRepository = $this->getMock('ReportsRunningStatusRepository', array('findOneBy', 'update', 'flush'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ],
                    [
                        OrgAcademicYearRepository::REPOSITORY_KEY,
                        $mockOrgAcademicYearRepository
                    ],
                    [
                        OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY,
                        $mockOrgPersonStudentRetentionTrackingGroupRepository
                    ],
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        ReportsRepository::REPOSITORY_KEY,
                        $mockReportsRepository
                    ],
                    [
                        ReportsRunningStatusRepository::REPOSITORY_KEY,
                        $mockReportsRunningStatusRepository
                    ]
                ]);

            $mockContainer->expects($this->any())
                ->method('get')
                ->willReturnMap(
                    [
                        [
                            AlertNotificationsService::SERVICE_KEY,
                            $mockAlertNotificationService
                        ],
                        [
                            OrganizationlangService::SERVICE_KEY,
                            $mockOrganizationLangService
                        ],
                        [
                            SearchService::SERVICE_KEY,
                            $mockSearchService
                        ],
                        [
                            SynapseConstant::JMS_SERIALIZER_CLASS_KEY,
                            $mockSerializer
                        ]
                    ]);



            $organizationDetails['name'] = 'ABC Test';
            $mockOrganizationLangService->method('getOrganization')->willReturn($organizationDetails);

            $mockOrgAcademicYear = $this->getMock('OrgAcademicYear', array('getYearId'));
            $mockYear = $this->getMock('Year', array('getId'));
            $mockOrgAcademicYear->method('getYearId')->willReturn($mockYear);
            $mockOrgAcademicYearRepository->method('find')->willReturn($mockOrgAcademicYear);

            $mockOrgPersonStudentRetentionTrackingGroupRepository->method('areStudentsAssignedToThisRetentionTrackingYear')->willReturn(true);

            $mockPerson = $this->getMock('Person', array('getId', 'getFirstname', 'getLastname'));
            $mockPerson->method('getFirstname')->willReturn('Test1');
            $mockPerson->method('getLastname')->willReturn('Test2');
            $mockPersonRepository->method('find')->willReturn($mockPerson);

            $mockReportsRepository->method('getAggregatedCompletionVariablesWithRisk')->willReturn($this->aggregateCompletionVariablesWithRisk);


            $mockReportsRunningStatus = $this->getMock('ReportsRunningStatus', array('getId', 'getFilteredStudentIds', 'setStatus', 'setResponseJson'));
            $mockReportsRunningStatus->method('setStatus')->willReturn('C');
            $mockReportsRunningStatus->method('getFilteredStudentIds')->willReturn('4614765, 4614752, 4614729, 4614721, 4615031, 4614728, 4614735, 4614736, 4614748, 4614756, 4614757, 4614761');
            $mockReportsRunningStatus->method('setResponseJson')->willReturn([]);
            $mockReportsRunningStatusRepository->method('findOneBy')->willReturn($mockReportsRunningStatus);

            $reportRunningDto = $this->createReportRunningDto($personId, $organizationId, $academicYearId);

            $completionReportService = new CompletionReportService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $completionReportService->generateReport($personId, $reportRunningDto, $rawData);
            unset($result['report_info']['report_date']); // un-setting the date as it always take the current time using "date" , which would always change .
            $this->assertEquals($expectedResult, $result);
        },  [
            'examples' =>
                [
                    [
                        //completion report for academic year 2016-2017
                        203,
                        4891006,
                        163,
                        [
                            "id" => 12,
                            "organization_id" => 203,
                            "report_id" => 12,
                            "report_sections" =>
                                [
                                    "report_name" => "Completion Report",
                                    "reportId" => 12,
                                    "short_code" => "CR"
                                ],
                            "search_attributes" =>
                                [
                                    "retention_date" =>
                                        [
                                            "academic_year_id" => 163,
                                            "start_date" => "2016-04-01",
                                            "end_date" => "2017-07-02",
                                            "academic_year_name" => "2016-2017"
                                        ],
                                    "risk_date" =>
                                        [
                                            "start_date" => "2016-04-01",
                                            "end_date" => "2017-02-27"
                                        ]
                                ],
                            "campus_info" =>
                                [
                                    "campus_id" => 203,
                                    "campus_name" => "ABC Test",
                                    "campus_logo" => NULL
                                ],
                            "request_json" => [],
                            "report_info" =>
                                [
                                    "report_id" => 12,
                                    "report_name" => "Completion Report",
                                    "short_code" => "CR",
                                    "report_instance_id" => 12,
                                    "report_by" =>
                                        [
                                            "first_name" => "Test1",
                                            "last_name" => "Test2"
                                        ]
                                ],
                            "report_data" =>
                                [
                                    "green" =>
                                        [
                                            "year1" =>
                                                [
                                                    "percent" => 0,
                                                    "number" => 0
                                                ],
                                            "count" => 6,
                                            "year2" =>
                                                [
                                                    "percent" => 50,
                                                    "number" => 1,
                                                ],
                                            "year3" =>
                                                [
                                                    "percent" => 67,
                                                    "number" => 2
                                                ],
                                            "year4" =>
                                                [
                                                    "percent" => 75,
                                                    "number" => 3
                                                ],
                                            "year5" =>
                                                [
                                                    "percent" => 80,
                                                    "number" => 4
                                                ],
                                            "year6" =>
                                                [
                                                    "percent" => 83,
                                                    "number" => 5
                                                ]
                                        ],
                                    "yellow" =>
                                        [
                                            "year1" =>
                                                [
                                                    "percent" => 0,
                                                    "number" => 0
                                                ],
                                            "count" => 3,
                                            "year2" =>
                                                [
                                                    "percent" => 20,
                                                    "number" => 1,
                                                ],
                                            "year3" =>
                                                [
                                                    "percent" => 50,
                                                    "number" => 2
                                                ],
                                            "year4" =>
                                                [
                                                    "percent" => 50,
                                                    "number" => 3
                                                ],
                                            "year5" =>
                                                [
                                                    "percent" => 400,
                                                    "number" => 4
                                                ],
                                            "year6" =>
                                                [
                                                    "percent" => 167,
                                                    "number" => 5
                                                ]
                                        ],
                                    "red" =>
                                        [
                                            "year1" =>
                                                [
                                                    "percent" => 0,
                                                    "number" => 0
                                                ],
                                            "count" => 2,
                                            "year2" =>
                                                [
                                                    "percent" => 25,
                                                    "number" => 1,
                                                ],
                                            "year3" =>
                                                [
                                                    "percent" => 40,
                                                    "number" => 2
                                                ],
                                            "year4" =>
                                                [
                                                    "percent" => 60,
                                                    "number" => 3
                                                ],
                                            "year5" =>
                                                [
                                                    "percent" => 80,
                                                    "number" => 4
                                                ],
                                            "year6" =>
                                                [
                                                    "percent" => 250,
                                                    "number" => 5
                                                ]
                                        ],
                                    "red2" =>
                                        [
                                            "year1" =>
                                                [
                                                    "percent" => 0,
                                                    "number" => 0
                                                ],
                                            "count" => 4,
                                            "year2" =>
                                                [
                                                    "percent" => 17,
                                                    "number" => 1,
                                                ],
                                            "year3" =>
                                                [
                                                    "percent" => 67,
                                                    "number" => 2
                                                ],
                                            "year4" =>
                                                [
                                                    "percent" => 75,
                                                    "number" => 3
                                                ],
                                            "year5" =>
                                                [
                                                    "percent" => 200,
                                                    "number" => 4
                                                ],
                                            "year6" =>
                                                [
                                                    "percent" => 125,
                                                    "number" => 5
                                                ]
                                        ],
                                    "overall" =>
                                        [
                                            "count" => 12,
                                            "year1" =>
                                                [
                                                    "percent" => 0,
                                                    "number" => 0
                                                ],
                                            "year2" =>
                                                [
                                                    "percent" => 24,
                                                    "number" => 4
                                                ],
                                            "year3" =>
                                                [
                                                    "percent" => 53,
                                                    "number" => 8
                                                ],
                                            "year4" =>
                                                [
                                                    "percent" => 63,
                                                    "number" => 12
                                                ],
                                            "year5" =>
                                                [
                                                    "percent" => 123,
                                                    "number" => 16
                                                ],
                                            "year6" =>
                                                [
                                                    "percent" => 133,
                                                    "number" => 20
                                                ]
                                        ],
                                    "total_students" => 12
                                ]
                        ]
                    ]
                ]
            ]);
    }

    private function createReportRunningDto($personId, $organizationId, $academicYearId){

        $reportSections['report_name'] = "Completion Report";
        $reportSections['reportId'] = 12;
        $reportSections['short_code'] = "CR";

        $retentionDate['academic_year_id'] = $academicYearId;
        $retentionDate['start_date'] = "2016-04-01";
        $retentionDate['end_date'] = "2017-07-02";
        $retentionDate['academic_year_name'] = "2016-2017";

        $riskDate['start_date'] = "2016-04-01";
        $riskDate['end_date'] = "2017-02-27";

        $searchAttributes['retention_date'] = $retentionDate;
        $searchAttributes['risk_date'] = $riskDate;

        $reportRunningStatusDto = new ReportRunningStatusDto();
        $reportRunningStatusDto->setId(12);
        $reportRunningStatusDto->setReportId(12);
        $reportRunningStatusDto->setShortCode('CR');
        $reportRunningStatusDto->setStatus('IP');
        $reportRunningStatusDto->setPersonId($personId);
        $reportRunningStatusDto->setOrganizationId($organizationId);
        $reportRunningStatusDto->setSearchAttributes($searchAttributes);
        $reportRunningStatusDto->setReportSections($reportSections);
        return $reportRunningStatusDto;

    }
}