<?php
namespace Synapse\SurveyBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Entity\OrgAcademicYear;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgCohortName;
use Synapse\CoreBundle\Repository\OrgCohortNameRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentCohortRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\SurveyBundle\Repository\OrgPersonStudentSurveyLinkRepository;
use Synapse\SurveyBundle\Repository\WessLinkRepository;

/**
 * @DI\Service("cohorts_service")
 */
class CohortsService extends AbstractService
{

    const SERVICE_KEY = 'cohorts_service';

    const NO_SURVEY_RESPONSES_MESSAGE = 'There are no cohorts to select, because no students in the cohorts have survey responses for the selected academic year and retention tracking group.';

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrgCohortNameRepository
     */
    private $orgCohortNameRepository;

    /**
     * @var OrgPersonStudentCohortRepository
     */
    private $orgPersonStudentCohortRepository;

    /**
     * @var OrgPersonStudentRetentionTrackingGroupRepository
     */
    private $orgPersonStudentRetentionTrackingGroupRepository;

    /**
     * @var OrgPersonStudentSurveyLinkRepository
     */
    private $orgPersonStudentSurveyLinkRepository;

    /**
     * @var ReportsRepository
     */
    private $reportsRepository;

    /**
     * @var WessLinkRepository
     */
    private $wessLinkRepository;

    /**
     * @var Container
     */
    private $container;


    /**
     * CohortService Constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger")
     *            })
     * @param $repositoryResolver
     * @param $container
     * @param $logger
     */
    public function __construct($repositoryResolver, $container, $logger)
    {
        parent::__construct($repositoryResolver, $logger);

        // Scaffolding
        $this->container = $container;

        // Services
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);

