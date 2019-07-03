<?php
namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Repository\DatablockMasterRepository;
use Synapse\CoreBundle\Repository\DatablockQuestionsRepository;
use Synapse\CoreBundle\Repository\EbiQuestionRepository;
use Synapse\CoreBundle\Repository\SurveyRepository;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\SurveyBundle\Repository\FactorRepository;
use Synapse\UploadBundle\Service\UploadServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\CoreBundle\Entity\DatablockQuestions;
use Symfony\Component\Stopwatch\Stopwatch;
use Synapse\SurveyBundle\Util\Constants\SurveyConstant;
use Synapse\UploadBundle\Util\Constants\UploadConstant;
use SplFileObject;

/**
 * Handle Survey uploads
 *
 * @DI\Service("survey_upload_service")
 */
class SurveyUploadService extends AbstractService implements UploadServiceInterface
{

    const SERVICE_KEY = 'survey_upload_service';

    /**
     * Object containing the loaded file
     *
     * @var CSVReader
     */
    private $fileReader;

    private $uploadFileLogService;

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

    private $type;


    /**
     * @var DatablockMasterRepository
     */
    private $datablockMasterRepository;

    /**
     * @var DatablockQuestionsRepository
     */
    private $datablockQuestionsRepository;

    /**
     * @var EbiQuestionRepository
     */
    private $ebiQuestionRepository;

    /**
     * @var FactorRepository
     */
    private $factorRepository;

    /**
     * @var SurveyRepository
     */
    private $surveyRepository;


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
        $this->cache = $cache;
        $this->resque = $resque;

        $this->creates = [];
        $this->updates = [];
        $this->jobs = [];

        $this->uploadFileLogService = $uploadFileLogService;
        $this->ebiConfigService = $ebiConfigService;

        $this->datablockMasterRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:DatablockMaster');
        $this->datablockQuestionsRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:DatablockQuestions');
        $this->ebiQuestionRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:EbiQuestion');
        $this->factorRepository = $this->repositoryResolver->getRepository('SynapseSurveyBundle:Factor');
        $this->surveyRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Survey');

