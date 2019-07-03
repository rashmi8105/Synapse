<?php

namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetQuestionRepository;
use Synapse\CoreBundle\Repository\OrgQuestionRepository;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\SurveyBundle\Repository\WessLinkRepository;


/**
 * @DI\Service("isq_permissionset_service")
 */
class ISQPermissionsetService extends AbstractService
{

    const SERVICE_KEY = 'isq_permissionset_service';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgPermissionsetQuestionRepository
     */
    private $orgPermissionsetQuestionRepository;

    /**
     * @var OrgQuestionRepository
     */
    private $orgQuestionsRepository;

    /**
     * @var WessLinkRepository
     */
    private $wessLinkRepository;


    /**
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container"),
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        $this->dateUtilityService = $this->container->get('date_utility_service');

        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPermissionset');
        $this->orgPermissionsetQuestionRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPermissionsetQuestion');
        $this->orgQuestionsRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgQuestion');
        $this->wessLinkRepository = $this->repositoryResolver->getRepository('SynapseSurveyBundle:WessLink');
    }


    /**
     * This function will get all ISQs Ids for a person in an organization given different variables.
     * First this will return, if only organization and person ids are added, all ISQs that a person
     * has access to within an organization.
     * Second, will return an empty array if personId or Organization Id is not there, or orgQuestions does not
     * intersect the ACTUAL ISQs the organization has
     *
     * @param int $personId => an intenger
     *                    The Person's system Id, cannot be defaulted and fails if not there
     * @param int $organizationId => an integer
     *                    The Organization's system Id, cannot be defaulted and fails if not there
     * @param null|Array $permissionsetArray => or Array: [0=>permissionset_id, 1=>permissionset_id, ..]
     *                    An array of permissionsets that the person has access to
     *                    Defaults to all permissionsets the person has if set to null
     * @param null|Array $orgQuestions => or Array: [0=>$orgQuestions, 1=>$orgQuestions, ..]
     *                    A list of organization ISQs that the persom wants to check to make sure the person has access to
     *                    Defaults to All ISQs when set to null
     * @param null|int $cohort => or Integer/Float/String of a number
     *                    A single cohort number, make sure all questions are in line with this cohort
     *                    Defaults to null when left blank
     * @param bool $shouldAllowAggregatePermissionsets => or true
     *                    Asks the User whether or not they want to include aggregate permissionsets within the returned ISQs
     * @param null|int $survey => or Integer/Float/String of a number
     *                    Checks ISQs against survey information
     *                    Defaults to null when set to null
     * @return Array => Array [0=>['org_question_id' => Question_Id], 1=>['org_question_id' => Question_Id]]
     */
    public function getFilteredISQIds($personId, $organizationId, $permissionsetArray = null, $orgQuestions = null, $cohort = null, $shouldAllowAggregatePermissionsets = true, $survey = null)
    {

        // personId and organization id are needed for this function
        if (!$personId) {
            return array();
        }
        if (!$organizationId) {
            return array();
        }

        $timeZone = $this->dateUtilityService->getOrganizationISOTimeZone($organizationId);


        // This is to check to make sure if someone somehow tries to place in a
        // permissionset the person does not have access to within the organization
        // in the function, then delete the permissionset
        $permissionsetCheck = array();
        $allPermissionSetIds = $this->orgPermissionsetRepository->getAllPermissionsetIdsByPerson($personId, $organizationId, $timeZone);
        foreach ($allPermissionSetIds as $allPermissionSetId) {
            $permissionsetCheck[] = $allPermissionSetId['org_permissionset_id'];
        }
        if (is_null($permissionsetArray)) {
            $permissionsetArray = $permissionsetCheck;
        } else {
            // need to double check that each person has access to the permissionsets in the database
            $permissionsetArray = array_intersect($permissionsetArray, $permissionsetCheck);
        }

        // the person can place in what orgQuestions they want to work with
        // We need to make sure that the org questions are allowed.
        $allISQsForOrganization = array();

        // get all isqs for the organization that the person has
        $orgQuestionsObjects = $this->orgQuestionsRepository->FindBy([
            'organization' => $organizationId
        ], array(
            'id' => 'ASC'
        ));
        if ($orgQuestionsObjects) {
            foreach ($orgQuestionsObjects as $orgQuestionsObject) {
                $allISQsForOrganization[] = (string)$orgQuestionsObject->getId();
            }
        }

        // sets orgQuestions to all if defaulted
        if (is_null($orgQuestions)) {
            $allRequestedISQs = $allISQsForOrganization;
        } else {
            $allRequestedISQs = array_intersect($orgQuestions, $allISQsForOrganization);
        }

        // check to see if allRequestedISQs and
        // permissionsetArrays is null, if so,
        // return a blank array
        if(empty($allRequestedISQs)){
           return array();
        }
        if(empty($permissionsetArray)){
           return array();
        }

        // see if the person has access and can stop worrying about the permissionsets question
        // If this is throwing an exception see getAllPermissionsetIdsByPerson to make sure
        // that the function is setting $permissionsetArray i.e. the organization and person does not match
        $hasPermissionsToAccessAllISQs = $this->orgPermissionsetQuestionRepository->permissionsetsHaveAccessToEveryCurrentAndFutureISQ($permissionsetArray, $shouldAllowAggregatePermissionsets);

        // If the person has access to All ISQs if the person
        // has access to all_current_and_future ISQs
        $orgISQsForThePerson = array();
        if ($hasPermissionsToAccessAllISQs) {

            // limiting questions based on cohort or survey
            if ($cohort || $survey) {

                // limit the question based on cohort or survey
                $limitedISQs = $this->orgPermissionsetQuestionRepository->filterISQsBasedOffOfCohortSurveyAndOrgQuestions($allRequestedISQs, $cohort, $survey);

                // prepare the isq to ship
                foreach ($limitedISQs as $limitedISQ) {
                    $orgISQsForThePerson[]['org_question_id'] = $limitedISQ['id'];
                }
            } else {
                // no need to limit prepare to ship $orgISQsForThePerson
                foreach ($allRequestedISQs as $anIndividualISQ) {
                    $orgISQsForThePerson[]['org_question_id'] = $anIndividualISQ;
                }
            }
        } else {
            $allRequestedISQs = implode(', ', $allRequestedISQs);

            // This will limit ISQs based on their permissionsets, this is ready to ship
            $orgISQsForThePerson = $this->orgPermissionsetQuestionRepository->getOrgQuestionsByPermissionsets($permissionsetArray, $shouldAllowAggregatePermissionsets,  $allRequestedISQs, $cohort, $survey);
        }
        return $orgISQsForThePerson;
    }


