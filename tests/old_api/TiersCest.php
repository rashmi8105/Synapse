<?php
require_once 'SynapseTestHelper.php';
class TiersCest extends SynapseTestHelper {
	
	private $token;
	
	private $lang =1;
	
	private $invalidId = -100;
	
	public function _before(ApiTester $I) {
		// $this->token = $this->authenticate($I);
		$I->sendPOST ( 'http://127.0.0.1:8080/oauth/v2/token', [
				'client_id' => "3_14tx5vbsnois4ggg0ok0c4gog8kg0ww488gwkg88044cog4884",
				'client_secret' => "4v5p8idswhs0404owsws48gwwccc4wksw4c8s80wcocwskockg",
				'grant_type' => "password",
				'username' => "david.warner@gmail.com",
				"password" => "ramesh@1974"
				] );
		$token = json_decode ( $I->grabResponse () );
		$this->token = $token->access_token;
	}
	
	public function testGetPrimaryTier(ApiTester $I) {
		$I->wantTo ( 'Get Primary Tier by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->amBearerAuthenticated( $this->token);
		$I->sendGET ('tiers/list?tier-level=primary');
		
		$I->seeResponseContains ( 'primary_tiers' );
		$I->seeResponseCodeIs ( 200 );
		
		$I->seeResponseIsJson ();
	}
	
	public function testCreatePrimaryTier(ApiTester $I) {
		$I->wantTo ( 'Create a Primary Tier by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->sendPOST ( 'tiers', [ 
				"tier_level"=> "primary",
			    "primary_tier_name"=> "Primary Tier Name".mt_rand(),
			    "primary_tier_id"=> mt_rand(),
			    "description"=> "Primary Tier Description",
			    "langid"=> $this->lang
		] );
		
		$I->seeResponseCodeIs ( 201 );
		$I->seeResponseContains ( 'id' );
		$I->seeResponseIsJson ();
	}
	
	public function testUpdatePrimaryTier(ApiTester $I) {
		$I->wantTo ( 'Update a Primary Tier by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "primary",
				"primary_tier_name"=> "Primary Tier Name".mt_rand(),
				"primary_tier_id"=> mt_rand(),
				"description"=> "Primary Tier Description",
				"langid"=> $this->lang
				] );
		$data = json_decode($I->grabResponse());
		$id = $data->data->id;
		$I->seeResponseCodeIs ( 201 );
		$I->seeResponseContains ( 'id' );
		$I->seeResponseIsJson ();
		
