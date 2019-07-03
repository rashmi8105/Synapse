<?php
namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\UploadBundle\Service\UploadServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVReader;
use Symfony\Component\Stopwatch\Stopwatch;
use SplFileObject;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\ReportsBundle\EntityDto\ElementDto;
use Synapse\ReportsBundle\EntityDto\ElementBucketDto;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\CalendarBundle\Job\InitialSync;
use Synapse\UploadBundle\Job\CreateReportElements;
use Synapse\UploadBundle\Util\Constants\UploadConstant;


/**
 * Handle Academic Update uploads
 *
 * @DI\Service("report_elements_upload_service")
 */
class ReportElementsUploadService extends AbstractService implements UploadServiceInterface
{

    const SERVICE_KEY = 'report_elements_upload_service';

    const ELEMENT_NAME = 'ElementName';

    const UPLOAD_ERROR = 'Upload Errors';

    const SECTIONID = 'SectionID';

    const ELEMENTNAME = 'ElementName';

    const DATATYPE = 'DataType';

    const DATASOURCE  = 'DataSource';

    const REDLOW = 'RedLow';

    const REDHIGH = 'RedHigh';

    const REDTEXT = 'RedText';

    const YELLOWLOW = 'YellowLow';

    const YELLOWHIGH = 'YellowHigh';

    const YELLOWTEXT = 'YellowText';

    const GREENLOW = 'GreenLow';

    const GREENHIGH = 'GreenHigh';

    const GREENTEXT = 'GreenText';

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
        $this->container = $container;
        $this->cache = $cache;        
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
        $this->logger->error(" >>>>>>>>>>>>>> I am in Report elemets upload Service");
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
            $this->affectedExternalIds[] = $row[strtolower(self::ELEMENT_NAME)];
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

            if ($idx === 0) {
                continue;
            }

            if (in_array($row[strtolower(self::ELEMENT_NAME)], $processed)) {
                continue;
            }

