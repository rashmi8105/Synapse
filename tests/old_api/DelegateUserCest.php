<?php
require_once 'SynapseTestHelper.php';
class DelegateUserCest extends SynapseTestHelper {
	
	private $token;
	private $organization = 1;
	private $inValidOrganization = -1;
	private $person = 1;
	private $delegateToPerson = 2;
	private $isSelectedTrue = true;
	private $isSelectedFalse = true;
	private $isDeletedTrue = true;
	private $isDeletedFalse = false;
	public function _before(ApiTester $I) {
		$this->token = $this->authenticate ( $I );
	}
	private function getDelegatesArray($organization, $person, $delegatedToPersonId, $isSelected, $isDeleted) {
		$delegator = array ();
		$delegator ['person_id'] = $person;
		$delegator ['organization_id'] = $organization;
		$delegates = array (
				"delegated_to_person_id" => $delegatedToPersonId,
				"is_selected" => $isSelected,
				"is_deleted" => $isDeleted 
		);
		$delegator ['delegated_users'] [] = $delegates;
		return json_encode ( $delegator );
	}
	public function testcreateDelegateUserInvalidAuthentication(ApiTester $I) {
		$delegateParams = $this->getDelegatesArray ( $this->organization, $this->person, $this->delegateToPerson, $this->isSelectedTrue, $this->isDeletedFalse );
		$I->wantTo ( 'Create delegate with invalid authentication by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( "invalid_token" );
		$I->sendPOST ( 'appointments/'.$this->organization.'/proxy', $delegateParams );
		$I->seeResponseCodeIs ( 401 );
		$I->seeResponseIsJson ();
	}
	public function testcreateDelegateUserValidValues(ApiTester $I, $scenario) {		
        //$scenario->skip("Failed");
		$delegateParams = $this->getDelegatesArray ( $this->organization, $this->person, $this->delegateToPerson, $this->isSelectedTrue, $this->isDeletedFalse );
		
		$I->wantTo ( 'Create delegate with invalid authentication by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->sendPOST ( 'appointments/'.$this->organization.'/proxy', $delegateParams );		
		$I->seeResponseContains ( 'person_id' );
		$I->seeResponseContains ( 'organization_id' );
		$I->seeResponseContains ( 'delegated_users' );
		$I->seeResponseContains ( 'calendar_sharing_id' );
		$I->seeResponseIsJson ( array (
				'organization_id' => $this->organization 
		) );
		$I->seeResponseIsJson ( array (
				'person_id' => $this->person 
		) );
		$I->seeResponseCodeIs ( 201 );
		$I->seeResponseIsJson ();
	}
	/*
	public function testcreateDelegateUserInValidValues(ApiTester $I) {
		$delegateParams = $this->getDelegatesArray ( $this->inValidOrganization, $this->person, $this->delegateToPerson, $this->isSelectedTrue, $this->isDeletedFalse );
	
		$I->wantTo ( 'Create delegate with invalid authentication by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->sendPOST ( 'appointments/'.$this->organization.'/proxy', $delegateParams );				
		$I->seeResponseCodeIs ( 400 );
		$I->seeResponseIsJson ();
	}
	*/

	public function testProxySelectedInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get proxy selected list invalid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated("invalid_token");
		$I->sendGET('appointments/'.$this->organization.'/proxySelected');
		$I->seeResponseCodeIs(401);
		$I->seeResponseIsJson();
			
	}
	public function testProxySelectedWithValidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get proxy selected list with valid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('appointments/'.$this->organization.'/proxySelected?user_id='.$this->person);		
		$I->seeResponseIsJson ( array (
				'organization_id' => $this->organization 
		) );
		$I->seeResponseIsJson ( array (
				'person_id' => $this->person 
		) );
		$I->seeResponseContains('delegated_users');		
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();	
	}
	public function testManagedUsersInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get managed Users list invalid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated("invalid_token");
		$I->sendGET('appointments/'.$this->organization.'/managedUsers');
		$I->seeResponseCodeIs(401);
		$I->seeResponseIsJson();
			
	}
	
	public function testManagedUsersWithValidAuthentication(ApiTester $I, $scenario)
	{
        //$scenario->skip("Failed");
		$I->wantTo('Get Managed Users list with valid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('appointments/'.$this->organization.'/managedUsers?person_id_proxy='.$this->delegateToPerson);	
		$I->seeResponseIsJson ( array (
				'organization_id' => $this->organization
		) );
		$I->seeResponseIsJson ( array (
				'person_id_proxy' => $this->person
		) );
		$I->seeResponseContains('managed_users');
		$I->seeResponseContains('calendar_sharing_id');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	}
	
}
