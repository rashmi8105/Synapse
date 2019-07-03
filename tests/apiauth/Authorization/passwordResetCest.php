<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';

class passwordResetCest extends SynapseRestfulTestBase
{

    /* LOL This is a funny buggie that needs to be tested, currently I
     * am testing password reset, the current system gives a token which
     * can be combined with the #/reset/token
     */
    // http://synapse-staging-backend.mnv-tech.com/api/v1/password/forgot?email=



    function resetPasswordCest(ApiAuthTester $I){
        $I->wantTo('Reset My password Safely');

        //$I->haveHttpHeader('Accept', 'application/json');
        //Send Request
        $I->sendGET('password/forgot?email=albus.dumbledore@mailinator.com', []);

        //Match all testData against received JSON
        $I->dontSeeResponseContains('token');

        //Does Response Code match
        $I->seeResponseCodeIs(200);
    }
    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }



}