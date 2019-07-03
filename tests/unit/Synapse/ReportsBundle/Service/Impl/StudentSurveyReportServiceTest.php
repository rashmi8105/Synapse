<?php
namespace Synapse\ReportsBundle\Service\Impl;

use Codeception\TestCase\Test;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CampusConnectionBundle\Service\Impl\CampusConnectionService;
use Synapse\CampusResourceBundle\Service\Impl\CampusResourceService;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\ReportsBundle\Repository\OrgCalcFlagsStudentReportsRepository;
use Synapse\ReportsBundle\Repository\ReportCalculatedValuesRepository;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportTipsRepository;
use Synapse\SurveyBundle\Repository\OrgPersonStudentSurveyLinkRepository;

class StudentSurveyReportServiceTest extends Test
{
    use \Codeception\Specify;

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


    private $organizationName = "TestOrganization";

    private $surveyList = [

        [
            'survey_id' => 1,
            'survey_name' => "survey1",
            'year_id' => "201415",
            'open_date' => "2015-03-01",
            'survey_completion_status' => "CompletedAll"

        ],
        [
            'survey_id' => 1,
            'survey_name' => "survey2",
            'year_id' => "201415",
            'open_date' => "2015-04-01",
            'survey_completion_status' => "Assigned"

        ],

    ];

    private $elementArray = [
        [
            'element_id' => 1,
            'element_name' => "sectionTitle",
            'element_icon' => "SomeIcon"
        ]
    ];

    private $tipsArray = [
        [
            'title' => "tips",
            'description' => "tip-description",
        ]
    ];

    private $sectionData = [
        [
            'section_id' => 1,
            'title' => "sextionTitle"
        ]
    ];


    protected function _before()
    {
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
        $this->mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
        $this->mockContainer = $this->getMock('Container', ['get']);
    }


    public function testGetStudentReport()
    {
        $this->specify("Test GetStudent Report ", function ($organizationId, $studentId, $reportType, $invalidStudent = false, $invalidReport = false, $expectedErrorMessage = "") {

            $mockReport = $this->getMock("Synapse\ReportsBundle\Entity\Reports", ['getId']);
            $mockReport->method('getId')->willReturn(1);


            $mockOrganization = $this->getMock('organization', ['getId', 'getOrganizationName', 'getLogoFileName', 'getPrimaryColor']);


            $mockOrganizationLang = $this->getMock("OrganizationLang", ['getOrganization', 'getOrganizationName']);
            $mockOrganizationLang->method('getOrganization')->willReturn($mockOrganization);
            $mockOrganizationLang->method('getOrganizationName')->willReturn($this->organizationName);

            $mockAcademicYearService = $this->getMock('AcademicYearService', ['getCurrentOrgAcademicYearId']);
            $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn(1);


            $mockOrgPersonStudentSurveyLinkRepository = $this->getMock('orgPersonStudentSurveyLinkRepository', ['listSurveysForStudent']);
            $mockOrgPersonStudentSurveyLinkRepository->method('listSurveysForStudent')->willReturn($this->surveyList);


            $mockReportsTipRepository = $this->getMock('ReportsTipRepository', ['getTipsForSection']);

            $mockReportsTipRepository->method('getTipsForSection')->willReturn($this->tipsArray);

            $mockReportCalculatedValuesRepository = $this->getMock("reportCalculatedValuesRepository", ['getReportDetailsByRepId', 'getReportElementDetailBySecId', 'getElementBucketByElementName']);

            $mockReportCalculatedValuesRepository->method('getReportDetailsByRepId')->willReturn($this->sectionData);

            $mockReportCalculatedValuesRepository->method('getReportElementDetailBySecId')->willReturn(
                [
                    'element_id' => 1,
                    'element_Name' => "sextionTitle",
                    'element_icon' => "SomeIcon"
                ]
            );

            $mockReportCalculatedValuesRepository->method('getElementBucketByElementName')->willReturn(
                [
                    [
                        'element_color' => "red",
                        'element_score' => 1,
                        'element_text' => "Element",
                        'survey_id' => 1,
                    ]
                ]

            );

            $mockDataProcessingUtilityService = $this->getMock("dataProcessingUtilityService", ['removeDuplicateElements']);
            $mockDataProcessingUtilityService->method('removeDuplicateElements')->willReturn($this->elementArray);

            $mockCampusResourceService = $this->getMock("CampusResourceService", ['getCampusResources']);
            $mockCampusResourceService->method('getCampusResources')->willReturn([]);

            $mockCampusConnection = $this->getMock("CampusConnectionService", ['getCampusConnections']);
            $mockCampusConnection->method('getCampusConnections')->willReturn([]);


            $mockCampusConnectionService = $this->getMock('campusConnectionService', ['getStudentCampusConnections']);
            $mockCampusConnectionService->method('getStudentCampusConnections')->willReturn(
                $mockCampusConnection
            );

            $mockReportsRepository = $this->getMock('ReportsRepository', ['findOneBy']);
            if ($invalidReport) {
                $mockReportsRepository->method('findOneBy')->willReturn(null);
            } else {
                $mockReportsRepository->method('findOneBy')->willReturn($mockReport);
            }


            $mockorganziationLangRepository = $this->getMock('OrganizationLangRepository', ['findOneBy']);
            $mockorganziationLangRepository->method('findOneBy')->willReturn($mockOrganizationLang);

            $mockPersonRepository = $this->getMock('PersonRepository', ['getUsersByUserIds']);
            if (!$invalidStudent) {
                $mockPersonRepository->method('getUsersByUserIds')->willReturn([
                        [
                            'user_id' => 1,
                            'user_firstname' => "firstname",
                            'user_lastname' => "lastname",
                            'user_email' => "abc@mailinator.com",
                        ]
                    ]
                );
            } else {
                $mockPersonRepository->method('getUsersByUserIds')->willReturn([
                        []
                    ]
                );
            }


            $mockOrgCalcFlagsStudentReportsRepository = $this->getMock('OrgCalcFlagsStudentReportsRepository', ['getLastCalculatedAtDateForStudent']);
            $mockOrgCalcFlagsStudentReportsRepository->method('getLastCalculatedAtDateForStudent')->willReturn(new \DateTime('2015-03-03'));

            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    ReportsRepository::REPOSITORY_KEY,
                    $mockReportsRepository
                ],
                [
                    PersonRepository::REPOSITORY_KEY,
                    $mockPersonRepository
                ],
                [
                    OrgPersonStudentSurveyLinkRepository::REPOSITORY_KEY,
                    $mockOrgPersonStudentSurveyLinkRepository
                ],
                [
                    ReportTipsRepository::REPOSITORY_KEY,
                    $mockReportsTipRepository
                ],
                [
                    OrganizationlangRepository::REPOSITORY_KEY,
                    $mockorganziationLangRepository
                ],
                [
                    ReportCalculatedValuesRepository::REPOSITORY_KEY,
                    $mockReportCalculatedValuesRepository
                ],
                [
                    OrgCalcFlagsStudentReportsRepository::REPOSITORY_KEY,
                    $mockOrgCalcFlagsStudentReportsRepository
                ]
            ]);

