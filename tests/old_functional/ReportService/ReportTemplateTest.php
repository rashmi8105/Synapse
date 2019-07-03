<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;

use Codeception\Util\Stub;
use Synapse\ReportsBundle\EntityDto\ReportsTemplatesDto;

class ReportTemplateTest extends \Codeception\TestCase\Test
{

	private $reportId = 8;
	
	private $personId = 1;
	
	private $orgId = 1;
	
	private $invalidReportId = 90;
	
	private $invalidPersonId = -1;
	
	/**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->reportTemplates = $this->container
        ->get('report_template_service');
		$this->snapshotService = $this->container
        ->get('surveysnapshot_service');		
    }
	
	public function testCreateReportTemplates()
	{
		$reportsTemplatesDto = new ReportsTemplatesDto();
		$reportsTemplatesDto->setPersonId($this->personId);		
		$reportsTemplatesDto->setOrgId($this->orgId);
		$reportsTemplatesDto->setTemplateName("Test Templates");
		$request_json['report_id'] = $this->reportId;
		$reportsTemplatesDto->setRequestJson($request_json);		
		$reportTemplates = $this->reportTemplates->createReportTemplate($reportsTemplatesDto);
		$this->assertInternalType('object', $reportTemplates);		
		$this->assertEquals($this->reportId, $reportTemplates->getRequestJson()['report_id']);
		$this->assertEquals($this->personId, $reportTemplates->getPersonId());
		$this->assertEquals($this->orgId, $reportTemplates->getOrgId());
		$this->assertEquals('Test Templates', $reportTemplates->getTemplateName());		
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testCreateReportTemplatesWithInvalidReportId()
	{
		$reportsTemplatesDto = new ReportsTemplatesDto();
		$reportsTemplatesDto->setPersonId($this->personId);		
		$reportsTemplatesDto->setOrgId($this->orgId);
		$reportsTemplatesDto->setTemplateName("Test Templates");
		$request_json['report_id'] = $this->invalidReportId;
		$reportsTemplatesDto->setRequestJson($request_json);		
		$reportTemplates = $this->reportTemplates->createReportTemplate($reportsTemplatesDto);
		$this->assertSame('{"errors": ["Report Not Found"],
			"data": [],
			"sideLoaded": []
			}', $reportTemplates);		
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testCreateReportTemplatesWithInvalidPersonId()
	{
		$reportsTemplatesDto = new ReportsTemplatesDto();
		$reportsTemplatesDto->setPersonId($this->invalidPersonId);		
		$reportsTemplatesDto->setOrgId($this->orgId);
		$reportsTemplatesDto->setTemplateName("Test Templates");
		$request_json['report_id'] = $this->reportId;
		$reportsTemplatesDto->setRequestJson($request_json);		
		$reportTemplates = $this->reportTemplates->createReportTemplate($reportsTemplatesDto);
		$this->assertSame('{"errors": ["Person Not Found"],
			"data": [],
			"sideLoaded": []
			}', $reportTemplates);			
	}

	public function testGetReportTemplates()
	{
		$reportsTemplatesDto = new ReportsTemplatesDto();
		$reportsTemplatesDto->setPersonId($this->personId);		
		$reportsTemplatesDto->setOrgId($this->orgId);
		$reportsTemplatesDto->setTemplateName("Test Templates");
		$request_json['report_id'] = $this->reportId;
		$reportsTemplatesDto->setRequestJson($request_json);		
		$reportTemplates = $this->reportTemplates->createReportTemplate($reportsTemplatesDto);
		$myReportTemplates = $this->snapshotService->getMyReportsTemplate($this->orgId, $this->personId);
		$this->assertInternalType('array', $myReportTemplates);		
		$this->assertInternalType('object', $myReportTemplates[0]);	
		$this->assertEquals($this->reportId, $myReportTemplates[0]->getRequestJson()['report_id']);
		$this->assertEquals($this->personId, $myReportTemplates[0]->getPersonId());
		$this->assertEquals($this->orgId, $myReportTemplates[0]->getOrgId());
		$this->assertEquals('Test Templates', $myReportTemplates[0]->getTemplateName());			
	}
	
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testGetReportTemplatesForInvalidPerson()
	{
		$reportsTemplatesDto = new ReportsTemplatesDto();
		$reportsTemplatesDto->setPersonId($this->personId);		
		$reportsTemplatesDto->setOrgId($this->orgId);
		$reportsTemplatesDto->setTemplateName("Test Templates");
		$request_json['report_id'] = $this->reportId;
		$reportsTemplatesDto->setRequestJson($request_json);		
		$reportTemplates = $this->reportTemplates->createReportTemplate($reportsTemplatesDto);
		$myReportTemplates = $this->snapshotService->getMyReportsTemplate($this->orgId, $this->invalidPersonId);
		$this->assertSame('{"errors": ["Person Not Found"],
			"data": [],
			"sideLoaded": []
			}', $myReportTemplates);	
	}
	
	public function testUpdateReportTemplates()
	{
		$reportsTemplatesDto = new ReportsTemplatesDto();
		$reportsTemplatesDto->setPersonId($this->personId);		
		$reportsTemplatesDto->setOrgId($this->orgId);
		$reportsTemplatesDto->setTemplateName("Edit Test Templates");
		$request_json['report_id'] = $this->reportId;
		$reportsTemplatesDto->setRequestJson($request_json);		
		$reportTemplates = $this->reportTemplates->createReportTemplate($reportsTemplatesDto);
		$id = $reportTemplates->getId();
		$reportsTemplatesDto->setId($id);
		$reportsTemplatesDto->setTemplateName("Edit - Test Templates");
		$editResponse = $this->reportTemplates->editReportTemplate($reportsTemplatesDto);	
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testUpdateReportTemplatesWithInvalidPersonId()
	{
		$reportsTemplatesDto = new ReportsTemplatesDto();
		$reportsTemplatesDto->setPersonId($this->personId);		
		$reportsTemplatesDto->setOrgId($this->orgId);
		$reportsTemplatesDto->setTemplateName("Edit Test Templates");
		$request_json['report_id'] = $this->reportId;
		$reportsTemplatesDto->setRequestJson($request_json);		
		$reportTemplates = $this->reportTemplates->createReportTemplate($reportsTemplatesDto);
		$id = $reportTemplates->getId();
		$reportsTemplatesDto->setId($id);
		$reportsTemplatesDto->setPersonId($this->invalidPersonId);		
		$reportsTemplatesDto->setTemplateName("Edit - Test Templates");
		$editResponse = $this->reportTemplates->editReportTemplate($reportsTemplatesDto);	
		$this->assertSame('{"errors": ["Person Not Found"],
			"data": [],
			"sideLoaded": []
			}', $editResponse);	
	}
	
	public function testDeleteTemplate()
	{
		$reportsTemplatesDto = new ReportsTemplatesDto();
		$reportsTemplatesDto->setPersonId($this->personId);		
		$reportsTemplatesDto->setOrgId($this->orgId);
		$reportsTemplatesDto->setTemplateName("Edit Test Templates");
		$request_json['report_id'] = $this->reportId;
		$reportsTemplatesDto->setRequestJson($request_json);		
		$reportTemplates = $this->reportTemplates->createReportTemplate($reportsTemplatesDto);
		$id = $reportTemplates->getId();
		$myReportTemplates = $this->snapshotService->deleteReportTemplate($this->orgId, $id, $this->personId);		
		$this->assertEquals($myReportTemplates, $id);			
	}
}