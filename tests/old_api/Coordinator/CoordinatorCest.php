<?php
require_once 'tests/api/SynapseTestHelper.php';

class CoordinatorCest extends SynapseTestHelper
{

    private $token;

    private $organization = 1;

    private $invalidOrg = - 1;

    private $langId = 1;

    private $invalidLangId = - 1;

    private $groupId = 1;

    private $invalidGroupId = - 1;

    private $personId = 2;

    private $invalidPersonId = - 1;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testCreateCoordinator(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Create a Coordinator of an Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('coordinators', [
            "firstname" => "Steven",
            "lastname" => "Jean",
            "phone" => "6-(767)284-3308",
            "ismobile" => true,
            "title" => "mr",
            "roleid" => 2,
            "email" => "jsullivan@devcast.edu",
            "organizationid" => $this->organization
        ]);
        $I->seeResponseContainsJson(array(
            'organizationid' => $this->organization
        ));
        $I->seeResponseContainsJson(array(
            'firstname' => "Steven"
        ));
        $I->seeResponseContainsJson(array(
            'lastname' => "Jean"
        ));
        $I->seeResponseContainsJson(array(
            'title' => "mr"
        ));
        $I->seeResponseContainsJson(array(
            'roleid' => 2
        ));
        $I->seeResponseContainsJson(array(
            'email' => "jsullivan@devcast.edu"
        ));
        $I->seeResponseContains('id');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testCreateCoordinatorInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Create a Coordinator of an Organization with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('coordinators', [
            "firstname" => "Steven",
            "lastname" => "Jean",
            "phone" => "6-(767)284-3308",
            "ismobile" => true,
            "title" => "mr",
            "roleid" => 2,
            "email" => "jsullivan@devcast.edu",
            "organizationid" => $this->organization
        ]);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
	/* Need to be Fixed
    public function testUpdateCoordinator(ApiTester $I)
    {
        $I->wantTo('Update Coordinator of an Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('coordinators', [
            "firstname" => "Steven",
            "lastname" => "Jean",
            "phone" => "6-(767)284-3308",
            "ismobile" => true,
            "title" => "mr",
            "roleid" => 2,
            "email" => "jsullivan1@devcast.edu",
            "organizationid" => $this->organization
        ]);
        $coordinators = json_decode($I->grabResponse());
        $I->sendPUT('coordinators', [
            "id" => $coordinators->data->id,
            "firstname" => "Steven 123",
            "lastname" => "Jean",
            "title" => "mr",
            "roleid" => 2,
            "email" => "jsullivan1@subash.edu",
            "ismobile" => 1,
            "phone" => "6-(767)284-3308",
            "organizationid" => $coordinators->data->organizationid
        ]);
        $I->seeResponseContainsJson(array(
            'organizationid' => $coordinators->data->organizationid
        ));
        $I->seeResponseContainsJson(array(
            'firstname' => "Steven 123"
        ));
        $I->seeResponseContainsJson(array(
            'lastname' => "Jean"
        ));
        $I->seeResponseContainsJson(array(
            'title' => "mr"
        ));
        $I->seeResponseContainsJson(array(
            'roleid' => 2
        ));
        $I->seeResponseContainsJson(array(
            'email' => "jsullivan1@subash.edu"
        ));
        $I->seeResponseContainsJson(array(
            'id' => $coordinators->data->id
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	*/

    public function testUpdateCoordinatorInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Update coordinator of an Organization with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPUT('coordinators', [
            "id" => $this->personId,
            "firstname" => "Steven 123",
            "lastname" => "Jean",
            "title" => "mr",
            "roleid" => 2,
            "email" => "jsullivan@subash.edu",
            "ismobile" => 1,
            "phone" => "6-(767)284-3308",
            "organizationid" => $this->organization
        ]);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testUpdateCoordinatorInvalid(ApiTester $I)
    {
        $I->wantTo('Update coordinator of an Organization with invalid person by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPUT('coordinators', [
            "id" => $this->invalidPersonId,
            "firstname" => "Steven 123",
            "lastname" => "Jean",
            "title" => "mr",
            "roleid" => 2,
            "email" => "jsullivan2@subash.edu",
            "ismobile" => 1,
            "phone" => "6-(767)284-3308",
            "organizationid" => $this->organization
        ]);
        $I->seeResponseContains('Person Not Found');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
	/* Need to be Fixed
    public function testGetCoordinatorById(ApiTester $I)
    {
        $I->wantTo('Get Coordinator By Person Id of an Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('coordinators', [
            "firstname" => "Steven",
            "lastname" => "Jean",
            "phone" => "6-(767)284-3308",
            "ismobile" => true,
            "title" => "mr",
            "roleid" => 2,
            "email" => "jsullivan3@devcast.edu",
            "organizationid" => $this->organization
        ]);
        $coordinators = json_decode($I->grabResponse());
        $I->sendGET('coordinators/' . $this->organization . '/' . $coordinators->data->id);
        $I->seeResponseContainsJson(array(
            'id' => $coordinators->data->id
        ));
        $I->seeResponseContainsJson(array(
            'firstname' => $coordinators->data->firstname
        ));
        $I->seeResponseContainsJson(array(
            'lastname' => $coordinators->data->lastname
        ));
        $I->seeResponseContainsJson(array(
            'title' => $coordinators->data->title
        ));
        $I->seeResponseContainsJson(array(
            'phone' => $coordinators->data->phone
        ));
        $I->seeResponseContainsJson(array(
            'roleid' => $coordinators->data->roleid
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	*/

    public function testGetCoordinatorByIdInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Coordinator By Person Id of an Organization with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('coordinators/' . $this->organization . '/' . $this->personId);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    
    public function testGetAllCoordinator(ApiTester $I)
    {
        $I->wantTo('Get All Coordinators of an Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('coordinators/' . $this->organization);        
        $I->seeResponseContains('coordinators');
        $I->seeResponseContains('id');
        $I->seeResponseContains('firstname');
        $I->seeResponseContains('lastname');
        $I->seeResponseContains('title');
        $I->seeResponseContains('email');
        $I->seeResponseContains('phone');        
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetAllCoordinatorInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get All Coordinators of an Organization with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('coordinators/' . $this->organization);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
	/* Need to be Fixed
    public function testDeleteCoordinator(ApiTester $I)
    {
        $I->wantTo('Delete Coordinator of an Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('coordinators', [
            "firstname" => "Steven",
            "lastname" => "Jean",
            "phone" => "6-(767)284-3308",
            "ismobile" => true,
            "title" => "mr",
            "roleid" => 2,
            "email" => "jsullivan4@devcast.edu",
            "organizationid" => $this->organization
        ]);
        $coordinators = json_decode($I->grabResponse());
        $I->sendDELETE('coordinators/' . $coordinators->data->organizationid . '/' . $coordinators->data->id);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	*/

    public function testDeleteCoordinatorInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Delete Coordinator of an Organization with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('coordinators/' . $this->organization . '/' . $this->personId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testDeleteCoordinatorInvalid(ApiTester $I)
    {
        $I->wantTo('Delete Invalid Coordinator of an Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendDELETE('coordinators/' . $this->organization . '/' . $this->invalidPersonId);
        $I->seeResponseContains("Person Not Found");
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testSendLinkCoordinator(ApiTester $I, $scenario)
    {
//        $scenario->skip("Errored");
        $I->wantTo('Send Link to a Coordinator by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('coordinators', [
            "firstname" => "Steven",
            "lastname" => "Jean",
            "phone" => "6-(767)284-3308",
            "ismobile" => true,
            "title" => "mr",
            "roleid" => 2,
            "email" => "jsullivan5@devcast.edu",
            "organizationid" => $this->organization
        ]);
        $coordinators = json_decode($I->grabResponse());
        $I->sendGET('coordinators/' . $coordinators->data->organizationid . '/' . $coordinators->data->id . '/sendlink');        
        $I->seeResponseContains('email_sent_status'); 
		/*
		* Token has been removed from the api response as security concern. 
		* Hence token key cannot be check here.
		*/
        //$I->seeResponseContains('token');
        $I->seeResponseContains('welcome_email_sentDate');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testSendLinkCoordinatorInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Send Link to a Coordinator with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('coordinators/' . $this->organization . '/' . $this->personId . '/sendlink');
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testSendLinkCoordinatorInvalid(ApiTester $I)
    {
        $I->wantTo('Send Link to a Invalid Coordinator by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('coordinators/' . $this->organization . '/' . $this->invalidPersonId . '/sendlink');
        $I->seeResponseContains('No Coordinator Found');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetRoles(ApiTester $I)
    {
        $I->wantTo('Search Group from an Organization Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('coordinators/roles/language/' . $this->langId);
        $I->seeResponseContainsJson(array(
            'roleid' => 3,
            'coordinator_type' => "Primary coordinator"
        ));
        $I->seeResponseContainsJson(array(
            'roleid' => 4,
            'coordinator_type' => "Technical coordinator"
        ));
        $I->seeResponseContainsJson(array(
            'roleid' => 5,
            'coordinator_type' => "Non Technical coordinator"
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetRolesInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Roles from an Organization Details with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('coordinators/roles/language/' . $this->langId);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetRolesInvalid(ApiTester $I)
    {
        $I->wantTo('Search Group from an Organization Details with invalid language by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('coordinators/roles/language/' . $this->invalidLangId);
        $I->seeResponseContains('Language not found');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}
