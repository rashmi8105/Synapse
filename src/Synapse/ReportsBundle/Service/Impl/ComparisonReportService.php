<?php

namespace Synapse\ReportsBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgQuestionResponseRepository;
use Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonOrgMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\NotificationChannelService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\DAO\ComparisonReportDAO;
use Synapse\ReportsBundle\Entity\ReportInstanceGroup;
use Synapse\ReportsBundle\Entity\Reports;
use Synapse\ReportsBundle\Entity\ReportsRunningJson;
use Synapse\ReportsBundle\Entity\ReportsRunningStatus;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\Job\ReportJob;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningJsonRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\SurveyBundle\Repository\SurveyResponseRepository;

/**
 * @DI\Service("comparison_report_service")
 */
class ComparisonReportService extends AbstractService
{
    const SERVICE_KEY = 'comparison_report_service';

    //Scaffolding

    /**
     * @var Redis
     */
    private $cache;


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
     * @var AlertNotificationsService
     */
    private $alertNotificationsService;

    /**
     * @var CSVUtilityService
     */
    private $csvUtilityService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var NotificationChannelService
     */
    private $notificationChannelService;


    //Repositories

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var orgQuestionResponseRepository
     */
    private $orgQuestionResponseRepository;


    /**
     * @var PersonEbiMetaDataRepository
     */
    private $personEbiMetaDataRepository;

    /**
     * @var PersonOrgMetaDataRepository
     */
    private $personOrgMetaDataRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var ReportsRepository
     */
    private $reportsRepository;

    /**
     * @var ReportsRunningJsonRepository
     */
    private $reportsRunningJsonRepository;

    /**
     * @var ReportsRunningStatusRepository
     */
    private $reportsRunningStatusRepository;

    /**
     * @var SurveyResponseRepository
     */
    private $surveyResponseRepository;

    //DAO

    /**
     * @var comparisonReportDAO
     */
    private $comparisonReportDAO;

    /**
     * ComparisonReportService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container"),
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

        //Scaffolding
        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->serializer = $this->container->get(SynapseConstant::JMS_SERIALIZER_CLASS_KEY);

        //Services
        $this->csvUtilityService = $this->container->get(CSVUtilityService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);

        //Repositories
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->orgQuestionResponseRepository = $this->repositoryResolver->getRepository(OrgQuestionResponseRepository::REPOSITORY_KEY);
        $this->personEbiMetaDataRepository = $this->repositoryResolver->getRepository(PersonEbiMetaDataRepository::REPOSITORY_KEY);
        $this->personOrgMetaDataRepository = $this->repositoryResolver->getRepository(PersonOrgMetaDataRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->reportsRepository = $this->repositoryResolver->getRepository(ReportsRepository::REPOSITORY_KEY);
        $this->reportsRunningJsonRepository = $this->repositoryResolver->getRepository(ReportsRunningJsonRepository::REPOSITORY_KEY);
        $this->reportsRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsRunningStatusRepository::REPOSITORY_KEY);
        $this->surveyResponseRepository = $this->repositoryResolver->getRepository(SurveyResponseRepository::REPOSITORY_KEY);

        // DAO
        $this->comparisonReportDAO = $this->container->get(ComparisonReportDAO::DAO_KEY);

        //Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->notificationChannelService = $this->container->get(NotificationChannelService::SERVICE_KEY);
    }

    /**
     * Initiate the job that generates the Compare Report
     *
     * @param ReportRunningStatusDto $reportRunningDto
     * @param int $reportInstanceId
     * @throw ValidationException
     */
    public function initiateCompareReport($reportRunningDto, $reportInstanceId)
    {

        $job = new ReportJob();
        $reportService = 'comparison_report_service';
        $job->args = array(
            'reportInstanceId' => $reportInstanceId,
            'reportRunningDto' => serialize($reportRunningDto),
            'service' => $reportService
        );
        $this->resque->enqueue($job, true);
    }


