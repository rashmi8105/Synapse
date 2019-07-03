<?php
namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\UploadBundle\Service\UploadServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\UploadBundle\Job\CreateCourse;
use Synapse\UploadBundle\Job\ProcessCourseUpload;
use Symfony\Component\Stopwatch\Stopwatch;
use Synapse\UploadBundle\Util\Constants\UploadConstant;
use SplFileObject;

/**
 * Handle course uploads
 *
 * @DI\Service("course_upload_service")
 */
class CourseUploadService extends AbstractService implements UploadServiceInterface
{

    const SERVICE_KEY = 'course_upload_service';

    const UNIQUECOURSESECID = 'UniqueCourseSectionId';

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
     * @var \SPLFileObject
     */
    private $fileObject;

    /**
     * The file handler to use for this upload
     *
     * @var Synapse\CoreBundle\Util\FileHandler|null
     */
    private $handler;

    private $courseService;

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

    private $orgCourseRepository;

    private $doctrine;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "courseService" = @DI\Inject("course_service"),
     *            "uploadFileLogService" = @DI\Inject("upload_file_log_service"),
     *            "cache" = @DI\Inject("synapse_redis_cache"),
     *            "resque" = @DI\Inject("bcc_resque.resque"),
     *            "doctrine" = @DI\Inject("doctrine"),
     *            "ebiConfigService" = @DI\Inject("ebi_config_service")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $courseService, $uploadFileLogService, $cache, $resque, $doctrine, $ebiConfigService)
    {
        parent::__construct($repositoryResolver, $logger, $doctrine);
        $this->courseService = $courseService;
        $this->uploadFileLogService = $uploadFileLogService;
        $this->organizationRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Organization");
        $this->orgCoursesRepository = $this->repositoryResolver->getRepository("SynapseAcademicBundle:OrgCourses");
        $this->cache = $cache;
        $this->resque = $resque;
        $this->creates = [];
        $this->updates = [];
        $this->jobs = [];
        $this->doctrine = $doctrine;
        $this->queue = 'default';
        $this->ebiConfigService = $ebiConfigService;
    }

    /**
     * Load the file into memory
     *
     * @param string $filePath
     *            Path to the current file
     * @return \SPLFileObject Returns a raw SPLFileObject
     */
    public function load($filePath, $orgId)
    {
        if (! file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->orgId = $orgId;

        $this->fileObject = new CSVReader($filePath, true, true);
        $this->affectedExternalIds = [];

        $rowVal = 0;
        foreach ($this->fileObject as $idx => $row) {
            $this->affectedExternalIds[] = $row[strtolower(self::UNIQUECOURSESECID)];
        }

        $this->createable = $this->affectedExternalIds;
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
        foreach ($this->fileObject as $idx => $row) {
            if ($idx === 0) {
                continue;
            }
            if (in_array($row[strtolower(self::UNIQUECOURSESECID)], $processed)) {
                continue;
            }
            if (in_array($row[strtolower(self::UNIQUECOURSESECID)], $this->createable)) {
                $this->create($idx, $row);
            }
            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $processed[] = $row[strtolower(self::UNIQUECOURSESECID)];
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


    public function generateDumpCSV($orgId)
    {
        $this->orgId = isset($this->orgId) ? $this->orgId : $orgId;

        $orgCourses = $this->orgCoursesRepository->getCourseForOrganization($orgId);

        $rows = [];
        if (isset($orgCourses) && count($orgCourses) > 0) {
            foreach ($orgCourses as $orgCourse) {
                $rows[] = [
                    'YearId' => $orgCourse['yearId'],
                    'TermId' => $orgCourse['termId'],
                    self::UNIQUECOURSESECID => $orgCourse['externalId'],

                    'SubjectCode' => $orgCourse['subjectCode'],
                    'CourseNumber' => $orgCourse['courseNumber'],
                    'SectionNumber' => $orgCourse['sectionNumber'],
                    'CourseName' => $orgCourse['courseName'],
                    'CreditHours' => $orgCourse['creditHours'],
                    'CollegeCode' => $orgCourse['collegeCode'],
                    'DeptCode' => $orgCourse['deptCode'],
                    'Days/Times' => $orgCourse['daysTimes'],
                    'Location' => $orgCourse['location']
                ];
            }
        }

        $columns = $this->getUploadFields($orgId);

        $file = new SplFileObject("data://course_uploads/{$this->orgId}-latest-course-data.csv", 'w');
        $file->fputcsv([
            'YearId',
            'TermId',
            self::UNIQUECOURSESECID,
            'SubjectCode',
            'CourseNumber',
            'SectionNumber',
            'CourseName',
            'CreditHours',
            'CollegeCode',
            'DeptCode',
            'Days/Times',
            'Location'
        ]);

        foreach ($rows as $fields) {
            $file->fputcsv($fields);
        }
    }

    private function getUploadFields()
    {
        $excluded = [
            'id',
            'createdAt',
            'createdBy',
            'modifiedAt',
            'modifiedBy',
            'deletedBy',
            'deletedAt'
        ];

        $courseItems = $this->doctrine->getManager()
            ->getClassMetadata('Synapse\AcademicBundle\Entity\OrgCourses')
            ->getFieldNames();
        $courseItems = array_diff($courseItems, $excluded);
        $courseItems = array_map(function ($value)
        {
            return ucfirst($value);
        }, $courseItems);

        return $courseItems;
    }

    private function create($idx, $person)
    {
        $this->creates[$idx] = $person;
    }

    public function getFileObject()
    {
        return $this->fileObject;
    }

    private function queueForWrite()
    {
        if (count($this->creates)) {

            $this->creates = array_change_key_case($this->creates,CASE_LOWER);

            $createObject = 'Synapse\UploadBundle\Job\CreateCourse';
            $jobNumber = uniqid();
            $job = new $createObject();
            $job->queue = $this->queue;
            $job->args = array(
                'creates' => $this->creates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId,
                'orgId' => $this->orgId
            );

            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }

        $this->creates = [];
    }


    public function updateDataFile($orgId)
    {
        $createObject = 'Synapse\UploadBundle\Job\UpdateCourseDataFile';
        $job = new $createObject();
        $job->args = array(
            'orgId' => $orgId
        );

        return $this->resque->enqueue($job, true);
    }
}
