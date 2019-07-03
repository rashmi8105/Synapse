<?php
require 'testrail.php';
require 'WebAppTestData.php';


class TestRailIntegration {

    function postResultsToTestRail($Tc_ID, $Result,$comment) {
        $I = new TestRailAPIClient('https://skyfactor.testrail.net/');
        $I->set_user('nupurgoel@qainfotech.com');
        $I->set_password('Qait@123');
        $Status_ID=$Result;
        $TestRunId=$this->getTestRunId();
        try{
        $I->send_post('add_result_for_case/' . $TestRunId . '/' . $Tc_ID . '', array('status_id' => $Status_ID, 'comment' => $comment,'elapsed'=>'5s'));
        }
     catch (Exception $exception)
        {  
         echo $Tc_ID."  ";
         echo"Invalid Post Request testcase ID or unable to post the results in TestRail due to may requests";
         echo "\n";
        } 
        
        }
    
    function CreateNewTestRun($env,$suite) {
          $I = new TestRailAPIClient('https://skyfactor.testrail.net/');
        $I->set_user('nupurgoel@qainfotech.com');
        $I->set_password('Qait@123');
        date_default_timezone_set("Asia/Calcutta");
    if ($suite=="api"){
        $data['suite_id'] = 31; // The ID of the suite
        $data['name'] = "Api_TestRun"." ".$env." ".date("d/M/Y H:i A");
        $runId = $I->send_post("add_run/2", $data); //project id is passed along with Data
        file_put_contents("tests".DIRECTORY_SEPARATOR."_data".DIRECTORY_SEPARATOR."TestRunData.json", json_encode($runId, JSON_PRETTY_PRINT));
    }
    else{
        $data['suite_id'] = 13; // The ID of the suite
        $data['name'] = "Acceptance_TestRun"." ".$env." ".date("d/M/Y H:i A");
        $runId = $I->send_post("add_run/2", $data); //project id is passed along with Data
        file_put_contents("tests".DIRECTORY_SEPARATOR."_data".DIRECTORY_SEPARATOR."TestRunData.json", json_encode($runId, JSON_PRETTY_PRINT));
    }

    }
    
    
    function GetTestCases() {
        $I = new TestRailAPIClient('https://mnv.testrail.com/');
        $I->set_user('nupurgoel@qainfotech.net');
        $I->set_password('123macmillan');
        $section=$I->send_get("get_sections/10/&suite_id=1332");
        $runId = $I->send_get("get_tests/1332");               
        $build=new BuildFeatures();
        $build->BuildFeatureFiles($runId,$section);
//        echo "count is   ".count($runId);
//        var_dump($runId[0]["case_id"]);exit;
//        file_put_contents("tests/_data/TestCases.json", json_encode($runId, JSON_PRETTY_PRINT));
    }

    static function getTestRunId() {
        $webapp = new WebAppTestData();
        $value = $webapp->getTestData(new static);
        $testRunId = $value["id"];
        return $testRunId;
    }

    

}
