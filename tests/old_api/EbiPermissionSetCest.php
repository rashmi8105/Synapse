<?php
require_once 'SynapseTestHelper.php';
class EbiPermissionSetCest  extends SynapseTestHelper
{
    private $token; 
    private $langId = 1;
   
    
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

	private function getJsonForCreateEBIPermissionSet()
    {
       	$permissionsetName = uniqid("EBIPemission_",true);
        $permission = [];  
		$permission['lang_id'] = $this->langId;				
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
