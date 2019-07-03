<?php
use Synapse\CoreBundle\Util\Helper;
require_once 'SynapseTestHelper.php';

class OfficeHoursCest extends SynapseTestHelper
{
	private $token;
    private $invalidPersonId=-1;
    private $personIdProxy=0;
    private $organizationId=1;
    private $personId=1;
	private $invalidOfficehour =-1;

	
    public function _before(ApiTester $I)
	{
		$this->token = $this->authenticate($I);
	}

    public function testCreateOfficeHoursSeriesWithoutAuthentication(ApiTester $I)
    {
        $I->wantTo('Create Office Hours Series Without Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 13 hour");
        $edate=new DateTime("+ 14 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d H:i:s'),
                "slot_end"=>$edate->format('Y-m-d H:i:s'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"N"
                ]
            ]
        );
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testCreateOfficeHoursSeriesWithAllValues(ApiTester $I)
    {
        $I->wantTo('Create Office Hours Series With All Values by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 13 hour");
        $edate=new DateTime("+ 14 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"D"
                ]
            ]
        );
        $I->seeResponseCodeIs(201);
        $request=json_decode($I->grabResponse());
        
        $I->seeResponseContainsJson(array('slot_type' =>$request->data->slot_type));
        $I->seeResponseContainsJson(array('person_id' =>$request->data->person_id));
        $I->seeResponseContainsJson(array('organization_id' =>$request->data->organization_id));
        $I->seeResponseContainsJson(array('person_id_proxy' =>$request->data->person_id_proxy));
        $I->seeResponseContainsJson(array('repeat_pattern' =>$request->data->series_info->repeat_pattern));
        $I->seeResponseContainsJson(array('repeat_range' =>$request->data->series_info->repeat_range));
        $I->seeResponseContainsJson(array('office_hours_id' =>$request->data->office_hours_id));
        $I->seeResponseContainsJson(array('location' =>$request->data->location));
        $I->seeResponseContainsJson(array('meeting_length' =>$request->data->series_info->meeting_length));
        $I->seeResponseContains('meeting_length');
        $I->seeResponseContains('repeat_pattern');
        $I->seeResponseContains('repeat_every');
        $I->seeResponseContains('repeat_days');
        $I->seeResponseContains('repeat_range');
        $I->seeResponseContains('meeting_length');
        $I->seeResponseContains('repeat_pattern');
        $I->seeResponseContains('repeat_occurence');
        $I->seeResponseContains('slot_start');
        $I->seeResponseContains('slot_end');
        $I->seeResponseContains('location');
        $I->seeResponseIsJson();
    }

