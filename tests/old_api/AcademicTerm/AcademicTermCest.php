<?php
require_once 'tests/api/SynapseTestHelper.php';
class AcademicTermCest extends SynapseTestHelper {
	private $token;
	private $organization = 1;
	private $invalidOrg = -2;
	private $personId = 1;
	private $academicYearId = 2;
	private $invalidYearId = - 1;
	private $academicTermId = 1;
	private $invalidTermId = - 1;
	private $start;
	private $end;
	private $timezone = 'Canada/Pacific';
	public function _before(ApiTester $I) {
		$this->token = $this->authenticate ( $I );
		$this->start = new DateTime ( "now" );
		$this->end = clone $this->start;
		$this->end->add ( new \DateInterval ( 'P1M' ) );
	}

	//Repository Test Comment...
	//2nd test comment...

	public function testCreateAcademicTerm(ApiTester $I, \Codeception\Scenario $scenario) {

		//$scenario->skip("This test is accessing a part of the code writing a system-out line. I attempted to discover why, but could not find the culprit. When system-out gets called, it adds it to the coverage report and causes the code coverage plug-in for our sonar reporting to fail. We have to find the reason the system-out gets called and remove it before we can re-enable this test.");
		$I->wantTo ( "Create Academic Term by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => $this->academicYearId,
				'name' => "Test Academic Term",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$I->seeResponseContainsJson ( array (
				'organization_id' => $this->organization 
		) );
		$I->seeResponseContainsJson ( array (
				'academic_year_id' => $this->academicYearId 
		) );
		$I->seeResponseContainsJson ( array (
				'name' => "Test Academic Term" 
		) );
		$I->seeResponseContains ( 'term_id' );
		$I->seeResponseContainsJson ( array (
				'start_date' => $this->start->format ( "Y-m-d" ) 
		) );
		$I->seeResponseContainsJson ( array (
				'end_date' => $this->end->format ( "Y-m-d" ) 
		) );
		$I->seeResponseCodeIs ( 201 );
		$I->seeResponseIsJson ();
	}
	public function testGetAcademicTerm(ApiTester $I, $scenario){
		//$scenario->skip('Causes system-out messages');
		$I->wantTo ( "Get Academic Term by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => $this->academicYearId,
				'name' => "Test Academic Term",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$term = json_decode ( $I->grabResponse () );
		$I->sendGET ( 'academicterms/' . $this->organization . "/" . $this->academicYearId . "/" . $term->data->term_id );
		$getTerm = json_decode ( $I->grabResponse () );
		$I->seeResponseContainsJson ( array (
				'organization_id' => $this->organization 
		) );
		$I->seeResponseContainsJson ( array (
				'academic_year_id' => $this->academicYearId 
		) );
		$I->seeResponseContainsJson ( array (
				'name' => "Test Academic Term" 
		) );
		$I->seeResponseContainsJson ( array (
				'term_id' => $term->data->term_id 
		) );
		$I->seeResponseCodeIs ( 200 );
		$I->seeResponseIsJson ();
	}
	public function testListAcademicTerms(ApiTester $I) {
		$I->wantTo ( "List Academic Terms by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => $this->academicYearId,
				'name' => "Test Academic Term",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$term = json_decode ( $I->grabResponse () );
		$I->sendGET ( 'academicterms/' . $this->organization . "/" . $this->academicYearId );
		$terms = json_decode ( $I->grabResponse () );
		$endTerm = end ( $terms->data->academic_terms );
		$I->seeResponseContainsJson ( array (
				'organization_id' => $this->organization 
		) );
		$I->seeResponseContainsJson ( array (
				'academic_year_id' => $this->academicYearId 
		) );
		$I->seeResponseContains ( 'academic_terms' );
		$I->seeResponseContainsJson ( array (
				'name' => "Test Academic Term" 
		) );
		$I->seeResponseContainsJson ( array (
				'term_id' => $term->data->term_id 
		) );
		$I->seeResponseCodeIs ( 200 );
		$I->seeResponseIsJson ();
	}
	public function testEditAcademicTerm(ApiTester $I) {
		$I->wantTo ( "Edit Academic Terms by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => $this->academicYearId,
				'name' => "Test Academic Term",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$term = json_decode ( $I->grabResponse () );
		$I->sendPUT ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => $this->academicYearId,
				'term_id' => $term->data->term_id,
				'name' => "Test Academic Term Edited",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$I->seeResponseCodeIs ( 204 );
	}
	public function testDeleteAcademicTerm(ApiTester $I) {
		$I->wantTo ( "Delete Academic Terms by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => $this->academicYearId,
				'name' => "Test Academic Term",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$term = json_decode ( $I->grabResponse () );
		$I->sendDELETE ( 'academicterms/' . $term->data->term_id );
		$I->seeResponseCodeIs ( 204 );
	}
	public function testCreateAcademicTermInvalidOrg(ApiTester $I) {
		$I->wantTo ( "Create Academic Term with invalid organization by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'academicterms', [ 
				'organization_id' => $this->invalidOrg,
				'academic_year_id' => $this->academicYearId,
				'name' => "Test Academic Term",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$I->seeResponseContains ( 'Unauthorized access to organization: '.$this->invalidOrg );
		$I->seeResponseCodeIs ( 403 );
		$I->seeResponseIsJson ();
	}
	public function testCreateAcademicTermInvalidYear(ApiTester $I) {
		$I->wantTo ( "Create Academic Term with invalid academic year by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => $this->invalidYearId,
				'name' => "Test Academic Term",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$I->seeResponseContains ( 'Academic Year Not Found.' );
		$I->seeResponseCodeIs ( 400 );
		$I->seeResponseIsJson ();
	}
	public function testCreateAcademicTermGreaterStartDate(ApiTester $I) {
		$I->wantTo ( "Create Academic Term with invalid start date greater than end date by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => $this->academicYearId,
				'name' => "Test Academic Term",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->end->format ( "Y-m-d" ),
				'end_date' => $this->start->format ( "Y-m-d" ) 
		] );
		$I->seeResponseContains ( 'End-Date should be grater than Start-Date' );
		$I->seeResponseCodeIs ( 400 );
		$I->seeResponseIsJson ();
	}
	public function testCreateAcademicTermBeyondYear(ApiTester $I, $scenario) {
        //$scenario->skip("Failed");
		$I->wantTo ( "Create Academic Term beyond academic year by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$this->start->sub ( new \DateInterval ( 'P7M' ) );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => 1,
				'name' => "Test Academic Term",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$I->seeResponseContains ( 'Term Period beyond Year Period' );
		$I->seeResponseCodeIs ( 400 );
		$I->seeResponseIsJson ();
	}
	public function testGetAcademicTermInvalidOrg(ApiTester $I) {
		$I->wantTo ( "Get Academic Term with invalid Organization by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendGET ( 'academicterms/' . $this->invalidOrg . "/" . $this->academicYearId . "/" . $this->academicTermId );
		$I->seeResponseContains ( 'Unauthorized access to organization: '.$this->invalidOrg );
		$I->seeResponseCodeIs ( 403 );
		$I->seeResponseIsJson ();
	}
	public function testGetAcademicTermInvalidYear(ApiTester $I) {
		$I->wantTo ( "Get Academic Term with invalid Academic Year by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendGET ( 'academicterms/' . $this->organization . "/" . $this->invalidYearId . "/" . $this->academicTermId );
		$I->seeResponseContains ( 'Academic Year Not Found.' );
		$I->seeResponseCodeIs ( 400 );
		$I->seeResponseIsJson ();
	}
	public function testGetAcademicTermInvalidTerm(ApiTester $I) {
		$I->wantTo ( "Get Academic Term with invalid Academic Term by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendGET ( 'academicterms/' . $this->organization . "/" . $this->academicYearId . "/" . $this->invalidTermId );
		$I->seeResponseContains ( 'Academic Term Not Found.' );
		$I->seeResponseCodeIs ( 400 );
		$I->seeResponseIsJson ();
	}
	public function testListAcademicTermsInvalidOrg(ApiTester $I) {
		$I->wantTo ( "List Academic Terms with invalid Organization by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendGET ( 'academicterms/' . $this->invalidOrg . "/" . $this->academicYearId );
		$I->seeResponseContains ( 'Unauthorized access to organization: '.$this->invalidOrg );
		$I->seeResponseCodeIs ( 403 );
		$I->seeResponseIsJson ();
	}
	public function testListAcademicTermsInvalidYear(ApiTester $I) {
		$I->wantTo ( "List Academic Terms with invalid academic year by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendGET ( 'academicterms/' . $this->organization . "/" . $this->invalidYearId );
		$I->seeResponseContains ( 'Academic Year Not Found.' );
		$I->seeResponseCodeIs ( 400 );
		$I->seeResponseIsJson ();
	}
	public function testEditAcademicTermInvalidOrg(ApiTester $I) {
		$I->wantTo ( "Edit Academic Terms with Invalid Organization by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPUT ( 'academicterms', [ 
				'organization_id' => $this->invalidOrg,
				'academic_year_id' => $this->academicYearId,
				'term_id' => $this->academicYearId,
				'name' => "Test Academic Term Edited",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$I->seeResponseContains ( 'Unauthorized access to organization: '.$this->invalidOrg );
		$I->seeResponseCodeIs ( 403 );
		$I->seeResponseIsJson ();
	}
	public function testEditAcademicTermInvalidYear(ApiTester $I) {
		$I->wantTo ( "Edit Academic Terms with Invalid Academic Year by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPUT ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => $this->invalidYearId,
				'term_id' => $this->academicYearId,
				'name' => "Test Academic Term Edited",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$I->seeResponseContains ( 'Academic Year Not Found.' );
		$I->seeResponseCodeIs ( 400 );
		$I->seeResponseIsJson ();
	}
	public function testEditAcademicTermInvalidTerm(ApiTester $I) {
		$I->wantTo ( "Edit Academic Terms with Invalid Academic Term by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPUT ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => $this->academicYearId,
				'term_id' => $this->invalidTermId,
				'name' => "Test Academic Term Edited",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$I->seeResponseContains ( 'Academic Term Not Found.' );
		$I->seeResponseCodeIs ( 400 );
		$I->seeResponseIsJson ();
	}
	public function testDeleteAcademicTermInvalidTerm(ApiTester $I) {
		$I->wantTo ( "Delete Academic Terms with invalid academic term by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendDELETE ( 'academicterms/' . $this->invalidTermId );
		$I->seeResponseContains ( 'Academic Term Not Found.' );
		$I->seeResponseCodeIs ( 400 );
		$I->seeResponseIsJson ();
	}
	public function testCreateAcademicTermInvalidAuthentication(ApiTester $I) {
		$I->wantTo ( 'Create Academic Term by API with invalid Authentication by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => $this->academicYearId,
				'name' => "Test Academic Term",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$I->seeResponseCodeIs ( 403 );
		$I->seeResponseIsJson ();
	}
	public function testGetAcademicTermInvalidAuthentication(ApiTester $I) {
		$I->wantTo ( 'Get Academic Term by API with invalid Authentication by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendGET ( 'academicterms/' . $this->organization . "/" . $this->academicYearId . "/" . $this->academicTermId );
		$I->seeResponseCodeIs ( 403 );
		$I->seeResponseIsJson ();
	}
	public function testListAcademicTermsInvalidAuthentication(ApiTester $I) {
		$I->wantTo ( 'List Academic Term by API with invalid Authentication by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendGET ( 'academicterms/' . $this->organization . "/" . $this->academicYearId );
		$I->seeResponseCodeIs ( 403 );
		$I->seeResponseIsJson ();
	}
	public function testEditAcademicTermInvalidAuthentication(ApiTester $I) {
		$I->wantTo ( 'Edit Academic Term by API with invalid Authentication by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPUT ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => $this->academicYearId,
				'term_id' => $this->academicTermId,
				'name' => "Test Academic Term Edited",
				'term_code' => substr ( uniqid (), 0, 9 ),
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$I->seeResponseCodeIs ( 403 );
		$I->seeResponseIsJson ();
	}
	public function testDeleteAcademicTermInvalidAuthentication(ApiTester $I) {
		$I->wantTo ( 'Delete Academic Term by API with invalid Authentication by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendGET ( 'academicterms/' . $this->organization . "/" . $this->academicTermId );
		$I->seeResponseCodeIs ( 403 );
		$I->seeResponseIsJson ();
	}
	public function testCreateAcademicTermWithSameTermCode(ApiTester $I) {
		$I->wantTo ( "Create Academic Term by API" );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->haveHttpHeader ( 'Accept', 'application/json' );
		$I->sendPOST ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => $this->academicYearId,
				'name' => "Test Academic Term",
				'term_code' => "Term123",
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$I->sendPOST ( 'academicterms', [ 
				'organization_id' => $this->organization,
				'academic_year_id' => $this->academicYearId,
				'name' => "Test Academic Term",
				'term_code' => "Term123",
				'start_date' => $this->start->format ( "Y-m-d" ),
				'end_date' => $this->end->format ( "Y-m-d" ) 
		] );
		$I->seeResponseContains ( 'Term Id already exists.' );
		$I->seeResponseCodeIs ( 400 );
		$I->seeResponseIsJson ();
	}
}
