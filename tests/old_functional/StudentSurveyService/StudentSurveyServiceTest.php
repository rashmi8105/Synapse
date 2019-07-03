<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;

class StudentSurveyServiceTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\CoreBundle\Service\Impl\SurveyService
     */
    private $surveyService;

    /**
     *
     * @var \Synapse\SurveyBundle\Service\Impl\SurveyCompareService
     */
    private $surveyCompareService;

    private $personStudentId = 6;

    private $invalidStudentId = - 1;

    private $organizationId = 1;

    private $surveyId = 1;

    private $invalidSurveyId = - 11;

    private $surveyIds = [1, 2];

    private $facultyId = 1;
    
    private $loggedInUser = 1;

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->surveyService = $this->container->get('survey_service');
        $this->surveyCompareService = $this->container->get('survey_compare_service');
    }
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->loggedInUser);
    }

    public function testListStudentISQQuestions()
    {
        $studentIsqs = $this->surveyService->listStudentISQQuestions($this->surveyId, $this->personStudentId, $this->organizationId,$this->facultyId);
        $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\StudentIsqQuesResponseDto", $studentIsqs);
        $this->assertEquals($this->surveyId, $studentIsqs->getSurveyId());
        $this->assertObjectHasAttribute("surveyId", $studentIsqs);
        $this->assertObjectHasAttribute("surveyName", $studentIsqs);
        $this->assertObjectHasAttribute("isqData", $studentIsqs);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testListStudentISQQuestionsInvalidStudentId()
    {
        $studentIsqs = $this->surveyService->listStudentISQQuestions($this->surveyId, $this->invalidStudentId, $this->organizationId,$this->facultyId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testListStudentISQQuestionsInvalidSurveyId()
    {
        $studentIsqs = $this->surveyService->listStudentISQQuestions($this->invalidSurveyId, $this->personStudentId, $this->organizationId,$this->facultyId);
    }

    public function testListStudentSurveysCompare()
    {
        //$this->markTestSkipped("Errored");
        $this->initializeRbac();
        $surveyCompare = $this->surveyCompareService->listStudentSurveysCompare($this->surveyIds, $this->personStudentId, $this->organizationId, $this->loggedInUser);
        $this->assertInternalType('array', $surveyCompare['survey_comparison']);
        $dtoResponse = $surveyCompare['survey_comparison'][0];
        $dtoResponseArr = $surveyCompare['survey_comparison'][0]->getResponse()[0];
        $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\SurveyCompResponseDto", $dtoResponse);
        $this->assertObjectHasAttribute("questionText", $dtoResponse);
        $this->assertObjectHasAttribute("response", $dtoResponse);
        $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\StudentSurveyResArrayDto", $dtoResponseArr);
        $this->assertObjectHasAttribute("responseText", $dtoResponseArr);
        $this->assertObjectHasAttribute("responseDecimal", $dtoResponseArr);
        $this->assertObjectHasAttribute("surveyId", $dtoResponseArr);
        $this->assertObjectHasAttribute("surveyDate", $dtoResponseArr);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testListStudentSurveysCompareInvalidStudentId()
    {
        $surveyCompare = $this->surveyCompareService->listStudentSurveysCompare($this->surveyIds, $this->invalidStudentId, $this->organizationId, $this->loggedInUser);
    }

}
