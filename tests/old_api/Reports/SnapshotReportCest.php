<?php
require_once 'tests/api/SynapseTestHelper.php';

class SnapshotReportCest extends SynapseTestHelper
{
	private $academicYear = 201415;
	
	private $snapshotReportId = 8;
	
	private $surveyId = 1;
	
	private $cohortId = 1;
	
	private $personId = 1;
	
	private $orgId = 1;
	
	private $ebiQuestionId = 22;
	
	private $optionValues = '6,7';
	
	private $invalidOptionValues = '1,2,3,4';
	
	public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }
	
	public function testCreateSnapshotReport(ApiTester $I)
	{
		$I->wantTo('Create Snapshot Reports');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('reports', [	"report_id" => $this->snapshotReportId,
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
											"reportId" => $this->snapshotReportId
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
            "report_id" => $this->snapshotReportId,
			"person_id" => $this->personId,
			"organization_id" => $this->orgId
        ));
	}
	
	public function testDrilldownResponse(ApiTester $I)
	{
		$I->wantTo('Snapshot Drilldown Response');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('reports', [	"report_id" => $this->snapshotReportId,
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
											"reportId" => $this->snapshotReportId
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