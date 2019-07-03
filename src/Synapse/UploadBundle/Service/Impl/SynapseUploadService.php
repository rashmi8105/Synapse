<?php
namespace Synapse\UploadBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use Codeception\Module\Doctrine2;
use Doctrine\Common\Cache\RedisCache;
use JMS\DiExtraBundle\Annotation as DI;
use SplFileObject;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\ConstraintViolationList;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonOrgMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\EntityService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\OrgProfileService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\ProfileService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\CoreBundle\Util\CSVWriter;
use Synapse\CoreBundle\Util\UtilServiceHelper;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Handle generic faculty/student uploads
 *
 * @DI\Service("synapse_upload_service")
 */
class SynapseUploadService extends AbstractService
{

    const SERVICE_KEY = 'synapse_upload_service';

    // Class Constants

    const EXT_ID = 'ExternalId';

    const ERROR_HEADER = 'Upload Errors';

    // Class Variables

    /**
     * @var array
     */
    private $affectedExternalIds;

    /**
     * @var array
     */
    private $updateable;

    /**
     * @var array
     */
    private $createable;

    /**
     * @var array
     */
    private $updates;

    /**
     * @var array
     */
    private $creates;

    /**
     * @var array
     */
    private $updatedRows;

    /**
     * @var array
     */
    private $createdRows;

    /**
     * @var array
     */
    private $jobs;

    /**
     * @var int
     */
    private $totalRows;

    /**
     * @var int
     */
    private $uploadId;

    /**
     * @var int
     */
    private $organizationId;

    /**
     * @var string
     */
    public $entityName;


    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RedisCache
     */
    private $cache;

    /**
     * @var Doctrine2
     */
    private $doctrine;

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var \Swift_Mailer
     */
    private $transport;

    /**
     * @var \Swift_MailTransport
     */
    private $transportReal;


    // Services

    /**
     * @var AlertNotificationsService
     */
    private $alertService;

    /**
     * @var DataProcessingUtilityService
     */
    private $dataProcessingUtilityService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var EntityService
     */
    private $entityService;

    /**
     * Object containing the loaded file
     *
     * @var CSVReader
     */
    private $fileReader;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var OrgProfileService
     */
    private $orgProfileService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var ProfileService
     */
    private $profileService;

    /**
     * @var UtilServiceHelper
     */
    private $utilServiceHelper;


    // Repositories

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OrgAcademicTermRepository
     */
    private $orgAcademicTermRepository;

    /**
     * @var OrgAcademicYearRepository;
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationRoleRepository;
     */
    private $organizationRoleRepository;

    /**
     * @var PersonEbiMetaDataRepository
     */
    private $personEbiMetadataRepository;

    /**
     * @var PersonOrgMetaDataRepository
     */
    private $personOrgMetadataRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;


