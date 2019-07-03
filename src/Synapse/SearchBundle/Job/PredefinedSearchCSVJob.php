<?php
namespace Synapse\SearchBundle\Job;

use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Repository\SearchRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\SearchBundle\Service\Impl\PredefinedSearchService;
use Synapse\UploadBundle\Job\CsvJob;

class PredefinedSearchCSVJob extends CsvJob
{
    const NOTIFICATION_KEY = 'Predefined_Search_Download';

    const NOTIFICATION_MESSAGE = 'Your predefined search download has completed.';

    private $columnHeaders = [
        'student_first_name' => 'Student First Name',
        'student_last_name' => 'Student Last Name',
        'external_id' => 'External Id',
        'student_primary_email' => 'Email',
        'student_risk_status' => 'Risk',
        'student_intent_to_leave' => 'Intent To Leave',
        'student_classlevel' => 'Class Standing',
        'student_status' => 'Is Active',
        'student_logins' => 'Activities Logged',
        'last_activity' => 'Last Activity'
    ];


    // Scaffolding

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;


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
     * @var PredefinedSearchService
     */
    private $predefinedSearchService;


    // Repositories

    /**
     * @var SearchRepository
     */
    private $ebiSearchRepository;


    /**
     * Fetches the data for a predefined search, creates a CSV with this data, and creates a notification for the user to download the CSV.
     *
     * @param array $args
     */
    public function run($args)
    {
        // Initialize services and repositories.
        $this->repositoryResolver = $this->getContainer()->get('repository_resolver');
        $this->alertNotificationsService = $this->getContainer()->get(AlertNotificationsService::SERVICE_KEY);
        $this->csvUtilityService = $this->getContainer()->get(CSVUtilityService::SERVICE_KEY);
        $this->dateUtilityService = $this->getContainer()->get(DateUtilityService::SERVICE_KEY);
        $this->predefinedSearchService = $this->getContainer()->get(PredefinedSearchService::SERVICE_KEY);
        $this->ebiSearchRepository = $this->repositoryResolver->getRepository(SearchRepository::REPOSITORY_KEY);

        // Retrieve parameters.
        $predefinedSearchKey = $args['predefined_search_key'];
        $facultyId = $args['faculty_id'];
        $organizationId = $args['organization_id'];
        $sortBy = $args['sort_by'];
        $onlyIncludeActiveStudents = $args['only_include_active_students'];

        // Format the file name and path.
        $currentDateTime = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, 'Ymd_His');
        $filePath = SynapseConstant::S3_ROOT . SynapseConstant::S3_CSV_EXPORT_DIRECTORY . '/';
        $fileName = "$organizationId-$predefinedSearchKey" . "_$currentDateTime.csv";
        $filePathForNotification = SynapseConstant::S3_CSV_EXPORT_DIRECTORY . "/$fileName";

        // Set up rows to be put at the top of the CSV.
        $predefinedSearchName = $this->ebiSearchRepository->findOneBy(['queryKey' => $predefinedSearchKey])->getName();
        $preliminaryRows = [
            ['Predefined Search'],
            [$predefinedSearchName],
            ['']
        ];

        // Get the records and put them in the CSV.
        $records = $this->predefinedSearchService->getPredefinedSearchResultsForCSV($predefinedSearchKey, $facultyId, $organizationId, $sortBy, $onlyIncludeActiveStudents);

        $this->csvUtilityService->generateCSV($filePath, $fileName, $records, $this->columnHeaders, $preliminaryRows);

        // Create a notification to allow the user to download the CSV.
        $this->alertNotificationsService->createCSVDownloadNotification(self::NOTIFICATION_KEY, self::NOTIFICATION_MESSAGE, $filePathForNotification, $facultyId);
    }
}