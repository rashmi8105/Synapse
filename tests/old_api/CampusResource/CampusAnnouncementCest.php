<?php
require_once 'tests/api/SynapseTestHelper.php';

class CampusAnnouncementCest extends SynapseTestHelper
{

    private $orgId = 1;
    
    private $langId = 1;
	
	private $invalidOrg = - 100;
	
	private $personId = 1;
	
	private $alertName = "System Message";
	
	private $alertDescription = "System Message Description";
	
	private $studentId = 8;
	
	private $studentExternalId = "external_id-8";
	
	private $shareToPersonId = 3;
	
	private $invalidStudentId = -1;
	
	private $messageDurationDay = "day";
	
	private $messageTypeBanner = "banner";

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testCreateSystemAlertBannerForDay(ApiTester $I)
    {
        $I->wantTo('Create System Message for Day by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
		
        $alertName = $this->alertName.rand(1,10);
        $sdate=new DateTime();
        $edate=new DateTime("+ 1 day");
        
        $I->sendPOST('campusannouncements', [
        		"organization_id"=> $this->orgId,
        		"person_id"=> $this->personId,
        		"lang_id"=> $this->langId,
        		"message"=> $alertName,
        		"message_type"=> $this->messageTypeBanner,
        		"message_duration"=> $this->messageDurationDay,
        		"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
        		"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            	"organization_id"=> $this->orgId,
        		"person_id"=> $this->personId,
        		"lang_id"=> $this->langId,
        		"message"=> $alertName,
        		"message_type"=> $this->messageTypeBanner,
        		"message_duration"=> $this->messageDurationDay,
        		"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
        		"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
        ));
        $I->seeResponseContains('id');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
    
    public function testCreateSystemAlertBannerForWeek(ApiTester $I)
    {
    	$I->wantTo('Create System Message for Week by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept-Language', null);
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    
    	$alertName = $this->alertName.rand(11,20);
    	$sdate=new DateTime();
    	$edate=new DateTime("+ 7 day");
    
    	$I->sendPOST('campusannouncements', [
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> $this->messageTypeBanner,
    			"message_duration"=> "week",
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    			]);
    
    	$resp = json_decode($I->grabResponse());
    	$I->seeResponseIsJson(array(
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> $this->messageTypeBanner,
    			"message_duration"=> "week",
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    	));
    	$I->seeResponseContains('id');
    	$I->seeResponseCodeIs(201);
    	$I->seeResponseIsJson();
    }
    
    public function testCreateSystemAlertBannerForMonth(ApiTester $I)
    {
    	$I->wantTo('Create System Message for Month by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept-Language', null);
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    
    	$alertName = $this->alertName.rand(21,30);
    	$sdate=new DateTime();
    	$edate=new DateTime("+ 1 month");
    
    	$I->sendPOST('campusannouncements', [
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> $this->messageTypeBanner,
    			"message_duration"=>  "month",
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    			]);
    
    	$resp = json_decode($I->grabResponse());
    	$I->seeResponseIsJson(array(
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> $this->messageTypeBanner,
    			"message_duration"=> "month",
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    	));
    	$I->seeResponseContains('id');
    	$I->seeResponseCodeIs(201);
    	$I->seeResponseIsJson();
    }
    
    public function testCreateSystemAlertBannerForCustomDate(ApiTester $I)
    {
    	$I->wantTo('Create System Message for Month by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept-Language', null);
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    
    	$alertName = $this->alertName.rand(31,40);
    	$sdate=new DateTime();
    	$edate=new DateTime("+ 10 hours");
    
    	$I->sendPOST('campusannouncements', [
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> $this->messageTypeBanner,
    			"message_duration"=>  "custom",
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    			]);
    
    	$resp = json_decode($I->grabResponse());
    	$I->seeResponseIsJson(array(
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> $this->messageTypeBanner,
    			"message_duration"=> "custom",
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    	));
    	$I->seeResponseContains('id');
    	$I->seeResponseCodeIs(201);
    	$I->seeResponseIsJson();
    }

    public function testGetAllScheduledSystemAlerts(ApiTester $I)
    {
    	$I->wantTo('Get All scheduled System Message by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept-Language', null);
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    
    	$alertName = $this->alertName.rand(41,50);
    	$sdate=new DateTime();
    	$edate=new DateTime("+ 1 day");
    
    	$I->sendPOST('campusannouncements', [
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> $this->messageTypeBanner,
    			"message_duration"=>  $this->messageDurationDay,
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    			]);
    	
    	$I->sendGET('campusannouncements?type=scheduled');
    	$resp = json_decode($I->grabResponse());
    	
        $I->seeResponseIsJson(array(
        		'organization_id' => $this->orgId
        ));
        $I->seeResponseIsJson(array(
        		'person_id' => $this->personId
        ));
        $I->seeResponseContains('system_message');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }
    
    public function testDeleteSystemAlert(ApiTester $I)
    {
    	$I->wantTo('Delete System Message by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept-Language', null);
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    
    	$alertName = $this->alertName.rand(51,60);
    	$sdate=new DateTime();
    	$edate=new DateTime("+ 1 day");
    
    	$I->sendPOST('campusannouncements', [
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> $this->messageTypeBanner,
    			"message_duration"=>  $this->messageDurationDay,
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    			]);
    	$resp = json_decode($I->grabResponse());
    	$id = $resp->data->id;
    	$I->sendDELETE("campusannouncements/$id");
    	
    	$I->canSeeResponseCodeIs(204);
    }
    
    public function testGetAllDeletedSystemAlerts(ApiTester $I)
    {
    	$I->wantTo('Get All Deleted System Message by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept-Language', null);
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    
    	$alertName = $this->alertName.rand(61,70);
    	$sdate=new DateTime();
    	$edate=new DateTime("+ 1 day");
    
    	$I->sendPOST('campusannouncements', [
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> $this->messageTypeBanner,
    			"message_duration"=>  $this->messageDurationDay,
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    			]);
		
    	$resp = json_decode($I->grabResponse());
    	$id = $resp->data->id;
    	$I->sendDELETE("campusannouncements/$id");
    	
    	$I->sendGET('campusannouncements?type=archived');
    	$resp = json_decode($I->grabResponse());
    	 
    	$I->seeResponseIsJson(array(
    			'organization_id' => $this->orgId
    	));
    	$I->seeResponseIsJson(array(
    			'person_id' => $this->personId
    	));
    	$I->seeResponseContains('system_message');
    	$I->canSeeResponseCodeIs(200);
    	$I->canSeeResponseIsJson();
    }
    
    public function testGetOneSystemAlertById(ApiTester $I)
    {
    	$I->wantTo('Get by id System Message by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept-Language', null);
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    
    	$alertName = $this->alertName.rand(71,80);
    	$sdate=new DateTime();
    	$edate=new DateTime("+ 1 day");
    
    	$I->sendPOST('campusannouncements', [
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> $this->messageTypeBanner,
    			"message_duration"=>  $this->messageDurationDay,
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    			]);
    
    	$resp = json_decode($I->grabResponse());
    	$id = $resp->data->id;
    	 
    	$I->sendGET("campusannouncements/$id");
    	$resp = json_decode($I->grabResponse());
    
    	$I->seeResponseIsJson(array(
    			'organization_id' => $this->orgId
    	));
    	$I->seeResponseIsJson(array(
    			'person_id' => $this->personId
    	));
    	$I->seeResponseContains('system_message');
    	$I->canSeeResponseCodeIs(200);
    	$I->canSeeResponseIsJson();
    }
    
    public function testEditSystemAlertById(ApiTester $I)
    {
    	$I->wantTo('Edit by id System Message by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept-Language', null);
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    
    	$alertName = $this->alertName.rand(81,90);
    	$sdate=new DateTime();
    	$edate=new DateTime("+ 1 day");
    
    	$I->sendPOST('campusannouncements', [
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> $this->messageTypeBanner,
    			"message_duration"=>  $this->messageDurationDay,
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    			]);
    
    	$resp = json_decode($I->grabResponse());
    	$id = $resp->data->id;
    	
    	$alertName = $this->alertName.rand(91,100);
    	$I->sendPUT('campusannouncements', [
    			"id"=> $id,
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> "alert bell",
    			"message_duration"=>  $this->messageDurationDay,
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    			]);
    	
    	$I->canSeeResponseCodeIs(204);

    }
    
    public function testListCampusAnnouncementsBanner(ApiTester $I)
    {
    	$I->wantTo('Get by id System Message Banner by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept-Language', null);
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    
    	$alertName = $this->alertName.rand(101,110);
    	$sdate=new DateTime();
    	$edate=new DateTime("+ 1 day");
    
    	$I->sendPOST('campusannouncements', [
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> $this->messageTypeBanner,
    			"message_duration"=>  $this->messageDurationDay,
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    			]);
    
    	$resp = json_decode($I->grabResponse());
    	$id = $resp->data->id;
    
    	$I->sendGET("campusannouncements/banner");
    	$resp = json_decode($I->grabResponse());
    
    	$I->seeResponseIsJson(array(
    			'organization_id' => $this->orgId
    	));
    	$I->seeResponseIsJson(array(
    			'person_id' => $this->personId
    	));
    	$I->seeResponseContains('system_message');
    	$I->canSeeResponseCodeIs(200);
    	$I->canSeeResponseIsJson();
    }
    
    public function testInavlidDateCampusAnnouncements(ApiTester $I)
    {
    	$I->wantTo('Set System Message Inavlid Date by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept-Language', null);
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    
    	$alertName = $this->alertName.rand(111,120);
    	$sdate=new DateTime();
    	$edate=new DateTime("- 1 day");
    
    	$I->sendPOST('campusannouncements', [
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> $this->messageTypeBanner,
    			"message_duration"=>  $this->messageDurationDay,
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    			]);
    
    	$resp = json_decode($I->grabResponse());
    	
    	$I->canSeeResponseCodeIs(400);
    }
    
    public function testCancelCampusAnnouncements(ApiTester $I)
    {
    	$I->wantTo('System Message Cancel by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept-Language', null);
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    
    	$alertName = $this->alertName.rand(121,130);
    	$sdate=new DateTime();
    	$edate=new DateTime("+ 1 day");
    
    	$I->sendPOST('campusannouncements', [
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"lang_id"=> $this->langId,
    			"message"=> $alertName,
    			"message_type"=> $this->messageTypeBanner,
    			"message_duration"=>  $this->messageDurationDay,
    			"start_date_time"=> $sdate->format('Y-m-d\TH:i:sO'),
    			"end_date_time"=> $edate->format('Y-m-d\TH:i:sO')
    			]);
    
    	$resp = json_decode($I->grabResponse());
    	$id = $resp->data->id;
    	
    	$I->sendPUT("campusannouncements/$id", [
    			"id"=> $id,
    			"organization_id"=> $this->orgId,
    			"person_id"=> $this->personId,
    			"status"=> 1,
    			"display_type"=> "alert bell"
    			]);
    	 
    	$I->canSeeResponseCodeIs(201);
    }
}