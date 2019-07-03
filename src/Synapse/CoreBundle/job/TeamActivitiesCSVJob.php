<?php
namespace Synapse\CoreBundle\job;

use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\TeamActivityService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\UploadBundle\Job\CsvJob;

class TeamActivitiesCSVJob extends CsvJob
{
    //Class Constants
    const TEAM_ACTIVITES_DOWNLOAD_COMPLETE_MESSAGE = "Your Team Activities download has completed";

    //Services
    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationsService;

    /**
     * @var CSVUtilityService
     */
    private $csvUtilityService;


    /**
     * @var TeamActivityService
     */
    private $teamActivityService;


    public function run($args)
    {
        //Variables
        $loggedInUserId = $args['loggedUserId'];
        $teamId = $args['teamId'];
        $teamMemberIdsString = $args['teamMemberIdsString'];
        $timePeriod = $args['timePeriod'];
        $customStartDate = $args['customStartDate'];
        $customEndDate = $args['customEndDate'];
        $organizationId = $args['organizationId'];
        $activityType = $args['activity_type'];
        $organizationCurrentDateTime = $args['organizationCurrentDatetime'];

        //Services
        $this->alertNotificationsService = $this->getContainer()->get('alertNotifications_service');
        $this->csvUtilityService = $this->getContainer()->get('csv_utility_service');
        $this->teamActivityService = $this->getContainer()->get('team_activity_service');

        $teamActivityArrayWithPermissionsApplied = $this->teamActivityService->getActivityDetailForMyTeamsCSV($organizationId, $loggedInUserId, $teamId, $teamMemberIdsString, $activityType, $timePeriod, $customStartDate, $customEndDate);

        $columnHeaders = [
            'activity_date' => 'Date',
            'team_member_external_id' => 'Team Member External Id',
            'team_member_firstname' => 'Team Member First Name',
            'team_member_lastname' => 'Team Member Last Name',
            'primary_email' => 'Primary Email',
            'student_external_id' => 'Student External Id',
            'student_firstname' => 'Student First Name',
            'student_lastname' => 'Student Last Name',
            'student_email' => 'Student Email',
            'activity_type' => 'Activity Type',
            'reason_text' => 'Reason Text'
        ];

        $fileName = $organizationId . "-team_" . $organizationCurrentDateTime . ".csv";
        $completeFilePath = SynapseConstant::S3_ROOT . SynapseConstant::S3_CSV_EXPORT_DIRECTORY . "/";
        $filePathForNotification = SynapseConstant::S3_CSV_EXPORT_DIRECTORY . "/$fileName";

        // Generates the CSV and stores it to defined path
        $this->csvUtilityService->generateCSV($completeFilePath, $fileName, $teamActivityArrayWithPermissionsApplied, $columnHeaders);

        //Create Alert Notification after CSV generation
        $this->alertNotificationsService->createCSVDownloadNotification('Team_Activity_Download', self::TEAM_ACTIVITES_DOWNLOAD_COMPLETE_MESSAGE, $filePathForNotification, $loggedInUserId);
    }
}