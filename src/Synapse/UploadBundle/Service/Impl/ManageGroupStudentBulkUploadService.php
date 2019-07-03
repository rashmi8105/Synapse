<?php

/*
 * Author:  Technically it is Ian Who made this
 * Note:    I copied GroupStudentBulkUploadService so I can actually create this class
 *          The comments I made are mostly for my understanding
 */

namespace Synapse\UploadBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Snc\RedisBundle\Doctrine\Cache\RedisCache;
use SplFileObject;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Repository\OrgGroupTreeRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Util\Constants\GroupUploadConstants;
use Synapse\CoreBundle\Util\CSVReader;


/**
 * Handle Academic Update bulk uploads for group students
 *
 * @DI\Service("manage_group_student_upload_service")
 */
class ManageGroupStudentBulkUploadService extends AbstractService
{

    const SERVICE_KEY = 'manage_group_student_upload_service';

    // constant for uploading errors
    const UPLOAD_ERROR = 'Upload Errors';

    /**
     * Object containing the loaded file
     *
     * @var CSVReader
     */
    private $fileReader;


    /**
     * @var Container
     */
    private $container;

    /**
     * @var UploadFileLogService
     */
    private $uploadFileLogService;

    /**
     * @var array
     */
    private $affectedExternalIds;

    /**
     * @var array
     */
    private $createable;

    /**
     * @var RedisCache
     */
    private $cache;

    /**
     * @var \Resque
     */
    private $resque;

    /**
     * @var array
     */
    private $creates;

    /**
     * @var array
     */
    private $jobs;

    /**
     * @var string
     */
    private $queue;

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
     * @var array indexes of rows that are duplicated
     */
    private $duplicatedExternalIDs;


    /**
     * @var OrgGroupTreeRepository
     */
    private $orgGroupTreeRepository;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OrgGroupRepository
     */
    private $orgGroup;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var AlertNotificationsService
     */
    private $alertService;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Swift_Transport
     */
    private $swiftMailer;