        $this->queue = 'default';
    }


    /**
     * Load the file into memory
     *
     * @param string $filePath - Path to the current file
     * @param int $orgId
     * @param $type
     * @return array
     * @throws \Exception
     */
    public function load($filePath, $orgId, $type)
    {
        if (! file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->orgId = $orgId;
        $this->type = $type;

        $this->fileReader = new CSVReader($filePath, true, true);
        $this->affectedExternalIds = [];

        $rowVal = 0;
        foreach ($this->fileReader as $idx => $row) {
            $this->affectedExternalIds[] = $rowVal ++;
        }

        $existingSurvey = null;
        $existingSurvey = $existingSurvey ? $existingSurvey : [];
        $this->createable = array_diff($this->affectedExternalIds, array_keys($existingSurvey));
        $this->totalRows = count($this->affectedExternalIds);

        $fileData = [
            'totalRows' => $this->totalRows,
            'new' => count($this->createable),
            'existing' => count($this->updateable)
        ];

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
                $row[SurveyConstant::UPLOAD_ERROR] = $rowErrors;
                $list[] = $row;
            }
        }

        $errorCSVFile = new SplFileObject("data://survey_uploads/errors/{$this->orgId}-{$this->uploadId}-upload-errors.csv", 'w');

        $csvHeaders = $this->fileReader->getColumns();
        $csvHeaders[SurveyConstant::UPLOAD_ERROR] = SurveyConstant::UPLOAD_ERROR;
        $errorCSVFile->fputcsv($csvHeaders);


        foreach ($list as $fields) {
            $errorCSVFile->fputcsv($fields);
        }
    }

    public function generateDumpCSV($type = SurveyConstant::BLOCK, $uploadID = null)
    {
        $rows = [];

        if (! $uploadID) {
            $uploadID = $this->uploadId;
        }
        $filename = "{$uploadID}-survey-" . $type . "-data.csv";

        $headerRow = [
            SurveyConstant::LONGITUDINALID,
            SurveyConstant::SURVEYID,
            SurveyConstant::FACTORID
        ];

        $surveyInfo = $this->datablockQuestionsRepository->getSurveyBlocks();
        if (isset($surveyInfo) && count($surveyInfo) > 0) {
            foreach ($surveyInfo as $data) {
                $rows[] = [
                    SurveyConstant::LONGITUDINALID => ! empty($data[SurveyConstant::LONGITUDINALID]) ? $data[SurveyConstant::LONGITUDINALID] : "",
                    SurveyConstant::SURVEYID => ! empty($data[SurveyConstant::SURVEYID]) ? $data[SurveyConstant::SURVEYID] : "",
                    SurveyConstant::FACTORID => ! empty($data[SurveyConstant::FACTORID]) ? $data[SurveyConstant::FACTORID] : "",
                    SurveyConstant::SURVEYBLOCKID => ! empty($data[SurveyConstant::SURVEYBLOCKID]) ? $data[SurveyConstant::SURVEYBLOCKID] : "",
                    SurveyConstant::QNBR => ! empty($data[SurveyConstant::QNBR]) ? $data[SurveyConstant::QNBR] : "",
                    SurveyConstant::QTEXT => ! empty($data[SurveyConstant::QTEXT]) ? $data[SurveyConstant::QTEXT] : "",
                    SurveyConstant::FIELDNAME => ! empty($data[SurveyConstant::FIELDNAME]) ? $data[SurveyConstant::FIELDNAME] : "",
                    SurveyConstant::FACTOR_DESC => ! empty($data[SurveyConstant::FACTOR_DESC]) ? $data[SurveyConstant::FACTOR_DESC] : ""
                ];
            }
        }

        $file = new SplFileObject("data://survey_uploads/{$filename}", 'w');

        array_push($headerRow, SurveyConstant::SURVEYBLOCKID, SurveyConstant::QNBR, SurveyConstant::QTEXT, SurveyConstant::FIELDNAME, SurveyConstant::FACTOR_DESC);

        $file->fputcsv($headerRow);

        foreach ($rows as $row) {
            $file->fputcsv($row);
        }
    }

    private function create($idx, $person)
    {
        $this->creates[$idx] = $person;
    }

    private function queueForWrite()
    {
        if (count($this->creates)) {

            $createObject = 'Synapse\UploadBundle\Job\CreateSurvey';
            $jobNumber = uniqid();
            $job = new $createObject();
            $job->queue = $this->queue;
            $job->args = array(
                'creates' => $this->creates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId,
                'orgId' => $this->orgId,
                'type' => $this->type
            );

            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }

        $this->creates = [];
    }

    public function saveSurveyBlock($type, $data)
    {
        if ($type != SurveyConstant::BLOCK) {
            $this->throwMessage(SurveyConstant::INVALID_TYPE, SurveyConstant::INVALID_TYPE_KEY);
        }

        $ebiQuestionId = isset($data[SurveyConstant::LONGITUDINALID]) ? $data[SurveyConstant::LONGITUDINALID] : "";
        $surveyId = $data['SurvID'];
        $factorId = $data['FactorID'];

        $datablockQuestionsEntity = new DatablockQuestions();

        $surveyBlockID = $data[SurveyConstant::SURVEYBLOCKID];
        $errorMsg       = SurveyConstant::INVALID_SURVEYBLOCK;
        $errorMsgKey    = SurveyConstant::INVALID_SURVEYBLOCK_KEY;

        $surveyBlockObject = $this->datablockMasterRepository->find($surveyBlockID);
        $this->isObjectExist($surveyBlockObject, $errorMsg, $errorMsgKey);

        $datablockQuestionsEntity->setDatablock($surveyBlockObject);

        if (strlen($ebiQuestionId) == 0) {
            $surveyObject = $this->surveyRepository->find($surveyId);
            $this->isObjectExist($surveyObject, SurveyConstant::INVALID_SURVEY, SurveyConstant::INVALID_SURVEY_KEY);
            $factorObject = $this->factorRepository->find($factorId);
            $this->isObjectExist($factorObject, SurveyConstant::INVALID_FACTOR, SurveyConstant::INVALID_FACTOR_KEY);
            $existingDatablockQuestionsEntity = $this->datablockQuestionsRepository->findOneBy(array('survey' => $surveyObject, 'factor' => $factorObject, 'datablock' => $surveyBlockObject));
            if(!$existingDatablockQuestionsEntity) {
                $datablockQuestionsEntity->setSurvey($surveyObject);
                $datablockQuestionsEntity->setFactor($factorObject);
                $datablockQuestionsEntity->setType('factor');
            }   else {
                return $existingDatablockQuestionsEntity;
            }
        } else {
            $ebiQuestionObject = $this->ebiQuestionRepository->find($ebiQuestionId);
            $this->isObjectExist($ebiQuestionObject, SurveyConstant::INVALID_LONGITUNALID, SurveyConstant::INVALID_LONGITUNALID_KEY);
            $existingDatablockQuestionsEntity = $this->datablockQuestionsRepository->findOneBy(array('ebiQuestion' => $ebiQuestionObject, 'datablock' => $surveyBlockObject));
            if(!$existingDatablockQuestionsEntity) {
                $datablockQuestionsEntity->setEbiQuestion($ebiQuestionObject);
                $datablockQuestionsEntity->setType('bank');
            } else {
                return $existingDatablockQuestionsEntity;
            }
        }

        return $datablockQuestionsEntity;
    }

    private function isObjectExist($object, $message, $key)
    {
        if (! isset($object) || empty($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    private function throwMessage($message, $key)
    {
        throw new ValidationException([
            $message
        ], $message, $key);
    }
}