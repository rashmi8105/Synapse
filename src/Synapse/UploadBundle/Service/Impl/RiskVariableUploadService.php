<?php
namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\UploadBundle\Service\UploadServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVReader;
use Symfony\Component\Stopwatch\Stopwatch;
use SplFileObject;
use Synapse\RiskBundle\Util\Constants\RiskVariableConstants;
use Synapse\RiskBundle\Util\Constants\RiskModelConstants;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\UploadBundle\Util\Constants\UploadConstant;


/**
 * Handle Academic Update uploads
 *
 * @DI\Service("riskvariable_upload_service")
 */
class RiskVariableUploadService extends AbstractService implements UploadServiceInterface
{

    const SERVICE_KEY = 'riskvariable_upload_service';

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
        $this->organizationRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Organization");

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
        $this->logger->error(" >>>>>>>>>>>>>> I am in Risk Variable Upload Service");
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
            $this->affectedExternalIds[] = $row[strtolower(self::RISK_VAR_NAME)];
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

            if (in_array($row[strtolower(self::RISK_VAR_NAME)], $processed)) {
                continue;
            }

            if (in_array($row[strtolower(self::RISK_VAR_NAME)], $this->createable)) {
                $this->create($idx, $row);
            }

            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $processed[] = $row[strtolower(self::RISK_VAR_NAME)];
            $i ++;
        }

        $this->queueForWrite();

        $this->cache->save("riskvariable.upload.{$this->uploadId}.jobs", $this->jobs);

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

        $errorCSVFile = new SplFileObject("data://risk_uploads/errors/1-{$this->uploadId}-upload-errors.csv", 'w');


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

    public function createTempBucket($variable, &$temp)
    {
        if ($variable['variable_type'] == 'continuous') {
            $temp['B' . $variable[RiskVariableConstants::BUCKET_VALUE] . 'Min'] = $variable['value1'];
            $temp['B' . $variable[RiskVariableConstants::BUCKET_VALUE] . 'Max'] = $variable['value2'];
        } else {
            $temp['B' . $variable[RiskVariableConstants::BUCKET_VALUE] . 'Cat'] = $variable['value1'];
        }
    }

    public function generateDumpCSV()
    {
        $riskRepo = $this->repositoryResolver->getRepository("SynapseRiskBundle:RiskVariable");
        $variables = $riskRepo->getRiskVarialbeList();
        $file = new SplFileObject("data://risk_uploads/risk-variable-data.csv", 'w');
        $header = $this->getHeader();
        $file->fputcsv($header);

        if (count($variables) > 0) {

            $temp = [];
            $currentId = NULL;

            $resp = [];

            foreach ($variables as $variable) {

                if (is_null($currentId)) {

                    $temp = $this->getInitalSet($variable, $temp);
                }

                if (is_null($currentId) || $currentId == $variable['id']) {
                    $this->createTempBucket($variable, $temp);

                    $currentId = $variable['id'];
                } else {

                    $resp[$currentId] = $temp;

                    $currentId = null;

                    $temp = [];
                    $temp = $this->getInitalSet($variable, $temp);
                    $this->createTempBucket($variable, $temp);
                }
            }
            if (count($temp) > 0) {
                $resp[$currentId] = $temp;
            }

            if (count($resp) > 0) {
                $this->writeCsv($file, $resp);
            }
        }

        return RiskModelConstants::RISK_AMAZON_URL . "risk-variable-data.csv";
    }

    public function writeCsv($file, $resp)
    {
        $writeCSV = [];
        ksort($resp);

        foreach ($resp as $r) {
            // $writeCSV['Id'] = $r['Id'];
            $writeCSV[RiskVariableConstants::RISK_VAR_RISKVARNAME] = $r[RiskVariableConstants::RISK_VAR_RISKVARNAME];
            $writeCSV[RiskVariableConstants::RISK_VAR_RISKVARTYPE] = $r[RiskVariableConstants::RISK_VAR_RISKVARTYPE];
            $writeCSV[RiskVariableConstants::RISK_VAR_CALCULATED] = $r[RiskVariableConstants::RISK_VAR_CALCULATED];
            $writeCSV[RiskVariableConstants::RISK_VAR_SOURCETYPE] = $r[RiskVariableConstants::RISK_VAR_SOURCETYPE];
            $writeCSV[RiskVariableConstants::RISK_VAR_CAMPUSID] = $r[RiskVariableConstants::RISK_VAR_CAMPUSID];
            $writeCSV[RiskVariableConstants::RISK_VAR_SOURCEID] = $r[RiskVariableConstants::RISK_VAR_SOURCEID];

            if ($r[RiskVariableConstants::RISK_VAR_RISKVARTYPE] == 'continuous') {
                for ($i = 1; $i <= 7; $i ++) {
                    $writeCSV['B' . $i . 'Min'] = @$r['B' . $i . 'Min'];
                    $writeCSV['B' . $i . 'Max'] = @$r['B' . $i . 'Max'];
                }
                for ($i = 1; $i <= 7; $i ++) {
                    $writeCSV['B' . $i . 'Cat'] = "";
                }
            } else {

                for ($i = 1; $i <= 7; $i ++) {
                    $writeCSV['B' . $i . 'Min'] = "";
                    $writeCSV['B' . $i . 'Max'] = "";
                }
                for ($i = 1; $i <= 7; $i ++) {
                    $writeCSV['B' . $i . 'Cat'] = @$r['B' . $i . 'Cat'];
                }
            }

            $writeCSV[RiskVariableConstants::RISK_VAR_CALTYPE] = $r[RiskVariableConstants::RISK_VAR_CALTYPE];
            if (isset($r[RiskVariableConstants::RISK_VAR_CALMIN])) {
                $minDate = new \DateTime($r[RiskVariableConstants::RISK_VAR_CALMIN]);
                $r[RiskVariableConstants::RISK_VAR_CALMIN] = $minDate->format('m/d/Y');
            }
            if (isset($r[RiskVariableConstants::RISK_VAR_CALMAX])) {
                $maxDate = new \DateTime($r[RiskVariableConstants::RISK_VAR_CALMAX]);
                $r[RiskVariableConstants::RISK_VAR_CALMAX] = $maxDate->format('m/d/Y');
            }
            $writeCSV[RiskVariableConstants::RISK_VAR_CALMIN] = $r[RiskVariableConstants::RISK_VAR_CALMIN];
            $writeCSV[RiskVariableConstants::RISK_VAR_CALMAX] = $r[RiskVariableConstants::RISK_VAR_CALMAX];
            $file->fputcsv($writeCSV);
        }
    }

    public function getHeader()
    {
        return [

            RiskVariableConstants::RISK_VAR_RISKVARNAME,
            RiskVariableConstants::RISK_VAR_RISKVARTYPE,
            RiskVariableConstants::RISK_VAR_CALCULATED,
            RiskVariableConstants::RISK_VAR_SOURCETYPE,
            RiskVariableConstants::RISK_VAR_CAMPUSID,
            RiskVariableConstants::RISK_VAR_SOURCEID,

            'B1Min',
            'B1Max',
            'B2Min',
            'B2Max',
            'B3Min',
            'B3Max',
            'B4Min',
            'B4Max',
            'B5Min',
            'B5Max',
            'B6Min',
            'B6Max',
            'B7Min',
            'B7Max',
            'B1Cat',
            'B2Cat',
            'B3Cat',
            'B4Cat',
            'B5Cat',
            'B6Cat',
            'B7Cat',
            RiskVariableConstants::RISK_VAR_CALTYPE,
            RiskVariableConstants::RISK_VAR_CALMIN,
            RiskVariableConstants::RISK_VAR_CALMAX
        ];
    }

    public function getInitalSet($variable, $temp)
    {
        // $temp['Id'] = $variable['id'];
        $temp[RiskVariableConstants::RISK_VAR_RISKVARNAME] = $variable['risk_b_variable'];
        $temp[RiskVariableConstants::RISK_VAR_RISKVARTYPE] = $variable['variable_type'];
        $temp[RiskVariableConstants::RISK_VAR_CALCULATED] = ($variable['is_calculated']) ? 'Yes' : 'No';
        $temp[RiskVariableConstants::RISK_VAR_SOURCETYPE] = $variable['source'];
        $temp[RiskVariableConstants::RISK_VAR_CAMPUSID] = $variable['campus_id'];
        $temp[RiskVariableConstants::RISK_VAR_CALTYPE] = $variable['calc_type'];
        $temp[RiskVariableConstants::RISK_VAR_CALMIN] = $variable['calculation_start_date'];
        $temp[RiskVariableConstants::RISK_VAR_CALMAX] = $variable['calculation_end_date'];

        $source = trim(strtolower($variable['source']));

        switch ($source) {
            case 'profile':

                $temp[RiskVariableConstants::RISK_VAR_SOURCEID] = $variable['meta_key'];

                break;
            case 'surveyquestion':
                $temp[RiskVariableConstants::RISK_VAR_SOURCEID] = $variable['survey_id'] . "-" . $variable['survey_questions_id'];

                break;
            case 'surveyfactor':
                $temp[RiskVariableConstants::RISK_VAR_SOURCEID] = $variable['survey_id'] . "-" . $variable['factor_id'];

                break;

            case 'isp':
                $temp[RiskVariableConstants::RISK_VAR_SOURCEID] = $variable['meta_name'];

                break;
            case 'isq':
                $temp[RiskVariableConstants::RISK_VAR_SOURCEID] = $variable['org_question_id'];
                break;

            case 'questionbank':
                $temp[RiskVariableConstants::RISK_VAR_SOURCEID] = $variable['ebi_question_id'];
                break;
            default:
                $temp[RiskVariableConstants::RISK_VAR_SOURCEID] = "";
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

            $createObject = 'Synapse\UploadBundle\Job\CreateRiskVariable';
            $jobNumber = uniqid();
            $job = new $createObject();
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

    public function getEbiProfileIdByName($profileName)
    {
        $profileRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:EbiMetadata");
        $profile = $profileRepo->findOneBy([
            'key' => $profileName
        ]);
        if ($profile) {
            return $profile->getId();
        } else {
            return - 1;
        }
    }

    public function getOrgProfileIdByName($profileName, $campus)
    {
        try {
            $profileRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgMetadata");
            $profile = $profileRepo->getProfileByName($profileName, $campus);
            return $profile['id'];
        } catch (\Exception $e) {
            throw new ValidationException([
                "Profile not found in this campus"
            ], "Profile not found in this campus", "profile not found");
        }
    }
    
}