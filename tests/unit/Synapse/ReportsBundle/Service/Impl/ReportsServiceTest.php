<?php

namespace Synapse\ReportsBundle\Service\Impl;

use Codeception\TestCase\Test;
use Synapse\AcademicBundle\Entity\OrgAcademicTerms;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CampusConnectionBundle\Service\Impl\CampusConnectionService;
use Synapse\CampusResourceBundle\Service\Impl\CampusResourceService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\DatablockQuestionsRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\OrganizationlangService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\CoreBundle\Service\Impl\OrgProfileService;
use Synapse\CoreBundle\Service\Impl\ProfileService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\UtilServiceHelper;
use Synapse\MapworksToolBundle\DAO\IssueDAO;
use Synapse\ReportsBundle\Entity\Reports;
use Synapse\ReportsBundle\Entity\ReportsRunningStatus;
use Synapse\ReportsBundle\EntityDto\AcademicUpdateReportDto;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\EntityDto\SurveyStatusReportDto;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\SearchBundle\EntityDto\SearchDto;
use Synapse\SearchBundle\EntityDto\SearchResultListDto;
use Synapse\SearchBundle\Repository\OrgSearchRepository;
use Synapse\SearchBundle\Service\Impl\SearchService;
use Synapse\SurveyBundle\Repository\SurveyQuestionsRepository;
use Synapse\SurveyBundle\Repository\WessLinkRepository;
use Synapse\SurveyBundle\Service\Impl\SurveyBlockService;

class ReportsServiceTest extends Test
{
    use \Codeception\Specify;
    /**
     *
     * @var \personId
     */
    private $personId = 1;
    private $organization = 1;
    private $organizationId = 62;
    private $loggedInUserId = 5048809;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRepositoryResolver;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogger;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContainer;

    protected function _before()
    {
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
        $this->mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
        $this->mockContainer = $this->getMock('Container', ['get']);
    }

