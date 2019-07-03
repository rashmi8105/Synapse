<?php

namespace Synapse\SearchBundle\Job;

use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\SearchBundle\Service\Impl\SearchService;
use Synapse\UploadBundle\Job\CsvJob;

class CustomSearchCSVJob extends CsvJob
{
    const CUSTOM_SEARCH_NOTIFICATION_KEY = 'Custom_Search_Download';

    const SURVEY_COMPLETION_NOTIFICATION_KEY = 'Survey_Completion_Download';

    const CUSTOM_SEARCH_NOTIFICATION_MESSAGE = 'Your custom search download has completed.';

    const SURVEY_COMPLETION_NOTIFICATION_MESSAGE = 'Your survey completion download has completed.';

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
     * @var SearchService
     */
    private $searchService;


    /**
     * Fetches the data for a custom search, creates a CSV with this data, and creates a notification for the user to download the CSV.
     *
     * @param array $args
     */
    public function run($args)
    {
        // Initialize services.
        $this->alertNotificationsService = $this->getContainer()->get(AlertNotificationsService::SERVICE_KEY);
        $this->csvUtilityService = $this->getContainer()->get(CSVUtilityService::SERVICE_KEY);
        $this->dateUtilityService = $this->getContainer()->get(DateUtilityService::SERVICE_KEY);
        $this->searchService = $this->getContainer()->get(SearchService::SERVICE_KEY);

        // Retrieve parameters.
        $customSearchDTO = unserialize($args['custom_search_dto']);
        $facultyId = $args['faculty_id'];
        $organizationId = $args['organization_id'];
        $sortBy = $args['sort_by'];

        $searchTypeKey = $customSearchDTO->getSearchType();

        if ($searchTypeKey == 'survey_completion') {
            $searchAttributes = $customSearchDTO->getSearchAttributes();

            $tookSurvey = $searchAttributes['survey_status'][0]['is_completed'];
            $title = 'Survey Completion';
            if ($tookSurvey) {
                $title .= ': Took Survey';
            } elseif ($tookSurvey === false) {
                $title .= ': Did Not Take Survey';
            } else {
                //Append nothing to $title
            }

            $surveyFilter = $searchAttributes['survey_filter'];
            $surveyName = $surveyFilter['survey_name'];
            $cohortName = $surveyFilter['cohort_name'];
            $yearName = $surveyFilter['year_name'];

            $preliminaryRows = [
                [$title],
                ["$surveyName, $cohortName ($yearName)"],
                ['']
            ];

            $notificationKey = self::SURVEY_COMPLETION_NOTIFICATION_KEY;
            $notificationMessage = self::SURVEY_COMPLETION_NOTIFICATION_MESSAGE;

        } else {
            // $searchTypeKey is 'custom_search'
            $preliminaryRows = [
                ['Custom Search'],
                ['Search Attributes-> ', $customSearchDTO->getSelectedAttributesCsv()],
                ['']
            ];

            $notificationKey = self::CUSTOM_SEARCH_NOTIFICATION_KEY;
            $notificationMessage = self::CUSTOM_SEARCH_NOTIFICATION_MESSAGE;
        }

        // Format the file name and path.
        $currentDateTime = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, 'Ymd_His');
        $filePath = SynapseConstant::S3_ROOT . SynapseConstant::S3_CSV_EXPORT_DIRECTORY . '/';
        $fileName = "$organizationId-$searchTypeKey" . "_$currentDateTime.csv";
        $filePathForNotification = SynapseConstant::S3_CSV_EXPORT_DIRECTORY . "/$fileName";

        // Get the records and put them in the CSV.
        $records = $this->searchService->getCustomSearchResultsForCSV($customSearchDTO, $facultyId, $organizationId, $sortBy);

        $this->csvUtilityService->generateCSV($filePath, $fileName, $records, $this->columnHeaders, $preliminaryRows);

        // Create a notification to allow the user to download the CSV.
        $this->alertNotificationsService->createCSVDownloadNotification($notificationKey, $notificationMessage, $filePathForNotification, $facultyId);
    }

}