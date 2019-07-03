<?php
namespace Synapse\ReportsBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\EntityDto\SurveySnapshotSectionDto;
use Synapse\ReportsBundle\EntityDto\SurveySnapshotSectionResponseDto;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SearchBundle\EntityDto\SearchDto;
use Synapse\SearchBundle\EntityDto\SearchResultListDto;
use Synapse\SearchBundle\Repository\OrgSearchRepository;
use Synapse\SearchBundle\Service\Impl\SearchService;
use Synapse\SurveyBundle\Repository\SurveyBranchRepository;
use Synapse\SurveyBundle\Repository\SurveyResponseRepository;

/**
 * @DI\Service("survey_reports_helper_service")
 */
class SurveyReportsHelperService extends AbstractService
{

    const SERVICE_KEY = 'survey_reports_helper_service';


    // Class constants

    private $greenvalues = array(6, 7);

    private $redvalues = array(1, 2);

    private $yellowvalues = array(3, 4, 5);


    // Scaffolding

    /**
     * @var Cache
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


    // Repositories

    /**
     * @var OrgSearchRepository
     */
    private $orgSearchRepository;

    /**
     * @var ReportsRunningStatusRepository
     */
    private $reportsRunningStatusRepository;

    /**
     * @var SurveyBranchRepository
     */
    private $surveyBranchRepository;

    /**
     * @var SurveyResponseRepository
     */
    private $surveyResponseRepository;


    // Services

    /**
     * @var SearchService
     */
    private $searchService;

    /**
     * @var TokenService
     */
    private $tokenService;

    /**
     * SurveyReportsHelperService Constructor
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

        // Scaffolding
        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->logger = $logger;
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        // Repositories
        $this->orgSearchRepository = $this->repositoryResolver->getRepository(OrgSearchRepository::REPOSITORY_KEY);
        $this->reportsRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsRunningStatusRepository::REPOSITORY_KEY);
        $this->surveyBranchRepository = $this->repositoryResolver->getRepository(SurveyBranchRepository::REPOSITORY_KEY);
        $this->surveyResponseRepository = $this->repositoryResolver->getRepository(SurveyResponseRepository::REPOSITORY_KEY);

        // Services
        $this->searchService = $this->container->get('search_service');
        $this->tokenService = $this->container->get(TokenService::SERVICE_KEY);
    }

    public function validateOptionPair($optionValues)
    {
        if (count($optionValues) > 1) {
            if (!($optionValues == array_intersect($optionValues, $this->redvalues)) && !($optionValues == array_intersect($optionValues, $this->yellowvalues)) && !($optionValues == array_intersect($optionValues, $this->greenvalues))) {
                throw new ValidationException([
                    "Invalid option pair"
                ], "Invalid option pair", "Invalid_option_pair");
            }
        }
        return true;
    }

    public function getReportInstance($reportInstanceId)
    {
        $reportInstance = $this->reportsRunningStatusRepository->find($reportInstanceId);
        $this->isObjectExist($reportInstance, "Invalid Report Instance", "Invalid_Report_Instance");
        return $reportInstance;
    }

    public function orderBy($sortBy)
    {
        $orderBy = $this->getSortByField(trim($sortBy));
        return $orderBy;
    }

    public function getPageNo($pageNo = '')
    {
        $pageNo = (int)$pageNo;
        if (!$pageNo) {
            $pageNo = ReportsConstants::PAGE_NO;
        }
        return $pageNo;
    }

    public function getOffset($offset = '')
    {
        $offset = (int)$offset;
        if (!$offset) {
            $offset = ReportsConstants::OFFSET;
        }
        return $offset;
    }

    /**
     * returns students with risk and intent to leave details
     *
     * @param int $organizationId
     * @param int $personId
     * @param array $students
     * @param array $studentList
     * @return array
     */
    public function formatDrilldownData($organizationId, $personId, $students, $studentList)
    {
        $classLevels = $this->searchService->prefetchSearchKeys($organizationId);
        $students = implode(',', $students);
        $resultSet = $this->orgSearchRepository->getRiskIntentData($personId, $students);
        $finalRespArr = array();
        $riskPermission = array();
        $intentToLeavePermission = array();

        if (!empty($resultSet)) {
            foreach ($resultSet as $res) {
                if ($res['intent_flag'] == 1) {
                    $intentToLeavePermission[$res['student_id']] = $res['intent_flag'];
                }
                if ($res['risk_flag'] == 1) {
                    $riskPermission[$res['student_id']] = $res['risk_flag'];
                }
            }
        }

        foreach ($studentList as $student) {
            $searchResultListDto = new SearchResultListDto();
            $searchResultListDto->setStudentId($student['student']);
            $searchResultListDto->setStudentFirstName($student['firstname']);
            $searchResultListDto->setStudentLastName($student['lastname']);
            /**
             * Added for excluding the risk and intent from the query
             */
            if (isset($riskPermission[$student['student']])) {
                $student['risk_flag'] = 1;
            } else {
                $student['risk_flag'] = 0;
            }

            if (isset($intentToLeavePermission[$student['student']])) {
                $student['intent_flag'] = 1;
            } else {
                $student['intent_flag'] = 0;
            }
            $risk_text = ($student['risk_flag'] && isset($student['risk_text'])) ? $student['risk_text'] : "gray";
            $searchResultListDto->setStudentRiskStatus($risk_text);
            $risk_imagename = ($student['risk_flag'] && isset($student['risk_imagename'])) ? $student['risk_imagename'] : "risk-level-icon-gray.png";
            $searchResultListDto->setStudentRiskImageName($risk_imagename);
            $intent_text = ($student['intent_flag'] && isset($student['intent_to_leave_text'])) ? $student['intent_to_leave_text'] : "";
            $searchResultListDto->setStudentIntentToLeave($intent_text);
            $intent_imagename = ($student['intent_flag'] && isset($student['intent_imagename'])) ? $student['intent_imagename'] : "";
            $searchResultListDto->setStudentIntentToLeaveImageName($intent_imagename);
            $classLevel = (isset($student['class_level'])) ? $classLevels['[CLASS_LEVELS]'][$student['class_level']] : "";
            $searchResultListDto->setStudentClasslevel($classLevel);
            $studentResponse = $student['response'];
            $searchResultListDto->setResponse($studentResponse);

            // Set student status
            if (! empty($student['is_active']) && (int)$student['is_active'] == 1 ) {
                $studentStatus = true;
            } else {
                $studentStatus = false;
            }
            $searchResultListDto->setStudentIsActive($studentStatus);
            $finalRespArr[] = $searchResultListDto;
        }
        return $finalRespArr;
    }

