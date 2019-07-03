<?php
namespace Synapse\ReportsBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Snc\RedisBundle\Doctrine\Cache\RedisCache;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Entity\ReportsRunningStatus;
use Synapse\ReportsBundle\EntityDto\FactorInfoDto;
use Synapse\ReportsBundle\EntityDto\ReportInfoDto;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\EntityDto\SurveyFactorReportDrilldownDto;
use Synapse\ReportsBundle\Job\FactorReportJob;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SearchBundle\Repository\OrgSearchRepository;
use Synapse\SearchBundle\Service\Impl\StudentListService;
use Synapse\SurveyBundle\Repository\FactorLangRepository;
use Synapse\SurveyBundle\Repository\FactorQuestionsRepository;
use Synapse\SurveyBundle\Repository\FactorRepository;
use Synapse\SurveyBundle\Repository\PersonFactorCalculatedRepository;
use Synapse\SurveyBundle\Repository\SurveyResponseRepository;

/**
 * @DI\Service("factorreport_service")
 */
class FactorReportService extends AbstractService
{

    const SERVICE_KEY = 'factorreport_service';

    //Class Constants

    /**
     * @var array
     */
    private $jobs;

    //Scaffolding

    /**
     * @var RedisCache
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
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var OrgPermissionsetService
     */
    private $orgPermissionsetService;

    /**
     * @var ReportDrilldownService
     */
    private $reportDrilldownService;

    /**
     * @var SurveyReportsHelperService
     */
    private $surveyReportsHelperService;

    /**
     * @var StudentListService
     */
    private $studentListService;

    /**
     * @var TokenService
     */
    private $tokenService;

    //Repositories

    /**
     * @var EbiMetadataRepository
     */
    private $ebiMetadataRepository;

    /**
     * @var FactorLangRepository
     */
    private $factorLangRepository;

    /**
     * @var FactorQuestionsRepository
     */
    private $factorQuestionsRepository;

    /**
     * @var FactorRepository
     */
    private $factorRepository;

    /**
     * @var OrganizationlangRepository
     */
    private $organizationLangRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * @var OrgSearchRepository
     */
    private $orgSearchRepository;

    /**
     * @var PersonFactorCalculatedRepository
     */
    private $personFactorCalculatedRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var ReportsRunningStatusRepository
     */
    private $reportsRunningStatusRepository;

    /**
     * @var SurveyResponseRepository
     */
    private $surveyResponseRepository;


    /**
     * FactorReportService constructor.
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

        //Repositories
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(EbiMetadataRepository::REPOSITORY_KEY);
        $this->factorRepository = $this->repositoryResolver->getRepository(FactorRepository::REPOSITORY_KEY);
        $this->factorLangRepository = $this->repositoryResolver->getRepository(FactorLangRepository::REPOSITORY_KEY);
        $this->factorQuestionsRepository = $this->repositoryResolver->getRepository(FactorQuestionsRepository::REPOSITORY_KEY);
        $this->organizationLangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->orgSearchRepository = $this->repositoryResolver->getRepository(OrgSearchRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->personFactorCalculatedRepository = $this->repositoryResolver->getRepository(PersonFactorCalculatedRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->reportsRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsRunningStatusRepository::REPOSITORY_KEY);
        $this->surveyResponseRepository = $this->repositoryResolver->getRepository(SurveyResponseRepository::REPOSITORY_KEY);

        //Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->orgPermissionsetService = $this->container->get(OrgPermissionsetService::SERVICE_KEY);
        $this->reportDrilldownService = $this->container->get(ReportDrilldownService::SERVICE_KEY);
        $this->studentListService = $this->container->get(StudentListService::SERVICE_KEY);
        $this->surveyReportsHelperService = $this->container->get(SurveyReportsHelperService::SERVICE_KEY);
        $this->tokenService = $this->container->get(TokenService::SERVICE_KEY);
    }

    /**
     * Initiate the job that generates the Survey Factors Report
     *
     * @param int $loggedUserId
     * @param int $surveyId
     * @param int $reportInstanceId
     * @param ReportRunningStatusDto $reportRunningDto
     */
    public function initiateFactorReport($loggedUserId, $surveyId, $reportInstanceId, $reportRunningDto)
    {
        $this->logger->debug("Create Factor Report -  " . $reportInstanceId);
        $jobNumber = uniqid();
        $job = new FactorReportJob();
        $job->args = array(
            'userId' => $loggedUserId,
            'surveyId' => $surveyId,
            'reportInstanceId' => $reportInstanceId,
            'reportRunningDto' => serialize($reportRunningDto)
        );
        try {
            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        } catch (\Exception $exp) {
            throw new ValidationException([
                'Failure in resque job.'
            ], 'Failure in resque job.', 'rescue_failure');
        }
    }

