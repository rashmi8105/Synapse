<?php

namespace Synapse\ReportsBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\OrganizationlangService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Service\Impl\JobService;
use Synapse\ReportsBundle\EntityDto\AcademicUpdateReportDto;
use Synapse\ReportsBundle\Job\AcademicUpdateReportCSVJob;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\SearchBundle\EntityDto\SearchDto;
use Synapse\SearchBundle\EntityDto\SearchResultListDto;
use Synapse\SearchBundle\Repository\OrgSearchRepository;
use Synapse\SearchBundle\Service\Impl\SearchService;


/**
 * @DI\Service("academic_update_report_service")
 */
class AcademicUpdateReportService extends AbstractService
{
    const SERVICE_KEY = 'academic_update_report_service';

    const CSV_NOTIFICATION_KEY = "academic_update_report";
    const CSV_NOTIFICATION_TEXT = "Your Academic Update report download has completed.";

    //scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Resque
     */
    private $resque;


    //services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationService;

    /**
     * @var CSVUtilityService
     */
    private $CSVUtilityService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var JobService
     */
    private $jobService;

    /**
     * @var OrganizationlangService
     */
    private $organizationLangService;

    /**
     * @var SearchService
     */
    private $searchService;


    //Repository

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgSearchRepository
     */
    private $orgSearchRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var ReportsRunningStatusRepository
     */
    private $reportsRunningStatusRepository;

    /**
     * @var ReportsRepository
     */
    private $reportsRepository;

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

        $this->container = $container;
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        //service
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->alertNotificationService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->CSVUtilityService = $this->container->get(CSVUtilityService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->jobService = $this->container->get(JobService::SERVICE_KEY);
        $this->organizationLangService = $this->container->get(OrganizationlangService::SERVICE_KEY);
        $this->searchService = $this->container->get(SearchService::SERVICE_KEY);

        //repositories
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgSearchRepository = $this->repositoryResolver->getRepository(OrgSearchRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->reportsRepository = $this->repositoryResolver->getRepository(ReportsRepository::REPOSITORY_KEY);
        $this->reportsRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsRunningStatusRepository::REPOSITORY_KEY);
    }

    /**
     * Initiating job for csv generation
     *
     * @param array $searchAttributes
     * @param string $selectedAttributesCSV
     * @param array $academicUpdatesSearchAttributes
     * @param integer $organizationId
     * @param integer $loggedUserId
     * @param integer $currentAcademicYearId
     */
    private function initiateAcademicUpdateReportCSVJob($searchAttributes, $selectedAttributesCSV, $academicUpdatesSearchAttributes, $organizationId, $loggedUserId, $currentAcademicYearId)
    {
        $academicUpdateReportCSVJob = new AcademicUpdateReportCSVJob();
        $jobNumber = uniqid();

        $academicUpdateReportCSVJob->args = array(
            'searchAttributes' => $searchAttributes,
            'selectedAttributesCSV' => $selectedAttributesCSV,
            'academicUpdatesSearchAttributes' => $academicUpdatesSearchAttributes,
            'organizationId' => $organizationId,
            'loggedInUserId' => $loggedUserId,
            'currentAcademicYear' => $currentAcademicYearId,
            'jobNumber' => $jobNumber,

        );

        $this->jobService->addJobToQueue($organizationId, 'AcademicUpdateReportCSVJob', $academicUpdateReportCSVJob, $loggedUserId);
    }

    /**
     * Get the search attributes and academicUpdate search attributes from the dto
     *
     * @param SaveSearchDto $saveSearchDto
     * @param string $dataToReturn
     * @return array|null
     */
    private function getSearchAndAcademicUpdateAttributes(SaveSearchDto $saveSearchDto, $dataToReturn = "search_attributes")
    {

        $searchAttributes = $saveSearchDto->getSearchAttributes();
        unset($searchAttributes['participating']);
        //Check if there are academic update filters. If there are, remove them, so the report is just run looking at academic_update.
        //Including academic record (which is what the custom search filters run on) will limit this report to only
        //academic updates where the academic update is the exact same as the academic record for those filter criteria.
        //The academic update filter criteria is assigned to a new variable, and only used to filter down the base report query.
        //Please see the comments on ESPRJ-16354 for the expected behavior of the report filter on academic updates.
        //This filter ONLY behaves this way in the case of the academic update report. All other filtering on academic
        //  updates should always consider the academic record instead of the individual academic updates.
        if ($searchAttributes['academic_updates']) {
            $academicUpdatesSearchAttributes = $searchAttributes['academic_updates'];
            unset($searchAttributes['academic_updates']);
        } else {
            $academicUpdatesSearchAttributes = null;
        }
        if ($dataToReturn == "search_attributes") {
            return $searchAttributes;
        } else {
            return $academicUpdatesSearchAttributes;
        }
    }