    public function isObjectExist($object, $message, $key)
    {
        if (!isset($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    private function getSortByField($sortBy)
    {

        $sortOrder = '';
        if (($sortBy[0] == '+') || ($sortBy[0] == '-')) {

            if ($sortBy[0] == '-') {
                $sortOrder = ' DESC';
            }

            $sortBy = substr($sortBy, 1, strlen($sortBy));
        }

        // This is done this tedious way to prevent SQL injection.  Unfortunately, "ORDER BY :sortBy" doesn't work.
        switch ($sortBy) {
            case 'student_last_name':
                $sortSQLsubstring = ' P.lastname ' . $sortOrder . ', P.firstname ' . $sortOrder;
                break;
            case 'student_risk_status':
            case 'risk_level':
                $sortSQLsubstring = ' P.risk_level' . $sortOrder . ', P.lastname, P.firstname';
                break;
            case 'student_classlevel':
                $sortSQLsubstring = ' class_level' . $sortOrder . ', P.lastname, P.firstname';
                break;
            case 'response':
                $sortSQLsubstring = ' response' . $sortOrder . ', P.lastname, P.firstname';
                break;
            default:
                $sortSQLsubstring = " P.risk_level, P.lastname, P.firstname ";
                break;
        }

        return $sortSQLsubstring;
    }

    public function getStudentListResponse($studentList, $personId)
    {
        $studentListDto = new SearchDto();
        $studentListDto->setPersonId($personId);

        $studentArray = [];
        foreach ($studentList as $student) {

            $studentDto = new SearchResultListDto();
            $studentDto->setStudentId($student['student']);
            $studentDto->setStudentFirstName($student['firstname']);
            $studentDto->setStudentLastName($student['lastname']);

            $studentArray[] = $studentDto;
        }

        $studentListDto->setSearchResult($studentArray);
        return $studentListDto;
    }

    public function getCSVExport($csvHeader, $orgId, $personId, $factorId, $finalRespArr)
    {
        $personRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Person');
        $fh = @fopen("data://report_downloads/{$orgId}-{$personId}-{$factorId}-factor-report.csv", 'w');
        if (isset($csvHeader) && count($csvHeader) > 0) {
            foreach ($csvHeader as $field) {
                fputcsv($fh, $field);
            }
        }
        if (!empty($finalRespArr)) {
            foreach ($finalRespArr as $array) {
                $personDetails = $personRepository->find($array->getStudentId());
                $primaryEmail = '';
                if ($personDetails) {
                    $personDetail = $personRepository->getPersonDetails($personDetails);
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
        if (isset($rows) && count($rows) > 0) {
            foreach ($rows as $fields) {
                fputcsv($fh, $fields);
            }
        }
        fclose($fh);
    }

    public function getDBField($questionTypeCode, $alias = 'sr')
    {
        if ($questionTypeCode == 'SA' || $questionTypeCode == 'ISQ-SA') {
            $field = $alias . '.char_value';
        } else
            if ($questionTypeCode == 'LA' || $questionTypeCode == 'ISQ-LA') {
                $field = $alias . '.charmax_value';
            } else {
                $field = $alias . '.decimal_value';
            }
        return $field;
    }

    public function getDoctrineField($questionTypeCode, $alias = 'sr')
    {
        if ($questionTypeCode == 'SA') {
            $field = $alias . '.charValue';
        } else
            if ($questionTypeCode == 'LA') {
                $field = $alias . '.charmaxValue';
            } else {
                $field = $alias . '.decimalValue';
            }
        return $field;
    }

    public function isInvalidPerson($personId, $reportInstance)
    {
        if ($personId != $reportInstance->getPerson()->getId()) {
            throw new ValidationException([
                "Invalid Person"
            ], "Invalid Person", "Invalid_Person");
        }
    }

}