    /**
     * Generates data for a Survey Factors report drilldown, based on the passed in factor and values for that factor.
     *
     * @param int $reportId
     * @param int $loggedInUserId
     * @param int $factorId
     * @param string $optionValues
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $outputType
     * @param string $sortBy
     * @return SurveyFactorReportDrilldownDto
     * @throws AccessDeniedException
     */
    public function getSurveyFactorsReportDrilldown($reportId, $loggedInUserId, $factorId, $optionValues, $pageNumber, $recordsPerPage, $outputType, $sortBy = '')
    {
        //Get the report running status object and organization ID
        $reportRunningStatusObject = $this->reportsRunningStatusRepository->find($reportId);
        $organizationId = $reportRunningStatusObject->getOrganization()->getId();
        $orgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
        $surveyFactorReportDrilldownDto = new SurveyFactorReportDrilldownDto();

        //Get the search attributes for the report and the survey ID on which the report was run.
        $reportResultArray = json_decode($reportRunningStatusObject->getResponseJson(), true);
        $surveyId = $reportResultArray['request_json']['search_attributes']['survey_filter']['survey_id'];
        $searchAttributes = $reportResultArray['request_json']['search_attributes'];

        //Get the factor lang object for the factor ID that was drilled down on, and get the name of the factor for that object.
        $factorLangObject = $this->factorLangRepository->findOneBy(['factor' => $factorId]);
        $factorName = $factorLangObject->getName();

        //Get the range of option values using the minimum and maximum option values passed in.
        //If only one option value is passed in, then the end range value becomes 1 + that value.
        $optionRangeBeginningValue = '';
        $optionRangeEndingValue = '';
        if (!empty($optionValues)) {
            $optionValueRange = $this->getOptionValueRange($optionValues);

            $optionRangeBeginningValue = $optionValueRange['range_beginning_value'];
            $optionRangeEndingValue = $optionValueRange['range_ending_value'];
        }

        //Determine the pagination of the result set.
        if (empty($pageNumber)) {
            $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }

        if (empty($recordsPerPage)) {
            $recordsPerPage = SynapseConstant::DEFAULT_RECORD_COUNT;
        }

        $startPoint = ($pageNumber * $recordsPerPage) - $recordsPerPage;

        //Get the student IDs that were included in the original report result
        $studentIdsIncludedInReport = $reportRunningStatusObject->getFilteredStudentIds();

        //Get the question ID, datablock ID, and factor IDs for the questions to which the faculty has individual and aggregate access.
        $facultyAccessibleQuestionsAndFactors = $this->factorRepository->getDatablockIdsWithFactorIdsAccessibleToFaculty($organizationId, $loggedInUserId, $surveyId, false, $factorId);

        //Gets an array with indices of the ebi_question_id with an array of student IDs at each index, and also limits the student list down to whom the faculty has access.
        $studentsRelatedToQuestionsArray = $this->getStudentsAssociatedWithFacultysDataBlocks($studentIdsIncludedInReport, $loggedInUserId, $organizationId, $facultyAccessibleQuestionsAndFactors);
        if (!empty($studentsRelatedToQuestionsArray)) {

            //Get a unique list of the student IDs regardless of the question they are associated with.
            $studentIds = array_unique(call_user_func_array('array_merge', $studentsRelatedToQuestionsArray));

            //Get the students that have a factor calculated value between the min and max range. This will supply the total count for the drilldown.
            $studentIdsWithCalculatedFactorValueWithinRange = $this->personFactorCalculatedRepository->getStudentsWithCalculatedFactorValueWithinRange($factorId, $surveyId, $studentIds, $optionRangeBeginningValue, $optionRangeEndingValue);

            // Determine whether the user has individual or aggregate permission to each of the students.
            $accessLevels = $this->orgPermissionsetRepository->getAccessLevelForFacultyAndStudents($loggedInUserId, $studentIdsWithCalculatedFactorValueWithinRange);

            $individuallyAccessibleStudents = array_keys($accessLevels, 1);
            $aggregateOnlyStudents = array_keys($accessLevels, 0);

            // For each of these groupings, determine which students are participants in the current year.
            $individuallyAccessibleParticipants = $this->orgPersonStudentYearRepository->getParticipantStudentsFromStudentList($individuallyAccessibleStudents, $organizationId, $orgAcademicYearId);
            $aggregateOnlyParticipants = $this->orgPersonStudentYearRepository->getParticipantStudentsFromStudentList($aggregateOnlyStudents, $organizationId, $orgAcademicYearId);

            //Set the counts of each group of students.
            $individualParticipantCount = count($individuallyAccessibleParticipants);
            $individualNonParticipantCount = count($individuallyAccessibleStudents) - $individualParticipantCount;

            $aggregateOnlyParticipantCount = count($aggregateOnlyParticipants);
            $aggregateOnlyNonParticipantCount = count($aggregateOnlyStudents) - $aggregateOnlyParticipantCount;

            // Throw an exception if there are no students which should be included in the drilldown,
            if ($individualParticipantCount == 0) {
                throw new AccessDeniedException("access_denied", "The students identified are either no longer participating in Mapworks or you do not have permission to them individually.");
            }

            //Convert the unique array of student IDs to a string of student IDs.
            $studentsRelatedToQuestionsString = implode(',', $individuallyAccessibleParticipants);

            //Get the ClassLevel Ebi metadata ID value
            $ebiMetadataObject = $this->ebiMetadataRepository->findOneBy(['key' => 'ClassLevel']);
            $ebiMetadataId = $ebiMetadataObject->getId();

            //Get the drilldown data for the specified factor and factor value range.
            $surveyFactorReportDrilldownStudents = $this->factorRepository->getSurveyFactorReportDrilldownStudents($studentsRelatedToQuestionsString, $startPoint, $recordsPerPage, $organizationId, $factorId, $surveyId, $optionRangeBeginningValue, $optionRangeEndingValue, $ebiMetadataId, $outputType, $sortBy, $orgAcademicYearId);

            //Get the student Ids from the drilldown results
            $drilldownStudentIds = array_column($surveyFactorReportDrilldownStudents, 'student');

            //If the student list is not empty, create the DTO to return the drilldown data, and set the necessary attributes.
            if (!empty($surveyFactorReportDrilldownStudents)) {
                $surveyFactorReportDrilldownDto->setPersonId($loggedInUserId);
                $surveyFactorReportDrilldownDto->setRecordsPerPage($recordsPerPage);
                $surveyFactorReportDrilldownDto->setCurrentPage($pageNumber);
                $surveyFactorReportDrilldownDto->setAggregateOnlyNonParticipantCount($aggregateOnlyNonParticipantCount);
                $surveyFactorReportDrilldownDto->setAggregateOnlyParticipantCount($aggregateOnlyParticipantCount);
                $surveyFactorReportDrilldownDto->setIndividualNonParticipantCount($individualNonParticipantCount);
                $surveyFactorReportDrilldownDto->setSearchAttributes($searchAttributes);
                $surveyFactorReportDrilldownDto->setQuestion($factorName);
                $surveyFactorReportDrilldownDto->setTotalRecords($individualParticipantCount);

                //Calculate the total page count
                $totalPageCount = ceil($individualParticipantCount / $recordsPerPage);
                $surveyFactorReportDrilldownDto->setTotalPages($totalPageCount);

                //Format the drilldown data, and set the formatted drilldown data to the DTO.
                $formattedDrilldownData = $this->surveyReportsHelperService->formatDrilldownData($organizationId, $loggedInUserId, $drilldownStudentIds, $surveyFactorReportDrilldownStudents);
                $surveyFactorReportDrilldownDto->setSearchResult($formattedDrilldownData);

                //If there is drilldown data and the desired output is a CSV file, create & populate that file, and return the file's name.
                if (!empty($formattedDrilldownData) && $outputType == 'csv') {
                    $this->generateSurveyFactorReportDrilldownCSV($formattedDrilldownData, $loggedInUserId, $organizationId, $factorId);
                    $fileName['file_name'] = $organizationId . "-" . $loggedInUserId . "-" . $factorId . "-factor-report.csv";
                    return $fileName;
                }
            }
        }
        return $surveyFactorReportDrilldownDto;
    }

