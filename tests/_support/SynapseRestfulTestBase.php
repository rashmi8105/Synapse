<?php


class SynapseRestfulTestBase
{
    /*
     * Authenticate Method for User
     * Requires:
     * ApiAuthTester
     * User Email
     * User Password
     */
    public function _authenticateUser($I, $userData)
    {

        $I->wantTo('Authenticate User');
        $I->sendPOST('http://localhost/oauth/v2/token', [
            'client_id' => "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
            'client_secret' => "3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480",
            'grant_type' => "password",
            'username' => $userData['email'],
            'password' => $userData['password']
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->seeResponseContains('access_token');
        $token = json_decode($I->grabResponse());

        return  $token->access_token;

    }


    /**
     * Gets the access token for a user of the admin site.
     * @param $I
     * @param $userData
     * @return mixed
     */
    public function _authenticateAdmin($I, $userData)
    {

        $I->wantTo('Authenticate User');
        $I->sendPOST('http://localhost/oauth/v2/token', [
            'client_id' => "2_14tx5vbsnois4ggg0ok0c4gog8kg0ww488gwkg88044cog4884",
            'client_secret' => "4v5p8idswhs0404owsws48gwwccc4wksw4c8s80wcocwskockg",
            'grant_type' => "password",
            'username' => $userData['email'],
            'password' => $userData['password']
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->seeResponseContains('access_token');
        $token = json_decode($I->grabResponse());

        return  $token->access_token;

    }

    /*
     *
     * APITestRunner for GET METHOD
     * Requires:
     * ApiAuthTester
     * authData - Array('email' => 'j@email.com', 'pass' => 'password')
     * apiCall - api/api/api
     * parameters Array(optional) include [] if none are needed)
     * response code expected 200, 401, etc
     * testData - Array ('key' => value)
     */
    public function _getAPITestRunner($I, $userData, $apiCall, $paramsForAPI, $responseCode, $testData)
    {
        //Get Access token from Authenticate
        if (array_key_exists('type', $userData) && $userData['type'] == 'admin')
            $access_token = $this->_authenticateAdmin($I, $userData);
        else
            $access_token = $this->_authenticateUser($I, $userData);

        //Authenticate Request
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($access_token);
        $I->haveHttpHeader('Accept', 'application/json');
        //Send Request
        $I->sendGET($apiCall, $paramsForAPI);

        // If we're checking for response code 403 or 400, also check it returns an empty data array.
        // (This does assume the response has the correct format.)
        //if ($responseCode == 403 or $responseCode == 400)
        //    $I->seeResponseContainsJson(['data' => []]);

        //Match all testData against received JSON
         foreach($testData as $dataPoint){
            $I->seeResponseContainsJson($dataPoint);
        } 
        //Does Response Code match
        if($responseCode != null)
            $I->seeResponseCodeIs($responseCode);

    }
    /*
     *
     * APITestRunner for POST METHOD
     * Requires:
     * ApiAuthTester
     * authData - Array('email' => 'j@email.com', 'pass' => 'password')
     * apiCall - api/api/api
     * parameters Array(optional) include [] if none are needed)
     * response code expected 200, 401, etc
     * testData - Array ('key' => value)
     */

    public function _postAPITestRunner($I, $userData, $apiCall, $paramsForAPI, $responseCode, $testData)
    {
        //Get Access token from Authenticate
        if (array_key_exists('type', $userData) && $userData['type'] == 'admin')
            $access_token = $this->_authenticateAdmin($I, $userData);
        else
            $access_token = $this->_authenticateUser($I, $userData);

        //Authenticate Request
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($access_token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST($apiCall, $paramsForAPI);
        foreach($testData as $dataPoint){

            $I->seeResponseContainsJson($dataPoint);
        }
        //Does Response Code match
        $I->seeResponseCodeIs($responseCode);

    }
    /*
     * APITestRunner for PUT METHOD
     * Requires:
     * ApiAuthTester
     * authData - Array('email' => 'j@email.com', 'pass' => 'password')
     * apiCall - api/api/api
     * parameters Array(optional) include [] if none are needed)
     * response code expected 200, 401, etc
     * testData - Array ('key' => value)
     */

    public function _putAPITestRunner($I, $userData, $apiCall, $paramsForAPI, $responseCode, $testData)
    {
        //Get Access token from Authenticate
        if (array_key_exists('type', $userData) && $userData['type'] == 'admin')
            $access_token = $this->_authenticateAdmin($I, $userData);
        else
            $access_token = $this->_authenticateUser($I, $userData);

        //Authenticate Request
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($access_token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT($apiCall, $paramsForAPI);
        foreach($testData as $dataPoint){

            $I->seeResponseContainsJson($dataPoint);
        }
        //Does Response Code match
        $I->seeResponseCodeIs($responseCode);

    }
    public function _deleteAPITestRunner($I, $userData, $apiCall, $paramsForAPI, $responseCode, $testData)
    {
        //Get Access token from Authenticate
        if (array_key_exists('type', $userData) && $userData['type'] == 'admin')
            $access_token = $this->_authenticateAdmin($I, $userData);
        else
            $access_token = $this->_authenticateUser($I, $userData);

        //Authenticate Request
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($access_token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE($apiCall, $paramsForAPI);
        /*foreach($testData as $dataPoint){

            $I->seeResponseContainsJson($dataPoint);
        }*/
        //Does Response Code match
        $I->seeResponseCodeIs($responseCode);

    }

    public function _academicYearsMySQLrunner($startDate = "2015-07-01", $endDate = "2015-12-01", $id = 110)
    {
        $query = 'UPDATE `synapse`.`org_academic_year` SET `start_date` ="'.$startDate.'", `end_date` ="'.$endDate.'" WHERE `id` = '.$id.' ;';
        $shellOutput = shell_exec("mysql -u root -psynapse synapse -e '$query'");
        if($shellOutput == '')
        {
            codecept_debug("Table Successfully updated");
        }
        else
        {
            codecept_debug($shellOutput);
        }
    }

    public function _academicTermsMySQLrunner($startDate = "2015-07-01", $endDate = "2015-12-01", $id = 123)
    {
        $query = 'UPDATE `synapse`.`org_academic_terms` SET `start_date` ="'.$startDate.'", `end_date` ="'.$endDate.'" WHERE `id` = '.$id.' ;';
        $shellOutput = shell_exec("mysql -u root -psynapse synapse -e '$query'");
        if($shellOutput == '')
        {
            codecept_debug("Table Successfully updated");
        }
        else
        {
            codecept_debug($shellOutput);
        }
    }



    /**
     * @param null $dayIWant
     * @return array|string
     *
     * This gets you the dynamic date based on the current date
     * if there is specific day, use the parameters, else you
     * get an array of each date
     */
    public function _DynamicDates($dayIWant = Null)
    {
        $returnArray = [];
        $today = new DateTime('today');
        $writeToday = $today->format('Y-m-d');
        $returnArray['today'] = str_replace('', ' ', $writeToday);

        $tomorrow = $today->modify('+1 day');
        $writeTomorrow = $tomorrow->format('Y-m-d');
        $returnArray['tomorrow'] = str_replace(' ', '', $writeTomorrow);

        $yesterday = $today->modify('-2 days');
        $writeYesterday = $yesterday->format('Y-m-d');
        $returnArray['yesterday'] = str_replace(' ', '', $writeYesterday);

        $oneWeek = $today->modify('8 days');
        $writeOneWeek = $oneWeek->format('Y-m-d');
        $returnArray['1Week'] = str_replace(' ', '', $writeOneWeek);

        $oneWeekAgo = $today->modify('-14 days');
        $writeOneWeekAgo = $oneWeekAgo->format('Y-m-d');
        $returnArray['1WeekAgo'] = str_replace(' ', '', $writeOneWeekAgo);

        $twoWeekAgo = $today->modify('-7 days');
        $writeTwoWeekAgo = $twoWeekAgo->format('Y-m-d');
        $returnArray['2WeeksAgo'] = str_replace(' ', '', $writeTwoWeekAgo);

        $twoWeeks = $today->modify('28 days');
        $writeTwoWeeks = $twoWeeks->format('Y-m-d');
        $returnArray['2Weeks'] = str_replace(' ', '', $writeTwoWeeks);

        if (strtolower($dayIWant) === "yesterday") {
            return $returnArray['yesterday'];
        } elseif (strtolower($dayIWant) === "today") {
            return $returnArray['today'];
        } elseif (strtolower($dayIWant) === "tomorrow") {
            return $returnArray['tomorrow'];
        } elseif (strtolower($dayIWant) === "1 week") {
            return $returnArray['1Week'];
        } elseif (strtolower($dayIWant) === "1 week ago") {
            return $returnArray['1WeekAgo'];
        }elseif(strtolower($dayIWant) === "2 weeks ago"){
            return $returnArray['2WeeksAgo'];
        }elseif(strtolower($dayIWant) === "2 weeks"){
            return $returnArray['2Weeks'];
        }else{
            return $returnArray;
        }
    }


    public function _updateDatabase($I, $query) {
        $output = shell_exec("mysql -u root -psynapse synapse -e '$query'");
        if ($output != null)
            $I->assertTrue(false);
    }


    public function _runRiskCalculation($I, $studentId) {
        /*
        $query = 'update synapse.org_riskval_calc_inputs set is_riskval_calc_required="y" where person_id='.$studentId.';';
        $output = shell_exec("mysql -u root -psynapse synapse -e '$query'");
        if ($output != null)
            $I->assertTrue(false);
        */

        // Mark this person as one who has fresh data that necessitates rerunning the risk calculation.
        $query = 'update synapse.org_calc_flags_risk set calculated_at = NULL where person_id='.$studentId.';';
        $output = shell_exec("mysql -u root -psynapse synapse -e '$query'");
        if ($output != null)
            $I->assertTrue(false);

        // Run the risk calculation for one person (i.e., the one we just flagged).
        $output = shell_exec('mysql -u root -psynapse synapse -e "call org_RiskFactorCalculation(1);"');
        if ($output != null)
            $I->assertTrue(false);

        /*
        $output = shell_exec('mysql -u root -psynapse synapse -e "CALL org_RiskFactorCalculation(DATE_ADD(NOW(), INTERVAL 50 second), 25);"');
        if ($output != null)
            $I->assertTrue(false);
        */
    }

}