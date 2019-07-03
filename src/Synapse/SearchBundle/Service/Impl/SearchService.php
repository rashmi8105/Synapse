<?php
namespace Synapse\SearchBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\Service\Utility\SearchUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\CoreBundle\Util\SqlUtils;
use Synapse\ReportsBundle\Repository\RetentionCompletionVariableNameRepository;
use Synapse\RiskBundle\Repository\RiskLevelsRepository;
use Synapse\SearchBundle\DAO\CustomSearchDAO;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\SearchBundle\Job\CustomSearchCSVJob;
use Synapse\SearchBundle\Repository\OrgSearchRepository;
use Synapse\SurveyBundle\Repository\FactorRepository;


/**
 * @DI\Service("search_service")
 */
class SearchService extends AbstractService
{

    const SERVICE_KEY = 'search_service';


    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Resque
     */
    private $resque;


    // Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var SearchUtilityService
     */
    private $searchUtilityService;

    /**
     * @var StudentListService
     */
    private $studentListService;


    // DAO

    /**
     * @var CustomSearchDAO
     */
    private $customSearchDAO;


    // Repositories

    /**
     * @var FactorRepository
     */
    private $factorRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrgAcademicTermRepository
     */
    private $orgAcademicTermRepository;

    /**
     * @var OrgSearchRepository
     */
    private $organizationSearchRepository;

    /**
     * @var OrgCoursesRepository
     */
    private $orgCoursesRepository;

    /**
     * @var OrgCourseStudentRepository
     */
    private $orgCourseStudentRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgSearchRepository
     */
    private $orgSearchRepository;

    /**
     * @var RiskLevelsRepository
     */
    private $riskLevelsRepository;

    /**
     * @var RetentionCompletionVariableNameRepository
     */
    private $retentionCompletionVariableNameRepository;

    /**
     * SearchService constructor.
     *
     * @DI\InjectParams({
     *     "repositoryResolver" = @DI\Inject("repository_resolver"),
     *     "logger" = @DI\Inject("logger"),
     *     "container" = @DI\Inject("service_container")
     * })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->searchUtilityService = $this->container->get(SearchUtilityService::SERVICE_KEY);
        $this->studentListService = $this->container->get(StudentListService::SERVICE_KEY);

        // DAO
        $this->customSearchDAO = $this->container->get(CustomSearchDAO::DAO_KEY);

        // Repositories
        $this->factorRepository = $this->repositoryResolver->getRepository(FactorRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->orgAcademicTermRepository = $this->repositoryResolver->getRepository(OrgAcademicTermRepository::REPOSITORY_KEY);
        $this->organizationSearchRepository = $this->repositoryResolver->getRepository(OrgSearchRepository::REPOSITORY_KEY);
        $this->orgCoursesRepository =  $this->repositoryResolver->getRepository(OrgCoursesRepository::REPOSITORY_KEY);
        $this->orgCourseStudentRepository = $this->repositoryResolver->getRepository(OrgCourseStudentRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgSearchRepository = $this->repositoryResolver->getRepository(OrgSearchRepository::REPOSITORY_KEY);
        $this->riskLevelsRepository = $this->repositoryResolver->getRepository(RiskLevelsRepository::REPOSITORY_KEY);
        $this->retentionCompletionVariableNameRepository = $this->repositoryResolver->getRepository(RetentionCompletionVariableNameRepository::REPOSITORY_KEY);
    }


    /**
     * Gets the filter criteria for the academic update report
     *
     * @param array $searchAttributes
     * @return null|string
     * @deprecated Will be removed in ESPRJ-17469
     */
    public function getFilterCriteriaForAcademicUpdate($searchAttributes)
    {
    	$filters = array();


    	if ( isset( $searchAttributes['term_ids']) && ! empty($searchAttributes['term_ids'])) {

    		$filters[] = '( oc.org_academic_terms_id IN (' . $searchAttributes['term_ids'] . ') )';
    	}

    	// If failure risk is selected as search Param

    	if ( isset( $searchAttributes['failure_risk']) && ($searchAttributes['failure_risk'] !== "")) {

    		if ($searchAttributes['failure_risk']) {
    			$filters[] = '( au.failure_risk_level = "high" )';
    		} else {
    			$filters[] = '( au.failure_risk_level = "low" )';
    		}
    	}

    	// If Grade is selected as search Param

    	if ( isset( $searchAttributes['grade']) && ! empty($searchAttributes['grade'])) {

    		$filters[] = '(' . $this->searchUtilityService->makeSqlQuery($searchAttributes['grade'], 'au.grade') . ')';
    	}

    	// If Absences or absence range is selected as search Param

        if (isset($searchAttributes['absences']) && (!empty($searchAttributes['absences']) || $searchAttributes['absences'] === "0")) {
            $filters[] = '( au.absence = ' . $searchAttributes['absences'] . ' )';
        } elseif (array_key_exists('absence_range', $searchAttributes) && !empty($searchAttributes['absence_range'])) {
            if (
                (array_key_exists('min_range', $searchAttributes['absence_range']) && (!empty($searchAttributes['absence_range']['min_range']) || $searchAttributes['absence_range']['min_range'] === "0"))
                &&
                (array_key_exists('max_range', $searchAttributes['absence_range']) && (!empty($searchAttributes['absence_range']['max_range']) || $searchAttributes['absence_range']['max_range'] === "0"))
            ) {
                $filters[] = '( au.absence between ' . $searchAttributes['absence_range']['min_range'] . ' and ' . $searchAttributes['absence_range']['max_range'] . ' )';
            }
        }

    	if ( isset( $searchAttributes['final_grade']) && ! empty($searchAttributes['final_grade'])) {

    		$filters[] = '( ' . $this->searchUtilityService->makeSqlQuery($searchAttributes['final_grade'], 'au.final_grade') . ' )';
    	}

    	$filterCount = 1;

    	$filterCriteria = '';



    	foreach ($filters as $key => $filter) {

    		if ($filterCount > 1) {
    			$filterCriteria .= ' AND ';
    		}

    		$filterCriteria .= ' ' . $filter;

    		$filterCount ++;
    	}

    	if (count($filters) == 0) {

    		return null;
    	}

    	if(trim($filterCriteria) != ''){
    		$filterCriteria = ' ( '.$filterCriteria.')  ';
    	}

    	// If start and end date are selected for search param

    	if ((isset( $searchAttributes['start_date'])
    			&& ! empty($searchAttributes['start_date']))
    			&& (isset( $searchAttributes['end_date'])
    					&& ! empty($searchAttributes['end_date']))
    ) {
    						$filterCriteria .= ' AND ( DATE(au.update_date) BETWEEN STR_TO_DATE("' . $searchAttributes['start_date'] . '", "' . SynapseConstant::METADATA_TYPE_DEFAULT_DATE_FORMAT . '")' . ' and STR_TO_DATE("' . $searchAttributes['end_date'] . '", "' . SynapseConstant::METADATA_TYPE_DEFAULT_DATE_FORMAT . '") )';
    					}

    					return $filterCriteria;
    }

/**
     * Get search keys
     *
     * @param integer $organizationId
     * @return array
     */
    public function prefetchSearchKeys ($organizationId)

        {
         //  Class Level MetadataId

        $query = "select ebi_metadata_id, list_name, list_value from ebi_metadata_list_values where ebi_metadata_id=(select id from ebi_metadata where meta_key = 'ClassLevel')";
        $ebiMetadata = $this->orgSearchRepository->getOrgSearch($query);
        
        if ( !isset( $ebiMetadata) ) {

            return [];
        }

        $ebiMetadataId = $ebiMetadata[0]['ebi_metadata_id'];

        $classLevels = array();

        foreach ($ebiMetadata as $metadata) {

            $classLevels[$metadata['list_value']] = $metadata['list_name'];
        }
        
        

         //  Get Current Academic Year

        $yearDetailsSql = "SELECT
    id, start_date, end_date
FROM
    org_academic_year
WHERE
    DATE(NOW()) BETWEEN start_date AND end_date
        AND organization_id = $organizationId
        AND deleted_at IS NULL";

        $yearDetailsArray = $this->orgSearchRepository->getOrgSearch($yearDetailsSql);

        if (count($yearDetailsArray) > 0) {
            $yearId = $yearDetailsArray[0]['id'];
            $yearStartDate = $yearDetailsArray[0]['start_date'];
            $yearEndDate = $yearDetailsArray[0]['end_date'];
        } else {
            $yearId = -1;
            $yearStartDate = '';
            $yearEndDate = '';
        }
        

         // Get Current Academic Term


        $termQuery = "select id from org_academic_terms where organization_id = $organizationId and deleted_at is null and Date(now()) between start_date  and end_date ;";
        $termIds = $this->orgSearchRepository->getOrgSearch($termQuery);

        $termIdText = -1;
        $termIdArray = array();
        foreach ($termIds as $termId) {
            $termIdArray[] = $termId['id'];
        }

        if (count($termIdArray) > 0) {
            $termIdText = implode(",", $termIdArray);
        }
        

        return array(
            '[ORG_ID]' => $organizationId,
            '[EBI_METADATA_CLASSLEVEL_ID]' => $ebiMetadataId,
            '[CLASS_LEVELS]' => $classLevels,
            '[CURRENT_ACADEMIC_YEAR]' => $yearId,
            '[CURRENT_ACADEMIC_TERM]' => $termIdText,
            '[CURR_YEAR_START_DATE]' => $yearStartDate,
            '[CURR_YEAR_END_DATE]' => $yearEndDate
        );
    }


    /**
     * Returns the requested page of results for the requested custom search attributes, sorted as requested.
     *
     * Note: This function does not attempt the monumental task of making custom search follow standards.
     * At this point, I'm only trying to clean it up a bit to make it easier to follow as I'm standardizing sorting.
     *
     * @param SaveSearchDto $customSearchDTO
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $sortBy
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @return array
     */
    public function getCustomSearchResults($customSearchDTO, $loggedInUserId, $organizationId, $sortBy, $pageNumber, $recordsPerPage)
    {
        $searchAttributes = $customSearchDTO->getSearchAttributes();
        $studentQuery = $this->getStudentListBasedCriteria($searchAttributes, $organizationId, $loggedInUserId);

        $studentIds = $this->customSearchDAO->getStudentsForCustomSearch($organizationId, $studentQuery);

        $dataToReturn = $this->studentListService->getStudentListWithMetadata($studentIds, $loggedInUserId, $organizationId, $sortBy, $pageNumber, $recordsPerPage);
        $dataToReturn['search_attributes'] = $searchAttributes;

        return $dataToReturn;
    }


    /**
     * Returns a list of all students, including their ids and names, for the given custom search.
     * This list is used to set up a bulk action.
     *
     * @param SaveSearchDto $customSearchDTO
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $studentStatus
     * @return array
     */
    public function getCustomSearchStudentIdsAndNames($customSearchDTO, $loggedInUserId, $organizationId, $studentStatus)
    {
        $searchAttributes = $customSearchDTO->getSearchAttributes();
        $studentQuery = $this->getStudentListBasedCriteria($searchAttributes, $organizationId, $loggedInUserId);
        $studentIds = $this->customSearchDAO->getStudentsForCustomSearch($organizationId, $studentQuery);

        $dataToReturn = $this->studentListService->getStudentIdsAndNames($studentIds, $loggedInUserId);

        return $dataToReturn;
    }


    /**
     * Creates a job which will create a CSV of all students in a custom search.
     *
     * @param SaveSearchDto $customSearchDTO
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $sortBy
     * @return array
     */
    public function createCustomSearchJob($customSearchDTO, $loggedInUserId, $organizationId, $sortBy)
    {
        $job = new CustomSearchCSVJob();
        $job->args = [
            'custom_search_dto' => serialize($customSearchDTO),
            'faculty_id' => $loggedInUserId,
            'organization_id' => $organizationId,
            'sort_by' => $sortBy
        ];
        $this->resque->enqueue($job, true);
        return [SynapseConstant::DOWNLOAD_IN_PROGRESS_MESSAGE];
    }


    /**
     * Returns the results of the given custom search for use in creating a CSV.
     *
     * @param SaveSearchDto $customSearchDTO
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $studentStatus
     * @param string $sortBy
     * @return array
     */
    public function getCustomSearchResultsForCSV($customSearchDTO, $loggedInUserId, $organizationId, $studentStatus, $sortBy)
    {
        $searchAttributes = $customSearchDTO->getSearchAttributes();
        $studentQuery = $this->getStudentListBasedCriteria($searchAttributes, $organizationId, $loggedInUserId);
        $studentIds = $this->customSearchDAO->getStudentsForCustomSearch($organizationId, $studentQuery);

        $records = $this->studentListService->getStudentListWithAdditionalDataForCSV($studentIds, $loggedInUserId, $organizationId, $sortBy);

        return $records;
    }


