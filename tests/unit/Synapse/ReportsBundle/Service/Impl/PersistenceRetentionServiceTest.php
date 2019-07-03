<?php

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\OrganizationlangService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Service\Impl\PersistenceRetentionService;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\ReportsBundle\Repository\RetentionCompletionVariableNameRepository;
use Synapse\SearchBundle\Service\Impl\SearchService;




class PersistenceRetentionServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \UnitTester
     */

    use \Codeception\Specify;

    public $sections = array(
        "reportId" => 12,
        "reportDisable" => false,
        "report_name" => "Persistence and Retention Report",
        "short_code" => "PRR",
        "reportFilterPages" => [
            [
                "reportPage" => "trackingGroup",
                "title" => "Select a tracking group",
            ]
            ,
            [
                "reportPage" => "riskRange",
                "title" => "Select a risk range",
            ]
            ,
            [
                "reportPage" => "filterAttributes",
                "title" => "Select attributes",
            ],
        ],
    );

    public $filter = array(
        "risk" => false,
        "active" => false,
        "activities" => false,
        "group" => false,
        "course" => false,
        "ebi" => false,
        "isp" => false,
        "static" => false,
        "factor" => false,
        "survey" => false,
        "isq" => false,
        "surveyMetadata" => false,
        "academicTerm" => false,
        "cohort" => false,
        "team" => false,
        "academicYears" => false,
    );

    public $rawData = [
        "organization_id" => "123",
        "person_id" => "1234567",
        "search_attributes" =>
            [
                "filterCount" => "2",
                "student_status" => "",
                "group_ids" => "",
                "datablocks" => [],
                "isps" => [],
                "static_cohort_list_ids" => "",
                "cohort_ids" => "",
                "retention_date" =>
                    [
                        "academic_year_id" => 190,
                        "start_date" => "2012-08-01",
                        "end_date" => "2012-09-10",
                        "academic_year_name" => "2012-13",
                    ],
                "risk_date" =>
                    [
                        "start_date" => "2012-08-09",
                        "end_date" => "2012-09-10",
                    ],
            ],
    ];

    protected $tester;

    /**
     * @expectedException \Synapse\CoreBundle\Exception\SynapseValidationException
     */
    public function testInvalidInitiateReportJob()
    {


        $reportRunningDto = $this->buildReportRunningDto(1, $this->filter, $this->sections);
        $mockIsCoordinator = false;
        $personId = 1234;


        //Create all mocks necessary for Service class creation
        $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
        $mockCache = $this->getMock('cache', array('run'));
        $mockResque = $this->getMock('resque', array('enqueue'));
        $mockLogger = $this->getMock('Logger', array('debug', 'error'));
        $mockContainer = $this->getMock('Container', array('get'));


        $mockOrganizationRoleRepo = $this->getMock('organizationRoleRepo', array('getUserCoordinatorRole'));


        $mockContainer->method('get')->willReturnMap(
            [
                ['bcc_resque.resque', $mockResque],

            ]
        );


        $mockRepositoryResolver->method('getRepository')->willReturnMap([
            ['SynapseCoreBundle:OrganizationRole', $mockOrganizationRoleRepo]
        ]);


        if (!$mockIsCoordinator) {
            $mockOrganizationId = null;

            $mockOrganizationRoleRepo->expects($this->any())->method('getUserCoordinatorRole')->with(
                $this->equalTo(1, $personId)
            )->willReturn($mockIsCoordinator);

            $mockResque->expects($this->any())->method('enqueue')->willReturn('');
            //Creating Class
            $PersistenceRetentionService = new PersistenceRetentionService(
                $mockRepositoryResolver,
                $mockLogger,
                $mockContainer,
                $mockCache,
                $mockResque
            );

            //Calling function
            $functionResults = $PersistenceRetentionService->initiateReportJob(
                $reportRunningDto
            );
        }
    }

    // Check validity of test with if statement

    private function buildReportRunningDto($orgIdStub, $filter, $sections = ['reportId' => 12])
    {
        $reportRunningStatus = new ReportRunningStatusDto();
        $reportRunningStatus->setId(1);
        $reportRunningStatus->setOrganizationId($orgIdStub);
        $reportRunningStatus->setReportId(12);
        $reportRunningStatus->setPersonId(1234);
        $reportRunningStatus->setSearchAttributes($filter);
        $reportRunningStatus->setReportSections($sections);
        $reportRunningStatus->setCreatedAt("2016-02-01 00:00:00");

        return $reportRunningStatus;
    }

    public function testValidInitiateReportJob()
    {
        $personId = 1234;


        $reportRunningDto = $this->buildReportRunningDto(1, $this->filter, $this->sections);
        $mockIsCoordinator = true;
        $expectedResult = null;


        //Create all mocks necessary for Service class creation
        $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
        $mockCache = $this->getMock('cache', array('run'));
        $mockResque = $this->getMock('resque', array('enqueue'));
        $mockLogger = $this->getMock('Logger', array('debug', 'error'));
        $mockContainer = $this->getMock('Container', array('get'));


        $mockOrganizationRoleRepo = $this->getMock('organizationRoleRepo', array('getUserCoordinatorRole'));


        $mockContainer->method('get')->willReturnMap(
            [
                ['bcc_resque.resque', $mockResque]
            ]
        );

        $mockRepositoryResolver->method('getRepository')->willReturnMap([
            ['SynapseCoreBundle:OrganizationRole', $mockOrganizationRoleRepo]
        ]);

        $mockOrganizationRoleRepo->expects($this->any())->method('getUserCoordinatorRole')->with(
            $this->equalTo(1, $personId)
        )->willReturn($mockIsCoordinator);

        $mockResque->expects($this->any())->method('enqueue')->willReturn('');

        //Creating Class
        $PersistenceRetentionService = new PersistenceRetentionService(
            $mockRepositoryResolver,
            $mockLogger,
            $mockContainer,
            $mockCache,
            $mockResque
        );

        //Calling function
        $functionResults = $PersistenceRetentionService->initiateReportJob(
            $reportRunningDto

        );

        $this->assertEquals($expectedResult, $functionResults);

    }

    public function testGenerateReportWithNewJSON()
    {
        $this->specify(
            'Test generate report',
            function (
                $mockReportRunningDto,
                $mockRiskStartDate,
                $mockRiskEndDate,
                $mockFilteredStudentList,
                $mockOrganizationLangObj,
                $expectedResult
            ) {

                //Create all mocks necessary for Service class creation
                $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));

                $mockLogger = $this->getMock('Logger', array('debug', 'error'));

                $mockContainer = $this->getMock('Container', array('get'));

                //Repositories that will be mocked away

                $mockAcademicYearRepository = $this->getMock('AcademicYearRepository', array('findFutureYears', 'find', 'getCurrentAcademicYear'));

                $mockEbiMetadataRepository = $this->getMock('EbiMetadataRepository', array('findOneBy'));

                $mockOrganizationRepository = $this->getMock('OrganizationRepository', array('find'));

                $mockOrgPersonStudentRetentionTrackingGroupRepository = $this->getMock('OrgPersonStudentRetentionTrackingGroupRepository', array('areStudentsAssignedToThisRetentionTrackingYear'));

                $mockReportsRepository = $this->getMock('ReportsRepository', array('getAggregatedRetentionVariablesWithRisk'));

                $mockReportRunningStatusRepository = $this->getMock('ReportRunningStatusRepository', array('find', 'update', 'flush'));

                $mockRetentionCompletionVariableNameRepository = $this->getMock('RetentionCompletionVariableNameRepository', array('getRetentionVariablesOrderedByYearType'));

                //Services that will be mocked away

                $mockAlertService = $this->getMock('AlertService', array('createNotification'));

                $mockDateUtilityService = $this->getMock('DateUtilityService', array('convertToUtcDatetime'));

                $mockOrganizationLangService = $this->getMock('OrganizationLangService', array('getOrganization'));

                $mockPersonService = $this->getMock('PersonService', array('find'));

                $mockSearchService = $this->getMock('SearchService', array('getRiskDates'));

                $mockSerializer = $this->getMock('Serializer', array('serialize'));

                $mockPersistenceRetentionService = $this->getMock('PersistenceRetentionService', array('formatRetentionDataset'));


                //Objects that will be mocked away

                $mockReportRunningStatusObj = $this->getMock('ReportRunningStatusObj',array('getFilteredStudentIds', 'setStatus', 'setResponseJson', 'getId', 'getCreatedAt'));

                $mockOrganizationImgObj = $this->getMock('OrganizationImgObj', array('getLogoFileName'));

                $mockPersonObj = $this->getMock('personObj', array('getFirstname', 'getLastname'));

                $mockObject = $this->getMock('object', array('explode', 'json_encode'));

                $mockReportRunningStatusDateObj = $this->getMock('date', array('format'));

                $mockReportId = "testReportID";
                $mockExplodedStudentList = explode(",", $mockFilteredStudentList);

                $mockOrganizationId = $mockReportRunningDto->getOrganizationId();
                $mockRiskDates['start_date'] =  $mockRiskStartDate;
                $mockRiskDates['end_date'] =  $mockRiskEndDate;
                $mockCurrentAcademicYearDetails = [
                    "org_academic_year_id" => 153,
                    "year_id" => "201516",
                    "year_name" => "2015-16",
                    "start_date" => "2012-08-09",
                    "end_date" => "2012-09-10"
                ];

                //mocking away all function calls outside of the tested function
                $mockRepositoryResolver->method('getRepository')->willReturnMap(
                    [
                        [EbiMetadataRepository::REPOSITORY_KEY, $mockEbiMetadataRepository],
                        [OrgAcademicYearRepository::REPOSITORY_KEY, $mockAcademicYearRepository],
                        [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                        [OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY, $mockOrgPersonStudentRetentionTrackingGroupRepository],
                        [ReportsRepository::REPOSITORY_KEY, $mockReportsRepository],
                        [ReportsRunningStatusRepository::REPOSITORY_KEY, $mockReportRunningStatusRepository],
                        [RetentionCompletionVariableNameRepository::REPOSITORY_KEY, $mockRetentionCompletionVariableNameRepository]
                    ]
                );
                $mockContainer->method('get')->willReturnMap(
                    [
                        [AlertNotificationsService::SERVICE_KEY, $mockAlertService],
                        [DateUtilityService::SERVICE_KEY, $mockDateUtilityService],
                        [OrganizationlangService::SERVICE_KEY, $mockOrganizationLangService],
                        [PersonService::SERVICE_KEY, $mockPersonService],
                        [SearchService::SERVICE_KEY, $mockSearchService],
                        [SynapseConstant::JMS_SERIALIZER_CLASS_KEY, $mockSerializer],
                        [PersistenceRetentionService::SERVICE_KEY, $mockPersistenceRetentionService]

                    ]
                );

                $mockReportRunningStatusRepository->method('find')->willReturn($mockReportRunningStatusObj);

                $mockReportRunningStatusObj->method('getFilteredStudentIds')->willReturn($mockFilteredStudentList);
                $mockReportRunningStatusObj->method('setStatus')->willReturn('IP');

                $mockObject->method('explode')->willReturn($mockExplodedStudentList);

                $mockReportRunningStatusRepository->method('update')->willReturn('');
                $mockReportRunningStatusRepository->method('flush')->willReturn('');

                $mockOrganizationLangService->method('getOrganization')->with(
                    $this->equalTo($mockOrganizationId)
                )->willReturn($mockOrganizationLangObj);
                $mockOrganizationRepository->method('find')->willReturn($mockOrganizationImgObj);
                $mockOrganizationImgObj->method('getLogoFileName')->willReturn('');

                $mockPersonService->method('find')->willReturn($mockPersonObj);

                $mockOrgPersonStudentRetentionTrackingGroupRepository->method('areStudentsAssignedToThisRetentionTrackingYear')->willReturn(true);

                $mockSearchService->method('getRiskDates')->willReturn($mockRiskDates);

                $mockOrgAcademicYear = $this->getMock('OrgAcademicYear',array('getYearId'));
                $mockYear = $this->getMock('Year',array('getId'));
                $mockOrgAcademicYear->method('getYearId')->willReturn($mockYear);
                $mockAcademicYearRepository->method('find')->willReturn($mockOrgAcademicYear);

                $mockRetentionCompletionVariableNameRepository->method('getRetentionVariablesOrderedByYearType')->willReturn($this->getRetentionVariablesOrderedByYearType());

                $mockAcademicYearRepository->method('getCurrentAcademicYear')->willReturn($mockCurrentAcademicYearDetails);

                $mockReportsRepository->method('getAggregatedRetentionVariablesWithRisk')->willReturn($this->getAggregatedRetentionVariablesWithRisk());
                $mockPersistenceRetentionService->method('mapDataToYearsFromRetentionTrack')->willReturn($this->getRetentionDataWithRisk());
                
                $mockReportRunningStatusObj->method('getCreatedAt')->willReturn($mockReportRunningStatusDateObj);
                $mockReportRunningStatusDateObj->method('format')->willReturn('2016-02-01 00:00:00');
                $mockPersonObj->method('getFirstname')->willReturn('Test1');
                $mockPersonObj->method('getLastname')->willReturn('Test2');

                $mockReportRunningStatusObj->method('setResponseJson')->willReturn('');
                $mockReportRunningStatusObj->method('getId')->willReturn($mockReportId);
                $mockAlertService->method('createNotification')->willReturn('');

                $mockSerializer->method('serialize')->willReturnCallback(function ($inputData) {
                    return json_encode($inputData);
                });

                //Creating class
                $PersistenceRetentionService = new PersistenceRetentionService($mockRepositoryResolver, $mockLogger, $mockContainer);

                //Calling function
                $functionResults = $PersistenceRetentionService->generateReport(
                    $mockReportRunningDto->getId(),
                    $mockReportRunningDto
                );

                $expectedResult = json_encode($expectedResult);

                $this->assertEquals($functionResults, $expectedResult);

            },
            [
                'examples' =>
                    [
                        //111, single retention data set per academic year
                        [
                            $this->buildReportRunningDto(1, $this->rawData['search_attributes'], $this->sections),
                            "2012-08-09",
                            "2012-09-10",
                            "12345,123456,1234567,12345678",
                            [
                                "name" => "Test University",
                            ],
                            [
                                "id" => 1,
                                "organization_id" => 1,
                                "report_id" => 12,
                                "report_sections" =>
                                    [
                                        "reportId" => 12,
                                        "reportDisable" => false,
                                        "report_name" => "Persistence and Retention Report",
                                        "short_code" => "PRR",
                                        "reportFilterPages" =>
                                            [
                                                0 =>
                                                    [
                                                        "reportPage" => "trackingGroup",
                                                        "title" => "Select a tracking group"
                                                    ],
                                                1 =>
                                                    [
                                                        "reportPage" => "riskRange",
                                                        "title" => "Select a risk range"
                                                    ],
                                                2 =>
                                                    [
                                                        "reportPage" => "filterAttributes",
                                                        "title" => "Select attributes"
                                                    ]
                                            ]
                                    ],
                                "search_attributes" =>
                                    [
                                        "filterCount" => "2",
                                        "student_status" => "",
                                        "group_ids" => "",
                                        "datablocks" => [],
                                        "isps" => [],
                                        "static_cohort_list_ids" => "",
                                        "cohort_ids" => "",
                                        "retention_date" =>
                                            [
                                                "academic_year_id" => 190,
                                                "start_date" => "2012-08-01",
                                                "end_date" => "2012-09-10",
                                                "academic_year_name" => "2012-13"
                                            ],
                                        "risk_date" =>
                                            [
                                                "start_date" => "2012-08-09",
                                                "end_date" => "2012-09-10"
                                            ]
                                    ],
                                "campus_info" =>
                                    [
                                        "campus_id" => 1,
                                        "campus_name" => "Test University",
                                        "campus_logo" => ""
                                    ],
                                "request_json" => new stdClass(),
                                "report_info" =>
                                    [
                                        "report_id" => 12,
                                        "report_name" => "Persistence and Retention Report",
                                        "short_code" => "PRR",
                                        "report_instance_id" => 1,
                                        "report_date" => "2016-02-01 00:00:00",
                                        "report_by" =>
                                            [
                                                "first_name" => "Test1",
                                                "last_name" => "Test2"
                                            ]
                                    ],
                                "report_data" =>
                                    [
                                        0 =>
                                            [
                                                "column_title" => "Retained to Midyear Year 1",
                                                "total_students_retained" => 11,
                                                "total_student_count" => 11,
                                                "percent" => 100,
                                                "persistence_retention_by_risk" =>
                                                    [
                                                        0 =>
                                                            [
                                                                "risk_color" => "green",
                                                                "students_retained" => 2,
                                                                "student_count" => 2,
                                                                "percent" => 100
                                                            ],
                                                        1 =>
                                                            [
                                                                "risk_color" => "yellow",
                                                                "students_retained" => 2,
                                                                "student_count" => 2,
                                                                "percent" => 100
                                                            ],
                                                        2 =>
                                                            [
                                                                "risk_color" => "red",
                                                                "students_retained" => 5,
                                                                "student_count" => 5,
                                                                "percent" => 100
                                                            ],
                                                        3 =>
                                                            [
                                                                "risk_color" => "red2",
                                                                "students_retained" => 2,
                                                                "student_count" => 2,
                                                                "percent" => 100
                                                            ]
                                                    ]
                                            ],
                                        1 =>
                                            [
                                                "column_title" => "Retained to Start of Year 2",
                                                "total_students_retained" => 11,
                                                "total_student_count" => 11,
                                                "percent" => 100,
                                                "persistence_retention_by_risk" =>
                                                    [
                                                        0 =>
                                                            [
                                                                "risk_color" => "green",
                                                                "students_retained" => 2,
                                                                "student_count" => 2,
                                                                "percent" => 100
                                                            ],
                                                        1 =>
                                                            [
                                                                "risk_color" => "yellow",
                                                                "students_retained" => 2,
                                                                "student_count" => 2,
                                                                "percent" => 100
                                                            ],
                                                        2 =>
                                                            [
                                                                "risk_color" => "red",
                                                                "students_retained" => 5,
                                                                "student_count" => 5,
                                                                "percent" => 100
                                                            ],
                                                        3 =>
                                                            [
                                                                "risk_color" => "red2",
                                                                "students_retained" => 2,
                                                                "student_count" => 2,
                                                                "percent" => 100
                                                            ]
                                                    ]
                                            ],
                                        2 =>
                                            [
                                                "column_title" => "Retained to Midyear Year 2",
                                                "total_students_retained" => 11,
                                                "total_student_count" => 11,
                                                "percent" => 100,
                                                "persistence_retention_by_risk" =>
                                                    [
                                                        0 =>
                                                            [
                                                                "risk_color" => "green",
                                                                "students_retained" => 2,
                                                                "student_count" => 2,
                                                                "percent" => 100
                                                            ],
                                                        1 =>
                                                            [
                                                                "risk_color" => "yellow",
                                                                "students_retained" => 2,
                                                                "student_count" => 2,
                                                                "percent" => 100
                                                            ],
                                                        2 =>
                                                            [
                                                                "risk_color" => "red",
                                                                "students_retained" => 5,
                                                                "student_count" => 5,
                                                                "percent" => 100
                                                            ],
                                                        3 =>
                                                            [
                                                                "risk_color" => "red2",
                                                                "students_retained" => 2,
                                                                "student_count" => 2,
                                                                "percent" => 100
                                                            ]
                                                    ]
                                            ],
                                        "total_students" => 4
                                    ]
                            ]
                        ]
                    ]
            ]
        );
    }



    private function getRetentionVariablesOrderedByYearType(){
        $retentionVariablesArray = [
            [
                "years_from_retention_track" => 0,
                "is_midyear_variable" => 1,
                "retention_variable" => "Retained to Midyear Year 1"
            ],
            [
                "years_from_retention_track" => 1,
                "is_midyear_variable" => 0,
                "retention_variable" => "Retained to Start of Year 2"
            ],
            [
                "years_from_retention_track" => 1,
                "is_midyear_variable" => 1,
                "retention_variable" => "Retained to Midyear Year 2"
            ],
            [
                "years_from_retention_track" => 2,
                "is_midyear_variable" => 0,
                "retention_variable" => "Retained to Start of Year 3"
            ],
            [
                "years_from_retention_track" => 2,
                "is_midyear_variable" => 1,
                "retention_variable" => "Retained to Midyear Year 3"
            ],
            [
                "years_from_retention_track" => 3,
                "is_midyear_variable" => 0,
                "retention_variable" => "Retained to Start of Year 4"
            ],
            [
                "years_from_retention_track" => 3,
                "is_midyear_variable" => 1,
                "retention_variable" => "Retained to Midyear Year 4"
            ]
        ];

        return $retentionVariablesArray;
    }
    
    private function getAggregatedRetentionVariablesWithRisk(){

        $aggregatedRetentionVariables = [
            [
                'years_from_retention_track' => 0,
                'risk_level_text' => 'green',
                'risk_level' => 4,
                'midyear_numerator_count' => 2,
                'beginning_year_numerator_count' => 0,
                'denominator_count' => 2
            ],
            [
                'years_from_retention_track' => 0,
                'risk_level_text' => 'yellow',
                'risk_level' => 3,
                'midyear_numerator_count' => 2,
                'beginning_year_numerator_count' => 0,
                'denominator_count' => 2
            ],
            [
                'years_from_retention_track' => 0,
                'risk_level_text' => 'red',
                'risk_level' => 2,
                'midyear_numerator_count' => 5,
                'beginning_year_numerator_count' => 0,
                'denominator_count' => 5
            ],
            [
                'years_from_retention_track' => 0,
                'risk_level_text' => 'red2',
                'risk_level' => 1,
                'midyear_numerator_count' => 2,
                'beginning_year_numerator_count' => 0,
                'denominator_count' => 2
            ],
            [
                'years_from_retention_track' => 1,
                'risk_level_text' => 'green',
                'risk_level' => 4,
                'midyear_numerator_count' => 2,
                'beginning_year_numerator_count' => 2,
                'denominator_count' => 2
            ],
            [
                'years_from_retention_track' => 1,
                'risk_level_text' => 'yellow',
                'risk_level' => 3,
                'midyear_numerator_count' => 2,
                'beginning_year_numerator_count' => 2,
                'denominator_count' => 2
            ],
            [
                'years_from_retention_track' => 1,
                'risk_level_text' => 'red',
                'risk_level' => 2,
                'midyear_numerator_count' => 5,
                'beginning_year_numerator_count' => 5,
                'denominator_count' => 5
            ],
            [
                'years_from_retention_track' => 1,
                'risk_level_text' => 'red2',
                'risk_level' => 1,
                'midyear_numerator_count' => 2,
                'beginning_year_numerator_count' => 2,
                'denominator_count' => 2
            ]
        ];
        return $aggregatedRetentionVariables;
    }

    private function getRetentionDataWithRisk(){

        $retentionDataWithRisk = [
            [
                [
                "years_from_retention_track" => 0,
                "risk_level_text" => "green",
                "risk_level" => 4,
                "midyear_numerator_count" => 2,
                "beginning_year_numerator_count" => 0,
                "denominator_count" => 2
                ],
                [
                    "years_from_retention_track" => 0,
                    "risk_level_text" => "yellow",
                    "risk_level" => 3,
                    "midyear_numerator_count" => 2,
                    "beginning_year_numerator_count" => 0,
                    "denominator_count" => 2
                ],
                [
                    "years_from_retention_track" => 0,
                    "risk_level_text" => "red",
                    "risk_level" => 2,
                    "midyear_numerator_count" => 5,
                    "beginning_year_numerator_count" => 0,
                    "denominator_count" => 5
                ],
                [
                    "years_from_retention_track" => 0,
                    "risk_level_text" => "red2",
                    "risk_level" => 1,
                    "midyear_numerator_count" => 2,
                    "beginning_year_numerator_count" => 0,
                    "denominator_count" => 2
                ]
            ],
            [
                [
                    "years_from_retention_track" => 1,
                    "risk_level_text" => "green",
                    "risk_level" => 4,
                    "midyear_numerator_count" => 2,
                    "beginning_year_numerator_count" => 2,
                    "denominator_count" => 2
                ],
                [
                    "years_from_retention_track" => 1,
                    "risk_level_text" => "yellow",
                    "risk_level" => 3,
                    "midyear_numerator_count" => 2,
                    "beginning_year_numerator_count" => 2,
                    "denominator_count" => 2
                ],
                [
                    "years_from_retention_track" => 1,
                    "risk_level_text" => "red",
                    "risk_level" => 2,
                    "midyear_numerator_count" => 5,
                    "beginning_year_numerator_count" => 5,
                    "denominator_count" => 5
                ],
                [
                    "years_from_retention_track" => 1,
                    "risk_level_text" => "red2",
                    "risk_level" => 1,
                    "midyear_numerator_count" => 2,
                    "beginning_year_numerator_count" => 2,
                    "denominator_count" => 2
                ]
            ]
        ];
        return $retentionDataWithRisk;
    }
}