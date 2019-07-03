<?php
namespace Synapse\SearchBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\EbiSearchHistory;
use Synapse\CoreBundle\Repository\EbiSearchHistoryRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\SearchRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\SearchBundle\DAO\PredefinedSearchDAO;
use Synapse\SearchBundle\Job\PredefinedSearchCSVJob;


/**
 * @DI\Service("predefined_search_service")
 */
class PredefinedSearchService extends AbstractService
{

    const SERVICE_KEY = 'predefined_search_service';

    private $inProgressAcademicUpdatePredefinedSearches = [
        'high_risk_of_failure',
        'four_or_more_absences',
        'in-progress_grade_of_c_or_below',
        'in-progress_grade_of_d_or_below',
        'two_or_more_in-progress_grades_of_d_or_below'
    ];

    private $finalGradePredefinedSearches = [
        'final_grade_of_c_or_below',
        'final_grade_of_d_or_below',
        'two_or_more_final_grades_of_d_or_below'
    ];


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
     * @var DataProcessingUtilityService
     */
    private $dataProcessingUtilityService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var StudentListService
     */
    private $studentListService;


    // DAO

    /**
     * @var PredefinedSearchDAO
     */
    private $predefinedSearchDAO;


    // Repositories

    /**
     * @var SearchRepository
     */
    private $ebiSearchRepository;

    /**
     * @var EbiSearchHistoryRepository
     */
    private $ebiSearchHistoryRepository;

    /**
     * @var OrgAcademicTermRepository
     */
    private $orgAcademicTermRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;


    /**
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
        $this->resque = $this->container->get('bcc_resque.resque');

        // Services
        $this->academicYearService = $this->container->get('academicyear_service');
        $this->dataProcessingUtilityService = $this->container->get('data_processing_utility_service');
        $this->dateUtilityService = $this->container->get('date_utility_service');
        $this->studentListService = $this->container->get('student_list_service');

        // DAO
        $this->predefinedSearchDAO = $this->container->get('predefined_search_dao');

        // Repositories
        $this->ebiSearchRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:EbiSearch');
        $this->ebiSearchHistoryRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:EbiSearchHistory');
        $this->orgAcademicTermRepository = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgAcademicTerms');
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPermissionset');
        $this->personRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Person');
    }


    /**
     * Returns a list of predefined searches in the given category, along with the last time the given faculty member ran them (if ever).
     * If the faculty member's overall permissions would prevent a search from returning results, that search will not be included in the list.
     * Note that this is far from a guarantee that all the searches listed will return results.
     *
     * @param string $category -- "student_search" or "academic_update_search" or "activity_search"
     * @param int $loggedInUserId
     * @return array
     */
    public function getPredefinedSearchListByCategory($category, $loggedInUserId)
    {
        // If the user doesn't have individual access to any students, he/she shouldn't be able to run any predefined searches.
        $userHasIndividualAccessToStudents = $this->orgPermissionsetRepository->determineWhetherUserHasIndividualAccess($loggedInUserId);

        if (!$userHasIndividualAccessToStudents) {
            return [];
        }

        // Get the full list of predefined searches in the given category.
        $predefinedSearches = $this->ebiSearchRepository->getPredefinedSearchListByCategory($category, $loggedInUserId);

        // Remove predefined searches from the list based on the user's permissions.
        if ($category == 'student_search') {
            $riskAndIntentToLeavePermissions = $this->orgPermissionsetRepository->getRiskAndIntentToLeavePermissions($loggedInUserId);

            if (!$riskAndIntentToLeavePermissions['intent_to_leave']) {
                $predefinedSearches = $this->dataProcessingUtilityService->removeRecords($predefinedSearches, 'search_key', ['high_intent_to_leave']);
            }

            if (!$riskAndIntentToLeavePermissions['risk_indicator']) {
                $predefinedSearches = $this->dataProcessingUtilityService->removeRecords($predefinedSearches, 'search_key', ['at_risk_students', 'high_priority_students']);
            }

        } elseif ($category == 'academic_update_search') {
            $academicUpdatePermissions = $this->orgPermissionsetRepository->getCourseAndAcademicUpdatePermissions($loggedInUserId);

            $userCanRunInProgressAcademicUpdateSearches = ($academicUpdatePermissions['view_courses'] && $academicUpdatePermissions['view_all_academic_update_courses']) || $academicUpdatePermissions['create_view_academic_update'];

            if (!$userCanRunInProgressAcademicUpdateSearches) {
                $predefinedSearches = $this->dataProcessingUtilityService->removeRecords($predefinedSearches, 'search_key', $this->inProgressAcademicUpdatePredefinedSearches);
            }

            $userCanRunFinalGradeSearches = ($academicUpdatePermissions['view_courses'] && $academicUpdatePermissions['view_all_final_grades']) || $academicUpdatePermissions['create_view_academic_update'];

            if (!$userCanRunFinalGradeSearches) {
                $predefinedSearches = $this->dataProcessingUtilityService->removeRecords($predefinedSearches, 'search_key', $this->finalGradePredefinedSearches);
            }
        }

        // Format last-run dates.
        foreach ($predefinedSearches as &$result) {
            if (isset($result['last_run'])) {
                $result['last_run'] = $this->dateUtilityService->convertDatabaseStringToISOString($result['last_run']);
            }
        }

        return $predefinedSearches;
    }