		$I->sendPUT ( 'tiers', [
				"id"=> $id,
				"tier_level"=> "primary",
				"primary_tier_name"=> "Primary Tier Name Edit".mt_rand(),
				"primary_tier_id"=> mt_rand(),
				"description"=> "Primary Tier Description Edit",
				"langid"=> $this->lang
				] );
		$I->seeResponseCodeIs ( 200 );
		$I->seeResponseContains ( 'id' );
		$I->seeResponseIsJson ();
	}
	
	public function testUpdatePrimaryTierInvalidId(ApiTester $I) {
		$I->wantTo ( 'Update a Primary Tier by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->sendPUT ( 'tiers', [
				"id"=> $this->invalidId,
				"tier_level"=> "primary",
				"primary_tier_name"=> "Primary Tier Name Edit".mt_rand(),
				"primary_tier_id"=> mt_rand(),
				"description"=> "Primary Tier Description Edit",
				"langid"=> $this->lang
				] );
		$I->seeResponseCodeIs ( 400 );
	}
	
	public function testGetSinglePrimaryTier(ApiTester $I) {
		$I->wantTo ( 'Get a Primary Tier by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "primary",
				"primary_tier_name"=> "Primary Tier Name".mt_rand(),
				"primary_tier_id"=> mt_rand(),
				"description"=> "Primary Tier Description",
				"langid"=> $this->lang
				] );
		$data = json_decode($I->grabResponse());
		
		$id = $data->data->id;
		$I->sendGET ("tiers/$id?tier-level=primary");
		$I->seeResponseCodeIs ( 200 );
		$I->seeResponseContains ( 'id' );
		$I->seeResponseContains ( 'tier_level' );
		$I->seeResponseContains ( 'primary_tier_name' );
		$I->seeResponseContains ( 'primary_tier_id' );
		$I->seeResponseIsJson ();
	}
	
	public function testCreatePrimaryTierSameName(ApiTester $I) {
		//$this->authenticate ( $I );
		$I->wantTo ( 'Create Same Name PrimaryTier by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$tierName = "Primary Tier Name".mt_rand();
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "primary",
				"primary_tier_name"=> $tierName,
				"primary_tier_id"=> mt_rand(),
				"description"=> "Primary Tier Description",
				"langid"=> $this->lang
				] );
		$I->seeResponseContains ( 'id' );
		
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "primary",
				"primary_tier_name"=> $tierName,
				"primary_tier_id"=> mt_rand(),
				"description"=> "Primary Tier Description",
				"langid"=> $this->lang
				] );
		
		$I->seeResponseCodeIs ( 400 );
		$I->seeResponseIsJson ();
	}
	
	public function testGetSecondaryTierList(ApiTester $I) {
		$I->wantTo ( 'Get Secondary Tier List by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "primary",
				"primary_tier_name"=> "Primary Tier Name".mt_rand(),
				"primary_tier_id"=> mt_rand(),
				"description"=> "Primary Tier Description",
				"langid"=> $this->lang
				] );
		$data = json_decode($I->grabResponse());
		$id = $data->data->id;
		
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "secondary",
				"primary_tier_id"=> $id,
				"secondary_tier_name"=> "Secondary Tier Name".mt_rand(),
				"secondary_tier_id"=> mt_rand(),
				"description"=> "Secondary Tier Description",
				"langid"=> $this->lang
				] );
		
		$I->sendGET ("tiers/list?tier-level=secondary&primary-tier-id=$id");	
		$I->seeResponseContains ( 'primary_tier_id' );
		$I->seeResponseContains ( 'secondary_tiers' );
		$I->seeResponseCodeIs ( 200 );
	
		$I->seeResponseIsJson ();
	}
	
	public function testGetSecondaryTierById(ApiTester $I) {
		$I->wantTo ( 'Get Secondary Tier Id by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "primary",
				"primary_tier_name"=> "Primary Tier Name".mt_rand(),
				"primary_tier_id"=> mt_rand(),
				"description"=> "Primary Tier Description",
				"langid"=> $this->lang
				] );
		$data = json_decode($I->grabResponse());
		$id = $data->data->id;
	
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "secondary",
				"primary_tier_id"=> $id,
				"secondary_tier_name"=> "Secondary Tier Name".mt_rand(),
				"secondary_tier_id"=> mt_rand(),
				"description"=> "Secondary Tier Description",
				"langid"=> $this->lang
				] );
		$dataSecondary = json_decode($I->grabResponse());
		$idSecondary = $dataSecondary->data->id;
	
		$I->sendGET ("tiers/$idSecondary?tier-level=secondary");
		$I->seeResponseContains ( 'id' );
		$I->seeResponseContains ( 'tier_level' );
		$I->seeResponseContains ( 'secondary_tier_name' );
		$I->seeResponseContains ( 'secondary_tier_id' );
		$I->seeResponseContains ( 'primary_tier_id' );
		$I->seeResponseContains ( 'description' );
		$I->seeResponseCodeIs ( 200 );
	
		$I->seeResponseIsJson ();
	}
	
	public function testCreateSecondaryTier(ApiTester $I) {
		$I->wantTo ( 'Create Secondary Tier by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "primary",
				"primary_tier_name"=> "Primary Tier Name".mt_rand(),
				"primary_tier_id"=> mt_rand(),
				"description"=> "Primary Tier Description",
				"langid"=> $this->lang
				] );
		$data = json_decode($I->grabResponse());
		$id = $data->data->id;
		
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "secondary",
				"primary_tier_id"=> $id,
				"secondary_tier_name"=> "Secondary Tier Name".mt_rand(),
				"secondary_tier_id"=> mt_rand(),
				"description"=> "Secondary Tier Description",
				"langid"=> $this->lang
				] );
		$I->seeResponseCodeIs ( 201 );
	
		$I->seeResponseIsJson ();
	}
	
	public function testUpdateSecondaryTier(ApiTester $I) {
		$I->wantTo ( 'Update Secondary Tier by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "primary",
				"primary_tier_name"=> "Primary Tier Name".mt_rand(),
				"primary_tier_id"=> mt_rand(),
				"description"=> "Primary Tier Description",
				"langid"=> $this->lang
				] );
		$data = json_decode($I->grabResponse());
		$id = $data->data->id;
	
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "secondary",
				"primary_tier_id"=> $id,
				"secondary_tier_name"=> "Secondary Tier Name".mt_rand(),
				"secondary_tier_id"=> mt_rand(),
				"description"=> "Secondary Tier Description",
				"langid"=> $this->lang
				] );
		$data = json_decode($I->grabResponse());
		$idSecondary = $data->data->id;
		
		$I->sendPUT ( 'tiers', [
				"id" => $idSecondary,
				"tier_level"=> "secondary",
				"primary_tier_id"=> $id,
				"secondary_tier_name"=> "Secondary Tier Name Edit".mt_rand(),
				"secondary_tier_id"=> mt_rand(),
				"description"=> "Secondary Tier Description Edit",
				"langid"=> $this->lang
				] );
		
		$I->seeResponseCodeIs ( 200 );
		$I->seeResponseIsJson ();
	}
	
	public function testCreatePrimaryTierInvalidName(ApiTester $I) {
		$I->wantTo ( 'Create a PrimaryTier by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->amBearerAuthenticated( $this->token);
		$tierId = mt_rand();
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "primary",
				"primary_tier_id"=> $tierId,
				"description"=> "Primary Tier Description",
				"langid"=> $this->lang
				] );
		$I->seeResponseCodeIs ( 400 );
		$I->seeResponseIsJson ();
	}
	
	public function testCreateSecondaryTierInvalidName(ApiTester $I) {
		$I->wantTo ( 'Create Secondary Tier by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "primary",
				"primary_tier_name"=> "Primary Tier Name".mt_rand(),
				"primary_tier_id"=> mt_rand(),
				"description"=> "Primary Tier Description",
				"langid"=> $this->lang
				] );
		$data = json_decode($I->grabResponse());
		$id = $data->data->id;
	
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "secondary",
				"primary_tier_id"=> $id,
				"secondary_tier_id"=> mt_rand(),
				"description"=> "Secondary Tier Description",
				"langid"=> $this->lang
				] );
		$I->seeResponseCodeIs ( 400 );
	
		$I->seeResponseIsJson ();
	}
	
	public function testDeleteSecondaryTier(ApiTester $I) {
		$I->wantTo ( 'Delete Secondary Tier by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "primary",
				"primary_tier_name"=> "Primary Tier Name".mt_rand(),
				"primary_tier_id"=> mt_rand(),
				"description"=> "Primary Tier Description",
				"langid"=> $this->lang
				] );
		$data = json_decode($I->grabResponse());
		$id = $data->data->id;
	
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "secondary",
				"primary_tier_id"=> $id,
				"secondary_tier_name"=> "Secondary Tier Name".mt_rand(),
				"secondary_tier_id"=> mt_rand(),
				"description"=> "Secondary Tier Description",
				"langid"=> $this->lang
				] );
		$data = json_decode($I->grabResponse());
		$idSecondary = $data->data->id;
	
		$I->sendDELETE( "tiers/$idSecondary?tier-level=primary");
	
		$I->seeResponseCodeIs ( 204 );
	}
	
	public function testCreatePrimaryTierInvalidAuth(ApiTester $I) {
		$this->authenticate ( $I );
		$I->wantTo ( 'Create a Primary Tier by Invalid Auth API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$tierId = mt_rand();
		$I->sendPOST ( 'tiers', [
				"tier_level"=> "primary",
				"primary_tier_name"=> "Primary Tier Name",
				"primary_tier_id"=> $tierId,
				"description"=> "Primary Tier Description",
				"langid"=> $this->lang
				] );
		$I->seeResponseCodeIs ( 403 );
		$I->seeResponseIsJson ();
	}
	
    public function testUpdatePrimaryTierInvalidAuth(ApiTester $I) {
        $this->authenticate ( $I );
		$I->wantTo ( 'Update a Primary Tier by Invalid Auth API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->haveHttpHeader ( 'Accept', 'application/json' );	
		$I->sendPUT ( 'tiers', [
				"id"=> $this->invalidId,
				"tier_level"=> "primary",
				"primary_tier_name"=> "Primary Tier Name Edit".mt_rand(),
				"primary_tier_id"=> mt_rand(),
				"description"=> "Primary Tier Description Edit",
				"langid"=> $this->lang
				] );
		$I->seeResponseCodeIs ( 403 );
	}
	
	public function testCreateHierarchyCampus(ApiTester $I)
    {
        $I->wantTo('Create Hierarchy Campus by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('tiers', [
            "tier_level" => "primary",
            "primary_tier_name" => "Primary Tier Name" . mt_rand(),
            "primary_tier_id" => mt_rand(),
            "description" => "Primary Tier Description",
            "langid" => $this->lang
        ]);
        $data = json_decode($I->grabResponse());
        $id = $data->data->id;
        
        $I->sendPOST('tiers', [
            "tier_level" => "secondary",
            "primary_tier_id" => $id,
            "secondary_tier_name" => "Secondary Tier Name" . mt_rand(),
            "secondary_tier_id" => mt_rand(),
            "description" => "Secondary Tier Description",
            "langid" => $this->lang
        ]);
        $data = json_decode($I->grabResponse());
        $idSecondary = $data->data->id;
        
        $I->sendPOST("tiers/$idSecondary/campuses", [
            "langid" => $this->lang,
            "primary_tier_id" => $idSecondary,
            "name" => "Test Campus" . mt_rand(),
            "nick_name" => "Test Campus" . mt_rand(),
            "subdomain" => "TestCampus" . mt_rand(),
            "campus_id" => mt_rand(),
            "timezone" => "Eastern",
            "is_ldap_saml_enabled" => false
        ]);
        $data = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(201);
        
        $I->seeResponseIsJson();
    }

    public function testMoveHierarchyCampus(ApiTester $I)
    {
        $I->wantTo('Move Hierarchy Campus by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('tiers', [
            "tier_level" => "primary",
            "primary_tier_name" => "Primary Tier Name" . mt_rand(),
            "primary_tier_id" => mt_rand(),
            "description" => "Primary Tier Description",
            "langid" => $this->lang
        ]);
        $data = json_decode($I->grabResponse());
        $id = $data->data->id;
        
        $I->sendPOST('tiers', [
            "tier_level" => "secondary",
            "primary_tier_id" => $id,
            "secondary_tier_name" => "Secondary Tier Name" . mt_rand(),
            "secondary_tier_id" => mt_rand(),
            "description" => "Secondary Tier Description",
            "langid" => $this->lang
        ]);
        $data = json_decode($I->grabResponse());
        $idSecondary = $data->data->id;
        
        $I->sendPOST("tiers/$idSecondary/campuses", [
            "langid" => $this->lang,
            "primary_tier_id" => $idSecondary,
            "name" => "Test Campus" . mt_rand(),
            "nick_name" => "Test Campus" . mt_rand(),
            "subdomain" => "TestCampus" . mt_rand(),
            "campus_id" => mt_rand(),
            "timezone" => "Eastern",
            "is_ldap_saml_enabled" => false
        ]);
        $data = json_decode($I->grabResponse());
        $idCampus = $data->data->id;
        
        $arrayUpdate = [
            "source_campus_type" => "hierarchy",
            "source_org_id" => $idCampus,
            "tier" => 3
        ];
        
        $I->sendPUT("tiers/$idSecondary/campus", $arrayUpdate);
        
        $data = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        
        $I->seeResponseIsJson();
    }

    public function testHierarchyCampusList(ApiTester $I)
    {
        $I->wantTo('List Hierarchy Campus by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('tiers', [
            "tier_level" => "primary",
            "primary_tier_name" => "Primary Tier Name" . mt_rand(),
            "primary_tier_id" => mt_rand(),
            "description" => "Primary Tier Description",
            "langid" => $this->lang
        ]);
        $data = json_decode($I->grabResponse());
        $id = $data->data->id;
        
        $I->sendPOST('tiers', [
            "tier_level" => "secondary",
            "primary_tier_id" => $id,
            "secondary_tier_name" => "Secondary Tier Name" . mt_rand(),
            "secondary_tier_id" => mt_rand(),
            "description" => "Secondary Tier Description",
            "langid" => $this->lang
        ]);
        $data = json_decode($I->grabResponse());
        $idSecondary = $data->data->id;
        
        $I->sendGET("tiers/$idSecondary/campuses");
        $data = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testDeleteHierarchyCampus(ApiTester $I)
    {
        $I->wantTo('Delete Hierarchy Campus by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('tiers', [
            "tier_level" => "primary",
            "primary_tier_name" => "Primary Tier Name" . mt_rand(),
            "primary_tier_id" => mt_rand(),
            "description" => "Primary Tier Description",
            "langid" => $this->lang
        ]);
        $data = json_decode($I->grabResponse());
        $id = $data->data->id;
        
        $I->sendPOST('tiers', [
            "tier_level" => "secondary",
            "primary_tier_id" => $id,
            "secondary_tier_name" => "Secondary Tier Name" . mt_rand(),
            "secondary_tier_id" => mt_rand(),
            "description" => "Secondary Tier Description",
            "langid" => $this->lang
        ]);
        $data = json_decode($I->grabResponse());
        $idSecondary = $data->data->id;
        
        $I->sendPOST("tiers/$idSecondary/campuses", [
            "langid" => $this->lang,
            "primary_tier_id" => $idSecondary,
            "name" => "Test Campus" . mt_rand(),
            "nick_name" => "Test Campus" . mt_rand(),
            "subdomain" => "TestCampus" . mt_rand(),
            "campus_id" => mt_rand(),
            "timezone" => "Eastern",
            "is_ldap_saml_enabled" => false
        ]);
        $data = json_decode($I->grabResponse());
        $idCampus = $data->data->id;
        
        $I->sendDELETE("tiers/$idSecondary/campuses/$idCampus");
        $I->seeResponseCodeIs(204);
    }

    public function testViewHierarchyCampus(ApiTester $I)
    {
        $I->wantTo('View Hierarchy Campus by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('tiers', [
            "tier_level" => "primary",
            "primary_tier_name" => "Primary Tier Name" . mt_rand(),
            "primary_tier_id" => mt_rand(),
            "description" => "Primary Tier Description",
            "langid" => $this->lang
        ]);
        $data = json_decode($I->grabResponse());
        $id = $data->data->id;
        
        $I->sendPOST('tiers', [
            "tier_level" => "secondary",
            "primary_tier_id" => $id,
            "secondary_tier_name" => "Secondary Tier Name" . mt_rand(),
            "secondary_tier_id" => mt_rand(),
            "description" => "Secondary Tier Description",
            "langid" => $this->lang
        ]);
        $data = json_decode($I->grabResponse());
        $idSecondary = $data->data->id;
        
        $I->sendPOST("tiers/$idSecondary/campuses", [
            "langid" => $this->lang,
            "primary_tier_id" => $idSecondary,
            "name" => "Test Campus" . mt_rand(),
            "nick_name" => "Test Campus" . mt_rand(),
            "subdomain" => "TestCampus" . mt_rand(),
            "campus_id" => mt_rand(),
            "timezone" => "Eastern",
            "is_ldap_saml_enabled" => false
        ]);
        $data = json_decode($I->grabResponse());
        $idCampus = $data->data->id;
        
        $I->sendGET("tiers/$idSecondary/campuses/$idCampus");
        $I->seeResponseCodeIs(200);
    }	
}