    /**
     * Get list of students based on given criteria
     *
     * @param array $searchAttributes
     * @param int $organizationId
     * @param int $loggedInUserId
     * @param string $fromAuReport
     * @param bool $aggPermFlag
     * @return string
     */
    public function getStudentListBasedCriteria($searchAttributes, $organizationId, $loggedInUserId, $fromAuReport = '', $aggPermFlag = false)
    {
        $this->appendStudentFilter4MergedStudents($filters, $this->fetchStudentsAccessibleToFaculty($searchAttributes, $fromAuReport, $aggPermFlag));

        $this->appendStudentFilter($filters, $this->checkAndIncludeStudentIds($searchAttributes), false, null, true);

        $this->appendStudentFilter($filters, $this->checkAndIncludeRetentionCompletionSelection($organizationId, $searchAttributes), false, null, true);

        $this->appendStudentFilter($filters, $this->checkAndIncludeGroupSelection($searchAttributes), false, 'ogs', false, true);

        $this->appendFilter($filters, $this->checkAndIncludeRiskSelection($searchAttributes));

        $this->appendFilter($filters, $this->checkAndIncludeIntentToLeaveSelection($searchAttributes));

        $this->appendStudentFilter($filters, $this->checkAndIncludeStudentsWithContacts($searchAttributes), false, 'contact');

        $this->appendStudentFilter($filters, $this->checkAndIncludeStudentsHavingReferrals($searchAttributes), false, 'referral');

        $this->appendStudentFilters($filters, $this->checkAndIncludeCoursesSelection($searchAttributes, $organizationId));

        $this->appendStudentFilters($filters, $this->checkAndIncludeSurveyQuestions($searchAttributes));

        $this->appendStudentFilters($filters, $this->checkAndIncludeISQQuestions($searchAttributes));

        $this->appendStudentFilters($filters, $this->checkAndIncludeAcademicUpdatesSelection($searchAttributes, $loggedInUserId, $organizationId), 'au');

        $this->appendStudentFilters($filters, $this->checkAndIncludeStudentsWithSurveyStatus($searchAttributes, $organizationId));

        $this->appendStudentFilter($filters, $this->checkAndIncludeSurveyCohortSelection($searchAttributes));

        $this->appendStudentFilter($filters, $this->checkAndIncludeSurveyFilterSelection($searchAttributes), false, 'opsc', false, true);

        $this->appendStudentFilter($filters, $this->checkAndIncludeStaticListSelection($searchAttributes));

        $this->appendStudentFilters($filters, $this->checkAndIncludeEbiProfileItemSelection($searchAttributes), 'pem', true);

        $this->appendStudentFilters($filters, $this->checkAndIncludeISPSelection($searchAttributes), 'pom', true);

        $this->appendStudentFilters($filters, $this->checkAndIncludeStudentsWithSurveyFactors($searchAttributes, $loggedInUserId, $organizationId), 'pfc');

        // Combine all the individual filters

        $query = $this->combineStudentFilters($organizationId, $loggedInUserId, $filters);


        // Risk Indicator date as Search Param (Reports)

        $riskDate = '';

        if (isset($searchAttributes['risk_indicator_date']) && !empty($searchAttributes['risk_indicator_date'])) {
            $riskDate .= ' and DATE(p.risk_update_date) <= STR_TO_DATE( "' . $searchAttributes['risk_indicator_date'] . '","%Y-%m-%d")';
        }

        $query .= $riskDate;
        $query .= ' ';

        // Get the start and end date of the current academic year, and insert them in the query.
        $academicYear = $this->orgAcademicYearRepository->getCurrentAcademicYear($organizationId);

        if ($academicYear) {
            $startDate = $academicYear['start_date'];
            $endDate = $academicYear['end_date'];
        } else {
            $startDate = '';
            $endDate = '';
        }

        $query = str_replace("[CURR_YEAR_START_DATE]", $startDate, $query);
        $query = str_replace("[CURR_YEAR_END_DATE]", $endDate, $query);

        return $query;
    }

    private function appendStudentFilter(&$filters, $filter, $exclude = false, $filterType = null, $isStudentIds = false, $connectWithAnd = false)
    {
        if (!empty($filter)) {
            if ($filterType) {
                if ($connectWithAnd) {
                    $existCondition = " AND $filterType.person_id = p.id\n) ";
                } else {
                    $existCondition = " WHERE $filterType.person_id = p.id\n) ";
                }
            } else {
                $existCondition = " AND person_id = p.id\n) ";
            }

            if ($exclude) {
                $filters[] = " NOT EXISTS (\n" . $filter . $existCondition;
            } else {
                if ($isStudentIds) {
                    $filters[] = " EXISTS (\n SELECT person_id FROM org_person_student WHERE deleted_at is NULL AND person_id in (" . $filter . ")" . $existCondition;
                } else {
                    $filters[] = " EXISTS (\n" . $filter . $existCondition;
                }
            }

            return true;
        }

        return false;
    }

    private function appendStudentFilter4MergedStudents (&$filters, $filter, $exclude=false) {

        if ( !empty( $filter) ) {

            if ( $exclude ) {

                $filters[] = " NOT EXISTS (\n" . $filter . " WHERE student_id = p.id\n) ";
            } else {

                $filters[] = " EXISTS (\n" . $filter . " WHERE student_id = p.id\n) ";
            }

            return true;
        }

        return false;
    }

    private function appendStudentFilters(&$filters, $filterArray, $filterType = null, $connectWithAnd = false)
    {
        if (empty($filterArray)) {
            return $filters;
        }

        foreach ($filterArray as $filter) {
            $exclude = isset($filter['exclude']);

            if ($exclude) {
                $filter = $filter['filter'];
            }

            $this->appendStudentFilter($filters, $filter, $exclude, $filterType, false, $connectWithAnd);
        }

        return $filters;
    }

    private function appendFilter (&$filters, $filter) {

        if ( !empty( $filter) ) {

            $filters[] = $filter;

            return true;
        }

        return false;
    }

    /**
     * Combine filters to create the final students filter query
     *
     * @param int $organizationId
     * @param int $loggedInUserId
     * @param array $filters
     * @return mixed
     */
    private function combineStudentFilters($organizationId, $loggedInUserId, &$filters)
    {
        $studentFilter = implode("\n AND ", $filters);

        $studentFilter = str_replace('[FACULTY_ID]', $loggedInUserId, $studentFilter);

        $studentFilter = str_replace('[ORG_ID]', $organizationId, $studentFilter);

        return $studentFilter;
    }

    /**
     * Builds the base query for Custom Searches
     *
     * @param null|array $searchAttributes
     * @param string $academicUpdateReportIndicatorString
     * @param bool $aggregatePermissionFlag
     * @return string
     */
    private function fetchStudentsAccessibleToFaculty($searchAttributes = null, $academicUpdateReportIndicatorString = '', $aggregatePermissionFlag = false)
    {
        $academicUpdatePermissionCondition = '';
        $riskIndicatorCondition = '';
        $intentToLeaveIndicatorCondition = '';
        if (!$aggregatePermissionFlag) {
            $individualPermissionCondition = 'AND OPS.accesslevel_ind_agg = 1 ';
        }

        if ($academicUpdateReportIndicatorString == 'auReport') {
            $academicUpdatePermissionCondition = ' AND ( OPS.create_view_academic_update = 1 or OPS.view_all_academic_update_courses = 1 or OPS.view_courses = 1 ) ';
        }

        if ($searchAttributes !== null) {
            if (!empty($searchAttributes['risk_indicator_ids'])) {
                $riskIndicatorCondition = ' AND OPS.risk_indicator = 1';
            }

            if (!empty($searchAttributes['intent_to_leave_ids'])) {
                $intentToLeaveIndicatorCondition = ' AND OPS.intent_to_leave = 1';
            }

            $retentionCompletionCondition = '';
            if(!empty($searchAttributes['retention_completion'])){
                $retentionCompletionCondition = ' AND OPS.retention_completion = 1';
            }
        }

        $isActiveCondition = $this->checkAndIncludeStudentsByActiveStatus($searchAttributes);
        $isParticipatingCondition = $this->checkAndIncludeStudentsByParticipation($searchAttributes);

        // Base query for Custom Search and Reports
        $baseQueryString = "
            SELECT DISTINCT
                merged.student_id
            FROM
                (
                    SELECT
                        ofspm.student_id,
                        ofspm.permissionset_id
                    FROM
                        org_faculty_student_permission_map ofspm
                    WHERE
                        ofspm.org_id = [ORG_ID]
                        AND ofspm.faculty_id = [FACULTY_ID]
                ) AS merged
                    INNER JOIN
                org_permissionset OPS
                        ON OPS.id = merged.permissionset_id
                        AND OPS.deleted_at IS NULL
                        $individualPermissionCondition
                        $academicUpdatePermissionCondition
                        $riskIndicatorCondition
                        $intentToLeaveIndicatorCondition
                        $retentionCompletionCondition
                        $isActiveCondition
                        $isParticipatingCondition
        ";

        return $baseQueryString;
    }

    /**
     * checks the student_status attribute in the search filter and add in the is_active condition to the query
     *
     * @param array $searchAttributes
     * @return string
     */
    private function checkAndIncludeStudentsByActiveStatus($searchAttributes)
    {
        $isActiveFilter = "";
        $criteria = [];
        if (!empty($searchAttributes['student_status'])) {

            $studentStatusInputArray = $searchAttributes['student_status'];
            $studentStatusSelectedArray = array_unique($studentStatusInputArray['status_value']);
            $orgAcademicYearIds = $studentStatusInputArray['org_academic_year_id'];

            $isStatusActiveSelected = in_array(1, $studentStatusSelectedArray);
            $isStatusInactiveSelected = in_array(0, $studentStatusSelectedArray);

            $applyIsActiveFilter = true;

            //If there are no organization academic year ids present, do not apply the filter.
            if (empty($orgAcademicYearIds)) {
                $applyIsActiveFilter = false;
            }

            if ($isStatusActiveSelected && $isStatusInactiveSelected) {
                //Include both active and inactive students for the given year(s).
                //This filter's year value(s) and the participating filter's year value(s) are completely independent of each other.
                $activeStatusCondition = "";
            } elseif ($isStatusActiveSelected) {
                //The user wants to filter on just active students. Set the status value.
                $activeStatusCondition = " AND opsy.is_active = 1 ";
            } elseif ($isStatusInactiveSelected) {
                //The user wants to filter on just inactive students. Set the status value.
                $activeStatusCondition = " AND opsy.is_active = 0 ";
            } else {
                //The user does not want to filter on active or inactive students. Do not apply the filter.
                $applyIsActiveFilter = false;
            }

            if ($applyIsActiveFilter) {
                foreach ($orgAcademicYearIds as $orgAcademicYearId) {
                    $statusQuery = "
                                SELECT
                                  1
                                FROM
                                  org_person_student_year opsy
                                WHERE
                                  opsy.person_id = merged.student_id
                                  AND opsy.deleted_at IS NULL
                                  $activeStatusCondition
                                  AND opsy.org_academic_year_id = $orgAcademicYearId
                                ";

                    $criteria[] = " EXISTS ( $statusQuery )";
                }
            }
            if (!empty($criteria)) {
                $isActiveFilter = "AND " . implode(" AND ", $criteria);
            }
        }
        return $isActiveFilter;
    }

    /**
     * checks the participating attribute in the search filter and add in the query conditions accordingly
     *
     * @param array $searchAttributes
     * @return string
     */
    private function checkAndIncludeStudentsByParticipation($searchAttributes)
    {
        $participatingFilter = "";
        $criteria = [];
        if (!empty($searchAttributes['participating'])) {

            $studentParticipatingInputArray = $searchAttributes['participating'];
            $studentParticipatingSelectedArray = array_unique($studentParticipatingInputArray['participating_value']);
            $orgAcademicYearIds = $studentParticipatingInputArray['org_academic_year_id'];

            $isParticipatingSelected = in_array(1, $studentParticipatingSelectedArray);
            $isNonParticipatingSelected = in_array(0, $studentParticipatingSelectedArray);

            foreach ($orgAcademicYearIds as $orgAcademicYearId) {
                $existsQuery = "SELECT
                                  1
                                FROM
                                  org_person_student_year opsy
                                WHERE
                                  opsy.person_id = merged.student_id
                                  AND opsy.org_academic_year_id = $orgAcademicYearId
                                  AND opsy.deleted_at IS NULL
                                  ";

                if ($isParticipatingSelected && $isNonParticipatingSelected) {
                    // Do not append the filter for participation.
                } elseif ($isParticipatingSelected) {
                    // Include the filter for only participating students
                    $criteria[] = " EXISTS ( $existsQuery )";
                } elseif ($isNonParticipatingSelected) {
                    // Include the filter for only non-participating students
                    $criteria[] = " NOT EXISTS ( $existsQuery )";
                } else {
                    // Do not append the filter for participation.
                }
            }
            if (!empty($criteria)) {
                $participatingFilter = "AND " . implode(' AND ', $criteria);
            }
        }
        return $participatingFilter;
    }

