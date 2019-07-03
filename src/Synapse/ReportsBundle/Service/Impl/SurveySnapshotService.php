<?php
namespace Synapse\ReportsBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\DatablockQuestionsRepository;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\EbiQuestionLangRepository;
use Synapse\CoreBundle\Repository\EbiQuestionOptionsRepository;
use Synapse\CoreBundle\Repository\EbiQuestionRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetDatablockRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetQuestionRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\OrgQuestionOptionsRepository;
use Synapse\CoreBundle\Repository\OrgQuestionRepository;
use Synapse\CoreBundle\Repository\OrgQuestionResponseRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\PersonConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\ReportsBundle\Entity\ReportRunDetails;
use Synapse\ReportsBundle\Entity\ReportsTemplate;
use Synapse\ReportsBundle\EntityDto\ReportInfoDto;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\EntityDto\ReportsTemplatesDto;
use Synapse\ReportsBundle\EntityDto\StudentsRequestDto;
use Synapse\ReportsBundle\EntityDto\SurveySnapshotReportDrilldownDto;
use Synapse\ReportsBundle\EntityDto\SurveySnapshotSectionDto;
use Synapse\ReportsBundle\EntityDto\SurveySnapshotSectionResponseDto;
use Synapse\ReportsBundle\Job\SurveySnapshotReportJob;
use Synapse\ReportsBundle\Repository\ReportRunDetailsRepository;
use Synapse\ReportsBundle\Repository\ReportSectionsRepository;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\ReportsBundle\Repository\ReportsTemplateRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RiskBundle\Repository\RiskLevelsRepository;
use Synapse\SearchBundle\EntityDto\SearchDto;
use Synapse\SearchBundle\EntityDto\SearchResultListDto;
use Synapse\SearchBundle\Repository\OrgSearchRepository;
use Synapse\SearchBundle\Service\Impl\StudentListService;
use Synapse\SurveyBundle\Repository\QuestionBankMapRepository;
use Synapse\SurveyBundle\Repository\SurveyBranchRepository;
use Synapse\SurveyBundle\Repository\SurveyQuestionsRepository;
use Synapse\SurveyBundle\Repository\SurveyResponseRepository;


/**
 * @DI\Service("surveysnapshot_service")
 *
 */
class SurveySnapshotService extends AbstractService
{

    const SERVICE_KEY = 'surveysnapshot_service';

    // Class Constants

    private $jobs;


    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Resque
     */
    private $resque;


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
     * @var OrgPermissionsetService
     */
    private $orgPermissionSetService;

    /**
     * @var ReportDrilldownService
     */
    private $reportDrilldownService;

    /**
     * @var StudentListService
     */
    private $studentListService;

    /**
     * @var SurveyReportsHelperService
     */
    private $surveyReportsHelperService;

    /**
     * @var TokenService
     */
    private $tokenService;


    // Repositories

    /**
     * @var DatablockQuestionsRepository
     */
    private $datablockQuestionsRepository;

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EbiMetadataRepository
     */
    private $ebiMetadataRepository;

    /**
     * @var EbiQuestionLangRepository
     */
    private $ebiQuestionLangRepository;

    /**
     * @var EbiQuestionOptionsRepository
     */
    private $ebiQuestionOptionsRepository;

    /**
     * @var EbiQuestionRepository
     */
    private $ebiQuestionRepository;

    /**
     * @var FactorReportService
     */
    private $factorReportService;

    /**
     * @var MetadataListValuesRepository
     */
    private $metadataListValuesRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationlangRepository
     */
    private $organizationLangRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrgCourseStudentRepository
     */
    private $orgCourseStudentRepository;

    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     * @var OrgGroupStudentsRepository
     */
    private $orgGroupStudentsRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgPermissionsetDatablockRepository
     */
    private $orgPermissionsetDatablockRepository;

    /**
     * @var OrgPermissionsetQuestionRepository
     */
    private $orgPermissionsetQuestionRepository;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * @var OrgQuestionOptionsRepository
     */
    private $orgQuestionOptionsRepository;

    /**
     * @var OrgQuestionRepository
     */
    private $orgQuestionRepository;

    /**
     * @var OrgQuestionResponseRepository
     */
    private $orgQuestionResponseRepository;

    /**
     * @var OrgSearchRepository
     */
    private $orgSearchRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var QuestionBankMapRepository
     */
    private $questionBankMapRepository;

    /**
     * @var ReportsRepository
     */
    private $reportsRepository;

    /**
     * @var ReportSectionsRepository
     */
    private $reportSectionsRepository;

    /**
     * @var ReportRunDetailsRepository
     */
    private $reportRunDetailsRepository;

    /**
     * @var ReportsRunningStatusRepository
     */
    private $reportsRunningStatusRepository;

    /**
     * @var ReportsTemplateRepository
     */
    private $reportsTemplateRepository;

    /**
     * @var RiskLevelsRepository
     */
    private $riskLevelsRepository;

    /**
     * @var SurveyBranchRepository
     */
    private $surveyBranchRepository;

    /**
     * @var SurveyQuestionsRepository
     */
    private $surveyQuestionRepository;

    /**
     * @var SurveyResponseRepository
     */
    private $surveyResponseRepository;

    /**
     * SurveySnapshotService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     * })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {

        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        // Scaffolding
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->serializer = $this->container->get(SynapseConstant::JMS_SERIALIZER_CLASS_KEY);

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->csvUtilityService = $this->container->get(CSVUtilityService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->factorReportService = $this->container->get(FactorReportService::SERVICE_KEY);
        $this->orgPermissionSetService = $this->container->get(OrgPermissionsetService::SERVICE_KEY);
        $this->reportDrilldownService = $this->container->get(ReportDrilldownService::SERVICE_KEY);
        $this->studentListService = $this->container->get(StudentListService::SERVICE_KEY);
        $this->surveyReportsHelperService = $this->container->get(SurveyReportsHelperService::SERVICE_KEY);
        $this->tokenService = $this->container->get(TokenService::SERVICE_KEY);

        // Repositories
        $this->datablockQuestionsRepository = $this->repositoryResolver->getRepository(DatablockQuestionsRepository::REPOSITORY_KEY);
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(EbiMetadataRepository::REPOSITORY_KEY);
        $this->ebiQuestionLangRepository = $this->repositoryResolver->getRepository(EbiQuestionLangRepository::REPOSITORY_KEY);
        $this->ebiQuestionOptionsRepository = $this->repositoryResolver->getRepository(EbiQuestionOptionsRepository::REPOSITORY_KEY);
        $this->ebiQuestionRepository = $this->repositoryResolver->getRepository(EbiQuestionRepository::REPOSITORY_KEY);
        $this->metadataListValuesRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationLangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->orgCourseStudentRepository = $this->repositoryResolver->getRepository(OrgCourseStudentRepository::REPOSITORY_KEY);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
        $this->orgGroupStudentsRepository = $this->repositoryResolver->getRepository(OrgGroupStudentsRepository::REPOSITORY_KEY);
        $this->orgPermissionsetDatablockRepository = $this->repositoryResolver->getRepository(OrgPermissionsetDatablockRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPermissionsetQuestionRepository = $this->repositoryResolver->getRepository(OrgPermissionsetQuestionRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->orgQuestionOptionsRepository = $this->repositoryResolver->getRepository(OrgQuestionOptionsRepository::REPOSITORY_KEY);
        $this->orgQuestionRepository = $this->repositoryResolver->getRepository(OrgQuestionRepository::REPOSITORY_KEY);
        $this->orgQuestionResponseRepository = $this->repositoryResolver->getRepository(OrgQuestionResponseRepository::REPOSITORY_KEY);
        $this->orgSearchRepository = $this->repositoryResolver->getRepository(OrgSearchRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->questionBankMapRepository = $this->repositoryResolver->getRepository(QuestionBankMapRepository::REPOSITORY_KEY);
        $this->reportsRepository = $this->repositoryResolver->getRepository(ReportsRepository::REPOSITORY_KEY);
        $this->reportRunDetailsRepository = $this->repositoryResolver->getRepository(ReportRunDetailsRepository::REPOSITORY_KEY);
        $this->reportSectionsRepository = $this->repositoryResolver->getRepository(ReportSectionsRepository::REPOSITORY_KEY);
        $this->reportsRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsRunningStatusRepository::REPOSITORY_KEY);
        $this->reportsTemplateRepository = $this->repositoryResolver->getRepository(ReportsTemplateRepository::REPOSITORY_KEY);
        $this->riskLevelsRepository = $this->repositoryResolver->getRepository(RiskLevelsRepository::REPOSITORY_KEY);
        $this->surveyBranchRepository = $this->repositoryResolver->getRepository(SurveyBranchRepository::REPOSITORY_KEY);
        $this->surveyQuestionRepository = $this->repositoryResolver->getRepository(SurveyQuestionsRepository::REPOSITORY_KEY);
        $this->surveyResponseRepository = $this->repositoryResolver->getRepository(SurveyResponseRepository::REPOSITORY_KEY);
    }

    /**
     * Generate Report PDF.
     *
     * @param int $personId
     * @param int $reportInstanceId
     * @param float $zoom
     */
    public function generateMyReportPDF($personId, $reportInstanceId, $zoom = 1.042)
    {
        $syUrl = '';
        $syUrl = $this->ebiConfigRepository->findOneByKey('System_URL');
        if ($syUrl) {
            $syUrl = $syUrl->getValue();
        }
        $token = $this->tokenService->generateToken($personId)->getToken();
        $person = $this->personRepository->find($personId);
        $organizationId = $person->getOrganization()->getId();
        $pageURL = '#/reports/activity-survey-report?person_id=' . $personId . '&report_instance_id=' . $reportInstanceId . '&access_token=' . $token;
        $currentAcademicYear = '';
        $curDate = new \DateTime('now');
        $academicYearDetails = $this->orgAcademicYearRepository->getCurrentAcademicDetails($curDate->setTime(0, 0, 0), $organizationId);
        if ($academicYearDetails) {
            $currentAcademicYear = $academicYearDetails[0]['yearId'];
        }
        $pdfURI = $syUrl . $pageURL;
        $fileName = $organizationId . '-' . $personId . '-' . $currentAcademicYear . '-activity-report-' . time();
        $fileName = md5(Helper::encrypt($fileName)) . '.pdf';
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=" . $fileName);
        $pdfGenerator = $contents = $this->container->get('knp_snappy.pdf');
        $pdfGenerator = $this->container->get('knp_snappy.pdf');

        $pdfGenerator->setOption('orientation', 'Landscape');
        $pdfGenerator->setOption('page-size', 'Letter');
        $pdfGenerator->setOption('javascript-delay', '2000');
        $pdfGenerator->setOption('zoom', $zoom);
        $pdfGenerator->setOption('disable-smart-shrinking', true);
        $pdfGenerator->setOption('margin-bottom', '0');
        $pdfGenerator->setOption('margin-left', '0');
        $pdfGenerator->setOption('margin-right', '0');
        $pdfGenerator->setOption('margin-top', '0');
        $pdfGenerator->setOption('load-error-handling', 'ignore');
        print $pdfGenerator->getOutput($pdfURI);
    }

    public function initiateSnapshotJob($reportRunningDto, $loggedUserId, $surveyId, $reportInstanceId)
    {
        $jobNumber = uniqid();
        $job = new SurveySnapshotReportJob();
        $job->args = array(
            'userId' => $loggedUserId,
            'surveyId' => $surveyId,
            'reportInstanceId' => $reportInstanceId,
            'reportRunningDto' => serialize($reportRunningDto)
        );
        $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
    }


    /**
     * Deprecated function to get Drilldown for SurveySnapshot.  Remove when time permits after ensuring it is not used
     * ToDo: Remove when time permits after ensuring it is not used
     * @deprecated
     */
    public function getJsonResponseDrilldown($personId, $reportInstanceId, $questionTypeCode, $questionNumber, $optionValues, $pageNo, $offset, $filter, $viewmode, $type, $factorId, $sortBy = '', $data = '', $source = '', $question, $optionId, StudentsRequestDto $studentReqDto = null, $print, $timezone)
    {
        if ($type == 'factor') {
            $response = $this->factorReportService->factorReportResponse($reportInstanceId, $personId, $factorId, $optionValues, $filter, $pageNo, $offset, $viewmode, $sortBy, $data);
        } else
            if (!empty($questionTypeCode) && empty(!$questionNumber)) {
                $response = $this->getDrillDownResponse($personId, $reportInstanceId, $questionTypeCode, $questionNumber, $optionValues, $pageNo, $offset, $viewmode, $sortBy, $data, $source, $question, $optionId, $studentReqDto);
            } else {

                $response = $this->getResponseJson($personId, $reportInstanceId, $questionTypeCode, $filter, $viewmode, $print, $timezone);
            }
        return $response;
    }

