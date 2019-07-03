<?php
namespace Synapse\CoreBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\job\TeamActivitiesCSVJob;
use Synapse\CoreBundle\Repository\FeatureMasterLangRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetFeaturesRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\TeamActivityCountDto;
use Synapse\RestBundle\Entity\TeamMembersActivitiesDto;
use Synapse\RestBundle\Entity\TeamsDto;

/**
 * @DI\Service("team_activity_service")
 */
class TeamActivityService extends AbstractService
{

    const SERVICE_KEY = 'team_activity_service';

    //Scaffolding
    /**
     * @var Container
     */
    private $container;


    //Services
    /**
     * @var ActivityService
     */
    private $activityService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var Resque
     */
    private $resque;


    //Repositories
    /**
     * @var FeatureMasterLangRepository
     */
    private $featureMasterLangRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrgPermissionsetFeaturesRepository
     */
    private $orgPermissionSetFeaturesRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var TeamMembersRepository
     */
    private $teamMembersRepository;


    /**
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
        //Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        //Services
        $this->activityService = $this->container->get('activity_service');
        $this->dateUtilityService = $this->container->get('date_utility_service');
        $this->personService = $this->container->get('person_service');
        $this->resque = $this->container->get('bcc_resque.resque');

        //Repositories
        $this->featureMasterLangRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:FeatureMasterLang');
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgAcademicYear');
        $this->orgPermissionSetFeaturesRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPermissionsetFeatures');
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPersonStudent');
        $this->teamMembersRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:TeamMembers');
    }


    /**
     * Get my teams recent activity based on loggedUserId and filter
     *
     * @param int $organizationId
     * @param int $loggedInUserId
     * @param string $timePeriod - 'today'|'week'|'month'
     * @return TeamsDto
     */
    public function getActivityCountsOfMyTeam($organizationId, $loggedInUserId, $timePeriod)
    {
        $this->logger->debug("Get My Teams Recent Activities for time period: " . $timePeriod);

        //Get Utc Time Range Using Organization's Time and specified time period
        $dateTimeRange = $this->getUtcDateRangeUsingOrganizationTimezoneAdjustedDate($timePeriod, $organizationId);
        $fromDate = $dateTimeRange['from_date'];
        $toDate = $dateTimeRange['to_date'];

        //Find the current academic year start and end for restricting the view to the current academic year
        $currentUtcDateTimeObject = new \DateTime('now');
        $orgAcademicYear = $this->orgAcademicYearRepository->getCurrentAcademicDetails($currentUtcDateTimeObject, $organizationId);

        $currentAcademicYearId = NULL;
        if (!empty($orgAcademicYear)) {
            $academicYearStartDate = $orgAcademicYear[0]['startDate'];
            $academicYearEndDate = $orgAcademicYear[0]['endDate'];
            $currentAcademicYearId = $orgAcademicYear[0]['id'];
        } else {
            $academicYearStartDate = $fromDate;
            $academicYearEndDate = $toDate;
        }

        //Getting counts, permission to the activity itself is not necessary per ESPRJ-10870
        $teamOpenReferralCountArray = $this->teamMembersRepository->getActivityCountsOfMyTeamByActivityType('open-referral', ['R'], $fromDate, $toDate, $loggedInUserId, $organizationId, $academicYearStartDate, $academicYearEndDate, null, $currentAcademicYearId);
        $teamInteractionCountArray = $this->teamMembersRepository->getActivityCountsOfMyTeamByActivityType('interaction', ['R', 'C', 'N', 'A'], $fromDate, $toDate, $loggedInUserId, $organizationId, $academicYearStartDate, $academicYearEndDate, null, $currentAcademicYearId);
        $teamLoginCountArray = $this->teamMembersRepository->getActivityCountsOfMyTeamByActivityType('login', ['L'], $fromDate, $toDate, $loggedInUserId, $organizationId, $academicYearStartDate, $academicYearEndDate, null, $currentAcademicYearId);

        //Find all unique team Ids that have activity counts
        $teamIds = array_merge(array_column($teamInteractionCountArray, 'team_id'), array_column($teamOpenReferralCountArray, 'team_id'), array_column($teamLoginCountArray, 'team_id'));
        $teamIds = array_unique($teamIds);

        $teamActivitiesJoined = [];
        $teamNamesById = [];

        //Using Array Indexing to map all team_activites_count and names to ids
        foreach ($teamOpenReferralCountArray as $teamOpenReferralRow) {
            $teamId = $teamOpenReferralRow['team_id'];
            //Map 'teams_id' => 'activity' => 'team_activities_count'
            $teamActivitiesJoined[$teamId]['open-referral'] = $teamOpenReferralRow['team_activities_count'];

            //Map 'teams_id' => 'team_name'
            $teamNamesById[$teamId] = $teamOpenReferralRow['team_name'];
        }

        foreach ($teamInteractionCountArray as $teamInteractionRow) {
            $teamId = $teamInteractionRow['team_id'];

            //Map 'teams_id' => 'activity' => 'team_activities_count'
            $teamActivitiesJoined[$teamId]['interaction'] = $teamInteractionRow['team_activities_count'];

            //Map 'teams_id' => 'team_name'
            $teamNamesById[$teamId] = $teamInteractionRow['team_name'];
        }

        foreach ($teamLoginCountArray as $teamLoginRow) {
            $teamId = $teamLoginRow['team_id'];

            //Map 'teams_id' => 'activity' => 'team_activities_count'
            $teamActivitiesJoined[$teamId]['login'] = $teamLoginRow['team_activities_count'];

            //Map 'teams_id' => 'team_name'
            $teamNamesById[$teamId] = $teamLoginRow['team_name'];
        }

        //Loading Counts into DTOs
        $teamsDto = new TeamsDto();
        $teamsDto->setPersonId($loggedInUserId);
        $recentActivitiesDtoArray = [];
        foreach ($teamIds as $teamId) {

            if (isset($teamActivitiesJoined[$teamId])) {

                $teamActivityCountDto = new TeamActivityCountDto();
                $teamActivityCountDto->setTeamId($teamId);
                $teamActivityCountDto->setTeamName($teamNamesById[$teamId]);
                if (isset($teamActivitiesJoined[$teamId]['open-referral'])) {
                    $teamActivityCountDto->setTeamOpenReferrals($teamActivitiesJoined[$teamId]['open-referral']);
                } else {
                    $teamActivityCountDto->setTeamOpenReferrals(0);
                }

                if (isset($teamActivitiesJoined[$teamId]['interaction'])) {
                    $teamActivityCountDto->setTeamActivities($teamActivitiesJoined[$teamId]['interaction']);
                } else {
                    $teamActivityCountDto->setTeamActivities(0);
                }

                if (isset($teamActivitiesJoined[$teamId]['login'])) {
                    $teamActivityCountDto->setTeamLogins($teamActivitiesJoined[$teamId]['login']);
                } else {
                    $teamActivityCountDto->setTeamLogins(0);
                }
                $recentActivitiesDtoArray[] = $teamActivityCountDto;
            }

        }

        $teamsDto->setRecentActivities($recentActivitiesDtoArray);

        $this->logger->info("Get My Teams Recent Activities for Logged UserId and Filter ");
        return $teamsDto;


    }