    /**
     * Check and include students with contacts
     *
     * @param array $searchAttributes
     * @return string
     */public function checkAndIncludeStudentsWithContacts ($searchAttributes)
    {
    	if ( !isset( $searchAttributes['contact_types']) || empty( $searchAttributes['contact_types']) ) {

    		return '';
    	}


    	switch ( strtolower( $searchAttributes['contact_types']) ) {
    		case "interaction":
    			$filter = " AND ct.parent_contact_types_id = 1 ";
    			break;
    
    		case "non-interaction":
    			$filter = " AND ct.parent_contact_types_id = 2 ";
    			break;
    
    		case "all":
    			$filter = " AND ct.parent_contact_types_id in (1, 2) ";
    			break;
    
    		default:
    			return '';
    	}
    
    	$filterQueries = array( 

            ' /* Select students with selected contacttype created by the faculty [FACULTY_ID] */
    
                SELECT
                    DISTINCT c.person_id_student person_id
                FROM
                    contacts c
                    INNER JOIN contact_types ct
                        ON ct.id = c.contact_types_id
                WHERE
                    c.organization_id = [ORG_ID]
    	            AND DATE(c.contact_date) BETWEEN "[CURR_YEAR_START_DATE]" AND "[CURR_YEAR_END_DATE]"
                    AND c.deleted_at IS NULL
                    [FILTER_CONTACT_TYPES]
                    AND c.person_id_faculty = [FACULTY_ID]
    	    ',

            ' /* Select students with selected contacttype shared with public and faculty having public_view */
    	        
                SELECT
                    distinct c.person_id_student person_id
                FROM
                    contacts c
                    INNER JOIN contact_types ct
                        ON ct.id = c.contact_types_id
                WHERE
                    c.organization_id = [ORG_ID]
    	            AND DATE(c.contact_date) BETWEEN "[CURR_YEAR_START_DATE]" AND "[CURR_YEAR_END_DATE]"
                    AND c.deleted_at IS NULL
                    [FILTER_CONTACT_TYPES]    	    
        	        AND c.person_id_faculty != [FACULTY_ID]
        	        AND c.access_public = 1
    	            AND ( /* groups or courses */

                    c.person_id_student IN 
        	           (
                        /* Students associated with Faculty via Groups */
    	    
                        SELECT 
                            s.person_id AS student_id
                        FROM
                            org_group_students AS s
                                JOIN
                            org_group_tree ogt ON s.org_group_id = ogt.descendant_group_id
                                AND ogt.deleted_at IS NULL
                                JOIN
                            org_group_faculty AS f ON f.org_group_id = ogt.ancestor_group_id
                                AND f.deleted_at IS NULL
                        WHERE
                            s.organization_id = [ORG_ID]
                            AND s.deleted_at IS NULL
                            AND f.person_id = [FACULTY_ID]
                            AND f.deleted_at IS NULL
    	        	        AND f.org_permissionset_id IN (
                                SELECT 
                                    org_permissionset_id
                                FROM
                                    org_permissionset_features opsf
                                WHERE
                                    opsf.organization_id = [ORG_ID]
                                    AND opsf.deleted_at IS NULL
                                    AND opsf.public_view = 1 
                            ) 
                        /* Students associated with Faculty via Groups */
    	               )
        	    
                   OR c.person_id_student IN
    	               (
    	               /* Students associated with Faculty via Courses */
    	    
    	               SELECT
                            DISTINCT ocs.person_id
                        FROM
                            org_course_student ocs
                            INNER JOIN org_course_faculty ocf
                                ON ocf.org_courses_id = ocs.org_courses_id
                                    AND ocf.organization_id = [ORG_ID]
                                    AND ocf.person_id = [FACULTY_ID]
                                    AND ocf.deleted_at IS NULL
    	                    INNER JOIN org_courses oc
    	                        ON oc.id = ocf.org_courses_id
                            INNER JOIN org_academic_terms as oat
                                ON oat.id = oc.org_academic_terms_id 
                                    AND oat.deleted_at IS NULL
                                    AND date(now()) 
                                        BETWEEN oat.start_date
                                            AND oat.end_date
    	               WHERE
                            ocs.organization_id = [ORG_ID]
                            AND ocs.deleted_at IS NULL
                            AND ocf.org_permissionset_id IN (
                                SELECT 
                                    org_permissionset_id
                                FROM
                                    org_permissionset_features opsf
                                WHERE
                                    opsf.organization_id = [ORG_ID]
                                    AND opsf.deleted_at IS NULL
                                    AND opsf.public_view = 1 
                            )
    	               /* Students associated with Faculty via Courses - ends */
                       )
                    ) /* groups or courses */
    	    ',

            ' /* Select students with selected contacttype shared with team faculty is in and faculty having team_view */
    	        
                SELECT
                    DISTINCT c.person_id_student person_id
                FROM
                    contacts c
                    INNER JOIN contact_types ct
                       ON ct.id = c.contact_types_id
    	            INNER JOIN contacts_teams cteams
    	               ON cteams.contacts_id = c.id
                WHERE
                    c.organization_id = [ORG_ID]
    	            AND DATE(c.contact_date) BETWEEN "[CURR_YEAR_START_DATE]" AND "[CURR_YEAR_END_DATE]"
                    AND c.deleted_at IS NULL
                    [FILTER_CONTACT_TYPES]
    	    
    	       /* contacts created by other faculties with team share */
    	    
    	        AND c.person_id_faculty != [FACULTY_ID]
    	        AND c.access_team = 1
    	        AND cteams.teams_id IN
    	           (
    	               /* check to ensure faculty is part of the team */
    	               SELECT 
    	                   teams_id
    	               FROM
    	                   team_members
    	               WHERE
    	                   person_id = [FACULTY_ID]
    	                   AND organization_id = [ORG_ID]
    	                   AND deleted_at IS NULL
    	           )
    	        AND ( /* groups or courses */

                c.person_id_student IN 
    	           (
                        /* Students associated with Faculty via Groups */
                        SELECT 
                            s.person_id AS student_id
                        FROM
                            org_group_students AS s
                                JOIN
                            org_group_tree ogt ON s.org_group_id = ogt.descendant_group_id
                                AND ogt.deleted_at IS NULL
                                JOIN
                            org_group_faculty AS f ON f.org_group_id = ogt.ancestor_group_id
                                AND f.deleted_at IS NULL
                        WHERE
                            s.organization_id = [ORG_ID]
                            AND s.deleted_at IS NULL
                            AND f.person_id = [FACULTY_ID]
                            AND f.deleted_at IS NULL
    	        	        AND f.org_permissionset_id IN (
                                SELECT 
                                    org_permissionset_id
                                FROM
                                    org_permissionset_features opsf
                                WHERE
                                    opsf.organization_id = [ORG_ID]
                                    AND opsf.deleted_at IS NULL
                                    AND opsf.team_view = 1 
                            ) 
                        /* Students associated with Faculty via Groups */
	               )
    	    
               OR c.person_id_student IN
	               (
    	               /* Students associated with Faculty via Courses */
    	    
    	               SELECT
                            DISTINCT ocs.person_id
                        FROM
                            org_course_student ocs
                            INNER JOIN org_course_faculty ocf
                                ON ocf.org_courses_id = ocs.org_courses_id
                                    AND ocf.organization_id = [ORG_ID]
                                    AND ocf.person_id = [FACULTY_ID]
                                    AND ocf.deleted_at IS NULL
    	                    INNER JOIN org_courses oc
    	                        ON oc.id = ocf.org_courses_id
                            INNER JOIN org_academic_terms as oat
                                ON oat.id = oc.org_academic_terms_id 
                                    AND oat.deleted_at is null
                                    AND date(now()) 
                                        BETWEEN oat.start_date
                                            AND oat.end_date
                        WHERE
                            ocs.organization_id = [ORG_ID]
                            AND ocs.deleted_at IS NULL
                            AND ocf.org_permissionset_id IN (
                                SELECT 
                                    org_permissionset_id
                                FROM
                                    org_permissionset_features opsf
                                WHERE
                                    opsf.organization_id = [ORG_ID]
                                    AND opsf.deleted_at IS NULL
                                    AND opsf.team_view = 1 
                            )
    	               /* Students associated with Faculty via Courses - ends */
                   )
                ) /* groups or courses */
    	    '
        );

        $filters = array();

        foreach ($filterQueries as $query) {

            $filters[] = str_replace('[FILTER_CONTACT_TYPES]', $filter, $query);
        }

        return 'select person_id from (' . implode('
    	    UNION ALL ', $filters) . ') contact';
    }
    /**
     * Include students having referrals
     *
     * @param array $searchAttributes
     * @return string
     */
    public function checkAndIncludeStudentsHavingReferrals ($searchAttributes)
    {
    	if ( !isset( $searchAttributes['referral_status']) || empty( $searchAttributes['referral_status']) ) {

    		return '';
    	}


    
    	switch ($searchAttributes['referral_status']) {
    
    		case "open":
    			$filter = " AND r.status = 'O' ";
    			break;
    
    		case "closed":
    			$filter = " AND r.status = 'C' ";
    			break;
    
    		case "all":
    			$filter = " AND r.status in ('O','C') ";
    			break;
    
    		default:
    			return '';
    	}

        $filterQueries = array(

            ' /* Select students with referrals in specified status 
                 either created by / assigned to / as an interested party 
                 to the faculty [FACULTY_ID] */

                select
                   distinct r.person_id_student person_id
                from
                    referrals r
                left join
                    referrals_interested_parties rip
                    on rip.referrals_id = r.id              
                where
                    r.organization_id = [ORG_ID]
                    AND DATE(r.created_at) BETWEEN "[CURR_YEAR_START_DATE]" AND "[CURR_YEAR_END_DATE]"
                    and r.deleted_at is null
                    [FILTER_REFERRAL_STATUS]
                    AND (r.person_id_faculty = [FACULTY_ID] 
                            OR r.person_id_assigned_to = [FACULTY_ID]
                            OR rip.person_id = [FACULTY_ID])
            ',

            ' /* Select students with referrals in specified status 
                 created by others and shared with public and faculty having public_view */
                
                select
                    distinct r.person_id_student
                from
                    referrals r
                where
                    r.organization_id = [ORG_ID]
                    AND DATE(r.created_at) BETWEEN "[CURR_YEAR_START_DATE]" AND "[CURR_YEAR_END_DATE]"
                    and r.deleted_at is null
                    [FILTER_REFERRAL_STATUS]
            
                    and r.person_id_faculty != [FACULTY_ID]
                    and r.access_public = 1

                    and ( /* groups or courses */

                    r.person_id_student in 
                       (
                        /* Students associated with Faculty via Groups */
            
                        select
                            s.person_id as student_id
                        from 
                            org_group_students as s
                            JOIN org_group_tree AS ogt ON s.org_group_id = ogt.descendant_group_id
                                AND ogt.deleted_at IS NULL
                            JOIN org_group_faculty AS f
                                ON f.org_group_id = ogt.ancestor_group_id AND f.deleted_at IS NULL
                        where
                            s.organization_id = [ORG_ID]
                            and s.deleted_at is null
                            and f.person_id = [FACULTY_ID]
                            and f.deleted_at is null
                            and f.org_permissionset_id in (
                                select 
                                    org_permissionset_id
                                from
                                    org_permissionset_features opsf
                                where
                                    opsf.organization_id = [ORG_ID]
                                    and opsf.deleted_at is null
                                    and opsf.public_view = 1 
                            ) 
                        /* Students associated with Faculty via Groups */
                       )
                
                   or r.person_id_student in
                       (
                       /* Students associated with Faculty via Courses */
            
                       select
                            distinct ocs.person_id
                        from
                            org_course_student ocs
                            inner join org_course_faculty ocf
                                on ocf.org_courses_id = ocs.org_courses_id
                                    and ocf.organization_id = [ORG_ID]
                                    and ocf.person_id = [FACULTY_ID]
                                    and ocf.deleted_at is null
                            inner join org_courses oc
                                on oc.id = ocf.org_courses_id
                            inner join org_academic_terms as oat
                                on oat.id = oc.org_academic_terms_id 
                                    and oat.deleted_at is null
                                    and date(now()) 
                                        between oat.start_date
                                            and oat.end_date
                       where
                            ocs.organization_id = [ORG_ID]
                            and ocs.deleted_at is null
                            and ocf.org_permissionset_id in (
                                select 
                                    org_permissionset_id
                                from
                                    org_permissionset_features opsf
                                where
                                    opsf.organization_id = [ORG_ID]
                                    and opsf.deleted_at is null
                                    and opsf.public_view = 1 
                            )
                       /* Students associated with Faculty via Courses - ends */
                       )
                    ) /* groups or courses */
            ',

            ' /* Select students with referrals in specified status 
                 created by others and shared with team faculty is in and faculty having team_view */
                
                select
                    distinct r.person_id_student person_id
                from
                    referrals r
                    inner join referrals_teams rteams
                       on rteams.referrals_id = r.id
                where
                    r.organization_id = [ORG_ID]
                    AND DATE(r.created_at) BETWEEN "[CURR_YEAR_START_DATE]" AND "[CURR_YEAR_END_DATE]"
                    and r.deleted_at is null
                    [FILTER_REFERRAL_STATUS]

               /* contacts created by other faculties with team share */
            
                and r.person_id_faculty != [FACULTY_ID]
                and r.access_team = 1
                and rteams.teams_id in
                   (
                       /* check to ensure faculty is part of the team */
                       select 
                           teams_id
                       from
                           team_members
                       where 
                           person_id = [FACULTY_ID]
                           and organization_id = [ORG_ID]
                           and deleted_at is null
                   )
                and ( /* groups or courses */

                r.person_id_student in 
                   (
                        /* Students associated with Faculty via Groups */
            
                        select
                            s.person_id as student_id
                        from 
                            org_group_students as s
                            JOIN org_group_tree AS ogt ON s.org_group_id = ogt.descendant_group_id
                                AND ogt.deleted_at IS NULL
                            JOIN org_group_faculty AS f
                                ON f.org_group_id = ogt.ancestor_group_id
                                   AND f.deleted_at IS NULL
                        where
                            s.organization_id = [ORG_ID]
                            and s.deleted_at is null
                            and f.person_id = [FACULTY_ID]
                            and f.deleted_at is null
                            and f.org_permissionset_id in (
                                select 
                                    org_permissionset_id
                                from
                                    org_permissionset_features opsf
                                where
                                    opsf.organization_id = [ORG_ID]
                                    and opsf.deleted_at is null
                                    and opsf.team_view = 1 
                            ) 
                        /* Students associated with Faculty via Groups */
                   )
            
               or r.person_id_student in
                   (
                       /* Students associated with Faculty via Courses */
            
                       select
                            distinct ocs.person_id
                        from
                            org_course_student ocs
                            inner join org_course_faculty ocf
                                on ocf.org_courses_id = ocs.org_courses_id
                                    and ocf.organization_id = [ORG_ID]
                                    and ocf.person_id = [FACULTY_ID]
                                    and ocf.deleted_at is null
                            inner join org_courses oc
                                on oc.id = ocf.org_courses_id
                            inner join org_academic_terms as oat
                                on oat.id = oc.org_academic_terms_id 
                                    and oat.deleted_at is null
                                    and date(now()) 
                                        between oat.start_date
                                            and oat.end_date
                        where
                            ocs.organization_id = [ORG_ID]
                            and ocs.deleted_at is null
                            and ocf.org_permissionset_id in (
                                select 
                                    org_permissionset_id
                                from
                                    org_permissionset_features opsf
                                where
                                    opsf.organization_id = [ORG_ID]
                                    and opsf.deleted_at is null
                                    and opsf.team_view = 1 
                            )
                       /* Students associated with Faculty via Courses - ends */
                   )
                ) /* groups or courses */
            '
        );

        $filters = array();

        foreach ($filterQueries as $query) {

            $filters[] = str_replace('[FILTER_REFERRAL_STATUS]', $filter, $query);
        }

        return 'select person_id from (' . implode('
    	    UNION ALL ', $filters) . ') referral';
    }
        /**
     * Include student Ids
     *
     * @param array $searchAttributes
     * @return string
     */
    private function checkAndIncludeStudentIds ($searchAttributes)
        {
        if ( isset($searchAttributes['student_ids']) && !empty($searchAttributes['student_ids'])) {


             // Student IDs will be a comma separated value

            return $searchAttributes['student_ids'];
        }

        return '';
    }