    /**
     * Returns the requested page of results for the requested predefined search, sorted as requested.
     *
     * @param string $predefinedSearchKey
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $sortBy
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param bool $onlyIncludeActiveStudents
     * @return array
     */
    public function getPredefinedSearchResults($predefinedSearchKey, $loggedInUserId, $organizationId, $sortBy, $pageNumber, $recordsPerPage, $onlyIncludeActiveStudents=true)
    {
        $studentIds = $this->getStudentsForPredefinedSearch($predefinedSearchKey, $loggedInUserId, $organizationId, $onlyIncludeActiveStudents);
        $dataToReturn = $this->studentListService->getStudentListWithMetadata($studentIds, $loggedInUserId, $organizationId, $sortBy, $pageNumber, $recordsPerPage);
        $this->updateSearchHistory($predefinedSearchKey, $loggedInUserId);
        return $dataToReturn;
    }


    /**
     * Returns an array of the person_ids of all students included in the given predefined search.
     *
     * @param string $predefinedSearchKey
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param bool $onlyIncludeActiveStudents
     * @return array
     */
    private function getStudentsForPredefinedSearch($predefinedSearchKey, $loggedInUserId, $organizationId, $onlyIncludeActiveStudents=true)
    {
        $inProgressGradesDOrBelow = ['D', 'F', 'F/No Pass'];

        $inProgressGradesCOrBelow = ['C', 'D', 'F', 'F/No Pass'];

        $finalGradesDOrBelow = ['D+', 'D', 'D-', 'F/No Pass'];

        $finalGradesCOrBelow = ['C+', 'C', 'C-', 'D+', 'D', 'D-', 'F/No Pass'];

        $activityPredefinedSearchesRequiringCurrentTerms = [
            'interaction_contacts',
            'no_interaction_contacts'
        ];

        // getting the current academic year   for the organization
        $currentOrgAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
        if ($currentOrgAcademicYear) {
            $currentOrgAcademicYearId = $currentOrgAcademicYear['org_academic_year_id'];
        } else {
            return [];
        }


        $predefinedSearchesRequiringCurrentTerms = array_merge($this->inProgressAcademicUpdatePredefinedSearches, $activityPredefinedSearchesRequiringCurrentTerms);
        $predefinedSearchesRequiringTermsForCurrentYear = $this->finalGradePredefinedSearches;
        $predefinedSearchesRequiringTerms = array_merge($predefinedSearchesRequiringCurrentTerms, $predefinedSearchesRequiringTermsForCurrentYear);

        if (in_array($predefinedSearchKey, $predefinedSearchesRequiringTerms)) {

            $termsForCurrentYear = $this->orgAcademicTermRepository->getAcademicTermsForYear($currentOrgAcademicYearId, $organizationId);

            if ($termsForCurrentYear) {
                if (in_array($predefinedSearchKey, $predefinedSearchesRequiringTermsForCurrentYear)) {
                    $orgAcademicTermIdsForCurrentYear = array_column($termsForCurrentYear, 'org_academic_term_id');

                } elseif (in_array($predefinedSearchKey, $predefinedSearchesRequiringCurrentTerms)) {

                    // From the list of academic terms for the current year, select the current terms.
                    $currentOrgAcademicTerms = array_filter($termsForCurrentYear, function($term) {
                        return $term['is_current_academic_term'] == 1;
                    });

                    if ($currentOrgAcademicTerms) {
                        $currentOrgAcademicTermIds = array_column($currentOrgAcademicTerms, 'org_academic_term_id');
                    } else {
                        return [];
                    }
                }
            } else {
                return [];
            }
        }

        switch ($predefinedSearchKey) {
            case 'all_my_students':
                $studentIds = $this->predefinedSearchDAO->getAllMyStudents($loggedInUserId, $organizationId, $currentOrgAcademicYearId, $onlyIncludeActiveStudents);
                break;
            case 'my_primary_campus_connections':
                $studentIds = $this->predefinedSearchDAO->getMyPrimaryCampusConnections($loggedInUserId, $organizationId, $currentOrgAcademicYearId, $onlyIncludeActiveStudents);
                break;
            case 'at_risk_students':
                $studentIds = $this->predefinedSearchDAO->getAtRiskStudents($loggedInUserId, $organizationId, $currentOrgAcademicYearId, $onlyIncludeActiveStudents);
                break;
            case 'high_intent_to_leave':
                $studentIds = $this->predefinedSearchDAO->getStudentsWithHighIntentToLeave($loggedInUserId, $organizationId, $currentOrgAcademicYearId, $onlyIncludeActiveStudents);
                break;
            case 'high_priority_students':
                $studentIds = $this->predefinedSearchDAO->getHighPriorityStudents($loggedInUserId, $currentOrgAcademicYearId, $onlyIncludeActiveStudents);
                break;
            case 'high_risk_of_failure':
                $studentIds = $this->predefinedSearchDAO->getStudentsAtRiskOfFailure($loggedInUserId, $organizationId, $currentOrgAcademicTermIds, $currentOrgAcademicYearId, $onlyIncludeActiveStudents);
                break;
            case 'four_or_more_absences':
                $studentIds = $this->predefinedSearchDAO->getStudentsWithExcessiveAbsences($loggedInUserId, $organizationId, $currentOrgAcademicTermIds, $currentOrgAcademicYearId, 4, $onlyIncludeActiveStudents);
                break;
            case 'in-progress_grade_of_c_or_below':
                $studentIds = $this->predefinedSearchDAO->getStudentsWithLowInProgressGrades($loggedInUserId, $organizationId, $currentOrgAcademicTermIds, $currentOrgAcademicYearId, $inProgressGradesCOrBelow, $onlyIncludeActiveStudents);
                break;
            case 'in-progress_grade_of_d_or_below':
                $studentIds = $this->predefinedSearchDAO->getStudentsWithLowInProgressGrades($loggedInUserId, $organizationId, $currentOrgAcademicTermIds, $currentOrgAcademicYearId, $inProgressGradesDOrBelow, $onlyIncludeActiveStudents);
                break;
            case 'two_or_more_in-progress_grades_of_d_or_below':
                $studentIds = $this->predefinedSearchDAO->getStudentsWithMultipleLowInProgressGrades($loggedInUserId, $organizationId, $currentOrgAcademicTermIds, $currentOrgAcademicYearId, $inProgressGradesDOrBelow, $onlyIncludeActiveStudents);
                break;
            case 'final_grade_of_c_or_below':
                $studentIds = $this->predefinedSearchDAO->getStudentsWithLowFinalGrades($loggedInUserId, $organizationId, $orgAcademicTermIdsForCurrentYear, $currentOrgAcademicYearId, $finalGradesCOrBelow, $onlyIncludeActiveStudents);
                break;
            case 'final_grade_of_d_or_below':
                $studentIds = $this->predefinedSearchDAO->getStudentsWithLowFinalGrades($loggedInUserId, $organizationId, $orgAcademicTermIdsForCurrentYear, $currentOrgAcademicYearId, $finalGradesDOrBelow, $onlyIncludeActiveStudents);
                break;
            case 'two_or_more_final_grades_of_d_or_below':
                $studentIds = $this->predefinedSearchDAO->getStudentsWithMultipleLowFinalGrades($loggedInUserId, $organizationId, $orgAcademicTermIdsForCurrentYear, $currentOrgAcademicYearId, $finalGradesDOrBelow, $onlyIncludeActiveStudents);
                break;
            case 'interaction_contacts':
                $studentIds = $this->predefinedSearchDAO->getStudentsHavingInteractionContacts($loggedInUserId, $organizationId, $currentOrgAcademicTermIds, $currentOrgAcademicYearId, $onlyIncludeActiveStudents);
                break;
            case 'no_interaction_contacts':
                $studentIds = $this->predefinedSearchDAO->getStudentsNotHavingInteractionContacts($loggedInUserId, $organizationId, $currentOrgAcademicTermIds, $currentOrgAcademicYearId, $onlyIncludeActiveStudents);
                break;
            case 'have_not_been_reviewed':
                $studentIds = $this->predefinedSearchDAO->getUnreviewedStudents($loggedInUserId, $organizationId, $currentOrgAcademicYearId, $onlyIncludeActiveStudents);
                break;
            default:
                $studentIds = [];
        }
        return $studentIds;
    }


