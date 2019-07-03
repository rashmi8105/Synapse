<?php
use Synapse\RestBundle\Entity\SurveyDto;
use Synapse\SurveyBundle\EntityDto\WessLinkDto;

class FactorServiceTest extends \Codeception\TestCase\Test
{



    private $factorId = 1;
    
    private $factorName = "Factor 1";
    private $langId = 1;
    private $orgId = 1;
    private $personId = 1;
    private $surveyId = 1;
  
    
    private $ebiQuesArr =  array(1,2);
    
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->factorService = $this->container->get('factor_service');
     
    }
    
    public function testListFactorOnPermission(){
    
        $factorQuestions = $this->factorService->listFactorOnPermission($this->orgId, $this->personId , $this->surveyId);
        $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\FactorListDto", $factorQuestions);
        $this->assertEquals($this->langId, $factorQuestions->getLangId());
    }
    
    public function testGetFactorQuestions(){
        
        $factorQuestions = $this->factorService->getFactorQuestions($this->factorId);
        $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\FactorQuestionsDto", $factorQuestions);
        $this->assertEquals($this->langId, $factorQuestions->getLangId());
        $this->assertEquals($this->factorId, $factorQuestions->getFactorId());
        $this->assertEquals(2, $factorQuestions->getTotalCount());
        $this->assertEquals($this->factorName, $factorQuestions->getFactorName());
      
        foreach($factorQuestions->getSurveyQuestions() as $surveyQuestion){
           $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\SurveyQuestionsDto", $surveyQuestion);
           $check = in_array($surveyQuestion->getId(),$this->ebiQuesArr);
           $this->assertEquals(true, $check);
       }
    }

    public function testDeleteFactorQuestions(){
        $deletedId = $this->factorService->deleteFactorQuestion($this->factorId, $this->ebiQuesArr[0]);
        $check = is_numeric($deletedId);
        $this->assertEquals(true, $check);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    
    public function testDeleteFactorQuestionsInvalidFactorId(){
        $deletedId = $this->factorService->deleteFactorQuestion(-100, $this->ebiQuesArr[0]);
       
    }
    
    public function testDeleteFactor(){
        $deletedId = $this->factorService->deleteFactor($this->factorId);
        $check = is_numeric($deletedId);
        $this->assertEquals(true, $check);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteInvalidFactor(){
        $deletedId = $this->factorService->deleteFactor(-100);
       
    }
    
} 