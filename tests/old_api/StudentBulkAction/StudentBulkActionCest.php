<?php
require_once 'tests/api/SynapseTestHelper.php';

class StudentBulkActionCest extends SynapseTestHelper
{

    private $organizationId = 1;

    private $studentArray = [
        [
            "student_id" => 6,
            "student_firstname" => "firstname",
            "student_lastname" => "lastname"
        ],
        [
            "student_id" => 8,
            "student_firstname" => "firstname",
            "student_lastname" => "lastname"
        ]
    ];

    private $invalidStudent = - 1;

    private $invalidOrgId = - 100;

    private $invalidActivity = 'ABC';

    private $actvityNotes = "N";

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testBulkActionNotes(ApiTester $I)
    {
        $I->wantTo('Create bulk action on Notes by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $actvity = $this->actvityNotes;
        $I->sendPOST("bulkactions/permissions?type=$actvity", [
            "organization_id" => $this->organizationId,
            "type" => $actvity,
            "students" => $this->studentArray
        ]);
        $resp = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testBulkActionAppointment(ApiTester $I)
    {
        $I->wantTo('Create bulk action on Appointment by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST('bulkactions/permissions?type=A', [
            "organization_id" => $this->organizationId,
            "type" => "A",
            "students" => $this->studentArray
        ]);
        $resp = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testBulkActionReferral(ApiTester $I)
    {
        $I->wantTo('Create bulk action on Referral by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST('bulkactions/permissions?type=R', [
            "organization_id" => $this->organizationId,
            "type" => "R",
            "students" => $this->studentArray
        ]);
        $resp = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testBulkActionContact(ApiTester $I)
    {
        $I->wantTo('Create bulk action on Contact by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST('bulkactions/permissions?type=C', [
            "organization_id" => $this->organizationId,
            "type" => "C",
            "students" => $this->studentArray
        ]);
        $resp = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testBulkActionEmail(ApiTester $I)
    {
        $I->wantTo('Create bulk action on Email by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST('bulkactions/permissions?type=E', [
            "organization_id" => $this->organizationId,
            "type" => "E",
            "students" => $this->studentArray
        ]);
        $resp = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testBulkActionInvalidActivity(ApiTester $I)
    {
        $I->wantTo('Create Invalid Bulk Action by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $activity = $this->invalidActivity;
        $I->sendPOST("bulkactions/permissions?type=$activity", [
            "organization_id" => $this->organizationId,
            "type" => "$activity",
            "students" => $this->studentArray
        ]);
        $resp = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(400);
    }

    public function testBulkActionInvalidOrg(ApiTester $I)
    {
        $I->wantTo('Create Invalid Bulk Action by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $invalidOrgId = $this->invalidOrgId;
        $actvity = $this->actvityNotes;
        $I->sendPOST("bulkactions/permissions?type=$actvity", [
            "organization_id" => $invalidOrgId,
            "type" => $actvity,
            "students" => $this->studentArray
        ]);
        $resp = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(400);
    }

    public function testBulkActionInvalidStudent(ApiTester $I)
    {
        $I->wantTo('Create Invalid Bulk Action by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $actvity = $this->actvityNotes;
        $I->sendPOST("bulkactions/permissions?type=$actvity", [
            "organization_id" => $this->organizationId,
            "type" => $actvity,
            "students" => $this->invalidStudent
        ]);
        $resp = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(400);
    }
}