<?php
namespace Synapse\ReportsBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\OrgCohortNameRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\Job\ActivityReportJob;
use Synapse\ReportsBundle\Job\ReportJob;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionRepository;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\RiskBundle\Repository\PersonRiskLevelHistoryRepository;
use Synapse\RiskBundle\Repository\RiskLevelsRepository;
use Synapse\SearchBundle\Repository\IntentToLeaveRepository;
use Synapse\SurveyBundle\Repository\PersonFactorCalculatedRepository;
use Synapse\SurveyBundle\Repository\WessLinkRepository;


/**
 * @DI\Service("executive_summary_service")
 */
class ExecutiveSummaryService extends AbstractService
{

    const SERVICE_KEY = 'executive_summary_service';

    const NO_STUDENTS_IN_SECTION_MESSAGE = 'No students found.';

    const NULL_CORRELATIONS_MESSAGE = 'The data does not allow for correlations to be calculated.';

    const NO_RETENTION_VARIABLES_MESSAGE = 'No retention variables are available.';

    const END_TERM_GPA_KEY = 'EndTermGPA';

    //Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var Serializer
     */
    private $serializer;

    //Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var ActivityReportService
     */
    private $activityReportService;

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationsService;

    /**
     * @var DataProcessingUtilityService
     */
    private $dataProcessingUtilityService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var GPAReportService
     */
    private $GPAReportService;

    /**
     * @var PersistenceRetentionService
     */
    private $persistenceRetentionService;


    // Repositories

    /**
     * @var EbiMetadataRepository
     */
    private $ebiMetadataRepository;

    /**
     * @var IntentToLeaveRepository
     */
    private $intentToLeaveRepository;

    /**
     * @var OrgAcademicTermRepository
     */
    private $orgAcademicTermsRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrgCohortNameRepository
     */
    private $orgCohortNameRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentRetentionRepository
     */
    private $orgPersonStudentRetentionRepository;

    /**
     * @var OrgPersonStudentRetentionTrackingGroupRepository
     */
    private $orgPersonStudentRetentionTrackingGroupRepository;


    /**
     * @var PersonEbiMetaDataRepository
     */
    private $personEbiMetadataRepository;

    /**
     * @var PersonFactorCalculatedRepository
     */
    private $personFactorCalculatedRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var PersonRiskLevelHistoryRepository
     */
    private $personRiskLevelHistoryRepository;

    /**
     * @var ReportsRepository
     */
    private $reportsRepository;

    /**
     * @var ReportsRunningStatusRepository
     */
    private $reportsRunningStatusRepository;

    /**
     * @var RiskLevelsRepository
     */
    private $riskLevelsRepository;

    /**
     * @var WessLinkRepository
     */
    private $wessLinkRepository;

    //job

    /**
     * @var ActivityReportJob
     */
    private $activityReportJob;


    /**
     * ExecutiveSummaryService Constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->serializer = $this->container->get(SynapseConstant::JMS_SERIALIZER_CLASS_KEY);

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->activityReportService = $this->container->get(ActivityReportService::SERVICE_KEY);
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->GPAReportService = $this->container->get(GPAReportService::SERVICE_KEY);
        $this->persistenceRetentionService = $this->container->get(PersistenceRetentionService::SERVICE_KEY);

        //Job
        $this->activityReportJob = new ActivityReportJob();

        // Repositories
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(EbiMetadataRepository::REPOSITORY_KEY);
        $this->intentToLeaveRepository = $this->repositoryResolver->getRepository(IntentToLeaveRepository::REPOSITORY_KEY);
        $this->orgAcademicTermsRepository = $this->repositoryResolver->getRepository(OrgAcademicTermRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->orgCohortNameRepository = $this->repositoryResolver->getRepository(OrgCohortNameRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRetentionRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRetentionRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRetentionTrackingGroupRepository =  $this->repositoryResolver->getRepository(OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY);
        $this->personEbiMetadataRepository = $this->repositoryResolver->getRepository(PersonEbiMetaDataRepository::REPOSITORY_KEY);
        $this->personFactorCalculatedRepository = $this->repositoryResolver->getRepository(PersonFactorCalculatedRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->personRiskLevelHistoryRepository = $this->repositoryResolver->getRepository(PersonRiskLevelHistoryRepository::REPOSITORY_KEY);
        $this->reportsRepository = $this->repositoryResolver->getRepository(ReportsRepository::REPOSITORY_KEY);
        $this->reportsRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsRunningStatusRepository::REPOSITORY_KEY);
        $this->riskLevelsRepository = $this->repositoryResolver->getRepository(RiskLevelsRepository::REPOSITORY_KEY);
        $this->wessLinkRepository = $this->repositoryResolver->getRepository(WessLinkRepository::REPOSITORY_KEY);

    }


    /**
     * initiates Executive Report Job.
     * static function, not Unit Testable
     *
     * @param int $reportInstanceId
     * @param object $reportRunningDto
     */
    public function initiateExecutiveSummaryJob($reportInstanceId, $reportRunningDto)
    {
        $job = new ReportJob();

        $reportService = 'executive_summary_service';

        $job->args = array(
            'reportInstanceId' => $reportInstanceId,
            'reportRunningDto' => serialize($reportRunningDto),
            'service' => $reportService
        );
        $this->resque->enqueue($job, true);
    }


