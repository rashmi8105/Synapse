<?php


namespace Synapse\ReportsBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\ReportsBundle\Entity\ReportsRunningStatus;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Symfony\Component\DependencyInjection\Container;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("gpa_report_service")
 */
class GPAReportService extends AbstractService
{

    const SERVICE_KEY = 'gpa_report_service';

    private $reportsRunningStatusRepository;

    private $personRiskLevelHistoryRepository;

    private $personEbiMetadataRepository;

    private $ebiMetadataRepository;

    private $resque;

    private $serializer;
    /**
     * @var Container
     */
    private $container;

    private $dateUtilityService;





    /**
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     * @param $resque
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container"),
     *            "resque" = @DI\Inject("bcc_resque.resque")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container, $resque)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->resque = $resque;

        $this->dateUtilityService = $this->container->get('date_utility_service');
    }

    /**
     * initiates GPAReportServiceJob.
     * static function, not Unit Testable
     *
     * @param int $reportRunningStatusId
     * @param object $reportRunningDto
     */
    public function initiateGPAReportServiceJob($reportInstanceId, $reportRunningDto)
    {

        $jobObj = 'Synapse\ReportsBundle\Job\ReportJob';

        $job = new $jobObj();

        $reportService = 'gpa_report_service';

        $job->args = array(
            'reportInstanceId' => $reportInstanceId,
            'reportRunningDto' => serialize($reportRunningDto),
            'service' => $reportService
        );
        $this->resque->enqueue($job, true);
    }

    /**
     * Retrieves and organizes all data needed for the GPA Report.
     * Inserts the resulting JSON into the reports_running_status table in the database.
     * ALL Steps tested, Not Unit Testable
     *
     *
     * @param int $reportRunningStatusId
     * @param ReportRunningStatusDto $reportRunningDto
     */
    public function generateReport($reportRunningStatusId, $reportRunningDto)
    {
        //Finding initialized Report and Updating with generated report
        $this->reportsRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_RUNNING_STATUS_REPO);
        $reportsRunningStatus = $this->reportsRunningStatusRepository->find($reportRunningStatusId);

        //Setting Current Report Date for Report Info
        $currentDateTime = date('Y-m-d\TH:i:sO');

        //Run Report
        $this->updateReportRunningStatusWithGPAReportJSON($reportRunningStatusId, $reportRunningDto, $reportsRunningStatus, $currentDateTime);
        $this->reportsRunningStatusRepository->flush();

