<?php
require_once 'SynapseTestHelper.php';
use Synapse\RestBundle\Entity\CreatePasswordDto;
use GuzzleHttp\json_decode;
class PasswordCest extends SynapseTestHelper
{
	private $token;
    private $email;

    public function _before (ApiTester $I)
    {
    	$this->token = $this->authenticate($I);
        $this->email = "bipinbihari.pradhan@techmahindra.com";
    }

    /*
	* Since security concerns, service doesn't include token in the response. so cannot check token in test case. Since removed
	*/

    public function testForgotPassword(ApiTester $I)
    {
        $I->wantTo('Test Forgot Password API with valid email');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET('password/forgot?email='.$this->email);
        $I->seeResponseContains($this->email);
        $I->seeResponseContains('email_sent_status');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testForgotPasswordInvalidEmail(ApiTester $I)
    {
        $I->wantTo('Test Forgot Password API with Invalid email');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET('password/forgot?email=invalidemail@gmail.com');
        $I->seeResponseContains('No Email Found');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testValidatetoken(ApiTester $I)
    {
    	$I->wantTo('Test Validate Token by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET('password/forgot?email='.$this->email);
		/*
		* Since security concerns, service doesn't include token in the response. so cannot pass token in test case to validate token. Since below is invalid
        $token = json_decode($I->grabResponse());
        $token = $token->data->token;
        $I->wantTo('Test Validate Token API with valid token');
        $I->sendGET('password/validatetoken/'.$token);
        $I->seeResponseContains('token_validation_status');
        $I->seeResponseContains('ebi_confidentiality_stmt');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        */
    }


    public function testValidatetokenWithInvalidToken(ApiTester $I)
    {
        $I->wantTo('Test Validate Token API with Invalid token');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET('password/validatetoken/342rdsfsdgsdfg');
        $I->seeResponseContains('Activation Token Expired');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();

    }

   public function testCreatePassword(ApiTester $I)
    {
    	$I->wantTo('Test Create Password with Valid Data');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('password/forgot?email='.$this->email);
        $token = json_decode($I->grabResponse());
		/*
		* Since security concerns, service doesn't include token in the response. so cannot post token in test case to validate. Since below is invalid */
        /*$token = $token->data->token;
        $I->sendPOST('password', [
            'token' => $token,
            'password' => "aex33drrrr",
            'is_confidentiality_accepted' => true,
            'client_id' => "dffd"
            ]);
        $I->seeResponseContains('signin_status');
        $I->seeResponseContains('person_id');
        $I->seeResponseContains('person_first_name');
        $I->seeResponseContains('person_last_name');
        $I->seeResponseContains('person_type');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        */

    }

    public function testCreatePasswordWithoutConfidStmt(ApiTester $I)
    {
    	$I->wantTo('Test Create Password No Confid stmt accepted');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('password/forgot?email='.$this->email);
        $token = json_decode($I->grabResponse());
		/*Since security concerns, service doesn't include token in the response. so cannot post token in test case to validate*/
        /*$token = $token->data->token;
        $I->sendPOST('password', [
            'token' => $token,
            'password' => "aex33drrrr",
            'is_confidentiality_accepted' => 0,
            'client_id' => "dffd"
            ]);
       $I->seeResponseCodeIs(400);
       $I->seeResponseIsJson();
    */

    }

}