    /**
     * Generates the Outcomes Comparison Report
     *
     * @param int $reportsRunningStatusId
     * @param ReportRunningStatusDto $reportRunningStatusDto
     * @return array
     */
    public function generateReport($reportsRunningStatusId, $reportRunningStatusDto)
    {

        $personId = $reportRunningStatusDto->getPersonId();
        $organizationId = $reportRunningStatusDto->getOrganizationId();
        $reportId = $reportRunningStatusDto->getReportId();

        $personObject = $this->personRepository->find($personId);

        $reportsObject = $this->reportsRepository->find($reportId);

        $reportsRunningStatusObject = $this->reportsRunningStatusRepository->find($reportsRunningStatusId);

        $reportData = [];

        if (empty($reportsRunningStatusObject)) {
            $errorMessage = 'The reports running status object is not defined';
            return $this->buildReportStatus($reportData, $reportsRunningStatusObject, $errorMessage);
        }

        if (empty($personObject)) {
            $errorMessage = 'The person object is not defined';
            return $this->buildReportStatus($reportData, $reportsRunningStatusObject, $errorMessage);
        }

        if (empty($reportsObject)) {
            $errorMessage = 'The report object is not defined';
            return $this->buildReportStatus($reportData, $reportsRunningStatusObject, $errorMessage);
        }

        $mandatoryFilters = $reportRunningStatusDto->getMandatoryFilters();

        if (empty($mandatoryFilters)) {
            $errorMessage = 'Compare report mandatory search attributes are not defined';
            return $this->buildReportStatus($reportData, $reportsRunningStatusObject, $errorMessage);
        }

        if (empty($mandatoryFilters['group_names'])) {
            $errorMessage = 'Sub population group names must not be empty';
            return $this->buildReportStatus($reportData, $reportsRunningStatusObject, $errorMessage);
        }

        $shortCode = $reportsObject->getShortCode();

        $queryResultsArray = array();

        $cohortId = $mandatoryFilters['latest_cohort']['id'];
        $surveyId = $mandatoryFilters['latest_survey']['id'];
        $gpaYearId = $mandatoryFilters['gpa_org_academic_year']['year']['year_id'];
        $surveyYearId = $mandatoryFilters['org_academic_year']['year']['year_id'];
        $retentionTrackingYearId = $mandatoryFilters['retention_org_academic_year']['year']['year_id'];
        $filteredStudentString = $reportsRunningStatusObject->getFilteredStudentIds();


        $reportData = $this->buildReportItems($reportsRunningStatusObject, $reportRunningStatusDto, $reportsObject, $personObject, $reportsRunningStatusId);

        if (empty($filteredStudentString)) {
            $statusCode = ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_CODE;
            $statusMessage = ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_MESSAGE;
            $reportData = $this->buildReportStatus($reportData, $reportsRunningStatusObject, $statusMessage, false, $statusCode);
            $this->notificationChannelService->sendNotificationToAllRegisteredChannels($personObject, $shortCode);
            return $reportData;
        } else {
            $filteredStudents = explode(",", $filteredStudentString);
        }

        // If request json consists of either
        if ((!empty($mandatoryFilters['datablocks']) || !empty($mandatoryFilters['isps']))) {
            // datablocks or isps
            $queryResultsArray = $this->getQueryDataForDataBlockOrIspWithGpaFactorAndRetention($organizationId, $personObject->getId(), $mandatoryFilters, $cohortId, $surveyId, $gpaYearId, $surveyYearId, $retentionTrackingYearId, $filteredStudents);
        } elseif (!empty($mandatoryFilters['isqs']) || !empty($mandatoryFilters['survey'])) {
            if (!empty($mandatoryFilters['isqs'])) {
                $surveyType = 'isqs';
            } elseif (!empty($mandatoryFilters['survey'])) {
                $surveyType = 'survey';
            } else {
                $surveyType = '';
            }

            // fetch survey array from json [isqs or survey , org or ebi]
            $surveyData = empty($surveyType) ? '' : $mandatoryFilters[$surveyType];
            // isqs or survey
            $queryResultsArray = $this->getQueryDataForISQorSurveyWithGpaFactorAndRetention($organizationId, $personObject->getId(), $surveyType, $cohortId, $surveyId, $gpaYearId, $surveyYearId, $surveyData, $retentionTrackingYearId, $filteredStudents);
            // then get query result for GPA, Factor and Retention
        }

        $factorJSON = null;
        $GPAJSON = null;
        $retentionCompletionJSON = null;
        $factorQueryResults = [];
        $gpaQueryResults = [];
        $retentionQueryResults = [];

        if (!empty($queryResultsArray)) {
            $factorQueryResults = $queryResultsArray['factor'];
            $gpaQueryResults = $queryResultsArray['gpa'];
            $retentionQueryResults = $queryResultsArray['retention'];
            $factorJSON = json_encode($factorQueryResults, JSON_NUMERIC_CHECK);
            $GPAJSON = json_encode($gpaQueryResults, JSON_NUMERIC_CHECK);
            $retentionCompletionJSON = json_encode($retentionQueryResults, JSON_NUMERIC_CHECK);
        }

        if (empty($queryResultsArray) || (empty($factorQueryResults) && empty($gpaQueryResults) && empty($retentionQueryResults))) {
            $statusCode = ReportsConstants::REPORT_NO_DATA_CODE;
            $statusMessage = ReportsConstants::REPORT_NO_DATA_MESSAGE;
            $reportData = $this->buildReportStatus($reportData, $reportsRunningStatusObject, $statusMessage, false, $statusCode);
            $this->notificationChannelService->sendNotificationToAllRegisteredChannels($personObject, $shortCode);
            return $reportData;
        }


        $reportRunningStatusDtoJSON = $this->serializer->serialize($reportRunningStatusDto, 'json');

        $reportsRunningStatusJSON = $this->serializer->serialize($reportsRunningStatusObject, 'json');

        $reportsRunningJson = new ReportsRunningJson();
        $reportsRunningJson->setFactorJson($factorJSON);
        $reportsRunningJson->setGpaJson($GPAJSON);
        $reportsRunningJson->setRetentionCompletionJson($retentionCompletionJSON);
        $reportsRunningJson->setReportRunningStatusJson($reportsRunningStatusJSON);
        $reportsRunningJson->setRequestJson($reportRunningStatusDtoJSON);
        $this->reportsRunningJsonRepository->persist($reportsRunningJson);
        $this->reportsRunningJsonRepository->flush();


        $compareReportJsonId = $reportsRunningJson->getId();


        // execute r-script

        $RScriptSystemPath = $this->ebiConfigService->get(ReportsConstants::EBI_CONFIG_R_SCRIPT_HOST_KEY);
        $RScriptPath = $this->ebiConfigService->get(ReportsConstants::EBI_CONFIG_R_SCRIPT_SYSTEM_PATH_KEY);

        $rScriptExecutionStatus = $this->comparisonReportDAO->executeRscriptJob($compareReportJsonId, $RScriptSystemPath, $RScriptPath);
        if ($rScriptExecutionStatus == 'NULL') {
            $errorMessage = 'There has been a failure in the script that calculates the values in this report. Please contact Mapworks Client Services.';
            $reportData = $this->buildReportStatus($reportData, $reportsRunningStatusObject, $errorMessage);
        }

        $this->notificationChannelService->sendNotificationToAllRegisteredChannels($personObject, $shortCode);

        return $reportData;
    }