    /**
     * Include Risk selection in the query
     *
     * @param array $searchAttributes
     * @return string
     */
    public function checkAndIncludeRiskSelection($searchAttributes)
    {
        if (isset($searchAttributes['risk_indicator_ids']) && !empty($searchAttributes['risk_indicator_ids'])) {
            $grayRisk = $this->riskLevelsRepository->findOneBy([
                'riskText' => 'gray'
            ]);
            $riskIndicators = explode(',', $searchAttributes['risk_indicator_ids']);
            if (is_object($grayRisk) && in_array($grayRisk->getId(), $riskIndicators)) {
                $riskSQL = ' /* Filter to select student with specified risk */ ( ' . $this->searchUtilityService->makeSqlQuery($searchAttributes['risk_indicator_ids'], ' p.risk_level') . ' OR p.risk_level IS NULL )';
            } else {
                $riskSQL = ' /* Filter to select student with specified risk */ ' . $this->searchUtilityService->makeSqlQuery($searchAttributes['risk_indicator_ids'], ' p.risk_level');
            }

        } else {
            $riskSQL = '';
        }

        return $riskSQL;
    }

    /**
     * Include IntentToLeave selection
     *
     * @param array $searchAttributes
     * @return string
     */
    private function checkAndIncludeIntentToLeaveSelection($searchAttributes)
    {

        if (isset($searchAttributes['intent_to_leave_ids']) && !empty($searchAttributes['intent_to_leave_ids'])) {

            return ' /* Filter to select student with specified intent to leave */
    	       
    	   ' . $this->searchUtilityService->makeSqlQuery($searchAttributes['intent_to_leave_ids'], ' p.intent_to_leave');
        }

        return '';
    }

    /**
     * Include group selection
     *
     * @param array $searchAttributes
     * @return string
     */
    private function checkAndIncludeGroupSelection ($searchAttributes)
    {
        if (isset($searchAttributes['group_ids']) && !empty( $searchAttributes['group_ids'])) {
            
    	   return ' /* Filter to select student in specified groups */
    	       
    	   ' . str_replace('[GROUP_IDS]', $searchAttributes['group_ids'], Helper::SQL_MY_GROUP_STUDENTS);
        }

        return '';
    }

    /**
     * include course filters to the sql
     *
     * @param array $searchAttributes
     * @param int $organizationId
     * @return array
     */
    private function checkAndIncludeCoursesSelection($searchAttributes, $organizationId)
    {
        if (!isset($searchAttributes[SearchConstant::COURSES]) || empty($searchAttributes[SearchConstant::COURSES])) {

            return [];
        }

        $coursesSelection = $searchAttributes[SearchConstant::COURSES];
        $filters = [];
        $studentIdsWithinAllMatchingCourses = [];
        $filterCriteriaRequired = false;
        $arrayIntersectRequired = false;
        foreach ($coursesSelection as $course) {
            $courseIdArray = [];
            $courseStudentIds = [];

            if (!isset($course['year_name']) || empty($course['year_name'])) {

                continue;
            }

            if (!isset($course['term_code']) || empty($course['term_code'])) {

                continue;
            }

            if (!isset($course['term_name']) || empty($course['term_name'])) {

                continue;
            }

            if (!isset($course['dept_code']) || empty($course['dept_code'])) {

                continue;
            }

            if (!isset($course['subject_code']) || empty($course['subject_code'])) {

                continue;
            }

            if (!isset($course['course_number']) || empty($course['course_number'])) {

                continue;
            }

            if (!isset($course['course_name']) || empty($course['course_name'])) {

                continue;
            }

            // filter needs to be added even if there is no course for request $course block or even when we get course there is no student for the selected course
            // once set true we will need to have ocs.person_id IN check to avoid getting all the students list
            $filterCriteriaRequired = true;

            // get orgAcademicYear
            $orgAcademicYearObject = $this->orgAcademicYearRepository->findOneBy([
                'organization' => $organizationId,
                'yearId' => $course['year_id']
            ]);
            $orgAcademicYearId = ($orgAcademicYearObject) ? $orgAcademicYearObject->getId() : null;

            // get orgAcademicTerm
            $orgAcademicTermObject = $this->orgAcademicTermRepository->findOneBy([
                'organization' => $organizationId,
                'orgAcademicYearId' => $orgAcademicYearId,
                'termCode' => $course['term_code'],
                'name' => $course['term_name']
            ]);
            $orgAcademicTermId = ($orgAcademicTermObject) ? $orgAcademicTermObject->getId() : null;

            // get all course objects per $course block in search request
            if (count($course['section_numbers']) > 0) {
                // $course['section_numbers'] is an array - its similar to IN() sql - OR logic
                $orgCoursesObjectArray = $this->orgCoursesRepository->findBy([
                    'orgAcademicYear' => $orgAcademicYearId,
                    'orgAcademicTerms' => $orgAcademicTermId,
                    'deptCode' => $course['dept_code'],
                    'subjectCode' => $course['subject_code'],
                    'courseNumber' => $course['course_number'],
                    'courseName' => $course['course_name'],
                    'sectionNumber' => $course['section_numbers']
                ]);
            } else {
                $orgCoursesObjectArray = $this->orgCoursesRepository->findBy([
                    'orgAcademicYear' => $orgAcademicYearId,
                    'orgAcademicTerms' => $orgAcademicTermId,
                    'deptCode' => $course['dept_code'],
                    'subjectCode' => $course['subject_code'],
                    'courseNumber' => $course['course_number'],
                    'courseName' => $course['course_name']
                ]);
            }

            // get all course ids which is actually the course section ids from the $orgCoursesObject
            if ($orgCoursesObjectArray) {
                foreach ($orgCoursesObjectArray as $orgCourseObject) {
                    $orgCourseId = $orgCourseObject->getId();
                    $courseIdArray[] = $orgCourseId;
                }
            }

            // get all students from orgCourseStudent for the $courseIdArray - its similar to IN() sql - OR logic
            $orgCourseStudentsObjectArray = $this->orgCourseStudentRepository->findBy([
                'course' => $courseIdArray
            ]);

            // get the students from $orgCourseStudentsObject
            if ($orgCourseStudentsObjectArray) {
                foreach ($orgCourseStudentsObjectArray as $orgCourseStudentsObject) {
                    if ($orgCourseStudentsObject->getPerson()) {
                        $orgCourseStudentId = $orgCourseStudentsObject->getPerson()->getId();
                        $courseStudentIds[] = $orgCourseStudentId;
                    }
                }
            }

            $courseStudentIds = array_unique($courseStudentIds);

            if ($arrayIntersectRequired) {
                // get the intersection of student ids from the new array $courseStudentIds and common students array $studentIdsWithinAllMatchingCourses
                $studentIdsWithinAllMatchingCourses = array_intersect($courseStudentIds, $studentIdsWithinAllMatchingCourses);
            } else {
                // first time $studentIdsWithinAllMatchingCourses will be empty so directly assign
                $studentIdsWithinAllMatchingCourses = $courseStudentIds;
                //set the flag true so that next time in loop intersect is used
                $arrayIntersectRequired = true;
            }
        }

        if ($filterCriteriaRequired) {
            // $studentIdsWithinAllMatchingCourses array has common students who are in any section of Course 1 AND who are also in any section of Course 2 (n number of courses)
            if (count($studentIdsWithinAllMatchingCourses)) {
                $filterCriteria = '
                (
                 ocs.person_id IN(' . implode(',', $studentIdsWithinAllMatchingCourses) . ')
                )
            ';
            } else {
                $filterCriteria = '
                (
                 ocs.person_id IN(NULL)
                )
            ';
            }

            $filters[] = $filterCriteria;
        }

        if (count($filters) == 0) {

            return [];
        }

        $filterQuery =
            ' /* Query to select students enrolled in the specified course / course-sections */
            
            select
                distinct person_id
            from
                org_course_student ocs
                inner join org_courses oc
                    on oc.id = ocs.org_courses_id
            where
                ocs.organization_id = [ORG_ID]
                and ocs.deleted_at is null
                and oc.org_academic_year_id = (

                    /* Academic Year Check - Course should be within the current academic year of the Campus */
        
                    select
                        id
                    from
                        org_academic_year oay
                    where
                        oay.organization_id = [ORG_ID]
                        and oay.deleted_at is null
                        and date(now()) between date(oay.start_date) and date(oay.end_date)
        
                    /* Academic Year Check - ends */
                )
                
                /* user selected courses */
                
                and ( [COURSE_SELECTIONS] )
                
                /* Check permission of Staff to view course */
                and (
                
                    oc.id in (
                
                        /* list of courses where the user is the faculty - need not have explicit view course permission */
                
                        select distinct
                            org_courses_id
                        from
                            org_course_faculty ocf
    	                    inner join org_courses oc
    	                        on oc.id = ocf.org_courses_id
                            inner join org_academic_terms as oat
                                on oat.id = oc.org_academic_terms_id 
                                    and oat.deleted_at is null
                                    and date(now()) 
                                        between oat.start_date 
                                            and oat.end_date
                        where
                            ocf.organization_id = [ORG_ID]
                            and ocf.person_id = [FACULTY_ID]
                            and ocf.deleted_at is null
                            and ocf.org_permissionset_id is not null
                
                        /* list of courses where the user is the faculty - ends */
                    )

                    or ocs.person_id in (

                        /* list of students where the user is the faculty and having view course permission */
                
                        select
                            distinct ocs.person_id
                        from
                            org_course_student ocs
                            inner join org_course_faculty ocf
                                on ocf.org_courses_id = ocs.org_courses_id
                                    and ocf.organization_id = [ORG_ID]
                                    and ocf.person_id = [FACULTY_ID]
                                    and ocf.deleted_at is null
    	                    inner join org_courses oc
    	                        on oc.id = ocf.org_courses_id
                            inner join org_academic_terms as oat
                                on oat.id = oc.org_academic_terms_id 
                                    and oat.deleted_at is null
                                    and date(now()) 
                                        between oat.start_date 
                                            and oat.end_date
                        where
                            ocs.organization_id = [ORG_ID]
                            and ocs.deleted_at is null
                            and ocf.org_permissionset_id in (
                                select 
                                    id
                                from
                                    org_permissionset ops
                                where
                                    ops.organization_id = [ORG_ID]
                                    and deleted_at is null
                                    and view_courses = 1 
                            )
                
                        /* list of students where the user is the faculty and having view course permission - ends */
                    )
                
                    or ocs.person_id in (
                            
                        /* list of students where faculty has view course permission */
                
                        select
                            distinct s.person_id
                        from 
                            org_group_students as s
                            JOIN org_group_tree AS ogt ON s.org_group_id = ogt.descendant_group_id
                                AND ogt.deleted_at IS NULL
                            JOIN org_group_faculty AS f
                                ON f.org_group_id = ogt.ancestor_group_id AND f.deleted_at IS NULL
                        where
                            s.organization_id = [ORG_ID]
                            and s.deleted_at is null
                            and f.person_id = [FACULTY_ID]
                            and f.org_permissionset_id in (
                                select 
                                    id
                                from
                                    org_permissionset ops
                                where
                                    ops.organization_id = [ORG_ID]
                                    and deleted_at is null 
                                    and view_courses = 1
                            )
                            
                        /* list of students where faculty has view course permission - ends */
                    )                 
                )

                /* Check permission of Staff to view course - ends */
        ';

        $queries = array();

        foreach ($filters as $filter) {

            $filterCriteria = str_replace('[COURSE_SELECTIONS]', $filter, $filterQuery);

            $filterCriteria .= "\n /* Query to select students enrolled in all of the specified course / course-sections - ends here */ ";

            $queries[] = $filterCriteria;
        }

        return $queries;
    }

