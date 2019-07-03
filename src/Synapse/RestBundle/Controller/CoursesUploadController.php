<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\logger;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PHPExcel_IOFactory;
use Resque;
use Symfony\Component\HttpFoundation\Request;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\CoreBundle\Util\CSVFile;
use Synapse\RestBundle\Entity\Response;
use Synapse\UploadBundle\Job\ProcessCourseFacultyUpload;
use Synapse\UploadBundle\Job\ProcessCourseStudentUpload;
use Synapse\UploadBundle\Job\ProcessCourseUpload;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Class CoursesUploadController
 *
 * @package Synapse\RestBundle\Controller
 *
 *          @Rest\Prefix("/coursesupload")
 */
class CoursesUploadController extends AbstractAuthController
{

    /**
     * @var Logger
     *
     *      @DI\Inject(SynapseConstant::CONTROLLER_LOGGING_CHANNEL)
     */
    private $logger;

    /**
     * @var UploadFileLogService
     *
     *      @DI\Inject(UploadFileLogService::SERVICE_KEY)
     */
    private $uploadFileLogService;

    private function returnFilename($filename)
    {
        if ($filename == 'faculty') {
            $filename = 'course-' . $filename . '_staff';
        }

        if ($filename == 'student') {
            $filename = 'course-' . $filename;
        }

        return $filename;
    }

    /**
     * Get OrgCourses, OrgCourseStudent, OrgCourseFaculty Upload Template
     *
     * @Rest\Get("/template/{orgId}")
     *
     * @QueryParam(name="type", requirements="(course|faculty|student)",strict=true, description="type of template, type = course | faculty | student")
     *
     * @codeCoverageIgnore
     */
    public function getCoursesUploadTemplateAction($orgId, ParamFetcher $paramFetcher)
    {
        $tempType = $paramFetcher->get('type');

        header(UploadConstant::CONTENT_TYPE_CSV);
        header('Content-Disposition: attachment; filename="' . $this->returnFilename($tempType) . '-upload-template.csv"');

        $courseTemplate = [
            'YearId',
            'TermId',
            CourseConstant::UNIQUECOURSESECID,
            'SubjectCode',
            'CourseNumber',
            'SectionNumber',
            'CourseName',
            'CreditHours',
            'CollegeCode',
            'DeptCode',
            'Days/Times',
            'Location'
        ];
        $studentTemplate = [
            CourseConstant::UNIQUECOURSESECID,
            'StudentId',
            'Remove'
        ];
        $facultyTemplate = [
            CourseConstant::UNIQUECOURSESECID,
            'FacultyID',
            'PermissionSet',
            'Remove'
        ];

        if ($tempType == 'course' && isset($orgId)) {
            $course = implode(",", $courseTemplate);
            echo $course;
        } elseif ($tempType == CourseConstant::FACULTY) {
            $faculty = implode(",", $facultyTemplate);
            echo $faculty;
        } elseif ($tempType == 'student') {
            $student = implode(",", $studentTemplate);
            echo $student;
        } else {}
        exit();
    }