    /**
     * Builds the ReportsRunningStatus object for the report and loads status/error information into the report data
     *
     * @param array $reportData
     * @param ReportsRunningStatus $reportsRunningStatusObject
     * @param string $message
     * @param bool $isError
     * @param string $statusCode
     * @return array
     */
    public function buildReportStatus($reportData, $reportsRunningStatusObject, $message, $isError = true, $statusCode = '')
    {

        if ($isError) {
            $reportData['error'] = $message;
            $status = 'F';
            $this->logger->addError(json_encode($reportData));
        } else if ($statusCode != '') {
            $reportData['status_message'] = [
                'code' => $statusCode,
                'description' => $message
            ];
            $status = 'C';
            $this->logger->addWarning(json_encode($reportData));
        } else {
            $status = 'IP';
        }

        if (isset($reportsRunningStatusObject)) {
            $this->alertNotificationsService->createReportNotification($reportsRunningStatusObject);
            $this->updateReportRunningStatus($reportsRunningStatusObject, $reportData, $status);
        }

        return $reportData;
    }


    /**
     * Sets and Flushes the Updated Reports Running Status Object
     *
     * @param ReportsRunningStatus $reportsRunningStatusObject
     * @param array $reportData
     * @param string $status
     * @return bool
     */
    public function updateReportRunningStatus($reportsRunningStatusObject, $reportData, $status)
    {
        $reportsRunningStatusObject->setStatus($status);
        $responseJSON = $this->serializer->serialize($reportData, 'json');
        $reportsRunningStatusObject->setResponseJson($responseJSON);
        $this->reportsRunningStatusRepository->persist($reportsRunningStatusObject);
        $this->reportsRunningStatusRepository->flush();
        return true;
    }


    /**
     * Builds the report results array if there is no report data for factor or gpa.
     *
     * @param ReportsRunningStatus $reportsRunningStatusObject
     * @param ReportRunningStatusDto $reportRunningStatusDto
     * @param Reports $reportsObject
     * @param Person $personObject
     * @param int $reportsRunningStatusId
     * @return array
     */
    public function buildReportItems($reportsRunningStatusObject, $reportRunningStatusDto, $reportsObject, $personObject, $reportsRunningStatusId)
    {
        $reportGeneratedTime = $reportsRunningStatusObject->getCreatedAt();
        $reportGeneratedTimestamp = $reportGeneratedTime->getTimestamp();
        $reportData = [];
        $reportData['request_json'] = $reportRunningStatusDto;
        $reportData['report_info'] = [
            'report_id' => $reportRunningStatusDto->getReportId(),
            'report_name' => $reportsObject->getName(),
            'short_code' => $reportsObject->getShortCode(),
            'report_instance_id' => $reportsRunningStatusId,
            'report_date' => date(SynapseConstant::DATE_FORMAT_WITH_TIMEZONE, $reportGeneratedTimestamp),
            'report_by' => [
                'first_name' => $personObject->getFirstname(),
                'last_name' => $personObject->getLastname()
            ]
        ];
        return $reportData;

    }

    /**
     * Gets the data & case statement for GPA, factor and retention queries
     *
     * @param int $organizationId
     * @param int $personId
     * @param string $surveyType
     * @param int $cohortId
     * @param int $surveyId
     * @param int $gpaYearId
     * @param int $surveyYearId
     * @param array $surveyData - details of survey whether ISQ or survey from json passed
     * @param int $retentionTrackingYearId
     * @param array|null $filteredStudents
     * @return array
     */
    public function getQueryDataForISQorSurveyWithGpaFactorAndRetention($organizationId, $personId, $surveyType, $cohortId, $surveyId, $gpaYearId, $surveyYearId, $surveyData, $retentionTrackingYearId, $filteredStudents = null)
    {
        $factorAndGpaQueryArray = [];
        $factorQuery = '';
        $gpaQuery = '';
        $retentionQuery = '';

        if ($surveyType == '' || $surveyData == '') {
            $factorAndGpaQueryArray['factor'] = $factorQuery;
            $factorAndGpaQueryArray['gpa'] = $gpaQuery;
            $factorAndGpaQueryArray['retention'] = $retentionQuery;
            return $factorAndGpaQueryArray;
        }

        $questionId = $surveyData['question_id'];
        $questionType = $surveyData['type'];
        $studentPopulationSurveyId = $surveyData['survey_id'];

        $rangeInDifferentQuestionTypes = [];

        for ($subPopulationCount = 1; $subPopulationCount <= 2; $subPopulationCount++) {
            if ($questionType == 'category') {

                // get each category_type value
                $rangeInDifferentQuestionTypes['category']['subpopulation' . $subPopulationCount] = array_column($surveyData['subpopulation' . $subPopulationCount]['category_type'], 'value');
            } elseif ($questionType == 'number') {

                // check whether selection is single range or multi range in population
                if ($surveyData['subpopulation' . $subPopulationCount]['is_single']) {  // if user selection is single range only
                    $rangeInDifferentQuestionTypes['number']['subpopulation' . $subPopulationCount]['single_value'] = $surveyData['subpopulation' . $subPopulationCount]['single_value'];
                } else {
                    $rangeInDifferentQuestionTypes['number']['subpopulation' . $subPopulationCount]['min_digits'] = $surveyData['subpopulation' . $subPopulationCount]['min_digits'];
                    $rangeInDifferentQuestionTypes['number']['subpopulation' . $subPopulationCount]['max_digits'] = $surveyData['subpopulation' . $subPopulationCount]['max_digits'];
                }
            }
        }

        // get select case clause sql for GPA, factor and retention
        $selectCaseClause = $this->surveyResponseRepository->createCaseQueryForISQorSurvey($rangeInDifferentQuestionTypes, $surveyType);

        if ($surveyType == 'isqs') {
            $gpaQuery = $this->orgQuestionResponseRepository->getGPAdataForISQ($organizationId, $personId, $gpaYearId, $questionId, $selectCaseClause, $filteredStudents);
            $factorQuery = $this->orgQuestionResponseRepository->getFactorDataForISQ($organizationId, $surveyId, $cohortId, $questionId, $personId, $surveyYearId, $selectCaseClause, $studentPopulationSurveyId, $filteredStudents);
            $retentionQuery = $this->orgQuestionResponseRepository->getRetentionDataForISQ($organizationId, $personId, $questionId, $studentPopulationSurveyId, $retentionTrackingYearId, $selectCaseClause, $filteredStudents);
        } elseif ($surveyType == 'survey') {
            $gpaQuery = $this->surveyResponseRepository->getGPAdataForSurvey($organizationId, $personId, $gpaYearId, $questionId, $selectCaseClause, $filteredStudents);
            $factorQuery = $this->surveyResponseRepository->getFactorDataForSurvey($organizationId, $surveyId, $cohortId, $questionId, $personId, $surveyYearId, $selectCaseClause, $studentPopulationSurveyId, $filteredStudents);
            $retentionQuery = $this->surveyResponseRepository->getRetentionDataForSurvey($organizationId, $personId, $questionId, $studentPopulationSurveyId, $retentionTrackingYearId, $selectCaseClause, $filteredStudents);
        }

        $factorAndGpaQueryArray['factor'] = $factorQuery;
        $factorAndGpaQueryArray['gpa'] = $gpaQuery;
        $factorAndGpaQueryArray['retention'] = $retentionQuery;
        return $factorAndGpaQueryArray;
    }

