<?php

namespace Step\Api;

class Loginapi extends \ApiTester {

    /**
     * @Given I am on skyfactor login page
     */
    public function iAmOnSkyfactorLoginPage() {
        $this->LoginPage();
    }

    /**
     * @When I get the access Token
     */
    public function iGetTheAccessToken() {
        $this->GetToken();
    }

    /**
     * @Then I am able to see the content
     */
    public function iAmAbleToSeeTheContent() {
        $this->ValidateJson();
    }

    public function LoginPage() {
        $I = $this;
        $I->wantTo('perform actions and see result');
        $I->haveHttpHeader("Accept", "application/json");
    }

    public function GetToken() {
        $I = $this;
        $I->sendPOST('oauth/v2/token', ["client_id" => "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s", "client_secret" => "3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480", "grant_type" => "password", "username" => "qanupur@mailinator.com", "password" => "Qait@123"]);
        $I->canSeeResponseContains("access_token");
        $var = $I->grabResponse();
        $decode = json_decode($var, true);
        $I->amBearerAuthenticated($decode["access_token"]);
    }

    public function ValidateJson() {
        $I = $this;
        $I->sendGET("api/v1/organization/215/overview");
        $I->canSeeResponseContains('"organization_id":"215"');
    }

}