    /**
     * Returns all survey and cohort combinations for which the user has access to ISQs.
     * The data returned also includes some extra information, such as the name and status of the survey,
     * formatted as a one-dimensional associative array straight from the database.
     *
     * If $userHasCoordinatorAccess is true, includes all survey and cohort combinations for which ISQs have been set up, regardless of the user's permissions.
     * If $isAggregateReporting is true (e.g., in report filters), all permission sets will be included for determining ISQ access.
     * If $isAggregateReporting is false (e.g., in custom search), only individual permission sets will be included for determining ISQ access.
     *
     * @param int $loggedInUserId
     * @param int $orgId
     * @param bool $isAggregateReporting
     * @param bool $userHasCoordinatorAccess
     * @param int|null $orgAcademicYearId
     * @param array|null $surveyStatus - if set, typically is ["launched", "closed"]
     * @return array
     */
    public function getSurveysAndCohortsHavingAccessibleISQs($loggedInUserId, $orgId, $isAggregateReporting, $userHasCoordinatorAccess, $orgAcademicYearId = null, $surveyStatus = null)
    {
        $permissionSets = $this->orgPermissionsetRepository->getAllPermissionsetIdsByPerson($loggedInUserId, $orgId);
        $permissionSets = array_column($permissionSets, 'org_permissionset_id');

        $userHasAccessToAllISQs = $this->orgPermissionsetQuestionRepository->permissionsetsHaveAccessToEveryCurrentAndFutureISQ($permissionSets, $isAggregateReporting);

        if ($userHasAccessToAllISQs || $userHasCoordinatorAccess) {
            $surveysAndCohorts = $this->wessLinkRepository->getCohortsAndSurveysForOrganizationForSetup($orgId, 'isq', $orgAcademicYearId, $surveyStatus);
        } else {
            $surveysAndCohorts = $this->wessLinkRepository->getSurveysAndCohortsHavingAccessibleISQs($orgId, $permissionSets, $isAggregateReporting, $orgAcademicYearId, $surveyStatus);
        }

        return $surveysAndCohorts;
    }
}