    /**
     * Generates the header rows for the CSV export of the Survey Factors Report drilldown, and call the function to create the CSV file.
     *
     * @param array $drilldownData
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param int $factorId
     * @return null
     */
    private function generateSurveyFactorReportDrilldownCSV($drilldownData, $loggedInUserId, $organizationId, $factorId)
    {
        //Get the person object
        $person = $this->personRepository->find($loggedInUserId);

        //Get the organization's current datetime, and explode it into an array for later use.
        $currentDateTime = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, 'Y-m-d H:i:s');
        $currentDateTimeArray = explode(' ', $currentDateTime);

        //Create the data for the second and third rows of the CSV file
        $secondRow = $person->getFirstname() . " " . $person->getLastname() . " on " . $currentDateTimeArray[0] . " at " . $currentDateTimeArray[1];
        $factor = $this->factorLangRepository->findOneBy(['factor' => $factorId]);
        $thirdRow = $factor->getName();

        //Build the array that will become the CSV file.
        $csvHeader[] = ["Survey Factors"];
        $csvHeader[] = [$secondRow];
        $csvHeader[] = [$thirdRow];
        $csvHeader[] = [];
        $csvHeader[] = [
            'FIRST NAME',
            'LAST NAME',
            'RISK INDICATOR',
            'CLASS LEVEL',
            'RESPONSE',
            'EXTERNAL ID',
            'PRIMARY EMAIL'
        ];

