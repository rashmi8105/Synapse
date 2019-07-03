<?php
namespace Synapse\UploadBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Snc\RedisBundle\Doctrine\Cache\RedisCache;
use SplFileObject;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\RiskBundle\Repository\RiskModelMasterRepository;
use Synapse\RiskBundle\Util\Constants\RiskModelConstants;
use Synapse\UploadBundle\Job\RiskModelAssignmentsUpload;
use Synapse\UploadBundle\Service\UploadServiceInterface;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Handle course uploads
 *
 * @DI\Service("risk_model_assignment_upload_service")
 */
class RiskModelAssignmentUploadService extends AbstractService implements UploadServiceInterface
{

    const SERVICE_KEY = 'risk_model_assignment_upload_service';

    const CAMPUSID = 'CampusID';

    const RISKGROUPID = 'RiskGroupID';

    const MODELID = 'ModelID';

    const UPLOAD_ERROR = 'Upload Errors';

    const ORGID = 'OrgID';

    /**
     * @var Container
     */
    private $container;

    /**
     * Object containing the loaded file
     *
     * @var CSVReader
     */
    private $fileReader;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var UploadFileLogService
     */
    private $uploadFileLogService;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var array
     */
    private $orgWithModelIds;

    /**
     * @var array
     */
    private $updateable;

    /**
     * @var array
     */
    private $createable;

    /**
     * @var RedisCache
     */
    private $cache;

    /**
     * @var Resque
     */
    private $resque;

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
    private $userId;

    /**
     * @var RiskModelMasterRepository
     */
    private $riskModelMasterRepository;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->updates = [];
        $this->creates = [];
        $this->jobs = [];
        $this->queue = 'default';

        //Services
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->uploadFileLogService = $this->container->get(UploadFileLogService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);

        //Repositories
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->riskModelMasterRepository = $this->repositoryResolver->getRepository(RiskModelMasterRepository::REPOSITORY_KEY);
    }

    /**
     * Load the file into memory
     *
     * @param string $filePath Path to the current file
     * @return \SPLFileObject Returns a raw SPLFileObject
     * @throws SynapseValidationException
     */
    public function load($filePath)
    {
        if (!file_exists($filePath)) {
            throw new SynapseValidationException("File not found");
        }

        $this->fileReader = new CSVReader($filePath, true, true);
        $this->orgWithModelIds = [];

        foreach ($this->fileReader as $idx => $row) {
            $this->orgWithModelIds[] = $row[strtolower(self::ORGID)];
        }

        $this->createable = $this->orgWithModelIds;
        $this->updateable = array_diff($this->orgWithModelIds, $this->createable);
        $this->totalRows = count($this->orgWithModelIds);

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
     * @return array|bool Returns array containing information about the upload, or false on failure
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
            if (in_array($row[strtolower(self::ORGID)], $this->createable)) {
                $this->create($idx, $row);
            }
            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $processed[] = $row[strtolower(self::ORGID)];
            $i++;
        }

        $this->queueForWrite();

        $this->cache->save("riskmodelassignment.upload.{$this->uploadId}.jobs", $this->jobs);

        if (!count($this->jobs)) {
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
                $row[strtolower(self::UPLOAD_ERROR)] = $rowErrors;
                $list[] = $row;
            }
        }

        $errorCSVFile = new SplFileObject("data://risk_uploads/errors/1-{$this->uploadId}-upload-errors.csv", 'w');

        $csvHeaders = $this->fileReader->getColumns();
        $csvHeaders[] = self::UPLOAD_ERROR;
        $errorCSVFile->fputcsv($csvHeaders);


        foreach ($list as $fields) {
            $errorCSVFile->fputcsv($fields);
        }
    }

    /*
     * Generate csv dump file
     */
    public function generateDumpCSV()
    {
        $modelData = $this->riskModelMasterRepository->getModelAssignments('all');
        $fileName = "RiskAdministrationExport.csv";
        $fileHandler = new SplFileObject(UploadConstant::DATA_SLASH.RiskModelConstants::RISK_DIR.$fileName, 'w');
        $headers = ['OrgID','OrgName','RiskGroupID','RiskGroupName','ModelID','ModelName','EnrollmentEndDate','EffectiveStartDate','EffectiveEndDate'];
        $fileHandler->fputcsv($headers);
         if (count($modelData) > 0) {
            foreach ($modelData as $riskModel) {
                if ($riskModel['calculation_start_date']) {
                    $newDateFormat = new \DateTime($riskModel['calculation_start_date']);
                    $riskModel['calculation_start_date'] = $newDateFormat->format('m/d/Y');
                }

                if ($riskModel['calculation_end_date']) {
                    $newDateFormat = new \DateTime($riskModel['calculation_end_date']);
                    $riskModel['calculation_end_date'] = $newDateFormat->format('m/d/Y');
                }

                if ($riskModel['enrollment_date']) {
                    $newDateFormat = new \DateTime($riskModel['enrollment_date']);
                    $riskModel['enrollment_date'] = $newDateFormat->format('m/d/Y');
                }
                $fileHandler->fputcsv($riskModel);
            }
        }
    }

    private function create($idx, $row)
    {
        $this->creates[$idx] = $row;
    }

    private function queueForWrite()
    {
        if (count($this->creates)) {

            $this->creates = array_change_key_case($this->creates,CASE_LOWER);

            $jobNumber = uniqid();
            $job = new RiskModelAssignmentsUpload();
            $job->queue = $this->queue;
            $job->args = array(
                'creates' => $this->creates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId,
                'userId' => $this->userId
            );
            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }

        $this->creates = [];
    }
}