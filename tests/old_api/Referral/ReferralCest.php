<?php
require_once 'tests/api/SynapseTestHelper.php';
use \Codeception\Scenario;

class ReferralCest extends SynapseTestHelper //TODO: Change this back to being included once this test can pass
{

    private $organizationId = 1;

    private $personStudentId = 1;
    
    private $personAnotherStudentId = 1;

    private $personFacultyId = 1;

    private $assignedTo = 1;
    private $assignedTo2 = 1;

    private $invalidStudent = -1;

    private $invalidFaculty = -1;

    private $referrerId = 1;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }
    
    private function referralData() {
        $referralRequest = [
            "organization_id" => $this->organizationId,
            "person_student_id" => $this->personStudentId,
            "person_staff_id" => $this->personFacultyId,
            "reason_category_subitem_id" => "2",
            "assigned_to_user_id" => $this->assignedTo,
            "interested_parties" => [
                [
                    "id" => 1
                ]
            ],
            "comment" => "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea",
            "issue_discussed_with_student" => true,
            "high_priority_concern" => false,
            "issue_revealed_to_student" => true, 
            "student_indicated_to_leave" => false,
            "notify_student" => true,
            "share_options" => [
                [
                    "private_share" => false,
                    "public_share" => true,
                    "teams_share" => false,
                    "team_ids" => [
                        [
                            "id" => 1,
                            "is_team_selected" => true
                        ]
                    ]
                ]
            ]
        ];
        
        return $referralRequest;
    }
   
    
   public function testCreateReferral(ApiTester $I, \Codeception\Scenario $scenario)
    {       
      
        $I->wantTo('Create an Referral by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');

        $I->sendPOST('referrals', $this->referralData());               
        $resp = json_decode($I->grabResponse());
        $referrerId = $resp->data->referral_id;
        $this->referrerId = $referrerId;
        $I->seeResponseContains('referral_id');
        $I->seeResponseIsJson(array(
            'organization_id' => $this->organizationId,
            'person_student_id' => $this->personStudentId,
            'person_staff_id' => $this->personFacultyId
        ));
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
    
    public function testDashboardReferral(ApiTester $I, Scenario $scenario)
    {    
        $I->wantTo('get Referrals for dashboard');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('referrals/dashboard?filter=all&status=C&offset=25&page_no=1&sortBy=-referral_date');
        $data = json_decode($I->grabResponse());
        
     $I->seeResponseContains('person_id');
		$I->seeResponseContains('organization_id');
		$I->seeResponseContains('total_records');
		$I->seeResponseContains('referrals');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testDashboardReferralCSV(ApiTester $I)
    {
        $I->wantTo('get Referrals for dashboard for CSV');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('referrals/dashboard?filter=all&status=C&offset=25&page_no=1&sortBy=-referral_date&output-format=csv');
        $data = json_decode($I->grabResponse());
    
        $I->seeResponseContains('You may continue to use Mapworks while your download completes. We will notify you when it is available.');
   
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testCreateReferralInvalidFaculty(ApiTester $I, Scenario $scenario)
    {
        //$scenario->skip("Skipping due to: PHP Fatal error:  Undefined class constant 'COURSES_ACCESS' in /vagrant/synapse-backend/src/Synapse/CoreBundle/Service/Impl/OrgPermissionsetService.php on line 1017");
        $I->wantTo('Create referral with invalid authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('referrals', [
            "organization_id" => $this->organizationId,
            "person_student_id" => $this->personStudentId,
            "person_staff_id" => $this->invalidFaculty,
            "reason_category_subitem_id" => "2",
            "assigned_to_user_id" => 0,
            "interested_parties" => [
                [
                    "id" => 1
                ]
            ],
            "comment" => "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea",
            "issue_discussed_with_student" => true,
            "high_priority_concern" => false,
            "issue_revealed_to_student" => true,
            "student_indicated_to_leave" => false,
            "share_options" => [
                [
                    "private_share" => false,
                    "public_share" => true,
                    "teams_share" => false,
                    "team_ids" => [
                        [
                            "id" => 1,
                            "is_team_selected" => true
                        ]
                    ]
                ]
            ]
        ]);
        $data = json_decode($I->grabResponse());
        $I->seeResponseContains('Person Not Found.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateReferralInvalidStudent(ApiTester $I, Scenario $scenario)
    {
       // $scenario->skip("Skipping due to: PHP Fatal error:  Undefined class constant 'COURSES_ACCESS' in /vagrant/synapse-backend/src/Synapse/CoreBundle/Service/Impl/OrgPermissionsetService.php on line 1017");
        $I->wantTo('Create referral with invalid authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('referrals', [
            "organization_id" => $this->organizationId,
            "person_student_id" => $this->invalidStudent,
            "person_staff_id" => $this->personFacultyId,
            "reason_category_subitem_id" => "2",
            "assigned_to_user_id" => 0,
            "interested_parties" => [
                [
                    "id" => 1
                ]
            ],
            "comment" => "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea",
            "issue_discussed_with_student" => true,
            "high_priority_concern" => false,
            "issue_revealed_to_student" => true,
            "student_indicated_to_leave" => false,
            "share_options" => [
                [
                    "private_share" => false,
                    "public_share" => true,
                    "teams_share" => false,
                    "team_ids" => [
                        [
                            "id" => 1,
                            "is_team_selected" => true
                        ]
                    ]
                ]
            ]
        ]);
        $data = json_decode($I->grabResponse());
        $I->seeResponseContains('Unauthorized access to organization: '.$this->invalidStudent);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetReferral(ApiTester $I, Scenario $scenario)
    {
      //  $scenario->skip("Skipping due to: PHP Fatal error:  Undefined class constant 'COURSES_ACCESS' in /vagrant/synapse-backend/src/Synapse/CoreBundle/Service/Impl/OrgPermissionsetService.php on line 1017");
        $I->wantTo('get Referrals');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('referrals');
        $data = json_decode($I->grabResponse());
        
        $I->seeResponseIsJson(array(
            'person_id' => $this->assignedTo,
            'referrals[0]["referral_id"]' => $this->referrerId
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    
    public function testGetSentReferral(ApiTester $I, Scenario $scenario)
    {
      //  $scenario->skip("Skipping due to: PHP Fatal error:  Undefined class constant 'COURSES_ACCESS' in /vagrant/synapse-backend/src/Synapse/CoreBundle/Service/Impl/OrgPermissionsetService.php on line 1017");
        $I->wantTo('get sent Referrals');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('referrals?type=sent');
        $data = json_decode($I->grabResponse());        
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetReceivedReferral(ApiTester $I, Scenario $scenario)
    {
       // $scenario->skip("Skipping due to: PHP Fatal error:  Undefined class constant 'COURSES_ACCESS' in /vagrant/synapse-backend/src/Synapse/CoreBundle/Service/Impl/OrgPermissionsetService.php on line 1017");
        $I->wantTo('get received Referrals');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('referrals?type=received');
        $data = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    
    public function testGetReferralByInvalidId(ApiTester $I, Scenario $scenario)
    {
       // $scenario->skip("Skipping due to: PHP Fatal error:  Undefined class constant 'COURSES_ACCESS' in /vagrant/synapse-backend/src/Synapse/CoreBundle/Service/Impl/OrgPermissionsetService.php on line 1017");
        $I->wantTo('get Referrals with Invalid id');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('referrals/0');
        $data = json_decode($I->grabResponse());
        $I->seeResponseContains("Referral Not Found");
        $I->seeResponseCodeIs(400);
    }
    
    public function testGetReferralById(ApiTester $I, Scenario $scenario)
    {
      //  $scenario->skip("Skipping due to: PHP Fatal error:  Undefined class constant 'COURSES_ACCESS' in /vagrant/synapse-backend/src/Synapse/CoreBundle/Service/Impl/OrgPermissionsetService.php on line 1017");
        $I->wantTo('get Referrals with id');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('referrals', [
        		"organization_id" => $this->organizationId,
        		"person_student_id" => $this->personStudentId,
        		"person_staff_id" => $this->personFacultyId,
        		"reason_category_subitem_id" => "2",
        		"assigned_to_user_id" => $this->assignedTo,
        		"interested_parties" => [
        		[
        		"id" => 1
        		]
        		],
        		"comment" => "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea",
        		"issue_discussed_with_student" => true,
        		"high_priority_concern" => false,
        		"issue_revealed_to_student" => true,
        		"student_indicated_to_leave" => false,
        		"share_options" => [
        		[
        		"private_share" => false,
        		"public_share" => true,
        		"teams_share" => false,
        		"team_ids" => [
        		[
        		"id" => 1,
        		"is_team_selected" => true
        		]
        		]
        		]
        		]
        		]);
        
        $resp = json_decode($I->grabResponse());
        $referrerId = $resp->data->referral_id;
        
        $I->sendGET('referrals/'.$referrerId);
        $data = json_decode($I->grabResponse());
        
        $I->seeResponseIsJson(array(
            'person_id' => $this->assignedTo,
            'referrals[0]["referral_id"]' => $this->referrerId
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	
    

    public function testGetReferralParties(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('get referral interested parties');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('referrals/interestedParties?orgId=' . $this->organizationId . '&studentId=' . $this->personStudentId);
        $data = json_decode($I->grabResponse());
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetReferralPartiesInvalidStudent(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('get referral interested parties invalid student');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('referrals/interestedParties?orgId=' . $this->organizationId . '&studentId=' . $this->invalidStudent);
      
        $data = json_decode($I->grabResponse());
     
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testGetReferralAssignees(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('get referral Assignees');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('referrals/assignees?orgId=' . $this->organizationId . '&studentId=' . $this->personStudentId);
        $data = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'assigned_to_users[0]["id"]' => 0
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetReferralAssigneeInvalidStudent(ApiTester $I, $scenario)
    {    
        $I->wantTo('get referral Assignees invalid person');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('referrals/assignees?orgId=' . $this->organizationId . '&studentId=' . $this->invalidStudent);    
        $data = json_decode($I->grabResponse());    
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    

    public function testEditReferral(ApiTester $I)
    {
        $I->wantTo('Edit an Referral by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('referrals', [
        		"organization_id" => $this->organizationId,
        		"person_student_id" => $this->personStudentId,
        		"person_staff_id" => $this->personFacultyId,
        		"reason_category_subitem_id" => "2",
        		"assigned_to_user_id" => $this->assignedTo,
        		"interested_parties" => [
        		[
        		"id" => 1
        		]
        		],
        		"comment" => "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea",
        		"issue_discussed_with_student" => true,
        		"high_priority_concern" => false,
        		"issue_revealed_to_student" => true,
        		"student_indicated_to_leave" => false,
        		"share_options" => [
        		[
        		"private_share" => false,
        		"public_share" => true,
        		"teams_share" => false,
        		"team_ids" => [
        		[
        		"id" => 1,
        		"is_team_selected" => true
        		]
        		]
        		]
        		]
        		]);
        
        $resp = json_decode($I->grabResponse());
        $referrerId = $resp->data->referral_id;
        
        $I->sendPUT('referrals', [
            "organization_id" => $this->organizationId,
            "person_student_id" => $this->personAnotherStudentId,
            "person_staff_id" => $this->personFacultyId,
            "referral_id" => $referrerId,
            "reason_category_subitem_id" => "2",
            "assigned_to_user_id" => $this->assignedTo,
            "interested_parties" => [
            [
            "id" => 1
            ]
            ],
            "comment" => "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea",
            "issue_discussed_with_student" => true,
            "high_priority_concern" => false,
            "issue_revealed_to_student" => true,
            "student_indicated_to_leave" => false,
            "share_options" => [
            [
            "private_share" => false,
            "public_share" => true,
            "teams_share" => false,
            "team_ids" => [
            [
            "id" => 1,
            "is_team_selected" => true
            ]
            ]
            ]
            ]
            ]);
        $I->seeResponseCodeIs(204);
    }	/* Need to be Fixed*/
    
  public function testGetReferralActivity(ApiTester $I, Scenario $scenario)
    {
    	$I->wantTo('get Referrals Activity by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->sendPOST('referrals', [
    			"organization_id" => $this->organizationId,
    			"person_student_id" => $this->personStudentId,
    			"person_staff_id" => $this->personFacultyId,
    			"reason_category_subitem_id" => "2",
    			"assigned_to_user_id" => $this->assignedTo,
    			"interested_parties" => [
    			[
    			"id" => 1
    			]
    			],
    			"comment" => "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea",
    			"issue_discussed_with_student" => true,
    			"high_priority_concern" => false,
    			"issue_revealed_to_student" => true,
    			"student_indicated_to_leave" => false,
    			"share_options" => [
    			[
    			"private_share" => false,
    			"public_share" => true,
    			"teams_share" => false,
    			"team_ids" => [
    			[
    			"id" => 1,
    			"is_team_selected" => true
    			]
    			]
    			]
    			]
    			]);
    
    	$resp = json_decode($I->grabResponse());
    	 
    	$I->sendGET('students/activity?student-id='.$this->personStudentId.'&category=referral&is-interaction=false');
    	$data = json_decode($I->grabResponse());
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
    
    public function testGetReferralActivityRelated(ApiTester $I, Scenario $scenario)
    {
    	$I->wantTo('get Referrals Related Activity by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->sendPOST('referrals', [
    			"organization_id" => $this->organizationId,
    			"person_student_id" => $this->personStudentId,
    			"person_staff_id" => $this->personFacultyId,
    			"reason_category_subitem_id" => "2",
    			"assigned_to_user_id" => $this->assignedTo,
    			"interested_parties" => [
    			[
    			"id" => 1
    			]
    			],
    			"comment" => "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea",
    			"issue_discussed_with_student" => true,
    			"high_priority_concern" => false,
    			"issue_revealed_to_student" => true,
    			"student_indicated_to_leave" => false,
    			"share_options" => [
    			[
    			"private_share" => false,
    			"public_share" => true,
    			"teams_share" => false,
    			"team_ids" => [
    			[
    			"id" => 1,
    			"is_team_selected" => true
    			]
    			]
    			]
    			]
    			]);
    
    	$resp = json_decode($I->grabResponse());
    
    	$I->sendGET('students/activity?student-id=' . $this->personStudentId . '&category=referral&is-interaction=false');
    	$lists = json_decode($I->grabResponse());
    	$activityLogId = $lists->data->activities[0]->activity_log_id;
    
    	$I->sendPOST('referrals', [
    			"activity_log_id" => $activityLogId,
    			"organization_id" => $this->organizationId,
    			"person_student_id" => $this->personStudentId,
    			"person_staff_id" => $this->personFacultyId,
    			"reason_category_subitem_id" => "2",
    			"assigned_to_user_id" => $this->assignedTo,
    			"interested_parties" => [
    			[
    			"id" => 1
    			]
    			],
    			"comment" => "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea",
    			"issue_discussed_with_student" => true,
    			"high_priority_concern" => false,
    			"issue_revealed_to_student" => true,
    			"student_indicated_to_leave" => false,
    			"share_options" => [
    			[
    			"private_share" => false,
    			"public_share" => true,
    			"teams_share" => false,
    			"team_ids" => [
    			[
    			"id" => 1,
    			"is_team_selected" => true
    			]
    			]
    			]
    			]
    			]);
    
    	$I->sendGET('students/activity?student-id=' . $this->personStudentId . '&category=all&is-interaction=false');
    	$data = json_decode($I->grabResponse());
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
    
    
}
