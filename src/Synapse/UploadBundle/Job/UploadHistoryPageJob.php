<?php
namespace Synapse\UploadBundle\Job;

use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\UtilServiceHelper;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;

class UploadHistoryPageJob extends CsvJob
{
	// Scaffolding
	/**
	 * @var repositoryResolver
	 */
	private $repositoryResolver;

	// Repositories
	/**
	 * @var PersonRepository
	 */
	private $personRepository;

	/**
	 * @var MetadataListValuesRepository
	 */
	private $metadataListValuesRepository;

	/**
	 * @var DateUtilityService
	 */
	private $dateUtilityService;


	// Services
	/**
	 * @var AlertNotificationsService
	 */
	private $alertNotificationsService;

	/**
	 * @var csvUtilityService
	 */
	private $csvUtilityService;
	
	/**
	 * @var uploadFileLogService
	 */
	private $uploadFileLogService;

	/**
	 * @var UtilServiceHelper
	 */
	private $utilServiceHelper;


	/**
	 * @param $args
	 */
	public function run($args) {

		//Repositories
		$this->repositoryResolver = $this->getContainer()->get('repository_resolver');
		$this->metadataListValuesRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
		$this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);


		//services
		$this->alertNotificationsService = $this->getContainer()->get(AlertNotificationsService::SERVICE_KEY);
		$this->csvUtilityService = $this->getContainer()->get(CSVUtilityService::SERVICE_KEY);
		$this->dateUtilityService = $this->getContainer()->get(DateUtilityService::SERVICE_KEY);
		$this->uploadFileLogService = $this->getContainer()->get(UploadFileLogService::SERVICE_KEY);
		$this->utilServiceHelper = $this->getContainer()->get(UtilServiceHelper::SERVICE_KEY);

		$currentDateTime = $args['currentDateTime'];
		$loggedInPerson = $args['loggedInUser'];
		$organizationId = $args['orgId'];
		$filter = $args['filter'];

		// This function can throw two errors.
		// one if the organization doesn't exist. one if the timeZone doesn't exist
		try {
			$readableTimeZone = $this->dateUtilityService->getOrganizationISOTimeZone($organizationId);
		} catch (\Exception $e) {
			$readableTimeZone = SynapseConstant::SKYFACTOR_DEFAULT_TIMEZONE;
		}

		$dateHeader = "Date (".$readableTimeZone.")";

		$fileName = $organizationId."-"."upload_history_page_".$currentDateTime.".csv";
		$dataArray = $this->uploadFileLogService->listHistory($loggedInPerson, $organizationId, $pageNo='', $offset='', $sortBy='', $filter, false, true);

		$uploadHistoryData = [];
		if(count($dataArray) > 0) {
		    foreach ($dataArray as $data) {
				$dateTime = new \DateTime($data['uploaded_date']);
				$uploadedDateString = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $dateTime, SynapseConstant::DAY_MONTH_YEAR_TWELVE_HOUR_TIMEZONE_DATE_FORMAT);
		        $data['uploaded_date'] = $uploadedDateString;
		        $uploadHistoryData[] = $data;
		    }
		}

		$csvHeaders = array(
			'upload_file_log_id' => 'Upload File Log Id',
			'file_name' => 'Original File',
			'uploaded_date' => $dateHeader,
			'firstname' => 'Uploaded By First Name',
			'lastname' => 'Uploaded By Last Name',
			'UploadedByExternalId' => 'Uploaded By External Id',
			'UploadedByEmail' => 'Uploaded By Email',
			'error_file_name' => 'Error File',
			'upload_type' => 'Upload Type',
			'upload_file_name' => 'Name'
		);

		$completeFilePath = SynapseConstant::S3_ROOT . SynapseConstant::S3_CSV_EXPORT_DIRECTORY . '/';

		$this->csvUtilityService->generateCSV($completeFilePath, $fileName, $uploadHistoryData, $csvHeaders);
	
		// Create Alert Notification after CSV generation
		$personObject = $this->personRepository->find($loggedInPerson);
		$alertNotificationFileName = SynapseConstant::S3_CSV_EXPORT_DIRECTORY.'/'."$fileName";
		$this->alertNotificationsService->createNotification('Activity_Download', 'Your upload history page download has completed', $personObject, NULL, NULL, NULL, $alertNotificationFileName, NULL, NULL, NULL, TRUE);
	}
	
}

