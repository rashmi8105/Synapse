<?php
use GuzzleHttp\json_decode;
require_once 'SynapseTestHelper.php';

class StudentTalkingPointsCest extends SynapseTestHelper
{
	private $studentId = 2;
	private $personId = 1;
	private $organizationId = 1;
	private $invalidStudentId = -1;
	
	public function _before(ApiTester $I)
	{
		$this->token = $this->authenticate($I);
	}
	/* Need to be Fixed
	public function testgetTalkingPointsWithPrePopulatedData(ApiTester $I)
	{
		// This functionality DB data are pre populated as service yet to be created
		$I->wantTo('Get Student Talking Points with pre populated db data by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('students/'.$this->studentId.'/talking_point');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$talkingPoints = json_decode($I->grabResponse());
		$I->seeResponseIsJson ( array (
		'person_student_id' => $this->studentId 
		) );
		$I->seeResponseIsJson (array(
		'person_staff_id' => $this->personId 
		));
		$I->seeResponseIsJson (array(
		'organization_id' => $this->organizationId 
		));
		$I->seeResponseIsJson (array(
		'talking_points_weakness_count' => count($talkingPoints->data->weakness)
		));
		$I->seeResponseIsJson (array(
		'talking_points_strengths_count' => count($talkingPoints->data->strength)
		));
	}
	*/

	public function testgetTalkingPointsWithInvalidStudentId(ApiTester $I)
	{
		// This functionality DB data are pre populated as service yet to be created
		$I->wantTo('Get Student Talking Points with invalid student id by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('students/'.$this->invalidStudentId.'/talking_point');
		$I->seeResponseCodeIs(403);
		$I->seeResponseContains ("Access Denied");
		$I->seeResponseIsJson();
		
	}
	
}