        // Repositories
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->orgCohortNameRepository = $this->repositoryResolver->getRepository(OrgCohortNameRepository::REPOSITORY_KEY);
        $this->orgPersonStudentCohortRepository = $this->repositoryResolver->getRepository(OrgPersonStudentCohortRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRetentionTrackingGroupRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY);
        $this->orgPersonStudentSurveyLinkRepository = $this->repositoryResolver->getRepository(OrgPersonStudentSurveyLinkRepository::REPOSITORY_KEY);
        $this->reportsRepository = $this->repositoryResolver->getRepository(ReportsRepository::REPOSITORY_KEY);
        $this->wessLinkRepository = $this->repositoryResolver->getRepository(WessLinkRepository::REPOSITORY_KEY);
    }


    /**
     * Returns data about surveys, including number of students who have responded, for the given organization,
     * grouped by cohort and academic year.
     * This list only includes cohort-survey combinations for which the students in the cohort have been assigned the survey.
     * Besides $orgId, the rest of the parameters are optional and are used to filter the data returned.
     * The $retentionTrackOrgAcademicYearId parameter is used to pre-filter the students included to those in the selected retention track.
     *
     * @param int $organizationId
     * @param int|null $orgAcademicYearId
     * @param array|null $status - survey status: an array containing "launched" or "closed" or both
     * @param boolean|null $hasResponses
     * @param int|null $retentionTrackOrgAcademicYearId
     * @return array
     */
    public function getCohortsAndSurveysForOrganizationForReporting($organizationId, $orgAcademicYearId, $status, $hasResponses, $retentionTrackOrgAcademicYearId)
    {
        // If we're restricting the surveys to an academic year, convert this to a year_id identifier, such as 201516, for use in the query.
        if (!empty($orgAcademicYearId)) {
            $yearId = $this->orgAcademicYearRepository->find($orgAcademicYearId)->getYearId()->getId();
        } else {
            $yearId = null;
        }

        // Get the needed data from the database, including counts of students who have and have not responded, for each cohort-survey combination.
        // If $retentionTrackYearId is set, only include students from that retention track.  Otherwise, include all students from the organization.
        if (!empty($retentionTrackOrgAcademicYearId)) {
            $retentionTrackStudents = $this->orgPersonStudentRetentionTrackingGroupRepository->getRetentionTrackingGroupStudents($organizationId, $retentionTrackOrgAcademicYearId);
            $rawCohortSurveyData = $this->orgPersonStudentSurveyLinkRepository->getCohortsAndSurveysForOrganizationForReporting($organizationId, $yearId, $status, $hasResponses, $retentionTrackStudents);
        } else {
            $rawCohortSurveyData = $this->orgPersonStudentSurveyLinkRepository->getCohortsAndSurveysForOrganizationForReporting($organizationId, $yearId, $status, $hasResponses);
        }

        // As a first pass at formatting the data, sort the database records into a 3-dimensional array,
        // where the first two dimensions are year and cohort.
        $dataGroupedByYearAndCohort = [];

        foreach ($rawCohortSurveyData as $record) {
            $dataGroupedByYearAndCohort[$record['year_id']][$record['cohort']][] = $record;
        }

        // Then iterate over each of these dimensions to format the data as desired.
        $cohortDataToReturn = [];

        foreach ($dataGroupedByYearAndCohort as $yearId => $dataGroupedByYear) {
            $orgAcademicYearId = $this->orgAcademicYearRepository->findOneBy(['organization' => $organizationId, 'yearId' => $yearId])->getId();
            foreach ($dataGroupedByYear as $cohort => $dataGroupedByCohort) {
                $recordToReturn = [];
                $recordToReturn['year_id'] = $yearId;
                $recordToReturn['org_academic_year_id'] = $orgAcademicYearId;
                $recordToReturn['cohort'] = $cohort;
                $recordToReturn['cohort_name'] = $dataGroupedByCohort[0]['cohort_name'];

                $surveyData = [];

                foreach ($dataGroupedByCohort as $record) {
                    // If some students have responded to a survey and some haven't, there will be two raw database records for the survey,
                    // with the 'Has_Responses' = 'Yes' record first.  In this case, there's no need to include this 'Has_Responses' = 'No' record.
                    // On the other hand, if there was no 'Has_Responses' = 'Yes' record, the survey will still be included below.
                    if ($record['Has_Responses'] == 'No') {
                        if (!empty($surveyData)) {
                            $lastSurveyId = $surveyData[count($surveyData) - 1]['survey_id'];
                            if ($lastSurveyId == $record['survey_id']) {
                                continue;
                            }
                        }
                    }

                    $surveyRecord = [];
                    $surveyRecord['survey_id'] = (int) $record['survey_id'];
                    $surveyRecord['survey_name'] = $record['survey_name'];
                    $surveyRecord['status'] = $record['status'];
                    $surveyRecord['open_date'] = $this->dateUtilityService->convertDatabaseStringToISOString($record['open_date']);
                    $surveyRecord['close_date'] = $this->dateUtilityService->convertDatabaseStringToISOString($record['close_date']);

                    if ($record['Has_Responses'] == 'Yes') {
                        $surveyRecord['students_responded_count'] = (int) $record['student_count'];
                    } else {
                        $surveyRecord['students_responded_count'] = 0;
                    }

                    $surveyData[] = $surveyRecord;
                }

                $recordToReturn['surveys'] = $surveyData;

                $cohortDataToReturn[] = $recordToReturn;
            }
        }

        $dataToReturn = [];
        $dataToReturn['org_id'] = $organizationId;

        if (!empty($cohortDataToReturn)) {
            $dataToReturn['cohorts'] = $cohortDataToReturn;
        } else {
            $dataToReturn['message'] = self::NO_SURVEY_RESPONSES_MESSAGE;
        }

        return $dataToReturn;
    }


    /**
     * Returns data about surveys for the given organization, grouped by cohort and academic year.
     * If $purpose is "isq", only returns cohort and survey combinations which have ISQs.
     * If $purpose is "survey_setup", data includes wess links.
     *
     * @param int $organizationId
     * @param string $purpose - "isq" or "survey_setup"
     * @param int|null $orgAcademicYearId
     * @param string $status - Description of this parameter, survey status launched,closed.
     * @return array
     */
    public function getCohortsAndSurveysForOrganizationForSetup($organizationId, $purpose, $orgAcademicYearId = null, $status = null)
    {
        // Get the needed data from the database.
        $rawCohortSurveyData = $this->wessLinkRepository->getCohortsAndSurveysForOrganizationForSetup($organizationId, $purpose, $orgAcademicYearId, $status);

        // As a first pass at formatting the data, sort the database records into a 3-dimensional array,
        // where the first two dimensions are year and cohort.
        $dataGroupedByYearAndCohort = [];

        foreach ($rawCohortSurveyData as $record) {
            $dataGroupedByYearAndCohort[$record['year_id']][$record['cohort']][] = $record;
        }

        // Then iterate over each of these dimensions to format the data as desired.
        $cohortDataToReturn = [];

        foreach ($dataGroupedByYearAndCohort as $yearId => $dataGroupedByYear) {
            foreach ($dataGroupedByYear as $cohort => $dataGroupedByCohort) {
                $recordToReturn = [];
                $recordToReturn['year_id'] = $yearId;
                $recordToReturn['org_academic_year_id'] = $dataGroupedByCohort[0]['org_academic_year_id'];
                $recordToReturn['year_name'] = $dataGroupedByCohort[0]['year_name'];
                $recordToReturn['cohort'] = $cohort;
                $recordToReturn['cohort_name'] = $dataGroupedByCohort[0]['cohort_name'];

                $surveyData = [];

                foreach ($dataGroupedByCohort as $record) {
                    $surveyRecord = [];
                    $surveyRecord['survey_id'] = (int) $record['survey_id'];
                    $surveyRecord['survey_name'] = $record['survey_name'];
                    $surveyRecord['status'] = $record['status'];
                    $surveyRecord['open_date'] = $this->dateUtilityService->convertDatabaseStringToISOString($record['open_date']);
                    $surveyRecord['close_date'] = $this->dateUtilityService->convertDatabaseStringToISOString($record['close_date']);

                    if ($purpose == 'survey_setup') {
                        $surveyRecord['wess_admin_link'] = $record['wess_admin_link'];
                    }

                    $surveyData[] = $surveyRecord;
                }

                $recordToReturn['surveys'] = $surveyData;

                $cohortDataToReturn[] = $recordToReturn;
            }
        }

        $dataToReturn = [];
        $dataToReturn['org_id'] = $organizationId;
        $dataToReturn['cohorts'] = $cohortDataToReturn;

        return $dataToReturn;
    }


    /**
     * Returns data about cohorts by org academic years and org id
     * This list only includes cohorts which students are assigned
     *
     * @param int $orgId
     * @param array|null $orgAcademicYearIds
     * @return array
     */
    public function getCohortsForOrganization($orgId, $orgAcademicYearIds = [])
    {
        $organizationCohorts = [];
        $organizationCohorts['org_id'] = $orgId;
        $organizationCohorts['cohorts'] = $this->orgPersonStudentCohortRepository->getCohortsByOrganization($orgId, $orgAcademicYearIds);
        return $organizationCohorts;
    }


    /**
     * Creates new Cohort if it does not exist
     *
     * @param Organization $organization
     * @param OrgAcademicYear $orgAcademicYear
     * @param int $cohort
     * @param string $cohortName
     * @return OrgCohortName|null $cohortNameEntity
     */
    public function createOrgCohortName($organization, $orgAcademicYear, $cohort, $cohortName)
    {
        $existingOrgCohortName = null;
        $orgCohortName = null;

        $existingOrgCohortName = $this->orgCohortNameRepository->findOneBy(
            ['organization' => $organization, 'orgAcademicYear' => $orgAcademicYear, 'cohort' => $cohort]);

        if(!empty($existingOrgCohortName)){
            $this->logger->addAlert('OrgCohortName Not Created.  It already exists');
            $orgCohortName = $existingOrgCohortName;
        }else{
            $orgCohortName = new OrgCohortName();
            $orgCohortName->setOrganization($organization);
            $orgCohortName->setOrgAcademicYear($orgAcademicYear);
            $orgCohortName->setCohort($cohort);
            $orgCohortName->setCohortName($cohortName);

            $this->orgCohortNameRepository->persist($orgCohortName, $flush = true);
            $this->logger->info("OrgCohortName - OrgCohortName Created ");

        }
        return $orgCohortName;

    }

    /**
     * Delete Cohort if it exists
     *
     * @param Organization $organization
     * @param OrgAcademicYear $orgAcademicYear
     * @param int $cohort
     * @return OrgCohortName|null $cohortNameEntity
     */
    public function deleteOrgCohortName($organization, $orgAcademicYear, $cohort)
    {
        $orgCohortName = null;

        $orgCohortName = $this->orgCohortNameRepository->findOneBy(
            ['organization' => $organization, 'orgAcademicYear' => $orgAcademicYear, 'cohort' => $cohort]);

        if(!empty($orgCohortName)){
            $this->orgCohortNameRepository->delete($orgCohortName, $flush = true);
        }else {
            $this->logger->addAlert('No Cohort Names Associated with this year and cohort');
        }

        return $orgCohortName;
    }

}