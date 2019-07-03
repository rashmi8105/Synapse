<?php
use Codeception\Util\Stub;
use Synapse\SurveyBundle\EntityDto\SurveyBlockDto;

class SurveyQuestionsServiceTest extends \Codeception\TestCase\Test
{

    /**
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     *
     * @var \Synapse\SurveyBundle\Service\Impl\SurveyDashboardService
     */
    private $surveyDashboardService;
    
    
    public $studentId = 6;
    public $surveyId = 1;
    public $markerId = 1;
    public $categoriesId = 1;
    public $filter = "factor";
    public $orgId = 1;
    public $invalidStudentId = -100;
    public $invalidSurveyId = -100;
    public $langId=1;
    public  $facultyId = 1;
    public $cohort =1;
    

  
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->studentQuestonService = $this->container->get('survey_questions_service');
    }
    
    public function testgetISQ(){
        
        $isq = $this->studentQuestonService->getISQ($this->orgId, $this->surveyId, $this->langId, $this->facultyId,'');
        $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\ISQResponseDto", $isq);
        $this->assertEquals($isq->getOrganizationId(), $this->orgId);
        $this->assertEquals($isq->getLangId(), $this->langId);
        $this->assertEquals($isq->getSurveyId(), $this->surveyId);
        foreach($isq->getIsqs() as $isqobj){
            $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\SurveyQuestionsArrayDto", $isqobj);
            $this->assertEquals(1, $isqobj->getId());
        }
        
    }

    public function testGetSurveyCompletionStatus(){
        $data = $this->studentQuestonService->getSurveyCompletionStatus($this->orgId, $this->langId, "Asia");
        $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\SurveyListDetailsDto", $data);
        $this->assertEquals($data->getOrganizationId(), $this->orgId);
        $this->assertEquals($data->getLangId(), $this->langId);
        foreach($data->getSurveys() as $survey){
            $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\SurveysDetailsArrayDto", $survey);
        }
        
    }
    
    public function testGetSurveyCohortQuestions(){
        
        $data = $this->studentQuestonService->getSurveyCohortQuestions($this->orgId, 1,1);
        $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\SurveyQuestionsResponseDto", $data);
        $this->assertEquals($data->getOrganizationId(), $this->orgId);
        $this->assertEquals($data->getSurveyId(), $this->surveyId);
        foreach($data->getSurveyQuestions() as $surveyQues){
            $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\SurveyQuestionsArrayDto", $surveyQues);
        }
    }

    
}