    /**
     * Get thh student list or array if academic update ids based on arguments passed
     *
     * @param array $searchAttributes
     * @param array $academicUpdatesSearchAttributes
     * @param integer $organizationId
     * @param integer $loggedInUserId
     * @param integer $currentAcademicYearId
     * @param string $sortByQueryString
     * @param string $limit
     * @param bool $studentListFlag
     * @param bool $isCount
     * @param bool $participationFlag
     * @return array
     */
    private function getAcademicUpdateIdsOrStudentList($searchAttributes, $academicUpdatesSearchAttributes, $organizationId, $loggedInUserId, $currentAcademicYearId, $sortByQueryString = '', $limit = '', $studentListFlag = false, $isCount = false, $participationFlag = true)
    {

        $studentSelectSQL = $this->searchService->getStudentListBasedCriteria($searchAttributes, $organizationId, $loggedInUserId, 'auReport');
        // Academic Update Filter Criteria based on the search attributes.
        $academicUpdateFilterCriteria = $this->searchService->getFilterCriteriaForAcademicUpdate($academicUpdatesSearchAttributes);
        $result = $this->reportsRepository->getAllAcademicUpdateReportInformationBasedOnCriteria($organizationId, $currentAcademicYearId, $studentSelectSQL, $academicUpdateFilterCriteria, $sortByQueryString, $limit, $studentListFlag, $isCount, $participationFlag);
        return $result;
    }

    /**
     * Called from the controller to generate the academic update report CSV
     *
     * @param SaveSearchDto $saveSearchDto
     * @param integer $loggedInUserId
     * @return string
     */
    public function createAcademicUpdateCSV($saveSearchDto, $loggedInUserId)
    {

        $searchAttributes = $this->getSearchAndAcademicUpdateAttributes($saveSearchDto);
        $academicUpdatesSearchAttributes = $this->getSearchAndAcademicUpdateAttributes($saveSearchDto, 'academic_updates');
        $organizationId = $saveSearchDto->getOrganizationId();
        $currentAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
        $selectedAttributesCSV = $saveSearchDto->getSelectedAttributesCsv();
        $this->initiateAcademicUpdateReportCSVJob($searchAttributes, $selectedAttributesCSV, $academicUpdatesSearchAttributes, $organizationId, $loggedInUserId, $currentAcademicYearId);
        return SynapseConstant::DOWNLOAD_IN_PROGRESS_MESSAGE;
    }