    /**
     * Checks permissions to activity (assumes connection to the student and individual permission)
     * If the loggedInUser does not have permission, removes reason_text
     * Also Adds Activity Type and Student Status to returned Array
     *
     * @param array $teamActivitiesArray
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param array $personTeamIdsArray
     * @return array
     */
    public function applyPermissionsAndAddColumnsStudentStatusAndActivityType($teamActivitiesArray, $loggedInUserId, $organizationId, $personTeamIdsArray)
    {
        $teamActivityArrayWithPermissionsApplied = [];

        if (count($teamActivitiesArray) > 0) {

            foreach ($teamActivitiesArray as $activity) {
                if (isset($activity['status'])) {
                    $activityStatus = $activity['status'];
                } else {
                    $activityStatus = '';
                }

                if ($activity['activity_code'] != "L") {
                    $studentObject = $this->orgPersonStudentRepository->findOneBy(['person' => $activity['student_id']]);
                    $activity['student_status'] = $studentObject->getStatus();

                    //Map FeatureName to Activity
                    $databaseFeatureNames = [
                        'A' => 'Booking',
                        'C' => 'Log Contacts',
                        'E' => 'Email',
                        'N' => 'Notes',
                        'R' => 'Referrals'
                    ];

                    $featureName = $databaseFeatureNames[$activity['activity_code']];

                    //Seeing if the user has access to the given feature and allowing reason to be set if access is there
                    //Assumption: Repository has already limited the activities to students I have access to.
                    $feature = $this->featureMasterLangRepository->findOneBy(['featureName' => $featureName]);
                    if (isset($feature)) {
                        $permissionSetIds = explode(",", $activity['org_permissionset_ids']);
                        $featureAccess = $this->orgPermissionSetFeaturesRepository->getFeaturePermissions($permissionSetIds, $organizationId, $feature->getId());
                        if (!$this->activityService->verifyAccessToActivity($organizationId, $loggedInUserId, $activity, $featureAccess, $personTeamIdsArray)) {
                            $activity['reason_text'] = null;
                        }
                    }
                } else {
                    //For some reason, the frontend expects these to be either empty strings or populated
                    // and messes up if I leave these null
                    $activity['student_firstname'] = '';
                    $activity['student_lastname'] = '';
                }
                $activityId = $this->activityService->getActivityId($activity);
                $activity['activity_id'] = $activityId;

                //Map Code to Type
                $databaseActivityTypes = [
                    'A' => 'appointment',
                    'C' => 'contact',
                    'L' => 'login',
                    'N' => 'note',
                    'R' => 'referral',
                ];

                $activityName = $databaseActivityTypes[$activity['activity_code']];

                //Setting to open referral if status is open
                if ($activityStatus == 'O' && $activityName == 'referral') {
                    $activityName = 'open referral';
                }

                $activity['activity_type'] = $activityName;

                $teamActivityArrayWithPermissionsApplied[] = $activity;

            }
        }
        return $teamActivityArrayWithPermissionsApplied;
    }


