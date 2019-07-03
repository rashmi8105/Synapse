<?php
namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\UploadBundle\Service\UploadServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVReader;
use Symfony\Component\Stopwatch\Stopwatch;
use SplFileObject;
use Synapse\UploadBundle\Util\Constants\UploadConstant;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * Handle Academic Update uploads
 *
 * @DI\Service("ourstud_report_upload_service")
 */
class OurStudentReportUploadService extends AbstractService implements UploadServiceInterface
{

    const SERVICE_KEY = 'ourstud_report_upload_service';

    const RISK_VAR_NAME = 'RiskVarName';

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

    private $academicUpdateService;

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
     *            "ebiConfigService" = @DI\Inject("ebi_config_service")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $uploadFileLogService, $cache, $resque, $ebiConfigService)
    {
        parent::__construct($repositoryResolver, $logger);
        
        $this->uploadFileLogService = $uploadFileLogService;
        
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
    public function load($filePath)
    {
        if (! file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->fileReader = new CSVReader($filePath, true, true);

        $this->affectedExternalIds = [];
        
        $rowVal = 0;
        
        foreach ($this->fileReader as $idx => $row) {
            $this->affectedExternalIds[] = $row[strtolower(UploadConstant::OUR_STUD_REPORT_COL_LONGITUDINALID)] . "-" . $row[strtolower(UploadConstant::OUR_STUD_REPORT_COL_SURVID)] . "-" . $row[strtolower(UploadConstant::OUR_STUD_REPORT_COL_FACTORID)];
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
            $processingRow = $row[strtolower(UploadConstant::OUR_STUD_REPORT_COL_LONGITUDINALID)] . "-" . $row[strtolower(UploadConstant::OUR_STUD_REPORT_COL_SURVID)] . "-" . $row[strtolower(UploadConstant::OUR_STUD_REPORT_COL_FACTORID)];
            if ($idx === 0) {
                continue;
            }
            
            if (in_array($processingRow, $processed)) {
                continue;
            }
            
            if (in_array($processingRow, $this->createable)) {
                $this->create($idx, $row);
            }
            /**
             * No Batch..
             * entire thing goes
             */
            /*
             * if (($i % $batchSize) === 0) { $this->queueForWrite(); }
             */
            
            $processed[] = $processingRow;
            $i ++;
        }
        
        $this->queueForWrite();
        
        $this->cache->save("ourstudreport.upload.{$this->uploadId}.jobs", $this->jobs);
        
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
                $rowErrors = $this->createRow($idx, $errors, $rowErrors);
                $row[self::UPLOAD_ERROR] = $rowErrors;
                $list[] = $row;
            }
        }
        
        $errorCSVFile = new SplFileObject("data://" . UploadConstant::OUR_STUD_REPORT_UPLOAD_DIR . "/errors/-1-{$this->uploadId}-upload-errors.csv", 'w');


        $csvHeaders = $this->fileReader->getColumns();
        $csvHeaders[self::UPLOAD_ERROR] = self::UPLOAD_ERROR;
        $errorCSVFile->fputcsv($csvHeaders);



        foreach ($list as $fields) {
            $errorCSVFile->fputcsv($fields);
        }
    }

    public function createRow($idx, $errors, $rowErrors)
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
        $reportSectionRepo = $this->repositoryResolver->getRepository("SynapseReportsBundle:ReportSections");
        
        $filename = "-1-our-student-report-date-existing.csv";
        $file = new SplFileObject("data://" . UploadConstant::OUR_STUD_REPORT_UPLOAD_DIR . "/$filename", 'w');
        $header = $this->getHeader();
        $file->fputcsv($header);
        $reportSectionData = $reportSectionRepo->ourStudentReportExistingData();
        if ($reportSectionData) {
            foreach ($reportSectionData as $reportSection) {
                $writeData = [];
                $writeData[] = $reportSection['longitu'];
                $writeData[] = $reportSection['survey'];
                $writeData[] = $reportSection['factor'];
                $writeData[] = $reportSection['report_sequence'];
                $writeData[] = $reportSection['report_section_name'];
                $writeData[] = $reportSection['report_section_name'];
                if (is_null($reportSection['is_choices'])) {
                    $bucketNames = explode(",", $reportSection['bucket_name']);
                    $minRages = explode(",", $reportSection['min_range']);
                    $maxRages = explode(",", $reportSection['max_range']);
                    $nLow = '';
                    $nHigh = '';
                    
                    $dLow = '';
                    $dHigh = '';
                    if (count($bucketNames) > 0) {
                        if ($bucketNames[0] == 'Denominator') {
                            $dLow = $minRages[0];
                            $nLow = $minRages[1];
                            
                            $dHigh = $maxRages[0];
                            $nHigh = $maxRages[1];
                        } else {
                            $nLow = $minRages[0];
                            $dLow = $minRages[1];
                            
                            $nHigh = $maxRages[0];
                            $dHigh = $maxRages[1];
                        }
                    }
                    $writeData[] = $nLow;
                    $writeData[] = $nHigh;
                    $writeData[] = $dLow;
                    $writeData[] = $dHigh;
                    
                    $writeData[] = "";
                    $writeData[] = "";
                } else {
                    
                    $writeData[] = '';
                    $writeData[] = '';
                    $writeData[] = '';
                    $writeData[] = '';
                    $numCh = [];
                    $dCh = [];
                    $bucketNames = explode(",", $reportSection['bucket_name']);
                    $bucketValues = explode(",", $reportSection['bucket_value']);
                    if (count($bucketNames) > 0) {
                        for ($i = 0; $i < count($bucketNames); $i ++) {
                            if ($bucketNames[$i] == 'Denominator') {
                                if (isset($bucketValues[$i])) {
                                    $dCh[] = $bucketValues[$i];
                                }
                            } else {
                                if (isset($bucketValues[$i])) {
                                    $numCh[] = $bucketValues[$i];
                                }
                            }
                        }
                    }
                   
                    if(count($numCh) > 0)
                    {
                        $writeData[] = implode(",", $numCh);
                    }else{
                        $writeData[] = fputcsv('');
                    }
                    if(count($dCh) > 0)
                    {
                        $writeData[] = implode(",", $dCh);
                    }else{
                        $writeData[] = fputcsv('');
                    }
                }
                
                $file->fputcsv($writeData);
            }
        }
        /*
         * if(count($weights) > 0) { foreach ($weights as $weight) { $file->fputcsv($weight); } }
         */
        // return RiskModelConstants::RISK_AMAZON_URL . "risk-model-data.csv";
       
    }

    public function writeCsv($file, $resp)
    {}

    public function getHeader()
    {
        $header = [
            
            'LongitudinalID',
            'SurvID',
            'FactorID',
            'ReportSectionID',
            'ReportSectionName',
            'DisplayLabel',
            'NumeratorLow',
            'NumeratorHigh',
            'DenominatorLow',
            'DenominatorHigh',
            'NumeratorChoices',
            'DenominatorChoices'
        ]
        ;
        return $header;
    }

    public function getInitalSet($variable, $temp)
    {}

    private function create($idx, $row)
    {
        $this->creates[$idx] = $row;
    }

    private function queueForWrite()
    {
        $this->logger->info("****************************** Q for write" . count($this->creates));
        if (count($this->creates)) {
            $this->logger->info("****************************** Q for write IF");
            $createObject = 'Synapse\UploadBundle\Job\CreateOurStudentReportData';
            $jobNumber = uniqid();
            $job = new $createObject();
            $job->queue = $this->queue;
            $job->args = array(
                'creates' => $this->creates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId
            // 'userId' => $this->userId
                        );
            
            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }
        
        $this->creates = [];
    }
}