        //Build the CSV file.
        $this->surveyReportsHelperService->getCSVExport($csvHeader, $organizationId, $loggedInUserId, $factorId, $drilldownData);
    }

    /**
     * Generate the Survey Factors Report
     *
     * @param int $reportRunningStatusId
     * @param int $surveyId
     * @param int $personId
     * @param ReportRunningStatusDto $reportRunningDto
     * @return array|string
     */
    public function generateReport($reportRunningStatusId, $surveyId, $personId, $reportRunningDto)
    {
        $reportRunningStatus = $this->reportsRunningStatusRepository->find($reportRunningStatusId);
        if (empty($reportRunningStatus)) {
            $error = ['error' => 'Report Running Status Not Found'];
            $responseJSON = $this->serializer->serialize($error, 'json');
            $reportRunningStatus->setStatus('F');
            $reportRunningStatus->setResponseJson($responseJSON);
            $this->reportsRunningStatusRepository->flush();
            return '';
        }
        $reportStudentIds = $reportRunningStatus->getFilteredStudentIds();
        $organizationId = $reportRunningStatus->getOrganization()->getId();
        $factorReport = array();
        $uniqueSetOfStudentsInReport = [];
        $factorReports = [];

        if (empty($reportStudentIds)) {
            $factorReports['status_message'] = [
                'code' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_CODE,
                'description' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_MESSAGE
            ];
        } else {
            //list of data blocks where the faculty/logged in user has permissions
            $questionsWithDataBlockId = $this->factorRepository->getDatablockIdsWithFactorIdsAccessibleToFaculty($organizationId, $personId, $surveyId, true);

            $ebiQuestionIds = array_unique(array_column($questionsWithDataBlockId, 'ebi_question_id'));

            //list of ebi_questions where the faculty/logged in user has permissions
            $ebiQuestionPermissions = $this->getStudentsAssociatedWithFacultysDataBlocks($reportStudentIds, $personId, $organizationId, $questionsWithDataBlockId);

            $factorListByPermission = $this->factorRepository->listFactorsByPermission($ebiQuestionIds);

            if (!empty($factorListByPermission)) {
                foreach ($factorListByPermission as $factorRecord) {
                    $factorId = $factorRecord['factor_id'];

                    $filteredStudents = $this->filterStudentId($factorId, $ebiQuestionPermissions);
                    if (!empty($filteredStudents)) {
                        $allStudents = call_user_func_array('array_merge', $filteredStudents);
                        $uniqueFilteredStudentIds = array_unique($allStudents);
                        $totalStudents = count($uniqueFilteredStudentIds);
                        $availableStudents = implode(',', $uniqueFilteredStudentIds);
                        $uniqueSetOfStudentsInReport += $uniqueFilteredStudentIds;
                        $factors = $this->factorRepository->getFactorReport($totalStudents, $surveyId, $organizationId, $availableStudents, $factorId);
                        if (!empty($factors)) {
                            $factorReport[] = $this->processFactor($factors, $reportRunningStatus, $surveyId);
                        }
                    }
                }

            }

            if (empty($factorListByPermission) || empty($factorReport)) {
                $factorReports['status_message'] = [
                    'code' => ReportsConstants::REPORT_NO_DATA_CODE,
                    'description' => ReportsConstants::REPORT_NO_DATA_MESSAGE
                ];
            }
        }

        $factorReports['report_info'] = $this->reportInfo($reportRunningStatus, $reportRunningStatusId, count($uniqueSetOfStudentsInReport));
        $factorReports['campus_info'] = $this->campusInfo($reportRunningStatus);
        $factorReports['request_json'] = $reportRunningDto;
        $factorReports['factors'] = $factorReport;

        $reportRunningStatus->setStatus('C');
        $responseJSON = $this->serializer->serialize($factorReports, 'json');
        $reportRunningStatus->setResponseJson($responseJSON);

        $this->reportsRunningStatusRepository->flush();

        $this->sendEmail($reportRunningStatus);
    }

    /**
     * Gets the list of student IDs & names to populate the bulk actions module within a report drilldown.
     *
     * @param int $reportId
     * @param int $organizationId
     * @param int $loggedInUserId
     * @param int $factorId
     * @param string $optionValues
     * @return array
     */
    public function getSurveyFactorReportDrilldownStudentNamesAndIds($reportId, $organizationId, $loggedInUserId, $factorId, $optionValues)
    {
        //Get the report running status object, the survey ID on which the report was run, and the students in the original report result
        $reportRunningStatusObject = $this->reportsRunningStatusRepository->find($reportId);
        $reportResultArray = json_decode($reportRunningStatusObject->getResponseJson(), true);
        $surveyId = $reportResultArray['request_json']['search_attributes']['survey_filter']['survey_id'];
        $studentIdsIncludedInReport = explode(',', $reportRunningStatusObject->getFilteredStudentIds());

        //Get the individually accessible list of student IDs from the students originally included in the report
        $individuallyAccessibleParticipantStudentIds = $this->reportDrilldownService->getIndividuallyAccessibleParticipants($loggedInUserId, $organizationId, $studentIdsIncludedInReport);

        //Get the range of option values using the minimum and maximum option values passed in.
        //If only one option value is passed in, then the end range value becomes 1 + that value.
        $optionRangeBeginningValue = '';
        $optionRangeEndingValue = '';
        if (!empty($optionValues)) {
            $optionValueRange = $this->getOptionValueRange($optionValues);

            $optionRangeBeginningValue = $optionValueRange['range_beginning_value'];
            $optionRangeEndingValue = $optionValueRange['range_ending_value'];
        }

        //Get the students that have a factor calculated value between the min and max range. This will supply the total count for the drilldown.
        $studentIdsWithCalculatedFactorValueWithinRange = $this->personFactorCalculatedRepository->getStudentsWithCalculatedFactorValueWithinRange($factorId, $surveyId, $individuallyAccessibleParticipantStudentIds, $optionRangeBeginningValue, $optionRangeEndingValue);

        //Get the formatted list of names and IDs to be returned
        $studentNamesAndIds = $this->studentListService->getStudentIdsAndNames($studentIdsWithCalculatedFactorValueWithinRange, $loggedInUserId);

        return $studentNamesAndIds;

    }

    /**
     * Gets an array containing the minimum and maximum factor option range values.
     *
     * @param string $optionValues
     * @return array
     */
    private function getOptionValueRange($optionValues)
    {
        $optionValues = explode(',', $optionValues);
        $this->surveyReportsHelperService->validateOptionPair($optionValues);

        $optionValueCount = count($optionValues);
        if ($optionValueCount == 1) {
            $optionRangeBeginningValue = $optionValues[0];
            $optionRangeEndingValue = $optionValues[0] + 1;
        } elseif ($optionValueCount > 0) {
            sort($optionValues);
            $optionRangeBeginningValue = $optionValues[0];
            $optionRangeEndingValue = $optionValues[$optionValueCount - 1] + 1;
        } else {
            $optionRangeBeginningValue = '';
            $optionRangeEndingValue = '';
        }

        $optionRangeValues = [
            'range_beginning_value' => $optionRangeBeginningValue,
            'range_ending_value' => $optionRangeEndingValue
        ];

        return $optionRangeValues;
    }


    /**
     * Filter the student ID list.
     *
     * @param $factorId
     * @param $ebiQuestionPermissions
     * @return array
     * @deprecated This function can be replaced by following the pattern shown in getSurveyFactorsReportDrilldown().
     */
    private function filterStudentId($factorId, $ebiQuestionPermissions)
    {
        $filteredStudents = array();
        $factorQuestions = $this->factorQuestionsRepository->getFactorQuestionList($factorId);
        if (!empty($factorQuestions)) {
            foreach ($factorQuestions as $factorQuestion) {
                //get the students for those questions
                $factorQuestionId = $factorQuestion['ebi_question_id'];

                $ebiStudents = isset($ebiQuestionPermissions[$factorQuestionId]) ? $ebiQuestionPermissions[$factorQuestionId] : array();
                if (!empty($ebiStudents)) {
                    $filteredStudents[] = $ebiQuestionPermissions[$factorQuestionId];
                }
            }
        }
        return $filteredStudents;
    }

    private function processFactor($factors, $reportRunningStatus, $surveyId)
    {
        $factorDetails = array();
        if (!empty($factors)) {
            foreach ($factors as $factor) {
                $factorId = $factor['factor_id'];
                $options = array();
                $redOption = array();
                $yellowOption = array();
                $greenOption = array();
                $factorInfoDto = new FactorInfoDto();

                $factorInfoDto->setFactorNumber($factorId);
                $factorInfoDto->setFactorText($factor['factor_name']);
                $redOption['option'] = 'red';
                //$redOption['option_min'] = floatval($factor['red_minimum']);
                //$redOption['option_max'] = floatval($factor['red_maximum']);
                $redOption['option_min'] = 1;
                $redOption['option_max'] = 2.9999;
                $redOption['responded_percentage'] = floatval($factor['responded_red_percentage']);
                $redOption['responded'] = (int)$factor['responded_red'];
                $options[] = $redOption;
                $yellowOption['option'] = 'yellow';
                //$yellowOption['option_min'] = floatval($factor['yellow_minimum']);
                //$yellowOption['option_max'] = floatval($factor['yellow_maximum']);
                $yellowOption['option_min'] = 3;
                $yellowOption['option_max'] = 5.9999;
                $yellowOption['responded_percentage'] = floatval($factor['responded_yellow_percentage']);
                $yellowOption['responded'] = (int)$factor['responded_yellow'];
                $options[] = $yellowOption;
                $greenOption['option'] = 'green';
                //$greenOption['option_min'] = floatval($factor['green_minimum']);
                //$greenOption['option_max'] = floatval($factor['green_maximum']);
                $greenOption['option_min'] = 6;
                $greenOption['option_max'] = 7.0000;
                $greenOption['responded_percentage'] = floatval($factor['responded_green_percentage']);
                $greenOption['responded'] = (int)$factor['responded_green'];
                $options[] = $greenOption;
                $summary['responded_percentage'] = floatval($factor['responded_percentage']);
                $summary['responded'] = (int)$factor['responded'];
                $summary['mean'] = floatval($factor['responded_mean']);
                $summary['std_deviation'] = floatval($factor['responded_std']);
                $additional_data['questions'] = $this->getQuestions($factorId, $surveyId);
                $factorInfoDto->setReportOptions($options);
                $factorInfoDto->setSummary($summary);
                $factorInfoDto->setAdditionalData($additional_data);
            }
        }
        return $factorInfoDto;
    }

    private function getQuestions($factorId, $surveyId)
    {
        $questions = array();
        $factorQuestions = $this->factorRepository->getFactorQuestions($factorId, $surveyId);
        if (!empty($factorQuestions)) {
            foreach ($factorQuestions as $factorQuestion) {
                $question = array();
                $question['q_no'] = $factorQuestion['question_id'];
                $question['q_text'] = $factorQuestion['question'];
                $questions[] = $question;
            }
        }
        return $questions;
    }

    /**
     * @param ReportsRunningStatus $reportRunningStatus
     * @param $reportRunningStatusId
     * @param $totalStudents
     * @return ReportInfoDto
     */
    private function reportInfo($reportRunningStatus, $reportRunningStatusId, $totalStudents)
    {
        $reportInfo = new ReportInfoDto();
        $reportInfo->setReportId($reportRunningStatus->getReports()->getId());
        $reportInfo->setReportInstanceId($reportRunningStatusId);
        $reportInfo->setReportName($reportRunningStatus->getReports()->getName());
        $reportInfo->setStatus('C');
        $reportInfo->setReportDescription($reportRunningStatus->getReports()->getDescription());
        $reportInfo->setReportDate($reportRunningStatus->getCreatedAt());
        $reportInfo->setTotalStudents($totalStudents);
        $reportInfo->setShortCode($reportRunningStatus->getReports()->getShortCode());
        $reportInfo->setReportDisable($reportRunningStatus->getReports()->getIsActive());
        $personName['first_name'] = $reportRunningStatus->getPerson()->getFirstname();
        $personName['last_name'] = $reportRunningStatus->getPerson()->getLastname();
        $reportInfo->setReportBy($personName);
        $reportFilter['numeric'] = true;
        $reportFilter['categorical'] = true;
        $reportFilter['scaled'] = true;
        $reportFilter['long_answer'] = true;
        $reportFilter['short_answer'] = true;
        $reportInfo->setReportFilter($reportFilter);
        return $reportInfo;
    }

    /**
     * @param ReportsRunningStatus $reportRunningStatus
     * @return array
     */
    private function campusInfo($reportRunningStatus)
    {
        $organization = $this->organizationLangRepository->findOneBy([
            'organization' => $reportRunningStatus->getOrganization()->getId()
        ]);
        $organizationInfo = array(
            'campus_id' => $organization->getOrganization()->getId(),
            'campus_name' => $organization->getOrganizationName(),
            'campus_logo' => $organization->getOrganization()->getLogoFileName(),
            'campus_color' => $organization->getOrganization()->getPrimaryColor()
        );
        return $organizationInfo;
    }

    /**
     * @param ReportsRunningStatus $runningStatusObj
     */
    private function sendEmail($runningStatusObj)
    {
        $alertService = $this->container->get('alertNotifications_service');
        $shortCode = $runningStatusObj->getReports()->getShortCode();
        $reportName = $runningStatusObj->getReports()->getName();;
        $person = $runningStatusObj->getPerson();
        $alertService->createNotification($shortCode, $reportName, $person, null, null, null, null, null, null, null, null, $runningStatusObj);
    }

    /**
     * get student ids mapped with ebi_question_id and datablock_id
     *
     * @param array $studentIdsIncludedInReport
     * @param integer $personId
     * @param integer $orgId
     * @param array $facultyDatablockQuestions
     * @return array
     */
    private function getStudentsAssociatedWithFacultysDataBlocks($studentIdsIncludedInReport, $personId, $orgId, $facultyDatablockQuestions)
    {
        $ebiQuestionIds = array_unique(array_column($facultyDatablockQuestions, 'ebi_question_id'));

        $studentsAssociatedWithDataBlock = $this->surveyResponseRepository->getStudentsBasedQuestionPermission($personId, $orgId, $ebiQuestionIds, $studentIdsIncludedInReport);

        $studentsAssociatedWithFacultysQuestions = [];

        $dataBlockWithStudents = [];

        foreach ($studentsAssociatedWithDataBlock as $studentWithDataBlock) {
            $dataBlockWithStudents[$studentWithDataBlock['datablock_id']][] = $studentWithDataBlock['student_id'];
        }
        foreach ($facultyDatablockQuestions as $facultyDatablockQuestion) {

            if (!isset($studentsAssociatedWithFacultysQuestions[$facultyDatablockQuestion['ebi_question_id']])) {

                $studentsAssociatedWithFacultysQuestions[$facultyDatablockQuestion['ebi_question_id']] = [];
            }

            $studentIds = array_merge($studentsAssociatedWithFacultysQuestions[$facultyDatablockQuestion['ebi_question_id']], $dataBlockWithStudents[$facultyDatablockQuestion['datablock_id']]);

            $studentsAssociatedWithFacultysQuestions[$facultyDatablockQuestion['ebi_question_id']] = array_unique($studentIds);

        }

        return $studentsAssociatedWithFacultysQuestions;
    }
}
