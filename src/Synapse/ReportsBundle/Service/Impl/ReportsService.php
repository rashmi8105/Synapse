<?php
namespace Synapse\ReportsBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use Codeception\Module\Doctrine2;
use JMS\DiExtraBundle\Annotation as DI;
use SplFileObject;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\DatablockQuestionsRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\ActivityService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\OrganizationlangService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\CoreBundle\Service\Impl\OrgProfileService;
use Synapse\CoreBundle\Service\Impl\ProfileService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\CoreBundle\Util\UtilServiceHelper;
use Synapse\MapworksToolBundle\DAO\IssueDAO;
use Synapse\PersonBundle\Service\PersonService;
use Synapse\ReportsBundle\DAO\GroupResponseReportDAO;
use Synapse\ReportsBundle\EntityDto\AcademicUpdateReportDto;
use Synapse\ReportsBundle\EntityDto\ActivitiesDto;
use Synapse\ReportsBundle\EntityDto\CampusActivityDto;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\EntityDto\SurveyStatusReportDto;
use Synapse\ReportsBundle\Job\SurveyStudentResponseJob;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository;
use Synapse\ReportsBundle\Repository\ReportSectionElementsRepository;
use Synapse\ReportsBundle\Repository\ReportSectionsRepository;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\SearchBundle\EntityDto\SearchDto;
use Synapse\SearchBundle\EntityDto\SearchResultListDto;
use Synapse\SearchBundle\Repository\OrgSearchRepository;
use Synapse\SearchBundle\Service\Impl\SearchService;
use Synapse\SurveyBundle\Repository\OrgPersonStudentSurveyLinkRepository;
use Synapse\SurveyBundle\Repository\SurveyQuestionsRepository;
use Synapse\SurveyBundle\Repository\WessLinkRepository;
use Synapse\SurveyBundle\Service\Impl\SurveyBlockService;

/**
 * @DI\Service("reports_service")
 */
class ReportsService extends ReportsHelperService
{

    const SERVICE_KEY = "reports_service";

    // Class Constants

    const PAGE_NO = 1;

    const OFFSET = 25;


    // Class VariablesDataProcessingUtilityService

    private $demographicItemArr = array(
        "AthleteStudent" => 21,
        "CampusResident" => 22,
        "EnrollType" => 23,
        "Gender" => 24,
        "GuardianEdLevel" => 25,
        "InternationalStudent"=> 26,
        "MilitaryServedUS" => 27,
        "RaceEthnicity"=> 28,
        "High School GPA" => 29,
        "Has Dependents" => 30
    );


    // Scaffolding

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Doctrine2
     */
    private $doctrine;

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var Serializer
     */
    private $serializer;


    // Services

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
     * @var comparisonReportService
     */
    private $comparisonReportService;

    /**
     * @var CompletionReportService
     */
    private $completionReportService;

    /**
     * @var CSVUtilityService
     */
    private $CSVUtilityService;

    /**
     * @var DataProcessingUtilityService
     */
    private $dataProcessingUtilityService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var ExecutiveSummaryService
     */
    private $executiveSummaryService;

    /**
     * @var FactorReportService
     */
    private $factorReportService;

    /**
     * @var FacultyUsageService
     */
    private $facultyUsageService;

    /**
     * @var GPAReportService
     */
    private $gpaReportService;

    /**
     * @var GroupResponseReportService
     */
    private $groupResponseReportService;

    /**
     * @var OrganizationlangService
     */
    private $organizationLangService;

    /**
     * @var OrgPermissionsetService
     */
    private $orgPermissionSetService;

    /**
     * @var OrgProfileService
     */
    private $orgProfileService;

    /**
     * @var OrganizationService
     */
    private $orgService;

    /**
     * @var OurStudentsReportService
     */
    private $ourStudentsReportService;

    /**
     * @var PdfReportsService
     */
    private $pdfReportService;

    /**
     * @var ProfileService
     */
    private $profileService;

    /**
     * @var ProfileSnapshotService
     */
    private $profileSnapshotService;

    /**
     * @var ReportsDtoVerificationService
     */
    private $reportsDtoVerificationService;

    /**
     * @var SearchService
     */
    private $searchService;

    /**
     * @var SurveyBlockService
     */
    private $surveyblockService;

    /**
     * @var SurveySnapshotService
     */
    private $surveySnapshotService;

    /**
     * @var UtilServiceHelper
     */
    private $utilServiceHelper;


    // DAO

    /**
     * @var GroupResponseReportDAO
     */
    private $groupResponseReportDAO;

    /**
     * @var IssueDAO
     */
    private $issueDAO;


    // Repositories

    /**
     * @var DatablockQuestionsRepository
     */
    private $dataBlockQuestionRepository;

    /**
     * @var MetadataListValuesRepository
     */
    private $metadataListValuesRepository;

    /**
     * @var OrgAcademicTermRepository
     */
    private $orgAcademicTermRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;


    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;


    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     * @var OrgPersonStudentRetentionTrackingGroupRepository
     */
    private $orgPersonStudentRetentionTrackingGroupRepository;

    /**
     * @var OrgPersonStudentSurveyLinkRepository
     */
    private $orgPersonStudentSurveyLinkRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;


    /**
     * @var OrgSearchRepository
     */
    private $orgSearchRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var PersonEbiMetaDataRepository
     */
    private $personEbiMetadataRepository;

    /**
     * @var ReportsRepository
     */
    private $reportsRepository;

    /**
     * @var ReportSectionsRepository
     */
    private $reportSectionsRepository;

    /**
     * @var ReportsRunningStatusRepository
     */
    private $reportsRunningStatusRepository;

    /**
     * @var ReportSectionElementsRepository
     */
    private $reportSectionElementsRepository;

    /**
     * @var SurveyQuestionsRepository
     */
    private $surveyQuestionsRepository;

    /**
     * @var WessLinkRepository
     */
    private $wessLinkRepository;



    /**
     * ReportsService constructor.
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

        // Scaffolding
        $this->container = $container;
        $this->doctrine = $this->container->get(SynapseConstant::DOCTRINE_CLASS_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->serializer = $this->container->get(SynapseConstant::JMS_SERIALIZER_CLASS_KEY);

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->activityReportService = $this->container->get(ActivityReportService::SERVICE_KEY);
        $this->activityService = $this->container->get(ActivityService::SERVICE_KEY);
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->comparisonReportService = $this->container->get(ComparisonReportService::SERVICE_KEY);
        $this->completionReportService = $this->container->get(CompletionReportService::SERVICE_KEY);
        $this->CSVUtilityService = $this->container->get(CSVUtilityService::SERVICE_KEY);
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->executiveSummaryService = $this->container->get(ExecutiveSummaryService::SERVICE_KEY);
        $this->factorReportService = $this->container->get(FactorReportService::SERVICE_KEY);
        $this->facultyUsageService =  $this->container->get(FacultyUsageService::SERVICE_KEY);
        $this->gpaReportService = $this->container->get(GPAReportService::SERVICE_KEY);
        $this->groupResponseReportService = $this->container->get(GroupResponseReportService::SERVICE_KEY);
        $this->organizationLangService = $this->container->get(OrganizationlangService::SERVICE_KEY);
        $this->orgProfileService = $this->container->get(OrgProfileService::SERVICE_KEY);
        $this->orgService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->ourStudentsReportService = $this->container->get(OurStudentsReportService::SERVICE_KEY);
        $this->comparisonReportService = $this->container->get(ComparisonReportService::SERVICE_KEY);
        $this->pdfReportService = $this->container->get(PdfReportsService::SERVICE_KEY);
        $this->orgPermissionSetService = $this->container->get(OrgPermissionsetService::SERVICE_KEY);
        $this->profileService = $this->container->get(ProfileService::SERVICE_KEY);
        $this->profileSnapshotService = $this->container->get(ProfileSnapshotService::SERVICE_KEY);
        $this->reportsDtoVerificationService = $this->container->get(ReportsDtoVerificationService::SERVICE_KEY);
        $this->searchService = $this->container->get(SearchService::SERVICE_KEY);
        $this->surveyblockService = $this->container->get(SurveyBlockService::SERVICE_KEY);
        $this->surveySnapshotService = $this->container->get(SurveySnapshotService::SERVICE_KEY);
        $this->utilServiceHelper = $this->container->get(UtilServiceHelper::SERVICE_KEY);

        // DAO
        $this->groupResponseReportDAO = $this->container->get(GroupResponseReportDAO::DAO_KEY);
        $this->issueDAO = $this->container->get(IssueDAO::DAO_KEY);

        // Repositories
        $this->dataBlockQuestionRepository = $this->repositoryResolver->getRepository(DatablockQuestionsRepository::REPOSITORY_KEY);
        $this->metadataListValuesRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->orgAcademicTermRepository = $this->repositoryResolver->getRepository(OrgAcademicTermRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRetentionTrackingGroupRepository =  $this->repositoryResolver->getRepository(OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY);
        $this->orgPersonStudentSurveyLinkRepository = $this->repositoryResolver->getRepository(OrgPersonStudentSurveyLinkRepository::REPOSITORY_KEY);
        $this->orgSearchRepository = $this->repositoryResolver->getRepository(OrgSearchRepository::REPOSITORY_KEY);
        $this->personEbiMetadataRepository = $this->repositoryResolver->getRepository(PersonEbiMetaDataRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->reportsRepository = $this->repositoryResolver->getRepository(ReportsRepository::REPOSITORY_KEY);
        $this->reportsRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsRunningStatusRepository::REPOSITORY_KEY);
        $this->reportSectionElementsRepository = $this->repositoryResolver->getRepository(ReportSectionElementsRepository::REPOSITORY_KEY);
        $this->reportSectionsRepository = $this->repositoryResolver->getRepository(ReportSectionsRepository::REPOSITORY_KEY);
        $this->surveyQuestionsRepository = $this->repositoryResolver->getRepository(SurveyQuestionsRepository::REPOSITORY_KEY);        
        $this->wessLinkRepository = $this->repositoryResolver->getRepository(WessLinkRepository::REPOSITORY_KEY);
    }


    /**
     * Gets all the students for a specific retention track for an organization
     *
     * @param integer $organizationId
     * @param integer $academicYearId
     * @return array
     */
    private function getStudentsBasedOnRetentionTrack($organizationId, $academicYearId)
    {
        $studentsArray = $this->orgPersonStudentRetentionTrackingGroupRepository->getRetentionTrackingGroupStudents($organizationId, $academicYearId);
        return $studentsArray;
    }