    /**
     * Create courses to upload for a student.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Course Student Upload",
     * section = "Course Upload",
     * statusCodes = {
     *                  201 = "Course student upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/student",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="organization")
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createCourseStudentUploadAction(ParamFetcher $paramFetcher)
    {
        $organization = $paramFetcher->get(UploadConstant::ORGN);
        $key = $paramFetcher->get('key');

        $pathParts = pathinfo($key);

        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://course_student_uploads/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://course_student_uploads/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }

        $file = new CSVFile("data://course_student_uploads/$key", true, ',', '"', '\\', true);
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

        $loggedInUserId = $this->getLoggedInUserId();
        $uploadFile = $this->uploadFileLogService->createUploadService($organization, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId, 'T');

        if (! in_array(strtolower(CourseConstant::UNIQUECOURSESECID), $columns) || (! in_array(strtolower(CourseConstant::STUDENTID), $columns)) || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }

        $this->uploadFileLogService->updateJobErrorPath($uploadFile);

        $job = new ProcessCourseStudentUpload();

        $job->args = array(
            UploadConstant::ORGN => $organization,
            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId(),
            UploadConstant::USERID => $loggedInUserId
        );

        $resque->enqueue($job);
        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Gets all CourseStudent uploads logs.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get all CourseStudent Upload Logs",
     * section = "Course Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/student")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getCourseStudentUploadsAction()
    {
        $uploads = $this->uploadFileLogService->findAllCourseStudentUploadLogs();
        $response = new Response($uploads, array());
        return $response;
    }

    /**
     * Gets one CourseStudent upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get CourseStudent Upload",
     * section = "Course Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/student/{id}")
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getCourseStudentUploadAction($id)
    {
        $upload = $this->uploadFileLogService->findOneCourseStudentUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Create Course Upload
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Course Student Upload",
     * section = "Course Upload",
     * statusCodes = {
     *                  201 = "Course upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="organization")
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createCourseUploadAction(ParamFetcher $paramFetcher)
    {
        $organization = $paramFetcher->get(UploadConstant::ORGN);
        $key = $paramFetcher->get('key');

        $pathParts = pathinfo($key);

        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://course_uploads/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://course_uploads/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }

        $file = new CSVFile("data://course_uploads/$key", true, ',', '"', '\\', true);
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

        $loggedInUserId = $this->getLoggedInUserId();
        $uploadFile = $this->uploadFileLogService->createUploadService($organization, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId, 'C');

        $requiredCols = [
            CourseConstant::YEARID,
            CourseConstant::TERMID,
            CourseConstant::UNIQUECOURSESECID,
            CourseConstant::COLLGEGCODE,
            CourseConstant::DEPTCODE,
            CourseConstant::SUBJECTCODE,
            CourseConstant::COURSENO,
            CourseConstant::COURSENAME,
            CourseConstant::SECNO,
            CourseConstant::CREDIT_HOURS
        ];

        $checkCols = 0;
        foreach ($requiredCols as $requiredCol) {
            $checkCols = (! in_array(strtolower($requiredCol), $columns)) ? 1 : 0;
            if ($checkCols == 1) {
                break;
            }
        }

        if ($checkCols == 1 || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }

        $this->uploadFileLogService->updateJobErrorPath($uploadFile);

        $job = new ProcessCourseUpload();

        $job->args = array(
            UploadConstant::ORGN => $organization,
            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId(),
            UploadConstant::USERID => $loggedInUserId
        );

        $resque->enqueue($job);
        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Gets a list of course uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Course Uploads",
     * section = "Course Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getCourseUploadsAction()
    {
        if (! $this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY)) {
            $this->logger->error("Get Course Upload- Not an authenticated user.");
            throw new AccessDeniedException();
        }
        $organizationId = $this->getLoggedInUserOrganizationId();
        $uploads = $this->uploadFileLogService->findAllCourseUploadLogs($organizationId);

        $response = new Response($uploads, array());
        return $response;
    }

    /**
     * Gets a single course upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Course Upload",
     * section = "Get Course Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{id}")
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getCourseUploadAction($id)
    {
        $upload = $this->uploadFileLogService->findOneCourseUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Create API to upload courses for faculty.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Course Student Upload",
     * section = "Course Upload",
     * statusCodes = {
     *                  201 = "Faculty course upload was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/faculty",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="organization")
     * @RequestParam(name="key")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createCourseFacultyUploadAction(ParamFetcher $paramFetcher)
    {
        $organization = $paramFetcher->get(UploadConstant::ORGN);
        $key = $paramFetcher->get('key');

        $pathParts = pathinfo($key);

        if ($pathParts[UploadConstant::EXTENSION] == 'xls' || $pathParts[UploadConstant::EXTENSION] == 'xlsx') {
            file_put_contents($this->container->getParameter(UploadConstant::KERNEL) . "/$key", fopen("data://course_faculty_uploads/$key", 'r'));
            $this->convertXLStoCSV($this->container->getParameter(UploadConstant::KERNEL) . "/$key", "data://course_faculty_uploads/{$pathParts['filename']}.csv");
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }

        $file = new CSVFile("data://course_faculty_uploads/$key", true, ',', '"', '\\', true);;
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
        $loggedInUserId = $this->getLoggedInUserId();
        $uploadFile = $this->uploadFileLogService->createUploadService($organization, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId, 'P');

        if (! in_array(strtolower(CourseConstant::UNIQUECOURSESECID), $columns) || (! in_array(strtolower(CourseConstant::FACULTYID), $columns)) || (! in_array(strtolower(CourseConstant::PERMISSIONSET), $columns)) || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }

        $this->uploadFileLogService->updateJobErrorPath($uploadFile);

        $job = new ProcessCourseFacultyUpload();

        $job->args = array(
            UploadConstant::ORGN => $organization,
            'key' => $key,
            UploadConstant::JOB_NUM => $jobNumber,
            UploadConstant::UPLOADID => $uploadFile->getId(),
            UploadConstant::USERID => $loggedInUserId
        );

        $resque->enqueue($job);
        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Gets a list of CourseFaculty uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Faculty Course Uploads",
     * section = "Course Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/faculty")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getCourseFacultyUploadsAction()
    {
        $uploads = $this->uploadFileLogService->findAllCourseFacultyUploadLogs();
        $response = new Response($uploads, array());
        return $response;
    }

    /**
     * Gets a single CourseFaculty upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Faculty Course Upload",
     * section = "Course Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/faculty/{id}")
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getCourseFacultyUploadAction($id)
    {
        $upload = $this->uploadFileLogService->findOneCourseFacultyUploadLog($id);
        $response = new Response($upload, array());
        return $response;
    }

    /**
     * Gets a list of pending course student uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Pending Student Course Uploads",
     * section = "Course Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/student/pending/{orgId}")
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getPendingCourseStudentUploadAction($orgId)
    {
        $upload = $this->uploadFileLogService->hasPendingView($orgId, 'T');
        return new Response([
            UploadConstant::UPLOAD => $upload
        ], []);
    }

    /**
     * Gets a list of pending course faculty uploads
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Pending Faculty Course Uploads",
     * section = "Course Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/faculty/pending/{orgId}")
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getPendingCourseFacultyUploadAction($orgId)
    {
        $upload = $this->uploadFileLogService->hasPendingView($orgId, 'P');
        return new Response([
            UploadConstant::UPLOAD => $upload
        ], []);
    }

    /**
     * Gets a list of all pending course uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get All Pending Course Uploads",
     * section = "Course Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/course/pending/{orgId}")
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getPendingCourseUploadAction($orgId)
    {
        $upload = $this->uploadFileLogService->hasPendingView($orgId, 'C');
        return new Response([
            UploadConstant::UPLOAD => $upload
        ], []);
    }

    /**
     * Gets a course's upload status.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Upload Status",
     * section = "Course Upload",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/status/{id}")
     * @Rest\View(statusCode=200)
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

    /*
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

    private function convertXLStoCSV($infile, $outfile)
    {
        $fileType = PHPExcel_IOFactory::identify($infile);
        $objReader = PHPExcel_IOFactory::createReader($fileType);

        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($infile);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->save($outfile);
    }
    public function authenticate()
    {
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted(UploadConstant::IS_AUTH_FULLY))) {
            throw new AccessDeniedException();
        }
    }
}
