<?php
require_once 'tests/api/SynapseTestHelper.php';

class CustomSearchSurveyStatusCest extends SynapseTestHelper
{
    private $token;
    
    private $organization = 1;
    
    private $invalidOrganization = - 1;
    
    private $invalidPerson = - 1;
    
    private $validPerson = 1;
    
    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }
    
    public function testCreateCustomSearchesForSurveyStatus(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search For Survey Status by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "",
            "intent_to_leave_ids" => "",
            "student_status" => 1,
            "group_ids" => "",
            "referral_status" => "",
            "contact_types" => "",
            "courses" => [],
            "isps" => [],
            "datablocks" => [],
            "academic_updates" => [
            "ignoreThis" => "",
            "isBlankAcadUpdate" => true
            ],
            "survey" => [],
            "survey_status" => [
                [
                    "survey_id"=> 11,
                    "is_optedout"=> true,
                    "is_completed"=> false,
                    "is_viewed_report"=> true
                ]
            ],
            "isqs" => [],
            "cohort_ids" => ""
            ]
            ]);
    
        $customSearch = json_decode($I->grabResponse());
    
        $I->seeResponseContainsJson(array('person_id' => $this->validPerson));
        $I->seeResponseContains('search_attributes');
        $I->seeResponseContains('total_records');
        $I->seeResponseContains('total_pages');
        $I->seeResponseContains('records_per_page');
        $I->seeResponseContains('current_page');
    
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testCreateCustomSearchesForSurveyStatusWithNotViewedAndCompleted(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search For Survey Status With Not Viewed And Completed by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "",
            "intent_to_leave_ids" => "",
            "student_status" => 1,
            "group_ids" => "",
            "referral_status" => "",
            "contact_types" => "",
            "courses" => [],
            "isps" => [],
            "datablocks" => [],
            "academic_updates" => [
            "ignoreThis" => "",
            "isBlankAcadUpdate" => true
            ],
            "survey" => [],
            "survey_status" => [
            [
            "survey_id"=> 11,
            "is_optedout"=> false,
            "is_completed"=> true,
            "is_viewed_report"=> false
            ]
            ],
            "isqs" => [],
            "cohort_ids" => ""
            ]
            ]);
    
        $customSearch = json_decode($I->grabResponse());
    
        $I->seeResponseContainsJson(array('person_id' => $this->validPerson));
        $I->seeResponseContains('search_attributes');
        $I->seeResponseContains('total_records');
        $I->seeResponseContains('total_pages');
        $I->seeResponseContains('records_per_page');
        $I->seeResponseContains('current_page');
    
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testCreateCustomSearchesForSurveyStatusWithEmptyViewedAndCompleted(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search For Survey Status With Empty Viewed And Completed by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "",
            "intent_to_leave_ids" => "",
            "student_status" => 1,
            "group_ids" => "",
            "referral_status" => "",
            "contact_types" => "",
            "courses" => [],
            "isps" => [],
            "datablocks" => [],
            "academic_updates" => [
            "ignoreThis" => "",
            "isBlankAcadUpdate" => true
            ],
            "survey" => [],
            "survey_status" => [
            [
            "survey_id"=> 11,
            "is_optedout"=> true,
            "is_completed"=> "",
            "is_viewed_report"=> ""
            ]
            ],
            "isqs" => [],
            "cohort_ids" => ""
            ]
            ]);
    
        $customSearch = json_decode($I->grabResponse());
    
        $I->seeResponseContainsJson(array('person_id' => $this->validPerson));
        $I->seeResponseContains('search_attributes');
        $I->seeResponseContains('total_records');
        $I->seeResponseContains('total_pages');
        $I->seeResponseContains('records_per_page');
        $I->seeResponseContains('current_page');
    
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}