    /**
     * If the specified filters include academic update information, include the sub-queries to get the students pertinent to the academic update filter.
     *
     * @param array $searchAttributes
     * @param int $loggedInUserId
     * @param int $organizationId
     * @return array
     */
    public function checkAndIncludeAcademicUpdatesSelection($searchAttributes, $loggedInUserId, $organizationId)
    {
        if (!isset($searchAttributes['academic_updates']) || empty($searchAttributes['academic_updates'])) {
            return [];
        }else{
            $academicUpdateSearchAttributes = $searchAttributes['academic_updates'];
        }

        //Get the appropriate SQL for the filters based on the search attributes.
        $filters = [' ar.update_date IS NOT NULL '];
        $this->appendFilter($filters, $this->checkAndIncludeGradesInAU($academicUpdateSearchAttributes));
        $this->appendFilter($filters, $this->checkAndIncludeAbsencesInAU($academicUpdateSearchAttributes));
        $this->appendFilter($filters, $this->checkAndIncludeFailureRiskInAU($academicUpdateSearchAttributes));

        //If there are term IDs, include them in the filters.
        if (!empty($academicUpdateSearchAttributes['term_ids'])) {
            $filters[] = '( oc.org_academic_terms_id IN (' . $academicUpdateSearchAttributes['term_ids'] . ') )';
        }

        $filterString = "";

        //Loop over each filter, and add them to the filter string for later inclusion in the query.
        foreach ($filters as $filter) {
            $filterString .= " AND $filter ";
        }

        //Get the passed in start and end date parameters.
        $startDateString = $academicUpdateSearchAttributes['start_date'];
        $endDateString = $academicUpdateSearchAttributes['end_date'];

        //If there is no specified start and end date, use the start and end date of the organization's current academic year.
        // Otherwise, convert the passed in start and end date strings from the user's time zone to UTC time
        if (empty($startDateString) && empty($endDateString)) {
            $currentYear = $this->academicYearService->getAcademicYear($organizationId, 'current', $loggedInUserId);

            $startDateObject = $currentYear->getStartDate();
            $endDateObject = $currentYear->getEndDate();

            $startDateString = $startDateObject->format(SynapseConstant::DATE_YMD_FORMAT);
            $endDateString = $endDateObject->format(SynapseConstant::DATE_YMD_FORMAT);
        }

        $startDateTimeString = $this->dateUtilityService->convertToUtcDatetime($organizationId, $startDateString);
        $endDateTimeString = $this->dateUtilityService->convertToUtcDatetime($organizationId, $endDateString, true);

        //Build the sub-query
        $query = "
            SELECT
                person_id
            FROM
              (
                SELECT
                    DISTINCT ofspm.student_id AS person_id
                FROM
                    academic_record ar
                        JOIN
                    ( SELECT 
                            DISTINCT org_id,
                            student_id,
                            faculty_id,
                            permissionset_id 
                      FROM 
                            org_faculty_student_permission_map 
                      WHERE 
                            faculty_id = [FACULTY_ID]
                    ) ofspm
                            ON ofspm.org_id = ar.organization_id
                            AND ofspm.student_id = ar.person_id_student
                        JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                        JOIN
                    org_courses oc
                            ON oc.organization_id = ar.organization_id
                            AND oc.id = ar.org_courses_id
                        JOIN
                    org_academic_terms oat
                            ON oat.organization_id = oc.organization_id
                            AND oat.id = oc.org_academic_terms_id
                        JOIN
                    org_academic_year oay
                            ON oay.organization_id = oat.organization_id
                            AND oay.id = oat.org_academic_year_id
                        LEFT JOIN
                    org_course_faculty ocf
                            ON ocf.organization_id = ar.organization_id
                            AND oc.id = ocf.org_courses_id
                            AND ocf.deleted_at IS NULL
                        JOIN
                    org_course_student ocs
                            ON ocs.organization_id = ar.organization_id
                            AND oc.id = ocs.org_courses_id
                            AND ocs.person_id = ar.person_id_student
                WHERE
                    ar.deleted_at IS NULL
                    AND op.deleted_at IS NULL
                    AND oc.deleted_at IS NULL
                    AND oat.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                    AND ocs.deleted_at IS NULL
                    AND ofspm.faculty_id = [FACULTY_ID]
                    AND ofspm.org_id = [ORG_ID]
                    AND ar.update_date BETWEEN '[AU_START_DATE]' AND '[AU_END_DATE]'
                    AND '[AU_END_DATE]' >= oat.start_date AND '[AU_START_DATE]' <= oat.end_date
                    AND CURDATE() BETWEEN oay.start_date AND oay.end_date
                    AND op.accesslevel_ind_agg = 1
                    AND
                    (
                        (op.view_courses = 1 AND op.view_all_academic_update_courses = 1)
                            OR
                        (op.create_view_academic_update = 1 AND ocf.person_id = [FACULTY_ID])
                    )
                    $filterString
              ) au  
              ";

        //String replace the start and end dates
        $query = str_replace('[AU_START_DATE]', $startDateTimeString, $query);
        $query = str_replace('[AU_END_DATE]', $endDateTimeString, $query);
        //Since custom search code down the line expects an array of queries for any limiting sub-queries, add the query to a single member array.
        //TODO: Make it to where we don't have to do this.
        $queryArray = [$query];

        return $queryArray;
    }


    private function checkAndIncludeGradesInAU ($searchAttributes) {

        $filter = ' ar.in_progress_grade in ( [INPROGRESS_GRADES] ) ';

        if ( !isset( $searchAttributes['grade']) || empty( $searchAttributes['grade']) ) {

            return '';
        }

        $grades = explode(',', $searchAttributes['grade']);
        $gradeValues = array();

        foreach ($grades as $grade) {

            $gradeValues[] = '"' . $grade . '"';
        }

        return str_replace('[INPROGRESS_GRADES]', implode(',', $gradeValues), $filter);
    }

    /**
     * Builds the academic update filter subsection on absences
     *
     * @param array $searchAttributes
     * @return string
     */
    private function checkAndIncludeAbsencesInAU($searchAttributes)
    {

        $singleValueAbsencesFilter = $searchAttributes['absences'];
        $absenceRangeFilter = $searchAttributes['absence_range'];
        $absenceQueryFilter = '';

        //Builds the single value absences where condition
        if (isset($singleValueAbsencesFilter) && (!empty($singleValueAbsencesFilter) || $singleValueAbsencesFilter == "0")) {
            $filter = ' ar.absence = [ABSENCE_SINGLE] ';
            $absenceQueryFilter = str_replace('[ABSENCE_SINGLE]', $singleValueAbsencesFilter, $filter);
        }

        //Builds the minimum / maximum / range absences where condition
        if (isset($absenceRangeFilter) && !empty($absenceRangeFilter)) {
            $minimumAbsenceRange = $searchAttributes['absence_range']['min_range'];
            $maximumAbsenceRange = $searchAttributes['absence_range']['max_range'];
            $isMinimumValueSet = (isset($minimumAbsenceRange) && (!empty($minimumAbsenceRange || $minimumAbsenceRange == "0")));
            $isMaximumValueSet = (isset($maximumAbsenceRange) && (!empty($maximumAbsenceRange || $maximumAbsenceRange == "0")));

            if ($isMinimumValueSet && $isMaximumValueSet) {
                $absenceQueryFilter = ' ( ar.absence BETWEEN [ABSENCE_RANGE_START] AND [ABSENCE_RANGE_END] ) ';
            } else if ($isMinimumValueSet) {
                $absenceQueryFilter = ' ( ar.absence >= [ABSENCE_RANGE_START] ) ';
            } else if ($isMaximumValueSet) {
                $absenceQueryFilter = ' ( ar.absence <= [ABSENCE_RANGE_END] ) ';
            }

            $absenceQueryFilter = str_replace('[ABSENCE_RANGE_START]', $minimumAbsenceRange, $absenceQueryFilter);
            $absenceQueryFilter = str_replace('[ABSENCE_RANGE_END]', $maximumAbsenceRange, $absenceQueryFilter);
        }

        return $absenceQueryFilter;
    }
    /**
     * Include failure risk in academic update
     *
     * @param array $searchAttributes
     * @return string
     */
    private function checkAndIncludeFailureRiskInAU ($searchAttributes) {

        $filter = ' ar.failure_risk_level = "[RISK_LEVEL]" ';

        if ( !isset( $searchAttributes['failure_risk']) || ($searchAttributes['failure_risk'] === '') ) {

            return '';
        }

        if ($searchAttributes['failure_risk']) {

            return str_replace('[RISK_LEVEL]', 'high', $filter);
        }

        return str_replace('[RISK_LEVEL]', 'low', $filter);
    }

    /**
     * Get list of students who have answered a EBI/Campus survey questions
     *
     * @param array $searchAttributes
     * @return array
     */
    private function checkAndIncludeSurveyQuestions($searchAttributes)
    {

        if (!isset($searchAttributes['survey']) || empty($searchAttributes['survey'])) {

            return [];
        }

        $filters = array();

        // Select students who have answered a EBI/Campus survey question in the specified way
        $filterQuery = "SELECT
                            DISTINCT
                            sr.person_id
                        FROM
                            survey_response sr
                                INNER JOIN
                            (SELECT
                                 *
                             FROM
                                org_faculty_student_permission_map
                             WHERE
                                faculty_id = [FACULTY_ID] ) ofspm
                                    ON ofspm.student_id = sr.person_id
                                    AND ofspm.org_id = sr.org_id
                                INNER JOIN survey_questions sq
                                    ON sq.id = sr.survey_questions_id
                                INNER JOIN org_permissionset_datablock opd
                                    ON ofspm.permissionset_id = opd.org_permissionset_id
                                    AND opd.organization_id = sr.org_id
                                INNER JOIN datablock_questions dq ON dq.datablock_id = opd.datablock_id
                                    AND sq.ebi_question_id = dq.ebi_question_id
                        WHERE
                            sr.org_id = [ORG_ID]
                                AND [SURVEY_QFILTER]
                                AND sr.deleted_at IS NULL
                                AND dq.deleted_at IS NULL
                                AND sq.deleted_at IS NULL
                                AND opd.deleted_at IS NULL
                        ";

        $surveyQuestionFilter = $this->checkAndIncludeSurveyQuestionFilter($searchAttributes['survey'], 'survey_questions');
        $filters = array_merge($filters, $surveyQuestionFilter);
        $queries = array();

        foreach ($filters as $filter) {

            $queries[] = str_replace('[SURVEY_QFILTER]', $filter, $filterQuery);
        }

        return $queries;
    }

    private function checkAndIncludeSurveyQuestionFilter ($surveySelections, $questionsKey) {

        $studPermQuery = '';
        if($questionsKey == 'isqs'){

            $queryForQTypes = array(

                '( oqr.survey_id = [SURVEY_ID] and oqr.org_question_id = [SURVEY_QID] and decimal_value in ( [SURVEY_QANS_MULTIPLE] ) )',
                '( oqr.survey_id = [SURVEY_ID] and oqr.org_question_id = [SURVEY_QID] and decimal_value between [SURVEY_QANS_RANGE_START] and [SURVEY_QANS_RANGE_END] )',
                '( oqr.survey_id = [SURVEY_ID] and oqr.org_question_id = [SURVEY_QID] and decimal_value in ( [SURVEY_QANS_SINGLE] ) )',
                ' and oqr.person_id in ([STUD_PERM_QUERY] ) ',
                '( oqr.survey_id = [SURVEY_ID] and oqr.org_question_id = [SURVEY_QID] and oqr.org_question_options_id in ( [MULTIPLE_OPTIONS_ID] ) and decimal_value = 1 )'
            );
            $studPermQuery = ' 
        	    SELECT
                    S.person_id AS student_id
                FROM
                    org_group_students AS S
                    JOIN org_group_tree OGT ON S.org_group_id = OGT.descendant_group_id
                        AND OGT.deleted_at IS NULL
                    JOIN org_group_faculty AS F ON F.org_group_id = OGT.ancestor_group_id
                        AND F.deleted_at IS NULL
                WHERE
                    S.organization_id = [ORG_ID]
                        AND S.deleted_at is null
                        AND F.person_id = [FACULTY_ID]
                        AND F.deleted_at is null
                        AND F.org_permissionset_id in (SELECT
                            org_permissionset_id
                        FROM
                            org_permissionset_question
                            INNER JOIN
                            org_permissionset
                            ON org_permissionset.id = org_permissionset_question.org_permissionset_id
                        where
                            org_permissionset.organization_id = [ORG_ID]
                            and
                            (
                            (org_question_id = [SURVEY_QID] AND org_permissionset_question.deleted_at IS NULL)
                            OR org_permissionset.current_future_isq = 1
                            )
                            and org_permissionset.deleted_at is null)
    	     UNION ALL SELECT
                    S.person_id AS student_id
                FROM
                    org_course_student AS S
                        INNER JOIN
                    org_courses AS C ON C.id = S.org_courses_id
                        AND C.deleted_at is null
                        INNER JOIN
                    org_course_faculty AS F ON F.org_courses_id = S.org_courses_id
                        AND F.deleted_at is null
                        INNER JOIN
                    org_academic_terms AS OAT ON OAT.id = C.org_academic_terms_id
                        AND OAT.deleted_at is null
                        AND DATE(now()) between OAT.start_date and OAT.end_date
                WHERE
                    S.organization_id = [ORG_ID]
                        AND S.deleted_at is null
                        AND F.organization_id = [ORG_ID]
                        AND F.deleted_at is null
                        AND F.person_id = [FACULTY_ID]
                        AND F.org_permissionset_id in (SELECT
                            org_permissionset_id
                        FROM
                            org_permissionset_question
                            INNER JOIN
                            org_permissionset
                            ON org_permissionset.id = org_permissionset_question.org_permissionset_id
                        where
                            org_permissionset.organization_id = [ORG_ID]
                            and
                            (
                            (org_question_id = [SURVEY_QID] AND org_permissionset_question.deleted_at IS NULL)
                            OR org_permissionset.current_future_isq = 1
                            )
                            and org_permissionset.deleted_at is null)
                    ';
        }else{

            $queryForQTypes = array(

                '( sr.survey_id = [SURVEY_ID] and sr.survey_questions_id = [SURVEY_QID] and decimal_value in ( [SURVEY_QANS_MULTIPLE] ) )',
                '( sr.survey_id = [SURVEY_ID] and sr.survey_questions_id = [SURVEY_QID] and decimal_value between [SURVEY_QANS_RANGE_START] and [SURVEY_QANS_RANGE_END] )',
                '( sr.survey_id = [SURVEY_ID] and sr.survey_questions_id = [SURVEY_QID] and decimal_value in ( [SURVEY_QANS_SINGLE] ) )'
            );
        }


        $filters = array();

        /*
         * For each survey and the questions selected
        */
        foreach( $surveySelections as $survey) {

            $surveyId = $survey['survey_id'];

            if ( empty($surveyId) ) {
                continue;
            }

            foreach ( $survey[$questionsKey] as $surveyQ ) {

                if(empty($surveyQ['id'])){

                    continue;
                }

                $filter = '';

                switch ($surveyQ['type']) {

                    case 'category':

                        if ( !$surveyQ['options'] || empty( $surveyQ['options']) ) {

                            continue;
                        }

                        $values = array();
                        foreach( $surveyQ['options'] as $option ){

                            $values[] = $option['value'];
                        }

                        $filter = str_replace( '[SURVEY_QANS_MULTIPLE]', implode( ',', $values), $queryForQTypes[0]);

                        break;

                    case 'number':

                        if ( !$surveyQ['min_range'] || empty( $surveyQ['min_range'])
                            || !$surveyQ['max_range'] || empty( $surveyQ['max_range']) ) {

                            continue;
                        }

                        // TODO: Need to check if single value needs to be handled

                        $filter = str_replace( '[SURVEY_QANS_RANGE_START]', $surveyQ['min_range'], $queryForQTypes[1]);
                        $filter = str_replace( '[SURVEY_QANS_RANGE_END]', $surveyQ['max_range'], $filter);

                        break;

                    case 'multiresponse':

                        if ( !$surveyQ['options'] || empty( $surveyQ['options']) ) {

                            continue;
                        }

                        $values = array();
                        foreach( $surveyQ['options'] as $option ){

                            $values[] = $option['id'];
                        }

                        $filter = str_replace( '[MULTIPLE_OPTIONS_ID]', implode( ',', $values), $queryForQTypes[4]);

                        break;

                    default:
                        continue;
                }

                if ( !empty( $filter) ) {

                    if($questionsKey == 'isqs'){

                        $filter.= str_replace( '[STUD_PERM_QUERY]', $studPermQuery, $queryForQTypes[3]);
                    }

                    $filter = str_replace( '[SURVEY_QID]', $surveyQ['id'], $filter);
                    $filters[] = str_replace( '[SURVEY_ID]', $surveyId, $filter);
                }
            }
        }

        return $filters;
    }