    /**
     * Retrieves and organizes all data needed for the Executive Summary Report.
     * Inserts the resulting JSON into the reports_running_status table in the database.
     *
     * @param int $reportsRunningStatusId
     * @param ReportRunningStatusDto $reportRunningStatusDto
     */
    public function generateReport($reportsRunningStatusId, $reportRunningStatusDto)
    {
        // Get and validate all the filter data we need.
        $loggedUserId = $reportRunningStatusDto->getPersonId();
        $organizationId = $reportRunningStatusDto->getOrganizationId();
        $reportId = $reportRunningStatusDto->getReportId();
        $searchAttributes = $reportRunningStatusDto->getSearchAttributes();

        $reportsRunningStatusObject = $this->reportsRunningStatusRepository->find($reportsRunningStatusId);

        $error = $this->validateFilterSelections($searchAttributes, $organizationId);

        if (!empty($error)) {
            $responseJSON = $this->serializer->serialize($error, 'json');
            $reportsRunningStatusObject->setStatus('F');
            $reportsRunningStatusObject->setResponseJson($responseJSON);
        } else {

            $currentOrgAcademicYearData = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);

            if (empty($currentOrgAcademicYearData)) {
                $currentOrgAcademicYearYearId = null;
            } else {
                $currentOrgAcademicYearYearId = $currentOrgAcademicYearData['year_id'];
            }

            $orgAcademicYearId = $searchAttributes['org_academic_year_id'];
            $orgAcademicTermsIds = $searchAttributes['org_academic_terms_id'];
            $retentionTrackOrgAcademicYearId = $searchAttributes['retention_date']['academic_year_id'];
            $riskStartDate = $searchAttributes['risk_start_date'];
            $riskEndDate = $searchAttributes['risk_end_date'];
            $cohortsAndSurveys = $reportRunningStatusDto->getReportSections()['cohorts'];
            $reportSections = $reportRunningStatusDto->getReportSections()['sections'];

            //Ensuring there are no duplicate terms
            $orgAcademicTermsIds = array_unique($orgAcademicTermsIds);

            $personObject = $this->personRepository->find($loggedUserId);
            $reportsObject = $this->reportsRepository->find($reportId);

            // Get an array of all the students selected for this report via the preliminary filters.
            $filteredStudentIdString = $reportsRunningStatusObject->getFilteredStudentIds();
            $filteredStudentIds = explode(',', $filteredStudentIdString);

            $reportGeneratedTime = $reportsRunningStatusObject->getCreatedAt();
            $reportGeneratedTimestamp = $reportGeneratedTime->getTimestamp();


            // This will keep track on whether or not all sections are set to include
            // retention tracking only
            $applyRetentionTrackingToCount = true;
            $studentCount = 0;

            if (empty($filteredStudentIdString)) {
                $reportData['status_message'] = [
                    'code' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_CODE,
                    'description' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_MESSAGE
                ];
                $this->logger->addWarning(json_encode($reportData['status_message']));
            }else{

                // Change Risk Dates to DateTimes
                $riskStartDateTime = $this->dateUtilityService->convertToUtcDatetime($organizationId, $riskStartDate);
                $riskEndDateTime = $this->dateUtilityService->convertToUtcDatetime($organizationId, $riskEndDate, true);

                $yearId = $this->orgAcademicYearRepository->find($orgAcademicYearId)->getYearId()->getId();


                // Find which retention variables should be included, based on the academic year and retention track year chosen.
                if ($retentionTrackOrgAcademicYearId && $currentOrgAcademicYearYearId) {
                    $retentionTrackingYearId = $this->orgAcademicYearRepository->find($retentionTrackOrgAcademicYearId)->getYearId()->getId();
                    $retentionVariables = $this->persistenceRetentionService->getMeaningfulRetentionVariables($retentionTrackingYearId, $currentOrgAcademicYearYearId);
                } else {
                    $retentionTrackingYearId = null;
                    $retentionVariables = [];
                }

                // Get a lookup table of survey data we'll need multiple times.  $surveyData is an array whose indexes are survey_ids
                // and whose values are associative arrays with keys 'survey_name' and 'included_in_persist_midyear_reporting'.
                $surveyData = $this->wessLinkRepository->getSurveysAndNamesForOrganization($organizationId, $yearId, 'closed');

                // Get students in the selected retention track for use in selected report sections.
                $retentionTrackStudentIds = $this->orgPersonStudentRetentionTrackingGroupRepository->getRetentionTrackingGroupStudents($organizationId, $retentionTrackOrgAcademicYearId);
                $filteredRetentionTrackStudentIds = array_intersect($filteredStudentIds, $retentionTrackStudentIds);

                // Get the data for the report by calling a function for each section that is to be included.
                // Assemble the sections in the order that they are provided in the POST, with the assumption that this is the same order
                // they were listed in on the filter page, which in turn is the same order they were assigned in the database.
                $reportItems = [];
                foreach ($reportSections as $section) {
                    switch ($section['section_name']) {
                        case 'What is Mapworks?':
                            if ($section['is_included']) {
                                $reportItems[] = $this->getMapworksOverviewSection($section);
                            }
                            break;

                        case 'Risk Profile':
                            if ($section['is_included']) {
                                if ($section['apply_retention_track']) {
                                    $reportItems[] = $this->getRiskProfileSection($section['section_name'], $filteredRetentionTrackStudentIds, $riskStartDateTime, $riskEndDateTime);
                                } else {
                                    $reportItems[] = $this->getRiskProfileSection($section['section_name'], $filteredStudentIds, $riskStartDateTime, $riskEndDateTime);
                                    $applyRetentionTrackingToCount = false;
                                }

                            }
                            break;

                        case 'GPA by Risk':
                            if ($section['is_included']) {
                                if ($section['apply_retention_track']) {
                                    $reportItems[] = $this->getGPAByRiskSection($section['section_name'], $organizationId, $filteredRetentionTrackStudentIds, $orgAcademicYearId, $orgAcademicTermsIds, $riskStartDateTime, $riskEndDateTime);
                                } else {
                                    $reportItems[] = $this->getGPAByRiskSection($section['section_name'], $organizationId, $filteredStudentIds, $orgAcademicYearId, $orgAcademicTermsIds, $riskStartDateTime, $riskEndDateTime);
                                    $applyRetentionTrackingToCount = false;
                                }

                            }
                            break;

                        case 'Intent to Leave and Persistence':
                            if ($section['is_included']) {
                                $reportItems[] = $this->getIntentToLeavePersistenceSection($section['section_name'], $filteredRetentionTrackStudentIds, $orgAcademicYearId, $cohortsAndSurveys, $surveyData, $retentionVariables, $organizationId, $retentionTrackingYearId);
                            }
                            break;

                        case 'Persistence and Retention by Risk':
                            if ($section['is_included']) {
                                $reportItems[] = $this->getPersistenceByRiskSection($section['section_name'], $organizationId, $filteredRetentionTrackStudentIds, $retentionTrackOrgAcademicYearId, $riskStartDateTime, $riskEndDateTime);
                            }
                            break;

                        case 'Top Factors with Correlation to Persistence and Retention':
                            if ($section['is_included']) {
                                $reportItems[] = $this->getCorrelationFactorsPersistenceSection($section['section_name'], $filteredRetentionTrackStudentIds, $orgAcademicYearId, $cohortsAndSurveys, $surveyData, $retentionVariables);
                            }
                            break;

                        case 'Top Factors with Correlation to GPA':
                            if ($section['is_included']) {
                                if ($section['apply_retention_track']) {
                                    $reportItems[] = $this->getCorrelationFactorsGPASection($section['section_name'], $filteredRetentionTrackStudentIds, $cohortsAndSurveys, $surveyData, $orgAcademicYearId, $orgAcademicTermsIds);
                                } else {
                                    $reportItems[] = $this->getCorrelationFactorsGPASection($section['section_name'], $filteredStudentIds, $cohortsAndSurveys, $surveyData, $orgAcademicYearId, $orgAcademicTermsIds);
                                    $applyRetentionTrackingToCount = false;
                                }
                            }
                            break;

                        case 'Activity Overview':
                            if ($section['is_included']) {
                                if ($section['apply_retention_track']) {
                                    $reportItems[] = $this->getActivityOverviewSection($section, $organizationId, $filteredRetentionTrackStudentIds, $orgAcademicYearId);
                                } else {
                                    $reportItems[] = $this->getActivityOverviewSection($section, $organizationId, $filteredStudentIds, $orgAcademicYearId);
                                    $applyRetentionTrackingToCount = false;
                                }
                            }
                            break;
                    }
                }

                if ($applyRetentionTrackingToCount) {
                    if ($filteredRetentionTrackStudentIds) {
                        $studentCount = count($filteredRetentionTrackStudentIds);
                    } else {
                        $studentCount = 0;
                    }
                } else {
                    $studentCount = count($filteredStudentIds);
                }
            }


            $reportData = [];
            $reportData['request_json'] = $reportRunningStatusDto;
            $reportData['report_info'] = [
                'report_id' => $reportId,
                'report_name' => $reportsObject->getName(),
                'short_code' => $reportsObject->getShortCode(),
                'report_instance_id' => $reportsRunningStatusId,
                'total_students' => $studentCount,
                'report_date' => date('Y-m-d\TH:i:sO', $reportGeneratedTimestamp),
                'report_by' => [
                    'first_name' => $personObject->getFirstname(),
                    'last_name' => $personObject->getLastname()
                ]
            ];
            if (empty($reportItems)) {
                $reportData['status_message'] = [
                    'code' => ReportsConstants::REPORT_NO_DATA_CODE,
                    'description' => ReportsConstants::REPORT_NO_DATA_MESSAGE
                ];
                $this->logger->addWarning(json_encode($reportData['status_message']));

            } else {
                $reportData['report_items'] = $reportItems;
            }

            $responseJSON = $this->serializer->serialize($reportData, 'json');
            $reportsRunningStatusObject->setStatus('C');
            $reportsRunningStatusObject->setResponseJson($responseJSON);
        };