    /**
     * Returns a list of all students, including their ids and names, for the given predefined search.
     * This list is used to set up a bulk action.
     *
     * @param string $predefinedSearchKey
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param $onlyIncludeActiveStudents
     * @return array
     */
    public function getPredefinedSearchStudentIdsAndNames($predefinedSearchKey, $loggedInUserId, $organizationId, $onlyIncludeActiveStudents = false)
    {
        $studentIds = $this->getStudentsForPredefinedSearch($predefinedSearchKey, $loggedInUserId, $organizationId, $onlyIncludeActiveStudents);
        $dataToReturn = $this->studentListService->getStudentIdsAndNames($studentIds, $loggedInUserId);
        return $dataToReturn;
    }


    /**
     * Creates a job which will create a CSV of all students in a predefined search.
     *
     * @param string $predefinedSearchKey
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $sortBy
     * @param $onlyIncludeActiveStudents
     * @return array
     */
    public function createPredefinedSearchJob($predefinedSearchKey, $loggedInUserId, $organizationId, $sortBy, $onlyIncludeActiveStudents = false)
    {
        $job = new PredefinedSearchCSVJob();
        $job->args = [
            'predefined_search_key' => $predefinedSearchKey,
            'faculty_id' => $loggedInUserId,
            'organization_id' => $organizationId,
            'sort_by' => $sortBy,
            'only_include_active_students' => $onlyIncludeActiveStudents
        ];
        $this->resque->enqueue($job, true);
        return [SynapseConstant::DOWNLOAD_IN_PROGRESS_MESSAGE];
    }


