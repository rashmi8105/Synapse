<?php

use GuzzleHttp\json_decode;
require_once 'SynapseTestHelper.php';

class PermissionSetCest extends SynapseTestHelper
{
	private $token;

	private $langValid = 1;
    private $langInvalid = -1;
    private $status = 'active';
    private $permissionSetname='bipin';
    private $permissionSetInvalid='invalid';
    
	public function _before(ApiTester $I)
	{
		//$this->token = $this->authenticate($I);
	}
	public function testEbiAdmin($I)
	{
		$I->sendPOST('http://127.0.0.1:8080/oauth/v2/token', [
		'client_id' => "3_14tx5vbsnois4ggg0ok0c4gog8kg0ww488gwkg88044cog4884",
		'client_secret' => "4v5p8idswhs0404owsws48gwwccc4wksw4c8s80wcocwskockg",
		'grant_type' => "password",
		'username' => "david.warner@gmail.com",
		"password" => "ramesh@1974"
		]);
		$token = json_decode($I->grabResponse());
		$this->token = $token->access_token;
	}

	public function testListPermissionSetByStatusValid(ApiTester $I)
	{
			$I->wantTo('List permission set by status with valid language by API');
			$I->haveHttpHeader('Content-Type', 'application/json');
			$I->amBearerAuthenticated( $this->token);
			$I->haveHttpHeader('Accept', 'application/json');
			$I->sendGET('permissionset/'.$this->langValid.'/list?status='.$this->status);
			$data = json_decode($I->grabResponse());
            $I->seeResponseContainsJson(array('lang_id' => "1"));
            $I->seeResponseContains('permission_template');
			$I->seeResponseCodeIs(200);
			$I->seeResponseIsJson();

	}

