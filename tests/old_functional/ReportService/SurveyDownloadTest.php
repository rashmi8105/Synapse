<?php
require_once 'tests/functional/FunctionalBaseTest.php';
use Synapse\ReportsBundle\Job\SurveyStudentResponseJob;

class SurveyDownloadTest extends FunctionalBaseTest
{

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    private $organizationId = 1;

    private $invalidOrganizationId = - 10;

    private $cohortId = 1;

    private $personId = 1;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->reportsService = $this->container->get('reports_service');
        $this->resque = $this->container->get('bcc_resque.resque');
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testSurveyDataKeyDownloadInvalidOrganization()
    {
        $surveyKey = $this->reportsService->cohortsKeyDownload($this->invalidOrganizationId, $this->cohortId);
        $this->assertSame('{"errors": ["Organization Not Found."],
			"data": [],
			"sideLoaded": []
			}', $surveyKey);
    }

    public function testSurveyDataKeyKeyDownload()
    {
        $surveyKey = $this->reportsService->cohortsKeyDownload($this->organizationId, $this->cohortId);
        $this->assertNotEmpty($surveyKey);
        $this->assertInternalType('array', $surveyKey);
        $this->assertEquals($surveyKey['cohort_id'], $this->cohortId);
        $this->assertNotEmpty($surveyKey['download_key_path']);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testSurveyDataDownloadInvalidOrganization()
    {
        $surveyData = $this->reportsService->cohortsSurveyReport($this->invalidOrganizationId, $this->cohortId, $this->personId);
        $this->assertSame('{"errors": ["Organization Not Found."],
			"data": [],
			"sideLoaded": []
			}', $surveyData);
    }

    public function testSurveyDataDownload()
    {
        $surveyData = $this->reportsService->cohortsSurveyReport($this->organizationId, $this->cohortId, $this->personId);
        $this->assertInternalType('array', $surveyData);
        $this->assertEquals($surveyData, array(
            "You may continue to use Mapworks while your download completes. We will notify you when it is available."
        ));
    }

    public function testCreateStudentSurveyResponseJob()
    {
        $jobNumber = uniqid();
        $job = new SurveyStudentResponseJob();
        $job->args = array(
            'jobNumber' => $jobNumber,
            'personId' => $this->personId,
            'currentDateTime' => date("Y-m-d_H-i-s"),
            'orgId' => $this->organizationId,
            'cohortId' => $this->cohortId,
            'headerColumns' => array(),
            'cohortPerson' => array(
                2,
                3,
                4
            ),
            'surveyArr' => array()
        );
        $jobs = $this->resque->enqueue($job, true);
        \PHPUnit_Framework_Assert::assertInstanceOf('Resque_Job_Status', $jobs);
    }

    public function testRunStudentSurveyResponseJob()
    {
        $jobNumber = uniqid();
        $job = new SurveyStudentResponseJob();
        $job->setKernelOptions([
            'kernel.root_dir' => 'app'
        ]);
        $errors = $job->run([
            'jobNumber' => $jobNumber,
            'personId' => $this->personId,
            'currentDateTime' => date("Y-m-d_H-i-s"),
            'orgId' => $this->organizationId,
            'cohortId' => $this->cohortId,
            'headerColumns' => array(),
            'cohortPerson' => array(
                2,
                3,
                4
            ),
            'surveyArr' => array()
        ]);
        $this->assertEquals(0, count($errors));
    }
}
