<?php
require_once 'tests/api/SynapseTestHelper.php';

class FactorReportCest extends SynapseTestHelper
{
	private $academicYear = 201415;
	
	private $factorReportId = 10;
	
	private $surveyId = 1;
	
	private $cohortId = 1;
	
	private $personId = 1;
	
	private $orgId = 1;
	
	private $factorId = 1;
	
	
	public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }
	
	public function testCreateFactorReport(ApiTester $I)
	{
		$I->wantTo('Create Factor Reports');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('reports', [	"report_id" => $this->factorReportId,
									"organization_id" => $this->orgId,
									"person_id" => $this->personId,
									"search_attributes" => [ 
										"filterCount" => "",
										"risk_indicator_date" => "",
										"risk_indicator_ids" => "",
										"student_status" => "",
										"group_ids" => "",
										"courses" => "",
										"datablocks" => "",
										"isps" => "",
										"isqs" => "",
										"survey_filter" => [ 
												"survey_id" => $this->surveyId,
												"academic_year_id" => $this->academicYear,
												"cohort_id" => $this->cohortId
											]
										],
									"report_sections" => [
											"reportId" => $this->factorReportId
										]
								]
						);
		$response = json_decode($I->grabResponse());		
		$I->seeResponseCodeIs ( 200 );
		$I->seeResponseContains('report_id');		
		$I->seeResponseContains('person_id');		
		$I->seeResponseContains('organization_id');
		$I->seeResponseContains('search_attributes');
		$I->seeResponseContains('report_sections');
		$I->seeResponseContainsJson(array(
            "report_id" => $this->factorReportId,
			"person_id" => $this->personId,
			"organization_id" => $this->orgId
        ));
	}
	
	public function testDrilldownResponse(ApiTester $I)
	{
		$I->wantTo('Factor Drilldown Response');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('reports', [	"report_id" => $this->factorReportId,
									"organization_id" => $this->orgId,
									"person_id" => $this->personId,
									"search_attributes" => [ 
										"filterCount" => "",
										"risk_indicator_date" => "",
										"risk_indicator_ids" => "",
										"student_status" => "",
										"group_ids" => "",
										"courses" => "",
										"datablocks" => "",
										"isps" => "",
										"isqs" => "",
										"survey_filter" => [ 
												"survey_id" => $this->surveyId,
												"academic_year_id" => $this->academicYear,
												"cohort_id" => $this->cohortId
											]
										],
									"report_sections" => [
											"reportId" => $this->factorReportId
										]
								]
						);
		$response = json_decode($I->grabResponse());
		$reportInstanceId = $response->data->id;		
		$I->sendGET('myreports/' . $reportInstanceId);		
		$response = json_decode($I->grabResponse());
		$I->seeResponseCodeIs ( 200 );
		$I->seeResponseContains('person_id');
		$I->seeResponseContains('status');
		$I->seeResponseContains('id');
		$I->seeResponseContainsJson(array(           
			"person_id" => $this->personId			
        ));
	}
}