    /**
     * Method acts as an entry point fo any of the reports
     *
     * @param ReportRunningStatusDto $reportRunningDto
     * @param integer $loggedUserId
     * @param integer $orgId
     * @param string $outputFormat
     * @param integer $pageNumber
     * @param integer $recordsPerPage
     * @param string $sortBy
     * @param bool $runImmediate
     * @param array $rawData
     * @param bool $debug
     * @return array|ReportRunningStatusDto
     */
    public function generateReport($reportRunningDto, $loggedUserId, $orgId, $outputFormat, $pageNumber, $recordsPerPage, $sortBy, $runImmediate = false, $rawData, $debug = false){

        //Verifying Dto org and person are the same as actual logged in person and org
        $this->reportsDtoVerificationService->verifyDto($orgId, $loggedUserId, $reportRunningDto);

        $this->orgRoleRepo = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrganizationRole');
        $reportSection = $reportRunningDto->getReportSections();
        $reportId = $reportSection['reportId'];
        $reportDetailsObj = $this->getReportDetails($reportId);
        $isCoordinator = $this->orgRoleRepo->getUserCoordinatorRole($orgId, $loggedUserId); // check for coordinator
        if(($isCoordinator && $reportDetailsObj->getIsCoordinatorReport() == 'y') || $reportDetailsObj->getShortCode() == "FUR" ) {
            // dont check anything.. as coorfdinator should be able to access the coordinators report ,no permission check needed
        }else{
            $this->checkAccessPermission($loggedUserId, $reportDetailsObj);
        }
        if($reportDetailsObj && $reportDetailsObj->getShortCode() == 'SUR-GRR'){
            $returnVal = $this->groupResponseReportService->generateGroupResponseReport($reportRunningDto, $loggedUserId, $orgId, $outputFormat, $pageNumber, $recordsPerPage, $sortBy);
            return $returnVal;
        }else{

            $retentionStudentArr = array();
            if ($reportDetailsObj->getShortCode() == "PRR" || $reportDetailsObj->getShortCode() == "CR") {
                $searchAttr = $reportRunningDto->getSearchAttributes();
                $academicYearId = $searchAttr['retention_date']['academic_year_id'];
                $retentionStudentArr = $this->getStudentsBasedOnRetentionTrack($orgId, $academicYearId);

            }
            $reportRunningStatus = $this->pdfReportService->createReportRunningStatus($reportRunningDto, $retentionStudentArr);
            $reportInstanceId = $reportRunningStatus->getId();
            if($reportDetailsObj->getShortCode() == 'MAR'){
                $this->activityReportService->initiateReportJob($reportRunningDto,$loggedUserId,$orgId,$reportInstanceId,$rawData);
            }elseif($reportDetailsObj->getShortCode() == 'SUR-SR'){
                $surveyId = $reportRunningDto->getSearchAttributes()['survey_filter']['survey_id'];
                if($runImmediate){
                    $this->surveySnapshotService->generateReport( $reportInstanceId, $surveyId, $loggedUserId, $reportRunningDto);
                }else{
                    $this->surveySnapshotService->initiateSnapshotJob($reportRunningDto, $loggedUserId, $surveyId, $reportInstanceId);
                }

            }elseif($reportDetailsObj->getShortCode() == 'PRO-SR'){
                if ($runImmediate){
                    $this->profileSnapshotService->generateReport($reportInstanceId, $reportRunningDto);
                } else {
                    $this->profileSnapshotService->initiateProfileSnapshotJob($reportInstanceId, $reportRunningDto);
                }

            }elseif($reportDetailsObj->getShortCode() == 'OSR'){
                $this->initiateStudentReportJob($reportRunningDto, $loggedUserId,$orgId,$reportInstanceId,$rawData);
                //$report = $this->getOurStudentsReport($reportRunningDto, $loggedUserId);
                //return $report;
            }elseif($reportDetailsObj->getShortCode() == 'SUR-FR'){
                $surveyId = $reportRunningDto->getSearchAttributes()['survey_filter']['survey_id'];
                $this->factorReportService->initiateFactorReport($loggedUserId,$surveyId, $reportInstanceId, $reportRunningDto);
            }elseif($reportDetailsObj->getShortCode() == 'FUR'){
                if($runImmediate){
                    $reportData =   $this->facultyUsageService->generateReport($reportInstanceId, $reportRunningDto);
                    return $reportData;
                }
                $this->facultyUsageService->initiateReportJob($reportInstanceId, $reportRunningDto);
                if($outputFormat == "csv"){
                    return array("Your download is In progress. You will be receiving a notification once it will be completed");
                }
            }elseif($reportDetailsObj->getShortCode() == 'PRR'){

                $this->prrService = $this->container->get('persistence_retention_service');
                if($debug){
                    $reportData =   $this->prrService->generateReport($reportInstanceId, $reportRunningDto);
                    return $reportData;
                }
                $this->prrService->initiateReportJob($reportRunningDto);
            }elseif($reportDetailsObj->getShortCode() == 'GPA'){
                if($runImmediate){
                    $this->gpaReportService->generateReport($reportInstanceId, $reportRunningDto);
                }else {
                    $this->gpaReportService->initiateGPAReportServiceJob($reportInstanceId, $reportRunningDto);
                }
            }elseif($reportDetailsObj->getShortCode() == 'CR'){
                if($debug){
                    $reportData = $this->completionReportService->generateReport($loggedUserId, $reportRunningDto, $rawData);
                    return $reportData;
                }
                $this->completionReportService->initiateReportJob($loggedUserId, $reportRunningDto, $rawData);

            }elseif($reportDetailsObj->getShortCode() == 'EXEC'){
                if ($runImmediate) {
                    $this->executiveSummaryService->generateReport($reportInstanceId, $reportRunningDto);
                }else{
                    $this->executiveSummaryService->initiateExecutiveSummaryJob($reportInstanceId, $reportRunningDto);
                }
            } elseif ($reportDetailsObj->getShortCode() == 'SUB-COM') {
                if ($runImmediate) {
                    $this->comparisonReportService->generateReport($reportInstanceId, $reportRunningDto);
                } else {
                    $this->comparisonReportService->initiateCompareReport($reportRunningDto , $reportInstanceId);
                }
            }


            return $reportRunningStatus;
        }
    }

 public function initiateStudentReportJob($reportRunningDto,$loggedUserId,$orgId,$reportInstanceId,$rawData){

        $getreportSections = $reportRunningDto->getReportSections();
        $jobObj = 'Synapse\ReportsBundle\Job\StudentReport';
        $jobNumber = uniqid();
        $job = new $jobObj();
        $perObj = $this->container->get('person_service')->find($loggedUserId);
        $firstname =  $perObj->getFirstname();
        $lastname =  $perObj->getLastname();

        $job->args = array(
            'userId' => $loggedUserId,
            'reportRunByLastName' => $lastname,
            'reportRunByFirstName' => $firstname,
            'reportInstanceId' => $reportInstanceId,
            'reportSections' => $getreportSections,
            'reportRunningDto' => serialize($reportRunningDto),
            'reportData' => $rawData
        );
        $this->resque->enqueue($job, true);
    }

    /**
     * validate current logged in user belongs to the user associated with the report .
     *
     * @param int $personId
     * @param int $reportInstance
     * @throws AccessDeniedException
     * @return int
     */
    public function validateReportRunningStatusBelongsToPerson($personId, $reportInstance)
    {
        if($personId != $reportInstance->getPerson()->getId())
        {
            throw new AccessDeniedException('You are trying to access a report you did not generate.');
        }

        return $personId;
    }

    /**
     * Given the mandatory and optional filters for a report,
     * returns a count of the number of students that will be included in the report.
     *
     * @param SaveSearchDto $customSearchDto
     * @param int $loggedUserId
     * @param string $reportShortCode
     * @return array
     */
    public function getStudentCountBasedCriteria(SaveSearchDto $customSearchDto, $loggedUserId, $reportShortCode = '')
    {
        $organizationId = $customSearchDto->getOrganizationId();
        $organization = $this->organizationRepository->find($organizationId);
        $this->isObjectExist($organization, SearchConstant::ORGN_ERROR, SearchConstant::ORGN_ERKEY);

        $dataToReturn = [];
        $dataToReturn['organization_id'] = $organizationId;
        $dataToReturn['person_id'] = $loggedUserId;

        // Get Search Params from Request Dto
        $searchAttributes = $customSearchDto->getSearchAttributes();

        // For Group Response Report, get an accurate student count by using the same query that is used to get the student count for the top of the report.
        // Otherwise, we might be counting students who are only connected to the user via courses and are thus not included in the report.
        if ($reportShortCode == 'SUR-GRR') {
            $surveyFilter = $searchAttributes['survey_filter'];
            $totalCounts = $this->groupResponseReportDAO
                ->getOverallCountGroupStudentCountAndResponseRateByFaculty($loggedUserId, $surveyFilter['org_academic_year_id'], $surveyFilter['cohort'], $surveyFilter['survey_id'], $searchAttributes['filter_sort']);

            $dataToReturn['student_count'] = $totalCounts[0]['student_id_cnt'];
            return $dataToReturn;
        }

        // Check if AU report
        if ($reportShortCode == 'AU-R') {
            if (!array_key_exists('academic_updates', $searchAttributes)) {
                $searchAttributes['academic_updates'] = [];
            }
            $searchAttributes['academic_updates']['is_current_academic_year'] = true;
        }


        // Set a flag based on whether the report is an aggregate report.
        $shortCodesForAggregateReports = [
            'SUR-SR',
            'OSR',
            'SUR-FR',
            'SUR-GRR',
            'MAR',
            'PRR',
            'CR',
            'GPA',
            'PRO-SR',
            'EXEC',
            'SUB-COM'
        ];

        if (in_array(trim($reportShortCode), $shortCodesForAggregateReports)) {
            $aggregateReportPermission = true;
        } else {
            $aggregateReportPermission = false;
        }


        $studentListQuery = $this->searchService->getStudentListBasedCriteria($searchAttributes, $organizationId, $loggedUserId, '', $aggregateReportPermission);

        $studentCountQuery = "  SELECT
                                    COUNT(DISTINCT p.id) AS student_count
                                FROM
                                    person p
                                WHERE
                                    p.organization_id = " . $organization->getId() . "
                                  AND  " .$studentListQuery;


        $mandatoryFilterStudentArray = '';
        if ($reportShortCode == 'SUB-COM') {
            $mandatoryFilters = $customSearchDto->getMandatoryFilters();


            if (!empty($mandatoryFilters)) {

                if (!empty($mandatoryFilters['isps'])) {
                    $mandatoryFilterStudentArray = $this->comparisonReportService->buildAndExecuteISPQuery($organizationId, $mandatoryFilters);
                } elseif (!empty($mandatoryFilters['datablocks'])) {
                    $mandatoryFilterStudentArray = $this->comparisonReportService->buildAndExecuteProfileQuery($organizationId, $mandatoryFilters);
                } elseif (!empty($mandatoryFilters['isqs'])) {
                    $mandatoryFilterStudentArray = $this->comparisonReportService->buildAndExecuteISQQuery($organizationId, $mandatoryFilters);
                } elseif (!empty($mandatoryFilters['survey'])) {
                    $mandatoryFilterStudentArray = $this->comparisonReportService->buildAndExecuteSurveyQuestionQuery($organizationId, $mandatoryFilters);
                }
            }
            if (!empty($mandatoryFilterStudentArray)) {
                $mandatoryFilterStudentArrayText = implode(",", $mandatoryFilterStudentArray);
            } else {
                $mandatoryFilterStudentArrayText = -1; // so that the query does not fail and  would return 0
            }
            $studentCountQuery = $studentCountQuery . " AND p.id IN ( $mandatoryFilterStudentArrayText )";
        }



        if ($reportShortCode == "PRR" || $reportShortCode == "CR") {

            $academicYearId = $searchAttributes['retention_date']['academic_year_id'];
            $retentionStudentArray = $this->getStudentsBasedOnRetentionTrack($organizationId, $academicYearId);
            $retentionStudentArrayText = implode(",", $retentionStudentArray);
            if (trim($retentionStudentArrayText) == "") {
                $retentionStudentArrayText = -1;
            }
            $studentCountQuery = $studentCountQuery . " AND p.id IN ( $retentionStudentArrayText )";
        }



        $studentCountQuery = $this->replacePlaceHolders($studentCountQuery, $organizationId);

        $studentCount = $this->orgSearchRepository->getOrgSearch($studentCountQuery);

        $dataToReturn['student_count'] = $studentCount[0]['student_count'];

        return $dataToReturn;
    }