    public function testCreateOfficeHoursSeriesDaily(ApiTester $I)
    {
        $I->wantTo('Create Office Hours Series On Repeat Pattern Daily by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 13 hour");
        $edate=new DateTime("+ 14 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"D"
                ]
            ]
        );
        $I->seeResponseCodeIs(201);
        $request=json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array('slot_type' =>$request->data->slot_type));
        $I->seeResponseContainsJson(array('person_id' =>$request->data->person_id));
        $I->seeResponseContainsJson(array('organization_id' =>$request->data->organization_id));
        $I->seeResponseContainsJson(array('person_id_proxy' =>$request->data->person_id_proxy));
        $I->seeResponseContainsJson(array('repeat_pattern' =>$request->data->series_info->repeat_pattern));
        $I->seeResponseContainsJson(array('repeat_range' =>$request->data->series_info->repeat_range));
        $I->seeResponseContainsJson(array('office_hours_id' =>$request->data->office_hours_id));
        $I->seeResponseContainsJson(array('location' =>$request->data->location));
        $I->seeResponseContainsJson(array('meeting_length' =>$request->data->series_info->meeting_length));
        $I->seeResponseContains('meeting_length');
        $I->seeResponseContains('repeat_pattern');
        $I->seeResponseContains('repeat_every');
        $I->seeResponseContains('repeat_days');
        $I->seeResponseContains('repeat_range');
        $I->seeResponseContains('meeting_length');
        $I->seeResponseContains('repeat_pattern');
        $I->seeResponseContains('repeat_occurence');
        $I->seeResponseContains('slot_start');
        $I->seeResponseContains('slot_end');
        $I->seeResponseContains('location');
        $I->seeResponseIsJson();
    }

    public function testCreateOfficeHoursSeriesWeekly(ApiTester $I)
    {
        $I->wantTo('Create Office Hours Series On Repeat Pattern Weekly by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 13 hour");
        $edate=new DateTime("+ 14 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"W"
                ]
            ]
        );
        $I->seeResponseCodeIs(201);
        $request=json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array('slot_type' =>$request->data->slot_type));
        $I->seeResponseContainsJson(array('person_id' =>$request->data->person_id));
        $I->seeResponseContainsJson(array('organization_id' =>$request->data->organization_id));
        $I->seeResponseContainsJson(array('person_id_proxy' =>$request->data->person_id_proxy));
        $I->seeResponseContainsJson(array('repeat_pattern' =>$request->data->series_info->repeat_pattern));
        $I->seeResponseContainsJson(array('repeat_range' =>$request->data->series_info->repeat_range));
        $I->seeResponseContainsJson(array('office_hours_id' =>$request->data->office_hours_id));
        $I->seeResponseContainsJson(array('location' =>$request->data->location));
        $I->seeResponseContainsJson(array('meeting_length' =>$request->data->series_info->meeting_length));
        $I->seeResponseContains('meeting_length');
        $I->seeResponseContains('repeat_pattern');
        $I->seeResponseContains('repeat_every');
        $I->seeResponseContains('repeat_days');
        $I->seeResponseContains('repeat_range');
        $I->seeResponseContains('meeting_length');
        $I->seeResponseContains('repeat_pattern');
        $I->seeResponseContains('repeat_occurence');
        $I->seeResponseContains('slot_start');
        $I->seeResponseContains('slot_end');
        $I->seeResponseContains('location');
        $I->seeResponseIsJson();
    }

    public function testCreateOfficeHoursSeriesMonthly(ApiTester $I)
    {
        $I->wantTo('Create Office Hours Series On Repeat Pattern Monthly by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 13 hour");
        $edate=new DateTime("+ 14 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"M"
                ]
            ]
        );
        $I->seeResponseCodeIs(201);
        $request=json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array('slot_type' =>$request->data->slot_type));
        $I->seeResponseContainsJson(array('person_id' =>$request->data->person_id));
        $I->seeResponseContainsJson(array('organization_id' =>$request->data->organization_id));
        $I->seeResponseContainsJson(array('person_id_proxy' =>$request->data->person_id_proxy));
        $I->seeResponseContainsJson(array('repeat_pattern' =>$request->data->series_info->repeat_pattern));
        $I->seeResponseContainsJson(array('repeat_range' =>$request->data->series_info->repeat_range));
        $I->seeResponseContainsJson(array('office_hours_id' =>$request->data->office_hours_id));
        $I->seeResponseContainsJson(array('location' =>$request->data->location));
        $I->seeResponseContainsJson(array('meeting_length' =>$request->data->series_info->meeting_length));
        $I->seeResponseContains('meeting_length');
        $I->seeResponseContains('repeat_pattern');
        $I->seeResponseContains('repeat_every');
        $I->seeResponseContains('repeat_days');
        $I->seeResponseContains('repeat_range');
        $I->seeResponseContains('meeting_length');
        $I->seeResponseContains('repeat_pattern');
        $I->seeResponseContains('repeat_occurence');
        $I->seeResponseContains('slot_start');
        $I->seeResponseContains('slot_end');
        $I->seeResponseContains('location');
        $I->seeResponseIsJson();
    }

    public function testCreateOfficeHoursSeriesNone(ApiTester $I)
    {
        $I->wantTo('Create Office Hours Series On Repeat Pattern None by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 12 hour");
        $edate=new DateTime("+ 13 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"N",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"N"
                ]
            ]
        );
        $I->seeResponseCodeIs(201);
        $request=json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array('slot_type' =>$request->data->slot_type));
        $I->seeResponseContainsJson(array('person_id' =>$request->data->person_id));
        $I->seeResponseContainsJson(array('organization_id' =>$request->data->organization_id));
        $I->seeResponseContainsJson(array('person_id_proxy' =>$request->data->person_id_proxy));
        $I->seeResponseContainsJson(array('repeat_pattern' =>$request->data->series_info->repeat_pattern));
        //$I->seeResponseContainsJson(array('repeat_range' =>$request->data->series_info->repeat_range));
        //$I->seeResponseContainsJson(array('office_hours_id' =>$request->data->office_hours_id));
        $I->seeResponseContainsJson(array('location' =>$request->data->location));
        $I->seeResponseContainsJson(array('meeting_length' =>$request->data->series_info->meeting_length));
        $I->seeResponseContains('meeting_length');
        $I->seeResponseContains('repeat_pattern');
        $I->seeResponseContains('repeat_every');
        $I->seeResponseContains('repeat_days');
        $I->seeResponseContains('repeat_range');
        $I->seeResponseContains('meeting_length');
        $I->seeResponseContains('repeat_pattern');
        $I->seeResponseContains('repeat_occurence');
        $I->seeResponseContains('slot_start');
        $I->seeResponseContains('slot_end');
        $I->seeResponseContains('location');
        $I->seeResponseIsJson();
    }

    public function testCreateOfficeHoursSeriesInvalidStartDate(ApiTester $I)
    {
        $I->wantTo('Create Office Hours Series with Invalid Start Date by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("- 1 day");
        $edate=new DateTime("+ 3 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"N"
                ]
            ]
        );
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateOfficeHoursSeriesInvalidEndDate(ApiTester $I)
    {
        $I->wantTo('Create Office Hours Series with Invalid End Date by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 3 hour");
        $edate=new DateTime("+ 1 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"N"
                ]
            ]
        );
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateOfficeHoursSeriesInvalidPerson(ApiTester $I)
    {
        $I->wantTo('Create Office Hours Series with Invalid Person by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 3 hour");
        $edate=new DateTime("+ 4 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->invalidPersonId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"N"
                ]
            ]
        );
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testEditOfficeHoursSeries(ApiTester $I)
    {
        $I->wantTo('Edit Office Hours Series by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 13 hour");
        $edate=new DateTime("+ 14 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"D"
                ]
            ]
        );
        $request=json_decode($I->grabResponse());
        $I->sendPUT('booking',
            [
                "office_hours_id"=>$request->data->office_hours_id,
                "appointment_id"=>1,
                "person_id"=> $request->data->person_id,
                "person_id_proxy"=>$request->data->person_id_proxy,
                "organization_id"=>$request->data->organization_id,
                "slot_type"=>$request->data->slot_type,
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"D"
                ]
            ]
        );
        $I->seeResponseCodeIs(200);
        $request=json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array('slot_type' =>$request->data->slot_type));
        $I->seeResponseContainsJson(array('person_id' =>$request->data->person_id));
        $I->seeResponseContainsJson(array('organization_id' =>$request->data->organization_id));
        $I->seeResponseContainsJson(array('person_id_proxy' =>$request->data->person_id_proxy));
        $I->seeResponseContainsJson(array('slot_start' =>$request->data->slot_start));
        $I->seeResponseContainsJson(array('slot_end' =>$request->data->slot_end));
        $I->seeResponseContainsJson(array('office_hours_id' =>$request->data->office_hours_id));
        $I->seeResponseContainsJson(array('location' =>$request->data->location));
        $I->seeResponseContainsJson(array('meeting_length' =>$request->data->series_info->meeting_length));
        $I->seeResponseContains('slot_start');
        $I->seeResponseContains('slot_end');
        $I->seeResponseContains('location');
        $I->seeResponseContains('slot_type');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('person_id');
        $I->seeResponseContains('person_id_proxy');
        $I->seeResponseIsJson();
    }

    public function testEditOfficeHoursSeriesInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Edit Office Hours Series With Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 13 hour");
        $edate=new DateTime("+ 14 hour");
        $I->sendPUT('booking',
            [
                "office_hours_id"=>1,
                "appointment_id"=>1,
                "person_id"=> 1,
                "person_id_proxy"=>1,
                "organization_id"=>1,
                "slot_type"=>'S',
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"N"
                ]
            ]
        );
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testEditOfficeHoursSeriesDaily(ApiTester $I)
    {
        $I->wantTo('Edit Office Hours Series On Repeat Pattern Daily by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 12 hour");
        $edate=new DateTime("+ 13 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"D",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"D"
                ]
            ]
        );
        $request=json_decode($I->grabResponse());
        $I->sendPUT('booking',
            [
                "office_hours_id"=>$request->data->office_hours_id,
                "appointment_id"=>1,
                "person_id"=> $request->data->person_id,
                "person_id_proxy"=>$request->data->person_id_proxy,
                "organization_id"=>$request->data->organization_id,
                "slot_type"=>$request->data->slot_type,
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>$request->data->series_info->repeat_pattern,
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"D"
                ]
            ]
        );
        $req= json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('slot_type' =>$req->data->slot_type));
        $I->seeResponseContainsJson(array('person_id' =>$req->data->person_id));
        $I->seeResponseContainsJson(array('organization_id' =>$req->data->organization_id));
        $I->seeResponseContainsJson(array('person_id_proxy' =>$req->data->person_id_proxy));
        $I->seeResponseContainsJson(array('slot_start' =>$req->data->slot_start));
        $I->seeResponseContainsJson(array('slot_end' =>$req->data->slot_end));
        $I->seeResponseContainsJson(array('repeat_pattern' =>$req->data->series_info->repeat_pattern));
        $I->seeResponseContainsJson(array('office_hours_id' =>$request->data->office_hours_id));
        $I->seeResponseContainsJson(array('location' =>$request->data->location));
        $I->seeResponseContainsJson(array('meeting_length' =>$request->data->series_info->meeting_length));

        $I->seeResponseContains('slot_start');
        $I->seeResponseContains('slot_end');
        $I->seeResponseContains('location');
        $I->seeResponseContains('slot_type');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('person_id');
        $I->seeResponseContains('person_id_proxy');
        $I->seeResponseIsJson();
    }

    public function testEditOfficeHoursSeriesWeekly(ApiTester $I)
    {
        $I->wantTo('Edit Office Hours Series On Repeat Pattern Weekly by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 12 hour");
        $edate=new DateTime("+ 13 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"W",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"W"
                ]
            ]
        );
        $request=json_decode($I->grabResponse());
        $I->sendPUT('booking',
            [
                "office_hours_id"=>$request->data->office_hours_id,
                "appointment_id"=>1,
                "person_id"=> $request->data->person_id,
                "person_id_proxy"=>$request->data->person_id_proxy,
                "organization_id"=>$request->data->organization_id,
                "slot_type"=>$request->data->slot_type,
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>$request->data->series_info->repeat_pattern,
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"W"
                ]
            ]
        );
        $req= json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('slot_type' =>$req->data->slot_type));
        $I->seeResponseContainsJson(array('person_id' =>$req->data->person_id));
        $I->seeResponseContainsJson(array('organization_id' =>$req->data->organization_id));
        $I->seeResponseContainsJson(array('person_id_proxy' =>$req->data->person_id_proxy));
        $I->seeResponseContainsJson(array('slot_start' =>$req->data->slot_start));
        $I->seeResponseContainsJson(array('slot_end' =>$req->data->slot_end));
        $I->seeResponseContainsJson(array('repeat_pattern' =>$req->data->series_info->repeat_pattern));
        $I->seeResponseContainsJson(array('office_hours_id' =>$request->data->office_hours_id));
        $I->seeResponseContainsJson(array('location' =>$request->data->location));
        $I->seeResponseContainsJson(array('meeting_length' =>$request->data->series_info->meeting_length));
        $I->seeResponseContains('slot_start');
        $I->seeResponseContains('slot_end');
        $I->seeResponseContains('location');
        $I->seeResponseContains('slot_type');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('person_id');
        $I->seeResponseContains('person_id_proxy');
        $I->seeResponseIsJson();
    }

   public function testEditOfficeHoursSeriesMonthly(ApiTester $I)
    {
        $I->wantTo('Edit Office Hours Series On Repeat Pattern Monthly by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 12 hour");
        $edate=new DateTime("+ 13 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"M"
                ]
            ]
        );
        $request=json_decode($I->grabResponse());
        $I->sendPUT('booking',
            [
                "office_hours_id"=>$request->data->office_hours_id,
                "appointment_id"=>1,
                "person_id"=> $request->data->person_id,
                "person_id_proxy"=>$request->data->person_id_proxy,
                "organization_id"=>$request->data->organization_id,
                "slot_type"=>$request->data->slot_type,
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>$request->data->series_info->repeat_pattern,
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"M"
                ]
            ]
        );
        $req= json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('slot_type' =>$req->data->slot_type));
        $I->seeResponseContainsJson(array('person_id' =>$req->data->person_id));
        $I->seeResponseContainsJson(array('organization_id' =>$req->data->organization_id));
        $I->seeResponseContainsJson(array('person_id_proxy' =>$req->data->person_id_proxy));
        $I->seeResponseContainsJson(array('slot_start' =>$req->data->slot_start));
        $I->seeResponseContainsJson(array('slot_end' =>$req->data->slot_end));
        $I->seeResponseContainsJson(array('repeat_pattern' =>$req->data->series_info->repeat_pattern));
        $I->seeResponseContainsJson(array('office_hours_id' =>$request->data->office_hours_id));
        $I->seeResponseContainsJson(array('location' =>$request->data->location));
        $I->seeResponseContainsJson(array('meeting_length' =>$request->data->series_info->meeting_length));
        $I->seeResponseContains('slot_start');
        $I->seeResponseContains('slot_end');
        $I->seeResponseContains('location');
        $I->seeResponseContains('slot_type');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('person_id');
        $I->seeResponseContains('person_id_proxy');
        $I->seeResponseIsJson();
    }

     public function testEditOfficeHoursSeriesNone(ApiTester $I)
      {
        $I->wantTo('Edit Office Hours Series On Repeat Pattern None by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 12 hour");
        $edate=new DateTime("+ 13 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"M"
                ]
            ]
        );
        $request=json_decode($I->grabResponse());
        $I->sendPUT('booking',
            [
                "office_hours_id"=>$request->data->office_hours_id,
                "appointment_id"=>1,
                "person_id"=> $request->data->person_id,
                "person_id_proxy"=>$request->data->person_id_proxy,
                "organization_id"=>$request->data->organization_id,
                "slot_type"=>$request->data->slot_type,
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>$request->data->series_info->repeat_pattern,
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"N"
                ]
            ]
        );
        $req= json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('slot_type' =>$req->data->slot_type));
        $I->seeResponseContainsJson(array('person_id' =>$req->data->person_id));
        $I->seeResponseContainsJson(array('organization_id' =>$req->data->organization_id));
        $I->seeResponseContainsJson(array('person_id_proxy' =>$req->data->person_id_proxy));
        $I->seeResponseContainsJson(array('slot_start' =>$req->data->slot_start));
        $I->seeResponseContainsJson(array('slot_end' =>$req->data->slot_end));
        $I->seeResponseContainsJson(array('repeat_pattern' =>$req->data->series_info->repeat_pattern));
        $I->seeResponseContainsJson(array('office_hours_id' =>$request->data->office_hours_id));
        $I->seeResponseContainsJson(array('location' =>$request->data->location));
        $I->seeResponseContainsJson(array('meeting_length' =>$request->data->series_info->meeting_length));
        $I->seeResponseContains('slot_start');
        $I->seeResponseContains('slot_end');
        $I->seeResponseContains('location');
        $I->seeResponseContains('slot_type');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('person_id');
        $I->seeResponseContains('person_id_proxy');
        $I->seeResponseIsJson();
    }

    public function testEditOfficeHoursSeriesWithInvalidOfficeHour(ApiTester $I)
    {
        $I->wantTo('Edit Office Hours Series With Invalid Office Hour by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 12 hour");
        $edate=new DateTime("+ 13 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"N",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"D"
                ]
            ]
        );
        $request=json_decode($I->grabResponse());
        $I->sendPUT('booking',
            [
                "office_hours_id"=>-1,
                "appointment_id"=>1,
                "person_id"=> $request->data->person_id,
                "person_id_proxy"=>$request->data->person_id_proxy,
                "organization_id"=>$request->data->organization_id,
                "slot_type"=>$request->data->slot_type,
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>$request->data->series_info->repeat_pattern,
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"D"
                ]
            ]
        );
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateOfficeHoursSeriesWithEmptyStartDate(ApiTester $I)
    {
        $I->wantTo('Create Office Hours Series With Empty Start Date by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 12 hour");
        $edate=new DateTime("+ 13 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>null,
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"D"
                ]
            ]
        );
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    public function testEditOfficeHoursSeriesWithEmptyStartDate(ApiTester $I)
    {
        $I->wantTo('Edit Office Hours Series With Empty Start Date by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate=new DateTime("+ 13 hour");
        $edate=new DateTime("+ 14 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"D"
                ]
            ]
        );
        $request=json_decode($I->grabResponse());
        $I->sendPUT('booking',
            [
                "office_hours_id"=>$request->data->office_hours_id,
                "appointment_id"=>1,
                "person_id"=> $request->data->person_id,
                "person_id_proxy"=>$request->data->person_id_proxy,
                "organization_id"=>$request->data->organization_id,
                "slot_type"=>$request->data->slot_type,
                "slot_start"=>null,
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"M",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"D"
                ]
            ]
        );
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
	 
	public function testCreateOfficeHour(ApiTester $I)
    {
        $I->wantTo('Create a OfficeHour by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sHrs = rand(100,200);
        $eHrs = $sHrs+1;
		$sdate=new DateTime("+ $sHrs hour");
        $edate=new DateTime("+ $eHrs hour");
        $I->sendPOST('booking', [
            "person_id" => $this->personId,
            "person_id_proxy" => $this->personIdProxy,
            "organization_id" => $this->organizationId,
            "slot_type" => "I",    
			"slot_start" => $sdate->format('Y-m-d\TH:i:sO'),	
			"slot_end" => $edate->format('Y-m-d\TH:i:sO'),
			"location" => "Porto Real",	

            "series_info" => [
                "meeting_length"=> 60,
                "repeat_pattern"=> "D",
                "repeat_every"=> 1,
                "repeat_days"=> "",
                "repeat_occurence"=> "0",
                "repeat_range"=> "N",
                "repeat_monthly_on"=> 0
            ],
            "meeting_length"=> 60
            
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
	
	public function testCreateOfficeHoursInvalidSlotStart(ApiTester $I)
    {
        $I->wantTo('Create a Invalid Slot Start OfficeHour by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
		$sdate=new DateTime("+ 13 hour");
        $edate=new DateTime("+ 12 hour");
        $I->sendPOST('booking', [
            "person_id" => $this->personId,
            "person_id_proxy" => $this->personIdProxy,
            "organization_id" => $this->organizationId,
            "slot_type" => "I",    
			"slot_start" => $sdate->format('Y-m-d\TH:i:sO'),	
			"slot_end" => $edate->format('Y-m-d\TH:i:sO'),
			"location" => "Porto Real",
            "series_info" => [
            "meeting_length"=> 60,
            "repeat_pattern"=> "D",
            "repeat_every"=> 1,
            "repeat_days"=> "",
            "repeat_occurence"=> "0",
            "repeat_range"=> "N",
            "repeat_monthly_on"=> 0
            ],
            "meeting_length"=> 60
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
	public function testCreateOfficeHoursInvalidPerson(ApiTester $I)
    {
        $I->wantTo('Create a Invalid Person OfficeHour by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
		$sdate=new DateTime("+ 3 hour");
        $edate=new DateTime("+ 2 hour");
        $I->sendPOST('booking', [
            "person_id" => $this->invalidPersonId,
            "person_id_proxy" => $this->personIdProxy,
            "organization_id" => $this->organizationId,
            "slot_type" => "I",    
			"slot_start" => $sdate->format('Y-m-d\TH:i:sO'),	
			"slot_end" => $edate->format('Y-m-d\TH:i:sO'),
			"location" => "Porto Real",
            "series_info" => [
            "meeting_length"=> 60,
            "repeat_pattern"=> "D",
            "repeat_every"=> 1,
            "repeat_days"=> "",
            "repeat_occurence"=> "0",
            "repeat_range"=> "N",
            "repeat_monthly_on"=> 0
            ],
            "meeting_length"=> 60
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
	
	public function testCreateOfficeHoursInvalidPersonProxy(ApiTester $I)
    {
        $I->wantTo('Create a Invalid Person Proxy OfficeHour by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
		$sdate=new DateTime("+ 13 hour");
        $edate=new DateTime("+ 12 hour");
        $I->sendPOST('booking', [
            "person_id" => $this->personId,
            "person_id_proxy" => $this->invalidPersonId,
            "organization_id" => $this->organizationId,
            "slot_type" => "I",    
			"slot_start" => $sdate->format('Y-m-d\TH:i:sO'),	
			"slot_end" => $edate->format('Y-m-d\TH:i:sO'),
			"location" => "Porto Real",
            "series_info" => [
            "meeting_length"=> 60,
            "repeat_pattern"=> "D",
            "repeat_every"=> 1,
            "repeat_days"=> "",
            "repeat_occurence"=> "0",
            "repeat_range"=> "N",
            "repeat_monthly_on"=> 0
            ],
            "meeting_length"=> 60
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
	
	public function testEditOfficeHour(ApiTester $I)
    {
        $I->wantTo('Edit OfficeHour by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $sHrs = rand(21,30);
        $eHrs = $sHrs+1;
        $sdate=new DateTime("+ $sHrs hour");
        $edate=new DateTime("+ $eHrs hour");
		$I->sendPOST('booking', [
            "person_id" => $this->personId,
            "person_id_proxy" => $this->personIdProxy,
            "organization_id" => $this->organizationId,
            "slot_type" => "I",    
			"slot_start" => $sdate->format('Y-m-d\TH:i:sO'),	
			"slot_end" => $edate->format('Y-m-d\TH:i:sO'),	
			"location" => "mylocation",
		    "series_info" => [
		    "meeting_length"=> 60,
		    "repeat_pattern"=> "D",
		    "repeat_every"=> 1,
		    "repeat_days"=> "",
		    "repeat_occurence"=> "0",
		    "repeat_range"=> "N",
		    "repeat_monthly_on"=> 0
		    ],
		    "meeting_length"=> 60
					
        ]);
		$officehour = json_decode($I->grabResponse());
        $I->sendPUT('booking', [
        	"office_hours_id" => $officehour->data->office_hours_id,
            "person_id" => $this->personId,
            "person_id_proxy" => $this->personIdProxy,
            "organization_id" => $this->organizationId,
            "slot_type" => "I",    
			"slot_start" => $sdate->format('Y-m-d\TH:i:sO'),	
			"slot_end" => $edate->format('Y-m-d\TH:i:sO'),	
			"location" => "mylocation",
			"is_cancelled" => false,
			"appointment_id" => 1
        ]);
        $office = json_decode($I->grabResponse());		
        $I->seeResponseContainsJson(array('office_hours_id' => $office->data->office_hours_id));
        $I->seeResponseContains('person_id');
        $I->seeResponseContains('person_id_proxy');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('slot_type');
		$I->seeResponseContains('slot_start');
		$I->seeResponseContains('slot_end');
		$I->seeResponseContains('location');
		$I->seeResponseContains('appointment_id');
		$I->seeResponseContains('is_cancelled');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();        
    }
	
	public function testGetOfficeHour(ApiTester $I)
	{
		$I->wantTo('Get an OfficeHour by API');
		$I->haveHttpHeader('Content-Type','application/json');
		$I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept','application/json');
		$sHrs = rand(31,40);
		$eHrs = $sHrs+1;
		$sdate=new DateTime("+ $sHrs hour");
		$edate=new DateTime("+ $eHrs hour");
		$I->sendPOST('booking', [
            "person_id" => $this->personId,
           "person_id_proxy" => $this->personIdProxy,
            "organization_id" => $this->organizationId,
            "slot_type" => "I",    
			"slot_start" => $sdate->format('Y-m-d\TH:i:sO'),	
			"slot_end" => $edate->format('Y-m-d\TH:i:sO'),	
			"location" => "mylocation",
		    "series_info" => [
		    "meeting_length"=> 60,
		    "repeat_pattern"=> "D",
		    "repeat_every"=> 1,
		    "repeat_days"=> "",
		    "repeat_occurence"=> "0",
		    "repeat_range"=> "N",
		    "repeat_monthly_on"=> 0
		    ],
		    "meeting_length"=> 60
					
        ]);
		$officehour = json_decode($I->grabResponse());
		
		$I->sendGET('booking?type=I'.'&id='.$officehour->data->office_hours_id);
		 $I->seeResponseContainsJson(array('office_hours_id' => $officehour->data->office_hours_id));
		$I->seeResponseContains('person_id');
        $I->seeResponseContains('person_id_proxy');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('slot_type');
		$I->seeResponseContains('slot_start');
		$I->seeResponseContains('slot_end');
		$I->seeResponseContains('location');		
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	
	}  
	
	public function testGetOfficeHourInvalidOfficehour(ApiTester $I)
	{
		$I->wantTo('Get an Invalid OfficeHour by API');
		$I->haveHttpHeader('Content-Type','application/json');
		$I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept','application/json');		
		$I->sendGET('booking?type=I'.'&id='.$this->invalidOfficehour);
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}
	
	public function testCancelOfficeHourProxyPerson(ApiTester $I)
    {
        $I->wantTo('Cancel Office hour by Proxy Person API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $sHrs = rand(41,50);
        $eHrs = $sHrs+1;
        $sdate=new DateTime("+ $sHrs hour");
        $edate=new DateTime("+ $eHrs hour");
		$I->sendPOST('booking', [
            "person_id" => $this->personId,
            "person_id_proxy" => $this->personIdProxy,
            "organization_id" => $this->organizationId,
            "slot_type" => "I",    
			"slot_start" => $sdate->format('Y-m-d\TH:i:sO'),	
			"slot_end" => $edate->format('Y-m-d\TH:i:sO'),	
			"location" => "mylocation",
		    "series_info" => [
		    "meeting_length"=> 60,
		    "repeat_pattern"=> "D",
		    "repeat_every"=> 1,
		    "repeat_days"=> "",
		    "repeat_occurence"=> "0",
		    "repeat_range"=> "N",
		    "repeat_monthly_on"=> 0
		    ],
		    "meeting_length"=> 60	
        ]);
		$officehour = json_decode($I->grabResponse());		
		$flag=1;
        $I->sendDELETE('booking?person='.$this->personId.'&isproxy='.$flag.'&id='.$officehour->data->office_hours_id);	
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }	
	

	public function testCancelOfficeHourPersonNotProxy(ApiTester $I)
    {
        $I->wantTo('Cancel Office hour Person Not Proxy by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $sHrs = rand(51,60);
        $eHrs = $sHrs+1;
        $sdate=new DateTime("+ $sHrs hour");
        $edate=new DateTime("+ $eHrs hour");
		$I->sendPOST('booking', [
            "person_id" => $this->personId,
            "person_id_proxy" => $this->personIdProxy,
            "organization_id" => $this->organizationId,
            "slot_type" => "I",    
			"slot_start" => $sdate->format('Y-m-d\TH:i:sO'),	
			"slot_end" => $edate->format('Y-m-d\TH:i:sO'),	
			"location" => "mylocation",
		    "series_info" => [
		    "meeting_length"=> 60,
		    "repeat_pattern"=> "D",
		    "repeat_every"=> 1,
		    "repeat_days"=> "",
		    "repeat_occurence"=> "0",
		    "repeat_range"=> "N",
		    "repeat_monthly_on"=> 0
		    ],
		    "meeting_length"=> 60	
        ]);
		$officehour = json_decode($I->grabResponse());		
		$flag=0;
        $I->sendDELETE('booking?person='.$this->personId.'&isproxy='.$flag.'&id='.$officehour->data->office_hours_id);	
        $I->seeResponseCodeIs(204);		
        $I->seeResponseIsJson();
    }

    public function testGetOfficeHourSeries(ApiTester $I)
    {
        $I->wantTo('Get an Office Hour Series by API');
        $I->haveHttpHeader('Content-Type','application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept','application/json');
        $sdate=new DateTime("+ 13 hour");
        $edate=new DateTime("+ 14 hour");
        $I->sendPOST('booking',
            [
                "person_id"=> $this->personId,
                "person_id_proxy"=>$this->personIdProxy,
                "organization_id"=>$this->organizationId,
                "slot_type"=>"S",
                "slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
                "slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
                "location"=>"Porto Real",
                "series_info"=>[
                    "meeting_length"=>60,
                    "repeat_pattern"=>"D",
                    "repeat_monthly_on"=>1,
                    "repeat_every"=>2,
                    "repeat_days"=>"0100001",
                    "repeat_occurence"=>NULL,
                    "repeat_range"=>"D"
                ]
            ]
        );
        $officehour = json_decode($I->grabResponse());
        $I->sendGET('appointments/'.$officehour->data->organization_id.'/'.$officehour->data->person_id);
        $res = json_decode($I->grabResponse());
        $I->sendGET('booking?type=S'.'&id='.$res->data->calendar_time_slots[0]->office_hours_id);
        $request = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array('slot_type' =>$request->data->slot_type));
        $I->seeResponseContainsJson(array('person_id' =>$request->data->person_id));
        $I->seeResponseContainsJson(array('organization_id' =>$request->data->organization_id));
        $I->seeResponseContainsJson(array('person_id_proxy' =>$request->data->person_id_proxy));
        $I->seeResponseContainsJson(array('slot_start' =>$request->data->slot_start));
        $I->seeResponseContainsJson(array('slot_end' =>$request->data->slot_end));
        $I->seeResponseContainsJson(array('office_hours_id' =>$request->data->office_hours_id));
        $I->seeResponseContainsJson(array('meeting_length' =>$request->data->meeting_length));
        $I->seeResponseContainsJson(array('repeat_pattern' =>$request->data->series_info->repeat_pattern));
        $I->seeResponseContainsJson(array('repeat_range' =>$request->data->series_info->repeat_range));
        $I->seeResponseContainsJson(array('location' =>$request->data->location));
        $I->seeResponseContains('person_id');
        $I->seeResponseContains('person_id_proxy');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('slot_type');
        $I->seeResponseContains('slot_start');
        $I->seeResponseContains('slot_end');
        $I->seeResponseContains('location');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetOfficeHourSeriesWithInvalidOfficeHour(ApiTester $I)
    {
        $I->wantTo('Get an Office Hour Series With Invalid Office Hour by API');
        $I->haveHttpHeader('Content-Type','application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept','application/json');
        $I->sendGET('booking?type=S'.'&id='.$this->invalidOfficehour);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testDeleteOfficeHourSeries(ApiTester $I)
    {
    	$I->wantTo('Delete an Office Hour Series by API');
    	$I->haveHttpHeader('Content-Type','application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept','application/json');
    	$sHrs = rand(61,70);
		$eHrs = $sHrs+1;
		$sdate=new DateTime("+ $sHrs hour");
		$edate=new DateTime("+ $eHrs hour");
    	$I->sendPOST('booking',
    			[
    			"person_id"=> $this->personId,
    			"person_id_proxy"=>$this->personIdProxy,
    			"organization_id"=>$this->organizationId,
    			"slot_type"=>"S",
    			"slot_start"=>$sdate->format('Y-m-d\TH:i:sO'),
    			"slot_end"=>$edate->format('Y-m-d\TH:i:sO'),
    			"location"=>"Porto Real",
    			"series_info"=>[
    			"meeting_length"=>60,
    			"repeat_pattern"=>"D",
    			"repeat_monthly_on"=>1,
    			"repeat_every"=>2,
    			"repeat_days"=>"0100001",
    			"repeat_occurence"=>NULL,
    			"repeat_range"=>"D"
    			]
    			]
    	);
    	$officehour = json_decode($I->grabResponse());
    	
    	$I->sendDELETE('booking/series/'.$officehour->data->office_hours_id);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }
}