    /**
     * This function will create a sub-query for the custom
     * search that will filter the students based off of
     * their cohorts and the org_academic_year. If the user
     * has not selected cohorts to filter on, then this
     * function will return an empty string.
     *
     * @param $searchAttributes
     * @return string
     */
    private function checkAndIncludeSurveyCohortSelection ($searchAttributes) {

        // see if the user wants to limit based off of cohorts
        // check the out to make sure it is filled
        if ( empty($searchAttributes['cohort_filter']) ) {

            return '';
        }

        // make sure the user has chosen an academic year to send cohorts
        if ( empty($searchAttributes['cohort_filter']['org_academic_year_id']) ) {

            return '';
        }

        // making sure that the user has select cohorts to send cohorts
        if ( empty($searchAttributes['cohort_filter']['cohorts']) ) {

            return '';
        }

        $query = ' /* Survey Cohort selection */

				SELECT
					opsc.person_id
				FROM
					org_person_student_cohort opsc
				WHERE
					opsc.org_academic_year_id = [ORG_ACADEMIC_YEAR_ID]
						AND opsc.organization_id = [ORG_ID]
						AND opsc.cohort IN ([COHORT_FILTER])
						AND opsc.deleted_at IS NULL
   	    ';

        $cohortIds = implode(', ',$searchAttributes['cohort_filter']['cohorts']);


        /* ESPRJ-9757 - fix added when the UI doesnt send any cohort but sends an array as below.
         Array
        (
            [0] =>
        )

        Fix :  check if the cohort ID string is empty then return ''
        */

        if(trim($cohortIds) == ""){
            return "";
        }

        $query =  str_replace( '[COHORT_FILTER]', $cohortIds, $query);
        $query = str_replace( '[ORG_ACADEMIC_YEAR_ID]', $searchAttributes['cohort_filter']['org_academic_year_id'], $query);

        return $query;
    }

    /**
     * Builds the query for the filters on survey completion.
     *
     * @param array $searchAttributes
     * @return string
     */
    private function checkAndIncludeSurveyFilterSelection($searchAttributes)
    {
        $isEmptySurveyFilter = empty($searchAttributes['survey_filter']);
        $isEmptyOrgAcademicYearId = empty($searchAttributes['survey_filter']['org_academic_year_id']);
        $isEmptySurveyId = empty($searchAttributes['survey_filter']['survey_id']);
        $isEmptyCohort = empty($searchAttributes['survey_filter']['cohort']);

        //If there is no survey filter, org academic year ID, survey ID, or cohort, this filter is not applicable.
        if ($isEmptySurveyFilter || $isEmptyOrgAcademicYearId || $isEmptySurveyId || $isEmptyCohort) {
            return '';
        }

        //Check to see if the responded date and responded flag are set.
        $surveyFilterRespondedDateIsSet = !empty($searchAttributes['survey_filter']['responded_date']);
        $surveyFilterRespondedIsSet = !empty($searchAttributes['survey_filter']['responded']);

        if ($surveyFilterRespondedDateIsSet && !$surveyFilterRespondedIsSet) {
            $searchAttributes['survey_filter']['responded'] = 'true';
            $surveyFilterRespondedIsSet = true;
        }

        //If the filter is for responded on a specific date, apply that filter.
        if ($surveyFilterRespondedDateIsSet && $surveyFilterRespondedIsSet) {
            $respondedDate = $searchAttributes['survey_filter']['responded_date'];
            $surveyCompletionDateCondition = " AND DATE(opssl.survey_completion_date) <= STR_TO_DATE( '$respondedDate', '%Y-%m-%d')  ";
        } else {
            $surveyCompletionDateCondition = '';
        }

        //If there is a filter on status, apply that filter.
        if ($surveyFilterRespondedIsSet) {
            $onlyIncludeStudentsWhoResponded = ($searchAttributes['survey_filter']['responded'] == 'true');

            if ($onlyIncludeStudentsWhoResponded) {
                $surveyCompletionStatusCondition = ' AND opssl.survey_completion_status IN ("CompletedMandatory", "CompletedAll") ';
            } else {
                $surveyCompletionStatusCondition = ' AND opssl.survey_completion_status NOT IN ("CompletedMandatory", "CompletedAll") ';
            }
        } else {
            $surveyCompletionStatusCondition = '';
        }

        //If there is a filter on opt out status, apply that filter.
        if (!empty($searchAttributes['survey_filter']['opted_out'])) {
            if($searchAttributes['survey_filter']['opted_out'] == 'true') {
                $surveyOptOutStatusCondition = " AND opssl.survey_opt_out_status = 'yes' AND opss.receivesurvey = 1";
            } else {
                $surveyOptOutStatusCondition = " AND opssl.survey_opt_out_status = 'no' AND opss.receivesurvey = 1";
            }
        } else {
            $surveyOptOutStatusCondition = '';
        }

        //Get the integer values of survey and cohort.
        $surveyFilterInt = (int)$searchAttributes['survey_filter']['survey_id'];
        $cohortInt = (int)$searchAttributes['survey_filter']['cohort'];


        $query = "

        SELECT
            DISTINCT opssl.person_id as person_id
        FROM
            org_person_student_cohort opsc
                INNER JOIN
            org_person_student_survey_link opssl
                ON opssl.person_id = opsc.person_id
                    AND opssl.org_academic_year_id = opsc.org_academic_year_id
                    AND opssl.org_id = opsc.organization_id
                    AND opssl.cohort = opsc.cohort
                INNER JOIN
            org_person_student_survey opss
                ON opss.person_id = opsc.person_id
                AND opss.survey_id = opssl.survey_id
        WHERE
            opsc.organization_id = [ORG_ID]
            AND (opss.receive_survey = 1 OR opssl.Has_Responses = 'Yes')
            AND opssl.deleted_at IS NULL
            AND opsc.deleted_at IS NULL
            AND opss.deleted_at IS NULL
            AND opsc.org_academic_year_id = [ORG_ACADEMIC_YEAR_ID]
            AND opssl.survey_id = [SURVEY_ID]
            AND opsc.cohort = [COHORT_ID]
            $surveyCompletionDateCondition
            $surveyCompletionStatusCondition
            $surveyOptOutStatusCondition
        ";

        $filter = str_replace('[ORG_ACADEMIC_YEAR_ID]', $searchAttributes['survey_filter']['org_academic_year_id'], $query);
        $filter = str_replace('[SURVEY_ID]', $surveyFilterInt, $filter);
        $filter = str_replace('[COHORT_ID]', $cohortInt, $filter);

        return $filter;
    }

    /**
     * Builds the query for the filters
     *
     * @param array $searchAttributes
     * @param integer $organizationId
     * @return array
     */
    private function checkAndIncludeStudentsWithSurveyStatus($searchAttributes, $organizationId)
    {
        if (!isset($searchAttributes['survey_status']) || empty($searchAttributes['survey_status'])) {
            return [];
        }

        $organizationCurrentAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
        $organizationCurrentAcademicYearYearId = $this->academicYearService->getCurrentOrganizationAcademicYearYearID($organizationId);

        $surveyStatusSearchFilterCriteria = $searchAttributes['survey_status'];

        $studentsWhoHaveTakenSurveysSubQuery = ' /* Include students who have taken survey */
    
            SELECT DISTINCT
                person_id
            FROM
                org_person_student_survey_link opssl
            WHERE
                opssl.survey_id = [SURVEY_ID]
                AND opssl.org_id = [ORG_ID]
                AND opssl.deleted_at is null
                AND opssl.org_academic_year_id IN ([ACADEMIC_YEAR_ID])
                [Yes_or_No]
                [OPTED_OUT_STATUS]
                [SURVEY_STATUS]
                AND opssl.receivesurvey = 1
        ';

        $studentReportViewStatusSubquery = ' /* Include students who have taken survey and viewed/not-viewed report */
            SELECT DISTINCT
                person_id
            FROM
                org_survey_report_access_history osr
            WHERE
                osr.survey_id = [SURVEY_ID]
                AND osr.org_id = [ORG_ID]
                AND osr.deleted_at IS NULL
                AND osr.year_id = ([ACADEMIC_YEAR_YEAR_ID])';

        $surveyStatusFilter = ' AND opssl.survey_completion_status IN ( "[SURVEY_STATUS]" )';

        $filters = array();

        foreach ($surveyStatusSearchFilterCriteria as $surveyStatus) {

            $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues = str_replace('[SURVEY_ID]', $surveyStatus['survey_id'], $studentsWhoHaveTakenSurveysSubQuery);
            $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues = str_replace('[ACADEMIC_YEAR_ID]', $organizationCurrentAcademicYearId, $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues);

            // condition appended  for is_completedd flag

            if (isset($surveyStatus['is_completed']) && $surveyStatus['is_completed'] !== '') {

                if (filter_var($surveyStatus['is_completed'], FILTER_VALIDATE_BOOLEAN)) {
                    $status = str_replace('[SURVEY_STATUS]', 'CompletedMandatory","CompletedAll', $surveyStatusFilter);
                    $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues = str_replace('[SURVEY_STATUS]', $status, $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues);
                    $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues = str_replace("[Yes_or_No]", " AND opssl.Has_Responses = 'Yes' ", $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues);
                } else {
                    $status = str_replace('[SURVEY_STATUS]', 'Assigned","InProgress', $surveyStatusFilter);
                    $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues = str_replace('[SURVEY_STATUS]', $status, $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues);
                    $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues = str_replace("[Yes_or_No]", " AND opssl.Has_Responses = 'No' ", $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues);
                }
            } else {
                $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues = str_replace('[SURVEY_STATUS]', "", $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues);
                $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues = str_replace("[Yes_or_No]", "", $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues);
            }

            // condition appended  for opted_out flag

            if (isset($surveyStatus['is_optedout']) && $surveyStatus['is_optedout'] !== '') {

                if (filter_var($surveyStatus['is_optedout'], FILTER_VALIDATE_BOOLEAN)) {
                    $optedOut = ' AND opssl.survey_opt_out_status = "Yes" ';
                    $studentsWhoHaveTakenSurveysSubqueryWithStringReplacedValues = str_replace("[OPTED_OUT_STATUS]", $optedOut, $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues);

                } else {
                    $optedOut = ' AND opssl.survey_opt_out_status = "No" ';
                    $studentsWhoHaveTakenSurveysSubqueryWithStringReplacedValues = str_replace("[OPTED_OUT_STATUS]", $optedOut, $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues);
                }
            } else {
                $studentsWhoHaveTakenSurveysSubqueryWithStringReplacedValues = str_replace("[OPTED_OUT_STATUS]", "", $studentsWhoHaveTakenSurveysSubQueryWithStringReplacedValues);
            }

            $filters[] = $studentsWhoHaveTakenSurveysSubqueryWithStringReplacedValues;

            // has the report been viewed confition
            if (isset($surveyStatus['is_viewed_report']) && $surveyStatus['is_viewed_report'] !== '') {
                $studentReportViewStatusSubQueryWithStringReplacedValues = str_replace(['[SURVEY_ID]', '[ACADEMIC_YEAR_YEAR_ID]'], [$surveyStatus['survey_id'], $organizationCurrentAcademicYearYearId], $studentReportViewStatusSubquery);
                if (filter_var($surveyStatus['is_viewed_report'], FILTER_VALIDATE_BOOLEAN)) {
                    $filters[] = $studentReportViewStatusSubQueryWithStringReplacedValues;
                } else {
                    $filters[] = array('exclude' => true, 'filter' => $studentReportViewStatusSubQueryWithStringReplacedValues);
                }
            }
        }
        return $filters;
    }

    /**
     * Include static list selection to query
     *
     * @param array $searchAttributes
     * @return string
     */
    private function checkAndIncludeStaticListSelection ($searchAttributes)
    {
    	if ( !isset( $searchAttributes['static_list_ids']) || empty( $searchAttributes['static_list_ids'])) {

    		return '';
    	}

    	$query = '
            SELECT
				DISTINCT person_id
			FROM
				org_static_list_students
			WHERE
				organization_id = [ORG_ID]
				AND deleted_at IS NULL 
                AND [STATICLIST_FILTER]
		    ';

        return str_replace('[STATICLIST_FILTER]',
            $this->searchUtilityService->makeSqlQuery($searchAttributes['static_list_ids'], 'org_static_list_id'),
            $query);
    }