    /**
     * SynapseUploadService constructor.
     *
     * @DI\InjectParams({
     *     "repositoryResolver" = @DI\Inject("repository_resolver"),
     *     "logger" = @DI\Inject("logger"),
     *     "container" = @DI\Inject("service_container")
     * })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->doctrine = $this->container->get(SynapseConstant::DOCTRINE_CLASS_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->transport = $this->container->get(SynapseConstant::MAILER_KEY);
        $this->transportReal = $this->container->get(SynapseConstant::SWIFT_MAILER_TRANSPORT_REAL_KEY);

        // Services
        $this->alertService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->entityService = $this->container->get(EntityService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->orgProfileService = $this->container->get(OrgProfileService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);;
        $this->profileService =  $this->container->get(ProfileService::SERVICE_KEY);
        $this->utilServiceHelper = $this->container->get(UtilServiceHelper::SERVICE_KEY);

        // Repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->orgAcademicTermRepository = $this->repositoryResolver->getRepository(OrgAcademicTermRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->personEbiMetadataRepository = $this->repositoryResolver->getRepository(PersonEbiMetaDataRepository::REPOSITORY_KEY);
        $this->personOrgMetadataRepository = $this->repositoryResolver->getRepository(PersonOrgMetaDataRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

        // Class variables
        $this->updates = [];
        $this->creates = [];
        $this->updatedRows = [];
        $this->createdRows = [];
        $this->jobs = [];
        $this->queue = 'default';

    }

    /**
     * Load the file into memory
     * @param string $filePath
     * @param int $organizationId
     * @throws \Exception
     * @return SPLFileObject Returns a raw SPLFileObject
     */
    public function load($filePath, $organizationId)
    {
        if (! file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->organizationId = $organizationId;

        $this->fileReader = new CSVReader($filePath, true, true);
        $this->affectedExternalIds = [];

        foreach ($this->fileReader as $idx => $row) {
            $this->affectedExternalIds[] = strtolower($row[strtolower(self::EXT_ID)]);
        }

        $existingPeople = $this->personRepository->getExternalIdsByOrgId($this->organizationId);
        $existingPeople = $existingPeople ? $existingPeople : [];
        $existingPeople =$this->dataProcessingUtilityService->arrayStringToLowerNullSafeAll($existingPeople);

        $this->createable = $this->dataProcessingUtilityService->nullSafeEqualsArrayDiff($this->affectedExternalIds, $existingPeople);
        $this->updateable = $this->dataProcessingUtilityService->nullSafeEqualsArrayDiff($this->affectedExternalIds, $this->createable);
        $this->totalRows = count($this->affectedExternalIds);

        $fileData = [
            'totalRows' => $this->totalRows,
            'new' => count($this->createable),
            'existing' => count($this->updateable)
        ];

        return $fileData;
    }

    /**
     * Processes the currently loaded file
     *
     * @param int $uploadId
     * @return array|bool Returns array containg information about the upload, or false on failure
     */
    public function process($uploadId)
    {
        $this->uploadId = $uploadId;

        $ebiProfileItems = $this->profileService->getProfiles('active');
        $orgProfileItems = $this->orgProfileService->getInstitutionSpecificProfileBlockItems($this->organizationId, false, 'active', false);
        $profileItems = array_merge($ebiProfileItems['profile_items'], $orgProfileItems['profile_items']);
        $this->cache->save("profileItems:all:{$this->organizationId}", $profileItems);

        $ebiArchivedProfileItems = $this->profileService->getProfiles('archive');
        $orgArchivedProfileItems = $this->orgProfileService->getInstitutionSpecificProfileBlockItems($this->organizationId, false, 'archive', false);
        $archivedProfileItems = array_merge($ebiArchivedProfileItems['profile_items'], $orgArchivedProfileItems['profile_items']);
        $this->cache->save("profileItems:archived:{$this->organizationId}", $archivedProfileItems);

        try {
            if ($this->totalRows < UploadConstant::EXPRESS_QUEUE_COUNT) {
                $this->queue = UploadConstant::EXPRESS_QUEUE;
            } else {
                $queues = json_decode($this->ebiConfigService->get('Upload_Queues'));
                $this->queue = $queues[mt_rand(0, count($queues) - 1)];
            }
        } catch (\Exception $e) {
            $this->queue = 'default';
        }
        $processed = [];
        $batchSize = 30;
        $i = 1;
        foreach ($this->fileReader as $idx => $row) {
        if (is_null($row)) {
        $row = [];
            }
            if ($idx === 0 || ! array_filter($row, function ($v)
            {
                return (! empty(trim($v)));
            })) {
                continue;
            }

            if (in_array(strtolower($row[strtolower(self::EXT_ID)]), $this->updateable) || in_array($row[strtolower(self::EXT_ID)], $processed)) {
                $person = $row[strtolower(self::EXT_ID)];
                $this->update($idx, $person, $row);
                $this->updatedRows[] = $row;
            } else {
                $this->create($idx, $row);
                $this->createdRows[] = $row;
            }
            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $processed[] = $row[strtolower(self::EXT_ID)];
            $i++;
        }

        $this->queueForWrite();

        $this->cache->save("organization.{$this->organizationId}.upload.{$this->uploadId}.jobs", $this->jobs);

        return [
            'jobs' => $this->jobs
        ];
    }

    /**
     * Generates error CSV.
     *
     * @param array $errors
     * @return string
     */
    public function generateErrorCSV($errors)
    {

        $tempFileName = uniqid("/tmp/$this->organizationId-$this->entityName-errors");
        $writer = new CSVWriter($tempFileName);

        $csvHeaders = $this->fileReader->getColumns();
        $csvHeaders[self::ERROR_HEADER] = self::ERROR_HEADER;
        $writer->addRow($csvHeaders);

        foreach ($this->fileReader as $idx => $row) {
            if (isset($errors[$idx])) {
                $rowErrors = '';
                foreach ($errors[$idx] as $id => $column) {
                    if ($id) {
                        $rowErrors .= "\r\n";
                    }
                    if (count($column['errors']) > 0) {
                        $rowErrors .= "{$column['name']} - ";
                        $rowErrors .= implode("{$column['name']} - ", $column['errors']);
                    } else {
                        $rowErrors .= "{$column['name']} - {$column['errors'][0]}";
                    }
                }
                $row[self::ERROR_HEADER] = $rowErrors;
                    $writer->addRow($row);
            }
        }

        $entityName = strtolower($this->entityName);
        if ($entityName == 'facutly') {
            $entityName = 'staff';
        }

        copy($tempFileName, UploadConstant::DATA_SLASH . $entityName . "_uploads/errors/{$this->organizationId}-{$this->uploadId}-upload-errors.csv");
        unlink($tempFileName);

        $errorFilename = "errors/{$this->organizationId}-{$this->uploadId}-upload-errors.csv";
        return $errorFilename;
    }


    /**
     * Generates data dump CSV file by specified data file type ("Student" or "Faculty")
     *
     * @param int $orgId
     * @param string $dataFileType - "Student" or "Faculty"
     * @return string - file name of data dump CSV
     */
    public function generateDumpCSV($orgId, $dataFileType = "Student")
    {
        //Create context for file in S3 bucket
        $opts = array(
            'http' => array(
                'header' => "Content-type: application/csv; charset=UTF-8"
            ),
        );
        $context = stream_context_create($opts);

        $lowercaseDataFileType = strtolower($dataFileType);

        //Truncate the dump file so that if this dump fails the old data is not left around to confuse clients
        $dumpFileName = UploadConstant::DATA_SLASH . $lowercaseDataFileType . "_uploads/{$orgId}-latest-" . $lowercaseDataFileType . "-dump.csv";
        if ($lowercaseDataFileType == 'student') {
            $timezoneObj = $this->organizationRepository->findOneById($orgId)->getTimeZone();
            $timezone = $this->utilServiceHelper->getDateByTimezone($timezoneObj, 'd-m-Y_H_i_O');

            $fileName = "{$orgId}-Student_Export-" . $timezone . ".csv";
            $dumpFileName = UploadConstant::DATA_SLASH . $lowercaseDataFileType . "_uploads/" . $fileName;
        }

        $tempFileName = uniqid("/tmp/$orgId-$lowercaseDataFileType-dump");
        $writer = new CSVWriter($tempFileName);

        $firstRow = true;

        $peopleCount = $this->personRepository->getOrganizationUsersByTypeCount($orgId, $lowercaseDataFileType);

        for ($offset = 0; $offset < $peopleCount; $offset = $offset + 500) {

            $rows = $this->personRepository->getDumpByOrganizationByTypePaged($orgId, $dataFileType, 500, $offset);

            if ($firstRow) {
                $writer->addRow(array_keys($rows[0]));
                $firstRow = false;
            }

            foreach ($rows as $r) {
                $writer->addRow($r);
            }

            unset($rows);
        }

        copy($tempFileName, $dumpFileName);
        unlink($tempFileName);

        return isset($fileName) ? $fileName : "{$orgId}-latest-" . $lowercaseDataFileType . "-dump.csv";
    }

    /**
     * Starts the job that updates the passed in datafile to reflect most recent changes
     *
     * @param int $orgId
     * @param string $dataFileType
     * @return \Resque_Job_Status|null
     */
    public function updateDataFile($orgId, $dataFileType = "Student")
    {
        //TODO: Make this an if statement, and don't concatenate object names.
        $createObject = 'Synapse\UploadBundle\Job\Update' . $dataFileType . 'DataFile';
        $job = new $createObject();
        $job->args = array(
            UploadConstant::ORGID => $orgId
        );

        return $this->resque->enqueue($job, true);
    }

    private function getPersonData($person, $ignore, &$personData)
    {

        foreach ($person as $key => $value) {
            if (! in_array($key, $ignore) ) {
                $key = ucfirst($key);
                if (is_a($value, UploadConstant::DATE_TIME)) {
                    $personData[$key] = $value->format(UploadConstant::DATETIME);
                } else {
                    $value = @iconv("UTF-8", "ISO-8859-2", $value);
                    $personData[$key] = $value;
                }
            }
        }
    }

    private function getContactData($contact, $ignore, &$personData)
    {
        foreach ($contact as $key => $value) {
            if (! in_array($key, $ignore)) {
                $key = ucfirst($key);
                $personData[$key] = $value;
            }
        }
    }

    private function getEbiMetadataData($person, $ignore, &$personData, $yearId = null, $termId = null)
    {
        $metadata = $this->personEbiMetadataRepository->findBy([
            'person' => $person['id'],
            'orgAcademicYear' => $yearId,
            'orgAcademicTerms' => $termId
        ]);


        foreach ($metadata as $metadataItem) {
            try {
                $key = $metadataItem->getEbiMetadata()->getKey();
                if (! in_array($key, $ignore)) {
                    if (is_a($metadataItem->getMetadataValue(), UploadConstant::DATE_TIME)) {
                        $personData[$key] = $metadataItem->getMetadataValue()->format(UploadConstant::DATETIME);
                    } else {
                        $personData[$key] = $metadataItem->getMetadataValue();
                    }
                }
            } catch (\Exception $e) {}
        }

    }

    private function getOrgMetadataData($person, $ignore, &$personData, $yearId = null, $termId = null)
    {
        $metadata = $this->personOrgMetadataRepository->findBy([
            'person' => $person['id'],
            'orgAcademicYear' => $yearId,
            'orgAcademicPeriods' => $termId
        ]);


        foreach ($metadata as $metadataItem) {
            try {
                $key = $metadataItem->getOrgMetadata()->getMetaKey();

                if (! in_array($key, $ignore)) {
                    if (is_a($metadataItem->getMetadataValue(), UploadConstant::DATE_TIME)) {
                        $personData[$key] = $metadataItem->getMetadataValue()->format(UploadConstant::DATETIME);
                    } else {
                        $personData[$key] = $metadataItem->getMetadataValue();
                    }
                }
            } catch (\Exception $e) {}
        }

    }

    /**
     * get upload fields for a student or faculty for an organization excluding fields in excluded array
     *
     * @param int $orgId
     * @param array $excluded
     * @return mixed
     */
    private function getUploadFields($orgId, $excluded)
    {

        $personItems = $this->doctrine->getManager()
            ->getClassMetadata('Synapse\CoreBundle\Entity\Person')
            ->getFieldNames();
        $personItems = array_diff($personItems, $excluded);

        $personItems = array_map(function ($value)
        {

            return ucfirst($value);
        }, $personItems);

        $orgPersonEntity = array();
        if ($this->entityName == UploadConstant::STUDENT) {
            array_push($orgPersonEntity, 'StudentPhoto');
            array_push($orgPersonEntity, 'IsActive');
            array_push($orgPersonEntity, 'SurveyCohort');
            array_push($orgPersonEntity, 'ReceiveSurvey');
            array_push($orgPersonEntity, 'YearId');
            array_push($orgPersonEntity, 'TermId');
            array_push($orgPersonEntity, 'PrimaryConnect');
            array_push($orgPersonEntity, 'RiskGroupId');
            array_push($orgPersonEntity, 'StudentAuthKey');
        } else {
            array_push($orgPersonEntity, 'FacultyAuthKey');
            array_push($orgPersonEntity, 'IsActive');

        }

        $contactItems = $this->doctrine->getManager()
            ->getClassMetadata('Synapse\CoreBundle\Entity\ContactInfo')
            ->getFieldNames();

        $contactItems = array_diff($contactItems, $excluded);

        $contactItems = array_map(function ($value)
        {
            return ucfirst($value);
        }, $contactItems);

        $profileItems = [];
        $orgProfileItems = [];
        if ($this->entityName == UploadConstant::STUDENT) {
            $profileItems = $this->profileService->getProfiles('active');

            $profileItems = array_column($profileItems['profile_items'], 'item_label');

            $orgProfileItems = $this->orgProfileService->getInstitutionSpecificProfileBlockItems($orgId, false, false, false);

            $orgProfileItems = array_column($orgProfileItems['profile_items'], 'item_label');
        }

        return array_merge($personItems, $orgPersonEntity, $contactItems, $profileItems, $orgProfileItems);
    }

    private function update($idx, $person, $data)
    {
        $this->updates[$idx] = [
            $person,
            $data
        ];
    }

    private function create($idx, $data)
    {
        $this->creates[$idx] = $data;
    }

    /**
     * Calls the job to write CSV data into DB.
     */
    private function queueForWrite()
    {
        if (count($this->creates)) {

            $this->creates = array_change_key_case($this->creates,CASE_LOWER);

            $createObject = 'Synapse\UploadBundle\Job\Create' . $this->entityName;
            $jobNumber = uniqid();
            $job = new $createObject();
            $job->queue = $this->queue;
            $job->args = array(
                'creates' => $this->creates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId,
                UploadConstant::ORGID => $this->organizationId
            );

            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }

        if (count($this->updates)) {
            $updateObject = 'Synapse\UploadBundle\Job\Update' . $this->entityName;
            $jobNumber = uniqid();
            $job = new $updateObject();
            $job->queue = $this->queue;
            $job->args = array(
                'updates' => $this->updates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId,
                UploadConstant::ORGID => $this->organizationId
            );

            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }

        $this->updates = [];
        $this->creates = [];
    }

    /**
     * Send FTP Notification
     *
     * @param int $orgId
     * @param string $entity
     * @param int $errorCount
     * @param string $downloadFailedLogFile
     * @return null
     */
    public function sendFTPNotification($orgId, $entity, $errorCount, $downloadFailedLogFile)
    {
        $coordinators = $this->organizationRoleRepository->getCoordinators($orgId);

        $subjectEntity = ucfirst($entity);
        $systemUrl = $this->ebiConfigService->getSystemUrl($orgId);
        foreach ($coordinators as $coordinator) {
            $this->sendAlert($coordinator->getPerson(), $orgId, "{$subjectEntity}_Upload_Notification", "{$subjectEntity} Import", $errorCount, $downloadFailedLogFile);
            $entityName = strtolower($this->entityName);
            $this->sentEmailNotification($coordinator->getPerson(), $orgId, "{$subjectEntity}_Upload_Notification", $errorCount, $systemUrl . "#/errors/{$entityName}_uploads/{$this->organizationId}-{$this->uploadId}-upload-errors.csv");
        }

        if (!$this->transport instanceof \Swift_Transport_SpoolTransport) {
            return;
        }

        $spool = $this->transport->getSpool();
        if (!$spool instanceof \Swift_MemorySpool) {
            return;
        }

        $spool->flushQueue($this->transportReal);
    }

    private function sendAlert($user, $orgId, $alertKey, $reason, $errorCount, $uploadFile = null)
    {
        $errorFileFlag = false;

        if ($errorCount > 0) {
            $errorFileFlag = true;
        }
            $this->alertService->createNotification($alertKey, $reason, $user, null, null, null, $uploadFile, null, null, null, $errorFileFlag);
    }


    /**
     * Send email Notification
     * @param Person $user
     * @param int $organizationId
     * @param string $emailKey
     * @param int $errorCount
     * @param string $uploadFile
     */
    private function sentEmailNotification($user, $organizationId, $emailKey, $errorCount, $uploadFile = null)
    {
        $downloadFailedLogFile = "";

        if ($errorCount > 0) {
            $uploadEmailText = 'Click <a class="external-link" href="DOWNLOAD_URL" target="_blank" style="color: rgb(41, 114, 155);text-decoration: underline;">here </a>to download error file .';
            $downloadFailedLogFile = str_replace('DOWNLOAD_URL', $uploadFile, $uploadEmailText);
        }

        $userId = $user->getId();
        $tokenValues = Array();
        $tokenValues['user_first_name'] = $user->getFirstname();
        $tokenValues['download_failed_log_file'] = $downloadFailedLogFile;

        // SkyFactor Logo

        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
        $tokenValues["Skyfactor_Mapworks_logo"] = "";
        if ($systemUrl) {
            $tokenValues["Skyfactor_Mapworks_logo"] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
        }

        $organizationLang = $this->organizationService->getOrganizationDetailsLang($organizationId);
        $languageId = $organizationLang->getLang()->getId();

        $emailTemplate = $this->emailTemplateLangRepository->getEmailTemplateByKey($emailKey, $languageId);

        if ($userId && $userId != null) {
            $responseArray = array();
            if ($emailTemplate) {
                $emailBody = $emailTemplate->getBody();
                $email = $user->getUsername();

                $emailBody = $this->emailService->generateEmailMessage($emailBody, $tokenValues);
                $bcc = $emailTemplate->getEmailTemplate()->getBccRecipientList();
                $subject = $emailTemplate->getSubject();
                $from = $emailTemplate->getEmailTemplate()->getFromEmailAddress();
                $responseArray['email_detail'] = array(
                    'from' => $from,
                    'subject' => $subject,
                    'bcc' => $bcc,
                    'body' => $emailBody,
                    'to' => $email,
                    'emailKey' => $emailKey,
                    'organizationId' => $organizationId
                );
            }
            $emailInst = $this->emailService->sendEmailNotification($responseArray['email_detail']);
            $this->emailService->sendEmail($emailInst);
        }
    }

    /**
     * Returns an array of all of the row data in the uploaded file that caused "updates" to occur.
     *
     * Note: The updates may not have occurred due to data errors found later in processing. So, be sure to do
     * error checking on any row data you may use.
     *
     * @return array
     */
    public function getUpdatedRows(){
        return $this->updatedRows;
    }

    /**
     * Returns an array of all of the row data in the uploaded file that caused "creates" to occur.
     *
     * Note: The creates may not have occurred due to data errors found later in processing. So, be sure to do
     * error checking on any row data you may use.
     *
     * @return array
     */
    public function getCreatedRows(){
        return $this->createdRows;
    }

    /**
     * Returns the OrganizationId for a file the file that has already been uploaded.
     *
     * @return mixed
     */
    public function getOrganizationId(){
        return $this->organizationId;
    }

    /**
     * Given an array ($outputErrors) of upload errors append additional validation errors ($validationErrors) to
     * the array, for the given row ($rowIdWithValidationErrors) that has errors.
     *
     * @param array $outputErrors - Resulting array that includes new validation errors for this row
     * @param ConstraintViolationList $validationErrors -  The validation errors that happened on this row
     * @param int $rowIdWithValidationErrors - The row ID that has errors
     * @return mixed
     */
    public function populateErrorsForUploadRecords($outputErrors, $validationErrors, $rowIdWithValidationErrors)
    {
        foreach ($validationErrors as $error) {
            $value = $error->getInvalidValue();
            $outputErrors[$rowIdWithValidationErrors][] = [
                'name' => ucfirst($error->getPropertyPath()),
                'value' => $value,
                'errors' => [
                    $error->getMessage()
                ]
            ];
        }
        return $outputErrors;
    }


    /**
     * Checks if there are errors that are benign within the upload
     *
     * @param ConstraintViolationList $validationErrors - errors gathered from the validator
     * @param array $requiredColumns - an array of errors that are required
     * @return bool
     */
    public function atLeastOneErrorIsFatal($validationErrors, $requiredColumns)
    {
        foreach ($validationErrors as $error) {
            $columnName = strtolower($error->getPropertyPath());
            if (in_array($columnName, $requiredColumns)) {
                return true;
            }
        }
        return false;
    }


    /**
     * Resets a person object's properties which have errors if only benign validation errors occur
     *
     * @param Person $person
     * @param ConstraintViolationList $validationErrors
     * @param array $originalPersonProperties
     * @return Person
     */
    public function resetInvalidPersonFieldsForUpdate($person, $validationErrors, $originalPersonProperties)
    {
        foreach ($validationErrors as $error) {
            $columnName = strtolower($error->getPropertyPath());

            $setPropertyName = "set" . $columnName;
            $person->$setPropertyName($originalPersonProperties[$columnName]);
        }
        return $person;
    }

    /**
     * Resets a contact info object's properties which have errors if only benign validation errors occur
     *
     * @param Person $person
     * @param ConstraintViolationList $validationErrors
     * @param array $originalPersonProperties
     * @return Person
     */
    public function resetInvalidContactInfoFieldsForUpdate($person, $validationErrors, $originalPersonProperties)
    {
        foreach ($validationErrors as $error) {
            $columnName = strtolower($error->getPropertyPath());

            // The PrimaryEmail Column sets contact_info.primary_email, and sets person.username
            if ($columnName === 'primaryemail') {
                $person->setUsername($originalPersonProperties['Username']);
            }

            $propertyName = ucfirst($columnName);
            $setPropertyName = "set" . $propertyName;
            $person->getContacts()[0]->$setPropertyName($originalPersonProperties[$columnName]);
        }
        return $person;
    }


    /**
     * Unsets a person object's properties which have errors if only benign validation errors occur
     *
     * @param Person $person
     * @param ConstraintViolationList $validationErrors
     * @return Person
     */
    public function unsetInvalidPersonFieldsForCreate($person, $validationErrors)
    {
        foreach ($validationErrors as $error) {
            $columnName = strtolower($error->getPropertyPath());
            $setPropertyName = "set" . $columnName;
            $person->$setPropertyName(null);
        }
        return $person;
    }

    /**
     * Unsets a contact info object's properties which have errors if only benign validation errors occur
     *
     * @param ContactInfo $contactInfo
     * @param ConstraintViolationList $validationErrors
     * @return Person
     */
    public function unsetInvalidContactInfoFieldsForCreate($contactInfo, $validationErrors)
    {
        foreach ($validationErrors as $error) {
            $columnName = strtolower($error->getPropertyPath());
            $propertyName = $columnName;
            $setPropertyName = "set" . $propertyName;
            $contactInfo->$setPropertyName(null);
        }
        return $contactInfo;
    }

}