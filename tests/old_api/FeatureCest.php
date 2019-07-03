<?php
require_once 'SynapseTestHelper.php';
class FeatureCest extends SynapseTestHelper
{

    private $token;

    private $orgId = 1;
    
    private $invalidOrgId = -2;
    
    private $langId = 1;
    
    private $referralFeatureId = 1;
    
    private $referralOrgId = 1;
    private $notesFeatureId = 2;
    private $notesOrgId = 2;
    private $logContactsFeatureId = 3;
    private $log_contacts_org_id = 3;
    private $booking_feature_id = 4;

    public function _before(ApiTester $I)
    {
       $this->token = $this->authenticate($I);
    }

    public function testupdateFeatureError(ApiTester $I)
    {
        $I->wantTo('Updating Features For an Organization- Invalid');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendPUT('features', [
            "organization_id" => $this->orgId,
            "referral_feature_id" => 1,
            "referral_org_id" => 1,
            "is_referral_enabled" => false,
            "notes_feature_id" => 2,
            "notes_org_id" => 2,
            "is_notes_enabled" => true,
            "log_contacts_feature_id" => 3,
            "log_contacts_org_id" => 3,
            "is_log_contacts_enabled" => true,
            "booking_feature_id" => 4,
            "booking_org_id" => 4,
            "is_booking_enabled" => false,
            "student_referral_notification_feature_id" => 5,
            "student_referral_notification_org_id" => 5,
            "is_student_referral_notification_enabled" => true,
            "reason_routing_feature_id" => 6,
            "reason_routing_org_id" => 7,
            "is_reason_routing_enabled" => false
        ]
        );
        $I->seeResponseCodeIs(400);
        
        $I->seeResponseIsJson();
    }
    
    public function testupdateFeature(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Updating Features For an Organization');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendPUT('features', [
            "organization_id" => $this->orgId,
            "referral_feature_id" => $this->referralFeatureId,
            "referral_org_id" => $this->referralOrgId,
            "is_referral_enabled" => false,
            "notes_feature_id" => $this->notesFeatureId,
            "notes_org_id" => $this->notesOrgId,
            "is_notes_enabled" => true,
            "log_contacts_feature_id" => $this->logContactsFeatureId,
            "log_contacts_org_id" => $this->log_contacts_org_id,
            "is_log_contacts_enabled" => true,
            "booking_feature_id" => $this->booking_feature_id,
            "booking_org_id" => 4,
            "is_booking_enabled" => false,
            "student_referral_notification_feature_id" => 5,
            "student_referral_notification_org_id" => 5,
            "is_student_referral_notification_enabled" => true,
            "reason_routing_feature_id" => 6,
            "reason_routing_org_id" => 6,
            "is_reason_routing_enabled" => false,
            "email_feature_id" => 7,
            "email_org_id" => 7,
            "is_email_enabled" => true,
            "primary_campus_connection_referral_routing_feature_id" => 8,
            "primary_campus_connection_referral_routing_org_id" => 8,
            "is_primary_campus_connection_referral_routing_enabled" => true
            ]
        );
        $feature = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array('organization_id' => $this->orgId));
        $I->seeResponseContainsJson(array('referral_feature_id' => $this->referralFeatureId));
        $I->seeResponseContainsJson(array('notes_feature_id' => $this->notesFeatureId));
        $I->seeResponseContainsJson(array('referral_org_id' => $this->referralOrgId));
        $I->seeResponseContainsJson(array('notes_org_id' => $this->notesOrgId));
        $I->seeResponseContainsJson(array('log_contacts_feature_id' => $this->logContactsFeatureId));
        $I->seeResponseContainsJson(array('log_contacts_org_id' => $this->log_contacts_org_id));
        $I->seeResponseContainsJson(array('booking_feature_id' => $this->booking_feature_id));
        $I->seeResponseCodeIs(201);
    
        $I->seeResponseIsJson();
    }
    
    
    public function testupdateFeatureInvalidAuthentication(ApiTester $I)
    {
    	$I->wantTo('Updating Features For an Organization with invalid Authentication');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendPUT('features', [
    			"organization_id" => $this->orgId,
    			"referral_feature_id" => $this->referralFeatureId,
    			"referral_org_id" => $this->referralOrgId,
    			"is_referral_enabled" => false,
    			"notes_feature_id" => $this->notesFeatureId,
    			"notes_org_id" => $this->notesOrgId,
    			"is_notes_enabled" => true,
    			"log_contacts_feature_id" => $this->logContactsFeatureId,
    			"log_contacts_org_id" => $this->log_contacts_org_id,
    			"is_log_contacts_enabled" => true,
    			"booking_feature_id" => $this->booking_feature_id,
    			"booking_org_id" => 4,
    			"is_booking_enabled" => false,
    			"student_referral_notification_feature_id" => 5,
    			"student_referral_notification_org_id" => 5,
    			"is_student_referral_notification_enabled" => true,
    			"reason_routing_feature_id" => 6,
    			"reason_routing_org_id" => 6,
    			"is_reason_routing_enabled" => false
    			]
    	);
    	$I->seeResponseCodeIs(403);
    	
    	$I->seeResponseIsJson();
    }

    public function testGetCampusFeatures(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Get Feature Requests for an organization');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET('features/'.$this->orgId.'/'.$this->langId);
        $details = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array('organization_id' => $this->orgId));
        $I->seeResponseContains('referral_feature_id');
        $I->seeResponseContains('notes_feature_id');
        $I->seeResponseContains('referral_org_id');
        $I->seeResponseContains('notes_org_id');
        $I->seeResponseContains('log_contacts_feature_id');
        $I->seeResponseContains('log_contacts_org_id');
        $I->seeResponseContains('booking_feature_id');
        $I->seeResponseContains('reason_routing_list');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
    
    
    public function testGetCampusFeaturesByInvalidOrgId(ApiTester $I)
    {
    	$I->wantTo('Get Feature Requests for an invalid organization');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->sendGET('features/'.$this->invalidOrgId.'/'.$this->langId);
    	$I->seeResponseCodeIs(403);
    	$I->seeResponseIsJson();
    }
    
    
    public function testGetCampusFeaturesInvalidAuthentication(ApiTester $I)
    {
    	$I->wantTo('Get Feature Requests for an organization with Invalid Authentication');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('features/'.$this->orgId.'/'.$this->langId);
    	$I->seeResponseCodeIs(403);
    	$I->seeResponseIsJson();
    }
}
