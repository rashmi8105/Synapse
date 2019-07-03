<?php
namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\UploadBundle\Service\UploadServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\CoreBundle\Entity\AcademicUpdate;
use Synapse\UploadBundle\Job\CreateAcademicUpdate;
use Synapse\UploadBundle\Job\ProcessAcademicUpdate;
use Symfony\Component\Stopwatch\Stopwatch;
use SplFileObject;

/**
 * Handle Facoruploads
 *
 * @DI\Service("factor_upload_service")
 */
class FactorUploadService extends AbstractService implements UploadServiceInterface
{

    const SERVICE_KEY = 'factor_upload_service';

    /**
     * The path to the file we're working on
     *
     * @var string
     */
    private $filePath;

    /**
     * Object containing the loaded file
     *
     * @var \SPLFileObject
     */
    private $fileObject;

    /**
     * The file handler to use for this upload
     *
     * @var Synapse\CoreBundle\Util\FileHandler|null
     */
    private $handler;

    private $uploadFileLogService;

    private $organizationRepository;

    private $affectedExternalIds;

    private $createable;


    private $cache;

    private $resque;

    private $creates;

    private $updates;

    private $jobs;

    private $totalRows;

    private $uploadId;

    private $orgId;

    private $userId;

    const FactorID = 'FactorID';

    const LongitudinalID = 'LongitudinalID';

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "uploadFileLogService" = @DI\Inject("upload_file_log_service"),
     *            "cache" = @DI\Inject("synapse_redis_cache"),
     *            "resque" = @DI\Inject("bcc_resque.resque"),
     *            "ebiConfigService" = @DI\Inject("ebi_config_service")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $uploadFileLogService, $cache, $resque, $ebiConfigService)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->uploadFileLogService = $uploadFileLogService;
        $this->organizationRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Organization");
        $this->factorQuesRepository = $this->repositoryResolver->getRepository("SynapseSurveyBundle:FactorQuestions");
        $this->cache = $cache;
        $this->resque = $resque;
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
    public function load($filePath, $orgId, $userId, $handler = null)
    {
        if (! file_exists($filePath)) {
            throw new \Exception("File not found");
        }
        
        $this->orgId = $orgId;
        $this->userId = $userId;

        $this->fileObject = new CSVReader($filePath, true, true);
        $this->affectedExternalIds = [];
        
        foreach ($this->fileObject as $idx => $row) {
            $this->affectedExternalIds[] = $row[strtolower(self::FactorID)];
        }
        $this->createable = $this->affectedExternalIds;
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
     * @return array|bool Returns array containg information about the upload, or false on failure
     */
    public function process($uploadId)
    {
        $this->uploadId = $uploadId;
       /* try {
            $queues = json_decode($this->ebiConfigService->get('Upload_Queues'));
            $this->queue = $queues[mt_rand(0, count($queues) - 1)];
        } catch (\Exception $e) {
            $this->queue = 'default';
        }*/
        $this->queue = 'default';
        $batchSize = 30;
        $i = 1;
        foreach ($this->fileObject as $idx => $row) {
            
            if ($idx === 0) {
                continue;
            }
            $this->create($idx, $row);
            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }
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

    public function generateDumpCSV($uploadId)
    {
        $file = new SplFileObject("data://factor_uploads/{$uploadId}-factor-data.csv", 'w');
        $getFatorData = $this->factorQuesRepository->getFactorQuestionsForDownload();
        
        $file->fputcsv([
            'LongitudinalID',
            'FactorID',
        ]);
        $rows = array();
        foreach ($getFatorData as $factorData) {
            $file->fputcsv($factorData);
        }
    }
    
    
    public function getFactorDownloadData(){
        
        $getFatorData = $this->factorQuesRepository->getFactorQuestionsForDownload();
        
        $downloadCol = array( 'LongitudinalID',
            'FactorID');
        
        $returnArr =  array(
            
            'data' => $getFatorData,
            'cols' => $downloadCol
        );
        return  $returnArr;
    }

    private function create($idx, $row)
    {
        $this->creates[$idx] = $row;
    }

    public function getFileObject()
    {
        return $this->fileObject;
    }

    private function queueForWrite()
    {
        if (count($this->creates)) {
            $createObject = 'Synapse\UploadBundle\Job\CreateFactor';
            $jobNumber = uniqid();
            $job = new $createObject();
            $job->queue = $this->queue;
            $job->args = array(
                'creates' => $this->creates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId,
                'userId' => $this->userId,
                'orgId' => $this->orgId
            );
            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }

        $this->creates = [];
    }
}