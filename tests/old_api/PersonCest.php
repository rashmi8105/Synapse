<?php
require_once 'SynapseTestHelper.php';
class PersonCest extends SynapseTestHelper
{
	private $token;
	
	private $organization = 1;
	private $invalidOrg = -200;
	private $personId = 1;
	private $invalidPersonId = -1;
	
	private $email = "bipinbihari.pradhan@techmahindra.com";
	
	public function _before(ApiTester $I)
	{
		$this->token = $this->authenticate($I);
	}
	
	public function testCreatePerson(ApiTester $I)
	{
		$I->wantTo('Create a Person by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('person',[	
    		"first_name"=> "John",
    		"last_name"=> "Ronald",
    		"primary_email" => "John@gmail.com",
            "organization" => $this->organization,
            "username" => "john@gmail.com"
			]);
		$I->seeResponseContainsJson(array('person' => array('firstname' => "John")));
		$I->seeResponseContainsJson(array('person' => array('lastname' => "Ronald")));
		$I->seeResponseContainsJson(array('person' => array('contacts' => (array('primary_email' => "John@gmail.com")))));
		$I->seeResponseContains('activation_token');
		$I->seeResponseContains('id');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	}
	
	
	public function testCreatePersonInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Create a Person with Invalid Authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('person',[
				"first_name"=> "John",
				"last_name"=> "Ronald",
				"primary_email" => "John@gmail.com",
				"organization" => $this->organization,
				"username" => "john@gmail.com"
				]);
		
		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
	}
	
	
	public function testPersonInvalidOrg(ApiTester $I)
	{
		$I->wantTo('Create a Person with invalid organization SSby API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('person',[
				"first_name"=> "John",
				"last_name"=> "Ronald",
				"primary_email" => "John@gmail.com",
				"organization" => $this->invalidOrg,
				"username" => "john@gmail.com"
				]);
		$I->seeResponseContains('Organization Not Found');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}
	
	public function testIsEmailExists(ApiTester $I)
	{
		$I->wantTo('Get Is Email Exists by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('person',[
				"first_name"=> "John",
				"last_name"=> "Ronald",
				"primary_email" => "John@gmail.com",
				"organization" => $this->organization,
				"username" => "john@gmail.com"
				]);
		$isEmail = json_decode($I->grabResponse());
		$I->sendGET('person/emailexists/'.$isEmail->data->person->contacts[0]->primary_email);
		$I->seeResponseContains("true");
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	}
	
	
	public function testIsEmailExistsInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get Is Email Exists with Invalid Authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('person',[
				"first_name"=> "John",
				"last_name"=> "Ronald",
				"primary_email" => "John@gmail.com",
				"organization" => $this->organization,
				"username" => "john@gmail.com"
				]);
		$isEmail = json_decode($I->grabResponse());
		$I->sendGET('person/emailexists/'.$this->email);
	
		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
	}
	
	
	public function testIsEmailExistsInvalid(ApiTester $I)
	{
		$I->wantTo('Get Is Email Exists by invalid email API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('person/emailexists/invalid@mail.com');
		$I->seeResponseContains("false");
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	}
	
	public function testGetPerson(ApiTester $I)
	{
	    $I->wantTo('Get a person by API');
	    $I->haveHttpHeader('Content-Type', 'application/json');
	    $I->amBearerAuthenticated( $this->token);
	    $I->haveHttpHeader('Accept', 'application/json');
	    $I->sendGET('person/'.$this->personId);
	    $I->seeResponseContainsJson(array('person_id' => $this->personId));
	    $I->seeResponseContains('person_first_name');
	    $I->seeResponseContains('person_last_name');
	    $I->seeResponseContains('title');
	    $I->seeResponseContains('person_email');
	    $I->seeResponseCodeIs(200);
	    $I->seeResponseIsJson();
	}
	
	public function testGetPersonInvalid(ApiTester $I)
	{
		$I->wantTo('Get a invalid person by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('person/'.$this->invalidPersonId);
		$I->seeResponseContains('Person Not Found.');
	    $I->seeResponseCodeIs(400);
	    $I->seeResponseIsJson();
	}
	
	public function testGetPersonInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get a invalid person by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('person/'.$this->invalidPersonId);
		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
	}
	
	
}