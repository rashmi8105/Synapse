<?php

require_once 'tests/api/SynapseTestHelper.php';

class SharedSearchCest extends SynapseTestHelper
{
    private $token;
    private $organization = 1;
    private $invalidOrg = -5;
    private $savedSearchId = 1;
    private $invalidSavedSearchId = -2;
    private $personId = 1;
    private $personIdSharedWith = 2;
    public function _before(ApiTester $I)
    {
    	$this->token = $this->authenticate($I);
    
    }

    public function testCreateSharedSearchWithDiffName(ApiTester $I)
    {
        $I->wantTo("Create shared search with different name by API");
        $I->haveHttpHeader('Content-Type','application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept','application/json');
        $I->sendPOST('sharedsearches',[
        		'organization_id'=>$this->organization,
        		'saved_search_id'=> $this->savedSearchId,
        		'saved_search_name'=>uniqid("SharedSearch_",true),
        		'shared_by_person_id' => $this->personId,
                'shared_with_person_ids' => $this->personIdSharedWith
        		]);
        $sharedSearch = json_decode($I->grabResponse());
        $I->seeResponseContains('saved_search_name');
        $I->seeResponseContains('id');
        $I->seeResponseContainsJson(array('organization_id' => $this->organization));
        $I->seeResponseContainsJson( array('saved_search_id' => $this->savedSearchId) );
        $I->seeResponseContainsJson( array('shared_by_person_id' => $this->personId) );
        $I->seeResponseContainsJson( array('shared_with_person_ids' => "$this->personIdSharedWith") );
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
    
     public function testCreateSharedSearchWithSameName(ApiTester $I)
        {
            $I->wantTo("Create shared search with same name by API");
            $I->haveHttpHeader('Content-Type','application/json');
            $I->amBearerAuthenticated($this->token);
            $I->haveHttpHeader('Accept','application/json');
            $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => uniqid("SharedSearch_",true),
            "search_attributes" => [
                "risk_indicator_ids" => "2",
                "intent_to_leave_ids" => "10,20",
                "group_ids" => "1,5,7",
                "referral_status" => "open",
                "contact_types" => "interaction"
                ]
            ]);
            $createSharedSearch = json_decode($I->grabResponse());
            $I->sendPOST('sharedsearches',[
                'organization_id'=>$this->organization,
                'saved_search_id'=> $createSharedSearch->data->saved_search_id,
                'saved_search_name'=>$createSharedSearch->data->saved_search_name,
                'shared_by_person_id' => $this->personId,
                'shared_with_person_ids' => $this->personIdSharedWith
                ]);
            $sharedSearch = json_decode($I->grabResponse());
            $I->seeResponseContains('saved_search_name');
            $I->seeResponseContains('id');
            $I->seeResponseContainsJson(array('organization_id' => $this->organization));
            $I->seeResponseContainsJson( array('saved_search_id' => $createSharedSearch->data->saved_search_id) );
            $I->seeResponseContainsJson( array('shared_by_person_id' => $this->personId) );
            $I->seeResponseContainsJson( array('shared_with_person_ids' => "$this->personIdSharedWith") );
            $I->seeResponseContainsJson( array('saved_search_name' => $createSharedSearch->data->saved_search_name) );
            $I->seeResponseCodeIs(201);
            $I->seeResponseIsJson();
        } 
    
    public function testGetSharedSearches(ApiTester $I){
        $I->wantTo("Get shared Searches by API");
        $I->haveHttpHeader('Content-Type','application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept','application/json');
        $I->sendPOST('sharedsearches',[
        		'organization_id'=>$this->organization,
        		'saved_search_id'=> $this->savedSearchId,
        		'saved_search_name'=>uniqid("SharedSearch_",true),
        		'shared_by_person_id' => $this->personId,
        		'shared_with_person_ids' => $this->personIdSharedWith
        		]);
        $sharedSearch = json_decode($I->grabResponse());
        $I->sendGET('sharedsearches');
        $sharedSearch = json_decode($I->grabResponse());
        $search = end($sharedSearch->data->shared_searches);
        $I->seeResponseContains('shared_searches');
        foreach ($sharedSearch as $search){
            
            $I->seeResponseContains("saved_search_id");
            $I->seeResponseContains("search_name");
            $I->seeResponseContains("shared_with_users");
            $I->seeResponseContains("shared_by_users");
        }
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();   
    }
    
    public function testEditSharedSearch(ApiTester $I)
    {
        $I->wantTo("Edit shared Searches by API");
        $I->haveHttpHeader('Content-Type','application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept','application/json');
        $I->sendPOST('sharedsearches',[
        		'organization_id'=>$this->organization,
        		'saved_search_id'=> $this->savedSearchId,
        		'saved_search_name'=>uniqid("SharedSearch_",true),
        		'shared_by_person_id' => $this->personId,
        		'shared_with_person_ids' => $this->personIdSharedWith
        		]);
        $createSharedSearch = json_decode($I->grabResponse());
        $I->sendPUT('sharedsearches',[
        		'organization_id'=>$this->organization,
        		'saved_search_id'=> $createSharedSearch->data->id,
        		'saved_search_name'=>uniqid("SharedSearch_",true),
        		'person_id' => $this->personIdSharedWith,
        		"search_attributes"=> [
                    "risk_indicator_ids"=> "1,2,3",
                    "intent_to_leave_ids"=> "10,20",
                    "group_ids"=> "1,2,1299",
                    "referral_status" => "open",
                    "contact_types" => "interaction"
                  ]
            ]);
        $editSharedSearch = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();  
        
    }
    
    public function testDeleteSharedSearch(ApiTester $I)
    {
        $I->wantTo("Delete shared Searches by API");
        $I->haveHttpHeader('Content-Type','application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept','application/json');
        $I->sendPOST('sharedsearches',[
        		'organization_id'=>$this->organization,
        		'saved_search_id'=> $this->savedSearchId,
        		'saved_search_name'=>uniqid("SharedSearch_",true),
        		'shared_by_person_id' => $this->personId,
        		'shared_with_person_ids' => $this->personIdSharedWith
        		]);
        $createSharedSearch = json_decode($I->grabResponse());
        $I->sendDELETE('sharedsearches/'.($createSharedSearch->data->id-1));
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    } 
    
    public function testCreateSharedSearchWithInvalidSaveSearchId(ApiTester $I)
    {
    	$I->wantTo("Create shared search with invalid save search Id by API");
    	$I->haveHttpHeader('Content-Type','application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept','application/json');
    	$I->sendPOST('sharedsearches',[
    			'organization_id'=>$this->organization,
    			'saved_search_id'=> $this->invalidSavedSearchId,
    			'saved_search_name'=>uniqid("SharedSearch_",true),
    			'shared_by_person_id' => $this->personId,
    			'shared_with_person_ids' => $this->personIdSharedWith
    			]);
    	$sharedSearch = json_decode($I->grabResponse());
    	$I->seeResponseContains('OrgSearch Not Found');
    	$I->seeResponseCodeIs(400);
    	$I->seeResponseIsJson();
    }
    
    public function testCreateSharedSearchWithInvalidOrganization(ApiTester $I, $scenario)
    {
    	$I->wantTo("Create shared search with invalid organization by API");
    	$I->haveHttpHeader('Content-Type','application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept','application/json');
    	$I->sendPOST('sharedsearches',[
    			'organization_id'=>$this->invalidOrg,
    			'saved_search_id'=> $this->savedSearchId,
    			'saved_search_name'=>uniqid("SharedSearch_",true),
    			'shared_by_person_id' => $this->personId,
    			'shared_with_person_ids' => $this->personIdSharedWith
    			]);
    	$sharedSearch = json_decode($I->grabResponse());
    	$I->seeResponseContains('Organization Not Found.');
    	$I->seeResponseCodeIs(400);
    	$I->seeResponseIsJson();
    }
    
    public function testEditSharedSearchWithInvalidSaveSearchId(ApiTester $I)
    {
    	$I->wantTo("Edit shared search with invalid save search Id by API");
    	$I->haveHttpHeader('Content-Type','application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept','application/json');
    	$I->sendPUT('sharedsearches',[
        		'organization_id'=>$this->organization,
        		'saved_search_id'=> $this->invalidSavedSearchId,
        		'saved_search_name'=>uniqid("SharedSearch_",true),
        		'person_id' => $this->personIdSharedWith,
        		"search_attributes"=> [
                    "risk_indicator_ids"=> "1,2,3",
                    "intent_to_leave_ids"=> "10,20",
                    "group_ids"=> "1,2,1299"
                  ]
            ]);
    	$sharedSearch = json_decode($I->grabResponse());
    	$I->seeResponseContains('OrgSearch Not Found');
    	$I->seeResponseCodeIs(400);
    	$I->seeResponseIsJson();
    }

    public function testEditSharedSearchWithInvalidOrganization(ApiTester $I)
    {
    	$I->wantTo("edit shared search with invalid organization by API");
    	$I->haveHttpHeader('Content-Type','application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept','application/json');
    	$I->sendPOST('sharedsearches',[
        		'organization_id'=>$this->organization,
        		'saved_search_id'=> $this->savedSearchId,
        		'saved_search_name'=>uniqid("SharedSearch_",true),
        		'shared_by_person_id' => $this->personId,
        		'shared_with_person_ids' => $this->personIdSharedWith
        		]);
        $createSharedSearch = json_decode($I->grabResponse());
        $I->sendPUT('sharedsearches',[
        		'organization_id'=>$this->invalidOrg,
        		'saved_search_id'=> $createSharedSearch->data->id - 1,
        		'saved_search_name'=>uniqid("SharedSearch_",true),
        		'person_id' => $this->personIdSharedWith
            ]);
    	$sharedSearch = json_decode($I->grabResponse());
    	$I->seeResponseContains('Organization Not Found.');
    	$I->seeResponseCodeIs(400);
    	$I->seeResponseIsJson();
    }
    
    public function testDeleteSharedSearchInvalid(ApiTester $I)
    {
    	$I->wantTo("Delete shared Searches invalid by API");
    	$I->haveHttpHeader('Content-Type','application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept','application/json');
    	
    	$I->sendDELETE('sharedsearches/-1');
    	$I->seeResponseContains('OrgSearch Not Found');
    	$I->seeResponseCodeIs(400);
    	$I->seeResponseIsJson();
    }
    
    public function testCreateSharedSearchInvalidAuthentication(ApiTester $I)
    {
    	$I->wantTo("Create shared search with invalid authentication by API");
    	$I->haveHttpHeader('Content-Type','application/json');
    	$I->haveHttpHeader('Accept','application/json');
    	$I->sendPOST('sharedsearches',[
    			'organization_id'=>$this->organization,
    			'saved_search_id'=> $this->savedSearchId,
    			'saved_search_name'=>uniqid("SharedSearch_",true),
    			'shared_by_person_id' => $this->personId,
    			'shared_with_person_ids' => $this->personIdSharedWith
    			]);
    	$I->seeResponseCodeIs(403);
    	$I->seeResponseIsJson();
    }
    
    public function testGetSharedSearchesWithInvalidAuthentication(ApiTester $I){
    	$I->wantTo("Get shared Searches With Invalid Authentication by API");
    	$I->haveHttpHeader('Content-Type','application/json');
    	$I->haveHttpHeader('Accept','application/json');
    	$I->sendGET('sharedsearches');
    	$I->seeResponseCodeIs(403);
    	$I->seeResponseIsJson();
    }
    
    public function testEditSharedSearchWithInvalidAuthentication(ApiTester $I)
    {
    	$I->wantTo("edit shared search with invalid authentication by API");
    	$I->haveHttpHeader('Content-Type','application/json');
    	$I->haveHttpHeader('Accept','application/json');
    	$I->sendPUT('sharedsearches');
    	$I->seeResponseCodeIs(403);
    	$I->seeResponseIsJson();
    }
    
    public function testDeleteSharedSearchInvalidAuthentication(ApiTester $I)
    {
    	$I->wantTo("Delete shared Searches invalid authentication by API");
    	$I->haveHttpHeader('Content-Type','application/json');
    	$I->haveHttpHeader('Accept','application/json');
    	$I->sendDELETE('sharedsearches/1');
    	$I->seeResponseCodeIs(403);
    	$I->seeResponseIsJson();
    }
    
    // New Test cases based on the low coverage
    public function testCreateSharedSearchFromCustomSearchDirect(ApiTester $I)
    {
        $I->wantTo("Create shared search From Custom Search Direct by API");
        $I->haveHttpHeader('Content-Type','application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept','application/json');
        $I->sendPOST('sharedsearches',[
            'organization_id'=>$this->organization,
            'saved_search_id'=> -1,
            'saved_search_name'=>uniqid("SharedSearch_",true),
            'shared_by_person_id' => $this->personId,
            'shared_with_person_ids' => $this->personIdSharedWith,
        		"search_attributes"=> [
                    "risk_indicator_ids"=> "1,2,3",
                    "intent_to_leave_ids"=> "10,20",
                    "group_ids"=> "1,2,1299"
                  ]
            ]);
        $sharedSearch = json_decode($I->grabResponse());
        $I->seeResponseContains('saved_search_name');
        $I->seeResponseContains('id');
        $I->seeResponseContainsJson(array('organization_id' => $this->organization));
        $I->seeResponseContainsJson( array('saved_search_id' => -1) );
        $I->seeResponseContainsJson( array('shared_by_person_id' => $this->personId) );
        $I->seeResponseContainsJson( array('shared_with_person_ids' => "$this->personIdSharedWith") );
        $I->seeResponseContains('search_attributes');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
    
    public function testCreateSharedSearchFromCustomSearchDirectSameName(ApiTester $I)
    {
        $I->wantTo("Create shared search From Custom Search Direct with Same Name by API");
        $I->haveHttpHeader('Content-Type','application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept','application/json');
        $I->sendPOST('sharedsearches',[
            'organization_id'=>$this->organization,
            'saved_search_id'=> -1,
            'saved_search_name'=>uniqid("SharedSearch_",true),
            'shared_by_person_id' => $this->personId,
            'shared_with_person_ids' => $this->personIdSharedWith,
            "search_attributes"=> [
            "risk_indicator_ids"=> "1,2,3",
            "intent_to_leave_ids"=> "10,20",
            "group_ids"=> "1,2,1299"
            ]
            ]);
        $sharedSearch = json_decode($I->grabResponse());
        
        $I->sendPOST('sharedsearches',[
            'organization_id'=>$this->organization,
            'saved_search_id'=> -1,
            'saved_search_name'=> $sharedSearch->data->saved_search_name,
            'shared_by_person_id' => $this->personId,
            'shared_with_person_ids' => $this->personIdSharedWith,
            "search_attributes"=> [
            "risk_indicator_ids"=> "1,2,3",
            "intent_to_leave_ids"=> "10,20",
            "group_ids"=> "1,2,1299"
            ]
            ]);
        
        $I->seeResponseContains('Search Name Already Exists.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testCreateSharedSearchFromSharedTabWithSameName(ApiTester $I)
    {
        $I->wantTo("Create shared search From Shared Tab with same name by API");
        $I->haveHttpHeader('Content-Type','application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept','application/json');
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => uniqid("SharedSearch_",true),
            "search_attributes" => [
            "risk_indicator_ids" => "2",
            "intent_to_leave_ids" => "10,20",
            "group_ids" => "1,5,7",
            "referral_status" => "open",
            "contact_types" => "interaction"
            ]
            ]);
        $createSharedSearch = json_decode($I->grabResponse());
        $I->sendPOST('sharedsearches',[
            'organization_id'=>$this->organization,
            'saved_search_id'=> $createSharedSearch->data->saved_search_id,
            'saved_search_name'=>$createSharedSearch->data->saved_search_name,
            'shared_by_person_id' => $this->personId,
            'shared_with_person_ids' => $this->personIdSharedWith
            ]);
        
        $sharedSearch = json_decode($I->grabResponse());
        $I->sendPOST('sharedsearches',[
            'organization_id'=>$this->organization,
            'saved_search_id'=> $sharedSearch->data->id - 1,
            'saved_search_name'=>$sharedSearch->data->saved_search_name,
            'shared_by_person_id' => $this->personId,
            'shared_with_person_ids' => $this->personIdSharedWith
            ]);
        $sharedSearch2 = json_decode($I->grabResponse());
        $I->seeResponseContains('Search Already Shared With Person');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testCreateSharedSearchFromSharedTabWithSameNameDifferentPerson(ApiTester $I)
    {
        $I->wantTo("Create shared search From Shared Tab with same name Different Person by API");
        $I->haveHttpHeader('Content-Type','application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept','application/json');
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => uniqid("SharedSearch_",true),
            "search_attributes" => [
            "risk_indicator_ids" => "2",
            "intent_to_leave_ids" => "10,20",
            "group_ids" => "1,5,7",
            "referral_status" => "open",
            "contact_types" => "interaction"
            ]
            ]);
        $createSharedSearch = json_decode($I->grabResponse());
        $I->sendPOST('sharedsearches',[
            'organization_id'=>$this->organization,
            'saved_search_id'=> $createSharedSearch->data->saved_search_id,
            'saved_search_name'=>$createSharedSearch->data->saved_search_name,
            'shared_by_person_id' => $this->personId,
            'shared_with_person_ids' => $this->personIdSharedWith
            ]);
    
        $sharedSearch = json_decode($I->grabResponse());
        $I->sendPOST('sharedsearches',[
            'organization_id'=>$this->organization,
            'saved_search_id'=> $sharedSearch->data->id - 1,
            'saved_search_name'=>$sharedSearch->data->saved_search_name,
            'shared_by_person_id' => $this->personId,
            'shared_with_person_ids' => 3
            ]);
        $sharedSearch2 = json_decode($I->grabResponse());
        $I->seeResponseContains('saved_search_name');
        $I->seeResponseContains('id');
        $I->seeResponseContainsJson(array('organization_id' => $this->organization));
        $I->seeResponseContainsJson( array('shared_by_person_id' => $this->personId) );
        $I->seeResponseContainsJson( array('shared_with_person_ids' => '3') );
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
    
    public function testCreateSharedSearchFromSharedTabWithDifferntName(ApiTester $I)
    {
        $I->wantTo("Create shared search From Shared Tab with Different name Different Person by API");
        $I->haveHttpHeader('Content-Type','application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept','application/json');
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => uniqid("SharedSearch_",true),
            "search_attributes" => [
            "risk_indicator_ids" => "2",
            "intent_to_leave_ids" => "10,20",
            "group_ids" => "1,5,7",
            "referral_status" => "open",
            "contact_types" => "interaction"
            ]
            ]);
        $createSharedSearch = json_decode($I->grabResponse());
        $I->sendPOST('sharedsearches',[
            'organization_id'=>$this->organization,
            'saved_search_id'=> $createSharedSearch->data->saved_search_id,
            'saved_search_name'=>$createSharedSearch->data->saved_search_name,
            'shared_by_person_id' => $this->personId,
            'shared_with_person_ids' => $this->personIdSharedWith
            ]);
    
        $sharedSearch = json_decode($I->grabResponse());
        $I->sendPOST('sharedsearches',[
            'organization_id'=>$this->organization,
            'saved_search_id'=> $sharedSearch->data->id - 1,
            'saved_search_name'=>uniqid("SharedSearch_",true),
            'shared_by_person_id' => $this->personId,
            'shared_with_person_ids' => $this->personIdSharedWith
            ]);
        $sharedSearch2 = json_decode($I->grabResponse());
        $I->seeResponseContains('saved_search_name');
        $I->seeResponseContains('id');
        $I->seeResponseContainsJson(array('organization_id' => $this->organization));
        $I->seeResponseContainsJson( array('shared_by_person_id' => $this->personId) );
        $I->seeResponseContainsJson( array('shared_with_person_ids' => "$this->personIdSharedWith") );
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
    
    public function testGetSharedSearchesWithSharedByUsers(ApiTester $I){
        $I->wantTo("Get shared Searches With Shared By Users by API");
        $I->haveHttpHeader('Content-Type','application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept','application/json');
        $I->sendPOST('sharedsearches',[
            'organization_id'=>$this->organization,
            'saved_search_id'=> $this->savedSearchId,
            'saved_search_name'=>uniqid("SharedSearch_",true),
            'shared_by_person_id' => $this->personIdSharedWith,
            'shared_with_person_ids' => $this->personId
            ]);
        $sharedSearch = json_decode($I->grabResponse());
        $I->sendGET('sharedsearches');
        $sharedSearch = json_decode($I->grabResponse());
        $search = end($sharedSearch->data->shared_searches);
        $I->seeResponseContains('shared_searches');
        foreach ($sharedSearch as $search){
    
            $I->seeResponseContains("saved_search_id");
            $I->seeResponseContains("search_name");
            $I->seeResponseContains("shared_with_users");
            $I->seeResponseContains("shared_by_users");
        }
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testDeleteSharedSearchBySharedByPerson(ApiTester $I)
    {
        $I->wantTo("Delete shared Searches by By Shared-By Person API");
        $I->haveHttpHeader('Content-Type','application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept','application/json');
        $I->sendPOST('sharedsearches',[
            'organization_id'=>$this->organization,
            'saved_search_id'=> $this->savedSearchId,
            'saved_search_name'=>uniqid("SharedSearch_",true),
            'shared_by_person_id' => $this->personId,
            'shared_with_person_ids' => $this->personIdSharedWith
            ]);
        $createSharedSearch = json_decode($I->grabResponse());
        $I->sendDELETE('sharedsearches/'.($createSharedSearch->data->id-1).'?shared_search_id='.$createSharedSearch->data->id.'&shared_by_user_id='.$this->personId);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }
    
    public function testEditSharedSearchByOwnerSameName(ApiTester $I)
    {
        $I->wantTo("Edit shared Searches By Owner Same Name API");
        $I->haveHttpHeader('Content-Type','application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept','application/json');
        $I->sendPOST('sharedsearches',[
            'organization_id'=>$this->organization,
            'saved_search_id'=> $this->savedSearchId,
            'saved_search_name'=>uniqid("SharedSearch_",true),
            'shared_by_person_id' => $this->personId,
            'shared_with_person_ids' => $this->personIdSharedWith
            ]);
        $createSharedSearch = json_decode($I->grabResponse());
        $I->sendPUT('sharedsearches',[
            'organization_id'=>$this->organization,
            'saved_search_id'=> $createSharedSearch->data->id - 1,
            'saved_search_name'=>$createSharedSearch->data->saved_search_name,
            'person_id' => $this->personId,
            "search_attributes"=> [
            "risk_indicator_ids"=> "1,2,3",
            "intent_to_leave_ids"=> "10,20",
            "group_ids"=> "1,2,1299",
            "referral_status" => "open",
            "contact_types" => "interaction"
            ]
            ]);
        $editSharedSearch = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }
}