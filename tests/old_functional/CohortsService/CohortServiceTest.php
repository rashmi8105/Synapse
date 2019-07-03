<?php
namespace Synapse\SurveyBundle\Service\Impl;
use Synapse\AcademicBundle\Entity\OrgAcademicYear;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgCohortName;


/**
 * Class CohortServiceTest
 * Functional Tests using organization (2) and North State data.  Any de-identified prod data should allow this
 * data to pass the tests.
 */
class CohortServiceTest extends \Codeception\TestCase\Test
{
    /**
     * @var \FunctionalTester
     */
    protected $tester;
    private $logger;
    private $container;
    private $repositoryResolver;

    protected function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->logger = $this->container->get('logger');
        $this->repositoryResolver = $this->container->get('repository_resolver');
    }


    // tests
    public function testGetCohortForOrganizationNoOrgAcademicYear()
    {
        $cohortService = new CohortsService($this->repositoryResolver,  $this->container, $this->logger);

        $orgId = 2;

        $expectedResult = [
            "org_id" => 2,
            "cohorts" => [
              [
                  "year_id" => "201516",
                  "org_academic_year_id" => "88",
                  "cohort" => "1",
                  "cohort_name" => "Survey Cohort 1"
              ],
              [
                  "year_id" => "201516",
                  "org_academic_year_id" => "88",
                  "cohort" => "2",
                  "cohort_name" => "Survey Cohort 2"
              ],
              [
                  "year_id" => "201516",
                  "org_academic_year_id" => "88",
                  "cohort" => "3",
                  "cohort_name" => "Survey Cohort 3"
              ]
            ]
          ];

        $cohorts = $cohortService->getCohortsForOrganization($orgId);

        $this->assertEquals($expectedResult, $cohorts);
    }

    public function testGetCohortForOrganizationWithOrgAcademicYear()
    {
        $cohortService = new CohortsService($this->repositoryResolver, $this->container, $this->logger);

        $orgId = 2;
        $orgAcademicYearId = 88;

        $expectedResult = [
            "org_id" => 2,
            "cohorts" => [
                [
                    "year_id" => "201516",
                    "org_academic_year_id" => "88",
                    "cohort" => "1",
                    "cohort_name" => "Survey Cohort 1"
                ],
                [
                    "year_id" => "201516",
                    "org_academic_year_id" => "88",
                    "cohort" => "2",
                    "cohort_name" => "Survey Cohort 2"
                ],
                [
                    "year_id" => "201516",
                    "org_academic_year_id" => "88",
                    "cohort" => "3",
                    "cohort_name" => "Survey Cohort 3"
                ]
            ]
        ];

        $cohorts = $cohortService->getCohortsForOrganization($orgId, $orgAcademicYearId);

        $this->assertEquals($expectedResult, $cohorts);
    }

    public function testGetCohortForOrganizationInvalidOrg()
    {
        $cohortService = new CohortsService($this->repositoryResolver, $this->container, $this->logger);

        $orgId = -1;

        $expectedResult = [
            "org_id" => -1,
            "cohorts" => []
        ];

        $cohorts = $cohortService->getCohortsForOrganization($orgId);

        $this->assertEquals($expectedResult, $cohorts);
    }

    public function testCreateOrgCohortNameAlreadyExists()
    {
        $cohortService = new CohortsService($this->repositoryResolver, $this->container, $this->logger);

        $orgId = 2;
        $orgAcademicYearId = 88;
        $cohort = 1;
        $cohortName = "Survey Cohort 1";

        $organizationRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Organization");
        $orgAcademicYearRepository = $this->repositoryResolver->getRepository("SynapseAcademicBundle:OrgAcademicYear");

        $organization = $organizationRepository->find($orgId);
        $orgAcademicYear = $orgAcademicYearRepository->find($orgAcademicYearId);


        $alreadyExists = $cohortService->createOrgCohortName($organization, $orgAcademicYear, $cohort, $cohortName);

        $this->assertEquals(null, $alreadyExists);
    }

    public function testCreateOrgCohortNameDoesNotExist()
    {
        $cohortService = new CohortsService($this->repositoryResolver, $this->container, $this->logger);

        $orgId = 2;
        $orgAcademicYearId = 88;
        $cohort = 5;
        $cohortName = "Survey Cohort 5";

        $newCohortNameExpected = new OrgCohortName();

        $organizationRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Organization");
        $orgAcademicYearRepository = $this->repositoryResolver->getRepository("SynapseAcademicBundle:OrgAcademicYear");
        $orgCohortRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgCohortName");


        $organization = $organizationRepository->find($orgId);
        $orgAcademicYear = $orgAcademicYearRepository->find($orgAcademicYearId);

        $newCohortNameExpected->setOrganization($organization);
        $newCohortNameExpected->setOrgAcademicYear($orgAcademicYear);
        $newCohortNameExpected->setCohort($cohort);
        $newCohortNameExpected->setCohortName($cohortName);

        $newCohortNameCreated = $cohortService->createOrgCohortName($organization, $orgAcademicYear, $cohort, $cohortName);

        $newCohortNameRetrieved = $orgCohortRepository->findBy($this->orgCohortNameRepository->findBy(
            ['organization' => $organization, 'org_academic_year' => $orgAcademicYear, 'cohort' => $cohort]));

        $this->assertEquals($newCohortNameCreated, $newCohortNameRetrieved);
        $this->assertEquals($newCohortNameExpected, $newCohortNameRetrieved);

    }
}