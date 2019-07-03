<?php

class SynapseTestHelper
{
    /* TODO: We need to probably add more than one test help method. One for authenticating Organizational users, one for admin users, and one for ART users*/
    /**
     * @param ApiTester $I
     * @return mixed The access token granted for logging in as the administrative user
     */
    public function authenticate(ApiTester $I)
    {
        $I->wantTo('Authenication');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('http://127.0.0.1:8080/oauth/v2/token', [
            'client_id' => "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
            'client_secret' => "3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480",
            'grant_type' => "password",
            'username' => "ramesh.kumhar@techmahindra.com",
            //TODO: This password isn't right for testing I don't think
            "password" => "ramesh@1974"
            ]);
        $I->seeResponseIsJson();
        $I->seeResponseContains('access_token');
        $I->seeResponseCodeIs(200);
        $tokenRequestResponse = json_decode($I->grabResponse());
        return  $tokenRequestResponse->access_token;
    
    }

    public function studentAuthenticate(ApiTester $I)
    {
    	$I->wantTo('Authenication');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendPOST('http://127.0.0.1:8080/oauth/v2/token', [
    			'client_id' => "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
    			'client_secret' => "3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480",
    			'grant_type' => "password",
    			'username' => "bipinbihari.pradhan@techmahindra.com",
    			//TODO: This password isn't right for testing I don't think
    			"password" => "ramesh@1974"
    			]);
    	$I->seeResponseIsJson();
    	$I->seeResponseContains('access_token');
    	$I->seeResponseCodeIs(200);
    	$tokenRequestResponse = json_decode($I->grabResponse());
    	return  $tokenRequestResponse->access_token;
    
    }
    
    public function authenticateFaculty(ApiTester $I)
    {
        $I->wantTo('Authenication');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('http://127.0.0.1:8080/oauth/v2/token', [
            'client_id' => "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
            'client_secret' => "3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480",
            'grant_type' => "password",
            'username' => "devadoss.poornachari@techmahindra.com",
            //TODO: This password isn't right for testing I don't think
            "password" => "ramesh@1974"
            ]);
        $I->seeResponseIsJson();
        $I->seeResponseContains('access_token');
        $I->seeResponseCodeIs(200);
        $tokenRequestResponse = json_decode($I->grabResponse());
        return  $tokenRequestResponse->access_token;
    
    }
}