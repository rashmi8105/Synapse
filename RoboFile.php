<?php

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
require 'tests/_support/TestRailIntegration.php';
require_once 'vendor/autoload.php';
require 'tests/_support/Helper/GenerateCSV.php';


class RoboFile extends \Robo\Tasks
{

// define public methods as commands
    private $name = array();
    private $CommandsArray = array();
    private $numberofInstance;
    use \Codeception\Task\MergeReports;
    use \Codeception\Task\SplitTestsByGroups;

    public function RunTests($arg1 = '', $arg2 = '')
    {
        $argumentArray = array($arg1, $arg2);

        $env = 'training-chrome';
        $suite = 'acceptance';

        for ($i = 0; $i < count($argumentArray); $i++) {
            if ($this->IsSubStringPresent($argumentArray[$i], "env")) {
                $envArray = explode("=", $argumentArray[$i]);
                $env = $envArray[1];
            }
            if ($this->IsSubStringPresent($argumentArray[$i], "suite")) {
                $suiteArray = explode("=", $argumentArray[$i]);
                $suite = $suiteArray[1];
            }
        }

        $this->CleanReportsDirectory();
        $this->CleanTestData();
        $this->CreateTestRun($env, $suite);
        if ($suite == "api") {
            $json = json_decode(file_get_contents("APIConfig.json"), TRUE);

        } else {
            $json = json_decode(file_get_contents("AcceptanceConfig.json"), TRUE);
        }
        for ($i = 0; $i < count($json['Directory']); $i++) {
            $this->parallelRun($suite, $env, $json['Directory'][$i]['instances'], $json['Directory'][$i]['Dir']);
        }

        $this->mergeReportsFromReportsFolder($suite);


    }

    public function mergeReportsFromReportsFolder($suite)
    {
        $this->mergeXMLReportsFromReportFolder($suite);
        $this->mergeHTMLReportsFromReportFolder($suite);
    }

    public function mergeXMLReportsFromReportFolder($suite)
    {
        $mergeXML = $this->taskMergeXmlReports();
        $dh = opendir("tests/Reports");

        while ($file = readdir($dh)) {
            if (strpos($file, ".xml") != false) {
                if (file_get_contents("tests/Reports/" . $file) == '') {

                    echo "empty report generated for tests/_output/" . $file;
                    echo "\n";
                } else {
                    $mergeXML->from("tests/Reports/" . $file);
                }
            }
        }

        if ($suite == "acceptance") {
            $mergeXML->into("tests/Reports/Complete_acceptance_report.xml")->run();
        }

        if ($suite == "api") {
            $mergeXML->into("tests/Reports/Complete_api_report.xml")->run();
        }

    }

    public function mergeHTMLReportsFromReportFolder($suite)
    {

        $mergeHTML = $this->taskMergeHTMLReports();
        $dh = opendir("tests/Reports");
        while ($file = readdir($dh)) {
            if (strpos($file, ".html") != false) {
                if (file_get_contents("tests/Reports/" . $file) == '') {
                    echo "empty report generated for tests/_output/" . $file;
                    echo "\n";
                } else {
                    $mergeHTML->from("tests/Reports/" . $file);
                }
            }
        }
        if ($suite == "acceptance") {
            $mergeHTML->into("tests/Reports/Complete_acceptance_report.html")->run();
        }
        if ($suite == "api") {
            $mergeHTML->into("tests/Reports/Complete_api_report.html")->run();
        }

    }

    public function CleanOutPutDirectory()
    {
        $files = glob('tests/_output/*'); //
        foreach ($files as $file) { //
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }

    }

    public function CleanReportsDirectory()
    {
        $files = glob('tests/Reports/*'); //
        foreach ($files as $file) { //
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }

    }


