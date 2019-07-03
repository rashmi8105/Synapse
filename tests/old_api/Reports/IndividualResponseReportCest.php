<?php
require_once 'tests/api/SynapseTestHelper.php';

class IndividualResponseReportCest extends SynapseTestHelper
{
    private $token;
    
    private $organization = 1;
    private $invalidOrganization = -10;
    private $personId = 1;
    private $surveyId = 1;
    private $cohortId = 1;
    private $reportId = 2;
    
    public function _before(ApiTester $I) {
        $this->token = $this->authenticate ( $I );
        $this->start = new DateTime ( "now" );
        $this->end = clone $this->start;
        $this->end->add ( new \DateInterval ( 'P1M' ) );
    }
    
    public function testGetSurveyStatusReport(ApiTester $I)
    {
        $I->wantTo ( "Get Survey Status Report by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'reports/1?sortBy=-student_last_name', [
            "organization_id"=> $this->organization,
            "person_id"=> $this->personId,
            "search_attributes"=> [
                "filterCount"=> 2,
                "survey_filter"=> [
                    "survey_id"=> $this->surveyId,
                    "academic_year_id"=> "201516",
                    "cohort_id"=> $this->cohortId,
                    "responded_date"=> "2015-12-30",
                    "opted_out"=> "true"
                ],
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
                "report_name"=> "Individual Response Report",
                "short_code"=> "SUR-IRR"
             ]
        ] );
        
        $reportData = json_decode($I->grabResponse());
        
