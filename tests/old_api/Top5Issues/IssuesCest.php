<?php
require_once 'tests/api/SynapseTestHelper.php';

class Issues extends SynapseTestHelper
{

    private $token;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testGetIssuesList(ApiTester $I)
    {
        $I->wantTo('Get Issues List');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $issueName = "Issue-Factor" . rand(1, 10);
        $I->sendPOST('issues', [
            "lang_id" => 1,
            "issue_name" => $issueName,
            "issue_image" => "dash-module-issue-classes-icon.png",
            "survey_id" => 1,
            "factors" => [
                "id" => 1,
                "text" => "Factor 1",
                "range_min" => "1",
                "range_max" => "10"
            ]
        ]);
        $resp = json_decode($I->grabResponse());
        $id = $resp->data->id;
        $I->sendGet('issues');
        // var_dump(json_decode($I->grabResponse())); exit;
        $I->seeResponseCodeIs(200);
        //$I->seeResponseContains('id');
        //$I->seeResponseContains('top_issue_name');
       // $I->seeResponseContains('top_issue_image');
        $I->seeResponseIsJson(array(
            'id' => $id,
            'top_issue_name' => $issueName,
            'top_issue_image' => 'dash-module-issue-classes-icon.png'
        ));
        
        $I->seeResponseIsJson();
    }

    public function testCreateIssueByFactor(ApiTester $I)
    {
        $I->wantTo('Create Issue by factor');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $issueName = "Issue-Factor" . rand(11, 20);
        $I->sendPOST('issues', [
            "lang_id" => 1,
            "issue_name" => $issueName,
            "issue_image" => "dash-module-issue-classes-icon.png",
            "survey_id" => 1,
            "factors" => [
                "id" => 1,
                "text" => "Factor 1",
                "range_min" => "1",
                "range_max" => "10"
            ]
        ]);
        
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        
        $I->seeResponseContains('id');
    }

    public function testCreateIssueByEbiQuestion(ApiTester $I)
    {
        $I->wantTo('Create Issue by EbiQuestion');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $issueName = "Issue-Factor" . rand(21, 30);
        $I->sendPOST('issues', [
            "lang_id" => 1,
            "issue_name" => $issueName,
            "issue_image" => "dash-module-issue-classes-icon.png",
            "survey_id" => 1,
            "questions" => [
                "id" => 8,
                "type" => "category",
                "text" => "In an average week, how many hours do you spend towards your work responsibilities?",
                "options" => [
                    [
                        "id" => 1,
                        "text" => "(1) Not at all",
                        "value" => "1"
                    ]
                ]
            ]
        ]);
        
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        
        $I->seeResponseContains('id');
    }

    public function testGetIssueByIdOfFactor(ApiTester $I)
    {
        $I->wantTo('Get Issue by Id');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $issueName = "Issue-Factor" . rand(31, 40);
        $I->sendPOST('issues', [
            "lang_id" => 1,
            "issue_name" => $issueName,
            "issue_image" => "dash-module-issue-classes-icon.png",
            "survey_id" => 1,
            "factors" => [
                "id" => 1,
                "text" => "Factor 1",
                "range_min" => "1",
                "range_max" => "10"
            ]
        ]);
        
        $resp = json_decode($I->grabResponse());
        $id = $resp->data->id;
        $I->sendGet('issues/' . $id);
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('id');
        
        $I->seeResponseIsJson(array(
            'id' => $id,
            'lang_id' => 1,
            'issue_name' => $issueName,
            'survey_id' => 1
        ));
        $I->seeResponseIsJson();
    }
    
    public function testGetIssueByIdOfEbiQuestion(ApiTester $I)
    {
    	$I->wantTo('Create Issue by EbiQuestion');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept-Language', null);
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$issueName = "Issue-EBI" . rand(71, 80);
    	$I->sendPOST('issues', [
    			"lang_id" => 1,
    			"issue_name" => $issueName,
    			"issue_image" => "dash-module-issue-classes-icon.png",
    			"survey_id" => 1,
    			"questions" => [
    			"id" => 8,
    			"type" => "category",
    			"text" => "In an average week, how many hours do you spend towards your work responsibilities?",
    			"options" => [
    			[
    			"id" => 1,
    			"text" => "(1) Not at all",
    			"value" => "1"
    			]
    			]
    			]
    			]);
    
    	$resp = json_decode($I->grabResponse());
        $id = $resp->data->id;
        $I->sendGet('issues/' . $id);
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('id');
        
        $I->seeResponseIsJson(array(
            'id' => $id,
            'lang_id' => 1,
            'issue_name' => $issueName,
            'survey_id' => 1
        ));
        $I->seeResponseIsJson();
    }