    /**
     * Returns the results of the given predefined search for use in creating a CSV.
     * Includes all students matching the search criteria, along with additional data
     * including name, external id, email, status, risk, intent to leave, class level, activity count, and date and type of last activity.
     * Applies risk and intent to leave permissions to ensure this data is not included about students if not permitted;
     * these students will be at the end of the list if sorting is done by risk or intent to leave.
     *
     * @param string $predefinedSearchKey
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $sortBy
     * @param $onlyIncludeActiveStudents
     * @return array
     */
    public function getPredefinedSearchResultsForCSV($predefinedSearchKey, $loggedInUserId, $organizationId, $sortBy, $onlyIncludeActiveStudents = false)
    {
        $studentIds = $this->getStudentsForPredefinedSearch($predefinedSearchKey, $loggedInUserId, $organizationId, $onlyIncludeActiveStudents);
        $records = $this->studentListService->getStudentListWithAdditionalDataForCSV($studentIds, $loggedInUserId, $organizationId, $sortBy);
        return $records;
    }


    /**
     * Inserts a record in the ebi_search_history table to record that the given faculty member performed the given search.
     * If there's a previous record for this faculty member and this search, soft deletes it.
     *
     * @param string $queryKey
     * @param int $facultyId
     */
    private function updateSearchHistory($queryKey, $facultyId)
    {
        $personObject = $this->personRepository->find($facultyId);
        $ebiSearchObject = $this->ebiSearchRepository->findOneBy(['queryKey' => $queryKey]);

        $existingEbiSearchHistoryObject = $this->ebiSearchHistoryRepository->findOneBy(['person' => $personObject, 'ebiSearch' => $ebiSearchObject]);

        if ($existingEbiSearchHistoryObject) {
            $this->ebiSearchHistoryRepository->delete($existingEbiSearchHistoryObject);
        }

        $ebiSearchHistoryObject = new EbiSearchHistory();
        $ebiSearchHistoryObject->setPerson($personObject);
        $ebiSearchHistoryObject->setEbiSearch($ebiSearchObject);

        $this->ebiSearchHistoryRepository->persist($ebiSearchHistoryObject);
    }
}