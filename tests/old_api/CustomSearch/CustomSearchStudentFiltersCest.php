<?php
require_once 'tests/api/SynapseTestHelper.php';

class CustomSearchStudentFiltersCest extends SynapseTestHelper
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
    
    public function getStudentsBasedSearchFilters(ApiTester $I, $scenario)
    {
        $I->wantTo('get Students Based Search Filters  by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search/students?status=active', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "",
            "intent_to_leave_ids" => "",
            "student_status" => "",
            "group_ids" => "",
            "referral_status" => "all",
            "contact_types" => "all",
            "courses" => [],
            "isps" => [],
            "datablocks" => [],
            "academic_updates" => [
            "ignoreThis" => "",
            "isBlankAcadUpdate" => true
            ],
            "survey" => [],
            "survey_status" => [],
            "isqs" => [],
            "cohort_ids" => ""
            ]
            ]);
    
        $customSearch = json_decode($I->grabResponse());
        
        $I->seeResponseContainsJson(array('person_id' => $this->validPerson));
        if(array_key_exists('search_result', $customSearch)){
            $searchDetails = $customSearch->data->search_result;
            foreach($searchDetails as $searchDetail){
                $I->seeResponseContains('id');
                $I->seeResponseContains('student_last_name');
                $I->seeResponseContains('student_first_name');
            }
        }
    
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function getStudentsBasedSearchFiltersWithNoContactTypesAndReferrals(ApiTester $I, $scenario)
    {
        $I->wantTo('get Students Based Search Filters With No Contact Types And Referrals by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search/students?status=active', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "2,3",
            "intent_to_leave_ids" => "",
            "student_status" => "",
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
            "isqs" => [],
            "cohort_ids" => ""
            ]
            ]);
    
        $customSearch = json_decode($I->grabResponse());
    
        $I->seeResponseContainsJson(array('person_id' => $this->validPerson));
        if(array_key_exists('search_result', $customSearch)){
            $searchDetails = $customSearch->data->search_result;
            foreach($searchDetails as $searchDetail){
                $I->seeResponseContains('id');
                $I->seeResponseContains('student_last_name');
                $I->seeResponseContains('student_first_name');
            }
        }
    
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function getStudentsBasedSearchFiltersInvalidOrganization(ApiTester $I, $scenario)
    {
        $I->wantTo('get Students Based Search Filters Invalid Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search/students?status=active', [
            "organization_id" => -5,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "",
            "intent_to_leave_ids" => "",
            "student_status" => "",
            "group_ids" => "",
            "referral_status" => "all",
            "contact_types" => "all",
            "courses" => [],
            "isps" => [],
            "datablocks" => [],
            "academic_updates" => [
            "ignoreThis" => "",
            "isBlankAcadUpdate" => true
            ],
            "survey" => [],
            "survey_status" => [],
            "isqs" => [],
            "cohort_ids" => ""
            ]
            ]);
    
        
        $I->seeResponseContains('Organization Not Found.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function getStudentsBasedSearchFiltersWithPredefinedKey(ApiTester $I, $scenario)
    {
        $I->wantTo('get Students Based Search Filters With Predefined Key by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search/students?status=inactive&predefined_key=All_My_Student', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson
            ]);
        
        $customSearch = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array('person_id' => $this->validPerson));
        if(array_key_exists('search_result', $customSearch)){
            $searchDetails = $customSearch->data->search_result;
            foreach($searchDetails as $searchDetail){
                $I->seeResponseContains('id');
                $I->seeResponseContains('student_last_name');
                $I->seeResponseContains('student_first_name');
            }
        }
    
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function getStudentsBasedSearchFiltersWithData(ApiTester $I, $scenario)
    {
        $I->wantTo('get Students Based Search Filters With Data by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search/students?status=active', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "3,4",
            "intent_to_leave_ids" => "",
            "student_status" => "",
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
            "isqs" => [],
            "cohort_ids" => ""
            ]
            ]);
    
        $customSearch = json_decode($I->grabResponse());
    
        $I->seeResponseContainsJson(array('person_id' => $this->validPerson));
        if(array_key_exists('search_result', $customSearch)){
            $searchDetails = $customSearch->data->search_result;
            foreach($searchDetails as $searchDetail){
                $I->seeResponseContains('id');
                $I->seeResponseContains('student_last_name');
                $I->seeResponseContains('student_first_name');
            }
        }
    
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}