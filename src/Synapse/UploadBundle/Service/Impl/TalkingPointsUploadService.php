<?php
namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\UploadBundle\Service\UploadServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\CoreBundle\Entity\TalkingPoints;
use Synapse\UploadBundle\Job\CreateTalkingPoints;
use Synapse\UploadBundle\Job\ProcessTalkingPoints;
use Symfony\Component\Stopwatch\Stopwatch;
use SplFileObject;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Handle Talking Points uploads
 *
 * @DI\Service("talking_points_upload_service")
 */
class TalkingPointsUploadService extends AbstractService implements UploadServiceInterface
{

    const SERVICE_KEY = 'talking_points_upload_service';

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

    private $talkingPointsService;

    private $uploadFileLogService;

    private $organizationRepository;

    private $affectedExternalIds;

    private $createable;

    private $updateable;

    private $cache;

    private $resque;

    private $creates;

    private $updates;

    private $jobs;

    private $totalRows;

    private $uploadId;

    private $orgId;

    private $talkingPointsRepository;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "talkingPointsService" = @DI\Inject("talkingpoint_service"),
     *            "uploadFileLogService" = @DI\Inject("upload_file_log_service"),
     *            "cache" = @DI\Inject("synapse_redis_cache"),
     *            "resque" = @DI\Inject("bcc_resque.resque"),
     *            "ebiConfigService" = @DI\Inject("ebi_config_service")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $talkingPointsService, $uploadFileLogService, $cache, $resque, $ebiConfigService)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->talkingPointsService = $talkingPointsService;
        $this->uploadFileLogService = $uploadFileLogService;
        $this->organizationRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Organization");
        $this->talkingPointsRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:TalkingPoints");
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
    public function load($filePath, $orgId = 1)
    {
        if (! file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->orgId = $orgId;

        $this->fileReader = new CSVReader($filePath, true, true);
        $this->affectedExternalIds = [];

        $rowVal = 0;
        foreach ($this->fileReader as $idx => $row) {
            $this->affectedExternalIds[] = $rowVal ++;
        }

        $existingTalkingPoints = null;
        $existingTalkingPoints = $existingTalkingPoints ? $existingTalkingPoints : [];
        $this->createable = array_diff($this->affectedExternalIds, array_keys($existingTalkingPoints));
        $this->totalRows = count($this->affectedExternalIds);

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

            $this->create($idx, $row);

            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $i ++;
        }

        $this->queueForWrite();

        $this->cache->save("organization.talkingpoints.upload.{$this->uploadId}.jobs", $this->jobs);

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
                $row[self::UPLOAD_ERROR] = $rowErrors;
                $list[] = $row;
            }
        }

        $errorCSVFile = new SplFileObject("data://talking_points_uploads/errors/1-{$this->uploadId}-upload-errors.csv", 'w');

        $csvHeaders = $this->fileReader->getColumns();
        $csvHeaders[self::UPLOAD_ERROR] = self::UPLOAD_ERROR;
        $errorCSVFile->fputcsv($csvHeaders);


        foreach ($list as $fields) {
            $errorCSVFile->fputcsv($fields);
        }
    }

    public function generateDumpCSV()
    {
        $getAllTalkingPoints = $this->talkingPointsRepository->getAllTalkingPoints();
        $rows = [];
        if (isset($getAllTalkingPoints) && count($getAllTalkingPoints) > 0) {

            $talkingPointArr = array();
            $repArr = array();

            foreach ($getAllTalkingPoints as $talkingPoint) {

                if (ucfirst($talkingPoint['type']) == "S") {
                    $arrKey = "Sur_" . $talkingPoint['qusetonid'];
                    $talkingPointArr[$arrKey]['kind'] = "Survey";
                    $talkingPointArr[$arrKey]['questionId'] = $talkingPoint['qusetonid'];
                }

                if (ucfirst($talkingPoint['type']) == "P") {
                    $arrKey = "Pro_" . $talkingPoint['metadateid'];
                    $talkingPointArr[$arrKey]['kind'] = "Profile";
                }

                if (! empty($talkingPoint['itemName'])) {
                    $talkingPointArr[$arrKey]['Item'] = $talkingPoint['itemName'];
                }

                if ($talkingPoint['talkingPointsType'] == "W") {
                    $talkingPointArr[$arrKey][UploadConstant::WEAKNESS_TEXT] = $talkingPoint['description'];
                    $talkingPointArr[$arrKey][UploadConstant::MIN_WEAKNESS_TEXT] = $talkingPoint['minRange'];
                    $talkingPointArr[$arrKey][UploadConstant::MAX_WEAKNESS_TEXT] = $talkingPoint['maxRange'];
                } else {

                    $talkingPointArr[$arrKey][UploadConstant::STRENGTH_TEXT] = $talkingPoint['description'];
                    $talkingPointArr[$arrKey][UploadConstant::MIN_STRENGTH_RANGE] = $talkingPoint['minRange'];
                    $talkingPointArr[$arrKey][UploadConstant::MAX_STRENGTH_RANGE] = $talkingPoint['maxRange'];
                }
            }

            foreach ($talkingPointArr as $data) {

                $rows[] = [
                    UploadConstant::QUES_PROFILE_ITEM => '',
                    'Item' => ! empty($data[strtolower('Item')]) ? $data[strtolower('Item')] : "",
                    'Kind' => ! empty($data[strtolower('kind')]) ? $data[strtolower('kind')] : "",
                    UploadConstant::WEAKNESS_TEXT => ! empty($data[strtolower(UploadConstant::WEAKNESS_TEXT)]) ? $data[strtolower(UploadConstant::WEAKNESS_TEXT)] : "",
                    UploadConstant::STRENGTH_TEXT => ! empty($data[strtolower(UploadConstant::STRENGTH_TEXT)]) ? $data[strtolower(UploadConstant::STRENGTH_TEXT)] : "",
                    'WeaknessLow' => ! empty($data[strtolower(UploadConstant::MIN_WEAKNESS_TEXT)]) ? $data[strtolower(UploadConstant::MIN_WEAKNESS_TEXT)] : "",
                    'WeaknessHigh' => ! empty($data[strtolower(UploadConstant::MAX_WEAKNESS_TEXT)]) ? $data[strtolower(UploadConstant::MAX_WEAKNESS_TEXT)] : "",
                    'StrengthLow' => ! empty($data[strtolower(UploadConstant::MIN_STRENGTH_RANGE)]) ? $data[strtolower(UploadConstant::MIN_STRENGTH_RANGE)] : "",
                    'StrengthHigh' => ! empty($data[strtolower(UploadConstant::MAX_STRENGTH_RANGE)]) ? $data[strtolower(UploadConstant::MAX_STRENGTH_RANGE)] : ""
                ];
            }
        }

        $file = new SplFileObject("data://talking_points_uploads/{$this->uploadId}-talking-points-data.csv", 'w');
        $file->fputcsv([
            UploadConstant::QUES_PROFILE_ITEM,
            'Item',
            'Kind',
            UploadConstant::WEAKNESS_TEXT,
            UploadConstant::STRENGTH_TEXT,
            'WeaknessLow',
            'WeaknessHigh',
            'StrengthLow',
            'StrengthHigh'
        ]);

        foreach ($rows as $fields) {
            $file->fputcsv($fields);
        }
    }

    private function create($idx, $person)
    {
        $this->creates[$idx] = $person;
    }

    public function getFileObject()
    {
        return $this->fileReader;
    }

    private function queueForWrite()
    {
        if (count($this->creates)) {

            $this->creates = array_change_key_case($this->creates,CASE_LOWER);

            $createObject = 'Synapse\UploadBundle\Job\CreateTalkingPoints';
            $jobNumber = uniqid();
            $job = new $createObject();
            $job->queue = $this->queue;
            $job->args = array(
                'creates' => $this->creates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId
            );

            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }

        $this->creates = [];
    }
}