    /**
     * Function to get the Team activities based the parameters
     *
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $teamId
     * @param string $teamMemberIdsString
     * @param string $activityType - 'interaction'|'open-referral'|'login'
     * @param string $timePeriod - today | week | month | custom filter
     * @param string $customStartDate - 'yyyy-dd-mm'
     * @param string $customEndDate - 'yyyy-dd-mm'
     * @param string $pageNumber
     * @param string $recordsPerPage
     * @param string $sortBy
     * @return TeamsDto
     */
    public function getActivityDetailsOfMyTeam($organizationId, $loggedInUserId, $teamId, $teamMemberIdsString, $activityType, $timePeriod, $customStartDate, $customEndDate, $pageNumber = null, $recordsPerPage = null, $sortBy = '')
    {
        $this->logger->debug("Getting My Teams' Activities Details, Team Id: " . $teamId . "TeamMemberId: " . $teamMemberIdsString . "ActivityType: " . $activityType);

        $this->validateTeamActivityDatesAndTeamMembers($timePeriod, $customStartDate, $customEndDate, $teamMemberIdsString);

        //Converting string to int array
        $teamMemberIdsArray = array_map('intval', explode(',', $teamMemberIdsString));

        //Validate Incoming $teamId as a team the loggedInUser is a part of
        $personTeams = $this->teamMembersRepository->getMyTeams($loggedInUserId, $organizationId);
        $personTeamIdsArray = array_column($personTeams, 'team_id');
        $isValidTeamId = in_array($teamId, $personTeamIdsArray);

        if (!$isValidTeamId) {
            throw new SynapseValidationException('You are not a member of the selected Team');

        }

        //Get Utc Time Range Using Organization's Time and specified time period
        $dateTimeRange = $this->getUtcDateRangeUsingOrganizationTimezoneAdjustedDate($timePeriod, $organizationId, $customStartDate, $customEndDate);
        $fromDate = $dateTimeRange['from_date'];
        $toDate = $dateTimeRange['to_date'];

        // Added this to find the current academic year start and end
        // for restricting the view to the current academic year ESPRJ-5571
        //Find the current academic year start and end for restricting the view to the current academic year
        $currentUtcDateTimeObject = new \DateTime('now');
        $orgAcademicYear = $this->orgAcademicYearRepository->getCurrentAcademicDetails($currentUtcDateTimeObject, $organizationId);
        $currentAcademicYearId = NULL;
        if (!empty($orgAcademicYear)) {
            $academicYearStartDate = $orgAcademicYear[0]['startDate'];
            $academicYearEndDate = $orgAcademicYear[0]['endDate'];
            $currentAcademicYearId = $orgAcademicYear[0]['id'];
        } else {
            $academicYearStartDate = $fromDate;
            $academicYearEndDate = $toDate;
        }

        // Get Pagination Details
        if (!$pageNumber) {
            $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }
        if (!$recordsPerPage) {
            $recordsPerPage = SynapseConstant::DEFAULT_RECORD_COUNT;
        }

        //Get Activity Codes
        $activityCodes = $this->getActivityCodesForTeamActivities($activityType);

        //Retrieve all data
        $teamActivitiesArray = $this->teamMembersRepository->getActivityDetailsOfMyTeam($activityType, $activityCodes, $loggedInUserId, $organizationId, $fromDate, $toDate, $academicYearStartDate, $academicYearEndDate, $teamMemberIdsArray, $pageNumber, $recordsPerPage, $sortBy, $currentAcademicYearId);
        $teamActivitiesCountArray = $this->teamMembersRepository->getActivityCountsOfMyTeamByActivityType($activityType, $activityCodes, $fromDate, $toDate, $loggedInUserId, $organizationId, $academicYearStartDate, $academicYearEndDate, $teamId, $currentAcademicYearId);

        $teamsDto = new TeamsDto();
        $teamsDto->setPersonId($loggedInUserId);
        $teamsDto->setTeamId($teamId);
        $teamsDto->setTeamMemberIds($teamMemberIdsString);

        if (isset($teamActivitiesCountArray[0]['team_activities_count'])) {
            $totalCount = $teamActivitiesCountArray[0]['team_activities_count'];
        } else {
            $totalCount = 0;
        }

        //ceil returns float, need integer
        $totalPageCount = intval(ceil($totalCount / $recordsPerPage));

        //Fixing Format for Front End (existing code)
        $activityTypeFormatted = strtolower($activityType);
        if ($activityTypeFormatted == "open-referral") {
            $activityTypeFormatted = "Open Referral";
        }

        //Load Teams Dto
        $teamsDto->setActivityType($activityTypeFormatted);
        $teamsDto->setFilter($timePeriod);
        $teamsDto->setTotalRecords($totalCount);
        $teamsDto->setTotalPages($totalPageCount);
        $teamsDto->setRecordsPerPage($recordsPerPage);
        $teamsDto->setCurrentPage($pageNumber);
        $teamMemberActivitiesDtoArray = [];

        if ($totalCount > 0) {
            $teamActivityArrayWithPermissionsApplied = $this->applyPermissionsAndAddColumnsStudentStatusAndActivityType($teamActivitiesArray, $loggedInUserId, $organizationId, $personTeamIdsArray);

            foreach ($teamActivityArrayWithPermissionsApplied as $activity) {
                //Load Team Members Activities Dto
                $teamMembersActivitiesDto = new TeamMembersActivitiesDto();

                $activityDate = new \DateTime($activity['activity_date']);
                $teamMembersActivitiesDto->setDate($activityDate->format('Y-m-d\TH:i:sO'));

                $teamMembersActivitiesDto->setTeamMemberId($activity['team_member_id']);
                $teamMembersActivitiesDto->setTeamMemberFirstName($activity['team_member_firstname']);
                $teamMembersActivitiesDto->setTeamMemberLastName($activity['team_member_lastname']);
                $teamMembersActivitiesDto->setTeamMemberEmailId($activity['primary_email']);
                $teamMembersActivitiesDto->setTeamMemberExternalId($activity['team_member_external_id']);
                $teamMembersActivitiesDto->setStudentId($activity['student_id']);
                $teamMembersActivitiesDto->setStudentExternalId($activity['student_external_id']);
                $teamMembersActivitiesDto->setStudentEmail($activity['student_email']);
                $teamMembersActivitiesDto->setStudentFirstName($activity['student_firstname']);
                $teamMembersActivitiesDto->setStudentLastName($activity['student_lastname']);
                $teamMembersActivitiesDto->setStudentStatus($activity['student_status']);
                $teamMembersActivitiesDto->setActivityId($activity['activity_id']);
                $teamMembersActivitiesDto->setActivityType($activity['activity_type']);
                $teamMembersActivitiesDto->setReasonText($activity['reason_text']);

                $teamMemberActivitiesDtoArray[] = $teamMembersActivitiesDto;

            }
        }
        $teamsDto->setTeamMembersActivities($teamMemberActivitiesDtoArray);
        $this->logger->info("Retrieved My Teams' Activities Details");
        return $teamsDto;
    }