        $I->seeResponseContainsJson(array('records_per_page' => 25));
        $I->seeResponseContainsJson(array('current_page' => 1));
        $I->seeResponseContains('total_records');
        $I->seeResponseContains('total_records');
        $I->seeResponseContains('report_sections');
        $I->seeResponseContains('percent_responded');
        $I->seeResponseContainsJson(array('report_sections' => array('reportId' => $this->reportId)));
        $I->seeResponseContainsJson(array('report_sections' => array('report_name' => 'Individual Response Report')));
        $I->seeResponseContainsJson(array('report_sections' => array('short_code' => 'SUR-IRR')));
        $I->seeResponseContainsJson(array('search_attributes' => array('survey_filter' => array('survey_id' => $this->surveyId))));
        $I->seeResponseContainsJson(array('search_attributes' => array('survey_filter' => array('cohort_id' => $this->cohortId))));
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson();
    }
    
    public function testGetSurveyStatusReportStudentList(ApiTester $I)
    {
        $I->wantTo ( "Get Survey Status Report Student List by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'reports/1?data=student_list', [
            "organization_id"=> $this->organization,
            "person_id"=> $this->personId,
            "search_attributes"=> [
            "filterCount"=> 2,
            "survey_filter"=> [
            "survey_id"=> $this->surveyId,
            "academic_year_id"=> "201516",
            "cohort_id"=> $this->cohortId
            ],
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
            "report_name"=> "Individual Response Report",
            "short_code"=> "SUR-IRR"
            ]
            ] );
    
        $reportData = json_decode($I->grabResponse());

        $I->seeResponseContainsJson(array('person_id' => $this->personId));
        $I->seeResponseContains('search_result');
        
        if(count($reportData->data->search_result) > 0){
            foreach($reportData->data->search_result as $report){
                $I->seeResponseContains('student_id');
                $I->seeResponseContains('student_first_name');
                $I->seeResponseContains('student_last_name');
            
            }
        }
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson();
    }
    
    public function testGetSurveyStatusReportNoCoordinatorAccess(ApiTester $I)
    {
        $I->wantTo ( "Get Survey Status Report No Coordinator Access by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->authenticateFaculty($I) );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'reports/1', [
            "organization_id"=> $this->organization,
            "person_id"=> 3,
            "search_attributes"=> [
            "filterCount"=> 2,
            "survey_filter"=> [
            "survey_id"=> $this->surveyId,
            "academic_year_id"=> "201516",
            "cohort_id"=> $this->cohortId
            ],
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
            "report_name"=> "Individual Response Report",
            "short_code"=> "SUR-IRR"
            ]
            ] );
    
        $reportData = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson();
        $I->seeResponseContains('You no longer have permission to run this report');
        
    }
    
    
    public function testGetSurveyStatusReportWithEmptySurveyFilter(ApiTester $I)
    {
        $I->wantTo ( "Get Survey Status Report With Empty Survey Filter by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'reports/1', [
            "organization_id"=> $this->organization,
            "person_id"=> $this->personId,
            "search_attributes"=> [
            "filterCount"=> 2,
            "survey_filter"=> [],
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
            "report_name"=> "Individual Response Report",
            "short_code"=> "SUR-IRR"
            ]
            ] );
    
        $reportData = json_decode($I->grabResponse());
    
        $I->seeResponseContains('Undefined index: survey_id');
        $I->seeResponseCodeIs(500);
        $I->seeResponseContainsJson();
    }
    
    public function testGetSurveyStatusReportWithEmptySurveyId(ApiTester $I)
    {
        $I->wantTo ( "Get Survey Status Report With Empty Survey Id by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'reports/1', [
            "organization_id"=> $this->organization,
            "person_id"=> $this->personId,
            "search_attributes"=> [
            "filterCount"=> 2,
            "survey_filter"=> [
            "academic_year_id"=> "201516",
            "cohort_id"=> $this->cohortId
            ],
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
            "report_name"=> "Individual Response Report",
            "short_code"=> "SUR-IRR"
            ]
            ] );
    
        $reportData = json_decode($I->grabResponse());

        $I->seeResponseCodeIs(500);
        $I->seeResponseContainsJson();
    }
    
    public function testGetSurveyStatusReportWithEmptyAcademicYear(ApiTester $I)
    {
        $I->wantTo ( "Get Survey Status Report With Empty Academic Year by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'reports/1', [
            "organization_id"=> $this->organization,
            "person_id"=> $this->personId,
            "search_attributes"=> [
            "filterCount"=> 2,
            "survey_filter"=> [
            "survey_id"=> $this->surveyId,
            "cohort_id"=> $this->cohortId,
            "responded"=> "true"
            ],
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
            "report_name"=> "Individual Response Report",
            "short_code"=> "SUR-IRR"
            ]
            ] );
    
        $reportData = json_decode($I->grabResponse());
    
        $I->seeResponseContainsJson(array('records_per_page' => 25));
        $I->seeResponseContainsJson(array('current_page' => 1));
        $I->seeResponseContains('total_records');
        $I->seeResponseContains('total_records');
        $I->seeResponseContains('report_sections');
        $I->seeResponseContains('percent_responded');
        $I->seeResponseContainsJson(array('report_sections' => array('reportId' => $this->reportId)));
        $I->seeResponseContainsJson(array('report_sections' => array('report_name' => 'Individual Response Report')));
        $I->seeResponseContainsJson(array('report_sections' => array('short_code' => 'SUR-IRR')));
        $I->seeResponseContainsJson(array('search_attributes' => array('survey_filter' => array('survey_id' => $this->surveyId))));
        $I->seeResponseContainsJson(array('search_attributes' => array('survey_filter' => array('cohort_id' => $this->cohortId))));
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson();
    }
    
    public function testGetSurveyStatusReportWithEmptyCohortId(ApiTester $I)
    {
        $I->wantTo ( "Get Survey Status Report With Empty Cohort Id by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'reports/1', [
            "organization_id"=> $this->organization,
            "person_id"=> $this->personId,
            "search_attributes"=> [
            "filterCount"=> 2,
            "survey_filter"=> [
            "academic_year_id"=> "201516"
            ],
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
            "report_name"=> "Individual Response Report",
            "short_code"=> "SUR-IRR"
            ]
            ] );
    
        $reportData = json_decode($I->grabResponse());
    
        $I->seeResponseCodeIs(500);
        $I->seeResponseContainsJson();
    }
}