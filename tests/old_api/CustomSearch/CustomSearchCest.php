<?php
require_once 'tests/api/SynapseTestHelper.php';

class CustomSearchCest extends SynapseTestHelper
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

    public function testCreateCustomSearches(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search?sortBy=-student_last_name', [
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

    public function testCreateCustomSearchesInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Create a Custom Search with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated('xxxxx');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search');
        $res = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
    }
	
    public function testCreateCustomSearchesForCourses(ApiTester $I)
    {
        $I->wantTo('Create Custom Search for courses by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "search_attributes" => [
                "risk_indicator_ids" => "",
                "intent_to_leave_ids" => "",
                "student_status" => 0,
                "group_ids" => "1",
                "referral_status" => "open",
                "contact_types" => "interaction",
                "courses" => [
                    [
                        "dept_code"=> "Maths",
                        "subject_code"=> "SUB0M",
                        "course_number"=> "CN0M",
                        "section_numbers"=> [
                                 "A"
                            ]
                    ]
                ]
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

    public function testCreateCustomSearchesForISP(ApiTester $I)
    {
        $I->wantTo('Create Custom Search for ISP by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "search_attributes" => [
                "risk_indicator_ids" => "",
                "intent_to_leave_ids" => "",
                "group_ids" => "1",
                "student_ids" => "",
                "referral_status" => "",
                "contact_types" => "non-interaction",
                "courses" => [],
                "isps" => [
                    
                    [
                        "id" => 1,
                        "item_data_type" => "N",
                        "is_single" => false,
                        "min_digits" => "30",
                        "max_digits" => "40"
                    ],
                    [
                        "id" => 2,
                        "item_data_type" => "S",
                        "category_type" => [
                            [
                                "answer" => "BCA",
                                "value" => "1"
                            ],
                            [
                                "answer" => "MCA",
                                "value" => "2"
                            ]
                        ]
                    ],
                    [
                        "id" => 3,
                        "item_data_type" => "D",
                        "start_date" => "2014-12-16",
                        "end_date" => "2015-12-16"
                    ]
                ]
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

    public function testCreateCustomSearchesForEBI(ApiTester $I)
    {
        $I->wantTo('Create Custom Search for EBI by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "search_attributes" => [
                "risk_indicator_ids" => "",
                "intent_to_leave_ids" => "",
                "group_ids" => "",
                "referral_status" => "null",
                "contact_types" => "null",
                "courses" => [],
                "isps" => [
                    ],
                "datablocks" => [
                    [
                        "profile_block_id" => 1,
                        "profile_items" => [
                            [
                                "id" => 4,
                                "item_data_type" => "N",
                                "is_single" => false,
                                "min_digits" => "80",
                                "max_digits" => "90"
                            ],
                            
                            [
                                "id" => 5,
                                "item_data_type" => "S",
                                "category_type" => [
                                    [
                                        "answer" => "BCA",
                                        "value" => "1"
                                    ],
                                    [
                                        "answer" => "MCA",
                                        "value" => "2"
                                    ]
                                ]
                            ],
                            
                            [
                                "id" => 6,
                                "item_data_type" => "D",
                                "start_date" => "2014-12-16",
                                "end_date" => "2015-12-16"
                            ]
                        ]
                    ]
                ]
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
    
    public function testCreateCustomSearchesInvalidOrg(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => -5,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "1,2,3",
            "intent_to_leave_ids" => "",
            "group_ids" => "1",
            "referral_status" => "closed",
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
        $I->seeResponseContains('Organization Not Found.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testCreateCustomSearchesForAcademicUpdates(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search with Academic Update Filter Criteria by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "",
            "intent_to_leave_ids" => "",
            "group_ids" => "",
            "referral_status" => "open",
            "contact_types" => "",
            "courses" => [],
            "isps" => [],
            "datablocks" => [],
            "academic_updates" => [
                "grade" => "A,B,C",
                "absences" => "",
                "absence_range" => [
                    "min_range" => "0",
                    "max_range" => "20"
                ],
                "is_current_academic_year" => true,
                "start_date" => "2015-08-12",
                "end_date" => "2016-08-11",
                "failure_risk" => true,
                "ignoreThis" => "",
                "isBlankAcadUpdate" => false
            ],
            "survey" => [],
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
    
    public function testCreateCustomSearchesForAcademicUpdatesWithLowRiskAndNoGrades(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search for Academic Update Filter Criteria with Low Risk And No Grades by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "",
            "intent_to_leave_ids" => "",
            "group_ids" => "",
            "referral_status" => "",
            "contact_types" => "",
            "courses" => [],
            "isps" => [],
            "datablocks" => [],
            "academic_updates" => [
            "grade" => "",
            "absences" => "10",
            "absence_range" => [
            "min_range" => "",
            "max_range" => ""
            ],
            "is_current_academic_year" => true,
            "start_date" => "2015-08-12",
            "end_date" => "2016-08-11",
            "failure_risk" => false,
            "ignoreThis" => "",
            "isBlankAcadUpdate" => false
            ],
            "survey" => [],
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
    
    public function testCreateCustomSearchesForAcademicUpdatesWithNoStartAndEndDate(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search for Academic Update Filter Criteria with No Start And End Date by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "",
            "intent_to_leave_ids" => "",
            "group_ids" => "",
            "referral_status" => "",
            "contact_types" => "",
            "courses" => [],
            "isps" => [],
            "datablocks" => [],
            "academic_updates" => [
            "grade" => "",
            "absences" => "10",
            "absence_range" => [
            "min_range" => "",
            "max_range" => ""
            ],
            "is_current_academic_year" => true,
            "start_date" => "",
            "end_date" => "",
            "failure_risk" => "",
            "ignoreThis" => "",
            "isBlankAcadUpdate" => false
            ],
            "survey" => [],
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
    
    public function testCreateCustomSearchesForCoursesWithNoDeptCode(ApiTester $I)
    {
        $I->wantTo('Create Custom Search for courses With No Dept Code by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "search_attributes" => [
            "risk_indicator_ids" => "",
            "intent_to_leave_ids" => "",
            "student_status" => 0,
            "group_ids" => "1",
            "referral_status" => "open",
            "contact_types" => "interaction",
            "courses" => [
            [
            "dept_code"=> "",
            "subject_code"=> "SUB0M",
            "course_number"=> "CN0M",
            "section_numbers"=> [
            "A"
            ]
            ]
            ]
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
    
    public function testCreateCustomSearchesForCoursesWithNoSubjectCode(ApiTester $I)
    {
        $I->wantTo('Create Custom Search for courses With No Subject Code by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "search_attributes" => [
            "risk_indicator_ids" => "",
            "intent_to_leave_ids" => "",
            "student_status" => 0,
            "group_ids" => "1",
            "referral_status" => "open",
            "contact_types" => "interaction",
            "courses" => [
            [
            "dept_code"=> "Maths",
            "subject_code"=> "",
            "course_number"=> "CN0M",
            "section_numbers"=> [
            "A"
            ]
            ]
            ]
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
    
    public function testCreateCustomSearchesForCoursesWithNoCourseNumber(ApiTester $I)
    {
        $I->wantTo('Create Custom Search for courses With No Course Number by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "search_attributes" => [
            "risk_indicator_ids" => "",
            "intent_to_leave_ids" => "",
            "student_status" => 0,
            "group_ids" => "1",
            "referral_status" => "open",
            "contact_types" => "interaction",
            "courses" => [
            [
            "dept_code"=> "Maths",
            "subject_code"=> "SUB0M",
            "course_number"=> "",
            "section_numbers"=> [
            "A"
            ]
            ]
            ]
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
    
    public function testCreateCustomSearchesForCoursesWithNoSectionNumbers(ApiTester $I)
    {
        $I->wantTo('Create Custom Search for courses With No Section Numbers by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search', [
            "organization_id" => $this->organization,
            "search_attributes" => [
            "risk_indicator_ids" => "",
            "intent_to_leave_ids" => "",
            "student_status" => 0,
            "group_ids" => "1",
            "referral_status" => "open",
            "contact_types" => "interaction",
            "courses" => [
            [
            "dept_code"=> "Maths",
            "subject_code"=> "SUB0M",
            "course_number"=> "CN0M",
            "section_numbers"=> []
            ]
            ]
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
    
    public function testCreateCustomSearchesWithData(ApiTester $I, $scenario)
    {
        $I->wantTo('Create Custom Search With Data by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('search?sortBy=-student_last_name', [
            "organization_id" => $this->organization,
            "person_id" => $this->validPerson,
            "search_attributes" => [
            "risk_indicator_ids" => "2,3",
            "intent_to_leave_ids" => "",
            "student_status" => "",
            "group_ids" => "",
            "referral_status" => "closed",
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
        $I->seeResponseContains('search_attributes');
        $I->seeResponseContains('total_records');
        $I->seeResponseContains('total_pages');
        $I->seeResponseContains('records_per_page');
        $I->seeResponseContains('current_page');
    
        if(array_key_exists('search_result', $customSearch)){
            $searchResults = $customSearch->data->search_result;
            foreach ($searchResults as $searchResult){
                
                $I->seeResponseContains('student_id');
                $I->seeResponseContains('student_first_name');
                $I->seeResponseContains('image_name');
                $I->seeResponseContains('student_risk_status');
                $I->seeResponseContains('student_risk_image_name');
                $I->seeResponseContains('student_status');
                $I->seeResponseContains('student_last_name');
            }
        }
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testCreateCustomSearchesForCSV(ApiTester $I, $scenario)
    {
    	$I->wantTo('Create Custom Search by API For CSV Download');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendPOST('search?output-format=csv', [
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
    			"isqs" => [],
    			"cohort_ids" => ""
    			]
    			]);
       	$I->seeResponseContains('You may continue to use Mapworks while your download completes. We will notify you when it is available.');
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
}