    public function IsSubStringPresent($String, $word)
    {
        if (strpos($String, $word) !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function parallelRun($suite, $env, $numberofInstance, $dir)
    {
        if ($numberofInstance == 0) {
            $numberofInstance = 2;
        }
        if ($suite == "api") {
            $this->CleanOutPutDirectory();
            $this->numberofInstance = $numberofInstance;
            $this->Split_Test($suite, $dir);
            $this->writeCommands($suite, $env, $dir);
            $this->RunParallely($suite);
            $this->MergeResults($suite, $dir);
            $this->WriteResultsToCSV($suite);
            $this->PostResult();
        } else {
            $this->CleanOutPutDirectory();
            $this->numberofInstance = $numberofInstance;
            $this->Split_Test($suite, $dir);
            $this->writeCommands($suite, $env, $dir);
            $this->RunParallely($suite);
            $this->MergeResults($suite, $dir);
            $this->DeleteDuplicates();
            $this->WriteResultsToCSV($suite);
            $this->PostResult();

        }

    }

    public function CleanTestData()
    {
        echo "Deleting End Time in Appointment data file";
        echo "\n";
        $jsonData = json_decode(file_get_contents("tests" . DIRECTORY_SEPARATOR . "_data" . DIRECTORY_SEPARATOR . "AppointmentData.json"), true);
        $jsonData['endTime'] = '';
        file_put_contents("tests" . DIRECTORY_SEPARATOR . "_data" . DIRECTORY_SEPARATOR . "AppointmentData.json", json_encode($jsonData, JSON_PRETTY_PRINT));
    }


    public function RunParallely($suite)
    {
        if ($suite == "acceptance") {
            $contentoffile = file_get_contents("Acceptance_commands.txt");
        } else {
            $contentoffile = file_get_contents("API_commands.txt");
        }
        $this->CommandsArray = explode(PHP_EOL, $contentoffile);
        $i = 0;
        while ($i < count($this->CommandsArray) - 1) {
            $this->Run($i);
            $i = $i + ($this->numberofInstance);
        }
    }

    public function writeCommands($suite, $env, $dir)
    {
        if ($suite == "acceptance") {
            $file = fopen("Acceptance_commands.txt", "w+");
        } else {
            $file = fopen("API_commands.txt", "w+");
        }
        for ($i = 0; $i < count($this->name); $i++) {
            $feature = $this->name[$i];
            if ($suite == "acceptance") {
                $command = "bin" . DIRECTORY_SEPARATOR . "codecept run $suite $dir/$feature  --env $env --xml $dir.scenario_report_$i._acceptance.xml --html $dir.scenario_report_$i._acceptance.html" . PHP_EOL;


                fwrite($file, $command);
            } else {
                $command = "bin" . DIRECTORY_SEPARATOR . "codecept run $suite $dir/$feature  --env $env --xml $dir.scenario_report_$i._api.xml --html $dir.scenario_report_$i._api.html" . PHP_EOL;
                fwrite($file, $command);
            }
        }
        fclose($file);
    }

    public function StartSeleniumServer()
    {
        $this->taskExec('java -Dwebdriver.chrome.driver=chromedriver.exe -jar selenium-server-standalone-3.0.1.jar')
            ->background()
            ->run();
    }

    public function CreateTestRun($env, $suite)
    {
        $testRail = new TestRailIntegration();
        $testRail->CreateNewTestRun($env, $suite);
    }

    public function Split_Test($suite = "acceptance", $dir = "Faculty")
    {
        if ($suite == "api") {
            $dh = opendir("tests/api/$dir");
        } else {
            $dh = opendir("tests/acceptance/$dir");
        }

        $i = 0;
        unset($this->name);
        while ($file = readdir($dh)) {
            if (strpos($file, ".feature") != false) {
                $this->name[$i] = $file;
                $i++;

            }
        }
        sort($this->name);

    }

    public function Run($i)
    {
        $parallel = $this->taskParallelExec();
        for ($j = $i; (($j < $i + $this->numberofInstance) && $this->CommandsArray[$j] != null); $j++) {

            $parallel->process($this->CommandsArray[$j]);
        }
        return $parallel->run();
    }

    function MergeResults($suite, $dir)
    {
        $this->mergeXMLReports($suite, $dir);
        $this->mergeHTMLReports($suite, $dir);
    }

    public function mergeXMLReports($suite, $dir)
    {
        $mergeXML = $this->taskMergeXmlReports();
        $dh = opendir("tests/_output");

        while ($file = readdir($dh)) {
            if ((strpos($file, "acceptance.xml") != false) || ((strpos($file, "api.xml") != false))) {
                if (file_get_contents("tests/_output/" . $file) == '') {

                    echo "empty report generated for tests/_output/" . $file;
                    echo "\n";
                } else {
                    $mergeXML->from("tests/_output/" . $file);
                }
            }
        }

        if ($suite == "acceptance") {
            $mergeXML->into("tests/_output/acceptance_report.xml")->run();
        }

        if ($suite == "api") {
            $mergeXML->into("tests/_output/acceptance_report.xml")->run();
        }

        copy("tests/_output/acceptance_report.xml", "tests/Reports/" . $dir . "_acceptance.xml");

    }

    public function mergeHTMLReports($suite, $dir)
    {

        $mergeHTML = $this->taskMergeHTMLReports();
        $dh = opendir("tests/_output");
        while ($file = readdir($dh)) {
            if ((strpos($file, "acceptance.html") != false) || (strpos($file, "api.html") != false)) {
                if (file_get_contents("tests/_output/" . $file) == '') {
                    echo "empty report generated for tests/_output/" . $file;
                    echo "\n";
                } else {
                    $mergeHTML->from("tests/_output/" . $file);
                }
            }
        }
        if ($suite == "acceptance") {
            $mergeHTML->into("tests/_output/acceptance_report.html")->run();
        }
        if ($suite == "api") {
            $mergeHTML->into("tests/_output/acceptance_report.html")->run();
        }


        copy("tests/_output/acceptance_report.html", "tests/Reports/" . $dir . "_acceptance.html");

        $files = glob('tests/_output/*.png'); //
        foreach ($files as $file) {   //
            $array = explode("/", $file);  //
            copy($file, "tests/Reports/" . $array[2]);
        }


    }

    public function WriteResultsToCSV()
    {
        $genrateCSV = new \Helper\GenerateCSV('tests/_output/acceptance_report.xml');
        $genrateCSV->putTestCaseDataIntoCSVFile();
    }

    public function PostResult()
    {
        $file = fopen(realpath(__DIR__) . '/TestResultFiles/TestCaseData' . date("YMD") . ".csv", "r");
        fgetcsv($file);
        while (!feof($file)) {
            $array = fgetcsv($file);
            $Test = new TestRailIntegration();
            if ($array[1] == '') {
                break;
            }
            $Test->postResultsToTestRail(trim($array[1]), trim($array[2]), trim($array[3]));
        }
        fclose($file);
    }

    public function DeleteDuplicates()
    {
        $xmlDoc = new DOMDocument();
        $xmlDoc->load("tests/_output/acceptance_report.xml");
        $results = $xmlDoc->getElementsByTagName('testcase');
        $testsuite = $xmlDoc->getElementsByTagName("testsuite");


        for ($i = 0; $i < $results->length; $i++) {
            $Element = $results->item($i);
            for ($j = $i + 1; $j < $results->length; $j++) {
                if ($results->item($j)->getAttribute("feature") == $Element->getAttribute("feature")) {
                    $results->item($j)->parentNode->removeChild($results->item($j));
                    $testsuite->item(0)->setAttribute("tests", $testsuite->item(0)->getAttribute("tests") - 1);
                }
            }
        }
        file_put_contents("tests/_output/report.xml", $xmlDoc->saveXML());
    }


}