            if (in_array($row[strtolower(self::ELEMENT_NAME)], $this->createable)) {
                $this->create($idx, $row);
            }

            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $processed[] = $row[strtolower(self::ELEMENT_NAME)];
            $i ++;
        }

        $this->queueForWrite();

        $this->cache->save("reportElement.upload.{$this->uploadId}.jobs", $this->jobs);

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
                $row[strtolower(self::UPLOAD_ERROR)] = $rowErrors;
                $list[] = $row;
            }
        }

        $errorCSVFile = new SplFileObject("data://reports_master/errors/1-{$this->uploadId}-upload-errors.csv", 'w');


        $csvHeaders = $this->fileReader->getColumns();
        $csvHeaders[strtolower(self::UPLOAD_ERROR)] = self::UPLOAD_ERROR;
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
        $sectionElementRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_ELEMENT_REPO);		
        $sectionElements = $sectionElementRepository->dumpSectionElements();		
        $file = new SplFileObject("data://reports_master/report-elements-data.csv", 'w');
        $header = $this->getHeader();
        $file->fputcsv($header);
        if (count($sectionElements) > 0) {               
            foreach ($sectionElements as $elements) {
                $resp = [];
                $resp[] = $this->getInitalSet($elements);
                if (count($resp) > 0) {
                    $this->writeCsv($file, $resp);
                }
            }
        }
		return  'https://ebi-synapse-bucket.s3.amazonaws.com/reports-master/report-elements-data.csv';
    }

    public function writeCsv($file, $resp)
    {
        $writeCSV = [];
        ksort($resp);

        foreach ($resp as $r) {            
            $writeCSV[self::SECTIONID] = $r[self::SECTIONID];
            $writeCSV[self::ELEMENT_NAME] = $r[self::ELEMENT_NAME];
            $writeCSV[self::DATATYPE] = $r[self::DATATYPE];
            $writeCSV[self::DATASOURCE] = $r[self::DATASOURCE];
            $writeCSV[self::REDLOW] = isset($r[self::REDLOW]) ? $r[self::REDLOW] : '';
			$writeCSV[self::REDHIGH] = isset($r[self::REDHIGH]) ? $r[self::REDHIGH] : '';
			$writeCSV[self::REDTEXT] = isset($r[self::REDTEXT]) ? $r[self::REDTEXT] : '';
            $writeCSV[self::YELLOWLOW] = isset($r[self::YELLOWLOW]) ? $r[self::YELLOWLOW] : '';
			$writeCSV[self::YELLOWHIGH] = isset($r[self::YELLOWHIGH]) ? $r[self::YELLOWHIGH] : '';
			$writeCSV[self::YELLOWTEXT] = isset($r[self::YELLOWTEXT]) ? $r[self::YELLOWTEXT] : '';
			$writeCSV[self::GREENLOW] = isset($r[self::GREENLOW]) ? $r[self::GREENLOW] : '';
			$writeCSV[self::GREENHIGH] = isset($r[self::GREENHIGH]) ? $r[self::GREENHIGH] : '';
			$writeCSV[self::GREENTEXT] = isset($r[self::GREENTEXT]) ? $r[self::GREENTEXT] : '';
            $file->fputcsv($writeCSV);
        }
    }

    public function getHeader()
    {
        return [
            self::SECTIONID,
			self::ELEMENT_NAME,
			self::DATATYPE,
			self::DATASOURCE,
			self::REDLOW,
			self::REDHIGH,
			self::REDTEXT,
			self::YELLOWLOW,
			self::YELLOWHIGH,
			self::YELLOWTEXT,
			self::GREENLOW,
			self::GREENHIGH,
			self::GREENTEXT
        ];
    }


    public function getInitalSet($elements)
    {        
        $temp = [];
        $elementId = $elements['elementId'];
		$this->sectionElementBucketRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_ELEMENT_BUCKET_REPO);	
		$elementBuckets = $this->sectionElementBucketRepository->findBy(['elementId' => $elementId ]);
		$temp[self::SECTIONID] = $elements['sectionId'];        
        $temp[self::ELEMENT_NAME] = $elements['elementName'];

		if($elements['sourceType'] == 'F')
		{
            $temp['DataType'] = 'Factor';
			$dataSource = $elements['factorId'];
		} else if ($elements['sourceType'] == 'Q') {
			$temp['DataType'] = 'QuestionBank';
            $dataSource = $elements['surveyQuestionId'];
		} else {
			$dataSource = '';
            $temp['DataType'] = '';
		}
        $temp[self::DATASOURCE] = $dataSource;
		if(!empty($elementBuckets))
		{
			foreach($elementBuckets as $elementBucket)
			{				
				if($elementBucket->getBucketName() == 'Red')		
				{
                    $temp[self::REDLOW] = $elementBucket->getRangeMin();
					$temp[self::REDHIGH] = $elementBucket->getRangeMax();
					$temp[self::REDTEXT] = $elementBucket->getBucketText();
				}
				if($elementBucket->getBucketName() == 'Yellow')
				{		
					$temp[self::YELLOWLOW] = $elementBucket->getRangeMin();
                    $temp[self::YELLOWHIGH] = $elementBucket->getRangeMax();
					$temp[self::YELLOWTEXT] = $elementBucket->getBucketText();
				}
				if($elementBucket->getBucketName() == 'Green')
				{
					$temp[self::GREENLOW] = $elementBucket->getRangeMin();
					$temp[self::GREENHIGH] = $elementBucket->getRangeMax();
					$temp[self::GREENTEXT] = $elementBucket->getBucketText();
				}
			}
		} else {
			$temp[self::REDLOW]     = '';
			$temp[self::REDHIGH]    = '';
			$temp[self::REDTEXT]    = '';
			$temp[self::YELLOWLOW]  = '';
			$temp[self::YELLOWHIGH] = '';
			$temp[self::YELLOWTEXT] = '';
			$temp[self::GREENLOW]   = '';
			$temp[self::GREENHIGH]  = '';
			$temp[self::GREENTEXT]  = '';
		}        
        return $temp;
    }

    private function create($idx, $row)
    {
        $this->creates[$idx] = $row;
    }

    private function queueForWrite()
    { 
         if (count($this->creates)) {            
            $jobNumber = uniqid();            
            $job = new CreateReportElements();            
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