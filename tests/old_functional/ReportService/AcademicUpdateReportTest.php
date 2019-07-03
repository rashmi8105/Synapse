<?php
use Codeception\Util\Stub;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;

class AcademicUpdateReportTest extends \Codeception\TestCase\Test
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
    private $invalidOrg = -10;
    private $surveyId = 1;
    private $cohortId = 1;
    private $reportId = 3;
    private $pageNo = 1;
    private $offset = 25;
    
    private $reportsService;
    
    public function _before() {
        $this->container = $this->getModule ( 'Symfony2' )->container;
        $this->reportsService = $this->container->get ( 'reports_service' );
    }
    
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->personId);
    }
    
    public function testGetAcademicUpdateReport()
    {
        $this->initializeRbac();
        $reportDto = $this->createReportRequestDto();
        $reportData = $this->reportsService->getAcademicUpdateReport($reportDto, $this->personId, 'json', $this->pageNo, $this->offset, '', 'student_last_name');

        $this->assertInternalType('array', $reportData);
        $this->assertNotNull($reportData['total_records']);
        $this->assertNotNull($reportData['total_pages']);
        $this->assertEquals($this->pageNo, $reportData['current_page']);
        $this->assertNotNull($reportData['report_data']);
        $this->assertEquals($this->offset, $reportData['records_per_page']);
        
        if(count($reportData['report_data']) > 0){
            foreach ($reportData['report_data'] as $data){
                $this->assertInstanceOf("Synapse\ReportsBundle\EntityDto\AcademicUpdateReportDto", $data);
                $this->assertNotNull($data->getStudentId());
                $this->assertNotNull($data->getStudentFirstName());
                $this->assertNotNull($data->getStudentLastName());
                $this->assertNotNull($data->getCourseName());
            }            
        }
    }
    
    public function testGetAcademicUpdateReportStudentList()
    {
        $this->initializeRbac();
        $reportDto = $this->createReportRequestDto();
        $reportData = $this->reportsService->getAcademicUpdateReport($reportDto, $this->personId, 'json', $this->pageNo, $this->offset, 'student_list');

        $this->assertInstanceOf("Synapse\SearchBundle\EntityDto\SearchDto", $reportData);
        $this->assertEquals($this->personId, $reportData->getPersonId());
    
        if(count($reportData->getSearchResult()) > 0){
            foreach ($reportData->getSearchResult() as $data){
                $this->assertInstanceOf("Synapse\SearchBundle\EntityDto\SearchResultListDto", $data);
                $this->assertNotNull($data->getStudentId());
                $this->assertNotNull($data->getStudentFirstName());
                $this->assertNotNull($data->getStudentLastName());
            }
        }
    }
    
    public function testGetAcademicUpdateReportWithAUFilter()
    {
        $this->initializeRbac();
        $reportDto = $this->createReportRequestDto();
        $searchAttributes = $reportDto->getSearchAttributes();
        $academicFilter['term_ids'] = "3";
        $academicFilter['is_current_academic_year'] = true;
        $academicFilter['failure_risk'] = "high";
        $academicFilter['grade'] = "B";
        $academicFilter['absences'] = "2";
        $academicFilter['final_grade'] = "C";
        $searchAttributes['academic_updates'] = $academicFilter;
        
        $reportDto->setSearchAttributes($searchAttributes);
        $reportData = $this->reportsService->getAcademicUpdateReport($reportDto, $this->personId, 'json', $this->pageNo, $this->offset);
    
        $this->assertInternalType('array', $reportData);
        $this->assertNotNull($reportData['total_records']);
        $this->assertNotNull($reportData['total_pages']);
        $this->assertEquals($this->pageNo, $reportData['current_page']);
        $this->assertNotNull($reportData['report_data']);
        $this->assertEquals($this->offset, $reportData['records_per_page']);
        
        if(count($reportData['report_data']) > 0){
            foreach ($reportData['report_data'] as $data){
                $this->assertInstanceOf("Synapse\ReportsBundle\EntityDto\AcademicUpdateReportDto", $data);
                $this->assertNotNull($data->getStudentId());
                $this->assertNotNull($data->getStudentFirstName());
                $this->assertNotNull($data->getStudentLastName());
                $this->assertNotNull($data->getCourseName());
            }            
        }
    }
    
    public function testGetAcademicUpdateReportWithAUFilterAbsenceRange()
    {
        $this->initializeRbac();
        $reportDto = $this->createReportRequestDto();
        $searchAttributes = $reportDto->getSearchAttributes();
        $academicFilter['absence_range']['min_range'] = "1";
        $academicFilter['absences'] = "";
        $academicFilter['failure_risk'] = "low";
        $academicFilter['absence_range']['max_range'] = "5";
        $academicFilter['final_grade'] = "C";
        $academicFilter['is_current_academic_year'] = true;
        $searchAttributes['academic_updates'] = $academicFilter;
        $searchAttributes['student_status'] = "0";
    
        $reportDto->setSearchAttributes($searchAttributes);
        $reportData = $this->reportsService->getAcademicUpdateReport($reportDto, $this->personId);
    
        $this->assertInternalType('array', $reportData);
        $this->assertNotNull($reportData['total_records']);
        $this->assertNotNull($reportData['total_pages']);
        $this->assertEquals($this->pageNo, $reportData['current_page']);
        $this->assertNotNull($reportData['report_data']);
        $this->assertEquals($this->offset, $reportData['records_per_page']);
    
        if(count($reportData['report_data']) > 0){
            foreach ($reportData['report_data'] as $data){
                $this->assertInstanceOf("Synapse\ReportsBundle\EntityDto\AcademicUpdateReportDto", $data);
                $this->assertNotNull($data->getStudentId());
                $this->assertNotNull($data->getStudentFirstName());
                $this->assertNotNull($data->getStudentLastName());
                $this->assertNotNull($data->getCourseName());
            }
        }
    }
    
    private function createReportRequestDto(){
    
        $reportDto = new SaveSearchDto();
        $reportDto->setOrganizationId($this->organizationId);
        $reportDto->setPersonId($this->personId);
    
        $searchAttributes['academic_updates']['grade'] = "";
        $searchAttributes['academic_updates']['final_grade'] = "";
        $searchAttributes['academic_updates']['absences'] = "";
        $searchAttributes['academic_updates']['term_ids'] = "3";
        $searchAttributes['student_status'] = "1";
        $searchAttributes['academic_updates']['is_current_academic_year'] = true;

        $searchAttributes['group_ids'] = "";
        $reportDto->setSearchAttributes($searchAttributes);
    
        $reportSections['reportId'] = $this->reportId;
        $reportSections['report_name'] = 'All Academic Updates Report';
        $reportSections['short_code'] = "AU-R";
        $reportDto->setReportSections($reportSections);
    
        return $reportDto;
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetAcademicUpdateReportInvalidOrganization()
    {
        $this->initializeRbac();
        $reportDto = $this->createReportRequestDto();
        
        $reportDto->setOrganizationId($this->invalidOrg);
        $reportData = $this->reportsService->getAcademicUpdateReport($reportDto, $this->personId, 'json', $this->pageNo, $this->offset, '', 'student_last_name');
    }
}