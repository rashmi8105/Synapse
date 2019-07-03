<?php
use Synapse\RestBundle\Entity\SurveyDto;
use Synapse\SurveyBundle\EntityDto\WessLinkDto;

class SurveyServiceTest extends \Codeception\TestCase\Test
{

    private $invalidType = "notablock";

    private $surveyData = array();

    private $typeBlock = "block";
    
    private $typeMarker = "marker";
    
    private $orgId = 1;
    
    private $surveyDataInvalidLongitunalID = array(
        "LongitudinalID" => "Q106",
        "SurvID" => "",
        "FactorID" => "",
        "SurveyBlockID" => "456",
        "RedLow" => 1,
        "RedHigh" => 2,
        "YellowLow" => 3,
        "YellowHigh" => 5,
        "GreenLow" => 6,
        "GreenHigh" => 7
    );
    
    private $surveyDataInvalidSurveyBlock = array(
        "LongitudinalID" => "Q107",
        "SurvID" => "",
        "FactorID" => "",
        "SurveyBlockID" => "123",
        "RedLow" => 1,
        "RedHigh" => 2,
        "YellowLow" => 3,
        "YellowHigh" => 5,
        "GreenLow" => 6,
        "GreenHigh" => 7
    );
    
    private $surveyDataInvalidSurveyMarker = array(
        "LongitudinalID" => "Q107",
        "SurvID" => "",
        "FactorID" => "",
        "MarkerID" => "123",
        "RedLow" => 1,
        "RedHigh" => 2,
        "YellowLow" => 3,
        "YellowHigh" => 5,
        "GreenLow" => 6,
        "GreenHigh" => 7
    );
    
    private $surveyDataInvalidSurvey = array(
        "LongitudinalID" => "",
        "SurvID" => "123",
        "FactorID" => "123",
        "SurveyBlockID" => "",
        "RedLow" => 1,
        "RedHigh" => 2,
        "YellowLow" => 3,
        "YellowHigh" => 5,
        "GreenLow" => 6,
        "GreenHigh" => 7
    );
    
    private $surveyDataInvalidFactor = array(
        "LongitudinalID" => "",
        "SurvID" => "123",
        "FactorID" => "123",
        "SurveyBlockID" => "",
        "RedLow" => 1,
        "RedHigh" => 2,
        "YellowLow" => 3,
        "YellowHigh" => 5,
        "GreenLow" => 6,
        "GreenHigh" => 7
    );

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->SurveyService = $this->container->get('survey_service');
        $this->SurveyUploadService = $this->container->get('survey_upload_service');
        $this->studentSurveyService = $this->container->get('studentsurvey_service');
    }
    
    private function setWessLinkDto()
    {
        $wessLinkDto = new WessLinkDto();
        $wessLinkDto->setWessSurveyId(2);
        $wessLinkDto->setWessCohortId(1);
        $wessLinkDto->setWessProdYear(2015);
        $wessLinkDto->setWessOrderId(12);
        $openDate = new DateTime ( "now" );
        $closeDate = clone $openDate;
        $closeDate->add ( new \DateInterval ( 'P7M' ) );
        $wessLinkDto->setOpenDate($openDate);
        $wessLinkDto->setCloseDate($closeDate);
        $wessLinkDto->setStatus("launched");
        $wessLinkDto->setWessLaunchedflag(true);
        return $wessLinkDto;
    }


    public function testViewSurvey()
    {
        $surveyService = $this->SurveyService->viewSurvey();
        $this->assertEquals('100', $surveyService->getSurveyId());
        $this->assertEquals('Fall Transition Survey', $surveyService->getSurveyName());
        $this->assertInternalType('object', $surveyService);
        $this->assertInternalType('object', $surveyService->getSurveyStartDate());
        $this->assertInternalType('object', $surveyService->getSurveyEndDate());
        $this->assertNotNull($surveyService);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testSurveyForInvalidType()
    {
        $serviceResp = $this->SurveyUploadService->saveSurveyBlock($this->invalidType, $this->surveyData);
        $this->assertSame('Invalid Type', $serviceResp);
    }    
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testSurveyForInvalidLongitunalId()
    {
        $serviceResp = $this->SurveyUploadService->saveSurveyBlock($this->typeBlock, $this->surveyDataInvalidLongitunalID);
        $this->assertSame('Invalid Longitunal ID', $serviceResp);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testSurveyForInvalidSurveyBlock()
    {
        $serviceResp = $this->SurveyUploadService->saveSurveyBlock($this->typeBlock, $this->surveyDataInvalidSurveyBlock);
        $this->assertSame('Invalid Survey Block', $serviceResp);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testSurveyForInvalidSurveyMarker()
    {
        $serviceResp = $this->SurveyUploadService->saveSurveyBlock($this->typeMarker, $this->surveyDataInvalidSurveyMarker);
        $this->assertSame('Invalid Survey Marker ID', $serviceResp);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testSurveyForInvalidSurvey()
    {
        $serviceResp = $this->SurveyUploadService->saveSurveyBlock($this->typeBlock, $this->surveyDataInvalidSurvey);
        $this->assertSame('Invalid Survey', $serviceResp);
    }    
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testSurveyForInvalidFactor()
    {
        $serviceResp = $this->SurveyUploadService->saveSurveyBlock($this->typeBlock, $this->surveyDataInvalidFactor);
        $this->assertSame('Invalid Factor', $serviceResp);
    }

    public function testGetSurveysCohorts()
    {
        $cohorts = $this->SurveyService->getSurveysCohorts($this->orgId);
        $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\SurveyCohortResponseDto", $cohorts);
        $this->assertInternalType('array', $cohorts->getSurveyCohorts());
        if(!empty($cohorts->getSurveyCohorts())){
            $this->assertNotEmpty($cohorts->getSurveyCohorts()[0]->getCohortName());
        }
    }
    
    public function testEditWessLink()
    {
        $wessDto = $this->setWessLinkDto();
        $wessLink = $this->SurveyService->editWessLink($wessDto);
        $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\WessLinkDto", $wessLink);
        $this->assertEquals($wessDto->getWessSurveyId(), $wessLink->getWessSurveyId());
        $this->assertEquals($wessDto->getWessCohortId(), $wessLink->getWessCohortId());
        $this->assertEquals($wessDto->getWessProdYear(), $wessLink->getWessProdYear());
        $this->assertEquals($wessDto->getWessOrderId(), $wessLink->getWessOrderId());
    }   

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditWessLinkInvalid()
    {
        $wessDto = $this->setWessLinkDto();
        $wessDto->setWessSurveyId(-1);
        $wessLink = $this->SurveyService->editWessLink($wessDto);
    }
    
    
    public function testlistStudentsSurveysData()
    {
        $surveys = $this->studentSurveyService->listStudentsSurveysData(6,'list');
        foreach($surveys as $survey){
            $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\StudentSurveyDetailsDto", $survey);
        }
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testlistStudentsSurveysDataInvalid()
    {
        $surveys = $this->studentSurveyService->listStudentsSurveysData(-1,'list');
        $this->assertSame('Invalid Student Id', $surveys);
        
    }
    
    public function testlistStudentsSurveysDataWithReport()
    {
        $surveys = $this->studentSurveyService->listStudentsSurveysData(6,'report');
        foreach($surveys as $survey){
            $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\StudentSurveyDetailsDto", $survey);
        }
    }
    
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testlistStudentsSurveysDataInvalidType()
    {
        $surveys = $this->studentSurveyService->listStudentsSurveysData(6,'Invalid List');
        $this->assertSame('Invalid list type', $surveys);
    
    }
} 