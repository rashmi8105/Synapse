<?php
namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\UploadBundle\Service\UploadServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\CoreBundle\Entity\Person;
use Synapse\UploadBundle\Job\RiskModelUpload;
use Synapse\RiskBundle\Util\Constants\RiskModelConstants;
use Synapse\UploadBundle\Util\Constants\UploadConstant;
use SplFileObject;

/**
 * Handle course uploads
 *
 * @DI\Service("risk_model_upload_service")
 */
class RiskModelUploadService extends AbstractService implements UploadServiceInterface
{

    const SERVICE_KEY = 'risk_model_upload_service';

    const MODELID = 'ModelID';

    const RISKVARNAME = 'RiskVarName';

    const WEIGHT = 'Weight';

    const UPLOAD_ERROR = 'Upload Errors';

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

    private $modelWithVars;

    private $updateable;

    private $createable;

    private $cache;

    private $resque;

    private $updates;

    private $creates;

    private $jobs;

    private $totalRows;

    private $uploadId;

    private $userId;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "personService" = @DI\Inject("person_service"),
     *            "uploadFileLogService" = @DI\Inject("upload_file_log_service"),
     *            "cache" = @DI\Inject("synapse_redis_cache"),
     *            "resque" = @DI\Inject("bcc_resque.resque"),
     *            "ebiConfigService" = @DI\Inject("ebi_config_service")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $personService, $uploadFileLogService, $entityService, $cache, $resque, $ebiConfigService)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->personService = $personService;
        $this->uploadFileLogService = $uploadFileLogService;
        $this->organizationRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Organization");
        $this->cache = $cache;
        $this->resque = $resque;
        $this->updates = [];
        $this->creates = [];
        $this->jobs = [];
        $this->queue = 'default';
        $this->ebiConfigService = $ebiConfigService;
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
    public function load($filePath, $userId, $handler = null)
    {
        if (! file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->userId = $userId;

        $this->fileReader = new CSVReader($filePath, true, true);
        $this->modelWithVars = [];

        foreach ($this->fileReader as $idx => $row) {
            $this->modelWithVars[] = $row[strtolower(self::MODELID)];
        }

        $this->createable = $this->modelWithVars;
        $this->updateable = array_diff($this->modelWithVars, $this->createable);
        $this->totalRows = count($this->modelWithVars);

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
            if (in_array($row[strtolower(self::MODELID)], $this->createable)) {
                $person = $row[strtolower(self::MODELID)];
                $this->create($idx, $row);
            }
            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $processed[] = $row[strtolower(self::MODELID)];
            $i ++;
        }

        $this->queueForWrite();

        $this->cache->save("riskmodel.upload.{$this->uploadId}.jobs", $this->jobs);

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
                $row[strtolower(self::UPLOAD_ERROR)] = $rowErrors;
                $list[] = $row;
            }
        }

        $errorCSVFile = new SplFileObject("data://risk_uploads/errors/1-{$this->uploadId}-upload-errors.csv", 'w');


        $csvHeaders = $this->fileReader->getColumns();
        $csvHeaders[strtolower(self::UPLOAD_ERROR)] = self::UPLOAD_ERROR;
        $errorCSVFile->fputcsv($csvHeaders);

        foreach ($list as $fields) {
            $errorCSVFile->fputcsv($fields);
        }
    }

    public function generateDumpCSV()
    {
        $this->logger->info(")))))))))))))))))))))))))))))))))))) " . __FUNCTION__);
        $weightRepo = $this->repositoryResolver->getRepository("SynapseRiskBundle:RiskModelWeights");
        $weights = $weightRepo->getRiskModelWeights();
        $file = new SplFileObject("data://risk_uploads/risk-model-data.csv", 'w');
        $header = $this->getHeader();
        $file->fputcsv($header);

        if(count($weights) > 0)
        {
            foreach ($weights as $weight)
            {
                $file->fputcsv($weight);
            }
        }
        return RiskModelConstants::RISK_AMAZON_URL . "risk-model-data.csv";
    }

    public function getHeader()
    {
        return [

            RiskModelConstants::MODELID,
            RiskModelConstants::RISKVARNAME,
            RiskModelConstants::WEIGHT
        ];
    }

    public function getTotalRecordsCount()
    {
        $weightRepo = $this->repositoryResolver->getRepository("SynapseRiskBundle:RiskModelWeights");
        $totalCount = $weightRepo->getTotalWeightCount();
        return ['total_count'=> (int) $totalCount];
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
            $job = new RiskModelUpload();
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