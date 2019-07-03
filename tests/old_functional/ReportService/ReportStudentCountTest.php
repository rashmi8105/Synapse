<?php
use Codeception\Util\Stub;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;

class ReportStudentCountTest extends \Codeception\TestCase\Test
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
    
    public function testGetStudentCountBasedCriteria()
    {
        $this->initializeRbac();
        $reportDto = $this->createReportRequestDto();
        $reportData = $this->reportsService->getStudentCountBasedCriteria($reportDto, $this->personId, 'AU-R');

        $this->assertInternalType('array', $reportData);
        $this->assertEquals($this->organizationId, $reportData['organization_id']);
        $this->assertEquals($this->personId, $reportData['person_id']);
        $this->assertNotNull($reportData['student_count']);
    }
    
    public function testGetStudentCountBasedCriteriaNoAUFilter()
    {
        $this->initializeRbac();
        $reportDto = $this->createReportRequestDto();
        $searchAttributes['group_ids'] = "";
        $reportDto->setSearchAttributes($searchAttributes);
        $reportData = $this->reportsService->getStudentCountBasedCriteria($reportDto, $this->personId, 'AU-R');

        $this->assertInternalType('array', $reportData);
        $this->assertEquals($this->organizationId, $reportData['organization_id']);
        $this->assertEquals($this->personId, $reportData['person_id']);
        $this->assertNotNull($reportData['student_count']);
    }
    
    public function testGetStudentCountBasedCriteriaWithSurveySnapshotCode()
    {
        $this->initializeRbac();
        $reportDto = $this->createReportRequestDto();
        $reportData = $this->reportsService->getStudentCountBasedCriteria($reportDto, $this->personId, 'SUR-SR');
        
        $this->assertInternalType('array', $reportData);
        $this->assertEquals($this->organizationId, $reportData['organization_id']);
        $this->assertEquals($this->personId, $reportData['person_id']);
        $this->assertNotNull($reportData['student_count']);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetStudentCountBasedCriteriaInvalidOrg()
    {
        $this->initializeRbac();
        $reportDto = $this->createReportRequestDto();
        
        $reportDto->setOrganizationId($this->invalidOrg);
        $reportData = $this->reportsService->getStudentCountBasedCriteria($reportDto, $this->personId, 'SUR-SR');
    }
    
    private function createReportRequestDto(){
    
        $reportDto = new SaveSearchDto();
        $reportDto->setOrganizationId($this->organizationId);
        $reportDto->setPersonId($this->personId);
    
        $searchAttributes['academic_updates']['grade'] = "";
        $searchAttributes['academic_updates']['final_grade'] = "";
        $searchAttributes['academic_updates']['absences'] = "";
        $searchAttributes['academic_updates']['term_ids'] = "";
        $searchAttributes['academic_updates']['is_current_academic_year'] = true;
    
        $searchAttributes['group_ids'] = "";
        $reportDto->setSearchAttributes($searchAttributes);
    
        $reportSections['reportId'] = $this->reportId;
        $reportSections['report_name'] = 'All Academic Updates Report';
        $reportSections['short_code'] = "AU-R";
        $reportDto->setReportSections($reportSections);
    
        return $reportDto;
    }
}