    /**
     * Get drill down by specific question from Survey Snapshot report
     * ToDo: Remove when time permits after ensuring it is not used
     *
     * @param int $personId
     * @param int $reportInstanceId
     * @param string $questionTypeCode
     * @param int $questionNumber
     * @param $optionValues
     * @param int $pageNumber
     * @param int $numberOfRecords
     * @param string $viewMode
     * @param string $sortByString
     * @param string $dataType - determines whether or not the full drilldown list should be returned (CSV)  or partial (drill down in application)
     * @param string $source
     * @param int $question - EBI or ISQ
     * @param int $optionId
     * @param $studentReqDto
     * @return array|SearchDto
     * @deprecated
     */
    public function getDrillDownResponse($personId, $reportInstanceId, $questionTypeCode, $questionNumber, $optionValues, $pageNumber, $numberOfRecords, $viewMode, $sortByString = '', $dataType = '', $source = '', $question, $optionId, $studentReqDto)
    {
        $questionText = "";
        if ($question == 'isq') {
            $orgQuestionObject = $this->orgQuestionRepository->find($questionNumber);
            $questionText = $orgQuestionObject->getQuestionText();
            $databaseTableAlias = 'orgResp';
        } else {
            $surveyQuestionObject = $this->surveyQuestionRepository->find($questionNumber);
            if ($surveyQuestionObject) {
                $ebiQuestionId = $surveyQuestionObject->getEbiQuestion()->getId();
                $ebiQuestionIdArray = [$ebiQuestionId];
                $ebiQuestionObject = $this->ebiQuestionLangRepository->findOneBy(['ebiQuestion' => $ebiQuestionId]);

                $questionText1 = $ebiQuestionObject->getQuestionText();
                $questionText2 = $ebiQuestionObject->getQuestionRpt();
                if (!empty($questionText1)) {
                    $questionText = "$questionText1 $questionText2";
                } else {
                    $questionText = $questionText2;
                }
            }
            $databaseTableAlias = 'sr';
        }
        $reportInstance = $this->surveyReportsHelperService->getReportInstance($reportInstanceId);
        $organizationId = $reportInstance->getOrganization()->getId();
        $jsonResponse = json_decode($reportInstance->getResponseJson(), true);
        $searchAttributes = $jsonResponse['request_json']['search_attributes'];
        $surveyId = $jsonResponse['request_json']['search_attributes']['survey_filter']['survey_id'];
        $studentId = explode(',', $reportInstance->getFilteredStudentIds());
        $searchDto = array();
        if (!empty($optionValues)) {
            $optionValues = explode(',', $optionValues);
            $this->surveyReportsHelperService->validateOptionPair($optionValues);
        }

        $dbFields = $this->surveyReportsHelperService->getDoctrineField($questionTypeCode, $databaseTableAlias);

        // get Filtered Students based on the ebi question permission
        $studentIds = [];

        if (is_null($studentReqDto) || !isset($studentReqDto)) {

            $selectedStudents = $reportInstance->getFilteredStudentIds();

            // Get Allowed Students based on User Permission
            $currentOrgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
            $students = $this->orgPermissionsetRepository->getStudentsForStaff($personId, $currentOrgAcademicYearId);
            $studentsArray = array_intersect(explode(',', $selectedStudents), $students);
            $studentsArray[] = null;
            $selectedStudents = implode(',', $studentsArray);
        } else {
            $selectedStudents = $studentReqDto->getStudentIds();
        }


        // Using common function in repo and that's expecting array
        if ($question == 'isq') {
            $filteredStudents = $this->fetchStudentsPermittedOrgQuestions($selectedStudents, $personId, $organizationId);
            $student_id = isset($filteredStudents[$questionNumber]) ? $filteredStudents[$questionNumber] : '';
        } else {
            $filteredStudents = $this->surveyResponseRepository->getStudentsBasedQuestionPermission($personId, $organizationId, $ebiQuestionIdArray, $selectedStudents);
            $student_id = array_column($filteredStudents, 'student_id');
        }
        if (!empty($filteredStudents)) {
            foreach ($studentId as $student) {
                if (in_array($student, $student_id)) {
                    $studentIds[] = $student;
                }
            }
        }
        if ($question == 'isq') {
            $respondedStudents = $this->orgQuestionRepository->getDrilldownRespondedStudents($studentIds, $questionNumber, $optionValues, $dbFields, $optionId);
            $field = 'orgResp';
        } else {
            $respondedStudents = $this->surveyResponseRepository->getDrilldownRespondedStudents($studentIds, $questionNumber, $optionValues, $dbFields);
            $field = 'sr';
        }
        $students = array_column($respondedStudents, 'person_id');
        $orderBy = $this->surveyReportsHelperService->orderBy(trim($sortByString));
        $pageNumber = $this->surveyReportsHelperService->getPageNo($pageNumber);
        $numberOfRecords = $this->surveyReportsHelperService->getOffset($numberOfRecords);
        $startPoint = ($pageNumber * $numberOfRecords) - $numberOfRecords;

        $ebiMetadataIdObj = $this->ebiMetadataRepository->findOneBy(['key' => 'ClassLevel']);
        $ebiMetadataId = $ebiMetadataIdObj->getId();

        if (!empty($students)) {
            $dbRepository = ($question == 'isq') ? $this->orgQuestionRepository : $this->surveyResponseRepository;
            $field = $this->surveyReportsHelperService->getDBField($questionTypeCode, $field);
            $studentIds = implode(',', $students);

            if (strtolower($dataType) == 'student_list') {
                $studentList = $dbRepository->listDrilldownStudents($studentIds, $startPoint, $numberOfRecords, $orderBy, $questionNumber, $field, $organizationId, $surveyId, $ebiMetadataId, 'student-list');
                $studentListArray = $this->surveyReportsHelperService->getStudentListResponse($studentList, $personId);
                return $studentListArray;

            } else {

                if ($questionTypeCode == 'Q' || $questionTypeCode == 'D' || $questionTypeCode == 'ISQ-Q' || $questionTypeCode == 'ISQ-D') {
                    $studentList = $dbRepository->listDrilldownStudentsByCategory($studentIds, $startPoint, $numberOfRecords, $orderBy, $questionNumber, $field, $organizationId, $surveyId, $ebiMetadataId, $viewMode);

                } else if ($questionTypeCode == 'ISQ-MR') {
                    $studentList = $this->orgQuestionRepository->listOrgMRStudentList($studentIds, $startPoint, $numberOfRecords, $orderBy, $questionNumber, $optionId, $organizationId, $surveyId, $ebiMetadataId, $viewMode);

                } else {
                    $studentList = $dbRepository->listDrilldownStudents($studentIds, $startPoint, $numberOfRecords, $orderBy, $questionNumber, $field, $organizationId, $surveyId, $ebiMetadataId, $viewMode);

                }
            }

            $countQuery = "SELECT FOUND_ROWS() cnt";
            $countQueryResult = $this->orgSearchRepository->getOrgSearch($countQuery);
            $drillDownSearchResult = $this->surveyReportsHelperService->formatDrilldownData($organizationId, $personId, $students, $studentList);

            $totalCount = $countQueryResult[0]['cnt'];
            $totalPageCount = ceil($totalCount / $numberOfRecords);
            $searchDto = new SearchDto();
            $searchDto->setPersonId($personId);
            $searchDto->setTotalRecords($totalCount);
            $searchDto->setTotalPages($totalPageCount);
            $searchDto->setRecordsPerPage($numberOfRecords);
            $searchDto->setCurrentPage($pageNumber);
            $searchDto->setQuestion($questionText);
            $searchDto->setSearchAttributes($searchAttributes);
            $searchDto->setSearchResult($drillDownSearchResult);
            if (!empty($drillDownSearchResult) && $viewMode == 'csv') {
                $this->exportCSV($drillDownSearchResult, $personId, $organizationId, $questionTypeCode, $questionNumber, $source);
                $fileName['file_name'] = $organizationId . "-" . $personId . "-" . $questionTypeCode . '-' . $questionNumber . "-snapshot-report.csv";
                return $fileName;
            }
        }
        return $searchDto;
    }


    /**
     * Deprecated function for Getting CSVs for the Survey Snapshot Report
     * ToDo: Remove when time permits after ensuring it is not used
     * @deprecated
     */
    private function exportCSV($finalRespArr, $personId, $orgId, $questionTypeCode, $questionNumber, $source)
    {
        if (($questionTypeCode == 'SA' || $questionTypeCode == 'LA' || $questionTypeCode == 'ISQ-SA' || $questionTypeCode == 'ISQ-LA') && $source != 'drilldown') {
            $csvHeader = [
                'RESPONSE'
            ];
        } else {
            $csvHeader = [
                'FIRST NAME',
                'LAST NAME',
                'RISK INDICATOR',
                'CLASS LEVEL',
                'RESPONSE',
                'EXTERNAL ID',
                'PRIMARY EMAIL'
            ];
        }
        $fh = @fopen("data://report_downloads/{$orgId}-{$personId}-{$questionTypeCode}-{$questionNumber}-snapshot-report.csv", 'w');
        fputcsv($fh, $csvHeader);
        if (!empty($finalRespArr)) {

            if (($questionTypeCode == 'SA' || $questionTypeCode == 'LA' || $questionTypeCode == 'ISQ-SA' || $questionTypeCode == 'ISQ-LA') && $source != 'drilldown') {
                foreach ($finalRespArr as $array) {
                    $rows[] = [
                        'response' => $array->getResponse()
                    ];
                }
            } else {
                foreach ($finalRespArr as $array) {
                    /*
                     * Include student external id and primary email
                     */
                    $primaryEmail = "";
                    $personDetails = $this->personRepository->find($array->getStudentId());
                    if (!$personDetails) {
                        throw new ValidationException([
                            PersonConstant::ERROR_PERSON_NOT_FOUND
                        ], PersonConstant::ERROR_PERSON_NOT_FOUND, PersonConstant::ERROR_PERSON_NOT_FOUND_KEY);
                    } else {
                        $personDetail = $this->personRepository->getPersonDetails($personDetails);
                        if (count($personDetail) > 0) {
                            $contacts = $personDetail[0]['contacts'][0];
                            if (!empty($contacts)) {
                                $primaryEmail = $contacts['primaryEmail'];
                            }
                        }
                    }

                    $rows[] = [
                        'first_name' => @iconv("UTF-8", "ISO-8859-2", $array->getStudentFirstName()),
                        'last_name' => @iconv("UTF-8", "ISO-8859-2", $array->getStudentLastName()),
                        'risk_indicator' => $array->getStudentRiskStatus(),
                        'class_level' => $array->getStudentClasslevel(),
                        'response' => $array->getResponse(),
                        'external_ID' => $personDetails->getExternalId() ? $personDetails->getExternalId() : null,
                        'primary_email' => $primaryEmail
                    ];
                }
            }
        }
        if (isset($rows) && count($rows) > 0) {
            foreach ($rows as $fields) {
                fputcsv($fh, $fields);
            }
        }
        fclose($fh);
    }


    /**
     * Deprecated Function for getting the JSON for the Survey Snapshot Report
     * ToDo: Remove when time permits after ensuring it is not used
     * @deprecated
     */
    public function getResponseJson($personId, $reportInstanceId, $category, $filter, $viewMode, $print, $timezone)
    {
        $reportInstance = $this->reportsRunningStatusRepository->find($reportInstanceId);

        $this->surveyReportsHelperService->isObjectExist($reportInstance, "Invalid Report Instance", "Invalid_Report_Instance");
        $this->surveyReportsHelperService->isInvalidPerson($personId, $reportInstance);
        $reportStatusDto = new ReportRunningStatusDto();
        $reportStatusDto->setId($reportInstanceId);
        $reportStatusDto->setPersonId($personId);
        $reportStatusDto->setStatus($reportInstance->getStatus());


        $responseJson = json_decode($reportInstance->getResponseJson(), true);
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
                throw new ValidationException([
                    "Access Denied"
                ], "Access Denied", "Access Denied");
            }

