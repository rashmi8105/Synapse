<?php

namespace Helper;

require_once 'GenerateCSV.php';
require 'tests/_support/TestRailIntegration.php';

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module {
  protected $moduleContainer;
    
    public $webDriver;  
    

    public function _failed(\Codeception\TestInterface $test, $fail) {        
//        $r= new \Codeception\Module\WebDriver($this->moduleContainer);
//        //$r->debugWebDriverLogs();        
//        $filename1 = preg_replace('~\W~', '.', Descriptor::getTestSignature($test));
//        $filename2=explode("testcaseID", $filename1);
//        $filename=$filename2[0]; 
//        $outputDir = codecept_output_dir();
//        $r->_saveScreenshot($outputDir . mb_strcut($filename, 0, 245, 'utf-8') . '.fail.png');
//       // $r->_savePageSource($outputDir . mb_strcut($filename, 0, 244, 'utf-8') . '.fail.html');
//        $r->debug("Screenshot and page source were saved into '$outputDir' dir");
    }
     
     
 


//public function _initialize() {
//        $this->wd_host =  sprintf('http://%s:%s/wd/hub', $this->config['host'], $this->config['port']);
//        $this->capabilities = $this->config['capabilities'];
//        $this->capabilities[\WebDriverCapabilityType::BROWSER_NAME] = $this->config['browser'];
//        $this->capabilities['firefox_profile'] = file_get_contents("Parallel1/profile.zip.b64");
//        $this->webDriver = \RemoteWebDriver::create($this->wd_host, $this->capabilities);
//        $this->webDriver->manage()->timeouts()->implicitlyWait($this->config['wait']);
//        $this->initialWindowSize();
//}
//    public function __destruct() {
//        $this->DeleteDuplicates();
//        $generate = new GenerateCSV("tests".DIRECTORY_SEPARATOR."_output".DIRECTORY_SEPARATOR."report.xml");
//        $generate->putTestCaseDataIntoCSVFile();
//        $this->PostResult();
//    }
//       
//
//    public function PostResult() {
//        $file = fopen(realpath(__DIR__ .DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR) .DIRECTORY_SEPARATOR. 'TestResultFiles'.DIRECTORY_SEPARATOR.'TestCaseData' . date("YMD") . ".csv", "r");
//        //$file = fopen(realpath(__DIR__ . '/../../../') . '/TestResultFiles/TestCaseData' . date("YMD") . ".csv", "r");
//        fgetcsv($file);
//        while (!feof($file)) {
//            $array = fgetcsv($file);
//            $Test = new \TestRailIntegration();
//            if ($array[1] == '') {
//                break;
//            }
//            $Test->postResultsToTestRail(trim($array[1]), trim($array[2]), trim($array[3]));
//        }
//        fclose($file);
//    }
//    
//    public function DeleteDuplicates() {
//        $xmlDoc = new \DOMDocument();
//        $xmlDoc->load("tests".DIRECTORY_SEPARATOR."_output".DIRECTORY_SEPARATOR."report.xml");
//        //$xmlDoc->load("tests/_output/report.xml");
//        $results = $xmlDoc->getElementsByTagName('testcase');
//        $testsuite = $xmlDoc->getElementsByTagName("testsuite");
//
//
//        for ($i = 0; $i < $results->length; $i++) {
//            $Element = $results->item($i);
//            for ($j = $i + 1; $j < $results->length; $j++) {
//
//                if ($results->item($j)->getAttribute("feature") == $Element->getAttribute("feature")) {
//                    $results->item($j)->parentNode->removeChild($results->item($j));
//                    $testsuite->item(0)->setAttribute("tests", $testsuite->item(0)->getAttribute("tests") - 1);
//                }
//            }
//        }
//        file_put_contents(realpath(__DIR__ .DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR."tests".DIRECTORY_SEPARATOR."_output".DIRECTORY_SEPARATOR."report.xml", $xmlDoc->saveXML());
//        //file_put_contents(realpath(__DIR__ . '/../../../') ."/tests/_output/report.xml", $xmlDoc->saveXML());
//    }



}

    
