<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;

class IndividualResponseReportTest extends \Codeception\TestCase\Test
{
    /**
     *
     * @var UnitTester
     */
    protected $tester;
    
    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;
    
    private $personId = 1;
    private $organizationId = 1;
    private $surveyId = 1;
    private $cohortId = 1;
    private $reportId = 2;
    private $pageNo = 1;
    private $offset = 25;
    
    private $reportsService;
    
    public function _before() {
        $this->container = $this->getModule ( 'Symfony2' )->container;
        $this->reportsService = $this->container->get ( 'reports_service' );
    }
    
    public function testGetSurveyStatusReport()
    {
        $reportDto = $this->createReportRequestDto();
        $reports = $this->reportsService->getSurveyStatusReport($reportDto, $this->personId, 'json', $this->pageNo, $this->offset);

        $this->assertInternalType('array', $reports);
        $this->assertNotNull($reports['total_records']);
        $this->assertNotNull($reports['total_pages']);
        $this->assertNotNull($reports['percent_responded']);
        $this->assertEquals($this->pageNo, $reports['current_page']);
        $this->assertEquals($this->offset, $reports['records_per_page']);
        $this->assertEquals($this->organizationId, $reports['request_json']->getOrganizationId());
        $this->assertEquals($this->personId, $reports['request_json']->getPersonId());
    }
    
    public function testGetSurveyStatusReportWithData()
    {
        $reportDto = $this->createReportRequestDto();
        
        $searchAttributes = $reportDto->getSearchAttributes();
        $searchAttributes['survey_filter']['responded_date'] = "";
        $searchAttributes['survey_filter']['opted_out'] = "";
        $reportDto->setSearchAttributes($searchAttributes);
        $reports = $this->reportsService->getSurveyStatusReport($reportDto, $this->personId, 'json', $this->pageNo, $this->offset);

        $this->assertInternalType('array', $reports);
        $this->assertNotNull($reports['total_records']);
        $this->assertNotNull($reports['total_pages']);
        $this->assertNotNull($reports['percent_responded']);
        $this->assertEquals($this->pageNo, $reports['current_page']);
        $this->assertEquals($this->offset, $reports['records_per_page']);
        $this->assertEquals($this->organizationId, $reports['request_json']->getOrganizationId());
        $this->assertEquals($this->personId, $reports['request_json']->getPersonId());
        
        if(count($reports['report_data']) > 0){
            foreach($reports['report_data'] as $reportData){
                $this->assertNotEmpty($reportData->getStudentId());
                $this->assertNotEmpty($reportData->getFirstName());
                $this->assertNotEmpty($reportData->getLastName());
                $this->assertNotEmpty($reportData->getEmail());
                $this->assertNotEmpty($reportData->getOptedOut());
                $this->assertNotEmpty($reportData->getResponded());
            }
        }
    }
    
    public function testGetSurveyStatusReportWithDataStudentList()
    {
        $reportDto = $this->createReportRequestDto();
    
        $searchAttributes = $reportDto->getSearchAttributes();
        $searchAttributes['survey_filter']['responded_date'] = "";
        $searchAttributes['survey_filter']['opted_out'] = "";
        $reportDto->setSearchAttributes($searchAttributes);
        $reports = $this->reportsService->getSurveyStatusReport($reportDto, $this->personId, 'json', $this->pageNo, $this->offset, 'student_list');
    
        $this->assertInstanceOf('Synapse\SearchBundle\EntityDto\SearchDto', $reports);
        $this->assertEquals($this->personId, $reports->getPersonId());
        if(count($reports->getSearchResult()) > 0){
            
            foreach($reports->getSearchResult() as $student){
                
                $this->assertNotEmpty($student->getStudentId());
                $this->assertNotEmpty($student->getStudentFirstname());
                $this->assertNotEmpty($student->getStudentLastName());
            }
        }
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetSurveyStatusReportNoCoordinatorAccess()
    {
        $reportDto = $this->createReportRequestDto();
        $reportDto->setPersonId(3);
        
        $reports = $this->reportsService->getSurveyStatusReport($reportDto, 3, 'json');
    }
    
    private function createReportRequestDto(){
        
        $reportDto = new SaveSearchDto();
        $reportDto->setOrganizationId($this->organizationId);
        $reportDto->setPersonId($this->personId);

        $searchAttributes['survey_filter']['survey_id'] = $this->surveyId;
        $searchAttributes['survey_filter']['academic_year_id'] = "201516";
        $searchAttributes['survey_filter']['cohort_id'] = $this->cohortId;
        $searchAttributes['survey_filter']['responded_date'] = "2015-12-30";
        $searchAttributes['survey_filter']['opted_out'] = "true";
        $searchAttributes['group_ids'] = "";
        $reportDto->setSearchAttributes($searchAttributes);
        
        $reportSections['reportId'] = $this->reportId;
        $reportSections['report_name'] = 'Individual Response Report';
        $reportSections['short_code'] = "SUR-IRR";
        $reportDto->setReportSections($reportSections);
        
        return $reportDto;
    }
    
    public function testGetSurveyStatusReportWithDataSort()
    {
        $reportDto = $this->createReportRequestDto();
    
        $searchAttributes = $reportDto->getSearchAttributes();
        $searchAttributes['survey_filter']['responded_date'] = "";
        $searchAttributes['survey_filter']['opted_out'] = "";
        $reportDto->setSearchAttributes($searchAttributes);
        $reports = $this->reportsService->getSurveyStatusReport($reportDto, $this->personId, 'json', $this->pageNo, $this->offset, '', 'student_last_name');
    
        $this->assertInternalType('array', $reports);
        $this->assertNotNull($reports['total_records']);
        $this->assertNotNull($reports['total_pages']);
        $this->assertNotNull($reports['percent_responded']);
        $this->assertEquals($this->pageNo, $reports['current_page']);
        $this->assertEquals($this->offset, $reports['records_per_page']);
        $this->assertEquals($this->organizationId, $reports['request_json']->getOrganizationId());
        $this->assertEquals($this->personId, $reports['request_json']->getPersonId());
    
        if(count($reports['report_data']) > 0){
            foreach($reports['report_data'] as $reportData){
                $this->assertNotEmpty($reportData->getStudentId());
                $this->assertNotEmpty($reportData->getFirstName());
                $this->assertNotEmpty($reportData->getLastName());
                $this->assertNotEmpty($reportData->getEmail());
                $this->assertNotEmpty($reportData->getOptedOut());
                $this->assertNotEmpty($reportData->getResponded());
            }
        }
    }
}