<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;

use Codeception\Util\Stub;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;

class SnapshotReportTest extends \Codeception\TestCase\Test
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
	
	private $snapshotReportId = 8;
	
	private $surveyId = 1;
	
	private $cohortId = 1;
	
	private $personId = 1;
	
	private $orgId = 1;
	
	private $ebiQuestionId = 22;
	
	private $optionValues = '6,7';
	
	private $invalidOptionValues = '1,2,3,4';
	
	private $shortAnswerId = 24;
	/**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->snapshotService = $this->container
        ->get('surveysnapshot_service');
		$this->pdfReportService = $this->container
        ->get('pdf_reports_service');
    }
    
	
	public function testCreateSnapshotReport()
	{
		$reportsDto = $this->generateReportsDto();
		$reportRunningStatus = $this->pdfReportService->createReportRunningStatus($reportsDto);
		$reportInstanceId = $reportRunningStatus->getId();
		$snapshotService = $this->snapshotService->generateReport($reportInstanceId, $this->surveyId, $this->personId, $reportsDto);		
	}
	
	public function testScaledQuestionsDrilldownResponse()
	{
		$reportsDto = $this->generateReportsDto();
		$reportRunningStatus = $this->pdfReportService->createReportRunningStatus($reportsDto);
		$reportInstanceId = $reportRunningStatus->getId();
		$this->snapshotService->generateReport($reportInstanceId, $this->surveyId, $this->personId, $reportsDto);
		$drilldownResponse = $this->snapshotService->getJsonResponseDrilldown($this->personId, $reportInstanceId, 'Q', $this->ebiQuestionId, '', '', '', '', '', '', '','','','','');	
		$this->assertNotEmpty($drilldownResponse);
		$this->assertInternalType('object', $drilldownResponse);
		$this->assertInternalType('array', $drilldownResponse->getSearchResult());
		$this->assertEquals($drilldownResponse->getPersonId(), $this->personId);
		$this->assertObjectHasAttribute("studentId", $drilldownResponse->getSearchResult()[0]);
		$this->assertObjectHasAttribute("riskLevel", $drilldownResponse->getSearchResult()[0]);
		$this->assertObjectHasAttribute("studentRiskStatus", $drilldownResponse->getSearchResult()[0]);
		$this->assertObjectHasAttribute("response", $drilldownResponse->getSearchResult()[0]);	
	}
	
	public function testDescriptiveQuestionExport()
	{
		$reportsDto = $this->generateReportsDto();
		$reportRunningStatus = $this->pdfReportService->createReportRunningStatus($reportsDto);
		$reportInstanceId = $reportRunningStatus->getId();
		$this->snapshotService->generateReport($reportInstanceId, $this->surveyId, $this->personId, $reportsDto);
		$exportResponse = $this->snapshotService->getJsonResponseDrilldown($this->personId, $reportInstanceId, 'LA', $this->shortAnswerId, '', '', '', '', 'csv', '', '','','','','');
		$this->assertInternalType('array', $exportResponse);		
	}
	
	public function testScaledQuestionsDrilldownResponseWithOptions()
	{
		$reportsDto = $this->generateReportsDto();
		$reportRunningStatus = $this->pdfReportService->createReportRunningStatus($reportsDto);		
		$reportInstanceId = $reportRunningStatus->getId();
		$snapshotService = $this->snapshotService->generateReport($reportInstanceId, $this->surveyId, $this->personId, $reportsDto);
		$drilldownResponse = $this->snapshotService->getJsonResponseDrilldown($this->personId, $reportInstanceId, 'Q', $this->ebiQuestionId, $this->optionValues, '', '', '', '', '', '','','','','');			
		$this->assertNotEmpty($drilldownResponse);
		$this->assertInternalType('object', $drilldownResponse);
		$this->assertInternalType('array', $drilldownResponse->getSearchResult());
		$this->assertEquals($drilldownResponse->getPersonId(), $this->personId);
		$this->assertObjectHasAttribute("studentId", $drilldownResponse->getSearchResult()[0]);
		$this->assertObjectHasAttribute("riskLevel", $drilldownResponse->getSearchResult()[0]);
		$this->assertObjectHasAttribute("studentRiskStatus", $drilldownResponse->getSearchResult()[0]);
		$this->assertObjectHasAttribute("response", $drilldownResponse->getSearchResult()[0]);		
		$this->assertEquals('More than 5 courses', $drilldownResponse->getSearchResult()[0]->getResponse());
		$this->assertEquals(2, $drilldownResponse->getSearchResult()[0]->getStudentId());
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
	public function testScaledQuestionsDrilldownResponseWithInvlidOptions()
	{
		$reportsDto = $this->generateReportsDto();
		$reportRunningStatus = $this->pdfReportService->createReportRunningStatus($reportsDto);		
		$reportInstanceId = $reportRunningStatus->getId();
		$snapshotService = $this->snapshotService->generateReport($reportInstanceId, $this->surveyId, $this->personId, $reportsDto);
		$drilldownResponse = $this->snapshotService->getJsonResponseDrilldown($this->personId, $reportInstanceId, 'Q', $this->ebiQuestionId, $this->invalidOptionValues, '', '', '', '', '', '','','','','');					
        $this->assertSame('{"errors": ["Invalid option pair"],
			"data": [],
			"sideLoaded": []
			}', $drilldownResponse);		
	}
	
	public function testResponseJson()
	{
		$reportsDto = $this->generateReportsDto();
		$reportRunningStatus = $this->pdfReportService->createReportRunningStatus($reportsDto);		
		$reportInstanceId = $reportRunningStatus->getId();
		$snapshotService = $this->snapshotService->generateReport($reportInstanceId, $this->surveyId, $this->personId, $reportsDto);
		$drilldownResponse = $this->snapshotService->getJsonResponseDrilldown($this->personId, $reportInstanceId, '', '', '', '', '', '', '', '', '','','','','');
		$this->assertInternalType('object', $drilldownResponse);
		$this->assertInternalType('array', $drilldownResponse->getResponseJson());
		$this->assertInternalType('array', $drilldownResponse->getResponseJson()['report_info']);
		$this->assertInternalType('array', $drilldownResponse->getResponseJson()['campus_info']);
		$this->assertInternalType('array', $drilldownResponse->getResponseJson()['sections']);
		$this->assertEquals($reportInstanceId, $drilldownResponse->getResponseJson()['report_instance_id']);
		$this->assertEquals('ebiQuestion', $drilldownResponse->getResponseJson()['sections'][0]['type']);
	}
	
	private function generateReportsDto()
	{
		$reportsRunningDto = new ReportRunningStatusDto;		
		$reportsRunningDto->setOrganizationId($this->orgId);
		$reportSection['reportId'] = $this->snapshotReportId;
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