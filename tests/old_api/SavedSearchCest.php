<?php
use GuzzleHttp\json_decode;
require_once 'SynapseTestHelper.php';

class SavedSearchCest extends SynapseTestHelper
{

    private $token;

    private $organization = 1;

    private $savedSearchName = "My saved search name ";

    private $invalidOrganization = -200;

    private $invalidPerson = - 1;

    private $invalidSaveSearchId = 0;

    private $savedSearch = "";

    private $saveSearchId = 0;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
        $this->savedSearchName .= rand(1000, 20000);
    }

    public function testCreateSavedSearches(ApiTester $I)
    {
        $I->wantTo('Create Saved Search by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => $this->savedSearchName,
            "search_attributes" => [
                "risk_indicator_ids" => "2",
                "intent_to_leave_ids" => "10,20",
                "group_ids" => "1,5,7",
                "referral_status" => "open",
                "contact_types" => "interaction"
            ]
        ]);
        $this->savedSearch = json_decode($I->grabResponse());
        $this->saveSearchId = $this->savedSearch->data->saved_search_id;
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContains('saved_search_id');
    }

    public function testCreateSavedSearchWithDuplicateName(ApiTester $I)
    {
        $I->wantTo('Create Saved Search With Duplicate Search Name by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => $this->savedSearch->data->saved_search_name,
            "search_attributes" => [
                "risk_indicator_ids" => "2",
                "intent_to_leave_ids" => "10,20",
                "group_ids" => "1,5,7",
                "referral_status" => "open",
                "contact_types" => "interaction"
            ]
        ]);
        $I->seeResponseContains('Saved Search name already exists in this organization.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateSavedSearchWithInvalidOrganization(ApiTester $I)
    {
        $I->wantTo('Create Saved Search With Invalid organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->invalidOrganization,
            "saved_search_name" => $this->savedSearch->data->saved_search_name,
            "search_attributes" => [
                "risk_indicator_ids" => "2",
                "intent_to_leave_ids" => "10,20",
                "group_ids" => "1,5,7",
                "referral_status" => "open",
                "contact_types" => "interaction"
            ]
        ]);
        $I->seeResponseContains('Organization Not Found.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testEditSavedSearch(ApiTester $I)
    {
        $I->wantTo('Edit a Saved Search by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_id" => $this->saveSearchId,
            "saved_search_name" => $this->savedSearchName,
            "person_id" => $this->savedSearch->data->person_id,
            "search_attributes" => [
                "risk_indicator_ids" => "2",
                "intent_to_leave_ids" => "10,20",
                "group_ids" => "1,5,7",
                "referral_status" => "open",
                "contact_types" => "interaction"
            ]
        ]);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }

    public function testEditSavedSearchWithInvalidPerson(ApiTester $I)
    {
        $I->wantTo('Edit a Saved Search With Invalid Person by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_id" => $this->saveSearchId,
            "saved_search_name" => $this->savedSearchName,
            "person_id" => $this->invalidPerson,
            "search_attributes" => [
                "risk_indicator_ids" => "2",
                "intent_to_leave_ids" => "10,20",
                "group_ids" => "1,5,7",
                "referral_status" => "open",
                "contact_types" => "interaction"
            ]
        ]);
        $I->seeResponseContains('Person Not Found.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testEditSavedSearchWithInvalidSearch(ApiTester $I)
    {
        $I->wantTo('Edit a Saved Search With Invalid Saved Search by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_id" => $this->invalidSaveSearchId,
            "saved_search_name" => $this->savedSearchName,
            "person_id" => $this->savedSearch->data->person_id,
            "search_attributes" => [
                "risk_indicator_ids" => "2",
                "intent_to_leave_ids" => "10,20",
                "group_ids" => "1,5,7",
                "referral_status" => "open",
                "contact_types" => "interaction"
            ]
        ]);
        $I->seeResponseContains('Saved Query Not Found.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCancelSavedSearch(ApiTester $I)
    {
        $I->wantTo('Cancel a Saved Search by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('savedsearches/' . $this->saveSearchId);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }

    public function testCancelSavedSearchWithInvalidSearch(ApiTester $I)
    {
        $I->wantTo('Cancel a Saved Search With Invalid Saved Search by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('savedsearches/' . $this->invalidSaveSearchId);
        $I->seeResponseContains('Saved Query Not Found.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateSavedSearchesForCourse(ApiTester $I)
    {
        $I->wantTo('Create Saved Search for course by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => $this->savedSearchName,
            "search_attributes" => [
                "risk_indicator_ids" => "2",
                "intent_to_leave_ids" => "10,20",
                "group_ids" => "1,5,7",
                "referral_status" => "open",
                "contact_types" => "interaction",
                "courses" => [
                    "department_id" => "IT",
                    "subject_id" => "SEC001",
                    "course_ids" => "0087",
                    "section_ids" => "SEC 1"
                ]
            ]
        ]);
        $this->savedSearch = json_decode($I->grabResponse());
        $this->saveSearchId = $this->savedSearch->data->saved_search_id;
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContains('saved_search_id');
    }

    public function testCreateSavedSearchesForISP(ApiTester $I)
    {
        $I->wantTo('Create Saved Search for ISP by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => $this->savedSearchName,
            "search_attributes" => [
                "risk_indicator_ids" => "2",
                "intent_to_leave_ids" => "10,20",
                "group_ids" => "1,5,7",
                "referral_status" => "open",
                "contact_types" => "interaction",
                "courses" => [
                    "department_id" => "IT",
                    "subject_id" => "SEC001",
                    "course_ids" => "0087",
                    "section_ids" => "SEC 1"
                ],
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
        $this->savedSearch = json_decode($I->grabResponse());
        $this->saveSearchId = $this->savedSearch->data->saved_search_id;
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContains('saved_search_id');
    }

    public function testCreateSavedSearchesForEBI(ApiTester $I)
    {
        $I->wantTo('Create Saved Search for EBI by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => $this->savedSearchName,
            "search_attributes" => [
                "risk_indicator_ids" => "2",
                "intent_to_leave_ids" => "10,20",
                "group_ids" => "1,5,7",
                "referral_status" => "open",
                "contact_types" => "interaction",
                "courses" => [
                    "department_id" => "IT",
                    "subject_id" => "SEC001",
                    "course_ids" => "0087",
                    "section_ids" => "SEC 1"
                ],
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
        $this->savedSearch = json_decode($I->grabResponse());
        $this->saveSearchId = $this->savedSearch->data->saved_search_id;
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContains('saved_search_id');
    }

    public function testEditSavedSearchForAll(ApiTester $I)
    {
        $I->wantTo('Edit a Saved Search for all attributes by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_id" => 1,
            "saved_search_name" => "Search1",
            "person_id" => 1,
            "search_attributes" => [
                "risk_indicator_ids" => "2",
                "intent_to_leave_ids" => "10,20",
                "group_ids" => "1,5,7",
                "referral_status" => "open",
                "contact_types" => "interaction",
                "courses" => [
                    "department_id" => "IT",
                    "subject_id" => "SEC001",
                    "course_ids" => "0087",
                    "section_ids" => "SEC 1"
                ],
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
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }
    
    public function testCreateSavedSearchesWithEmptyName(ApiTester $I)
    {
        $I->wantTo('Create Saved Search With Empty Name by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => "",
            "search_attributes" => [
            "risk_indicator_ids" => "2",
            "intent_to_leave_ids" => "10,20",
            "group_ids" => "1,5,7",
            "referral_status" => "open",
            "contact_types" => "interaction"
            ]
            ]);
        $savedSearch = json_decode($I->grabResponse());
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Name field cannot be empty');
    }
    
    public function testCreateSavedSearchesWithNameLengthMoreThanAllowed(ApiTester $I)
    {
        $I->wantTo('Create Saved Search With Name Length More Than Allowed by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $searchName = '';
        for($i = 0;$i<14;$i++){
            $searchName .= uniqid("SavedSearch",true);
        }
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => $searchName,
            "search_attributes" => [
            "risk_indicator_ids" => "2",
            "intent_to_leave_ids" => "10,20",
            "group_ids" => "1,5,7",
            "referral_status" => "open",
            "contact_types" => "interaction"
            ]
            ]);
        $savedSearch = json_decode($I->grabResponse());

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Name cannot be more than 120 character limit');
    }
    
    public function testCreateSavedSearchesWithNoContactTypes(ApiTester $I)
    {
        $I->wantTo('Create Saved Search With No Contact Types by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => uniqid("SavedSearch",true),
            "search_attributes" => [
            "risk_indicator_ids" => "2",
            "intent_to_leave_ids" => "10,20",
            "group_ids" => "1,5,7",
            "referral_status" => "open",
            "contact_types" => ""
            ]
            ]);
        $savedSearch = json_decode($I->grabResponse());

        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContains('saved_search_id');
        $I->seeResponseContainsJson(array('organization_id' => $this->organization));
        $I->seeResponseContains('person_id');
        $I->seeResponseContains('search_attributes');
    }
    
    public function testEditSavedSearchWithEmptyName(ApiTester $I)
    {
        $I->wantTo('Edit a Saved Search With Empty Name by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => uniqid("SavedSearch",true),
            "search_attributes" => [
            "risk_indicator_ids" => "2",
            "intent_to_leave_ids" => "10,20",
            "group_ids" => "1,5,7",
            "referral_status" => "open",
            "contact_types" => ""
            ]
            ]);
        $savedSearch = json_decode($I->grabResponse());

        $I->sendPUT('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_id" => $savedSearch->data->saved_search_id,
            "saved_search_name" => "",
            "person_id" => $savedSearch->data->person_id,
            "search_attributes" => [
            "risk_indicator_ids" => "2",
            "intent_to_leave_ids" => "10,20",
            "group_ids" => "1,5,7",
            "referral_status" => "open",
            "contact_types" => "interaction"
            ]
            ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Name field cannot be empty');
    }
    
    public function testEditSavedSearchWithNameLengthMoreThanAllowed(ApiTester $I)
    {
        $I->wantTo('Edit a Saved Search With Name Length More Than Allowed by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
    
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => uniqid("SavedSearch",true),
            "search_attributes" => [
            "risk_indicator_ids" => "2",
            "intent_to_leave_ids" => "10,20",
            "group_ids" => "1,5,7",
            "referral_status" => "open",
            "contact_types" => ""
            ]
            ]);
        $savedSearch = json_decode($I->grabResponse());
    
        $searchName = '';
        for($i = 0;$i<14;$i++){
            $searchName .= uniqid("SavedSearch",true);
        }
        $I->sendPUT('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_id" => $savedSearch->data->saved_search_id,
            "saved_search_name" => $searchName,
            "person_id" => $savedSearch->data->person_id,
            "search_attributes" => [
            "risk_indicator_ids" => "2",
            "intent_to_leave_ids" => "10,20",
            "group_ids" => "1,5,7",
            "referral_status" => "open",
            "contact_types" => "interaction"
            ]
            ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Name cannot be more than 120 character limit');
    }

    public function testEditSavedSearchWithEmptyContactTypes(ApiTester $I)
    {
        $I->wantTo('Edit a Saved Search With Empty Contact Types by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
    
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => uniqid("SavedSearch",true),
            "search_attributes" => [
            "risk_indicator_ids" => "2",
            "intent_to_leave_ids" => "10,20",
            "group_ids" => "1,5,7",
            "referral_status" => "open",
            "contact_types" => ""
            ]
            ]);
        $savedSearch = json_decode($I->grabResponse());

        $I->sendPUT('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_id" => $savedSearch->data->saved_search_id,
            "saved_search_name" => uniqid("SavedSearch",true),
            "person_id" => $savedSearch->data->person_id,
            "search_attributes" => [
            "risk_indicator_ids" => "2",
            "intent_to_leave_ids" => "10,20",
            "group_ids" => "1,5,7",
            "referral_status" => "open",
            "contact_types" => ""
            ]
            ]);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }
    
    public function testGetSavedSearch(ApiTester $I)
    {
        $I->wantTo('Get Saved Search by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => uniqid("SavedSearch",true),
            "search_attributes" => [
            "risk_indicator_ids" => "2",
            "intent_to_leave_ids" => "10,20",
            "group_ids" => "1,5,7",
            "referral_status" => "open",
            "contact_types" => ""
            ]
            ]);
        $savedSearch = json_decode($I->grabResponse());
        $I->sendGET('savedsearches/'.$savedSearch->data->saved_search_id);
        
        $I->seeResponseContainsJson(array('organization_id' => $this->organization));
        $I->seeResponseContainsJson(array('saved_search_id' => $savedSearch->data->saved_search_id));
        $I->seeResponseContains('date_created');
        $I->seeResponseContains('person_id');
        $I->seeResponseContains('search_attributes');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetSavedSearchWithInvalidId(ApiTester $I)
    {
        $I->wantTo('Get Saved Search With Invalid Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('savedsearches/'.$this->invalidSaveSearchId);
    
        $I->seeResponseContains('Saved Query Not Found.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testListSavedSearches(ApiTester $I)
    {
        $I->wantTo('List Saved Search by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
    
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => uniqid("SavedSearch",true),
            "search_attributes" => [
            "risk_indicator_ids" => "2",
            "intent_to_leave_ids" => "10,20",
            "group_ids" => "1,5,7",
            "referral_status" => "open",
            "contact_types" => ""
            ]
            ]);
        
        $I->sendGET('savedsearches');
        $listSearches = json_decode($I->grabResponse());
        
        foreach($listSearches as $search){
            $I->seeResponseContains('saved_search_id');
            $I->seeResponseContains('search_name');
            $I->seeResponseContains('date_created');
        }
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}