    public function testEditIssueOfFactor(ApiTester $I)
    {
        $I->wantTo('Edit Issue by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $issueName = "Issue-Factor" . rand(41, 50);
        $I->sendPOST('issues', [
            "lang_id" => 1,
            "issue_name" => $issueName,
            "issue_image" => "dash-module-issue-classes-icon.png",
            "survey_id" => 1,
            "factors" => [
                "id" => 1,
                "text" => "Factor 1",
                "range_min" => "1",
                "range_max" => "10"
            ]
        ]);
        $resp = json_decode($I->grabResponse());
        $id = $resp->data->id;
        // $issueName = "Issue-Factor" . rand(51, 60);
        $I->sendPUT('issues', [
            'id' => $id,
            "lang_id" => 1,
            "issue_name" => $issueName,
            "issue_image" => "dash-module-issue-courses-icon.png",
            "survey_id" => 1,
            "factors" => [
                "id" => 1,
                "text" => "Factor 1",
                "range_min" => "1",
                "range_max" => "10"
            ]
        ]);
        $I->seeResponseCodeIs(204);
    }

    public function testEditIssueOfEbiQuestion(ApiTester $I)
    {
    	$I->wantTo('Edit Issue by EbiQuestion');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept-Language', null);
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$issueName = "Issue-EBI" . rand(81, 90);
    	$I->sendPOST('issues', [
    			"lang_id" => 1,
    			"issue_name" => $issueName,
    			"issue_image" => "dash-module-issue-classes-icon.png",
    			"survey_id" => 1,
    			"questions" => [
    			"id" => 8,
    			"type" => "category",
    			"text" => "In an average week, how many hours do you spend towards your work responsibilities?",
    			"options" => [
    			[
    			"id" => 1,
    			"text" => "(1) Not at all",
    			"value" => "1"
    			]
    			]
    			]
    			]);
    
    	$resp = json_decode($I->grabResponse());
    	$id = $resp->data->id;
    	$I->sendPUT('issues', [
            'id' => $id,
            "lang_id" => 1,
            "issue_name" => $issueName,
            "issue_image" => "dash-module-issue-courses-icon.png",
            "survey_id" => 1,
            "questions" => [
                "id" => 9,
                "type" => "category",
                "text" => "Please identify the course in which you are having the most difficulty (e.g., English 101).",
                "options" => [
                    [
                        "id" => 8,
                        "text" => "(1) Not at all",
                        "value" => "1"
                    ],
                    [
                        "id" => 9,
                        "text" => "(2)",
                        "value" => "2"
                    ]
                ]
            ]
        ]);
        $I->seeResponseCodeIs(204);
    }
    
    public function testDeleteIssue(ApiTester $I)
    {
        $I->wantTo('Delete Issue by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $issueName = "Issue-Factor" . rand(61, 70);
        $I->sendPOST('issues', [
            "lang_id" => 1,
            "issue_name" => $issueName,
            "issue_image" => "dash-module-issue-classes-icon.png",
            "survey_id" => 1,
            "factors" => [
                "id" => 1,
                "text" => "Factor 1",
                "range_min" => "1",
                "range_max" => "10"
            ]
        ]);
        $resp = json_decode($I->grabResponse());
        $id = $resp->data->id;
        $I->sendDelete('issues?issueId=' . $id);
        $I->seeResponseCodeIs(204);
    }

    public function testGetTop5Issues(ApiTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('Get top 5 issues');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGet('issues/top5?survey_id=1&cohort_id=1');
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('id');
        $I->seeResponseContains('issue_name');
        $I->seeResponseContains('issue_icon');
        $I->seeResponseContains('percentage');
        
        $I->seeResponseIsJson();
    }

    public function testGetIssueByInvalidId(ApiTester $I)
    {
        $I->wantTo('Get issues By Id');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGet('issues/-1');
        
        $I->seeResponseCodeIs(400);
    }
}