    public function testListPermissionSetByStatusInvalid(ApiTester $I)
    {
        $I->wantTo('List permission set with invalid language by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('permissionset/'.$this->langInvalid.'/list?status='.$this->status);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testListPermissionSetByStatusInvalidAuthentication(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $this->authenticate($I);
        $I->wantTo('Get EBI persmission sets with invalid authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('permissionset/'.$this->langValid.'/list?status=active');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();

    }
	
	 public function testCreateEbiPermissionSet(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Create a EBI Permissionset by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateEBIPermissionSet();
        $I->sendPOST('permissionset',$request);
        $I->seeResponseCodeIs(201);       
		$I->seeResponseContainsJson(array('permission_template_name' => $request['permission_template_name']));
        $I->seeResponseContainsJson(array('risk_indicator' => true));
        $I->seeResponseContainsJson(array('intent_to_leave' => true));
        
        $I->seeResponseIsJson();
    }

		
	 public function testCreatePermissionSetWithOutAuthenticate(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $this->authenticate($I);
        $I->wantTo('Create a Permissionset Without Authentication ');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
		$request = $this->getJsonForCreateEBIPermissionSet();
        $I->sendPOST('permissionset',$request);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();

    }
	
	 public function testCreateEBIPermissionSetWithOutName(ApiTester $I)
    {
        $I->wantTo('Create a EBI Permissionset Without Permissionset Name');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateEBIPermissionSet();
        $request['permission_template_name'] = null;
        $I->sendPOST('permissionset',$request);
        $I->seeResponseCodeIs(400);
         
    
        $I->seeResponseIsJson();
    }

	private function getJsonForCreateEBIPermissionSet()
    {
        $permissionsetName = uniqid("EBIPemission_",true);
        $permission = [];
        $permission['lang_id'] = $this->langValid;
        $permission['permission_template_name'] = $permissionsetName;
        $permission['access_level'] = ['individual_and_aggregate' => true,'aggregate_only'=>false];
        $permission['risk_indicator'] = true;
        $permission['intent_to_leave'] = true;
        
        $permission['reports_access'][] = $this->reportAccessArray();    
    
        $profileBlocks = [];
        for($i = 1; $i<5;$i++)
        {
            $profileBlocks[] = $this->createDataBlocks($i,true);
        }
        $permission['profile_blocks'] = $profileBlocks;
        $surveyBlocks = [];
        for($i = 8; $i<11;$i++)
        {
            $surveyBlocks[] = $this->createDataBlocks($i,true);
        }
        $permission['survey_blocks'] = $surveyBlocks;
    
    
    
        $features = [];
        for($i = 1; $i < 6;$i++)
        {
            $features[] = $this->createFeature($i);
        }
        $permission['features'] = $features;
        return $permission;
    }

    private function createFeature($id)
    {
        $shareOption = ["public_share"=>["view" => true,"create" => false],"teams_share"=>["view" => true,"create" => false],"private_share"=>["create" => true]];
        if($id == 1)
        {
        $feature = ["id"=>$id,"direct_referral" => $shareOption, "reason_routed_referral" => $shareOption ];
        $feature['receive_referrals'] = true;
        }
        else {
            $feature = ["id"=>$id,"public_share"=>["view" => true,"create" => false],"teams_share"=>["view" => true,"create" => false],"private_share"=>["create" => true]];
        }
            return $feature;
    }
    private function createDataBlocks($id,$isSelected = false)
    {
        $block = ["block_id" => $id,"block_selection" => $isSelected];
        return $block;
    }	
	
	
	/**
     *
     * Test Cases For getEBIPermissionsetAction
     */
    public function testGetPermissionset(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo('Get A EBI Permission set');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateEBIPermissionSet();
        $I->sendPOST('permissionset',$request);
        $permission = json_decode($I->grabResponse());
       
        $request['permission_template_id'] = $permission->data->permission_template_id;
         
        $I->sendGET('permissionset/'.$this->langValid.'/id/'.$request['permission_template_id']);
        //var_dump(json_decode($I->grabResponse()));die;
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('permission_template_id' => $request['permission_template_id']));
        $I->seeResponseContainsJson(array('permission_template_name' => $request['permission_template_name']));
        $I->seeResponseContainsJson(array('risk_indicator' => true));
        $I->seeResponseContainsJson(array('intent_to_leave' => true));
    	
        $I->seeResponseIsJson();
    }


    public function testGetPermissionsetInvalidAuthentication(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $this->authenticate($I);
        $I->wantTo('Get A EBI Permission set with invalid Authentication');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('permissionset/'.$this->langValid.'/id/1');

        $I->seeResponseCodeIs(403);
    }

	   public function testGetPermissionsetInvalidLang(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo('Get A EBI Permission set for Invalid Langid');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateEBIPermissionSet();
        $I->sendPOST('permissionset',$request);
        $permission = json_decode($I->grabResponse());
        $request['permission_template_id'] = $permission->data->permission_template_id;
        $I->sendGET('permissionset/'.$this->langInvalid.'/id/'.$request['permission_template_id']);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testEditPermissionSet(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo('Edit Permissionset by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateEBIPermissionSet();
        $I->sendPOST('permissionset',$request);
        $permission = json_decode($I->grabResponse());
        $request['permission_template_id'] = $permission->data->permission_template_id;
        $request['intent_to_leave'] = false;
        $I->sendPUT('permissionset',$request);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('permission_template_name' => $request['permission_template_name']));
        $I->seeResponseContainsJson(array('risk_indicator' => true));
        $I->seeResponseContainsJson(array('intent_to_leave' => false));
        $I->seeResponseIsJson();
    }
    public function testEditPermissionSetWithoutAuthentication(ApiTester $I)
    {
        $I->wantTo('Edit Permissionset Without Valid Authentication');
        $I->haveHttpHeader('Content-Type', 'application/json');        
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateEBIPermissionSet();
        $I->sendPOST('permissionset',$request);
        $permission = json_decode($I->grabResponse());
        $request['permission_template_id'] = 1;
        $request['intent_to_leave'] = false;       
        $I->sendPUT('permissionset',$request);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testEditPermissionSetWithInvalidId(ApiTester $I)
    {
        $I->wantTo('Edit Permissionset With Invalid Id');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateEBIPermissionSet();
        $I->sendPOST('permissionset',$request);
        $request['permission_template_id'] = -1;
        $request['intent_to_leave'] = false;
        $I->sendPUT('permissionset',$request);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function  testEditPermissionSetWithOutName(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo('Edit Permissionset Without Name');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateEBIPermissionSet();
        $I->sendPOST('permissionset',$request);
        $permission = json_decode($I->grabResponse());
        $request['permission_template_id'] = $permission->data->permission_template_id;
        $request['permission_template_name'] = null;
        $request['intent_to_leave'] = false;
        $I->sendPUT('permissionset',$request);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testIsPermissionSetExistsValid(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo('Is PermissionSet Exists by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateEBIPermissionSet();
        $I->sendPOST('permissionset',$request);
        $res = json_decode($I->grabResponse());
        $I->sendGET('permissionset/exists/'.$res->data->permission_template_name);
        $I->seeResponseContains("true");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

    }

    public function testIsPermissionSetExistsInvalidAuthentication(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $this->authenticate($I);
        $I->wantTo('Is PermissionSet Exists Without Valid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('permissionset/exists/'.$this->permissionSetname);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testIsPermissionSetExistsWithInvalidName(ApiTester $I)
    {
        $I->wantTo('Is PermissionSet Exists Without Name by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('permissionset/exists/'.$this->permissionSetInvalid);
        $I->seeResponseContains("false");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

    }

    public function testUpdatePermissionSetStatus(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo('Update Permission set by status by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $request = $this->getJsonForCreateEBIPermissionSet();
        $I->sendPOST('permissionset',$request);
        $res = json_decode($I->grabResponse());
        $I->sendPUT('permissionset/updatestatus',[
                "lang_id"=> 1,
                "permission_template_name"=> $res->data->permission_template_name,
                "permission_template_id"=> $res->data->permission_template_id,
                "permission_template_status"=> "active"
        ]);
        $I->seeResponseContains('permission_template_name');
        $I->seeResponseContains('permission_template_id');
        $I->seeResponseContains('permission_template_status');
        $I->seeResponseContainsJson(array('permission_template_id' => $res->data->permission_template_id));
        $I->seeResponseContainsJson(array('permission_template_name' => $res->data->permission_template_name));
        $I->seeResponseCodeIs(200);
    }

    public function testUpdatePermissionSetStatusInvalidAuthentication(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $this->authenticate($I);
        $I->wantTo('Update Permission set by status With Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('permissionset/updatestatus',[
            "lang_id"=> 1,
            "permission_template_name"=> "Financial Aid Advisiors",
            "permission_template_id"=> 1,
            "permission_template_status"=> "active"
        ]);
        $I->seeResponseCodeIs(403);
    }


    public function testUpdatePermissionSetStatusInvalid(ApiTester $I)
    {
        $I->wantTo('Update Permission set by status with invalid permission by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendPUT('permissionset/updatestatus',[
            "lang_id"=> 1,
            "permission_template_name"=> "Financial Aid Advisiors",
            "permission_template_id"=> -1,
            "permission_template_status"=> "invalid"
        ]);
        $I->seeResponseCodeIs(400);
    }

    public function testGetDatablocks(ApiTester $I)
    {
        $I->wantTo('Get Data Blocks by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('permissionset/'.$this->langValid.'/type/profile');
        $request=json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array('data_block_type' => $request->data->data_block_type));
        $I->seeResponseContainsJson(array('lang_id' => $request->data->lang_id));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetDatablocksInvalidAuthentication(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $this->authenticate($I);
        $I->wantTo('Get Data Blocks With Invalid Authentication  by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('permissionset/'.$this->langValid.'/type/profile');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetDatablocksInvalidLang(ApiTester $I)
    {
        $I->wantTo('Get Data Blocks With Invalid Language by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET('permissionset/'.$this->langInvalid.'/type/profile');
        $I->seeResponseCodeIs(400);
    }
    
    private function reportAccessArray() {
    	for($i = 1; $i<5; $i++){
    		$report['id'] = $i;
    		$report['name'] = 'report'.$i;
    		$report['short_code'] = "SHORT_COdE".$i;
    		$report['selection'] = true;
    		$reports[] = $report;
    	}
    
    	return $reports;
    }
}