    /**
     * Gets the list of Students in the academic update report
     *
     * @param SaveSearchDto $saveSearchDto
     * @param integer $loggedInUserId
     * @return SearchDto
     */
    public function getStudentsForAcademicUpdateReport($saveSearchDto, $loggedInUserId)
    {

        $searchAttributes = $this->getSearchAndAcademicUpdateAttributes($saveSearchDto);
        $academicUpdatesSearchAttributes = $this->getSearchAndAcademicUpdateAttributes($saveSearchDto, 'academic_updates');
        $organizationId = $saveSearchDto->getOrganizationId();
        $currentAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
        $studentList = $this->getAcademicUpdateIdsOrStudentList($searchAttributes, $academicUpdatesSearchAttributes, $organizationId, $loggedInUserId, $currentAcademicYearId, '', '', true);

        $studentListDto = new SearchDto();
        $studentListDto->setPersonId($loggedInUserId);
        $studentArray = [];
        foreach ($studentList as $student) {
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
     * Generating runtime paginated Academic Update Report
     *
     * @param SaveSearchDto $saveSearchDto
     * @param integer $loggedUserId
     * @param integer $pageNumber
     * @param integer $recordsPerPage
     * @param string $sortByFieldString
     * @param \DateTime $currentDate
     * @return array
     */
    public function generateReport(SaveSearchDto $saveSearchDto, $loggedUserId, $pageNumber, $recordsPerPage, $sortByFieldString, $currentDate)
    {

        $organizationId = $saveSearchDto->getOrganizationId();
        $organizationTimeZone = $this->dateUtilityService->getOrganizationISOTimeZone($organizationId);
        $searchAttributes = $this->getSearchAndAcademicUpdateAttributes($saveSearchDto);
        $academicUpdatesSearchAttributes = $this->getSearchAndAcademicUpdateAttributes($saveSearchDto, 'academic_updates');

        // Get Current Academic Year Id.
        $currentAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
        $pageNumber = (int)$pageNumber;
        if (!$pageNumber) {
            $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }
        $recordsPerPage = (int)$recordsPerPage;
        if (!$recordsPerPage) {
            $recordsPerPage = SynapseConstant::DEFAULT_RECORD_COUNT;
        }

        $startPoint = ($pageNumber * $recordsPerPage) - $recordsPerPage;
        $calculateLimit = " LIMIT $startPoint , $recordsPerPage ";


        $studentSelectSQL = $this->searchService->getStudentListBasedCriteria($searchAttributes, $organizationId, $loggedUserId, 'auReport');
        $academicUpdateFilterCriteria = $this->searchService->getFilterCriteriaForAcademicUpdate($academicUpdatesSearchAttributes);

        // Total academic update count
        $resultCount = $this->reportsRepository->getAllAcademicUpdateReportInformationBasedOnCriteria($organizationId, $currentAcademicYearId, $studentSelectSQL, $academicUpdateFilterCriteria, '', '', false, true, true);
        $totalCount = $resultCount[0]['total_count'];

        // Total count of students in the report
        $studentsInReport = $this->reportsRepository->getAllAcademicUpdateReportInformationBasedOnCriteria($organizationId, $currentAcademicYearId, $studentSelectSQL, $academicUpdateFilterCriteria, '', '', true, true, false);
        $totalStudentCount = count($studentsInReport);
        unset($studentsInReport);

        // Total participating students count
        $reportParticipatingStudentCountArray = $this->reportsRepository->getAllAcademicUpdateReportInformationBasedOnCriteria($organizationId, $currentAcademicYearId, $studentSelectSQL, $academicUpdateFilterCriteria, '', '', true);
        $participantStudentCount = count($reportParticipatingStudentCountArray);
        unset($reportParticipatingStudentCountArray);

        $nonParticipantStudentCount = $totalStudentCount - $participantStudentCount;

        $allAcademicUpdateIds = $this->reportsRepository->getAllAcademicUpdateReportInformationBasedOnCriteria($organizationId, $currentAcademicYearId, $studentSelectSQL, $academicUpdateFilterCriteria, $sortByFieldString, $calculateLimit);
        $academicUpdateReportData = $this->reportsRepository->getAllAcademicUpdateReportForListedAcademicUpdateIds($allAcademicUpdateIds, $currentAcademicYearId, true, $organizationTimeZone);

        $academicUpdateReportData = $this->updateReportDataWithPermissionToViewRisk($academicUpdateReportData, $loggedUserId);
        $academicUpdateReportResponse = [];

        $academicUpdateDtoArray = $this->createAcademicUpdateDtoArray($academicUpdateReportData);

        $totalPageCount = ceil($totalCount / $recordsPerPage);
        $academicUpdateReportResponse['non_participant_count'] = $nonParticipantStudentCount;
        $academicUpdateReportResponse['total_records'] = $totalCount;
        $academicUpdateReportResponse['total_pages'] = $totalPageCount;
        $academicUpdateReportResponse['records_per_page'] = $recordsPerPage;
        $academicUpdateReportResponse['current_page'] = $pageNumber;

        $reportSections = [];
        $reportSections['reportId'] = $saveSearchDto->getReportSections()['reportId'];
        $reportSections['report_name'] = $saveSearchDto->getReportSections()['report_name'];
        $reportSections['short_code'] = $saveSearchDto->getReportSections()['short_code'];
        $academicUpdateReportResponse['report_sections'] = $reportSections;

        // report info section added

        $personObject = $this->personRepository->find($loggedUserId);

        $startDate = $saveSearchDto->getSearchAttributes()['org_academic_year']['year']['start_date'];
        $endDate = $saveSearchDto->getSearchAttributes()['org_academic_year']['year']['end_date'];

        $reportInfo = array(
            'report_id' => $saveSearchDto->getReportSections()['report_id'],
            'report_name' => $saveSearchDto->getReportSections()['report_name'],
            'short_code' => $saveSearchDto->getReportSections()['short_code'],
            'report_date' => $currentDate->format(SynapseConstant::DATE_FORMAT_WITH_TIMEZONE),
            'report_start_date' => $startDate,
            'report_end_date' => $endDate,
            'report_by' => array(
                'first_name' => $personObject->getFirstname(),
                'last_name' => $personObject->getLastname()
            )
        );

        $academicUpdateReportResponse['report_info'] = $reportInfo;
        $academicUpdateReportResponse['search_attributes'] = $saveSearchDto->getSearchAttributes();
        $academicUpdateReportResponse['report_data'] = $academicUpdateDtoArray;
        return $academicUpdateReportResponse;
    }

    /**
     * Update ReportData with permission for the faculty to view risk for the students
     *
     * @param array $reportData
     * @param integer $loggedUserId
     * @return array
     */
    private function updateReportDataWithPermissionToViewRisk($reportData, $loggedUserId)
    {
        if (count($reportData) > 0) {
            $studentArray = [];
            foreach ($reportData as $student) {
                $studentArray[] = $student['student_id'];
            }
            $studentListAsString = implode(",", $studentArray);
            $riskViewPermissionArray = $this->orgSearchRepository->getRiskIntentData($loggedUserId, $studentListAsString);

            $riskArray = [];
            foreach ($riskViewPermissionArray as $riskViewPermission) {
                if ($riskViewPermission['risk_flag'] == 1) {
                    $riskArray[$riskViewPermission['student_id']] = $riskViewPermission['risk_flag'];
                }
            }
        }

        $updateReportData = [];
        foreach ($reportData as $reportItem) {

            if (isset($riskArray[$reportItem['student_id']])) {
                $reportItem['risk_flag'] = 1;
            } else {
                $reportItem['risk_flag'] = 0;
            }
            $riskText = ($reportItem['risk_flag'] && isset($reportItem['risk_text'])) ? $reportItem['risk_text'] : "";
            $riskImageName = ($reportItem['risk_flag'] && isset($reportItem['risk_imagename'])) ? $reportItem['risk_imagename'] : "";
            $reportItem['risk_text'] = $riskText;
            $reportItem['risk_imagename'] = $riskImageName;
            $updateReportData[] = $reportItem;
        }
        return $updateReportData;
    }

    /**
     * Generating CSV for the academic update report
     *
     * @param array $searchAttributes
     * @param string $selectedAttributeCSV
     * @param array $academicUpdatesSearchAttributes
     * @param integer $organizationId
     * @param integer $loggedUserId
     * @param integer $currentAcademicYearId
     * @return array
     */
    public function generateReportCSV($searchAttributes, $selectedAttributeCSV, $academicUpdatesSearchAttributes, $organizationId, $loggedUserId, $currentAcademicYearId)
    {

        $headers = [
            'external_id' => 'Student Id',
            'student_first_name' => 'Student First Name',
            'student_last_name' => 'Student Last Name',
            'email' => 'Email',
            'course_name' => 'Course Name',
            'faculty_first_name' => 'Faculty First Name',
            'faculty_last_name' => 'Faculty Last Name',
            'created_at' => 'Created',
            'failure_risk' => 'Failure Risk',
            'inprogress_grade' => 'Inprogress Grade',
            'absences' => 'Absences',
            'comment' => 'Comment',
            'class_level' => 'Class Level',
            'student_status' => 'Student Status',
            'risk_text' => 'Student Risk Level',
            'term_name' => 'Academic Term',
            'update_type' => 'Input Method'
        ];


        $filePath = SynapseConstant::S3_ROOT . "report_downloads/";
        $fileName = $organizationId . "-academic-update-" . time() . ".csv";
        $filePathForNotification = "report_downloads/" . $fileName;

        $preliminaryRows = [
            ['Search Attributes->', $selectedAttributeCSV]
        ];


        $allAcademicUpdateIds = $this->getAcademicUpdateIdsOrStudentList($searchAttributes, $academicUpdatesSearchAttributes, $organizationId, $loggedUserId, $currentAcademicYearId, 'default', '');

        $organizationTimeZone = $this->dateUtilityService->getOrganizationISOTimeZone($organizationId);
        $chunkedAllAcademicUpdateIds = array_chunk($allAcademicUpdateIds, 10000); // process 10000 record in one go, processing all the records at a time has memory issues.

        $tempFileName = uniqid($fileName);
        $CSVWriter = $this->CSVUtilityService->createCSVFileInTempFolder($tempFileName);
        $this->CSVUtilityService->writeToFile($CSVWriter, $preliminaryRows);
        $this->CSVUtilityService->writeToFile($CSVWriter, $headers, true);

        foreach ($chunkedAllAcademicUpdateIds as $academicUpdateIds) {

            $academicUpdateReportDataInChunks = $this->reportsRepository->getAllAcademicUpdateReportForListedAcademicUpdateIds($academicUpdateIds, $currentAcademicYearId, true, $organizationTimeZone);
            $academicUpdateReportDataInChunks = $this->updateReportDataWithPermissionToViewRisk($academicUpdateReportDataInChunks, $loggedUserId);
            $rowsToWrite = $this->CSVUtilityService->getRowsToWrite($academicUpdateReportDataInChunks, $headers);
            $this->CSVUtilityService->writeToFile($CSVWriter, $rowsToWrite);
            unset($academicUpdateReportDataInChunks);
        }

        $this->CSVUtilityService->copyFileToDirectory($tempFileName, $filePath, $fileName);
        $CSVFileNameArray = ['file_name' => $fileName];

        $this->alertNotificationService->createCSVDownloadNotification(self::CSV_NOTIFICATION_KEY, self::CSV_NOTIFICATION_TEXT, $filePathForNotification, $loggedUserId);
        return $CSVFileNameArray;
    }


    /**
     * Covert academic update report Data to array of AcademicUpdateReport Dto
     *
     * @param array $academicUpdateReportData
     * @return AcademicUpdateReportDto[]
     */
    private function createAcademicUpdateDtoArray($academicUpdateReportData)
    {

        $reportDataList = [];
        foreach ($academicUpdateReportData as $reportItem) {

            $reportItemDto = new AcademicUpdateReportDto();
            $reportItemDto->setStudentId($reportItem['student_id']);
            $reportItemDto->setStudentFirstName($reportItem['student_first_name']);
            $reportItemDto->setStudentLastName($reportItem['student_last_name']);

            $reportItemDto->setFacultyFirstName($reportItem['faculty_first_name']);
            $reportItemDto->setFacultyLastName($reportItem['faculty_last_name']);
            $reportItemDto->setAcademicUpdateId($reportItem['academic_update_id']);
            $reportItemDto->setCourseName($reportItem['course_name']);
            if (!empty($reportItem['created_at'])) {
                $createdAt = new \DateTime($reportItem['created_at']);
                $reportItemDto->setCreatedAt($createdAt);
            } else {
                $reportItemDto->setCreatedAt(null);
            }
            $reportItemDto->setByRequest($reportItem['by_request']);
            $reportItemDto->setFailureRisk($reportItem['failure_risk']);
            $reportItemDto->setInprogressGrade($reportItem['inprogress_grade']);
            $reportItemDto->setAbsences($reportItem['absences'] ? $reportItem['absences'] : '');

            // Student Status
            $studentStatus = (isset($reportItem['student_status'])) ? $reportItem['student_status'] : 1;
            $reportItemDto->setStudentIsActive($studentStatus);

            //Student Risk Level and image Name
            $riskText = (!empty($reportItem['risk_text'])) ? $reportItem['risk_text'] : "gray";
            $reportItemDto->setRiskText($riskText);

            $riskImageName = (!empty($reportItem['risk_imagename'])) ? $reportItem['risk_imagename'] : "risk-level-icon-gray.png";
            $reportItemDto->setRiskImageName($riskImageName);
            //Course name as concatenation of subject code and course number
            $courseName = (isset($reportItem['course_name'])) ? $reportItem['course_name'] : "";
            $reportItemDto->setCourseName($courseName);
            //Academic Update Comments
            $comment = (isset($reportItem['comment'])) ? $reportItem['comment'] : "";
            $reportItemDto->setComment($comment);
            //Academic Term Details
            $termName = (isset($reportItem['term_name'])) ? $reportItem['term_name'] : "";
            $reportItemDto->setTermId($reportItem['term_id']);
            $reportItemDto->setTermName($termName);

            $reportDataList[] = $reportItemDto;
        }
        return $reportDataList;
    }
}