            if ($viewMode == "csv") {
                $res = $this->surveyReportsHelperService->generateFURCsv($responseJson);
                return $res;
            }
            return $responseJson;
        }


        if ($responseJson['report_sections']['short_code'] == "PRR" || $responseJson['report_sections']['short_code'] == "CR") {

            if ($viewMode == "csv") {
                $res = $this->surveyReportsHelperService->generatePRRCsv($responseJson);
                return $res;
            }

        }


        $jsonResponse = array();
        if ($category != '') {
            $category = explode(',', $category);
            foreach ($responseJson['sections'] as $json) {
                if (in_array($json['question_type_code'], $category)) {
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
        return ($reportStatusDto);
    }


    /**
     * Creates Report Template.
     *
     * @param ReportsTemplatesDto $reportTemplatesDto
     * @return ReportsTemplatesDto
     */
    public function createReportTemplate(ReportsTemplatesDto $reportTemplatesDto)
    {
        $this->logger->info("Create Report Template");

        $personId = $reportTemplatesDto->getPersonId();
        $person = $this->personRepository->find($personId);
        $reportInfo = $reportTemplatesDto->getReportInfo();
        $reportId = $reportInfo['report_id'];
        $reports = $this->reportsRepository->find($reportId);
        $this->surveyReportsHelperService->isObjectExist($reports, 'Reports Not Found', 'reports_not_found');
        $this->surveyReportsHelperService->isObjectExist($person, 'Person Not Found', 'person_not_found');
        $organizationId = $reportTemplatesDto->getOrganizationId();
        $organization = $this->organizationRepository->find($organizationId);
        $this->surveyReportsHelperService->isObjectExist($organization, 'Organization Not Found', 'organization_not_found');
        $reportTemplate = new ReportsTemplate();
        $reportTemplate->setPerson($person);
        $reportTemplate->setOrganization($organization);
        $reportTemplate->setReports($reports);
        $reportTemplate->setTemplateName($reportTemplatesDto->getTemplateName());
        $responseJSON = $this->serializer->serialize($reportTemplatesDto, 'json');
        $reportTemplate->setFilterCriteria($responseJSON);
        $reportTemplateObj = $this->reportsTemplateRepository->create($reportTemplate);
        $this->reportsTemplateRepository->flush();
        $reportTemplatesDto->setId($reportTemplateObj->getId());
        $this->logger->info("Report Template is created -" . $reportTemplateObj->getId());
        return $reportTemplatesDto;
    }

    /**
     * Lists the Report Templates.
     *
     * @param int $organizationId
     * @param int $personId
     * @return array
     */
    public function getMyReportsTemplate($organizationId, $personId)
    {
        $person = $this->personRepository->find($personId);
        $this->surveyReportsHelperService->isObjectExist($person, 'Person Not Found', 'person_not_found');
        $reportsTemplates = $this->reportsTemplateRepository->getReportTemplates($person, $organizationId);
        $reportTemplatesArray = array();
        if (!empty($reportsTemplates)) {
            foreach ($reportsTemplates as $reportsTemplate) {
                $reports = array();
                $reportsTemplateDto = new ReportsTemplatesDto();
                $reportsTemplateDto->setId($reportsTemplate['id']);
                $reportsTemplateDto->setOrganizationId($reportsTemplate['organization']);
                $reportsTemplateDto->setPersonId($reportsTemplate['person']);
                $reportsTemplateDto->setTemplateName($reportsTemplate['templateName']);
                $reportsTemplateDto->setTemplateDate($reportsTemplate['template_date']);
                $reportsTemplateDto->setRequestJson(json_decode($reportsTemplate['filterCriteria'], true));
                $reports['report_id'] = $reportsTemplate['reports'];
                $reports['report_name'] = $reportsTemplate['report_name'];
                $reports['short_code'] = $reportsTemplate['short_code'];
                $reportsTemplateDto->setReportInfo($reports);
                $reportTemplatesArray[] = $reportsTemplateDto;
            }
        }
        return $reportTemplatesArray;
    }

    /**
     * Edits Reports Template.
     *
     * @param ReportsTemplatesDto $reportTemplatesDto
     * @return ReportsTemplatesDto
     */
    public function editReportTemplate(ReportsTemplatesDto $reportTemplatesDto)
    {
        $this->logger->info("Edit Report Template");
        // Valid Person check
        $personId = $reportTemplatesDto->getPersonId();
        $person = $this->personRepository->find($personId);
        $this->surveyReportsHelperService->isObjectExist($person, 'Person Not Found', 'person_not_found');

        // Valid report Check
        $reportInfo = $reportTemplatesDto->getReportInfo();
        $reportId = $reportInfo['report_id'];
        $reports = $this->reportsRepository->find($reportId);
        $this->surveyReportsHelperService->isObjectExist($reports, 'Reports Not Found', 'reports_not_found');
        // Valid Organization Check
        $organizationId = $reportTemplatesDto->getOrganizationId();
        $organization = $this->organizationRepository->find($organizationId);
        $this->surveyReportsHelperService->isObjectExist($organization, 'Organization Not Found', 'organization_not_found');
        // Get report template by Id
        $reportTemplate = $this->reportsTemplateRepository->findOneById($reportTemplatesDto->getId());
        $reportTemplate->setPerson($person);
        $reportTemplate->setOrganization($organization);
        $reportTemplate->setReports($reports);
        $reportTemplate->setTemplateName($reportTemplatesDto->getTemplateName());
        $responseJSON = $this->serializer->serialize($reportTemplatesDto, 'json');
        $reportTemplate->setFilterCriteria($responseJSON);
        $this->reportsTemplateRepository->flush();

        return $reportTemplatesDto;
    }

    public function deleteReportTemplate($orgId, $templateId, $loggedInUser)
    {
        $this->logger->info("Delete Report Template");

        /**
         * Valid Person check
         */
        $person = $this->personRepository->find($loggedInUser);
        $this->surveyReportsHelperService->isObjectExist($person, 'Person Not Found', 'person_not_found');
        /**
         * Valid Organization Check
         */
        $organization = $this->organizationRepository->find($orgId);
        $this->surveyReportsHelperService->isObjectExist($organization, 'Organization Not Found', 'organization_not_found');
        /**
         * Get the report Template based on person and organization
         */
        $reportTemplate = $this->reportsTemplateRepository->findOneBy(array(
            'id' => $templateId,
            'organization' => $organization,
            'person' => $person
        ));
        $this->surveyReportsHelperService->isObjectExist($reportTemplate, 'Report Template Not Found', 'report_template_not_found');
        $this->reportsTemplateRepository->delete($reportTemplate);

        $this->reportsTemplateRepository->flush();
        $this->logger->info("Delete Report Template");
        return $templateId;
    }


    /**
     * Generates the Main Survey Snapshot Report
     *
     * @param int $reportRunningStatusId
     * @param int $surveyId
     * @param int $personId
     * @param ReportRunningStatusDto $reportRunningDto
     */
    public function generateReport($reportRunningStatusId, $surveyId, $personId, $reportRunningDto)
    {
        $reportRunningStatus = $this->reportsRunningStatusRepository->find($reportRunningStatusId);
        $reportStudentIds = $reportRunningStatus->getFilteredStudentIds();
        $organizationId = $reportRunningStatus->getOrganization()->getId();
        $personObject = $reportRunningStatus->getPerson();
        $reportObject = $reportRunningStatus->getReports();
        $personName = [];
        $reportFilter = [];

        // ISQ - Org Questions End
        $organizationLanguage = $this->organizationLangRepository->findOneBy([
            'organization' => $organizationId
        ]);
        $organization = $organizationLanguage->getOrganization();
        $searchAttributes = $reportRunningDto->getSearchAttributes();

        //  Filtered Questions from optional filters
        $surveyQuestions = $this->getSelectedSurveyQuestionsForReport($searchAttributes);
        $questionsWithDataBlockId = $this->surveyQuestionRepository->getDataBlockQuestionsBasedPermission($organizationId, $personId, $surveyId, $surveyQuestions, true);
        $descriptiveQuestions = $scaledQuestions = $numericQuestions = [];

        if (count($questionsWithDataBlockId) > 0) {
            if (!empty($reportStudentIds)) {
                $ebiQuestionPermissions = $this->getStudentsAssociatedWithDataBlocks($reportStudentIds, $personId, $organizationId, $questionsWithDataBlockId);
                if (count($ebiQuestionPermissions) > 0) {
                    $scaledQuestions = $this->getScaledCategoryQuestions($surveyId, $reportRunningStatusId, $ebiQuestionPermissions, $personId);
                    $descriptiveQuestions = $this->getDescriptiveQuestions($surveyId, $reportRunningStatusId, $ebiQuestionPermissions, $personId);
                    $numericQuestions = $this->getNumericQuestions($surveyId, $reportRunningStatusId, $ebiQuestionPermissions, $personId);
                }
            }
        }
        $scaledOrganizationQuestions = $descriptiveOrgQuestions = $numericOrgQuestions = [];
        $organizationMultipleResponse = [];

        if (!empty($reportStudentIds)) {
            $orgQuestionPermissions = $this->fetchStudentsPermittedOrgQuestions($reportStudentIds, $personId, $organizationId);
            if (count($orgQuestionPermissions) > 0) {
                $organizationMultipleResponse = $this->getMultiResponseQuestions($surveyId, $reportRunningStatusId, $orgQuestionPermissions, $personId);
                $scaledOrganizationQuestions = $this->getScaledCategoryQuestions($surveyId, $reportRunningStatusId, $orgQuestionPermissions, $personId, 'orgQuestions');
                $descriptiveOrgQuestions = $this->getDescriptiveQuestions($surveyId, $reportRunningStatusId, $orgQuestionPermissions, $personId, 'orgQuestions');
                $numericOrgQuestions = $this->getNumericQuestions($surveyId, $reportRunningStatusId, $orgQuestionPermissions, $personId, 'orgQuestions');
            }

            if (empty($organizationLanguage)) {
                $error = ['error' => 'Organization Language not found'];
                $responseJSON = $this->serializer->serialize($error, 'json');
                $reportRunningStatus->setStatus('F');
                $reportRunningStatus->setResponseJson($responseJSON);
                $this->reportsRunningStatusRepository->flush();
                $this->createReportNotification($reportRunningStatus);
                return false;
            } else {

                $surveyQuestions = $descriptiveQuestions + $scaledQuestions + $numericQuestions + $organizationMultipleResponse + $scaledOrganizationQuestions + $descriptiveOrgQuestions + $numericOrgQuestions;

                if (empty($surveyQuestions)) {
                    $reportData['status_message'] = [
                        'code' => ReportsConstants::REPORT_NO_DATA_CODE,
                        'description' => ReportsConstants::REPORT_NO_DATA_MESSAGE
                    ];
                } else {
                    ksort($surveyQuestions);
                    $surveyQuestions = array_values($surveyQuestions);

                    $reportInfoDto = new ReportInfoDto();
                    $reportInfoDto->setReportId($reportObject->getId());
                    $reportInfoDto->setReportInstanceId($reportRunningStatusId);
                    $reportInfoDto->setReportName($reportObject->getName());
                    $reportInfoDto->setReportDescription($reportObject->getDescription());
                    $reportInfoDto->setReportDate($reportRunningStatus->getCreatedAt());
                    $students = explode(',', $reportStudentIds);
                    $totalStudents = count($students);
                    $reportInfoDto->setTotalStudents($totalStudents);
                    $reportInfoDto->setShortCode($reportObject->getShortCode());
                    $reportInfoDto->setReportDisable($reportObject->getIsActive());
                    $personName['first_name'] = $personObject->getFirstname();
                    $personName['last_name'] = $personObject->getLastname();
                    $reportInfoDto->setReportBy($personName);
                    $reportFilter['numeric'] = true;
                    $reportFilter['categorical'] = true;
                    $reportFilter['scaled'] = true;
                    $reportFilter['long_answer'] = true;
                    $reportFilter['short_answer'] = true;
                    $reportInfoDto->setReportFilter($reportFilter);

                    $organizationInformationArray = array(
                        'campus_id' => $organizationId,
                        'campus_name' => $organizationLanguage->getOrganizationName(),
                        'campus_logo' => $organization->getLogoFileName(),
                        'campus_color' => $organization->getPrimaryColor()
                    );
                    $reportData['report_info'] = $reportInfoDto;
                    $reportData['campus_info'] = $organizationInformationArray;
                    $reportData['sections'] = $surveyQuestions;
                }
            }
        } else {
            $reportData['status_message'] = [
                'code' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_CODE,
                'description' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_MESSAGE
            ];
        }
        $reportData['request_json'] = $reportRunningDto;
        $reportData['report_instance_id'] = $reportRunningStatusId;
        $responseJSON = $this->serializer->serialize($reportData, 'json');
        $reportRunningStatus->setStatus('C');
        $reportRunningStatus->setResponseJson($responseJSON);
        $this->reportsRunningStatusRepository->flush();
        $this->createReportNotification($reportRunningStatus);
    }

    /**
     * get scaled category questions for survey Id and report running status Id
     *
     * @param int $surveyId
     * @param int $reportRunningStatusId
     * @param array $ebiQuestionPermissions
     * @param int $personId
     * @param string $type
     * @return array
     */
    private function getScaledCategoryQuestions($surveyId, $reportRunningStatusId, $ebiQuestionPermissions, $personId, $type = 'ebiQuestion')
    {

        $reportRunningStatus = $this->reportsRunningStatusRepository->find($reportRunningStatusId);
        $reportStudentIds = $reportRunningStatus->getFilteredStudentIds();
        $reportStudentIds = explode(',', $reportStudentIds);
        $ebiQuestionPermissionIds = array_keys($ebiQuestionPermissions);
        $questionsResponse = [];
        $questionOptions = [];
        if (!empty($reportStudentIds)) {
            $organizationId = $reportRunningStatus->getOrganization()->getId();
            if ($type == 'ebiQuestion') {
                if (!empty($ebiQuestionPermissionIds)) {
                    $surveyQuestions = $this->surveyResponseRepository->getScaledCategorySurveyQuestions($surveyId, $organizationId, $reportStudentIds, $ebiQuestionPermissionIds);
                }
                $questionId = 'ebi_question_id';
                $code = '';
                $questionNumber = 'qnbr';
            } else {
                if (!empty($ebiQuestionPermissionIds)) {
                    $surveyQuestions = $this->orgQuestionResponseRepository->getScaledCategoryISQCalculatedResponses($surveyId, $reportStudentIds, $organizationId, $ebiQuestionPermissionIds);
                }
                $questionId = 'org_question_id';
                $code = 'ISQ-';
                $questionNumber = 'org_question_id';
            }
            if (!empty($surveyQuestions)) {
                foreach ($surveyQuestions as $surveyQuestion) {
                    $ebiQuestionId = $surveyQuestion[$questionId];
                    $surveyQuestionNumber = $surveyQuestion[$questionNumber];
                    $surveyQuestionId = ($type == 'ebiQuestion') ? $surveyQuestion['survey_questions_id'] : $surveyQuestionNumber;
                    $surveySnapshotSectionDto = new SurveySnapshotSectionDto();
                    $questionType = $surveyQuestion['question_type'];
                    if ($questionType == 'D') {
                        $questionTypeModified = 'category';
                    } else if ($questionType == 'Q') {
                        $questionTypeModified = 'scaled';
                    } else {
                        $questionTypeModified = 'Multiple Response';
                    }
                    $surveySnapshotSectionDto->setQuestionTypeCode($code . $questionType);
                    $surveySnapshotSectionDto->setQuestionType($questionTypeModified);
                    $surveySnapshotSectionDto->setQuestionText($surveyQuestion['question_text']);
                    $surveySnapshotSectionDto->setSurveyQuestionId($surveyQuestionId);
                    $surveySnapshotSectionDto->setType($type);
                    $standardDeviation = number_format($surveyQuestion['standard_deviation'], 2);
                    $surveySnapshotSectionDto->setQuestionQnbr($surveyQuestionNumber);

                    $studentIdsForReport = $ebiQuestionPermissions[$ebiQuestionId];
                    $totalStudents = count($studentIdsForReport);
                    $surveySnapshotSectionDto->setTotalStudents($totalStudents);

                    if ($type == 'ebiQuestion') {
                        $surveyResponses = $this->surveyResponseRepository->getScaledTypeQuestions($surveyId, $organizationId, $studentIdsForReport, $surveyQuestionId);
                    } else {
                        $surveyResponses = $this->orgQuestionResponseRepository->getScaledTypeISQResponses($surveyId, $organizationId, $studentIdsForReport, $ebiQuestionId);

                    }
                    $branchDetails = $this->surveyBranchRepository->getQuestionBranchDetails($surveyId, $surveyQuestionId);
                    $branching = [];
                    if (!empty($branchDetails)) {
                        foreach ($branchDetails as $branchDetail) {
                            $branches = [];
                            $branches['option_text'] = $branchDetail['option_text'];
                            $branches['source_qtype'] = $branchDetail['type'];
                            $branches['source_qnbr'] = $branchDetail['qnbr'];
                            $branches['source_question_text'] = $branchDetail['question_text'];
                            $branching[] = $branches;
                        }
                    }
                    $response = [];
                    $redValues = array(
                        1,
                        2
                    );
                    $yellowValues = array(
                        3,
                        4,
                        5
                    );
                    $greenValues = array(
                        6,
                        7
                    );
                    $redResponse = 0;
                    $yellowResponse = 0;
                    $greenResponse = 0;
                    if (!empty($surveyResponses)) {
                        $totalResponded = 0;
                        foreach ($surveyResponses as $surveyResponse) {
                            $totalResponded += $surveyResponse['student_count'];
                        }

                        $surveySnapshotSectionDto->setTotalStudentsResponded($totalResponded);
                        $totalStudentPercentage = number_format(($totalResponded * 100) / $totalStudents, 1);
                        $surveySnapshotSectionDto->setRespondedPercentage($totalStudentPercentage);

                        foreach ($surveyResponses as $surveyResponse) {
                            $responses = [];
                            $surveySnapshotSectionResponseDto = new SurveySnapshotSectionResponseDto();
                            $surveySnapshotSectionResponseDto->setResponseText($surveyResponse['option_text']);
                            $optionResponded = $surveyResponse['student_count'];
                            $optionRespondedPercent = number_format(($optionResponded * 100) / $totalResponded, 1);
                            $surveySnapshotSectionResponseDto->setResponsePercentage($optionRespondedPercent);
                            $studentResponse = $surveyResponse['option_value'];
                            $surveySnapshotSectionResponseDto->setOptionValue($studentResponse);
                            if ($questionTypeModified == 'scaled') {
                                $responses[] = $studentResponse;
                                if (in_array($studentResponse, $redValues)) {
                                    $redResponse += $optionRespondedPercent;
                                }
                                if (in_array($studentResponse, $yellowValues)) {
                                    $yellowResponse += $optionRespondedPercent;
                                }
                                if (in_array($studentResponse, $greenValues)) {
                                    $greenResponse += $optionRespondedPercent;
                                }
                            }
                            $surveySnapshotSectionResponseDto->setNoResponded($optionResponded);
                            $response[] = $surveySnapshotSectionResponseDto;
                        }
                    }
                    if ($questionTypeModified == 'scaled') {
                        $result = [];
                        $responseArray = [];
                        $responseOptions = [];
                        if (!empty($response)) {
                            foreach ($response as $res) {
                                $option = $res->getOptionValue();
                                $responseOptions[] = $option;
                                $responseArray[$option] = $res;
                            }
                            for ($i = 1; $i <= 7; $i++) {
                                if (in_array($i, $responseOptions)) {
                                    $result[] = $responseArray[$i];
                                } else {
                                    $surveySnapshotSectionResponseDto = new SurveySnapshotSectionResponseDto();
                                    $surveySnapshotSectionResponseDto->setResponseText('(' . $i . ')');
                                    $surveySnapshotSectionResponseDto->setResponsePercentage('0');
                                    $surveySnapshotSectionResponseDto->setOptionValue($i);
                                    $surveySnapshotSectionResponseDto->setNoResponded('0');
                                    $result[] = $surveySnapshotSectionResponseDto;
                                    $result = array_values($result);
                                }
                            }
                        }
                        $surveySnapshotSectionDto->setMean($surveyQuestion['mean']);
                        $surveySnapshotSectionDto->setStdDeviation($standardDeviation);
                        $surveySnapshotSectionDto->setRedResponses($redResponse);
                        $surveySnapshotSectionDto->setYellowResponses($yellowResponse);
                        $surveySnapshotSectionDto->setGreenResponses($greenResponse);
                        $surveySnapshotSectionDto->setRedOptions('1,2');
                        $surveySnapshotSectionDto->setYellowOptions('3,4,5');
                        $surveySnapshotSectionDto->setGreenOptions('6,7');
                    } else {
                        $result = $response;
                    }
                    $surveySnapshotSectionDto->setBranchDetails($branching);
                    $surveySnapshotSectionDto->setResponseOptions($result);
                    $questionsResponse[$code . $surveyQuestionNumber] = $surveySnapshotSectionDto;
                    $questionOptions[$questionTypeModified][$surveyQuestionNumber] = $surveySnapshotSectionDto;
                }
            }
        }
        // store category and scaled questions in table
        $this->saveReportRunDetails('category', $questionOptions, $reportRunningStatus, $personId);
        $this->saveReportRunDetails('scaled', $questionOptions, $reportRunningStatus, $personId);
        $this->reportRunDetailsRepository->flush();
        return $questionsResponse;
    }

    public function saveReportRunDetails($section, $questionOptions, $reportRunningStatus, $personId)
    {
        $person = $this->personRepository->find($personId);
        $sectionObj = $this->reportSectionsRepository->findOneBy([
            'title' => $section
        ]);
        if (array_key_exists($section, $questionOptions)) {
            $categorySection = array_values($questionOptions[$section]);
            $serializer = $this->container->get('jms_serializer');
            $categorySection = $serializer->serialize($categorySection, 'json');
            $ReportRunDetails = new ReportRunDetails();
            $ReportRunDetails->setReportInstance($reportRunningStatus);
            if (!empty($sectionObj)) {
                $ReportRunDetails->setSection($sectionObj);
            }
            $ReportRunDetails->setPerson($person);
            $ReportRunDetails->setResponseJson($categorySection);
            $this->reportRunDetailsRepository->create($ReportRunDetails);
        }
    }

    /**
     * get descriptive questions for survey Id and report running status Id
     *
     * @param int $surveyId
     * @param int $reportRunningStatusId
     * @param array $ebiQuestionPermissions
     * @param int $personId
     * @param string $type
     * @return array
     */
    private function getDescriptiveQuestions($surveyId, $reportRunningStatusId, $ebiQuestionPermissions, $personId, $type = 'ebiQuestion')
    {
        $reportRunningStatus = $this->reportsRunningStatusRepository->find($reportRunningStatusId);
        $reportStudentIds = $reportRunningStatus->getFilteredStudentIds();
        $reportStudentIds = explode(',', $reportStudentIds);
        $organizationId = $reportRunningStatus->getOrganization()->getId();
        $finalResponse = [];
        $questionOptions = [];
        if (!empty($reportStudentIds)) {
            if ($type == 'ebiQuestion') {
                $shortAnswer = $this->surveyResponseRepository->getDescriptiveQuestions($surveyId, $organizationId, $reportStudentIds, array_keys($ebiQuestionPermissions), 'SA', 'char_value');
                $questionId = 'ebi_question_id';
            } else {
                $shortAnswer = $this->orgQuestionResponseRepository->getDescriptiveISQResponses($surveyId, $organizationId, $reportStudentIds, 'SA', 'char_value', array_keys($ebiQuestionPermissions));
                $questionId = 'org_question_id';
            }
            if (!empty($shortAnswer)) {
                foreach ($shortAnswer as $shortAnswerQuestion) {
                    $ebiQuestionId = $shortAnswerQuestion[$questionId];
                    if ($type == 'ebiQuestion') {
                        $questionNumber = $shortAnswerQuestion['qnbr'];
                        $questionTypeCode = 'SA';
                        $code = '';
                        $surveyQuestionId = $shortAnswerQuestion['survey_questions_id'];
                    } else {
                        $questionNumber = $ebiQuestionId;
                        $questionTypeCode = 'ISQ-SA';
                        $code = 'ISQ-';
                        $surveyQuestionId = $questionNumber;
                    }
                    $shortAnswersArray[$surveyQuestionId]['question_type'] = 'shortanswer';
                    $shortAnswersArray[$surveyQuestionId]['question_type_code'] = $questionTypeCode;
                    $shortAnswersArray[$surveyQuestionId]['question_text'] = $shortAnswerQuestion['question_text'];
                    $shortAnswersArray[$surveyQuestionId]['question_qnbr'] = $questionNumber;
                    $shortAnswersArray[$surveyQuestionId]['survey_question_id'] = $surveyQuestionId;
                    $shortAnswersArray[$surveyQuestionId]['response'][] = $shortAnswerQuestion;
                }
                if (!empty($shortAnswersArray)) {
                    foreach ($shortAnswersArray as $shortAnswerArray) {
                        $sQID = $shortAnswerArray['question_qnbr'];
                        $surveyResponseSectionDto = new SurveySnapshotSectionDto();
                        $surveyResponseSectionDto->setQuestionTypeCode($shortAnswerArray['question_type_code']);
                        $surveyResponseSectionDto->setQuestionType($shortAnswerArray['question_type']);
                        $surveyResponseSectionDto->setQuestionText($shortAnswerArray['question_text']);
                        $surveyResponseSectionDto->setQuestionQnbr($sQID);
                        $surveyResponseSectionDto->setType($type);
                        $surveyResponseSectionDto->setSurveyQuestionId($shortAnswerArray['survey_question_id']);
                        $descriptive = [];
                        $totalResponded = 0;
                        foreach ($shortAnswerArray['response'] as $descriptiveResponse) {
                            $surveySnapshotSectionResponseDto = new SurveySnapshotSectionResponseDto();
                            $totalResponded += $descriptiveResponse['student_count'];
                            $surveySnapshotSectionResponseDto->setResponseText($descriptiveResponse['char_value']);
                            $descriptive[] = $surveySnapshotSectionResponseDto;
                        }
                        $surveyResponseSectionDto->setResponseOptions($descriptive);
                        $surveyResponseSectionDto->setTotalStudentsResponded($totalResponded);
                        $finalResponse[$code . $sQID] = $surveyResponseSectionDto;
                        $questionOptions['shortanswer'][$sQID] = $surveyResponseSectionDto;
                    }
                }
            }
            if ($type == 'ebiQuestion') {
                $longAnswer = $this->surveyResponseRepository->getDescriptiveQuestions($surveyId, $organizationId, $reportStudentIds, array_keys($ebiQuestionPermissions), 'LA', 'charmax_value');
                $questionId = 'ebi_question_id';
            } else {
                $longAnswer = $this->orgQuestionResponseRepository->getDescriptiveISQResponses($surveyId, $organizationId, $reportStudentIds, 'LA', 'charmax_value', array_keys($ebiQuestionPermissions));
                $questionId = 'org_question_id';
            }
            if (!empty($longAnswer)) {
                foreach ($longAnswer as $longAnswerQuestion) {
                    $ebiQuestionId = $longAnswerQuestion[$questionId];
                    $surveyQuestionId = $longAnswerQuestion['survey_questions_id'];
                    if ($type == 'ebiQuestion') {
                        $questionNumber = $longAnswerQuestion['qnbr'];
                        $questionTypeCode = 'LA';
                        $code = '';
                        $longAnswersArray[$surveyQuestionId]['survey_question_id'] = $surveyQuestionId;
                    } else {
                        $questionNumber = $ebiQuestionId;
                        $questionTypeCode = 'ISQ-LA';
                        $code = 'ISQ-';
                        $longAnswersArray[$surveyQuestionId]['survey_question_id'] = $questionNumber;
                    }
                    $longAnswersArray[$surveyQuestionId]['question_type'] = 'longanswer';
                    $longAnswersArray[$surveyQuestionId]['question_type_code'] = $questionTypeCode;
                    $longAnswersArray[$surveyQuestionId]['question_text'] = $longAnswerQuestion['question_text'];
                    $longAnswersArray[$surveyQuestionId]['question_qnbr'] = $questionNumber;
                    $longAnswersArray[$surveyQuestionId]['response'][] = $longAnswerQuestion;
                }
                if (!empty($longAnswersArray)) {
                    foreach ($longAnswersArray as $longAnswerArray) {
                        $sQID = $longAnswerArray['question_qnbr'];
                        $surveyResponseSectionDto = new SurveySnapshotSectionDto();
                        $surveyResponseSectionDto->setQuestionTypeCode($longAnswerArray['question_type_code']);
                        $surveyResponseSectionDto->setQuestionType($longAnswerArray['question_type']);
                        $surveyResponseSectionDto->setQuestionText($longAnswerArray['question_text']);
                        $surveyResponseSectionDto->setQuestionQnbr($sQID);
                        $surveyResponseSectionDto->setType($type);
                        $surveyResponseSectionDto->setSurveyQuestionId($longAnswerArray['survey_question_id']);
                        $descriptive = [];
                        $totalResponded = 0;
                        foreach ($longAnswerArray['response'] as $descriptiveResponse) {
                            $surveySnapshotSectionResponseDto = new SurveySnapshotSectionResponseDto();
                            $totalResponded += $descriptiveResponse['student_count'];
                            $surveySnapshotSectionResponseDto->setResponseText($descriptiveResponse['charmax_value']);
                            $descriptive[] = $surveySnapshotSectionResponseDto;
                        }
                        $surveyResponseSectionDto->setResponseOptions($descriptive);
                        $surveyResponseSectionDto->setTotalStudentsResponded($totalResponded);
                        $finalResponse[$code . $sQID] = $surveyResponseSectionDto;
                        $questionOptions['longanswer'][$sQID] = $surveyResponseSectionDto;
                    }
                }
            }
        }
        $this->saveReportRunDetails('shortanswer', $questionOptions, $reportRunningStatus, $personId);
        $this->saveReportRunDetails('longanswer', $questionOptions, $reportRunningStatus, $personId);
        return $finalResponse;
    }

    /**
     * get numeric questions for survey Id and report running status Id
     *
     * @param int $surveyId
     * @param int $reportRunningStatusId
     * @param array $ebiQuestionPermissions
     * @param int $personId
     * @param string $type
     * @return array
     */
    private function getNumericQuestions($surveyId, $reportRunningStatusId, $ebiQuestionPermissions, $personId, $type = 'ebiQuestion')
    {
        $reportRunningStatus = $this->reportsRunningStatusRepository->find($reportRunningStatusId);
        $reportStudentIds = $reportRunningStatus->getFilteredStudentIds();
        $reportStudentIds = explode(',', $reportStudentIds);
        $organizationId = $reportRunningStatus->getOrganization()->getId();
        $numericResponseResultsReportRunning = [];
        $numericResponseResults = [];
        if (!empty($reportStudentIds)) {
            if ($type == 'ebiQuestion') {
                $numericQuestions = $this->surveyResponseRepository->getNumericQuestions($surveyId, $organizationId, $reportStudentIds, array_keys($ebiQuestionPermissions));
                $questionId = 'ebi_question_id';
                $questionNumber = 'qnbr';
                $questionTypeCode = 'NA';
                $code = '';
            } else {
                $numericQuestions = $this->orgQuestionResponseRepository->getNumericISQResponseCounts($surveyId, $reportStudentIds, $organizationId, array_keys($ebiQuestionPermissions));
                $questionId = 'org_question_id';
                $questionNumber = 'org_question_id';
                $questionTypeCode = 'ISQ-NA';
                $code = 'ISQ-';
            }
            foreach ($numericQuestions as $numericQuestion) {
                $ebiQuestionId = $numericQuestion[$questionId];
                $numericQuestionNumber = $numericQuestion[$questionNumber];
                $surveyQuestionId = ($type == 'ebiQuestion') ? $numericQuestion['survey_questions_id'] : $numericQuestionNumber;
                $responses = [];
                $surveySnapshotSectionDto = new SurveySnapshotSectionDto();
                $surveySnapshotSectionDto->setQuestionType('numeric');
                $surveySnapshotSectionDto->setQuestionTypeCode($questionTypeCode);
                $surveySnapshotSectionDto->setQuestionText($numericQuestion['question_text']);
                $surveySnapshotSectionDto->setQuestionQnbr($numericQuestionNumber);
                $surveySnapshotSectionDto->setSurveyQuestionId($surveyQuestionId);
                $surveySnapshotSectionDto->setType($type);
                $reportStudentIds = $ebiQuestionPermissions[$ebiQuestionId];
                $total = count($reportStudentIds);
                $surveySnapshotSectionDto->setTotalStudentsResponded($numericQuestion['student_count']);
                $surveySnapshotSectionDto->setTotalStudents($total);
                if ($type == 'ebiQuestion') {
                    $numericResponse = $this->surveyResponseRepository->getNumericQuestionsResponse($surveyId, $organizationId, $reportStudentIds, $surveyQuestionId)[0];
                } else {
                    $numericResponse = $this->orgQuestionResponseRepository->getNumericISQCalculatedResponses($surveyId, $organizationId, $reportStudentIds, $ebiQuestionId)[0];
                }
                $branchDetails = $this->surveyBranchRepository->getQuestionBranchDetails($surveyId, $surveyQuestionId);
                $branching = [];
                if (!empty($branchDetails)) {
                    foreach ($branchDetails as $branchDetail) {
                        $branches['option_text'] = $branchDetail['option_text'];
                        $branches['source_qtype'] = $branchDetail['type'];
                        $branches['source_qnbr'] = $branchDetail['qnbr'];
                        $branches['source_question_text'] = $branchDetail['question_text'];
                        $branching[] = $branches;
                    }
                }
                if ($numericResponse) {
                    $summary = [];
                    if ($type == 'ebiQuestion') {
                        $responseValues = $this->surveyResponseRepository->getNumericResponse($surveyId, $organizationId, implode(',', $reportStudentIds), $surveyQuestionId);
                    } else {
                        $responseValues = $this->orgQuestionResponseRepository->getNumericISQResponses($surveyId, $organizationId, $reportStudentIds, $ebiQuestionId);
                    }
                    if (!empty($responseValues)) {
                        foreach ($responseValues as $responseValue) {
                            $responses[] = $responseValue['decimal_value'];
                        }
                    }
                    $summary[] = $this->getNumericSummary($numericResponse, $total, $responses);
                    $binValues = $this->getBinRange($numericResponse['maximum_value'], $numericResponse['minimum_value'], $responses, $numericResponse['responded_count']);
                    $surveySnapshotSectionDto->setResponseSummary($summary);
                    $surveySnapshotSectionDto->setResponseOptions($binValues);
                    $surveySnapshotSectionDto->setBranchDetails($branching);
                    $numericResponseResults[$code . $numericQuestionNumber] = $surveySnapshotSectionDto;
                    $numericResponseResultsReportRunning['numeric'][$numericQuestionNumber] = $surveySnapshotSectionDto;
                }
            }
        }
        $this->saveReportRunDetails('numeric', $numericResponseResultsReportRunning, $reportRunningStatus, $personId);
        return $numericResponseResults;
    }

    private function array_count_between_elements($array, $min, $max)
    {
        $count = 0;
        foreach ($array as $val) {
            if (($val >= $min && $val < $max)) {
                ++$count;
            }
        }
        return $count;
    }

    private function getBinRange($maximum, $minimum, $responses, $responded)
    {
        $bin = array();
        $binCount = 10;
        $binCount = ($maximum <= $binCount) ? $maximum : $binCount;
        $noLoop = round(($maximum - $minimum) / $binCount);
        for ($i = 0; $i <= $noLoop; $i++) {
            $binStart = $minimum + ($i * $binCount);
            $binEnd = $binStart + $binCount;
            $totalRespondedBtwBinRange = $this->array_count_between_elements($responses, $binStart, $binEnd);
            $binRange = $binStart . '-' . $binEnd;
            $binPercentage = ($totalRespondedBtwBinRange * 100) / $responded;
            $bin['bin_range'] = $binRange;
            $bin['responded'] = $totalRespondedBtwBinRange;
            $bin['responded_percentage'] = number_format($binPercentage, 1);
            $response[] = $bin;
        }
        return $response;
    }

    /**
     * Get Numeric Response summery
     *
     * @param array $numericResponse
     * @param int $total
     * @param array $responses
     * @return SurveySnapshotSectionResponseDto
     */
    private function getNumericSummary($numericResponse, $total, $responses)
    {
        $minimum = $numericResponse['minimum_value'];
        $maximum = $numericResponse['maximum_value'];
        $responded = $numericResponse['responded_count'];
        $standardDeviation = number_format($numericResponse['standard_deviation'], 2);
        $mean = number_format($numericResponse['mean'], 2);
        sort($responses);
        if (count($responses) % 2 == 0) {
            $middleKey = ceil(count($responses) / 2);
            $previousMiddleKey = $middleKey - 1;
            $median = ($responses[$middleKey] + $responses[$previousMiddleKey]) / 2;
        } else {
            $middleKey = ceil(count($responses) / 2) - 1;
            $median = $responses[$middleKey];
        }
        $countByResponse = array_count_values($responses);
        asort($countByResponse);
        end($countByResponse);
        $mode = key($countByResponse);

        $percent = number_format(($responded * 100) / $total, 1);
        $responseOption = new SurveySnapshotSectionResponseDto();
        $responseOption->setPercentageOfResponse($percent);
        $responseOption->setMean($mean);
        $responseOption->setMode($mode);
        $responseOption->setMedian($median);
        $responseOption->setMin($minimum);
        $responseOption->setMax($maximum);
        $responseOption->setStdDeviation($standardDeviation);
        return $responseOption;
    }

    /**
     * Create Report notification
     *
     * @param ReportRunningStatusDto $reportRunningStatusObject
     */
    private function createReportNotification($reportRunningStatusObject)
    {
        $reportRunningStatus = $reportRunningStatusObject->getReports();
        $shortCode = $reportRunningStatus->getShortCode();
        $reportName = $reportRunningStatus->getName();
        $person = $reportRunningStatusObject->getPerson();
        $this->alertNotificationsService->createNotification($shortCode, $reportName, $person, null, null, null, null, null, null, null, null, $reportRunningStatusObject);
    }

    /**
     * get students associated to dataBlocks
     *
     * @param string $reportStudents
     * @param int $personId
     * @param int $organizationId
     * @param array $questionsWithDataBlockId
     * @return array
     */
    private function getStudentsAssociatedWithDataBlocks($reportStudents, $personId, $organizationId, $questionsWithDataBlockId)
    {
        $ebiQuestionIds = array_column($questionsWithDataBlockId, 'ebi_question_id');
        $studentsAssociatedWithDataBlock = $this->surveyResponseRepository->getStudentsBasedQuestionPermission($personId, $organizationId, $ebiQuestionIds, $reportStudents);
        $reportStudentsArr = explode(',', $reportStudents);

        $dataBlockWithStudents = [];
        foreach ($studentsAssociatedWithDataBlock as $studentWithDataBlock) {
            if (in_array($studentWithDataBlock['student_id'], $reportStudentsArr)) {
                $dataBlockWithStudents[$studentWithDataBlock['datablock_id']][] = $studentWithDataBlock['student_id'];
            }
        }

        $questionsWithStudents = [];
        foreach ($questionsWithDataBlockId as $questionWithDataBlock) {
            if (!isset($dataBlockWithStudents[$questionWithDataBlock['datablock_id']])
                || empty($dataBlockWithStudents[$questionWithDataBlock['datablock_id']])
            ) {
                continue;
            }
            if (!isset($questionsWithStudents[$questionWithDataBlock['ebi_question_id']])) {
                $questionsWithStudents[$questionWithDataBlock['ebi_question_id']] = [];
            }

            $studentIds = array_merge($questionsWithStudents[$questionWithDataBlock['ebi_question_id']], $dataBlockWithStudents[$questionWithDataBlock['datablock_id']]);
            $questionsWithStudents[$questionWithDataBlock['ebi_question_id']] = array_unique($studentIds);
        }
        return $questionsWithStudents;
    }

    /**
     * get survey questions based from search attributes
     *
     * @param array $searchAttributes
     * @return array
     */
    private function getSelectedSurveyQuestionsForReport($searchAttributes)
    {

        if (!isset($searchAttributes['survey'])
            || empty($searchAttributes['survey'])
        ) {

            return [];
        }

        $surveyQuestions = [];
        $searchAttributes = $searchAttributes['survey'];

        foreach ($searchAttributes as $searchAttribute) {

            if (!isset($searchAttribute['survey_id'])
                || empty($searchAttribute['survey_id'])
            ) {

                continue;
            }

            if (!isset($searchAttribute['survey_questions'])
                || empty($searchAttribute['survey_questions'])
            ) {

                continue;
            }

            foreach ($searchAttribute['survey_questions'] as $surveyQues) {

                if (!isset($surveyQues['id'])
                    || empty($surveyQues['id'])
                ) {

                    continue;
                }
                $surveyQuestions[] = $surveyQues['id'];
            }
        }

        return $surveyQuestions;
    }

    private function getSelectedSurveyOrgQuestionsForReport($searchAttributes)
    {

        if (!isset($searchAttributes['isqs'])
            || empty($searchAttributes['isqs'])
        ) {

            return [];
        }

        $orgQuestions = [];
        $searchAttributes = $searchAttributes['isqs'];

        foreach ($searchAttributes as $searchAttribute) {

            if (!isset($searchAttribute['survey_id'])
                || empty($searchAttribute['survey_id'])
            ) {

                continue;
            }

            if (!isset($searchAttribute['isqs'])
                || empty($searchAttribute['isqs'])
            ) {

                continue;
            }
            foreach ($searchAttribute['isqs'] as $orgQues) {

                if (!isset($orgQues['id'])
                    || empty($orgQues['id'])
                ) {

                    continue;
                }
                $orgQuestions[] = $orgQues['id'];
            }
        }

        return $orgQuestions;
    }

    /**
     * @param $reportStudents
     * @param $personId
     * @param $orgId
     * @return array
     */
    public function fetchStudentsPermittedOrgQuestions($reportStudents, $personId, $orgId)
    {
        $reportStudentsArr = explode(',', $reportStudents);
        $studentsAssociatedWithOrgQuestions = $this->orgPermissionsetQuestionRepository->getStudentsAnsweredOrgQuestionPermission($personId, $orgId, $reportStudentsArr);
        $studentsAndISQsWhichAGivenFacultyMemberHasISQPermissionsToSee = [];
        foreach ($studentsAssociatedWithOrgQuestions as $studentWithOrgQuestions) {
            if (in_array($studentWithOrgQuestions['student_id'], $reportStudentsArr)) {
                $studentsAndISQsWhichAGivenFacultyMemberHasISQPermissionsToSee[$studentWithOrgQuestions['org_question_id']][] = $studentWithOrgQuestions['student_id'];
            }
        }
        return $studentsAndISQsWhichAGivenFacultyMemberHasISQPermissionsToSee;
    }


    /**
     * Get the DrillDown Response for Survey Snapshot Report
     *
     * @param int $loggedInUserId
     * @param int $reportInstanceId
     * @param int $questionId
     * @param string $questionSource
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $sortBy
     * @param array $optionValues
     * @return SurveySnapshotReportDrillDownDto
     * @throws AccessDeniedException
     */
    public function getDrilldownJSONResponse($loggedInUserId, $reportInstanceId, $questionId, $questionSource, $pageNumber, $recordsPerPage, $sortBy, $optionValues)
    {
        $allAccessFlag = false;     // This will be set to true if the user has individual access and risk access for all students included in the report.

        // Get an array of all the students selected for this report via the preliminary filters.
        $reportInstance = $this->reportsRunningStatusRepository->find($reportInstanceId);
        $filteredStudentIds = explode(',', $reportInstance->getFilteredStudentIds());

        $organizationId = $reportInstance->getOrganization()->getId();

        //Get the current org academic year and the survey ID on which the report was run.
        $reportResultArray = json_decode($reportInstance->getResponseJson(), true);
        $surveyId = $reportResultArray['request_json']['search_attributes']['survey_filter']['survey_id'];
        $currentOrgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);


        // Get a list of students whose data should be accessible for the given question.
        // Then intersect with the filtered students to get the students potentially available in the drilldown.
        // Note: These students may not actually be individually accessible because of aggregate permission sets.
        // The access level for these students needs to be handled later, after we know how many actually have responses.
        $accessibleStudentIds = $this->getAccessibleStudentsForQuestion($loggedInUserId, $organizationId, $surveyId, $questionSource, $questionId);
        $filteredAccessibleStudentIds = array_intersect($filteredStudentIds, $accessibleStudentIds);

        // Check if the user has individual access and access to risk for all students included in the report.
        $accessLevelCounts = $this->orgPermissionsetRepository->getGroupedAccessLevelsForFacultyAndStudents($loggedInUserId, $filteredAccessibleStudentIds);
        if (is_null($accessLevelCounts[0])) {
            $riskPermission = $this->orgPermissionsetRepository->getGroupedRiskPermissionsForFacultyAndStudents($loggedInUserId, $filteredAccessibleStudentIds);
            if (is_null($riskPermission[0])) {
                $listContainsNonParticipants = $this->orgPersonStudentYearRepository->doesStudentIdListContainNonParticipants($filteredAccessibleStudentIds, $currentOrgAcademicYearId);
                if (!$listContainsNonParticipants) {
                    $allAccessFlag = true;
                }
            }
        }

        // If the user has individual and risk access to all the students included in the report, then we don't have to be as careful;
        // we can just get the records for the requested page and separately get a count of all students that have a response for this question.
        if ($allAccessFlag) {
            $drilldownRecords = $this->getDrilldownRecords($loggedInUserId, $filteredAccessibleStudentIds, $questionSource, $questionId, $optionValues, $sortBy, $recordsPerPage, $pageNumber, $allAccessFlag, $currentOrgAcademicYearId);

            if ($questionSource == 'isq') {
                $recordCount = $this->orgQuestionResponseRepository->getOrgQuestionResponsesByQuestionAndStudentIds($questionId, $filteredAccessibleStudentIds, $optionValues, true);
            } else {
                $recordCount = $this->surveyResponseRepository->getSurveyResponsesByQuestionAndStudentIds($questionId, $filteredAccessibleStudentIds, $optionValues, true);
            }

            //IndividualParticipantCount is the same as record count here, so no need to set variable
            //When $allAccessFlag is true, all the below counts have to be zero.
            $aggregateOnlyParticipantCount = 0;
            $aggregateOnlyNonParticipantCount = 0;
            $individualNonParticipantCount = 0;


        } else {

            // Get responses for the given question for the list of students we just found.
            // This is a preliminary query to narrow down the list of students to only those who have responses to this question.
            // It's needed to get an accurate count for the aggregation restriction.
            if ($questionSource == 'isq') {
                $responses = $this->orgQuestionResponseRepository->getOrgQuestionResponsesByQuestionAndStudentIds($questionId, $filteredAccessibleStudentIds, $optionValues);
            } else {
                $responses = $this->surveyResponseRepository->getSurveyResponsesByQuestionAndStudentIds($questionId, $filteredAccessibleStudentIds, $optionValues);
            }

            $studentIdsForStudentsWhoHaveResponses = array_column($responses, 'person_id');

            // Determine whether the user has individual or aggregate permission to each of the students who have a response to this question.
            $accessLevels = $this->orgPermissionsetRepository->getAccessLevelForFacultyAndStudents($loggedInUserId, $studentIdsForStudentsWhoHaveResponses);
            $individuallyAccessibleStudents = array_keys($accessLevels, 1);
            $aggregateOnlyStudents = array_keys($accessLevels, 0);

            // For each of these groupings, determine which students are participants in the current year.
            $individuallyAccessibleParticipants = $this->orgPersonStudentYearRepository->getParticipantStudentsFromStudentList($individuallyAccessibleStudents, $organizationId, $currentOrgAcademicYearId);
            $aggregateOnlyParticipants = $this->orgPersonStudentYearRepository->getParticipantStudentsFromStudentList($aggregateOnlyStudents, $organizationId, $currentOrgAcademicYearId);

            //Set the counts of each group of students.
            $individualParticipantCount = count($individuallyAccessibleParticipants);
            $individualNonParticipantCount = count($individuallyAccessibleStudents) - $individualParticipantCount;

            $aggregateOnlyParticipantCount = count($aggregateOnlyParticipants);
            $aggregateOnlyNonParticipantCount = count($aggregateOnlyStudents) - $aggregateOnlyParticipantCount;

            // Determine whether to allow access to any drilldown records, and throw an exception if not.
            if ($individualParticipantCount == 0) {
                throw new AccessDeniedException('There are no accessible students in this drilldown.');
            } else {
                $recordCount = $individualParticipantCount;
            }

            // Get all the data needed for the current page of results, including student names, risk, class levels, and responses.
            $drilldownRecords = $this->getDrilldownRecords($loggedInUserId, $individuallyAccessibleParticipants, $questionSource, $questionId, $optionValues, $sortBy, $recordsPerPage, $pageNumber, false, $currentOrgAcademicYearId);
        }


        // ToDo (long-term): Create new DTOs similar to the ones used in the Profile Snapshot Report, which extend the same base DTOs, rather than using SearchResultListDto and SearchDto, which have many unnecessary properties.
        // ToDo (long-term): This creation of the new DTOs should happen at the same time that the JSON for search and reports is improved application-wide.

        // Assign values to the DTO properties for each student.
        $drilldownRecordDtos = [];
        foreach ($drilldownRecords as $record) {
            $drilldownRecordDto = new SearchResultListDto();

            $drilldownRecordDto->setStudentId($record['student_id']);
            $drilldownRecordDto->setStudentFirstName($record['firstname']);
            $drilldownRecordDto->setStudentLastName($record['lastname']);
            $drilldownRecordDto->setStudentRiskStatus($record['risk_color']);
            $drilldownRecordDto->setStudentRiskImageName($record['risk_image_name']);
            $drilldownRecordDto->setStudentClasslevel($record['class_level']);
            $drilldownRecordDto->setResponse($record['response']);

            // Set student status

            if (! empty($record['is_active']) && (int)$record['is_active'] == 1 ) {
                $studentStatus = true;
            } else {
                $studentStatus = false;
            }
            $drilldownRecordDto->setStudentIsActive($studentStatus);
            $drilldownRecordDtos[] = $drilldownRecordDto;
        }

        // Assign appropriate values to the top-level DTO properties.
        $surveySnapshotReportDrilldownDto = new SurveySnapshotReportDrilldownDto();
        $surveySnapshotReportDrilldownDto->setPersonId($loggedInUserId);
        $surveySnapshotReportDrilldownDto->setCurrentPage($pageNumber);
        $surveySnapshotReportDrilldownDto->setRecordsPerPage($recordsPerPage);
        $surveySnapshotReportDrilldownDto->setTotalRecords($recordCount);
        $surveySnapshotReportDrilldownDto->setAggregateOnlyNonParticipantCount($aggregateOnlyNonParticipantCount);
        $surveySnapshotReportDrilldownDto->setAggregateOnlyParticipantCount($aggregateOnlyParticipantCount);
        $surveySnapshotReportDrilldownDto->setIndividualNonParticipantCount($individualNonParticipantCount);

        $pageCount = ceil($recordCount / $recordsPerPage);
        $surveySnapshotReportDrilldownDto->setTotalPages($pageCount);

        $questionText = $this->getQuestionText($questionSource, $questionId);
        $surveySnapshotReportDrilldownDto->setQuestion($questionText);

        $searchAttributes = $reportResultArray['request_json']['search_attributes'];
        $surveySnapshotReportDrilldownDto->setSearchAttributes($searchAttributes);

        $surveySnapshotReportDrilldownDto->setSearchResult($drilldownRecordDtos);

        return $surveySnapshotReportDrilldownDto;
    }


    /**
     * get Accessible Students by Connection and access to question id (ISQ or Survey Response)
     *
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param int $surveyId
     * @param string $questionSource - 'isq'|'ebi'
     * @param int $questionId
     * @return array
     */
    private function getAccessibleStudentsForQuestion($loggedInUserId, $organizationId, $surveyId, $questionSource, $questionId)
    {
        $hasAccessToAllISQs = false;
        if ($questionSource == 'isq') {
            $permissionSets = $this->orgPermissionsetRepository->getAllPermissionsetIdsByPerson($loggedInUserId, $organizationId);

            if ($permissionSets) {
                $permissionSetArray = [];
                foreach ($permissionSets as $permissionSet) {
                    $permissionSetArray[] = $permissionSet['org_permissionset_id'];
                }

                $hasAccessToAllISQs = $this->orgPermissionsetQuestionRepository->permissionsetsHaveAccessToEveryCurrentAndFutureISQ($permissionSetArray, false);
            }

            if ($hasAccessToAllISQs) {
                $accessibleStudents = $this->orgPermissionsetRepository->getParticipatingAndNonParticipatingStudentIdsBasedOnFacultyPermission($loggedInUserId, $organizationId);
            } else {
                $accessibleStudents = $this->orgPermissionsetQuestionRepository->getStudentIdsBasedOnFacultyOrgQuestionPermission($loggedInUserId, $organizationId, $surveyId, $questionId);
            }

        } else {
            $questionBankObject = $this->questionBankMapRepository->findOneBy(['surveyQuestion' => $questionId])->getQuestionBank();
            $datablock = $this->datablockQuestionsRepository->findOneBy(['questionBank' => $questionBankObject])->getDatablock()->getId();
            $groups = $this->orgPermissionsetDatablockRepository->getGroupsWithGivenDatablockForPerson($loggedInUserId, $datablock);
            $courses = $this->orgPermissionsetDatablockRepository->getCoursesWithGivenDatablockForPerson($loggedInUserId, $datablock);

            $studentsInGroups = [];
            $studentsInCourses = [];
            if (!empty($groups)) {
                $studentsInGroups = $this->orgGroupStudentsRepository->getStudentsByGroups($groups);
            }
            if (!empty($courses)) {
                $studentsInCourses = $this->orgCourseStudentRepository->getStudentsByCourses($courses);
            }

            $accessibleStudents = array_unique(array_merge($studentsInGroups, $studentsInCourses));
        }

        return $accessibleStudents;
    }


    /**
     * Get Drill Down Records, checking risk & intent-to-leave permission
     *
     * @param int $loggedInUserId
     * @param array $studentIds
     * @param string $questionSource - 'isq'|'ebi'
     * @param int $questionId
     * @param array $optionValues
     * @param string $sortBy
     * @param int|null $recordsPerPage
     * @param int|null $pageNumber
     * @param bool|false $allAccessFlag
     * @param int|null $orgAcademicYearId
     * @return array
     * @throws SynapseValidationException
     */
    private function getDrilldownRecords($loggedInUserId, $studentIds, $questionSource, $questionId, $optionValues, $sortBy, $recordsPerPage = null, $pageNumber = null, $allAccessFlag = false, $orgAcademicYearId = null)
    {
        $classLevelMetadataId = $this->ebiMetadataRepository->findOneBy(['key' => 'ClassLevel'])->getId();

        if (is_numeric($recordsPerPage) && is_numeric($pageNumber)) {
            $offset = $recordsPerPage * ($pageNumber - 1);      // The index of the first record to include in the list.
        } else {
            $offset = 0;
        }
        
        if ($questionSource == 'isq') {
            $responseRepository = $this->orgQuestionResponseRepository;
        } else {
            $responseRepository = $this->surveyResponseRepository;
        }

        if (strpos($sortBy, 'risk') === false) {

            // For sorting by anything besides risk, we simply get the appropriate number of results at the appropriate offset,
            // then determine the risk permission for them to be applied later.
            $drilldownRecords = $responseRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndResponses($questionId, $studentIds, $classLevelMetadataId, $optionValues, $sortBy, $recordsPerPage, $offset, $orgAcademicYearId);
            if (empty($drilldownRecords)) {
                throw new SynapseValidationException("There are no accessible students in this drilldown.");
            }
            $studentsOnCurrentPage = array_column($drilldownRecords, 'student_id');

            $riskPermission = $this->orgPermissionsetRepository->getRiskPermissionForFacultyAndStudents($loggedInUserId, $studentsOnCurrentPage);

        } else {

            // For sorting by risk, we need to create separate lists of students with and without the risk permission;
            // otherwise, the risk level of those without the risk permission would be obvious by where they appear in the list.
            // The $offset and $recordsPerPage are used to determine which student list to use (or possibly both).
            // The records for students without the risk permission will appear alphabetically at the end of the list.

            if ($allAccessFlag) {
                $studentsWithRiskPermission = $studentIds;
                $studentsWithoutRiskPermission = [];
                $riskPermission = [];
            } else {
                $riskPermission = $this->orgPermissionsetRepository->getRiskPermissionForFacultyAndStudents($loggedInUserId, $studentIds);
                $studentsWithRiskPermission = array_keys($riskPermission, 1);
                $studentsWithoutRiskPermission = array_keys($riskPermission, 0);
            }

            if (is_null($recordsPerPage)) {
                $drilldownRecordsWithRisk = $responseRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndResponses($questionId, $studentsWithRiskPermission, $classLevelMetadataId, $optionValues, $sortBy, null, null, $orgAcademicYearId);
                $drilldownRecordsWithoutRisk = $responseRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndResponses($questionId, $studentsWithoutRiskPermission, $classLevelMetadataId, $optionValues, 'name', null, null, $orgAcademicYearId);
                $drilldownRecords = array_merge($drilldownRecordsWithRisk, $drilldownRecordsWithoutRisk);

            } else {

                if ($offset < count($studentsWithRiskPermission)) {
                    $drilldownRecords = $responseRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndResponses($questionId, $studentsWithRiskPermission, $classLevelMetadataId, $optionValues, $sortBy, $recordsPerPage, $offset, $orgAcademicYearId);

                    if ((count($drilldownRecords) < $recordsPerPage) && (count($studentsWithoutRiskPermission) > 0)) {
                        $noRiskRecordLimit = $recordsPerPage - count($drilldownRecords);
                        $extraDrilldownRecords = $responseRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndResponses($questionId, $studentsWithoutRiskPermission, $classLevelMetadataId, $optionValues, 'name', $noRiskRecordLimit, 0, $orgAcademicYearId);
                        $drilldownRecords = array_merge($drilldownRecords, $extraDrilldownRecords);
                    }
                } else {
                    $noRiskOffset = $offset - count($studentsWithRiskPermission);
                    $drilldownRecords = $responseRepository->getStudentNamesAndRiskLevelsAndClassLevelsAndResponses($questionId, $studentsWithoutRiskPermission, $classLevelMetadataId, $optionValues, 'name', $recordsPerPage, $noRiskOffset, $orgAcademicYearId);
                }
            }
        }

        $grayRiskImageName = $this->riskLevelsRepository->findOneBy(['riskText' => 'gray'])->getImageName();

        // Find the question type.
        // If it's a categorical or scaled question, get a lookup table of options
        if ($questionSource == 'isq') {
            // ToDo: Get the question type for ISQs. Josh S Done
            $orgQuestion = $this->orgQuestionRepository->find($questionId);
            $questionType = $orgQuestion->getQuestionType();

        } else {
            $questionBankObject = $this->questionBankMapRepository->findOneBy(['surveyQuestion' => $questionId])->getQuestionBank();
            $questionType = $questionBankObject->getQuestionType();
        }

        $optionNames = [];
        if (in_array($questionType, ['categorical', 'scaled'])) {
            if ($questionSource == 'isq') {
                $orgQuestionOptionsArray = $this->orgQuestionOptionsRepository->findBy(['orgQuestion' => $questionId]);
                foreach ($orgQuestionOptionsArray as $orgQuestionOption) {
                    $optionNames[$orgQuestionOption['option_value']] = $orgQuestionOption['option_name'];
                }
            } else {
                $surveyQuestion = $this->surveyQuestionRepository->find($questionId);
                $questionId = $surveyQuestion->getEbiQuestion();
                $ebiQuestionOptionsArray = $this->ebiQuestionOptionsRepository->findBy(['ebiQuestion' => $questionId]);

                foreach ($ebiQuestionOptionsArray as $ebiQuestionOptions) {
                    $optionValue = $ebiQuestionOptions->getOptionValue();
                    $optionText = $ebiQuestionOptions->getExtendedOptionText();
                    $optionNames[$optionValue] = $optionText;
                }

            }
        }

        // Replace or null out risk and/or response values as needed.
        foreach ($drilldownRecords as &$record) {

            // For records with risk permission but without a risk value, set the risk color to gray.
            // Null out risk for records without those permissions.
            if ($allAccessFlag || $riskPermission[$record['student_id']]) {
                if (empty($record['risk_color'])) {
                    $record['risk_color'] = 'gray';
                    $record['risk_image_name'] = $grayRiskImageName;
                }
            } else {
                $record['risk_color'] = null;
                $record['risk_image_name'] = null;
            }

            // Use the text option for categorical and scaled questions; for the other types, use the actual recorded value.
            if (in_array($questionType, ['categorical', 'scaled'])) {
                $index = (int)$record['response'];
                $record['response'] = $optionNames[$index];
            }
        }

        return $drilldownRecords;
    }


    /**
     * Get the question text for either an ISQ or EBI question.
     *
     * @param string $questionSource
     * @param int $questionId
     * @return string
     */
    private function getQuestionText($questionSource, $questionId)
    {
        if ($questionSource == 'isq') {
            $orgQuestionObject = $this->orgQuestionRepository->find($questionId);
            $questionText = $orgQuestionObject->getQuestionText();
        } else {
            $questionBankObject = $this->questionBankMapRepository->findOneBy(['surveyQuestion' => $questionId])->getQuestionBank();
            $introText = $questionBankObject->getIntroText();
            $questionText = $questionBankObject->getText();
            if (!empty($introText)) {
                $questionText = "$introText $questionText";
            }
        }

        return $questionText;
    }

    /**
     * Gets the student data for a drilldown from the Survey Snapshot report
     *
     * @param int $loggedInUserId
     * @param int $reportInstanceId
     * @param int $questionId
     * @param string $questionSource
     * @param string $sortBy
     * @param array $optionValues
     * @param string $questionTypeCode
     * @return array
     */
    public function getDrillDownCSV($loggedInUserId, $reportInstanceId, $questionId, $questionSource, $sortBy, $optionValues, $questionTypeCode)
    {
        // For term-specific items, a year may not have been passed in but is needed in the item text.
        $reportInstance = $this->reportsRunningStatusRepository->find($reportInstanceId);
        $filteredStudentIds = explode(',', $reportInstance->getFilteredStudentIds());

        $organizationId = $reportInstance->getOrganization()->getId();

        //Get the current org academic year and the survey ID on which the report was run.
        $reportResultArray = json_decode($reportInstance->getResponseJson(), true);
        $surveyId = $reportResultArray['request_json']['search_attributes']['survey_filter']['survey_id'];

        $person = $this->personRepository->find($loggedInUserId);

        // Find all students that should be included in the CSV.
        $accessibleStudents = $this->getAccessibleStudentsForQuestion($loggedInUserId, $organizationId, $surveyId, $questionSource, $questionId);
        $studentIds = array_intersect($filteredStudentIds, $accessibleStudents);

        //Get all individually accessible participant students for the user
        $individuallyAccessibleParticipants = $this->reportDrilldownService->getIndividuallyAccessibleParticipants($loggedInUserId, $organizationId, $studentIds);

        // Get all the data needed, including student names, risk, class levels, and profile item values.
        $surveyDrilldownRecords = $this->getDrilldownRecords($loggedInUserId, $individuallyAccessibleParticipants, $questionSource, $questionId, $optionValues, $sortBy);

        // Format the file name and path.
        $filePath = SynapseConstant::S3_ROOT . ReportsConstants::S3_REPORT_CSV_EXPORT_DIRECTORY . '/';
        $fileName = "{$organizationId}-{$loggedInUserId}-{$questionTypeCode}-{$questionId}-snapshot-report.csv";

        $organizationCurrentDateTime = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, 'Y-m-d H:i:s');
        $organizationCurrentDateTimeArray = explode(' ', $organizationCurrentDateTime);

        //Create the data for the second and third rows of the CSV file
        $reportUsersNameAndDate = $person->getFirstname() . " " . $person->getLastname() . " on " . $organizationCurrentDateTimeArray[0] . " at " . $organizationCurrentDateTimeArray[1];

        if ($questionSource == 'isq') {
            $questionObject = $this->orgQuestionRepository->find($questionId);
            $questionText = $questionObject->getQuestionText();
        } else {
            $questionBankObject = $this->questionBankMapRepository->findOneBy(['surveyQuestion' => $questionId])->getQuestionBank();
            $questionText = $questionBankObject->getIntroText() . " " . $questionBankObject->getText();
        }

        $preliminaryRows = [
            ['Survey Snapshot Report'],
            [$reportUsersNameAndDate],
            [$questionText],
            ['']
        ];

        $columnHeaders = [
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'external_id' => 'External Id',
            'username' => 'Email',
            'risk_color' => 'Risk',
            'class_level' => 'Class Level',
            'response' => 'Response'
        ];

        $this->csvUtilityService->generateCSV($filePath, $fileName, $surveyDrilldownRecords, $columnHeaders, $preliminaryRows);

        $response = [];
        $response['file_name'] = $fileName;
        return $response;
    }


    /**
     * For option Names-only, get a list of Student Ids and Names Given all the filtering in the report
     * Note: Used for Bulk Actions
     *
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param int $reportInstanceId
     * @param string $questionSource - 'isq'|'ebi'
     * @param int $questionId
     * @param array $optionValues
     * @return array
     */
    public function getStudentIdsAndNames($loggedInUserId, $organizationId, $reportInstanceId, $questionSource, $questionId, $optionValues)
    {

        // Get an array of all the students selected for this report via the preliminary filters.
        $reportInstance = $this->reportsRunningStatusRepository->find($reportInstanceId);
        $filteredStudentIds = explode(',', $reportInstance->getFilteredStudentIds());

        //Get the current org academic year and the survey ID on which the report was run.
        $reportResultArray = json_decode($reportInstance->getResponseJson(), true);
        $surveyId = $reportResultArray['request_json']['search_attributes']['survey_filter']['survey_id'];

        // Find the ids of all students who should be included.
        $accessibleStudents = $this->getAccessibleStudentsForQuestion($loggedInUserId, $organizationId, $surveyId, $questionSource, $questionId);
        $studentIdsFromAccessibleStudents = array_intersect($filteredStudentIds, $accessibleStudents);

        $individuallyAccessibleParticipants = $this->reportDrilldownService->getIndividuallyAccessibleParticipants($loggedInUserId, $organizationId, $studentIdsFromAccessibleStudents);

        // Get responses for the given question for the list of students we just found.
        if ($questionSource == 'isq') {
            $responses = $this->orgQuestionResponseRepository->getOrgQuestionResponsesByQuestionAndStudentIds($questionId, $individuallyAccessibleParticipants, $optionValues);
        } else {
            $responses = $this->surveyResponseRepository->getSurveyResponsesByQuestionAndStudentIds($questionId, $individuallyAccessibleParticipants, $optionValues);
        }

        $studentIdsForStudentsWhoHaveResponses = array_column($responses, 'person_id');

        // Add in the names of these students.
        $dataToReturn = $this->studentListService->getStudentIdsAndNames($studentIdsForStudentsWhoHaveResponses, $loggedInUserId);

        return $dataToReturn;
    }


    /**
     * Get Survey Responses|Org Question Responses for individual question from the Survey Snapshot Report Main Report and Exporting as CSV
     *
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param int $reportInstanceId
     * @param string $questionSource - 'isq'|'ebi'
     * @param int $questionId
     * @param array $optionValues
     * @param string $questionTypeCode
     * @return array
     */
    public function getReportSurveyValuesForCSV($loggedInUserId, $organizationId, $reportInstanceId, $questionSource, $questionId, $optionValues, $questionTypeCode)
    {

        // Get an array of all the students selected for this report via the preliminary filters.
        $reportInstance = $this->reportsRunningStatusRepository->find($reportInstanceId);
        $filteredStudentIds = explode(',', $reportInstance->getFilteredStudentIds());

        //Get the current org academic year and the survey ID on which the report was run.
        $reportResultArray = json_decode($reportInstance->getResponseJson(), true);
        $surveyId = $reportResultArray['request_json']['search_attributes']['survey_filter']['survey_id'];

        $accessibleStudents = $this->getAccessibleStudentsForQuestion($loggedInUserId, $organizationId, $surveyId, $questionSource, $questionId);
        $studentIds = array_intersect($filteredStudentIds, $accessibleStudents);


        // Prevent the user from viewing profile item values for students if the number of those students is below a threshold.
        if (count($accessibleStudents) == 0) {
            throw new AccessDeniedException('You do not have permission to view data for this profile item and these students.');
        }


        // Get responses for the given question for the list of students we just found.
        if ($questionSource == 'isq') {
            $responses = $this->orgQuestionResponseRepository->getOrgQuestionResponsesByQuestionAndStudentIds($questionId, $studentIds, $optionValues);
        } else {
            $responses = $this->surveyResponseRepository->getSurveyResponsesByQuestionAndStudentIds($questionId, $studentIds, $optionValues);
        }

        // Format the file name and path.
        $filePath = SynapseConstant::S3_ROOT . ReportsConstants::S3_REPORT_CSV_EXPORT_DIRECTORY . '/';
        $fileName = "{$organizationId}-{$loggedInUserId}-{$questionTypeCode}-{$questionId}-snapshot-report.csv";


        $columnHeaders = [
            'response' => 'RESPONSE'
        ];

        $this->csvUtilityService->generateCSV($filePath, $fileName, $responses, $columnHeaders);

        $response = [];
        $response['file_name'] = $fileName;
        return $response;
    }

    /**
     * Get Multiple Questions for surveyId and reportRunningStatusId
     *
     * @param int $surveyId
     * @param int $reportRunningStatusId
     * @param array $ebiQuestionPermissions
     * @param int $personId
     * @return array
     */
    public function getMultiResponseQuestions($surveyId, $reportRunningStatusId, $ebiQuestionPermissions, $personId)
    {
        $reportRunningStatus = $this->reportsRunningStatusRepository->find($reportRunningStatusId);
        $reportStudentIds = $reportRunningStatus->getFilteredStudentIds();
        $reportStudentIds = explode(',', $reportStudentIds);
        $questionsResponse = [];
        $questionOptions = [];

        if (!empty($reportStudentIds)) {
            $organizationId = $reportRunningStatus->getOrganization()->getId();
            $surveyQuestions = $this->orgQuestionResponseRepository->getMultiResponseISQCalculatedResponses($surveyId, $reportStudentIds, $organizationId, array_keys($ebiQuestionPermissions));
            if (!empty($surveyQuestions)) {
                foreach ($surveyQuestions as $surveyQuestion) {
                    $ebiQuestionId = $surveyQuestion['org_question_id'];
                    $questionNumber = $surveyQuestion['org_question_id'];
                    $surveyQuestionId = $questionNumber;
                    $surveySnapshotSectionDto = new SurveySnapshotSectionDto();
                    $questionTypeCode = $surveyQuestion['question_type'];
                    $questionType = 'Multiple Response';
                    $surveySnapshotSectionDto->setQuestionTypeCode('ISQ-' . $questionTypeCode);
                    $surveySnapshotSectionDto->setQuestionType($questionType);
                    $surveySnapshotSectionDto->setQuestionText($surveyQuestion['question_text']);
                    $surveySnapshotSectionDto->setSurveyQuestionId($surveyQuestionId);
                    $standardDeviation = number_format($surveyQuestion['standard_deviation'], 2);
                    $surveySnapshotSectionDto->setStdDeviation($standardDeviation);
                    $surveySnapshotSectionDto->setQuestionQnbr($questionNumber);
                    $reportStudentIds = $ebiQuestionPermissions[$ebiQuestionId];
                    $totalStudents = count($reportStudentIds);
                    $surveySnapshotSectionDto->setTotalStudents($totalStudents);
                    $surveyResponses = $this->orgQuestionResponseRepository->getMultiResponseISQResponses($surveyId, $organizationId, $reportStudentIds, $ebiQuestionId);
                    $branchDetails = $this->surveyBranchRepository->getQuestionBranchDetails($surveyId, $surveyQuestionId);
                    $branching = array();
                    if (!empty($branchDetails)) {
                        foreach ($branchDetails as $branchDetail) {
                            $branches = array();
                            $branches['option_text'] = $branchDetail['option_text'];
                            $branches['source_qtype'] = $branchDetail['type'];
                            $branches['source_qnbr'] = $branchDetail['qnbr'];
                            $branches['source_question_text'] = $branchDetail['question_text'];
                            $branching[] = $branches;
                        }
                    }
                    $response = [];
                    if (!empty($surveyResponses)) {
                        $totalResponded = $this->orgQuestionResponseRepository->getMultiResponseISQResponseCount($surveyId, $organizationId, $reportStudentIds, $ebiQuestionId);
                        $totalResponded = ($totalResponded[0]['student_count']) ? $totalResponded[0]['student_count'] : 0;
                        $surveySnapshotSectionDto->setTotalStudentsResponded($totalResponded);
                        $totalStudentPercentage = number_format(($totalResponded * 100) / $totalStudents, 1);
                        $surveySnapshotSectionDto->setRespondedPercentage($totalStudentPercentage);

                        foreach ($surveyResponses as $surveyResponse) {
                            $surveySnapshotSectionResponseDto = new SurveySnapshotSectionResponseDto();
                            $surveySnapshotSectionResponseDto->setResponseText($surveyResponse['option_text']);
                            $optionResponded = $surveyResponse['student_count'];
                            $optionRespondedPercent = number_format(($optionResponded * 100) / $totalResponded, 1);
                            $surveySnapshotSectionResponseDto->setResponsePercentage($optionRespondedPercent);
                            $studentResponse = $surveyResponse['option_value'];
                            $surveySnapshotSectionResponseDto->setOptionValue($studentResponse);
                            $surveySnapshotSectionResponseDto->setNoResponded($optionResponded);
                            $surveySnapshotSectionResponseDto->setOptionId($surveyResponse['option_id']);
                            $response[] = $surveySnapshotSectionResponseDto;
                        }
                    }
                    $result = $response;
                    $surveySnapshotSectionDto->setBranchDetails($branching);
                    $surveySnapshotSectionDto->setResponseOptions($result);
                    $questionsResponse['ISQ-' . $questionNumber] = $surveySnapshotSectionDto;
                    $questionOptions[$questionType][$questionNumber] = $surveySnapshotSectionDto;
                }
            }
        }
        // store category and scaled questions in table
        $this->saveReportRunDetails('Multiple Response', $questionOptions, $reportRunningStatus, $personId);
        $this->reportsRunningStatusRepository->flush();

        return $questionsResponse;
    }

}
