<?php
namespace Synapse\CoreBundle\job;

use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\PriorityStudentsService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\UploadBundle\Job\CsvJob;

class PriorityStudentsCSVJob extends CsvJob
{
    const NOTIFICATION_KEY = 'My_Students_Download';

    const NOTIFICATION_MESSAGE = 'Your My Students download has completed.';

    private $columnHeaders = [
        'student_first_name' => 'Student First Name',
        'student_last_name' => 'Student Last Name',
        'external_id' => 'External Id',
        'student_primary_email' => 'Email',
        'student_risk_status' => 'Risk',
        'student_intent_to_leave' => 'Intent To Leave',
        'student_classlevel' => 'Class Standing',
        'student_logins' => 'Activities Logged',
        'student_status' => 'Is Active',
        'last_activity' => 'Last Activity'
    ];

    private $riskColors = [
        'red2',
        'red',
        'yellow',
        'green',
        'gray'
    ];

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationsService;

    /**
     * @var CSVUtilityService
     */
    private $csvUtilityService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var PriorityStudentsService
     */
    private $priorityStudentsService;


    /**
     * Fetches the data for a drilldown from the My Students module, creates a CSV with this data, and creates a notification for the user to download the CSV.
     *
     * @param array $args
     */
    public function run($args)
    {
        // Initialize services.
        $this->alertNotificationsService = $this->getContainer()->get(AlertNotificationsService::SERVICE_KEY);
        $this->csvUtilityService = $this->getContainer()->get(CSVUtilityService::SERVICE_KEY);
        $this->dateUtilityService = $this->getContainer()->get(DateUtilityService::SERVICE_KEY);
        $this->priorityStudentsService = $this->getContainer()->get(PriorityStudentsService::SERVICE_KEY);

        // Retrieve parameters.
        $searchKey = $args['search_key'];
        $facultyId = $args['faculty_id'];
        $organizationId = $args['organization_id'];
        $sortBy = $args['sort_by'];
        $onlyIncludeActiveStudents = $args['only_include_active_students'];

        // Format the file name and path.
        if (in_array($searchKey, $this->riskColors)) {
            $searchKeyForFileName = "my_students_with_$searchKey" . "_risk";
        } else {
            $searchKeyForFileName = $searchKey;
        }

        $currentDateTime = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, 'Ymd_His');
        $filePath = SynapseConstant::S3_ROOT . SynapseConstant::S3_CSV_EXPORT_DIRECTORY . '/';
        $fileName = "$organizationId-$searchKeyForFileName" . "_$currentDateTime.csv";
        $filePathForNotification = SynapseConstant::S3_CSV_EXPORT_DIRECTORY . "/$fileName";

        // Set up rows to be put at the top of the CSV.
        if (in_array($searchKey, $this->riskColors)) {
            $title = "My students with $searchKey risk";
        } elseif ($searchKey == 'my_students') {
            $title = 'My students';
        } elseif ($searchKey == 'high_priority_students') {
            $title = 'High priority students';
        } else {
            $title = '';
        }

        $preliminaryRows = [
            [$title],
            ['']
        ];

        // Get the records and put them in the CSV.
        $records = $this->priorityStudentsService->getMyStudentsForCSV($searchKey, $facultyId, $organizationId, $sortBy, $onlyIncludeActiveStudents);

        $this->csvUtilityService->generateCSV($filePath, $fileName, $records, $this->columnHeaders, $preliminaryRows);

        // Create a notification to allow the user to download the CSV.
        $this->alertNotificationsService->createCSVDownloadNotification(self::NOTIFICATION_KEY, self::NOTIFICATION_MESSAGE, $filePathForNotification, $facultyId);
    }
}