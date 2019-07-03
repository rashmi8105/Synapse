<?php
use Synapse\ReportsBundle\Service\Impl\GPAReportService;

class RetrieveGpaDataTest extends \Codeception\TestCase\Test
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
    const GPA_ID = 83;

    protected function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->resque = $this->container->get('bcc_resque.resque');
        $this->logger = $this->container->get('logger');
        $this->repositoryResolver = $this->container->get('repository_resolver');
    }



    // tests
    public function testRetrieveGpaDataNormal()
    {
        $orgAcademicYearIds = [57];
        $orgAcademicTermIds = [283];
        $studentFilter = [55, 56];

        $gpaReportService = new GPAReportService($this->repositoryResolver, $this->logger, $this->container, $this->resque);


        try {
            $gpaReportService->retrieveGpaData( self::GPA_ID, $studentFilter,  $orgAcademicYearIds, $orgAcademicTermIds);
        }
        catch(Exception $e){

            $this->assertEmpty($e);
        }

    }

    /*
     * No Student Data should result in a Query that returns Nothing instead of an exception
     */
    public function testRetrieveGpaWithEmptyStudentArray()
    {
        $orgAcademicYearIds = [57];
        $orgAcademicTermIds = [283];
        $studentFilter = [];

        $gpaReportService = new GPAReportService($this->repositoryResolver, $this->logger, $this->container, $this->resque);

        try {

            $gpa =  $gpaReportService->retrieveGpaData( self::GPA_ID, $studentFilter,  $orgAcademicYearIds, $orgAcademicTermIds);
            $this->assertEquals($gpa, array());
        }
        catch(Exception $e){
            $this->assertEmpty($e);
        }

    }

    /*
     * No Year Data should result in a Query that returns Nothing instead of an exception
     */
    public function testRetrieveGpaWithEmptyAcademicYear()
    {
        $orgAcademicYearIds = [];
        $orgAcademicTermIds = [283];
        $studentFilter = [55, 56];

        $gpaReportService = new GPAReportService($this->repositoryResolver, $this->logger, $this->container, $this->resque);

        try {
            $gpa =  $gpaReportService->retrieveGpaData( self::GPA_ID, $studentFilter,  $orgAcademicYearIds, $orgAcademicTermIds);
            $this->assertEquals($gpa, array());
        }
        catch(Exception $e){
            $this->assertEmpty($e);
        }

    }

    /*
     * No Year Data should result in a Query that returns Nothing instead of an exception
     */
    public function testRetrieveGpaWithEmptyAcademicTerm()
    {
        $orgAcademicYearIds = [57];
        $orgAcademicTermIds = [];
        $studentFilter = [55, 56];

        $gpaReportService = new GPAReportService($this->repositoryResolver, $this->logger, $this->container, $this->resque);

        try {
            $gpa =  $gpaReportService->retrieveGpaData( self::GPA_ID, $studentFilter,  $orgAcademicYearIds, $orgAcademicTermIds);
            $this->assertEquals($gpa, array());
        }
        catch(Exception $e){
            $this->assertEmpty($e);
        }

    }

}