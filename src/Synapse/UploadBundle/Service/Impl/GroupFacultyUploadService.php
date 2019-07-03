<?php
namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\UploadBundle\Service\UploadServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\OrgGroupFaculty;
use Synapse\UploadBundle\Job\AddGroupFaculty;
use Synapse\UploadBundle\Job\ProcessGroupFacultyUpload;
use SplFileObject;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Handle group faculty uploads
 *
 * @DI\Service("group_faculty_upload_service")
 */
class GroupFacultyUploadService extends AbstractService implements UploadServiceInterface
{

    const SERVICE_KEY = 'group_faculty_upload_service';

    /**
     * The path to the file we're working on
     *
     * @var string
     */
    private $filePath;

    /**
     * Object containing the loaded file
     *
     * @var CSVReader
     */
    private $fileReader;

    /**
     * The file handler to use for this upload
     *
     * @var Synapse\CoreBundle\Util\FileHandler|null
     */
    private $handler;

    private $personService;

    private $uploadFileLogService;

    private $organizationRepository;

    private $affectedExternalIds;

    private $updateable;

    private $createable;

    private $cache;

    private $resque;

    private $updates;

    private $creates;

    private $jobs;

    private $totalRows;

    private $uploadId;

    private $orgId;

    private $groupId;

    private $orgGroupFacultyRepository;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "personService" = @DI\Inject("person_service"),
     *            "uploadFileLogService" = @DI\Inject("upload_file_log_service"),
     *            "entityService" = @DI\Inject("entity_service"),
     *            "cache" = @DI\Inject("synapse_redis_cache"),
     *            "resque" = @DI\Inject("bcc_resque.resque"),
     *            "ebiConfigService" = @DI\Inject("ebi_config_service"),
     *            "alertService" = @DI\Inject("alertNotifications_service"),
     *            "container" = @DI\Inject("service_container"),
     *            })
     */
    public function __construct($repositoryResolver, $logger, $personService, $uploadFileLogService, $entityService, $cache, $resque, $ebiConfigService, $alertService, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->personService = $personService;
        $this->uploadFileLogService = $uploadFileLogService;
        $this->organizationRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Organization");
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgGroupFaculty");
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrganizationRole');
        $this->cache = $cache;
        $this->resque = $resque;
        $this->updates = [];
        $this->creates = [];
        $this->jobs = [];
        $this->queue = 'default';
        $this->ebiConfigService = $ebiConfigService;
        $this->alertService = $alertService;
        $this->container = $container;
    }

    /**
     * Load the file into memory
     *
     * @param string $filePath
     *            Path to the current file
     * @param Synapse\CoreBundle\Util\FileHandler|null $handler
     *            The file handler to use to load this file.
     *            If null, defaults to automatic detection.
     * @return \SPLFileObject Returns a raw SPLFileObject
     */
    public function load($filePath, $orgId, $groupId, $handler = null)
    {
        if (! file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->orgId = $orgId;
        $this->groupId = $groupId;

        $this->fileReader = new CSVReader($filePath, true, true);
        $this->affectedExternalIds = [];

        foreach ($this->fileReader as $idx => $row) {
            $this->affectedExternalIds[] = $row[strtolower(UploadConstant::EXTERNALID)];
        }

        $orgGroupFaculty = $this->orgGroupFacultyRepository->findBy([
            'orgGroup' => $groupId
        ]);
        $existingFaculty = [];
        foreach ($orgGroupFaculty as $orgGroupFaculty) {
            $existingFaculty[$orgGroupFaculty->getPerson()->getExternalId()] = true;
        }
        $existingFaculty = $existingFaculty ? $existingFaculty : [];
        $this->createable = array_diff($this->affectedExternalIds, array_keys($existingFaculty));
        $this->updateable = array_diff($this->affectedExternalIds, $this->createable);
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
     * @return array|bool Returns array containg information about the upload, or false on failure
     */
    public function process($uploadId)
    {
        $this->uploadId = $uploadId;
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
            if ($idx === 0) {
                continue;
            }
            if (in_array($row[strtolower(UploadConstant::EXTERNALID)], $processed)) {
                continue;
            }
            if (in_array($row[strtolower(UploadConstant::EXTERNALID)], $this->createable)) {
                $person = $row[strtolower(UploadConstant::EXTERNALID)];
                $this->create($idx, $row);
            }
            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $processed[] = $row[strtolower(UploadConstant::EXTERNALID)];
            $i ++;
        }

        $this->queueForWrite();

        $this->cache->save("organization.{$this->orgId}.upload.{$this->uploadId}.jobs", $this->jobs);

        if (! count($this->jobs)) {
            $this->uploadFileLogService->updateValidRowCount($uploadId, 0);
        }

        return [
            'jobs' => $this->jobs
        ];
    }

    public function generateErrorCSV($errors)
    {
        $list = [];

        foreach ($this->fileReader as $idx => $row) {
            if (isset($errors[$idx])) {
                $rowErrors = '';
                foreach ($errors[$idx] as $id => $column) {
                    if ($id) {
                        $rowErrors .= "\r";
                    }
                    if (count($column['errors']) > 0) {
                        $rowErrors .= "{$column['name']} - ";
                        $rowErrors .= implode("{$column['name']} - ", $column['errors']);
                    } else {
                        $rowErrors .= "{$column['name']} - {$column['errors'][0]}";
                    }
                }
                $row[UploadConstant::UPLOAD_ERRORS] = $rowErrors;
                $list[] = $row;
            }
        }

        $errorCSVFile = new SplFileObject("data://group_uploads/errors/{$this->orgId}-{$this->uploadId}-upload-errors.csv", 'w');


        $csvHeaders = $this->fileReader->getColumns();
        $csvHeaders[UploadConstant::UPLOAD_ERRORS] = UploadConstant::UPLOAD_ERRORS;
        $errorCSVFile->fputcsv($csvHeaders);


        foreach ($list as $fields) {
            $errorCSVFile->fputcsv($fields);
        }
    }

    public function generateDumpCSV($groupId)
    {
        $orgGroupFaculty = $this->orgGroupFacultyRepository->findBy([
            'orgGroup' => $groupId
        ]);

        $rows = [];

        foreach ($orgGroupFaculty as $orgGroupFaculty) {
            $rows[] = [
                strtolower(UploadConstant::EXTERNALID) => $orgGroupFaculty->getPerson()->getExternalId()
            ];
        }

        $file = new SplFileObject("data://group_uploads/{$groupId}-latest-faculty-dump.csv", 'w');

        $file->fputcsv([
            strtolower(UploadConstant::EXTERNALID)
        ]);

        foreach ($rows as $fields) {
            $file->fputcsv($fields);
        }
    }

    private function create($idx, $person)
    {
        $this->creates[$idx] = $person;
    }

    private function queueForWrite()
    {
        if (count($this->creates)) {
            $jobNumber = uniqid();
            $job = new AddGroupFaculty();
            $job->queue = $this->queue;
            $job->args = array(
                'creates' => $this->creates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId,
                'orgId' => $this->orgId,
                'groupId' => $this->groupId
            );

            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }

        $this->creates = [];
    }

    public function updateDataFile($orgId)
    {
        $createObject = 'Synapse\UploadBundle\Job\UpdateGroupFacultyDataFile';
        $job = new $createObject();
        $job->args = array(
            'orgId' => $orgId
        );

        return $this->resque->enqueue($job, true);
    }

}