    /**
     * Gets the data & case statement for GPA, factor and retention queries of datablock Or Isp
     *
     * @param int $organizationId
     * @param int $personId
     * @param array $mandatoryFilters
     * @param int $cohortId
     * @param int $surveyId
     * @param int $gpaYearId
     * @param int $surveyYearId
     * @param int $retentionTrackingYearId
     * @param array|null $filteredStudents
     * @return array
     */
    public function getQueryDataForDataBlockOrIspWithGpaFactorAndRetention($organizationId, $personId, $mandatoryFilters, $cohortId, $surveyId, $gpaYearId, $surveyYearId, $retentionTrackingYearId, $filteredStudents = null)
    {
        $queryResultsArray = [];
        if (!empty($mandatoryFilters['isps'])) {
            $ISPDataArray = $mandatoryFilters['isps'];
            $orgMetaDataId = $ISPDataArray['id'];
            if ($ISPDataArray['calendar_assignment'] == 'T' || $ISPDataArray['calendar_assignment'] == 'Y') {
                $orgAcademicTermId = !empty($ISPDataArray['year_term']['term_id']) ? $ISPDataArray['year_term']['term_id'] : null;
                $orgAcademicYearId = $ISPDataArray['year_term']['year_id'];
            } else {
                $orgAcademicTermId = null;
                $orgAcademicYearId = null;
            }
            $selectCaseQuery = $this->comparisonReportDAO->createCaseQueryForIspOrProfileblock($ISPDataArray, 'isp');
            $queryResultsArray['factor'] = $this->personOrgMetaDataRepository->getFactorDataForISP($orgMetaDataId, $organizationId, $personId, $cohortId, $surveyId, $surveyYearId, $orgAcademicTermId, $orgAcademicYearId,  $selectCaseQuery, $filteredStudents);
            $queryResultsArray['gpa'] = $this->personOrgMetaDataRepository->getGpaDataForISP($orgMetaDataId, $organizationId, $personId, $gpaYearId, $orgAcademicTermId, $orgAcademicYearId, $selectCaseQuery, $filteredStudents);
            $queryResultsArray['retention'] = $this->personOrgMetaDataRepository->getRetentionDataForISP($orgMetaDataId, $organizationId, $personId, $retentionTrackingYearId, $orgAcademicTermId, $orgAcademicYearId, $selectCaseQuery, $filteredStudents);
        } elseif (!empty($mandatoryFilters['datablocks'])) {
            $profileDataArray = $mandatoryFilters['datablocks']['profile_items'];
            $profileDataId = $profileDataArray['id'];
            if ($profileDataArray['calendar_assignment'] == 'T' || $profileDataArray['calendar_assignment'] == 'Y') {
                $orgAcademicTermId = !empty($profileDataArray['year_term']['term_id']) ? $profileDataArray['year_term']['term_id'] : null;
                $orgAcademicYearId = $profileDataArray['year_term']['year_id'];
            } else {
                $orgAcademicTermId = null;
                $orgAcademicYearId = null;
            }
            $selectCaseQuery = $this->comparisonReportDAO->createCaseQueryForIspOrProfileblock($profileDataArray, 'profile');
            $queryResultsArray['factor'] = $this->personEbiMetaDataRepository->getFactorDataForProfileblock($profileDataId, $organizationId, $personId, $cohortId, $surveyId, $surveyYearId,$orgAcademicTermId, $orgAcademicYearId, $selectCaseQuery, $filteredStudents);
            $queryResultsArray['gpa'] = $this->personEbiMetaDataRepository->getGpaDataForProfileblock($profileDataId, $organizationId, $personId, $gpaYearId, $orgAcademicTermId, $orgAcademicYearId, $selectCaseQuery, $filteredStudents);
            $queryResultsArray['retention'] = $this->personEbiMetaDataRepository->getRetentionDataForProfileBlock($profileDataId, $organizationId, $personId, $retentionTrackingYearId, $orgAcademicTermId, $orgAcademicYearId, $selectCaseQuery, $filteredStudents);
        }
        return $queryResultsArray;
    }
     