        $alertNotificationsService = $this->container->get('alertNotifications_service');
        $alertNotificationsService->createReportNotification($reportsRunningStatus);

    }

    /**
     * Returns ReportRunningStatusDto (withJSON) object ready for update to DB
     * UNIT TESTED
     *
     * @param INT $reportRunningStatusId
     * @param ReportRunningStatusDto $reportRunningDto
     * @param ReportsRunningStatus $reportsRunningStatus
     * @param string $currentDateTime
     * @return mixed
     */
    public function updateReportRunningStatusWithGPAReportJSON($reportRunningStatusId, $reportRunningDto, $reportsRunningStatus, $currentDateTime)
    {
        $statusMessage = [];
        //Find EndTermGPA id (default key used)
        $gpaId = $this->getEndTermGpaId();

        //get orgId
        $orgId = $reportRunningDto->getOrganizationId();

        //Retrieving Mandatory Filters
        $searchAttributes = $reportRunningDto->getSearchAttributes();
        $orgAcademicYearIds = $searchAttributes['org_academic_year_id'];
        $orgAcademicTermIds = $searchAttributes['org_academic_terms_id'];
        $riskCalculationStart = $searchAttributes['risk_start_date'];
        $riskCalculationEnd = $searchAttributes['risk_end_date'];

        $mandatoryFilters = array($orgAcademicYearIds, $orgAcademicTermIds, $riskCalculationStart, $riskCalculationEnd, $orgId, $gpaId);

        if (!$this->areMandatoryFiltersSet($mandatoryFilters)) {
            $this->logger->addError("There was a validation error: this report is missing a mandatory filter.");
            throw new ValidationException(array('Sorry, something went wrong. Please try to run the report again. If this problem persists, please contact support@map-works.com'), "There was a validation error: this report is missing a mandatory filter.", "validation_errors", 400);
        }

        //Using Optional Filters
        // Get an array of all the students selected for this report via the optional filters.
        $studentList = $this->getFilteredStudentIds($reportsRunningStatus);
        
        //Send User Message if Optional Filter returns 0 students
        if (empty($studentList)) {
            $statusMessage['code'] = ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_CODE;
            $statusMessage['description'] = ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_MESSAGE;
            $structuredReport['total_student_count'] = 0;
            $this->logger->addWarning(json_encode($statusMessage));
        } else {

            //convert to UTC datetime
            $riskCalculationStartUTC = $this->dateUtilityService->convertToUtcDatetime($orgId, $riskCalculationStart);
            $riskCalculationEndUTC = $this->dateUtilityService->convertToUtcDatetime($orgId, $riskCalculationEnd, true);//true for enddate

            //run report
            $structuredReport = $this->buildReportItems($orgId, $gpaId, $studentList, $orgAcademicYearIds, $orgAcademicTermIds, $riskCalculationStartUTC, $riskCalculationEndUTC);

            if(empty($structuredReport)) {
                $statusMessage['code'] = ReportsConstants::REPORT_NO_DATA_CODE;
                $statusMessage['description'] = ReportsConstants::NO_GPA_SCORES_MESSAGE;
                $this->logger->addWarning(json_encode($statusMessage));
            }

            //Calculating Number of Students from Optional Filters if not 0
            $studentCount = count($studentList);
            $structuredReport['total_student_count'] = strval($studentCount);

        }

        //Assemble JSON Report Items

        $responseJSON = $this->assembleJSON($structuredReport, $reportRunningDto, $reportRunningStatusId, $statusMessage, $currentDateTime);
        $reportsRunningStatus->setStatus('C');
        $reportsRunningStatus->setResponseJson($responseJSON);

        return $reportsRunningStatus;
    }

    /**
     * builds and returns ReportItems Array with all report data formatted
     * UNIT TESTED
     *
     * @param int $orgId
     * @param int $gpaId
     * @param array $filteredStudentIds
     * @param array $orgAcademicYearIds
     * @param array $orgAcademicTermIds
     * @param string $riskCalculationStart
     * @param string $riskCalculationEnd
     * @return array $reportItems
     */
    public function buildReportItems($orgId, $gpaId, $filteredStudentIds, $orgAcademicYearIds, $orgAcademicTermIds, $riskCalculationStart, $riskCalculationEnd)
    {

        $reportItems = [];

        //guarantee no duplicate Years Or Terms
        $orgAcademicYearIds = array_unique($orgAcademicYearIds);
        $orgAcademicTermIds = array_unique($orgAcademicTermIds);

        //Generate Individual Scores
        $individualGpaScores = $this->retrieveGpaData($gpaId, $filteredStudentIds, $orgAcademicYearIds, $orgAcademicTermIds);


        //If no data exists from Report Process, return empty array()
        if(!empty($individualGpaScores)){

            //Calculate Report
            $meanGpaAndPercentUnder2 = $this->getMeanGPAandPercentUnder2($individualGpaScores, $orgAcademicYearIds, $orgAcademicTermIds);
            $riskWithGpaByOrg = $this->retrieveGpaByRiskData($orgId, $riskCalculationStart, $riskCalculationEnd, $orgAcademicYearIds, $orgAcademicTermIds, $gpaId, $filteredStudentIds);

            //Structuring Report for JSON
            $structuredReport = $this->formatReportDataForJSON($meanGpaAndPercentUnder2, $riskWithGpaByOrg);
            $reportItems['organization_id'] = strval($orgId);
            $reportItems['gpa_term_summaries_by_year'] = $structuredReport;

        }
        return $reportItems;
    }

    /**
     * Cycles Through List of Filters and checks if they are empty
     * UNIT TESTED
     *
     * @param array $mandatoryFilters
     * @return boolean
     */
    public function areMandatoryFiltersSet($mandatoryFilters)
    {
        foreach ($mandatoryFilters as $filter) {
            if (empty($filter)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns EndTermGPA, default key 'EndTermGPA'
     * Not Unit Testable Only Repository Return
     *
     * @param array $endTermGpaKey
     * @return int $gpaId
     */
    public function getEndTermGpaId($endTermGpaKey = 'EndTermGPA')
    {
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:EbiMetadata');
        $ebiMetadataGPA = $this->ebiMetadataRepository->findOneBy(['key' => $endTermGpaKey]);
        $gpaId = $ebiMetadataGPA->getId();

        return $gpaId;
    }

    /**
     * Returns list of students filtered by criteria
     * Transforms string to array
     * UNIT TESTED
     *
     * @param object $reportsRunningStatus
     * @return array $studentFilter
     */
    public function getFilteredStudentIds($reportsRunningStatus)
    {
        $studentListAsString = $reportsRunningStatus->getFilteredStudentIds();

        //changing returned string into array
        $studentArrayWithString = explode(",", $studentListAsString);

        //Make Array empty if string was empty, filters out arrays with empty
        $studentArrayWithOutSingleElementEmptyStrings = array_filter($studentArrayWithString);
        $studentFilter = array_map('intval', $studentArrayWithOutSingleElementEmptyStrings);
        return $studentFilter;
    }

    /**
     * Gets Mean GPA and percentage of students with GPA under 2.00
     * All steps Tested, not Unit Testable
     *
     * @param int $gpaId
     * @param array $studentFilter
     * @param array $orgAcademicYearIds
     * @param array $orgAcademicTermIds
     * @param array $riskWithGPAbyOrg
     *
     * @return array $meanGPAAndPercentUnder2
     */
    public function retrieveGpaData($gpaId, $studentFilter, $orgAcademicYearIds, $orgAcademicTermIds)
    {
        $this->personEbiMetadataRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:PersonEbiMetadata');
        $individualGpaScores = $this->personEbiMetadataRepository->getMetadataValuesByEbiMetadataAndStudentIds($gpaId, $studentFilter, $orgAcademicYearIds, $orgAcademicTermIds);
        return $individualGpaScores;
    }


    /**
     * Gets Risk Level related to Mean GPA from Person Risk Level Repository
     * All steps Tested, not Unit Testable
     *
     * @param int $orgId
     * @param string $riskCalculationStart
     * @param string $riskCalculationEnd
     * @param array $orgAcademicYearIds
     * @param array $orgAcademicTermIds
     * @param int $gpaId
     * @param array $studentFilter
     *
     * @return array $riskWithGPAbyOrg
     */
    public function retrieveGpaByRiskData($orgId, $riskCalculationStart, $riskCalculationEnd, $orgAcademicYearIds, $orgAcademicTermIds, $gpaId, $studentFilter)
    {
        $this->personRiskLevelHistoryRepository = $this->repositoryResolver->getRepository('SynapseRiskBundle:PersonRiskLevelHistory');
        $riskWithGpaByOrg = $this->personRiskLevelHistoryRepository->getRiskLevelAndMeanEbiMetadataByOrgYearTerm($orgId, $riskCalculationStart, $riskCalculationEnd, $orgAcademicYearIds, $orgAcademicTermIds, $gpaId, $studentFilter);
        return $riskWithGpaByOrg;
    }

    /**
     * Combines, format, converts report information into JSON structure
     * converts ids to names where appropriate
     * All steps Tested, not Unit Testable
     *
     * Returns Combined sections GPA and GPAbyRisk
     * @param array $meanGpaAndPercentUnder2
     * @param array $riskWithGpaByOrg
     *
     * @return array $combinedSectionsByYearTerm
     */
    public function formatReportDataForJSON($meanGpaAndPercentUnder2, $riskWithGpaByOrg)
    {
        //Converts Risk Level to Color for JSON
        $riskLevelRepo = $this->repositoryResolver->getRepository('SynapseRiskBundle:RiskLevels');
        $riskWithGpaByOrgWithRiskName = array();

        foreach($riskWithGpaByOrg as $riskEntry){
            $replaceLevelWithColor = $riskEntry;
            $riskLevel = $riskEntry['risk_level'];
            $riskColor = $riskLevelRepo->findOneBy(['id' => $riskLevel]);
            $replaceLevelWithColor['risk_level'] = $riskColor->getRiskText();

            $riskWithGpaByOrgWithRiskName[] = $replaceLevelWithColor;
        }

        //Converts Ids to Names and collates information by Year instead of Section for JSON response
        $combinedSectionsByYearTerm = $this->collateByYearTermAndReplaceId($meanGpaAndPercentUnder2, $riskWithGpaByOrgWithRiskName);
        return $combinedSectionsByYearTerm;
    }

    /**
     * Assigns all constructed information into final JSON format and Serializes into JSON
     * Returns JSON object
     * UNIT TESTED
     *
     * @param array $structuredReport
     * @param object $reportRunningDto
     * @param int $reportsRunningStatusId
     * @param array $statusMessage
     * @param string $currentDateTime
     * @return array $reportJSON
     */
    public function assembleJSON($structuredReport, $reportRunningDto, $reportRunningStatusId, $statusMessage, $currentDateTime)
    {

        $reportInfo = $this->buildReportInfoMetadata($reportRunningDto, $currentDateTime);
        $this->serializer = $this->container->get('jms_serializer');

        // Assemble the final JSON for the report.
        $reportData = [];
        $reportData['request_json'] = $reportRunningDto;
        $reportData['report_instance_id'] = strval($reportRunningStatusId);
        $reportData['report_info'] = $reportInfo;
        if(!empty($statusMessage)){
            $reportData['status_message'] = $statusMessage;
        }

        $reportData['report_items'] = $structuredReport;

        $reportJSON = $this->serializer->serialize($reportData, 'json');

        return $reportJSON;
    }

    /**
     * builds report info section
     *
     * @param object $reportRunningDto
     * @param string $currentDateTime
     * @return string $utcDateTimeAsString
     */
    public function buildReportInfoMetadata($reportRunningDto, $currentDateTime){
        $reportSections = $reportRunningDto->getReportSections();

        $personId = $reportRunningDto->getPersonId();
        $personService = $this->container->get('person_service');
        $person = $personService->find($personId);

        $reportInfo = array(
            'report_id' => strval($reportSections['reportId']),
            'report_name' => $reportSections['report_name'],
            'short_code' => $reportSections['short_code'],
            'report_instance_id' => strval($reportRunningDto->getId()),
            'report_date' => $currentDateTime,
            'report_by' => array(
                'first_name' => $person->getFirstname(),
                'last_name' =>  $person->getLastname()
            )
        );

        return $reportInfo;
    }

    /**
     * Returns array with Mean GPA And % of Students under 2.00 GPA per year/term
     * UNIT TESTED
     *
     * @param array $orgStudentsWithGPA
     * @param array $yearFilter
     * @param array $termFilter
     * @return array
     */
    public function getMeanGPAandPercentUnder2($orgStudentsWithGPA, $yearFilter, $termFilter)
    {
        $aggGPABuilder = array();

        //Local Constants
        $gpaLimit = 2.00;
        $roundingFactor = 2;
        $multiplicationFactor = 100;

        foreach ($yearFilter as $keyToYear => $year) {
            foreach ($termFilter as $keyToTerm => $term) {

                //Initialize count variables
                $studentTotalCount = 0;
                $gpaSum = 0;
                $studentsUnderGpaLimit = 0;

                //Loop through and sum
                foreach ($orgStudentsWithGPA as $studentWithGPA) {
                    //Term is not related to year
                    if ($year == $studentWithGPA['org_academic_year_id'] && $term == $studentWithGPA['org_academic_terms_id']) {


                        $studentTotalCount++;
                        $gpaSum += $studentWithGPA['metadata_value'];

                        if ($studentWithGPA['metadata_value'] < $gpaLimit) {
                            $studentsUnderGpaLimit++;
                        }
                    }
                }

                if ($studentTotalCount > 0) {

                    $roundedMeanGPA = round($gpaSum / $studentTotalCount, $roundingFactor);
                    $formattedRoundedMeanGPA = number_format($roundedMeanGPA, $roundingFactor, '.', '');
                    $percentageOfStudentsUnderGpaLimit = round(($studentsUnderGpaLimit / $studentTotalCount) * $multiplicationFactor, $roundingFactor);
                    $formattedPercentageOfStudentsUnderGpaLimit = number_format($percentageOfStudentsUnderGpaLimit, $roundingFactor, '.', '');
                    $formattedStudentCount = strval($studentTotalCount);

                    $aggGPABuilder[] = array('org_academic_year_id' => $year,
                        'org_academic_terms_id' => $term,
                        'mean_gpa' => $formattedRoundedMeanGPA,
                        'percent_under_2' => $formattedPercentageOfStudentsUnderGpaLimit,
                        'student_count' => $formattedStudentCount);
                }
            }
        }
        return $aggGPABuilder;
    }

    /**
     * Combines GPA and GPAbyRisk Sections by Year and Term, also replaces Year/Term id with Name
     * UNIT TESTED
     *
     * @param array $gpaWithYearTermIds
     * @param array $gpaByRiskWithYearTermIds
     * @return array
     */
    public function collateByYearTermAndReplaceId($gpaWithYearTermIds, $gpaByRiskWithYearTermIds)
    {
        //initialize arrays and helper objects
        $orgAcademicYearRepository = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgAcademicYear');
        $orgAcademicTermRepository = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgAcademicTerms');

        $mergeYears = array();
        $gpaSections = array();
        $riskWithGpaHolder = array();

        //MergingYearData
        foreach ($gpaWithYearTermIds as $gpaRow) {
            $mergeYears[] = $gpaRow['org_academic_year_id'];
        }

        $mergeYears = array_unique($mergeYears);

        //Merging Risk Color Data
        foreach ($gpaByRiskWithYearTermIds as $gpaByRisk) {
            //Renaming risk_level to risk color, mean_value to mean_gpa
            //Using YearId/TermId as key to combine all risk levels to use in function buildTerms
            $riskWithGpaHolder[$gpaByRisk['org_academic_year_id']][$gpaByRisk['org_academic_terms_id']][] = ['risk_color' => $gpaByRisk['risk_level'], 'student_count' => $gpaByRisk['student_count'], 'mean_gpa' => $gpaByRisk['mean_value']];
        }

        //Forming JSON
        foreach ($mergeYears as $yearId) {
            $orgAcademicYear = $orgAcademicYearRepository->findOneBy(['id' => $yearId]);
            $orgAcademicYearName = $orgAcademicYear = $orgAcademicYear->getName();

            $gpaSections[] = array(
                'year_name' => $orgAcademicYearName,
                'gpa_summary_by_term' =>
                    $this->buildTermsJSON($gpaWithYearTermIds, $yearId, $riskWithGpaHolder,$orgAcademicTermRepository));
        }

        return $gpaSections;
    }

    /**
     * Builds Term JSON for gpa section per year(no risk)
     * UNIT TESTED
     *
     * @param array $gpaWithYearTermIds
     * @param int $yearId
     * @return array
     */
    public function buildTermsJSON($gpaWithYearTermIds, $yearId, $riskWithGpaHolder, $orgAcademicTermRepository)
    {

        $termsRelatedToYear = array();

        foreach ($gpaWithYearTermIds as $gpaRow) {
            if ($yearId == $gpaRow['org_academic_year_id']) {
                $orgAcademicTerm = $orgAcademicTermRepository->findOneBy(['id' => $gpaRow['org_academic_terms_id']]);
                $orgAcademicTermName = $orgAcademicTerm->getName();

                $termsRelatedToYear[] = [
                    'term_name' => $orgAcademicTermName,
                    'student_count' => $gpaRow['student_count'],
                    'mean_gpa' => $gpaRow['mean_gpa'],
                    'percent_under_2' => $gpaRow['percent_under_2'],
                    'gpa_summary_by_risk' => $riskWithGpaHolder[$gpaRow['org_academic_year_id']][$gpaRow['org_academic_terms_id']]
                ];
            }
        }

        return $termsRelatedToYear;
    }

}
