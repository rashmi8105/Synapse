<?php
require_once 'tests/api/SynapseTestHelper.php';
class StaticListCest extends SynapseTestHelper {
	private $token;
	
	private $orgId = 1;
	
	private $invalidOrg = - 100;
	
	private $personId = 1;
	
	private $staticListName = "Static List";
	
	private $staticListDescription = "Static List Description";
	
	private $studentId = 8;
	
	private $studentExternalId = "external_id-8";
	
	private $shareToPersonId = 3;
	
	private $invalidStudentId = -1;
	
	public function _before(ApiTester $I) {
		$this->token = $this->authenticate ( $I );
	}
	
	public function testCreateStaticList(ApiTester $I) {
		$I->wantTo ( "Create Static List by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$staticListName = $this->staticListName . rand ( 1, 10 );
		$I->sendPOST ( 'staticlists', [ 
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription 
		] );
		$staticList = json_decode ( $I->grabResponse () );
		$I->seeResponseContains ( 'staticlist_id' );
		$I->seeResponseContainsJson ( array (
				'staticlist_name' => $staticListName 
		) );
		$I->seeResponseContainsJson ( array (
				'staticlist_description' => $this->staticListDescription 
		) );
		$I->seeResponseContainsJson ( array (
				'person_id' => $this->personId 
		) );
		$I->seeResponseCodeIs ( 201 );
		$I->seeResponseIsJson ();
	}
	
	public function testGetAllStaticList(ApiTester $I)
	{
		$I->wantTo('Get Static List');
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$staticListName = $this->staticListName . rand ( 11, 20 );
		$I->sendPOST ( 'staticlists', [ 
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription 
		] );
		$resp = json_decode($I->grabResponse());
		$id = $resp->data->staticlist_id;
		
		$I->sendPUT ( "staticlists/$id/students", [
				"student_edit_type" => "add",
				"student_id" => $this->studentExternalId,
				"staticlist_id" => $id,
				"org_id" => $this->orgId
				] );
		
		$I->sendGet('staticlists?offset=25&page_no=1&sortBy=-created_at&org_id='.$this->orgId);
		// var_dump(json_decode($I->grabResponse())); exit;
		$I->seeResponseCodeIs(200);
		$I->seeResponseContains('total_records');
		$I->seeResponseContains('total_pages');
		$I->seeResponseContains('records_per_page');
		$I->seeResponseContains('current_page');
		$I->seeResponseContains('static_list_details');
	
		$I->seeResponseIsJson();
	}
	
	public function testEditStaticList(ApiTester $I) {
		$I->wantTo ( "Edit Static List by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$staticListName = $this->staticListName . rand ( 21, 30 );
		$I->sendPOST ( 'staticlists', [
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription
				] );
		$resp = json_decode ( $I->grabResponse () );
		$id = $resp->data->staticlist_id;
		$staticListName = $this->staticListName . rand ( 31, 40 );
		$staticListDescription = $this->staticListDescription . rand ( 31, 40 );
		$I->sendPUT ( "staticlists/$id", [
				"staticlist_id" => $id,
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $staticListDescription
				] );
		
		
		$I->seeResponseContainsJson ( array (
				'staticlist_id' => $id
		) );
		$I->seeResponseContainsJson ( array (
				'staticlist_name' => $staticListName
		) );
		$I->seeResponseContainsJson ( array (
				'staticlist_description' => $staticListDescription
		) );
		$I->seeResponseContainsJson ( array (
				'person_id' => $this->personId
		) );
		$I->seeResponseCodeIs ( 201 );
		$I->seeResponseIsJson ();
	}
	
	public function testGetStaticListById(ApiTester $I)
	{
		$I->wantTo('Get Static List By Id.');
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$staticListName = $this->staticListName . rand ( 41, 50 );
		$I->sendPOST ( 'staticlists', [
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription
				] );
		$resp = json_decode($I->grabResponse());
		$id = $resp->data->staticlist_id;
	
		$I->sendPUT ( "staticlists/$id/students", [
				"student_edit_type" => "add",
				"student_id" => $this->studentExternalId,
				"staticlist_id" => $id,
				"org_id" => $this->orgId
				] );
	
		$I->sendGet("staticlists/$id?offset=25&org_id=87&page_no=1");
		//var_dump(json_decode($I->grabResponse())); exit;
	
		$I->seeResponseCodeIs(200);
		$I->seeResponseContains('total_students');
		$I->seeResponseContains('red2');
		$I->seeResponseContains('red1');
		$I->seeResponseContains('yellow');
		$I->seeResponseContains('green');
		$I->seeResponseContains('gray');
		$I->seeResponseContains('total_pages');
		$I->seeResponseContains('records_per_page');
		$I->seeResponseContains('current_page');
		$I->seeResponseContains('total_records');
	
		$I->seeResponseContainsJson ( array (
				'staticlist_name' => $staticListName
		) );
		$I->seeResponseContainsJson ( array (
				'staticlist_description' => $this->staticListDescription
		) );
	
		$I->seeResponseIsJson();
	}
	
	public function testEditStudentToStaticList(ApiTester $I) {
		$I->wantTo ( "Edit Static List Student by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$staticListName = $this->staticListName . rand ( 51, 60 );
		$I->sendPOST ( 'staticlists', [
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription
				] );
		$resp = json_decode ( $I->grabResponse () );
	
		$id = $resp->data->staticlist_id;
		$I->sendPUT ( "staticlists/$id/students", [
				"student_edit_type" => "add",
				"student_id" => $this->studentExternalId,
				"staticlist_id" => $id,
				"org_id" => $this->orgId
				] );
	
		$I->seeResponseCodeIs ( 200 );
		$I->seeResponseIsJson ();
	}
	
	public function testStaticListShare(ApiTester $I) {
		$I->wantTo ( "Static List Share by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$staticListName = $this->staticListName . rand ( 61, 70 );
		$I->sendPOST ( 'staticlists', [
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription
				] );
		$resp = json_decode ( $I->grabResponse () );
	
		$id = $resp->data->staticlist_id;
		$I->sendPOST ( "staticlists/$id/shares", [
				"person_id" => $this->shareToPersonId,
				"org_id" => $this->orgId
				] );
	
		$I->seeResponseCodeIs ( 201 );
	
		$I->seeResponseContains('staticlist_id');
		$I->seeResponseContainsJson ( array (
				'person_id' => $this->shareToPersonId
		) );
		$I->seeResponseContainsJson ( array (
				'org_id' => $this->orgId
		) );
		$I->seeResponseContainsJson ( array (
				'shared_person_name' => $staticListName
		) );
		$I->seeResponseIsJson ();
	}
	
	public function testStaticListDelete(ApiTester $I) {
		$I->wantTo ( "Static List Delete by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$staticListName = $this->staticListName . rand ( 71, 80 );
		$I->sendPOST ( 'staticlists', [
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription
				] );
		$resp = json_decode ( $I->grabResponse () );
	
		$id = $resp->data->staticlist_id;
	
		$I->sendDELETE ( "staticlists/$id/".$this->orgId);
	
		$I->seeResponseCodeIs ( 204 );
	
	}
	
	public function testCountStudentToStaticList(ApiTester $I) {
		$I->wantTo ( "Count Static List Student by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$staticListName = $this->staticListName . rand ( 81, 90 );
		$I->sendPOST ( 'staticlists', [
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription
				] );
		$resp = json_decode ( $I->grabResponse () );
	
		$id = $resp->data->staticlist_id;
		$I->sendPUT ( "staticlists/$id/students", [
				"student_edit_type" => "add",
				"student_id" => $this->studentExternalId,
				"staticlist_id" => $id,
				"org_id" => $this->orgId
				] );
	
		$I->sendGet("staticlists/count/$id");
	
		$I->seeResponseCodeIs(200);
		$I->seeResponseContains('TotalStudents');
		$I->seeResponseIsJson ();
	}
	
	public function testRemoveStudentToStaticList(ApiTester $I) {
		$I->wantTo ( "Remove Static List Student by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$staticListName = $this->staticListName . rand ( 91, 100 );
		$I->sendPOST ( 'staticlists', [
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription
				] );
		$resp = json_decode ( $I->grabResponse () );
	
		$id = $resp->data->staticlist_id;
		$I->sendPUT ( "staticlists/$id/students", [
				"student_edit_type" => "add",
				"student_id" => $this->studentExternalId,
				"staticlist_id" => $id,
				"org_id" => $this->orgId
				] );
	    
		$I->sendPUT ( "staticlists/$id/students", [
				"student_edit_type" => "remove",
				"student_id" => $this->studentId,
				"staticlist_id" => $id,
				"org_id" => $this->orgId
				] );
	
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson ();
	}
	
	public function testAddInvalidStudentToStaticList(ApiTester $I) {
		$I->wantTo ( "Remove Static List Student by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$staticListName = $this->staticListName . rand ( 101, 110 );
		$I->sendPOST ( 'staticlists', [
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription
				] );
		$resp = json_decode ( $I->grabResponse () );
	
		$id = $resp->data->staticlist_id;
		$I->sendPUT ( "staticlists/$id/students", [
				"student_edit_type" => "add",
				"student_id" => $this->invalidStudentId,
				"staticlist_id" => $id,
				"org_id" => $this->orgId
				] );
		 
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson ();
	}
	
	public function testGetAllStaticListWitoutSort(ApiTester $I)
	{
		$I->wantTo('Get Static List');
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$staticListName = $this->staticListName . rand ( 111, 120 );
		$I->sendPOST ( 'staticlists', [
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription
				] );
		$resp = json_decode($I->grabResponse());
		$id = $resp->data->staticlist_id;
	
		$I->sendPUT ( "staticlists/$id/students", [
				"student_edit_type" => "add",
				"student_id" => $this->studentExternalId,
				"staticlist_id" => $id,
				"org_id" => $this->orgId
				] );
	
		$I->sendGet('staticlists?offset=25&page_no=1&org_id='.$this->orgId);
		$I->seeResponseCodeIs(200);
		$I->seeResponseContains('total_records');
		$I->seeResponseContains('total_pages');
		$I->seeResponseContains('records_per_page');
		$I->seeResponseContains('current_page');
		$I->seeResponseContains('static_list_details');
	
		$I->seeResponseIsJson();
	}
	
	public function testManageInvalidStudentToStaticList(ApiTester $I) {
		$I->wantTo ( "Manage Static List Invalid Student by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$staticListName = $this->staticListName . rand ( 121, 130 );
		$I->sendPOST ( 'staticlists', [
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription
				] );
		$resp = json_decode ( $I->grabResponse () );
	
		$id = $resp->data->staticlist_id;
			
		$I->sendPUT ( "staticlists/manage", [
				"student_edit_type" => "add",
				"staticlist_id" => $id,
				"org_id" => $this->orgId,
				"students_details" => [
				[
				"student_id"=> $this->invalidStudentId
				]
				]
				] );
	
		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson ();
	}
	
	public function testAddStudentsToStaticLists(ApiTester $I){
	    $I->wantTo ( "Add Students To Static Lists by API" );
	    $I->haveHttpHeader ( 'Content-Type', 'application/json' );
	    $I->amBearerAuthenticated ( $this->token );
	    $staticListName = $this->staticListName . rand ( 131, 140 );
	    $I->sendPOST ( 'staticlists', [
	    		"org_id" => $this->orgId,
	    		"staticlist_name" => $staticListName,
	    		"staticlist_description" => $this->staticListDescription
	    		] );
	    $resp = json_decode ( $I->grabResponse () );
	    
	    $id = $resp->data->staticlist_id;
	    	
	    $I->sendPOST ( "students/staticlists", [
	    		"student_edit_type" => "add",
	            "student_id" => $this->studentId,
	    		"org_id" => $this->orgId,
	    		"static_list_details" => [
	    		[
	    		"static_list_id"=> $id
	    		]
	    		]
	    		] );
	    
	    $I->seeResponseCodeIs(201);
	    $I->seeResponseIsJson ();
	}
	
	public function testRemoveStudentsToStaticLists(ApiTester $I){
		$I->wantTo ( "Add Students To Static Lists by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$staticListName = $this->staticListName . rand ( 141, 150 );
		$I->sendPOST ( 'staticlists', [
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription
				] );
		$resp = json_decode ( $I->grabResponse () );
		 
		$id = $resp->data->staticlist_id;
	
		$I->sendPOST ( "students/staticlists", [
				"student_edit_type" => "add",
				"student_id" => $this->studentId,
				"org_id" => $this->orgId,
				"static_list_details" => [
				[
				"static_list_id"=> $id
				]
				]
				] );
		$I->sendPUT ( "students/staticlists/$id", [
				"student_edit_type"=> "remove",
                "student_id"=> $this->studentId,
                "org_id"=> $this->orgId,
                "staticlist_id"=> $id,
                "static_list_details"=> [
                    [
                        "student_id"=> $this->studentId
                    ]
                ] ]);
		$I->seeResponseCodeIs(204);
		$I->seeResponseIsJson ();
	}
	
	public function testCreateDuplicateNameStaticList(ApiTester $I){
		$I->wantTo ( "Add Students To Static Lists by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$staticListName = $this->staticListName . rand ( 151, 160 );
		$I->sendPOST ( 'staticlists', [
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription
				] );
		
		$I->sendPOST ( 'staticlists', [
				"org_id" => $this->orgId,
				"staticlist_name" => $staticListName,
				"staticlist_description" => $this->staticListDescription
				] );
		$I->seeResponseCodeIs(400);
	}
}