     /**
     * Takes the responseJson as input, decodes it, build the header rows and preliminary rows,
     * then passes those plus the decoded JSON as an array to generateCSV function in CSVUtility Service.
     *
     * @param array $responseJson
     * @return string filename
     */
    public function generateCompareReportCSV($responseJson)
    {

        $organizationId = $responseJson['request_json']['organization_id'];
        $mandatoryFilters = '';
        $selectedSubpopulationsArray = '';

        if(empty($responseJson['report_info'])
            || empty($responseJson['request_json']['mandatory_filters'])
            || empty($responseJson['request_json']['mandatory_filters']['filterText']))
        {
            throw new SynapseValidationException(ReportsConstants::REPORT_CSV_ERROR);
        }

        $createdBy = 'Created by '.$responseJson['report_info']['report_by']['first_name']." ".$responseJson['report_info']['report_by']['last_name'];

        $reportDateTime = new \DateTime($responseJson['report_info']['report_date']);

        $formattedReportDateTimeForCSV = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $reportDateTime, SynapseConstant::DATE_AT_TIME_TIMEZONE_FORMAT);
        $createdBy .= " on ".$formattedReportDateTimeForCSV;

        $mandatoryFilters = $responseJson['request_json']['mandatory_filters'];

        $surveyFactorName = $mandatoryFilters['org_academic_year']['year']['name'].
                                        ' '.$mandatoryFilters['latest_survey']['name'].
                                        ' '.$mandatoryFilters['latest_cohort']['name'];
        $surveyFactorNameForCSV = 'SURVEY FACTORS for '.$surveyFactorName;

        $gpaTermYearName = $mandatoryFilters['gpa_org_academic_year']['year']['name'];

        $retentionYearName = $mandatoryFilters['retention_org_academic_year']['year']['name'];

        if (!empty($mandatoryFilters['isps'])) {
            $selectedSubpopulationsArray = $mandatoryFilters['isps'];
        } elseif(!empty($mandatoryFilters['datablocks'])) {
            $selectedSubpopulationsArray = $mandatoryFilters['datablocks']['profile_items'];
        } elseif(!empty($mandatoryFilters['isqs'])) {
            $selectedSubpopulationsArray = $mandatoryFilters['isqs'];
        } elseif(!empty($mandatoryFilters['survey']))  {
            $selectedSubpopulationsArray = $mandatoryFilters['survey'];
        }

        $filterTextArray = $mandatoryFilters['filterText'];
        if (isset($responseJson['request_json']['search_attributes']['filterText'])) {
            $optionalFilterText = trim($responseJson['request_json']['search_attributes']['filterText']);
        } else {
            $optionalFilterText = '';
        }

        $csvPreliminaryRows = $this->getCSVPreliminaryRows($filterTextArray, $selectedSubpopulationsArray);


        $preliminaryRows = [
            ['Compare Report'],
            [$createdBy],
            [$csvPreliminaryRows['selected_text']],
            [$csvPreliminaryRows['subpopulation1_text']],
            [$csvPreliminaryRows['subpopulation2_text']]
        ];

        if (!empty($optionalFilterText)) {
            $preliminaryRows[] = ["Additional filter criteria: ". $optionalFilterText];
        }

        $preliminaryRows[] = [''];
        $preliminaryRows[] = [$surveyFactorNameForCSV];

