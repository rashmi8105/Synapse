<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;

use Codeception\Util\Stub;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;

class FactorReportTest extends \Codeception\TestCase\Test
{

	/**
     * @var UnitTester
     */
    protected $tester;
    
    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;
	
	private $academicYear = 201415;
	
	private $factorReportId = 10;
	
	private $surveyId = 1;
	
	private $cohortId = 1;
	
	private $personId = 1;
	
	private $orgId = 1;
	
	private $factorId = 1;
	
	private $invalidReportInstanceId = -1;
	
	/**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->factorService = $this->container
        ->get('factorreport_service');
		$this->pdfReportService = $this->container
        ->get('pdf_reports_service');
		$this->snapshotService = $this->container
        ->get('surveysnapshot_service');
    }
    
	
	public function testCreateFactorReport()
	{
		$reportsDto = $this->generateReportsDto();
		$reportRunningStatus = $this->pdfReportService->createReportRunningStatus($reportsDto);
		$reportInstanceId = $reportRunningStatus->getId();
		$factorService = $this->factorService->generateReport($reportInstanceId, $this->surveyId, $this->personId, $reportsDto);		
	}
	
	public function testResponseJson()
	{
		$reportsDto = $this->generateReportsDto();
		$reportRunningStatus = $this->pdfReportService->createReportRunningStatus($reportsDto);
		$reportInstanceId = $reportRunningStatus->getId();
		$this->factorService->generateReport($reportInstanceId, $this->surveyId, $this->personId, $reportsDto);
		$drilldownResponse = $this->snapshotService->getJsonResponseDrilldown($this->personId, $reportInstanceId, '', '', '', '', '', '', '', 'factor', '','','','','');		
		$this->assertNotEmpty($drilldownResponse);
		$this->assertInternalType('object', $drilldownResponse);
		$this->assertInternalType('array', $drilldownResponse->getResponseJson());
		$this->assertEquals($drilldownResponse->getPersonId(), $this->personId);
		$this->assertInternalType('array', $drilldownResponse->getResponseJson()['campus_info']);
		$this->assertInternalType('array', $drilldownResponse->getResponseJson()['request_json']);
		$this->assertInternalType('array', $drilldownResponse->getResponseJson()['factors']);
	}
	
	public function testScaledQuestionsDrilldownResponseWithOptions()
	{
		$reportsDto = $this->generateReportsDto();
		$reportRunningStatus = $this->pdfReportService->createReportRunningStatus($reportsDto);		
		$reportInstanceId = $reportRunningStatus->getId();
		$factorService = $this->factorService->generateReport($reportInstanceId, $this->surveyId, $this->personId, $reportsDto);
		$drilldownResponse = $this->snapshotService->getJsonResponseDrilldown($this->personId, $reportInstanceId, '', '', '', '', '', '', '', 'factor', $this->factorId,'','','','');				
		$this->assertNotEmpty($drilldownResponse);
		$this->assertInternalType('object', $drilldownResponse);
		$this->assertInternalType('array', $drilldownResponse->getSearchResult());
		$this->assertEquals($drilldownResponse->getPersonId(), $this->personId);
		$this->assertObjectHasAttribute("studentId", $drilldownResponse->getSearchResult()[0]);
		$this->assertObjectHasAttribute("riskLevel", $drilldownResponse->getSearchResult()[0]);
		$this->assertObjectHasAttribute("studentRiskStatus", $drilldownResponse->getSearchResult()[0]);
		$this->assertObjectHasAttribute("response", $drilldownResponse->getSearchResult()[0]);		
		$this->assertEquals('4.6667', $drilldownResponse->getSearchResult()[0]->getResponse());		
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	 
	public function testDrilldownResponseWithInvalidFactorId()
	{		
		$drilldownResponse = $this->snapshotService->getJsonResponseDrilldown($this->personId, $this->invalidReportInstanceId, '', '', '', '', '', '', '', 'factor', '','','','','');					
        $this->assertSame('{"errors": ["Invalid Report Instance"],
			"data": [],
			"sideLoaded": []
			}', $drilldownResponse);		
	}
	
	public function testFactorDrilldownResponse()
	{
		$reportsDto = $this->generateReportsDto();
		$reportRunningStatus = $this->pdfReportService->createReportRunningStatus($reportsDto);		
		$reportInstanceId = $reportRunningStatus->getId();
		$factorService = $this->factorService->generateReport($reportInstanceId, $this->surveyId, $this->personId, $reportsDto);
		$drilldownResponse = $this->snapshotService->getJsonResponseDrilldown($this->personId, $reportInstanceId, '', '', '', '', '', '', '', 'factor', '','','','','');		
		$this->assertInternalType('object', $drilldownResponse);
		$this->assertInternalType('array', $drilldownResponse->getResponseJson());
		$this->assertInternalType('array', $drilldownResponse->getResponseJson()['report_info']);
		$this->assertInternalType('array', $drilldownResponse->getResponseJson()['campus_info']);
		$this->assertInternalType('array', $drilldownResponse->getResponseJson()['factors']);
		$this->assertEquals($reportInstanceId, $drilldownResponse->getResponseJson()['report_info']['report_instance_id']);		
	}
	
	private function generateReportsDto()
	{
		$reportsRunningDto = new ReportRunningStatusDto;		
		$reportsRunningDto->setOrganizationId($this->orgId);
		$reportSection['reportId'] = $this->factorReportId;
		$searchAttributes['filterCount'] = '';
		$searchAttributes['risk_indicator_date'] = '';
		$searchAttributes['risk_indicator_ids'] = '';
		$searchAttributes['student_status'] = '';
		$searchAttributes['group_ids'] = '';
		$searchAttributes['courses'] = [];
		$searchAttributes['datablocks'] = [];
		$searchAttributes['isps'] = [];
		$searchAttributes['isqs'] = [];
		$surveyFilter['survey_id'] = $this->surveyId;
		$surveyFilter['academic_year_id'] = $this->academicYear;
		$surveyFilter['cohort_id'] = $this->cohortId;
		$searchAttributes['survey_filter'] = $surveyFilter;
		$reportsRunningDto->setReportSections($reportSection);
		$reportsRunningDto->setPersonId($this->personId);		
		$reportsRunningDto->setSearchAttributes($searchAttributes);		
		return $reportsRunningDto;
		
	}
}