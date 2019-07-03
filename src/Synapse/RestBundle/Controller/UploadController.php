<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PHPExcel_IOFactory;
use Resque;
use SplFileObject;
use Symfony\Component\HttpFoundation\Request;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\EntityNotFoundException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Service\Impl\OrgProfileService;
use Synapse\CoreBundle\Service\Impl\ProfileService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\CoreBundle\Util\CSVFile;
use Synapse\RestBundle\Entity\ProfileDto;
use Synapse\RestBundle\Entity\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SurveyBundle\Service\Impl\SurveyBlockService;
use Synapse\UploadBundle\EntityDto\DataFileDto;
use Synapse\UploadBundle\Job\ProcessAcademicUpdateUpload;
use Synapse\UploadBundle\Job\ProcessFacultyUpload;
use Synapse\UploadBundle\Job\ProcessGroupFacultyUpload;
use Synapse\UploadBundle\Job\ProcessGroupStudentUpload;
use Synapse\UploadBundle\Job\ProcessStudentUpload;
use Synapse\UploadBundle\Job\ProcessSurveyUpload;
use Synapse\UploadBundle\Service\Impl\AcademicUpdateUploadService;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\UploadBundle\Service\Impl\UploadService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Class UploadController
 *
 * @package Synapse\RestBundle\Controller
 *
 *          @Rest\Prefix("/upload")
 */
class UploadController extends AbstractAuthController
{
    /**
     *
     * @var array
     */
    private $academicUpdateUploadFields = [
        'uniquecoursesectionid',
        'studentid',
        'failurerisk',
        'inprogressgrade',
        'finalgrade',
        'absences',
        'comments',
        'senttostudent'
    ];

    /**
     * @var AcademicUpdateUploadService
     *
     *      @DI\Inject(AcademicUpdateUploadService::SERVICE_KEY)
     */
    private $academicUpdateUploadService;

    /**
     * @var OrgProfileService
     *
     *      @DI\Inject(OrgProfileService::SERVICE_KEY)
     */
    private $orgProfileService;

    /**
     * @var ProfileService
     *
     *      @DI\Inject(ProfileService::SERVICE_KEY)
     */
    private $profileService;

    /**
     * @var SurveyBlockService
     *
     *      @DI\Inject(SurveyBlockService::SERVICE_KEY)
     */
    private $surveyBlockService;

    /**
     * @var UploadFileLogService
     *
     *      @DI\Inject(UploadFileLogService::SERVICE_KEY)
     */
    private $uploadFileLogService;

    /**
     * @var UploadService
     *
     *      @DI\Inject(UploadService::SERVICE_KEY)
     */
    private $uploadService;

    /**
     * Creates a new student upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Student Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  201 = "Student upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/student")
     * @Rest\View(statusCode=201)
     * @RequestParam(name="organization")
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createStudentUploadAction(ParamFetcher $paramFetcher)
    {

        $organization = $paramFetcher->get(UploadConstant::ORGN);
        $key = $paramFetcher->get('key');

        $this->ensureEBIorCoordinatorUserAccess($organization);
        $pathParts = pathinfo($key);

        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://student_uploads/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://student_uploads/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }

        $file = new CSVFile("data://student_uploads/$key", true, ',', '"', '\\', true);
        $file->seek(PHP_INT_MAX);
        $rowsTotal = $file->key();
        $file->seek(0);
        foreach ($file as $idx => $row) {
            $columns = array_keys($row);
            break;
        }

        $resque = $this->get(UploadConstant::RESQUE);

        $jobNumber = uniqid();
        /*
         * Passing logged in user id
        */
        $loggedInUserId = $this->getLoggedInUserId() ? $this->getLoggedInUserId():null;
        $uploadFile = $this->uploadFileLogService->createUploadService($organization, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId, 'S');

        if (! in_array(strtolower(UploadConstant::EXTERNALID), $columns) || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }

        $this->uploadFileLogService->updateJobErrorPath($uploadFile);

        $job = new ProcessStudentUpload();

        $job->args = array(
            UploadConstant::ORGN => $organization,
            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId()
        );

