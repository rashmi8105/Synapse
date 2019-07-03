<?php
require_once 'tests/api/SynapseTestHelper.php';

class ReportStudentCountCest extends SynapseTestHelper
{
    private $token;
    private $organization = 1;
    private $invalidOrganization = -10;
    private $personId = 1;
    private $surveyId = 1;
    private $cohortId = 1;
    private $reportId = 3;
    
    public function _before(ApiTester $I) {
        $this->token = $this->authenticate ( $I );
    }
    
    public function testGetStudentCountBasedCriteria(ApiTester $I)
    {
        $I->wantTo ( "Get Student Count Based Criteria by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'reports/student_population?report_short_code=AU-R', [
            "organization_id"=> $this->organization,
            "person_id"=> $this->personId,
            "search_attributes"=> [
            "filterCount"=> 0,
            "risk_indicator_date"=> "",
            "risk_indicator_ids"=> "",
            "student_status"=> "",
            "group_ids"=> "",
            "courses"=> [],
            "datablocks"=> [],
            "isps"=> [],
            "static_list_ids"=> "",
            "academic_updates"=> [
            "grade"=> "",
            "final_grade"=> "",
            "absences"=> "",
            "absence_range"=> [
            "min_range"=> "",
            "max_range"=> ""
            ],
            "term_ids"=> "3",
            "is_current_academic_year"=> true,
            "isBlankAcadUpdate"=>true
            ]
            ],
            "report_sections"=> [
            "reportId"=> $this->reportId,
            "report_name"=> "All Academic Updates Report",
            "short_code"=> "AU-R"
            ]] );
        
        $reportData = json_decode($I->grabResponse());

        $I->seeResponseContainsJson(array('organization_id' => $this->organization));
        $I->seeResponseContainsJson(array('person_id' => $this->personId));
        $I->seeResponseContains('student_count');
    }
    
    public function testGetStudentCountBasedCriteriaNoAUFilter(ApiTester $I)
    {
        $I->wantTo ( "Get Student Count Based Criteria No AU Filter by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'reports/student_population?report_short_code=AU-R', [
            "organization_id"=> $this->organization,
            "person_id"=> $this->personId,
            "search_attributes"=> [
            "filterCount"=> 0,
            "risk_indicator_date"=> "",
            "risk_indicator_ids"=> "",
            "student_status"=> "",
            "group_ids"=> "",
            "courses"=> [],
            "datablocks"=> [],
            "isps"=> [],
            "static_list_ids"=> ""
            ],
            "report_sections"=> [
            "reportId"=> $this->reportId,
            "report_name"=> "All Academic Updates Report",
            "short_code"=> "AU-R"
            ]] );
    
        $reportData = json_decode($I->grabResponse());
    
        $I->seeResponseContainsJson(array('organization_id' => $this->organization));
        $I->seeResponseContainsJson(array('person_id' => $this->personId));
        $I->seeResponseContains('student_count');
    }
    
    public function testGetStudentCountBasedCriteriaWithSurveySnapshotCode(ApiTester $I)
    {
        $I->wantTo ( "Get Student Count Based Criteria With Survey Snapshot Code by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'reports/student_population?report_short_code=SUR-SR', [
            "organization_id"=> $this->organization,
            "person_id"=> $this->personId,
            "search_attributes"=> [
            "filterCount"=> 0,
            "risk_indicator_date"=> "",
            "risk_indicator_ids"=> "",
            "student_status"=> "",
            "group_ids"=> "",
            "courses"=> [],
            "datablocks"=> [],
            "isps"=> [],
            "static_list_ids"=> ""
            ],
            "report_sections"=> [
            "reportId"=> $this->reportId,
            "report_name"=> "All Academic Updates Report",
            "short_code"=> "AU-R"
            ]] );
    
        $reportData = json_decode($I->grabResponse());
    
        $I->seeResponseContainsJson(array('organization_id' => $this->organization));
        $I->seeResponseContainsJson(array('person_id' => $this->personId));
        $I->seeResponseContains('student_count');
    }
    
    public function testGetStudentCountBasedCriteriaInvalidOrganization(ApiTester $I)
    {
        $I->wantTo ( "Get Student Count Based Criteria invalid Organization by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'reports/student_population?report_short_code=SUR-SR', [
            "organization_id"=> $this->invalidOrganization,
            "person_id"=> $this->personId,
            "search_attributes"=> [
            "filterCount"=> 0,
            "risk_indicator_date"=> "",
            "risk_indicator_ids"=> "",
            "student_status"=> "",
            "group_ids"=> "",
            "courses"=> [],
            "datablocks"=> [],
            "isps"=> [],
            "static_list_ids"=> ""
            ],
            "report_sections"=> [
            "reportId"=> $this->reportId,
            "report_name"=> "All Academic Updates Report",
            "short_code"=> "AU-R"
            ]] );
    
        $reportData = json_decode($I->grabResponse());
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(400);
        $I->seeResponseContains('Organization Not Found.');
    }
    
    public function testGetStudentCountBasedFactorCriteria(ApiTester $I)
    {
        $I->wantTo ( "Get Student Count Based sending fator arr" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'reports/student_population?report_short_code=AU-R', [
            "organization_id"=> $this->organization,
            "person_id"=> $this->personId,
            "search_attributes"=> [
            "filterCount"=> 1,
            "risk_indicator_date"=> "",
            "risk_indicator_ids"=> "",
            "student_status"=> "",
            "group_ids"=> "",
            "courses"=> [],
            "datablocks"=> [],
            "isps"=> [],
            "static_list_ids"=> "",
            "survey_filter" => [
                "survey_id"=> 1,
                "factors" => [
                    [
                        "id"=> 1,
                        "value_min"=> 1,
                        "value_max"=> 7
                    ]
                ]
            ],
            
            "academic_updates"=> [
            "grade"=> "",
            "final_grade"=> "",
            "absences"=> "",
            "absence_range"=> [
            "min_range"=> "",
            "max_range"=> ""
            ],
            "term_ids"=> "",
            "is_current_academic_year"=> false,
            "isBlankAcadUpdate"=>false
            ]
            ],
            "report_sections"=> [
            "reportId"=> $this->reportId,
            "report_name"=> "All Academic Updates Report",
            "short_code"=> "AU-R"
            ]] );
    
        $reportData = json_decode($I->grabResponse());
    
        $I->seeResponseContainsJson(array('organization_id' => $this->organization));
        $I->seeResponseContainsJson(array('person_id' => $this->personId));
        $I->seeResponseContains('student_count');
    }
    
}