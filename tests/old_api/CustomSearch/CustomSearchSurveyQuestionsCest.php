<?php
require_once 'tests/api/SynapseTestHelper.php';

class CustomSearchSurveyQuestionsCest extends SynapseTestHelper
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
    
    public function testCreateCustomSearchesForSurveyQuestions(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search For Survey Questions by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "1,2,3",
            "intent_to_leave_ids" => "1,2",
            "student_status" => 1,
            "group_ids" => "1",
            "referral_status" => "all",
            "contact_types" => "all",
            "courses" => [],
            "isps" => [],
            "datablocks" => [],
            "academic_updates" => [
            "ignoreThis" => "",
            "isBlankAcadUpdate" => true
            ],
            "survey" => [
            [
                "survey_id"=> 11,
                "survey_questions"=> [
                    [
                        "id"=> 257,
                        "type"=> "category",
                        "options"=> [
                            [
                                "answer"=> "(1) Not at all",
                                "value"=> "1"
                            ],
                            [
                                "answer"=> "(2)",
                                "value"=> "2"
                            ]
                        ]
                    ],
                    [
                        "id"=> 258,
                        "type"=> "number",
                        "min_range"=> 1,
                        "max_range"=> 10
                    ]
                ]
            ]
        ],
            "survey_status" => [],
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
    
    public function testCreateCustomSearchesForSurveyQuestionsWithNoSurveyId(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search For Survey Questions With No SurveyId by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "1,2,3",
            "intent_to_leave_ids" => "1,2",
            "student_status" => 1,
            "group_ids" => "1",
            "referral_status" => "all",
            "contact_types" => "all",
            "courses" => [],
            "isps" => [],
            "datablocks" => [],
            "academic_updates" => [
            "ignoreThis" => "",
            "isBlankAcadUpdate" => true
            ],
            "survey" => [
            [
            "survey_id"=> "",
            "survey_questions"=> [
            [
            "id"=> 257,
            "type"=> "category",
            "options"=> [
            [
            "answer"=> "(1) Not at all",
            "value"=> "1"
            ],
            [
            "answer"=> "(2)",
            "value"=> "2"
            ]
            ]
            ]
            ]
            ]
            ],
            "survey_status" => [],
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
    
    public function testCreateCustomSearchesForSurveyQuestionsWithNoQuestionIdAndOptions(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search For Survey Questions With No Question Id And Options by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "1,2,3",
            "intent_to_leave_ids" => "1,2",
            "student_status" => 1,
            "group_ids" => "1",
            "referral_status" => "all",
            "contact_types" => "all",
            "courses" => [],
            "isps" => [],
            "datablocks" => [],
            "academic_updates" => [
            "ignoreThis" => "",
            "isBlankAcadUpdate" => true
            ],
            "survey" => [
            [
            "survey_id"=> 11,
            "survey_questions"=> [
            [
            "id"=> 257,
            "type"=> "category",
            "options"=> []
            ],
            [
            "id"=> "",
            "type"=> "number",
            "min_range"=> 0,
            "max_range"=> 10
            ]
            ]
            ]
            ],
            "survey_status" => [],
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
    
    public function testCreateCustomSearchesForSurveyQuestionsWithNoMinMaxRange(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search For Survey Questions With No Min Max Range by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "",
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
            "survey" => [
            [
            "survey_id"=> 11,
            "survey_questions"=> [
            [
            "id"=> 257,
            "type"=> "",
            "options"=> []
            ],
            [
            "id"=> 258,
            "type"=> "number",
            "min_range"=> "",
            "max_range"=> ""
            ]
            ]
            ]
            ],
            "survey_status" => [],
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
    
    public function testCreateCustomSearchesForISQQuestions(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search For ISQ Questions by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "1,2,3",
            "intent_to_leave_ids" => "1,2",
            "student_status" => 1,
            "group_ids" => "1",
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
            "isqs" => [
            [
            "survey_id"=> 11,
            "isqs"=> [
            [
            "id"=> 1561,
            "type"=> "category",
            "options"=> [
            [
            "answer"=> "Yes",
            "value"=> "1",
            "id"=> 7334
            ],
            [
            "id"=> 7335,
            "answer"=> "No",
            "value"=> "2"
            ]
            ]
            ]
            ]
            ]
            ],
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