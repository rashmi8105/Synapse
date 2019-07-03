<?php
use Doctrine\DBAL\Types\BooleanType;
require_once 'SynapseTestHelper.php';
class OrgPermissionSetCest  extends SynapseTestHelper
{
    private $token;
    private $organization = 1;
    
    private $invalidOrganization = -1;
   
    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    
    }
    
    public function testCreateOrganizationPermissionSetWithAll(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Create a Organization Permissionset by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateOrgPermissionSet(); 
        //print_r($request); exit;   
        $I->sendPOST('orgpermissionset',$request);        
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson(array('permission_template_name' => $request['permission_template_name']));
        $I->seeResponseContainsJson(array('risk_indicator' => true));
        $I->seeResponseContainsJson(array('intent_to_leave' => true));
        $I->seeResponseContains('permission_template_id');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('permission_template_id');
        $I->seeResponseContains('permission_template_name');
        $I->seeResponseContains('profile_blocks');
        $I->seeResponseContains('survey_blocks');
        $I->seeResponseContains('features');
        $I->seeResponseContains('isp');
        $I->seeResponseContains('isq');
        
        $I->seeResponseIsJson();
    }

    
    public function testGetFeatureMasterStatus(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
    	$I->wantTo('Get feature master status for organization by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('orgpermissionset/feature?orgid='.$this->organization);
    	$data = json_decode($I->grabResponse());	
    	$I->seeResponseContainsJson(array('organization_id' => $this->organization));
    	$I->seeResponseContains('referral_feature_id');
    	$I->seeResponseContains('is_referral_enabled');
    	$I->seeResponseContains('notes_feature_id');
    	$I->seeResponseContains('log_contacts_feature_id');
    	$I->seeResponseContains('is_notes_enabled');
    	$I->seeResponseContains('is_log_contacts_enabled');
    	$I->seeResponseContains('booking_feature_id');
    	$I->seeResponseContains('is_booking_enabled');
    	$I->seeResponseContains('student_referral_notification_feature_id');
    	$I->seeResponseContains('is_student_referral_notification_enabled');
    	$I->seeResponseContains('reason_routing_feature_id');
    	$I->seeResponseContains('is_reason_routing_enabled');
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    	
    	
    }
    
    
    public function testGetFeatureMasterStatusByInvalidOrgId(ApiTester $I)
    {
    	$I->wantTo('Get feature master status for invalid organization by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('orgpermissionset/feature?orgid='.$this->invalidOrganization);
    	$I->seeResponseCodeIs(400);
    	$I->seeResponseIsJson();
    }
    
    
    public function testGetFeatureMasterStatusByInvalidAuthentication(ApiTester $I)
    {
    	$I->wantTo('Get feature master status for organization with invalid authentication by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated('invalid_token');
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('orgpermissionset/feature?orgid='.$this->invalidOrganization);
    	$I->seeResponseCodeIs(401);
    	$I->seeResponseIsJson();
    }
    
    public function testCreateOrganizationPermissionSetWithOutName(ApiTester $I)
    {
        $I->wantTo('Create a Organization Permissionset Without Permissionset Name');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateOrgPermissionSet();
        $request['permission_template_name'] = null;
        $I->sendPOST('orgpermissionset',$request);
        $I->seeResponseCodeIs(400);
         
    
        $I->seeResponseIsJson();
    }
    
    public function testCreateOrganizationPermissionSetWithOutAuthenticate(ApiTester $I)
    {
        $I->wantTo('Create a Organization Permissionset Without Authentication ');
        $I->haveHttpHeader('Content-Type', 'application/json');
        // $I->amBearerAuthenticated( "fsdgdfgdfg123424");
        $request = $this->getJsonForCreateOrgPermissionSet();
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateOrgPermissionSet();
        $I->sendPOST('orgpermissionset',$request);
        $I->seeResponseCodeIs(403);
         
    
        $I->seeResponseIsJson();
    }
    
    /**
     *
     * Test cases for updateOrganizationPermissionSetAction
     */
    
    public function testUpdateOrganizationPermissionSetWithAll(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo('Update a Organization Permissionset With Valid Data');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateOrgPermissionSet();
        $I->sendPOST('orgpermissionset',$request);
        $permission = json_decode($I->grabResponse());
        $request['permission_template_id'] = $permission->data->permission_template_id;
        $request['intent_to_leave'] = false;
        $I->sendPUT('orgpermissionset',$request);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('permission_template_id' => $request['permission_template_id']));
        $I->seeResponseContainsJson(array('permission_template_name' => $request['permission_template_name']));
        $I->seeResponseContainsJson(array('risk_indicator' => true));
        $I->seeResponseContainsJson(array('intent_to_leave' => false));
        $I->seeResponseContains('permission_template_id');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('permission_template_id');
        $I->seeResponseContains('permission_template_name');
        $I->seeResponseContains('profile_blocks');
        $I->seeResponseContains('survey_blocks');
        $I->seeResponseContains('features');
        $I->seeResponseContains('isp');
        $I->seeResponseContains('isq');
    
        $I->seeResponseIsJson();
    }
    
    public function testUpdateOrganizationPermissionSetWithOutName(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo('Update a Organization Permissionset With Valid Data');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateOrgPermissionSet();
        $I->sendPOST('orgpermissionset',$request);
        $permission = json_decode($I->grabResponse());
        $request['permission_template_id'] = $permission->data->permission_template_id;
        $request['permission_template_name'] = null;
        $request['intent_to_leave'] = false;
        $I->sendPUT('orgpermissionset',$request);
        $I->seeResponseCodeIs(400);
         
    
        $I->seeResponseIsJson();
    }
    
    public function testUpdateOrganizationPermissionSetWithAuthenication(ApiTester $I)
    {
        $I->wantTo('Update a Organization Permissionset With out Valid Authentication');
        $I->haveHttpHeader('Content-Type', 'application/json');
      
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateOrgPermissionSet();
       
        $request['permission_template_id'] = 1;
        $request['intent_to_leave'] = false;
        
        $I->sendPUT('orgpermissionset',$request);
        $I->seeResponseCodeIs(403);
    
        $I->seeResponseIsJson();
    }
    public function testUpdateOrganizationPermissionSetWithInvalidId(ApiTester $I)
    {
        $I->wantTo('Update a Organization Permissionset With Invalid Id');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateOrgPermissionSet();
        $I->sendPOST('orgpermissionset',$request);
        $permission = json_decode($I->grabResponse());
        $request['permission_template_id'] = -1;
        $request['intent_to_leave'] = false;
         
        $I->sendPUT('orgpermissionset',$request);
        $I->seeResponseCodeIs(400);
    
        $I->seeResponseIsJson();
    }
    
    
    /**
     *
     * Test Cases For getOrganizationPersmissionsetAction
     */
    public function testGetOrganizationPersmissionset(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo('Get A Organization Permission set');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $request = $this->getJsonForCreateOrgPermissionSet();
        $I->sendPOST('orgpermissionset',$request);
        $permission = json_decode($I->grabResponse());
        $request['permission_template_id'] = $permission->data->permission_template_id;
         
        $I->sendGET('orgpermissionset?id='. $request['permission_template_id']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('permission_template_id' => $request['permission_template_id']));
        $I->seeResponseContainsJson(array('permission_template_name' => $request['permission_template_name']));
        $I->seeResponseContainsJson(array('risk_indicator' => true));
        $I->seeResponseContainsJson(array('intent_to_leave' => true));
        $I->seeResponseContains('permission_template_id');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('permission_template_id');
        $I->seeResponseContains('permission_template_name');
        $I->seeResponseContains('profile_blocks');
        $I->seeResponseContains('survey_blocks');
        $I->seeResponseContains('features');
        $I->seeResponseContains('isp');
        $I->seeResponseContains('isq');
        $I->seeResponseIsJson();
    }
    
    public function testGetOrganizationPersmissionsetWithOutValidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get A permission Set  Without Valid Authentication');
        $I->haveHttpHeader('Content-Type', 'application/json');
        
        $I->haveHttpHeader('Accept', 'application/json');

        $request['permission_template_id'] = 1;
        
        $I->sendGET('orgpermissionset');
        $I->seeResponseCodeIs(403);
        $I->sendGET('orgpermissionset?id='. $request['permission_template_id']);
        $I->seeResponseIsJson();
    }
    
    

    public function testGetOrganizationPermissionsetsValid(ApiTester $I)
    {
        $I->wantTo('Get organization persmission sets with valid organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('orgpermissionset/list?orgId='.$this->organization);
        $I->seeResponseContainsJson(array('organization_id' => $this->organization));
        $I->seeResponseContains('permission_templates');
        $I->seeResponseContains('profile_blocks');
        $I->seeResponseContains('isp');
        $I->seeResponseContains('survey_blocks');
        $I->seeResponseContains('isq');
        $I->seeResponseContains('permission_template_id');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('permission_template_name');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

    }

    public function testGetOrganizationPermissionsetsInvalid(ApiTester $I)
    {
        $I->wantTo('Get organization persmission sets with invalid organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('orgpermissionset/list?orgId='.$this->invalidOrganization);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetOrganizationPermissionsetsInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get organization persmission sets with invalid authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('orgpermissionset/list?orgId='.$this->organization);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();

    }
	
    public function testGetLoggedInPermissionSet(ApiTester $I){
    	$I->wantTo('Get permission set templates for the logged in users.');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$request = $this->getJsonForCreateOrgPermissionSet();
    	$I->sendPOST('orgpermissionset',$request);
    	$I->sendPOST('organization/'.$this->organization.'/group', $this->createGroup());    	    	
    	$I->sendGET('orgpermissionset/permissions');
    	$permission = json_decode($I->grabResponse());
    	$I->seeResponseContainsJson(array('organization_id' => $permission->data->organization_id));
    	$I->seeResponseContains('organization_id');
    	$I->seeResponseContains('permission_templates');
    	$I->seeResponseContains('profile_blocks');
    	$I->seeResponseContains('isp');
    	$I->seeResponseContains('survey_blocks');
    	$I->seeResponseContains('isq');
    	$I->seeResponseContains('permission_template_id');
    	$I->seeResponseContains('organization_id');
    	$I->seeResponseContains('permission_template_name');
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
    
    public function testGetSurveyBlocksPermission(ApiTester $I){
    	$I->wantTo('Get Survey Blocks Permission for the logged in users.');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');    	    	
    	$I->sendGET('orgpermissionset/surveyBlocks');
    	$permission = json_decode($I->grabResponse());    	    	
    	$I->seeResponseContains('survey_blocks');    	
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
    
    public function testGetProfileBlockPermission(ApiTester $I){
    	$I->wantTo('Get Profile Block Permission for the logged in users.');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');    	
    	$I->sendGET('orgpermissionset/profileBlocks');
    	$permission = json_decode($I->grabResponse());
    	$I->seeResponseContains('profile_blocks');
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
	
    public function testGetAllowedIspIsqBlocks(ApiTester $I){
    	$I->wantTo('Get Allowed Isp Blocks for the logged in users.');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');    	
    	$I->sendGET('orgpermissionset/ispBlocks');
    	$permission = json_decode($I->grabResponse());
    	$I->seeResponseContains('isp');
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
    
    public function testGetAllowedIsqIsqBlocks(ApiTester $I){
    	$I->wantTo('Get Allowed Isq Blocks for the logged in users.');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');    	
    	$I->sendGET('orgpermissionset/isqBlocks');
    	$permission = json_decode($I->grabResponse());
    	$I->seeResponseContains('isq');
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
    
    public function testGetRiskIndicator(ApiTester $I){
    	$I->wantTo('Get Risk Indicator for the logged in users.');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');    	
    	$I->sendGET('orgpermissionset/riskIndicator');    	
    	$I->seeResponseContains('risk_indicator');
    	$I->seeResponseContains('intent_to_leave');
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
    
    public function testGetAllowedFeaturesBlock(ApiTester $I, $scenario){
        //$scenario->skip("Failed");
    	$I->wantTo('Get Allowed Features Block for the logged in users.');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('orgpermissionset/featuresBlock');
    	$I->seeResponseContains('id');
    	$I->seeResponseContains('name');
    	$I->seeResponseContains('private_share');
    	$I->seeResponseContains('public_share');
    	$I->seeResponseContains('teams_share');
    	$I->seeResponseContains('receiveReferrals');    	
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
    
    public function testGetAllowedFeaturesList(ApiTester $I, $scenario){
        //$scenario->skip("Failed");
    	$I->wantTo('Get Allowed Features Block for the logged in users.');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('orgpermissionset/features');
    	$I->seeResponseContains('features');
    	$I->seeResponseContains('id');
    	$I->seeResponseContains('name');
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
        
    public function testGetAllowedFeatures(ApiTester $I){
    	$I->wantTo('Get Allowed Features individually for the logged in users.');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('orgpermissionset/featureAccess?featureType=CREATE_CONTACT');
    	$response = json_decode($I->grabResponse());    	
    	$I->seeResponseCodeIs(BooleanType::hasType('boolean'));    	
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
        
    public function testGetAccessLevelPermission(ApiTester $I){
    	$I->wantTo('Get Access Level Permission for the logged in users.');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('orgpermissionset/accessLevel');
    	$I->seeResponseContains('individual_and_aggregate');
    	$I->seeResponseContains('aggregate_only');
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
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
    
    /**
     * 
     *  Common Functions
     */
   
    private function getJsonForCreateOrgPermissionSet()
    {
        $permissionsetName = uniqid("OrgPemission_",true);
        $permission = [];
        $permission['organization_id'] = $this->organization;
        $permission['permission_template_name'] = $permissionsetName;
        $permission['access_level'] = ['individual_and_aggregate' => true,'aggregate_only'=>false];
        $permission['risk_indicator'] = true;
        $permission['intent_to_leave'] = true;        
        $permission['courses_access']['view_courses'] = true;
        $permission['courses_access']['create_view_academic_update'] = true;
        $permission['courses_access']['view_all_academic_update_courses'] = true;
        $permission['courses_access']['view_all_final_grades'] = true;
        
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
    
    
        $isp = [];
            for($i = 1; $i<2;$i++)
            {
            $isp[] = $this->createIndividual($i,true);
    }
    $permission['isp'] = $isp;
    
    $isq = [];
    for($i = 1; $i<3;$i++)
    {
    $isq[] = $this->createIndividual($i,true);
    }
    $permission['isq'] = $isq;
    
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
    
    private function createIndividual($id,$isSelected = false)
    {
        $isp = ["id" => $id, "survey_id"=> 1, "cohort_id"=> 3,"block_selection" => $isSelected];
        return $isp;
    }
        
    private function createGroup(){
    	$group = [
    	"organization_id"=> $this->organization,
    	"parent_group_id"=> 0,
    	"group_name"=> uniqid("Group_",true),
    	"staff_list"=> [
    	[
    	"staff_id"=> 1,
    	"staff_permissionset_id"=> 1,
    	"staff_is_invisible" => 1
    	],
    	[
    	"staff_id"=> 3,
    	"staff_permissionset_id"=> 1,
    	"staff_is_invisible" => 0
    	]
    	]
    	];
    	
    	return $group;
    }
}