    /**
     * Creates Job for generating a Team Activities CSV
     *
     * @param int $organizationId
     * @param int $loggedInUserId
     * @param int $teamId
     * @param string $teamMemberIdsString
     * @param string $activityType - 'interaction'|'open-referral'|'login'
     * @param string $timePeriod - 'today'|'week'|'month'|'custom'
     * @param string $customStartDate - 'yyyy-mm-dd'
     * @param string $customEndDate - 'yyyy-mm-dd'
     * @return string
     */
    public function createActivityDetailsCsvJob($organizationId, $loggedInUserId, $teamId, $teamMemberIdsString, $activityType, $timePeriod, $customStartDate, $customEndDate)
    {
        $organizationCurrentDateTime = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, 'Ymd_His');

        // Creates the job for CSV generation
        $jobNumber = uniqid();
        $job = new TeamActivitiesCSVJob();
        $job->args = array(
            'jobNumber' => $jobNumber,
            'loggedUserId' => $loggedInUserId,
            'teamId' => $teamId,
            'teamMemberIdsString' => $teamMemberIdsString,
            'timePeriod' => $timePeriod,
            'customStartDate' => $customStartDate,
            'customEndDate' => $customEndDate,
            'organizationId' => $organizationId,
            'organizationCurrentDatetime' => $organizationCurrentDateTime,
            'activity_type' => $activityType
        );