    public function testGetUserReports()
    {
        $this->specify('test GetUserReports method', function ($filter, $expectedResult) {
            $mockPersonRepository = $this->getMock("PersonRepository", ["find"]);
            $personObject = new Person();
            $personObject->setId($this->loggedInUserId);
            $personObject->setOrganization(new Organization());
            $mockPersonRepository->method("find")->willReturn($personObject);
            $mockReportsRepository = $this->getMock("ReportsRepository", ["findOneBy", "getAllNonCoordinatorsReports", "getAllCoordinatorReports", "getReportsTeamLeader", "getSpecificReports"]);
            $mockReportsRepository->method("getAllNonCoordinatorsReports")->willReturn($this->getReportsData("all"));
            $mockReportsRepository->method("getAllCoordinatorReports")->willReturn($this->getReportsData("coordinator"));
            $mockReportsRepository->method("getReportsTeamLeader")->willReturn($this->getReportsData("teamleader"));
            $mockReportsRepository->method("getSpecificReports")->willReturn($this->getReportsData("faculty"));


            $mockReportsRunningStatusRepository = $this->getMock("ReportsRunningStatusRepository", ["getLastRunDateForMyReport"]);
            $mockReportsRunningStatusRepository->method("getLastRunDateForMyReport")->willReturn(["modified_at" => "08/23/2017"]);
            $mockTeamMemberRepository = $this->getMock("TeamMemberRepository", ["findOneBy"]);
            $reportObject = new Reports();
            $mockReportsRepository->method("findOneBy")->willReturn($reportObject);
            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
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
                ],
                [
                    TeamMembersRepository::REPOSITORY_KEY,
                    $mockTeamMemberRepository
                ]
            ]);
            $mockOrgPermissionSetService = $this->getMock("OrgPermissionSetService", ["getAllowedReports", "getCoursesAccess"]);
            $mockOrgPermissionSetService->method("getCoursesAccess")->willReturn(["view_all_academic_update_courses" => true]);
            $this->mockContainer->method('get')->willReturnMap([
                [
                    OrgPermissionsetService::SERVICE_KEY,
                    $mockOrgPermissionSetService
                ],
            ]);
            $reportsService = new ReportsService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $results = $reportsService->getUserReports($this->loggedInUserId, $filter, null);
            $this->assertEquals($results, $expectedResult);
        }, [
            "examples" => [
                // example validates reports returned through service method with expected result case "All" reports
                [
                    'all',
                    [
                        'total_count' => 8,
                        'reports' =>
                            [
                                'Activity' =>
                                    [
                                        [
                                            'reportId' => '3',
                                            'reportName' => 'All Academic Updates Report',
                                            'reportDescription' => 'See all academic updates for your students.  Export to csv, perform individual or bulk actions',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'AU-R',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                    ],
                                'Outcomes' =>
                                    [
                                        [
                                            'reportId' => '18',
                                            'reportName' => 'Compare',
                                            'reportDescription' => 'Compare outcomes of two student subpopulations. Choose by profile item or survey question, compare survey factors and GPA.',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'SUB-COM',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                    ],
                                'Survey and Profile' =>
                                    [
                                        [
                                            'reportId' => '6',
                                            'reportName' => 'Group Response Report',
                                            'reportDescription' => 'Compare survey response rates for different groups.  Export to csv',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => NULL,
                                            'shortCode' => 'SUR-GRR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '2',
                                            'reportName' => 'Individual Response Report',
                                            'reportDescription' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'SUR-IRR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '9',
                                            'reportName' => 'Our Students Report',
                                            'reportDescription' => 'See Top Five issues, high-level survey data and demographics for a single survey and cohort.. Export to pdf',
                                            'isBatchJob' => false,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'OSR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '14',
                                            'reportName' => 'Profile Snapshot Report',
                                            'reportDescription' => 'See aggregated profile data for a given student population and academic year. Drill down to see individual students and export to csv.',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'PRO-SR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '10',
                                            'reportName' => 'Survey Factors Report',
                                            'reportDescription' => 'See aggregated values of all survey factors for a given survey and cohort.  Drill down to see individual students',
                                            'isBatchJob' => false,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'SUR-FR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '8',
                                            'reportName' => 'Survey Snapshot Report',
                                            'reportDescription' => 'See aggregated responses to all survey questions for a given survey and cohort.  Drill down to see individual students',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'SUR-SR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                    ],
                            ],
                    ]
                ],
                // example validates reports returned through service method with expected result case "coordinator" reports
                [
                    'coordinator',
                    [
                        'total_count' => 13,
                        'reports' =>
                            [
                                'Outcomes' =>
                                    [
                                        [
                                            'reportId' => '13',
                                            'reportName' => 'Completion Report',
                                            'reportDescription' => 'View completion rates of one to six years by retention tracking group and by risk.  Export to csv, print to pdf.',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'y',
                                            'shortCode' => 'CR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '15',
                                            'reportName' => 'GPA Report',
                                            'reportDescription' => 'View average GPA over time, overall and by risk.  View percent of students with GPA < 2.0.  Export to csv, print to pdf.',
                                            'isBatchJob' => false,
                                            'isCoordinatorReport' => 'y',
                                            'shortCode' => 'GPA',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '12',
                                            'reportName' => 'Persistence and Retention Report',
                                            'reportDescription' => 'View persistence and retention by retention tracking group and by risk.  Export to csv, print to pdf.',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'y',
                                            'shortCode' => 'PRR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                    ],
                                'Activity' =>
                                    [
                                        [
                                            'reportId' => '16',
                                            'reportName' => 'Executive Summary Report',
                                            'reportDescription' => 'See key statistics on effectiveness: persistence/retention, GPA, activity, and more. Print to pdf.',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'y',
                                            'shortCode' => 'EXEC',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '11',
                                            'reportName' => 'Faculty/Staff Usage Report',
                                            'reportDescription' => 'Identify faculty/staff members and their activity. Export to csv.',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'y',
                                            'shortCode' => 'FUR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '7',
                                            'reportName' => 'Our Mapworks Activity',
                                            'reportDescription' => 'View statistics on faculty and student activity tracked in Mapworks for a given date range.  Export to pdf',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'y',
                                            'shortCode' => 'MAR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '3',
                                            'reportName' => 'All Academic Updates Report',
                                            'reportDescription' => 'See all academic updates for your students.  Export to csv, perform individual or bulk actions',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'AU-R',
                                            'lastRunDate' => '08/23/2017',
                                        ]
                                    ],
                                'Survey and Profile' =>
                                    [
                                        [
                                            'reportId' => '6',
                                            'reportName' => 'Group Response Report',
                                            'reportDescription' => 'Compare survey response rates for different groups.  Export to csv',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => NULL,
                                            'shortCode' => 'SUR-GRR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '2',
                                            'reportName' => 'Individual Response Report',
                                            'reportDescription' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'SUR-IRR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '9',
                                            'reportName' => 'Our Students Report',
                                            'reportDescription' => 'See Top Five issues, high-level survey data and demographics for a single survey and cohort.. Export to pdf',
                                            'isBatchJob' => false,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'OSR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '14',
                                            'reportName' => 'Profile Snapshot Report',
                                            'reportDescription' => 'See aggregated profile data for a given student population and academic year. Drill down to see individual students and export to csv.',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'PRO-SR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '10',
                                            'reportName' => 'Survey Factors Report',
                                            'reportDescription' => 'See aggregated values of all survey factors for a given survey and cohort.  Drill down to see individual students',
                                            'isBatchJob' => false,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'SUR-FR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '8',
                                            'reportName' => 'Survey Snapshot Report',
                                            'reportDescription' => 'See aggregated responses to all survey questions for a given survey and cohort.  Drill down to see individual students',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'SUR-SR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                    ],
                            ],
                    ]
                ],
                // example validates reports returned through service method with expected result case "teamleader" reports
                [
                    'teamleader',
                    [
                        'total_count' => 8,
                        'reports' =>
                            [
                                'Activity' =>
                                    [
                                        [
                                            'reportId' => '3',
                                            'reportName' => 'All Academic Updates Report',
                                            'reportDescription' => 'See all academic updates for your students.  Export to csv, perform individual or bulk actions',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'AU-R',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '11',
                                            'reportName' => 'Faculty/Staff Usage Report',
                                            'reportDescription' => 'Identify faculty/staff members and their activity. Export to csv.',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'y',
                                            'shortCode' => 'FUR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                    ],
                                'Survey and Profile' =>
                                    [
                                        [
                                            'reportId' => '6',
                                            'reportName' => 'Group Response Report',
                                            'reportDescription' => 'Compare survey response rates for different groups.  Export to csv',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => NULL,
                                            'shortCode' => 'SUR-GRR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '2',
                                            'reportName' => 'Individual Response Report',
                                            'reportDescription' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'SUR-IRR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '9',
                                            'reportName' => 'Our Students Report',
                                            'reportDescription' => 'See Top Five issues, high-level survey data and demographics for a single survey and cohort.. Export to pdf',
                                            'isBatchJob' => false,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'OSR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '14',
                                            'reportName' => 'Profile Snapshot Report',
                                            'reportDescription' => 'See aggregated profile data for a given student population and academic year. Drill down to see individual students and export to csv.',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'PRO-SR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '10',
                                            'reportName' => 'Survey Factors Report',
                                            'reportDescription' => 'See aggregated values of all survey factors for a given survey and cohort.  Drill down to see individual students',
                                            'isBatchJob' => false,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'SUR-FR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '8',
                                            'reportName' => 'Survey Snapshot Report',
                                            'reportDescription' => 'See aggregated responses to all survey questions for a given survey and cohort.  Drill down to see individual students',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'SUR-SR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                    ],
                            ],
                    ]
                ],
                // example validates reports returned through service method with expected result case "faculty" but not a team leader , not displaying faculty usage report
                [
                    'faculty',
                    [
                        'total_count' => 7,
                        'reports' =>
                            [
                                'Activity' =>
                                    [
                                        [
                                            'reportId' => '3',
                                            'reportName' => 'All Academic Updates Report',
                                            'reportDescription' => 'See all academic updates for your students.  Export to csv, perform individual or bulk actions',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'AU-R',
                                            'lastRunDate' => '08/23/2017',
                                        ]
                                    ],
                                'Survey and Profile' =>
                                    [
                                        [
                                            'reportId' => '6',
                                            'reportName' => 'Group Response Report',
                                            'reportDescription' => 'Compare survey response rates for different groups.  Export to csv',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => NULL,
                                            'shortCode' => 'SUR-GRR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '2',
                                            'reportName' => 'Individual Response Report',
                                            'reportDescription' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'SUR-IRR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '9',
                                            'reportName' => 'Our Students Report',
                                            'reportDescription' => 'See Top Five issues, high-level survey data and demographics for a single survey and cohort.. Export to pdf',
                                            'isBatchJob' => false,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'OSR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '14',
                                            'reportName' => 'Profile Snapshot Report',
                                            'reportDescription' => 'See aggregated profile data for a given student population and academic year. Drill down to see individual students and export to csv.',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'PRO-SR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '10',
                                            'reportName' => 'Survey Factors Report',
                                            'reportDescription' => 'See aggregated values of all survey factors for a given survey and cohort.  Drill down to see individual students',
                                            'isBatchJob' => false,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'SUR-FR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                        [
                                            'reportId' => '8',
                                            'reportName' => 'Survey Snapshot Report',
                                            'reportDescription' => 'See aggregated responses to all survey questions for a given survey and cohort.  Drill down to see individual students',
                                            'isBatchJob' => true,
                                            'isCoordinatorReport' => 'n',
                                            'shortCode' => 'SUR-SR',
                                            'lastRunDate' => '08/23/2017',
                                        ],
                                    ],
                            ],
                    ]
                ]
            ]
        ]);
    }

    private function getReportsData($type)
    {
        $reportsData = [];
        switch ($type) {
            case "all":
                $reportsData = [
                    [
                        'view_name' => 'Activity',
                        'id' => '3',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'All Academic Updates Report',
                        'description' => 'See all academic updates for your students.  Export to csv, perform individual or bulk actions',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'AU-R',
                        'is_active' => 'y',
                        'report_view_id' => '1',
                    ],
                    [
                        'view_name' => 'Outcomes',
                        'id' => '18',
                        'created_by' => '-25',
                        'modified_by' => '-25',
                        'deleted_by' => NULL,
                        'created_at' => '2017-08-03 19:11:08',
                        'modified_at' => '2017-08-03 19:11:08',
                        'deleted_at' => NULL,
                        'name' => 'Compare',
                        'description' => 'Compare outcomes of two student subpopulations. Choose by profile item or survey question, compare survey factors and GPA.',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'SUB-COM',
                        'is_active' => 'y',
                        'report_view_id' => '2',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '6',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Group Response Report',
                        'description' => 'Compare survey response rates for different groups.  Export to csv',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => NULL,
                        'short_code' => 'SUR-GRR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '2',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Individual Response Report',
                        'description' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'SUR-IRR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '9',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Our Students Report',
                        'description' => 'See Top Five issues, high-level survey data and demographics for a single survey and cohort.. Export to pdf',
                        'is_batch_job' => 'n',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'OSR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '14',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Profile Snapshot Report',
                        'description' => 'See aggregated profile data for a given student population and academic year. Drill down to see individual students and export to csv.',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'PRO-SR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '10',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Survey Factors Report',
                        'description' => 'See aggregated values of all survey factors for a given survey and cohort.  Drill down to see individual students',
                        'is_batch_job' => 'n',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'SUR-FR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '8',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Survey Snapshot Report',
                        'description' => 'See aggregated responses to all survey questions for a given survey and cohort.  Drill down to see individual students',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'SUR-SR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ]
                ];
                break;
            case "coordinator":
                $reportsData = [  // expected results
                    [
                        'view_name' => 'Outcomes',
                        'id' => '13',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Completion Report',
                        'description' => 'View completion rates of one to six years by retention tracking group and by risk.  Export to csv, print to pdf.',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'y',
                        'short_code' => 'CR',
                        'is_active' => 'y',
                        'report_view_id' => '2',
                    ],
                    [
                        'view_name' => 'Activity',
                        'id' => '16',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Executive Summary Report',
                        'description' => 'See key statistics on effectiveness: persistence/retention, GPA, activity, and more. Print to pdf.',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'y',
                        'short_code' => 'EXEC',
                        'is_active' => 'y',
                        'report_view_id' => '1',
                    ],
                    [
                        'view_name' => 'Activity',
                        'id' => '11',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Faculty/Staff Usage Report',
                        'description' => 'Identify faculty/staff members and their activity. Export to csv.',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'y',
                        'short_code' => 'FUR',
                        'is_active' => 'y',
                        'report_view_id' => '1',
                    ],
                    [
                        'view_name' => 'Outcomes',
                        'id' => '15',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'GPA Report',
                        'description' => 'View average GPA over time, overall and by risk.  View percent of students with GPA < 2.0.  Export to csv, print to pdf.',
                        'is_batch_job' => 'n',
                        'is_coordinator_report' => 'y',
                        'short_code' => 'GPA',
                        'is_active' => 'y',
                        'report_view_id' => '2',
                    ],
                    [
                        'view_name' => 'Activity',
                        'id' => '7',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Our Mapworks Activity',
                        'description' => 'View statistics on faculty and student activity tracked in Mapworks for a given date range.  Export to pdf',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'y',
                        'short_code' => 'MAR',
                        'is_active' => 'y',
                        'report_view_id' => '1',
                    ],
                    [
                        'view_name' => 'Outcomes',
                        'id' => '12',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Persistence and Retention Report',
                        'description' => 'View persistence and retention by retention tracking group and by risk.  Export to csv, print to pdf.',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'y',
                        'short_code' => 'PRR',
                        'is_active' => 'y',
                        'report_view_id' => '2',
                    ],
                ];
                break;
            case "teamleader":
                $reportsData = [
                    [
                        'view_name' => 'Activity',
                        'id' => '3',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'All Academic Updates Report',
                        'description' => 'See all academic updates for your students.  Export to csv, perform individual or bulk actions',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'AU-R',
                        'is_active' => 'y',
                        'report_view_id' => '1',
                    ],
                    [
                        'view_name' => 'Activity',
                        'id' => '11',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Faculty/Staff Usage Report',
                        'description' => 'Identify faculty/staff members and their activity. Export to csv.',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'y',
                        'short_code' => 'FUR',
                        'is_active' => 'y',
                        'report_view_id' => '1',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '6',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Group Response Report',
                        'description' => 'Compare survey response rates for different groups.  Export to csv',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => NULL,
                        'short_code' => 'SUR-GRR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '2',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Individual Response Report',
                        'description' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'SUR-IRR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '9',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Our Students Report',
                        'description' => 'See Top Five issues, high-level survey data and demographics for a single survey and cohort.. Export to pdf',
                        'is_batch_job' => 'n',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'OSR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '14',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Profile Snapshot Report',
                        'description' => 'See aggregated profile data for a given student population and academic year. Drill down to see individual students and export to csv.',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'PRO-SR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '10',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Survey Factors Report',
                        'description' => 'See aggregated values of all survey factors for a given survey and cohort.  Drill down to see individual students',
                        'is_batch_job' => 'n',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'SUR-FR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '8',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Survey Snapshot Report',
                        'description' => 'See aggregated responses to all survey questions for a given survey and cohort.  Drill down to see individual students',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'SUR-SR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ]
                ];
                break;
            case "faculty":
                $reportsData = [
                    [
                        'view_name' => 'Activity',
                        'id' => '3',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'All Academic Updates Report',
                        'description' => 'See all academic updates for your students.  Export to csv, perform individual or bulk actions',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'AU-R',
                        'is_active' => 'y',
                        'report_view_id' => '1',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '6',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Group Response Report',
                        'description' => 'Compare survey response rates for different groups.  Export to csv',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => NULL,
                        'short_code' => 'SUR-GRR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '2',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Individual Response Report',
                        'description' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'SUR-IRR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '9',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Our Students Report',
                        'description' => 'See Top Five issues, high-level survey data and demographics for a single survey and cohort.. Export to pdf',
                        'is_batch_job' => 'n',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'OSR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '14',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Profile Snapshot Report',
                        'description' => 'See aggregated profile data for a given student population and academic year. Drill down to see individual students and export to csv.',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'PRO-SR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '10',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Survey Factors Report',
                        'description' => 'See aggregated values of all survey factors for a given survey and cohort.  Drill down to see individual students',
                        'is_batch_job' => 'n',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'SUR-FR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ],
                    [
                        'view_name' => 'Survey and Profile',
                        'id' => '8',
                        'created_by' => NULL,
                        'modified_by' => NULL,
                        'deleted_by' => NULL,
                        'created_at' => NULL,
                        'modified_at' => NULL,
                        'deleted_at' => NULL,
                        'name' => 'Survey Snapshot Report',
                        'description' => 'See aggregated responses to all survey questions for a given survey and cohort.  Drill down to see individual students',
                        'is_batch_job' => 'y',
                        'is_coordinator_report' => 'n',
                        'short_code' => 'SUR-SR',
                        'is_active' => 'y',
                        'report_view_id' => '3',
                    ]
                ];
                break;
        }
        return $reportsData;
    }

    private function getReportRunningStatus($reportExists)
    {
        if ($reportExists) {
            $reportsRunningStatusObject = new ReportsRunningStatus();
        } else {
            throw new SynapseValidationException("Report does not exist");
        }
        return $reportsRunningStatusObject;
    }

    private function getReportRunningStatusDto($reportId, $personId)
    {
        $reportRunningStatusDto = new ReportRunningStatusDto();
        $reportRunningStatusDto->setId($reportId);
        $reportRunningStatusDto->setPersonId($personId);
        return $reportRunningStatusDto;
    }

    public function testValidateReportRunningStatusBelongsToPerson()
    {
        $this->specify('test report running status belong to person', function ($personId, $expectedResult) {
            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'addWarning'));
            $mockContainer = $this->getMock('Container', array('get'));
            // Inititializing Repository to be mocked
            $mockReportsRunningStatusRepository = $this->getMock('ReportsRunningStatusRepository', array(
                'find'
            ));
            /**
             * Mock ReportsRunningStatus Object
             */
            $mockReportInstance = $this->getMock('ReportsRunningStatus', array('getPerson'));
            $mockReportsRunningStatusRepository->expects($this->any())
                ->method('find')
                ->will($this->returnValue($mockReportInstance));
            $mockPersonObj = $this->getMock('Person', array(
                'getId'
            ));
            $mockReportInstance->expects($this->any())
                ->method('getPerson')
                ->will($this->returnValue($mockPersonObj));
            $mockPersonObj->expects($this->any())
                ->method('getId')
                ->will($this->returnValue($this->personId));
            $reportService = new ReportsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $response = $reportService->validateReportRunningStatusBelongsToPerson($personId, $mockReportInstance);
            } catch (AccessDeniedException $e) {
                $response = $e->getMessage();
            }
            $this->assertEquals($expectedResult, $response);
        }, ['examples' => [
            [ // example1 with valid personid
                1,
                1
            ],
            [ // example2 with invalid person id
                2,
                // Expected Exception message.
                'You are trying to access a report you did not generate.'
            ]
        ]]);
    }

    public function testGenerateReportCSV()
    {
        $this->specify('test genrate report csv', function ($personId, $reportInstanceId, $responseJson, $tCase, $csvFileName, $expectedResult) {
            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'addWarning'));
            $mockContainer = $this->getMock('Container', array('get'));
            // Inititializing Repository to be mocked
            $mockReportsRunningStatusRepository = $this->getMock('ReportsRunningStatusRepository', array(
                'find'
            ));
            $mockRepositoryResolver->method('getRepository')->willReturnMap([["SynapseReportsBundle:ReportsRunningStatus", $mockReportsRunningStatusRepository]]);
            /**
             * Mock ReportsRunningStatus Object
             */
            $mockReportInstance = $this->getMock('ReportsRunningStatus', array('getResponseJson', 'getPerson'));
            $mockReportsRunningStatusRepository->expects($this->any())
                ->method('find')
                ->will($this->returnValue($mockReportInstance));
            $mockReportInstance->expects($this->any())->method('getResponseJson')
                ->willReturn($responseJson);
            $mockPersonObj = $this->getMock('Person', array(
                'getId'
            ));
            $mockReportInstance->expects($this->any())
                ->method('getPerson')
                ->will($this->returnValue($mockPersonObj));
            $mockPersonObj->expects($this->any())
                ->method('getId')
                ->will($this->returnValue($this->personId));
            $mockComparisionReportService = $this->getMock('ComparisonReportService', array('generateCompareReportCSV'));
            $mockDateUtilityService = $this->getMock('DateUtilityService', array('getFormattedDateTimeForOrganization'));
            $mockCSVUtilityService = $this->getMock('CSVutilityService', array('generateCSV'));
            $mockComparisionReportService->expects($this->any())
                ->method('generateCompareReportCSV')->willReturn($csvFileName);
            $mockContainer->method('get')
                ->willReturnMap(
                    [
                        [ComparisonReportService::SERVICE_KEY, $mockComparisionReportService],
                        [CSVUtilityService::SERVICE_KEY,$mockCSVUtilityService],
                        [DateUtilityService::SERVICE_KEY, $mockDateUtilityService]
                    ]
                );
            $mockDateUtilityService->method('getFormattedDateTimeForOrganization')->willReturn('20170704_164200');
            $reportService = new ReportsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $functionResults = $reportService->generateReportCSV($personId, $reportInstanceId, $tCase);
            $this->assertEquals($expectedResult, $functionResults);
        }, ['examples' => [
            [ // Example1 Categroy type profile block
                1,
                27316,
                $this->getCompareResponseCategoryTypeJson(),
                false,
                "20-compare_report_20170704.csv",
                "20-compare_report_20170704.csv"
            ],
            [// Example 2 date type profile block
                1,
                27316,
                $this->getCompareResponseDateTypeJson(),
                false,
                "80-compare_report_20170704.csv",
                "80-compare_report_20170704.csv"
            ],
            [// Example 3 Invalid Report type
                1,
                27316,
                $this->getInvalidReportTypeJson(),
                false,
                "",
                ""
            ],
            [// Example 4 ISQ with category type question
                1,
                27316,
                $this->getCategoryTypeIsqSurveyJson(),
                false,
                "20-compare_report_20170704.csv",
                "20-compare_report_20170704.csv"
            ],
            [// Example 4 Faculty Staff Usage Report
                1,
                27316,
                $this->getFacultyStaffJson(),
                true,
                '20-27478-Faculty-Staff_Usage_Report.csv',
                '20-27478-Faculty-Staff_Usage_Report.csv'
            ],
            [// Example 5 GPA Usage Report
                1,
                27316,
                $this->getSampleJson1(),
                true,
                '2-4801-GPA-report.csv',
                '2-4801-GPA-report.csv'
            ],
            [// Example 6 GPA Usage Report
                1,
                27316,
                $this->getSampleJson2(),
                true,
                '2-4802-GPA-report.csv',
                '2-4802-GPA-report.csv'
            ]
        ]]);
    }

    private function getFacultyStaffJson()
    {
        $json = '{
  "id": 27478,
  "organization_id": 20,
  "report_id": "11",
  "report_sections": {
    "reportId": "11",
    "reportDisable": false,
    "report_name": "Faculty/Staff Usage Report",
    "short_code": "FUR",
    "canGenarateWithOutFilter": true,
    "hideStudentsCound": true,
    "reportDesc": "Provide reporting of actionable data for Skyfactor admins, campus coordinators and faculty/staff.",
    "reportFilterPages": [
      {
        "reportPage": "filterAttributes",
        "title": "Filter options"
      }
    ],
    "reportFilter": {
      "participating": false,
      "risk": false,
      "active": false,
      "retentionCompletion": false,
      "activities": false,
      "academicYears": true,
      "group": true,
      "course": false,
      "ebi": false,
      "isp": false,
      "static": false,
      "factor": false,
      "survey": false,
      "isq": false,
      "surveyMetadata": false,
      "academicTerm": false,
      "cohort": false,
      "team": true
    },
    "RequestParam": "",
    "pageurl": "/reports/faculty-usage-report/",
    "id": "11",
    "report_description": "Identify faculty/staff members and their activity. Export to csv.",
    "is_batch_job": true,
    "is_coordinator_report": "y"
  },
  "search_attributes": {
    "academic_year": {
      "start_date": "2016-08-16",
      "end_date": "2017-07-11"
    },
    "group_ids": "",
    "team_ids": "",
    "participating": {
      "participating_value": [
        1
      ],
      "org_academic_year_id": [
        "190"
      ]
    }
  },
  "campus_info": {
    "campus_id": 20,
    "campus_name": "SynapseBetaOrg0020",
    "campus_logo": "images/default-mw-header-logo.png"
  },
  "request_json": {
    "id": 27478,
    "report_id": 11,
    "organization_id": 20,
    "person_id": 5056928,
    "search_attributes": [],
    "report_sections": {
      "reportId": "11",
      "reportDisable": false,
      "report_name": "Faculty/Staff Usage Report",
      "short_code": "FUR",
      "canGenarateWithOutFilter": true,
      "hideStudentsCound": true,
      "reportDesc": "Provide reporting of actionable data for Skyfactor admins, campus coordinators and faculty/staff.",
      "reportFilterPages": [
        {
          "reportPage": "filterAttributes",
          "title": "Filter options"
        }
      ],
      "reportFilter": {
        "participating": false,
        "risk": false,
        "active": false,
        "retentionCompletion": false,
        "activities": false,
        "academicYears": true,
        "group": true,
        "course": false,
        "ebi": false,
        "isp": false,
        "static": false,
        "factor": false,
        "survey": false,
        "isq": false,
        "surveyMetadata": false,
        "academicTerm": false,
        "cohort": false,
        "team": true
      },
      "RequestParam": "",
      "pageurl": "/reports/faculty-usage-report/",
      "id": "11",
      "report_description": "Identify faculty/staff members and their activity. Export to csv.",
      "is_batch_job": true,
      "is_coordinator_report": "y"
    }
  },
  "report_info": {
    "report_id": "11",
    "report_name": "Faculty/Staff Usage Report",
    "short_code": "FUR",
    "report_instance_id": 27478,
    "report_start_date": "2016-08-16",
    "report_end_date": "2017-07-11",
    "report_by": {
      "first_name": "retention1",
      "last_name": "faculty"
    }
  },
  "report_data": [
    {
      "person_id": 51706,
      "lastname": "Yoder",
      "firstname": "Thaddeus",
      "external_id": 51706,
      "username": "MapworksTestingUser00051706@mailinator.com",
      "student_connected": 48,
      "contacts_student_count": 0,
      "contacted_student_percentage": 0,
      "interaction_contact_student_count": 0,
      "interaction_contact_student_percentage": 0,
      "reports_viewed_student_count": 0,
      "reports_viewed_student_percentage": 0,
      "notes_count": 0,
      "referrals_count": 1,
      "last_login": "2017-01-30T16:02:13+0000",
      "days_login": 5
    },
    {
      "person_id": 220640,
      "lastname": "Wiggins",
      "firstname": "Lizbeth",
      "external_id": 220640,
      "username": "MapworksTestingUser00220640@mailinator.com",
      "student_connected": 0,
      "contacts_student_count": 0,
      "contacted_student_percentage": 0,
      "interaction_contact_student_count": 0,
      "interaction_contact_student_percentage": 0,
      "reports_viewed_student_count": 0,
      "reports_viewed_student_percentage": 0,
      "notes_count": 0,
      "referrals_count": 0,
      "last_login": "2017-01-30T16:02:13+0000",
      "days_login": 0
    }
  ]
}';
        return $json;
    }

    private function getCategoryTypeIsqSurveyJson()
    {
        $json = '{
  "request_json": {
    "id": 27437,
    "report_id": 18,
    "organization_id": 20,
    "person_id": 5056928,
    "search_attributes": {
      "org_academic_year_id": [
        190
      ],
      "org_academic_year": {
        "year": {
          "id": 190,
          "name": "Newname2016-2017",
          "year_id": 201617
        }
      },
      "gpa_org_academic_year": {
        "year": {
          "id": 190,
          "name": "Newname2016-2017",
          "year_id": 201617
        }
      },
      "latest_survey": {
        "name": "Transition One",
        "id": 15
      },
      "latest_cohort": {
        "name": "Survey Cohort 1",
        "id": 1
      },
      "group_names": [
        "Completely disagree",
        "Disagree somewhat"
      ],
      "datablocks": [],
      "isps": [],
      "isqs": {
        "survey_id": "11",
        "year_id": "201516",
        "question_id": 2397,
        "type": "category",
        "cohort": "2",
        "subpopulation1": {
          "category_type": [
            {
              "id": 33565,
              "answer": "Completely disagree",
              "value": "1",
              "subpopulationOneselected": true
            }
          ]
        },
        "subpopulation2": {
          "category_type": [
            {
              "id": 33566,
              "answer": "Disagree somewhat",
              "value": "2",
              "subpopulationTwoselected": true
            }
          ]
        }
      },
      "survey": [],
      "filterText": [
        "isq",
        {
          "text": "Organization: 020 Question ID: 02397",
          "yearTerm": ""
        },
        [
          "Completely disagree",
          "Disagree somewhat"
        ]
      ]
    },
    "report_sections": {
      "reportId": "18",
      "reportDisable": false,
      "report_name": "Compare",
      "short_code": "SUB-COM",
      "reportDesc": "Compare outcomes of two student subpopulations. Choose subpopulations by profile item, ISP, survey question or ISQ. Compare survey factors and GPA. Export to csv, print to pdf.",
      "reportFilterPages": [
        {
          "reportPage": "itemType",
          "title": "Select Subpopulation Attribute",
          "breadCrumb": "Select Type",
          "visited": false,
          "showHelp": true
        },
        {
          "reportPage": "treeSelector",
          "title": "Select Profile Item",
          "breadCrumbs": [
            {
              "type": "isp",
              "text": "Profile Item"
            },
            {
              "type": "isq",
              "text": "Survey Cohort"
            }
          ],
          "subPages": [
            {
              "prefix": "a",
              "reportPage": "treeSelector",
              "title": "Select one Cohort from one Survey",
              "breadCrumb": "Survey Cohort"
            },
            {
              "prefix": "b",
              "reportPage": "surveyQuestion",
              "title": "Select one survey question",
              "breadCrumb": "Survey Question",
              "showSearch": true
            }
          ],
          "visited": false,
          "showSearch": true
        },
        {
          "reportPage": "subPopulation",
          "title": "",
          "breadCrumb": "Values",
          "visited": false
        }
      ],
      "reportFilter": {
        "participating": false,
        "risk": true,
        "active": true,
        "activities": false,
        "group": true,
        "course": true,
        "ebi": true,
        "isp": true,
        "static": true,
        "factor": true,
        "survey": true,
        "isq": true,
        "surveyMetadata": false,
        "academicTerm": false,
        "cohort": false,
        "team": false
      },
      "RequestParam": "",
      "pageurl": "/reports/compare/",
      "templateUrl": "/partials/reports/outcomesComparisonReport/modals/report-parameters-modal.html",
      "controller": "OutcomeComparisonController",
      "id": "18",
      "report_description": "Compare outcomes of two student subpopulations. Choose by profile item or survey question, compare survey factors and GPA.",
      "is_batch_job": true,
      "is_coordinator_report": "n"
    }
  },
  "report_info": {
    "report_id": 18,
    "report_name": "Compare",
    "short_code": "SUB-COM",
    "report_instance_id": 27437,
    "report_date": "2017-07-10T13:27:43+0000",
    "report_by": {
      "first_name": "retention1",
      "last_name": "faculty"
    }
  },
  "status_message": {
    "code": "R1002",
    "description": "There are currently no students who have the required data for this report. Please add the needed information or change the filter criteria, then attempt to run the report again."
  },
  "report_items": {
    "organizationId": 20,
    "factor": [],
    "gpa": []
  }
}';
        return $json;
    }

    private function getCompareResponseDateTypeJson()
    {
        $json = '{
  "request_json": {
    "id": 27316,
    "report_id": 18,
    "organization_id": 80,
    "person_id": 1,
    "search_attributes": {
      "org_academic_year_id": [
        190
      ],
      "org_academic_year": {
        "year": {
          "id": 190,
          "name": "Newname2016-2017",
          "year_id": 201617
        }
      },
      "gpa_org_academic_year": {
        "year": {
          "id": 190,
          "name": "Newname2016-2017",
          "year_id": 201617
        }
      },
      "latest_survey": {
        "name": "Transition One",
        "id": 15
      },
      "latest_cohort": {
        "name": "Survey Cohort 1",
        "id": 1
      },
      "group_names": [
        "One",
        "Two"
      ],
      "datablocks": {
            "profile_block_id": 21,
            "profile_items": {
              "id": 48,
              "display_name": "ApplicationDate",
              "item_data_type": "D",
              "item_meta_key": "Gender",
              "calendar_assignment": "N",
              "year_term": false,
              "subpopulation1": {
                "start_date": "06/20/2017",
                "end_date": "06/22/2017"
              },
              "subpopulation2": {
                "start_date": "06/06/2017",
                "end_date": "06/14/2017"
              }
            },
            "profile_block_name": "Admissions-Dates"
          },
      "isps": [],
      "isqs": [],
      "survey": [],
      "filterText": [
        "profileItem",
        {
          "text": "Gender",
          "yearTerm": ""
        },
        [
          "One",
          "Two"
        ]
      ]
    },
    "report_sections": {
      "reportId": "18",
      "reportDisable": false,
      "report_name": "Compare",
      "short_code": "SUB-COM",
      "reportDesc": "Compare outcomes of two student subpopulations. Choose subpopulations by profile item, ISP, survey question or ISQ. Compare survey factors and GPA. Export to csv, print to pdf.",
      "reportFilterPages": [
        {
          "reportPage": "itemType",
          "title": "Select Subpopulation Attribute",
          "breadCrumb": "Select Type",
          "visited": false,
          "showHelp": true
        },
        {
          "reportPage": "treeSelector",
          "title": "Select Profile Item",
          "breadCrumbs": [
            {
              "type": "isp",
              "text": "Profile Item"
            },
            {
              "type": "isq",
              "text": "Survey Cohort"
            }
          ],
          "subPages": [
            {
              "prefix": "a",
              "reportPage": "treeSelector",
              "title": "Select one Cohort from one Survey",
              "breadCrumb": "Survey Cohort"
            },
            {
              "prefix": "b",
              "reportPage": "surveyQuestion",
              "title": "Select one survey question",
              "breadCrumb": "Survey Question",
              "showSearch": true
            }
          ],
          "visited": false,
          "showSearch": true
        },
        {
          "reportPage": "subPopulation",
          "title": "",
          "breadCrumb": "Values",
          "visited": false
        }
      ],
      "reportFilter": {
        "participating": false,
        "risk": true,
        "active": true,
        "activities": false,
        "group": true,
        "course": true,
        "ebi": true,
        "isp": true,
        "static": true,
        "factor": true,
        "survey": true,
        "isq": true,
        "surveyMetadata": false,
        "academicTerm": false,
        "cohort": false,
        "team": false
      },
      "RequestParam": "",
      "pageurl": "/reports/compare/",
      "templateUrl": "/partials/reports/outcomesComparisonReport/modals/report-parameters-modal.html",
      "controller": "OutcomeComparisonController",
      "id": "18",
      "report_description": "Compare outcomes of two student subpopulations. Choose by profile item or survey question, compare survey factors and GPA.",
      "is_batch_job": true,
      "is_coordinator_report": "n"
    }
  },
  "report_info": {
    "reports_id": 18,
    "report_name": "Compare",
    "short_code": "SUB-COM",
    "report_instance_id": 27316,
    "report_date": "2017-07-03T07:04:34+0000",
    "report_by": {
      "first_name": "retention1",
      "last_name": "faculty"
    }
  },
  "report_items": {
    "organizationId": 20,
    "factor": [],
    "gpa": []
  }
}';
        return $json;
    }

    private function getInvalidReportTypeJson()
    {
        $json = '{
  "request_json": {
    "id": 27316,
    "report_id": 18,
    "organization_id": 80,
    "person_id": 1,
    "search_attributes": {
      "org_academic_year_id": [
        190
      ],
      "org_academic_year": {
        "year": {
          "id": 190,
          "name": "Newname2016-2017",
          "year_id": 201617
        }
      },
      "gpa_org_academic_year": {
        "year": {
          "id": 190,
          "name": "Newname2016-2017",
          "year_id": 201617
        }
      },
      "latest_survey": {
        "name": "Transition One",
        "id": 15
      },
      "latest_cohort": {
        "name": "Survey Cohort 1",
        "id": 1
      },
      "group_names": [
        "One",
        "Two"
      ],
      "datablocks": {
            "profile_block_id": 21,
            "profile_items": {
              "id": 48,
              "display_name": "ApplicationDate",
              "item_data_type": "D",
              "item_meta_key": "Gender",
              "calendar_assignment": "N",
              "year_term": false,
              "subpopulation1": {
                "start_date": "06/20/2017",
                "end_date": "06/22/2017"
              },
              "subpopulation2": {
                "start_date": "06/06/2017",
                "end_date": "06/14/2017"
              }
            },
            "profile_block_name": "Admissions-Dates"
          },
      "isps": [],
      "isqs": [],
      "survey": [],
      "filterText": [
        "profileItem",
        {
          "text": "Gender",
          "yearTerm": ""
        },
        [
          "One",
          "Two"
        ]
      ]
    },
    "report_sections": {
      "reportId": "18",
      "reportDisable": false,
      "report_name": "Compare",
      "short_code": "SUB-COM",
      "reportDesc": "Compare outcomes of two student subpopulations. Choose subpopulations by profile item, ISP, survey question or ISQ. Compare survey factors and GPA. Export to csv, print to pdf.",
      "reportFilterPages": [],
      "reportFilter": {
        "participating": false,
        "risk": true,
        "active": true,
        "activities": false,
        "group": true,
        "course": true,
        "ebi": true,
        "isp": true,
        "static": true,
        "factor": true,
        "survey": true,
        "isq": true,
        "surveyMetadata": false,
        "academicTerm": false,
        "cohort": false,
        "team": false
      },
      "RequestParam": "",
      "pageurl": "/reports/compare/",
      "templateUrl": "/partials/reports/outcomesComparisonReport/modals/report-parameters-modal.html",
      "controller": "OutcomeComparisonController",
      "id": "18",
      "report_description": "Compare outcomes of two student subpopulations. Choose by profile item or survey question, compare survey factors and GPA.",
      "is_batch_job": true,
      "is_coordinator_report": "n"
    }
  },
  "report_info": {
    "reports_id": 18,
    "report_name": "Compare",
    "short_code": "SUB-PRE",
    "report_instance_id": 27316,
    "report_date": "2017-07-03T07:04:34+0000",
    "report_by": {
      "first_name": "retention1",
      "last_name": "faculty"
    }
  },
  "report_items": {
    "organizationId": 20,
    "factor": [],
    "gpa": []
  }
}';
        return $json;
    }

    private function getCompareResponseCategoryTypeJson()
    {
        $json = '{
  "request_json": {
    "id": 27316,
    "report_id": 18,
    "organization_id": 20,
    "person_id": 1,
    "search_attributes": {
      "org_academic_year_id": [
        190
      ],
      "org_academic_year": {
        "year": {
          "id": 190,
          "name": "Newname2016-2017",
          "year_id": 201617
        }
      },
      "gpa_org_academic_year": {
        "year": {
          "id": 190,
          "name": "Newname2016-2017",
          "year_id": 201617
        }
      },
      "latest_survey": {
        "name": "Transition One",
        "id": 15
      },
      "latest_cohort": {
        "name": "Survey Cohort 1",
        "id": 1
      },
      "group_names": [
        "Male",
        "Female"
      ],
      "datablocks": {
        "profile_block_id": 13,
        "profile_items": {
          "id": 1,
          "display_name": "Gender",
          "item_data_type": "S",
          "item_meta_key": "Gender",
          "calendar_assignment": "N",
          "year_term": false,
          "subpopulation1": {
            "category_type": [
              {
                "answer": "Male",
                "value": "1",
                "sequence_no": 0,
                "id": "",
                "subpopulationOneselected": true
              }
            ]
          },
          "subpopulation2": {
            "category_type": [
              {
                "answer": "Female",
                "value": "0",
                "sequence_no": 0,
                "id": "",
                "subpopulationTwoselected": true
              }
            ]
          }
        },
        "profile_block_name": "Demographic"
      },
      "isps": [],
      "isqs": [],
      "survey": [],
      "filterText": [
        "profileItem",
        {
          "text": "Gender",
          "yearTerm": ""
        },
        [
          "Male",
          "Female"
        ]
      ]
    },
    "report_sections": {
      "reportId": "18",
      "reportDisable": false,
      "report_name": "Compare",
      "short_code": "SUB-COM",
      "reportDesc": "Compare outcomes of two student subpopulations. Choose subpopulations by profile item, ISP, survey question or ISQ. Compare survey factors and GPA. Export to csv, print to pdf.",
      "reportFilterPages": [
        {
          "reportPage": "itemType",
          "title": "Select Subpopulation Attribute",
          "breadCrumb": "Select Type",
          "visited": false,
          "showHelp": true
        },
        {
          "reportPage": "treeSelector",
          "title": "Select Profile Item",
          "breadCrumbs": [
            {
              "type": "isp",
              "text": "Profile Item"
            },
            {
              "type": "isq",
              "text": "Survey Cohort"
            }
          ],
          "subPages": [
            {
              "prefix": "a",
              "reportPage": "treeSelector",
              "title": "Select one Cohort from one Survey",
              "breadCrumb": "Survey Cohort"
            },
            {
              "prefix": "b",
              "reportPage": "surveyQuestion",
              "title": "Select one survey question",
              "breadCrumb": "Survey Question",
              "showSearch": true
            }
          ],
          "visited": false,
          "showSearch": true
        },
        {
          "reportPage": "subPopulation",
          "title": "",
          "breadCrumb": "Values",
          "visited": false
        }
      ],
      "reportFilter": {
        "participating": false,
        "risk": true,
        "active": true,
        "activities": false,
        "group": true,
        "course": true,
        "ebi": true,
        "isp": true,
        "static": true,
        "factor": true,
        "survey": true,
        "isq": true,
        "surveyMetadata": false,
        "academicTerm": false,
        "cohort": false,
        "team": false
      },
      "RequestParam": "",
      "pageurl": "/reports/compare/",
      "templateUrl": "/partials/reports/outcomesComparisonReport/modals/report-parameters-modal.html",
      "controller": "OutcomeComparisonController",
      "id": "18",
      "report_description": "Compare outcomes of two student subpopulations. Choose by profile item or survey question, compare survey factors and GPA.",
      "is_batch_job": true,
      "is_coordinator_report": "n"
    }
  },
  "report_info": {
    "reports_id": 18,
    "report_name": "Compare",
    "short_code": "SUB-COM",
    "report_instance_id": 27316,
    "report_date": "2017-07-03T07:04:34+0000",
    "report_by": {
      "first_name": "retention1",
      "last_name": "faculty"
    }
  },
  "report_items": {
    "organizationId": 20,
    "factor": [],
    "gpa": []
  }
}';
        return $json;
    }

    /**
     * Get all the reports only accessible to coordinators
     *
     * @return array
     */
    private function getcoordinatorReportsArr()
    {
        $coordinatorReports = [['short_code' => 'MAR'], ['short_code' => 'FUR'], ['short_code' => 'PRR'], ['short_code' => 'CR'], ['short_code' => 'GPA']];
        return $coordinatorReports;
    }

    /**
     * Get all reports
     *
     * @return array
     */
    private function getAllReportsArr()
    {
        $getAllReports = [['id' => '13', 'name' => 'Faculty/Staff Usage Report', 'description' => 'Identify faculty/staff members and their activity. Export to csv.', 'is_batch_job' => 'y', 'is_coordinator_report' => 'y', 'short_code' => 'FUR', 'is_active' => 'y']];
        return $getAllReports;
    }

    /**
     * Get sample Json
     *
     * @return array
     */
    private function getSampleJson1()
    {
        // Defined sample json
        $sampleJsonString1 = '{
          "request_json": {
             "id": 4801,
             "report_id": 15,
             "organization_id": 2,
             "person_id": 2,
             "search_attributes": {
             "filterCount": 2,
             "org_academic_year_id": [1, 88],
             "org_academic_terms_id": [365, 234],
             "risk_start_date": "2015-09-09",
             "risk_end_date": "2015-12-09",
             "risk_indicator_ids": "",
             "student_status": "",
             "group_ids": "",
             "courses": [],
             "datablocks": [],
             "isps": [],
             "survey": [14],
             "cohort_ids": "",
             "static_list_ids": ""
                },
             "report_sections": {
             "reportId": 15,
             "report_name": "yearly ace manual",
             "short_code": "gpa"
          	    }
          	},
           "report_instance_id": "4801",
             "report_items": {
               "organization_id": "2",
               "total_student_count": "2011",
               "gpa_term_summaries_by_year": [{
               "year_name": "2014-2015",
             "gpa_summary_by_term": [{
               "term_name": "TermX",
               "student_count": "2011",
               "mean_gpa": "1.02",
               "percent_under_2": "99.10",
             "gpa_summary_by_risk": [{
               "risk_color": "green",
               "student_count": "0"
          	 }, {
               "risk_color": "yellow",
               "student_count": "0"
          	}, {
               "risk_color": "red",
               "student_count": "0"
              }, {
               "risk_color": "red2",
               "student_count": "0"
              }, {
               "risk_color": "gray",
               "student_count": "2011",
               "mean_gpa": "1.02"
               }]
              }, {
               "term_name": "Term10",
               "student_count": "34",
               "mean_gpa": "2.20",
               "percent_under_2": "44.12"
               }]
             }, {
               "year_name": "",
               "gpa_summary_by_term": [{
               "term_name": "Term10",
               "student_count": "58",
               "mean_gpa": "2.38",
               "percent_under_2": "27.59",
               "gpa_summary_by_risk": [{
                  "risk_color": "green",
                  "student_count": "0"
                }, {
                  "risk_color": "yellow",
                  "student_count": "0"
                }, {
                  "risk_color": "red",
                  "student_count": "0"
                }, {
                  "risk_color": "red2",
                  "student_count": "0"
                }, {
                  "risk_color": "gray",
                  "student_count": "58",
                  "mean_gpa": "2.50"
                  }]
                }]
             }]
          	}
           }';
        return $sampleJsonString1;
    }

    /**
     * Get sample Json
     *
     * @return array
     */
    private function getSampleJson2()
    {
        // Defined sample json
        $sampleJsonString2 = '{
          "request_json": {
             "id": 4802,
             "report_id": 15,
             "organization_id": 2,
             "person_id": 2,
              "search_attributes": {
             "filterCount": 2,
             "org_academic_year_id": [1, 88],
             "org_academic_terms_id": [365, 234],
             "risk_start_date": "2015-09-09",
             "risk_end_date": "2015-12-09",
             "risk_indicator_ids": "",
             "student_status": "",
             "group_ids": "",
             "courses": [],
             "datablocks": [],
             "isps": [],
             "survey": [14],
             "cohort_ids": "",
             "static_list_ids": ""
                },
             "report_sections": {
             "reportId": 15,
             "report_name": "GPA Report",
             "short_code": "gpa"
          	    }
          	},
           "report_instance_id": "4802",
             "report_items": {
               "organization_id": "2",
               "total_student_count": "35",
               "gpa_term_summaries_by_year": [{
               "year_name": "2013-2014",
             "gpa_summary_by_term": [{
               "term_name": "Term13-14",
               "student_count": "35",
               "mean_gpa": "1.05",
               "percent_under_2": "95.20",
             "gpa_summary_by_risk": [{
               "risk_color": "green",
               "student_count": "0"
          	 }, {
               "risk_color": "yellow",
               "student_count": "0"
          	}, {
               "risk_color": "red",
               "student_count": "0"
              }, {
               "risk_color": "red2",
               "student_count": "0"
              }, {
               "risk_color": "gray",
               "student_count": "2011",
               "mean_gpa": "1.02"
               }]
              }]
             }, {
               "year_name": "",
               "gpa_summary_by_term": [{
               "term_name": "Term12",
               "student_count": "40",
               "mean_gpa": "2.05",
               "percent_under_2": "35.29",
               "gpa_summary_by_risk": [{
                  "risk_color": "green",
                  "student_count": "0"
                }, {
                  "risk_color": "yellow",
                  "student_count": "0"
                }, {
                  "risk_color": "red",
                  "student_count": "0"
                }, {
                  "risk_color": "red2",
                  "student_count": "0"
                }, {
                  "risk_color": "gray",
                  "student_count": "58",
                  "mean_gpa": "2.50"
                  }]
                }]
             }]
          	}
           }';
        return $sampleJsonString2;
    }

    public function testGetOurStudentsReport()
    {
        $this->specify("Test our Students Reports",
            function ($customSearchDto, $rawData, $loggedInUserId) {
                $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                    'getRepository'
                ]);
                $mockContainer = $this->getMock('Container', [
                    'get'
                ]);
                $mockLogger = $this->getMock('Logger', [
                    'debug'
                ]);
                $mockLogger->method('debug')
                    ->willReturn(1);
                $mockSurveyBlockService = $this->getMockBuilder('Synapse\SurveyBundle\Service\Impl\SurveyBlockService')
                    ->disableOriginalConstructor()
                    ->setMethods([])
                    ->getMock();
                $mockCampusResourceService = $this->getMockBuilder('Synapse\CampusResourceBundle\Service\Impl\CampusResourceService')
                    ->disableOriginalConstructor()
                    ->setMethods([])
                    ->getMock();
                $mockProfileService = $this->getMockBuilder('Synapse\CoreBundle\Service\Impl\ProfileService')
                    ->disableOriginalConstructor()
                    ->setMethods([])
                    ->getMock();
                $mockOrgProfileService = $this->getMockBuilder('Synapse\CoreBundle\Service\Impl\OrgProfileService')
                    ->disableOriginalConstructor()
                    ->setMethods([])
                    ->getMock();
                $mockDoctrine = $this->getMock('doctrine', []);
                $mockCampusConnectionService = $this->getMockBuilder('Synapse\CampusConnectionBundle\Service\Impl\CampusConnectionService')
                    ->disableOriginalConstructor()
                    ->setMethods([])
                    ->getMock();
                $mockActivityReportService = $this->getMockBuilder('Synapse\ReportsBundle\Service\Impl\ActivityReportService')
                    ->disableOriginalConstructor()
                    ->setMethods([])
                    ->getMock();
                $mockSurveySnapshotService = $this->getMockBuilder('Synapse\ReportsBundle\Service\Impl\SurveySnapshotService')
                    ->disableOriginalConstructor()
                    ->setMethods([])
                    ->getMock();
                $mockProfileSnapshotService = $this->getMockBuilder('Synapse\ReportsBundle\Service\Impl\ProfileSnapshotService')
                    ->disableOriginalConstructor()
                    ->setMethods([])
                    ->getMock();
                $mockJmsSerializer = $this->getMock('jms_serializer', []);
                $mockFactorReportService = $this->getMockBuilder('Synapse\ReportsBundle\Service\Impl\FactorReportService')
                    ->disableOriginalConstructor()
                    ->setMethods([])
                    ->getMock();
                $mockGpaReportService = $this->getMockBuilder('Synapse\ReportsBundle\Service\Impl\GPAReportService')
                    ->disableOriginalConstructor()
                    ->setMethods([])
                    ->getMock();
                $mockReportsDtoService = $this->getMockBuilder('Synapse\ReportsBundle\Service\Impl\ReportsDtoVerificationService')
                    ->disableOriginalConstructor()
                    ->setMethods([])
                    ->getMock();
                $mockResqueService = $this->getMockBuilder('BCC\ResqueBundle\Resque')
                    ->disableOriginalConstructor()
                    ->setMethods([])
                    ->getMock();
                $mockPdfReportService = $this->getMockBuilder('Synapse\ReportsBundle\Service\Impl\PdfReportsService')
                    ->disableOriginalConstructor()
                    ->setMethods([])
                    ->getMock();
                $mockReportEntity = $this->getMock('Synapse\ReportsBundle\Entity\Reports', []);
                $mockReportsRepo = $this->getMock('ReportsRepository', [
                    'find'
                ]);
                $mockIssueDAO = $this->getMock('IssueDAO', ['generateStudentIssuesTemporaryTable', 'getTopIssuesFromStudentIssues']);
                $mockReportsRepo->method('find')
                    ->willReturn($mockReportEntity);
                $mockIssueDAO->method('generateStudentIssuesTemporaryTable')->willReturn(true);
                $mockIssueDAO->method('getTopIssuesFromStudentIssues')
                    ->willReturn([
                        [
                            'issue_name' => "issue1",
                            'denominator' => "issue1",
                            'percent' => 1,
                            'icon' => "tesst"
                        ]
                    ]);
                $mockReportRunningStatusEntity = $this->getMock('Synapse\ReportsBundle\Entity\ReportsRunningStatus', [
                    'getFilteredStudentIds'
                ]);
                $mockReportRunningStatusEntity->method('getFilteredStudentIds')
                    ->willReturn("1,2");
                $mockReportsRunningStatusRepo = $this->getMock('ReportsRunningStatusRepository', [
                    'find'
                ]);
                $mockReportsRunningStatusRepo->method('find')
                    ->willReturn($mockReportRunningStatusEntity);
                $mockAcademicYearRepo = $this->getMock('AcademicYear', [
                    'getCurrentAcademicDetails'
                ]);
                $mockAcademicYearRepo->method('getCurrentAcademicDetails')
                    ->willReturn([
                        [
                            'id' => 1,
                            'startDate' => date(SynapseConstant::DEFAULT_DATE_FORMAT)
                        ]
                    ]);
                $mockAcademicTermRepo = $this->getMock('OrgAcademicTerm', ['findBy']);
                $mockAcademicTerm = new OrgAcademicTerms();
                $mockAcademicTermRepo->method('findBy')->willReturn($mockAcademicTerm);
                $mockOrganizationEntity = $this->getMock('Organization', [
                    'getId',
                    'getTimezone',
                    'getLogoFileName'
                ]);
                $mockOrganizationEntity->method('getId')
                    ->willReturn(1);
                $mockOrganizationEntity->method('getTimezone')
                    ->willReturn('Asia/Kolkotta');
                $mockOrgService = $this->getMock('OrganizationService', array(
                    'find'
                ));
                $mockOrgService->method('find')
                    ->willReturn($mockOrganizationEntity);
                $mockOrgLangService = $this->getMock('OrganizationLangService', array(
                    'getOrganization'
                ));
                $mockOrgLangService->method('getOrganization')
                    ->willReturn(array(
                        'name' => "org1"
                    ));
                $mockDataProcessingUtilityService = $this->getMock('DataProcessingUtilityService', ['sortMultiDimensionalArray']);
                $mockDataProcessingUtilityService->method('sortMultiDimensionalArray')->willReturn([
                    [
                        'value' => "Listname",
                        'count' => 1,
                        'percentage' => 50
                    ]
                ]);
                $mockOurStudentsReportService = $this->getMock('OurStudentsReportService', ['getSurveyBasedSections']);
                $mockOurStudentsReportService->method('getSurveyBasedSections')->willReturn(["It doesn't matter what we return, because this test isn't testing report content."]);
                $mockAlertNotificationsService = $this->getMockBuilder('Synapse\CoreBundle\Service\Impl\AlertNotificationService')
                    ->disableOriginalConstructor()
                    ->setMethods(['createNotification'])
                    ->getMock();
                $mockAlertNotificationsService->method('createNotification')
                    ->willReturn(1);
                $mockPersonEntity = $this->getMock('Person', [
                    'getId',
                    'getOrganization',
                    'getFirstname',
                    'getLastname'
                ]);
                $mockPersonEntity->method('getId')
                    ->willReturn(1);
                $mockPersonEntity->method('getFirstname')
                    ->willReturn("firstname");
                $mockPersonEntity->method('getLastname')
                    ->willReturn("lastname");
                $mockPersonEntity->method('getOrganization')
                    ->willReturn($mockOrganizationEntity);
                $mockPersonRepo = $this->getMock('PersonRepository', [
                    'find'
                ]);
                $mockPersonRepo->method('find')
                    ->willReturn($mockPersonEntity);
                $mockOrgRepo = $this->getMock('OrganizationRepository', [
                    'find'
                ]);
                $mockOrgRepo->method('find')
                    ->willReturn($mockOrganizationEntity);
                $mockPersonRepo->method('find')
                    ->willReturn($mockPersonEntity);
                $mockEbiMetadataRepository = $this->getMock('EbiMetadataRepository', [
                    'findOneByKey'
                ]);
                $ebiMetadataEntity = $this->getMock('EbiMetaData', [
                    'getId'
                ]);
                $ebiMetadataEntity->method('getId')
                    ->willReturn(1);
                $surveyQuestionEntity = $this->getMock('SurveyQuestions', [
                    'getId'
                ]);
                $surveyQuestionEntity->method('getId')
                    ->willReturn(1);
                $mockEbiMetadataRepository->method('findOneByKey')
                    ->willReturn($ebiMetadataEntity);
                $mockOrgSearchRepository = $this->getMock('OrgSearchRepository', [
                    'getOrgSearch'
                ]);
                $mockFactorRepository = $this->getMock('FactorRepository', [
                    'getDataBlockQuestionsBasedPermission',
                    'listFactorsByPermission'
                ]);
                $mockFactorRepository->method('getDataBlockQuestionsBasedPermission')
                    ->willReturn([
                        [
                            'ebi_question_id' => 1
                        ]
                    ]);
                $mockFactorRepository->method('listFactorsByPermission')
                    ->willReturn([
                        [
                            'factor_id' => 1
                        ]
                    ]);
                $mockSurveyQuestionsRepository = $this->getMock('SurveyQuestionsRepository', [
                    'findBy'
                ]);
                $mockSurveyQuestionsRepository->method('findBy')
                    ->willReturn($surveyQuestionEntity);
                $mockOrgSearchRepository->method('getOrgSearch')
                    ->willReturn([
                            [
                                'section_id' => 1,
                                'section_name' => "Demographics",
                                'numerator_count' => 1,
                                'denominator_count' => 2,
                                'element_name' => "CampusResident",
                                'element_id' => "1",
                                'meta_name' => "CampusResident",
                                'count_students' => 1,
                                'list_name' => "Listname"
                            ]
                        ]
                    );
                $mockRepositoryResolver->method('getRepository')
                    ->willReturnMap([
                        [
                            'SynapseReportsBundle:Reports',
                            $mockReportsRepo
                        ],
                        [
                            'SynapseReportsBundle:ReportsRunningStatus',
                            $mockReportsRunningStatusRepo
                        ],
                        [
                            'SynapseCoreBundle:Person',
                            $mockPersonRepo
                        ],
                        [
                            'SynapseCoreBundle:Organization',
                            $mockOrgRepo
                        ],
                        [
                            'SynapseAcademicBundle:OrgAcademicYear',
                            $mockAcademicYearRepo
                        ],
                        [
                            'SynapseAcademicBundle:OrgAcademicTerms',
                            $mockAcademicTermRepo
                        ],
                        [
                            'SynapseCoreBundle:EbiMetadata',
                            $mockEbiMetadataRepository
                        ],
                        [
                            'SynapseSearchBundle:OrgSearch',
                            $mockOrgSearchRepository
                        ],
                        [
                            'SynapseSurveyBundle:Factor',
                            $mockFactorRepository
                        ],
                        [
                            'SynapseSurveyBundle:SurveyQuestions',
                            $mockSurveyQuestionsRepository
                        ]
                    ]);
                $mockContainer->expects($this->any())
                    ->method('get')
                    ->willReturnMap(
                        [
                            [
                                SurveyBlockService::SERVICE_KEY,
                                $mockSurveyBlockService
                            ],
                            [
                                CampusConnectionService::SERVICE_KEY,
                                $mockCampusConnectionService
                            ],
                            [
                                CampusResourceService::SERVICE_KEY,
                                $mockCampusResourceService
                            ],
                            [
                                PdfReportsService::SERVICE_KEY,
                                $mockPdfReportService
                            ],
                            [
                                ActivityReportService::SERVICE_KEY,
                                $mockActivityReportService
                            ],
                            [
                                SurveySnapshotService::SERVICE_KEY,
                                $mockSurveySnapshotService
                            ],
                            [
                                ProfileSnapshotService::SERVICE_KEY,
                                $mockProfileSnapshotService
                            ],
                            [
                                DataProcessingUtilityService::SERVICE_KEY,
                                $mockDataProcessingUtilityService
                            ],
                            [
                                SynapseConstant::RESQUE_CLASS_KEY,
                                $mockResqueService
                            ],
                            [
                                ProfileService::SERVICE_KEY,
                                $mockProfileService
                            ],
                            [
                                OrgProfileService::SERVICE_KEY,
                                $mockOrgProfileService
                            ],
                            [
                                SynapseConstant::DOCTRINE_CLASS_KEY,
                                $mockDoctrine
                            ],
                            [
                                FactorReportService::SERVICE_KEY,
                                $mockFactorReportService
                            ],
                            [
                                SynapseConstant::JMS_SERIALIZER_CLASS_KEY,
                                $mockJmsSerializer
                            ],
                            [
                                GPAReportService::SERVICE_KEY,
                                $mockGpaReportService
                            ],
                            [
                                ReportsDtoVerificationService::SERVICE_KEY,
                                $mockReportsDtoService
                            ],
                            [
                                OrganizationlangService::SERVICE_KEY,
                                $mockOrgLangService
                            ],
                            [
                                OurStudentsReportService::SERVICE_KEY,
                                $mockOurStudentsReportService
                            ],
                            [
                                AlertNotificationsService::SERVICE_KEY,
                                $mockAlertNotificationsService
                            ],
                            [
                                IssueDAO::DAO_KEY,
                                $mockIssueDAO
                            ]
                        ]);
                $reportService = new ReportsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $data = $reportService->getOurStudentsReport($customSearchDto, $loggedInUserId, $rawData);
                $this->assertInternalType('array', $data);
                $this->assertEquals($rawData, $data['request_json']);
            }, [
                'examples' => [
//                    Example 1: Test GetOurStudentsReport with CampusResidentDisplayFilter
                    [
                        $this->createCustomSearchDto($this->createDataBlockArrWithCampusResidentDisplayFilter()),
                        $this->createRawData(),
                        1
                    ],
//                    Example 2: Test GetOurStudentsReport without CampusResidentDisplayFilter
                    [
                        $this->createCustomSearchDto($this->createDataBlockArrWithoutCampusResidentDisplayFilter()),
                        $this->createRawData(),
                        1
                    ]
                ]
            ]);
    }

    private function createCustomSearchDto($displayFilterArr = null, $ispArr = null)
    {
        $searchAttribute = [];
        $customSearchDto = new ReportRunningStatusDto();
        $customSearchDto->setId(1);
        $customSearchDto->setOrganizationId($this->organization);
        $searchAttribute["risk_indicator_ids"] = "1,2,3";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "1";
        $searchAttribute["referral_status"] = "'o'";
        $searchAttribute["contact_types"] = "1,2";
        $searchAttribute["filterCount"] = 7;
        $searchAttribute["survey_filter"] = array(
            "survey_id" => 11,
            "org_academic_year_id" => "201516",
            "survey_name" => "Transition One",
            "cohort" => 1,
            "cohort_Name" => "Survey Cohort 1",
            "factors" => []
        );
        if (!is_null($ispArr)) {
            $searchAttribute["isps"] = $ispArr;
        }
        if (!is_null($displayFilterArr)) {
            $searchAttribute['displayFilter']['sections'][] = $displayFilterArr;
        } else {
            $searchAttribute['displayFilter']['sections'][] = [];
        }
        $customSearchDto->setSearchAttributes($searchAttribute);
        return $customSearchDto;
    }

    private function createRawData()
    {
        $rawdata = ' {
            "organization_id": ' . $this->organization . ',
            "person_id": "4885320",
            "saved_search_id": "",
            "saved_search_name": "",
            "search_attributes": {
            "filterCount": 7,
    		"survey_filter": {
    			"survey_id": 11,
    			"org_academic_year_id": "201516",
    			"survey_name": "Transition One",
    			"cohort": 1,
    			"cohort_Name": "Survey Cohort 1",
    			"factors": []
    		},
            "risk_indicator_ids": "1,2,3",
            "intent_to_leave_ids": "",
            "group_ids": "1",
            "referral_status": "o",
            "contact_types": "1,2",
            "courses": [],
            "isps": [],
            "datablocks": [],
            "academic_updates": {
            "ignoreThis": "",
            "isBlankAcadUpdate": true
            }
        }';
        return $rawdata;
    }

    private function createDataBlockArrWithCampusResidentDisplayFilter()
    {
        $displayFilterArr = [
            "section_id" => 2,
            "title" => "demographics",
            "display_title" => "demographics",
            "section_element" => "scaled-value",
            "elements" => [
                [
                    "element_id" => 22,
                    "title" => "CampusResident",
                    "icon" => "",
                    "isSelected" => 1,
                    "display_option_term" => [
                        "term_id" => 1,
                        "term_name" => "some Year"
                    ],
                ],
            ]
        ];
        return $displayFilterArr;
    }

    private function createDataBlockArrWithoutCampusResidentDisplayFilter()
    {
        $displayFilterArr = [
            "section_id" => 2,
            "title" => "demographics",
            "display_title" => "demographics",
            "section_element" => "scaled-value",
            "elements" => [
                [
                    "element_id" => 21,
                    "title" => "AthleteStudent",
                    "icon" => "",
                    "isSelected" => 1
                ],
            ]
        ];
        return $displayFilterArr;
    }

    public function testCohortsKeyDownload()
    {
        $this->specify("Test downloadOrgQuestionKey() function called in testCohortsKeyDownload()",
            function ($cohortId) {
                $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                    'getRepository'
                ]);
                $mockContainer = $this->getMock('Container', [
                    'get'
                ]);
                $mockLogger = $this->getMock('Logger', [
                    'debug'
                ]);
                $mockLogger->method('debug')
                    ->willReturn(1);
                // Service Mocks
                $mockOrganizationService = $this->getMock("OrganizationService", ["find"]);
                // Repository Mocks
                $mockMetadataListValuesRepository = $this->getMock("MetadataListValuesRepository", ["findByListName"]);
                $mockWessLinkRepository = $this->getMock("WessLinkRepository", ["findBy"]);
                $mockSurveyQuestionsRepository = $this->getMock("SurveyQuestionsRepository", ["getOrgQuestionsForSurvey", "getOrgQuestionOptions", "getUniqueSurveyQuestionsForCohort"]);
                $mockDataBlockQuestionsRepository = $this->getMock("DataQuestionsRepository", ["getFactorForSurvey"]);
                // Entity Mocks
                $mockOrganization = $this->getMock("Organization", ["getTimeZone"]);
                $metadataListValues = $this->getMock("MetadataListValues", ["getListValue"]);
                $mockWessLink = $this->getMock("WessLink", ["getSurvey"]);
                $mockSurvey = $this->getMock("Survey", ["getId"]);
                $mockRepositoryResolver->method('getRepository')
                    ->willReturnMap([
                        [
                            MetadataListValuesRepository::REPOSITORY_KEY,
                            $mockMetadataListValuesRepository
                        ],
                        [
                            WessLinkRepository::REPOSITORY_KEY,
                            $mockWessLinkRepository
                        ],
                        [
                            SurveyQuestionsRepository::REPOSITORY_KEY,
                            $mockSurveyQuestionsRepository
                        ],
                        [
                            DatablockQuestionsRepository::REPOSITORY_KEY,
                            $mockDataBlockQuestionsRepository
                        ]
                    ]);
                $mockContainer->expects($this->any())
                    ->method('get')
                    ->willReturnMap(
                        [
                            [
                                OrganizationService::SERVICE_KEY,
                                $mockOrganizationService
                            ]
                        ]);
                $mockOrganizationService->expects($this->any())
                    ->method('find')
                    ->will($this->returnValue($mockOrganization));
                $mockMetadataListValuesRepository->expects($this->any())
                    ->method('findByListName')
                    ->will($this->returnValue([$metadataListValues]));
                $mockOrganization->expects($this->any())
                    ->method('getTimeZone')
                    ->will($this->returnValue("Central"));
                $metadataListValues->expects($this->any())
                    ->method('getListValue')
                    ->will($this->returnValue("US/Central"));
                $mockWessLinkRepository->expects($this->once())
                    ->method('findBy')
                    ->will($this->returnValue([$mockWessLink]));
                $mockWessLink->expects($this->once())
                    ->method('getSurvey')
                    ->will($this->returnValue($mockSurvey));
                $mockSurveyQuestionsRepository->expects($this->any())
                    ->method('getOrgQuestionsForSurvey')
                    ->will($this->returnValue($this->getOrgQuestionDetailsBySurveyAndCohortArray()));
                $mockSurveyQuestionsRepository->expects($this->any())
                    ->method('getOrgQuestionOptions')
                    ->will($this->returnValue($this->getQrgQuestionOptionsForQuestion()));
                $mockDataBlockQuestionsRepository->expects($this->any())
                    ->method('getFactorForSurvey')
                    ->will($this->returnValue([]));
                $mockSurveyQuestionsRepository->expects($this->any())
                    ->method('getUniqueSurveyQuestionsForCohort')
                    ->will($this->returnValue([]));
                $reportService = new ReportsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $response = $reportService->cohortsKeyDownload($this->organization, $cohortId);
                $this->assertInternalType('array', $response);
                $this->assertEquals($cohortId, $response['cohort_id']);
            }, [
                'examples' => [
                    [
                        1
                    ],
                    [
                        2
                    ]
                ]
            ]);
    }

    private function getOrgQuestionDetailsBySurveyAndCohortArray()
    {
        return [
            [
                "survey_ques_id" => 1092,
                "org_ques_text" => "How satisfied were you with your Orientation leader?",
                "org_question_type" => "Q",
                "org_question_id" => 15
            ],
            [
                "survey_ques_id" => 1093,
                "org_ques_text" => "When did you attend Orientation?",
                "org_question_type" => "D",
                "org_question_id" => 16
            ],
            [
                "survey_ques_id" => 1091,
                "org_ques_text" => "Choose two of the following",
                "org_question_type" => "MR",
                "org_question_id" => 17
            ],
            [
                "survey_ques_id" => 1094,
                "org_ques_text" => "How old is dirt?",
                "org_question_type" => "NA",
                "org_question_id" => 18
            ]
        ];
    }

    private function getQrgQuestionOptionsForQuestion()
    {
        return [
            [
                "org_option_id" => 1,
                "org_option_text" => "(1) Not At All",
                "org_option_value" => 1,
                "sequence" => 1
            ],
            [
                "org_option_id" => 2,
                "org_option_text" => "(2) ",
                "org_option_value" => 2,
                "sequence" => 2
            ],
            [
                "org_option_id" => 3,
                "org_option_text" => "(3) ",
                "org_option_value" => 3,
                "sequence" => 3
            ]
        ];
    }

    private $searchAttribute = [
        "filterCount" => 5,
        "org_academic_year_id" => 324,
        "org_academic_year" =>
            [
                "year" =>
                    [
                        "id" => 324,
                        "name" => "201718",
                        "year_id" => "201718",
                        "start_date" => "2017-07-07",
                        "end_date" => "2018-09-29",
                        "can_delete" =>
                            [
                                "is_current_year" => 1,
                            ]
                    ]
            ],
        "risk_indicator_date" => null,
        "risk_indicator_ids" => "1,2,3,4,6",
        "group_ids" => null,
        "group_names" => [],
        "courses" => [],
        "datablocks" => [],
        "isps" => [],
        "static_list_ids" => null,
        "static_list_names" => [],
        "academic_updates" =>
            [
                "grade" => null,
                "final_grade" => null,
                "absences" => null,
                "absence_range" =>
                    [
                        "min_range" => null,
                        "max_range" => null
                    ],
                "failure_risk" => null,
                "term_ids" => null,
                "is_current_academic_year" => 1
            ],
        "retention_completion" => [],
        "participating" =>
            [
                "participating_value" =>
                    [
                        "0" => 1
                    ],
                "org_academic_year_id" =>
                    [
                        "0" => 324
                    ]
            ],
        "student_status" =>
            [
                "status_value" => [],
                "org_academic_year_id" =>
                    [
                        "0" => 324
                    ]
            ],
        "filterText" => "Risk : Red2 / Red / Yellow / Green / Gray, Participating Students : (201718):Yes"
    ];

    public function testGetIndividualResponseReportData()
    {
        $this->specify("Test getIndividualResponseReportData() function", function ($customSearchDto, $studentList, $OrganizationSearchArray, $loggedUserId, $outputFormat, $pageNumber, $offset, $dataType, $sortBy, $expectedResult) {
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockPerson = $this->getMock('Person', array(
                'find',
                'getFirstname',
                'getLastname'
            ));
            $mockPersonRepository = $this->getMock('Person', array(
                'find',
                'getFirstname',
                'getLastname'
            ));
            $mockPersonRepository->expects($this->any())
                ->method('find')
                ->will($this->returnValue($mockPerson));
            $mockReports = $this->getMock('Reports', array(
                'findOneById'
            ));
            $mockReportsRepository = $this->getMock('ReportsRepository', array(
                'findOneById',
                'getIndividualResponseReportData'
            ));
            $mockReportsRepository->expects($this->any())
                ->method('findOneById')
                ->will($this->returnValue($mockReports));
            $mockReportsRepository->expects($this->any())
                ->method('getIndividualResponseReportData')
                ->willReturn($studentList);
            $mockOrganizationRole = $this->getMock('OrganizationRole', array(
                'findBy'
            ));
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRole', array(
                'findBy'
            ));
            $mockOrganizationRoleRepository->expects($this->any())
                ->method('findBy')
                ->will($this->returnValue($mockOrganizationRole));
            $mockOrganization = $this->getMock('Organization', array(
                'find',
                'getId'
            ));
            $mockOrganizationRepository = $this->getMock('Organization', array(
                'find',
                'getId'
            ));
            $mockOrganizationRepository->expects($this->any())
                ->method('find')
                ->will($this->returnValue($mockOrganization));
            $mockOrganizationSearchRepository = $this->getMock('OrgSearch', array(
                'getOrgSearch'
            ));
            $mockOrganizationSearchRepository->expects($this->any())
                ->method('getOrgSearch')
                ->willReturn($OrganizationSearchArray);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        ReportsRepository::REPOSITORY_KEY,
                        $mockReportsRepository
                    ],
                    [
                        OrganizationRoleRepository::REPOSITORY_KEY,
                        $mockOrganizationRoleRepository
                    ],
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ],
                    [
                        OrgSearchRepository::REPOSITORY_KEY,
                        $mockOrganizationSearchRepository
                    ]
                ]);
            $mockDateUtilityService = $this->getMock('date_utility_service', array(
                'getTimezoneAdjustedCurrentDateTimeForOrganization'
            ));
            $mockDateUtilityService->expects($this->any())
                ->method('getTimezoneAdjustedCurrentDateTimeForOrganization')
                ->will($this->returnValue(new \DateTime('2017-10-10T06:00:15+0000')));
            $mockOrganizationService = $this->getMock('org_service', array(
                'find'
            ));
            $mockOrganizationService->expects($this->any())
                ->method('find')
                ->will($this->returnValue($mockOrganization));
            $mockSearchService = $this->getMock('search_service', array(
                'getStudentListBasedCriteria',
                'prefetchSearchKeys'
            ));
            $mockSearchService->expects($this->any())
                ->method('prefetchSearchKeys')
                ->will($this->returnValue(array()));
            $utilServiceHelper = $this->getMock('UtilServiceHelper', array(
                'generateCSV'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $mockContainer->expects($this->any())
                ->method('get')
                ->willReturnMap(
                    [
                        [
                            DateUtilityService::SERVICE_KEY,
                            $mockDateUtilityService
                        ],
                        [
                            OrganizationService::SERVICE_KEY,
                            $mockOrganizationService
                        ],
                        [
                            SearchService::SERVICE_KEY,
                            $mockSearchService
                        ],
                        [
                            UtilServiceHelper::SERVICE_KEY,
                            $utilServiceHelper
                        ]
                    ]
                );
            $reportService = new ReportsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $reportData = $reportService->getIndividualResponseReportData($customSearchDto, $loggedUserId, $outputFormat, $pageNumber, $offset, $dataType, $sortBy);
            $this->assertEquals($expectedResult, $reportData);
        }, [
            'examples' => [
//                Example 1: Test report when dataType = '' and optional filters too restrictive
                [
                    $this->getCustomSearchDto(
                        [
                            'organization_id' => 99,
                            'person_id' => 113802,
                            'search_attributes' =>
                                [
                                    'filterCount' => 2,
                                    'survey_filter' =>
                                        [
                                            'survey_id' => 17,
                                            'academic_year_name' => '2016-2017 Academic Year',
                                            'org_academic_year_id' => 168,
                                            'year_id' => 201617,
                                            'survey_name' => 'Transition Two',
                                            'cohort' => 1,
                                            'cohort_Name' => 'Survey Cohort 1'
                                        ],
                                    'org_academic_year_id' => 168,
                                    'org_academic_year' => [
                                        [
                                            'id' => 168,
                                            'name' => '2016-2017 Academic Year',
                                            'year_id' => 201617,
                                            'start_date' => '2016-08-15',
                                            'end_date' => '2017-08-09',
                                            'can_delete' => false,
                                            'is_current_year' => true
                                        ]
                                    ],
                                    'risk_indicator_date' => '',
                                    'risk_indicator_ids' => '',
                                    'group_ids' => '',
                                    'group_names' => [],
                                    'courses' => [],
                                    'datablocks' => [],
                                    'isps' => [],
                                    'static_list_ids' => '',
                                    'static_list_names' => [],
                                    'retention_completion' => [],
                                    'participating' => [
                                        'participating_value' => [1],
                                        'org_academic_year_id' => [168]
                                    ],
                                    'student_status' => [
                                        'status_value' => [],
                                        'org_academic_year_id' => [168]
                                    ],
                                    'filterText' => 'Survey Filters : 2016-2017 Academic Year Transition Two / Survey Cohort 1, Participating Students : (2016-2017 Academic Year):Yes'
                                ],
                            'report_sections' => [
                                'reportId' => 2,
                                'report_name' => 'Individual Response Report',
                                'short_code' => 'SUR-IRR',
                                'reportDesc' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions.',
                                'reportFilterPages' => [
                                    [
                                        'reportPage' => 'survey',
                                        'title' => 'Select a survey'
                                    ],
                                    [
                                        'reportPage' => 'cohort',
                                        'title' => 'Select a Cohort'
                                    ],
                                    [
                                        'reportPage' => 'filterAttributes',
                                        'title' => 'Select attributes'
                                    ]
                                ],
                                'reportFilter' => [
                                    'participating' => false,
                                    'risk' => true,
                                    'active' => false,
                                    'retentionCompletion' => true,
                                    'activities' => false,
                                    'group' => true,
                                    'course' => true,
                                    'ebi' => true,
                                    'isp' => true,
                                    'static' => true,
                                    'factor' => false,
                                    'survey' => false,
                                    'isq' => false,
                                    'surveyMetadata' => true,
                                    'academicTerm' => false,
                                    'cohort' => false,
                                    'team' => false
                                ],
                                'RequestParam' => 1,
                                'pageurl' => '/generated-reports/SUR-IRR/',
                                'id' => 2,
                                'report_description' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                                'is_batch_job' => true,
                                'is_coordinator_report' => 'n',
                                'report_id' => 2,
                                'campus_info' => [
                                    'organization_id' => 99,
                                    'primary_color' => '#4673a7',
                                    'secondary_color' => '#1e73d5',
                                    'inactivity_timeout' => 45,
                                    'refer_for_academic_assistance' => false,
                                    'send_to_student' => false,
                                    'can_view_in_progress_grade' => false,
                                    'can_view_absences' => false,
                                    'can_view_comments' => false,
                                    'calendar_type' => 'google',
                                    'calendar_sync' => true,
                                    'calendar_sync_users' => 0,
                                    'campus_name' => 'SynapseBetaOrg0099',
                                    'campus_logo' => 'images/default-mw-header-logo.png'
                                ]
                            ],
                            'report_id' => 2
                        ]
                    ),
                    [],
                    [],
                    113802,
                    'json',
                    1,
                    0,
                    '',
                    '-last_name',
                    [
                        'total_records' => 0,
                        'status_message' => [
                            'code' => 'R1001',
                            'description' => 'The optional filters you selected returned no results. Try refining your optional filters.',
                        ],
                    ]
                ],
//                Example 2: Test report when outputFormat = 'csv'
                [
                    $this->getCustomSearchDto(
                        [
                            'organization_id' => 99,
                            'person_id' => 113802,
                            'search_attributes' =>
                                [
                                    'filterCount' => 2,
                                    'survey_filter' =>
                                        [
                                            'survey_id' => 17,
                                            'academic_year_name' => '2016-2017 Academic Year',
                                            'org_academic_year_id' => 168,
                                            'year_id' => 201617,
                                            'survey_name' => 'Transition Two',
                                            'cohort' => 1,
                                            'cohort_Name' => 'Survey Cohort 1'
                                        ],
                                    'org_academic_year_id' => 168,
                                    'org_academic_year' => [
                                        [
                                            'id' => 168,
                                            'name' => '2016-2017 Academic Year',
                                            'year_id' => 201617,
                                            'start_date' => '2016-08-15',
                                            'end_date' => '2017-08-09',
                                            'can_delete' => false,
                                            'is_current_year' => true
                                        ]
                                    ],
                                    'risk_indicator_date' => '',
                                    'risk_indicator_ids' => '',
                                    'group_ids' => '',
                                    'group_names' => [],
                                    'courses' => [],
                                    'datablocks' => [],
                                    'isps' => [],
                                    'static_list_ids' => '',
                                    'static_list_names' => [],
                                    'retention_completion' => [],
                                    'participating' => [
                                        'participating_value' => [1],
                                        'org_academic_year_id' => [168]
                                    ],
                                    'student_status' => [
                                        'status_value' => [],
                                        'org_academic_year_id' => [168]
                                    ],
                                    'filterText' => 'Survey Filters : 2016-2017 Academic Year Transition Two / Survey Cohort 1, Participating Students : (2016-2017 Academic Year):Yes'
                                ],
                            'report_sections' => [
                                'reportId' => 2,
                                'report_name' => 'Individual Response Report',
                                'short_code' => 'SUR-IRR',
                                'reportDesc' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions.',
                                'reportFilterPages' => [
                                    [
                                        'reportPage' => 'survey',
                                        'title' => 'Select a survey'
                                    ],
                                    [
                                        'reportPage' => 'cohort',
                                        'title' => 'Select a Cohort'
                                    ],
                                    [
                                        'reportPage' => 'filterAttributes',
                                        'title' => 'Select attributes'
                                    ]
                                ],
                                'reportFilter' => [
                                    'participating' => false,
                                    'risk' => true,
                                    'active' => false,
                                    'retentionCompletion' => true,
                                    'activities' => false,
                                    'group' => true,
                                    'course' => true,
                                    'ebi' => true,
                                    'isp' => true,
                                    'static' => true,
                                    'factor' => false,
                                    'survey' => false,
                                    'isq' => false,
                                    'surveyMetadata' => true,
                                    'academicTerm' => false,
                                    'cohort' => false,
                                    'team' => false
                                ],
                                'RequestParam' => 1,
                                'pageurl' => '/generated-reports/SUR-IRR/',
                                'id' => 2,
                                'report_description' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                                'is_batch_job' => true,
                                'is_coordinator_report' => 'n',
                                'report_id' => 2,
                                'campus_info' => [
                                    'organization_id' => 99,
                                    'primary_color' => '#4673a7',
                                    'secondary_color' => '#1e73d5',
                                    'inactivity_timeout' => 45,
                                    'refer_for_academic_assistance' => false,
                                    'send_to_student' => false,
                                    'can_view_in_progress_grade' => false,
                                    'can_view_absences' => false,
                                    'can_view_comments' => false,
                                    'calendar_type' => 'google',
                                    'calendar_sync' => true,
                                    'calendar_sync_users' => 0,
                                    'campus_name' => 'SynapseBetaOrg0099',
                                    'campus_logo' => 'images/default-mw-header-logo.png'
                                ]
                            ],
                            'report_id' => 2
                        ]
                    ),
                    [],
                    [],
                    113802,
                    'csv',
                    5,
                    1,
                    'student_list',
                    '-email',
                    [
                        'total_records' => 0,
                        'status_message' => [
                            'code' => 'R1001',
                            'description' => 'The optional filters you selected returned no results. Try refining your optional filters.',
                        ],
                    ]
                ],
                //              Example 3: Test report when outputFormat json & dataType = ''
                [
                    $this->getCustomSearchDto(
                        [
                            'organization_id' => 90,
                            'person_id' => 113802,
                            'search_attributes' =>
                                [
                                    'filterCount' => 2,
                                    'survey_filter' =>
                                        [
                                            'survey_id' => 17,
                                            'academic_year_name' => '2016-2017 Academic Year',
                                            'org_academic_year_id' => 168,
                                            'year_id' => 201617,
                                            'survey_name' => 'Transition Two',
                                            'cohort' => 1,
                                            'cohort_Name' => 'Survey Cohort 1'
                                        ],
                                    'org_academic_year_id' => 168,
                                    'org_academic_year' => [
                                        [
                                            'id' => 168,
                                            'name' => '2016-2017 Academic Year',
                                            'year_id' => 201617,
                                            'start_date' => '2016-08-15',
                                            'end_date' => '2017-08-09',
                                            'can_delete' => false,
                                            'is_current_year' => true
                                        ]
                                    ],
                                    'risk_indicator_date' => '',
                                    'risk_indicator_ids' => '',
                                    'group_ids' => '',
                                    'group_names' => [],
                                    'courses' => [],
                                    'datablocks' => [],
                                    'isps' => [],
                                    'static_list_ids' => '',
                                    'static_list_names' => [],
                                    'retention_completion' => [],
                                    'participating' => [
                                        'participating_value' => [1],
                                        'org_academic_year_id' => [168]
                                    ],
                                    'student_status' => [
                                        'status_value' => [],
                                        'org_academic_year_id' => [168]
                                    ],
                                    'filterText' => 'Survey Filters : 2016-2017 Academic Year Transition Two / Survey Cohort 1, Participating Students : (2016-2017 Academic Year):Yes'
                                ],
                            'report_sections' => [
                                'reportId' => 2,
                                'report_name' => 'Individual Response Report',
                                'short_code' => 'SUR-IRR',
                                'reportDesc' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions.',
                                'reportFilterPages' => [
                                    [
                                        'reportPage' => 'survey',
                                        'title' => 'Select a survey'
                                    ],
                                    [
                                        'reportPage' => 'cohort',
                                        'title' => 'Select a Cohort'
                                    ],
                                    [
                                        'reportPage' => 'filterAttributes',
                                        'title' => 'Select attributes'
                                    ]
                                ],
                                'reportFilter' => [
                                    'participating' => false,
                                    'risk' => true,
                                    'active' => false,
                                    'retentionCompletion' => true,
                                    'activities' => false,
                                    'group' => true,
                                    'course' => true,
                                    'ebi' => true,
                                    'isp' => true,
                                    'static' => true,
                                    'factor' => false,
                                    'survey' => false,
                                    'isq' => false,
                                    'surveyMetadata' => true,
                                    'academicTerm' => false,
                                    'cohort' => false,
                                    'team' => false
                                ],
                                'RequestParam' => 1,
                                'pageurl' => '/generated-reports/SUR-IRR/',
                                'id' => 2,
                                'report_description' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                                'is_batch_job' => true,
                                'is_coordinator_report' => 'n',
                                'report_id' => 2,
                                'campus_info' => [
                                    'organization_id' => 90,
                                    'primary_color' => '#4673a7',
                                    'secondary_color' => '#1e73d5',
                                    'inactivity_timeout' => 45,
                                    'refer_for_academic_assistance' => false,
                                    'send_to_student' => false,
                                    'can_view_in_progress_grade' => false,
                                    'can_view_absences' => false,
                                    'can_view_comments' => false,
                                    'calendar_type' => 'google',
                                    'calendar_sync' => true,
                                    'calendar_sync_users' => 0,
                                    'campus_name' => 'SynapseBetaOrg0099',
                                    'campus_logo' => 'images/default-mw-header-logo.png'
                                ]
                            ],
                            'report_id' => 2
                        ]
                    ),
                    [['student_count' => 1,
                        'count' => 1,
                        'person_id' => 113802,
                        'first_name' => '',
                        'last_name' => '',
                        'email' => '',
                        'phone_number' => '',
                        'opted_out' => '',
                        'survey_responded_status' => true,
                        'survey_status' => true]],
                    [[
                        'student_count' => 1,
                        'count' => 1,
                        'person_id' => 113802,
                        'first_name' => '',
                        'last_name' => '',
                        'email' => '',
                        'phone_number' => '',
                        'opted_out' => '',
                        'survey_responded_status' => true,
                        'survey_status' => true
                    ]],
                    113802,
                    'json',
                    2,
                    0,
                    '',
                    'email',
                    [

                        'non_participant_count' => 0,
                        'total_records' => 1,
                        'total_pages' => 1.0,
                        'records_per_page' => 25,
                        'current_page' => 2,
                        'percent_responded' => '1 / 1 (100%)',
                        'report_sections' => [
                            'reportId' => 2,
                            'report_name' => 'Individual Response Report',
                            'short_code' => 'SUR-IRR'
                        ],
                        'search_attributes' => [ 'filterCount' => 2,
                            'survey_filter' =>
                                [
                                    'survey_id' => 17,
                                    'academic_year_name' => '2016-2017 Academic Year',
                                    'org_academic_year_id' => 168,
                                    'year_id' => 201617,
                                    'survey_name' => 'Transition Two',
                                    'cohort' => 1,
                                    'cohort_Name' => 'Survey Cohort 1'
                                ],
                            'org_academic_year_id' => 168,
                            'org_academic_year' => [
                                [
                                    'id' => 168,
                                    'name' => '2016-2017 Academic Year',
                                    'year_id' => 201617,
                                    'start_date' => '2016-08-15',
                                    'end_date' => '2017-08-09',
                                    'can_delete' => false,
                                    'is_current_year' => true
                                ]
                            ],
                            'risk_indicator_date' => '',
                            'risk_indicator_ids' => '',
                            'group_ids' => '',
                            'group_names' => [],
                            'courses' => [],
                            'datablocks' => [],
                            'isps' => [],
                            'static_list_ids' => '',
                            'static_list_names' => [],
                            'retention_completion' => [],
                            'participating' => [
                                'participating_value' => [1],
                                'org_academic_year_id' => [168]
                            ],
                            'student_status' => [
                                'status_value' => [],
                                'org_academic_year_id' => [168]
                            ],
                            'filterText' => 'Survey Filters : 2016-2017 Academic Year Transition Two / Survey Cohort 1, Participating Students : (2016-2017 Academic Year):Yes'
                        ],
                        'report_data' => [$this->getSurveyStatusReportDto([
                            'student_count' => 1,
                            'count' => 1,
                            'person_id' => 113802,
                            'first_name' => '',
                            'last_name' => '',
                            'email' => '',
                            'phone_number' => '',
                            'opted_out' => '',
                            'responded' => true,
                            'responded_at' => null
                        ])],
                        'request_json' => $this->getCustomSearchDto(
                            array(
                                'organization_id' => 90,
                                'savedSearchId' => '',
                                'savedSearchName' => '',
                                'dateCreated' => '',
                                'person_id' => 113802,
                                'search_attributes' => [
                                    'filterCount' => 2,
                                    'survey_filter' => [
                                        'survey_id' => 17,
                                        'academic_year_name' => '2016-2017 Academic Year',
                                        'org_academic_year_id' => 168,
                                        'year_id' => 201617,
                                        'survey_name' => 'Transition Two',
                                        'cohort' => 1,
                                        'cohort_Name' => 'Survey Cohort 1'
                                    ],
                                    'org_academic_year_id' => 168,
                                    'org_academic_year' => [
                                        [
                                            'id' => 168,
                                            'name' => '2016-2017 Academic Year',
                                            'year_id' => 201617,
                                            'start_date' => '2016-08-15',
                                            'end_date' => '2017-08-09',
                                            'can_delete' => '',
                                            'is_current_year' => 1
                                        ]
                                    ],
                                    'risk_indicator_date' => '',
                                    'risk_indicator_ids' => '',
                                    'group_ids' => '',
                                    'group_names' => [],
                                    'courses' => [],
                                    'datablocks' => [],
                                    'isps' => [],
                                    'static_list_ids' => '',
                                    'static_list_names' => [],
                                    'retention_completion' => [],
                                    'participating' => [
                                        'participating_value' => [1],
                                        'org_academic_year_id' => [168]
                                    ],
                                    'student_status' => [
                                        'status_value' => [],
                                        'org_academic_year_id' => [168]
                                    ],
                                    'filterText' => 'Survey Filters : 2016-2017 Academic Year Transition Two / Survey Cohort 1, Participating Students : (2016-2017 Academic Year):Yes'
                                ],
                                'savedSearches' => '',
                                'activityReport' => '',
                                'report_sections' => [
                                    'reportId' => 2,
                                    'report_name' => 'Individual Response Report',
                                    'short_code' => 'SUR-IRR',
                                    'reportDesc' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions.',
                                    'reportFilterPages' => [
                                        [
                                            'reportPage' => 'survey',
                                            'title' => 'Select a survey',
                                        ],
                                        [
                                            'reportPage' => 'cohort',
                                            'title' => 'Select a Cohort'
                                        ],
                                        [
                                            'reportPage' => 'filterAttributes',
                                            'title' => 'Select attributes'
                                        ]
                                    ],
                                    'reportFilter' => [
                                        'participating' => false,
                                        'risk' => true,
                                        'active' => false,
                                        'retentionCompletion' => true,
                                        'activities' => false,
                                        'group' => true,
                                        'course' => true,
                                        'ebi' => true,
                                        'isp' => true,
                                        'static' => 1,
                                        'factor' => '',
                                        'survey' => '',
                                        'isq' => '',
                                        'surveyMetadata' => 1,
                                        'academicTerm' => '',
                                        'cohort' => '',
                                        'team' => ''
                                    ],
                                    'RequestParam' => 1,
                                    'pageurl' => '/generated-reports/SUR-IRR/',
                                    'id' => 2,
                                    'report_description' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                                    'is_batch_job' => true,
                                    'is_coordinator_report' => 'n',
                                    'report_id' => 2,
                                    'campus_info' => [
                                        'organization_id' => 90,
                                        'primary_color' => '#4673a7',
                                        'secondary_color' => '#1e73d5',
                                        'inactivity_timeout' => 45,
                                        'refer_for_academic_assistance' => '',
                                        'send_to_student' => '',
                                        'can_view_in_progress_grade' => '',
                                        'can_view_absences' => '',
                                        'can_view_comments' => '',
                                        'calendar_type' => 'google',
                                        'calendar_sync' => 1,
                                        'calendar_sync_users' => 0,
                                        'campus_name' => 'SynapseBetaOrg0099',
                                        'campus_logo' => 'images/default-mw-header-logo.png'
                                    ]
                                ],
                                'report_id' => 2,
                                'selectedAttributesCsv' => '',
                                'searchType' => ''
                            )
                        ),
                        'person_id' => 113802,
                        'report_by' => [
                            'first_name' => '',
                            'last_name' => ''
                        ],
                        'report_date' => $this->getDateTime('2017-10-10T06:00:15+0000')
                    ]
                ],
                // Example 4: Test report with multiple filters
                [
                    $this->getCustomSearchDto(
                        [
                            'organization_id' => 203,
                            'person_id' => 4878971,
                            'search_attributes' =>
                                [
                                    'filterCount' => 2,
                                    'survey_filter' =>
                                        [
                                            'survey_id' => 17,
                                            'academic_year_name' => '2016-2017 Academic Year',
                                            'org_academic_year_id' => 168,
                                            'year_id' => 201617,
                                            'survey_name' => 'Transition Two',
                                            'cohort' => 1,
                                            'cohort_Name' => 'Survey Cohort 1'
                                        ],
                                    'org_academic_year_id' => 168,
                                    'org_academic_year' => [
                                        [
                                            'id' => 168,
                                            'name' => '2016-2017 Academic Year',
                                            'year_id' => 201617,
                                            'start_date' => '2016-08-15',
                                            'end_date' => '2017-08-09',
                                            'can_delete' => false,
                                            'is_current_year' => true
                                        ]
                                    ],
                                    'risk_indicator_date' => '',
                                    'risk_indicator_ids' => '',
                                    "group_ids" => "377668,346053,377445",
                                    'group_names' => ["#clear", "All", "g1", "g2", "g3"],
                                    'courses' => [[
                                        "term_code" => "Spring2017",
                                        "term_name" => "Spring",
                                        "college_code" => "123",
                                        "year_name" => "2016-2017",
                                        "year_id" => '201617',
                                        "dept_code" => "123",
                                        "subject_code" => "123",
                                        "course_name" => "NewCourse",
                                        "course_number" => "123",
                                        "section_numbers" => []
                                    ]],
                                    'datablocks' => [],
                                    'isps' => [],
                                    'static_list_ids' => '',
                                    'static_list_names' => [],
                                    'retention_completion' => [],
                                    'participating' => [
                                        'participating_value' => [1],
                                        'org_academic_year_id' => [168]
                                    ],
                                    'student_status' => [
                                        'status_value' => [],
                                        'org_academic_year_id' => [168]
                                    ],
                                    'filterText' => 'Survey Filters : 2016-2017 Academic Year Transition Two / Survey Cohort 1, Participating Students : (2016-2017 Academic Year):Yes'
                                ],
                            'report_sections' => [
                                'reportId' => 2,
                                'report_name' => 'Individual Response Report',
                                'short_code' => 'SUR-IRR',
                                'reportDesc' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions.',
                                'reportFilterPages' => [
                                    [
                                        'reportPage' => 'survey',
                                        'title' => 'Select a survey'
                                    ],
                                    [
                                        'reportPage' => 'cohort',
                                        'title' => 'Select a Cohort'
                                    ],
                                    [
                                        'reportPage' => 'filterAttributes',
                                        'title' => 'Select attributes'
                                    ]
                                ],
                                'reportFilter' => [
                                    'participating' => false,
                                    'risk' => true,
                                    'active' => false,
                                    'retentionCompletion' => true,
                                    'activities' => false,
                                    'group' => true,
                                    'course' => true,
                                    'ebi' => true,
                                    'isp' => true,
                                    'static' => true,
                                    'factor' => false,
                                    'survey' => false,
                                    'isq' => false,
                                    'surveyMetadata' => true,
                                    'academicTerm' => false,
                                    'cohort' => false,
                                    'team' => false
                                ],
                                'RequestParam' => 1,
                                'pageurl' => '/generated-reports/SUR-IRR/',
                                'id' => 2,
                                'report_description' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                                'is_batch_job' => true,
                                'is_coordinator_report' => 'n',
                                'report_id' => 2,
                                'campus_info' => [
                                    'organization_id' => 203,
                                    'primary_color' => '#4673a7',
                                    'secondary_color' => '#1e73d5',
                                    'inactivity_timeout' => 45,
                                    'refer_for_academic_assistance' => false,
                                    'send_to_student' => false,
                                    'can_view_in_progress_grade' => false,
                                    'can_view_absences' => false,
                                    'can_view_comments' => false,
                                    'calendar_type' => 'google',
                                    'calendar_sync' => true,
                                    'calendar_sync_users' => 0,
                                    'campus_name' => 'SynapseBetaOrg0099',
                                    'campus_logo' => 'images/default-mw-header-logo.png'
                                ]
                            ],
                            'report_id' => 2
                        ]
                    ),
                    [[
                        'student_count' => 0,
                        'count' => 0,
                        'person_id' => 4878971,
                        'first_name' => '',
                        'last_name' => '',
                        'email' => '',
                        'phone_number' => '',
                        'opted_out' => '',
                        'survey_responded_status' => true,
                        'survey_status' => true
                    ]],
                    [[
                        'student_count' => 0,
                        'count' => 0,
                        'person_id' => 4878971,
                        'first_name' => '',
                        'last_name' => '',
                        'email' => '',
                        'phone_number' => '',
                        'opted_out' => '',
                        'survey_responded_status' => true,
                        'survey_status' => true
                    ]],
                    4878971,
                    'json',
                    1,
                    0,
                    '',
                    'last_name',
                    [
                        'non_participant_count' => 0,
                        'total_records' => 0,
                        'total_pages' => 0.0,
                        'records_per_page' => 25,
                        'current_page' => 1,
                        'percent_responded' => '0 / 0 (0%)',
                        'report_sections' => [
                            'reportId' => 2,
                            'report_name' => 'Individual Response Report',
                            'short_code' => 'SUR-IRR'
                        ],
                        'search_attributes' => [
                            'filterCount' => 2,
                            'survey_filter' =>
                                [
                                    'survey_id' => 17,
                                    'academic_year_name' => '2016-2017 Academic Year',
                                    'org_academic_year_id' => 168,
                                    'year_id' => 201617,
                                    'survey_name' => 'Transition Two',
                                    'cohort' => 1,
                                    'cohort_Name' => 'Survey Cohort 1'
                                ],
                            'org_academic_year_id' => 168,
                            'org_academic_year' => [
                                [
                                    'id' => 168,
                                    'name' => '2016-2017 Academic Year',
                                    'year_id' => 201617,
                                    'start_date' => '2016-08-15',
                                    'end_date' => '2017-08-09',
                                    'can_delete' => false,
                                    'is_current_year' => true
                                ]
                            ],
                            'risk_indicator_date' => '',
                            'risk_indicator_ids' => '',
                            "group_ids" => "377668,346053,377445",
                            'group_names' => ["#clear", "All", "g1", "g2", "g3"],
                            'courses' => [[
                                "term_code" => "Spring2017",
                                "term_name" => "Spring",
                                "college_code" => "123",
                                "year_name" => "2016-2017",
                                "year_id" => '201617',
                                "dept_code" => "123",
                                "subject_code" => "123",
                                "course_name" => "NewCourse",
                                "course_number" => "123",
                                "section_numbers" => []
                            ]],
                            'datablocks' => [],
                            'isps' => [],
                            'static_list_ids' => '',
                            'static_list_names' => [],
                            'retention_completion' => [],
                            'participating' => [
                                'participating_value' => [1],
                                'org_academic_year_id' => [168]
                            ],
                            'student_status' => [
                                'status_value' => [],
                                'org_academic_year_id' => [168]
                            ],
                            'filterText' => 'Survey Filters : 2016-2017 Academic Year Transition Two / Survey Cohort 1, Participating Students : (2016-2017 Academic Year):Yes'
                        ],
                        'report_data' => [$this->getSurveyStatusReportDto([
                            'student_count' => 0,
                            'count' => 0,
                            'person_id' => 4878971,
                            'first_name' => '',
                            'last_name' => '',
                            'email' => '',
                            'phone_number' => '',
                            'opted_out' => '',
                            'responded' => true,
                            'responded_at' => null
                        ])],
                        'request_json' => $this->getCustomSearchDto([
                            'organization_id' => 203,
                            'savedSearchId' => '',
                            'savedSearchName' => '',
                            'dateCreated' => '',
                            'person_id' => 4878971,
                            'search_attributes' => [
                                'filterCount' => 2,
                                'survey_filter' => [
                                    'survey_id' => 17,
                                    'academic_year_name' => '2016-2017 Academic Year',
                                    'org_academic_year_id' => 168,
                                    'year_id' => 201617,
                                    'survey_name' => 'Transition Two',
                                    'cohort' => 1,
                                    'cohort_Name' => 'Survey Cohort 1'
                                ],
                                'org_academic_year_id' => 168,
                                'org_academic_year' => [
                                    [
                                        'id' => 168,
                                        'name' => '2016-2017 Academic Year',
                                        'year_id' => 201617,
                                        'start_date' => '2016-08-15',
                                        'end_date' => '2017-08-09',
                                        'can_delete' => '',
                                        'is_current_year' => 1
                                    ]
                                ],
                                'risk_indicator_date' => '',
                                'risk_indicator_ids' => '',
                                "group_ids" => "377668,346053,377445",
                                'group_names' => ["#clear", "All", "g1", "g2", "g3"],
                                'courses' => [[
                                    "term_code" => "Spring2017",
                                    "term_name" => "Spring",
                                    "college_code" => "123",
                                    "year_name" => "2016-2017",
                                    "year_id" => '201617',
                                    "dept_code" => "123",
                                    "subject_code" => "123",
                                    "course_name" => "NewCourse",
                                    "course_number" => "123",
                                    "section_numbers" => []
                                ]],
                                'datablocks' => [],
                                'isps' => [],
                                'static_list_ids' => '',
                                'static_list_names' => [],
                                'retention_completion' => [],
                                'participating' => [
                                    'participating_value' => [1],
                                    'org_academic_year_id' => [168],
                                ],
                                'student_status' => [
                                    'status_value' => [],
                                    'org_academic_year_id' => [168]
                                ],
                                'filterText' => 'Survey Filters : 2016-2017 Academic Year Transition Two / Survey Cohort 1, Participating Students : (2016-2017 Academic Year):Yes'
                            ],
                            'savedSearches' => '',
                            'activityReport' => '',
                            'report_sections' => [
                                'reportId' => 2,
                                'report_name' => 'Individual Response Report',
                                'short_code' => 'SUR-IRR',
                                'reportDesc' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions.',
                                'reportFilterPages' => [
                                    [
                                        'reportPage' => 'survey',
                                        'title' => 'Select a survey'
                                    ],
                                    [
                                        'reportPage' => 'cohort',
                                        'title' => 'Select a Cohort'
                                    ],
                                    [
                                        'reportPage' => 'filterAttributes',
                                        'title' => 'Select attributes'
                                    ]
                                ],
                                'reportFilter' => [
                                    'participating' => '',
                                    'risk' => 1,
                                    'active' => '',
                                    'retentionCompletion' => 1,
                                    'activities' => '',
                                    'group' => 1,
                                    'course' => 1,
                                    'ebi' => 1,
                                    'isp' => 1,
                                    'static' => 1,
                                    'factor' => '',
                                    'survey' => '',
                                    'isq' => '',
                                    'surveyMetadata' => 1,
                                    'academicTerm' => '',
                                    'cohort' => '',
                                    'team' => '',
                                ],
                                'RequestParam' => 1,
                                'pageurl' => '/generated-reports/SUR-IRR/',
                                'id' => 2,
                                'report_description' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                                'is_batch_job' => true,
                                'is_coordinator_report' => 'n',
                                'report_id' => 2,
                                'campus_info' => [
                                    'organization_id' => 203,
                                    'primary_color' => '#4673a7',
                                    'secondary_color' => '#1e73d5',
                                    'inactivity_timeout' => 45,
                                    'refer_for_academic_assistance' => '',
                                    'send_to_student' => '',
                                    'can_view_in_progress_grade' => '',
                                    'can_view_absences' => '',
                                    'can_view_comments' => '',
                                    'calendar_type' => 'google',
                                    'calendar_sync' => 1,
                                    'calendar_sync_users' => 0,
                                    'campus_name' => 'SynapseBetaOrg0099',
                                    'campus_logo' => 'images/default-mw-header-logo.png'
                                ]
                            ],
                            'report_id' => 2,
                            'selectedAttributesCsv' => '',
                            'searchType' => ''
                        ]),
                        'person_id' => 4878971,
                        'report_by' => [
                            'first_name' => '',
                            'last_name' => ''
                        ],
                        'report_date' => $this->getDateTime('2017-10-10T06:00:15+0000')
                    ]
                ]
            ]
        ]);
    }

    private function getCustomSearchDto($saveSearchValuesArray)
    {
        $saveSearchDto = new SaveSearchDto();
        $saveSearchDto->setOrganizationId($saveSearchValuesArray['organization_id']);
        $saveSearchDto->setPersonId($saveSearchValuesArray['person_id']);
        $saveSearchDto->setSearchAttributes($saveSearchValuesArray['search_attributes']);
        $saveSearchDto->setReportSections($saveSearchValuesArray['report_sections']);
        $saveSearchDto->setReportId($saveSearchValuesArray['report_id']);
        return $saveSearchDto;
    }

    private function getDateTime($date)
    {
        $dateTimeObject = new \DateTime($date);
        $dateTime = date_format($dateTimeObject, "Y-m-d\TH:i:sO");
        return $dateTime;
    }

    private function getSurveyStatusReportDto($surveyStatusReport)
    {
        $surveyStatusReportDto = new SurveyStatusReportDto();
        $surveyStatusReportDto->setStudentId($surveyStatusReport['person_id']);
        $surveyStatusReportDto->setFirstName($surveyStatusReport['first_name']);
        $surveyStatusReportDto->setLastName($surveyStatusReport['last_name']);
        $surveyStatusReportDto->setEmail($surveyStatusReport['email']);
        $surveyStatusReportDto->setPhoneNumber($surveyStatusReport['phone_number']);
        $surveyStatusReportDto->setOptedOut($surveyStatusReport['opted_out']);
        $surveyStatusReportDto->setResponded($surveyStatusReport['responded']);
        $surveyStatusReportDto->setRespondedAt($surveyStatusReport['responded_at']);
        return $surveyStatusReportDto;
    }

    private $reportData = [
        "reportId" => 3,
        "report_name" => "All Academic Updates Report",
        "short_code" => "AU-R",
        "reportDesc" => "See all academic updates for your students.  Export to csv, perform individual or bulk actions.",
        "reportFilterPages" => [
            "0" => [
                "reportPage" => "filterAttributes",
                "title" => "Select Optional Report Filters"
            ]
        ],
        "reportFilter" => [
            "participating" => null,
            "risk" => 1,
            "retentionCompletion" => 1,
            "active" => null,
            "activities" => null,
            "group" => 1,
            "course" => 1,
            "ebi" => 1,
            "isp" => 1,
            "static" => 1,
            "factor" => null,
            "survey" => null,
            "isq" => null,
            "surveyMetadata" => null,
            "academicTerm" => 1,
            "cohort" => null,
            "team" => null
        ],
        "RequestParam" => 2,
        "pageurl" => "/generated-reports/AU-R/",
        "id" => 3,
        "report_description" => "See all academic updates for your students.  Export to csv, perform individual or bulk actions",
        "is_batch_job" => 1,
        "is_coordinator_report" => "n",
        "report_id" => 3,
        "campus_info" => [
            "organization_id" => 62,
            "primary_color" => "#4673a7",
            "secondary_color" => "#1e73d5",
            "inactivity_timeout" => 60,
            "refer_for_academic_assistance" => 1,
            "send_to_student" => 1,
            "can_view_in_progress_grade" => 1,
            "can_view_absences" => 1,
            "can_view_comments" => 1,
            "calendar_type" => "google",
            "calendar_sync" => 1,
            "calendar_sync_users" => 7,
            "campus_name" => "Synapse Beta Org0062",
            "campus_logo" => "images/default-mw-header-logo.png"
        ]
    ];
    private $studentList = [
        "0" =>
            [
                "student_id" => 5056105,
                "first_name" => "01kstd21june",
                "last_name" => "01kstd21june"
            ],
        "1" =>
            [
                "student_id" => 4956587,
                "first_name" => "Angel",
                "last_name" => "Abbott"
            ],
        "2" =>
            [
                "student_id" => 4958587,
                "first_name" => "Jace",
                "last_name" => "Abbott"
            ],
        "3" =>
            [
                "student_id" => 4953587,
                "first_name" => "Madelyn",
                "last_name" => "Abbott"
            ],
        "4" =>
            [
                "student_id" => 4955587,
                "first_name" => "Madison",
                "last_name" => "Abbott"
            ],
        "5" =>
            [
                "student_id" => 4957587,
                "first_name" => "Maya",
                "last_name" => "Abbott"
            ]
    ];
    private $allAcademicUpdateIds = [
        "0" => 6826421,
        "1" => 6826394,
        "2" => 6826349,
        "3" => 6826340,
        "4" => 6826320,
        "5" => 6826305
    ];
    private $allAcademicUpdateReportData = [
        "0" => [
            "student_id" => 5056105,
            "external_id" => "01kstd21june",
            "student_first_name" => "01kstd21june",
            "student_last_name" => "01kstd21june",
            "email" => "01kstd21june@mailinator.com",
            "course_id" => 378895,
            "course_name" => "int33int33",
            "faculty_id" => 5056049,
            "faculty_first_name" => "Pulkit",
            "faculty_last_name" => "CoordinatorIntegration",
            "academic_update_id" => 6826192,
            "created_at" => "2017-08-21 06:21:35",
            "failure_risk" => "High",
            "inprogress_grade" => "P",
            "absences" => 68,
            "by_request" => 0,
            "comment" => "iuiyiu",
            "class_level" => null,
            "student_status" => 1,
            "risk_imagename" => "risk-level-icon-gray.png",
            "risk_text" => "gray",
            "term_id" => 638,
            "term_name" => 1,
            "update_type" => "By request",
            "risk_flag" => 1
        ],
        "1" => [
            "student_id" => 5056105,
            "external_id" => "01kstd21june",
            "student_first_name" => "01kstd21june",
            "student_last_name" => "01kstd21june",
            "email" => "01kstd21june@mailinator.com",
            "course_id" => "378895",
            "course_name" => "int33int33",
            "faculty_id" => "5056049",
            "faculty_first_name" => "Pulkit",
            "faculty_last_name" => "CoordinatorIntegration",
            "academic_update_id" => "6826165",
            "created_at" => "2017-08-21 06:21:35",
            "failure_risk" => "High",
            "inprogress_grade" => "P",
            "absences" => 60,
            "by_request" => 0,
            "comment" => "iuiyiu",
            "class_level" => null,
            "student_status" => 1,
            "risk_imagename" => "risk-level-icon-gray.png",
            "risk_text" => "gray",
            "term_id" => 638,
            "term_name" => 1,
            "update_type" => "By request",
            "risk_flag" => 1
        ],
        "2" => [
            "student_id" => 5056105,
            "external_id" => "01kstd21june",
            "student_first_name" => "01kstd21june",
            "student_last_name" => "01kstd21june",
            "email" => "01kstd21june@mailinator.com",
            "course_id" => 378895,
            "course_name" => "int33int33",
            "faculty_id" => 5056049,
            "faculty_first_name" => "Pulkit",
            "faculty_last_name" => "CoordinatorIntegration",
            "academic_update_id" => 6826164,
            "created_at" => "2017-08-21 06:21:35",
            "failure_risk" => "High",
            "inprogress_grade" => "P",
            "absences" => null,
            "by_request" => 0,
            "comment" => "iuiyiu",
            "class_level" => null,
            "student_status" => 1,
            "risk_imagename" => "risk-level-icon-gray.png",
            "risk_text" => "gray",
            "term_id" => 638,
            "term_name" => 1,
            "update_type" => "By request",
            "risk_flag" => 1
        ],
        "3" => [
            "student_id" => "5056105",
            "external_id" => "01kstd21june",
            "student_first_name" => "01kstd21june",
            "student_last_name" => "01kstd21june",
            "email" => "01kstd21june@mailinator.com",
            "course_id" => 378895,
            "course_name" => "int33int33",
            "faculty_id" => 5056049,
            "faculty_first_name" => "Pulkit",
            "faculty_last_name" => "CoordinatorIntegration",
            "academic_update_id" => 6826237,
            "created_at" => "2017-08-24 12:02:40",
            "failure_risk" => "Low",
            "inprogress_grade" => null,
            "absences" => 0,
            "by_request" => 0,
            "comment" => null,
            "class_level" => null,
            "student_status" => 1,
            "risk_imagename" => "risk-level-icon-gray.png",
            "risk_text" => "gray",
            "term_id" => 638,
            "term_name" => 1,
            "update_type" => "adhoc",
            "risk_flag" => 1
        ],
        "4" => [
            "student_id" => 5056105,
            "external_id" => "01kstd21june",
            "student_first_name" => "01kstd21june",
            "student_last_name" => "01kstd21june",
            "email" => "01kstd21june@mailinator.com",
            "course_id" => 378895,
            "course_name" => "int33int33",
            "faculty_id" => 5056049,
            "faculty_first_name" => "Pulkit",
            "faculty_last_name" => "CoordinatorIntegration",
            "academic_update_id" => 6826340,
            "created_at" => "2017-09-07 19:24:45",
            "failure_risk" => "High",
            "inprogress_grade" => "A",
            "absences" => 11,
            "by_request" => 0,
            "comment" => 11,
            "class_level" => null,
            "student_status" => 1,
            "risk_imagename" => "risk-level-icon-gray.png",
            "risk_text" => "gray",
            "term_id" => 638,
            "term_name" => 1,
            "update_type" => "adhoc",
            "risk_flag" => 1
        ],
        "5" => [
            "student_id" => 5056105,
            "external_id" => "01kstd21june",
            "student_first_name" => "01kstd21june",
            "student_last_name" => "01kstd21june",
            "email" => "01kstd21june@mailinator.com",
            "course_id" => 378895,
            "course_name" => "int33int33",
            "faculty_id" => 5056049,
            "faculty_first_name" => "Pulkit",
            "faculty_last_name" => "CoordinatorIntegration",
            "academic_update_id" => 6826320,
            "created_at" => "2017-09-07 19:24:45",
            "failure_risk" => "High",
            "inprogress_grade" => "A",
            "absences" => null,
            "by_request" => 0,
            "comment" => 11,
            "class_level" => null,
            "student_status" => 1,
            "risk_imagename" => "risk-level-icon-gray.png",
            "risk_text" => "gray",
            "term_id" => 638,
            "term_name" => 1,
            "update_type" => "By request",
            "risk_flag" => 1
        ]
    ];
    private $riskIndentData = [
        "0" => [
            "student_id" => 4953587,
            "permissionset_id" => 361,
            "intent_flag" => 1,
            "risk_flag" => 1
        ],
        "1" => [
            "student_id" => 4955587,
            "permissionset_id" => 361,
            "intent_flag" => 1,
            "risk_flag" => 1
        ],
        "2" => [
            "student_id" => 4956587,
            "permissionset_id" => 361,
            "intent_flag" => 1,
            "risk_flag" => 1
        ],
        "3" => [
            "student_id" => 4957587,
            "permissionset_id" => 361,
            "intent_flag" => 1,
            "risk_flag" => 1
        ],
        "4" => [
            "student_id" => 4958587,
            "permissionset_id" => 361,
            "intent_flag" => 1,
            "risk_flag" => 1
        ],
        "5" => [
            "student_id" => 5056105,
            "permissionset_id" => 361,
            "intent_flag" => 1,
            "risk_flag" => 1
        ]
    ];
}



