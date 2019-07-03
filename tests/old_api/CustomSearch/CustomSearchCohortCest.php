<?php
require_once 'tests/api/SynapseTestHelper.php';

class CustomSearchCohortCest extends SynapseTestHelper
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
    
    public function testCreateCustomSearchesForCohortIds(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search For Cohort Ids by API');
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
                "survey_status" => [],
                "static_list_ids" =>"",
                "isqs" => [],
                "cohort_ids" => "1,2,3"
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
    
    public function testCreateCustomSearchesForStaticListIds(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search For Static List Ids by API');
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
            "survey_status" => [],
            "static_list_ids" =>"1,2,3",
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