        // Puts the created job in job-queue
        $this->resque->enqueue($job, true);
        return SynapseConstant::DOWNLOAD_IN_PROGRESS_MESSAGE;

    }


    /**
     * Function to get the Team activities formatted for the CSV job
     *
     * @param int $organizationId
     * @param int $loggedInUserId
     * @param int $teamId
     * @param string $teamMemberIdsString
     * @param string $activityType - 'interaction'|'open-referral'|'login'
     * @param string $timePeriod - 'today' | 'week' | 'month' | 'custom'
     * @param string $customStartDate - 'yyyy-dd-mm'
     * @param string $customEndDate - 'yyyy-dd-mm'
     * @return string
     */
    public function getActivityDetailForMyTeamsCSV($organizationId, $loggedInUserId, $teamId, $teamMemberIdsString, $activityType, $timePeriod, $customStartDate, $customEndDate)
    {
        $this->validateTeamActivityDatesAndTeamMembers($timePeriod, $customStartDate, $customEndDate, $teamMemberIdsString);

        //Converting string to int array
        $teamMemberIdsArray = array_map('intval', explode(',', $teamMemberIdsString));

        //Validate Incoming $teamId as a team the loggedInUser is a part of
        $personTeams = $this->teamMembersRepository->getMyTeams($loggedInUserId, $organizationId);
        $personTeamIdsArray = array_column($personTeams, 'team_id');
        $isValidTeamId = in_array($teamId, $personTeamIdsArray);

        if (!$isValidTeamId) {
            throw new SynapseValidationException('You are not a member of the selected Team');
        }

        //Get Utc Time Range Using Organization's Time and specified time period
        $dateTimeRange = $this->getUtcDateRangeUsingOrganizationTimezoneAdjustedDate($timePeriod, $organizationId, $customStartDate, $customEndDate);
        $fromDate = $dateTimeRange['from_date'];
        $toDate = $dateTimeRange['to_date'];

        // Added this to find the current academic year start and end
        // for restricting the view to the current academic year ESPRJ-5571
        //Find the current academic year start and end for restricting the view to the current academic year
        $currentUtcDateTimeObject = new \DateTime('now');
        $orgAcademicYear = $this->orgAcademicYearRepository->getCurrentAcademicDetails($currentUtcDateTimeObject, $organizationId);


        if (!empty($orgAcademicYear)) {
            $academicYearStartDate = $orgAcademicYear[0]['startDate'];
            $academicYearEndDate = $orgAcademicYear[0]['endDate'];
            $currentAcademicYearId = $orgAcademicYear[0]['id'];

        } else {
            $academicYearStartDate = $fromDate;
            $academicYearEndDate = $toDate;
            $currentAcademicYearId = null;
        }

        //Get Activity Codes
        $activityCodes = $this->getActivityCodesForTeamActivities($activityType);

        $teamActivitiesArray = $this->teamMembersRepository->getActivityDetailsOfMyTeam($activityType, $activityCodes, $loggedInUserId, $organizationId, $fromDate, $toDate, $academicYearStartDate, $academicYearEndDate, $teamMemberIdsArray, null, null, '', $currentAcademicYearId);
        $teamActivitiesArrayWithPermissionsApplied = $this->applyPermissionsAndAddColumnsStudentStatusAndActivityType($teamActivitiesArray, $loggedInUserId, $organizationId, $personTeamIdsArray);

        return $teamActivitiesArrayWithPermissionsApplied;
    }


    /**
     * Get Utc Date Range Using Organization's Current Time in their Timezone
     * Use Custom Dates if the time period for the range is custom
     *
     * @param string $timePeriod - 'today' | 'week' | 'month' | 'custom' filter
     * @param string $customStartDate - 'yyyy-dd-mm'
     * @param string $customEndDate - 'yyyy-dd-mm'
     * @param int $organizationId
     * @return array
     */
    public function getUtcDateRangeUsingOrganizationTimezoneAdjustedDate($timePeriod, $organizationId, $customStartDate = '', $customEndDate = '')
    {
        $dateTimeRange = [];

        //Custom Dates from Frontend Are in Users Time Zone and need to be converted to UTC
        if ($timePeriod == 'custom') {
            $dateTimeRange['from_date'] = $this->dateUtilityService->convertToUtcDatetime($organizationId, $customStartDate, false);
            $dateTimeRange['to_date'] = $this->dateUtilityService->convertToUtcDatetime($organizationId, $customEndDate, true);
        } else {
            //getting Current DateTime Object for Organization
            $currentDateTimeObject = $this->dateUtilityService->getTimezoneAdjustedCurrentDateTimeForOrganization($organizationId);

            $dateTimeRange = $this->dateUtilityService->buildDateTimeRangeByTimePeriodAndDateTimeObject($timePeriod, $currentDateTimeObject);
            $dateTimeRange['from_date'] = $this->dateUtilityService->getFormattedCurrentUtcDateTimeStringFromOrganizationDateTimeString($dateTimeRange['from_date'], $organizationId);
            $dateTimeRange['to_date'] = $this->dateUtilityService->getFormattedCurrentUtcDateTimeStringFromOrganizationDateTimeString($dateTimeRange['to_date'], $organizationId);
        }

        return $dateTimeRange;
    }


    /**
     * Throws Exception if incoming Dates and Arrays Fail Validation
     *
     * @param string $timePeriod - 'today' | 'week' | 'month' | 'custom' filter
     * @param string $startDate - 'yyyy-dd-mm'
     * @param string $endDate - 'yyyy-dd-mm'
     * @param array $teamMemberIds
     * @throws SynapseValidationException
     */
    public function validateTeamActivityDatesAndTeamMembers($timePeriod, $startDate, $endDate, $teamMemberIds)
    {
        $startUnixTimeStamp = strtotime($startDate);
        $endUnixTimeStamp = strtotime($endDate);

        if ($timePeriod == 'custom' && (is_bool($startUnixTimeStamp) || is_bool($endUnixTimeStamp))) {
            throw new SynapseValidationException('Start date and end date are not valid.  This filter is mandatory for custom filter');
        }
        if ($timePeriod == 'custom' && $endUnixTimeStamp < $startUnixTimeStamp) {
            throw new SynapseValidationException('Start date cannot be greater than end date');
        }

        if (empty($teamMemberIds)) {
            throw new SynapseValidationException('Team member ids filter cannot be empty');
        }
    }


    /**
     * Retrieve Activity Codes for Given Activity Type
     * TODO: These relationships should really be in the database in some way
     *
     * @param string $activityType 'open-referral'|'interaction'|'login'|'all'  (all is not currently used)
     * @return array
     */
    public function getActivityCodesForTeamActivities($activityType)
    {
        if ($activityType == "open-referral") {
            $activityCodes = ['R'];
        } elseif ($activityType == "interaction") {
            $activityCodes = ['A', 'C', 'N', 'R'];
        } elseif ($activityType == "login") {
            $activityCodes = ['L'];
        } else {
            $activityCodes = ['A', 'C', 'E', 'L', 'N', 'R'];
        }
        return $activityCodes;
    }
}