        $resque->enqueue($job);
        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Creates a new survey upload job.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Survey Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  201 = "Survey upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/survey",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="key")
     * @RequestParam(name="type")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createSurveyUploadAction(ParamFetcher $paramFetcher)
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        }

        $organization = 1;
        $key = $paramFetcher->get('key');
        $type = $paramFetcher->get('type');

        $pathParts = pathinfo($key);

        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://survey_uploads/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://survey_uploads/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }

        $file = new CSVFile("data://survey_uploads/$key", true, ',', '"', '\\', true);
        $file->seek(PHP_INT_MAX);
        $rowsTotal = $file->key();
        $file->seek(0);
        foreach ($file as $idx => $row) {
            $columns = array_keys($row);
            break;
        }

        $resque = $this->get(UploadConstant::RESQUE);

        $jobNumber = uniqid();
        /*
         * Passing logged in user id
        */
        $loggedInUserId = $this->getLoggedInUserId() ? $this->getLoggedInUserId() : null;
        $uploadFile = $this->uploadFileLogService->createSurveyUploadLog($organization, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId, $type);

        $reqdCol = 'SurveyBlockID';
        if ($type != UploadConstant::BLOCK) {
            $reqdCol = 'MarkerID';
        }

        if (! in_array(strtolower($reqdCol), $columns) || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }

        $this->uploadFileLogService->updateJobErrorPath($uploadFile);

        $job = new ProcessSurveyUpload();
        //$job->queue = 'surveyUpload_queue';
        $job->args = array(
            UploadConstant::ORGN => $organization,
            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            'type' => $type,
            UploadConstant::UPLOADID => $uploadFile->getId()
        );

        $resque->enqueue($job);
        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Gets student uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student Uploads",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/student")
     * @Rest\View(200)
     *
     * @return Response
     */
    public function getStudentUploadsAction()
    {
        if (! $this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY)) {
            throw new AccessDeniedException();
        }
        $organizationId = $this->getLoggedInUserOrganizationId();
        $uploads = $this->uploadFileLogService->findAllStudentUploadLogs($organizationId);
        $response = new Response($uploads, array());
        return $response;
    }

    /**
     * Get Student Upload Template
     *
     * @ApiDoc(
     * resource = true,
     * description = "Download Student Upload template",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/student/template/{organizationId}")
     * @Rest\View(200)
     *
     * @codeCoverageIgnore
     *
     */
    public function getStudentUploadTemplateAction($organizationId)
    {
        $template = "student";
        $fileName = 'student-upload-template.csv';

        $orgProfileItemsDataList = $this->orgProfileService->getInstitutionSpecificProfileBlockItems($organizationId, false, 'active', false);
        $profileItemDataList = $this->profileService->getProfiles('active');
        $ebiProfileItems = array_column($profileItemDataList['profile_items'], 'item_label');
        $orgProfileItems = array_column($orgProfileItemsDataList['profile_items'], 'item_label');
        $additionalColumns = array_merge($ebiProfileItems, $orgProfileItems);


        $fileName = $this->uploadService->downloadUploadTemplate($template, $fileName, $additionalColumns);
        $response = new HttpFoundationResponse();
        $response->headers->set('Content-type', 'text/csv');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', basename($fileName)));
        $response->headers->set('Content-Length', filesize($fileName));
        $response->setContent(readfile($fileName));
        unlink($fileName);
        return $response;
    }

    /**
     * Get Survey Upload Template
     *
     * @Rest\Get("/survey/template")
     *
     * @QueryParam(name="type", strict=true, description="type of template, type = survey")
     *
     * @codeCoverageIgnore
     */
    public function getSurveyUploadTemplateAction(ParamFetcher $paramFetcher)
    {
        $this->get('tinyrbac.manager')->setAuthEnabled(false);

        $type = $paramFetcher->get('type');

        $surveyMappingData = $this->surveyBlockService->getTemplateData();

        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="survey-' . $type . '-template.csv"');

        $blockTemplate[] = 'SurveyBlockID';

        $surveyTemplate = [
            'LongitudinalID',
            'SurvID',
            'FactorID'
        ];

        $surveyTemplate = array_merge($surveyTemplate, $blockTemplate);

        $surveyTemplate = implode(",", $surveyTemplate);

        echo $surveyTemplate;
        foreach ($surveyMappingData as $mapData) {
            echo "\n";
            echo implode(",", $mapData);
        }

        echo "\n";

        exit();
    }


    /**
     * Get Survey Upload Template
     *
     * @Rest\Get("/survey/data")
     *
     * @QueryParam(name="type", strict=true, description="type of template, type = survey")
     *
     * @codeCoverageIgnore
     */
    public function getSurveyExistingFileAction(ParamFetcher $paramFetcher)
    {
        $this->get('tinyrbac.manager')->setAuthEnabled(false);
        $type = $paramFetcher->get('type');

        if ($type == UploadConstant::BLOCK) {
            $uploadType = 'SB';
        } elseif ($type = 'marker') {
            $uploadType = 'SM';
        }

        $upload = $this->uploadFileLogService->getLastRowByType($uploadType);
        $fileName = "{$upload['id']}-survey-" . $type . "-data.csv";
        $file = fopen("data://survey_uploads/{$fileName}", 'r');

        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        if ($file) {
            fpassthru($file);
            fClose($file);
        } else {
            throw new SynapseValidationException("File Name Not Found");
        }
    }


    /**
     * Gets a student upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/student/{id}")
     * @Rest\View(200)
     *
     * @param int $id
     * @return Response
     */
    public function getStudentUploadAction($id)
    {
        $upload = $this->uploadFileLogService->findOneStudentUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Gets survey upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Survey Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/survey/{id}")
     * @Rest\View(200)
     *
     * @param int $id
     * @return Response
     */
    public function getSurveyUploadAction($id)
    {
        $upload = $this->uploadFileLogService->findSurveyUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Get AcademicUpdate Upload Template
     *
     * @Rest\Get("/academicupdates/template")
     *
     * @QueryParam(name="type", strict=true, description="type of template, type = academicupdates")
     *
     * @param ParamFetcher $paramFetcher
     * @codeCoverageIgnore
     */
    public function getAcademicUpdateUploadTemplateAction(ParamFetcher $paramFetcher)
    {
        // This controller will be deprecated when the front end begins to add in
        $this->get('tinyrbac.manager')->setAuthEnabled(false);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="academic-update-template.csv"');
        $loggedInUserOrganizationId = $this->getLoggedInUserOrganizationId();

        $academicUpdatesTemplate = $this->academicUpdateUploadService->getHeaders($loggedInUserOrganizationId, true);

        $academicUpdates = implode(",", $academicUpdatesTemplate);
        echo $academicUpdates;

        echo "\n";

        exit();
    }


    /**
     * Get AcademicUpdate Upload Template
     *
     * @Rest\Get("/academicupdates/template/{organizationId}")
     *
     * @param organizationId
     */
    public function getAcademicUpdateUploadTemplateWithOrganizationIdAction($organizationId)
    {
        $this->get('tinyrbac.manager')->setAuthEnabled(false);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="academic-update-template.csv"');
        $user = $this->getUser();
        $loggedInUserOrganizationId = $user->getOrganization()->getId();

        if ($loggedInUserOrganizationId === -1 && $organizationId != null) {
            $loggedInUserOrganizationId = $organizationId;
        }
        $academicUpdatesTemplate = $this->academicUpdateUploadService->getHeaders($loggedInUserOrganizationId, true);

        $academicUpdates = implode(",", $academicUpdatesTemplate);
        echo $academicUpdates;

        echo "\n";

        exit();
    }

    /**
     * Gets an academic update upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Academic Update Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/academicupdates/{id}",requirements={"id" = "\d+"})
     * @Rest\View(200)
     *
     * @param int $id
     * @return Response
     */
    public function getAcademicUpdateUploadAction($id)
    {
        $upload = $this->uploadFileLogService->findAllUploadLogs($id, 'A');
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Creates a faculty upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Faculty Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  201 = "Faculty upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/faculty")
     * @Rest\View(201)
     * @RequestParam(name="organization")
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createFacultyUploadAction(ParamFetcher $paramFetcher)
    {
        $organization = $paramFetcher->get(UploadConstant::ORGN);
        $key = $paramFetcher->get('key');

        $this->ensureEBIorCoordinatorUserAccess($organization);

        $pathParts = pathinfo($key);

        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://faculty_uploads/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://faculty_uploads/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }

        $file = new CSVFile("data://faculty_uploads/$key", true, ',', '"', '\\', true);
        $file->seek(PHP_INT_MAX);
        $rowsTotal = $file->key();
        $file->seek(0);
        foreach ($file as $idx => $row) {
            $columns = array_keys($row);
            break;
        }

        $resque = $this->get(UploadConstant::RESQUE);

        $jobNumber = uniqid();
        /*
         * Passing logged in user id
        */

        $loggedInUserId = $this->getLoggedInUserId() ? $this->getLoggedInUserId() : null;
        $uploadFile = $this->uploadFileLogService->createUploadService($organization, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId, 'F');

        if (! in_array(strtolower(UploadConstant::EXTERNALID), $columns) || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }

        $this->uploadFileLogService->updateJobErrorPath($uploadFile);

        $job = new ProcessFacultyUpload();
        //$job->queue = 'facultyUpload_queue';
        $job->args = array(
            UploadConstant::ORGN => $organization,
            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId()
        );

        $resque->enqueue($job);
        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Create API to upload an academic update.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Academic Update Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  201 = "Survey upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/academicupdates",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="organization")
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createAcademicUpdateUploadAction(ParamFetcher $paramFetcher)
    {
        $organization = $paramFetcher->get(UploadConstant::ORGN);
        $key = $paramFetcher->get('key');

        $pathParts = pathinfo($key);

        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://academic_update_uploads/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://academic_update_uploads/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }

        $file = new CSVFile("data://academic_update_uploads/$key", true, ',', '"', '\\', true);
        $file->seek(PHP_INT_MAX);
        $rowsTotal = $file->key();
        $file->seek(0);
        foreach ($file as $idx => $row) {
            $columns = array_keys($row);
            break;
        }

        $resque = $this->get(UploadConstant::RESQUE);

        $jobNumber = uniqid();
        /*
         * Passing logged in user id
        */
        $loggedInUserId = $this->getLoggedInUserId() ? $this->getLoggedInUserId() : null;
        $uploadFile = $this->uploadFileLogService->createAcademicUpdateUploadLog($organization, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId);

        if (!in_array(strtolower('UniqueCourseSectionId'), $columns) || !in_array(strtolower('StudentId'), $columns) || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            throw new ValidationException([
                UploadConstant::REQUIRED_FIELD_ERROR
            ], UploadConstant::REQUIRED_FIELD_ERROR, "required_field_error");
        }

        /**
         * Validate the CSV columns
         *
         * @var array
         */
        $invalidColumns = [];
        foreach ($columns as $csvColumn) {
            if (!in_array(strtolower($csvColumn), $this->academicUpdateUploadFields)) {
                $invalidColumns[] = ucfirst($csvColumn);
            }
        }

        if (count($invalidColumns) > 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            throw new ValidationException([
                UploadConstant::INVALID_FIELD_ERROR . implode(', ', $invalidColumns)
            ], UploadConstant::INVALID_FIELD_ERROR . implode(', ', $invalidColumns), "invalid_field_error");
        }
        $csvFileObj = new SplFileObject("data://academic_update_uploads/$key", 'r');
        $csvHeaders = array_map('trim', explode(',', $csvFileObj->current()));
        $duplicates = [];
        if (!empty($csvHeaders)) {
            $duplicates = array_unique(array_diff_assoc($csvHeaders, array_unique($csvHeaders)));
        }
        if (!empty($duplicates)) {
            throw new ValidationException([
                UploadConstant::DUPLICATE_FIELDS_ERROR . implode(', ', $duplicates)
            ], UploadConstant::DUPLICATE_FIELDS_ERROR . implode(', ', $duplicates), "duplicate_field_error");
        }
        $this->uploadFileLogService->updateJobErrorPath($uploadFile);

        // Passing server array since its required while sending link in email
        $serverArray = [
            'HTTPS' => $_SERVER['HTTPS'],
            'HTTP_HOST' => $_SERVER['HTTP_HOST']
        ];
        $job = new ProcessAcademicUpdateUpload();
        $job->args = array(
            'orgId' => $organization,
            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId(),
            'userId' => $loggedInUserId,
            'serverArray' => $serverArray
        );

        $resque->enqueue($job);
        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Gets faculty uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Faculty Uploads",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/faculty")
     * @Rest\View(200)
     *
     * @return Response
     */
    public function getFacultyUploadsAction()
    {
        if (! $this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY)) {
            throw new AccessDeniedException();
        }

        $organizationId = $this->getLoggedInUserOrganizationId();
        $uploads = $this->uploadFileLogService->findAllFacultyUploadLogs($organizationId);

        $response = new Response($uploads, array());
        return $response;
    }

    /**
     * Get Faculty Upload Template
     *
     * @ApiDoc(
     * resource = true,
     * description = "Download Faculty Upload template",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/faculty/template")
     * @Rest\View(200)
     *
     * @codeCoverageIgnore
     *
     */
    public function getFacultyUploadTemplateAction()
    {
        $template = "faculty";
        $fileName = 'faculty_staff-upload-template.csv';
        $fileName  = $this->uploadService->downloadUploadTemplate($template, $fileName);
        $response = new HttpFoundationResponse();
        $response->headers->set('Content-type', 'text/csv');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', basename($fileName)));
        $response->headers->set('Content-Length', filesize($fileName));
        $response->setContent(readfile($fileName));
        unlink($fileName);
        return $response;
    }


    /**
     * Gets a faculty upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Faculty Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/faculty/{id}")
     * @Rest\View(200)
     *
     * @param int $id
     * @return Response
     */
    public function getFacultyUploadAction($id)
    {
        $upload = $this->uploadFileLogService->findOneFacultyUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Create an upload for a group of students.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create GroupStudent Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  201 = "GroupStudent upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/group/{groupId}/student")
     * @Rest\View(201)
     * @RequestParam(name="organization")
     * @RequestParam(name="key")
     *
     * @param int $groupId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createGroupStudentUploadAction($groupId, ParamFetcher $paramFetcher)
    {
        $organization = $paramFetcher->get(UploadConstant::ORGN);
        $key = $paramFetcher->get('key');

        $pathParts = pathinfo($key);

        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://group_uploads/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://group_uploads/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }

        $file = new CSVFile("data://group_uploads/$key", true, ',', '"', '\\', true);
        $file->seek(PHP_INT_MAX);
        $rowsTotal = $file->key();
        $file->seek(0);
        foreach ($file as $idx => $row) {
            $columns = array_keys($row);
            break;
        }

        $resque = $this->get(UploadConstant::RESQUE);

        $jobNumber = uniqid();

        // Passing logged in user id

        $loggedUserId = $this->getLoggedInUserId() ? $this->getLoggedInUserId() : null;
        $uploadFile = $this->uploadFileLogService->createGroupStudentUploadLog($organization, $key, $columns, $rowsTotal, $jobNumber, $groupId, $loggedUserId);

        if (!in_array(strtolower(UploadConstant::EXTERNALID), $columns) || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            if ($rowsTotal == 0) {
                throw new SynapseValidationException('Please check your uploaded file, it should have at least one ExternalId');
            } else {
                $response = new Response($uploadFile, []);
                return $response;
            }
        }

        $this->uploadFileLogService->updateJobErrorPath($uploadFile);

        $job = new ProcessGroupStudentUpload();
        $job->args = array(
            UploadConstant::ORGN => $organization,
            'groupId' => $groupId,
            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId()
        );

        $resque->enqueue($job);

        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Gets all groupStudent uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get GroupStudent Uploads",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/group/{groupId}/student")
     * @Rest\View(200)
     *
     * @param int $groupId
     * @return Response
     */
    public function getGroupStudentUploadsAction($groupId)
    {
        $uploads = $this->uploadFileLogService->findAllGroupStudentUploadLogs($groupId);
        $response = new Response($uploads, array());
        return $response;
    }

    /**
     * Get GroupStudent Upload Template
     *
     * @Rest\Get("/group/student/template")
     *
     * @codeCoverageIgnore
     */
    public function getGroupStudentUploadTemplateAction()
    {
        $this->get('tinyrbac.manager')->setAuthEnabled(false);
        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="group-student-upload-template.csv"');

        $columnCount = 1;
        echo "ExternalId\n";
        exit();
    }

    /**
     * Gets a groupStudent upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get GroupStudent Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/group/{groupId}/student/{id}")
     * @Rest\View(200)
     *
     * @param int $groupId
     * @param int $id
     * @return Response
     */
    public function getGroupStudentUploadAction($groupId, $id)
    {
        $upload = $this->uploadFileLogService->findOneGroupStudentUploadLog($groupId, $id);
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Creates an upload for a groupFaculty job.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create GroupFaculty Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  201 = "GroupFaculty Upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/group/{groupId}/faculty")
     * @Rest\View(201)
     * @RequestParam(name="organization")
     * @RequestParam(name="key")
     *
     * @param int $groupId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createGroupFacultyUploadAction($groupId, ParamFetcher $paramFetcher)
    {
        $organization = $paramFetcher->get(UploadConstant::ORGN);
        $key = $paramFetcher->get('key');

        $pathParts = pathinfo($key);

        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://group_uploads/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://group_uploads/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }

        $file = new CSVFile("data://group_uploads/$key", true, ',', '"', '\\', true);
        $file->seek(PHP_INT_MAX);
        $rowsTotal = $file->key();
        $file->seek(0);
        foreach ($file as $idx => $row) {
            $columns = array_keys($row);
            break;
        }

        $resque = $this->get(UploadConstant::RESQUE);

        $jobNumber = uniqid();

        $uploadFile = $this->uploadFileLogService->createGroupFacultyUploadLog($organization, $key, $columns, $rowsTotal, $jobNumber, $groupId);

        if (! in_array(strtolower(UploadConstant::EXTERNALID), $columns) || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }

        $this->uploadFileLogService->updateJobErrorPath($uploadFile);

        $job = new ProcessGroupFacultyUpload();
        //$job->queue = 'groupFacultyUpload_queue';
        $job->args = array(
            UploadConstant::ORGN => $organization,
            'groupId' => $groupId,
            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId()
        );

        $resque->enqueue($job);

        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Gets all groupFaculty uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get GroupFaculty Uploads",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/group/{groupId}/faculty")
     * @Rest\View(200)
     *
     * @param int $groupId
     * @return Response
     * @deprecated 'uploadFileLogService does not contain findAllGroupFacultyUploadLogs()'
     */
    public function getGroupFacultyUploadsAction($groupId)
    {
        $uploads = $this->uploadFileLogService->findAllGroupFacultyUploadLogs($groupId);
        $response = new Response($uploads, array());
        return $response;
    }

    /**
     * Get GroupFaculty Upload Template
     *
     * @Rest\Get("/group/faculty/template")
     *
     * @codeCoverageIgnore
     */
    public function getGroupFacultyUploadTemplateAction()
    {
        $this->get('tinyrbac.manager')->setAuthEnabled(false);
        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="group-faculty-upload-template.csv"');

        $columnCount = 1;
        echo "ExternalId\n";
        exit();
    }

    /**
     * Gets a groupFaculty upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get GroupFaculty Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/group/{groupId}/faculty/{id}")
     * @Rest\View(200)
     *
     * @param int $groupId
     * @param int $id
     * @return Response
     * @deprecated 'uploadFileLogService does not contain findOneGroupFacultyUploadLog()'
     */
    public function getGroupFacultyUploadAction($groupId, $id)
    {
        $upload = $this->uploadFileLogService->findOneGroupFacultyUploadLog($groupId, $id);
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Gets all pending student uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Pending Student Uploads",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/student/pending/{orgId}")
     * @Rest\View(200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getPendingStudentUploadAction($orgId)
    {
        $upload = $this->uploadFileLogService->hasPendingView($orgId, 'S');
        return new Response([
            UploadConstant::UPLOAD => $upload
        ], []);
    }

    /**
     * Gets pending faculty uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Pending Faculty Uploads",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/faculty/pending/{orgId}")
     * @Rest\View(200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getPendingFacultyUploadAction($orgId)
    {
        $upload = $this->uploadFileLogService->hasPendingView($orgId, 'F');
        return new Response([
            UploadConstant::UPLOAD => $upload
        ], []);
    }

    /**
     * Gets pending group student uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Pending GroupStudent Uploads",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/group/{groupId}/student/pending/{orgId}")
     * @Rest\View(200)
     *
     * @param int $groupId
     * @param int $orgId
     * @return Response
     */
    public function getPendingGroupStudentUploadAction($groupId, $orgId)
    {
        $upload = $this->uploadFileLogService->groupHasPendingView($groupId, $orgId);
        return new Response([
            UploadConstant::UPLOAD => $upload
        ], []);
    }

    /**
     * Gets pending group faculty uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Pending Group Faculty Uploads",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/group/{groupId}/faculty/pending/{orgId}")
     * @Rest\View(200)
     *
     * @param int $groupId
     * @param int $orgId
     * @return Response
     */
    public function getPendingGroupFacultyUploadAction($groupId, $orgId)
    {
        $upload = $this->uploadFileLogService->groupHasPendingView($groupId, $orgId);
        return new Response([
            UploadConstant::UPLOAD => $upload
        ], []);
    }

    /**
     * Gets the status of an upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Upload Status",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/status/{id}")
     * @Rest\View(200)
     *
     * @param int $id
     * @return Response
     */
    public function getUploadStatusAction($id)
    {
        $upload = $this->uploadFileLogService->getStatus($id);
        return new Response([
            UploadConstant::UPLOAD => $upload
        ], []);
    }

    /**
     * Creates a datafile generation job.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Datafile Generation Job",
     * input = "Synapse\UploadBundle\EntityDto\DataFileDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Uploads",
     * statusCodes = {
     *                  201 = "Datafile generation job was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/datafile", requirements={"_format"="json"})
     * @Rest\View(201)
     *
     * @param DataFileDto $dataFileDto
     * @return Response
     */
    public function createDatafileAction(DataFileDto $dataFileDto)
    {
        $loggedInUser = $this->getLoggedInUser();
        $organization = $this->getLoggedInUserOrganization();
        $organizationId = $this->getLoggedInUserOrganizationId();
        if (strtolower($dataFileDto->getType()) != 'staticlist') { // static lists will get checked in the service function
            $this->ensureEBIorCoordinatorUserAccess($organizationId);
        }

        return $this->uploadService->prepareDatafileJob($loggedInUser, $dataFileDto->getType(), $organization, $dataFileDto->getUploadTypeId());

    }


    /**
     * Marks an upload as invalid.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Mark Upload Invalid",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/markinvalid/{id}")
     * @Rest\View(200)
     *
     * @param int $id
     * @return Response
     */
    public function markInvalidAction($id)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $this->uploadFileLogService->markStatusInvalid($id,$organizationId);
    }


    /**
     * @param array $array
     * @param int $value
     * @param int $key
     * @return boolean
     *
     * @codeCoverageIgnore
     */
    private function myInArray($array, $value, $key)
    {
        // loop through the array
        foreach ($array as $val) {
            // if $val is an array cal myInArray again with $val as array input
            if (is_array($val)) {
                if ($this->myInArray($val, $value, $key)) {
                    return true;
                }
            } else {
                if ($array[$key] == $value) {
                    return true;
                }
            }
        }
        return false;
    }

    private function hmacsha1($key, $data)
    {
        $blocksize = 64;
        $hashfunc = 'sha1';
        if (strlen($key) > $blocksize) {
            $key = pack('H*', $hashfunc($key));
        }
        $key = str_pad($key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);
        $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));

        return bin2hex($hmac);
    }

    private function hex2b64($str)
    {
        $raw = '';
        for ($i = 0; $i < strlen($str); $i += 2) {
            $raw .= chr(hexdec(substr($str, $i, 2)));
        }
        return base64_encode($raw);
    }

    /**
     * Convert XLS files to CSV
     *
     * @param string $infile
     * @param string $outfile
     */
    private function convertXLStoCSV($infile, $outfile)
    {
        $fileType = PHPExcel_IOFactory::identify($infile);
        $objReader = PHPExcel_IOFactory::createReader($fileType);

        $objPHPExcel = $objReader->load($infile);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->save($outfile);
    }

    /**
     * List all uploads file history.
     *
     * @ApiDoc(
     * resource = true,
     * description = "List Uploads File History",
     * section = "Uploads",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/history/{orgId}")
     * @QueryParam(name="page_no", strict=false, description="page_no")
     * @QueryParam(name="offset", strict=false, description="offset")
     * @QueryParam(name="sortBy", strict=false, description="sorting field")
     * @QueryParam(name="filter", strict=false, description="filter")
     * @QueryParam(name="output-format", strict=false, description="output-format: csv")
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listHistoryAction($orgId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $pageNo = $paramFetcher->get('page_no');
        $offset = $paramFetcher->get('offset');
        $sortBy =  $paramFetcher->get('sortBy');
        $filter =  $paramFetcher->get('filter');
        $outputFormat = trim($paramFetcher->get('output-format'));
        $isCSV =  (strtolower($outputFormat) == 'csv') ? true : false;

        $uploadHistory = $this->uploadFileLogService->listHistory($loggedInUserId, $orgId, $pageNo, $offset, $sortBy, $filter, $isCSV);
        return new Response($uploadHistory);
    }

}