<?php
use GuzzleHttp\json_decode;
require_once 'SynapseTestHelper.php';
class UploadCest extends SynapseTestHelper
{
	private $token;
	 public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }
    public function createStudentUpload(ApiTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('create a student upload by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('upload/student', [
            'organization' => '1',
            'key' => 'student-upload.csv'
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        /* @TODO: Create test CSV in _data with known
         *  data so seeResponseContainsJson() can be used.
        */
    }

    public function getStudentUploads(ApiTester $I)
    {
        $I->wantTo('get all student uploads by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
		 $I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('upload/student');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        /* @TODO: Create test CSV in _data with known
         *  data so seeResponseContainsJson() can be used.
        */
    }

    public function getStudentUpload(ApiTester $I)
    {
        $I->wantTo('get a student upload by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
         $I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('upload/student/1');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        /* @TODO: Create test CSV in _data with known
         *  data so seeResponseContainsJson() can be used.
        */
    }
}