    /**
     * Generates the Custom Search / Report Filtering query for Profile Items
     *
     * @param array $searchAttributes
     * @return array
     */
    private function checkAndIncludeEbiProfileItemSelection($searchAttributes)
    {

        if (!isset($searchAttributes['datablocks']) || empty($searchAttributes['datablocks'])) {
            return [];
        }

        $query = ' /* Select students having matching profile item value with in the campus */
        
                    SELECT DISTINCT
                        pem.person_id
                    FROM
                        person_ebi_metadata pem
                            INNER JOIN
                        org_person_student ops ON ops.person_id = pem.person_id
                            INNER JOIN
                        ( SELECT * FROM org_faculty_student_permission_map WHERE faculty_id = [FACULTY_ID]) ofspm ON ofspm.student_id = ops.person_id
                            AND ofspm.org_id = ops.organization_id
                            INNER JOIN
                        datablock_metadata dm ON dm.ebi_metadata_id = pem.ebi_metadata_id
                            INNER JOIN
                        org_permissionset_datablock opd ON opd.org_permissionset_id = ofspm.permissionset_id
                            AND opd.organization_id = ofspm.org_id
                            AND dm.datablock_id = opd.datablock_id
                            INNER JOIN
                        ebi_metadata em ON em.id = pem.ebi_metadata_id
                    WHERE
                        pem.deleted_at IS NULL
                            AND ops.deleted_at IS NULL
                            AND dm.deleted_at IS NULL
                            AND opd.deleted_at IS NULL
                            AND em.deleted_at IS NULL
                            AND ops.organization_id = [ORG_ID]
                            AND ofspm.faculty_id = [FACULTY_ID]
                            AND pem.ebi_metadata_id = [METADATA_ID]
                            AND [METADATA_VALUE_FILTER]
                            [PROFILE_ITEM_CONDITION]
                            [NOT_EXISTS_CONDITION]
            ';

        $profileBlocks = [];
        $inverseProfileQuery = '
                AND
                    NOT EXISTS (
                        SELECT 1
                        FROM
                            person_ebi_metadata pem2
                        WHERE
                            pem2.deleted_at IS NULL
                            AND pem2.ebi_metadata_id = [METADATA_ID]
                            AND [METADATA_VALUE_FILTER_2]
                            [PROFILE_ITEM_CONDITION_2]
                
                            AND pem2.person_id = pem.person_id
                    )
            ';

        foreach ($searchAttributes['datablocks'] as $dataBlock) {

            if (!isset($dataBlock['profile_items']) || empty($dataBlock['profile_items'])) {

                continue;
            }
            $profileItems = $this->checkAndIncludeProfileItems($dataBlock['profile_items'], $query, 'pem.metadata_value', 'ebi', $inverseProfileQuery);
            $profileBlocks = array_merge($profileBlocks, $profileItems);

        }
        return $profileBlocks;
    }

    /**
     * Generates the Custom Search / Report Filter query for institution specific profile items.
     *
     * @param array $searchAttributes
     * @return array
     */
    private function checkAndIncludeISPSelection($searchAttributes)
    {

        if (!isset($searchAttributes['isps']) || empty($searchAttributes['isps'])) {

            return [];
        }

        $query = ' /* Select students having matching campus specified profile item value */
    	
                    SELECT DISTINCT
                        pom.person_id
                    FROM
                        person_org_metadata pom
                            INNER JOIN
                        org_person_student ops ON ops.person_id = pom.person_id
                            INNER JOIN
                        ( SELECT * FROM org_faculty_student_permission_map WHERE faculty_id = [FACULTY_ID]) ofspm ON ofspm.student_id = ops.person_id
                            AND ofspm.org_id = ops.organization_id
                            INNER JOIN
                        org_permissionset_metadata opm ON ofspm.permissionset_id = opm.org_permissionset_id
                            AND opm.organization_id = ofspm.org_id
                            INNER JOIN
                        org_metadata om ON om.id = pom.org_metadata_id
                    WHERE
                        pom.deleted_at IS NULL
                            AND ops.deleted_at IS NULL
                            AND opm.deleted_at IS NULL
                            AND om.deleted_at IS NULL
                            AND ops.organization_id = [ORG_ID]
                            AND ofspm.faculty_id = [FACULTY_ID]
                            AND pom.org_metadata_id = [METADATA_ID]
                            AND [METADATA_VALUE_FILTER]
                            [PROFILE_ITEM_CONDITION]
                            [NOT_EXISTS_CONDITION]
                            
    	       /* Select students having matching campus specified profile item value - ends */
            ';

        $inverseProfileQuery = '
                AND
                    NOT EXISTS (
                        SELECT 1
                        FROM
                            person_org_metadata pom2
                        WHERE
                            pom2.deleted_at IS NULL
                            AND pom2.org_metadata_id = [METADATA_ID]
                            AND [METADATA_VALUE_FILTER_2]
                            [PROFILE_ITEM_CONDITION_2]
    	
                            AND pom2.person_id = pom.person_id
                    )
            ';

        return $this->checkAndIncludeProfileItems($searchAttributes['isps'], $query, 'pom.metadata_value', 'org', $inverseProfileQuery);
    }
    
    private function checkAndIncludeProfileItems ($profileItems, $query, $field , $type = "ebi", $inverseProfileQuery)
        {
        $filters = array();
        $tableAliasArr = explode(".", $field);
        $tableAlias = $tableAliasArr[0];



        foreach ($profileItems as $metadata) {

            $value = '';
            $value2 = '';

            switch ($metadata[SearchConstant::META_DATA_TYPE]) {

                case 'S':
                    $value = $this->getSelectedCategoricalValues($metadata, $field);

                    //For inverse profile condition - NOT EXISTS snippet
                    $value2 = $this->getSelectedCategoricalValues($metadata, $tableAliasArr[0] . '2.' . $tableAliasArr[1], 'not');
                    break;

                case 'N':
                    $value = $this->getSelectedNumericValue($metadata, $field);
                    //For inverse profile condition - NOT EXISTS snippet
                    $value2 = $this->getSelectedNumericValue($metadata, $tableAliasArr[0] . '2.' . $tableAliasArr[1], 'not');
                    break;

                case 'D':
                    $value = $this->getSelectedDateValue($metadata, $field);


                    //For inverse profile condition - NOT EXISTS snippet
                    $value2 = $this->getSelectedDateValue($metadata, $tableAliasArr[0] . '2.' . $tableAliasArr[1], 'not');
                    break;
            }


            if (!empty($value)) {

                $filter = str_replace('[METADATA_ID]', $metadata['id'], $query);

                if ($type == "org") {
                    $orgMetaRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgMetadata");
                    $orgMetadata = $orgMetaRepo->findOneBy(array(
                        'id' => $metadata['id']
                    ));

                    $profileMetaData = $orgMetadata;
                } else {
                    $ebiMetataRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:EbiMetadata");
                    $ebiMetadata = $ebiMetataRepo->findOneBy(array(
                        'id' => $metadata['id'],
                        'definitionType' => 'E'
                    ));
                    $profileMetaData = $ebiMetadata;
                }

                if (isset($profileMetaData)) {

                    $filter = str_replace('[METADATA_VALUE_FILTER]', $value, $filter);

                    if ($profileMetaData->getScope() == 'Y') {
                        /**
                         *  Add the "NOT EXISTS" snippet if the Profile is term specific
                         */
                        $filter = str_replace('[NOT_EXISTS_CONDITION]', $inverseProfileQuery, $filter);
                        $filter = str_replace('[METADATA_VALUE_FILTER_2]', $value2, $filter);
                        $filter = str_replace('[METADATA_ID]', $metadata['id'], $filter);
                        $yearIds = [];
                        if (isset($metadata['years']) && is_array($metadata['years']) && count($metadata['years']) > 0) {

                            foreach ($metadata['years'] as $year) {

                                if (isset($year['year_id'])) {
                                    $yearIds[] = $year['year_id'];
                                } else {
                                    continue;
                                }
                            }
                            $profileItemCond = "AND " . $tableAlias . ".org_academic_year_id in (" . implode(',', $yearIds) . " ) ";
                            $profileItemCond2 = "AND " . $tableAlias . "2.org_academic_year_id in (" . implode(',', $yearIds) . " ) ";
                            $filterNew = str_replace('[PROFILE_ITEM_CONDITION]', $profileItemCond, $filter);
                            $filterNew = str_replace('[PROFILE_ITEM_CONDITION_2]', $profileItemCond2, $filterNew);
                            $filters[] = $filterNew;
                        } else {

                            $filter = str_replace('[NOT_EXISTS_CONDITION]', '', $filter);
                            /* if no Year is found from the request  set  year and term id as "-1",
                             * so that no data would be returned ,
                             */
                            $yearIds[] = -1;
                            $profileItemCond = "AND " . $tableAlias . ".org_academic_year_id in (" . implode(',', $yearIds) . " ) ";
                            $profileItemCond2 = "AND " . $tableAlias . "2.org_academic_year_id in (" . implode(',', $yearIds) . " ) ";
                            $filterNew = str_replace('[PROFILE_ITEM_CONDITION]', $profileItemCond, $filter);
                            $filterNew = str_replace('[PROFILE_ITEM_CONDITION_2]', $profileItemCond2, $filterNew);
                            $filters[] = $filterNew;
                        }
                    } 
					elseif($profileMetaData->getScope() == 'T' ){
                        /**
                         *  Add the "NOT EXISTS" snippet if the Profile is term specific
                         */
                        $filter = str_replace('[NOT_EXISTS_CONDITION]', $inverseProfileQuery, $filter);
                        $filter = str_replace('[METADATA_VALUE_FILTER_2]', $value2, $filter);
                        $filter = str_replace('[METADATA_ID]', $metadata['id'], $filter);
                        $termIds = [];
                        if ($type == "org") {
                            $fieldName = "org_academic_periods_id";
                        } else {
                            $fieldName = "org_academic_terms_id";
                        }

                        if (isset($metadata['terms']) && is_array($metadata['terms']) && count($metadata['terms']) > 0) {

                            foreach ($metadata['terms'] as $term) {
                                if (isset($term['term_id'])) {
                                    $termIds[] = $term['term_id'];
                                } else {
                                    continue;
                                }
                            }
                            $profileItemCond = "AND " . $tableAlias . "." . $fieldName . " in ( " . implode(',', $termIds) . " )";
                            $profileItemCond2 = "AND " . $tableAlias . "2." . $fieldName . " in ( " . implode(',', $termIds) . " )";
                            $filter = str_replace('[PROFILE_ITEM_CONDITION_2]', $profileItemCond2, $filter);
                            $filters[] = str_replace('[PROFILE_ITEM_CONDITION]', $profileItemCond, $filter);

                        } else {
                            /*
                             *  For term specific profile items if no term id is sent  from UI
                             *  setting term to -1 so no data would be sent
                             */
                            $termIds[] = -1;
                            $profileItemCond = "AND " . $tableAlias . "." . $fieldName . " in ( " . implode(',', $termIds) . " )";
                            $profileItemCond2 = "AND " . $tableAlias . "2." . $fieldName . " in ( " . implode(',', $termIds) . " )";
                            $filter = str_replace('[PROFILE_ITEM_CONDITION_2]', $profileItemCond2, $filter);
                            $filters[] = str_replace('[PROFILE_ITEM_CONDITION]', $profileItemCond, $filter);
                        }
                    } else {

                        $filter = str_replace('[NOT_EXISTS_CONDITION]', '', $filter);
                        /*
                         * if profile item is not a term or a year then set it
                         * to blank.. i.e no conditions are added
                         */
                        $profileItemCond = "";
                        $filterNew = str_replace('[PROFILE_ITEM_CONDITION]', $profileItemCond, $filter);
                        $filterNew = str_replace('[PROFILE_ITEM_CONDITION_2]', $profileItemCond, $filterNew);
                        $filters[] = $filterNew;
                    }
                }
            }
        }


        return $filters;
    }

    /**
     * Gets the categorical condition based on the notCondition('not' || '')
     *
     * @param array $metadata
     * @param string $field
     * @param string $notCondition
     * @return string
     */
    private function getSelectedCategoricalValues($metadata, $field, $notCondition = '')
    {

        if (!isset($metadata['category_type']) || empty($metadata['category_type'])) {
            return '';
        }

        $values = array();

        foreach ($metadata["category_type"] as $value) {

            $values[] = $value['value'];
        }

        if (count($values) == 0) {
            return '';
        }

        return $field . SqlUtils::makeFilterCondition($values, true, $notCondition);
    }

    /**
     * Gets the Numerical Profile Item Condition based on the notCondition('not' || '')
     *
     * @param array $metadata
     * @param string $field
     * @param string $notCondition
     * @return string
     *
     */
    private function getSelectedNumericValue ($metadata, $field, $notCondition = '')
        {
        if ( !isset($metadata['is_single']) || !is_bool( $metadata[ 'is_single']) ) {
            
            return '';
        }

        if ($metadata['is_single']) {
            $condition = ($notCondition == 'not') ? $field . ' != ' . $metadata['single_value'] : $field . ' = ' . $metadata['single_value'];
            return $condition;

        } else {

            if (!isset($metadata['min_digits']) || is_numeric($metadata['min_digits'])
                && !isset($metadata['max_digits']) || is_numeric($metadata['max_digits'])
            ) {
                $condition = ($notCondition == 'not') ? $field . ' not between ' . $metadata['min_digits'] . ' and ' . $metadata['max_digits']
                    : $field . ' between ' . $metadata['min_digits'] . ' and ' . $metadata['max_digits'];
                return $condition;

            } else if (!isset($metadata['min_digits']) || is_numeric($metadata['min_digits'])) {
                return $field . ' >= ' . $metadata['min_digits'];

            } else if (!isset($metadata['max_digits']) || is_numeric($metadata['max_digits'])) {

                return $field . ' <= ' . $metadata['max_digits'];
            }
        }

        return '';
    }

