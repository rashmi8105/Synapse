<?php
namespace Synapse\ReportsBundle\Job;

use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;


class ActivityCSVJob extends ReportsJob
{

    //scaffolding
    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    //services
    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    //Repositories

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;
    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var ReportsRepository
     */
    private  $reportsRepository;


    public function run($args)
    {
        //scaffolding
        $this->repositoryResolver = $this->getContainer()->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);

        //services
        $this->academicYearService = $this->getContainer()->get(AcademicYearService::SERVICE_KEY);

        //repositories
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->reportsRepository = $this->repositoryResolver->getRepository(ReportsRepository::REPOSITORY_KEY);


        $orgId = $args['orgId'];
        $loggedInUserId = $args['personId'];
        $person = $this->personRepository->find($args['personId']);

        if (isset($args['access']) && $args['access'] == 1) {
            $accessPersonId = '';
        } else {
            $accessPersonId = $loggedInUserId;
        }

        // find out the list of participating students.
        $currentAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($orgId);
        $currentAcademicYearId = $currentAcademicYear['org_academic_year_id'];
        $participantStudentsListforStaff = $this->orgPermissionsetRepository->getStudentsForStaff($loggedInUserId, $currentAcademicYearId);

        $stdActivitys = $this->reportsRepository->getCampusActvityDetails($orgId, $args['yearStartDate'], $args['yearEndDate'], $args['type'], $participantStudentsListforStaff, $accessPersonId, $loggedInUserId, $args['sharingAccess']);

        $csvHeader = [
            'ExternalID',
            'StudentFirstName',
            'StudentLastName',
            'PrimaryEmail',
            'CreatedbyFirstName',
            'CreatedbyLastName',
            'CreatedDate',
            'ActivityType',
            'Description',
            'Reason',
            'ContactType',
            'Open/CloseStatus',
            'AssignedTo',
            'InterestedParty',
            'SharingLevel',
            'Attended Appointment'
        ];

        $currentDateTime = date("Y-m-d_H-i-s", strtotime($args['currentDate']));
        $completeFilePath = "roaster_uploads/{$orgId}-{$loggedInUserId}-{$currentDateTime}-activity-report.csv";

        $csvFilePath = @fopen("data://{$completeFilePath}", 'w+');
        fputcsv($csvFilePath, $csvHeader);

        if (isset($stdActivitys) && count($stdActivitys) > 0) {
            foreach ($stdActivitys as $stdActivity) {

                $activityStatus = '';
                $hasStdAttended = '';

                if ($stdActivity[ReportsConstants::ACTIVITY_TYPE] == "Referral") {

                    if ($stdActivity[ReportsConstants::ACTIVITY_STATUS] == "C") {
                        $activityStatus = ReportsConstants::REFERAL_STATUS_CLOSED;
                    } else {
                        $activityStatus = ReportsConstants::REFERAL_STATUS_OPEN;
                    }

                }

                if ($stdActivity[ReportsConstants::ACTIVITY_TYPE] == "Appointment") {
                    $hasStdAttended = $stdActivity['has_attended'] == 1 ? 'Yes' : 'No';
                }
                if ($stdActivity['access_team'] == 1) {
                    $sharingLevel = 'Team';
                } elseif ($stdActivity['access_private'] == 1) {
                    $sharingLevel = 'Private';
                } else {
                    $sharingLevel = 'Public';
                }

                $activity = [
                    $stdActivity['external_id'],
                    $stdActivity['student_firstname'],
                    $stdActivity['student_lastname'],
                    $stdActivity['email'],
                    $stdActivity['faculty_firstname'],
                    $stdActivity['faculty_lastname'],
                    $stdActivity['created_at'],
                    ucfirst($stdActivity[ReportsConstants::ACTIVITY_TYPE]),
                    $stdActivity['details'],
                    $stdActivity['reason'],
                    $stdActivity['contact_type'],
                    $activityStatus,
                    $stdActivity['assignedpersonname'],
                    $stdActivity['interested_parties'],
                    $sharingLevel,
                    $hasStdAttended
                ];
                fputcsv($csvFilePath, $activity);

            }
        }
        fclose($csvFilePath);
        $alertService = $this->getContainer()->get(ReportsConstants::ALERT_SERVICE);
        $alertService->createNotification(ReportsConstants::ACTIVITY_DOWNLOAD, ReportsConstants::ACTIVITY_DOWNLOAD_DESCRIPTION, $person, NULL, NULL, NULL, $completeFilePath, NULL, NULL, NULL, TRUE);
    }
}