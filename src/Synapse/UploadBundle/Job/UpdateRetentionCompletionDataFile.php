<?php
namespace Synapse\UploadBundle\Job;

use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Job\ReportsJob;
use Synapse\UploadBundle\Service\Impl\RetentionCompletionUploadService;


class UpdateRetentionCompletionDataFile extends ReportsJob
{

    //scaffolding
    /**
     * @var Container
     */
    private $container;

    //services

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
     * @var RetentionCompletionUploadService
     */
    private $retentionCompletionUploadService;


    public function run($args)
    {
        $organizationId = $args['orgId'];
        $personId = $args['person'];

        //scaffolding
        $this->container = $this->getContainer();

        //services
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->csvUtilityService = $this->container->get(CSVUtilityService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->retentionCompletionUploadService = $this->container->get(RetentionCompletionUploadService::SERVICE_KEY);


        $retentionData = $this->retentionCompletionUploadService->getRetentionCompletionDownloadData($organizationId);
        $currentDateTime = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, 'Ymd_His');

        $filePath = SynapseConstant::S3_ROOT . SynapseConstant::S3_REPORT_DOWNLOADS_DIRECTORY . '/';
        $fileName = "$organizationId-RetentionCompletion" . "_$currentDateTime.csv";
        $filePathForNotification = SynapseConstant::S3_REPORT_DOWNLOADS_DIRECTORY . "/$fileName";


        $columnHeaders = [
            'external_id' => 'ExternalId',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'primary_email' => 'PrimaryEmail',
            'retention_tracking_year' => 'Retention Tracking Year',
            'retained_to_midyear_year_1' => 'Retained to Midyear Year 1',
            'retained_to_start_of_year_2' => 'Retained to Start of Year 2',
            'retained_to_midyear_year_2' => 'Retained to Midyear Year 2',
            'retained_to_start_of_year_3' => 'Retained to Start of Year 3',
            'retained_to_midyear_year_3' => 'Retained to Midyear Year 3',
            'retained_to_start_of_year_4' => 'Retained to Start of Year 4',
            'retained_to_midyear_year_4' => 'Retained to Midyear Year 4',
            'completed_degree_in_1_year_or_less' => 'Completed Degree in 1 Year or Less',
            'completed_degree_in_2_years_or_less' => 'Completed Degree in 2 Years or Less',
            'completed_degree_in_3_years_or_less' => 'Completed Degree in 3 Years or Less',
            'completed_degree_in_4_years_or_less' => 'Completed Degree in 4 Years or Less',
            'completed_degree_in_5_years_or_less' => 'Completed Degree in 5 Years or Less',
            'completed_degree_in_6_years_or_less' => 'Completed Degree in 6 Years or Less'
        ];
        
        $this->csvUtilityService->generateCSV($filePath, $fileName, $retentionData, $columnHeaders);
        $this->alertNotificationsService->createCSVDownloadNotification('Retention_Completion_Data_Generated', "Your Retention Completion data download has completed.", $filePathForNotification, $personId);
    }

}
