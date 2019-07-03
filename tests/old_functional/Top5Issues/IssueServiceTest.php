<?php
use Codeception\Util\Stub;
use Synapse\SurveyBundle\Entity\Issue;
use Synapse\SurveyBundle\Entity\IssueLang;
use Synapse\SurveyBundle\Entity\IssueOptions;
use Synapse\SurveyBundle\EntityDto\IssuesListDto;
use Synapse\SurveyBundle\EntityDto\IssueCreateFactorDto;
use Synapse\SurveyBundle\EntityDto\IssueCreateQuestionsDto;
use Synapse\SurveyBundle\EntityDto\IssueCreateQuesOptionsDto;
use Synapse\SurveyBundle\EntityDto\IssueCreateDto;
use Synapse\SurveyBundle\EntityDto\Top5IssuesDto;
use Synapse\CoreBundle\Util\Constants\SurveyConstant;
use Synapse\RestBundle\Entity\TotalStudentsListDto;
use Synapse\RestBundle\Entity\PersonDTO;

class IssueServiceTest extends \Codeception\TestCase\Test
{
	/**
     * @var UnitTester
     */
    protected $tester;
    
    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;
    
    /**
     * @var \Synapse\SurveyBundle\Service\IssueService
     */
    private $issueService;
    
    private $userId = 1;
	
	private $lang = 1;
	
	private $surveyId = 1;
	
	private $coursesIcon = "dash-module-issue-courses-icon.png";
	