    /**
     * Gets the date Type Profile Item Condition based on the notCondition('not' || '')
     *
     * @param array $metadata
     * @param string $field
     * @param string $notCondition
     * @return string
     */
    private function getSelectedDateValue ($metadata, $field, $notCondition = '')
    {
        $profileItemDateFieldFormat = ' STR_TO_DATE( ' . $field . ', "'.SynapseConstant::METADATA_TYPE_DATE_FORMAT.'") ';
        $profileItemDateFieldDefaultFormat = ' STR_TO_DATE( ' . $field . ', "'.SynapseConstant::METADATA_TYPE_DEFAULT_DATE_FORMAT.'") ';

        if ((isset($metadata['start_date']) && !empty($metadata['start_date'])) && (isset($metadata['end_date']) && !empty($metadata['end_date']))) {

            $startDate = new \DateTime($metadata['start_date']);
            $startDate = $startDate->format(SynapseConstant::DATE_YMD_FORMAT);

            $endDate = new \DateTime($metadata['end_date']);
            $endDate = $endDate->format(SynapseConstant::DATE_YMD_FORMAT);

            $condition = ($notCondition == 'not') ? 'NOT' : '';



            return '(
                        (' . $profileItemDateFieldFormat . ' ' . $condition . ' BETWEEN ' . "'$startDate'" . ' AND ' . "'$endDate'" . ') 
                        OR
                        (' . $profileItemDateFieldDefaultFormat . ' ' . $condition . ' BETWEEN ' . "'$startDate'" . ' AND ' . "'$endDate'" . ')
                    )';

        } else if (isset($metadata['start_date']) && !empty($metadata['start_date'])) {
            $startDate = new \DateTime($metadata['start_date']);
            $startDate = $startDate->format(SynapseConstant::DATE_YMD_FORMAT);

            return $field . ' >= ' . "'$startDate'";

        } else if (isset($metadata['end_date']) && !empty($metadata['end_date'])) {

            $endDate = new \DateTime($metadata['end_date']);
            $endDate = $endDate->format(SynapseConstant::DATE_YMD_FORMAT);

            return $field . ' <= ' . "'$endDate'";
        }
        return '';
    }


    /**
     * If the specified filters include survey factors information, include the sub-queries to get the students pertinent to the survey factors filter.
     *
     * @param array $searchAttributes
     * @param int $organizationId
     * @param int $loggedInUserId
     * @return array
     */
    private function checkAndIncludeStudentsWithSurveyFactors($searchAttributes, $loggedInUserId, $organizationId)
    {

        $faculty = $this->orgPersonFacultyRepository->findOneBy(array(
            "organization" => $organizationId,
            "person" => $loggedInUserId
        ));
        if (empty($faculty)) {
            return [];
        }
        if ((!isset($searchAttributes['survey_filter']) || empty($searchAttributes['survey_filter']))
            || (!isset($searchAttributes['survey_filter']['survey_id']) || empty($searchAttributes['survey_filter']['survey_id']))
            || (!isset($searchAttributes['survey_filter']['factors']) || empty($searchAttributes['survey_filter']['factors']))
        ) {
            return [];

        }
        $searchAttributes = $searchAttributes['survey_filter'];
        $factorsSql = '';
        foreach ($searchAttributes['factors'] as $factor) {
            $factorId = $factor['id'];
            if (empty($factorId)
                && empty($factor['value_min'])
                && empty($factor['value_max'])
                && empty($factor['survey_id'])
            ) {

                continue;
            }

            $surveyFactor = $this->factorRepository->find($factorId);
            if (empty($surveyFactor)) {
                continue;
            }

            //  Survey Factor Query Append
            $factorsSql .= ' (pfc.survey_id = ' . $factor['survey_id'] . '
                               AND pfc.deleted_at IS NULL
                               AND dq.factor_id = ' . $factorId . '
                               AND dq.survey_id = ' . $factor['survey_id'] . '
                               AND (pfc.factor_id = ' . $factorId . '
                               AND pfc.mean_value BETWEEN ' . floatval($factor['value_min']) . ' AND ' . floatval($factor['value_max']) . ')
                               AND pfc.modified_at = ( SELECT 
                                                           modified_at
                                                       FROM
                                                           person_factor_calculated AS fc
                                                       WHERE
                                                           fc.organization_id = [ORG_ID]
                                                               AND fc.person_id = pfc.person_id
                                                               AND fc.factor_id = ' . $factorId . '
                                                               AND fc.survey_id = ' . $factor['survey_id'] . '
                                                       ORDER BY modified_at DESC
                                                       LIMIT 1)
                               )';


            if ($factorId != $searchAttributes['factors'][count($searchAttributes['factors']) - 1]['id']) {
                $factorsSql .= ' OR ';
            }

        }

        if (empty($factorsSql)) {
            return [];
        }
        // Select only those students whose calculated factor value is within specified range
        $query = " SELECT
                        person_id
                    FROM
                        (SELECT DISTINCT
                            pfc.person_id AS person_id
                        FROM
                            person_factor_calculated pfc
                                JOIN
                            org_faculty_student_permission_map ofspm ON (ofspm.student_id = pfc.person_id
                                AND ofspm.faculty_id = [FACULTY_ID]
                                AND ofspm.org_id = [ORG_ID])
                                JOIN
                            org_permissionset_datablock opd ON (ofspm.permissionset_id = opd.org_permissionset_id
                                AND opd.deleted_at IS NULL
                                AND opd.organization_id = [ORG_ID])
                                JOIN
                            datablock_questions dq ON (opd.datablock_id = dq.datablock_id
                                AND dq.deleted_at IS NULL)
                        WHERE
                            pfc.organization_id = [ORG_ID]
                                AND  $factorsSql
                        ) AS pfc ";

        $queryArray = [$query];
        return $queryArray;
    }

    /**
     * Include ISQ questions
     *
     * @param array $searchAttributes
     * @return array
     */
    private function checkAndIncludeISQQuestions ($searchAttributes)
    {
    	if ( !isset( $searchAttributes['isqs']) || empty($searchAttributes['isqs'])) {
    
    		return [];
    	}
    
    	$filterQuery = ' /* Select students who have answered a EBI/Campus org/ISQ question in the specified way */
            SELECT
                DISTINCT oqr.person_id
            FROM
                org_question_response oqr
            WHERE
                oqr.org_id = [ORG_ID]
                AND [SURVEY_QFILTER]
    	    
    	    /* Select students who have answered a Campus/ISQ question in the specified way - END */
            ';
    	$filters = [];
    	$surveyQuestionFilter = $this->checkAndIncludeSurveyQuestionFilter( $searchAttributes['isqs'], 'isqs');
        $filters = array_merge($filters, $surveyQuestionFilter);

        $queries = [];

        foreach ($filters as $filter) {

            $queries[] = str_replace('[SURVEY_QFILTER]', $filter, $filterQuery);
        }

        return $queries;
    }

    /**
     * Get start and end dates for risk
     *
     * @param int $organizationId
     * @param array $searchAttributes
     * @return array
     */
    public function getRiskDates($organizationId, $searchAttributes)
    {
        $academicYearId = $searchAttributes['retention_date']['academic_year_id'];
        $orgAcademicYearInstance = $this->orgAcademicYearRepository->find($academicYearId);

        if (isset($searchAttributes['risk_date'])) {
            if (isset($searchAttributes['risk_date']['start_date'])) {
                $riskStartDate = $searchAttributes['risk_date']['start_date'];
            } else {
                $riskStartDate = $orgAcademicYearInstance->getStartDate()->format('Y-m-d');
            }

            if (isset($searchAttributes['risk_date']['end_date'])) {
                $riskEndDate = $searchAttributes['risk_date']['end_date'];

            } else {
                $riskEndDate = $orgAcademicYearInstance->getEndDate()->format('Y-m-d');
            }

        } else {
            $riskStartDate = $orgAcademicYearInstance->getStartDate()->format('Y-m-d');
            $riskEndDate = $orgAcademicYearInstance->getEndDate()->format('Y-m-d');
        }

        $riskStartDatetime = $this->dateUtilityService->convertToUtcDatetime($organizationId, $riskStartDate);
        $riskEndDatetime = $this->dateUtilityService->convertToUtcDatetime($organizationId, $riskEndDate, true);

        return [
            'start_date' => $riskStartDatetime,
            'end_date' => $riskEndDatetime
        ];
    }


    /**
     * Including search criteria for Retention and Completion variables
     *
     * @param integer $organizationId
     * @param array $searchAttributes
     * @return string
     * @throws SynapseValidationException
     */
    private function checkAndIncludeRetentionCompletionSelection($organizationId, $searchAttributes)
    {
        if (empty($searchAttributes['retention_completion'])) {
            return '';
        }
        // valid variables declared for validating what we get from UI,  for the reason that if wrong variables is passed it would break the query
        $validVariablesNames = $this->retentionCompletionVariableNameRepository->getAllVariableNames();

        $allYearIdsOfOrganization = $this->orgAcademicYearRepository->getAllAcademicYearsForOrganization($organizationId);


        $retentionTrackingYear = $searchAttributes['retention_completion']['retention_tracking_year'];


        //This protects from SQL Injection
        if (!in_array($retentionTrackingYear, $allYearIdsOfOrganization)) {
            throw new SynapseValidationException("Invalid Retention Tracking Year"); // throw exception if invalid Retention Tracking Year passed
        }
        if (empty($searchAttributes['retention_completion']['variables'])) {
            $query = "SELECT 
                        person_id
                    FROM
                        org_person_student_retention_tracking_group_view AS opsrcvv
                    WHERE
                        organization_id = $organizationId 
                        AND retention_tracking_year = '" . $retentionTrackingYear . "'";

            return $query;
        }

        $retentionCompletionVariables = $searchAttributes['retention_completion']['variables'];

        $variableString = '';

        foreach ($retentionCompletionVariables as $retentionCompletionVariableArray) {
            foreach ($retentionCompletionVariableArray as $variableName => $variableValueArray) {
                $variableValues = implode(",", $variableValueArray);

                if (!in_array($variableName, $validVariablesNames) || trim($variableValues) == "") {
                    throw new SynapseValidationException("Invalid attributes passed for retention and completion search criteria"); // throw exception if invalid values passed
                } else {
                    $variableString .= " AND var_selection.`" . $variableName . "` IN ( " . $variableValues . " ) ";
                }
            }
        }
        $query = "SELECT
                    person_id
                  FROM
                    (SELECT
                        person_id,
                        retention_tracking_year AS `Retention Tracking Year`,
                        retained_to_midyear_year_1 AS `Retained to Midyear Year 1`,
                        retained_to_start_of_year_2 AS `Retained to Start of Year 2`,
                        retained_to_midyear_year_2 AS `Retained to Midyear Year 2`,
                        retained_to_start_of_year_3 AS `Retained to Start of Year 3`,
                        retained_to_midyear_year_3 AS `Retained to Midyear Year 3`,
                        retained_to_start_of_year_4 AS `Retained to Start of Year 4`,
                        retained_to_midyear_year_4 AS `Retained To Midyear Year 4`,
                        completed_degree_in_1_year_or_less AS `Completed Degree in 1 Year or Less`,
                        CASE
                            WHEN completed_degree_in_2_years_or_less IS NULL THEN NULL
                            WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                            ELSE completed_degree_in_2_years_or_less
                        END AS `Completed Degree in 2 Years or Less`,
                        CASE
                            WHEN completed_degree_in_3_years_or_less IS NULL THEN NULL
                            WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                            WHEN completed_degree_in_2_years_or_less = 1 THEN 1
                            ELSE completed_degree_in_3_years_or_less
                        END AS `Completed Degree in 3 Years or Less`,
                        CASE
                            WHEN completed_degree_in_4_years_or_less IS NULL THEN NULL
                            WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                            WHEN completed_degree_in_2_years_or_less = 1 THEN 1
                            WHEN completed_degree_in_3_years_or_less = 1 THEN 1
                            ELSE completed_degree_in_4_years_or_less
                        END AS `Completed Degree in 4 Years or Less`,
                        CASE
                            WHEN completed_degree_in_5_years_or_less IS NULL THEN NULL
                            WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                            WHEN completed_degree_in_2_years_or_less = 1 THEN 1
                            WHEN completed_degree_in_3_years_or_less = 1 THEN 1
                            WHEN completed_degree_in_4_years_or_less = 1 THEN 1
                            ELSE completed_degree_in_5_years_or_less
                        END AS `Completed Degree in 5 Years or Less`,
                        CASE
                            WHEN completed_degree_in_6_years_or_less IS NULL THEN NULL
                            WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                            WHEN completed_degree_in_2_years_or_less = 1 THEN 1
                            WHEN completed_degree_in_3_years_or_less = 1 THEN 1
                            WHEN completed_degree_in_4_years_or_less = 1 THEN 1
                            WHEN completed_degree_in_5_years_or_less = 1 THEN 1
                            ELSE completed_degree_in_6_years_or_less
                        END AS `Completed Degree in 6 Years or Less`
                    FROM
                        (SELECT
                            p.id AS person_id,
                            opsrcpv.retention_tracking_year,
                            oay.name AS retention_tracking_year_name,
                            MAX(opsrcpv.retained_to_midyear_year_1) AS retained_to_midyear_year_1,
                            MAX(opsrcpv.retained_to_start_of_year_2) AS retained_to_start_of_year_2,
                            MAX(opsrcpv.retained_to_midyear_year_2) AS retained_to_midyear_year_2,
                            MAX(opsrcpv.retained_to_start_of_year_3) AS retained_to_start_of_year_3,
                            MAX(opsrcpv.retained_to_midyear_year_3) AS retained_to_midyear_year_3,
                            MAX(opsrcpv.retained_to_start_of_year_4) AS retained_to_start_of_year_4,
                            MAX(opsrcpv.retained_to_midyear_year_4) AS retained_to_midyear_year_4,
                            MAX(opsrcpv.completed_degree_in_1_year_or_less) AS completed_degree_in_1_year_or_less,
                            MAX(opsrcpv.completed_degree_in_2_years_or_less) AS completed_degree_in_2_years_or_less,
                            MAX(opsrcpv.completed_degree_in_3_years_or_less) AS completed_degree_in_3_years_or_less,
                            MAX(opsrcpv.completed_degree_in_4_years_or_less) AS completed_degree_in_4_years_or_less,
                            MAX(opsrcpv.completed_degree_in_5_years_or_less) AS completed_degree_in_5_years_or_less,
                            MAX(opsrcpv.completed_degree_in_6_years_or_less) AS completed_degree_in_6_years_or_less
                        FROM
                            person p
                                INNER JOIN
                            org_person_student_retention_completion_pivot_view opsrcpv ON p.id = opsrcpv.person_id
                                INNER JOIN
                            org_academic_year oay ON oay.year_id = opsrcpv.retention_tracking_year
                                AND oay.organization_id = p.organization_id
                        WHERE
                            p.organization_id = " . $organizationId . " 
                            AND opsrcpv.retention_tracking_year = '" . $retentionTrackingYear . "'
                        GROUP BY opsrcpv.organization_id, opsrcpv.person_id, opsrcpv.retention_tracking_year) AS var_query) AS var_selection
                        WHERE 1 = 1
                        $variableString";

        return $query;
    }
}
