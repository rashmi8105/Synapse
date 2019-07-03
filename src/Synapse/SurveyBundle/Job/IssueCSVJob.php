<?php
namespace Synapse\SurveyBundle\Job;

use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\MapworksToolBundle\EntityDto\IssuePaginationDTO;
use Synapse\SurveyBundle\Service\Impl\IssueService;
use Synapse\UploadBundle\Job\CsvJob;

class IssueCSVJob extends CsvJob
{
    const NOTIFICATION_MESSAGE = 'Your Issue CSV download has completed.';

    private $columnHeaders = [
        'student_first_name' => 'Student First Name',
        'student_last_name' => 'Student Last Name',
        'student_primary_email' => 'Email',
        'external_id' => 'External Id',
        'student_risk_status' => 'Risk',
        'student_intent_to_leave' => 'Intent To Leave',
        'student_classlevel' => 'Class Standing',
        'student_logins' => 'Activities Logged',
        'last_activity' => 'Last Activity'
    ];

    // Services

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
     * @var IssueService
     */
    private $issueService;

    /**
     * Fetches the data for a predefined search, creates a CSV with this data, and creates a notification for the user to download the CSV.
     *
     * @param array $args
     */
    public function run($args)
    {

        // Initialize services and repositories.
        $this->alertNotificationsService = $this->getContainer()->get(AlertNotificationsService::SERVICE_KEY);
        $this->csvUtilityService = $this->getContainer()->get(CSVUtilityService::SERVICE_KEY);
        $this->dateUtilityService = $this->getContainer()->get(DateUtilityService::SERVICE_KEY);
        $this->issueService = $this->getContainer()->get(IssueService::SERVICE_KEY);

        // Retrieve parameters.
        $facultyId = $args['faculty_id'];
        $organizationId = $args['organization_id'];
        $issueInputDTO = unserialize($args['top_issues_pagination']);


        // Format the file name and path.
        $jobKeyName = "students_top_issues";
        $currentDateTime = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, SynapseConstant::DEFAULT_CSV_FILENAME_DATETIME_FORMAT);
        $filePath = SynapseConstant::S3_ROOT . SynapseConstant::S3_CSV_EXPORT_DIRECTORY . '/';
        $fileName = $organizationId . "-" . $jobKeyName . "_" . $currentDateTime . ".csv";
        $filePathForNotification = SynapseConstant::S3_CSV_EXPORT_DIRECTORY . "/" . $fileName;

        // Get the records and put them in the CSV.
        $topIssueDTO = $this->issueService->getTopIssuesWithStudentList($organizationId, $facultyId, $issueInputDTO);
        $topIssues = $topIssueDTO->getTopIssues();

        $records = [];
        $issueString = '';
        //TODO: If there is more than one issue, sorting will not be correct.  We have no use case for more than one issue currently, hence tech debt ESPRJ-17206
        foreach ($issueInputDTO->getTopIssuesPagination() as $issuePaginationDTO) {
            if ($issuePaginationDTO->getDisplayStudents()) {
                $records = array_merge($topIssues[$issuePaginationDTO->getTopIssue() - 1]['students_with_issue_paginated_list'], $records);
                $issueString .= $topIssues[$issuePaginationDTO->getTopIssue() - 1]['name'] . ' ';
            }
        }

        // Set up rows to be put at the top of the CSV.
        $academicYearName = $topIssueDTO->getAcademicYearName();
        $surveyName = $topIssueDTO->getSurveyName();
        $cohort = $topIssueDTO->getCohort();
        $title = "Top Issues";
        $preliminaryRows = [
            [$title],
            ["$academicYearName $surveyName Cohort $cohort"],
            [$issueString],
            ['']
        ];

        $this->csvUtilityService->generateCSV($filePath, $fileName, $records, $this->columnHeaders, $preliminaryRows);

        // Create a notification to allow the user to download the CSV.
        $this->alertNotificationsService->createCSVDownloadNotification('Activity_Download', self::NOTIFICATION_MESSAGE, $filePathForNotification, $facultyId);
    }
}