    /**
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Logger $logger
     * @param Container $container
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container"),
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->uploadFileLogService = $this->container->get('upload_file_log_service');

        $this->cache = $this->container->get('synapse_redis_cache');
        $this->resque = $this->container->get('bcc_resque.resque');
        $this->creates = [];
        $this->jobs = [];
        $this->queue = 'default';
        $this->ebiConfigService = $this->container->get('ebi_config_service');
        $this->emailService = $this->container->get('email_service');
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:EmailTemplateLang");
        $this->orgGroup = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroup');
        $this->orgGroupTreeRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupTree');
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrganizationRole');
        $this->organizationService = $this->container->get('org_service');
        $this->alertService = $this->container->get('alertNotifications_service');
        $this->mailer = $this->container->get('mailer');
        $this->swiftMailer = $this->container->get('swiftmailer.transport.real');
    }

    /**
     * Load the csv file into memory
     *
     * @param string $filePath
     * @param string|int $orgId
     * @throws \Exception
     * @return array
     */
    public function load($filePath, $organizationId)
    {
        // logging information of the upload
        $this->logger->info("*************************************************************************");
        $this->logger->info(GroupUploadConstants::MODULE_NAME . __FILE__ . " loading");

        if (!file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        // set the object userId, orgId and file object
        $this->organizationId = $organizationId;
        $this->fileReader = new CSVReader($filePath, true, true);

        $this->affectedExternalIds = [];
        $this->duplicatedExternalIDs = [];

        foreach ($this->fileReader as $idx => $row) {
            $this->affectedExternalIds[] = $row[strtolower(GroupUploadConstants::EXTERNAL_ID)];
        }

        // difference between the original array and the unique items in the array is the duplicated values.
        $duplicatedValues = array_diff_key($this->affectedExternalIds, array_unique($this->affectedExternalIds));

        // use an array intersection to preserve indices of duplicates (using the structure of the original array)
        $this->duplicatedExternalIDs = array_intersect($this->affectedExternalIds, $duplicatedValues);


        // This gets the creatable (changed rows) and total rows
        $this->createable = $this->affectedExternalIds;

        // get the total rows
        $this->totalRows = count($this->createable);
        $fileData = [
            'totalRows' => $this->totalRows,
            'new' => count($this->createable)
        ];

        return $fileData;
    }

    /**
     * Processes the currently loaded file
     *
     * @param string|int $uploadId
     * @return array|bool Returns array containing information about the upload, or false on failure
     */
    public function process($uploadId)
    {
        // Debugging information, use if there are troubles
        $this->logger->info("*************************************************************************");
        $this->logger->info(GroupUploadConstants::MODULE_NAME . __FUNCTION__ . "  processing");

        // set the uploadId in the object with the sent uploadId
        $this->uploadId = $uploadId;

        // gets all of the queries
        try {
            // get the upload queues that can be used
            $uploadQueues = $this->ebiConfigService->get('Upload_Queues');
            $queues = json_decode($uploadQueues);

            // choose a random queue to use
            $queueToUse = mt_rand(0, count($queues) - 1);
            $this->queue = $queues[$queueToUse];


        } catch (\Exception $e) {

            // If a random queue in use is chosen, or an error with the decoding
            // go to default
            $this->queue = 'default';
        }

        // set the processed array and batch Size to 30
        $processed = [];
        $batchSize = 30;

        // number of iterations of loop below, used to queue for write when $i is divisible by $batchSize
        $i = 1;

        // for each row in the file object
        foreach ($this->fileReader as $idx => $row) {

            // Logging the information of the logger
            $this->logger->info("^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^");
            $this->logger->info(GroupUploadConstants::MODULE_NAME . __FUNCTION__ . $idx);

            // This will be handled by looking at each row and seeing if each row is unique
            $processingRow = $row[strtolower(GroupUploadConstants::EXTERNAL_ID)];



            // processing Row, if it is the header
            $this->logger->info($processingRow);
            if ($idx === 0) {
                continue;
            }


            // Place the row to be created
            if (in_array($processingRow, $this->createable)) {
                $this->create($idx, $row);
            }

            // If we have the batchSize amount
            if (($i % $batchSize) === 0) {
                // run the run function that will add the 30 rows into
                // the database
                $this->queueForWrite();
            }

            //put the row into the processed array
            $processed[] = $processingRow;

            // increase the count
            $i ++;
        }

        // If the number of uploaded rows is not divisible by 30
        $this->queueForWrite();

        // save the uploaded rows into the cache
        $this->cache->save("groupstudentbulk.upload.{$this->uploadId}.jobs", $this->jobs);

        //
        if (! count($this->jobs)) {
            // update valid row count
            $this->uploadFileLogService->updateValidRowCount($uploadId, 0);
            $this->uploadFileLogService->updateUpdatedRowCount($uploadId, 0);
        }

        // return the uploaded jobs
        return [
            'jobs' => $this->jobs
        ];
    }

    /**
     * Function to send notification in email and bell notification
     *
     * @param int $organizationId
     * @param string $entity
     * @param int $errorCount
     * @param string $downloadFailedLogFile
     */
    public function sendFTPNotification($organizationId, $entity, $errorCount, $downloadFailedLogFile)
    {
        $coordinators = $this->organizationRoleRepository->getCoordinators($organizationId);

        $subjectEntity = ucfirst($entity);
        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);

        foreach ($coordinators as $coordinator) {
            $this->sendAlert($coordinator->getPerson(), $organizationId, "{$subjectEntity}_Upload_Notification", "{$subjectEntity} Import", $errorCount, $downloadFailedLogFile);
            $this->emailService->sendUploadCompletionEmail($coordinator->getPerson(), $organizationId, "{$subjectEntity}_Upload_Notification", $errorCount, $systemUrl . "#/errors/group_uploads/{$this->organizationId}-{$this->uploadId}-upload-errors.csv");
        }

        $transport = $this->mailer->getTransport();
        if (!$transport instanceof \Swift_Transport_SpoolTransport) {
            return;
        }

        $spool = $transport->getSpool();
        if (!$spool instanceof \Swift_MemorySpool) {
            return;
        }

        $spool->flushQueue($this->swiftMailer);
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
     * @param $orgId => the organization that uploaded te information
     * @param $errors => an error array
     * @return string
     *
     * This will create the CSV file based off of the errors given
     */
    public function generateErrorCSV($orgId,$errors)
    {
        // create the list to place into db
        $list = [];
        // Logging information for debugging
        $this->logger->info(__FUNCTION__);

        // for each row
        foreach ($this->fileReader as $idx => $row) {
            // if there is an error
            if (isset($errors[$idx])) {
                // create the error row
                $rowErrors = '';
                $rowErrors = $this->createRow($idx, $errors, $rowErrors);
                $row[self::UPLOAD_ERROR] = $rowErrors;

                // add it to the list
                $list[] = $row;
            }
        }

        // logging information for the debugger
        $this->logger->info(__FUNCTION__);

        // create new csv object
        $errorCSVFile = new SplFileObject("data://group_uploads/errors/{$orgId}-{$this->uploadId}-upload-errors.csv", 'w');
        //   $file = new SplFileObject("data:///vagrant/synapse-backend/Amazon/$orgId/multi-group-student/{$orgId}-{$this->uploadId}-upload-errors.csv", 'w');


        $csvHeaders = $this->fileReader->getColumns();
        $csvHeaders[self::UPLOAD_ERROR] = self::UPLOAD_ERROR;
        $errorCSVFile->fputcsv($csvHeaders);



        // place each row into the csv file
        foreach ($list as $fields) {

            $errorCSVFile->fputcsv($fields);
        }
        $errorFilename = "errors/{$orgId}-{$this->uploadId}-upload-errors.csv";
        return $errorFilename;
    }

    /**
     * @param $idx => the row id
     * @param $errors => the array of errors
     * @param $rowErrors => the string of errors... will always be ''
     * @return string
     *
     * This function will create a row based off of each
     * row's errors
     */
    public function createRow($idx, $errors, $rowErrors)
    {
        // for each row
        $rowErrors = "";
        foreach ($errors[$idx] as $id => $column) {

            // break up the error
            if ($id) {
                $rowErrors = $rowErrors . "; ";
            }
            // if there are more than one error, implode the information by - for each error

            if (count($column['errors']) > 0) {
                $rowErrors = $rowErrors . "{$column['name']} - ";
                $rowErrors = $rowErrors . implode("{$column['name']} - ", $column['errors']);
            } else {
                // only one, don't implode
                $rowErrors = $rowErrors . "{$column['name']} - {$column['errors'][0]}";
            }

        }
        // return the row
        return $rowErrors;
    }

    private function create($idx, $person)
    {
        // log information for debugging
        $this->logger->info(__FUNCTION__);
        // add the $person to the creates id...
        $this->creates[$idx] = $person;
    }

    /**
     * This function will set up the upload for the add function
     */
    private function queueForWrite()
    {
        if (count($this->creates)) {
            // Debugging information
            $this->logger->info(__FILE__ . __FUNCTION__);

            $createObject = 'Synapse\UploadBundle\Job\ManageGroupStudentBulkUpload';

            // get unique id
            $jobNumber = uniqid();

            // create the job with the information given
            $job = new $createObject();
            $job->queue = $this->queue;
            $job->args = array(
                'creates' => $this->creates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId,
                'orgId' => $this->organizationId,
                'duplicatedExternalIDs' => implode(",", $this->duplicatedExternalIDs)
            );


            // send the job
            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }
        // reset the object so the same thing is not uploaded each time
        $this->creates = [];
    }

    /**
     * @param $orgId => int the organization ID
     *
     */
   // This is probably not necessary
    public function createExisting($orgId)
    {
        // Debugger information
        $this->logger->info("****************************************************" . __FUNCTION__);
        //Debugging information
        $this->logger->info($orgId."****************************************************" . __FUNCTION__);

        $header = $this->getTemplateHeaders($orgId);

        $opts = array(
            'http'=>array(
                'header'=>"Content-type: application/csv; charset=UTF-8"
            ),
        );
        $context = stream_context_create($opts);

        // Fetch current set of students in groups
        $studentParentGroupsChildrenGroups = $this->orgGroupTreeRepository->getAllStudentGroupCombinationsWithAncestorForAnOrganization($orgId);

        // TODO change the name of the file to something a tad more relevant to what I am uploading
        $filename = "{$orgId}-students-bulk-existing.csv";

        $file = new SplFileObject("data://group_uploads/{$filename}", 'w');

        // Handle if there is no students in a group
        if (count($studentParentGroupsChildrenGroups) == 0) {

            $msg = "There are no students added to group.";
            $this->logger->info( $orgId . "->" . $msg);

            $file->fputcsv($header);
            $file->fwrite( $msg);
            $file->fflush();

            unset($file);
            return;
        }



        $msg = "Still generating file..." . date("m-d-Y h:i:sa");
        $file->fputcsv($header);
        $file->fwrite( $msg);
        $file->fflush();

        unset($file);

        $this->logger->info( $orgId . "-> created $filename on AWS with message ($msg)...");

        // Generate the dump file with students along with the groups they have been added to
        $previousRow = array();
        $previousRow['ExternalId'] = null;
        $temp = array();

        // Generate the dump file in temp directory and then copy to AWS
        $tempFileName = uniqid("/tmp/{$orgId}-student-uploads-");
        $writer = new SplFileObject($tempFileName, 'w', false, $context);

        $this->logger->info( $orgId . "-> Creating group-students dump file... $tempFileName");

        $writer->fputcsv($header);
        foreach ($studentParentGroupsChildrenGroups as $studentParentGroupStudentGroup) {

            if ($previousRow['ExternalId'] != $studentParentGroupStudentGroup[strtolower('External_ID')] && !empty($temp)) {
                $writer->fputcsv($temp);
                // clear all column names
                $temp = array();
            }
            if (empty($temp)) {
                foreach ($header as $column) {
                    if (!isset($temp[$column])) {
                        $temp[$column] = '';
                    }
                }
            }
            // set the students id, name and email
            $temp[GroupUploadConstants::EXTERNAL_ID] = $studentParentGroupStudentGroup['external_id'];
            $temp[GroupUploadConstants::FIRSTNAME] = $studentParentGroupStudentGroup['firstname'];
            $temp[GroupUploadConstants::LASTNAME] = $studentParentGroupStudentGroup['lastname'];
            $temp[GroupUploadConstants::PRIMARY_EMAIL] = $studentParentGroupStudentGroup['username'];

            // set column names
            if (empty($temp[$studentParentGroupStudentGroup['header_name']])) {
                $temp[$studentParentGroupStudentGroup['header_name']] = $studentParentGroupStudentGroup['person_in'];
            } else {
                $temp[$studentParentGroupStudentGroup['header_name']] = $temp[$studentParentGroupStudentGroup['header_name']] . "; " . $studentParentGroupStudentGroup['person_in'];
            }
            $previousRow = $temp;


        }
        if(!is_null($temp)){
            $writer->fputcsv($temp);
        }
        $writer->fflush();
        unset($writer);

        $this->logger->info( $orgId . "-> group-students file $tempFileName created.  Copying to AWS now...");

        copy($tempFileName, "data://group_uploads/".$filename);
        unlink($tempFileName);

        $this->logger->info( $orgId . "-> file $tempFileName copied to AWS as $filename.");
    }

    public function getTemplateHeaders($organizationId){

        $topLevelGroups = $this->orgGroup->getTopLevelGroups($organizationId, false);
        $childrenOfAllStudents = $this->orgGroup->getImmediateChildrenOfAllStudentsGroup($organizationId);
        $header = $this->getHeader();
        // add in the top level groups
        if(!empty($topLevelGroups)){
            foreach($topLevelGroups as $topLevelGroup) {
                $header[] = $topLevelGroup['group_name'];
            }
        }
        // add in the children of ALLSTUDENTS
        if(!empty($childrenOfAllStudents)){
            foreach($childrenOfAllStudents as $childOfAllStudents) {
                $header[] = $childOfAllStudents['group_name'];
            }
        }
        return $header;
    }

    // This will get the header of the file
    public function getHeader()
    {
        $header = [
            GroupUploadConstants::EXTERNAL_ID,
            GroupUploadConstants::FIRSTNAME,
            GroupUploadConstants::LASTNAME,
            GroupUploadConstants::PRIMARY_EMAIL,
        ]
        ;
        return $header;
    }

    public function updateDataFile($orgId)
    {
        $createObject = 'Synapse\UploadBundle\Job\UpdateGroupStudentDataFile';
        $job = new $createObject();
        $job->args = array(
            'orgId' => $orgId
        );

        return $this->resque->enqueue($job, true);
    }

}
