<?php
namespace Helper;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class GenerateCSV {

    public $XML;

    public function __construct($FilePath) {
        $this->XML = simplexml_load_file($FilePath) or die("Error: Cannot create object");
        
    }

    public function getName($i) {
        return $this->XML->testsuite->testcase[$i]['name'];
    }

    public function getFeature($i) {
        return $this->XML->testsuite->testcase[$i]['feature'];
    }

    Public function getError($i) {
        return $this->XML->testsuite->testcase[$i]->error;
    }

    Public function getFailure($i, $j = 0) {
        return $this->XML->testsuite->testcase[$i]->failure[$j];
    }

    public function putTestCaseDataIntoCSVFile() {
        $this->deleteTestDataFileIfAlreadyExist();
        $file = fopen(realpath(__DIR__ .DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..") .DIRECTORY_SEPARATOR. 'TestResultFiles'.DIRECTORY_SEPARATOR.'TestCaseData' . date("YMD") . ".csv", "w+");
        //$file = fopen(realpath(__DIR__ . '/../../..') . '/TestResultFiles/TestCaseData' . date("YMD") . ".csv", "w+");
        fputcsv($file, array("TestCase", "TestCaseID", "StatusID", "Message"));
        $numberofTestNode = $this->XML->testsuite['tests'];

        for ($i = 0; $i < $numberofTestNode; $i++) {
            $TestStatus = $this->getStatus($i);
            $Message = $this->getMessage($i);
            $nameString = $this->getName($i);
            $TestCase = $this->getTestCaseName($nameString);
            $TestCaseID = $this->getTestCaseID($nameString);

            fputcsv($file, array($TestCase, $TestCaseID, $TestStatus, $Message));
        }

        fclose($file);
    }

    public function getStatus($i) {

        //checking if tests is passed
        if ($this->XML->testsuite->testcase[$i]->children() == '') {
            $TestStatus = "1";
        } else {
            if ($this->getFailure($i) != '') {
                $TestStatus = "5";
            }
            if ($this->getError($i) != '') {
                $TestStatus = "4";
            }
        }
        return $TestStatus;
    }

    Public function getMessage($i) {
        $Message = '';

        //checking if tests is passed
        if ($this->XML->testsuite->testcase[$i]->children() == '') {
            $Message = "This Test Case is Passed";
        } else {
            if ($this->getFailure($i) != '') {
                $Message = "This Test Is Failed \n";
                $lines = $this->getFailure($i);
                $stacktrace = explode("\n", $lines);
                $Message = $Message . "\n" . $stacktrace[1];

                $j = 1;
                while (1) {
                    if ($this->getFailure($i, $j) != '') {
                        $Message1 = $this->getFailure($i, $j);
                        $stacktrace = explode("\n", $Message1);
                        $j++;

                        $Message = $Message . "\n" . $stacktrace[1];
                    } else {
                        break;
                    }
                }
            }
            if ($this->getError($i) != '') {
                if ($Message == '') {
                    $Message = "Error occured in this Test Case\n";
                } else {
                    $Message = $Message . "\n" . "Error occured in this Test Case";
                }

                $lines = $this->getError($i);
                $stacktrace = explode("\n", $lines);
                $Message = $Message . "\n" . $stacktrace[1];
            }
        }

        return $Message;
    }

    public function getTestCaseName($nameString) {
        if (strpos($nameString, "|") != false) {
            $EditedNameString = explode("|", $nameString);
            $nameString = $EditedNameString[0];
        }
        $TestString = explode("-", $nameString);

        return $TestString[0];
    }

    public function getTestCaseID($nameString) {
        if (strpos($nameString, "|") != false) {
            $EditedNameString = explode("|", $nameString);
            $nameString = $EditedNameString[0];
        }
        $TestString = explode("-", $nameString);

        if(array_key_exists(1,explode("=", $TestString[1])))
         {
        $TestCaseID = explode("=", $TestString[1])[1];
        return trim($TestCaseID);
        }
        else
        {   echo"'\n";
            echo "wrong test caseid format in $nameString";
            
            return;
        }
      
        
    }

    public function deleteTestDataFileIfAlreadyExist() {
        
        if (file_exists(realpath(__DIR__ .DIRECTORY_SEPARATOR. '..'.DIRECTORY_SEPARATOR.'..') . '/TestResultFiles/TestCaseData' . date("YMD") . ".csv")) {
            unlink("TestCaseData" . date("YMD") . ".csv");
        }
    }

}