            $this->mockContainer->method('get')->willReturnMap([
                [
                    CampusConnectionService::SERVICE_KEY,
                    $mockCampusConnectionService
                ],
                [
                    CampusResourceService::SERVICE_KEY,
                    $mockCampusResourceService
                ],
                [
                    DataProcessingUtilityService::SERVICE_KEY,
                    $mockDataProcessingUtilityService
                ],
                [
                    AcademicYearService::SERVICE_KEY,
                    $mockAcademicYearService
                ]

            ]);

            $studentSurveyReportService = new StudentSurveyReportService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            try {
                $results = $studentSurveyReportService->getStudentReport($organizationId, $studentId, $reportType);
                $studentReportArray = $results->getStudentReport();
                $studentReport = $studentReportArray[0];

                $this->assertInstanceOf('Synapse\ReportsBundle\EntityDto\ReportDto', $results);
                $this->assertInstanceOf('Synapse\ReportsBundle\EntityDto\StudentReportDto', $studentReport);

                $this->assertEquals($studentReport->getCampusInfo()['campus_name'], $this->organizationName);
                $surveyInfo = $studentReport->getSurveyInfo();
                foreach ($surveyInfo as $surveyKey => $survey) {
                    $this->assertInstanceOf('Synapse\ReportsBundle\EntityDto\StudentSurveyInfoDto', $survey);
                    $this->assertEquals($survey->getId(), $this->surveyList[$surveyKey]['survey_id']);
                    $this->assertEquals($survey->getSurveyName(), $this->surveyList[$surveyKey]['survey_name']);
                    $this->assertEquals($survey->getYear(), $this->surveyList[$surveyKey]['year_id']);
                    $this->assertEquals($survey->getStartDate(), $this->surveyList[$surveyKey]['open_date']);
                    $this->assertEquals($survey->getSurveyStatus(), $this->surveyList[$surveyKey]['survey_completion_status']);
                }

                $reportsSections = $studentReport->getReportSections();
                foreach ($reportsSections as $reportSection) {

                    $this->assertEquals($reportSection->getSectionId(), 1);

                    $elements = $reportSection->getElements();

                    foreach ($elements as $elemetKey => $element) {
                        $this->assertInstanceOf('Synapse\ReportsBundle\EntityDto\ElementDto', $element);
                        $this->assertEquals($element->getElementId(), $this->elementArray[$elemetKey]['element_id']);
                        $this->assertEquals($element->getElementName(), $this->elementArray[$elemetKey]['element_name']);
                        $this->assertEquals($element->getElementIcon(), $this->elementArray[$elemetKey]['element_icon']);
                    }
                    $tips = $reportSection->getTips();
                    foreach ($tips as $tipKey => $tip) {
                        $this->assertInstanceOf('Synapse\ReportsBundle\EntityDto\ElementDto', $element);
                        $this->assertEquals($tip->getTipsName(), $this->tipsArray[$tipKey]['title']);
                        $this->assertEquals($tip->getTipsDescription(), $this->tipsArray[$tipKey]['description']);
                    }
                }

            } catch (\Exception $e) {
                $this->assertEquals($e->getMessage(), $expectedErrorMessage);
            }
        }, [
            'examples' => [
                //Valid data for generating student report
                [
                    $organizationId = 1, $studentId = 1, $reportType = "student-report"
                ],
                // Invalid report type, throws exception
                [
                    $organizationId = 1, $studentId = 1, $reportType = "InvalidReport", false, true, "Report type InvalidReport not found."
                ],
                //Invalid Student id, throws exception
                [
                    $organizationId = 1, $studentId = -11, $reportType = "InvalidReport", true, false, "Person ID Not Found."
                ]
            ]
        ]);
    }

}