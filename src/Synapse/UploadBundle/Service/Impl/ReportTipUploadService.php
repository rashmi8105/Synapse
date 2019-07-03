<?php
namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\UploadBundle\Service\UploadServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVReader;
use Symfony\Component\Stopwatch\Stopwatch;
use SplFileObject;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\ReportsBundle\EntityDto\TipsDto;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\UploadBundle\Job\CreateReportTip;
use Synapse\UploadBundle\Util\Constants\UploadConstant;


/**
 * Handle Report Tip Uploads
 *
 * @DI\Service("report_tip_upload_service")
 */
class ReportTipUploadService extends AbstractService implements UploadServiceInterface
{

    const SERVICE_KEY = 'report_tip_upload_service';

    const TIP_NAME = 'TipName';

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

    private $userId;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *
     *            "uploadFileLogService" = @DI\Inject("upload_file_log_service"),
     *            "cache" = @DI\Inject("synapse_redis_cache"),
     *            "resque" = @DI\Inject("bcc_resque.resque"),
	  *            "container" = @DI\Inject("service_container"),
     *            "ebiConfigService" = @DI\Inject("ebi_config_service")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $uploadFileLogService, $cache, $resque, $ebiConfigService, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        $this->uploadFileLogService = $uploadFileLogService;
        $this->organizationRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Organization");
		$this->container = $container;
        $this->cache = $cache;
        $this->resque = $resque;
        $this->creates = [];
        $this->updates = [];
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
    public function load($filePath, $userId)
    {
        $this->logger->error(" >>>>>>>>>>>>>> I am in Report Tip upload Service");
        if (! file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->logger->info(" >>>>>>>>>>>>>> I got File" . $filePath);
        $this->userId = $userId;

        $this->fileReader = new CSVReader($filePath, true, true);
        $this->logger->info(" >>>>>>>>>>>>>> I got File - CSV LOADDED");
        $this->affectedExternalIds = [];

        $rowVal = 0;		
        foreach ($this->fileReader as $idx => $row) {
            $this->affectedExternalIds[] = $row[self::TIP_NAME];
        }		
        $this->logger->error(" >>>>>>>>>>>>>> I got File - FOR LOADDED");
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

            if (in_array($row[self::TIP_NAME], $this->createable)) {
                $this->create($idx, $row);
            }

            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $processed[] = $row[self::TIP_NAME];
            $i ++;
        }

        $this->queueForWrite();

        $this->cache->save("reportTip.upload.{$this->uploadId}.jobs", $this->jobs);

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
                $rowErrors = $this->createRow($idx,$errors, $rowErrors);
                $row[self::UPLOAD_ERROR] = $rowErrors;
                $list[] = $row;
            }
        }

        $errorCSVFile = new SplFileObject("data://reports_master/errors/1-{$this->uploadId}-upload-errors.csv", 'w');


        $csvHeaders = $this->fileReader->getColumns();
        $csvHeaders[self::UPLOAD_ERROR] = self::UPLOAD_ERROR;
        $errorCSVFile->fputcsv($csvHeaders);

        foreach ($list as $fields) {
            $errorCSVFile->fputcsv($fields);
        }
    }

    public function createRow($idx,$errors, $rowErrors)
    {
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
        return $rowErrors;
    }

    public function generateDumpCSV()
    {
        $tipRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_TIPS_REPO);		
        $tipsArray = $tipRepository->dumpSectionTips();                        
        $file = new SplFileObject("data://reports_master/report-tip-data.csv", 'w');
        $header = $this->getHeader();
        $file->fputcsv($header);
        if (count($tipsArray) > 0) {
            $csvArray = [];
            $temp = [];
            $currentId = NULL;
            $resp = [];
            foreach ($tipsArray as $tipData) {
                $temp['SectionID'] = $tipData['sectionId'];
                $temp['TipName'] = $tipData['tipName'];
                $temp['TipText'] = $tipData['tipText'];                
                $temp['DisplayOrder'] = $tipData['sequence']; 
                $resp[] = $temp;
            }          
            if (count($resp) > 0) {
                $this->writeCsv($file, $resp);
            }
        }
		return  'https://ebi-synapse-bucket.s3.amazonaws.com/reports-master/report-tip-data.csv';        
    }

    public function writeCsv($file, $resp)
    {
        $writeCSV = [];
        ksort($resp);        
        foreach ($resp as $r) {            
            $writeCSV['SectionID'] = $r['SectionID'];
            $writeCSV['TipName'] = $r['TipName'];
            $writeCSV['TipText'] = $r['TipText'];            
            $writeCSV['DisplayOrder'] = $r['DisplayOrder'];
            $file->fputcsv($writeCSV);
        }
    }

    public function getHeader()
    {
        return [
            'SectionID',
            'TipName',
			'TipText',
            'DisplayOrder'
        ];
    }

    private function create($idx, $row)
    {
        $this->creates[$idx] = $row;
    }

    private function queueForWrite()
    {
        $validatorObj = $this->container->get('report_element_validator_service');
		$reportSetupService = $this->container->get('reportsetup_service');
		$reportTipsRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_TIPS_REPO);		
		if (count($this->creates)) {            
            $jobNumber = uniqid();            
            $job = new CreateReportTip();            
            $this->resque = $this->container->get('bcc_resque.resque');                       
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