        $currentDateTime = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, SynapseConstant::DEFAULT_CSV_FILENAME_DATETIME_FORMAT);
        $filePath = SynapseConstant::S3_ROOT . ReportsConstants::S3_REPORT_CSV_EXPORT_DIRECTORY . '/';
        $fileName = "$organizationId-compare_report_$currentDateTime.csv";

        $compareReportFactorCSVData  = $this->buildCompareReportCSVArraySections('factor', $responseJson['report_items']);

        $csvRowIndex = count($compareReportFactorCSVData) + 1;

        $compareReportGpaCSVData  = $this->buildCompareReportCSVArraySections('gpa', $responseJson['report_items'], $gpaTermYearName, $csvRowIndex);

        $csvRowIndex = $csvRowIndex + count($compareReportGpaCSVData) + 1;

        $compareReportRetentionCSVData  = $this->buildCompareReportCSVArraySections('retention_completion', $responseJson['report_items'], $retentionYearName, $csvRowIndex);

        $compareReportCSVData = array_merge($compareReportFactorCSVData, $compareReportGpaCSVData, $compareReportRetentionCSVData);

        $this->csvUtilityService->generateCSV($filePath, $fileName, $compareReportCSVData, null, $preliminaryRows);

        return $fileName;
    }

    /**
     * Generate compare report csv data.
     *
     * @param string $key (this is used to isolate the factor,gpa and retention data from reportItem)
     * @param array $reportItems
     * @param string $termYearName
     * @param int $csvRowCount
     * @return array
     */
     public function buildCompareReportCSVArraySections($key, $reportItems, $termYearName = null, $csvRowCount = 0)
    {
        $compareReportdata = array();
        if ($key == "factor") {
            $compareReportdata[$csvRowCount][] = 'Factor';
            $columnName = "factor_name";
        } elseif($key == 'retention_completion') {
            $compareReportdata[$csvRowCount][] = 'RETENTION/COMPLETION for Retention Tracking Group ' . $termYearName;
            $csvRowCount++;
            $columnName = "retention_completion_variable_name";
        } else {
            $compareReportdata[$csvRowCount][] = 'GPA BY TERM for '.$termYearName;
            $csvRowCount++;
            $compareReportdata[$csvRowCount][] = 'Term GPA for Academic Year: ' . $termYearName;
            $columnName = "term_name";
        }

        if(!empty($reportItems[$key])) {
            if($key == 'retention_completion') {
                $compareReportdata[$csvRowCount][] = 'Retention/Completion';
                $compareReportdata[$csvRowCount][] = 'Population';
                $compareReportdata[$csvRowCount][] = 'N';
                $compareReportdata[$csvRowCount][] = 'Percent Retained';
                $compareReportdata[$csvRowCount][] = 'Chi-square';
            } else {
                $compareReportdata[$csvRowCount][] = 'Population';
                $compareReportdata[$csvRowCount][] = 'N';
                $compareReportdata[$csvRowCount][] = 'Mean';
                $compareReportdata[$csvRowCount][] = 'Standard Deviation';
                $compareReportdata[$csvRowCount][] = 't-score';
            }
            $compareReportdata[$csvRowCount][] = 'p-value';
            foreach ($reportItems[$key] as $id => $value) {
                for ($subPopulationCount = 1; $subPopulationCount <= 2; $subPopulationCount++) {
                    if (!empty($value['subpopulation' . $subPopulationCount])) {
                        $csvRowCount++;
                        $compareReportdata[$csvRowCount][] = $value[$columnName];
                        $compareReportdata[$csvRowCount][] = $value['subpopulation' . $subPopulationCount]['name'];
                        $compareReportdata[$csvRowCount][] = $value['subpopulation' . $subPopulationCount]['n_value'];
                        if($key == 'retention_completion') {
                            $compareReportdata[$csvRowCount][] = $value['subpopulation' . $subPopulationCount]['percentage_retained'];
                            $compareReportdata[$csvRowCount][] = ($value['chi_square'] == '-') ? '' : $value['chi_square'];
                        } else {
                            $compareReportdata[$csvRowCount][] = $value['subpopulation' . $subPopulationCount]['mean_value'];
                            $compareReportdata[$csvRowCount][] = $value['subpopulation' . $subPopulationCount]['standard_deviation_value'];
                            $compareReportdata[$csvRowCount][] = ($value['t_score'] == '-') ? '' : $value['t_score'];
                        }
                        $compareReportdata[$csvRowCount][] = ($value['p_value'] == '-') ? '' : $value['p_value'];
                    }
                }
            }
        }

        $csvRowCount++;
        $compareReportdata[$csvRowCount][] = '';

        return $compareReportdata;
    }

    /** Based on the type of mandatory filter used to generate the Compare report,
     * builds the selected mandatory filter descriptor for the CSV header row.
     *
     * @param array $filterTextArray
     * @param array $selectedIspOrIsqBlocks
     * @return array
     */
    public function getCSVPreliminaryRows($filterTextArray, $selectedIspOrIsqBlocks)
    {

        $selectedItem = '';
        $csvPreliminaryRows = array();
        $yearAndTermInfo = '';

        if (!empty($filterTextArray)) {
            if ($filterTextArray[0] == 'isp') {
                $selectedItem = ReportsConstants::ISP_FULL_NAME;
            } elseif ($filterTextArray[0] == 'profileItem') {
                $selectedItem = ReportsConstants::PROFILE_ITEM_FULL_NAME;
            } elseif ($filterTextArray[0] == 'isq') {
                $selectedItem = ReportsConstants::ISQ_FULL_NAME;
            } elseif ($filterTextArray[0] == 'surveyQuestion') {
                $selectedItem = ReportsConstants::SURVEY_FULL_NAME;
            }

            if (!empty($filterTextArray[1]['yearTerm'])) {
                $yearAndTermInfo = '[for ' . $filterTextArray[1]['yearTerm'] . ']';
            }
            $selectedText = "Selected " . $selectedItem . ' ' . $yearAndTermInfo . ": " . $filterTextArray[1]['text'];
            $csvPreliminaryRows['selected_text'] = $selectedText;
        }

        $subpopulation1ValuesString = $this->getSubPopulationHeaderText('subpopulation1', $selectedIspOrIsqBlocks);
        $subpopulation2ValuesString = $this->getSubPopulationHeaderText('subpopulation2', $selectedIspOrIsqBlocks);

        $subpopulation1Text = "Subpopulation 1: " . $filterTextArray[2][0] . ', defined as: ' . $subpopulation1ValuesString;
        $subpopulation2Text = "Subpopulation 2: " . $filterTextArray[2][1] . ', defined as: ' . $subpopulation2ValuesString;

        $csvPreliminaryRows['subpopulation1_text'] = $subpopulation1Text;
        $csvPreliminaryRows['subpopulation2_text'] = $subpopulation2Text;

        return $csvPreliminaryRows;

    }

    /**
     * Creates the subpopulation header text based on the selected ISP or ISQ block
     * Sentence structure as follows:
     * SELECTED $MANDATORY FILTER$ [FOR $YEAR$ OR $TERM$]: $SELECTED MANDATORY FILTER ITEM NAME$
     *
     * @param string $subPopulationKey
     * @param array $selectedIspOrIsqBlocks
     * @return string
     */
    public function getSubPopulationHeaderText($subPopulationKey, $selectedIspOrIsqBlocks)
    {
        $subPopulationValuesString = "";
        $profileItemDataType = "";
        $surveyDataType = "";

        if (!empty($selectedIspOrIsqBlocks['item_data_type'])) {
            $profileItemDataType = $selectedIspOrIsqBlocks['item_data_type'];
        } elseif (!empty($selectedIspOrIsqBlocks['type'])) {
            $surveyDataType = $selectedIspOrIsqBlocks['type'];
        }

        $subPopulationData = $selectedIspOrIsqBlocks[$subPopulationKey];

        $isScaledProfileItem = (!empty($profileItemDataType) && $profileItemDataType == 'S');
        $isNumericProfileItem = (!empty($profileItemDataType) && $profileItemDataType == 'N');
        $isDateProfileItem = (!empty($profileItemDataType) && $profileItemDataType == 'D');

        $isCategoricalSurveyQuestion = (!empty($surveyDataType) && $surveyDataType == 'category');
        $isNumericSurveyQuestion = (!empty($surveyDataType) && $surveyDataType == 'number');

        if (!empty($profileItemDataType) || !empty($surveyDataType)) {

            if ($isScaledProfileItem || $isCategoricalSurveyQuestion) {
                $subPopulationValuesString .= implode(', ', array_column($subPopulationData['category_type'], 'answer'));
            } elseif ($isNumericProfileItem || $isNumericSurveyQuestion) {

                if ($subPopulationData['is_single']) {
                    $subPopulationValuesString .= $subPopulationData['single_value'];
                } else {
                    $subPopulationValuesString .= 'between ' . $subPopulationData['min_digits'] . " and " . $subPopulationData['max_digits'];
                }
            } elseif ($isDateProfileItem) {
                $subPopulationValuesString .= $subPopulationData['start_date'] . ' and ' . $subPopulationData['end_date'];
            }
        }

        return $subPopulationValuesString;
    }

    /**
     *  This is a wrapper function to build the where clause and pass it into the repository function that forms the main query
     *  It executes the final SQL query to fetch student ids list based on mandatory(ISP selection scenario) and optional filter selections
     *
     * @param int $organizationId
     * @param array $mandatoryFilters
     * @return array
     */
     public function buildAndExecuteISPQuery($organizationId, $mandatoryFilters)
     {
            $ispDataArray = $mandatoryFilters['isps'];

            $whereClause = $this->createWhereClauseForIspOrIsq($ispDataArray,'isp');
            $orgMetadataId = $ispDataArray['id'];

            $personIds = $this->personOrgMetaDataRepository->getStudentIdsListBasedOnISPSelection($organizationId, $orgMetadataId, $whereClause);
            return $personIds;
     }

    /**
     *  This is a wrapper functions to build the where clause and pass it into the repository function that forms the main query
     *  It executes the final SQL query to fetch student ids list based on mandatory(Profile Item selection scenario) and optional filter selections
     *
     * @param int $organizationId
     * @param array $mandatoryFilters
     * @return array
     */
    public function buildAndExecuteProfileQuery($organizationId, $mandatoryFilters)
    {
        $profileDataArray = $mandatoryFilters['datablocks']['profile_items'];
        $whereClause = $this->createWhereClauseForIspOrIsq($profileDataArray,'profile');
        $ebiMetaDataId = $profileDataArray['id'];
        $personIds = $this->personEbiMetaDataRepository->getStudentIdsListBasedOnProfileItemSelection($organizationId, $ebiMetaDataId, $whereClause);
        return $personIds;
    }

    /**
     *  This is a wrapper functions to build the where clause and pass it into the repository function that forms the main query
     *  It executes the final SQL query to fetch student ids list based on mandatory(ISQ selection scenario) and optional filter selections
     *
     * @param int $organizationId
     * @param array $mandatoryFilters
     * @return array
     */
    public function buildAndExecuteISQQuery($organizationId, $mandatoryFilters)
     {
         $isqDataArray = $mandatoryFilters['isqs'];
         $whereClause = $this->createWhereClauseForIspOrIsq($isqDataArray,'isqs');
         $orgQuestionId = $isqDataArray['question_id'];
         $personIds = $this->orgQuestionResponseRepository->getStudentIdsListBasedOnISQSelection($organizationId, $orgQuestionId, $whereClause);
         return $personIds;
     }

    /**
     *  This is a wrapper functions to build the where clause and pass it into the repository function that forms the main query
     *  It executes the final SQL query to fetch student ids list based on mandatory(Survey Question selection scenario) and optional filter selections
     *
     * @param int $organizationId
     * @param array $mandatoryFilters
     * @return array
     */
    public function buildAndExecuteSurveyQuestionQuery($organizationId, $mandatoryFilters)
    {
        $surveyDataArray = $mandatoryFilters['survey'];
        $whereClause = $this->createWhereClauseForIspOrIsq($surveyDataArray,'survey');
        $ebiQuestionId = $surveyDataArray['question_id'];
        $personIds = $this->surveyResponseRepository->getStudentIdsListBasedOnSurveyQuestionSelection($organizationId, $ebiQuestionId, $whereClause);
        return $personIds;
    }

    /**
     * Creates/builds the SQL WHERE clause for the ISP & Profile Item or ISQ & survey scenario for Compare Report
     * This WHERE clause will be appended to the student count (How Many Students Are Included)
     * query to arrive at the count of students based on the applied mandatory and optional filters
     *
     * @param array $dataArray Array holding all data values for ISP/Profile or ISQ/Survey Item selection flow by user
     * @param string $metadataType Takes values "isp" or "profile" or "isqs" or "survey"
     * @return array
     */
    public function createWhereClauseForIspOrIsq($dataArray, $metadataType)
    {

        $whereClause = '';
        if ($metadataType == 'isp') {
            $tableAlias = 'pom';
            $tableColumn = 'metadata_value';
        } else if ($metadataType == 'profile') {
            $tableAlias = 'pem';
            $tableColumn = 'metadata_value';
        } elseif ($metadataType == 'isqs') {
            $tableAlias = 'oqr';
            $tableColumn = 'decimal_value';
        } elseif ($metadataType == 'survey') {
            $tableAlias = 'sr';
            $tableColumn = 'decimal_value';
        } else {
            throw new SynapseValidationException(ReportsConstants::INVALID_METADATA_TYPE_ERROR_MESSAGE);
        }
        if ((!empty($dataArray['item_data_type']) && $dataArray['item_data_type'] == 'S') || ((!empty($dataArray['type']) && $dataArray['type'] == 'category'))) {

            $subpopulation1CategoryArray = $dataArray['subpopulation1']['category_type'];
            $subpopulation2CategoryArray = $dataArray['subpopulation2']['category_type'];

            $parameters["category_values"] = array_merge(array_column($subpopulation1CategoryArray, 'value'), array_column($subpopulation2CategoryArray, 'value'));

            $whereClause['where_query'] = "$tableAlias.$tableColumn IN (:category_values)";

            $whereClause['parameters'] = $parameters;

        } elseif ((!empty($dataArray['item_data_type']) && $dataArray['item_data_type'] == 'N') || ((!empty($dataArray['type']) && $dataArray['type'] == 'number'))) {
            if ($dataArray['subpopulation1']['is_single']) {
                $parameters['subpopulation1_single_values'] = $dataArray['subpopulation1']['single_value'];
                $subpopulation1WhereClause = "$tableAlias.$tableColumn IN (:subpopulation1_single_values)";

            } else {
                $parameters['subpopulation1_min_digits'] = $dataArray['subpopulation1']['min_digits'];
                $parameters['subpopulation1_max_digits'] = $dataArray['subpopulation1']['max_digits'];
                $subpopulation1WhereClause = "($tableAlias.$tableColumn BETWEEN :subpopulation1_min_digits AND :subpopulation1_max_digits)";
            }

            if ($dataArray['subpopulation2']['is_single']) {
                $parameters['subpopulation2_single_values'] = $dataArray['subpopulation2']['single_value'];
                $subpopulation2WhereClause = "$tableAlias.$tableColumn IN (:subpopulation2_single_values)";

            } else {
                $parameters['subpopulation2_min_digits'] = $dataArray['subpopulation2']['min_digits'];
                $parameters['subpopulation2_max_digits'] = $dataArray['subpopulation2']['max_digits'];

                $subpopulation2WhereClause = "($tableAlias.$tableColumn BETWEEN :subpopulation2_min_digits AND :subpopulation2_max_digits)";

            }
            $whereClause['where_query'] = "(" . $subpopulation1WhereClause . " OR " . $subpopulation2WhereClause . ")";
            $whereClause['parameters'] = $parameters;
        } elseif ((!empty($dataArray['item_data_type']) && $dataArray['item_data_type'] == 'D')) {
            $metaValueDateFormat = SynapseConstant::METADATA_TYPE_DATE_FORMAT;
            $metaValueDefaultDateFormat = SynapseConstant::METADATA_TYPE_DEFAULT_DATE_FORMAT;

            $parameters['subpopulation1_start_date'] = $dataArray['subpopulation1']['start_date'];
            $parameters['subpopulation1_end_date'] = $dataArray['subpopulation1']['end_date'];

            $parameters['subpopulation2_start_date'] = $dataArray['subpopulation2']['start_date'];
            $parameters['subpopulation2_end_date'] = $dataArray['subpopulation2']['end_date'];

            $subpopulation1WhereClause = "((STR_TO_DATE($tableAlias.$tableColumn , '$metaValueDateFormat' ) BETWEEN STR_TO_DATE(:subpopulation1_start_date , '$metaValueDateFormat' ) AND STR_TO_DATE(:subpopulation1_end_date , '$metaValueDateFormat' )) OR (STR_TO_DATE($tableAlias.$tableColumn , '$metaValueDefaultDateFormat' ) BETWEEN STR_TO_DATE(:subpopulation1_start_date , '$metaValueDefaultDateFormat' ) AND STR_TO_DATE(:subpopulation1_end_date , '$metaValueDefaultDateFormat' )))";

            $subpopulation2WhereClause = "((STR_TO_DATE($tableAlias.$tableColumn , '$metaValueDateFormat' ) BETWEEN STR_TO_DATE(:subpopulation2_start_date , '$metaValueDateFormat' ) AND STR_TO_DATE(:subpopulation2_end_date , '$metaValueDateFormat' )) OR (STR_TO_DATE($tableAlias.$tableColumn , '$metaValueDefaultDateFormat' ) BETWEEN STR_TO_DATE(:subpopulation2_start_date , '$metaValueDefaultDateFormat' ) AND STR_TO_DATE(:subpopulation2_end_date , '$metaValueDefaultDateFormat' )))";

            $whereClause['where_query'] = "(" . $subpopulation1WhereClause . " OR " . $subpopulation2WhereClause . ")";
            $whereClause['parameters'] = $parameters;

        } else {
            throw new SynapseValidationException(ReportsConstants::INVALID_METADATA_TYPE_ERROR_MESSAGE);
        }

        return $whereClause;
    }

}