    public function getReportDetails($reportId){

        $this->reportsRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORTS_REPO);
        $reportObj = $this->reportsRepository->findOneById($reportId);
        return $reportObj;

    }

    private function isObjectExist($object, $message, $key)
    {
        if (! isset($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    /**
     * Get all reports based on user access
     *
     * @param Integer $loggedInUserId
     * @param String $filter
     * @param String $source
     * @return array
     */
    public function getUserReports($loggedInUserId, $filter, $source)
    {
        if ($filter != null) {
            $filter = strtolower($filter);
        }
        if ($source != null) {
            $source = strtolower($filter);
        }

        $personObject = $this->personRepository->find($loggedInUserId);
        $organizationId = $personObject->getOrganization()->getId();
        $reportJson = array();
        $reportIds = array();
        $allowedReport = $this->orgPermissionSetService->getAllowedReports($loggedInUserId);

        if (isset($allowedReport['reports_access'])) {
            $reportIds = array_column($allowedReport['reports_access'], 'id');
        }

        //Get reports based on type
        switch ($filter) {
            case 'all':
                $getAllReports = $this->reportsRepository->getAllNonCoordinatorsReports();
                break;
            case 'coordinator':
                $allCoordinatorReports = $this->reportsRepository->getAllCoordinatorReports();
                $specificReports = $this->reportsRepository->getSpecificReports($reportIds);
                $getAllReports = array_merge($allCoordinatorReports, $specificReports);
                break;
            case 'teamleader':
                $getAllReports = $this->reportsRepository->getReportsTeamLeader($reportIds);
                break;
            case 'faculty':
                $getAllReports = $this->reportsRepository->getSpecificReports($reportIds);
                break;
        }

        $courseAccess = $this->orgPermissionSetService->getCoursesAccess($loggedInUserId);
        $totalReports = 0;

        foreach ($getAllReports as $getReports) {
            $isCoordinatorReportShownOnPermissionPage = ($source == "permission" && ($getReports['is_coordinator_report'] == 'y'));
            $isStudentReport = ($getReports['name'] == ReportsConstants::STUDENT_REPORT);
            $isAcademicUpdateReportIncludedOnFiltersOtherThanAll = ($getReports['short_code'] == 'AU-R' && $filter != 'all');
            $doesUserHaveAllAcademicUpdatesCoursesPermission = (!$courseAccess['view_all_academic_update_courses']);
            if ($isCoordinatorReportShownOnPermissionPage || $isStudentReport || ($isAcademicUpdateReportIncludedOnFiltersOtherThanAll && $doesUserHaveAllAcademicUpdatesCoursesPermission)) {
                continue;
            }
            //Get report last run date
            $reportLastRunDate = $this->reportsRunningStatusRepository->getLastRunDateForMyReport($organizationId, $getReports['id'], $loggedInUserId);
            $lastRunDate = "";
            if (!empty($reportLastRunDate['modified_at'])) {
                $lastRunDateDateObject = new \DateTime($reportLastRunDate['modified_at']);
                $lastRunDate = $lastRunDateDateObject->format(SynapseConstant::DATE_FORMAT);
            }
            $viewName = $getReports['view_name'];
            $reportJson[$viewName][] = [
                "reportId" => (int)$getReports['id'],
                "reportName" => $getReports['name'],
                "reportDescription" => $getReports['description'],
                "isBatchJob" => $this->checkNullResponse($getReports['is_batch_job'] == 'y') ? true : false,
                "isCoordinatorReport" => $getReports['is_coordinator_report'],
                "shortCode" => $getReports['short_code'],
                "lastRunDate" => $lastRunDate
            ];
            $totalReports++;
        }
        $returnSet = array(
            'total_count' => $totalReports,
            'reports' => $reportJson
        );
        return $returnSet;
    }

    /**
     * Builds the data for the individual response report based on the passed in filters.
     *
     * @param SaveSearchDto $customSearchDto
     * @param integer $loggedUserId
     * @param string $outputFormat
     * @param string $pageNumber
     * @param string $offset
     * @param string $dataType
     * @param string $sortBy
     * @return array
     * @throws SynapseValidationException
     */
    public function getIndividualResponseReportData(SaveSearchDto $customSearchDto, $loggedUserId, $outputFormat = 'json', $pageNumber = '', $offset = '', $dataType = '', $sortBy = '')
    {
        // Report created By Details
        $personObject = $this->personRepository->find($loggedUserId);
        if (empty($personObject)) {
            throw new SynapseValidationException('The user does not exist within Mapworks');
        }

        $organizationId = $customSearchDto->getOrganizationId();
        $organization = $this->organizationRepository->find($organizationId);
        if (empty($organization)) {
            throw new SynapseValidationException('Organization Not Found.');
        }

        $reportSection = $customSearchDto->getReportSections();
        $reportId = $reportSection['reportId'];
        $reportDetailsObject = $this->getReportDetails($reportId);
        $participantStudentCount = 0;
        $nonParticipantStudentCount = 0;
        $totalStudentCountIncludingNonParticipants = 0;
        $respondentsCount = 0;
        $response = $searchAttributes = [];
        $organizationRoleObject = $this->organizationRoleRepository->findBy([
            'organization' => $organizationId,
            'person' => $loggedUserId
        ]);

        if (empty($organizationRoleObject)) {
            $this->checkAccessPermission($loggedUserId, $reportDetailsObject);
        }

        $customSearchAttributes = $customSearchDto->getSearchAttributes();
        $surveyId = ($customSearchAttributes['survey_filter']['survey_id']) ? $customSearchAttributes['survey_filter']['survey_id'] : 0 ;

        // Get student list filter query by using search attribute
        $studentListFilterQuery = $this->searchService->getStudentListBasedCriteria($customSearchAttributes, $organizationId, $loggedUserId);

        $pageNumber = (int)$pageNumber;
        if (!$pageNumber) {
            $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }
        $offset = (int)$offset;
        if (!$offset) {
            $offset = SynapseConstant::DEFAULT_RECORD_COUNT;
        }

        $calculateLimit = $this->getLimit($pageNumber, $offset);

        $personDetails = [];
        if (isset($personObject)) {
            $personDetails['first_name'] = $personObject->getFirstname();
            $personDetails['last_name'] = $personObject->getLastname();
        }

        $reportDateTime = $this->dateUtilityService->getTimezoneAdjustedCurrentDateTimeForOrganization($organizationId);
        $reportDate = date_format($reportDateTime, SynapseConstant::DATE_FORMAT_WITH_TIMEZONE);
        $organizationTimeZone = $reportDateTime->getTimezone()->getName();

        $studentList = $this->reportsRepository->getIndividualResponseReportData($surveyId, $studentListFilterQuery, '', false, true);
        if (empty($studentList)) {
            $response['total_records'] = 0;
            $response['status_message'] = [
                'code' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_CODE,
                'description' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_MESSAGE
            ];
            return $response;
        }

        if (strtolower($dataType) == 'student_list') {
            return $this->getStudentListResponse($studentList, $loggedUserId);
        }

        if ($outputFormat == 'csv') {
            $individualResponseReport = $this->reportsRepository->getIndividualResponseReportData($surveyId, $studentListFilterQuery, '');
            if (empty($individualResponseReport)) {
                $response['total_records'] = 0;
                $individualResponseReport['status_message'] = [
                    'code' => ReportsConstants::REPORT_NO_DATA_CODE,
                    'description' => ReportsConstants::REPORT_NO_DATA_MESSAGE
                ];
                return $individualResponseReport;
            } else {
                $fileName = $organizationId . "-individual-survey-response-" . time() . ".csv";
                $csvHeaders = array(
                    'student_id' => 'Student Id',
                    'first_name' => 'First Name',
                    'last_name' => 'Last Name',
                    'email' => 'Email',
                    'phone_number' => 'Phone Number',
                    'opted_out' => 'Opted Out',
                    'survey_status' => 'Survey Status',
                    'responded_at' => 'Date & Time( ' . $organizationTimeZone . ' )',
                    'survey_responded_status' => 'Survey Responded Status'
                );
                $filePath = SynapseConstant::S3_ROOT . ReportsConstants::S3_REPORT_CSV_EXPORT_DIRECTORY . '/';
                $fileName =  $this->CSVUtilityService->generateCSV($filePath, $fileName, $individualResponseReport, $csvHeaders);
                return ['file_name' => $fileName ];
            }
        } else {
            $individualResponseReport = $this->reportsRepository->getIndividualResponseReportData($surveyId, $studentListFilterQuery, $calculateLimit, false, false, $sortBy);
            if (empty($individualResponseReport)) {
                $response['status_message'] = [
                    'code' => ReportsConstants::REPORT_NO_DATA_CODE,
                    'description' => ReportsConstants::REPORT_NO_DATA_MESSAGE
                ];
            } else {

                $participantStudentCount = $this->getStudentCountBasedCriteria($customSearchDto, $loggedUserId);
                $participantStudentCount = (int)$participantStudentCount['student_count'];

                $searchDtoToCountIncludingNonParticipantStudents = clone($customSearchDto);
                $searchAttributes = $searchDtoToCountIncludingNonParticipantStudents->getSearchAttributes();
                unset($searchAttributes['participating']);
                $searchDtoToCountIncludingNonParticipantStudents->setSearchAttributes($searchAttributes);

                //get total students including the non participant students
                $totalStudentCountIncludingNonParticipants = $this->getStudentCountBasedCriteria($searchDtoToCountIncludingNonParticipantStudents, $loggedUserId);
                if (empty($totalStudentCountIncludingNonParticipants)) {
                    $response['status_message'] = [
                        'code' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_CODE,
                        'description' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_MESSAGE
                    ];
                } else {
                    $totalStudentCountIncludingNonParticipants = $totalStudentCountIncludingNonParticipants['student_count'];
                    $nonParticipantStudentCount = $totalStudentCountIncludingNonParticipants - $participantStudentCount;

                    //Get all respondents for respondent counts, including non-participants
                    $responded = $this->reportsRepository->getIndividualResponseReportData($surveyId, $studentListFilterQuery, '', true);
                    $respondentsCount = (int)$responded[0]['count'];
                }
            }
        }

        $individualResponseReportList = [];
        $individualResponseStatus = [
            'CompletedAll',
            'CompletedMandatory'
        ];

        foreach ($individualResponseReport as $studentIndividualResponseStatus) {

            $surveyReportDto = new SurveyStatusReportDto();
            $surveyReportDto->setStudentId($studentIndividualResponseStatus['person_id']);
            $surveyReportDto->setFirstName($studentIndividualResponseStatus['first_name']);
            $surveyReportDto->setLastName($studentIndividualResponseStatus['last_name']);
            $surveyReportDto->setEmail($studentIndividualResponseStatus['email']);
            $surveyReportDto->setPhoneNumber($studentIndividualResponseStatus['phone_number']);
            if(is_null($studentIndividualResponseStatus['opted_out'])){
                $optedOut = 'No';
            }else{
                $optedOut = $studentIndividualResponseStatus['opted_out'];
            }
            $surveyReportDto->setOptedOut($optedOut);
            $surveyReportDto->setResponded($studentIndividualResponseStatus['survey_responded_status']);

            if (in_array($studentIndividualResponseStatus['survey_status'], $individualResponseStatus)) {
                if (!empty($studentIndividualResponseStatus['responded_at'])) {

                    //survey completion date and time should be displayed based on organization timezone  in the format of mm/dd/yyyy hh:mm(AM|PM) (TimeZone)
                    $respondedDate = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, new \DateTime($studentIndividualResponseStatus['responded_at']), SynapseConstant::DEFAULT_CSV_COLUMN_DATETIME_FORMAT);
                    $surveyReportDto->setRespondedAt($respondedDate);
                }
            } else {

                $surveyReportDto->setRespondedAt(null);
            }
            $individualResponseReportList[] = $surveyReportDto;
        }

        $totalPageCount = ceil($participantStudentCount / $offset);
        $response['non_participant_count'] = $nonParticipantStudentCount;
        $response['total_records'] = $totalStudentCountIncludingNonParticipants;
        $response['total_pages'] = $totalPageCount;
        $response['records_per_page'] = $offset;
        $response['current_page'] = $pageNumber;

        $percentage = 0;
        if ($totalStudentCountIncludingNonParticipants > 0) {
            $percentage = round(($respondentsCount / $totalStudentCountIncludingNonParticipants) * 100, 2);
        }

        $response['percent_responded'] = $respondentsCount . ' / ' . $totalStudentCountIncludingNonParticipants . ' (' . $percentage . '%)';

        $reportSections = array();
        $reportSections['reportId'] = $reportSection['reportId'];
        $reportSections['report_name'] = $reportSection['report_name'];
        $reportSections['short_code'] = $reportSection['short_code'];
        $response['report_sections'] = $reportSections;
        $response['search_attributes'] = $customSearchDto->getSearchAttributes();
        $response['report_data'] = $individualResponseReportList;
        $response['request_json'] = $customSearchDto;
        $response['person_id'] = $loggedUserId;
        $response['report_by'] = $personDetails;
        $response['report_date'] = $reportDate;
        return $response;
    }

    /**
     * return Empty Value for null value
     */
    protected function checkNullResponse($input)
    {
        return $input ? $input : '';
    }

    public function generateStudentReport()
    {
        $this->orgStudentReportsRepo = $this->repositoryResolver->getRepository('SynapseReportsBundle:OrgCalcFlagsStudentReports');
        $currentTimeDiff = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " -20 minutes"));
        $studentList = $this->orgStudentReportsRepo->getStudentList($currentTimeDiff);
        $this->updateDB();
        if (! empty($studentList)) {
            foreach ($studentList as $student) {
                $studentId = $student['student'];
                $this->container->get('pdf_service')->generatePDF($studentId);
            }
        }
    }

    public function getMyReports($orgId, $person, $limit, $offset)
    {
        $this->reportsRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_RUNNING_STATUS_REPO);
        $offset = (int)$offset;
        if(!$offset){
            $offset = ReportsConstants::PAGE_NO;
        }
        $limit = (int)$limit;
        if(!$limit){
            $limit = ReportsConstants::OFFSET;
        }
        $startPoint = Helper::getStartPointRecord($offset, $limit);
        $reports = $this->reportsRunningStatusRepository->getReportsForStudent($orgId, $person, $limit, $startPoint);
        if(!empty($reports))
        {
            foreach($reports as $report)
            {
                $reportStatusDto = new ReportRunningStatusDto();
                $reportStatusDto->setId($report['id']);
                $reportStatusDto->setCreatedAt($report['createdDate']);
                $reportStatusDto->setReportId($report['report_id']);
                $reportStatusDto->setOrgId($report['orgId']);
                $reportStatusDto->setPersonId($report['person']);
                $reportStatusDto->setIsViewed($report['is_viewed']);
                $reportStatusDto->setReportCustomTitle($report['title']);
                $reportStatusDto->setStatus($report['reportStatus']);
                $reportStatusDto->setShortCode($report['short_code']);
                $reportsArray[] = $reportStatusDto;
            }
        }
        return $reportsArray;
    }

    /**
     * Get JSON Response for Faculty/Staff Usage Report
     *
     * @param int $personId
     * @param int $reportInstanceId
     * @param string $questionTypeCode
     * @param string $filter
     * @param string $outputFormat
     * @param string $print
     * @param string $timezone
     * @param bool $testCase
     * @param string $sortBy
     * @param string $pageNumber
     * @param string $offset
     * @return ReportRunningStatusDto
     * @throws AccessDeniedException
     */
    public function getResponseJson($personId, $reportInstanceId, $questionTypeCode, $filter, $outputFormat, $print, $timezone, $testCase = false, $sortBy = '', $pageNumber = '', $offset = '')
    {
        $this->tCase = $testCase;

        $reportsRunningStatusObject = $this->reportsRunningStatusRepository->find($reportInstanceId);
        $this->isObjectExist($reportsRunningStatusObject, "Invalid Report Instance", "Invalid_Report_Instance");

        $this->validateReportRunningStatusBelongsToPerson($personId, $reportsRunningStatusObject);

        $reportStatusDto = new ReportRunningStatusDto();
        $reportStatusDto->setId($reportInstanceId);
        $reportStatusDto->setPersonId($personId);
        $reportStatusDto->setStatus($reportsRunningStatusObject->getStatus());
        $responseJson = json_decode($reportsRunningStatusObject->getResponseJson(), true);

        if (isset($print) && $print == "pdf") {
            $timezone = urldecode($timezone);
            $operate = $timezone[0];
            $offset = $timezone;
            list($hours, $minutes) = explode(':', $offset);
            $hours = trim($hours);
            $minutes = trim($minutes);
            $hours = str_replace("-", "", $hours);
            $minutes = str_replace("-", "", $minutes);
            $hours = str_replace("+", "", $hours);
            $minutes = str_replace("+", "", $minutes);
            $convertText = "PT" . $hours . "H" . $minutes . "M";
            $reportDate = new \DateTime($responseJson['report_info']['report_date']);
            if ($operate == "+" || trim($operate) == "") {
                $reportDate->add(new \DateInterval($convertText));

            } elseif ($operate == "-") {
                $reportDate->sub(new \DateInterval($convertText));
            }

            $responseJson['report_info']['report_date'] = $reportDate->format('m-d-Y');
            $responseJson['report_info']['report_time'] = $reportDate->format('g:i a');
        }

        if ($responseJson['report_sections']['short_code'] == "FUR") {

            if ($responseJson['request_json']['person_id'] != $personId) {
                throw new AccessDeniedException("Access Denied");
            }

            // Getting proper sort column name and sorting order from $sortBy to sort report data.
            switch (trim($sortBy)) {
                case 'lastname':
                case '+lastname':
                    $sortByColumnName = 'lastname';
                    $sortByColumnOrder = SORT_ASC;
                    break;
                case '-lastname':
                    $sortByColumnName = 'lastname';
                    $sortByColumnOrder = SORT_DESC;
                    break;
                case 'student_connected':
                case '+student_connected':
                    $sortByColumnName = 'student_connected';
                    $sortByColumnOrder = SORT_ASC;
                    break;
                case '-student_connected':
                    $sortByColumnName = 'student_connected';
                    $sortByColumnOrder = SORT_DESC;
                    break;
                case 'reports_viewed_student_count':
                case '+reports_viewed_student_count':
                    $sortByColumnName = 'reports_viewed_student_count';
                    $sortByColumnOrder = SORT_ASC;
                    break;
                case '-reports_viewed_student_count':
                    $sortByColumnName = 'reports_viewed_student_count';
                    $sortByColumnOrder = SORT_DESC;
                    break;
                case 'contacts_student_count':
                case '+contacts_student_count':
                    $sortByColumnName = 'contacts_student_count';
                    $sortByColumnOrder = SORT_ASC;
                    break;
                case '-contacts_student_count':
                    $sortByColumnName = 'contacts_student_count';
                    $sortByColumnOrder = SORT_DESC;
                    break;
                case 'interaction_contact_student_count':
                case '+interaction_contact_student_count':
                    $sortByColumnName = 'interaction_contact_student_count';
                    $sortByColumnOrder = SORT_ASC;
                    break;
                case '-interaction_contact_student_count':
                    $sortByColumnName = 'interaction_contact_student_count';
                    $sortByColumnOrder = SORT_DESC;
                    break;
                case 'notes_count':
                case '+notes_count':
                    $sortByColumnName = 'notes_count';
                    $sortByColumnOrder = SORT_ASC;
                    break;
                case '-notes_count':
                    $sortByColumnName = 'notes_count';
                    $sortByColumnOrder = SORT_DESC;
                    break;
                case 'referrals_count':
                case '+referrals_count':
                    $sortByColumnName = 'referrals_count';
                    $sortByColumnOrder = SORT_ASC;
                    break;
                case '-referrals_count':
                    $sortByColumnName = 'referrals_count';
                    $sortByColumnOrder = SORT_DESC;
                    break;
                case 'last_login':
                case '+last_login':
                    $sortByColumnName = 'last_login';
                    $sortByColumnOrder = SORT_ASC;
                    break;
                case '-last_login':
                    $sortByColumnName = 'last_login';
                    $sortByColumnOrder = SORT_DESC;
                    break;
                case 'days_login':
                case '+days_login':
                    $sortByColumnName = 'days_login';
                    $sortByColumnOrder = SORT_ASC;
                    break;
                case '-days_login':
                    $sortByColumnName = 'days_login';
                    $sortByColumnOrder = SORT_DESC;
                    break;
                default:
                    $sortByColumnName = 'lastname';
                    $sortByColumnOrder = SORT_ASC;
                    break;
            }

            // Get no of sorted data by using offset value
            if (isset($responseJson['report_data'])) {
                // Sort report data array with default sort_by value to obtain sorted order data
                usort($responseJson['report_data'], function($firstArray, $secondArray) use ($sortByColumnName) {
                    if(is_numeric($firstArray[$sortByColumnName]) && !is_numeric($secondArray[$sortByColumnName]))
                        return 1;
                    else if(!is_numeric($firstArray[$sortByColumnName]) && is_numeric($secondArray[$sortByColumnName]))
                        return -1;
                    else
                        return ($firstArray[$sortByColumnName] < $secondArray[$sortByColumnName]) ? -1 : 1;
                });

            }

            $reportData = isset($responseJson['report_data']) ? $responseJson['report_data'] : [];
            $sortByColumn = [];
            foreach ($reportData as $reportDataIndex => $reportRecord) {
                $sortByColumn['firstname'][$reportDataIndex]  = $reportRecord['firstname'];
                $sortByColumn['lastname'][$reportDataIndex]  = $reportRecord['lastname'];
                $sortByColumn['person_id'][$reportDataIndex]  = $reportRecord['person_id'];
                $sortByColumn[$sortByColumnName][$reportDataIndex]  = $reportRecord[$sortByColumnName];
            }

            // Sorting report data to ensure deterministic sort by faculty lastname, firstname and id DESC
            if ($sortByColumnName == 'lastname') {
                // adding this condition to avoid sorting for lastname twice when $sortByColumnName = 'lastname'
                // SORT_NATURAL|SORT_FLAG_CASE is used to make strings like lastname and firstname case insensitive
                array_multisort($sortByColumn[$sortByColumnName], $sortByColumnOrder, SORT_NATURAL|SORT_FLAG_CASE, $sortByColumn['firstname'], SORT_ASC, SORT_NATURAL|SORT_FLAG_CASE, $sortByColumn['person_id'], SORT_DESC, $responseJson['report_data']);
            } else {
                array_multisort($sortByColumn[$sortByColumnName], $sortByColumnOrder, $sortByColumn['lastname'], SORT_ASC, SORT_NATURAL|SORT_FLAG_CASE, $sortByColumn['firstname'], SORT_ASC, SORT_NATURAL|SORT_FLAG_CASE, $sortByColumn['person_id'], SORT_DESC, $responseJson['report_data']);
            }
            $pageNumber = $pageNumber ? (int)$pageNumber : SynapseConstant::DEFAULT_PAGE_NUMBER;
            $offset = $offset ? (int)$offset : SynapseConstant::DEFAULT_RECORD_COUNT;
            $responseJson['total_records'] = count($responseJson['report_data']);
            $responseJson['records_per_page'] = $offset;
            $responseJson['current_page'] = $pageNumber;
            $responseJson['report_data'] = array_chunk($responseJson['report_data'], $offset, true);
            $responseJson['total_pages'] = count($responseJson['report_data']);
            $responseJson['report_data'] = $responseJson['report_data'][$pageNumber-1];
            return $responseJson;
        }

        $jsonResponse = [];
        if ($questionTypeCode != '') {
            $questionTypeCode = explode(',', $questionTypeCode);
            foreach ($responseJson['sections'] as $json) {
                if (in_array($json['question_type_code'], $questionTypeCode)) {
                    if (!empty($filter)) {
                        if (preg_match('/' . $filter . '/', $json['question_text'])) {
                            $jsonResponse[] = $json;
                        }
                    } else {
                        $jsonResponse[] = $json;
                    }
                }
            }
            $reportStatusDto->setResponseJson($jsonResponse);
        } else
            if (!empty($filter)) {
                foreach ($responseJson['sections'] as $json) {
                    if (preg_match('/' . $filter . '/', $json['question_text'])) {
                        $jsonResponse[] = $json;
                    }
                }
                $reportStatusDto->setResponseJson($jsonResponse);
            } else {
                $reportStatusDto->setResponseJson($responseJson);
            }

        return $reportStatusDto;
    }

    public function generateCSV ($records, $fileName, $options = null) {

        $opts = array(
            'http'=>array(
                'header'=>"Content-type: application/csv; charset=ISO-8859-2"
            ),
        );
        $context = stream_context_create($opts);
         $basePath  = "data://report_downloads/";

         $origFilename = $fileName;
        // Unit test case run csv of GPA reprt generates in tmp folder
        if($this->tCase){
            $fileName = "/tmp/".$fileName;
        }else{
            $fileName = $basePath.$fileName;
        }
        $file = new SplFileObject($fileName, 'w', false, $context);

        if(is_null($options)){
            $options =  array();
        }

        $columns = array();
        foreach( $records as $record) {
            foreach( $record as $column => $value) {
                if(!in_array($column,$options['ignored'])){
                    if(array_key_exists($column,$options['columnNamesMap'])){
                        $column =  $options['columnNamesMap'][$column]['display_name'];
                    }
                    $columns[] = $column;
                }
            }
            break;
        }

        //$coloumns = implode(",",$columns);
        $file->fputcsv($columns);

        foreach( $records  as $record) {
           $newArr =  array();
           foreach($record as $key => $value)
           if(!in_array($key,$options['ignored'])){

               if(array_key_exists($key,$options['columnNamesMap'])){
                   if(isset($options['columnNamesMap'][$key]['type'])){
                      $value =  $this->$options['columnNamesMap'][$key]['type']($value);
                   }
               }
                   $newArr[] = $value;
           }
           $file->fputcsv($newArr);
        }

        return array('file_name' => $origFilename);
    }

    private function convertDate($value)
    {
        if(empty($value)){
            return $value;
        }
        $value = new \DateTime($value);
        return $value->format('m-d-y');
    }

    /**
     * Generate Our StudentsReport
     *
     * @param SaveSearchDto $customSearchDto
     * @param integer $loggedInUserId
     * @param array $rawReportData
     * @return mixed
     */
    public function getOurStudentsReport($customSearchDto, $loggedInUserId, $rawReportData)
    {
        $reportSection = $customSearchDto->getReportSections();

        // Getting the filter details for the factors questions and surveyId and cohort
        // Checking if optional filters are empty
        $searchFilters = $customSearchDto->getSearchAttributes();

        $surveyDetailsArray = array_key_exists('survey_filter', $searchFilters) ? $searchFilters['survey_filter'] : '';

        //REQUIRED PARAMETERS
        $surveyId = array_key_exists('survey_id', $surveyDetailsArray) ? $surveyDetailsArray['survey_id'] : '';
        $cohortId = array_key_exists('cohort', $surveyDetailsArray) ? $surveyDetailsArray['cohort'] : '';

        $reportId = $reportSection['reportId'];
        $reportObject = $this->reportsRepository->find($reportId);
        if (empty($reportObject)) {
            throw new SynapseValidationException('Report Not Found');
        }

        // getting the report running status Id.
        $reportId = $customSearchDto->getId();

        // finding out the list of filtered students  from the report running status table
        $reportRunningStatusObject = $this->reportsRunningStatusRepository->find($reportId);
        if (empty($reportRunningStatusObject)) {
            throw new SynapseValidationException('Report Running Status Not Found');
        }
        $studentIdsString = $reportRunningStatusObject->getFilteredStudentIds();

        $personObject = $this->personRepository->find($loggedInUserId);
        if (empty($personObject)) {
            throw new SynapseValidationException('Person Not Found');
        }
        $organizationId = $personObject->getOrganization()->getId();

        $organizationDetails = $this->organizationLangService->getOrganization($organizationId);

        $organizationObject = $this->organizationRepository->find($organizationId);
        if (empty($organizationObject)) {
            throw new SynapseValidationException('Organization Not Found');
        }
        $organizationImage = '';
        if ($organizationObject) {
            $organizationImage = $organizationObject->getLogoFileName();
        }
        $campusInfo = array(
            'campus_id' => $organizationId,
            'campus_name' => $organizationDetails['name'],
            'campus_logo' => $organizationImage,
        );

        if (trim($studentIdsString) != "") {
            $studentIds = explode(",", $studentIdsString);
        } else {
            $studentIds = array();
        }

        // end of finding out the list of filtered students  from the report running status table
        $totalStudents = null;

        if (empty($studentIds)) {
            $reportData['status_message'] = [
                'code' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_CODE,
                'description' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_MESSAGE
            ];
        } else {
            $organizationAcademicYearId = $searchFilters['survey_filter']['org_academic_year_id'];

            //Calling Top 5 Query
            $isTempTableGenerated = $this->issueDAO->generateStudentIssuesTemporaryTable($organizationId, $loggedInUserId, $organizationAcademicYearId, $surveyId, $cohortId);

            if ($isTempTableGenerated) {
                $issuesReport = $this->issueDAO->getTopIssuesFromStudentIssues(ReportsConstants::OUR_STUDENT_REPORT_NUMBER_OF_ISSUES, $studentIds);
            } else {
                $this->logger->error("Our Student Reports Top Issues Temporary Table failed");
                $issuesReport = [];
            }

            //Finding Sum of All Students After Filter
            $totalStudents = count($studentIds); // total student count after the filters have been appied

            if (!empty($totalStudents)) {
                $ourStudentsReportArray = array();
                $ourStudentsReportArray['total_students_count'] = $totalStudents;
            }

            // Collating Top 5 Issues and adding it to a Report Section
            $issueArray = [];
            $sectionsToReturn = [];

            $top5IssueSectionId = 1;
            $top5IssueSectionElementId = 11;

            if ($issuesReport) {

                foreach ($issuesReport as $individualIssue) {
                    $ourStudentsTop5issueArray = array();
                    $ourStudentsTop5issueArray['element_id'] = $top5IssueSectionElementId;
                    $ourStudentsTop5issueArray['name'] = $individualIssue['issue_name'];
                    $ourStudentsTop5issueArray['total_students'] = $individualIssue['denominator'];
                    $percentage = $individualIssue['percent'];
                    $ourStudentsTop5issueArray['percentage'] = $percentage;
                    $ourStudentsTop5issueArray['image'] = $individualIssue['icon'];
                    $issueArray[] = $ourStudentsTop5issueArray;
                    $top5IssueSectionElementId++;
                }
            }
            array_push($sectionsToReturn, array("section_id" => $top5IssueSectionId, "title" => "Top 5 Issues", "elements" => $issueArray));

            // Start Collating Demographics and Adding it to a Report Section
            $termArray = $this->orgAcademicTermRepository->findBy(
                array(
                    'orgAcademicYearId' => $organizationAcademicYearId,
                    'organization' => $organizationId,
                )
            );

            if (empty($termArray)) {
                throw new SynapseValidationException('Academic Term Not Found');
            }

            $termIdArray = array();
            foreach ($termArray as $term) {

                $termIdArray[] = $term->getId();
            }

            $termIdText = implode(",", $termIdArray);
            if ($termIdText == "") {
                $termIdText = -1;
            }

            $academicArray['yearId'] = $organizationAcademicYearId;
            $academicArray['termIds'] = $termIdText;

            $reportDemoSQL = $this->getOurStudentsReportSQL($surveyId, $loggedInUserId, $studentIdsString, $academicArray);

            $demographicData = $this->orgSearchRepository->getOrgSearch($reportDemoSQL);

            $demographicItems = [];
            usort(
                $demographicData,
                function ($leftSide, $rightSide) {
                    return $leftSide['count_students'] > $rightSide['count_students'];
                }
            );
            foreach ($demographicData as $individualDemographicData) {
                if (!array_key_exists($individualDemographicData['meta_name'], $demographicItems)) {
                    $demographicItems [$individualDemographicData['meta_name']] = [];
                }
                $percentage = round(($individualDemographicData['count_students'] / $totalStudents) * 100, 1);
                array_push($demographicItems[$individualDemographicData['meta_name']],
                    [
                        'value' => $individualDemographicData['list_name'],
                        'count' => $individualDemographicData['count_students'],
                        'percentage' => $percentage,
                    ]);
            }

            $demographicArray = [];
            $demographicItemArray = $this->demographicItemArr;
            foreach ($demographicItems as $key => $demographics) {
                $sortedDemographics = $this->dataProcessingUtilityService->sortMultiDimensionalArray($demographics, 'percentage', 'desc');
                $demographicItemValueList = [];
                $ourStudentsDemographicItemsArray = array();
                $ourStudentsDemographicItemsArray['title'] = $key;
                $ourStudentsDemographicItemsArray['element_id'] = $demographicItemArray[$key];
                foreach ($sortedDemographics as $metaData) {
                    $ourStudentsDemographicItemValuesArray = array();
                    $ourStudentsDemographicItemValuesArray['name'] = $metaData['value'];
                    $ourStudentsDemographicItemValuesArray['count'] = $metaData['count'];
                    $ourStudentsDemographicItemValuesArray['percentage'] = $metaData['percentage'];
                    $demographicItemValueList[] = $ourStudentsDemographicItemValuesArray;

                }
                $ourStudentsDemographicItemsArray['elements'] = $demographicItemValueList;
                $demographicArray[] = $ourStudentsDemographicItemsArray;
            }
            array_push($sectionsToReturn, array("section_id" => "2", "title" => "demographics", "elements" => $demographicArray));

            // Get and format the data for the sections based on surveys
            $sectionsFromSurveys = $this->ourStudentsReportService->getSurveyBasedSections($loggedInUserId, $organizationId, $surveyId, $studentIds);

            $sectionsToReturn = array_merge($sectionsToReturn, $sectionsFromSurveys);
            if (empty($sectionsToReturn)) {
                $reportData['status_message'] = [
                    'code' => ReportsConstants::REPORT_NO_DATA_CODE,
                    'description' => ReportsConstants::REPORT_NO_DATA_MESSAGE
                ];
            }
            $reportData['report_data']['sections'] = $sectionsToReturn;
        }

        $reportData['report_sections'] = $reportSection;

        $currentDateObject = new \DateTime('now');
        $currentDateObject->setTimezone(new \DateTimeZone('UTC'));

        $reportData['campus_info'] = $campusInfo;
        $reportData['report_info'] = array(
            'report_id' => $reportObject->getId(),
            'report_name' => $reportObject->getName(),
            'short_code' => $reportObject->getShortCode(),
            'report_instance_id' => $reportId,
            'report_date' => $currentDateObject->format('Y-m-d\TH:i:sO'),
            'report_by' => array(
                'first_name' => $personObject->getFirstname(),
                'last_name' => $personObject->getLastname(),
            ),
            'students_count' => $totalStudents,
        );

        //return json
        $reportData['request_json'] = $rawReportData;

        $url = "/reports/OSR/$reportId";
        $this->alertNotificationsService->createNotification("OSR", $reportData['report_info']['report_name'], $personObject, null, null, null, $url, null, null, null, 1, $reportRunningStatusObject);

        return $reportData;
    }

    public function objToArr($template){
        $encoders = array(
                new JsonEncoder()
        );
        $normalizers = array(
                new GetSetMethodNormalizer()
        );
        $serializer = new Serializer($normalizers, $encoders);
        $template = $serializer->serialize($template, 'json');
        $template = json_decode($template, true);
        return $template;
    }

    public function getCommonReportSQL( $customSearchDto, $loggedUserId){
        $searchAttributes = $customSearchDto->getSearchAttributes();
        $orgService = $this->container->get(SearchConstant::ORGANIZATION_SERVICE);
        $organization = $orgService->find($customSearchDto->getOrganizationId());
        $this->isObjectExist($organization, SearchConstant::ORGN_ERROR, SearchConstant::ORGN_ERKEY);

        $searchService = $this->container->get('search_service');
        $studentList = $searchService->getStudentListBasedCriteria($searchAttributes, $organization->getId(), $loggedUserId);
        return $studentList;
    }

    private function getOurStudentsReportSQL( $surveyId, $loggedUserId ,$studentIds,$academicArr)
    {
        //$studentList = $this->getCommonReportSQL($customSearchDto, $loggedUserId);

        $academicYear = $academicArr['yearId'];
        $academicTermIds =  $academicArr['termIds'];

        if(trim($studentIds) == ""){
            $studentIds = '-1';
        }

        $studentList = " pem.person_id in ( $studentIds )" ;

        // reverting back 8087 ,  "="  changed to IN as it should consider all terms for the current academic year

        $sql1 = '
            (select eml.meta_name, emlv.list_name, emlv.list_value, emlv.ebi_metadata_id, count(distinct pem.person_id) as count_students
            from ebi_metadata_list_values emlv
            join person_ebi_metadata pem on (pem.ebi_metadata_id = emlv.ebi_metadata_id and pem.metadata_value = emlv.list_value)
            join ebi_metadata_lang eml on eml.ebi_metadata_id = pem.ebi_metadata_id
            join org_person_student ops on ops.person_id  = pem.person_id and ops.deleted_at is null
            where eml.meta_name in ("Gender","RaceEthnicity","CampusResident","EnrollType","GuardianEdLevel","MilitaryServedUS" , "AthleteStudent","InternationalStudent")
            and eml.deleted_at is null
            and emlv.deleted_at is null
            and pem.deleted_at is null
            and ops.deleted_at is null
            and case when eml.meta_name = "CampusResident" THEN
                pem.org_academic_year_id = [YEARID] AND org_academic_terms_id  IN ( [TERMSID] ) 
                ELSE 
                    1
            END
            ';

        $sql2 ='';
        if($studentList){
         $sql2 =' and '.$studentList;
        }
        $sql3 = " group by eml.meta_name, emlv.list_value, emlv.list_name, emlv.id )";

        $sql4 = "
            UNION ALL
            (SELECT
            if(ebi_question_options.ebi_question_id in (select ebi_question_id from survey_questions where qnbr = 133 and survey_id = $surveyId), 'Has Dependents', 'High School GPA') as'meta_name',
            if(ebi_question_options.ebi_question_id in (select ebi_question_id from survey_questions where qnbr = 133 and survey_id = $surveyId) and ebi_question_options.option_text != 'No dependents', 'Has dependents', ebi_question_options.option_text) as 'list_name',
            ebi_question_options.id as list_value,
            survey_questions.ebi_question_id as 'ebi_metadata_id',
            count(distinct survey_response.person_id)
            FROM
            survey_questions
            inner join
            ebi_question_options
            on ebi_question_options.ebi_question_id = survey_questions.ebi_question_id
            inner JOIN
            survey_response
            on
            survey_questions.survey_id = survey_response.survey_id
            and
            survey_questions.id = survey_response.survey_questions_id
            WHERE
            (
            /* These are the ids for dependents*/
            ebi_question_options.ebi_question_id in (select ebi_question_id from survey_questions where qnbr = 133 and survey_id = $surveyId) or
            /* These are the ids for High School GPA*/
            ebi_question_options.ebi_question_id in (select ebi_question_id from survey_questions where qnbr = 30 and survey_id = $surveyId))
            and survey_questions.survey_id = $surveyId
            and option_value = decimal_value
            /* Place person id's here */
            and survey_response.person_id in ($studentIds)
            group by meta_name, list_name)";

        $sql5 = " order by meta_name,list_name ";
        $sql = $sql1.$sql2.$sql3.$sql4.$sql5;

        $sql =  str_replace("[YEARID]", $academicYear, $sql);
        $sql =  str_replace("[TERMSID]", $academicTermIds, $sql);

        return $sql;
    }

    function getTotalStudentsForOurStudents( $customSearchDto, $loggedUserId){
        $studentList = $this->getCommonReportSQL($customSearchDto, $loggedUserId);
        $sql1 = 'select count(distinct(p.id)) as total_students from
                org_person_student as ops
                inner join
                person as p ON p.id = ops.person_id
                where ops.deleted_at is null
            ';
        $sql2 ='';
        if($studentList){
            $sql2 =' and '.$studentList;
        }

        $sql = $sql1.$sql2;
        return $sql;
    }


    public function getOurStudentReportList()
    {
        $reportRepo = $this->repositoryResolver->getRepository("SynapseReportsBundle:Reports");
        $reportElementRepo = $this->repositoryResolver->getRepository("SynapseReportsBundle:ReportSectionElements");
        $reportEntity = $reportRepo->findOneBy([
            'shortCode' => 'OSR'
            ]);
        if (! $reportEntity) {
            throw new ValidationException([
                "Our Student Report Not Found"
                ], "Our Student Report Not Found", "not_found");
        }
        return $reportElementRepo->getReportListByReportId($reportEntity);

    }
    private function getLimit($pageNo, $offset)
    {
        $startPoint = ($pageNo * $offset) - $offset;
        return " LIMIT $startPoint , $offset ";
    }

    private function getStudentStatusFilter($searchAttributes)
    {
        $status = 'any';

        if ( array_key_exists('student_status', $searchAttributes) ) {

            if ($searchAttributes['student_status'] == "0") {

                $status = 'inactive';
            } elseif ($searchAttributes['student_status'] == "1") {

                $status = 'active';
            }
        }

        return $status;
    }

    private function getStudentListResponse($studentList, $loggedUserId){

        $studentListDto = new SearchDto();
        $studentListDto->setPersonId($loggedUserId);

        $studentArray = [];
        foreach ($studentList as $student){

            $studentDto = new SearchResultListDto();
            $studentDto->setStudentId($student['student_id']);
            $studentDto->setStudentFirstName($student['first_name']);
            $studentDto->setStudentLastName($student['last_name']);

            $studentArray[] = $studentDto;
        }

        $studentListDto->setSearchResult($studentArray);
        return $studentListDto;
    }

    /**
     * Get campus activity for activity Report
     *
     * @param integer $organizationId
     * @param integer $yearId
     * @param bool $access
     * @param string $type
     * @param integer $pageNumber
     * @param integer $recordsPerPage
     * @param integer $personId
     * @param integer $debug
     * @param integer $sortBy
     * @return CampusActivityDto
     * @throws SynapseValidationException
     */
    public function getCampusActivity($organizationId, $yearId, $access, $type, $pageNumber, $recordsPerPage, $personId, $debug, $sortBy)
    {
        $yearStartDate = '';
        $yearEndDate = '';
        $organization = $this->orgService->find($organizationId);
        $this->isObjectExist($organization, ReportsConstants::ORG_NOT_FOUND, ReportsConstants::ORG_NOT_FOUND_KEY);

        //current date for this organization
        $currentDateTime = $this->getDateByTimezone($organization->getTimeZone());

        $pageNumber = (int)$pageNumber;
        if (!$pageNumber) {
            $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }
        $recordsPerPage = (int)$recordsPerPage;
        if (!$recordsPerPage) {
            $recordsPerPage = SynapseConstant::DEFAULT_RECORD_COUNT;
        }
        $validType = ['email', 'note', 'contact', 'referrals', 'appointment'];
        if ($type != '' && !in_array($type, $validType)) {
            throw new SynapseValidationException('Invalid activities type');
        }

        if ($yearId) {
            $getYearDetails = $this->orgAcademicYearRepository->findOneBy([
                'organization' => $organizationId,
                'yearId' => $yearId
            ]);
            if (!$getYearDetails) {
                throw new SynapseValidationException('Academic Year Not Found');
            } else {
                $yearStartDate = $getYearDetails->getStartDate()->format(ReportsConstants::DATE_FORMAT);
                $yearEndDate = $getYearDetails->getEndDate()->format(ReportsConstants::DATE_FORMAT);
            }
        }

        $accessPersonId = ($access) ? '' : $personId;

        // get the list of Participant Students

        $currentAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
        $currentAcademicYearId = $currentAcademicYear['org_academic_year_id'];

        $participantStudentsListforStaff = $this->orgPermissionsetRepository->getStudentsForStaff($personId, $currentAcademicYearId);

        if (count($participantStudentsListforStaff) > 0) {
            $activityDetails = $this->getActivities($organizationId, $yearStartDate, $yearEndDate, $type, $participantStudentsListforStaff, $accessPersonId, $pageNumber, $recordsPerPage, $personId, $debug, $sortBy);
            $totalActivityCount = $activityDetails['total_count'];
            $activityDetails = $activityDetails['records'];
            $totalPageCount = ceil($totalActivityCount / $recordsPerPage);
        } else {
            return [];
        }

        $activityArray = array();
        foreach ($activityDetails as $activity) {

            $activityStatus = '';
            if ($activity['activity_type'] == 'referrals') {
                $activityStatus = $activity['activity_status'] == 'Closed' ? 'Closed' : 'Open';
            }

            if ($activity['activity_type'] == 'appointment') {
                $activityStatus = $activity['activity_status'] < $currentDateTime ? 'Closed' : 'Open';
                $studentName = $activity['student_firstname'];
            } else {
                $studentName = $activity['student_lastname'] . ', ' . $activity['student_firstname'];
            }

            $activitiesDto = new ActivitiesDto();
            $activitiesDto->setActivityId($activity['activity_id']);
            $activitiesDto->setActivityType(ucfirst($activity['activity_type']));
            $activitiesDto->setActivityStatus($this->checkNullResponse($activityStatus));
            $activitiesDto->setActivityCreatedBy($activity['created_by']);

            $activityDate = new \DateTime($activity['activity_date']);
            $activityDate->setTimezone(new \DateTimeZone('UTC'));

            $activitiesDto->setActivityCreatedOn(new \DateTime($activity['activity_date']));
            $activitiesDto->setStudentId($activity['student_id']);
            $activitiesDto->setStudentName($studentName);
            $activitiesDto->setActivityDetails($this->checkNullResponse($activity['details']));
            $activityArray[] = $activitiesDto;
        }

        $campusActivityDto = new CampusActivityDto();
        $campusActivityDto->setTotalRecords($totalActivityCount);
        $campusActivityDto->setTotalPages($totalPageCount);
        $campusActivityDto->setRecordsPerPage($recordsPerPage);
        $campusActivityDto->setCurrentPage($pageNumber);
        $campusActivityDto->setActivityFilterDate(new \DateTime($currentDateTime));

        $campusActivityDto->setActivities($activityArray);
        return $campusActivityDto;
    }

    public function  getDownloadCampusActivity($orgId, $yearId, $access, $type, $personId) {
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(ReportsConstants::ACADEMIC_YEAR_REPO);
        $this->orgService = $this->repositoryResolver->getRepository(ReportsConstants::ORG_REPO);
        $this->metadataListValues = $this->repositoryResolver->getRepository(ReportsConstants::METADATA_REPO);

        $activityList = array();
        $activityData = array();
        $yearStartDate = '';
        $yearEndDate = '';


        $organization = $this->orgService->find($orgId);
        $this->isObjectExist($organization, ReportsConstants::ORG_NOT_FOUND, ReportsConstants::ORG_NOT_FOUND_KEY);

        $timezone = $organization->getTimeZone();
        $timez = $this->repositoryResolver->getRepository(ReportsConstants::METADATA_REPO)->findByListName($timezone);
        if ($timez) {
            $timez = $timez[0]->getListValue();
        }

        /**
         * current date for this organization
         */
        $currentDate = $this->getDateByTimezone($organization->getTimeZone());

        $validType = array(ReportsConstants::ACTIVITIES_TYPE_EMAIL,ReportsConstants::ACTIVITIES_TYPE_NOTE, ReportsConstants::ACTIVITIES_TYPE_CONTACT, ReportsConstants::ACTIVITIES_TYPE_REF, ReportsConstants::ACTIVITIES_TYPE_APP);

        if($type != '' && !in_array($type, $validType)) {
            throw new ValidationException([
                    ReportsConstants::INVALID_ACTIVITY_TYPE
                ], ReportsConstants::INVALID_ACTIVITY_TYPE, ReportsConstants::INVALID_ACTIVITY_TYPE_CODE);
        }
        if($yearId) {
            $getYearDetails = $this->orgAcademicYearRepository->findOneBy([
                        ReportsConstants::DB_FIELD_ORGANIZATION => $orgId,
                        ReportsConstants::ACADEMIC_YEAR_ID => $yearId
                        ]);
            if (! $getYearDetails) {
                throw new ValidationException([
                    ReportsConstants::ACADEMIC_YEAR_NOT_FOUND
                ], ReportsConstants::ACADEMIC_YEAR_NOT_FOUND, ReportsConstants::ACADEMIC_YEAR_NOT_FOUND_CODE);
            } else {
                $yearStartDate = $getYearDetails->getStartDate()->format(ReportsConstants::DATE_FORMAT);
                $yearEndDate = $getYearDetails->getEndDate()->format(ReportsConstants::DATE_FORMAT);
            }
        }

		$this->activityService = $this->container->get('activity_service');
		$sharingAccess = $this->activityService->getSharingAccess($personId);


		$jobObj = 'Synapse\ReportsBundle\Job\ActivityCSVJob';

        $jobNumber = uniqid();
        $job = new $jobObj();
        $job->args = array(
            'jobNumber' => $jobNumber,
            'personId' => $personId,
            'currentDate' => $currentDate,
			'orgId' => $orgId,
			'type' => $type,
			'yearStartDate' => $yearStartDate,
			'yearEndDate' => $yearEndDate,
			'access' => $access,
            'sharingAccess' => $sharingAccess
        );
        $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);

        return [SynapseConstant::DOWNLOAD_IN_PROGRESS_MESSAGE];
    }

    public function cohortsKeyDownload($orgId, $cohortId) {
        $this->wessLinkRepo = $this->repositoryResolver->getRepository(ReportsConstants::WESS_LINK_REPO);
        $this->surveyQuestionsRepo = $this->repositoryResolver->getRepository(ReportsConstants::SURVEY_QUESTIONS_REPOR);
        $orgService = $this->container->get(SearchConstant::ORGANIZATION_SERVICE);
        $this->metadataListValues = $this->repositoryResolver->getRepository(ReportsConstants::METADATA_REPO);
        $organization = $orgService->find($orgId);
        $this->isObjectExist($organization, ReportsConstants::ORG_NOT_FOUND, ReportsConstants::ORG_NOT_FOUND_KEY);

        $timezone = $organization->getTimeZone();
        $timez = $this->metadataListValues->findByListName($timezone);
        if ($timez) {
            $timez = $timez[0]->getListValue();
        }

        /**
         * current date for this organization
         */
        $currentDateTime = $this->getDateByTimezone($organization->getTimeZone());


        $cohortKeyRep = array();
        $cohortsDetails = $this->wessLinkRepo->findBy(array(
                    ReportsConstants::DB_FIELD_ORGANIZATION => $orgId,
                    ReportsConstants::DB_FIELD_COHORT_CODE => $cohortId
                ), array('survey' => 'ASC'));

        $currentDateTime = date("Ymd_His_T", strtotime($currentDateTime));
        $completeFilePath = "roaster_uploads/{$orgId}-{$currentDateTime}-cohort-key.csv";
        if($cohortsDetails){
            $csvHeader = [
                'Question Number',
                'question_rpt',
                'option_value',
                'option_text',
                'question_type',
                'Factor Id',
                'Factor Name'
            ];

            $csvFilePath = @fopen("data://{$completeFilePath}", 'w+');
            @fputcsv($csvFilePath, $csvHeader);
            $index = 1;
            foreach($cohortsDetails as $cohorts) {
                $surveyId = $cohorts->getSurvey()->getId();
                $this->downloadOrgQuestionKey($surveyId, $csvFilePath, $orgId, $index, $cohortId);
                $this->downloadFactorKey($surveyId, $csvFilePath, $index);
                $survey_id[] = $surveyId;
                $index++;
            }
            $this->downloadEbiQuestionKey($survey_id, $csvFilePath);
            @fclose($csvFilePath);
            $cohortKeyRep = [ReportsConstants::DOWNLOAD_KEY_PATH => $completeFilePath, ReportsConstants::COHORT_ID => $cohortId];
        }

        return $cohortKeyRep;

    }

    /**
     * Function to dump/download survey data for the selected cohort.
     *
     * @param int $organizationId
     * @param int $cohortId
     * @param int $personId
     * @param int $yearId
     * @return string
     */
    public function cohortsSurveyReport($organizationId, $cohortId, $personId, $yearId)
    {
        $organization = $this->orgService->find($organizationId);
        $this->isObjectExist($organization, ReportsConstants::ORG_NOT_FOUND, ReportsConstants::ORG_NOT_FOUND_KEY);

        // Current date for this organization
        $currentDateTime = $this->getDateByTimezone($organization->getTimeZone());
        $currentDateTime = date("Ymd_His_T", strtotime($currentDateTime));

        // Find the academic year id
        $academicYear = $this->orgAcademicYearRepository->findOneBy(array(
            'organization' => $organizationId,
            'yearId' => $yearId
        ));
        $academicYearId = $academicYear->getId();
        $cohortsDetails = $this->wessLinkRepository->getSurveysForCohortAndYear($organizationId, $cohortId, $yearId);
        $cohortPerson = array();
        $surveyList = [];
        $count = 1;
        if ($cohortsDetails) {
            foreach ($cohortsDetails as $cohorts) {
                $surveyList[] = $cohorts['survey_id'];
                $cohortStudents = $this->orgPersonStudentSurveyLinkRepository->getStudentIdsForSurveyAndCohort($cohorts['survey_id'], $cohortId, $organizationId);
                foreach ($cohortStudents as $cohortStudent) {
                    if (!in_array($cohortStudent['person_id'], $cohortPerson)) {
                        $cohortPerson[] = $cohortStudent['person_id'];
                    }
                }
                unset($cohortStudents);
            }
            $tempHeader = array();

            // Include ReceiveSurvey in header column
            $index = 1;
            foreach ($surveyList as $surveyIdHeader) {
                $tempHeader[] = $index . '-ReceiveSurvey';
                $index++;
            }

            foreach ($surveyList as $surveyIdHeader) {
                $surveyQuestions = $this->surveyQuestionsRepository->getQuestionsQnbrForSurvey($surveyIdHeader);

                if ($surveyQuestions) {
                    foreach ($surveyQuestions as $questionHeader) {
                        $tempHeader[] = $count . '-Q' . $questionHeader['survey_ques_no'];
                    }
                }
                $orgSurveyQuestions = $this->surveyQuestionsRepository->getOrgQuestionsForSurvey($surveyIdHeader, $organizationId, $cohortId);
                if (!empty($orgSurveyQuestions)) {
                    foreach ($orgSurveyQuestions as $orgSurveyQuestion) {
                        $tempHeader[] = $count . '-ISQ' . $orgSurveyQuestion['org_question_id'];
                    }
                }
                $surveyFactors = $this->dataBlockQuestionRepository->getFactorForSurvey($surveyIdHeader);
                if (!empty($surveyFactors)) {
                    foreach ($surveyFactors as $surveyFactor) {
                        $tempHeader[] = $count . '-Factor ' . $surveyFactor['factor_id'];
                    }
                }
                $tempHeader[] = $count . '-SurveyResponseDate';
                unset($orgSurveyQuestions);
                unset($surveyQuestions);
                unset($surveyFactors);
                $count++;
            }

            // Get Header Columns for Student Profile Items
            $headerColumns = $this->getUploadFields($organizationId, 'student');
            $headerColumns = array_merge($headerColumns, $tempHeader);

            $jobNumber = uniqid();
            $job = new SurveyStudentResponseJob();
            $job->args = array(
                'jobNumber' => $jobNumber,
                'personId' => $personId,
                'currentDateTime' => $currentDateTime,
                'orgId' => $organizationId,
                'cohortId' => $cohortId,
                'headerColumns' => $headerColumns,
                'cohortPerson' => $cohortPerson,
                'surveyList' => $surveyList,
                'academicYearId' => $academicYearId
            );
            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        } else {
            throw new SynapseValidationException('Cohort Details Not Found');
        }
        return SynapseConstant::DOWNLOAD_IN_PROGRESS_MESSAGE;
    }

    private function getUploadFields($orgId, $entityName) {
        $excluded = [
            'id',
            'createdAt',
            'authUsername',
            'createdBy',
            'modifiedAt',
            'modifiedBy',
            'deletedBy',
            'deletedAt',
            'activationToken',
            'confidentialityStmtAcceptDate',
            'tokenExpiryDate',
            'person',
            'username',
            'password',
            'welcomeEmailSentDate',
            'riskLevel',
            'riskUpdateDate',
            'intentToLeave',
            'intentToLeaveUpdateDate',
            'riskModel',
            'lastContactDate',
            'lastActivity',
            'Dateofbirth',
            'dateofbirth'

        ];

        $excluded[] = ($entityName == ReportsConstants::ENTITY_TYPE_STUDENT) ? 'officePhone' : 'homePhone';
        $excluded[] = ($entityName == ReportsConstants::ENTITY_TYPE_STUDENT) ? 'OfficePhone' : 'HomePhone';

        $personItems = $this->doctrine->getManager()
            ->getClassMetadata('Synapse\CoreBundle\Entity\Person')
            ->getFieldNames();
        $personItems = array_diff($personItems, $excluded);

        $personItems = array_map(function ($value)
        {
            return ucfirst($value);
        }, $personItems);

        $orgPersonEntity = array();
        array_push($orgPersonEntity, 'Studentphoto');
        array_push($orgPersonEntity, 'IsActive');
        array_push($orgPersonEntity, 'SurveyCohort');
        array_push($orgPersonEntity, 'ReceiveSurvey');
        array_push($orgPersonEntity, 'YearID');
        array_push($orgPersonEntity, 'TermID');
        array_push($orgPersonEntity, 'PrimaryConnect');
        array_push($orgPersonEntity, 'RiskGroupID');
        array_push($orgPersonEntity, 'StudentAuthKey');

        $contactItems = $this->doctrine->getManager()
            ->getClassMetadata('Synapse\CoreBundle\Entity\ContactInfo')
            ->getFieldNames();

        $contactItems = array_diff($contactItems, $excluded);

        $contactItems = array_map(function ($value)
        {
            return ucfirst($value);
        }, $contactItems);

        $profileItems = [];
        $orgProfileItems = [];
        $profileItems = $this->profileService->getProfiles('active');

        $profileItems = array_column($profileItems['profile_items'], 'item_label');

        $orgProfileItems = $this->orgProfileService->getInstitutionSpecificProfileBlockItems($orgId, false, false, false);

        $orgProfileItems = array_column($orgProfileItems['profile_items'], 'item_label');

        return array_merge($personItems, $orgPersonEntity, $contactItems, $profileItems, $orgProfileItems);
    }

    protected function getDateByTimezone($timez)
    {
        $timezone = $this->metadataListValuesRepository->findByListName($timez);
        if ($timezone) {
            $timezone = $timezone[0]->getListValue();
        }
        try {
            $currentNow = new \DateTime('now', new \DateTimeZone($timezone));
            $currentNow->setTimezone(new \DateTimeZone('UTC'));
        } catch (Exception $e) {
            $currentNow = new \DateTime('now');
        }
        $currentDate = $currentNow->format(ReportsConstants::YMD);
        return $currentDate;
    }

    public function ourStudentsDisplayOptions()
    {
        $ourStudentsSections = array();
        $ourStudentsTop5 = [
            "section_id" => "1",
            "title" => "top5issue",
            "display_title" => "top5issue",
            "section_element" => "dough-nut-ring",
            "elements" => []
        ];

        for ($issueElementId = 1; $issueElementId <= 5; $issueElementId ++) {
            array_push($ourStudentsTop5["elements"], [
                "element_id" => '1' . $issueElementId,
                "title" => 'Top' . $issueElementId,
                "icon" => ""
            ]);
        }

        $ourStudentsDemo = [
            "section_id" => "2",
            "title" => "demographics",
            "display_title" => "demographics",
            "section_element" => "scaled-value",
            "elements" => []
        ];

        $demoItemArr = $this->demographicItemArr;
        foreach ($demoItemArr as $key => $demoItem) {
            array_push($ourStudentsDemo["elements"], [
                "element_id" => $demoItem,
                "title" => $key,
                "icon" => ""
            ]);
        }

        $reportSections = $this->getOurStudentReportList();
        $sectionNameArr = [];

        foreach ($reportSections as $indDemoData) {
            if (! array_key_exists($indDemoData["sectionName"], $sectionNameArr)) {
                $sectionNameArr[$indDemoData["sectionName"]] = [];
                array_push($sectionNameArr[$indDemoData["sectionName"]], [
                    "section_id" => $indDemoData["sectionId"],
                    "title" => $indDemoData["sectionName"],
                    "display_title" => $indDemoData["sectionName"],
                    "section_element" => "scaled-value",
                    "elements" => [
                        [
                            "element_id" => $indDemoData["id"],
                            "title" => $indDemoData["displayLabel"],
                            "icon" => ""
                        ]
                    ]
                ]);
            } else {
                array_push($sectionNameArr[$indDemoData["sectionName"]][0]["elements"], [
                    "element_id" => $indDemoData["id"],
                    "title" => $indDemoData["displayLabel"],
                    "icon" => ""
                ]);
            }
        }
        $ourStudentsReportSections = [];
        array_push($ourStudentsSections, $ourStudentsTop5);
        array_push($ourStudentsSections, $ourStudentsDemo);

        foreach ($sectionNameArr as $key => $value) {
            array_push($ourStudentsReportSections, $value[0]);
            array_push($ourStudentsSections, $value[0]);
        }

        $sectionsArr = [];
        $sectionsArr['sections'] = $ourStudentsSections;
        return $sectionsArr;
    }

    public function checkAccessPermission($loggedUserId, $reportDetailsObj)
    {
        $permissionSet = $this->container->get("orgpermissionset_service");
        $allowedReport = $permissionSet->getAllowedReports($loggedUserId);

        $reportCode = [];
        if(isset($allowedReport['reports_access'])){
            $reportCode = array_column($allowedReport['reports_access'], 'short_code');
        }
        if(!in_array($reportDetailsObj->getShortCode(), $reportCode))
        {
            throw new ValidationException([
                'You no longer have permission to run this report'
            ], 'You no longer have permission to run this report', 'report_no_permission');
        }
        return true;
    }

	public function replacePlaceHolders($sql, $orgId)
	{
	    $prefetchKeys = $this->container->get('search_service')->prefetchSearchKeys($orgId);

	    foreach( $prefetchKeys as $key => $value) {
			if($key != '[CLASS_LEVELS]')
			{
				$sql = str_replace( $key, $value, $sql);
			}
	    }

	    return $sql;
	}


    /**
     * Gets the list of past and current retention tracking years
     * The parameter $reportId is used to give a report-specific message.
     *
     * @param int $organizationId
     * @param int|null $reportId
     * @return array
     * @throws SynapseValidationException
     */
    public function getRetentionTrackYears($organizationId, $reportId = null)
    {
        $currentDate = new \DateTime('now');

        $currentOrgAcademicYearDetails = $this->orgAcademicYearRepository->getCurrentOrPreviousAcademicYearUsingCurrentDate($currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT), $organizationId);
        if (!empty($currentOrgAcademicYearDetails)) {
            $currentYearId = $currentOrgAcademicYearDetails[0]['year_id'];
        } else {
            throw new SynapseValidationException('Institution has no academic years set.');
        }

        $retentionTrackOrgAcademicYearIds = $this->orgPersonStudentRetentionTrackingGroupRepository->getRetentionTrackingOrgAcademicYearIdsForOrganization($organizationId, $currentYearId);

        // If none of the years in the list have retention track students, return a message rather than the list of years.
        if (empty($retentionTrackOrgAcademicYearIds)) {
            if (isset($reportId)) {
                // This message is intended for the Executive Summary Report, where retention tracks are only required for some sections.
                $nonRetentionTrackYears = $this->orgAcademicYearRepository->getPastAndCurrentAcademicYearNames($organizationId);
                $yearString = $this->dataProcessingUtilityService->formatListWithConjunction($nonRetentionTrackYears, 'or');
                $message = "There are no students in the retention track for $yearString.";

                $sections = $this->reportSectionsRepository->getReportSectionsForSelectedReport($reportId, 'required');

                $sectionNames = array_column($sections, 'section_name');
                if (!empty($sectionNames)) {
                    $sectionString = $this->dataProcessingUtilityService->formatListWithConjunction($sectionNames, 'or') . ',';
                } else {
                    $sectionString = 'any sections';
                }
                $message .= "  The report will not include $sectionString which depend on having students in the retention track.";

            } else {
                // Generic message for reports (such as the Persistence/Retention Report and Completion Report) which completely depend on retention tracks.
                $message = 'Retention tracks have not been selected for any students. The report will not contain any meaningful data.';
            }
            $yearsToReturn = ['message' => $message];
        } else {
            $yearsToReturn = $retentionTrackOrgAcademicYearIds;
        }

        return $yearsToReturn;
    }


    /**
     * Generates the exported CSV from a Faculty Usage Report.
     *
     * Given the response JSON, returns an associative array of the form:
     *      ['file_name' => $filename]
     * where $filename is the name of the generated CSV file.
     *
     * @param array $responseJson
     * @return array
     */
    private function generateFacultyUsageReportCSV($responseJson)
    {
        $organizationId = $responseJson['organization_id'];
        $reportId = $responseJson['id'];
        $fileName = "$organizationId-$reportId-Faculty-Staff_Usage_Report.csv";

        $headers = [
            'faculty_name' => 'Faculty member name',
            'external_id' => 'Faculty external ID',
            'username' => 'Faculty e-mail',
            'student_connected' => 'Students connected',
            'contacts_student_count' => 'Students contacted',
            'interaction_contact_student_count' => 'Students with interaction contact',
            'notes_count' => 'Total notes created',
            'referrals_count' => 'Total referrals created',
            'reports_viewed_student_count' => 'Viewed reports of N students',
            'last_login' => 'Last login',
            'days_login' => 'Days when logged in'
        ];


        foreach ($responseJson['report_data'] as $rowNumber => $row) {
            unset($row['person_id']);
            $row['faculty_name'] = $row["lastname"] . " " . $row["firstname"];
            unset($row['lastname']);
            unset($row['firstname']);
            if ((int)$row['contacts_student_count'] != 0) {
                $row['contacts_student_count'] = $row['contacts_student_count'] . "(" . $row['contacted_student_percentage'] . " % )";
            }
            if ((int)$row['interaction_contact_student_count'] != 0) {
                $row['interaction_contact_student_count'] = $row['interaction_contact_student_count'] . "(" . $row['interaction_contact_student_percentage'] . " % )";
            }

            if (isset($row['last_login']) && trim($row['last_login']) != "") {
                $lastLoginDateTimeObj = new \DateTime($row['last_login']);
                $row['last_login'] = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $lastLoginDateTimeObj, "m/d/Y");
            } else {
                $row['last_login'] = "never";
            }

            $orderedRow = [];
            foreach ($headers as $columnKey => $column) {
                if (!isset($row[$columnKey])) {
                    $row[$columnKey] = '';
                }
                $orderedRow[$columnKey] = $row[$columnKey];
            }
            $responseJson['report_data'][$rowNumber] = $orderedRow;
        }
        $filePath = "data://report_downloads/";
        $this->CSVUtilityService->generateCSV($filePath, $fileName, $responseJson['report_data'], $headers);
        return ['file_name' => $fileName];
    }

    private function generatePRRCsv($responseJson){

	   $reportType = $responseJson['report_sections']['short_code'];

	    if($reportType == "PRR"){
	        $repName = "-Persistence-Retention-report.csv";
	    }elseif($reportType == "CR"){
	        $repName = "-Completion-Report.csv";
	    }

	    $fileName  = $responseJson['organization_id']."-".$responseJson['id'].$repName;

	    $finalArr =  array();
	    if($reportType == "PRR"){
        /*
         * The csv generation logic got PRR  is here as there has been a change in the json structure
         */
	        $dataArr =  [];
	        foreach($responseJson['report_data'] as $data){
	            $yearKey = str_replace(" ","_",$data['column_title']);
	            foreach($data['persistence_retention_by_risk'] as $risk){
	                $dataArr[$risk['risk_color']][$yearKey] = $risk;
	            }
    	    }
    	    foreach($dataArr as $dataKey => $data){
    	        $finalArrList =  [];
    	        $finalArrList['color'] = $dataKey;
    	        foreach($data as $dKey => $dat){
    	            $finalArrList[$dKey."_students_retained"]  = $dat['students_retained'];
    	            $finalArrList[$dKey."_student_count"]  = $dat['student_count'];
    	            $finalArrList[$dKey."_percent"]  = $dat['percent'];
    	        }
    	        $finalArr[] = $finalArrList;
    	    }

            /*
             * The below section is added for finding out the element in the array which has the maximum keys,
             *  chek if those elements are there for other color codes , if not there add them and set them to 0
             *
             * e.g.
             *
             * suppose the array looks like
             *
             * [
             *     [
             *         color => green,
             *         midyear => 1

             *     ],[
             *         color =>red,
             *         midyear =>1
             *         retainyear =>2
             *     ]
             * ]
             *
             * the below code would find out the element with max keys ..i.e key =1 in above case
             *
             * and then construct the final Array as
             *
             * [
             *     [
             *         color => green,
             *         midyear => 1,
             *         retainyear =>0

             *     ],[
             *         color =>red,
             *         midyear =>1
             *         retainyear =>2
             *     ]
             * ]
             *
             *
             */
    	    $finalCnt = 0;
    	    $finalKey = 0;
    	    foreach($finalArr as $key => $arr){
    	        $countArr =  count($arr);
    	        if($countArr > $finalCnt){
    	            $finalCnt = $countArr;
    	            $finalKey= $key;
    	        }
    	    }
    	    /*
    	     * Fix for ESPRJ-8482
    	    */
    	    if(count($finalArr) > 0){
    	        foreach($finalArr[$finalKey] as $key => $val){
    	            for($cnt = 0 ; $cnt <= (count($finalArr) - 1) ;$cnt++){
    	                if( !array_key_exists($key,$finalArr[$cnt])){
    	                    $finalArr[$cnt][$key] = 0;
    	                }
    	            }
    	        }
    	    }
	    }else{
            /*
             * The CR logic remains the same ..
             */

    	    foreach($responseJson['report_data'] as $key => $reportData){
    	        if($key == "total_students" ){
    	            continue;
    	        }
    	        $datArr =  array();
    	        $datArr['color'] = $key;
    	        foreach($reportData as $rpKey => $val){
    	            if($rpKey == "count"){
    	                continue;
    	            }
    	            $datArr[$rpKey."_percent"] = $val['percent'];
    	            $datArr[$rpKey."_number"] = $val['number'];
    	        }
    	        $finalArr[] = $datArr;
    	    }
    	    /*
    	     * Fix for ESPRJ-8482
    	    */
    	    if(count($finalArr) > 0){
    	        foreach($finalArr[count($finalArr) - 1] as $key => $val){
    	            if(trim($val) == ""){
    	                $finalArr[count($finalArr) - 1][$key] = 0;
    	            }

    	            for($cnt = 0 ; $cnt < (count($finalArr) - 1) ;$cnt++){
    	                if( !array_key_exists($key,$finalArr[$cnt])){
    	                    $finalArr[$cnt][$key] = 0;
    	                }
    	            }
    	        }
    	    }
	    }

	    $this->generateCSV($finalArr, $fileName);
	    return  array('file_name' => $fileName);
	}


    /**
     * Generate GPA csv report
     *
     * @param array $responseJson
     * @return array
     */
    private function generateGPACsv($responseJson)
    {
        $reportName = "-GPA-report.csv";

        $fileName = $responseJson['request_json']['organization_id'] . "-" . $responseJson['request_json']['id'] . $reportName;

        $finalGpaReportArray = array();

        foreach ($responseJson['report_items']['gpa_term_summaries_by_year'] as $key => $reportData) {

            foreach ($reportData as $reportDataKey => $reportData) {

                if ($reportDataKey == "year_name") {
                    $year = $reportData;
                }

                if ($reportDataKey == "gpa_summary_by_term") {

                    foreach ($reportData as $report) {
                        $responseDataArray = array();
                        $responseDataArray['year_name'] = (isset($year) && !empty($year)) ? $year : "";
                        $responseDataArray['term_name'] = (isset($report['term_name']) && !empty($report['term_name'])) ? $report['term_name'] : "";
                        $responseDataArray['student_count'] = (isset($report['student_count']) && !empty($report['student_count'])) ? $report['student_count'] : 0;
                        $responseDataArray['mean_gpa'] = (isset($report['mean_gpa']) && !empty($report['mean_gpa'])) ? $report['mean_gpa'] : 0;
                        $responseDataArray['percent_under_2'] = (isset($report['percent_under_2']) && !empty($report['percent_under_2'])) ? $report['percent_under_2'] : 0;
                        if (count($responseDataArray) > 0) {
                            $finalGpaReportArray[] = $responseDataArray;
                        }
                    }
                }
            }
        }
        $optionArray = array(
            'columnNamesMap' => array(
                'year_name' => array(
                    'display_name' => 'Year'
                ),
                'term_name' => array(
                    'display_name' => 'Term Name'
                ),
                'student_count' => array(
                    'display_name' => 'Student Count'
                ),
                'mean_gpa' => array(
                    'display_name' => 'Mean GPA'
                ),
                'percent_under_2' => array(
                    'display_name' => 'Percent Under 2.0 GPA'
                )
            ),
            'ignored' => array()
        );
        $this->generateCSV($finalGpaReportArray, $fileName, $optionArray);
        return array(
            'file_name' => $fileName
        );
    }


    /**
     * Returns a list of report sections for the given report, optionally filtered by $retentionTrackingType,
     * optionally including report_section_elements for the sections which have them.
     *
     * @param int $reportId
     * @param string|null $retentionTrackingType - "required" or "optional" or "none"
     * @param boolean|null $includeElements
     * @return array
     */
    public function getReportSections($reportId, $retentionTrackingType, $includeElements)
    {
        $sectionData = $this->reportSectionsRepository->getReportSectionsForSelectedReport($reportId, $retentionTrackingType);

        // Convert '1'/'0' to true/false.
        foreach ($sectionData as &$section) {
            $section['survey_contingent'] = filter_var($section['survey_contingent'], FILTER_VALIDATE_BOOLEAN);
            $section['academic_term_contingent'] = filter_var($section['academic_term_contingent'], FILTER_VALIDATE_BOOLEAN);
            $section['risk_contingent'] = filter_var($section['risk_contingent'], FILTER_VALIDATE_BOOLEAN);
        }

        if ($includeElements) {
            foreach ($sectionData as &$section) {
                $elements = $this->reportSectionElementsRepository->findBy(['sectionId' => $section['section_id']]);
                if ($elements) {
                    $elementsToReturn = [];
                    foreach ($elements as $element) {
                        $elementToReturn = [];
                        $elementToReturn['element_id'] = $element->getId();
                        $elementToReturn['element_name'] = $element->getTitle();
                        $elementsToReturn[] = $elementToReturn;
                    }
                    $section['elements'] = $elementsToReturn;
                }
            }
        }

        $dataToReturn = [];
        $dataToReturn['report_id'] = $reportId;
        $dataToReturn['sections'] = $sectionData;

        return $dataToReturn;
    }


    /**
     * Returns information about the report_section_elements for the given section.
     * If $hasText is true, only returns elements which have a non-null description.
     *
     * @param $sectionId
     * @param $hasText
     * @return array
     */
    public function getReportSectionElements($sectionId, $hasText)
    {
        $elementData = $this->reportSectionElementsRepository->getElementsForSelectedSection($sectionId, $hasText);
        return $elementData;
    }

    /**
     *
     * @param unknown $reportSectionsInfo
     * @param unknown $sectionIdsSorted
     * @param string $isAsc
     * @return number|unknown
     */
    private function sortSectionElementsByScore($reportSectionsInfo, $sectionIdsSorted, $isAsc = true)
    {
        foreach ($reportSectionsInfo as $section) {
            foreach ($sectionIdsSorted as $sectionId) {
                if ($section->getSectionId() === $sectionId) {
                    $elements = $section->getElements();
                    usort($elements, function ($ele1, $ele2, $isAsc)
                    {
                        if (floatval($ele1->getElementScores()->getElementScore()) == floatval($ele2->getElementScores()->getElementScore())) {
                            return 0;
                        }
                        if (floatval($ele1->getElementScores()->getElementScore()) < floatval($ele2->getElementScores()->getElementScore())) {
                            return ($isAsc) ? - 1 : 1;
                        } else {
                            return ($isAsc) ? 1 : - 1;
                        }
                    });
                }
            }
        }
        return $reportSectionsInfo;
    }

    /**
     * Validates if the passed person_id is of the user who generated the report or not, fetches response and decodes responseJson,
     * then based on report short-code appropriate ReportsService method for generating the report CSV will be called.

     * @param int $personId The ID of the person attempting to retrieve this report
     * @param int $reportInstanceId The ID of the report being retrieved
     * @param boolean $tCase Unit test case run csv  GPA,Completion and Faculty/StaffUsage report generates in tmp folder
     * @return string $filename
     */
    public function generateReportCSV($personId, $reportInstanceId, $tCase = false)
    {
        $reportRunningStatusObject = $this->reportsRunningStatusRepository->find($reportInstanceId);

        if (!$reportRunningStatusObject) {
            throw new SynapseValidationException("Report object not found");
        }
        $this->validateReportRunningStatusBelongsToPerson($personId, $reportRunningStatusObject);
        $responseJson = json_decode($reportRunningStatusObject->getResponseJson(), true);

        $reportShortCode = strtoupper($responseJson['request_json']['report_sections']['short_code']);

        $reportFileName = '';

        //TODO: Remove tCase and it's usages as a part of technical debt ticket ESPRJ-14069
        $this->tCase = $tCase;

        switch($reportShortCode)
        {
            case "SUB-COM":
                $reportFileName = $this->comparisonReportService->generateCompareReportCSV($responseJson);
                break;
            case "PRR":
            case "CR":
                $reportFileNameArray = $this->generatePRRCsv($responseJson);
                $reportFileName = $reportFileNameArray['file_name'];
                break;
            case "FUR":
                $reportFileNameArray = $this->generateFacultyUsageReportCSV($responseJson);
                $reportFileName = $reportFileNameArray['file_name'];
                break;
            case "GPA":
                $reportFileNameArray = $this->generateGPACsv($responseJson);
                $reportFileName = $reportFileNameArray['file_name'];
                break;
            default:
                $reportFileName = '';
                break;
        }
        return $reportFileName;
    }
}
