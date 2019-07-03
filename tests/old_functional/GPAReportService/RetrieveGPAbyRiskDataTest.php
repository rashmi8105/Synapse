<?php
use Synapse\ReportsBundle\Service\Impl\GPAReportService;

class RetrieveGPAbyRiskDataTest extends \Codeception\TestCase\Test
{
    /**
     * @var \FunctionalTester
     */
    protected $tester;
    private $logger;
    private $container;
    private $repositoryResolver;
    private $resque;

    //Constants
    const ORG_ID = 1;
    const RISK_CALCULATION_START = "2015-09-09 12:12:12";
    const RISK_CALCULATION_END = "2015-12-09 12:12:12";
    const GPA_ID = 83;

    protected function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->resque = $this->container->get('bcc_resque.resque');
        $this->logger = $this->container->get('logger');
        $this->repositoryResolver = $this->container->get('repository_resolver');
    }

    protected function _after()
    {
    }

    /*
     * This test looks at a normal data case irregardless of it being the right data passed and see if it generates an exeception
     */
    public function testRetrieveGpabyRiskDataNormal()
    {
        $orgAcademicYearIds = [57];
        $orgAcademicTermIds = [283];
        $studentFilter = [55, 56];

        $gpaReportService = new GPAReportService($this->repositoryResolver, $this->logger, $this->container, $this->resque);


        try {
            $gpaReportService->retrieveGpaByRiskData(self::ORG_ID, self::RISK_CALCULATION_START, self::RISK_CALCULATION_END, $orgAcademicYearIds, $orgAcademicTermIds, self::GPA_ID, $studentFilter);
        }
        catch(Exception $e){

            $this->assertEmpty($e);
        }

    }

    /*
     * No Student Data should result in a Query that returns Nothing instead of an exception
     */
    public function testRetrieveGpabyRiskDataWithEmptyStudentArray()
    {
        $orgAcademicYearIds = [57];
        $orgAcademicTermIds = [283];
        $studentFilter = [];

        $gpaReportService = new GPAReportService($this->repositoryResolver, $this->logger, $this->container, $this->resque);

        try {

            $gpaWithRisk = $gpaReportService->retrieveGpaByRiskData(self::ORG_ID, self::RISK_CALCULATION_START, self::RISK_CALCULATION_END, $orgAcademicYearIds, $orgAcademicTermIds, self::GPA_ID, $studentFilter);
            $this->assertEquals($gpaWithRisk, array());
        }
        catch(Exception $e){
            $this->assertEmpty($e);
        }

    }

    /*
     * No Year Data should result in a Query that returns Nothing instead of an exception
     */
    public function testRetrieveGpabyRiskDataWithEmptyAcademicYear()
    {
        $orgAcademicYearIds = [];
        $orgAcademicTermIds = [283];
        $studentFilter = [55, 56];

        $gpaReportService = new GPAReportService($this->repositoryResolver, $this->logger, $this->container, $this->resque);

        try {
            $gpaWithRisk = $gpaReportService->retrieveGpaByRiskData(self::ORG_ID, self::RISK_CALCULATION_START, self::RISK_CALCULATION_END, $orgAcademicYearIds, $orgAcademicTermIds, self::GPA_ID, $studentFilter);
            $this->assertEquals($gpaWithRisk, array());
        }
        catch(Exception $e){
            $this->assertEmpty($e);
        }

    }

    /*
     * No Year Data should result in a Query that returns Nothing instead of an exception
     */
    public function testRetrieveGpabyRiskDataWithEmptyAcademicTerm()
    {
        $orgAcademicYearIds = [57];
        $orgAcademicTermIds = [];
        $studentFilter = [55, 56];

        $gpaReportService = new GPAReportService($this->repositoryResolver, $this->logger, $this->container, $this->resque);

        try {
            $gpaWithRisk = $gpaReportService->retrieveGpaByRiskData(self::ORG_ID, self::RISK_CALCULATION_START, self::RISK_CALCULATION_END, $orgAcademicYearIds, $orgAcademicTermIds, self::GPA_ID, $studentFilter);
            $this->assertEquals($gpaWithRisk, array());
        }
        catch(Exception $e){
            $this->assertEmpty($e);
        }

    }
}