        $this->reportsRunningStatusRepository->flush();
        $this->alertNotificationsService->createReportNotification($reportsRunningStatusObject);
        return $reportData;
    }


    /**
     * Validates filter selections for attributes in $searchAttributes,
     * including academic year, academic terms, retention track year, and risk window dates.
     * Returns the error wrapped in an array if they're not valid for a variety of reasons.
     *
     * @param array $searchAttributes
     * @param int $organizationId
     * @return array
     */
    private function validateFilterSelections($searchAttributes, $organizationId)
    {
        // Make sure the academic year is set, exists, belongs to the correct organization, and is not a future year.
        $orgAcademicYearId = $searchAttributes['org_academic_year_id'];
        if (empty($orgAcademicYearId)) {
            return ['error' => 'An academic year must be selected in the mandatory filter.'];
        } else {
            $orgAcademicYearObject = $this->orgAcademicYearRepository->find($orgAcademicYearId);
            $yearId = $orgAcademicYearObject->getYearId()->getId();
            if (empty($orgAcademicYearObject)) {
                return ['error' => 'The academic year selected does not exist.'];
            } else {
                $organizationIdAssociatedWithYear = $orgAcademicYearObject->getOrganization()->getId();
                if ($organizationIdAssociatedWithYear != $organizationId) {
                    return ['error' => 'The academic year selected does not belong to the organization.'];
                }
                $academicYearTense = $this->academicYearService->determinePastCurrentOrFutureYearForOrganization($organizationId, $orgAcademicYearId);
                if ($academicYearTense == 'future') {
                    return ['error' => 'The academic year selected cannot be a future year.'];
                }
            }
        }

        // Check that the terms selected all exist and belong to the year selected.
        foreach ($searchAttributes['org_academic_terms_id'] as $termId) {
            $term = $this->orgAcademicTermsRepository->find($termId);
            if (empty($term)) {
                return ['error' => 'An academic term selected does not exist.'];
            } else {
                $orgAcademicYearIdAssociatedWithTerm = $term->getOrgAcademicYear()->getId();
                if ($orgAcademicYearIdAssociatedWithTerm != $orgAcademicYearId) {
                    return ['error' => 'An academic term selected does not belong to the academic year selected.'];
                }
            }
        }

        // If a retention track year has been chosen, check that it belongs to the correct organization  and it should not be a future year
        if (!empty($searchAttributes['retention_date']['academic_year_id'])) {
            $retentionTrackOrgAcademicYearId = $searchAttributes['retention_date']['academic_year_id'];
            $retentionTrackingOrgAcademicYearObject = $this->orgAcademicYearRepository->find($retentionTrackOrgAcademicYearId);
            if (!$retentionTrackingOrgAcademicYearObject) {
                return ['error' => 'The retention track year selected does not exist.'];
            }
            $retentionTrackingYearId = $retentionTrackingOrgAcademicYearObject->getYearId()->getId();
            if ($retentionTrackingYearId <= $yearId) {
                $organizationIdAssociatedWithYear = $orgAcademicYearObject->getOrganization()->getId();
                if ($organizationIdAssociatedWithYear != $organizationId) {
                    return ['error' => 'The retention track year selected does not belong to the organization.'];
                }
            } else {
                return ['error' => 'Selected retention tracking year cannot be a future year.'];
            }

            $areStudentsInRetentionTrack = $this->orgPersonStudentRetentionTrackingGroupRepository->areStudentsAssignedToThisRetentionTrackingYear($organizationId, $retentionTrackOrgAcademicYearId);
            if (!$areStudentsInRetentionTrack) {
                return ['error' => ReportsConstants::NO_STUDENTS_AVAILABLE_FOR_RETENTION_TRACKING_YEAR];
            }

        }

        // Check that the risk window dates are valid and formatted correctly, and that the start date is not later than the end date.
        $riskStartDateTime = date_create_from_format('Y-m-d', $searchAttributes['risk_start_date']);
        if (!$riskStartDateTime || (date_format($riskStartDateTime, 'Y-m-d') != $searchAttributes['risk_start_date'])) {
            return ['error' => 'Risk start date is invalid or formatted incorrectly.'];
        }
        $riskEndDateTime = date_create_from_format('Y-m-d', $searchAttributes['risk_end_date']);
        if (!$riskEndDateTime || (date_format($riskEndDateTime, 'Y-m-d') != $searchAttributes['risk_end_date'])) {
            return ['error' => 'Risk end date is invalid or formatted incorrectly.'];
        }
        if ($riskStartDateTime > $riskEndDateTime) {
            return ['error' => 'Risk start date cannot be after risk end date.'];
        }

        return [];
    }


    /**
     * Returns an array containing metadata (including attributes 'ebi_metadata_id', 'variable_name', and 'org_academic_year_id')
     * about the retention variables which will produce meaningful student data, based on the $baseAcademicYearId
     * (the year used for survey data) and $retentionTrackYearId.
     *
     * There are three ideas which lead to the restrictions:
     * 1. We assume a student's retention track year is his/her first year at the school, so it doesn't make sense for
     *      the base academic year to be before the retention track year.
     * 2. We don't want to report on variables which have not had time to happen yet (even if there is bad data on them).
     *      For example, if the current year is chosen as the retention track year, not enough time has passed to know
     *      if the students have been retained to year 2.
     * 3. We don't want to report on outcomes which precede their predictors.  It's meaningless to look at
     *      the relationship between an earlier retention variable and a later survey.  If the data is valid, then, for
     *      example, we would always be reporting that 100% of students who said they intended to leave had been retained,
     *      because they had already been retained long enough to take the survey.
     *
     * This function assumes that validateFilterSelections has already been called, so the relationship between the
     * base academic year and the retention track year is one which can produce meaningful data.  This function goes
     * one step further to determine which of the retention variables are valid for the given combination.
     *
     * @param int $baseAcademicYearId
     * @param int $retentionTrackYearId
     * @param int $orgId
     * @return array
     */
    public function getMeaningfulRetentionVariables($baseAcademicYearId, $retentionTrackYearId, $orgId)
    {
        $baseAcademicYearTense = $this->academicYearService->determinePastCurrentOrFutureYearForOrganization($orgId, $baseAcademicYearId);

        $retentionVariables = [];

        if ($baseAcademicYearId == $retentionTrackYearId) {
            // Include the PersistMidYear variable whenever the base academic year and retention track year are the same.
            $persistMidYearId = $this->ebiMetadataRepository->findOneBy(['key' => 'PersistMidYear'])->getId();

            $retentionVariables[] = [
                'ebi_metadata_id' => $persistMidYearId,
                'variable_name' => 'Persist to Mid-Year',
                'org_academic_year_id' => $retentionTrackYearId
            ];

            if ($baseAcademicYearTense == 'past') {
                // Also include the RetainYear2 variable as long as the year chosen is not the current year.
                $yearIdForRetentionTrackYear = $this->orgAcademicYearRepository->find($retentionTrackYearId)->getYearId()->getId();
                // The year_id has a format like 201516, so adding 101 is incrementing it.
                $yearIdForYearAfterRetentionTrackYear = $yearIdForRetentionTrackYear + 101;
                $yearAfterRetentionTrackYear = $this->orgAcademicYearRepository->findOneBy(['organization' => $orgId, 'yearId' => $yearIdForYearAfterRetentionTrackYear]);
                if (!empty($yearAfterRetentionTrackYear)) {
                    $orgAcademicYearIdForYearAfterRetentionTrackYear = $yearAfterRetentionTrackYear->getId();
                    $retainYear2Id = $this->ebiMetadataRepository->findOneBy(['key' => 'RetainYear2'])->getId();
                    $retentionVariables[] = [
                        'ebi_metadata_id' => $retainYear2Id,
                        'variable_name' => 'Retained in Year 2',
                        'org_academic_year_id' => $orgAcademicYearIdForYearAfterRetentionTrackYear
                    ];
                }

                $currentYearId = $this->academicYearService->findCurrentAcademicYearForOrganization($orgId)['year_id'];
                $yearIdFor2YearsAfterRetentionTrackYear = $yearIdForRetentionTrackYear + 202;
                if ($yearIdFor2YearsAfterRetentionTrackYear <= $currentYearId) {
                    // Also include the RetainYear3 variable as long as the year chosen is at least two years ago.
                    $year2YearsAfterRetentionTrackYear = $this->orgAcademicYearRepository->findOneBy(['organization' => $orgId, 'yearId' => $yearIdFor2YearsAfterRetentionTrackYear]);
                    if (!empty($year2YearsAfterRetentionTrackYear)) {
                        $orgAcademicYearIdFor2YearsAfterRetentionTrackYear = $year2YearsAfterRetentionTrackYear->getId();
                        $retainYear3Id = $this->ebiMetadataRepository->findOneBy(['key' => 'RetainYear3'])->getId();
                        $retentionVariables[] = [
                            'ebi_metadata_id' => $retainYear3Id,
                            'variable_name' => 'Retained in Year 3',
                            'org_academic_year_id' => $orgAcademicYearIdFor2YearsAfterRetentionTrackYear
                        ];
                    }
                }
            }
        } else {
            // If the base academic year and retention track year are not the same, then based on validations which
            // are assumed to have already happened, the retention track year is the year before the base academic year,
            // and the base academic year is not the current year.  In these circumstances, it always only makes sense
            // to report on the RetainYear3 variable.
            $yearIdForRetentionTrackYear = $this->orgAcademicYearRepository->find($retentionTrackYearId)->getYearId()->getId();
            $yearIdFor2YearsAfterRetentionTrackYear = $yearIdForRetentionTrackYear + 202;
            $year2YearsAfterRetentionTrackYear = $this->orgAcademicYearRepository->findOneBy(['organization' => $orgId, 'yearId' => $yearIdFor2YearsAfterRetentionTrackYear]);
            if (!empty($year2YearsAfterRetentionTrackYear)) {
                $orgAcademicYearIdFor2YearsAfterRetentionTrackYear = $year2YearsAfterRetentionTrackYear->getId();
                $retainYear3Id = $this->ebiMetadataRepository->findOneBy(['key' => 'RetainYear3'])->getId();
                $retentionVariables[] = [
                    'ebi_metadata_id' => $retainYear3Id,
                    'variable_name' => 'Retained in Year 3',
                    'org_academic_year_id' => $orgAcademicYearIdFor2YearsAfterRetentionTrackYear
                ];
            }
        }

        return $retentionVariables;
    }


    /**
     * Returns the 'What is Mapworks?' section, correctly formatted for the final report.
     *
     * @param array $section - includes the section name and information about whether section elements are included
     * @return array
     */
    public function getMapworksOverviewSection($section)
    {
        $reportItem = [];
        $reportItem['section_name'] = $section['section_name'];

        $elementsToReturn = [];
        foreach ($section['elements'] as $element) {
            if ($element['is_included'] && !empty($element['element_text'])) {
                $elementToReturn = [
                    'element_name' => $element['element_name'],
                    'element_text' => $element['element_text']
                ];
                $elementsToReturn[] = $elementToReturn;
            }
        }

        $reportItem['section_text'] = $elementsToReturn;
        return $reportItem;
    }


    /**
     * Returns the 'Risk Profile' section, correctly formatted for the final report.
     *
     * @param string $sectionName
     * @param array $studentIds
     * @param string $riskStartDateTime - 'yyyy-mm-dd hh:mm:ss' format
     * @param string $riskEndDateTime - 'yyyy-mm-dd hh:mm:ss' format
     * @return array
     */
    public function getRiskProfileSection($sectionName, $studentIds, $riskStartDateTime, $riskEndDateTime)
    {
        $reportItem = [];
        $reportItem['section_name'] = $sectionName;

        if (empty($studentIds)) {
            $reportItem['message'] = self::NO_STUDENTS_IN_SECTION_MESSAGE;
        } else {
            $riskLevelSet = $this->personRiskLevelHistoryRepository->getRiskLevelHistoryByDateRange($riskStartDateTime, $riskEndDateTime, $studentIds);

            //get total students
            $queryTotalStudents = array_sum(array_column($riskLevelSet, 'total_students'));
            $totalStudents = count($studentIds);
            $reportItem['total_students'] = $totalStudents;

            //calculate gray students
            $grayId = $this->riskLevelsRepository->findOneBy(['riskText' => 'gray'])->getId();
            $riskLevelSetWithGray = [];
            $grayRisk = [];
            foreach ($riskLevelSet as $riskLevel) {
                if ($riskLevel['risk_level'] == $grayId) {
                    $grayRisk['risk_level'] = $riskLevel['risk_level'];
                    //Including old gray count in case an entry of null risk exists in the person_risk_level_history
                    $grayRisk['total_students'] = $riskLevel['total_students'] + ($totalStudents - $queryTotalStudents);
                    $riskLevelSetWithGray[] = $grayRisk;
                } else {
                    $riskLevelSetWithGray[] = $riskLevel;
                }
            }

            $updatedRiskLevels = [];

            //filling missing risk data
            foreach ($riskLevelSetWithGray as $riskLevel) {
                $riskData = [];
                $riskColor = $this->riskLevelsRepository->find($riskLevel['risk_level']);
                $riskData['risk_level'] = $riskColor->getRiskText();
                $riskData['total_students'] = $riskLevel['total_students'];
                $riskData['risk_percentage'] = strval(round(($riskLevel['total_students'] / $totalStudents) * 100, 1));
                $riskData['color_value'] = $riskColor->getColorHex();
                $updatedRiskLevels[] = $riskData;
            }

            $reportItem['risk_levels'] = $updatedRiskLevels;

        }
        return $reportItem;
    }


    /**
     * Returns the 'GPA by Risk' section, correctly formatted for the final report.
     *
     * @param string $sectionName
     * @param int $organizationId
     * @param array $studentIds
     * @param int $orgAcademicYearId
     * @param array $orgAcademicTermsIds
     * @param string $riskStartDateTime - 'yyyy-mm-dd hh:mm:ss' format
     * @param string $riskEndDateTime - 'yyyy-mm-dd hh:mm:ss' format
     * @return array
     */
    public function getGPAByRiskSection($sectionName, $organizationId, $studentIds, $orgAcademicYearId, $orgAcademicTermsIds, $riskStartDateTime, $riskEndDateTime)
    {
        $reportItem = [];
        $reportItem['section_name'] = $sectionName;

        if (empty($studentIds)) {
            $reportItem['message'] = self::NO_STUDENTS_IN_SECTION_MESSAGE;
        } else {
            $gpaId = $this->GPAReportService->getEndTermGpaId(self::END_TERM_GPA_KEY);

            //Converting int year to array of ints for GPA Service
            $orgAcademicYearIds = [$orgAcademicYearId];

            $gpaReportWithOrgAndCount = $this->GPAReportService->buildReportItems($organizationId, $gpaId, $studentIds, $orgAcademicYearIds, $orgAcademicTermsIds, $riskStartDateTime, $riskEndDateTime);

            if (empty($gpaReportWithOrgAndCount)) {
                $reportItem['message'] = ReportsConstants::NO_GPA_SCORES_MESSAGE;
            } else {
                //Removing percent_under_2 section
                $gpaReportWithOrgAndCount = $this->dataProcessingUtilityService->recursiveRemovalByArrayKey($gpaReportWithOrgAndCount, 'percent_under_2');

                //Eliminating Unneeded Org and Count and year
                $gpaReportRaw = $gpaReportWithOrgAndCount['gpa_term_summaries_by_year'];
                $gpaReportRawNoYear = $gpaReportRaw[0]['gpa_summary_by_term'];
                $reportItem['gpa_summary_by_term'] = $gpaReportRawNoYear;
            }
        }

        return $reportItem;
    }


    /**
     * Returns the 'Intent to Leave and Persistence' section, correctly formatted for the final report.
     *
     * @param string $sectionName
     * @param array $studentIds
     * @param int $orgAcademicYearId
     * @param array $cohortsAndSurveys
     * @param array $surveyData
     * @param array $retentionVariables
     * @param integer $organizationId
     * @param string $retentionTrackingYearId
     * @return array
     */
    private function getIntentToLeavePersistenceSection($sectionName, $studentIds, $orgAcademicYearId, $cohortsAndSurveys, $surveyData, $retentionVariables, $organizationId, $retentionTrackingYearId)
    {
        $reportItem = [];
        $reportItem['section_name'] = $sectionName;

        if (empty($studentIds)) {
            $reportItem['message'] = self::NO_STUDENTS_IN_SECTION_MESSAGE;
        } elseif (empty($retentionVariables)) {
            $reportItem['message'] = self::NO_RETENTION_VARIABLES_MESSAGE;
        } else {

            // Unwind the requested cohorts and surveys, and find the students who indicated intent to leave for each combination;
            // then get retention data for these students and assemble the data.
            $cohortSpecificData = [];

            $orgAcademicYearObject = $this->orgAcademicYearRepository->find($orgAcademicYearId);
            $yearId = $orgAcademicYearObject->getYearId()->getId();


            foreach ($cohortsAndSurveys as $cohortAndSurveys) {
                $cohort = $cohortAndSurveys['cohort'];
                $cohortName = $this->orgCohortNameRepository->findOneBy(['orgAcademicYear' => $orgAcademicYearId, 'cohort' => $cohort])->getCohortName();

                $surveySpecificData = [];

                foreach ($cohortAndSurveys['surveys'] as $surveyId) {

                    if (isset($surveyData[$surveyId])) {
                        $persistenceSpecificData = [];

                        $studentsIntendingToLeave = $this->intentToLeaveRepository->getStudentsWhoIntendToLeave($surveyId, $cohort, $orgAcademicYearId, $studentIds);
                        $intentToLeaveCount = count($studentsIntendingToLeave);

                        $persistenceSpecificData[] = [
                            'title' => 'N = Responded Intent to Leave',
                            'count' => $intentToLeaveCount
                        ];

                        if ($intentToLeaveCount > 0) {
                            $retentionData = $this->orgPersonStudentRetentionRepository->getAggregatedCountsOfRetentionDataByStudentList($retentionTrackingYearId, $organizationId, $studentsIntendingToLeave);

                            $retentionData = $this->mapRetentionData($retentionData);
                            $midyearCount = 0;
                            $beginningYearCount = 0;
                            $denominatorCount = 0;
                            foreach ($retentionVariables as $yearsFromRetentionTrack => $retentionVariable) {

                                // Omit the PersistMidYear variable if the survey came after mid-year.
                                if(isset($retentionData[$yearsFromRetentionTrack]['midyear_numerator_count'])){
                                    $midyearCount = $retentionData[$yearsFromRetentionTrack]['midyear_numerator_count'];
                                }
                                if(isset($retentionData[$yearsFromRetentionTrack]['beginning_year_numerator_count'])){
                                    $beginningYearCount = $retentionData[$yearsFromRetentionTrack]['beginning_year_numerator_count'];
                                }
                                if(isset($retentionData[$yearsFromRetentionTrack]['denominator_count'])){
                                    $denominatorCount = $retentionData[$yearsFromRetentionTrack]['denominator_count'];
                                }




                                if (isset($retentionVariables[$yearsFromRetentionTrack][0])) {
                                    $persistenceBeginningYearSpecificData = [
                                        'title' => $retentionVariables[$yearsFromRetentionTrack][0],
                                        'students_retained' => '-',
                                        'student_count' => '-',
                                        'percent' => '-'
                                    ];

                                    $persistenceBeginningYearSpecificData['students_retained'] = $beginningYearCount;
                                    $persistenceBeginningYearSpecificData['student_count'] = $denominatorCount;

                                    if ($persistenceBeginningYearSpecificData['student_count'] > 0) {
                                        $persistenceBeginningYearSpecificData['percent'] = round((($beginningYearCount / $denominatorCount) * 100), 1);
                                    } else {
                                        $persistenceBeginningYearSpecificData['percent'] = "-";

                                    }
                                    $persistenceSpecificData[] = $persistenceBeginningYearSpecificData;
                                }


                                if (isset($retentionVariables[$yearsFromRetentionTrack][1])) {

                                    $persistenceMidYearSpecificData = [
                                        'title' => $retentionVariables[$yearsFromRetentionTrack][1],
                                        'students_retained' => '-',
                                        'student_count' => '-',
                                        'percent' => '-'
                                    ];

                                    if ($surveyData[$surveyId]['included_in_persist_midyear_reporting']) {

                                        $persistenceMidYearSpecificData['students_retained'] = $midyearCount;
                                        $persistenceMidYearSpecificData['student_count'] = $denominatorCount;

                                        if ($persistenceMidYearSpecificData['student_count'] > 0) {
                                            $persistenceMidYearSpecificData['percent'] = round((($midyearCount / $denominatorCount) * 100), 1);
                                        } else {
                                            $persistenceMidYearSpecificData['percent'] = "-";
                                        }
                                    }
                                    $persistenceSpecificData[] = $persistenceMidYearSpecificData;
                                }
                            }
                        }

                        $surveySpecificData[] = [
                            'survey_id' => $surveyId,
                            'survey_name' => $surveyData[$surveyId]['survey_name'],
                            'persistence_specific_data' => $persistenceSpecificData
                        ];
                    }
                }

                $cohortSpecificData[] = [
                    'cohort' => $cohort,
                    'cohort_name' => $cohortName,
                    'survey_specific_data' => $surveySpecificData
                ];
            }

            if (!empty($cohortSpecificData)) {
                $reportItem['cohort_specific_data'] = $cohortSpecificData;
            } else {
                $reportItem['message'] = 'Insufficient data to display.';
            }
        }

        return $reportItem;
    }


    /**
     * Returns the 'Persistence and Retention by Risk' section, correctly formatted for the final report.
     *
     * @param string $sectionName
     * @param int $organizationId
     * @param array $studentIds
     * @param int $retentionTrackYearId
     * @param string $riskStartDateTime - 'yyyy-mm-dd hh:mm:ss' format
     * @param string $riskEndDateTime - 'yyyy-mm-dd hh:mm:ss' format
     * @return array
     */
    private function getPersistenceByRiskSection($sectionName, $organizationId, $studentIds, $retentionTrackYearId, $riskStartDateTime, $riskEndDateTime)
    {
        $reportItem = [];
        $reportItem['section_name'] = $sectionName;

        if (empty($studentIds)) {
            $reportItem['message'] = self::NO_STUDENTS_IN_SECTION_MESSAGE;
        } else {
            $reportItem['section_data'] = $this->persistenceRetentionService->formatRetentionDataset($studentIds, $organizationId, $riskStartDateTime, $riskEndDateTime, $retentionTrackYearId);
        }

        return $reportItem;
    }


    /**
     * Returns the 'Top Factors with Correlation to Persistence and Retention' section, correctly formatted for the final report.
     *
     * @param string $sectionName
     * @param array $studentIds
     * @param int $orgAcademicYearId
     * @param array $cohortsAndSurveys
     * @param array $surveyData
     * @param array $retentionVariables
     * @return array
     */
    private function getCorrelationFactorsPersistenceSection($sectionName, $studentIds, $orgAcademicYearId, $cohortsAndSurveys, $surveyData, $retentionVariables)
    {
        $reportItem = [];
        $reportItem['section_name'] = $sectionName;

        if (empty($studentIds)) {
            $reportItem['message'] = self::NO_STUDENTS_IN_SECTION_MESSAGE;
        } elseif (empty($retentionVariables)) {
            $reportItem['message'] = self::NO_RETENTION_VARIABLES_MESSAGE;
        } else {

            // Unwind the requested cohorts, surveys, and retention variables;
            // find the top factors for each combination;
            // then assemble the data in a similar structure.
            $cohortSpecificData = [];

            foreach ($cohortsAndSurveys as $cohortAndSurveys) {
                $cohort = $cohortAndSurveys['cohort'];
                $cohortName = $this->orgCohortNameRepository->findOneBy(['orgAcademicYear' => $orgAcademicYearId, 'cohort' => $cohort])->getCohortName();

                $surveySpecificData = [];

                foreach ($cohortAndSurveys['surveys'] as $surveyId) {

                    $persistenceSpecificData = [];

                    foreach ($retentionVariables as $yearsFromRetentionTrack => $retentionVariable) {
                        foreach ($retentionVariable as $isMidYearVariable => $retentionVariableName) {

                            $allowedVariable = !$isMidYearVariable || ($surveyData[$surveyId]['included_in_persist_midyear_reporting'] && $isMidYearVariable);
                            if (isset($surveyData[$surveyId]) && $allowedVariable) {
                                $topFactorsWithCorrelations = $this->personFactorCalculatedRepository->getTopFactorsCorrelatedToPersistence($studentIds, $surveyId, $cohort, $orgAcademicYearId, $yearsFromRetentionTrack, $isMidYearVariable);
                                $topFactors = array_column($topFactorsWithCorrelations, 'name');

                                if (!empty($topFactors)) {
                                    $persistenceSpecificData[] = [
                                        'title' => $retentionVariables[$yearsFromRetentionTrack][$isMidYearVariable],
                                        'factors' => $topFactors
                                    ];
                                } else {
                                    $persistenceSpecificData[] = [
                                        'title' => $retentionVariables[$yearsFromRetentionTrack][$isMidYearVariable],
                                        'message' => "The data does not allow for correlations to be calculated."
                                    ];
                                }
                            }
                        }
                    }

                    if (!empty($persistenceSpecificData)) {
                        $surveySpecificData[] = [
                            'survey_id' => $surveyId,
                            'survey_name' => $surveyData[$surveyId]['survey_name'],
                            'persistence_specific_data' => $persistenceSpecificData
                        ];
                    }
                }

                if (!empty($surveySpecificData)) {
                    $cohortSpecificData[] = [
                        'cohort' => $cohort,
                        'cohort_name' => $cohortName,
                        'survey_specific_data' => $surveySpecificData
                    ];
                }
            }

            if (!empty($cohortSpecificData)) {
                $reportItem['cohort_specific_data'] = $cohortSpecificData;
            } else {
                $reportItem['message'] = self::NO_RETENTION_VARIABLES_MESSAGE;
            }
        }

        return $reportItem;
    }


    /**
     * Returns the 'Top Factors with Correlation to GPA' section, correctly formatted for the final report.
     *
     * @param string $sectionName
     * @param array $studentIds
     * @param array $cohortsAndSurveys
     * @param array $surveyData
     * @param int $orgAcademicYearId
     * @param array $orgAcademicTermsIds
     * @return array
     */
    public function getCorrelationFactorsGPASection($sectionName, $studentIds, $cohortsAndSurveys, $surveyData, $orgAcademicYearId, $orgAcademicTermsIds)
    {
        $reportItem = [];
        $reportItem['section_name'] = $sectionName;

        if (empty($studentIds)) {
            $reportItem['message'] = self::NO_STUDENTS_IN_SECTION_MESSAGE;
        } else {

            //Retrieve GPA id by Key
            $gpaId = $this->ebiMetadataRepository->findOneBy(['key' => self::END_TERM_GPA_KEY])->getId();

            // Unwind the requested cohorts, surveys, and terms;
            // find the top factors for each combination;
            // then assemble the data in a similar structure.
            $cohortSpecificData = [];

            foreach ($cohortsAndSurveys as $cohortAndSurveys) {
                $cohort = $cohortAndSurveys['cohort'];
                $cohortName = $this->orgCohortNameRepository->findOneBy(['orgAcademicYear' => $orgAcademicYearId, 'cohort' => $cohort])->getCohortName();

                $surveySpecificData = [];

                foreach ($cohortAndSurveys['surveys'] as $surveyId) {

                    if (isset($surveyData[$surveyId])) {
                        $termSpecificData = [];

                        foreach ($orgAcademicTermsIds as $orgAcademicTermId) {
                            $topFactorsWithCorrelations = $this->personFactorCalculatedRepository->getTopFactorsCorrelatedWithEbiMetadata($studentIds, $surveyId, $cohort, $orgAcademicYearId, $gpaId, $orgAcademicYearId, $orgAcademicTermId);
                            $topFactors = array_column($topFactorsWithCorrelations, 'name');

                            $term = $this->orgAcademicTermsRepository->find($orgAcademicTermId);
                            $termName = $term->getName();

                            if (!empty($topFactors)) {
                                $termSpecificData[] = [
                                    'title' => $termName,
                                    'factors' => $topFactors
                                ];
                            } else {
                                $termSpecificData[] = [
                                    'title' => $termName,
                                    'message' => self::NULL_CORRELATIONS_MESSAGE
                                ];
                            }
                        }

                        $surveySpecificData[] = [
                            'survey_id' => $surveyId,
                            'survey_name' => $surveyData[$surveyId]['survey_name'],
                            'term_specific_data' => $termSpecificData
                        ];
                    }
                }

                $cohortSpecificData[] = [
                    'cohort' => $cohort,
                    'cohort_name' => $cohortName,
                    'survey_specific_data' => $surveySpecificData
                ];
            }

            if (!empty($cohortSpecificData)) {
                $reportItem['cohort_specific_data'] = $cohortSpecificData;
            } else {
                $reportItem['message'] = 'Insufficient data to display.';
            }
        }

        return $reportItem;
    }


    /**
     * Returns the 'Activity Overview' section, correctly formatted for the final report.
     *
     * @param array $section - includes the section name and information about whether section elements are included
     * @param array $studentIds
     * @param int $orgAcademicYearId
     * @param int $orgId
     * @return array
     */
    private function getActivityOverviewSection($section, $orgId, $studentIds, $orgAcademicYearId)
    {
        $reportItem = [];
        $reportItem['section_name'] = $section['section_name'];

        if (empty($studentIds)) {
            $reportItem['message'] = self::NO_STUDENTS_IN_SECTION_MESSAGE;
        } else {

            $args = [];
            $orgAcademicYearObject = $this->orgAcademicYearRepository->find($orgAcademicYearId);

            //building parameter set for ActivityOverview Section
            $args['reporting_on_student_ids'] = implode($studentIds, ",");
            $facultySet = $this->orgPersonFacultyRepository->getAllFacultiesForOrg($orgId);
            $facultyArray = [];
            foreach ($facultySet as $faculty) {
                $facultyArray[] = $faculty['faculty_id'];
            }
            $args['reporting_on_faculty_ids'] = implode($facultyArray, ",");
            $args['orgId'] = $orgId;
            $args['start_date'] = $orgAcademicYearObject->getStartDate()->format('Y-m-d');
            $args['end_date'] = $orgAcademicYearObject->getEndDate()->format('Y-m-d');
            $totalStudentCount = count($studentIds);

            //Getting Search Repo for use in Section Data
            $searchRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:EbiSearch');

            $rawActivityOverview = $this->activityReportJob->getSectionData($this->activityReportService, 'ActivityOverview', $searchRepository, $args);
            $collatedActivityOverview = $this->buildIncludedActivityElements($rawActivityOverview, $section['elements'], $totalStudentCount);
            $reportItem['section_data'] = $collatedActivityOverview;
        }
        return $reportItem;
    }


    /**
     * Builds elements that are supposed to be included using the correct format
     *
     * @param array $rawActivityOverview - data from a query, where each record includes summary data about a particular activity type
     * @param array $elementInclusionData - list of activity types and whether they should be included in the report
     * @param int $totalStudentCount - number of students included in this report section
     * @return array
     */
    public function buildIncludedActivityElements($rawActivityOverview, $elementInclusionData, $totalStudentCount)
    {
        // Labels for the rows
        $metricLabels = [
            '# of Activities Logged',
            '# of Students Involved',
            '% of Students',
            '# of Faculty/Staff Logged',
            '# of Faculty/Staff Received'
        ];

        // Activity keys used in $rawActivityOverview, with the activity types that will be used to label the columns.
        // These activity types are also the report_section_elements, and are listed in $elementInclusionData.
        $activityAssociations = [
            'R' => 'Referrals',
            'A' => 'Appointments',
            'C' => 'Contacts',
            'IC' => 'Interaction Contacts',
            'N' => 'Notes',
            'AU' => 'Academic Updates'
        ];

        $labelsWithActivities = [];

        foreach ($metricLabels as $metricLabel) {
            $activities = [];
            foreach ($activityAssociations as $activityKey => $activityType) {
                if ($this->isElementIncluded($elementInclusionData, $activityType)) {
                    $activities[] = $this->buildActivityElement($metricLabel, $activityKey, $activityType, $rawActivityOverview, $totalStudentCount);
                }
            }
            $labelsWithActivities[] = [
                'label' => $metricLabel,
                'activities' => $activities
            ];
        }

        return $labelsWithActivities;
    }


    /**
     * Returns true/false depending on whether activity type should be included
     *
     * @param array $elementInclusionData - list of activity types and whether they should be included in the report
     * @param string $type
     * @return bool
     */
    public function isElementIncluded($elementInclusionData, $type)
    {
        foreach ($elementInclusionData as $element) {
            if (!empty($element) && $element['element_name'] === $type && $element['is_included']) {
                return true;
            }
        }
        return false;
    }


    /**
     * Returns individual activity entry, formatted correctly to be added to the JSON structure we're building.
     *
     * @param string $metricLabel - row label
     * @param string $activityKey - character or 2-character key to be matched in $rawActivityOverview
     * @param string $activityType - column label (which is associated with $activityKey)
     * @param array $rawActivityOverview - data from a query, where each record includes summary data about a particular activity type
     * @param int $totalStudentCount - number of students included in this report section
     * @return array
     */
    public function buildActivityElement($metricLabel, $activityKey, $activityType, $rawActivityOverview, $totalStudentCount)
    {
        $index = array_search($activityKey, array_column($rawActivityOverview, 'element_type'));

        $singleElement = [];
        $singleElement['activity_type'] = $activityType;

        if ($index !== false) {
            $result = $rawActivityOverview[$index];

            switch ($metricLabel) {
                case '# of Activities Logged':
                    $singleElement['value'] = $result['activity_count'];
                    break;

                case '# of Students Involved':
                    $singleElement['value'] = $result['student_count'];
                    break;

                case '% of Students':
                    $singleElement['value'] = round(($result['student_count'] / $totalStudentCount) * 100, 1);
                    break;

                case '# of Faculty/Staff Logged':
                    $singleElement['value'] = $result['faculty_count'];
                    break;

                case '# of Faculty/Staff Received':
                    if ($activityKey == 'R') {
                        $singleElement['value'] = $result['received_referrals'];
                    } else {
                        $singleElement['value'] = '-';
                    }
                    break;

                default:
                    $singleElement = [];
                    break;
            }
        } else {
            if ($activityKey == 'R' || $metricLabel != '# of Faculty/Staff Received') {
                $singleElement['value'] = 0;
            } else {
                $singleElement['value'] = '-';
            }
        }

        return $singleElement;
    }

    /**
     * Map retention data with years from retention track
     *
     * @param $retentionData
     * @return array
     */
    private function mapRetentionData($retentionData){

        $mappedToYearRetentionDataWithRisk = [];
        foreach($retentionData as $dataPoint) {
            $mappedToYearRetentionDataWithRisk[$dataPoint['years_from_retention_track']] = $dataPoint;
        }
        return $mappedToYearRetentionDataWithRisk;

    }

}