	private $classIcon = "dash-module-issue-classes-icon.png";
    
    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->issueService = $this->container
        ->get('issue_service');
    }

    public function testCreateIssueByFactor()
    {
		$issueName = "Issue-Factor" . rand(1, 100);
        $issueDto = $this->createIssueDto('factor',$issueName);
        $issue = $this->issueService->createIssue($issueDto);
        $this->assertEquals($issueName, $issue->getIssueName());
        $this->assertGreaterThan(0, $issue->getId());
       
    }
    
    public function testCreateIssueByQuestion()
    {
    	$issueName = "Issue-Question" . rand(601, 700);
    	$issueDto = $this->createIssueDto('question',$issueName,'','category');
    	$issue = $this->issueService->createIssue($issueDto);
    	$this->assertEquals($issueName, $issue->getIssueName());
    	$this->assertGreaterThan(0, $issue->getId());
    	 
    }

    public function testEditIssueByFactor()
    {
       $issueName = "Issue-Factor" . rand(101, 200);
       $issueDto = $this->createIssueDto('factor',$issueName);
       $issue = $this->issueService->createIssue($issueDto);
       
	   $issueDto = $this->createIssueDto('factor',$issueName,$issue->getId());
       $issueUpdate = $this->issueService->editIssue($issueDto);
       $this->assertEquals($issueName, $issueUpdate->getIssueName());
       $this->assertEquals($issue->getId(), $issueUpdate->getId());
       $this->assertEquals($this->coursesIcon, $issueUpdate->getIssueImage());
       
    }
    
    public function testEditIssueByQuestion()
    {
    	$issueName = "Issue-Question" . rand(601, 700);
    	$issueDto = $this->createIssueDto('question',$issueName,'','category');
    	$issue = $this->issueService->createIssue($issueDto);
    	 
    	$issueDto = $this->createIssueDto('question',$issueName,$issue->getId(),'category');
    	$issueUpdate = $this->issueService->editIssue($issueDto);
    	$this->assertEquals($issueName, $issueUpdate->getIssueName());
    	$this->assertEquals($issue->getId(), $issueUpdate->getId());
    	$this->assertEquals($this->coursesIcon, $issueUpdate->getIssueImage());
    	 
    }
    
    public function testGetIssueByIdOfFactor()
    {
        $issueName = "Issue-Factor" . rand(201, 300);
        $issueDto = $this->createIssueDto('factor',$issueName);
        $issue = $this->issueService->createIssue($issueDto);
		
        $issueFound = $this->issueService->getIssue($issue->getId());
        
        $this->assertEquals($issueName,$issueFound[0]->getIssueName());
        $this->assertEquals($this->lang, $issueFound[0]->getlangId());
        
        $this->assertEquals($issue->getId(), $issueFound[0]->getId());
        $this->assertEquals($this->surveyId,$issueFound[0]->getSurveyId());
		$this->assertEquals($this->classIcon, $issueFound[0]->getIssueImage());
        $this->assertObjectHasAttribute('factors', $issueFound[0]);

    }
    
    public function testGetIssueByIdOfQuestion()
    {
    	$issueName = "Issue-Question" . rand(201, 300);
    	$issueDto = $this->createIssueDto('question',$issueName,'','category');
    	$issue = $this->issueService->createIssue($issueDto);
    
    	$issueFound = $this->issueService->getIssue($issue->getId());
    
    	$this->assertEquals($issueName,$issueFound[0]->getIssueName());
    	$this->assertEquals($this->lang, $issueFound[0]->getlangId());
    
    	$this->assertEquals($issue->getId(), $issueFound[0]->getId());
    	$this->assertEquals($this->surveyId,$issueFound[0]->getSurveyId());
    	$this->assertEquals($this->classIcon, $issueFound[0]->getIssueImage());
    	$this->assertObjectHasAttribute('questions', $issueFound[0]);
    
    }
    
    public function testGetIssuesBySurveyId()
    {
    	$issueName = "Issue-Factor" . rand(301, 400);
    	$issueDto = $this->createIssueDto('factor',$issueName);
    	$issue = $this->issueService->createIssue($issueDto);
    	$issueFound = $this->issueService->listIssues($this->surveyId);
    	$this->assertEquals($issueName,$issueFound[0]->getTopIssueName());
    	$this->assertEquals($this->classIcon, $issueFound[0]->getTopIssueImage());
    	$this->assertObjectHasAttribute('id', $issueFound[0]);
    	$this->assertObjectHasAttribute('percentage', $issueFound[0]);
    }
    
    public function testGetIssues()
    {
    	$issueName = "Issue-Factor" . rand(401, 500);
    	$issueDto = $this->createIssueDto('factor',$issueName);
    	$issue = $this->issueService->createIssue($issueDto);
    	$issueFound = $this->issueService->listIssues();
    	$this->assertEquals($issueName,$issueFound[0]->getTopIssueName());
    	$this->assertEquals($this->classIcon, $issueFound[0]->getTopIssueImage());
    	$this->assertObjectHasAttribute('id', $issueFound[0]);
    	$this->assertObjectHasAttribute('percentage', $issueFound[0]);
    }
    
    public function testDeleteIssue(){
        $issueName = "Issue-Factor" . rand(501, 600);
        $issueDto = $this->createIssueDto('factor',$issueName);
        $issue = $this->issueService->createIssue($issueDto);
        $this->issueService->deleteIssue($issue->getId());
    }
    
   
    private function createIssueDto ($type = 'factor',$issueName = "Functional_Test_Issue",$updateId = null,$questionType = null)
    {
        $issueDto = new IssueCreateDto();
        $issueDto->setLangId($this->lang);
        $issueDto->setIssueName($issueName);
        $issueDto->setSurveyId($this->surveyId);
        $issueDto->setIssueImage($this->classIcon);

		if($type == 'factor'){
		    $factorDto = new IssueCreateFactorDto();
		    if($updateId){
    			$factorDto->setId(2);
                $factorDto->setRangeMin(2);
                $factorDto->setRangeMax(8);
                $factorDto->setText("Factor 2");
		    }else{
		        $factorDto->setId(1);
		        $factorDto->setRangeMin(1);
		        $factorDto->setRangeMax(10);
		        $factorDto->setText("Factor 1");
		    }
            
            $issueDto->setFactors($factorDto);
		}else{
			// question
            $questionDto = new IssueCreateQuestionsDto();
            if($updateId){
                $questionDto->setId(9);
                $questionDto->setType($questionType);
                
                if($questionType=='category'){
                	$optionSet = new IssueCreateQuesOptionsDto();
                	$optionSet->setId(9);
                	$optionSet->setValue("2");
                	$optionSet->setText("(2)");
                }
            }else{
                $questionDto->setId(8);
                $questionDto->setType($questionType);
    
    			if($questionType=='category'){
    				$optionSet = new IssueCreateQuesOptionsDto();
    				$optionSet->setId(1);
    				$optionSet->setValue("1");
    				$optionSet->setText("(1) Not at all");
    			}
            }
			$questionDto->setOptions($optionSet);
			$issueDto->setQuestions($questionDto);
		}
		
        if ($updateId)
        {
			
			$issueDto->setId($updateId);
            $issueDto->setIssueImage($this->coursesIcon);
        }
       
        return $issueDto;
    }
    
    
}
